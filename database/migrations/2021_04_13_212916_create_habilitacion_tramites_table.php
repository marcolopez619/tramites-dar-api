<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabilitacionTramitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('habilitacion_tramite', function (Blueprint $table) {
            $table->id( 'id_hab_tramite' );
            $table->date( 'fecha_inicial' );
            $table->date( 'fecha_final' );
            $table->integer( 'estado' );
            $table->integer( 'id_tramite' );
            $table->integer( 'id_periodo_gestion' );

            $table->foreign( 'id_tramite' )->references( 'id_tramite' )->on( 'tramite' )->onDelete( 'cascade' );
            $table->foreign( 'id_periodo_gestion' )->references( 'id_periodo_gestion' )->on( 'periodo_gestion' )->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('habilitacion_tramite');
    }
}
