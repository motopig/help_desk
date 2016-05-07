<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_message', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order')->unsigned();
			$table->integer('status')->unsigned()->default(0);
			$table->integer('ask')->unsigned()->default(0);
			$table->integer('reply')->unsigned()->default(0);
			$table->integer('user')->unsigned()->default(0);
            $table->integer('admin')->unsigned()->default(0);
            $table->text('content');
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
		Schema::drop('order_message');
	}

}
