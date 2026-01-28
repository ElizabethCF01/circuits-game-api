<?php

namespace App\Http\Requests;

use App\Rules\ValidLevelGrid;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_x' => 'required|integer|min:0',
            'start_y' => 'required|integer|min:0',
            'required_circuits' => 'required|integer|min:0',
            'max_commands' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'grid_width' => 'required|integer|min:1|max:20',
            'grid_height' => 'required|integer|min:1|max:20',
            'tiles' => ['required', 'json', new ValidLevelGrid()],
            'is_public' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'grid_width.max' => 'Grid dimensions cannot exceed 20x20.',
            'grid_height.max' => 'Grid dimensions cannot exceed 20x20.',
        ];
    }
}
