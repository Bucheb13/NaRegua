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
        Schema::table('vendas', function (Blueprint $table) {
            $table->foreign(['barbearia_id'])->references(['id'])->on('barbearias')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['cliente_id'])->references(['id'])->on('usuarios')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropForeign('vendas_barbearia_id_foreign');
            $table->dropForeign('vendas_cliente_id_foreign');
        });
    }
};
