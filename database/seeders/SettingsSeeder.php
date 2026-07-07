<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['chave' => 'company_name', 'valor' => 'MUNDONET', 'grupo' => 'visual'],
            ['chave' => 'primary_color', 'valor' => '#0B3D91', 'grupo' => 'visual'],
            ['chave' => 'secondary_color', 'valor' => '#1a5cc7', 'grupo' => 'visual'],
            ['chave' => 'logo_path', 'valor' => null, 'grupo' => 'visual'],
            ['chave' => 'ixc_url', 'valor' => env('IXC_API_URL', ''), 'grupo' => 'api'],
            ['chave' => 'ixc_token', 'valor' => env('IXC_API_TOKEN', ''), 'grupo' => 'api'],
            ['chave' => 'ixc_secret', 'valor' => env('IXC_API_SECRET', ''), 'grupo' => 'api'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['chave' => $setting['chave']],
                $setting
            );
        }
    }
}
