<p align="center">
  <a href="#" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="ApplyFlow Logo">
  </a>
</p>

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status"></a>
  <a href="#"><img src="https://img.shields.io/github/stars/seu-usuario/applyflow" alt="Stars"></a>
  <a href="#"><img src="https://img.shields.io/github/license/seu-usuario/applyflow" alt="License"></a>
  <a href="#"><img src="https://img.shields.io/badge/Laravel-10-red" alt="Laravel Version"></a>
</p>

---

## Sobre o ApplyFlow | About ApplyFlow

**Português:**  
ApplyFlow é uma plataforma web moderna construída com Laravel que simplifica o processo de busca e candidatura a empregos.  
O sistema centraliza tudo que um candidato precisa:  

- Descoberta de vagas  
- Acompanhamento de candidaturas  
- Gerenciamento de perfil  
- Geração de currículo  
- Compatibilidade de vagas com IA  

ApplyFlow foca em produtividade, automação e experiência limpa para o usuário.  

**English:**  
ApplyFlow is a modern web platform built with Laravel that simplifies the job search and application process.  
The system centralizes everything a candidate needs:  

- Job discovery  
- Application tracking  
- Profile management  
- Resume generation  
- AI-powered job matching  

ApplyFlow focuses on productivity, automation, and a clean user experience.  

---

## Funcionalidades Principais | Core Features

**Português:**  
- Dashboard inteligente com filtros e ranking  
- Sistema de acompanhamento de candidaturas (candidatado, entrevista, contratado, etc.)  
- Gerenciamento de perfil profissional  
- Geração automática de currículo (PDF pronto para impressão)  
- Integração com IA para:  
  - Análise de currículo  
  - Compatibilidade com vagas  
  - Resumos estratégicos  

**English:**  
- Smart job dashboard with filters and ranking  
- Application tracking system (applied, interview, hired, etc.)  
- Professional profile management  
- Automatic resume generation (print-ready PDF)  
- AI integration for:  
  - Resume analysis  
  - Job compatibility scoring  
  - Strategic summaries  

---

## Arquitetura | Architecture Overview

**Português:**  
- **Controllers:** Lógica de negócios (Auth, Dashboard, Profile, Jobs)  
- **Models:** Representação das entidades do banco (User, Job)  
- **Services:** Integração com IA, lógica de matching, scraping  
- **Views (Blade):** Renderização da interface  
- **API Layer:** Autenticação via JWT  

**English:**  
- **Controllers:** Handle business logic (Auth, Dashboard, Profile, Jobs)  
- **Models:** Represent database entities (User, Job)  
- **Services:** AI integration, matching logic, scraping  
- **Views (Blade):** UI rendering  
- **API Layer:** JWT-based authentication  

---

## Tecnologias | Technologies

**Português & English:**  
- Laravel 10  
- PHP 8.1+  
- Blade Templates  
- Tailwind CSS  
- SQLite / MySQL  
- JWT Authentication (tymon/jwt-auth)  
- Python (processamento de IA | AI processing)  

---

## Instalação | Installation

```bash
git clone https://github.com/seu-usuario/applyflow.git
cd applyflow

composer install
cp .env.example .env
php artisan key:generate

# Banco de dados | Database
touch database/database.sqlite
php artisan migrate

# Frontend
npm install
npm run dev

# Rodar servidor | Run server
php artisan serve
