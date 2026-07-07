<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'fatura_augix',
        'contrato_augix',
        'cliente_augix',
        'numero',
        'valor',
        'valor_pago',
        'data_emissao',
        'data_vencimento',
        'data_pagamento',
        'situacao',
        'codigo_barras',
        'pix_copia_cola',
        'pix_qr_code',
        'obs',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contrato_augix', 'contrato_augix');
    }

    public function isOverdue()
    {
        return $this->situacao === 'A' && $this->data_vencimento->isPast();
    }

    public function isOpen()
    {
        return in_array($this->situacao, ['A', 'AB']);
    }
}
