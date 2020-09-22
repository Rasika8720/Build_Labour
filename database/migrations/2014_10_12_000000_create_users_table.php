<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->string( 'country', 150 )->nullable();
            $table->string( 'mobile_number', 150 )->nullable();
            $table->string( 'address' , 150)->nullable();
            $table->string( 'profile_photo_url', 150 )->nullable();
            $table->string( 'verification_code', 150 )->nullable();
            $table->string('marital_status', 20)->nullable(); // married or single
            $table->string('gender', 10)->nullable(); // male or female
            $table->date( 'is_verified' )->nullable();
            $table->smallInteger('role_id')->unsigned()->default(1); // 1 is for Worker Role
            $table->rememberToken();
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
