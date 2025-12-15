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
            'end' => 'required|date|after_or_equal:start',
            'category_id' => 'nullable|exists:task_categories,id',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'start.required' => 'Start date is required',
            'end.required' => 'End date is required',
            'end.after_or_equal' => 'End date cannot be before start date',
        ];
    }
}
