<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraspasosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traspaso', function (Blueprint $table) {
            $table->id( 'id_traspaso' );
            $table->integer( 'id_univ_destino' );
            $table->integer( 'id_carrera_destino' );
            $table->string( 'descripcion' );
            $table->integer( 'anio_ingreso' );
            $table->integer( 'materias_aprobadas' );
            $table->integer( 'materias_reprobadas' );
            $table->date('fecha_solicitud');
            $table->integer( 'id_estudiante' );
            $table->integer( 'id_motivo' );

            $table->foreign('id_estudiante')->references( 'id_estudiante' )->on( 'estudiante' )->onDelete('cascade');
            $table->foreign('id_motivo')->references( 'id_motivo' )->on( 'motivo' )->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traspaso');
    }
}
