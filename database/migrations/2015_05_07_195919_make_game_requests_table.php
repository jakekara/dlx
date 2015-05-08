<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeGameRequestsTable extends Migration {

    /**
     This table is meant to hold game requests
     so they can be accessed more easily than 
     as part of the games table
     **/
    
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('game_requests', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->bigInteger('sender');
            $table->bigInteger('game_id');
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
        
        Schema::dropIfExists('game_requests');
	}

}
