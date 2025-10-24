<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\Validator;


class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_user()
    {
        $user = User::factory()->create([
            'name' => 'Luan Carlo',
            'email' => 'luan@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'luan@example.com',
        ]);
    }

    /** @test */
    public function it_updates_a_user()
    {
        $user = User::factory()->create();

        $user->update(['name' => 'Atualizado']);

        $this->assertEquals('Atualizado', $user->fresh()->name);
    }

    /** @test */
    public function it_fails_when_name_exceeds_255_characters()
    {
        $data = [
            'name' => str_repeat('A', 256),
            'email' => 'user@example.com',
            'password' => 'password',
        ];

        // Simula a validação do controller
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Deve falhar
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function it_passes_when_name_is_255_characters()
    {
        $data = [
            'name' => str_repeat('A', 255),
            'email' => 'user255@example.com',
            'password' => 'password',
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Deve passar
        $this->assertFalse($validator->fails());

        // Cria o usuário no banco
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'user255@example.com',
        ]);
    }

    /** @test */
    public function it_fails_to_update_user_with_invalid_email()
    {
        $user = User::factory()->create();

        $validator = Validator::make(
            ['email' => 'invalid-email'],
            ['email' => 'required|email']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test */
    public function it_fails_when_email_is_not_unique()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $data = [
            'name' => 'Another User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
        
}
