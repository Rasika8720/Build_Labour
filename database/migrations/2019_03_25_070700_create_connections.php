<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->increments('connection_id');
            $table->integer( 'user1' )->unsigned()->index();
            $table->integer( 'user2' )->unsigned()->index();
            $table->integer( 'requested_by' )->unsigned();
            $table->integer( 'requested_to' )->unsigned();
            $table->string( 'status' , 20 )->default( 'pending' );
            $table->text( 'message' )->nullable();
            $table->datetime( 'added_at' );
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
