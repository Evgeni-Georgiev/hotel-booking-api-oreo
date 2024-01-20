<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $getRandomDay = fake()->numberBetween(1, 5);
        $checkInDate = Carbon::now()->addDays($getRandomDay);
        $checkOutDate = $checkInDate->copy()->addDays($getRandomDay);

        $roomIds = Room::pluck('id')->toArray();
        $room = Room::find(fake()->randomElement($roomIds));
        $customerIds = Customer::pluck('id')->toArray();
        $customer = Customer::find(fake()->randomElement($customerIds));

        return [
            'room_id' => $room->id,
            'customer_id' => $customer->id,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'total_price' => fake()->numberBetween(1500, 6000)
        ];
    }
}
