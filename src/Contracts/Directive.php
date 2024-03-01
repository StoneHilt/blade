<?php

namespace StoneHilt\Blade\Contracts;

/**
 * Interface Directive
 *
 * @package StoneHilt\Blade\Contracts
 */
interface Directive
{
    /**
     * @return string
     */
    public static function name(): string;

    /**
     * @param $expression
     * @return string
     */
    public function __invoke(...$expression): string;
}
