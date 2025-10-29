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
        Schema::create('barbeiros', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com usuários (barbeiros)
            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnDelete(); // Se usuário for deletado, exclui o barbeiro
            
            // Relacionamento com barbearia
            $table->foreignId('barbearia_id')
                ->nullable()
                ->constrained('barbearias')
                ->nullOnDelete(); // Se barbearia for deletada, define null
            
            $table->string('especialidade')->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barbeiros');
    }
};
