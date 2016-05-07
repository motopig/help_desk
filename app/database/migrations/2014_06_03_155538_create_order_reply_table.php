<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order')->unsigned()->default('0');
			$table->integer('reply')->unsigned()->default('0');
			$table->text('content');
			$table->integer('enclosure')->unsigned()->default('0');
			$table->integer('msgno')->unsigned()->default('0');
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
		Schema::drop('order_reply');
	}

}
