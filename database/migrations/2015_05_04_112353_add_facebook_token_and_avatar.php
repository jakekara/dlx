<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacebookTokenAndAvatar extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add columns for facebook token and avatar
        Schema::table('users', function($table) 
        {
            $table->string('fb_token');
            $table->string('fb_avatar');
        });
   }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

        // remove columns
        Schema::table('users', function($table) 
        {
            $table->dropColumn('fb_token');
            $table->dropColumn('fb_avatar');

        });


	}

}
