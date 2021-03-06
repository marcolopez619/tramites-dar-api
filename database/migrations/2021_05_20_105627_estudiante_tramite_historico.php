<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EstudianteTramiteHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiante_tramite_historico', function (Blueprint $table) {
            $table->id('id_estudiante_tramite_historico');
            $table->integer( 'id_tramite' )->nullable( false );
            $table->integer( 'id_estado' )->nullable( false );
            $table->integer( 'id_entidad' )->nullable( false );
            $table->timestamp( 'fecha_proceso' )->nullable( false );
            $table->string( 'observaciones' );
            $table->integer( 'id_estudiante' );
            $table->integer( 'id_anulacion' );
            $table->integer( 'id_cambio_carrera' );
            $table->integer( 'id_transferencia' );
            $table->integer( 'id_suspencion' );
            $table->integer( 'id_readmision' );
            $table->integer( 'id_traspaso' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiante_tramite_historico');
    }
}
