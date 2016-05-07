<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('work_order', function(Blueprint $table) {
			$table->increments('id');
            $table->integer('work_type_id')->unsigned();
            $table->enum('work_level', array('1', '2', '3'))->default('1');
            $table->enum('work_status', array('1', '2', '3', '4', '5'))->default('1');
            $table->enum('work_time', array('true', 'false'))->default('false');
            $table->enum('work_email', array('true', 'false'))->default('false');
            $table->enum('assess', array('1', '2', '3', '4'))->default('1');
            $table->enum('memo', array('true', 'false'))->default('false');
            $table->integer('group_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('admin_id')->unsigned();
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
		Schema::drop('work_order');
	}

}
