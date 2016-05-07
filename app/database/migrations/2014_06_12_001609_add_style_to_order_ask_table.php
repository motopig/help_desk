<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStyleToOrderAskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order_ask', function(Blueprint $table)
		{
			$table->enum('style', array('order', 'inbox', 'spam'))->default('order');
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
			$table->dropColumn('style');
		});
	}

}
