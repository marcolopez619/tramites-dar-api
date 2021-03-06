<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnulacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anulacion', function (Blueprint $table) {
            $table->id('id_anulacion');
            $table->timestamp('fecha_solicitud');
            $table->string( 'motivo' );
            $table->integer('id_carrera_origen')->nullable( false );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anulacion');
    }
}
