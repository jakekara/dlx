<?php namespace App\Http\Controllers;

/**
    GameController.php
    by Jake Kara
    jkara@g.harvard.edu
    
    Handle all viewing and interacting with games

**/

use App\Game;
use App\Word;
use App\AppInvite;
use App\DictionaryWord;
//use Illuminate\Routing\Controllers;
use Illuminate\Support\Facades\Request;
use Input;
use DB;
use Auth;
use App\User;

use App\Library\JsonResponseHelper;
use App\Library\FacebookHelper;

class GameController extends Controller
{
    /**
        Remove players from friend list
    **/
    public function removePlayersFromList($players, $list)
    {
       
        foreach($list as $key => $listee)
        {
            foreach($players as $player)
            {
                if ($listee->id == $player["id"])
                {
                    unset($list[$key]);
                }
            }
        }

        return $list;
    }
    
    /**
        Show a requested game as a player or spectator
    **/
    public function showGame($game_id)
    {
        // find game
        $game = Game::find($game_id);
    
        // load friends list to populate "invite" field
        $fbHelper = new FacebookHelper;
        $friendList = $fbHelper->getFriends();
        $gameFriendList = $fbHelper->getGameFriends();
        $currentPlayers = $game->getPlayersArray();
        
        if ($gameFriendList != null)
        {
            $gameFriendList = $gameFriendList->getProperty('data');
            if ($gameFriendList != null)
            {
                $gameFriendList = $gameFriendList->asArray();
                
                // remove all current players from list 
                $gameFriendList = $this->removePlayersFromList($currentPlayers, $gameFriendList);
            }
            else
            {
                // at least send an empty array
                $gameFriendList = array();
                
                
            }
        }
        
        if ($friendList != null)
        {
            $friendList = $friendList->getProperty('data');
            if ($friendList != null)
            {
                $friendList = $friendList->asArray();
                
                // remove all current players
                $friendList = $this->removePlayersFromList($currentPlayers, $friendList);
            }
            else
            {
                //at least send an empty array
                $friendList = array();
            }
        }
        try 
        {
            $wordList = $game->getWordsInOrder();
            $playersList = $game->getPlayersArray();
            
            if ($game === NULL || count($game) === 0)
            {
                 return view('errors.503', array(
                    'message'=>"No such game."
                 ));
            }
            
            // get invited friends
            $invitedFriends = $game->getInvites();
            $requestedFriends = $game->getRequests();
            // if user is autheticated, and user's id
            // is in list of active players,
            // load the game as a player
            if (Auth::check() && $game->isPlayer(Auth::user()->id)) //strstr($players, ":" . Auth::user()->id .":"))
            {
                
                return view('user.game', array(
                    'game'=>$game,
                    'game_id'=>$game_id,
                    'wordList'=> $wordList,
                    'message'=>'',
                    'friendList'=>$friendList,
                    'appFriendsList'=>$gameFriendList,
                    'joinRequestsList' =>   $requestedFriends,
                    'invitedFriendsList' => $invitedFriends,
                    'playersList' => $playersList
                    
                ));

            }
            // otherwise, load spectator view of game
            else 
            {
                $invited = 'NO';
                
                /*
                // determine if youre on the invite list
                foreach (json_decode($invitedFriends) as $invitee)
                {
                    if ($invitee->id == Auth::user()->id)
                    {
                        $invited = 'YES';
                        break;
                    }
                }
                                // determine if youre on the invite list
                foreach (json_decode($invitedFriends) as $requestee)
                {
                    if ($requestee->id == Auth::user()->id)
                    {
                        $requested = 'YES';
                        break;
                    }
                }
                */
                
                // determine if user has requested to join the game
                // or has been invited.
                
                $requested = 'NO';                
                if ($game->isRequesting(Auth::user()->id))
                {
                    $requested = "YES";
                }
                $invited = 'NO';
                
                if ($game->isInvited(Auth::user()->id))
                {
                    $invited = "YES";
                }
                $wordList = $game->getWordsInOrderAsArray();

                return view ('guest.game', array(   
                    'wordList' => $wordList,
                    'game_id'=>$game_id,
                    'invited' => $invited,
                    'requested' => $requested
                ));
            }

        }
        catch (Exception $e)
        {
            return view('error.503');
        }
        
        
    }
    
    /**
        Reject a request to join a game
    **/
    public function rejectRequest()
    {
        
        $jsonHelper = new JsonResponseHelper;
        if (!Input::has('game_id') || ! Input::has('user_id'))
        {
            return $jsonHelper->failJson("Bad input");
        }
        
        $game = Game::find(Input::get('game_id'));
        if ($game == NULL)
        {
            return $jsonHelper->failJson("No game found.");
        }
        if ($game->rejectRequest(Input::get('user_id')))
        {
            return $jsonHelper->succeedJson("request rejected.");
        }
        $jsonHelper->failJson("Failed to reject request.");
    }
    
    
    /**
        Reject an invitation to join a game
    **/
    public function rejectInvitation()
    {
        $jsonHelper = new JsonResponseHelper;

        if (!Input::has('game_id'))
        {
            return $jsonHelper->failJson("Bad input");
        }
        
        $game = Game::find(Input::get('game_id'));
        if ($game == NULL)
        {
            return $jsonHelper->failJson("No game found.");
        }
        
        if ($game->rejectInvite())
        {
            return $jsonHelper->succeedJson("Rejected invitation");
        }
        
        return $jsonHelper->failJson("Failed to reject invitation");
    }
    
    /**
        Add a word to the game
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
            
            $points = $overlap * 100;
            $points += 10 * (strlen(Input::get('word')));
            
            // update score
            $game = Game::find(Input::get('game_id'));
            $game->score += $points;
            $game->save();
                               
            // add the word
            $newWord = new Word;
            $newWord->word =  strtolower(Input::get('word'));
            $newWord->game_id = Input::get('game_id');
            $newWord->position = $position;
            $newWord->save();
            
            // return to the game
            $this->showGame($game->id);

        }
    }
    
    /** 
        Play a word and turn word list 
    **/
    public function playWordAjax()
    {
                
        $jsonHelper = new JsonResponseHelper;
        
        // determine if game exists
        $game = Game::find(Input::get('game_id'));
        if ( $game == NULL)
        {
            return array(
                "status" => "ERROR",
                "detailedStatus" => "Game does not exist.",
            );

        }
        
        // get word list sorted by position
        $words = $game->getWordsInOrder();
        
        // determine if there is an input word
        $word = Input::get('word');
        
        if ($word == NULL)
        {
            return array(
                "status" => "FAILURE",
                "detailedStatus" => "No word supplied."
            );
        }
        
        else if (!ctype_alpha($word))
        {
            return array(
                "status" => "FAILURE",
                "detailedStatus" => "Word must contain only letters."
            );
        }
    
        
        // determine if user is authenticated
        if (!Auth::check())
        {
            return array(
                "status" => "FAILURE",
                "detailedStatus" => "You are not logged in."
            );
        }
        
         /**
            --------------------------------------------------
            TAKING TURNS IS DISABLED. PLAY AS MUCH AS YOU LIKE
            --------------------------------------------------
            // I decided there's really no need to have users take turns
            // adding words. If you want to add five or six words in one
            // sitting, go for it, and you won't get hung up waiting for 
            // a friend to make a move.    
            // if we got a new turn, 
            // update whose turn it is
            // and if button is active or not
        
        // determine if it's the user's turn
        else if (!Game::find(Input::get('game_id'))->turn == Auth::user()->id)
        {
            return array(
                "status" => "FAILURE",
                "detailedStatus" => "It's not your turn.",
            );
        }
        **/
        
        // determine if it's in the dictionary
        $inDictionary = DictionaryWord::where('word', 'LIKE', strtolower($word))->first();
        if ($inDictionary == null)
        {
            return array(
                "status" => "FAILURE",
                "detailedStatus" => "Not a dictionary word."
            );
        }
        
        $duplicate = Word::where('word', '=', $word)
            ->where('game_id', '=', Input::get('game_id'))
            ->first();
        if ($duplicate != null)
        {
            return array(
                "status" => "FAILURE",
                "detailedStatus" => "That word, '" . $duplicate->word . "', has already been used."
            );

        }
                                                   
        // determine if there are any words played yet
        if (count($words) == 0)
        {
            // add word
            $this->addWord(0, 1);
            // go to next player
            $game->nextTurn();
            //return  $jsonHelper->succeedJson("Added first word.");
            return $this->getAllGameData();
        }
        
        
        // use array_keys so we can sort array and iterate
        // through using integer index, not word id
        $keys = array_keys($words->toArray());
        $firstWord = $words[$keys[0]];
        $lastWord = $words[$keys[(count($words) - 1)]];
        
        // determine if we can add the word at the beginning
        // old way - $overlap = GameController::canAppend($word, $firstWord->word);
        $overlap = $game->canPrependToThisGame($word);
        
        if ($overlap >= 2)
        {
            // add word to beginning
            $position = $firstWord->position - 1;
            $this->addWord($position, $overlap);
            // go to next player
            $game->nextTurn();
            $game = Game::find(Input::get('game_id'));
    
            
            return $this->getAllGameData();
            /* return json
            return json_encode(array(
                "status" => "SUCCESS",
                "detailedStatus" => "Played word '". Input::get('word') ."' at beginning."
            ), JSON_PRETTY_PRINT);*/
        }
        else
        {
            // determine if we can prepend the word
            // old way $overlap = GameController::canAppend($lastWord->word, $word);
            $overlap = $game->canAppendToThisGame($word);
            
            if ($overlap >= 2)
            {
                $position = $lastWord->position + 1;
                $this->addWord($position, $overlap);
                // go to next player
                $game->nextTurn();
                $game = Game::find(Input::get('game_id'));
                    
                return $this->getAllGameData();
                /*
                return json_encode(array(
                    "status" => "SUCCESS",
                    "detailedStatus" => "Played word '". Input::get('word') ."' at end."
                ), JSON_PRETTY_PRINT);
                */

            }
            else
            {        
                return array(
                    "status" => "FAILURE",
                    "detailedStatus" => "Could not append word '". Input::get('word') 
                    ."' at beginning or end.",
               );

            }
        }
 
        
        return array(
            "status" => "FAILURE",
            "detailedStatus" => "Unkown failure playing word '". Input::get('word') ."'.",
            "overlap" => $overlap,
            "wordList" => $game->getWordsInOrderAsArray()
        );
    }
    
    // get glom
    public function getGlomAjax()
    {
        
    }
    
  
    /**
        Invite someone to join a game
    **/
    public function sendInvitation()
    {
        $jsonHelper = new JsonResponseHelper;
        $playerId = Input::get('player_id');
        $gameId = Input::get('game_id');
        $game = Game::find($gameId);
        
        if ($game === NULL)
        {
            return $jsonHelper->fail("No game found.");
        }
        
        if ($game->addInvite($playerId))
        {
            return array(
                "status" => "SUCCESS",
                "detailedStatus" => "sent invitation",
                "playerId" => $playerId,
                "playerName" => User::find($playerId)->name
            );
        }
        
        return $jsonHelper->failJson("Failed to send invitation");
    }
    
    /**
        Send a request to join a game
    **/
    public function requestToJoin()
    {
        $jsonHelper = new JsonResponseHelper;
        
        $gameId = Input::get('game_id');
        $game = Game::find($gameId);
        
        if ($game === NULL)
        {
            return $jsonHelper->fail("No game found.");
        }
        
        if ($game->addRequest())
        {
            return $jsonHelper->succeedJson("Sent request to join game.");
        }
        
        return $jsonHelper->failJson("Failed to send request to join game " . $gameId . ".");

    }
    
    /**
        Accept a request to join a game
    **/
    public function acceptRequest ()
    {
        $jsonHelper = new JsonResponseHelper;
        
        if (!Input::has('player_id') || !Input::has('game_id'))
        {
            return $jsonHelper->failJson("Bad input");

        }
        
        $playerId = Input::get('player_id');
        $gameId = Input::get('game_id');
        $game = Game::find($gameId);
        
        if ($game === NULL)
        {
            return $jsonHelper->fail("No game found.");
        }
        
        if ($game->acceptRequest($playerId))
        {
            return $jsonHelper->succeedJson("Accepted request by player to join.");
        }
        
        return $jsonHelper->failJson("Failed to accept player " . $playerId);
        
    }
    
    /**
        Accept an invitation to join a game
    **/
    public function acceptInvitation()
    {
        $jsonHelper = new JsonResponseHelper;
        $gameId = Input::get('game_id');
        $game = Game::find($gameId);
        
        if ($game === NULL)
        {
            return $jsonHelper->fail("No game found.");
        }
        
        if ($game->acceptInvite())
        {
            return $jsonHelper->succeedJson("Accepted invitation");
        }
        
        
        return $jsonHelper->failJson("Failed to accept invitation");
    }
    
    
    /**
        determine if two words can be added together
        by appending $second on to first
    **/
    public static function canAppend($first, $second)
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
        rewrite of getAllGameDataJson
        to phase out unnecessary json
        encoding
    **/
    
    public function getAllGameData()
    {
        $game = Game::find(Input::get('game_id'));
        
        return array(
                'status' => 'SUCCESS',
                'wordList' => $game->getWordsInOrderAsArray(),
                'score' => number_format($game->score),
                'turn' => $game->turn,
                'turnName' => User::find($game->turn)->name,
                'userList' => $game->getPlayersArray(),
                'requestsList' => $game->getRequests(),
                'invitesList' => $game->getInvites()
        );
        
    }
    
    
    /**
        return Game data via json
        
        Phasing out because there's 
        no need to json encode. Now using
        getAllGameData() instead
        
    **
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
        
    }*/
    
    public function getGamesJson()
    {
        if (!Auth::check())
        {
            return json_encode(array(
                'status' => 'FAILURE',
                'detailedStatus' => 'Not logged in.'
            ));

        }
        
        $id = Auth::user()->id;
        
    }
    
    /**
        Start a new game
    **/
    public function startNewGame()
    {
     
        $jsonHelper = new JsonResponseHelper();
        if (!Auth::check())
        {
            $jsonHelper->failJson("Not logged in!");
        }
        
        $newGame = new Game;
        
        $newGame->active = ":" . Auth::user()->id . ":";
        $newGame->players = ":" . Auth::user()->id . ":";
        $newGame->turn = Auth::user()->id ;
        $newGame->score = 0;
        $newGame->save();
        
        return array(
            "status" => "SUCCESS",
            "newGameId" => $newGame->id
        );
        
    }
    
    /**
        Quit a game (remove user from active players list)
        
        TODO - eventually rewrite this to abstract Game model's
        quit() method for better MVC-ness
    **/
    public function quitGame($gameId)
    {
        $jsonHelper = new JsonResponseHelper();
        if (!Auth::check())
        {
            return $jsonHelper->failJson("Not logged in!");
        }
    
        // padd user id colons
        $paddedUserId = ":" . Auth::user()->id . ":";
        
        // load game
        $game = Game::where('players', 'LIKE', "%:" . Auth::user()->id . ":%")
            ->where('id', '=', $gameId)->first();
        
        // fail if we didn't find a game
        if ($game == NULL)
        {
            return $jsonHelper->failJson("No such game.");
        }
        
        // see if the user is a player
        $players = $game->players;
        
        // load active users string
        if (strpos($players, $paddedUserId) === false)
        {
            return "'" . $paddedUserId . "' not in player list: '" . $players . "'";
            // user not in active players list
            return $jsonHelper->failJson("You're not a player." . $game->players);
        }

        // delete player from active player list
        $game->players = str_replace($paddedUserId, "", $game->players);
        $game->save();
        if ($game->players == "" && count($game->words()) > 0)
        {
            $game->delete();
        }
        return array(
            "status" => "SUCCESS",
            "detailedStatus" => "Quit game " . $gameId
        );

        // TO PONDER -- Should games ever be entirely deleted from database?
        // perhaps if they have no words and the only player is the one deleting the game.
    }
    
    
    public function inviteToApp($user_id)
    {
        
        $invitation = AppInvite::where('facebook_id', '=', $user_id)->first();
        if ($invitation == null)
        {
            $invitation = new AppInvite;
            $invitation->facebook_id = $user_id;
            $invitation->save();
            return array(
                'status' => "SUCCESS", 
                'divToDelete' => 'inviteFriendToDyslexiconListItem_' . $user_id,
                'detailedStatus' => 'Friend request sent',
                'friendId' => $user_id,
                'appId' => env('FB_APPID')
            );
        }
        return array("status"=>"FAILURE",
                     "detailedStatus" => "Cannot send request. Friend has already been invited");
    }
    
    /**
        show the non-logged-in game view
    **/
    public function showGameGuest($game_id)
    {

        $game = Game::find($game_id);
        $wordList = $game->getWordsInOrderAsArray();

        return view ('guest.permaGameView', array(
            'wordList' => $wordList
        ));
    }
    
}

