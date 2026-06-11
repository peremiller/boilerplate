<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_loads()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
    }

    public function test_reset_link_sent_to_existing_user()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $response = $this->post('/password/email', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_link_not_sent_for_nonexistent_email()
    {
        Notification::fake();

        $response = $this->post('/password/email', [
            'email' => 'nobody@example.com',
        ]);

        $response->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    public function test_email_field_is_required()
    {
        $response = $this->post('/password/email', []);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_must_be_valid_format()
    {
        $response = $this->post('/password/email', [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
