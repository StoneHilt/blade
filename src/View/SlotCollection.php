<?php

namespace StoneHilt\Blade\View;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\View\ComponentSlot;

/**
 * Class SlotCollection
 *
 * @package StoneHilt\Blade\View
 */
class SlotCollection extends Collection
{
    use ForwardsCalls;

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return parent::__call($method, $parameters);
        }

        $last = $this->last();

        if ($last instanceof ComponentSlot && method_exists($last, $method)) {
            return $last->{$method}($parameters);
        }

        throw new \BadMethodCallException(
            sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            )
        );
    }

    /**
     * Get the slot's HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->reduce(
            function ($result, $slot, $key) {
                if ($slot instanceof Htmlable) {
                    return $result . $slot->toHtml();
                }

                return $result . $slot;
            },
            ''
        );
    }
}
