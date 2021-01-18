<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_statuses', function (Blueprint $table) {
            $table->bigincrements('id');
            $table->string('name');
        });

        Schema::create('upload_ad_statuses', function (Blueprint $table) {
            $table->bigincrements('id');
            $table->string('name');
        });

        DB::table('upload_statuses')->insert(
            array(
                'id' => 1,
                'name' => 'New'
            )
        );

        DB::table('upload_statuses')->insert(
            array(
                'id' => 2,
                'name' => 'Completed'
            )
        );

        DB::table('upload_ad_statuses')->insert(
            array(
                'id' => 1,
                'name' => 'Error'
            )
        );

        DB::table('upload_ad_statuses')->insert(
            array(
                'id' => 2,
                'name' => 'Passed'
            )
        );

        DB::table('upload_ad_statuses')->insert(
            array(
                'id' => 3,
                'name' => 'Modified'
            )
        );

        Schema::create('uploads', function (Blueprint $table) {
            $table->bigincrements('id');
            $table->string('file_name')->nullable();
            $table->date('uploaded_date')->nullable();
            $table->unsignedBigInteger('status');
            $table->foreign('status')->references('id')->on('upload_statuses');
            $table->string('approve')->nullable();
            $table->date('approve_date')->nullable();
            $table->timestamps();
        });
        
        Schema::create('upload_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('upload_id');
            $table->foreign('upload_id')->references('id')->on('uploads');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('about')->nullable();
            $table->string('exp_level')->nullable();
            $table->string('contract_type')->nullable(); // employment type
            $table->string('salary')->nullable();
            $table->string('salary_type')->nullable();
            $table->string('project_size')->nullable();
            $table->string('location')->nullable();
            $table->string('company_name')->nullable();
            $table->string('job_role')->nullable();
            $table->string('job_logo')->nullable();
            $table->string('job_url')->nullable();
            $table->unsignedBigInteger('status');
            $table->foreign('status')->references('id')->on('upload_ad_statuses');
            $table->date('date_posted')->nullable();
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
        Schema::dropIfExists('uploads');
        Schema::dropIfExists('upload_ads');
        Schema::dropIfExists('upload_statuses');
        Schema::dropIfExists('upload_ad_statuses');
    }
}
