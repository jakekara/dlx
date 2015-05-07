<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveUsersToGamesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add columns for user invites and user join requests
        Schema::table('games', function($table) 
        {
            // user requests will be for users who request
            // to play a game they're not currently part of
            $table->string('user_requests', 3000);
            
            // user invites will be for active players to 
            // invite their friends who are using the app
            $table->string('user_invites', 3000);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// drop columns
        $table->dropColumn('user_invites');
        $table->dropColumn('user_requests');
	}

}
