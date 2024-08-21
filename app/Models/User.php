<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function roles(): HasOne {
        return $this->hasOne(Role::class);
    }

    public function hasPermission($permission) {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) return true;
        }

        return false;
    }
}