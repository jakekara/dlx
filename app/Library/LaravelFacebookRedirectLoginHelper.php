<?php namespace App;
/**
    This is necessary to override Facebook sdk4's storing
    session data by directly accessing $_SESSION superglobal
    since laravel abstracts that with Session
    
    most of code via
    https://stackoverflow.com/questions/23501811/new-facebook-sdk-4-throw-exception-about-php-session-active-in-laravel
**/

use Session;
use Input;

use Facebook\FacebookRedirectLoginHelper;

class LaravelFacebookRedirectLoginHelper extends FacebookRedirectLoginHelper 
{    
    
    
    protected function storeState($state)
    {
        Session::put('fb.state', $state);

    }

    protected function loadState()
    {
        return $this->state =  Session::get('fb.state');
    }


    
    /**
    protected function storeState($state)
    {
        Session::put('state', $state);
    }

    protected function loadState()
    {
    $this->state = Session::get('state');
    return $this->state;
    }


    protected function isValidRedirect()
    {
        $savedState = $this->loadState();
        if (!$this->getCode() || !isset($_GET['state']))
        {
            return false;
        }
        
        $givenState = $_GET['state'];
        $savedLen = mb_strlen($savedState);
        $givenLen = mb_strlen($givenState);
        
        if ($savedLen !== $givenLen) 
        {
            return false;
        }
        
        $result = 0;
        for ($i = 0; $i < $savedLen; $i++) 
        {
            $result |= ord($savedState[$i]) ^ ord($givenState[$i]);
        }
        
        return $result === 0;
    }



    protected function getCode()
    {
        return Input::has('code') ? Input::get('code') : null;
    }


    //Fix for state value from Auth redirect not equal to session stored state value
    //Get FacebookSession via User access token from code
    public function getAccessTokenDetails($app_id,$app_secret,$redirect_url,$code)
    {

        $token_url = "https://graph.facebook.com/oauth/access_token?"
          . "client_id=" . $app_id . "&redirect_uri=" . $redirect_url
          . "&client_secret=" . $app_secret . "&code=" . $code;

        $response = file_get_contents($token_url);
        $params = null;
        parse_str($response, $params);

        return $params;
    }
    **/

}