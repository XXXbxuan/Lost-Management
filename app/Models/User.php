<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',   // <--- FIXED: Matches your database column
        'email',
        'password',
        'role',       // Passenger, Staff, or Admin
        'points',     // For your Reward System
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper: Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    /**
     * Relationship: User has one Staff profile
     */
    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }
}