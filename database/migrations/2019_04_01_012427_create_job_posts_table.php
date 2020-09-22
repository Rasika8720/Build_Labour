<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('about')->nullable();
            $table->string('exp_level')->nullable();
            $table->string('contract_type')->nullable(); // employment type
            $table->string('salary')->nullable();
            $table->text('reports_to_json')->nullable();
            $table->string('location')->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('job_role_id')->unsigned()->nullable();
            $table->boolean('is_template')->nullable();
            $table->integer('created_by')->unsigned();
            $table->timestamps();
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