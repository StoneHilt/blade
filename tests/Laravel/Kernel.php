<?php

namespace StoneHilt\Blade\Tests\Laravel;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\Filesystem;

/**
 * Class Kernel
 *
 * @package StoneHilt\Bootstrap\Tests\Laravel
 */
class Kernel extends \Illuminate\Foundation\Console\Kernel
{
    /**
     * @return void
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function bootstrap()
    {
        parent::bootstrap();

        $this->autoloadProvider();
    }

    /**
     * @return void
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    protected function autoloadProvider(): void
    {
        $files = $this->app->make(Filesystem::class);

        if (!$files->exists($path = $this->app->basePath() . '/composer.json')) {
            return;
        }

        $package = json_decode($files->get($path), true);

        if (isset($package['extra']['laravel']['providers'])) {
            foreach ($package['extra']['laravel']['providers'] as $provider) {
                $this->app->register($provider);
            }
        }
    }
}