<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddInboxToCompanyEmailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('company_email', function(Blueprint $table) {
			$table->string('inbox_path')->default('INBOX'); // 收件箱目录
			$table->string('spam_path')->default('SPAM'); // 垃圾箱目录
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('company_email', function(Blueprint $table) {
			$table->dropColumn('inbox_path');
			$table->dropColumn('spam_path');
		});
	}

}