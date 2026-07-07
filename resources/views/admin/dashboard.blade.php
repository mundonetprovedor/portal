<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Portal MUNDONET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0B3D91;
            --secondary-color: #1a5cc7;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: #f4f6f9; }
        .admin-header {
            background: #1a1a2e; color: white; padding: 16px 24px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .admin-header h4 { margin: 0; font-weight: 700; }
        .main-content { padding: 32px 24px; max-width: 1200px; margin: 0 auto; }
        .card-custom {
            background: white; border-radius: 16px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border: none; margin-bottom: 24px; overflow: hidden;
        }
        .card-header-custom {
            background: var(--primary-color); color: white; padding: 16px 24px;
            font-weight: 600; display: flex; align-items: center; gap: 10px;
        }
        .card-body-custom { padding: 24px; }
        .btn-primary-custom {
            background: var(--primary-color); border: none; color: white;
            padding: 10px 24px; border-radius: 8px; font-weight: 600;
        }
        .btn-primary-custom:hover { background: var(--secondary-color); color: white; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 14px; border: 2px solid #e9ecef; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(11,61,145,0.1); }
        .color-input { width: 60px; height: 40px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h4><i class="fas fa-cog me-2"></i>Painel Administrativo</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:white;">
                <i class="fas fa-home me-1"></i> Portal
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:white;">
                    <i class="fas fa-sign-out-alt me-1"></i> Sair
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success" style="border-radius:12px;border:none;">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:12px;border:none;">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-plug"></i> Configuração da API IXC
                    </div>
                    <div class="card-body-custom">
                        <form method="POST" action="{{ route('admin.config.api') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">URL do IXC</label>
                                <input type="url" class="form-control" name="ixc_url"
                                       value="{{ $settings['ixc_url'] ?? '' }}"
                                       placeholder="https://painel.seudominio.com.br" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Token</label>
                                <input type="text" class="form-control" name="ixc_token"
                                       value="{{ $settings['ixc_token'] ?? '' }}"
                                       placeholder="Seu token de acesso" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Secret</label>
                                <input type="password" class="form-control" name="ixc_secret"
                                       value="{{ $settings['ixc_secret'] ?? '' }}"
                                       placeholder="Seu secret" required>
                            </div>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-save me-2"></i>Salvar Configuração
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="fas fa-palette"></i> Configuração Visual
                    </div>
                    <div class="card-body-custom">
                        <form method="POST" action="{{ route('admin.config.visual') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome da Empresa</label>
                                <input type="text" class="form-control" name="company_name"
                                       value="{{ $settings['company_name'] ?? 'MUNDONET' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Logomarca</label>
                                <input type="file" class="form-control" name="logo" accept="image/*">
                                @if(isset($settings['logo_path']) && file_exists(public_path($settings['logo_path'])))
                                    <div class="mt-2">
                                        <img src="{{ asset($settings['logo_path']) }}" alt="Logo atual" style="max-height:40px;">
                                    </div>
                                @endif
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Cor Principal</label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" class="color-input" name="primary_color"
                                               value="{{ $settings['primary_color'] ?? '#0B3D91' }}">
                                        <input type="text" class="form-control form-control-sm"
                                               value="{{ $settings['primary_color'] ?? '#0B3D91' }}" readonly
                                               style="max-width:100px;">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Cor Secundária</label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="color" class="color-input" name="secondary_color"
                                               value="{{ $settings['secondary_color'] ?? '#1a5cc7' }}">
                                        <input type="text" class="form-control form-control-sm"
                                               value="{{ $settings['secondary_color'] ?? '#1a5cc7' }}" readonly
                                               style="max-width:100px;">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-save me-2"></i>Salvar Visual
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
