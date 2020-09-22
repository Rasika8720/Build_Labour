<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveIsCurrentFieldFromWorkExperienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_experience', function (Blueprint $table) {

            $table->dropColumn('isCurrent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_experience', function (Blueprint $table) {
            //
        });
    }
}
