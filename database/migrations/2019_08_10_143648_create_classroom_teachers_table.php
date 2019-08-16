<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassroomTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classroom_teachers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string( 'staff_id' );
            $table->string( 'first_name' );
            $table->string( 'last_name' );
            $table->integer( 'campus_id' )->unsigned();
            $table->foreign( 'campus_id' )->references( 'id' )->on( 'campuses' )->onDelete( 'cascade' );
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classroom_teachers');
    }
}
