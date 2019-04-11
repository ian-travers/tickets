<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseSetup;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://ticketbeast.lan';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });
    }
}
