<?php

namespace Database\Factories;

use App\Enum\RoomStatusEnum;
use App\Enum\RoomTypeEnum;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomStatus = Arr::random(RoomStatusEnum::cases());
        $roomType = Arr::random(RoomTypeEnum::cases());
        return [
            'number' => fake()->numberBetween(100, 500),
            'type' => $roomType->value,
            'price_per_night' => fake()->randomFloat(2, 50, 500),
            'status' => $roomStatus->value,
        ];
    }
}
