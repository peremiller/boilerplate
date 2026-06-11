<?php

namespace Tests\Unit\Models;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes_are_name_email_password()
    {
        $user = new User();

        $this->assertEquals(['name', 'email', 'password'], $user->getFillable());
    }

    public function test_password_and_remember_token_are_hidden()
    {
        $user = new User();

        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }

    public function test_email_verified_at_is_cast_to_datetime()
    {
        $user = new User();

        $casts = $user->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }

    public function test_password_excluded_from_array_serialization()
    {
        $user = factory(User::class)->make();

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_remember_token_excluded_from_array_serialization()
    {
        $user = factory(User::class)->make();

        $array = $user->toArray();

        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_id_is_not_mass_assignable()
    {
        $user = new User(['id' => 999, 'name' => 'John', 'email' => 'john@example.com', 'password' => 'secret']);

        $this->assertNull($user->id);
    }

    public function test_email_verified_at_is_a_carbon_instance()
    {
        $user = factory(User::class)->create(['email_verified_at' => now()]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    public function test_unverified_user_has_null_email_verified_at()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $this->assertNull($user->email_verified_at);
    }
}
