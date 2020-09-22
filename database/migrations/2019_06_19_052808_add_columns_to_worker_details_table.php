<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToWorkerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('worker_details', function (Blueprint $table) {

            $table->boolean('has_tfn')->unsigned()->nullable();
            $table->boolean('has_abn')->unsigned()->nullable();
            $table->boolean('has_whitecard')->unsigned()->nullable();
            $table->boolean('willing_to_relocate')->unsigned()->nullable();

            $table->string('most_recent_role')->nullable();
            $table->string('suburb')->nullable();
            $table->integer('exp_year')->unsigned()->nullable();
            $table->integer('exp_month')->unsigned()->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('worker_details', function (Blueprint $table) {
            //
        });
    }
}
