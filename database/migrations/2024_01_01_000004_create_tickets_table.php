<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('os_augix')->nullable();
            $table->string('cliente_augix');
            $table->string('contrato_augix')->nullable();
            $table->string('tipo_problema');
            $table->text('observacoes')->nullable();
            $table->string('situacao')->default('A');
            $table->string('protocolo')->nullable();
            $table->timestamp('data_abertura')->nullable();
            $table->timestamp('data_fechamento')->nullable();
            $table->timestamps();

            $table->foreign('cliente_augix')->references('cliente_augix')->on('clients');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
