# i-Educar — Instalação Completa para VPS

Sistema de gestão escolar com **todos os módulos** inclusos:
- ✅ **Relatório** (reports-package)
- ✅ **Educacenso** (educacenso-package)
- ✅ **Transporte Escolar** (transport-package)
- ✅ **Biblioteca** (library-package)
- ✅ **Pré-matrícula Digital** (pre-matricula-digital)

## 🚀 Instalação Rápida (VPS)

### Pré-requisitos
- Docker e Docker Compose instalados
- Git instalado

### Comando único

```bash
git clone git@github.com:portabilis/i-educar.git && cd i-educar && bash setup.sh
```

### Portas customizadas (evitar conflitos)

Se já possui projetos rodando na VPS, defina portas alternativas:

```bash
APP_PORT=9090 DB_PORT=5434 REDIS_PORT=6381 bash setup.sh
```

### Variáveis disponíveis

| Variável | Padrão | Descrição |
|----------|--------|-----------|
| `APP_PORT` | `8880` | Porta HTTP do i-Educar |
| `DB_PORT` | `5433` | Porta exposta do PostgreSQL |
| `REDIS_PORT` | `6380` | Porta exposta do Redis |
| `INSTALL_DIR` | `/var/www/ieducar` | Diretório de instalação |
| `DB_NAME` | `ieducar` | Nome do banco de dados |
| `DB_USER` | `ieducar` | Usuário do banco |
| `DB_PASS` | `ieducar` | Senha do banco |

## 📝 Primeiro Acesso

Após a instalação, acesse: `http://SEU-IP:8880`

- **Usuário**: `admin`
- **Senha**: `123456789`

> ⚠️ **Troque a senha padrão imediatamente!**

## 🛠️ Comandos Úteis

```bash
# Ver status dos containers
docker compose ps

# Ver logs da aplicação
docker compose logs -f php

# Parar tudo
docker compose down

# Reiniciar
docker compose up -d
```

---
Automatizado por Antigravity (IA) para Douglas.
