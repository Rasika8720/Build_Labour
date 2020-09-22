<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyEducationDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('educations', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->integer('start_month')->after('description')->nullable();
            $table->integer('start_year')->after('start_month')->nullable();
            $table->integer('end_month')->after('start_year')->nullable();
            $table->integer('end_year')->after('end_month')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('educations', function (Blueprint $table) {
            //
        });
    }
}
