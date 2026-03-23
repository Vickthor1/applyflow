from credentials import CredentialManager
from openai import OpenAI

creds = CredentialManager()
client = OpenAI(api_key=creds.get('OPENAI_API_KEY'))

try:
    response = client.chat.completions.create(
        model="gpt-3.5-turbo",
        messages=[{"role": "user", "content": "Diga 'Conexão OK' se estiver me ouvindo."}]
    )
    print(response.choices[0].message.content)
except Exception as e:
    print(f"Erro: {e}")