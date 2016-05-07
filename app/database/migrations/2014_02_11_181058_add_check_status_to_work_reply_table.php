<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCheckStatusToWorkReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('work_reply', function(Blueprint $table) {
			$table->enum('check_status', array('0', '1', '2'))->default('0');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('work_reply', function(Blueprint $table) {
			$table->dropColumn('check_status');
		});
	}

}
