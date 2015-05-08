<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Http\Middleware;

Route::get('/teapot', function()
{
    return 'I am a teapot';
});

/*
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);*/


Route::get('logout', function()
{
    Auth::logout();
    return redirect('/');
});

Route::get('login/status', function(){
    if (Auth::check())
    {
        return "Logged in as " . $Auth::user()->name;
    }
    else
    {
        return "Not logged in.";
    }
});


//Route::get('login', 'AuthController@login');
Route::match(['get', 'post'], '/', 'HomeController@goHome');
Route::match(['get', 'post'], 'leaderboard', 'LeaderboardController@index');

Route::get('facebook/login', 'AuthController@facebookLogin');
Route::post('facebook/setAccessToken', 'AuthController@setFacebookAccessToken');
Route::post('facebook/updateUser', 'AuthController@updateFacebookUser');

Route::group(['middleware' => ['App\Http\Middleware\checker']], function()
{
    Route::get('home', 'HomeController@goHome');

    // game play
    Route::post('game/all', 'GameController@getAllGameDataJson');
    Route::post('game/turn', 'GameController@getTurnJson');
    Route::get('game/{game_id}', "GameController@showGame");
    Route::get('games', "HomeController@games");
    
    // game management
    Route::post('game/new', 'GameController@startNewGame');
    Route::post('game/playWord', "GameController@playWordAjax");
    Route::post('game/quit/{game_id}', "GameController@quitGame");
    Route::post('game/invite', "GameController@sendInvitation");
    Route::post('game/join', "GameController@requestToJoin");
    Route::post('game/accept/invitation', "GameController@acceptInvitation");
    Route::post('game/accept/request', "GameController@acceptRequest");
    Route::post('game/reject/invitation', "GameController@rejectInvitation");
    Route::post('game/reject/request', "GameController@rejectRequest");
    
    //invite friend to app
    Route::post('invite/{user_id}', "GameController@inviteToApp");
   
});
