<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiante', function (Blueprint $table) {
            $table->id('id_estudiante');
            $table->integer('ru');
            $table->string( 'ci' );
            $table->string( 'complemento' )->nullable();
            $table->string( 'paterno' )->nullable();
            $table->string( 'materno' )->nullable();
            $table->string( 'nombres' );
            $table->date( 'fecha_nacimiento' );
            $table->boolean( 'sexo' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiante');
    }
}
