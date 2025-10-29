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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('barbearia_id');
            $table->bigInteger('cliente_id');
            $table->bigInteger('barbeiro_id');
            $table->bigInteger('servico_id');
            $table->timestamp('data_hora');
            $table->enum('status', ['agendado', 'concluido', 'cancelado'])->default('agendado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
