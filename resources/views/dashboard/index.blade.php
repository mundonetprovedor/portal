@extends('layouts.app')

@section('title', 'Dashboard - Portal MUNDONET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Painel do Assinante</h4>
        <p class="text-muted mb-0">Bem-vindo(a), {{ $client->nome }}</p>
    </div>
    <a href="{{ route('dashboard.refresh') }}" class="btn btn-primary-custom btn-sm">
        <i class="fas fa-sync-alt me-1"></i> Atualizar Dados
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-custom no-hover">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e8f0fe;color:var(--primary-color);">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-value">{{ $contracts->count() }}</div>
                <div class="stat-label">Contrato(s)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom no-hover">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff3cd;color:#856404;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-value">{{ $openInvoices->count() }}</div>
                <div class="stat-label">Fatura(s) em Aberto</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom no-hover">
            <div class="stat-card">
                <div class="stat-icon" style="background:#f8d7da;color:#721c24;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-value">{{ $overdueInvoices->count() }}</div>
                <div class="stat-label">Fatura(s) Vencida(s)</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fas fa-user"></i> Dados do Titular
            </div>
            <div class="card-body-custom">
                <div class="info-row">
                    <span class="info-label">Nome</span>
                    <span class="info-value">{{ $client->nome }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">CPF</span>
                    <span class="info-value">{{ $client->cpf }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Código</span>
                    <span class="info-value">{{ $client->cliente_augix }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Situação</span>
                    <span class="info-value">
                        @if($client->situacao === 'A')
                            <span class="badge badge-status badge-active">Ativo</span>
                        @else
                            <span class="badge badge-status badge-inactive">Inativo</span>
                        @endif
                    </span>
                </div>
                @if($client->telefone)
                <div class="info-row">
                    <span class="info-label">Telefone</span>
                    <span class="info-value">{{ $client->telefone }}</span>
                </div>
                @endif
                @if($client->celular)
                <div class="info-row">
                    <span class="info-label">Celular</span>
                    <span class="info-value">{{ $client->celular }}</span>
                </div>
                @endif
                @if($client->email)
                <div class="info-row">
                    <span class="info-label">E-mail</span>
                    <span class="info-value">{{ $client->email }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="card card-custom mt-4">
            <div class="card-header-custom">
                <i class="fas fa-map-marker-alt"></i> Endereço
            </div>
            <div class="card-body-custom">
                @if($client->rua)
                <div class="info-row">
                    <span class="info-label">Rua</span>
                    <span class="info-value">{{ $client->rua }}{{ $client->numero ? ', ' . $client->numero : '' }}</span>
                </div>
                @endif
                @if($client->bairro)
                <div class="info-row">
                    <span class="info-label">Bairro</span>
                    <span class="info-value">{{ $client->bairro }}</span>
                </div>
                @endif
                @if($client->cidade)
                <div class="info-row">
                    <span class="info-label">Cidade</span>
                    <span class="info-value">{{ $client->cidade }}{{ $client->estado ? ' - ' . $client->estado : '' }}</span>
                </div>
                @endif
                @if($client->cep)
                <div class="info-row">
                    <span class="info-label">CEP</span>
                    <span class="info-value">{{ $client->cep }}</span>
                </div>
                @endif
                @if(!$client->rua && !$client->bairro && !$client->cidade)
                <p class="text-muted text-center py-3 mb-0">
                    <i class="fas fa-info-circle me-1"></i> Endereço não disponível
                </p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        @foreach($contracts as $contract)
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <i class="fas fa-wifi"></i> Plano Contratado
            </div>
            <div class="card-body-custom">
                <div class="info-row">
                    <span class="info-label">Plano</span>
                    <span class="info-value fw-bold">{{ $contract->plano_nome }}</span>
                </div>
                @if($contract->plano_velocidade)
                <div class="info-row">
                    <span class="info-label">Velocidade</span>
                    <span class="info-value">{{ $contract->plano_velocidade }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        @if($contract->situacao === 'A')
                            <span class="badge badge-status badge-active">Ativo</span>
                        @else
                            <span class="badge badge-status badge-inactive">Inativo</span>
                        @endif
                    </span>
                </div>
                @if($contract->data_ativacao)
                <div class="info-row">
                    <span class="info-label">Ativação</span>
                    <span class="info-value">{{ $contract->data_ativacao->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($contract->plano_valor)
                <div class="info-row">
                    <span class="info-label">Valor</span>
                    <span class="info-value">R$ {{ number_format($contract->plano_valor, 2, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fas fa-file-invoice-dollar"></i> Últimas Faturas
            </div>
            <div class="card-body-custom p-0">
                @if($allInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allInvoices->take(5) as $invoice)
                            <tr class="{{ $invoice->isOverdue() ? 'invoice-overdue' : '' }}">
                                <td>{{ $invoice->numero ?? $invoice->fatura_augix }}</td>
                                <td>R$ {{ number_format($invoice->valor, 2, ',', '.') }}</td>
                                <td>{{ $invoice->data_vencimento->format('d/m/Y') }}</td>
                                <td>
                                    @if($invoice->isOverdue())
                                        <span class="badge badge-status badge-inactive">Vencida</span>
                                    @elseif($invoice->isOpen())
                                        <span class="badge badge-status badge-pending">Em Aberto</span>
                                    @else
                                        <span class="badge badge-status badge-active">Paga</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-4 mb-0">
                    <i class="fas fa-info-circle me-1"></i> Nenhuma fatura encontrada
                </p>
                @endif
            </div>
            @if($allInvoices->count() > 5)
            <div class="card-body-custom border-top text-center">
                <a href="{{ route('invoices') }}" class="btn btn-outline-custom btn-sm">
                    Ver Todas as Faturas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
