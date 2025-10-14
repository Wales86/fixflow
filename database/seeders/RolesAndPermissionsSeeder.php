<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles from enum
        foreach (UserRole::cases() as $role) {
            Role::create(['name' => $role->value]);
        }

        // Update cache to know about the newly created roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
