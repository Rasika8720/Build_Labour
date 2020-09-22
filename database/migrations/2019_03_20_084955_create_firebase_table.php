<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirebaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_firebase', function (Blueprint $table) {

            $table->increments('fid');
            $table->integer( 'user_id')->index();
            $table->string( 'device_token' )->index();
            $table->dateTime( 'added_at' );
            $table->dateTime( 'expire_at' );
            $table->tinyInteger( 'push_notification_enabled')->unsigned()->default(1);

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
