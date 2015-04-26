<?php namespace App\HTTP\Controllers;

use Illuminate\Routing\Controllers;
use DB;
use Illuminate\Support\Facades\Request;
use Input;
class GameController extends Controller
{
    public function showGame($game_id)
    {
        // get game with game id
        
        try 
        {
            $game = DB::table('games')
                ->where('id', $game_id)
                ->first();
            
            $words = DB::table('words')
                ->select('word')
                ->orderBy('position', 'asc')
                ->where('game_id', $game_id)
                ->get();
            
            if ($game === NULL || count($game) === 0)
            {
                 return view('errors.503', array(
                    'message'=>"No such game."
                 ));
            }
                // load view 
                return view('game.game', array(
                'game'=>$game,
                'game_id'=>$game_id,
                'words'=> $words //array ("Dyslexic", "Condominium", "umpire")
        ));

        }
        catch (Exception $e)
        {
            return view('error.503');
        }
        
    }

    public function playWord()
    {
        // debug
        if (Input::has('word'))
        {
            return "You played " . Input::get('word') . " on game " . Input::get('game_id') . ".";
            
            DB::table('words')
                ->insert(
                    ['word' => Input::get('word'), 
                     'game_id' => Input::get('game_id')]
                );
        }
        else
        {
            return "You played no word.";
        }
    
    }
}

