<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyWorkerDetails2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('worker_details', function (Blueprint $table) {
            $table->integer('when')->nullable()->after('introduction');
            $table->integer('max_distance')->nullable()->after('when');
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
