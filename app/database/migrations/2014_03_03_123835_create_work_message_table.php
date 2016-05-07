<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('work_message', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('work_order_id')->unsigned();
			$table->enum('work_status', array('1', '2', '3', '4', '5'));
			$table->enum('type', array('ask', 'reply'));
			$table->integer('ask_id')->unsigned()->default(0);
            $table->integer('reply_id')->unsigned()->default(0);
            $table->integer('user_id')->unsigned()->default(0);
            $table->integer('admin_id')->unsigned()->default(0);
            $table->integer('reply_admin_id')->unsigned()->default(0);
            $table->enum('status', array('false', 'true'))->default('false');
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
		Schema::drop('work_message');
	}

}
