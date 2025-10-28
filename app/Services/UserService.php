<?php

namespace App\Services;

use App\Dto\User\CreateUserData;
use App\Dto\User\UpdateUserData;
use App\Dto\User\UserData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getUsers(int $workshopId): LengthAwarePaginator
    {
        return User::query()
            ->where('workshop_id', $workshopId)
            ->with('roles')
            ->orderBy('name')
            ->paginate(15)
            ->through(fn ($user) => UserData::from($user));
    }

    public function create(CreateUserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password),
                'workshop_id' => auth()->user()->workshop_id,
            ]);

            $user->assignRole($data->role);

            return $user;
        });
    }

    public function update(User $user, UpdateUserData $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data->name,
                'email' => $data->email,
            ]);

            $user->syncRoles($data->roles);

            return $user->fresh();
        });
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
