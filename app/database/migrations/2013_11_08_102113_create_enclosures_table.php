<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnclosuresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enclosures', function(Blueprint $table) {
			$table->increments('id');
            $table->string('enclosure_name');
            $table->enum('type', array('file', 'image'))->default('image');
            $table->string('suffix', 50);
            $table->string('path');
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
		Schema::drop('enclosures');
	}

}
