<?php namespace App\HTTP\Controllers;

use Illuminate\Routing\Controllers;

class LeaderboardController extends Controller
{
    public function index()
    {
        // get top 10 games
        $games = DB('games')->get();
        
        return view('home.leaderboard', array('games'=>$games));
    }
}