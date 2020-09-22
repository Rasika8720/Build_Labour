<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobDisputeHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_dispute_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dispute_id');
            $table->text('message');
            $table->integer('sent_by');
            $table->enum('message_type', ['message', 'file']);
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
        Schema::dropIfExists('job_dispute_history');
    }
}
