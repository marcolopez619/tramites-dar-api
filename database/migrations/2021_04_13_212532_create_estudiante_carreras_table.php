<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudianteCarrerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiante_carrera', function (Blueprint $table) {
            $table->id('id_estudiante_carrera');
            $table->bigInteger('id_estudiante');
            $table->bigInteger('id_carrera');
            $table->integer('estado');

            $table->foreign('id_estudiante')->references('id_estudiante')->on( 'estudiante' )->onDelete('cascade');
            $table->foreign('id_carrera')->references('id_carrera')->on( 'carrera' )->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiante_carrera');
    }
}
