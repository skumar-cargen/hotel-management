<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Domains this user manages.
     */
    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'domain_user')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Check if user is a Domain Manager (not a full Admin).
     */
    public function isDomainManager(): bool
    {
        return $this->hasRole('Domain Manager') && ! $this->hasRole('Admin');
    }

    /**
     * Check if user is a super Admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Get IDs of domains this user manages.
     */
    public function managedDomainIds(): array
    {
        if ($this->isAdmin()) {
            return Domain::pluck('id')->toArray();
        }

        return $this->domains()->where('domain_user.is_active', true)->pluck('domains.id')->toArray();
    }

    /**
     * Get IDs of hotels within user's managed domains.
     */
    public function managedHotelIds(): array
    {
        if ($this->isAdmin()) {
            return Hotel::pluck('id')->toArray();
        }

        $domainIds = $this->managedDomainIds();

        return \DB::table('domain_hotel')
            ->whereIn('domain_id', $domainIds)
            ->pluck('hotel_id')
            ->unique()
            ->toArray();
    }

    /**
     * Check if user can access a specific domain.
     */
    public function canAccessDomain(int $domainId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($domainId, $this->managedDomainIds());
    }

    /**
     * Check if user can access a specific hotel.
     */
    public function canAccessHotel(int $hotelId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($hotelId, $this->managedHotelIds());
    }
}
