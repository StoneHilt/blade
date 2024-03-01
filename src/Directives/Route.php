<?php

namespace StoneHilt\Blade\Directives;

use StoneHilt\Blade\Contracts\Directive;

/**
 * Class Route
 *
 * @package StoneHilt\Blade\Directives
 */
class Route implements Directive
{
    /**
     * @inheritDoc
     */
    public static function name(): string
    {
        return 'route';
    }

    /**
     * @param  mixed  ...$parameters
     * @return string
     */
    public function __invoke(...$parameters): string
    {
        return route(...$parameters);
    }
}
