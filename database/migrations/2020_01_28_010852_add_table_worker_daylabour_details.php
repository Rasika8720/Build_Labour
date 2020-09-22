<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableWorkerDaylabourDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_daylabour_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('address');
            $table->string('address_lat');
            $table->string('address_long');
            $table->integer('max_work_distance');
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
        Schema::dropIfExists('worker_daylabour_details');
    }
}
