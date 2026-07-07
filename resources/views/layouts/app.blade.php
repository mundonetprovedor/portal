<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal MUNDONET')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::get('primary_color', '#0B3D91') }};
            --secondary-color: {{ \App\Models\Setting::get('secondary_color', '#1a5cc7') }};
            --bg-light: #f4f6f9;
            --text-dark: #1a1a2e;
            --text-muted: #6c757d;
            --white: #ffffff;
            --shadow: 0 2px 15px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.12);
            --radius: 12px;
            --radius-lg: 16px;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .navbar-brand-custom {
            background: var(--primary-color);
            color: var(--white) !important;
            padding: 0;
            height: 64px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand-custom .brand-content {
            display: flex;
            align-items: center;
            padding: 0 24px;
            height: 100%;
            gap: 12px;
        }

        .navbar-brand-custom img {
            height: 40px;
            width: auto;
        }

        .navbar-brand-custom .brand-text {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--white) !important;
            background: rgba(255,255,255,0.15);
        }

        .navbar-nav .nav-link i {
            margin-right: 6px;
        }

        .btn-logout {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: var(--white);
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.25);
            color: var(--white);
        }

        .main-content {
            padding: 32px 24px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .card-custom {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: none;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .card-custom .card-header-custom {
            background: var(--primary-color);
            color: var(--white);
            padding: 16px 24px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-custom .card-body-custom {
            padding: 24px;
        }

        .card-custom.no-hover:hover {
            transform: none;
        }

        .stat-card {
            padding: 24px;
            text-align: center;
        }

        .stat-card .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.5rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: var(--bg-light);
            border: none;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            padding: 12px 16px;
        }

        .table-custom tbody td {
            padding: 14px 16px;
            border-color: #f0f0f0;
            vertical-align: middle;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            color: var(--white);
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            color: var(--white);
            transform: translateY(-1px);
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-outline-custom:hover {
            background: var(--primary-color);
            color: var(--white);
        }

        .alert-custom {
            border: none;
            border-radius: var(--radius);
            padding: 16px 20px;
        }

        .footer-custom {
            background: var(--white);
            border-top: 1px solid #e9ecef;
            padding: 20px 0;
            margin-top: 40px;
        }

        .footer-custom .text-muted {
            font-size: 0.85rem;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.25rem;
            padding: 8px;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 16px 12px;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .navbar-collapse {
                background: var(--primary-color);
                padding: 16px;
                border-radius: 0 0 12px 12px;
                margin-top: 8px;
            }

            .navbar-nav .nav-link {
                padding: 12px 16px !important;
            }

            .brand-text {
                font-size: 1rem !important;
            }
        }

        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-muted);
            min-width: 140px;
            font-size: 0.9rem;
        }

        .info-value {
            color: var(--text-dark);
            font-weight: 500;
        }

        .invoice-overdue {
            border-left: 4px solid #dc3545 !important;
            background: #fff5f5 !important;
        }

        .btn-copy-barcode {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-copy-barcode:hover {
            transform: scale(1.05);
        }

        .pix-area {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }

        .pix-area .pix-code {
            font-family: monospace;
            font-size: 0.75rem;
            word-break: break-all;
            background: var(--white);
            padding: 12px;
            border-radius: 8px;
            border: 1px dashed var(--primary-color);
            margin-top: 12px;
        }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-brand-custom">
        <div class="brand-content">
            @php
                $logoPath = \App\Models\Setting::get('logo_path');
                $companyName = \App\Models\Setting::get('company_name', 'MUNDONET');
            @endphp
            @if($logoPath && file_exists(public_path($logoPath)))
                <img src="{{ asset($logoPath) }}" alt="{{ $companyName }}">
            @else
                <i class="fas fa-globe" style="font-size: 1.5rem;"></i>
            @endif
            <span class="brand-text">{{ $companyName }}</span>
        </div>

        <button class="navbar-toggler mobile-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('invoices*') ? 'active' : '' }}" href="{{ route('invoices') }}">
                        <i class="fas fa-file-invoice-dollar"></i> Faturas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('support*') ? 'active' : '' }}" href="{{ route('support') }}">
                        <i class="fas fa-headset"></i> Suporte
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 d-none d-md-block" style="font-size: 0.85rem;">
                    <i class="fas fa-user-circle me-1"></i> {{ session('client_name', 'Cliente') }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-logout btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="footer-custom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-muted">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('company_name', 'MUNDONET') }}. Todos os direitos reservados.</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i> Portal Seguro
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
