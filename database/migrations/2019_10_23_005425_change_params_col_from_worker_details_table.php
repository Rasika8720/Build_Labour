<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeParamsColFromWorkerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('worker_details', function (Blueprint $table) {

            $table->dropColumn(['params']);

        });

        Schema::table('worker_details', function (Blueprint $table) {

            $table->text('params')->nullable();
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
