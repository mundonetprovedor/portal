<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'os_augix',
        'cliente_augix',
        'contrato_augix',
        'tipo_problema',
        'observacoes',
        'status',
        'protocolo',
        'data_abertura',
        'data_fechamento',
    ];

    protected $casts = [
        'data_abertura' => 'datetime',
        'data_fechamento' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_augix', 'cliente_augix');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contrato_augix', 'contrato_augix');
    }
}
