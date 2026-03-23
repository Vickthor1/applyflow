<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Criar algumas vagas de teste
        $jobs = [
            [
                'user_id' => $user->id,
                'title' => 'Senior PHP Developer',
                'company' => 'Tech Corp',
                'description' => 'Procuramos um desenvolvedor PHP senior com experiência em Laravel.',
                'location' => 'São Paulo, SP',
                'link' => 'https://example.com/job1',
                'match_score' => 95,
                'salary' => 'R$ 10.000 - R$ 15.000',
                'job_type' => 'full-time',
                'source' => 'LinkedIn',
                'applied' => false,
            ],
            [
                'user_id' => $user->id,
                'title' => 'Python Backend Developer',
                'company' => 'StartUp XYZ',
                'description' => 'Desenvolvedor Python para backend de API REST.',
                'location' => 'São Paulo, SP',
                'link' => 'https://example.com/job2',
                'match_score' => 85,
                'salary' => 'R$ 8.000 - R$ 12.000',
                'job_type' => 'full-time',
                'source' => 'Indeed',
                'applied' => false,
            ],
            [
                'user_id' => $user->id,
                'title' => 'Full Stack Developer',
                'company' => 'Digital Agency',
                'description' => 'Full stack com React e Node.js para projeto de longo prazo.',
                'location' => 'Remoto',
                'link' => 'https://example.com/job3',
                'match_score' => 75,
                'salary' => 'R$ 7.000 - R$ 11.000',
                'job_type' => 'part-time',
                'source' => 'Remotive',
                'applied' => true,
                'application_status' => 'applied',
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::create($jobData);
        }

        echo "✓ Usuário de teste criado: test@example.com (senha: password)\n";
        echo "✓ " . count($jobs) . " vagas de teste criadas\n";
    }
}

