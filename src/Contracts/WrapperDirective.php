<?php

namespace StoneHilt\Blade\Contracts;

/**
 * Interface WrapperDirective
 *
 * @package StoneHilt\Blade\Contracts
 */
interface WrapperDirective extends Directive
{
    /**
     * @return string
     */
    public function close(): string;
}
