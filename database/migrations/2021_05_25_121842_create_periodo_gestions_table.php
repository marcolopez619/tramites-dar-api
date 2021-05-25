<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
