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

            $workshop = Workshop::create([
                'name' => $data->workshop_name,
            ]);

            $workshop->makeCurrent();

            $ownerRole = Role::firstOrCreate(['name' => 'Owner']);

            $user = User::create([
                'workshop_id' => $workshop->id,
                'name' => $data->owner_name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            $user->assignRole($ownerRole);

            return $user;
        });
    }
}
