<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoardRequest extends FormRequest
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
            'employee_id' => 'required|exists:employees,id',
            'category_id' => 'required|exists:task_categories,id',
            'name' => 'required'
        ];
    }
    public function messags()
    {
        return [
            'name.required' => 'Board Title is required',
            'employee_id.exists' => 'Invalid user',
            'category_id' => 'Please choose task Category'
        ];
    }
}
