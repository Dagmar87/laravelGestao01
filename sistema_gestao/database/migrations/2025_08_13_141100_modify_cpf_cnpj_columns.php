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
        // Alterar a coluna cnpj na tabela unidades
        Schema::table('unidades', function (Blueprint $table) {
            $table->string('cnpj', 14)->change();
        });

        // Alterar a coluna cpf na tabela colaboradors
        Schema::table('colaboradors', function (Blueprint $table) {
            $table->string('cpf', 11)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter as alterações em caso de rollback
        Schema::table('unidades', function (Blueprint $table) {
            $table->bigInteger('cnpj')->change();
        });

        Schema::table('colaboradors', function (Blueprint $table) {
            $table->bigInteger('cpf')->change();
        });
    }
};
