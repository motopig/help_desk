<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCheckreplyToWorkReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('work_reply', function(Blueprint $table) {
			$table->enum('checkreply', array('0', '1'))->default('0');
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
			$table->dropColumn('checkreply');
		});
	}

}
