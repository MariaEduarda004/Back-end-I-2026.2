# Agenda de Estudos

Projeto didático em PHP puro para demonstrar os conteúdos de Desenvolvimento
Back-end I. O usuário pode criar uma conta, entrar, cadastrar tarefas, marcar
tarefas como concluídas e escolher um tema claro ou escuro.

## Requisitos

- PHP 8.1 ou mais recente.
- Extensão `mbstring` habilitada.
- Permissão para o PHP gravar arquivos na pasta `storage/`.

## Como executar

### Servidor embutido do PHP

No terminal, a partir da pasta do projeto, execute:

```text
php -S localhost:8000 -t public
```

Abra `http://localhost:8000/cadastro.php` no navegador. Esse servidor serve
para desenvolvimento local, não para publicar o sistema na internet.

### XAMPP ou WampServer

Copie a pasta do projeto para `htdocs` (XAMPP) ou `www` (WampServer), inicie o
Apache e abra `http://localhost/NOME-DA-PASTA/public/cadastro.php`.

## Fluxo para testar

1. Crie uma conta com nome, e-mail e senha de pelo menos 8 caracteres.
2. Entre com essa conta e cadastre uma tarefa com matéria, prazo e prioridade.
3. Marque a tarefa como concluída e alterne o tema.
4. Saia da conta e tente abrir o painel: o sistema deve pedir login.
5. Tente cadastrar uma conta com um e-mail já usado ou uma tarefa sem título
   para ver as mensagens de validação.

## Organização dos arquivos

- `config/app.php`: inicia a sessão e carrega os outros arquivos PHP.
- `funcoes/`: funções de login, validação, exibição segura e acesso aos JSON.
- `public/`: páginas que o navegador pode abrir e arquivos de estilo.
- `views/`: cabeçalho e rodapé reutilizados pelas páginas.
- `storage/`: arquivos JSON privados com usuários e tarefas.

O painel fica em `public/index.php`. Ele exige uma sessão autenticada. As senhas
são guardadas com `password_hash()` e verificadas com `password_verify()`.
O tema é salvo em um cookie, e as tarefas e contas ficam nos arquivos JSON.
