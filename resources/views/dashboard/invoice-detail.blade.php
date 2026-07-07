@extends('layouts.app')

@section('title', 'Detalhe da Fatura - Portal MUNDONET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="fas fa-file-invoice me-2" style="color:var(--primary-color);"></i>
            Detalhe da Fatura
        </h4>
        <p class="text-muted mb-0">Fatura Nº {{ $invoice->numero ?? $invoice->fatura_augix }}</p>
    </div>
    <a href="{{ route('invoices') }}" class="btn btn-outline-custom btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fas fa-receipt"></i> Informações da Fatura
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Número</span>
                            <span class="info-value">{{ $invoice->numero ?? $invoice->fatura_augix }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Valor</span>
                            <span class="info-value fw-bold fs-5" style="color:var(--primary-color);">
                                R$ {{ number_format($invoice->valor, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Emissão</span>
                            <span class="info-value">{{ $invoice->data_emissao ? $invoice->data_emissao->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Vencimento</span>
                            <span class="info-value">{{ $invoice->data_vencimento->format('d/m/Y') }}</span>
                        </div>
                        @if($invoice->data_pagamento)
                        <div class="info-row">
                            <span class="info-label">Pagamento</span>
                            <span class="info-value">{{ $invoice->data_pagamento->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                @if($invoice->isOverdue())
                                    <span class="badge badge-status badge-inactive fs-6">Vencida</span>
                                @elseif($invoice->isOpen())
                                    <span class="badge badge-status badge-pending fs-6">Em Aberto</span>
                                @else
                                    <span class="badge badge-status badge-active fs-6">Paga</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->isOpen() && $invoice->codigo_barras)
        <div class="card card-custom mt-4">
            <div class="card-header-custom">
                <i class="fas fa-barcode"></i> Código de Barras
            </div>
            <div class="card-body-custom">
                <div class="p-3" style="background:#f8f9fa;border-radius:12px;">
                    <code class="d-block text-break" style="font-size:0.85rem;word-break:break-all;">
                        {{ $invoice->codigo_barras }}
                    </code>
                </div>
                <button class="btn btn-primary-custom mt-3" onclick="copyText('{{ $invoice->codigo_barras }}')">
                    <i class="fas fa-copy me-2"></i>Copiar Código de Barras
                </button>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if($invoice->isOpen() && $invoice->pix_copia_cola)
        <div class="card card-custom">
            <div class="card-header-custom" style="background:#25d366;">
                <i class="fas fa-qrcode"></i> PIX
            </div>
            <div class="card-body-custom">
                <p class="text-muted mb-3">Copie o código abaixo e pague pelo PIX:</p>
                <div class="pix-area">
                    <div class="pix-code" style="font-size:0.7rem;">
                        {{ $invoice->pix_copia_cola }}
                    </div>
                </div>
                <button class="btn btn-success w-100 mt-3" onclick="copyText('{{ $invoice->pix_copia_cola }}')">
                    <i class="fas fa-copy me-2"></i>Copiar Código PIX
                </button>
            </div>
        </div>
        @endif

        <div class="card card-custom mt-4 no-hover">
            <div class="card-body-custom text-center">
                <i class="fas fa-shield-alt fa-2x mb-3" style="color:var(--primary-color);"></i>
                <h6 class="fw-bold">Pagamento Seguro</h6>
                <p class="text-muted" style="font-size:0.85rem;">
                    Este boleto é de responsabilidade da MUNDONET.
                    Em caso de dúvidas, entre em contato com o suporte.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-body" style="background:var(--primary-color);color:white;border-radius:12px;padding:12px 20px;">
                        <i class="fas fa-check-circle me-2"></i>Código copiado com sucesso!
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });
    }
</script>
@endsection
