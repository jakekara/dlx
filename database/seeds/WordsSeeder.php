<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Word;

class WordsSeeder extends Seeder {
    
    public function run()
    {
        $word = new Word();
        $word->game_id = 1;
        $word->word = 'jake';
        $word->save();   
    }
}