<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contents', function(Blueprint $table)
		{
            $table->engine = "InnoDB";
			$table->integer('id', true);
			$table->string('title', 100);
			$table->string('path', 100);
			$table->integer('type_id')->index('type_id');
			$table->integer('user_id')->unsigned()->index('user_id');
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
		Schema::drop('contents');
	}

}
