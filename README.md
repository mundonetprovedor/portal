# Portal do Assinante MUNDONET

Sistema web de autoatendimento para assinantes, integrado ao IXC Soft.

## Deploy no EasyPanel (2 minutos)

### 1. Crie o projeto no EasyPanel

- Tipo: **Docker Compose**
- Repositório: `mundonetprovedor/portal`
- Branch: `main`

### 2. Variáveis de ambiente

```env
APP_NAME=Portal MUNDONET
APP_ENV=production
APP_DEBUG=false
APP_URL=https://portal.seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=portal_mundonet
DB_USERNAME=mundonet
DB_PASSWORD=SENHA_FORTE

DB_ROOT_PASSWORD=SENHA_ROOT_FORTE

IXC_API_URL=https://ixc.mundonetbandalarga.com.br
IXC_API_TOKEN=15
IXC_API_SECRET=SEU_SECRET

ADMIN_PASSWORD=SENHA_ADMIN

APP_PORT=80
```

### 3. Deploy

Clique em **Deploy**. Pronto!

### 4. Primeira inicialização

Após o deploy, acesse o terminal do container `app` e rode:

```bash
bash docker/entrypoint.sh
```

Isso instala o Composer, roda migrations e configura o sistema.

### 5. SSL

Ative Let's Encrypt no EasyPanel.

---

## Deploy Local

```bash
git clone https://github.com/mundonetprovedor/portal.git
cd portal
cp .env.example .env
# Edite .env com suas credenciais
docker compose up -d
# Acesse http://localhost:8080
```

---

## Tecnologias

- Laravel 12 + PHP 8.3
- Bootstrap 5 + Font Awesome
- MariaDB 11
- Nginx + PHP-FPM
- Docker (imagens pré-compiladas)

## Licença

MIT License
