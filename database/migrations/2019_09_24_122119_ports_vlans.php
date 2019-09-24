<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PortsVlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'port_vlan', function (Blueprint $table) {
            $table->unsignedInteger( 'port_id' )->nullable();
            $table->unsignedInteger( 'vlan_id' )->nullable();

            $table->primary( [ 'port_id', 'vlan_id' ] );

            $table->foreign( 'port_id' )->references( 'id' )->on( 'ports' )->onDelete( 'cascade' ); 
            $table->foreign( 'vlan_id' )->references( 'id' )->on( 'vlans' )->onDelete( 'cascade' ); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'port_vlan' );
    }
}
