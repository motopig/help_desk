<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOrderToOrderQuereTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order_quere', function(Blueprint $table)
		{
			$table->integer('order')->unsigned()->default('0');
			$table->text('title');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('order_quere', function(Blueprint $table)
		{
			$table->dropColumn('order');
			$table->dropColumn('title');
		});
	}

}
