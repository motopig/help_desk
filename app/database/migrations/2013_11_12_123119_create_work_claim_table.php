<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkClaimTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('work_claim', function(Blueprint $table) {
			$table->increments('id');
            $table->integer('work_order_id')->unsigned();
            $table->integer('admin_id')->unsigned();
            $table->integer('order')->unsigned()->default(0);
            $table->enum('assign', array('true', 'false'))->default('false');
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
		Schema::drop('work_claim');
	}

}
