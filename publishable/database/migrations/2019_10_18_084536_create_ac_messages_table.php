<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_messages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('m_from')->default(0);
			$table->integer('m_to')->nullable()->default(0);
			$table->text('message', 65535);
			$table->boolean('is_read')->default(0);
			$table->boolean('m_from_delete')->default(0);
			$table->boolean('m_to_delete')->default(0);
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
		Schema::drop('ac_messages');
	}

}
