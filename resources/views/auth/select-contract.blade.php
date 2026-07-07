<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Contrato - Portal MUNDONET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ \App\Models\Setting::get('primary_color', '#0B3D91') }};
            --secondary-color: {{ \App\Models\Setting::get('secondary_color', '#1a5cc7') }};
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 20px;
        }

        .select-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        }

        .select-card h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .select-card p {
            color: #6c757d;
            margin-bottom: 24px;
        }

        .contract-option {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .contract-option:hover {
            border-color: var(--primary-color);
            background: #f8f9ff;
        }

        .contract-option input[type="radio"] {
            display: none;
        }

        .contract-option.selected {
            border-color: var(--primary-color);
            background: #f0f4ff;
        }

        .contract-option .contract-name {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.05rem;
        }

        .contract-option .contract-detail {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 4px;
        }

        .btn-select {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 16px;
            transition: all 0.3s ease;
        }

        .btn-select:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="select-card">
        <div class="text-center mb-4">
            <div style="width:60px;height:60px;background:var(--primary-color);border-radius:15px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fas fa-file-contract" style="color:white;font-size:1.5rem;"></i>
            </div>
            <h2>Selecionar Contrato</h2>
            <p>Encontramos múltiplos contratos para este CPF. Selecione qual deseja acessar:</p>
        </div>

        <form method="POST" action="{{ route('select-contract') }}">
            @csrf
            @foreach($clients as $index => $client)
                <label class="contract-option" for="client_{{ $index }}" onclick="selectContract(this)">
                    <input type="radio" name="client_index" id="client_{{ $index }}" value="{{ $index }}" {{ $index === 0 ? 'checked' : '' }}>
                    <div class="contract-name">
                        <i class="fas fa-user me-2"></i>{{ $client['nome'] ?? 'Cliente ' . ($index + 1) }}
                    </div>
                    <div class="contract-detail">
                        <i class="fas fa-id-card me-1"></i>
                        CPF: {{ $client['cpf'] ?? '' }}
                        @if(isset($client['situacao']))
                            &nbsp;|&nbsp;
                            <span class="badge {{ $client['situacao'] === 'A' ? 'bg-success' : 'bg-danger' }}">
                                {{ $client['situacao'] === 'A' ? 'Ativo' : 'Inativo' }}
                            </span>
                        @endif
                    </div>
                </label>
            @endforeach

            <button type="submit" class="btn btn-select">
                <i class="fas fa-arrow-right me-2"></i>Acessar
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="fas fa-arrow-left me-1"></i>Voltar ao Login
        </a>
    </div>

    <script>
        function selectContract(element) {
            document.querySelectorAll('.contract-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
        }

        document.querySelector('.contract-option.selected')?.classList.add('selected');
        if (document.querySelector('.contract-option input[checked]')) {
            document.querySelector('.contract-option input[checked]').closest('.contract-option').classList.add('selected');
        }
    </script>
</body>
</html>
