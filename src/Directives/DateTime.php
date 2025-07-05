<?php

namespace StoneHilt\Blade\Directives;

use Illuminate\Support\Carbon;
use StoneHilt\Blade\Contracts\Directive;

/**
 * Class DateTime
 *
 * @package StoneHilt\Blade\Directives
 */
class DateTime implements Directive
{
    /**
     * @inheritDoc
     */
    public static function name(): string
    {
        return 'datetime';
    }

    /**
     * @inheritDoc
     */
    public function __invoke(...$expression): string
    {
        $dateTime = is_string($expression[0]) ? new Carbon($expression[0]) : $expression[0];

        if ($dateTime instanceof \DateTime) {
            return $dateTime->format(config('formats.defaults.datetime', 'Y-m-d H:i'));
        }

        throw new \InvalidArgumentException(
            sprintf(
                'The DateTime blade must be provided a valid datetime object or a string parsable as a DateTime object: "%s" provided',
                $expression[0]
            )
        );
    }
}
