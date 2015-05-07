<?php namespace App\Repositories;

/**
    Adapted from tutorial on socialite, which I
    phased out to use direct facebook php sdk4
    because I needed more than authentication.
    I also needed access to friends so player can
    invite their friends.
**/

use App\User;
class UserRepository {
    
    public function findByUsernameOrCreate($userData)
    {
        
        $user = User::firstOrCreate([
            'id' => $userData->id,
        ]);

        $user->fb_token = $userData->token;
        $user->fb_avatar = $userData->avatar;
        $user->name = $userData->name;
        $user->email = $userData->email;
        $user->save();
        
        return $user;
    }
}