<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMarkToOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order', function(Blueprint $table)
		{
			$table->text('mark')->nullable();
			$table->integer('tab')->unsigned()->default('0');
			$table->integer('trash')->unsigned()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('order', function(Blueprint $table)
		{
			Schema::drop('mark');
			Schema::drop('tab');
			Schema::drop('trash');
		});
	}

}
