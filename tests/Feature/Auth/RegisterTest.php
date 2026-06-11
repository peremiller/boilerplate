<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_loads()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_cannot_access_registration_page()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/home');
    }

    public function test_user_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_password_is_hashed_on_registration()
    {
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_requires_name()
    {
        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
    }

    public function test_registration_requires_email()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_valid_email_format()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_rejects_duplicate_email()
    {
        factory(User::class)->create(['email' => 'taken@example.com']);

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertCount(1, User::where('email', 'taken@example.com')->get());
    }

    public function test_registration_requires_password()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_rejects_password_shorter_than_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_requires_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_is_not_flashed_to_session_on_validation_failure()
    {
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertFalse(session()->has('password'));
    }
}
