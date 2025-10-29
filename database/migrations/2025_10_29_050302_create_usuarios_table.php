<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('barbearia_id')->nullable();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->enum('tipo', ['admin', 'barbeiro', 'cliente']);
            $table->string('telefone')->nullable();
            $table->timestamps();
            $table->string('especialidade')->nullable();
            $table->string('status', 20)->nullable()->default('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
