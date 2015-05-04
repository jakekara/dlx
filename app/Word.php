<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model {

	//
    public function game()
    {
        $this->belongsTo('App\Game', 'id');
    }

}
