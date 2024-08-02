<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts() {
        return [
            'password' => 'hashed'
        ];
    }

    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_key');
    }
}