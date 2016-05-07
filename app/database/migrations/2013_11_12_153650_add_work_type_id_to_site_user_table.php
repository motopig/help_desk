<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWorkTypeIdToSiteUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_user', function(Blueprint $table) {
            $table->integer('work_type_id')->unsigned()->after('user_id');
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
            $table->dropColumn('work_type_id');
		});
	}

}
