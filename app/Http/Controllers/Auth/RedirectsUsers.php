<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

trait RedirectsUsers
{
    /**
     * Get the post-authentication redirect path based on user permissions.
     */
    protected function redirectPath(User $user): string
    {
        return $user->can('view_dashboard')
            ? route('dashboard', absolute: false)
            : route('repair-orders.mechanic', absolute: false);
    }
}
