<?php

namespace App\Http\Requests;

use App\Rules\UniquePaymentForBooking;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:booking,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => [
                'required',
                'date',
                new UniquePaymentForBooking($this->input('booking_id'), $this->input('payment_date')),
            ],
            'status' => 'required|in:complete,pending,failed,down_payment',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'The booking ID is required.',
            'booking_id.exists' => 'The selected booking does not exist.',
            'amount.required' => 'The payment amount is required.',
            'amount.numeric' => 'The payment amount must be a number.',
            'amount.min' => 'The payment amount must be at least :min.',
            'payment_date.required' => 'The payment date is required.',
            'payment_date.date' => 'Invalid date format for the payment date.',
            'status.required' => 'The payment status is required.',
            'status.in' => 'Invalid payment status. Accepted values are: complete, pending, failed, down_payment.',
        ];
    }
}
