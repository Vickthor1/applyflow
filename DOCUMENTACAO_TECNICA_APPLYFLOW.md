# Documentação Técnica - ApplyFlow

## 1. Introdução

Este documento apresenta a arquitetura, requisitos, implementação e uso do sistema ApplyFlow, plataforma de busca/aplicação de vagas de emprego com suporte a perfil de usuário, análise de compatibilidade e acompanhamento de candidaturas.

A proposta segue o modelo da documentação do projeto Homework, com seções: visão geral, requisitos, projeto lógico, implementação, instalação, manual e considerações finais.

## 2. Visão Geral do ApplyFlow

ApplyFlow é um sistema Web desenvolvido em Laravel (PHP 10+), com autenticação tradicional (login/register), área de dashboard de vagas, gerenciamento de perfil e recuperação de conta por e-mail.

Fluxo principal:
- Usuário cadastra-se / faz login.
- Visita Dashboard onde vaga são listadas e filtradas (localização, tipo, salário, origem).
- Aplica vagas (status de candidaturas) e acompanha dados analíticos.
- Atualiza perfil, habilidades, experiência, bio.
- Gera e exibe currículo (view dedicada com opção imprimir/salvar PDF).
- Recuperação de senha: envio de link + reset.

## 3. Requisitos

### 3.1 Funcionais

- Registro e login de usuário
- Edição de perfil: nome, email, linkedin, bio, skills, experiência
- Dashboard de vagas com filtro e ranking por match_score
- Aplicar em vaga e acompanhar status (applied, viewed, interview, rejected, hired)
- IA para análise de perfil (script Python `bot/ai_resume_bot.py`)
- Sistema de recuperação de senha via e-mail
- Página de currículo responsiva para impressão

### 3.2 Não-funcionais

- Segurança (hash de senha, sessões)
- Banco de dados SQLite em ambiente local, possivelmente MySQL em produção
- Modularidade: controllers, services e jobs independentes
- UI limpa e com suporte a multi-idioma (pt/en/es)

## 4. Arquitetura e Projeto Lógico

### 4.1 Estrutura de diretórios

- `app/Http/Controllers`: controllers (`AuthController`, `DashboardController`, `ProfileController`, `JobController`, `ApplicationController`)
- `app/Models`: `User`, `Job`
- `app/Services`: `JobScraperService`, `MatchService`, `ResumeService`
- `routes/web.php`: rotas de autenticação, dashboard, perfil, aplicações e integração IA
- `resources/views`: views com Blade (login, register, dashboard, profile, resume, reset-password, forgot-password)
- `resources/css/app.css`: estilos globais e específicos das páginas
- `database/migrations`: esquema de banco
- `bot/ai_resume_bot.py`: análise de IA externa

### 4.2 Modelo de dados

#### `users`
- `id`, `name`, `email`, `password`, `linkedin`, `bio`, `skills` (JSON), `experience`, timestamps

#### `jobs`
- `id`, `title`, `company`, `description`, `location`, `link`, `match_score`, `applied`, `salary`, `job_type`, `source`, `applied_at`, `application_status`, `user_id`, timestamps

#### `password_reset_tokens` (novo)
- `email`, `token`, `created_at`

### 4.3 Rotas principais

#### Guest
- `GET /login` - exibir login
- `POST /login` - processar login
- `GET /register` - exibir registro
- `POST /register` - processar cadastro
- `GET /forgot-password` - exibir formulário de recuperação
- `POST /forgot-password` - enviar link de reset
- `GET /reset-password/{token}` - exibir reset
- `POST /reset-password` - aplicar nova senha

#### Auth
- `POST /logout` - logout
- `GET /dashboard` - listagem e filtros de vagas
- `GET /profile`; `POST /profile` - gerenciar perfil
- `POST /jobs/{jobId}/apply` - aplicar em vaga
- `PUT /jobs/{jobId}/status` - atualizar status de candidatura
- `GET /applications`, `GET /applications/stats` - status/candidaturas
- `GET /meu-curriculo` - exibir currículo
- `GET /api/analyze-job/{id}` - análise IA de job (via Python script)

#### API JWT
- `POST /api/login` - login JWT
- `POST /api/register` - registro JWT
- `POST /api/logout` - logout JWT (middleware auth:api)
- `GET /api/user` - obter usuário autenticado (middleware auth:api)

## 5. Implementação

### 5.1 AuthController
- Login, registro, logout (sessão para web)
- Recuperação de senha usando `Password` facade
- Reset salva `password` hash e `remember_token`
- **JWT API**: `apiLogin`, `apiRegister`, `apiLogout`, `getUser` usando `tymon/jwt-auth`

### 5.2 ProfileController
- `show()` retorna view `profile` com `auth()->user()`.
- `update()` salva `name`, `email`, `linkedin`, `bio`, `experience`, e processa `skills_text` em array.

### 5.3 DashboardController
- Filtra `Job` por location, job_type, min_salary, source
- Calcula dados analíticos (contagem, média, group by)

### 5.4 JobController
- `store`: traduz título/descrição com `GoogleTranslate('pt')` e cria vaga
- `analyzeWithAI`: chama `bot/ai_resume_bot.py`, passa `job->description` + perfil do usuário, retorna JSON processado

### 5.5 ApplicationController
- Marca vaga como aplicada, atualiza `application_status`, retorna as stats do usuário

### 5.6 CSS centralizado
- `resources/css/app.css` contém estilos globais + resume + profile, removendo `style` inline nas views

### 5.7 JWT Authentication
- Pacote: `tymon/jwt-auth`
- User implementa `JWTSubject`
- Guards: `web` (session) e `api` (jwt)
- Rotas API: `/api/login`, `/api/register`, `/api/logout`, `/api/user`
- Middleware: `auth:api` para proteger endpoints API

## 6. Instalação e execução

### 6.1 Requisitos
- PHP 8.1+ / 10
- Composer
- Node (Vite) e npm yarn
- SQLite (ou MySQL com ajustes em `.env`)

### 6.2 Passos
1. Clonar repositório
2. `composer install`
3. `cp .env.example .env`
4. Configurar `.env` (`APP_URL`, `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`) e mailer (`MAIL_MAILER=log` ou SMTP)
5. Criar arquivo SQLite: `touch database/database.sqlite`
6. `php artisan migrate`
7. `php artisan key:generate`
8. `npm install` e `npm run dev` (ou build)
9. `php artisan serve`

### 6.3 Recuperação de senha
- Formulário: `/forgot-password`
- Reset via token: `/reset-password/{token}`
- `password_reset_tokens` armazena token e email
- No local dev, emails ficam em `storage/logs/laravel.log` via driver `log`

## 7. Manual do usuário

### 7.1 Usuário comum (Web)
- Fazer login/Register
- Preencher perfil (nome/email/linkedin/bio/skills/experience)
- Visitar dashboard e aplicar em vagas
- Acompanhar status em `applications` API
- Gerar currículo em `/meu-curriculo`

### 7.2 Desenvolvedor API (JWT)
- **Login**: `POST /api/login` com `{"email": "...", "password": "..."}` → retorna `{"token": "...", "user": {...}}`
- **Registro**: `POST /api/register` com `{"name": "...", "email": "...", "password": "...", "password_confirmation": "..."}`
- **Usar API**: Enviar header `Authorization: Bearer {token}`
- **Logout**: `POST /api/logout` (com token)
- **Obter usuário**: `GET /api/user` (com token)
- **Análise IA**: `GET /api/analyze-job/{id}` (com token de sessão web, pois usa auth())

### 7.3 Administrador (não implementado full ainda)
- Se houver interface futura, deverá incluir gerência de jobs, usuários e relatórios.

## 8. Considerações finais

O ApplyFlow é um MVP robusto com foco em busca rápida de vagas e tracking de candidaturas. O ponto forte atual é a integração com IA para análise de compatibilidade e a experiência única de gerar currículo imprimível.

### Próximos incrementos recomendados
- Autenticação de dois fatores (2FA)
- Repositoriag de uploads de CV e histórico de candidaturas
- Filtros mais avançados (salário mínimo/máximo por campo numérico no banco)
- Dashboard para administrador e controle de usuários
- Email real via SMTP/SendGrid/SES

---

> Observação: esta documentação foi gerada a partir do código existente atualmente no repositório e segue o exemplo estruturado do documento `Documentação Técnica da Aplicação Web Homework` (texto, listas, arquitetura e manual de uso).