<?php

namespace StoneHilt\Blade\Directives;

use StoneHilt\Blade\Contracts\Directive;

/**
 * Class Aware
 *
 * @package StoneHilt\Blade\Directives
 */
class Aware implements Directive
{
    /**
     * @return string
     */
    public static function name(): string
    {
        return 'aware';
    }

    /**
     * @param  mixed  ...$expression
     * @return string
     */
    public function __invoke(...$expression): string
    {
        return "<?php foreach ({$expression[0]} as \$__key => \$__value) {
    \$__consumeVariable = is_string(\$__key) ? \$__key : \$__value;
    \$\$__consumeVariable = is_string(\$__key) ? \$__env->getConsumableComponentData(\$__key, \$__value) : \$__env->getConsumableComponentData(\$__value);
} ?>";
    }
}
