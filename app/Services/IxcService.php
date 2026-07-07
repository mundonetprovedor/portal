<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\IxcApiException;

class IxcService
{
    protected string $baseUrl;
    protected string $token;
    protected string $secret;
    protected int $timeout;
    protected int $connectTimeout;

    public function __construct()
    {
        $this->baseUrl = config('ixc.api_url');
        $this->token = config('ixc.token');
        $this->secret = config('ixc.secret');
        $this->timeout = config('ixc.timeout', 30);
        $this->connectTimeout = config('ixc.connect_timeout', 10);

        $dbToken = Setting::get('ixc_token');
        $dbSecret = Setting::get('ixc_secret');
        $dbUrl = Setting::get('ixc_url');

        if ($dbToken) $this->token = $dbToken;
        if ($dbSecret) $this->secret = $dbSecret;
        if ($dbUrl) $this->baseUrl = $dbUrl;
    }

    protected function getClient(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->withOptions([
            'verify' => false,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
        ]);
    }

    protected function throwException(string $message, $response = null): void
    {
        Log::error('IXC API Error: ' . $message, [
            'response' => $response?->body(),
            'status' => $response?->status(),
        ]);
        throw new IxcApiException($message);
    }

    public function findClientByCpf(string $cpf): array
    {
        $cpf = $this->cleanCpf($cpf);

        try {
            $response = $this->getClient()
                ->get("{$this->baseUrl}/api/clientes", [
                    ' cpf' => $cpf,
                ]);

            if (!$response->successful()) {
                $this->throwException('Erro ao consultar cliente na API IXC', $response);
            }

            $data = $response->json();

            if (empty($data['registros']) || !is_array($data['registros'])) {
                return [];
            }

            return $data['registros'];
        } catch (IxcApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('IXC API Connection Error: ' . $e->getMessage());
            throw new IxcApiException('Não foi possível conectar ao servidor IXC. Verifique a configuração.');
        }
    }

    public function getContracts(string $clienteAugix): array
    {
        try {
            $response = $this->getClient()
                ->get("{$this->baseUrl}/api/clientes_contratos", [
                    'clientes_augix' => $clienteAugix,
                ]);

            if (!$response->successful()) {
                $this->throwException('Erro ao consultar contratos na API IXC', $response);
            }

            $data = $response->json();

            return $data['registros'] ?? [];
        } catch (IxcApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('IXC API Connection Error: ' . $e->getMessage());
            throw new IxcApiException('Não foi possível conectar ao servidor IXC.');
        }
    }

    public function getInvoices(string $contratoAugix): array
    {
        try {
            $response = $this->getClient()
                ->get("{$this->baseUrl}/api/clientes_boletos", [
                    'clientes_contratos_augix' => $contratoAugix,
                ]);

            if (!$response->successful()) {
                $this->throwException('Erro ao consultar faturas na API IXC', $response);
            }

            $data = $response->json();

            return $data['registros'] ?? [];
        } catch (IxcApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('IXC API Connection Error: ' . $e->getMessage());
            throw new IxcApiException('Não foi possível conectar ao servidor IXC.');
        }
    }

    public function getTicket(string $osAugix): ?array
    {
        try {
            $response = $this->getClient()
                ->get("{$this->baseUrl}/api/chamados/{$osAugix}");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            return $data['registros'][0] ?? null;
        } catch (\Exception $e) {
            Log::error('IXC API Error getting ticket: ' . $e->getMessage());
            return null;
        }
    }

    public function createTicket(string $clienteAugix, string $contratoAugix, string $tipo, string $obs): array
    {
        try {
            $payload = [
                'clientes_augix' => $clienteAugix,
                'clientes_contratos_augix' => $contratoAugix,
                'assunto' => $tipo,
                'descricao' => $obs,
                'tipo' => 'S',
                'prioridade' => 'N',
            ];

            $response = $this->getClient()
                ->post("{$this->baseUrl}/api/chamados", $payload);

            if (!$response->successful()) {
                $this->throwException('Erro ao criar chamado na API IXC', $response);
            }

            $data = $response->json();

            return $data;
        } catch (IxcApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('IXC API Error creating ticket: ' . $e->getMessage());
            throw new IxcApiException('Não foi possível criar o chamado.');
        }
    }

    public function syncClient(array $ixcClient): Client
    {
        $clienteAugix = $ixcClient['id'] ?? $ixcClient['cliente_augix'] ?? uniqid();

        return Client::updateOrCreate(
            ['cliente_augix' => $clienteAugix],
            [
                'nome' => $ixcClient['nome'] ?? '',
                'cpf' => $this->formatCpf($ixcClient['cpf'] ?? ''),
                'rg' => $ixcClient['rg'] ?? null,
                'telefone' => $ixcClient['telefone'] ?? null,
                'celular' => $ixcClient['celular'] ?? null,
                'email' => $ixcClient['email'] ?? null,
                'data_nascimento' => $ixcClient['data_nascimento'] ?? null,
                'sexo' => $ixcClient['sexo'] ?? null,
                'rua' => $ixcClient['rua'] ?? null,
                'numero' => $ixcClient['numero'] ?? null,
                'bairro' => $ixcClient['bairro'] ?? null,
                'cidade' => $ixcClient['cidade'] ?? null,
                'estado' => $ixcClient['estado'] ?? null,
                'cep' => $ixcClient['cep'] ?? null,
                'complemento' => $ixcClient['complemento'] ?? null,
                'situacao' => $ixcClient['situacao'] ?? 'A',
                'obs' => $ixcClient['obs'] ?? null,
            ]
        );
    }

    public function syncContracts(string $clienteAugix, array $ixcContracts): void
    {
        foreach ($ixcContracts as $contract) {
            $contratoAugix = $contract['id'] ?? $contract['clientes_contratos_augix'] ?? uniqid();

            Contract::updateOrCreate(
                ['contrato_augix' => $contratoAugix],
                [
                    'cliente_augix' => $clienteAugix,
                    'plano_nome' => $contract['planos_nome'] ?? $contract['plano'] ?? '',
                    'plano_velocidade' => $contract['planos_velocidade'] ?? null,
                    'plano_valor' => $contract['planos_valor'] ?? null,
                    'data_ativacao' => $contract['data_ativacao'] ?? null,
                    'data_vencimento' => $contract['data_vencimento'] ?? null,
                    'situacao' => $contract['situacao'] ?? 'A',
                    'tipo' => $contract['tipo'] ?? null,
                    'obs' => $contract['obs'] ?? null,
                ]
            );
        }
    }

    public function syncInvoices(string $contratoAugix, string $clienteAugix, array $ixcInvoices): void
    {
        foreach ($ixcInvoices as $invoice) {
            $faturaAugix = $invoice['id'] ?? $invoice['clientes_boletos_augix'] ?? uniqid();

            Invoice::updateOrCreate(
                ['fatura_augix' => $faturaAugix],
                [
                    'contrato_augix' => $contratoAugix,
                    'cliente_augix' => $clienteAugix,
                    'numero' => $invoice['numero'] ?? null,
                    'valor' => $invoice['valor'] ?? 0,
                    'valor_pago' => $invoice['valor_pago'] ?? null,
                    'data_emissao' => $invoice['data_emissao'] ?? null,
                    'data_vencimento' => $invoice['data_vencimento'] ?? now(),
                    'data_pagamento' => $invoice['data_pagamento'] ?? null,
                    'situacao' => $invoice['situacao'] ?? 'A',
                    'codigo_barras' => $invoice['linha_digitavel'] ?? $invoice['codigo_barras'] ?? null,
                    'pix_copia_cola' => $invoice['pix_copia_cola'] ?? null,
                    'pix_qr_code' => $invoice['pix_qr_code'] ?? null,
                    'obs' => $invoice['obs'] ?? null,
                ]
            );
        }
    }

    public function syncTicket(string $clienteAugix, array $ixcTicket): Ticket
    {
        $osAugix = $ixcTicket['id'] ?? $ixcTicket['chamados_augix'] ?? uniqid();

        return Ticket::updateOrCreate(
            ['os_augix' => $osAugix],
            [
                'cliente_augix' => $clienteAugix,
                'contrato_augix' => $ixcTicket['clientes_contratos_augix'] ?? null,
                'tipo_problema' => $ixcTicket['assunto'] ?? $ixcTicket['tipo'] ?? '',
                'observacoes' => $ixcTicket['descricao'] ?? null,
                'situacao' => $ixcTicket['situacao'] ?? 'A',
                'protocolo' => $ixcTicket['protocolo'] ?? null,
                'data_abertura' => $ixcTicket['data_abertura'] ?? null,
                'data_fechamento' => $ixcTicket['data_fechamento'] ?? null,
            ]
        );
    }

    public function cleanCpf(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf);
    }

    public function formatCpf(string $cpf): string
    {
        $cpf = $this->cleanCpf($cpf);
        if (strlen($cpf) !== 11) return $cpf;
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    public function validateCpf(string $cpf): bool
    {
        $cpf = $this->cleanCpf($cpf);

        if (strlen($cpf) !== 11) return false;
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $rest = $sum % 11;
        $digit1 = $rest < 2 ? 0 : 11 - $rest;

        if ($cpf[9] != $digit1) return false;

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $rest = $sum % 11;
        $digit2 = $rest < 2 ? 0 : 11 - $rest;

        return $cpf[10] == $digit2;
    }
}
