<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePmsTasKRequest extends FormRequest
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
            'card_id' => 'required|exists:pms_cards,id'
        ];
    }
    public function messags()
    {
        return [
            'title.required' => 'Task Title is required',
            'card_id.exists' => 'Invalid card',
        ];
    }
}
