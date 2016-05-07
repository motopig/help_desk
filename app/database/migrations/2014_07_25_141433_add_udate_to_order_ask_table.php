<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUdateToOrderAskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order_ask', function(Blueprint $table)
		{
			$table->integer('udate')->unsigned()->default('0');
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
			$table->dropColumn('udate');
		});
	}

}