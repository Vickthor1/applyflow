<p align="center">
  <a href="#" target="_blank">
    <img src="imgs/logo-applyflow.png" width="400" alt="ApplyFlow Logo">
  </a>
</p>

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status"></a>
  <a href="#"><img src="https://img.shields.io/github/stars/seu-usuario/applyflow" alt="Stars"></a>
  <a href="#"><img src="https://img.shields.io/github/license/seu-usuario/applyflow" alt="License"></a>
  <a href="#"><img src="https://img.shields.io/badge/Laravel-10-red" alt="Laravel Version"></a>
  <a href="#"><img src="https://img.shields.io/badge/PHP-8.1+-blue" alt="PHP Version"></a>
</p>

---

## Sobre o ApplyFlow | About ApplyFlow

**Português:**  
🚀 **ApplyFlow** é uma plataforma web moderna construída com Laravel que transforma a forma como candidatos encontram e se candidatam a vagas.  
Tudo que um candidato precisa, em um só lugar:  

- 🔍 Descoberta de vagas inteligentes  
- 📄 Acompanhamento detalhado de candidaturas  
- 👤 Gerenciamento de perfil profissional  
- 📝 Geração automática de currículo pronto para impressão  
- 🤖 Compatibilidade de vagas com IA  

**Diferencial:** foco em produtividade, automação e experiência limpa para o usuário.

**English:**  
🚀 **ApplyFlow** is a modern web platform built with Laravel that transforms the job search and application process.  
Everything a candidate needs, in one place:  

- 🔍 Smart job discovery  
- 📄 Detailed application tracking  
- 👤 Professional profile management  
- 📝 Automatic resume generation (print-ready PDF)  
- 🤖 AI-powered job matching  

**Highlight:** Focused on productivity, automation, and a clean user experience.

---

## Por que ApplyFlow? | Why ApplyFlow?

**Português:**  
- 💡 Plataforma completa que economiza tempo e esforço do candidato  
- ⚡ Interface intuitiva e responsiva  
- 🤖 IA que sugere oportunidades ideais e resume perfis estratégicos  

**English:**  
- 💡 Complete platform that saves candidates time and effort  
- ⚡ Intuitive and responsive interface  
- 🤖 AI that suggests ideal opportunities and summarizes profiles strategically  

---

## Funcionalidades Principais | Core Features

**Português:**  
- 📊 Dashboard inteligente com filtros e ranking  
- ✅ Sistema de acompanhamento de candidaturas (candidatado, entrevista, contratado, etc.)  
- 👤 Gerenciamento de perfil profissional completo  
- 📝 Geração automática de currículo em PDF  
- 🤖 Integração com IA para:  
  - Análise de currículo  
  - Compatibilidade com vagas  
  - Resumos estratégicos  

**English:**  
- 📊 Smart job dashboard with filters and ranking  
- ✅ Application tracking system (applied, interview, hired, etc.)  
- 👤 Full professional profile management  
- 📝 Automatic resume generation (PDF)  
- 🤖 AI integration for:  
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
