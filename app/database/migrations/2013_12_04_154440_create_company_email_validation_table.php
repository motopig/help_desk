<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyEmailValidationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_email_validation', function(Blueprint $table) {
			$table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->text('plain');
            $table->integer('validation_time')->unsigned();
            $table->enum('validation', array('true', 'false'))->default('false');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_email_validation');
	}

}
