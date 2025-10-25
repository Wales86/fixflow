<?php

namespace Tests;

use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public Workshop $workshop;

    public User $user;

    public Workshop $workshop1;

    public Workshop $workshop2;

    public User $user1;

    public User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions for every test
        $this->seed(RolesAndPermissionsSeeder::class);
    }
}
