<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('submissions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('video_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('title');
			$table->text('description')->nullable();
			$table->timestamps();

			$table->foreign('video_id')
				->references('id')->on('videos')
				->onDelete('cascade');
			$table->foreign('user_id')
				->references('id')->on('users')
				->onDelete('cascade');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submissions');
    }
}