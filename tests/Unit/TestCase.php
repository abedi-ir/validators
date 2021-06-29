<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $app = new Jalno\Lumen\Application(dirname(dirname(__DIR__)), Jalno\Validators\Package::class);
        $app->withFacades();
        $app->withEloquent();
        $app->configure('app');
        return $app;
    }
}
