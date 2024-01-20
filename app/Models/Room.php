<?php

namespace App\Models;

use App\Enum\RoomStatusEnum;
use App\Enum\RoomTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    /**
     * Set name of the model in the database table.
     *
     * @var string $table
     */
    protected $table = 'room';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'number',
        'type',
        'price_per_night',
        'status',
    ];

    protected $casts = [
        'type' => RoomTypeEnum::class,
        'status' => RoomStatusEnum::class,
    ];

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
