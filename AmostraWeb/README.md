# AmostraWeb

Sistema para gerenciar o ciclo de vida das amostras enviadas aos clientes:
pedidos, amostras, prazos de avaliação, solicitações de prorrogação, cadastros
(clientes, produtos, estabelecimentos, usuários), permissões e auditoria.

A documentação funcional está na pasta `Docs/` na raiz do repositório.

## Requisitos

- PHP 8.3 ou superior
- Composer
- MySQL 8 (ou SQLite para desenvolvimento)

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure o banco no `.env` (`DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) e rode:

```bash
php artisan migrate
```

As migrations também criam a lista padrão de permissões (CRUD de cada módulo).

### Dados de exemplo

```bash
php artisan migrate:fresh --seed
```

> **Atenção:** `migrate:fresh` apaga todas as tabelas antes de recriar.

O seed cria estabelecimentos, usuários de exemplo (admin, gestores e vendedores), clientes,
produtos, pedidos, amostras em todos os status, solicitações de prazo e os grupos
"Gestores" e "Vendedores". A senha dos usuários de exemplo está em
`database/seeders/UsuarioExemploSeeder.php`. Exemplos de login: `ana.ribeiro` (administradora),
`carla.mendes` (gestora) e `bruno.lima` (vendedor).

## E-mail (redefinição de senha)

O "Esqueci minha senha" envia um código por e-mail. Em desenvolvimento, com `MAIL_MAILER=log`,
o e-mail é gravado em `storage/logs/laravel.log`. Para enviar de verdade, configure o SMTP no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.suaempresa.com.br
MAIL_PORT=587
MAIL_USERNAME=usuario
MAIL_PASSWORD=senha
MAIL_FROM_ADDRESS="nao-responda@suaempresa.com.br"
```

## Rodando

```bash
php artisan serve
```

### Rotina automática (status "Atrasada")

Amostras em análise com o prazo vencido passam para **Atrasada** automaticamente (RN009).
Isso acontece de duas formas:

1. A cada 10 minutos, na primeira requisição de um usuário logado (funciona sem configuração).
2. Pelo agendador do Laravel, de hora em hora. Em produção, configure o cron do servidor:

```bash
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

Para rodar manualmente: `php artisan amostras:atualizar-atrasadas`.

## Testes

```bash
php artisan test
```

Os testes usam SQLite em memória e cobrem login, permissões, escopo por perfil,
pedidos, ciclo de vida da amostra, solicitações de prazo, importação do ERP,
redefinição de senha, logs e a abertura de todas as telas.

## Onde fica cada coisa

| O quê | Onde |
|---|---|
| Nome, logos, módulos e permissões padrão | `config/amostraweb.php` |
| Regras de cada entidade | `app/Models` (ex.: `Amostra::alterarStatus`, `Pedido::visivelPara`) |
| Logs automáticos (com IP) | `app/Models/Concerns/Auditavel.php` e `app/Models/Log.php` |
| Código sequencial (000001…) | `app/Models/Concerns/GeraCodigoSequencial.php` |
| Bloqueio por permissão | `app/Http/Middleware/VerificarPermissao.php` + `routes/web.php` |
| Importação CSV do ERP | `app/Services/Importacao` |
| Menu lateral | `app/View/Composers/MenuComposer.php` |
| Traduções (pt-BR) | `lang/pt_BR` |
| Estilos e cores | `public/css/layout` (cores em `root.css`) |
| Comportamentos comuns (abas, máscaras, menu) | `public/js/app.js` |
