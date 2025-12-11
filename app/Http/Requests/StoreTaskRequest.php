<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => 'required',
            'start' => 'required|date',
            'end' => 'required|date',
            'isImportant' => 'required|integer'
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'start.required' => 'Start date is required',
            'end.required' => 'End date is required',
        ];
    }
}
