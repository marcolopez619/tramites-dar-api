<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioPerfilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_perfil', function (Blueprint $table) {
            $table->id( 'id_usuario_perfil' );
            $table->integer( 'id_usuario' )->nullable( false );
            $table->integer( 'id_perfil' )->nullable( false );

            $table->foreign( 'id_usuario' )->references( 'id_usuario' )->on( 'usuario' )->onDelete( 'cascade' );
            $table->foreign( 'id_perfil' )->references( 'id_perfil' )->on( 'perfil' )->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario_perfil');
    }
}
