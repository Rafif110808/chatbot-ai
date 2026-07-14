<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAiSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temperature' => 'required|numeric|min:0|max:2',
            'max_tokens' => 'required|integer|min:100|max:8192',
            'system_prompt' => 'nullable|string|max:1000',
            'language' => 'required|string|in:indonesia,english',
            'answer_length' => 'required|string|in:short,medium,detailed',
        ];
    }

    public function messages(): array
    {
        return [
            'temperature.required' => 'Temperature wajib diisi.',
            'temperature.numeric' => 'Temperature harus berupa angka.',
            'temperature.min' => 'Temperature minimal 0.',
            'temperature.max' => 'Temperature maksimal 2.',
            'max_tokens.required' => 'Max tokens wajib diisi.',
            'max_tokens.integer' => 'Max tokens harus berupa angka bulat.',
            'max_tokens.min' => 'Max tokens minimal 100.',
            'max_tokens.max' => 'Max tokens maksimal 8192.',
            'language.in' => 'Bahasa harus indonesia atau english.',
            'answer_length.in' => 'Panjang jawaban harus short, medium, atau detailed.',
        ];
    }
}
