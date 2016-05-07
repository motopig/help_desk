<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOrderToEnclosuresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('enclosures', function(Blueprint $table)
		{
			$table->integer('order')->unsigned()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('enclosures', function(Blueprint $table)
		{
			$table->dropColumn('order');
		});
	}

}
