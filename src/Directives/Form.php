<?php

namespace StoneHilt\Blade\Directives;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\View\ComponentAttributeBag;
use StoneHilt\Blade\Contracts\WrapperDirective;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Form
 *
 * @package StoneHilt\Blade\Directives
 */
class Form implements WrapperDirective
{
    /**
     * @var array $spoofedMethods
     */
    protected static array $spoofedMethods = [
        Request::METHOD_DELETE,
        Request::METHOD_PATCH,
        Request::METHOD_PUT,
    ];

    /**
     * @var array $essentialOptions
     */
    protected static array $essentialOptions = [
        'method',
        'action',
        'route',
    ];

    /**
     * @var Application $app
     */
    protected $app;

    /**
     * @param  Application  $application
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    /**
     * @inheritDoc
     */
    public static function name(): string
    {
        return 'form';
    }

    /**
     * @inheritDoc
     */
    public function __invoke(...$expression): string
    {
        $essentialOptions = array_fill_keys(static::$essentialOptions, null);

        if (is_array($expression[0])) {
            $options = array_merge(
                $essentialOptions,
                $expression[0]
            );

            if (isset($options['route'])) {
                [$options['method'], $options['action']] = $this->determineMethodUriFromRoute($options['route']);
            }
        } else {
            $options = [
                'method' => $expression[0] ?? 'GET',
                'action' => $expression[1] ?? '#',
                'route'  => null,
            ];
        }

        return $this->renderOpening(
            strtoupper($options['method'] ?? Request::METHOD_GET),
            $options['action'],
            new ComponentAttributeBag(array_diff_key($options, $essentialOptions))
        );
    }

    /**
     * @inheritDoc
     */
    public function close(): string
    {
        return '</form>';
    }

    /**
     * @param string $method
     * @param string $action
     * @param ComponentAttributeBag $attributes
     * @return string
     */
    protected function renderOpening(string $method, string $action, ComponentAttributeBag $attributes): string
    {
        $methodAlias = '';
        if (in_array($method, static::$spoofedMethods)) {
            $methodAlias = sprintf(
                '<input name="_method" value="%s" />',
                $method
            );
        }

        $attributes['method'] = ($method !== Request::METHOD_GET ? Request::METHOD_POST : $method);
        $attributes['action'] = $action;

        return sprintf(
            '<form %s>%s%s',
            $attributes,
            $methodAlias,
            $method !== Request::METHOD_GET ? $this->csrfInput() : ''
        );
    }

    /**
     * @return string
     */
    protected function csrfInput(): string
    {
        return csrf_field()->toHtml();
    }

    /**
     * @param array|string $route
     * @return array
     * @throws BindingResolutionException
     * @throws UrlGenerationException
     */
    protected function determineMethodUriFromRoute(array|string $route): array
    {
        $parameters = [];
        if (is_array($route)) {
            [$route, $parameters] = $route;
        }

        $route = $this->app->make('router')
            ->getRoutes()
            ->getByName($route);

        $method = array_first($route->methods());

        $uri = $this->app->make('url')->toRoute($route, $parameters, true);

        return [$method, $uri];
    }
}
