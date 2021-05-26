<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodoGestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodo_gestion', function (Blueprint $table) {
            $table->id( 'id_periodo_gestion' );
            $table->integer( 'id_periodo' );
            $table->integer( 'id_gestion' );
            $table->boolean( 'estado' );
            $table->timestamp( 'fecha_modificacion' )->default( DB::raw( 'NOW()' ));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('periodo_gestion');
    }
}
