<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('course')->nullable();
            $table->string('school')->nullable();
            $table->string('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('user_id')->unsigned();
            $table->integer('course_id')->unsigned()->nullable();
            $table->string('education_status')->nullable();
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
    }
}
