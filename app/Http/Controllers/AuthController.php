<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AuthenticateUser;
use App\UserRepository;
use Illuminate\Http\Request;

use Auth;
use Input;
use App\User;

use App\LaravelFacebookRedirectLoginHelper;
use App\Library\JsonResponseHelper;

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
        $user->save();
        
        


        // update name if we have it
        if (Input::has('facebookName'))
        {
            // ensure value isn't null
            if ($facebookName == null)
            {
                return $jsonHelper->failJson("Invalid name");
            }

            $user->name = $facebookName;
        }
        
        // set facebook access token
        if (Input::has('facebookAccessToken'))
        {
            
            // ensure value isn't null
            if ($facebookAccessToken == null)
            {
                return $jsonHelper->failJson("Invalid access token");
            }
            $user->fb_token = $facebookAccessToken;
        }
        
        // save user
        $user->save();
        
        
        // log the user in to the system;
        Auth::login($user);
        
        
        return $jsonHelper->succeedJson('Updated user info');
    }
    
}
