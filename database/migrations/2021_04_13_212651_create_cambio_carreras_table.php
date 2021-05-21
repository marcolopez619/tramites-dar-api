<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCambioCarrerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cambio_carrera', function (Blueprint $table) {
            $table->id( 'id_cambio_carrera' );
            $table->integer( 'id_carrera_origen' );
            $table->integer( 'id_carrera_destino' );
            $table->date('fecha_solicitud');
            $table->boolean('convalidacion');
            $table->string( 'motivo' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cambio_carrera');
    }
}
