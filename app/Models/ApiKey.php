<?php

namespace App\Models;

use Database\Factories\ApiKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    /** @use HasFactory<ApiKeyFactory> */
    use HasFactory;

    protected $fillable = [
        'api_client_id',
        'name',
        'key_prefix',
        'key_hash',
        'last_used_at',
        'revoked_at',
    ];

    protected $hidden = [
        'key_hash',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(ApiRequestLog::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isUsable(): bool
    {
        return ! $this->isRevoked() && $this->client?->isActive();
    }

    /**
     * @return array{plain_text: string, model: self}
     */
    public static function generateFor(ApiClient $client, ?string $name = null): array
    {
        $plainText = 'rl_'.Str::random(48);
        $prefix = substr($plainText, 0, 12);

        $model = static::query()->create([
            'api_client_id' => $client->id,
            'name' => $name,
            'key_prefix' => $prefix,
            'key_hash' => hash('sha256', $plainText),
        ]);

        return [
            'plain_text' => $plainText,
            'model' => $model,
        ];
    }

    public static function findByPlainText(string $plainText): ?self
    {
        return static::query()
            ->where('key_hash', hash('sha256', $plainText))
            ->first();
    }

    public function revoke(): void
    {
        if ($this->revoked_at === null) {
            $this->forceFill(['revoked_at' => now()])->save();
        }
    }

    /**
     * Replace the secret for this key record and return the new plain-text value once.
     *
     * @return array{plain_text: string, model: self}
     */
    public function regenerate(?string $name = null): array
    {
        $plainText = 'rl_'.Str::random(48);

        $this->forceFill([
            'name' => $name ?? $this->name,
            'key_prefix' => substr($plainText, 0, 12),
            'key_hash' => hash('sha256', $plainText),
            'revoked_at' => null,
            'last_used_at' => null,
        ])->save();

        return [
            'plain_text' => $plainText,
            'model' => $this->fresh(),
        ];
    }
}
