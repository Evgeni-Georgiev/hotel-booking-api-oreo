<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => 'required|string|min:3',
            'email' => 'required|string|unique:user,email',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'password_confirmation.required' => 'The password confirmation is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.confirmed_without' => 'Please confirm your password.',
            'email.required' => 'Email field is required.',
            'email.unique' => 'Email is already taken.',
            'email.email' => 'Email must be a valid email address.'
        ];
    }
}
