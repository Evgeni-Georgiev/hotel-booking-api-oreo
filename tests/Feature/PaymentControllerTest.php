<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
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

    public function getPaymentData() {
        return Payment::factory()->create([
            'booking_id' => $this->getBookingData()[0]->id,
            'amount' => $this->getBookingData()[0]->total_price,
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'complete',
        ]);
    }

    public function testCreatePaymentViaPostRequestReturnsJsonResponseWhenValidaData(): void
    {
        // Given
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $paymentData = [
            'booking_id' => $this->getBookingData()[0]->id,
            'amount' => $this->getBookingData()[0]->total_price,
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'complete'
        ];

        // When
        $response = $this->postJson(route('payment.store'), $paymentData);

        // Then
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Payment proceeded!',
                'data' => $paymentData,
            ]);

        $this->assertDatabaseHas('payment', $paymentData);
    }

    public function testShowPaymentViaGetRequestThrowsExceptionWhenPaymentNotFound(): void
    {
        // Given
        $nonExistentPaymentId = 15;
        $this->getBookingData();

        // When
        $response = $this->getJson(route('payment.show', $nonExistentPaymentId));

        // Then
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Payment not found!',
            ]);
    }

    public function testIndexPaymentViaGetRequestReturnJsonResponseWhenValidData(): void
    {
        // Given
        $this->getPaymentData();

        // When
        $response = $this->getJson(route('payment.index'));

        // Then
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['booking_id', 'amount', 'payment_date', 'status'],
                ],
            ]);
    }
}
