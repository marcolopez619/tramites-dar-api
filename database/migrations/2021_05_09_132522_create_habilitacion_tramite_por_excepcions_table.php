<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHabilitacionTramitePorExcepcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('habilitacion_tramite_por_excepcion', function (Blueprint $table) {
            $table->id('id_habilitacion_por_excepcion');
            $table->timestamp( 'fecha_habilitacion' )->default( DB::raw( 'NOW()' ) );
            $table->timestamp( 'fecha_regularizacion' );
            $table->string( 'motivo' );
            $table->integer( 'id_estudiante' );
            $table->integer( 'id_tramite' );
            $table->integer( 'id_estado' );
            $table->integer( 'id_periodo_gestion' );

            $table->foreign('id_estudiante')->references( 'id_estudiante' )->on( 'estudiante' )->onDelete('cascade');
            $table->foreign('id_tramite')->references( 'id_tramite' )->on( 'tramite' )->onDelete('cascade');
            $table->foreign('id_estado')->references( 'id_estado' )->on( 'estado' )->onDelete('cascade');
            $table->foreign('id_periodo_gestion')->references( 'id_periodo_gestion' )->on( 'periodo_gestion' )->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('habilitacion_tramite_por_excepcion');
    }
}
