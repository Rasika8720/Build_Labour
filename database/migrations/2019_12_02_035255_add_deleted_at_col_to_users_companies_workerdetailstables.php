<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtColToUsersCompaniesWorkerdetailsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('worker_details', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_companies_workerdetails_tables', function (Blueprint $table) {
            //
        });
    }
}
