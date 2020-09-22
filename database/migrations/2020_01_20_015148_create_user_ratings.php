<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('creator_id');
            $table->integer('job_id');
            $table->string('star_rating', 5);
            $table->string('comment', 300);
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
        Schema::dropIfExists('user_ratings');
    }
}
