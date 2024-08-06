<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

use App\Observers\PermissionObserver;

#[ObservedBy([PermissionObserver::class])]
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key'
    ];
}