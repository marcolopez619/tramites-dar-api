<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerfilModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfil_modulo', function (Blueprint $table) {
            $table->id( 'id_perfil_modulo' );
            $table->integer( 'id_perfil' )->nullable( false );
            $table->integer( 'id_modulo' )->nullable( false );

            $table->foreign( 'id_perfil' )->references( 'id_perfil' )->on( 'perfil' )->onDelete( 'cascade' );
            $table->foreign( 'id_modulo' )->references( 'id_modulo' )->on( 'modulo' )->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfil_modulo');
    }
}
