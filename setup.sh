#!/bin/bash

# setup.sh - i-Educar All-in-One Installer for VPS
# Cores para o output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}==================================================${NC}"
echo -e "${GREEN}    i-Educar All-in-One Installer (VPS Ready)     ${NC}"
echo -e "${BLUE}==================================================${NC}"

# 1. Verificar dependências
echo -e "${YELLOW}1. Verificando dependências...${NC}"
for cmd in git docker docker-compose; do
    if ! command -v $cmd &> /dev/null && ! [ "$cmd" == "docker-compose" -a "$(docker compose version 2>/dev/null)" ]; then
        echo "$cmd não encontrado. Por favor, instale $cmd primeiro."
        exit 1
    fi
done

# 2. Clonar Repositório
echo -e "${YELLOW}2. Clonando repositório...${NC}"
DEFAULT_DIR="ieducar-vps"
read -p "Nome da pasta para instalação (Padrão: $DEFAULT_DIR): " INSTALL_DIR
INSTALL_DIR=${INSTALL_DIR:-$DEFAULT_DIR}

if [ -d "$INSTALL_DIR" ]; then
    echo -e "${YELLOW}A pasta $INSTALL_DIR já existe. Entrando nela...${NC}"
    cd "$INSTALL_DIR"
else
    git clone https://github.com/douglas14031999/ieducar-maestro-custom.git "$INSTALL_DIR"
    cd "$INSTALL_DIR"
fi

# 3. Configuração de Portas (Evita conflitos)
echo -e "${YELLOW}3. Configurando portas do sistema...${NC}"
DEFAULT_PORT=8080
read -p "Qual porta você deseja usar para o i-Educar? (Padrão: $DEFAULT_PORT): " APP_PORT
APP_PORT=${APP_PORT:-$DEFAULT_PORT}

# Verificar se a porta está em uso
while lsof -Pi :$APP_PORT -sTCP:LISTEN -t >/dev/null ; do
    echo -e "${YELLOW}Porta $APP_PORT já está em uso.${NC}"
    read -p "Escolha outra porta: " APP_PORT
done

# 4. Preparar .env
echo -e "${YELLOW}4. Preparando ambiente (.env)...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    # Atualizar portas no .env
    sed -i "s/APP_PORT=.*/APP_PORT=$APP_PORT/" .env
    # Gerar chaves se necessário
    echo "APP_KEY=" >> .env
fi

# 5. Subir containers
echo -e "${YELLOW}5. Subindo containers (Docker)...${NC}"
docker compose up -d

# 6. Instalação de dependências e banco
echo -e "${YELLOW}6. Finalizando instalação do software...${NC}"
docker compose exec php composer install --no-dev
docker compose exec php composer plug-and-play
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate --force

# 7. Limpeza Final
echo -e "${YELLOW}7. Limpando caches...${NC}"
docker compose exec php php artisan cache:clear
docker compose exec php php artisan view:clear

echo -e "${BLUE}==================================================${NC}"
echo -e "${GREEN}    INSTALAÇÃO CONCLUÍDA COM SUCESSO!            ${NC}"
echo -e "Acesse o sistema em: http://seu-ip-ou-dominio:${APP_PORT}"
echo -e "${BLUE}==================================================${NC}"
