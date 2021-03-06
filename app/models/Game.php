<?php namespace App;

/**
    Game model, extends interface
    for database via Eloquent ORM
    
**/

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Http\Controllers\GameController;
class Game extends Model 
{
    /**
     creat relationship to words 
	**/
    public function words()
    {
        return $this->hasMany('App\Word', 'game_id');
    }
    
    /**
        return invitations from invitations 
    **/
    public function invites()
    {
        return $this->hasMany('App\GameInvite', 'game_id');
    }
    
    /**
        return requests from requests table
    **/
    public function requests()
    {
        return $this->hasMany('App\GameRequest', 'game_id');
    }

    /**
        get an ordered object of words records
    **/
    public function getWordsInOrder()
    {
        return $this->words->sortBy(function($role){
            return $role->position;
        });;
        
    }
    
    /**
        get full glom
    **/
    public function glom()
    {
        $wordObject = $this->getWordsInOrder();
        $glomString = "";
        
        foreach ($wordObject as $word)
        {
            // determine how much to overlap
            $overlap = Game::canAppend($glomString, $word->word);
            
            if ($overlap > 0)
            {
                
                $glomString = substr($glomString, 0, strlen($glomString) - $overlap);
            }
            
            $glomString .= $word->word;
        }
        
        return $glomString;
    }
    
    /**
        get the first n letters of glom, all
        glommed together
    **/
    public function glomPreview($limit = 50)
    {
        return substr($this->glom(), 0, $limit);
    }

    // get an ordered array of words
    public function getWordsInOrderAsArray()
    {
        $words = $this->getWordsInOrder();
        $keys = array_keys($words->toArray());
        $array = [];
        
        for ($i = 0; $i < count($keys); $i++)
        {
            $array[$i] = $words[$keys[$i]]->word;
        }

        return $array;

    }
    
    // get an array containing players who
    // have requested to join a game
    public function getRequests()
    {   
        $rawFriends = $this->user_requests;
        $requests = $this->colonListAsArray($rawFriends);
        
        return $requests;
    }
    
    // get an array containing players who
    // have been invited to play 
    public function getInvites()
    {
        $rawFriends = $this->user_invites;
        
        $invites = $this->colonListAsArray($rawFriends);
        
        
        return $invites;
    }
    
    // get an array of current players
    public function getPlayersArray()
    {
        $rawFriends = $this->players;
        return $this->colonListAsArray($rawFriends);
    }
    
    // return a cleaned up colon-separated list as Json
    public function colonListAsJson($rawPlayers)
    {
        
        return json_encode($this->colonListAsArray($rawPlayers));
    }
    
    // clean up a double-colon separated list
    // of players and return it as Json
    public function colonListAsArray($rawPlayers)
    {
        // if empty, return null
        if (strlen($rawPlayers) < 1)
        {
            return array();
        }
        
        // convert :: to :
        $rawPlayers = str_replace("::", ":", $rawPlayers);
    
        // delete leading and trailing ":"
        $length = strlen($rawPlayers);
        $rawPlayers = substr($rawPlayers, 1, $length-2);
            
        // convert to array
        $playersArray = explode(":", $rawPlayers);
        
        // package with names, since right now we only
        // have ids
        $arrayWithNames = [];
        foreach ($playersArray as $playerId)
        {
            $user = User::find($playerId);
            
            // user not in the system
            if ($user == NULL)
            {
                continue;
            }
            
            $playerArrayWithName = array(
                "id"=>$playerId,
                "name"=>$user->name,
                "first_name"=>$user->first_name
            );
            
            array_push($arrayWithNames, $playerArrayWithName);
        }
        return $arrayWithNames;
    }
    
    /**
        Quit game
    **/
    public function quit()
    {
        if (Auth::check())
        {
            if ($this->isPlayer(Auth::user()->id))
            {
                $this->players = str_replace(Auth::user()->paddedId, "", $this->players);
                return "success";
            }
        }
        
        return null;
    }
    
    /**
        reject an invitation
    **/
    public function rejectInvite()
    {
        // ensure authentication
        if (!Auth::check())
        {
            return null;
        }
        
        // ensure logged in user is a player
        if ($this->isInvited(Auth::user()->id))
        {
            // delete player invitation
            $this->user_invites = str_replace(Auth::user()->paddedId(), "", $this->user_invites);
            $this->save();
            return "success";
        }
        
        return null;

    }
    
    
    /**
        reject a join request
    **/
    public function rejectRequest($playerId)
    {
        // ensure authentication
        if (!Auth::check())
        {
            return null;
        }
        
        // ensure logged in user is a player
        if ($this->isPlayer(Auth::user()->id))
        {
            // delete player request
            $this->user_requests = str_replace(":" . $playerId . ":", "", $this->user_requests);
            $this->save();
            return "success";
        }
        
        return null;
    }
    
    /**
        determine if player id is in a given list,
        since players, user_invites and user_requests
        all use colon-padded ids in the form of
        :id1:id2:id3:
    **/
    private function isInList($playerId, $list)
    {
        if (strpos($list, ":" . $playerId . ":") !== false)
        {
            return true;
        }
        
        return false;
    }

    
    /**
        determine if user is in invites list
    **/
    public function isInvited($playerId)
    {
        $list = $this->user_invites;
        return $this->isInList($playerId, $list);
    }
   
    
    /**
        determine if user is in requests list
    **/
    public function isRequesting($playerId)
    {
        $list = $this->user_requests;
        return $this->isInList($playerId, $list);
    }
    
    
    /**
        determine if user is in players list
    **/
    public function isPlayer($playerId)
    {
        $list = $this->players;
        return $this->isInList($playerId, $list);
    }
    
    
    /**
        add a request to join game
    **/
    public function addRequest()
    {
        // ensure authentication
        if (!Auth::check())
        {
            return null;
        }
        
        // make sure there isn't already
        // a request in, and the user is
        // not already a player
        if ($this->isPlayer(Auth::user()->id))
        {
            return null;
        }
        if ($this->isRequesting(Auth::user()->id))
        {
            return null;
        }
        
        $this->user_requests .= ":" . Auth::user()->id . ":";
        $this->save();
        
        return "success";
        
    }

    
    // invite someone to the game
    public function addInvite($playerId)
    {
        // ensure authentication
        if (!Auth::check())
        {
            return null;
        }
        
        $invites = $this->user_invites;

        // Only proceed to invite user
        // if not already invited
        if ($this->isInvited($playerId) ||
            $this->isPlayer($playerId))
        {
            return null;
        }
        
        $this->user_invites = $this->user_invites . ":" . $playerId . ":";
        $this->save();
        return "success";
    }
    
    // iterate whose turn it is
    public function nextTurn()
    {
        // get user id string
        $turn = strval($this->turn);
        
        $players = $this->getPlayersArray();
        
        for ($i = 0; $i < count($players); $i++)
        {
            $player = $players[$i];
            if ($player["id"] == $turn)
            {
                // set next player (wrap to start if at end of list)
                // to "turn"
                $nextPlayer = $players[($i+1) % count($players)];
                $this->turn = $nextPlayer["id"];
                $this->save();
            }
        }
        
    }
    
    // join a game you've been invited to
    public function acceptInvite()
    {
        // ensure authentication
        if (!Auth::check())
        {
            return null;
        }
        
        // if player ID is in the list of invited people
        // add them to the players list
        // and remove them from the invited list
        
        $invitedPlayers = $this->user_invites;
        $paddedId = ":" . Auth::user()->id . ":";
        
        
        if (!$this->isPlayer(Auth::user()->id)) 
        {
            // remove from invited players
            $this->user_invites = str_replace($paddedId, "", $this->user_invites);
            
            $this->players = $this->players . $paddedId;
            $this->save();
            
            return "success";

        }
        
        return null;
        
    }
    
    // accept a user's request to join a game
    public function acceptRequest($playerId)
    {
        if (!Auth::check())
        {
            return null;
        }
        
        // make sure the logged in user has access to the game
        if (!$this->isPlayer(Auth::user()->id))
        {
            return null;
        }
        
        // ensure player has requested to join
        if (!$this->isRequesting($playerId))
        {
            return null;
        }
        
        $paddedPlayerId = ":".$playerId.":";

        // if already a player, drop from request list
        if ($this->isPlayer($playerId))
        {
            $this->user_requests = str_replace($paddedPlayerId, "", $this->user_requests);
            $this->save();
            return null;
        }
            
        // good to go
        $this->user_requests = str_replace($paddedPlayerId, "", $this->user_requests);
        $this->players = $this->players . $paddedPlayerId;
        $this->save();
        return "success";
    }
    

        
    /**
        determine if two words can be added together
        by appending $second on to $first
    **/
    public static function canAppend($first, $second)
    {
        // determine the max number of letters that could overlap
        // requiring at least two letters to be overlapped
        // and two letters to be added
        
        /*
        $maxOverlap = min(strlen($first), strlen($second)) - 2;
        
        if ($maxOverlap < 2)
        {
            // not long enough
            return -2;
        }
        
        for ($i = 2; $i <= $maxOverlap; $i++)
        */
        for ($i = 2; $i < max(strlen($first), strlen($second)); $i++)
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
        determine if a word can be appended to this game
    **/
    public function canAppendToThisGame($word)
    {
        return Game::canAppend($this->glom(), $word);
    }   
    
    /**
        determine if a word can be prepended to this game
    **/
    public function canPrependToThisGame($word)
    {
        return Game::canAppend($word, $this->glom());
    }
}

