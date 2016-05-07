<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveGradeToGroupAdminUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('group_admin_user', function(Blueprint $table) {
            $table->dropColumn('grade');
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
            $table->enum('grade', array('1', '2'))->default('2');
		});
	}

}
