<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\DonorHealthProfile;
use App\Models\User;

class DonorHealthProfilePolicy
{
    public function view(User $user, DonorHealthProfile $profile): bool
    {
        if ($user->role === UserRole::DONOR && $user->donor) {
            return $profile->donor_id === $user->donor->id;
        }

        return false;
    }

    public function update(User $user, DonorHealthProfile $profile): bool
    {
        if ($user->role === UserRole::DONOR && $user->donor) {
            return $profile->donor_id === $user->donor->id;
        }

        return false;
    }
}


