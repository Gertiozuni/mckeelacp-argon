<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSwitchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('switches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('ip_address');
            $table->smallInteger('ethernet_ports')->nullable();
            $table->smallInteger('fiber_ports');
            $table->string('mac_address')->nullable();
            $table->integer( 'campus_id' )->unsigned()->nullable();
            $table->foreign( 'campus_id' )->references( 'id' )->on( 'campuses' )->onDelete( 'cascade' );
            $table->string('location');
            $table->string('sub_location')->nullable();
            $table->string('model')->nullable();
            $table->boolean('active')->default( 0 );
            $table->timestamp( 'uptime' )->nullable();
            $table->timestamp( 'checked_in' )->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('switches');
    }
}
