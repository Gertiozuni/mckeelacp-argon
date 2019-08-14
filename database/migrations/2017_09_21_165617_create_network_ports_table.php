<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkPortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_ports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('port');
            $table->string('description')->nullable();
            $table->integer('switch_id')->unsigned();
            $table->foreign( 'switch_id' )->references( 'id' )->on( 'network_switches' )->onDelete( 'cascade' ); 
            $table->string('mode')->nullable();
            $table->boolean( 'fiber' );
            $table->tinyInteger('active');
            $table->timestamp('last_updated')->nullable();
            $table->timestamp('checked_in')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_ports');
    }
}
