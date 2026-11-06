<?php

namespace App\Policies;

use App\Models\ApiClient;
use App\Models\User;

class ApiClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ApiClient $apiClient): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ApiClient $apiClient): bool
    {
        return true;
    }

    public function delete(User $user, ApiClient $apiClient): bool
    {
        return true;
    }

    public function toggle(User $user, ApiClient $apiClient): bool
    {
        return true;
    }
}
