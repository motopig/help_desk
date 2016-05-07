<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBindUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bind_user', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('group_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('admin_id')->unsigned();
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
		Schema::drop('bind_user');
	}

}
