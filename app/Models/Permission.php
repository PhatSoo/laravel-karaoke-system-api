<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Observers\PermissionObserver;

#[ObservedBy([PermissionObserver::class])]
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'related_table'
    ];

    public function roles(): BelongsToMany{
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }
}