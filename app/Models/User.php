<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'photo',
        'status',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'string',
        'role' => 'string',
    ];

    /**
     * Get the user's photo URL or default avatar
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return ($this->role ?? 'customer') === 'admin';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return ($this->status ?? 'active') === 'active';
    }

    /**
     * Scope to filter active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->orWhereNull('status');
    }

    /**
     * Scope to filter by role
     */
    public function scopeRole($query, $role)
    {
        if ($role === 'customer') {
            return $query->where('role', $role)
                         ->orWhereNull('role');
        }
        return $query->where('role', $role);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
