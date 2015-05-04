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


Route::get('login', 'AuthController@login');
Route::get('/', 'HomeController@goHome');
Route::get('home', 'HomeController@goHome');
Route::match(['get', 'post'], 'leaderboard', 'LeaderboardController@index');

Route::group(['middleware' => ['App\Http\Middleware\checker']], function()
{
    Route::post('game/all', 'GameController@getAllGameDataJson');
    Route::post('game/turn', 'GameController@getTurnJson');
    Route::post('game/playWord', "GameController@playWordAjax");
    Route::get('game/{game_id}', "GameController@showGame");
    Route::get('games', "HomeController@games");
   
});
