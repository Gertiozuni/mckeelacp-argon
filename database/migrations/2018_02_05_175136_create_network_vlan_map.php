<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkVlanMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_vlan_map', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('port_id')->unsigned();
            $table->foreign( 'port_id' )->references( 'id' )->on( 'network_ports' )->onDelete( 'cascade' ); 
            $table->integer('vlan');
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
