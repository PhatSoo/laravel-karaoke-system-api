<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($table_name) {
        $role = $this->role;

        if ($role->hasPermission($table_name)) return true;

        return false;
    }
}