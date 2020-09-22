<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyWorkExperience extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_experience', function (Blueprint $table) {
            $table->dropColumn('salary');
            $table->string('location')->after('company_id')->nullable();
            $table->string('project_size')->after('location')->nullable();
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
            $table->string('salary')->nullable();
            $table->dropColumn('location');
            $table->dropColumn('project_size');
        });
    }
}
