<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderRemarkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_remark', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order')->unsigned()->default('0');
			$table->integer('order_ask')->unsigned()->default('0');
			$table->integer('order_reply')->unsigned()->default('0');
			$table->integer('admin')->unsigned()->default('0');
			$table->text('content');
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
		Schema::drop('order_remark');
	}

}
