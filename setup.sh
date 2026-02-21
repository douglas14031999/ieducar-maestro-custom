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
if ! command -v docker &> /dev/null; then
    echo "Docker não encontrado. Por favor, instale o Docker primeiro."
    exit 1
fi

if ! command -v docker-compose &>/dev/null && ! docker compose version &>/dev/null; then
    echo "Docker Compose não encontrado. Por favor, instale o Docker Compose."
    exit 1
fi

# 2. Configuração de Portas (Evita conflitos)
echo -e "${YELLOW}2. Configurando portas do sistema...${NC}"
DEFAULT_PORT=8080
read -p "Qual porta você deseja usar para o i-Educar? (Padrão: $DEFAULT_PORT): " APP_PORT
APP_PORT=${APP_PORT:-$DEFAULT_PORT}

# Verificar se a porta está em uso
while lsof -Pi :$APP_PORT -sTCP:LISTEN -t >/dev/null ; do
    echo -e "${YELLOW}Porta $APP_PORT já está em uso.${NC}"
    read -p "Escolha outra porta: " APP_PORT
done

# 3. Preparar .env
echo -e "${YELLOW}3. Preparando ambiente (.env)...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    # Atualizar portas no .env
    sed -i "s/APP_PORT=.*/APP_PORT=$APP_PORT/" .env
    # Gerar chaves se necessário
    echo "APP_KEY=" >> .env
fi

# 4. Subir containers
echo -e "${YELLOW}4. Subindo containers (Docker)...${NC}"
docker compose up -d

# 5. Instalação de dependências e banco
echo -e "${YELLOW}5. Finalizando instalação do software...${NC}"
docker compose exec php composer install --no-dev
docker compose exec php composer plug-and-play
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate --force

# Limpeza Final
echo -e "${YELLOW}6. Limpando caches...${NC}"
docker compose exec php php artisan cache:clear
docker compose exec php php artisan view:clear

echo -e "${BLUE}==================================================${NC}"
echo -e "${GREEN}    INSTALAÇÃO CONCLUÍDA COM SUCESSO!            ${NC}"
echo -e "Acesse o sistema em: http://seu-ip-ou-dominio:${APP_PORT}"
echo -e "${BLUE}==================================================${NC}"
