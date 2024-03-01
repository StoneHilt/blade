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
use StoneHilt\Blade\Directives\Form;
use StoneHilt\Blade\Directives\Inherit;
use StoneHilt\Blade\Directives\Route;
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
        Form::class,
        Inherit::class,
        Route::class,
    ];

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
}
