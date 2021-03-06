<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderQuereTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_quere', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->unsigned();
			$table->text('byname');
			$table->string('from_email', 255);
            $table->string('to_email', 255);
            $table->text('content');
            $table->enum('execute', array('0', '1'))->default('0');
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
		Schema::drop('order_quere');
	}

}
