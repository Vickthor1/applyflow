#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para testar e debugar login no LinkedIn
Simpler version para verificar fluxo de autenticação
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time
import sys
import logging

# Configurar logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

def get_chrome_options():
    """Configurar opções do Chrome"""
    chrome_options = Options()
    # Comentar headless para ver o que está acontecendo
    # chrome_options.add_argument("--headless")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
    chrome_options.add_experimental_option('useAutomationExtension', False)
    chrome_options.add_argument("--window-size=1920,1080")
    chrome_options.add_argument("user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36")
    return chrome_options

def test_login(email, password):
    """
    Funciona para testar login no LinkedIn
    Este script permite visualizar o que está acontecendo
    """
    driver = None
    try:
        logger.info("=" * 60)
        logger.info("Iniciando teste de login no LinkedIn")
        logger.info("=" * 60)
        
        logger.info("Criando instância do Chrome...")
        driver = webdriver.Chrome(options=get_chrome_options())
        
        logger.info(f"Email: {email}")
        logger.info(f"Password: {'*' * len(password)}")
        
        # Acessar página de login
        logger.info("\n[1] Acessando página de login...")
        driver.get("https://www.linkedin.com/login")
        time.sleep(3)
        
        # Verificar se está na página de login
        current_url = driver.current_url
        logger.info(f"URL atual: {current_url}")
        
        # Tentar encontrar campo de email
        logger.info("\n[2] Procurando campo de email...")
        try:
            email_field = WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.ID, "username"))
            )
            logger.info("✓ Campo de email encontrado")
            
            # Limpar e preencher email
            email_field.clear()
            email_field.send_keys(email)
            logger.info(f"✓ Email inserido: {email}")
            time.sleep(1)
        except Exception as e:
            logger.error(f"✗ Erro ao encontrar/preencher email: {e}")
            logger.info("Tentando ID alternativo: 'email-or-phone'")
            try:
                email_field = driver.find_element(By.ID, "email-or-phone")
                email_field.send_keys(email)
                logger.info(f"✓ Email inserido (campo alt): {email}")
            except:
                logger.error("Campo de email não encontrado com nenhum seletor")
                return False
        
        # Tentar encontrar campo de senha
        logger.info("\n[3] Procurando campo de senha...")
        try:
            password_field = WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.ID, "password"))
            )
            logger.info("✓ Campo de senha encontrado")
            
            # Preencher senha
            password_field.clear()
            password_field.send_keys(password)
            logger.info("✓ Senha inserida")
            time.sleep(1)
        except Exception as e:
            logger.error(f"✗ Erro ao encontrar/preencher senha: {e}")
            return False
        
        # Clicar no botão de login
        logger.info("\n[4] Procurando botão de login...")
        try:
            # Tentar diferentes seletores para o botão
            submit_button = None
            selectors = [
                (By.XPATH, "//button[@type='submit']"),
                (By.XPATH, "//button[@aria-label='Sign in']"),
                (By.XPATH, "//button[contains(text(), 'Sign in')]"),
                (By.CSS_SELECTOR, "button[type='submit']"),
            ]
            
            for selector_type, selector_value in selectors:
                try:
                    submit_button = WebDriverWait(driver, 5).until(
                        EC.element_to_be_clickable((selector_type, selector_value))
                    )
                    logger.info(f"✓ Botão encontrado com seletor: {selector_value}")
                    break
                except:
                    continue
            
            if not submit_button:
                logger.error("Botão de login não encontrado")
                return False
            
            logger.info("Clicando no botão de login...")
            submit_button.click()
            logger.info("✓ Botão clicado")
            
        except Exception as e:
            logger.error(f"✗ Erro ao clicar no botão: {e}")
            return False
        
        # Aguardar após login
        logger.info("\n[5] Aguardando resposta do servidor...")
        logger.info("⏳ Aguardando 10 segundos para processamento...")
        time.sleep(10)
        
        # Verificar resultado
        logger.info("\n[6] Verificando resultado do login...")
        current_url = driver.current_url
        logger.info(f"URL após login: {current_url}")
        
        # Possíveis URLs após login bem-sucedido
        success_indicators = [
            "feed",
            "home",
            "mynetwork",
            "jobs",
            "notifications"
        ]
        
        # Possíveis URLs indicando falha
        failure_indicators = [
            "checkpoint",
            "challenge",
            "login",
            "error"
        ]
        
        if any(indicator in current_url.lower() for indicator in failure_indicators):
            logger.warning("⚠️  Possível desafio de segurança ou login falhou")
            logger.info(f"URL contém: {[ind for ind in failure_indicators if ind in current_url.lower()]}")
            
            # Tirar screenshot para debug
            screenshot_path = "c:/laragon/www/applyflow/bot/login_failed.png"
            driver.save_screenshot(screenshot_path)
            logger.info(f"Screenshot salvo em: {screenshot_path}")
            
            # Mostrar página source para debug
            logger.info("\n[DEBUG] Página HTML (primeiros 500 caracteres):")
            logger.info(driver.page_source[:500])
            
            return False
        elif any(indicator in current_url.lower() for indicator in success_indicators):
            logger.info("✓ LOGIN BEM-SUCEDIDO!")
            logger.info(f"Você está em: {current_url}")
            return True
        else:
            logger.info("? Status desconhecido - URL não corresponde a padrão esperado")
            logger.info(f"Verifique a página em: {current_url}")
            
            # Tirar screenshot para verificação manual
            screenshot_path = "c:/laragon/www/applyflow/bot/login_status.png"
            driver.save_screenshot(screenshot_path)
            logger.info(f"Screenshot salvo em: {screenshot_path}")
            
            return None
        
    except Exception as e:
        logger.error(f"\n✗ ERRO GERAL: {e}")
        import traceback
        traceback.print_exc()
        return False
        
    finally:
        if driver:
            logger.info("\n[?] Fechando navegador em 5 segundos...")
            logger.info("    (Você pode interagir com o navegador neste tempo)")
            time.sleep(5)
            driver.quit()
            logger.info("✓ Navegador fechado")

def main():
    """Função principal"""
    print("\n" + "="*60)
    print("  TESTE DE LOGIN - LINKEDIN")
    print("="*60 + "\n")
    
    if len(sys.argv) < 3:
        print("Uso: python test_login.py <email> <password>")
        print("\nExemplo:")
        print("  python test_login.py seu@email.com sua_senha")
        print("\nNota: O navegador NÃO estará em headless mode,")
        print("      então você poderá ver exatamente o que está acontecendo.")
        sys.exit(1)
    
    email = sys.argv[1]
    password = sys.argv[2]
    
    result = test_login(email, password)
    
    if result is True:
        logger.info("\n" + "="*60)
        logger.info("RESULTADO: ✓ LOGIN BEM-SUCEDIDO")
        logger.info("="*60)
        sys.exit(0)
    elif result is False:
        logger.info("\n" + "="*60)
        logger.info("RESULTADO: ✗ LOGIN FALHOU")
        logger.info("="*60)
        sys.exit(1)
    else:
        logger.info("\n" + "="*60)
        logger.info("RESULTADO: ? STATUS DESCONHECIDO - VERIFICAR MANUALMENTE")
        logger.info("="*60)
        sys.exit(2)

if __name__ == "__main__":
    main()
