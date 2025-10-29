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
            $table->bigIncrements('id');
            $table->bigInteger('venda_id')->index();
            $table->bigInteger('produto_id');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_unitario', 10);
            $table->decimal('subtotal', 10);
            $table->timestamps();
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
