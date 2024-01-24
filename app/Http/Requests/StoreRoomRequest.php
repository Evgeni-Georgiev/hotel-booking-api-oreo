<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'number' => 'required|string|unique:room,number,',
            'type' => 'required|in:single,double,flat',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'The room number is required.',
            'number.string' => 'The room number must be a string.',
            'number.unique' => 'The room number is already taken.',
            'type.required' => 'The room type is required.',
            'type.in' => 'Invalid room status. Accepted values are: single, double, flat.',
            'price_per_night.required' => 'The price per night is required.',
            'price_per_night.numeric' => 'The price per night must be a number.',
            'price_per_night.min' => 'The price per night must be at least :min.',
            'status.required' => 'The room status is required.',
            'status.in' => 'Invalid room status. Accepted values are: available, occupied.',
        ];
    }
}
