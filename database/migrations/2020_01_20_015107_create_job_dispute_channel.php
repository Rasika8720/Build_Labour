<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobDisputeChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_dispute_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id');
            $table->enum('status', ['Open', 'Closed']);
            $table->string('awarded_to', 50);
            $table->enum('creator', ['Company', 'Worker']);
            $table->string('subject', 200);
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
        Schema::dropIfExists('job_dispute_channel');
    }
}
