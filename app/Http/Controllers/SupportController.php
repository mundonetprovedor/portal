<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Ticket;
use App\Services\IxcService;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    protected IxcService $ixcService;

    public function __construct(IxcService $ixcService)
    {
        $this->ixcService = $ixcService;
    }

    public function index()
    {
        $clientId = session('client_id');

        if (!$clientId) {
            return redirect()->route('login');
        }

        $client = Client::findOrFail($clientId);
        $contracts = Contract::where('cliente_augix', $client->cliente_augix)->get();
        $tickets = Ticket::where('cliente_augix', $client->cliente_augix)
            ->orderByDesc('data_abertura')
            ->get();

        return view('support.index', compact('client', 'contracts', 'tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contrato_augix' => 'required|string',
            'tipo_problema' => 'required|in:semconexao,lentidao',
            'observacoes' => 'required|string|min:10|max:500',
        ], [
            'contrato_augix.required' => 'Selecione o contrato.',
            'tipo_problema.required' => 'Selecione o tipo do problema.',
            'tipo_problema.in' => 'Tipo de problema inválido.',
            'observacoes.required' => 'Descreva o problema.',
            'observacoes.min' => 'A descrição deve ter no mínimo 10 caracteres.',
            'observacoes.max' => 'A descrição deve ter no máximo 500 caracteres.',
        ]);

        $clientId = session('client_id');
        $client = Client::findOrFail($clientId);

        $tipoLabel = match($request->tipo_problema) {
            'semconexao' => 'Sem Conexão',
            'lentidao' => 'Lentidão Constante',
            default => $request->tipo_problema,
        };

        try {
            $result = $this->ixcService->createTicket(
                $client->cliente_augix,
                $request->contrato_augix,
                $tipoLabel,
                $request->observacoes
            );

            $osAugix = $result['id'] ?? $result['su_oss_chamado'] ?? null;
            $protocolo = $result['protocolo'] ?? null;

            $ticket = Ticket::create([
                'os_augix' => $osAugix,
                'cliente_augix' => $client->cliente_augix,
                'contrato_augix' => $request->contrato_augix,
                'tipo_problema' => $tipoLabel,
                'observacoes' => $request->observacoes,
                'situacao' => 'A',
                'protocolo' => $protocolo,
                'data_abertura' => now(),
            ]);

            return redirect()->route('support')
                ->with('success', 'Chamado aberto com sucesso!')
                ->with('new_ticket', $ticket);

        } catch (\Exception $e) {
            return redirect()->route('support')
                ->with('error', 'Erro ao abrir chamado: ' . $e->getMessage());
        }
    }
}
