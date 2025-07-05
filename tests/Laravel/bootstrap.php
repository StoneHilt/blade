<?php

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(dirname(__DIR__))
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    \StoneHilt\Blade\Tests\Laravel\Kernel::class
);

$app->useBootstrapPath(__DIR__);
$app->useConfigPath(__DIR__ . '/config');
$app->useStoragePath(__DIR__ . '/storage');

$app->instance('path.resources', __DIR__ . '/storage');

return $app;
