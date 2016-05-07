<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeLevelToAdminUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $table = Schema::getConnection()->getTablePrefix() . 'admin_user';

        DB::statement("ALTER TABLE `" . $table . "` MODIFY `level`  ENUM('1','2','3','4')  NOT NULL DEFAULT '4'");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        $table = Schema::getConnection()->getTablePrefix() . 'admin_user';

        DB::statement("ALTER TABLE `" . $table . "` MODIFY `level`  ENUM('1','2','3')  NOT NULL DEFAULT '3'");
	}

}
