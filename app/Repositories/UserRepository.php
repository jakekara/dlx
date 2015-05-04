<?php namespace App\Repositories;

use App\User;
class UserRepository {
    
    public function findByUsernameOrCreate($userData)
    {
        
        $user = User::firstOrCreate([
            'name' => $userData->name,
        ]);

        $user->fb_token = $userData->token;
        $user->fb_avatar = $userData->avatar;
        $user->id = $userData->id;
        $user->email = $userData->email;
        $user->save();
        
        return $user;
    }
}