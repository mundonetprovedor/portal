@extends('layouts.app')

@section('title', 'Suporte - Portal MUNDONET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="fas fa-headset me-2" style="color:var(--primary-color);"></i>
            Suporte
        </h4>
        <p class="text-muted mb-0">Abra um chamado ou acompanhe seus atendimentos</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-custom btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
</div>

@if(session('new_ticket'))
<div class="card card-custom mb-4" style="border-left:4px solid #28a745;">
    <div class="card-body-custom">
        <h6 class="fw-bold text-success mb-3">
            <i class="fas fa-check-circle me-2"></i>Chamado Aberto com Sucesso!
        </h6>
        <div class="row">
            <div class="col-md-4">
                <div class="info-row">
                    <span class="info-label">Protocolo</span>
                    <span class="info-value fw-bold">{{ session('new_ticket')->protocolo ?? 'Aguardando' }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value"><span class="badge badge-status badge-pending">Em Atendimento</span></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-row">
                    <span class="info-label">Data</span>
                    <span class="info-value">{{ session('new_ticket')->data_abertura ? session('new_ticket')->data_abertura->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fas fa-plus-circle"></i> Abrir Chamado
            </div>
            <div class="card-body-custom">
                <form method="POST" action="{{ route('support.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="contrato_augix" class="form-label fw-semibold">Contrato</label>
                        <select class="form-select" id="contrato_augix" name="contrato_augix" required
                                style="border-radius:10px;padding:12px;">
                            <option value="">Selecione o contrato</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->contrato_augix }}"
                                        {{ $contracts->count() === 1 ? 'selected' : '' }}>
                                    {{ $contract->plano_nome }} - {{ $contract->contrato_augix }}
                                </option>
                            @endforeach
                        </select>
                        @error('contrato_augix')
                            <div class="text-danger mt-1" style="font-size:0.85rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tipo_problema" class="form-label fw-semibold">Tipo do Problema</label>
                        <select class="form-select" id="tipo_problema" name="tipo_problema" required
                                style="border-radius:10px;padding:12px;">
                            <option value="">Selecione o tipo</option>
                            <option value="semconexao" {{ old('tipo_problema') === 'semconexao' ? 'selected' : '' }}>
                                <i class="fas fa-plug"></i> Sem Conexão
                            </option>
                            <option value="lentidao" {{ old('tipo_problema') === 'lentidao' ? 'selected' : '' }}>
                                <i class="fas fa-tachometer-alt"></i> Lentidão Constante
                            </option>
                        </select>
                        @error('tipo_problema')
                            <div class="text-danger mt-1" style="font-size:0.85rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="observacoes" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes"
                                  rows="4" required minlength="10" maxlength="500"
                                  placeholder="Descreva detalhadamente o problema que está enfrentando..."
                                  style="border-radius:10px;padding:12px;">{{ old('observacoes') }}</textarea>
                        <div class="form-text">Mínimo 10 caracteres. Máximo 500.</div>
                        @error('observacoes')
                            <div class="text-danger mt-1" style="font-size:0.85rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="fas fa-paper-plane me-2"></i>Abrir Chamado
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fas fa-history"></i> Chamados Anteriores
            </div>
            <div class="card-body-custom p-0">
                @if($tickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Protocolo</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td>
                                    <strong>{{ $ticket->protocolo ?? $ticket->os_augix ?? '-' }}</strong>
                                </td>
                                <td>{{ $ticket->tipo_problema }}</td>
                                <td>{{ $ticket->data_abertura ? $ticket->data_abertura->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    @if($ticket->situacao === 'A' || $ticket->situacao === 'AB')
                                        <span class="badge badge-status badge-pending">Em Atendimento</span>
                                    @elseif($ticket->situacao === 'F' || $ticket->situacao === 'FE')
                                        <span class="badge badge-status badge-active">Finalizado</span>
                                    @else
                                        <span class="badge badge-status badge-pending">{{ $ticket->situacao }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Nenhum chamado encontrado</h6>
                    <p class="text-muted" style="font-size:0.85rem;">Seus chamados aparecerão aqui.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
