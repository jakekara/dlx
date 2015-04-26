<?php namespace App\HTTP\Controllers;

use Illuminate\Routing\Controllers;

class LeaderboardController extends Controller
{
    public function index()
    {
            return view('home.Leaderboard');
    }
}