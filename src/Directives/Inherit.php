<?php

namespace StoneHilt\Blade\Directives;

use StoneHilt\Blade\Contracts\Directive;

/**
 * Class Inherit
 *
 * @package StoneHilt\Blade\Directives
 */
class Inherit implements Directive
{
    /**
     * @return string
     */
    public static function name(): string
    {
        return 'inherit';
    }

    /**
     * @param  mixed  ...$expression
     * @return string
     */
    public function __invoke(...$expression): string
    {
        return "<?php foreach ({$expression[0]} as \$__key => \$__alias) {
    \$__parentVariable = is_string(\$__key) ? \$__key : \$__alias;
    \$\$__alias = \$__env->getParentComponentData(\$__parentVariable);
} ?>";
    }
}
