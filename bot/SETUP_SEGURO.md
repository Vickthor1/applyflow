# 🔐 Sistema Seguro de Credenciais - ApplyFlow Bot

## Por que credenciais no arquivo .env?

Em vez de enviar suas credenciais do LinkedIn através da web interface (inseguro), o ApplyFlow agora usa um sistema seguro baseado em variáveis de ambiente:

✅ **Seguro**: As credenciais nunca são expostas pela web interface
✅ **Oculto**: Arquivo `.env` é ignorado no git (não é compartilhado)
✅ **Mascarado**: Senhas são mascaradas em logs
✅ **Local**: Tudo rodando localmente na sua máquina

## 🚀 Como Configurar

### Passo 1: Copiar arquivo de exemplo
```bash
cd bot
cp .env.example .env
```

### Passo 2: Editar arquivo .env
Abra `bot/.env` e configure com suas credenciais:

```env
# Credenciais do LinkedIn Bot
LINKEDIN_EMAIL=seu@email.com        # Seu email do LinkedIn
LINKEDIN_PASSWORD=sua_senha_segura  # Sua senha do LinkedIn
BOT_KEYWORD=php                     # Keyword padrão para buscar vagas
BOT_MAX_APPLICATIONS=3              # Número máximo de aplicações
BOT_DEBUG=false                     # true para ver navegador, false para headless
```

### Passo 3: Usar o bot

#### Opção A: Via Dashboard Web
1. Acesse http://localhost:8000/dashboard
2. Vá para a seção "🤖 Auto-apply com Bot"
3. Digite a palavra-chave desejada
4. Clique "Aplicar às Vagas"
5. O bot usará credenciais do arquivo `.env` automaticamente

#### Opção B: Via Terminal (mais direto)
```bash
cd c:\laragon\www\applyflow
python bot/bot.py
```

Ou com palavra-chave customizada:
```bash
python bot/bot.py PHP
python bot/bot.py "Python Developer"
python bot/bot.py Laravel
```

### Passo 4: Testar Login (recomendado)
Antes de usar a aplicação automática, teste se o loginI funciona:

```bash
cd c:\laragon\www\applyflow
python bot/test_login.py seu@email.com sua_senha
```

Este script vai:
- Abrir o navegador (não em headless)
- Tentar fazer login
- Mostrar exatamente onde/quando falha
- Tirar screenshot se houver erro

## 📋 Arquivos do Sistema

```
bot/
├── .env              ← SUAS CREDENCIAIS (nunca commitar!)
├── .env.example      ← Exemplo de como configurar
├── bot.py            ← Script principal de auto-aplicação
├── test_login.py     ← Script para testar login
└── credentials.py    ← Gerenciador seguro de credenciais
```

## 🔒 Segurança

### O que é protegido?
- Credenciais carregadas do arquivo local (não passam pela web)
- Senhas mascaradas em logs (aparecem como `****`)
- Arquivo .env adicionado ao `.gitignore`

### O que fazer?
✅ Mantenha o arquivo `.env` **APENAS NA SUA MÁQUINA**
✅ Nunca compartilhe o arquivo `.env`
✅ Nunca faça push/commit do `.env` para o repositório
✅ Se compartilhar o projeto, compartilhe apenas `.env.example`

### O que NÃO fazer?
❌ Não envie credenciais pelo formulário web
❌ Não commit o arquivo `.env` no git
❌ Não compartilhe o `.env` por email/chat
❌ Não coloque credenciais em URLs

## 🐛 Troubleshooting

### Erro: "Arquivo .env não encontrado"
```
Solução: Crie o arquivo bot/.env copiando bot/.env.example
cp bot/.env.example bot/.env
```

### Erro: "Credenciais não configuradas"
```
Solução: Edite bot/.env e preencha:
- LINKEDIN_EMAIL
- LINKEDIN_PASSWORD
```

### Erro: "Login falhou"
```
Solução 1: Verificar credenciais
- Email e senha estão corretos?

Solução 2: Usar test_login.py para debug
python bot/test_login.py seu@email.com sua_senha

Solução 3: LinkedIn pode estar bloqueando
- Desabilitar 2FA temporariamente
- Verificar se há notificações de segurança
- Tentar em horário diferente
```

### O navegador não abre (modo headless)
```
Solução: Ativar modo debug em bot/.env
BOT_DEBUG=true
```

## 📚 Variáveis Disponíveis

| Variável | Descrição | Obrigatório | Padrão |
|----------|-----------|-------------|--------|
| `LINKEDIN_EMAIL` | Email do LinkedIn | ✅ | Nenhum |
| `LINKEDIN_PASSWORD` | Senha do LinkedIn | ✅ | Nenhum |
| `BOT_KEYWORD` | Keyword padrão | ❌ | `php` |
| `BOT_MAX_APPLICATIONS` | Max aplicações | ❌ | `3` |
| `BOT_DEBUG` | Mostrar navegador | ❌ | `false` |

## 🎯 Próximos Passos

1. ✅ Copie `.env.example` para `.env`
2. ✅ Configure suas credenciais
3. ✅ Teste com `test_login.py`
4. ✅ Use via dashboard ou terminal

## 💡 Dicas

- **Primeiro teste**: Use `test_login.py` para verificar credenciais
- **Debug**: Mude `BOT_DEBUG=true` para ver o navegador
- **Logs**: Verifique `storage/logs/laravel.log` para erros
- **Limite de aplicações**: Aumente `BOT_MAX_APPLICATIONS` com cuidado (respeito LinkedIn)

---

**Dúvidas?** Checa os logs ou use o modo debug para entender o fluxo! 🚀
