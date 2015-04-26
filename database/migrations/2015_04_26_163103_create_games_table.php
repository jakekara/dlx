<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('games', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->boolean('active');
			$table->bigInteger('turn');
            $table->bigInteger('score');
            $table->string('players', 3000); //max 30 chars per ID, * 100 players
			$table->timestamps();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
        Schema::drop('games');
	}

}
