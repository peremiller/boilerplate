<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function verificationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/email/verify');

        $response->assertRedirect('/login');
    }

    public function test_verification_notice_shown_to_unverified_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
    }

    public function test_verified_user_redirected_from_verification_notice()
    {
        $user = factory(User::class)->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertRedirect('/home');
    }

    public function test_user_can_verify_email_with_valid_signed_url()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);
        $url = $this->verificationUrl($user);

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect('/home');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verification_rejects_tampered_hash()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'tampered-hash']
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_already_verified_user_is_redirected_on_verify()
    {
        $user = factory(User::class)->create(['email_verified_at' => now()]);
        $url = $this->verificationUrl($user);

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect('/home');
    }

    public function test_user_can_resend_verification_email()
    {
        Notification::fake();

        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->post('/email/resend');

        $response->assertRedirect();
        $this->assertTrue(session()->has('resent'));
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
