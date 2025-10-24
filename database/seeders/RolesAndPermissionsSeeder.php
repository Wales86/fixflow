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
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = UserPermission::all();

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $ownerRole = Role::create(['name' => UserRole::OWNER->value]);
        $officeRole = Role::create(['name' => UserRole::OFFICE->value]);
        $mechanicRole = Role::create(['name' => UserRole::MECHANIC->value]);

        // Owner has all permissions except specific ones
        $excludedForOwner = [
            UserPermission::VIEW_REPAIR_ORDERS_MECHANIC->value,
        ];

        $ownerPermissions = Permission::all()
            ->whereNotIn('name', $excludedForOwner);

        $ownerRole->givePermissionTo($ownerPermissions);

        // Office can view, create, and update (but not delete)
        $officeRole->givePermissionTo([
            UserPermission::VIEW_DASHBOARD->value,

            UserPermission::VIEW_CLIENTS->value,
            UserPermission::CREATE_CLIENTS->value,
            UserPermission::UPDATE_CLIENTS->value,

            UserPermission::VIEW_VEHICLES->value,
            UserPermission::CREATE_VEHICLES->value,
            UserPermission::UPDATE_VEHICLES->value,

            UserPermission::VIEW_REPAIR_ORDERS->value,
            UserPermission::CREATE_REPAIR_ORDERS->value,
            UserPermission::UPDATE_REPAIR_ORDERS->value,
            UserPermission::UPDATE_REPAIR_ORDER_STATUS->value,

            UserPermission::VIEW_INTERNAL_NOTES->value,
            UserPermission::CREATE_INTERNAL_NOTES->value,
            UserPermission::UPDATE_INTERNAL_NOTES->value,
        ]);

        // Mechanic can view repair orders list (mechanic view only)
        $mechanicRole->givePermissionTo([
            UserPermission::VIEW_REPAIR_ORDERS_MECHANIC->value,
            UserPermission::CREATE_TIME_ENTRIES->value,
            UserPermission::UPDATE_TIME_ENTRIES->value,
        ]);

        // Update cache to know about the newly created permissions and roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
