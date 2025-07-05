<?php

namespace StoneHilt\Blade\View\Components\Support;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * Class Stack
 *
 * @package StoneHilt\Blade\View\Components\Support
 */
class Stack extends Component
{
    /**
     * @var array $stacks
     */
    public static array $stacks = [];

    /**
     * @var string $defaultName
     */
    public static string $defaultName = '_default';

    /**
     * Create a new component instance.
     */
    public function __construct(public ?string $name = null, public bool $render = false, public string|bool|null $once = null)
    {
        if (!isset(static::$stacks[static::$defaultName])) {
            static::$stacks[static::$defaultName] = new Collection();
        }
    }

    /**
     * @param $content
     * @param string|null $key
     * @param string|null $name
     * @return void
     */
    public static function prepend($content, ?string $key = null, ?string $name = null): void
    {
        $collection = static::getCollection($name ?? static::$defaultName);

        $collection->prepend($content, $key);
    }

    /**
     * @param $content
     * @param string|null $key
     * @param string|null $name
     * @return void
     */
    public static function append($content, ?string $key = null, ?string $name = null): void
    {
        $collection = static::getCollection($name ?? static::$defaultName);

        if (!empty($key)) {
            // Make sure key is added only once
            $collection->getOrPut($key, $content);
        } else {
            $collection->push($content);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return function (array $viewData) {
            $name = $this->name ?? static::$defaultName;

            if ($viewData['slot']->isNotEmpty()) {
                static::append(
                    $viewData['slot']->toHtml(),
                    $this->once === true ? sha1(microtime(true)) : ($this->once ?: null),
                    $name
                );
            }

            if (!$this->render) {
                return '';
            }

            return $this->view(
                'components.support.stack',
                [
                    'stack' => static::getCollection($name),
                ]
            );
        };
    }

    /**
     * @param string $name
     * @return Collection
     */
    protected static function getCollection(string $name): Collection
    {
        if (!isset(static::$stacks[$name])) {
            static::$stacks[$name] = new Collection();
        }

        return static::$stacks[$name];
    }
}
