<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudianteAnulacionModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiante_anulacion', function (Blueprint $table) {
            $table->id('id_estudiante_anulacion');
            $table->integer( 'id_tramite' )->nullable( false );
            $table->integer( 'id_estado' )->nullable( false );
            $table->integer( 'id_entidad' )->nullable( false );
            $table->timestamp( 'fecha_proceso' )->nullable( false );
            $table->string( 'observaciones' );
            $table->integer( 'id_estudiante' );
            $table->integer( 'id_anulacion' )->default( 0 );
            $table->integer( 'id_cambio_carrera' )->default( 0 );
            $table->integer( 'id_transferencia' )->default( 0 );

            $table->foreign( 'id_estudiante' )->references( 'id_estudiante' )->on( 'estudiante' )->onDelete('cascade');
            $table->foreign( 'id_anulacion' )->references( 'id_anulacion' )->on( 'anulacion' )->onDelete('cascade');
            $table->foreign( 'id_cambio_carrera' )->references( 'id_cambio_carrera' )->on( 'cambio_carrera' )->onDelete('cascade');
            $table->foreign( 'id_transferencia' )->references( 'id_transferencia' )->on( 'transferencia' )->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiante_anulacion');
    }
}
