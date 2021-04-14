<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadmisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readmision', function (Blueprint $table) {
            $table->id( 'id_readmision' );
            $table->integer( 'id_carrera' );
            $table->date('fecha_solicitud');
            $table->string( 'motivo' );
            $table->integer( 'id_suspencion' ); //* llave foranea
            $table->integer( 'id_estudiante'); //* llave foranea
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('readmision');
    }
}
