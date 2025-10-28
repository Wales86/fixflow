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

        $excludedForOwner = [
            UserPermission::VIEW_REPAIR_ORDERS_MECHANIC->value,
        ];

        $ownerPermissions = Permission::all()
            ->whereNotIn('name', $excludedForOwner);

        $ownerRole->syncPermissions($ownerPermissions);

        $officeRole->syncPermissions([
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

            UserPermission::VIEW_MECHANICS->value,
            UserPermission::CREATE_MECHANICS->value,
            UserPermission::UPDATE_MECHANICS->value,

            UserPermission::VIEW_USERS->value,
            UserPermission::CREATE_USERS->value,
            UserPermission::UPDATE_USERS->value,
            UserPermission::DELETE_USERS->value,
        ]);

        $mechanicRole->syncPermissions([
            UserPermission::VIEW_REPAIR_ORDERS_MECHANIC->value,
            UserPermission::CREATE_TIME_ENTRIES->value,
            UserPermission::UPDATE_TIME_ENTRIES->value,
            UserPermission::CREATE_INTERNAL_NOTES->value,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
