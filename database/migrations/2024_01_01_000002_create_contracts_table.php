<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contrato_augix')->unique();
            $table->string('cliente_augix');
            $table->string('plano_nome');
            $table->string('plano_velocidade')->nullable();
            $table->decimal('plano_valor', 10, 2)->nullable();
            $table->date('data_ativacao')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->string('situacao')->default('A');
            $table->string('tipo')->nullable();
            $table->text('obs')->nullable();
            $table->timestamps();

            $table->foreign('cliente_augix')->references('cliente_augix')->on('clients');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
