<?php

namespace Database\Factories;

use App\Enum\PaymentStatusEnum;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentStatus = Arr::random(PaymentStatusEnum::cases());
        $booking = Booking::factory()->create();

        // Generate payment_date that is before or equal to the check_in_date
        $paymentDate = fake()->dateTimeBetween('-1 week', $booking->check_in_date);
        return [
            'booking_id' => $booking,
            'amount' => fake()->randomFloat(2, 150, 300),
            'payment_date' => $paymentDate,
            'status' => $paymentStatus,
        ];
    }
}
