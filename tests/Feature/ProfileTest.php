<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class ProfileTest extends TestCase
{
    /**
     * Teste: Acessar página de perfil
     */
    public function test_can_access_profile_page(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        $response = $this->get('/profile');
        
        $response->assertStatus(200);
    }

    /**
     * Teste: Usuário não autenticado não pode acessar perfil
     */
    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->get('/profile');
        
        $response->assertRedirect('/login');
    }

    /**
     * Teste: Atualizar informações do perfil
     */
    public function test_can_update_profile(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $response = $this->post('/profile', [
            'name' => 'Updated Name',
            'email' => $user->email
        ]);
        
        $updatedUser = User::find($user->id);
        $this->assertEquals('Updated Name', $updatedUser->name);
    }

    /**
     * Teste: Dados do perfil estão corretos na view
     */
    public function test_profile_view_displays_user_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        $this->actingAs($user);
        $response = $this->get('/profile');
        
        $viewUser = $response->viewData('user');
        
        $this->assertEquals($user->name, $viewUser->name);
        $this->assertEquals($user->email, $viewUser->email);
    }
}
