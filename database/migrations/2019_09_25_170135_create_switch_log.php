<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSwitchLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('switch_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('switch_id');
            $table->unsignedInteger('port_id')->nullable();
            $table->text('event');
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign( 'switch_id' )->references( 'id' )->on( 'switches' )->onDelete( 'cascade' ); 
            $table->foreign( 'port_id' )->references( 'id' )->on( 'ports' ); 
            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )->onDelete( 'set null' ); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('switch_logs');
    }
}
