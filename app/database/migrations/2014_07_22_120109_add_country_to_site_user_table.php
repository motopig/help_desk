<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCountryToSiteUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_user', function(Blueprint $table)
		{
			$table->text('country')->nullable();
			$table->text('user')->nullable();
			$table->text('remark')->nullable();
			$table->text('mark')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('site_user', function(Blueprint $table)
		{
			$table->dropColumn('country');
			$table->dropColumn('user');
			$table->dropColumn('remark');
			$table->dropColumn('mark');
		});
	}

}