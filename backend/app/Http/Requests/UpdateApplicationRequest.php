<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ignore the current record's own ID when checking name uniqueness on update
        $applicationId = $this->route('application')?->id;

        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255', "unique:applications,name,{$applicationId}"],
            'url'         => ['sometimes', 'required', 'url', 'max:2048'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'An application with this name already exists.',
            'url.url'     => 'The URL must be a valid URL (e.g. https://app.internal).',
        ];
    }
}
