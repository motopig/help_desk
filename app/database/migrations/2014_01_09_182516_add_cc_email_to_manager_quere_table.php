<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCcEmailToManagerQuereTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manager_quere', function(Blueprint $table) {
			$table->string('cc_email', 255)->default('0');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manager_quere', function(Blueprint $table) {
			$table->dropColumn('cc_email');
		});
	}

}
