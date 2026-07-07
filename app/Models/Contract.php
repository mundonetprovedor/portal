<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    protected $fillable = [
        'contrato_augix',
        'cliente_augix',
        'plano_nome',
        'plano_velocidade',
        'plano_valor',
        'data_ativacao',
        'data_vencimento',
        'situacao',
        'tipo',
        'obs',
    ];

    protected $casts = [
        'data_ativacao' => 'date',
        'data_vencimento' => 'date',
        'plano_valor' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_augix', 'cliente_augix');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'contrato_augix', 'contrato_augix');
    }
}
