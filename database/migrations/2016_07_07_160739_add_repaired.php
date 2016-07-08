<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepaired extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('batch', function($table)
		{
    		$table->string('repaired')->nullable();
    		$table->string('repaired_by_id')->nullable();
			$table->string('repaired_by_name')->nullable();
			$table->dateTime('repaired_date')->nullable();
    		
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
