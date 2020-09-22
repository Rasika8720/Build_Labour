<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusAndTemplateNameColumnsForJobPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->boolean('status')->default(1)->comment('1 - active , 0 - closed');
            $table->string('template_name')->comment('template name for a job');
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
