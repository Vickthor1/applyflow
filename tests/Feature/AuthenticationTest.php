<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;

class AuthenticationTest extends TestCase
{
    /**
     * Teste: Acessar página de login sem estar autenticado
     */
    public function test_can_access_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Teste: Login com credenciais válidas
     */
    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Teste: Login com credenciais inválidas
     */
    public function test_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);

        $this->assertGuest();
    }

    /**
     * Teste: Logout
     */
    public function test_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');
        
        $this->assertGuest();
    }

    /**
     * Teste: Acessar dashboard sem estar autenticado redireciona para login
     */
    public function test_redirect_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}

