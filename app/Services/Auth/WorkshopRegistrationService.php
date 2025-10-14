<?php

namespace App\Services\Auth;

use App\Dto\RegisterWorkshopData;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class WorkshopRegistrationService
{
    public function register(RegisterWorkshopData $data): User
    {
        return DB::transaction(static function () use ($data) {
            // Create workshop (tenant)
            $workshop = Workshop::create([
                'name' => $data->workshop_name,
            ]);

            // Make this workshop the current tenant
            $workshop->makeCurrent();

            // Ensure the Owner role exists (it should be created during seeding)
            $ownerRole = Role::firstOrCreate(['name' => 'Owner']);

            // Create the owner user
            $user = User::create([
                'workshop_id' => $workshop->id,
                'name' => $data->owner_name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            // Assign Owner role to the user
            $user->assignRole($ownerRole);

            return $user;
        });
    }
}
