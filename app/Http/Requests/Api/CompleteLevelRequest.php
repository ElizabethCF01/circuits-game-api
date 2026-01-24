<?php

namespace App\Http\Requests\Api;

use App\Enums\Command;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commands' => ['required', 'array', 'min:1'],
            'commands.*' => ['required', 'string', Rule::in(Command::values())],
        ];
    }

    public function messages(): array
    {
        $validCommands = implode(', ', Command::values());

        return [
            'commands.required' => 'Commands are required',
            'commands.array' => 'Commands must be an array',
            'commands.min' => 'At least one command is required',
            'commands.*.in' => "Invalid command. Valid commands: {$validCommands}",
        ];
    }
}
