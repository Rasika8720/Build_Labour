<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobDaylabourBooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_daylabour_booking', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id');
            $table->integer('user_id');
            $table->enum('status', ['Open', 'Cancelled', 'Closed']);
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
        Schema::dropIfExists('job_daylabour_booking');
    }
}
