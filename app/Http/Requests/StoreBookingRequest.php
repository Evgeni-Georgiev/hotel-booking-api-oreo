<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'room_id' => 'nullable|exists:room,id',
            'customer_id' => 'required|exists:customer,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ];
    }

    // Optionally, you can customize error messages by overriding the messages() method:
    public function messages(): array
    {
        return [
            'room_id.exists' => 'The selected room does not exist.',
            'customer_id.required' => 'The customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'check_in_date.required' => 'The check-in date is required.',
            'check_in_date.date' => 'Invalid date format for the check-in date.',
            'check_out_date.required' => 'The check-out date is required.',
            'check_out_date.date' => 'Invalid date format for the check-out date.',
            'check_out_date.after' => 'The check-out date must be after the check-in date.',
        ];
    }
}
