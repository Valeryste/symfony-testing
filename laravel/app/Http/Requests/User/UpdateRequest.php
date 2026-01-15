<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['sometimes', 'string', 'unique:users', 'max:255', 'regex:/^[a-zA-Z0-9]+([_.-]?[a-zA-Z0-9])*$/'],
            'email' => ['sometimes', 'email', 'unique:users', 'max:255'],
            'is_active' => ['sometimes', 'bool'],
            'role_id' => ['sometimes', 'int']
        ];
    }

}
