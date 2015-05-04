<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {

    
	public function words()
    {
        return $this->hasMany('App\Word', 'game_id');
    }

    // get an ordered object of words records
    public function getWordsInOrder()
    {
        return $this->words->sortBy(function($role){
            return $role->position;
        });;
        
    }

    // get an ordered array
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
}
