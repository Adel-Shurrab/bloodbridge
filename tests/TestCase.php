<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            $db = \Illuminate\Support\Facades\DB::connection()->getPdo();

            @$db->sqliteCreateFunction('radians', fn($degrees) => deg2rad($degrees));
            @$db->sqliteCreateFunction('cos', fn($radians) => cos($radians));
            @$db->sqliteCreateFunction('sin', fn($radians) => sin($radians));
            @$db->sqliteCreateFunction('acos', fn($value) => acos($value));
        }
    }
}
