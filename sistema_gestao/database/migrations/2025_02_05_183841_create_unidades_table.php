<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome_fantasia');
            $table->string('razao_social');
            $table->bigInteger('cnpj');
            $table->integer('bandeira_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('unidades', function ($table) {
            $table->foreign('bandeira_id')->references('id')->on('bandeiras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
