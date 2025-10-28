<?php

namespace App\Services;

use App\Dto\User\UserData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}
