<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $adminPassword = config('ixc.admin_password', env('ADMIN_PASSWORD', 'changeme'));
        $dbPassword = Setting::get('admin_password', $adminPassword);

        if (!Hash::check($request->password, $dbPassword) && $request->password !== $dbPassword) {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }

        session(['admin_authenticated' => true]);

        return redirect()->route('admin.dashboard');
    }

    public function dashboard()
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $settings = Setting::pluck('valor', 'chave')->toArray();

        return view('admin.dashboard', compact('settings'));
    }

    public function saveApiConfig(Request $request)
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'ixc_url' => 'required|url',
            'ixc_token' => 'required|string',
            'ixc_secret' => 'required|string',
        ]);

        Setting::set('ixc_url', $request->ixc_url, 'api');
        Setting::set('ixc_token', $request->ixc_token, 'api');
        Setting::set('ixc_secret', $request->ixc_secret, 'api');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Configurações da API salvas com sucesso!');
    }

    public function saveVisualConfig(Request $request)
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
        ]);

        Setting::set('company_name', $request->company_name, 'visual');
        Setting::set('primary_color', $request->primary_color, 'visual');
        Setting::set('secondary_color', $request->secondary_color, 'visual');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logo'), $filename);
            Setting::set('logo_path', 'uploads/logo/' . $filename, 'visual');
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Configurações visuais salvas com sucesso!');
    }

    public function logout()
    {
        session()->forget('admin_authenticated');
        return redirect()->route('admin.login');
    }
}
