@extends('layouts.app')

@section('title', 'Faturas - Portal MUNDONET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="fas fa-file-invoice-dollar me-2" style="color:var(--primary-color);"></i>
            Faturas
        </h4>
        <p class="text-muted mb-0">Visualize e gerencie suas faturas</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-custom btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
</div>

@if($allInvoices->count() > 0)
<div class="card card-custom">
    <div class="card-header-custom">
        <i class="fas fa-list"></i> Todas as Faturas
    </div>
    <div class="card-body-custom p-0">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allInvoices as $invoice)
                    <tr class="{{ $invoice->isOverdue() ? 'invoice-overdue' : '' }}">
                        <td>
                            <strong>{{ $invoice->numero ?? $invoice->fatura_augix }}</strong>
                        </td>
                        <td>
                            <strong>R$ {{ number_format($invoice->valor, 2, ',', '.') }}</strong>
                        </td>
                        <td>{{ $invoice->data_vencimento->format('d/m/Y') }}</td>
                        <td>
                            {{ $invoice->data_pagamento ? $invoice->data_pagamento->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            @if($invoice->isOverdue())
                                <span class="badge badge-status badge-inactive">Vencida</span>
                            @elseif($invoice->isOpen())
                                <span class="badge badge-status badge-pending">Em Aberto</span>
                            @else
                                <span class="badge badge-status badge-active">Paga</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($invoice->isOpen())
                            <div class="btn-group btn-group-sm">
                                @if($invoice->codigo_barras)
                                <button class="btn btn-outline-primary btn-copy-barcode"
                                        onclick="copyBarcode('{{ $invoice->codigo_barras }}')"
                                        title="Copiar Código de Barras">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                @endif

                                @if($invoice->pix_copia_cola)
                                <button class="btn btn-outline-success btn-copy-barcode"
                                        onclick="copyPix('{{ $invoice->pix_copia_cola }}')"
                                        title="Copiar PIX">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                                @endif

                                <a href="{{ route('invoice.detail', $invoice->fatura_augix) }}"
                                   class="btn btn-outline-secondary"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            @else
                            <span class="text-muted">
                                <i class="fas fa-check-circle text-success"></i>
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card card-custom">
    <div class="card-body-custom text-center py-5">
        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Nenhuma fatura encontrada</h5>
        <p class="text-muted">Suas faturas aparecerão aqui quando estiverem disponíveis.</p>
    </div>
</div>
@endif

<div id="pixModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="background:var(--primary-color);color:white;border-radius:16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-qrcode me-2"></i>Pagamento PIX</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Copie o código abaixo e cole no aplicativo do seu banco:</p>
                <div class="pix-area">
                    <div class="pix-code" id="pixCode"></div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-primary-custom w-100" onclick="copyPixFromModal()">
                    <i class="fas fa-copy me-2"></i>Copiar Código PIX
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyBarcode(code) {
        navigator.clipboard.writeText(code).then(() => {
            showToast('Código de barras copiado!');
        });
    }

    function copyPix(code) {
        document.getElementById('pixCode').textContent = code;
        new bootstrap.Modal(document.getElementById('pixModal')).show();
    }

    function copyPixFromModal() {
        const code = document.getElementById('pixCode').textContent;
        navigator.clipboard.writeText(code).then(() => {
            showToast('Código PIX copiado!');
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-body d-flex align-items-center" style="background:var(--primary-color);color:white;border-radius:12px;padding:12px 20px;">
                    <i class="fas fa-check-circle me-2"></i>${message}
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endsection
