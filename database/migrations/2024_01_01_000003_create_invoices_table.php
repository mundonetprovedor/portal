<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('fatura_augix')->unique();
            $table->string('contrato_augix');
            $table->string('cliente_augix');
            $table->string('numero')->nullable();
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_pago', 10, 2)->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('situacao')->default('A');
            $table->text('codigo_barras')->nullable();
            $table->text('pix_copia_cola')->nullable();
            $table->string('pix_qr_code')->nullable();
            $table->text('obs')->nullable();
            $table->timestamps();

            $table->foreign('contrato_augix')->references('contrato_augix')->on('contracts');
            $table->foreign('cliente_augix')->references('cliente_augix')->on('clients');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
