<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Portal MUNDONET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::get('primary_color', '#0B3D91') }};
            --secondary-color: {{ \App\Models\Setting::get('secondary_color', '#1a5cc7') }};
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, #0d47a1 100%);
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: rgba(255,255,255,0.98);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            padding: 48px 40px;
            backdrop-filter: blur(20px);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo img {
            height: 70px;
            width: auto;
            margin-bottom: 16px;
        }

        .login-logo .logo-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 2rem;
            color: white;
            box-shadow: 0 8px 25px rgba(11,61,145,0.3);
        }

        .login-logo h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            letter-spacing: 1px;
        }

        .login-logo p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 8px;
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 24px;
        }

        .form-floating-custom label {
            color: #6c757d;
            font-weight: 500;
        }

        .form-floating-custom .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 16px 16px 16px 48px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            height: 56px;
        }

        .form-floating-custom .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(11,61,145,0.1);
        }

        .form-floating-custom .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 1.1rem;
            z-index: 5;
            transition: color 0.3s ease;
        }

        .form-floating-custom .form-control:focus ~ .input-icon {
            color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(11,61,145,0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: #6c757d;
            font-size: 0.8rem;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 12px 16px;
            margin-bottom: 24px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
                border-radius: 16px;
            }

            .login-logo h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                @php
                    $logoPath = \App\Models\Setting::get('logo_path');
                    $companyName = \App\Models\Setting::get('company_name', 'MUNDONET');
                @endphp
                @if($logoPath && file_exists(public_path($logoPath)))
                    <img src="{{ asset($logoPath) }}" alt="{{ $companyName }}">
                @else
                    <div class="logo-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                @endif
                <h1>{{ $companyName }}</h1>
                <p>Portal do Assinante</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-floating-custom">
                    <input type="text"
                           class="form-control @error('cpf') is-invalid @enderror"
                           id="cpf"
                           name="cpf"
                           value="{{ old('cpf') }}"
                           placeholder="000.000.000-00"
                           maxlength="14"
                           required
                           autofocus>
                    <i class="fas fa-id-card input-icon"></i>
                    <label for="cpf">Informe seu CPF</label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Entrar
                </button>
            </form>

            <div class="login-footer">
                <p class="mb-0">
                    <i class="fas fa-lock me-1"></i>
                    Acesso seguro e criptografado
                </p>
                <p class="mt-2 mb-0">
                    <a href="{{ route('admin.login') }}">
                        <i class="fas fa-cog me-1"></i>Área Administrativa
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cpfInput = document.getElementById('cpf');

            cpfInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length > 11) {
                    value = value.substring(0, 11);
                }

                if (value.length > 9) {
                    value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
                } else if (value.length > 6) {
                    value = value.replace(/^(\d{3})(\d{3})(\d{1,3})$/, '$1.$2.$3');
                } else if (value.length > 3) {
                    value = value.replace(/^(\d{3})(\d{1,3})$/, '$1.$2');
                }

                e.target.value = value;
            });

            cpfInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
