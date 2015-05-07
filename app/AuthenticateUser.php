<?php namespace App;

use Laravel\Socialite\Contracts\Factory as Socialite;
use Illuminate\Contracts\Auth\Guard;
use App\Repositories\UserRepository;

class AuthenticateUser {
    
    private $users;
    private $auth;
    private $socialite;
    
    public function __construct(UserRepository $users, Socialite $socialite, Guard $auth)
    {
        $this->users = $users;
        $this->socialite = $socialite;
        $this->auth = $auth;
    }
    
    public function execute($hasCode, $listener)
    {
        if (! $hasCode)
        {
          return $this->getAuthorizationFirst();
        }
        
        $user = $this->users->findByUsernameOrCreate($this->getFacebookUser());
        $this->auth->login($user,true);
        
        return $listener->userHasLoggedIn($user);
        
    }
    
    
    private function getFacebookUser()
    {
        return $this->socialite->driver('facebook')->user();
    }

    public function getAuthorizationFirst()
    {
        return $this->socialite->driver('facebook')->redirect();
    }
    
    
    public function loginUserToApp()
    {
    }
    
}
?>