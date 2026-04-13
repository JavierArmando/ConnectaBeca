<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConctactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conctacts', function (Blueprint $table) {
            $table->id();
            $table-> string('nombre',80);
            $table->string('correo',60);
            $table->enum('prioridad',['Alta','Media','Baja']);
            $table->string('asunto',60);
            $table->text('mensaje');
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
        Schema::dropIfExists('conctacts');
    }
}
