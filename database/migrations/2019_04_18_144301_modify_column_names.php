<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('dob', 'date_of_birth');
        });

        Schema::table('worker_details', function (Blueprint $table) {
            $table->renameColumn('nrole_info', 'introduction');
            $table->renameColumn('nrole_when', 'when');
            $table->renameColumn('nrole_travel_to_home', 'max_distance');
            $table->renameColumn('nrole_address', 'address');
            $table->renameColumn('nrole_state', 'state');
            $table->renameColumn('right_to_work_au', 'right_to_work');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('title', 'ticket');
        });

        Schema::table('user_skills', function (Blueprint $table) {
            $table->renameColumn('name', 'skill');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
