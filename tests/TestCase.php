<?php

namespace Tests;

use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions for every test
        $this->seed(RolesAndPermissionsSeeder::class);
    }
}
