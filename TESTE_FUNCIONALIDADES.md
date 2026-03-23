# 📋 Plano de Testes - ApplyFlow

## ✅ Status de Execução

Data de Início: 17 de Março de 2026

---

## 1️⃣ AUTENTICAÇÃO

### 1.1 Login
- **Teste**: Login com usuário de teste
- **Credenciais**: 
  - Email: `test@example.com`
  - Senha: `password`
- **Esperado**: Redirecionar para dashboard
- **Status**: ⏳ PENDENTE (Acessar http://localhost:8000/login)

### 1.2 Register (Novo Usuário)
- **Teste**: Criar novo usuário via formulário de registro
- **Dados**:
  - Nome: Test User 2
  - Email: test2@example.com
  - Senha: password123
- **Esperado**: Usuário criado e logado automaticamente
- **Status**: ⏳ PENDENTE

### 1.3 Logout
- **Teste**: Fazer logout
- **Esperado**: Redirecionar para login
- **Status**: ⏳ PENDENTE

---

## 2️⃣ DASHBOARD

### 2.1 Listagem de Vagas
- **Teste**: Verificar listagem de vagas do usuário
- **Esperado**: 
  - ✓ 3 vagas de teste exibidas
  - ✓ Cards com informações: título, empresa, localização, salário, score
  - ✓ Links funcionais (Ver Vaga, Gerar CV, Baixar CV)
  - ✓ Badge de source (Remotive, Indeed, LinkedIn, etc)
- **Status**: ⏳ PENDENTE

### 2.2 Analytics
- **Teste**: Verificar seção de analytics
- **Esperado**:
  - ✓ Total de vagas: 3
  - ✓ Score médio exibido
  - ✓ Gráficos renderizados (Chart.js)
  - ✓ Vagas por fonte e por tipo
- **Status**: ⏳ PENDENTE

### 2.3 Filtros
- **Teste**: Aplicar filtros de busca
- **Filtros a testar**:
  - Por localização
  - Por tipo de emprego (full-time, part-time, etc)
  - Por salário mínimo
  - Por fonte da vaga
- **Esperado**: Vagas filtradas corretamente
- **Status**: ⏳ PENDENTE

### 2.4 Busca de Vagas
- **Teste**: Usar função "Buscar Vagas por Tags"
- **Entrada**: `PHP,Laravel,Python`
- **Esperado**: 
  - ✓ Sistema chama comando artisan jobs:search
  - ✓ Vagas são adicionadas ao dashboard
  - ✓ Página recarrega após 2 segundos
- **Status**: ⏳ PENDENTE

---

## 3️⃣ PROFILE (PERFIL DO USUÁRIO)

### 3.1 Visualizar Perfil
- **Teste**: Acessar /profile
- **Esperado**: Formulário com dados do usuário
- **Status**: ⏳ PENDENTE

### 3.2 Atualizar Perfil
- **Teste**: Atualizar informações do usuário
- **Dados**: Alterar nome e outras informações
- **Esperado**: Dados salvos no banco
- **Status**: ⏳ PENDENTE

---

## 4️⃣ CURRÍCULO (RESUME)

### 4.1 Gerar CV
- **Teste**: Clicar em "Gerar CV" para uma vaga
- **Esperado**: 
  - ✓ Endpoint /resume/{jobId} responde
  - ✓ CV personalizado para a vaga é gerado
- **Status**: ⏳ PENDENTE

### 4.2 Baixar CV
- **Teste**: Clicar em "Baixar CV"
- **Esperado**: 
  - ✓ Download de arquivo PDF
  - ✓ CV salvo como resume_*.html
- **Status**: ⏳ PENDENTE

---

## 5️⃣ APLICAÇÃO AUTOMÁTICA (BOT)

### 5.1 Auto-Apply
- **Teste**: Usar funcionalidade "Auto-apply com Bot"
- **Dados**:
  - Email LinkedIn: (não testado, requer credenciais reais)
  - Keyword: php
- **Esperado**: Sistema inicia processo de aplicação automática
- **Status**: ⏳ PENDENTE (Requer credenciais LinkedIn)

---

## 📊 RESUMO

| Funcionalidade | Status | Observações |
|---|---|---|
| Autenticação (Login) | ⏳ | Pronto para testar |
| Autenticação (Register) | ⏳ | Pronto para testar |
| Logout | ⏳ | Pronto para testar |
| Dashboard | ⏳ | 3 vagas de teste criadas |
| Analytics | ⏳ | Dados preparados |
| Filtros | ⏳ | Pronto para testar |
| Busca de Vagas | ⏳ | Pronto para testar |
| Perfil do Usuário | ⏳ | Pronto para testar |
| CV Personalizado | ⏳ | Pronto para testar |
| Download de CV | ⏳ | Pronto para testar |
| Auto-Apply Bot | ⏳ | Requer credenciais reais |

---

## 🔧 AMBIENTE DE TESTE

- **Servidor**: http://localhost:8000
- **Banco de Dados**: SQLite (database.sqlite)
- **Framework**: Laravel 12.54.1
- **Usuário de Teste**:
  - Email: `test@example.com`
  - Senha: `password`
- **Vagas de Teste Criadas**: 3

---

## 📝 NOTAS

1. Servidor Laravel rodando em background na porta 8000
2. Arquivo .env modificado para usar SQLite (backup em .env.backup)
3. Migrações executadas com sucesso
4. Dados de teste inseridos via TestSeeder
5. CSS renovado - interface moderna com Tailwind CSS
