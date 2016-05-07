<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAskAnchorToWorkAskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('work_ask', function(Blueprint $table) {
			$table->string('ask_anchor', 50);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('work_ask', function(Blueprint $table) {
			$table->dropColumn('ask_anchor');
		});
	}

}
