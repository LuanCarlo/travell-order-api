<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class OrderTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function it_creates_an_order()
    {
        $user = User::factory()->create([
            'name' => 'Luan Carlo',
            'email' => 'luan@example.com',
        ]);

        $order = Order::factory()->create([
            'destination' => 'Rio de Janeiro',
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('order', [
            'destination' => 'Rio de Janeiro',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_fails_when_destination_is_missing()
    {
        $data = [
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
            'user_id' => 1,
        ];

        $validator = Validator::make($data, [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'user_id' => 'required|integer',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('destination', $validator->errors()->toArray());
    }

    /** @test */
    public function it_fails_when_return_date_is_before_departure_date()
    {
        $data = [
            'destination' => 'São Paulo',
            'departure_date' => '2025-12-10',
            'return_date' => '2025-12-01',
            'user_id' => 1,
        ];

        $validator = Validator::make($data, [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'user_id' => 'required|integer',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('return_date', $validator->errors()->toArray());
    }

    /** @test */
    public function it_fails_when_user_id_is_missing()
    {
        $data = [
            'destination' => 'Recife',
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
        ];

        $validator = Validator::make($data, [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'user_id' => 'required|integer',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
    }

    /** @test */
    public function it_fails_when_destination_exceeds_255_characters()
    {
        $data = [
            'destination' => str_repeat('A', 256),
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
            'user_id' => 1,
        ];

        $validator = Validator::make($data, [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'user_id' => 'required|integer',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('destination', $validator->errors()->toArray());
    }

    /** @test */
    public function it_passes_with_valid_data()
    {
        $user = User::factory()->create();

        $data = [
            'destination' => 'Salvador',
            'departure_date' => '2025-12-01',
            'return_date' => '2025-12-10',
            'user_id' => $user->id,
        ];

        $validator = Validator::make($data, [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
            'user_id' => 'required|integer',
        ]);

        $this->assertFalse($validator->fails());

        $order = Order::create($data);

        $this->assertDatabaseHas('order', [
            'destination' => 'Salvador',
            'user_id' => $user->id,
        ]);
    }
}
