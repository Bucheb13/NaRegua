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
        Schema::create('vendas_itens', function (Blueprint $table) {
            $table->id();
$table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
$table->foreignId('produto_id')
      ->constrained()
      ->restrictOnDelete();
$table->integer('quantidade')->default(1);
$table->decimal('preco_unitario', 10, 2);
$table->decimal('subtotal', 10, 2);
$table->timestamps();
$table->index('venda_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas_itens');
    }
};
