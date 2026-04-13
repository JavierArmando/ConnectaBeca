<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Usuario que recibe la notificación
            $table->string('type'); // 'mensaje', 'beca_nueva', 'beca_actualizada'
            $table->string('title'); // Título de la notificación
            $table->text('message'); // Contenido
            $table->string('url')->nullable(); // Link a donde ir
            $table->boolean('read')->default(false); // Si fue leída
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'read']); // Para queries rápidas
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
