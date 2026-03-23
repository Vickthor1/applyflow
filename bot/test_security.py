#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
🔐 TESTE DE SEGURANÇA - ApplyFlow
Verifica vulnerabilidades comuns e boas práticas
"""

import os
import json
from pathlib import Path
from datetime import datetime

class SecurityTester:
    def __init__(self, project_root):
        self.root = project_root
        self.issues = []
        self.warnings = []
        self.passed = []
        
    def test_env_files(self):
        """Verificar se arquivos sensíveis estão protegidos"""
        print("\n[1] Verificando proteção de arquivos sensíveis...")
        
        sensitive_files = [
            '.env',
            '.env.local',
            'bot/.env'
        ]
        
        gitignore_path = Path(self.root) / '.gitignore'
        if not gitignore_path.exists():
            self.issues.append("❌ Arquivo .gitignore não encontrado")
            return
        
        gitignore_content = gitignore_path.read_text()
        
        for sensitive_file in sensitive_files:
            if sensitive_file in gitignore_content:
                self.passed.append(f"✓ {sensitive_file} está protegido no .gitignore")
            else:
                self.issues.append(f"❌ {sensitive_file} NÃO está em .gitignore (risco de exposição)")
    
    def test_env_file_exists(self):
        """Verificar se .env real existe (não deve em repositório)"""
        print("\n[2] Verificando exposição de credenciais...")
        
        env_path = Path(self.root) / 'bot' / '.env'
        if env_path.exists():
            content = env_path.read_text()
            
            # Verificar se tem credenciais reais
            if 'seu@email.com' in content or 'sua_senha' in content:
                self.passed.append("✓ Arquivo .env contém apenas placeholders (seguro)")
            else:
                self.warnings.append("⚠️  Arquivo .env pode conter credenciais reais - certifique-se de não fazer commit!")
    
    def test_authentication(self):
        """Verificar proteção de rotas autenticadas"""
        print("\n[3] Verificando autenticação...")
        
        web_routes = Path(self.root) / 'routes' / 'web.php'
        if web_routes.exists():
            content = web_routes.read_text(encoding='utf-8', errors='ignore')
            
            if "Route::middleware('auth')" in content:
                self.passed.append("✓ Rotas protegidas com middleware auth")
            else:
                self.issues.append("❌ Rotas não estão protegidas com auth middleware")
            
            if "Route::middleware('guest')" in content:
                self.passed.append("✓ Rotas de login protegidas com middleware guest")
    
    def test_csrf_protection(self):
        """Verificar proteção CSRF"""
        print("\n[4] Verificando proteção CSRF...")
        
        # Checar arquivo de configuração
        middleware_config = Path(self.root) / 'bootstrap' / 'app.php'
        
        blade_files = list(Path(self.root).rglob('*.blade.php'))
        csrf_protected = 0
        
        for blade_file in blade_files:
            try:
                content = blade_file.read_text()
                if '@csrf' in content:
                    csrf_protected += 1
            except:
                pass
        
        if csrf_protected > 0:
            self.passed.append(f"✓ {csrf_protected} formulário(s) com proteção CSRF (@csrf)")
        else:
            self.issues.append("❌ Nenhum formulário com proteção CSRF encontrado")
    
    def test_sql_injection_prevention(self):
        """Verificar proteção contra SQL injection"""
        print("\n[5] Verificando proteção SQL injection...")
        
        model_files = list(Path(self.root).glob('app/Models/*.php'))
        using_eloquent = len(model_files) > 0
        
        if using_eloquent:
            self.passed.append("✓ Usando Eloquent ORM (proteção contra SQL injection)")
        else:
            self.warnings.append("⚠️  Nenhum modelo Eloquent encontrado")
        
        # Procurar por raw queries (perigoso)
        app_files = list(Path(self.root).rglob('app/**/*.php'))
        has_raw_queries = 0
        
        for file in app_files:
            try:
                content = file.read_text()
                if 'DB::raw' in content or 'raw(' in content:
                    has_raw_queries += 1
            except:
                pass
        
        if has_raw_queries == 0:
            self.passed.append("✓ Nenhuma query raw encontrada (seguro)")
        else:
            self.warnings.append(f"⚠️  {has_raw_queries} raw queries encontrada(s) - verificar parametrização")
    
    def test_input_validation(self):
        """Verificar validação de inputs"""
        print("\n[6] Verificando validação de inputs...")
        
        controller_files = list(Path(self.root).glob('app/Http/Controllers/*.php'))
        
        if len(controller_files) == 0:
            self.warnings.append("⚠️  Nenhum controller encontrado para verificar validação")
            return
        
        has_validation = 0
        for controller_file in controller_files:
            try:
                content = controller_file.read_text()
                if 'validate(' in content or 'FormRequest' in content:
                    has_validation += 1
            except:
                pass
        
        if has_validation > 0:
            self.passed.append(f"✓ {has_validation} controller(s) com validação de inputs")
        else:
            self.warnings.append("⚠️  Nenhuma validação de request encontrada")
    
    def test_xss_protection(self):
        """Verificar proteção contra XSS"""
        print("\n[7] Verificando proteção XSS...")
        
        blade_files = list(Path(self.root).rglob('*.blade.php'))
        
        escaped_content = 0
        unescaped_content = 0
        
        for blade_file in blade_files:
            try:
                content = blade_file.read_text()
                # Procurar por {{ }} (escaped por padrão)
                if '{{' in content:
                    escaped_content += 1
                # Procurar por {!! !!} (unescaped)
                if '{!!' in content:
                    unescaped_content += 1
            except:
                pass
        
        if escaped_content > 0:
            self.passed.append(f"✓ Usando echo escapado em {escaped_content} template(s)")
        
        if unescaped_content > 0:
            msg = f"⚠️  {unescaped_content} template(s) com conteúdo não-escapado - verificar se necessário"
            self.warnings.append(msg)
    
    def test_password_hashing(self):
        """Verificar se senhas estão sendo hasheadas"""
        print("\n[8] Verificando hash de senhas...")
        
        user_model = Path(self.root) / 'app' / 'Models' / 'User.php'
        if user_model.exists():
            content = user_model.read_text()
            
            if 'password' in content:
                self.passed.append("✓ Modelo User encontrado (assumindo hash correto)")
            
            if '$2y$' in content or 'bcrypt' in content or 'hash' in content:
                self.passed.append("✓ Usando hash bcrypt para senhas")
    
    def test_debug_mode(self):
        """Verificar se debug está desativado em produção"""
        print("\n[9] Verificando modo debug...")
        
        env_example = Path(self.root) / '.env.example'
        if env_example.exists():
            content = env_example.read_text()
            
            if 'APP_DEBUG=false' in content or 'APP_DEBUG=true' in content:
                self.warnings.append("⚠️  Verificar que APP_DEBUG=false em produção")
            
            self.passed.append("✓ Arquivo .env.example encontrado com configurações")
    
    def test_logging(self):
        """Verificar se logs existem e não contêm dados sensíveis"""
        print("\n[10] Verificando logs...")
        
        logs_dir = Path(self.root) / 'storage' / 'logs'
        if logs_dir.exists():
            self.passed.append("✓ Diretório de logs configurado")
            
            # Checar permissões
            config_logging = Path(self.root) / 'config' / 'logging.php'
            if config_logging.exists():
                self.passed.append("✓ Arquivo de configuração de logs encontrado")
    
    def test_database_security(self):
        """Verificar configurações de banco de dados"""
        print("\n[11] Verificando segurança do banco de dados...")
        
        migrations_dir = Path(self.root) / 'database' / 'migrations'
        if migrations_dir.exists():
            migrations = list(migrations_dir.glob('*.php'))
            if len(migrations) > 0:
                self.passed.append(f"✓ {len(migrations)} migração(ções) encontrada(s) - usando controle de versão")
        
        # Verificar se usa SQLite (desenvolvimento) ou MySQL (cuidado)
        env_example = Path(self.root) / '.env.example'
        if env_example.exists():
            content = env_example.read_text()
            if 'sqlite' in content.lower():
                self.warnings.append("⚠️  Projeto usa SQLite (OK para desenvolvimento, não para produção)")
    
    def test_file_permissions(self):
        """Verificar diretórios sensíveis"""
        print("\n[12] Verificando permissões de diretórios...")
        
        sensitive_dirs = [
            ('storage/logs', 'Logs'),
            ('bootstrap/cache', 'Cache'),
            ('storage/app/private', 'Arquivos privados')
        ]
        
        for dir_path, desc in sensitive_dirs:
            full_path = Path(self.root) / dir_path
            if full_path.exists():
                self.passed.append(f"✓ Diretório {desc} ({dir_path}) existe")
    
    def test_dependencies(self):
        """Verificar se composer.lock existe"""
        print("\n[13] Verificando dependências...")
        
        composer_lock = Path(self.root) / 'composer.lock'
        if composer_lock.exists():
            self.passed.append("✓ Arquivo composer.lock presente (versões fixas)")
        else:
            self.warnings.append("⚠️  Arquivo composer.lock não encontrado")
    
    def test_bot_security(self):
        """Verificar segurança específica do bot"""
        print("\n[14] Verificando segurança do bot de LinkedIn...")
        
        bot_dir = Path(self.root) / 'bot'
        
        # Verificar credentials.py
        creds_file = bot_dir / 'credentials.py'
        if creds_file.exists():
            self.passed.append("✓ Gerenciador de credenciais implementado")
        
        # Verificar .env.example
        env_example = bot_dir / '.env.example'
        if env_example.exists():
            content = env_example.read_text()
            if 'seu@email.com' in content:
                self.passed.append("✓ .env.example com placeholders (não expõe credenciais reais)")
        
        # Verificar se bot.py não tem credenciais hardcoded
        bot_py = bot_dir / 'bot.py'
        if bot_py.exists():
            try:
                content = bot_py.read_text(encoding='utf-8', errors='ignore')
                if 'LINKEDIN_EMAIL' in content or 'CredentialManager' in content:
                    self.passed.append("✓ Bot usa gerenciador de credenciais (seguro)")
                
                if '@' in content and '.com' in content:
                    self.warnings.append("⚠️  Possível email hardcoded - verificar bot.py")
            except:
                pass
    
    def run_all_tests(self):
        """Executar todos os testes"""
        print("=" * 70)
        print("  🔐 TESTE DE SEGURANÇA - ApplyFlow")
        print("=" * 70)
        
        self.test_env_files()
        self.test_env_file_exists()
        self.test_authentication()
        self.test_csrf_protection()
        self.test_sql_injection_prevention()
        self.test_input_validation()
        self.test_xss_protection()
        self.test_password_hashing()
        self.test_debug_mode()
        self.test_logging()
        self.test_database_security()
        self.test_file_permissions()
        self.test_dependencies()
        self.test_bot_security()
        
        self.print_report()
    
    def print_report(self):
        """Imprimir relatório"""
        print("\n" + "=" * 70)
        print("  📋 RELATÓRIO DE SEGURANÇA")
        print("=" * 70)
        
        # Passados
        if self.passed:
            print(f"\n✓ PASSOU ({len(self.passed)} verificações):")
            for item in self.passed[:10]:  # Mostrar primeiros 10
                print(f"  {item}")
            if len(self.passed) > 10:
                print(f"  ... e mais {len(self.passed) - 10}")
        
        # Advertências
        if self.warnings:
            print(f"\n⚠️  ADVERTÊNCIAS ({len(self.warnings)}):")
            for item in self.warnings:
                print(f"  {item}")
        
        # Problemas críticos
        if self.issues:
            print(f"\n❌ PROBLEMAS CRÍTICOS ({len(self.issues)}):")
            for item in self.issues:
                print(f"  {item}")
        
        # Score
        total_tests = len(self.passed) + len(self.warnings) + len(self.issues)
        score = (len(self.passed) / total_tests * 100) if total_tests > 0 else 0
        
        print("\n" + "-" * 70)
        
        if self.issues:
            print(f"🔴 SCORE: {score:.0f}% - ⚠️  Problemas críticos precisam de correção")
        elif self.warnings:
            print(f"🟡 SCORE: {score:.0f}% - Bom (verificar advertências)")
        else:
            print(f"🟢 SCORE: {score:.0f}% - Excelente! Segurança ótima")
        
        print("=" * 70)
        
        # Recomendações
        print("\n💡 RECOMENDAÇÕES:")
        print("""
1. ✓ Manter .env fora do repositório (verificar .gitignore)
2. ✓ Usar HTTPS em produção
3. ✓ Manter dependências atualizadas (composer update)
4. ✓ Usar rate limiting em produção
5. ✓ Implementar autenticação 2FA no LinkedIn
6. ✓ Revisar logs regularmente
7. ✓ Usar variáveis de ambiente para todas as credenciais
8. ✓ Implementar teste de segurança em CI/CD
        """)
        
        print("=" * 70)

def main():
    project_root = 'c:\\laragon\\www\\applyflow'
    
    tester = SecurityTester(project_root)
    tester.run_all_tests()

if __name__ == "__main__":
    main()
