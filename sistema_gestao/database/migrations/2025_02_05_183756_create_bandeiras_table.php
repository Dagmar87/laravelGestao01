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
        Schema::create('bandeiras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome'); 
            $table->integer('grupo_economico_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('bandeiras', function($table){
            $table->foreign('grupo_economico_id')->references('id')->on('grupo_economicos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bandeiras');
    }
};
