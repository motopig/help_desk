<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBccToOrderQuereTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// ALTER TABLE `no_1_order_quere` ADD `bcc` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `enclosure`;
		Schema::table('order_quere', function(Blueprint $table)
		{
			$table->text('bcc')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('order_quere', function(Blueprint $table)
		{
			$table->dropColumn('bcc');
		});
	}

}