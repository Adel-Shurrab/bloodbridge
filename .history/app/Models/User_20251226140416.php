<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Roles
    public const ROLE_DONOR = 'donor';
    public const ROLE_ORGANIZATION = 'organization';
    public const ROLE_ADMIN = 'admin';

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
        'organization_id'
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
            self::ROLE_ORGANIZATION => route('filament.organization.pages.dashboard'),
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
