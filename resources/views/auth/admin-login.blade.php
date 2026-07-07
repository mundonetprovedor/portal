<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portal MUNDONET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0B3D91;
            --secondary-color: #1a5cc7;
        }
        * { font-family: 'Inter', sans-serif; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
        .admin-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        }
        .admin-card h2 { color: #1a1a2e; font-weight: 700; }
        .admin-card .icon-shield {
            width: 70px; height: 70px; background: #1a1a2e;
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 1.8rem; color: white;
        }
        .form-control { border: 2px solid #e9ecef; border-radius: 10px; padding: 12px 16px; }
        .form-control:focus { border-color: #1a1a2e; box-shadow: 0 0 0 3px rgba(26,26,46,0.1); }
        .btn-admin { background: #1a1a2e; border: none; color: white; padding: 12px; border-radius: 10px; font-weight: 600; width: 100%; }
        .btn-admin:hover { background: #0f3460; color: white; }
    </style>
</head>
<body>
    <div class="admin-card">
        <div class="text-center">
            <div class="icon-shield"><i class="fas fa-shield-alt"></i></div>
            <h2>Painel Admin</h2>
            <p class="text-muted mb-4">Acesso restrito a administradores</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:10px;border:none;">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Senha de Acesso</label>
                <input type="password" class="form-control" name="password" required autofocus
                       placeholder="Digite a senha administrativa">
            </div>
            <button type="submit" class="btn btn-admin">
                <i class="fas fa-sign-in-alt me-2"></i>Entrar
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-muted" style="text-decoration:none;font-size:0.9rem;">
                <i class="fas fa-arrow-left me-1"></i>Voltar ao Portal
            </a>
        </div>
    </div>
</body>
</html>
