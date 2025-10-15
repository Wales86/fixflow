<?php

namespace Tests;

use App\Models\Workshop;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public Workshop $workshop;
}
