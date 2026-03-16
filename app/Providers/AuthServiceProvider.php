<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\BloodRequest;
use App\Models\DonorHealthProfile;
use App\Models\Organization;
use App\Models\RequestResponse;
use App\Models\User;
use App\Policies\BloodRequestPolicy;
use App\Policies\DonorHealthProfilePolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\RequestResponsePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        BloodRequest::class => BloodRequestPolicy::class,
        RequestResponse::class => RequestResponsePolicy::class,
        DonorHealthProfile::class => DonorHealthProfilePolicy::class,
        Organization::class => OrganizationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin super-access: admins may perform any ability on any model.
        Gate::before(function (User $user, string $ability) {
            return $user->role === UserRole::ADMIN ? true : null;
        });
    }
}


