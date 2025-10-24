<?php

namespace App\Policies;

use App\Enums\UserPermission;
use App\Models\User;

class DashboardPolicy
{
    public function view(User $user): bool
    {
        return $user->can(UserPermission::VIEW_DASHBOARD->value);
    }
}
