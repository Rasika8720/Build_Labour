<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatChannels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_channels', function (Blueprint $table) {
            $table->increments( 'channel_id');
            $table->string('name', 50)->nullable();
            $table->string('type', 50)->default( 'one-on-one' ); // group or single
            $table->integer( 'connection_id' )->nullable(); // used for one-on-one channels null for group channels
            $table->dateTime( 'created_at' );
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
