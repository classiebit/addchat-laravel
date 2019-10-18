<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_settings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('s_name', 512)->nullable();
			$table->text('s_value', 65535)->nullable();
			$table->dateTime('dt_updated')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ac_settings');
	}

}
