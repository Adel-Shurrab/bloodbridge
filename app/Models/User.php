<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    // Roles
    public const ROLE_DONOR = 1;
    public const ROLE_ORGANIZATION = 2;
    public const ROLE_ADMIN = 3;

    public static function getRoleOptions(): array
    {
        return [
            self::ROLE_DONOR => 'متبرع',
            self::ROLE_ORGANIZATION => 'منظمة',
            self::ROLE_ADMIN => 'مسؤول النظام',
        ];
    }

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
            self::ROLE_ADMIN => route('filament.admin.pages.dashboard'),
            self::ROLE_DONOR => route('filament.donor.pages.dashboard'),
            self::ROLE_ORGANIZATION => ($org = $this->organization)
                ? route('filament.organization.pages.dashboard', ['tenant' => $org->slug])
                : route('home'),
            default => route('home'),
        };
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();

        return match ($panelId) {
            'admin' => $this->role === self::ROLE_ADMIN,
            'donor' => $this->role === self::ROLE_DONOR,
            'organization' => $this->role === self::ROLE_ORGANIZATION,
            default => false,
        };
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->organization ? collect([$this->organization]) : collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
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
}
