<?php

namespace App\Http\Controllers;

use App\Services\IxcService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected IxcService $ixcService;

    public function __construct(IxcService $ixcService)
    {
        $this->ixcService = $ixcService;
    }

    public function showLogin()
    {
        if (session('client_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|min:11',
        ], [
            'cpf.required' => 'Por favor, informe seu CPF.',
            'cpf.min' => 'CPF deve conter 11 dígitos.',
        ]);

        $cpf = $this->ixcService->cleanCpf($request->cpf);

        if (!$this->ixcService->validateCpf($cpf)) {
            return back()->withErrors(['cpf' => 'CPF inválido. Verifique e tente novamente.'])->withInput();
        }

        try {
            $clients = $this->ixcService->findClientByCpf($cpf);

            if (empty($clients)) {
                return back()->withErrors(['cpf' => 'CPF não encontrado em nossa base de dados.'])->withInput();
            }

            if (count($clients) === 1) {
                $clientData = $clients[0];
                $client = $this->ixcService->syncClient($clientData);

                $contracts = $this->ixcService->getContracts($client->cliente_augix);
                $this->ixcService->syncContracts($client->cliente_augix, $contracts);

                Session::put('client_id', $client->id);
                Session::put('client_cpf', $client->cpf);
                Session::put('client_name', $client->nome);

                return redirect()->route('dashboard');
            }

            Session::put('multiple_clients', $clients);
            Session::put('cpf_lookup', $cpf);

            return view('auth.select-contract', ['clients' => $clients]);

        } catch (\Exception $e) {
            return back()->withErrors(['cpf' => 'Erro ao consultar dados. Tente novamente mais tarde.'])->withInput();
        }
    }

    public function selectContract(Request $request)
    {
        $request->validate([
            'client_index' => 'required|integer',
        ]);

        $clients = Session::get('multiple_clients');

        if (!$clients || !isset($clients[$request->client_index])) {
            return redirect()->route('login')->withErrors(['cpf' => 'Sessão expirada. Faça login novamente.']);
        }

        $clientData = $clients[$request->client_index];
        $client = $this->ixcService->syncClient($clientData);

        $contracts = $this->ixcService->getContracts($client->cliente_augix);
        $this->ixcService->syncContracts($client->cliente_augix, $contracts);

        Session::forget('multiple_clients');
        Session::forget('cpf_lookup');

        Session::put('client_id', $client->id);
        Session::put('client_cpf', $client->cpf);
        Session::put('client_name', $client->nome);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Session::forget('client_id');
        Session::forget('client_cpf');
        Session::forget('client_name');
        Session::forget('multiple_clients');
        Session::forget('cpf_lookup');

        return redirect()->route('login');
    }
}
