<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:3', 'max:50', 'unique:players,nickname'],
        ];
    }

    public function messages(): array
    {
        return [
            'nickname.required' => 'Nickname is required',
            'nickname.min' => 'Nickname must be at least 3 characters',
            'nickname.max' => 'Nickname must not exceed 50 characters',
            'nickname.unique' => 'This nickname is already taken',
        ];
    }
}
