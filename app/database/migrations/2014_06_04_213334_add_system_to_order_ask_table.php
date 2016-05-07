<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSystemToOrderAskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order_ask', function(Blueprint $table)
		{
			$table->integer('system')->unsigned()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('order_ask', function(Blueprint $table)
		{
			Schema::drop('system');
		});
	}

}
