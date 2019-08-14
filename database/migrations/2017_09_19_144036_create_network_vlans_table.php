<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkVlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_vlans', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->smallInteger('vlan');
            $table->string('description');
            $table->smallInteger('subnet');
            $table->boolean('alert');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_vlans');
    }
}
