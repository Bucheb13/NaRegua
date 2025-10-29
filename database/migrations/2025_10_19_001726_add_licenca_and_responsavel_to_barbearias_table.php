<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->string('responsavel_nome')->nullable()->after('nome');
            $table->string('cnpj', 20)->nullable()->after('responsavel_nome');
            $table->enum('licenca_status', ['ativa', 'expirada', 'pendente'])->default('pendente')->after('cnpj');
            $table->date('licenca_validade')->nullable()->after('licenca_status');
            $table->string('logo')->nullable()->after('licenca_validade');
        });
    }

    public function down(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->dropColumn(['responsavel_nome', 'cnpj', 'licenca_status', 'licenca_validade', 'logo']);
        });
    }
};
