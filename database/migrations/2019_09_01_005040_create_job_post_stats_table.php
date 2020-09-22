<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobPostStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_post_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->integer('performed_by')->unsigned(); // a scorer
            $table->integer('scored_to')->unsigned(); // score to a user
            $table->string('category')->comment('viewed, invited, favourite, not_suitable, company_viewed_profile');
            $table->timestamp('created_at');

            $table->foreign('performed_by')
                ->references('id')->on('users');

            $table->foreign('scored_to')
                ->references('id')->on('users');
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
