<?php

namespace App\Http\Requests\ApiClient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApiClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $clientId = $this->route('client')?->id ?? $this->route('client');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('api_clients', 'name')->ignore($clientId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
            'rate_limit_per_minute' => ['required', 'integer', 'min:1', 'max:100000'],
            'rate_limit_per_hour' => ['required', 'integer', 'min:1', 'max:1000000'],
            'rate_limit_per_day' => ['required', 'integer', 'min:1', 'max:10000000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active'),
            ]);
        }
    }
}
