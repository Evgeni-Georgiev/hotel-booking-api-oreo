<?php

namespace Tests\Feature;

use App\Events\BookingCanceledEvent;
use App\Events\BookingMadeEvent;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->customerData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '931-858-4071'
        ];
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Close the mock
        Mockery::close();
    }

    public function getUserToken(): string
    {
        $user = User::factory()->create();
        return $user->createToken('appToken')->plainTextToken;
    }

    public function getBookingData(int $bookingCount = 1) {
        $room = Room::factory()->create();
        $customer = Customer::factory()->create();
        return Booking::factory()->count($bookingCount)->create([
            'room_id' => $room->id,
            'customer_id' => $customer->id,
            'check_in_date' => now()->addDay()->format('Y-m-d'),
            'check_out_date' => now()->addDays(2)->format('Y-m-d'),
        ]);
    }

    public function testCreateBookingViaPostRequestReturnsJsonResponseWhenValidaData(): void
    {
        $room = Room::factory()->create();
        $customer = Customer::factory()->create();

        $validData = [
            'room_id' => $room->id,
            'customer_id' => $customer->id,
            'check_in_date' => now()->format('Y-m-d'),
            'check_out_date' => now()->addDays(2)->format('Y-m-d'),
        ];
        Event::fake();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('booking.store'), $validData);

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(['message' => 'Booking created successfully!'])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'room_id',
                    'customer_id',
                    'check_in_date',
                    'check_out_date',
                ],
            ]);

        Event::assertDispatched(BookingMadeEvent::class, function ($event) use ($response) {
            return $event->booking->room_id === $response->json('data.room_id');
        });
    }

    public function testShowBookingViaPostRequestReturnsJsonResponseWhenValidaData(): void
    {
        // Given
        Event::fake();

        // When
        $response = $this->getJson(route('booking.show', ['booking' => $this->getBookingData()[0]]));

        // Then
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'room_id',
                    'customer_id',
                    'check_in_date',
                    'check_out_date',
                    'total_price',
                ],
            ]);

        Event::assertNotDispatched(BookingCanceledEvent::class);
    }

    public function testSoftDeleteBookingViaPostRequestReturnsJsonResponseWhenValidaData(): void
    {
        // Given
        $room = Room::factory()->create();
        $customer = Customer::factory()->create();
        $booking = Booking::factory()->create([
            'room_id' => $room->id,
            'customer_id' => $customer->id,
            'check_in_date' => now()->addDay()->format('Y-m-d'),
            'check_out_date' => now()->addDays(2)->format('Y-m-d'),
        ]);
        Event::fake();

        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->deleteJson(route('booking.destroy', ['booking' => $booking]));

        // Then
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => 'Booking canceled successfully.']);

        Event::assertDispatched(BookingCanceledEvent::class, function ($event) use ($booking, $room) {
            return $event->booking->id === $booking->id && $event->room->id === $room->id;
        });
    }

    public function testIndexBookingViaPostRequestReturnsJsonResponseWhenValidaData(): void
    {
        // Given
        $this->getBookingData(5);

        // When
        $response = $this->getJson(route('booking.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    [
                        'room_id',
                        'customer_id',
                        'check_in_date',
                        'check_out_date',
                        'total_price',
                    ],
                ],
            ])
            ->assertJsonCount(5, 'data');
    }

    public function testSoftDeleteBookingThrowsExceptionForNonExistingBooking(): void
    {
        // Given
        $nonExistingBooking = 10;
        $this->getBookingData();

        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->deleteJson(route('booking.destroy', ['booking' => Booking::class]));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
            'message' => 'Booking not found!'
        ]);
    }

    public function testBookingWithUnavailableDateRangeException(): void
    {
        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('booking.store'), $this->getBookingData()[0]->toArray());

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['message' => 'Room is not available for the specified dates range.']);
    }

    public function testBookingCreationViaPostRequestWhenValidationFailsWithInvalidData(): void
    {
        // Given
        $invalidData = [
            'room_id' => 'invalid data',
            'customer_id' => 'invalid data',
            'check_in_date' => 'invalid data',
            'check_out_date' => 'invalid data',
        ];

        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('booking.store'), $invalidData);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'room_id', 'customer_id', 'check_in_date', 'check_out_date'
        ]);
    }

    public function testBookingCreationViaPostRequestWhenValidationFailsWithMissingRequiredData(): void
    {
        // Given
        $invalidData = [];

        // When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('booking.store'), $invalidData);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'customer_id', 'check_in_date', 'check_out_date'
        ]);
    }

    public function testBookingCreationViaPostRequestWhenValidationFailsWithNonExistingRoomData(): void
    {
        // Given
        $invalidData = [
            'room_id' => 15,
            'customer_id' => 1,
            'check_in_date' => now()->addDay()->format('Y-m-d'),
            'check_out_date' => now()->addDays(2)->format('Y-m-d'),
        ];

        //When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('booking.store'), $invalidData);

        //Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['room_id']);
    }

    public function testBookingCreationViaPostRequestWhenValidationFailsWithNonExistingCustomerData(): void
    {
        // Given
        $invalidData = [
            'room_id' => 1,
            'customer_id' => 77,
            'check_in_date' => now()->addDay()->format('Y-m-d'),
            'check_out_date' => now()->addDays(2)->format('Y-m-d'),
        ];

        //When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('booking.store'), $invalidData);

        //Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['customer_id']);
    }

    public function testBookingCreationViaPostRequestWhenValidationFailsWithCheckOutDateNoAfterCheckInDate(): void
    {
        // Given
        $invalidData = [
            'room_id' => 1,
            'customer_id' => 77,
            'check_in_date' => now()->addDays(2)->format('Y-m-d'),
            'check_out_date' => now()->addDay()->format('Y-m-d'),
        ];

        //When
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getUserToken())
            ->postJson(route('booking.store'), $invalidData);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['check_out_date']);
    }

}
