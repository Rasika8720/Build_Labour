<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobDaylabourDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_daylabour_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id');
            $table->integer('pay_rate');
            $table->dateTime('job_date');
            $table->time('shift_start');
            $table->time('shift_end');
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
        Schema::dropIfExists('job_daylabour_details');
    }
}
