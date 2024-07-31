<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'users_roles';

    protected $hidden = [
        'user_id',
        'role_key'
    ];

    protected $fillable = [
        'user_id',
        'role_key'
    ];

    protected $with = ['user', 'role'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo {
        return $this->belongsTo(Role::class, 'role_key', 'key');
    }
}