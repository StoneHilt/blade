<?php

namespace StoneHilt\Blade\Directives;

use Illuminate\Support\Carbon;
use StoneHilt\Blade\Contracts\Directive;

/**
 * Class Time
 *
 * @package StoneHilt\Blade\Directives
 */
class Time implements Directive
{
    /**
     * @inheritDoc
     */
    public static function name(): string
    {
        return 'time';
    }

    /**
     * @inheritDoc
     */
    public function __invoke(...$expression): string
    {
        $dateTime = is_string($expression[0]) ? new Carbon($expression[0]) : $expression[0];

        if ($dateTime instanceof \DateTime) {
            return $dateTime->format(config('formats.defaults.time', 'H:i:s'));
        }

        throw new \InvalidArgumentException(
            sprintf(
                'The Time blade must be provided a valid datetime object or a string parsable as a DateTime object: "%s" provided',
                $expression[0]
            )
        );
    }
}
