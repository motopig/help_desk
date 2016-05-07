<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyEmailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_email', function(Blueprint $table) {
			$table->increments('id');
            $table->string('email');
            $table->integer('company_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->enum('disabled', array('true', 'false'))->default('false');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_email');
	}

}
