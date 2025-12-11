<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'email' => 'required|email',
            'username' => 'required|string',
            'role' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|regex:/^[0-9]{10}$/',
            'dob' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter valid email',
            'username.required' => 'Username is required',
            'role.required' => 'Please select role',
            'phone.regex' => 'Please enter valid phone number'
        ];
    }
}
