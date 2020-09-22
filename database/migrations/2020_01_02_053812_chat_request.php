<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChatRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_request', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requesting_user', false, true);
            $table->integer('requested_user', false, true);
            $table->enum('status', ['Pending', 'Accepted', 'Rejected']);
            $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
