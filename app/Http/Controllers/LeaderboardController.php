<?php namespace App\HTTP\Controllers;

use Illuminate\Routing\Controllers;
use DB;
use Socialize;
class LeaderboardController extends Controller
{
    public function index()
    {
        // get top 10 games
        $games = DB::table('games')->get();
        
        return view('home.leaderboard', array(
            'games'=>$games
            // , 'message'=>$user = Socialize::with('facebook')->user()->name
        ));
    }
}