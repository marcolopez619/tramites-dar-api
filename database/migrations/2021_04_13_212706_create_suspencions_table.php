<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuspencionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suspencion', function (Blueprint $table) {
            $table->id( 'id_suspencion' );
            $table->integer( 'id_carrera' );
            $table->integer( 'tiempo_solicitado' );
            $table->string( 'descripcion' );
            $table->date('fecha_solicitud');
            $table->string( 'motivo' );
            $table->integer('id_estudiante');

            $table->foreign('id_estudiante')->references( 'id_estudiante' )->on( 'estudiante' )->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suspencion');
    }
}
