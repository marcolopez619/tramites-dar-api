<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrera', function (Blueprint $table) {
            $table->id('id_carrera');
            $table->string('nombre');
            $table->integer('estado');
            $table->bigInteger('id_facultad');
            $table->bigInteger('id_tipo_carrera');

            $table->foreign( 'id_facultad' )->references('id_facultad')->on('facultad')->onDelete('cascade');
            $table->foreign( 'id_tipo_carrera' )->references('id_tipo_carrera')->on('tipo_carrera')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrera');
    }
}
