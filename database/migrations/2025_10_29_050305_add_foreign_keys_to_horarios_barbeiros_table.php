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
        Schema::table('horarios_barbeiros', function (Blueprint $table) {
            $table->foreign(['barbeiro_id'], 'horarios_barbeiros_barbeiro_id_fkey')->references(['id'])->on('usuarios')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios_barbeiros', function (Blueprint $table) {
            $table->dropForeign('horarios_barbeiros_barbeiro_id_fkey');
        });
    }
};
