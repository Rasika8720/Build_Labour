<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNightShiftWalletBalanceToWorkerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('worker_details', function (Blueprint $table) {
//            $table->boolean('night_shift')->after('day_labour_status');
//            $table->integer('wallet_balance')->after('night_shift');
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
