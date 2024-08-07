<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Observers\RoleObserver;

#[ObservedBy([RoleObserver::class])]
class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key'
    ];

    public function permissions(): BelongsToMany{
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'role_key', 'permission_key', 'key', 'key');
    }
}
