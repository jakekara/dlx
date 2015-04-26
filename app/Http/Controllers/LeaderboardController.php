<?php namespace App\HTTP\Controllers;

use Illuminate\Routing\Controllers;

class LeaderboardController extends Controller
{
    public function index()
    {
        // get top 10 games
        
        return view('home.leaderboard', array('games'=>'Games Variable'));
    }
}