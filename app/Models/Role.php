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
        'name'
    ];

    public function permissions(): BelongsToMany{
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function hasPermission($table_name) {
        return $this->permissions->contains('related_table', $table_name);
    }
}