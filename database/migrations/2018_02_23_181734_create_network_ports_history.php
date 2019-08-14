<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkPortsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_ports_history', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('port_id')->unsigned();
            $table->foreign( 'port_id' )->references( 'id' )->on( 'network_ports' )->onDelete( 'cascade' ); 
            $table->text( 'info' );
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )->onDelete( 'cascade' ); 
            $table->timestamp('created_at')->nullable();

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
