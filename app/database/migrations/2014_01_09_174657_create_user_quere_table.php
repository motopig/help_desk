<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserQuereTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_quere', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('quere_id')->unsigned();
			$table->integer('company_id')->unsigned();
			$table->integer('ask_id')->unsigned()->default(0);
            $table->integer('reply_id')->unsigned()->default(0);
			$table->string('from_email', 255);
            $table->string('to_email', 255);
            $table->enum('status', array('0', '1'))->default('0');
            $table->enum('event', array('0', '1'))->default('0');
            $table->enum('execute', array('0', '1'))->default('0');
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
		Schema::drop('user_quere');
	}

}
