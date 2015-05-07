<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Word;

class WordsSeeder extends Seeder {
    
    public function run()
    {
        $word = Word::firstOrCreate(array('game_id'=>1, 'word'=>'jake'));
        $word->save();
    }
}