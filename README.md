<p align="center"> <a href="#" target="_blank"> <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="ApplyFlow Logo"> </a> </p> <p align="center"> <a href="#"><img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status"></a> <a href="#"><img src="https://img.shields.io/github/stars/seu-usuario/applyflow" alt="Stars"></a> <a href="#"><img src="https://img.shields.io/github/license/seu-usuario/applyflow" alt="License"></a> <a href="#"><img src="https://img.shields.io/badge/Laravel-10-red" alt="Laravel Version"></a> </p>
About ApplyFlow

ApplyFlow is a modern web platform built with Laravel that simplifies the job search and application process.

The system centralizes everything a candidate needs:

Job discovery
Application tracking
Profile management
Resume generation
AI-powered job matching

ApplyFlow focuses on productivity, automation, and a clean user experience.

Core Features

ApplyFlow provides essential tools for job seekers:

Smart job dashboard with filters and ranking
Application tracking system (applied, interview, hired, etc.)
Professional profile management
Automatic resume generation (print-ready PDF)
AI integration for:
Resume analysis
Job compatibility scoring
Strategic summaries
Architecture Overview

The project follows a clean Laravel structure:

Controllers: Handle business logic (Auth, Dashboard, Profile, Jobs)
Models: Represent database entities (User, Job)
Services: AI integration, matching logic, scraping
Views (Blade): UI rendering
API Layer: JWT-based authentication
Technologies

ApplyFlow is built using modern technologies:

Laravel 10
PHP 8.1+
Blade Templates
Tailwind CSS
SQLite / MySQL
JWT Authentication (tymon/jwt-auth)
Python (AI processing)
Installation

Follow these steps to run the project locally:

git clone https://github.com/seu-usuario/applyflow.git
cd applyflow

composer install
cp .env.example .env
php artisan key:generate

# Database
touch database/database.sqlite
php artisan migrate

# Frontend
npm install
npm run dev

# Run server
php artisan serve
Configuration

Update your .env file:

DB_CONNECTION=sqlite
MAIL_MAILER=log
API Authentication (JWT)

ApplyFlow also provides an API layer:

POST /api/login
POST /api/register
GET /api/user (requires token)
POST /api/logout

Use:

Authorization: Bearer {token}
AI Integration

ApplyFlow includes a Python-based AI module:

bot/ai_resume_bot.py

This module is responsible for:

Analyzing job descriptions
Comparing with user profile
Generating strategic resume summaries
Resume System

Users can generate a professional resume at:

/meu-curriculo

Features:

Clean layout
Printable (A4 format)
PDF export
Auto-filled from profile
Roadmap

Planned improvements:

LinkedIn data auto-import
Resume upload (PDF parsing)
Admin dashboard
Notifications system
Advanced filters
Smarter AI matching
Contributing

Thank you for considering contributing to ApplyFlow!

You can help by:

Reporting bugs
Suggesting features
Submitting pull requests
Code of Conduct

Please be respectful and constructive when contributing to this project.

Security Vulnerabilities

If you discover a security issue, please report it privately instead of opening a public issue.

License

ApplyFlow is open-sourced software licensed under the MIT license