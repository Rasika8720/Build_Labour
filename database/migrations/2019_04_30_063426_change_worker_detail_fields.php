<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeWorkerDetailFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('worker_details', function (Blueprint $table) {
            $table->boolean('english_skill')->default(0)->change();
            $table->boolean('drivers_license')->default(0)->change();
            $table->boolean('right_to_work')->default(0)->change();
            $table->boolean('has_registered_vehicle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
