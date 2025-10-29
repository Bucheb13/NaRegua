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
            $table->id(); // Chave primária
            $table->foreignId('barbearia_id')->nullable()->constrained('barbearias')->nullOnDelete(); 
            // FK opcional para barbearia, permite clientes externos
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha'); // armazenaremos hash da senha
            $table->enum('tipo', ['admin','barbeiro','cliente']);
            $table->string('telefone')->nullable();
            $table->timestamps(); // created_at e updated_at
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
