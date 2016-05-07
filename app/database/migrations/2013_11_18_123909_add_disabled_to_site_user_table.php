<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDisabledToSiteUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_user', function(Blueprint $table) {
            $table->enum('disabled', array('true', 'false'))->default('false');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('site_user', function(Blueprint $table) {
            $table->dropColumn('disabled');
		});
	}

}
