<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transferencia', function (Blueprint $table) {
            $table->id( 'id_transferencia' );
            $table->integer( 'id_carrera_origen' )->nullable( false );
            $table->integer( 'id_carrera_destino' )->nullable( false );
            $table->timestamp( 'fecha_solicitud' )->nullable( false );
            $table->string( 'motivo' );
            $table->boolean( 'convalidacion' )->default( false );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transferencia');
    }
}
