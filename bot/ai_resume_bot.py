#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import json
import os

# Puxa a chave do arquivo .env
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from credentials import CredentialManager

def analisar_e_modificar(job_description, user_profile):
    try:
        from openai import OpenAI
    except ImportError:
        print(json.dumps({"error": "A biblioteca 'openai' não está instalada no PHP. Abra o terminal do Laragon e rode: pip install openai"}))
        sys.exit(1)

    creds = CredentialManager()
    api_key = creds.get('OPENAI_API_KEY')

    if not api_key:
        print(json.dumps({"error": "A chave OPENAI_API_KEY não foi encontrada no arquivo bot/.env"}))
        sys.exit(1)

    try:
        client = OpenAI(api_key=api_key)
        
        prompt = f"""
        Você é um especialista em reescrever currículos para passar em sistemas ATS.
        
        VAGA DE EMPREGO:
        {job_description}
        
        MEU RESUMO PROFISSIONAL ATUAL:
        {user_profile}
        
        Sua tarefa: Reescreva o meu resumo profissional para que ele dê um match perfeito com esta vaga, destacando as habilidades que eu já tenho e que a vaga pede.
        
        Retorne APENAS um JSON válido com:
        "match_score": (int de 0 a 100),
        "keywords": (lista com 3 palavras-chave inseridas),
        "novo_resumo": (string com o texto do currículo modificado e altamente persuasivo)
        """

        response = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[{"role": "user", "content": prompt}],
            response_format={ "type": "json_object" }
        )
        
        print(response.choices[0].message.content)
        
    except Exception as e:
        print(json.dumps({"error": f"Erro da API OpenAI: {str(e)}"}))
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "O PHP não enviou a vaga e o perfil para o Python."}))
        sys.exit(1)
        
    vaga = sys.argv[1]
    perfil = sys.argv[2]
    
    analisar_e_modificar(vaga, perfil)