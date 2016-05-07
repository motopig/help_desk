<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkAskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('work_ask', function(Blueprint $table) {
			$table->increments('id');
            $table->integer('work_order_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('ask');
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
		Schema::drop('work_ask');
	}

}
