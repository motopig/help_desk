<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('title');
			$table->integer('status')->unsigned()->default('0');
			$table->integer('level')->unsigned()->default('0');
			$table->integer('group')->unsigned()->default('0');
            $table->enum('type', array('user', 'admin'))->default('user');
            $table->integer('ask')->unsigned()->default('0');
            $table->integer('reply')->unsigned()->default('0');
            $table->enum('disabled', array('true', 'false'))->default('false');
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
		Schema::drop('order');
	}

}
