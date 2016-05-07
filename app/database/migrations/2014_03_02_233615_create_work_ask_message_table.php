<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkAskMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('work_ask_message', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('work_order_id')->unsigned();
			$table->integer('ask_id')->unsigned();
            $table->integer('admin_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->enum('status', array('0', '1'))->default('0');
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
		Schema::drop('work_ask_message');
	}

}
