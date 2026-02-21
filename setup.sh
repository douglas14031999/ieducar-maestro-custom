#!/bin/bash
set -euo pipefail

# ============================================================
# setup.sh - i-Educar All-in-One Installer for VPS
# Instala com TODOS os módulos: Relatório, Educacenso,
# Transporte, Biblioteca e Pré-matrícula Digital
# ============================================================

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# ---- Configurações padrão (altere aqui ou passe via ENV) ----
INSTALL_DIR="${INSTALL_DIR:-/var/www/ieducar}"
APP_PORT="${APP_PORT:-8880}"
DB_PORT="${DB_PORT:-5433}"
REDIS_PORT="${REDIS_PORT:-6380}"
DB_NAME="${DB_NAME:-ieducar}"
DB_USER="${DB_USER:-ieducar}"
DB_PASS="${DB_PASS:-ieducar}"
REPO_URL="${REPO_URL:-https://github.com/portabilis/i-educar.git}"

# ---- Pacotes extras (Biblioteca e Transporte) ----
LIBRARY_REPO="https://github.com/portabilis/i-educar-library-package.git"
TRANSPORT_REPO="https://github.com/portabilis/i-educar-transport-package.git"

header() {
    echo ""
    echo -e "${BLUE}==================================================${NC}"
    echo -e "${GREEN}  i-Educar All-in-One Installer (VPS Ready)       ${NC}"
    echo -e "${GREEN}  Módulos: Relatório | Educacenso | Transporte    ${NC}"
    echo -e "${GREEN}           Biblioteca | Pré-matrícula Digital     ${NC}"
    echo -e "${BLUE}==================================================${NC}"
    echo ""
}

log()  { echo -e "${GREEN}[✔]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
err()  { echo -e "${RED}[✖]${NC} $1"; exit 1; }
step() { echo -e "\n${BLUE}▸ PASSO $1:${NC} $2"; }

# ============================================================
# PASSO 0: Verificar dependências
# ============================================================
check_deps() {
    step "0" "Verificando dependências..."

    command -v git &>/dev/null || err "Git não encontrado. Instale com: apt install git"
    command -v docker &>/dev/null || err "Docker não encontrado. Instale: https://docs.docker.com/engine/install/"

    if docker compose version &>/dev/null; then
        COMPOSE_CMD="docker compose"
    elif command -v docker-compose &>/dev/null; then
        COMPOSE_CMD="docker-compose"
    else
        err "Docker Compose não encontrado."
    fi

    log "Git, Docker e Docker Compose disponíveis."
}

# ============================================================
# PASSO 1: Verificar portas
# ============================================================
check_ports() {
    step "1" "Verificando portas ($APP_PORT, $DB_PORT, $REDIS_PORT)..."

    for port in $APP_PORT $DB_PORT $REDIS_PORT; do
        if ss -tlnp 2>/dev/null | grep -q ":${port} " || \
           netstat -tlnp 2>/dev/null | grep -q ":${port} "; then
            err "Porta $port já está em uso! Defina outra via variável de ambiente. Ex: APP_PORT=9090 bash setup.sh"
        fi
    done

    log "Portas $APP_PORT, $DB_PORT, $REDIS_PORT estão livres."
}

# ============================================================
# PASSO 2: Clonar repositório
# ============================================================
clone_repo() {
    step "2" "Clonando repositório em $INSTALL_DIR..."

    if [ -d "$INSTALL_DIR/.git" ]; then
        warn "Repositório já existe em $INSTALL_DIR. Pulando clone..."
        cd "$INSTALL_DIR"
        git pull --ff-only 2>/dev/null || warn "git pull falhou (pode estar em branch diferente). Continuando..."
    else
        git clone "$REPO_URL" "$INSTALL_DIR"
        cd "$INSTALL_DIR"
    fi

    log "Repositório pronto em $INSTALL_DIR"
}

# ============================================================
# PASSO 3: Baixar módulos extras (Biblioteca e Transporte)
# ============================================================
install_extra_modules() {
    step "3" "Instalando módulos extras (Biblioteca e Transporte)..."

    mkdir -p packages/portabilis

    # Biblioteca
    if [ -d "packages/portabilis/i-educar-library-package/.git" ]; then
        warn "Pacote Biblioteca já existe. Atualizando..."
        cd packages/portabilis/i-educar-library-package
        git pull --ff-only 2>/dev/null || true
        cd "$INSTALL_DIR"
    else
        rm -rf packages/portabilis/i-educar-library-package
        git clone "$LIBRARY_REPO" packages/portabilis/i-educar-library-package
    fi
    log "Módulo Biblioteca instalado."

    # Transporte
    if [ -d "packages/portabilis/i-educar-transport-package/.git" ]; then
        warn "Pacote Transporte já existe. Atualizando..."
        cd packages/portabilis/i-educar-transport-package
        git pull --ff-only 2>/dev/null || true
        cd "$INSTALL_DIR"
    else
        rm -rf packages/portabilis/i-educar-transport-package
        git clone "$TRANSPORT_REPO" packages/portabilis/i-educar-transport-package
    fi
    log "Módulo Transporte instalado."
}

# ============================================================
# PASSO 4: Configurar composer.json para incluir TODOS os módulos
# ============================================================
patch_composer() {
    step "4" "Configurando composer.json com todos os módulos..."

    # Verificar se os repositórios dos módulos extras já estão no composer.json
    if ! grep -q "i-educar-library-package" composer.json; then
        warn "Adicionando repositório Biblioteca ao composer.json..."
        # Usa php para manipular JSON de forma segura
        $COMPOSE_CMD exec -T php php -r '
            $json = json_decode(file_get_contents("composer.json"), true);

            // Adicionar repositórios se não existirem
            $hasLib = false;
            $hasTrans = false;
            foreach ($json["repositories"] ?? [] as $repo) {
                if (str_contains($repo["url"] ?? "", "i-educar-library-package")) $hasLib = true;
                if (str_contains($repo["url"] ?? "", "i-educar-transport-package")) $hasTrans = true;
            }
            if (!$hasLib) {
                $json["repositories"][] = ["type" => "path", "url" => "./packages/portabilis/i-educar-library-package", "symlink" => true];
            }
            if (!$hasTrans) {
                $json["repositories"][] = ["type" => "path", "url" => "./packages/portabilis/i-educar-transport-package", "symlink" => true];
            }

            // Adicionar dependências se não existirem
            if (!isset($json["require"]["portabilis/i-educar-library-package"])) {
                $json["require"]["portabilis/i-educar-library-package"] = "*";
            }
            if (!isset($json["require"]["portabilis/i-educar-transport-package"])) {
                $json["require"]["portabilis/i-educar-transport-package"] = "*";
            }

            file_put_contents("composer.json", json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
            echo "composer.json atualizado com sucesso.\n";
        '
    else
        log "composer.json já contém referências aos módulos extras."
    fi
}

# ============================================================
# PASSO 5: Criar .env
# ============================================================
create_env() {
    step "5" "Configurando .env..."

    if [ ! -f .env ]; then
        cp .env.example .env
    fi

    # Atualizar portas e credenciais no .env
    sed -i "s|^DB_HOST=.*|DB_HOST=postgres|" .env
    sed -i "s|^DB_PORT=.*|DB_PORT=5432|" .env
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env
    sed -i "s|^REDIS_HOST=.*|REDIS_HOST=redis|" .env

    # Adicionar variáveis de porta Docker se não existem
    grep -q "^DOCKER_NGINX_PORT=" .env || echo "DOCKER_NGINX_PORT=$APP_PORT" >> .env
    grep -q "^DOCKER_POSTGRES_PORT=" .env || echo "DOCKER_POSTGRES_PORT=$DB_PORT" >> .env
    grep -q "^DOCKER_REDIS_PORT=" .env || echo "DOCKER_REDIS_PORT=$REDIS_PORT" >> .env

    # Atualizar portas Docker se já existem
    sed -i "s|^DOCKER_NGINX_PORT=.*|DOCKER_NGINX_PORT=$APP_PORT|" .env
    sed -i "s|^DOCKER_POSTGRES_PORT=.*|DOCKER_POSTGRES_PORT=$DB_PORT|" .env
    sed -i "s|^DOCKER_REDIS_PORT=.*|DOCKER_REDIS_PORT=$REDIS_PORT|" .env

    # Configurar UID/GID do host
    HOST_UID_VAL=$(id -u)
    HOST_GID_VAL=$(id -g)
    sed -i "s|^HOST_UID=.*|HOST_UID=$HOST_UID_VAL|" .env
    sed -i "s|^HOST_GID=.*|HOST_GID=$HOST_GID_VAL|" .env

    log ".env configurado (Nginx:$APP_PORT, Postgres:$DB_PORT, Redis:$REDIS_PORT)"
}

# ============================================================
# PASSO 6: Build e Start dos containers
# ============================================================
start_containers() {
    step "6" "Subindo containers Docker (pode demorar no primeiro build)..."

    $COMPOSE_CMD up -d --build

    # Aguardar PostgreSQL ficar pronto
    echo -n "  Aguardando PostgreSQL..."
    for i in $(seq 1 30); do
        if $COMPOSE_CMD exec -T postgres pg_isready -U "$DB_USER" &>/dev/null; then
            echo ""
            log "PostgreSQL pronto!"
            break
        fi
        echo -n "."
        sleep 2
    done

    # Verificar se todos os containers estão rodando
    RUNNING=$($COMPOSE_CMD ps --format '{{.Status}}' 2>/dev/null | grep -c "Up" || true)
    if [ "$RUNNING" -lt 4 ]; then
        warn "Alguns containers podem não estar rodando. Verificando..."
        $COMPOSE_CMD ps
    else
        log "Todos os containers estão rodando."
    fi
}

# ============================================================
# PASSO 7: Instalar dependências e banco de dados
# ============================================================
install_app() {
    step "7" "Instalando i-Educar (composer + migrations + seed)..."

    # composer new-install faz: install + plug-and-play + key:generate + storage:link + migrate
    $COMPOSE_CMD exec -T php composer new-install

    log "Dependências e migrações concluídas."

    # Seed do banco
    step "7b" "Populando banco de dados (seed)..."
    $COMPOSE_CMD exec -T php php artisan db:seed --force

    log "Banco de dados populado com dados iniciais."
}

# ============================================================
# PASSO 8: Patch do composer.json (módulos extras) - pós-containers
# ============================================================
install_modules_post() {
    step "8" "Registrando módulos Biblioteca e Transporte no Composer..."
    patch_composer

    # Rodar composer update para instalar os pacotes extras
    $COMPOSE_CMD exec -T php composer update portabilis/i-educar-library-package portabilis/i-educar-transport-package --no-interaction

    # Rodar plug-and-play para registrar os service providers
    $COMPOSE_CMD exec -T php composer plug-and-play

    # Rodar migrate para aplicar migrations dos módulos
    $COMPOSE_CMD exec -T php php artisan migrate --force

    log "Módulos Biblioteca e Transporte registrados e migrados."
}

# ============================================================
# PASSO 9: Limpeza de caches
# ============================================================
clear_caches() {
    step "9" "Limpando caches..."

    $COMPOSE_CMD exec -T php php artisan cache:clear 2>/dev/null || true
    $COMPOSE_CMD exec -T php php artisan config:clear 2>/dev/null || true
    $COMPOSE_CMD exec -T php php artisan view:clear 2>/dev/null || true
    $COMPOSE_CMD exec -T php php artisan route:clear 2>/dev/null || true

    log "Caches limpos."
}

# ============================================================
# PASSO 10: Resumo final
# ============================================================
show_summary() {
    local SERVER_IP
    SERVER_IP=$(hostname -I 2>/dev/null | awk '{print $1}' || echo "SEU-IP")

    echo ""
    echo -e "${BLUE}==================================================${NC}"
    echo -e "${GREEN}   ✅ INSTALAÇÃO CONCLUÍDA COM SUCESSO!           ${NC}"
    echo -e "${BLUE}==================================================${NC}"
    echo ""
    echo -e "  📌 ${GREEN}URL:${NC}      http://${SERVER_IP}:${APP_PORT}"
    echo -e "  👤 ${GREEN}Usuário:${NC}  admin"
    echo -e "  🔑 ${GREEN}Senha:${NC}    123456789"
    echo ""
    echo -e "  📦 ${YELLOW}Módulos instalados:${NC}"
    echo -e "     • Relatório (reports-package)"
    echo -e "     • Educacenso (educacenso-package)"
    echo -e "     • Pré-matrícula Digital"
    echo -e "     • Biblioteca (library-package)"
    echo -e "     • Transporte Escolar (transport-package)"
    echo ""
    echo -e "  🐳 ${YELLOW}Portas Docker:${NC}"
    echo -e "     • Nginx (HTTP):   ${APP_PORT}"
    echo -e "     • PostgreSQL:     ${DB_PORT}"
    echo -e "     • Redis:          ${REDIS_PORT}"
    echo ""
    echo -e "  ${RED}⚠️  TROQUE A SENHA PADRÃO NO PRIMEIRO ACESSO!${NC}"
    echo -e "${BLUE}==================================================${NC}"
}

# ============================================================
# EXECUÇÃO PRINCIPAL
# ============================================================
main() {
    header
    check_deps
    check_ports
    clone_repo
    install_extra_modules
    create_env
    start_containers
    install_app
    install_modules_post
    clear_caches
    show_summary
}

main "$@"
