<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'username' => 'required|string',
            'address' => 'nullable',
            'phone' => 'nullable|regex:/^[0-9]{10}$/',
            'dob' => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg',
            'password' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username is required',
            'phone.regex' => 'Please enter valid phone number',
            'profile_image.mimes' => 'Image format should be jpeg,png or jpg only',
        ];
    }
}
