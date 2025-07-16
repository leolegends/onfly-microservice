<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Garantir que estamos usando SQLite em memória para testes
        if (config('database.default') !== 'sqlite_testing') {
            config(['database.default' => 'sqlite_testing']);
        }
    }
}
