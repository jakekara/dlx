<?php namespace App\Http\Controllers;

use App\Game;
use App\Word;
//use Illuminate\Routing\Controllers;
use Illuminate\Support\Facades\Request;
use Input;
use DB;
use Auth;
use App\User;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\Graphuser;
use Facebook\FacebookRedirectLoginHelper;

class GameController extends Controller
{
    
    /**
        deprecated
    **
    public function getGame($game_id)
    {
        try 
        {
            $game = Gamme::find($game_id);

                        if ($game === NULL || count($game) === 0)
            {
                return "no such game";
            }
            
            
            
            return $game;
        }
        catch (Exception $e)
        {
            return "getGame exception";
        }
    } **/
    
    
    /**
        Show a requested game as a player or spectator
    **/
    public function showGame($game_id)
    {
        FacebookSession::setDefaultApplication(env('FB_APPID'), env('FB_APPSECRET'));

        $session = new FacebookSession(Auth::user()->fb_token);
        $request = new FacebookRequest(
          $session,
          'GET',
          '/'.Auth::user()->id.'/invitable_friends'
        );
        $response = $request->execute();
        $graphObject = $response->getGraphObject();
        
        dd($response);
        
        try 
        {
            $game = Game::find($game_id);
            $words = $game->getWordsInOrder();
            $players = $game->players;
            
            if ($game === NULL || count($game) === 0)
            {
                 return view('errors.503', array(
                    'message'=>"No such game."
                 ));
            }
            
            // if user is autheticated, and user's id
            // is in list of active players,
            // load the game as 
            // load view 
            if (Auth::check() && strstr($players, ":" . Auth::user()->id .":"))
            {
                return view('game.player', array(
                    'game'=>$game,
                    'game_id'=>$game_id,
                    'words'=> $words,
                    'message'=>''
                                        
                ));

            }
            // otherwise, load spectator view of game
            else 
            {
                return view ('game.spectator', array(
                    'words'=> $words
                ));
            }

        }
        catch (Exception $e)
        {
            return view('error.503');
        }
        
        
    }
    
    /**
        Add a word to the database
    **/
    private function addWord($position, $overlap)
    {        
        // debug
        if (!Input::has('word'))
        {
            return "You played no word.";
        }
        else
        {
            
            $points = pow($overlap, 10);
            
            // update score
            $game = Game::find(Input::get('game_id'));
            $game->score += $points;
            $game->save();
                               
            // add the word
            $newWord = new Word;
            $newWord->word =  Input::get('word');
            $newWord->game_id = Input::get('game_id');
            $newWord->position = $position;
            $newWord->save();
            
            // return to the game
            $this->showGame($game->id);

        }
    }
    
    /** Play a word and turn word list **/
    public function playWordAjax()
    {
                
        // determine if game exists
        $game = Game::find(Input::get('game_id'));
        if ( $game == NULL)
        {
            return json_encode(array(
                "status" => "ERROR",
                "detailedStatus" => "Game does not exist.",
            ));

        }
        
        // get word list sorted by position
        /*
        $words = $game->words->sortBy(function($role)
        {
            return $role->position;
        });;
        */
        $words = $game->getWordsInOrder();
        
        // determine if there is an input word
        $word = Input::get('word');
        
        if ($word == NULL)
        {
            return json_encode(array(
                "status" => "FAILURE",
                "detailedStatus" => "No word supplied."
            ));
        }
        
        else if (!ctype_alpha($word))
        {
            return json_encode(array(
                "status" => "FAILURE",
                "detailedStatus" => "Word must contain only letters."
            ));
        }
        
        // determine if user is authenticated
        if (!Auth::check())
        {
            return json_encode(array(
                "status" => "FAILURE",
                "detailedStatus" => "You are not logged in."
            ));
        }
        
        // determine if it's the user's turn
        else if (!Game::find(Input::get('game_id'))->turn == Auth::user()->id)
        {
            return json_encode(array(
                "status" => "FAILURE",
                "detailedStatus" => "It's not your turn.",
            ));
        }
        
        // determine if there are any words played yet
        if (count($words) == 0)
        {
            // add word
            return "Add first word";
        }
        
        
        // use array_keys so we can sort array and iterate
        // through using integer index, not word id
        $keys = array_keys($words->toArray());
        $firstWord = $words[$keys[0]];
        $lastWord = $words[$keys[(count($words) - 1)]];
        
        // determine if we can add the word at the beginning
        $overlap = GameController::canAppend($word, $firstWord->word);
        if ($overlap >= 2)
        {
            // add word to beginning
            $position = $firstWord->position - 1;
            $this->addWord($position, $overlap);
            $game = Game::find(Input::get('game_id'));

            // return json
            return json_encode(array(
                "status" => "SUCCESS",
                "detailedStatus" => "Played word '". Input::get('word') ."' at beginning."
            ), JSON_PRETTY_PRINT);
        }
        else
        {
            $overlap = GameController::canAppend($lastWord->word, $word);
            
            if ($overlap >= 2)
            {
                $position = $lastWord->position + 1;
                $this->addWord($position, $overlap);
                $game = Game::find(Input::get('game_id'));

                return json_encode(array(
                    "status" => "SUCCESS",
                    "detailedStatus" => "Played word '". Input::get('word') ."' at end."
                ), JSON_PRETTY_PRINT);

            }
            else
            {        
                return json_encode(array(
                    "status" => "FAILURE",
                    "detailedStatus" => "Could not append word '". Input::get('word') 
                    ."' at beginning or end.",
                ), JSON_PRETTY_PRINT);

            }
        }
 
        
        return json_encode(array(
            "status" => "FAILURE",
            "detailedStatus" => "Unkown failure playing word '". Input::get('word') ."'.",
            "overlap" => $overlap,
            "wordList" => $game->getWordsInOrderAsArray()
        ), JSON_PRETTY_PRINT);
    }
    
    // get glom
    public function getGlomAjax()
    {
        
    }
    
    /**
        determine if two words can be added together
        by appending $second on to first
    **/
    
    protected function canAppend($first, $second)
    {
        // determine the max number of letters that could overlap
        // requiring at least two letters to be overlapped
        // and two letters to be added
        
        $maxOverlap = min(strlen($first), strlen($second)) - 2;
        if ($maxOverlap < 2)
        {
            // not long enough
            return -2;
        }
        
        for ($i = 2; $i <= $maxOverlap; $i++)
        {
            // compare first i letters of $second
            // and last i letters of $first
            

            
            $endOfFirst = substr($first, strlen($first) - $i, $i);
            $startOfSecond = substr($second, 0, $i);
                        
            if ( $endOfFirst == $startOfSecond)
            {
                return $i;
            }
        }
        
        return -1;
    }
    
    /** 
     return whose turn it is via json object 
    **/
    public function getTurnJson()
    {
        
        if (Auth::check())
        {
            $game_id = Input::get('game_id');
            if ($game_id == NULL)
            {
                return json_encode(array(
                    'status' => 'FAILURE'
                ), JSON_PRETTY_PRINT);
            }
            $user_id = Input::get('user_id');
            if ($user_id == NULL)
            {
                return json_encode(array(
                    'status' => 'FAILURE'
                ), JSON_PRETTY_PRINT);
            }
            else 
            {
                return json_encode(array(
                    "status" => "SUCCESS",
                    "turnName" => User::find(Game::find(Input::get('game_id'))->turn)->name,
                    "turn" => Game::find(Input::get('game_id'))->turn

                ), JSON_PRETTY_PRINT);
            }
        }
        else 
        {
            return json_encode(array(
                'status' => 'FAILURE'
            ), JSON_PRETTY_PRINT);
        }
    }
    
    /**
        functions to get JSON data via ajax requests
    **/    
    public function getWordsJson($game_id)
    {
        $game = Word::where('game_id' , '=', $game_id);
    }
    
    /**
        return Game data via json
    **/
    public function getAllGameDataJson()
    {
        // get points
        // get wordList
        // get turn
        // get turnName - the real name of player who's up
        // get userList - real names
        $game = Game::find(Input::get('game_id'));
        
        
        
        return json_encode(array(
                'status' => 'SUCCESS',
                'wordList' => $game->getWordsInOrderAsArray(),
                'score' => $game->score,
                'turn' => $game->turn,
                'turnName' => User::find($game->turn)->name,
                'userList' => $game->active
            ), JSON_PRETTY_PRINT);
        
    }
}

