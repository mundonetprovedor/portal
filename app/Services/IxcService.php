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
        $this->baseUrl = rtrim(config('ixc.api_url', env('IXC_API_URL', '')), '/');
        $this->token = config('ixc.token', env('IXC_API_TOKEN', ''));
        $this->secret = config('ixc.secret', env('IXC_API_SECRET', ''));
        $this->timeout = config('ixc.timeout', 30);
        $this->connectTimeout = config('ixc.connect_timeout', 10);

        $dbToken = Setting::get('ixc_token');
        $dbSecret = Setting::get('ixc_secret');
        $dbUrl = Setting::get('ixc_url');

        if ($dbToken) $this->token = $dbToken;
        if ($dbSecret) $this->secret = $dbSecret;
        if ($dbUrl) $this->baseUrl = rtrim($dbUrl, '/');
    }

    protected function getClient(): \Illuminate\Http\Client\PendingRequest
    {
        $auth = base64_encode($this->token . ':' . $this->secret);

        return Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
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
            'url' => $response?->effectiveUri(),
        ]);
        throw new IxcApiException($message);
    }

    public function findClientByCpf(string $cpf): array
    {
        $cpf = $this->cleanCpf($cpf);
        $cpfFormatted = $this->formatCpf($cpf);

        try {
            $response = $this->getClient()
                ->withHeader('ixcsoft', 'listar')
                ->post("{$this->baseUrl}/webservice/v1/cliente", [
                    'qtype' => 'cliente.cnpj_cpf',
                    'query' => $cpfFormatted,
                    'oper' => '=',
                    'page' => '1',
                    'rp' => '100',
                    'sortname' => 'cliente.id',
                    'sortorder' => 'asc',
                ]);

            if (!$response->successful()) {
                $this->throwException('Erro ao consultar cliente na API IXC (HTTP ' . $response->status() . ')', $response);
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
            throw new IxcApiException('Não foi possível conectar ao servidor IXC. Verifique a configuração da API.');
        }
    }

    public function getContracts(string $clienteAugix): array
    {
        try {
            $response = $this->getClient()
                ->withHeader('ixcsoft', 'listar')
                ->post("{$this->baseUrl}/webservice/v1/cliente_contrato", [
                    'qtype' => 'cliente_contrato.id_cliente',
                    'query' => $clienteAugix,
                    'oper' => '=',
                    'page' => '1',
                    'rp' => '1000',
                    'sortname' => 'cliente_contrato.id',
                    'sortorder' => 'desc',
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
                ->withHeader('ixcsoft', 'listar')
                ->post("{$this->baseUrl}/webservice/v1/fn_areceber", [
                    'qtype' => 'fn_areceber.id_contrato',
                    'query' => $contratoAugix,
                    'oper' => '=',
                    'page' => '1',
                    'rp' => '1000',
                    'sortname' => 'fn_areceber.data_vencimento',
                    'sortorder' => 'asc',
                    'grid_param' => json_encode([
                        ['TB' => 'fn_areceber.liberado', 'OP' => '=', 'P' => 'S'],
                        ['TB' => 'fn_areceber.status', 'OP' => '!=', 'P' => 'C'],
                        ['TB' => 'fn_areceber.status', 'OP' => '!=', 'P' => 'R'],
                    ]),
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
                ->withHeader('ixcsoft', 'listar')
                ->post("{$this->baseUrl}/webservice/v1/su_oss_chamado", [
                    'qtype' => 'su_oss_chamado.id',
                    'query' => $osAugix,
                    'oper' => '=',
                    'page' => '1',
                    'rp' => '1',
                    'sortname' => 'su_oss_chamado.id',
                    'sortorder' => 'desc',
                ]);

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

    public function getClientTickets(string $clienteAugix): array
    {
        try {
            $response = $this->getClient()
                ->withHeader('ixcsoft', 'listar')
                ->post("{$this->baseUrl}/webservice/v1/su_oss_chamado", [
                    'qtype' => 'su_oss_chamado.id_cliente',
                    'query' => $clienteAugix,
                    'oper' => '=',
                    'page' => '1',
                    'rp' => '1000',
                    'sortname' => 'su_oss_chamado.id',
                    'sortorder' => 'desc',
                ]);

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['registros'] ?? [];
        } catch (\Exception $e) {
            Log::error('IXC API Error getting client tickets: ' . $e->getMessage());
            return [];
        }
    }

    public function createTicket(string $clienteAugix, string $contratoAugix, string $tipo, string $obs): array
    {
        try {
            $payload = [
                'tipo' => 'C',
                'id_cliente' => $clienteAugix,
                'id_assunto' => '1',
                'id_filial' => '1',
                'origem_endereco' => 'M',
                'prioridade' => '1',
                'setor' => '1',
                'status' => 'A',
                'mensagem' => $obs,
            ];

            if ($contratoAugix) {
                $payload['id_contrato_kit'] = $contratoAugix;
            }

            $response = $this->getClient()
                ->post("{$this->baseUrl}/webservice/v1/su_oss_chamado", $payload);

            if (!$response->successful()) {
                $this->throwException('Erro ao criar chamado na API IXC (HTTP ' . $response->status() . ')', $response);
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

    public function getPix(string $idAreceber): ?array
    {
        try {
            $response = $this->getClient()
                ->post("{$this->baseUrl}/webservice/v1/get_pix", [
                    'id_areceber' => $idAreceber,
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('IXC API Error getting PIX: ' . $e->getMessage());
            return null;
        }
    }

    public function getBoleto(string $idAreceber): ?array
    {
        try {
            $response = $this->getClient()
                ->post("{$this->baseUrl}/webservice/v1/get_boleto", [
                    'boletos' => $idAreceber,
                    'tipo_boleto' => 'arquivo',
                    'base64' => 'S',
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('IXC API Error getting boleto: ' . $e->getMessage());
            return null;
        }
    }

    public function syncClient(array $ixcClient): Client
    {
        $clienteAugix = $ixcClient['id'] ?? uniqid();

        return Client::updateOrCreate(
            ['cliente_augix' => $clienteAugix],
            [
                'nome' => $ixcClient['razao'] ?? $ixcClient['nome'] ?? '',
                'cpf' => $this->formatCpf($ixcClient['cnpj_cpf'] ?? $ixcClient['cpf'] ?? ''),
                'rg' => $ixcClient['ie_identidade'] ?? null,
                'telefone' => $ixcClient['fone'] ?? $ixcClient['telefone'] ?? null,
                'celular' => $ixcClient['telefone_celular'] ?? $ixcClient['celular'] ?? null,
                'email' => $ixcClient['email'] ?? null,
                'data_nascimento' => $ixcClient['data_nascimento'] ?? null,
                'sexo' => $ixcClient['Sexo'] ?? $ixcClient['sexo'] ?? null,
                'rua' => $ixcClient['endereco'] ?? $ixcClient['rua'] ?? null,
                'numero' => $ixcClient['numero'] ?? null,
                'bairro' => $ixcClient['bairro'] ?? null,
                'cidade' => $ixcClient['cidade'] ?? null,
                'estado' => $ixcClient['uf'] ?? $ixcClient['estado'] ?? null,
                'cep' => $ixcClient['cep'] ?? null,
                'complemento' => $ixcClient['complemento'] ?? null,
                'situacao' => $ixcClient['ativo'] === 'S' ? 'A' : ($ixcClient['situacao'] ?? 'I'),
                'obs' => $ixcClient['obs'] ?? null,
            ]
        );
    }

    public function syncContracts(string $clienteAugix, array $ixcContracts): void
    {
        foreach ($ixcContracts as $contract) {
            $contratoAugix = $contract['id'] ?? uniqid();

            $statusMap = [
                'A' => 'A',
                'I' => 'I',
                'P' => 'P',
                'N' => 'N',
                'D' => 'D',
            ];

            Contract::updateOrCreate(
                ['contrato_augix' => $contratoAugix],
                [
                    'cliente_augix' => $clienteAugix,
                    'plano_nome' => $contract['contrato'] ?? $contract['planos_nome'] ?? '',
                    'plano_velocidade' => $contract['planos_velocidade'] ?? null,
                    'plano_valor' => $contract['planos_valor'] ?? null,
                    'data_ativacao' => $contract['data_ativacao'] ?? null,
                    'data_vencimento' => $contract['data'] ?? $contract['data_vencimento'] ?? null,
                    'situacao' => $statusMap[$contract['status'] ?? 'A'] ?? 'A',
                    'tipo' => $contract['tipo'] ?? null,
                    'obs' => $contract['obs'] ?? null,
                ]
            );
        }
    }

    public function syncInvoices(string $contratoAugix, string $clienteAugix, array $ixcInvoices): void
    {
        foreach ($ixcInvoices as $invoice) {
            $faturaAugix = $invoice['id'] ?? uniqid();

            Invoice::updateOrCreate(
                ['fatura_augix' => $faturaAugix],
                [
                    'contrato_augix' => $contratoAugix,
                    'cliente_augix' => $clienteAugix,
                    'numero' => $invoice['documento'] ?? null,
                    'valor' => $invoice['valor'] ?? 0,
                    'valor_pago' => $invoice['valor_recebido'] ?? null,
                    'data_emissao' => $invoice['data_emissao'] ?? null,
                    'data_vencimento' => $invoice['data_vencimento'] ?? now(),
                    'data_pagamento' => $invoice['pagamento_data'] ?? null,
                    'situacao' => $invoice['status'] ?? 'A',
                    'codigo_barras' => $invoice['nn_boleto'] ?? $invoice['boleto'] ?? null,
                    'pix_copia_cola' => $invoice['pix_txid'] ?? null,
                    'pix_qr_code' => null,
                    'obs' => $invoice['obs'] ?? null,
                ]
            );
        }
    }

    public function syncTicket(string $clienteAugix, array $ixcTicket): Ticket
    {
        $osAugix = $ixcTicket['id'] ?? uniqid();

        return Ticket::updateOrCreate(
            ['os_augix' => $osAugix],
            [
                'cliente_augix' => $clienteAugix,
                'contrato_augix' => $ixcTicket['id_contrato_kit'] ?? null,
                'tipo_problema' => $ixcTicket['mensagem'] ?? $ixcTicket['assunto'] ?? '',
                'observacoes' => $ixcTicket['mensagem'] ?? null,
                'situacao' => $ixcTicket['status'] ?? 'A',
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
