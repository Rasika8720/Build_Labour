<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkExperienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_experience', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_role')->nullable();
            $table->string('company_name')->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->text('responsibilities')->nullable();
            $table->string('salary')->nullable();
            $table->boolean('isCurrent')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('user_id')->unsigned();

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

