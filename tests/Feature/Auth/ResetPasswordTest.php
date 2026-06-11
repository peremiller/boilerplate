<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function createToken(User $user): string
    {
        return Password::broker()->createToken($user);
    }

    public function test_reset_password_page_loads_with_token()
    {
        $user = factory(User::class)->create();
        $token = $this->createToken($user);

        $response = $this->get("/password/reset/{$token}");

        $response->assertStatus(200);
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = factory(User::class)->create();
        $token = $this->createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/home');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_reset_fails_with_invalid_token()
    {
        $user = factory(User::class)->create();

        $response = $this->post('/password/reset', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_reset_requires_password_confirmation()
    {
        $user = factory(User::class)->create();
        $token = $this->createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_reset_requires_minimum_password_length()
    {
        $user = factory(User::class)->create();
        $token = $this->createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_is_not_flashed_to_session_on_reset_failure()
    {
        $user = factory(User::class)->create();

        $this->post('/password/reset', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $this->assertFalse(session()->has('password'));
    }
}
