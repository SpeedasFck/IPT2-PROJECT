<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_with_a_hashed_password(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jamie Customer',
            'email' => 'jamie@example.test',
            'password' => 'CarefulPass123',
            'password_confirmation' => 'CarefulPass123',
        ]);

        $response->assertRedirect(route('account.show'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'jamie@example.test', 'is_admin' => false]);
        $this->assertTrue(Hash::check('CarefulPass123', User::where('email', 'jamie@example.test')->value('password')));
    }

    public function test_customer_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create(['password' => 'CarefulPass123']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'CarefulPass123',
        ])->assertRedirect(route('account.show'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_registration_requires_a_confirmed_strong_password(): void
    {
        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Jamie Customer',
            'email' => 'jamie@example.test',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors(['password']);
    }

    public function test_account_page_requires_authentication(): void
    {
        $this->get(route('account.show'))->assertRedirect(route('login'));
    }
}