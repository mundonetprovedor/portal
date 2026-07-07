<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'cpf',
        'rg',
        'telefone',
        'celular',
        'email',
        'data_nascimento',
        'sexo',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'complemento',
        'cliente_augix',
        'situacao',
        'obs',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'cliente_augix', 'cliente_augix');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'cliente_augix', 'cliente_augix');
    }

    public function getRouteKeyName()
    {
        return 'cpf';
    }
}
