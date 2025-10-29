<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
$table->foreignId('barbearia_id')->constrained()->cascadeOnDelete();
$table->foreignId('cliente_id')
      ->nullable()
      ->constrained('usuarios')
      ->nullOnDelete();
$table->decimal('valor_total', 10, 2)->default(0);
$table->dateTime('data_venda')->default(DB::raw('CURRENT_TIMESTAMP'));
$table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
