# Biblo - Sistema de Biblioteca Escolar

PHP + MySQL (mysqli). Painel com dashboard, alunos, livros, empréstimos/devoluções (com data prevista e atraso) e usuários.

## Instalação no Laragon

1. Copie a pasta do projeto para `C:\laragon\www\biblo` e inicie o Apache e o MySQL no Laragon.
2. Crie o banco e as tabelas importando o `schema.sql` (pode ser reimportado sem perder dados):
   ```
   mysql -uroot < schema.sql
   ```
   (ou pelo HeidiSQL/phpMyAdmin: abra e execute o conteúdo de `schema.sql`). O banco se chama `bd_saul`.
3. Crie o administrador (a senha é digitada no terminal, nada é gravado no repositório):
   ```
   cd C:\laragon\www\biblo
   php migrations\criar_admin.php "Seu Nome" seu@email.com admin
   ```
   O script também corrige a coluna `senha` de bancos antigos (VARCHAR(20) → 255). Se o admin já existir, a senha é redefinida.
4. (Opcional) Dados fictícios de demonstração: `mysql -uroot < seed_demo.sql`
5. Acesse `http://localhost/biblo/` e entre com o login (ou e-mail) e a senha escolhida.

Se o seu MySQL não usa `root` sem senha, defina as variáveis `BIBLO_DB_HOST`, `BIBLO_DB_USER`, `BIBLO_DB_PASS` e `BIBLO_DB_NAME` (valem para o site e para o `criar_admin.php`).

## Funcionalidades

- Login por login/e-mail com `password_hash`/`password_verify`; todo o painel exige sessão.
- Novos usuários só podem ser criados por um administrador logado (menu Usuários).
- Dashboard: totais de livros, alunos, empréstimos ativos, atrasados, devolvidos, exemplares disponíveis, últimos empréstimos, livros mais emprestados e gráfico por mês.
- Empréstimos: valida disponibilidade do livro e aluno ativo; devolução com cálculo de atraso.
- Formulários com proteção CSRF e consultas com prepared statements.

> Os arquivos `.html` na raiz são páginas do template Nura Admin e não fazem parte do sistema.
