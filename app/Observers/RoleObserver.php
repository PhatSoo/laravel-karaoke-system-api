<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{
    public function created(Role $role):void
    {
        $slug = \Str::slug($role->name, '_');
        $role->key = '0' . $role->id . '_' . $slug;
        $role->save();
    }

    public function updating(Role $role):void
    {
        $slug = \Str::slug($role->name, '_');
        $role->key = '0' . $role->id . '_' . $slug;
    }
}
