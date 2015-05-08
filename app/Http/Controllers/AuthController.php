<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AuthenticateUser;
use App\UserRepository;
use Illuminate\Http\Request;

use Auth;
use Input;
use App\User;
use Response;

// using javascript now to authenticate
use App\LaravelFacebookRedirectLoginHelper;
use App\Library\JsonResponseHelper;
use App\Library\FacebookHelper;

class AuthController extends Controller {

	// log user into app
    public function login(AuthenticateUser $authenticateUser, Request $request)
    {
        return $authenticateUser->execute($request->has('code'), $this);
    }
    
    public function userHasLoggedIn($user)
    {
        return redirect("/home");
    }
    
    public function facebookLogin()
    {

        $helper = new LaravelFacebookRedirectLoginHelper(env('FB_REDIRECT'), env('FB_APPID'), env('FB_APPSECRET'));
        //echo '<a href="' . $helper->getLoginUrl() . '">Login with Facebook</a>';

        return view('guest.loginWithFacebook', array(
            'loginUrl' =>  $helper->getLoginUrl(), 
            'appId' => env('FB_APPID')
        ));
    }
    
  
    
    /**
        Add or update a user entry for logged in facebook user
    **/
    public function updateFacebookUser()
    {
        $jsonHelper = new JsonResponseHelper;
        

        // fail if we don't have a user id
        if (!Input::has('facebookId'))
        {
            return $jsonHelper->failJson('Missing input');
        }
        
        $facebookId = Input::get('facebookId');
        $facebookName = Input::get('facebookName');
        $facebookAccessToken = Input::get('facebookAccessToken');
        
        if ($facebookId == NULL)
        {
            return $jsonHelper->failJson("Null input: " . $facebookId );
        }
        
        if ($facebookId  < 5)
        {
            return $jsonHelper->failJson("Facebook Id cannot be zero");
        }
        
        
        // set user id
        $user = User::firstOrNew(array(
            'id' => $facebookId
        ));

        $user->id = $facebookId;   

        $infoUpdated = 1;
        
        $user->save();
        
        if (!Auth::check())
        {
            Auth::loginUsingId($facebookId);
        }

        // update name if we have it
        if (Input::has('facebookName'))
        {
            // ensure value isn't null
            if ($facebookName == null)
            {
                return $jsonHelper->failJson("Invalid name");
            }
            $infoUpdated += 2;

            $user->name = $facebookName;
            $user->save();
        }
        
        // set facebook access token
        if (Input::has('facebookAccessToken'))
        {
            
            // ensure value isn't null
            if ($facebookAccessToken == null)
            {
                if (strlen($facebookAccessToken > 0))
                {
                    return $jsonHelper->failJson("Invalid access token");
                    
                }
                
            }
            
            // try to extend the access token
            // get new session
            $fbHelper = new FacebookHelper;
            $session = $fbHelper->getSessionWithToken($facebookAccessToken);
            // try to extend token
            
            if ($session)
            {    
                $checkAccessToken = $session->getAccessToken();
                if ($checkAccessToken == $facebookAccessToken)
                {
                    $longLivedAccessToken = $checkAccessToken->extend();
                    if ($longLivedAccessToken != NULL)
                    {
                        if (strlen($longLivedAccessToken) > 5)
                        {
                            $infoUpdated += 20;
                            $facebookAccessToken = $longLivedAccessToken;
                        }
                    }
                }
            }
            
            
            $infoUpdated += 4;
            Auth::user()->fb_token = $facebookAccessToken;
            Auth::user()->save();
        }        
        
        // log the user in to the system;
        //Auth::login($user);
        
        if (Auth::check())
        {
            $infoUpdated += 10;
        }

        if ($facebookName != null)
        {
            // this is an attempt to solve persistance problem 
            // that makes returning regular json_encode data
            // not persist, which is critical for login to work
            return Response::json(array("status"=>"SUCCESS",
            "detailedStatus" => 'Updated user info ' . $infoUpdated,
                'facebook_name' => $facebookName
            ));
        }
        else if ($facebookAccessToken != null)
        {
            return array("status"=>"SUCCESS",
                 "detailedStatus" => 'Updated user info ' . $infoUpdated,
                'facebook_token' => $user->fb_token
            );
        }
        return array(
            "status" => "SUCCESS",
            "detailedStatus" => 'Updated user info ' . $infoUpdated
        );
    }
    
}
