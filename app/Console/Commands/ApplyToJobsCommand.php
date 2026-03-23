<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Job;

class ApplyToJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:apply {email} {password} {--keyword=php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark jobs as applied based on keyword (credentials from .env)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keyword = $this->option('keyword');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $useConfig = $email === 'config' || $password === 'config';

        // Usar credenciais do arquivo .env do bot se indicado
        $botEnvPath = base_path('bot/.env');
        
        if ($useConfig) {
            if (!file_exists($botEnvPath)) {
                $this->error('✗ Arquivo bot/.env não encontrado');
                $this->line('Execute: cp bot/.env.example bot/.env');
                $this->line('Depois edite bot/.env com suas credenciais do LinkedIn');
                return self::FAILURE;
            }

            // Ler credenciais do .env
            $botEmail = $this->getEnvValue($botEnvPath, 'LINKEDIN_EMAIL');
            $botPassword = $this->getEnvValue($botEnvPath, 'LINKEDIN_PASSWORD');
            $botDebug = $this->getEnvValue($botEnvPath, 'BOT_DEBUG', 'false');
        } else {
            // Usar credenciais passadas
            $botEmail = $email;
            $botPassword = $password;
            $botDebug = 'false';
        }
        
        // Validar credenciais
        if (!$botEmail || $botEmail === 'seu@email.com') {
            $this->error('✗ Email do LinkedIn não configurado');
            return self::FAILURE;
        }
        
        if (!$botPassword || $botPassword === 'sua_senha_aqui') {
            $this->error('✗ Senha do LinkedIn não configurada');
            return self::FAILURE;
        }
        
        $this->info("Processando aplicações para keyword: {$keyword}");

        // Buscar vagas que combinam com a keyword
        $jobs = Job::where('title', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->where('applied', '!=', 1)
            ->limit(10)
            ->get();

        if ($jobs->isEmpty()) {
            $this->warn("Nenhuma vaga encontrada para keyword: {$keyword}");
            return self::SUCCESS;
        }

        $appliedCount = 0;
        foreach ($jobs as $job) {
            $job->applied = 1;
            $job->application_status = 'applied';
            $job->save();
            $appliedCount++;
        }

        $this->info("✓ Marcadas {$appliedCount} vaga(s) com ação 'applied'");
        $this->info("Keyword: {$keyword}");
        $this->line("");
        $this->info("📝 Nota: Execute o bot Python para aplicar de verdade no LinkedIn:");
        $this->line("  python bot/bot.py");
        $this->line("");
        
        return self::SUCCESS;
    }
    
    /**
     * Obter valor de uma variável no arquivo .env
     */
    private function getEnvValue($filePath, $key, $default = null)
    {
        if (!file_exists($filePath)) {
            return $default;
        }
        
        $lines = file($filePath);
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Ignorar comentários e linhas vazias
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            
            // Parsear KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($envKey, $envValue) = explode('=', $line, 2);
                if (trim($envKey) === $key) {
                    return trim($envValue);
                }
            }
        }
        
        return $default;
    }
}
