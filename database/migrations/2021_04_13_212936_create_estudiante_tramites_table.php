<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudianteTramitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiante_tramite', function (Blueprint $table) {
            $table->id( 'id_estudiante_tramite' );
            $table->integer( 'id_estudiante' );
            $table->integer( 'id_tramite' );
            $table->integer( 'id_estado' );
            $table->integer( 'id_entidad' );
            $table->date( 'fecha' );
            $table->string( 'observaciones' );

            $table->foreign( 'id_estudiante' )->references( 'id_estudiante' )->on( 'estudiante' )->onDelete( 'cascade' );
            $table->foreign( 'id_tramite' )->references( 'id_tramite' )->on( 'tramite' )->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiante_tramite');
    }
}
