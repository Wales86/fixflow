<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Client permissions
            'view clients',
            'create clients',
            'update clients',
            'delete clients',

            // Vehicle permissions
            'view vehicles',
            'create vehicles',
            'update vehicles',
            'delete vehicles',

            // Repair Order permissions
            'view repair orders',
            'create repair orders',
            'update repair orders',
            'delete repair orders',
            'update repair order status',

            // Internal Note permissions
            'view internal notes',
            'create internal notes',
            'update internal notes',
            'delete internal notes',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $ownerRole = Role::create(['name' => UserRole::OWNER->value]);
        $officeRole = Role::create(['name' => UserRole::OFFICE->value]);
        $mechanicRole = Role::create(['name' => UserRole::MECHANIC->value]);

        // Owner has all permissions
        $ownerRole->givePermissionTo(Permission::all());

        // Office can view, create, and update (but not delete)
        $officeRole->givePermissionTo([
            'view clients',
            'create clients',
            'update clients',

            'view vehicles',
            'create vehicles',
            'update vehicles',

            'view repair orders',
            'create repair orders',
            'update repair orders',
            'update repair order status',

            'view internal notes',
            'create internal notes',
            'update internal notes',
        ]);

        // Mechanic has no list/index permissions
        // They can only view individual items assigned to them (handled in policies)

        // Update cache to know about the newly created permissions and roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
