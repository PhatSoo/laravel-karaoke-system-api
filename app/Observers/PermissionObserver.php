<?php

namespace App\Observers;

use App\Models\Permission;

class PermissionObserver
{
    public function created(Permission $permission):void
    {
        $slug = \Str::slug($permission->name, '_');
        $permission->key = '0' . $permission->id . '_' . $slug;
        $permission->save();
    }

    public function updating(Permission $permission):void
    {
        $slug = \Str::slug($permission->name, '_');
        $permission->key = '0' . $permission->id . '_' . $slug;
    }
}