<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\BloodRequest;
use App\Models\User;

class BloodRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function view(User $user, BloodRequest $bloodRequest): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::ORGANIZATION) {
            return $bloodRequest->organization?->user_id === $user->id;
        }

        if ($user->role === UserRole::DONOR && $user->donor) {
            return $bloodRequest->responses()
                ->where('donor_id', $user->donor->id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, BloodRequest $bloodRequest): bool
    {
        return false;
    }

    public function delete(User $user, BloodRequest $bloodRequest): bool
    {
        return false;
    }
}
