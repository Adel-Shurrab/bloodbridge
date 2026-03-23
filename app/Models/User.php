<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;

class User extends Authenticatable implements FilamentUser, HasTenants, MustVerifyEmail, HasLocalePreference
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, SoftDeletes, Notifiable;

    public const DEFAULT_IS_ACTIVE = true;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getDashboardUrl(): string
    {
        return match ($this->role) {
            \App\Enums\UserRole::ADMIN => route('filament.admin.pages.dashboard'),
            \App\Enums\UserRole::DONOR => route('filament.donor.pages.dashboard'),
            \App\Enums\UserRole::ORGANIZATION => ($org = $this->organization)
                ? route('filament.organization.pages.dashboard', ['tenant' => $org->slug])
                : route('home'),
            default => route('home'),
        };
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();

        // Inactive users cannot access any panels.
        if (! $this->is_active) {
            return false;
        }

        return match ($panelId) {
            'admin' => $this->role === \App\Enums\UserRole::ADMIN,
            'donor' => $this->role === \App\Enums\UserRole::DONOR,
            'organization' => $this->role === \App\Enums\UserRole::ORGANIZATION,
            default => false,
        };
    }

    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->role !== \App\Enums\UserRole::ORGANIZATION) {
            return collect();
        }

        return $this->organization ? collect([$this->organization]) : collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->role !== \App\Enums\UserRole::ORGANIZATION) {
            return false;
        }

        return $this->organization?->id === $tenant->id;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => \App\Enums\UserRole::class,
        ];
    }

    public function donor()
    {
        return $this->hasOne(Donor::class);
    }

    public function organization()
    {
        return $this->hasOne(Organization::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }

    /**
     * Get the user's preferred locale for notifications.
     */
    public function preferredLocale(): string
    {
        return $this->locale ?? config('app.fallback_locale');
    }
}
