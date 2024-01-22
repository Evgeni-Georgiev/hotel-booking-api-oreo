<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Payment;

class UniquePaymentForBooking implements Rule
{
    private int $bookingId;
    private string $paymentDate;

    public function __construct($bookingId, $paymentDate)
    {
        $this->bookingId = $bookingId;
        $this->paymentDate = $paymentDate;
    }

    /**
     * Checks if there is already a payment made for the same booking on the same date.
     */
    public function passes($attribute, $value): bool
    {
        return !Payment::where('booking_id', $this->bookingId)
            ->where('payment_date', $this->paymentDate)
            ->exists();
    }

    public function message(): string
    {
        return 'A payment for this booking on the same date already exists.';
    }
}
