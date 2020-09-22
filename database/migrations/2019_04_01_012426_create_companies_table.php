<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('business_type_id')->unsigned()->nullable();
            $table->integer('tier_id')->unsigned()->nullable();
            $table->integer('main_company_id')->unsigned()->nullable();
            $table->string('main_function_answer')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('introduction')->nullable();
            $table->string('website')->nullable();
            $table->boolean('operate_outside_states')->default(0);
            $table->string('states')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->text('locations_json')->nullable();
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
