<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->date( 'fecha_inicial' );
            $table->date( 'fecha_final' );
            $table->integer( 'id_estudiante' );
            $table->integer( 'id_tramite' );
            $table->integer( 'id_estado' );

            $table->foreign('id_estudiante')->references( 'id_estudiante' )->on( 'estudiante' )->onDelete('cascade');
            $table->foreign('id_tramite')->references( 'id_tramite' )->on( 'tramite' )->onDelete('cascade');
            $table->foreign('id_estado')->references( 'id_estado' )->on( 'estado' )->onDelete('cascade');

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
