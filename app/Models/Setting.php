<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'chave',
        'valor',
        'grupo',
    ];

    public static function get($chave, $default = null)
    {
        $setting = self::where('chave', $chave)->first();
        return $setting ? $setting->valor : $default;
    }

    public static function set($chave, $valor, $grupo = 'geral')
    {
        return self::updateOrCreate(
            ['chave' => $chave],
            ['valor' => $valor, 'grupo' => $grupo]
        );
    }

    public static function getGroup($grupo)
    {
        return self::where('grupo', $grupo)->pluck('valor', 'chave')->toArray();
    }
}
