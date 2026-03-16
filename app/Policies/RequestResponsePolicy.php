<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\RequestResponse;
use App\Models\User;

class RequestResponsePolicy
{
    public function view(User $user, RequestResponse $response): bool
    {
        if ($user->role === UserRole::DONOR && $user->donor) {
            return $response->donor_id === $user->donor->id;
        }

        if ($user->role === UserRole::ORGANIZATION && $user->organization) {
            return $response->bloodRequest?->organization_id === $user->organization->id;
        }

        return false;
    }

    public function update(User $user, RequestResponse $response): bool
    {
        if ($user->role === UserRole::ORGANIZATION && $user->organization) {
            return $response->bloodRequest?->organization_id === $user->organization->id;
        }

        return false;
    }
}


