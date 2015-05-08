<?php namespace App\Library;

use App\AppInvite;
/**
    Use for facebook interactions for the current
    logged in user
**/

use Auth;
use Input;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
class FacebookHelper
{
    /** getSession or return NULL **/
    
    public function getSession()
    {
        
        // ensure authentication
        if (!Auth::check())
        {
           
            return null;
        }
        
        
        // check database for token
        $token = Auth::user()->fb_token;
        
        // if none found, redirect
        if (!$token)
        {
            return null;
        }
        
        return $this->getSessionWithToken($token);
    }
    
    /** get session or return NULL **/
    
    public function getSessionWithToken($token)
    {
        
        // try logging in with token
        FacebookSession::setDefaultApplication(env('FB_APPID'), env('FB_APPSECRET'));
        $session = new FacebookSession($token);

        // test for valid session by issing a request
        try 
        {
            $request = new FacebookRequest(
                $session,
                'GET',
                '/me'
                );

            $response = $request->execute();
            $graphObject = $response->getGraphObject();
        }
        catch (\Exception $e)
        {
            return null;
        }
        
        return $session;
    }
    
    /** 
        get friends who are not already playing the game 
    **/
    public function getFriends()
    {
        // get a session or fail
        $session = $this->getSession();
        
        if ($session == null)
        {
            return null;
        }

        try 
        {
            
            $invitedFriends = AppInvite::all();
            
            $excludes = "[";
            if ($invitedFriends != null)
            {
                foreach ($invitedFriends as $invitee)
                {
                    $excludes .= '"' . $invitee->facebook_id . '",';
                }
            }
            $excludes .= "]";
            
            $request = new FacebookRequest(
                $session,
                'GET',
                '/me/invitable_friends?excluded_ids=' . $excludes
                );

            $response = $request->execute();
            $graphObject = $response->getGraphObject();

            return $graphObject;
        }
        catch (FacebookRequestException $e)
        {
            return null;
        }
    }
    
    /** 
        get friends who are playing the game 
    **/
    public function getGameFriends()
    {
        // get a session or fail
        $session = $this->getSession();
        
        if ($session == null)
        {
            return null;
        }

        try 
        {
            $request = new FacebookRequest(
                $session,
                'GET',
                '/me/friends'
                );

            $response = $request->execute();
            $graphObject = $response->getGraphObject();

            return $graphObject;
        }
        catch (FacebookRequestException $e)
        {
            return null;
        }
    }
    
    // specified by facebook
    public function base64_url_decode($input) 
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    /**
        verify signed_request
        code adapted from https://developers.facebook.com/docs/facebook-login/using-login-with-games
    **/
    public function verifySignedRequest($signed_request)
    {
        
        // check that we have it
        if (! $signed_request)
        {
            return null;
        }

        list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

        $secret = env('FB_APPSECRET');
        
        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);

        // confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
               
            error_log('Bad Signed JSON signature!');
            return null;
        }
        return $data;
    }
}
?>
