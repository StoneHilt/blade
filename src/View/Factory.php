<?php

namespace StoneHilt\Blade\View;

use Illuminate\View\ComponentSlot;
use Illuminate\View\Factory as IlluminateFactory;

/**
 * Class Factory
 *
 * @package StoneHilt\Blade\View
 */
class Factory extends IlluminateFactory
{
    /**
     * @param string $key
     * @return mixed|null
     */
    public function getParentComponentData(string $key): mixed
    {
        $currentComponent = count($this->componentStack);

        if ($currentComponent === 0) {
            return null;
        }

        for ($i = $currentComponent - 1; $i >= 0; $i--) {
            $data = $this->componentData[$i] ?? [];

            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
        }

        return null;
    }

    /**
     * Start the slot rendering process.
     *
     * @param  string  $name
     * @param  string|null  $content
     * @param  array  $attributes
     * @return void
     */
    public function slot($name, $content = null, $attributes = [])
    {
        if (func_num_args() === 2 || $content !== null) {
            $this->slots[$this->currentComponent()][$name] = $content;
        } elseif (ob_start()) {
            $this->slotStack[$this->currentComponent()][] = [$name, $attributes];
        }
    }

    /**
     * Save the slot content for rendering.
     *
     * @return void
     */
    public function endSlot()
    {
        last($this->componentStack);

        $currentSlot = array_pop(
            $this->slotStack[$this->currentComponent()]
        );

        [$currentName, $currentAttributes] = $currentSlot;

        $componentName = $this->currentComponent();

        $slot = new ComponentSlot(
            trim(ob_get_clean()),
            $currentAttributes
        );

        if (!isset($this->slots[$componentName][$currentName])) {
            $this->slots[$componentName][$currentName] = $slot;
        } elseif ($this->slots[$componentName][$currentName] instanceof SlotCollection) {
            $this->slots[$componentName][$currentName]->push($slot);
        } else {
            $this->slots[$componentName][$currentName] = new SlotCollection(
                [
                    $this->slots[$componentName][$currentName],
                    $slot
                ]
            );
        }
    }
}
