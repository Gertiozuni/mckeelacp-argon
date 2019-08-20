<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vlans', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->smallInteger('vlan');
            $table->string('description')->nullable();
            $table->smallInteger('subnet');
            $table->boolean( 'alert' )->default( 1 );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vlans');
    }
}
