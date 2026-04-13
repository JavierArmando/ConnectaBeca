<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBecasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('becas', function (Blueprint $table) {
            $table->id();
            
            // --- A COLUMNAS ---
            $table->string('nombre');                 // Para el nombre de la beca
            $table->string('tipo');                   // Básica, Superior, etc.
            $table->decimal('monto', 10, 2)->nullable(); // Dinero (Ej. 5000.00)
            $table->text('descripcion')->nullable();  // Descripción larga
            $table->string('imagen')->nullable();     // Ruta de la foto
            // -----------------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('becas');
    }
}