<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string( 'nombre', 50 )->nullable( false );
            $table->string( 'password' )->nullable( false );
            $table->string( 'celular', 10 );
            $table->integer( 'estado' );
            $table->integer( 'id_estudiante' )->default( -1 );
            $table->integer( 'id_universidad' )->default( 1 );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}
