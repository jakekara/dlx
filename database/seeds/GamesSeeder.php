<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Game;

class GamesSeeder extends Seeder {
    
    public function run()
    {
        $game = Game::firstOrCreate(array('id'=>1));
        $game->players = ':1017291602:';
        $game->score = 150000;
        $game->turn = 1017291602;
        $game->save();

    }
}