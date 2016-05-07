<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPossToPostssTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('postss', function(Blueprint $table)
		{
			$table = Schema::getConnection()->getTablePrefix() . 'order_reply';

        	DB::statement("ALTER TABLE `" . $table . "` MODIFY `enclosure`  TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('postss', function(Blueprint $table)
		{
			$table = Schema::getConnection()->getTablePrefix() . 'order_reply';

        	DB::statement("ALTER TABLE `" . $table . "` MODIFY `enclosure`  TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
		});
	}

}
