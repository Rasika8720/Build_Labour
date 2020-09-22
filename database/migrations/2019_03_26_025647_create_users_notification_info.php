<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersNotificationInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_notification_info', function (Blueprint $table) {
            $table->increments( 'unid' );
            $table->integer( 'user_id' )->index();
            $table->integer( 'unread_notifications' )->default( 0 );
            $table->integer( 'unread_messages' )->default( 0 );
            $table->integer( 'badge_count' )->default( 0 );
            $table->dateTime( 'last_notification_check' )->nullable();
            $table->dateTime( 'last_message_check' )->nullable();
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
