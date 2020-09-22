<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUserSkillsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_skills')->truncate();

        Schema::table('user_skills', function (Blueprint $table) {

            $table->dropColumn('skill');
            $table->dropColumn('description');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->integer('skill_id')->unsigned();
            $table->integer('level_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('level_id')->references('id')->on('levels');
            $table->foreign('skill_id')->references('id')->on('skills');
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
