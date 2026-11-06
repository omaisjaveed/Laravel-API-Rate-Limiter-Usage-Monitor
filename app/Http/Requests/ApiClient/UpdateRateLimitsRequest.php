<?php

namespace App\Http\Requests\ApiClient;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRateLimitsRequest extends FormRequest
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
        return [
            'rate_limit_per_minute' => ['required', 'integer', 'min:1', 'max:100000'],
            'rate_limit_per_hour' => ['required', 'integer', 'min:1', 'max:1000000'],
            'rate_limit_per_day' => ['required', 'integer', 'min:1', 'max:10000000'],
        ];
    }
}
