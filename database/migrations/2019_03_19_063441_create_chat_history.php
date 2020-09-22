<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_history', function (Blueprint $table) {
            $table->increments('history_id');
            $table->text( 'message' );
            $table->integer( 'channel_id' )->unsigned()->index();
            $table->integer( 'sent_by' )->unsigned()->index();
            $table->timestamp( 'sent_at');
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
