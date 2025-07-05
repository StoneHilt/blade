<?php

namespace StoneHilt\Blade\Tests\Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\PackageManifest;
use StoneHilt\Blade\Tests\TestCase;

/**
 * Class FeatureTestCase
 *
 * @package StoneHilt\Blade\Tests\Feature
 */
class FeatureTestCase extends TestCase
{
    /**
     * @return void
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        $this->afterApplicationCreated(
            function () {
                $this->app->make(PackageManifest::class)->build();
                $this->app->make(Factory::class)->addLocation(__DIR__ . '/views');
            }
        );

        \Illuminate\Foundation\Testing\RefreshDatabaseState::$migrated = true;
        parent::setUp();
    }
}
