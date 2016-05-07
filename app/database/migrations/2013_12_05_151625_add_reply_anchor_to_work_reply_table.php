<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddReplyAnchorToWorkReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('work_reply', function(Blueprint $table) {
			$table->string('reply_anchor', 50);
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
			$table->dropColumn('reply_anchor');
		});
	}

}
