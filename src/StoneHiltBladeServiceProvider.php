<?php

namespace StoneHilt\Blade;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\ViewFinderInterface;
use StoneHilt\Blade\Contracts\Directive;
use StoneHilt\Blade\Contracts\WrapperDirective;
use StoneHilt\Blade\Directives\Aware;
use StoneHilt\Blade\Directives\Date;
use StoneHilt\Blade\Directives\DateTime;
use StoneHilt\Blade\Directives\Form;
use StoneHilt\Blade\Directives\Inherit;
use StoneHilt\Blade\Directives\Route;
use StoneHilt\Blade\Directives\Time;
use StoneHilt\Blade\View\Factory;

/**
 * Class StoneHiltBladeServiceProvider
 *
 * @package StoneHilt\Blade
 */
class StoneHiltBladeServiceProvider extends ServiceProvider
{
    /**
     * @var array|string[]
     */
    protected static array $directives = [
        Aware::class,
        Date::class,
        DateTime::class,
        Form::class,
        Inherit::class,
        Route::class,
        Time::class,
    ];

    protected static string $packageConfigFile = __DIR__.'/../config/blade.php';
    protected static string $packageResourceViews = __DIR__.'/../resources/views';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFactory();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootBladeDirectives();
        $this->bootComponentHelpers();
        $this->addConfiguration();
        $this->addComponentViews();
    }

    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $factory = $this->createFactory($resolver, $finder, $app['events']);

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $factory->setContainer($app);

            $factory->share('app', $app);

            $app->terminating(static function () {
                Component::forgetFactory();
            });

            return $factory;
        });
    }

    /**
     * @return void
     */
    public function bootBladeDirectives(): void
    {
        /** @var Directive $directive */
        foreach (static::$directives as $directive) {
            Blade::directive(
                $directive::name(),
                function ($expression) use ($directive) {
                    return sprintf(
                        '<?php echo (app()->make(%s::class))(%s); ?>',
                        $directive,
                        $expression
                    );
                }
            );

            if (is_a($directive, WrapperDirective::class, true)) {
                Blade::directive(
                    'end' . $directive::name(),
                    function () use ($directive) {
                        return sprintf(
                            '<?php echo (app()->make(%s::class))->close(); ?>',
                            $directive
                        );
                    }
                );
            }
        }
    }

    /**
     * @return void
     */
    public function bootComponentHelpers(): void
    {
        ComponentAttributeBag::macro(
            'empty',
            function () {
                return empty($this->attributes);
            }
        );
    }

    /**
     * Create a new Factory Instance.
     *
     * @param  EngineResolver  $resolver
     * @param  ViewFinderInterface  $finder
     * @param  Dispatcher  $events
     * @return Factory
     */
    protected function createFactory($resolver, $finder, $events)
    {
        return new Factory($resolver, $finder, $events);
    }

    /**
     * @return void
     */
    protected function addConfiguration(): void
    {
        $this->mergeConfigFrom(static::$packageConfigFile, 'defaults');

        $this->publishes(
            [
                static::$packageConfigFile => config_path('defaults.php'),
            ],
            'defaults-config',
        );
    }

    /**
     * @return void
     */
    protected function addComponentViews(): void
    {
        $this->loadViewsFrom(static::$packageResourceViews, 'stonehilt');

        Blade::componentNamespace('StoneHilt\\Blade\\View\\Components', 'stonehilt');
        Blade::anonymousComponentPath(static::$packageResourceViews, 'bootstrap');

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    static::$packageResourceViews => resource_path('views/vendor/stonehilt'),
                ],
                'stonehilt-views',
            );
        }
    }
}
