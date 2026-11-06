<?php

namespace App\Models;

use Database\Factories\ApiRequestLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    /** @use HasFactory<ApiRequestLogFactory> */
    use HasFactory;

    protected $fillable = [
        'api_client_id',
        'api_key_id',
        'endpoint',
        'method',
        'status_code',
        'response_time_ms',
        'rate_limit_limit',
        'rate_limit_remaining',
        'was_blocked',
        'ip_address',
        'requested_at',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'response_time_ms' => 'integer',
            'rate_limit_limit' => 'integer',
            'rate_limit_remaining' => 'integer',
            'was_blocked' => 'boolean',
            'requested_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
