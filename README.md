# i-Educar Maestro Custom

Este repositório contém uma versão customizada do i-Educar com os módulos de **Biblioteca** e **Transporte Escolar** já integrados, além de correções automáticas de permissões de menu.

## 🚀 Instalação All-in-One (VPS)

Para instalar na sua VPS com apenas um comando, execute:

```bash
bash <(curl -sSL https://raw.githubusercontent.com/douglas14031999/ieducar-maestro-custom/main/setup.sh)
```

### O que este script faz:
1.  **Isolamento**: Permite escolher a porta (ex: 8080) para não conflitar com outros projetos na VPS.
2.  **Módulos**: Instala automaticamente a Biblioteca e o Transporte.
3.  **Permissões**: Aplica a "vacina" que libera os menus para o administrador.
4.  **Docker**: Sobe todos os serviços necessários em containers isolados.

## 🛠️ Requisitos
*   Docker e Docker Compose instalados na VPS.
*   Git instalado.

## 📝 Notas de Configuração
Após o setup, o arquivo `.env` será gerado automaticamente. Se você já possui um PostgreSQL rodando na máquina host e deseja usá-lo em vez do container, basta ajustar as variáveis `DB_HOST`, `DB_PORT`, `DB_USERNAME` e `DB_PASSWORD` no `.env` e reiniciar os containers.

---
Customizado por Antigravity (IA) para Douglas.
