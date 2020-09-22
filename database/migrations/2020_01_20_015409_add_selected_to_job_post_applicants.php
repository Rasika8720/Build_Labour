<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelectedToJobPostApplicants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_post_applicants', function (Blueprint $table) {
            $table->dateTime('selected_at')->nullable()->after('applied_at');
            $table->enum('selected', ['Pending', 'Accepted', 'Awarded', 'Declined'])
                ->after('job_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_post_applicants', function (Blueprint $table) {
            //
        });
    }
}
