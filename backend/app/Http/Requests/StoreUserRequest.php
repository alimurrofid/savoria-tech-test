<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:8'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'role_id'       => ['nullable', 'exists:roles,id'],
            'is_admin'      => ['boolean'],
        ];
    }
}
