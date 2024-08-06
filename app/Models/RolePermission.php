<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'roles_permissions';

    protected $hidden = [
        'id'
    ];

    protected $fillable = [
        'role_key',
        'permission_key'
    ];
}