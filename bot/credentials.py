import os
from dotenv import load_dotenv

class CredentialManager:
    def __init__(self):
        # Carrega o arquivo .env que está na mesma pasta do bot
        dotenv_path = os.path.join(os.path.dirname(__file__), '.env')
        load_dotenv(dotenv_path)

    def get(self, key):
        return os.getenv(key)