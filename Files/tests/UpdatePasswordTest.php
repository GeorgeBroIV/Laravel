<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->put('/user/password', [
            'current_password' => 'Passw0rd!!',
            'password' => 'new-Passw0rd!!',
            'password_confirmation' => 'new-Passw0rd!!',
        ]);

        $this->assertTrue(Hash::check('new-Passw0rd!!', $user->fresh()->password));
    }

    public function test_current_password_must_be_correct()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->put('/user/password', [
            'current_password' => 'wrong-Passw0rd!!',
            'password' => 'new-Passw0rd!!',
            'password_confirmation' => 'new-Passw0rd!!',
        ]);

        $response->assertSessionHasErrors();

        $this->assertTrue(Hash::check('Passw0rd!!', $user->fresh()->password));
    }

    public function test_new_passwords_must_match()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->put('/user/password', [
            'current_password' => 'Passw0rd!!',
            'password' => 'new-Passw0rd!!',
            'password_confirmation' => 'wrong-Passw0rd!!',
        ]);

        $response->assertSessionHasErrors();

        $this->assertTrue(Hash::check('Passw0rd!!', $user->fresh()->password));
    }
}
