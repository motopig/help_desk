<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLevelToGroupAdminUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('group_admin_user', function(Blueprint $table) {
            $table->enum('grade', array('1', '2'))->default('2');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('group_admin_user', function(Blueprint $table) {
            $table->dropColumn('grade');
		});
	}

}
