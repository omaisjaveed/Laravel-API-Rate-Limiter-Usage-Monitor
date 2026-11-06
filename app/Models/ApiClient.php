<?php

namespace App\Models;

use Database\Factories\ApiClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiClient extends Model
{
    /** @use HasFactory<ApiClientFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'rate_limit_per_minute',
        'rate_limit_per_hour',
        'rate_limit_per_day',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'rate_limit_per_minute' => 'integer',
            'rate_limit_per_hour' => 'integer',
            'rate_limit_per_day' => 'integer',
        ];
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(ApiRequestLog::class);
    }

    public function activeApiKeys(): HasMany
    {
        return $this->apiKeys()->whereNull('revoked_at');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
