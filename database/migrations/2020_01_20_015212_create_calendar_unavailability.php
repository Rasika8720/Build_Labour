<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarUnavailability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_unavailability', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('job_id')->nullable();
            $table->enum('type', ['Shift', 'Unavailable']);
            $table->date('date');
            $table->string('description', 100);
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
        Schema::dropIfExists('calendar_unavailability');
    }
}
