<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;

class DashboardTest extends TestCase
{
    /**
     * Teste: Dashboard exibe vagas do usuário
     */
    public function test_dashboard_displays_user_jobs(): void
    {
        $user = User::factory()->create();
        
        Job::factory()->count(3)->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $jobs = $response->viewData('jobs');
        
        $this->assertCount(3, $jobs);
    }

    /**
     * Teste: Analytics carrega estatísticas
     */
    public function test_dashboard_loads_statistics(): void
    {
        $user = User::factory()->create();
        Job::factory()->count(3)->create(['user_id' => $user->id]);
        
        $this->actingAs($user);
        $response = $this->get('/dashboard');
        
        $stats = $response->viewData('stats');
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_jobs', $stats);
        $this->assertArrayHasKey('avg_match_score', $stats);
    }

    /**
     * Teste: Vagas mostram informações corretas
     */
    public function test_job_card_displays_correct_information(): void
    {
        $user = User::factory()->create();
        
        Job::factory()->create([
            'user_id' => $user->id,
            'title' => 'Senior PHP Developer',
            'company' => 'Tech Corp',
            'location' => 'São Paulo, SP',
            'source' => 'LinkedIn'
        ]);
        
        $this->actingAs($user);
        $response = $this->get('/dashboard');
        
        $jobs = $response->viewData('jobs');
        $firstJob = $jobs->first();
        
        $this->assertNotNull($firstJob->title);
        $this->assertNotNull($firstJob->company);
        $this->assertNotNull($firstJob->location);
        $this->assertNotNull($firstJob->source);
    }
}
