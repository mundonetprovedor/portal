# Portal do Assinante MUNDONET

Sistema web completo de autoatendimento para assinantes da MUNDONET, integrado ao IXC Soft via API.

## Funcionalidades

- **Login por CPF** com validação e máscara automática
- **Dashboard** com dados do titular, endereço, plano e faturas
- **Gestão Financeira** com listagem de faturas, código de barras, PIX
- **Suporte** com abertura de chamados via API IXC
- **Painel Administrativo** com configurações de API e visual
- **Interface Moderna** responsiva inspirada em bancos digitais

## Requisitos

- Docker & Docker Compose
- PHP 8.3
- MySQL/MariaDB
- Nginx

## Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/mundonetprovedor/portal.git
cd portal
```

### 2. Configure o ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:

```env
APP_NAME="Portal MUNDONET"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://portal.seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=portal_mundonet
DB_USERNAME=mundonet
DB_PASSWORD=sua_senha_segura

IXC_API_URL=https://painel.seudominio.com.br
IXC_API_TOKEN=seu_token
IXC_API_SECRET=seu_secret

ADMIN_PASSWORD=sua_senha_admin
```

### 3. Inicie o sistema

```bash
docker compose up -d
```

### 4. Execute as migrações e seeders

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

### 5. Gere a chave da aplicação

```bash
docker compose exec app php artisan key:generate --force
```

### 6. Crie o link de storage

```bash
docker compose exec app php artisan storage:link
```

## Variáveis de Ambiente

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `APP_NAME` | Nome da aplicação | Portal MUNDONET |
| `APP_ENV` | Ambiente | production |
| `APP_DEBUG` | Modo debug | false |
| `APP_URL` | URL da aplicação | http://localhost |
| `DB_HOST` | Host do banco | db |
| `DB_PORT` | Porta do banco | 3306 |
| `DB_DATABASE` | Nome do banco | portal_mundonet |
| `DB_USERNAME` | Usuário do banco | mundonet |
| `DB_PASSWORD` | Senha do banco | secret_password |
| `IXC_API_URL` | URL do IXC Soft | - |
| `IXC_API_TOKEN` | Token da API IXC | - |
| `IXC_API_SECRET` | Secret da API IXC | - |
| `ADMIN_PASSWORD` | Senha do admin | changeme |
| `APP_PORT` | Porta de acesso | 8080 |

## Deploy no EasyPanel

1. Faça push do código para o GitHub
2. No EasyPanel, crie um novo projeto
3. Conecte o repositório GitHub
4. Configure as variáveis de ambiente no painel
5. O EasyPanel detectará automaticamente o `docker-compose.yml`
6. O sistema estará disponível na porta configurada

### Configuração DNS

Após o deploy, configure o DNS do seu domínio para apontar para o IP do servidor:

```
portal.seudominio.com.br  →  IP_DO_SERVIDOR
```

### Certificado SSL

O EasyPanel suporta Let's Encrypt. Ative o SSL após configurar o DNS.

## Configuração da API IXC

### 1. Acesse o painel administrativo

```
https://portal.seudominio.com.br/admin/login
```

Use a senha definida em `ADMIN_PASSWORD`.

### 2. Configure a API

Na aba "Configuração da API IXC":

- **URL do IXC**: Ex. `https://painel.seudominio.com.br`
- **Token**: Obtido no IXC Soft → Configurações → API
- **Secret**: Obtido no IXC Soft → Configurações → API

### 3. Configure o Visual

Na aba "Configuração Visual":

- Nome da empresa
- Logomarca
- Cores do tema

## Estrutura do Projeto

```
portal/
├── app/
│   ├── Exceptions/          # Exceções customizadas
│   ├── Http/
│   │   ├── Controllers/     # Controladores
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── SupportController.php
│   │   │   └── AdminController.php
│   │   ├── Middleware/      # Middleware
│   │   │   ├── AuthenticateClient.php
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/       # Validações
│   ├── Models/              # Modelos
│   │   ├── Client.php
│   │   ├── Contract.php
│   │   ├── Invoice.php
│   │   ├── Ticket.php
│   │   └── Setting.php
│   ├── Providers/           # Providers
│   └── Services/            # Serviços
│       └── IxcService.php   # Integração com IXC API
├── bootstrap/
├── config/                  # Configurações
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── ixc.php              # Config IXC API
│   └── ...
├── database/
│   ├── migrations/          # Migrações
│   └── seeders/             # Seeders
├── docker/
│   ├── nginx/               # Config Nginx
│   └── php/                 # Config PHP-FPM
├── public/                  # Arquivos públicos
├── resources/
│   └── views/               # Templates Blade
│       ├── layouts/
│       ├── auth/
│       ├── dashboard/
│       ├── admin/
│       └── support/
├── routes/
│   └── web.php              # Rotas
├── storage/
├── Dockerfile
├── docker-compose.yml
├── .env.example
└── README.md
```

## Tecnologias

- **Backend**: Laravel 12, PHP 8.3
- **Frontend**: Blade, Bootstrap 5, Font Awesome 6
- **Banco**: MySQL/MariaDB 11
- **Servidor**: Nginx + PHP-FPM
- **Containerização**: Docker + Docker Compose
- **API**: IXC Soft REST API

## Segurança

- Validação de CPF (algoritmo oficial)
- Proteção CSRF em todos os formulários
- Rate Limiting configurado
- Sanitização de entradas
- Headers de segurança HTTP
- Timeout nas chamadas API
- Logs de erro
- Token/Secret nunca expostos no frontend

## Comandos Úteis

```bash
# Acessar o container
docker compose exec app sh

# Rodar migrações
docker compose exec app php artisan migrate

# Limpar cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear

# Ver logs
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f db

# Parar o sistema
docker compose down

# Parar e remover dados
docker compose down -v
```

## Licença

MIT License

## Suporte

Em caso de dúvidas ou problemas, entre em contato com a equipe MUNDONET.
