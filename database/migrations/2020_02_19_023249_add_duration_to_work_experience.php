<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDurationToWorkExperience extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_experience', function (Blueprint $table) {
            $table->integer('duration_number')->after('end_year');
            $table->string('duration_type')->after('duration_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_experience', function (Blueprint $table) {
            //
        });
    }
}
