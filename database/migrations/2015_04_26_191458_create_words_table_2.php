<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordsTable2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('words', function(Blueprint $table)
		{
            $table->bigInteger('game_id');
            $table->string('word', 100);
            $table->integer('position');
            $table->primary(array('game_id', 'word'));
            
            $table->foreign('game_id')
                ->references('id')
                ->on('games');
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
		Schema::dropIfExists('words');    
	}

}
