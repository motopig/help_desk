<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('work_reply', function(Blueprint $table) {
			$table->increments('id');
            $table->integer('work_order_id')->unsigned();
            $table->integer('admin_id')->unsigned();
            $table->text('reply');
            $table->integer('enclosure_id')->unsigned();
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
		Schema::drop('work_reply');
	}

}
