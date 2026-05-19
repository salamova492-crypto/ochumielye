<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Вход');
    }

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Регистрация');
    }

    public function test_visitor_can_register(): void
    {
        $response = $this->post(route('register.post'), [
            'fullName' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+71234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_leader_can_login_and_redirects_to_cabinet(): void
    {
        $password = 'password123';
        $leader = User::factory()->leader()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post(route('login.post'), [
            'email' => $leader->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('cabinet.index'));
        $this->assertAuthenticatedAs($leader);
    }

    public function test_visitor_login_redirects_to_home(): void
    {
        $password = 'password123';
        $visitor = User::factory()->visitor()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post(route('login.post'), [
            'email' => $visitor->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($visitor);
    }

    public function test_login_with_invalid_credentials_shows_error(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_works(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_register_requires_valid_data(): void
    {
        $response = $this->post(route('register.post'), [
            'fullName' => '',
            'email' => 'not-an-email',
            'phone' => '',
            'password' => 'short',
            'password_confirmation' => 'not-matching',
        ]);

        $response->assertSessionHasErrors(['fullName', 'email', 'phone', 'password']);
    }
}
