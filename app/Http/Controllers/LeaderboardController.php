<?php namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Routing\Controllers;
use DB;
use Socialize;
use App\Game;

class LeaderboardController extends Controller
{
    public function index()
    {
        // get top 10 games
        $games = Game::orderBy('score', 'desc')->take(5)->get();
        
        return view('home.leaderboard', array(
            'games'=>$games
            // , 'message'=>$user = Socialize::with('facebook')->user()->name
        ));
    }
}