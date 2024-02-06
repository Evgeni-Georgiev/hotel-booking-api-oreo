<?php

namespace Tests\Feature;

use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Close the mock
        Mockery::close();
    }

    public function testIndexRoomViaGetRequestReturnJsonResponseWhenValidData(): void
    {
        // Given
        $rooms = Room::factory()->count(2)->create();
        $roomsCollection = RoomResource::collection($rooms);

        // When
        $response = $this->getJson(route('room.index'));

        // Then
        $response->assertJson([
            'message' => 'rooms',
            'rooms' => $roomsCollection->response()->getData(true),
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testIndexRoomViaGetRequestReturnJsonResponseWhenValidDataWithNoData(): void
    {
        // When
        $response = $this->getJson(route('room.index'));

        // Then
        $response->assertJson([
            'message' => 'rooms',
            'rooms' => [],
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testShowRoomViaGetRequestThrowsExceptionWhenRoomNotFound(): void
    {
        // When
        $response = $this->getJson(route('room.show', ['room' => Room::class]));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Room not found!',
            ]);
    }
    public function testShowRoomViaGetRequestReturnJsonResponseWhenValidData(): void
    {
        // Given
        $room = Room::factory()->create();

        // When
        $response = $this->getJson(route('room.show', ['room' => $room]));

        // Then
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Room found!',
                'data' => [
                    'number' => $room->number,
                    'type' => $room->type->value,
                    'price_per_night' => $room->price_per_night,
                    'status' => $room->status->value
                ],
            ]);
    }

    public function testCreateRoomViaPostRequestValidationErrorWhenInvalidData(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());

        // When
        $response = $this->postJson(route('room.store'), []);

        // Then
        $response->assertJsonValidationErrors([
            'number', 'type', 'price_per_night', 'status'
        ]);
    }

    public function testCreateRoomViaPostRequestValidationErrorWhenNumberAsString(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());

        // When
        $response = $this->postJson(route('room.store'), ['number' => 123]);

        // Then
        $response->assertJsonValidationErrors(['number']);
    }

    public function testCreateRoomViaPostRequestValidationErrorWhenNumberAlreadyTaken(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());
        $room = Room::factory()->create(['number' => '123']);

        // When
        $response = $this->postJson(route('room.store'), ['number' => $room->number]);

        // Then
        $response->assertJsonValidationErrors(['number']);
    }

    public function testCreateRoomViaPostRequestValidationErrorWhenInvalidRoomType(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());

        // When
        $response = $this->postJson(route('room.store'), ['type' => 'invalid_type']);

        // Then
        $response->assertJsonValidationErrors(['type']);
    }

    public function testCreateRoomViaPostRequestValidationErrorWhenInvalidRoomPricePerNight(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());

        // When
        $response = $this->postJson(route('room.store'), ['price_per_night' => 'invalid_price']);

        // Then
        $response->assertJsonValidationErrors(['price_per_night']);
    }

    public function testCreateRoomViaPostRequestValidationErrorWhenInvalidRoomPricePerNightMinimumValue(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());

        // When
        $response = $this->postJson(route('room.store'), ['price_per_night' => -5]);

        // Then
        $response->assertJsonValidationErrors(['price_per_night']);
    }

    public function testCreateRoomViaPostRequestValidationErrorWhenInvalidRoomStatus(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());

        // When
        $response = $this->postJson(route('room.store'), ['status' => 'invalid_status']);

        // Then
        $response->assertJsonValidationErrors(['status']);
    }

    public function testCreateRoomViaPostRequestReturnJsonResponseWhenValidData(): void
    {
        // Given
        Sanctum::actingAs(User::factory()->create());
        $roomData = [
            'number' => '101',
            'type' => 'single',
            'price_per_night' => 100.00,
            'status' => 'available',
        ];

        // When
        $response = $this->postJson(route('room.store'), $roomData);

        //Then
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Room created successfully!',
                'data' => $roomData,
            ]);
        $this->assertDatabaseHas('room', $roomData);
    }
}
