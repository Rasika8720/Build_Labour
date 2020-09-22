<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('educations', function (Blueprint $table) {
            $table->integer('start_day')->unsigned()->nullable()->after('description');
            $table->integer('end_day')->unsigned()->nullable()->after('start_year');
            $table->integer('school_id')->unsigned()->nullable()->after('school');
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
