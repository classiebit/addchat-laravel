<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAcUsersMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ac_users_messages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('users_id');
			$table->integer('buddy_id');
			$table->integer('messages_count')->default(1);
			$table->unique(['users_id','buddy_id'], 'users_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ac_users_messages');
	}

}
