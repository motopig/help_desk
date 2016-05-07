<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEmailToOrderAskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order_ask', function(Blueprint $table)
		{
			$table->integer('email')->unsigned()->default('0');
			$table->integer('attachment')->unsigned()->default('0');
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
			$table->dropColumn('email');
			$table->dropColumn('attachment');
		});
	}

}
