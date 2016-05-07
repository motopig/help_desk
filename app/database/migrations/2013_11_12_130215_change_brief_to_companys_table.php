<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeBriefToCompanysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        $table = Schema::getConnection()->getTablePrefix() . 'companys';

        DB::statement("ALTER TABLE `" . $table . "` MODIFY `brief` text NULL");
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        $table = Schema::getConnection()->getTablePrefix() . 'companys';

        DB::statement("ALTER TABLE `" . $table . "` MODIFY `brief` text NOT NULL");
    }

}
