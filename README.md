# Agenda de Estudos

Aplicação didática em PHP puro para organizar tarefas de estudo. O projeto demonstra cadastro e autenticação de usuários, sessões, cookies, validação de formulários e persistência em arquivos JSON.

## Requisitos

- PHP 8.1 ou superior.
- Extensão `mbstring` habilitada.
- Permissão de escrita na pasta `storage/`.

Não é necessário instalar ou iniciar MySQL/MariaDB: os dados são armazenados localmente em arquivos JSON.

## Como executar

Na raiz do projeto, abra um terminal e inicie o servidor embutido do PHP:

```powershell
php -v
php -S 127.0.0.1:8000 -t public
```

Se o comando `php` não estiver no `PATH`, invoque o executável da instalação local. Por exemplo, com XAMPP:

```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t public
```

Com o servidor em execução, acesse <http://127.0.0.1:8000/cadastro.php>, crie uma conta e entre no sistema. Para encerrar o servidor, volte ao terminal e pressione `Ctrl+C`.

O servidor embutido é destinado ao desenvolvimento local; não o use para publicar a aplicação na internet.

## Funcionalidades

- Cadastro com nome de usuário de 3 a 24 caracteres (`a-z`, `0-9` e `_`) e senha de pelo menos 8 caracteres.
- Armazenamento de senhas com `password_hash()` e autenticação com `password_verify()`.
- Criação e conclusão de tarefas, isoladas por usuário.
- Alternância entre tema claro e escuro, salva em cookie por 30 dias.
- Proteções didáticas: token CSRF em formulários, escape de conteúdo HTML e configuração segura de sessão.

## Estrutura do projeto

```text
public/       Páginas acessíveis pelo navegador e folha de estilos
src/          Inicialização da sessão e funções compartilhadas
storage/      Arquivos JSON de usuários e tarefas
```

Pontos de estudo:

| Conceito | Onde observar |
|---|---|
| Cadastro e hash de senha | `public/cadastro.php` |
| Autenticação e renovação da sessão | `public/login.php` |
| Sessão e funções compartilhadas | `src/bootstrap.php` |
| Tema e cookies | `public/tema.php` e `src/bootstrap.php` |
| Validação e tarefas | `public/index.php` |
| Encerramento da sessão | `public/logout.php` |
| Persistência em JSON | `storage/*.json` e funções `read_json()` / `update_json()` em `src/bootstrap.php` |

## Observações

- Os arquivos em `storage/` contêm dados locais do projeto. Não os compartilhe nem use dados ou senhas pessoais.
- Este projeto tem finalidade educacional e não foi preparado para uso em produção ou exposição pública.
