<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public const ROLE_ADMIN = 'مدیر';

    public const ROLE_ACCOUNTANT = 'حسابدار';

    public const ROLE_STAFF = 'کارمند';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function preference(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Organizations this user is explicitly allowed to work in. Ignored
     * for admins, who always have access to every organization.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isAccountant(): bool
    {
        return $this->hasRole(self::ROLE_ACCOUNTANT);
    }

    public function isStaff(): bool
    {
        return $this->hasRole(self::ROLE_STAFF);
    }

    /**
     * IDs of the organizations this user may see/act within. Admins are
     * not restricted, so callers should check isAdmin() first and skip
     * scoping entirely rather than relying on this returning "all IDs".
     *
     * @return array<int, int>
     */
    public function accessibleOrganizationIds(): array
    {
        return $this->organizations()->pluck('organizations.id')->all();
    }

    public function canAccessOrganization(?int $organizationId): bool
    {
        if (! $organizationId) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        return in_array($organizationId, $this->accessibleOrganizationIds(), true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_ACCOUNTANT, self::ROLE_STAFF]);
    }
}
