<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'email' => 'required|email|unique:employees,email',
            'password' => 'required',
            'username' => 'required|string',
            'role' => 'required|string',
            'address' => 'nullable',
            'phone' => 'nullable|regex:/^[0-9]{10}$/',
            'dob' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'email.emaail' => 'Please enter valid email',
            'password' => 'Password is required',
            'username' => 'Please enter your username',
            'role' => 'Please select role',
            'phone.regex' => 'Enter valid phone number'
        ];
    }
}
