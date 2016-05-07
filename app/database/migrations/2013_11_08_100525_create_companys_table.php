<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companys', function(Blueprint $table) {
			$table->increments('id');
            $table->string('company_name');
            $table->string('mobile', 50);
            $table->string('address');
            $table->integer('logo');
            $table->string('path');
            $table->text('brief');
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
		Schema::drop('companys');
	}

}
