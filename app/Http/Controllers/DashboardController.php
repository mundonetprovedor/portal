<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Services\IxcService;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

        $allInvoices = collect();
        foreach ($contracts as $contract) {
            $invoices = Invoice::where('contrato_augix', $contract->contrato_augix)->get();
            $allInvoices = $allInvoices->concat($invoices);
        }

        $allInvoices = $allInvoices->sortByDesc('data_vencimento')->values();

        $openInvoices = $allInvoices->filter(fn($inv) => $inv->isOpen());
        $overdueInvoices = $allInvoices->filter(fn($inv) => $inv->isOverdue());

        return view('dashboard.index', compact(
            'client',
            'contracts',
            'allInvoices',
            'openInvoices',
            'overdueInvoices'
        ));
    }

    public function refreshData()
    {
        $clientId = session('client_id');

        if (!$clientId) {
            return redirect()->route('login');
        }

        try {
            $client = Client::findOrFail($clientId);

            $contracts = $this->ixcService->getContracts($client->cliente_augix);
            $this->ixcService->syncContracts($client->cliente_augix, $contracts);

            $localContracts = Contract::where('cliente_augix', $client->cliente_augix)->get();

            foreach ($localContracts as $contract) {
                $invoices = $this->ixcService->getInvoices($contract->contrato_augix);
                $this->ixcService->syncInvoices($contract->contrato_augix, $client->cliente_augix, $invoices);
            }

            return redirect()->route('dashboard')->with('success', 'Dados atualizados com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Erro ao atualizar dados: ' . $e->getMessage());
        }
    }

    public function invoices()
    {
        $clientId = session('client_id');

        if (!$clientId) {
            return redirect()->route('login');
        }

        $client = Client::findOrFail($clientId);
        $contracts = Contract::where('cliente_augix', $client->cliente_augix)->get();

        $allInvoices = collect();
        foreach ($contracts as $contract) {
            $invoices = Invoice::where('contrato_augix', $contract->contrato_augix)->get();
            $allInvoices = $allInvoices->concat($invoices);
        }

        $allInvoices = $allInvoices->sortByDesc('data_vencimento')->values();

        return view('dashboard.invoices', compact('client', 'allInvoices'));
    }

    public function invoiceDetail(string $id)
    {
        $clientId = session('client_id');

        if (!$clientId) {
            return redirect()->route('login');
        }

        $client = Client::findOrFail($clientId);
        $invoice = Invoice::where('fatura_augix', $id)
            ->where('cliente_augix', $client->cliente_augix)
            ->firstOrFail();

        return view('dashboard.invoice-detail', compact('client', 'invoice'));
    }
}
