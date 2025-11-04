<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = UserPermission::all();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $ownerRole = Role::firstOrCreate(['name' => UserRole::OWNER->value]);
        $officeRole = Role::firstOrCreate(['name' => UserRole::OFFICE->value]);
        $mechanicRole = Role::firstOrCreate(['name' => UserRole::MECHANIC->value]);

        $ownerRole->syncPermissions(UserPermission::ownerPermissions());

        $officeRole->syncPermissions(UserPermission::officePermissions());

        $mechanicRole->syncPermissions(UserPermission::mechanicPermissions());

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
