<?php

namespace App\Policies;

use App\Models\ApiKey;
use App\Models\User;

class ApiKeyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function revoke(User $user, ApiKey $apiKey): bool
    {
        return true;
    }

    public function regenerate(User $user, ApiKey $apiKey): bool
    {
        return true;
    }
}
