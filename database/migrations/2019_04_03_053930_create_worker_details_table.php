<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('profile_description')->nullable();
            $table->string('english_skill')->nullable();
            $table->string('drivers_license')->nullable();
            $table->string('right_to_work_au')->nullable();
            $table->string('main_skill')->nullable();
            $table->string('nrole_info')->nullable();
            $table->string('nrole_when')->nullable();
            $table->string('nrole_travel_to_home')->nullable();
            $table->string('nrole_address')->nullable();
            $table->string('nrole_state')->nullable();
            $table->boolean('nrole_right_to_work_au')->unsigned()->default(1);
            $table->integer('user_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
