<?php

namespace StoneHilt\Blade\Tests;

use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;

/**
 * Class TestCase
 *
 * @package StoneHilt\Blade\Tests
 */
class TestCase extends IlluminateTestCase
{
    /**
     * @var FakerGenerator|null $faker
     */
    private static ?FakerGenerator $faker = null;

    /**
     * @inheritDoc
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/Laravel/bootstrap.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * @return FakerGenerator
     */
    protected static function faker(): FakerGenerator
    {
        if (!isset(self::$faker)) {
            self::$faker = static::makeFaker();
        }

        return self::$faker;
    }

    /**
     * @param $locale
     * @return FakerGenerator
     */
    protected static function makeFaker($locale = null): FakerGenerator
    {
        return Factory::create($locale ?? Factory::DEFAULT_LOCALE);
    }

    /**
     * @param array $classes
     * @return string
     */
    protected static function buildClassList(array $classes): string
    {
        return implode(
            ' ',
            array_filter($classes)
        );
    }
}
