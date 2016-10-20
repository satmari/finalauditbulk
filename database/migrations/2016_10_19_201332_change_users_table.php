<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('users', function ($table) {

			
			$table->string('company')->nullable(); //added latter
			//$table->dropColumn('defect_type_status'); //drop
			// $table->string('name', 50)->nullable()->change(); //change
   			//$table->renameColumn('defect_type_status', 'visible'); //rename


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
