<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountAndContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_update_contact_and_philippine_delivery_details(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('account.update'), [
            'name' => 'Casey Shopper',
            'phone' => '+639171234567',
            'delivery_address' => '12 Sample Street, Barangay Central',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1100',
        ])->assertRedirect(route('account.edit'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Casey Shopper',
            'phone' => '+639171234567',
            'postal_code' => '1100',
        ]);
    }

    public function test_customer_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create(['password' => 'InitialPass123']);

        $this->actingAs($user)->put(route('account.password'), [
            'current_password' => 'InitialPass123',
            'password' => 'UpdatedPass123',
            'password_confirmation' => 'UpdatedPass123',
        ])->assertRedirect(route('account.edit'));

        $this->assertTrue(Hash::check('UpdatedPass123', $user->fresh()->password));
    }

    public function test_contact_form_stores_valid_message_and_rejects_invalid_email(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Jordan Customer',
            'email' => 'jordan@example.test',
            'subject' => 'Question about an order',
            'message' => 'Could you share an update on my order?',
        ])->assertRedirect(route('contact.show'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', ['email' => 'jordan@example.test']);

        $this->from(route('contact.show'))->post(route('contact.store'), [
            'name' => 'Jordan Customer',
            'email' => 'invalid-email',
            'subject' => 'Help',
            'message' => 'Please contact me.',
        ])->assertRedirect(route('contact.show'))
            ->assertSessionHasErrors('email');

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $payload = [
            'name' => 'Jordan Customer',
            'email' => 'jordan@example.test',
            'subject' => 'Question',
            'message' => 'Please contact me.',
        ];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('contact.store'), $payload)->assertRedirect(route('contact.show'));
        }

        $this->post(route('contact.store'), $payload)->assertTooManyRequests();
    }

    public function test_policy_draft_pages_render(): void
    {
        $this->get(route('policies.terms'))->assertOk()->assertSee('Terms and Conditions');
        $this->get(route('policies.privacy'))->assertOk()->assertSee('Privacy Policy');
        $this->get(route('policies.returns'))->assertOk()->assertSee('Returns and Refunds');
    }
}