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
        'is_active'
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
        $adminRole = app_constant('user.roles.ADMIN');
        $donorRole = app_constant('user.roles.DONOR');
        $organizationRole = app_constant('user.roles.ORGANIZATION');

        return match ($this->role) {
            $adminRole => route('filament.admin.pages.dashboard'),
            $donorRole => route('filament.donor.pages.dashboard'),
            $organizationRole => route('filament.organization.pages.dashboard'),
            default => route('home'),
        };
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();
        $adminRole = app_constant('user.roles.ADMIN');
        $donorRole = app_constant('user.roles.DONOR');
        $organizationRole = app_constant('user.roles.ORGANIZATION');

        return match ($panelId) {
            'admin' => $this->role === $adminRole,
            'donor' => $this->role === $donorRole,
            'organization' => $this->role === $organizationRole,
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
