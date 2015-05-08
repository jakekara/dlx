<?php 

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeGameInvitationsTable extends Migration {

    /**
     This table is meant to hold game invitations
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
        Schema::create('game_invites', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->bigInteger('sender');
            $table->bigInteger('recipient');
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
		//
        Schema::dropIfExists('game_invites');   
	}

}
