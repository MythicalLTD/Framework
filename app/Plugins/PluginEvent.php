<?php

namespace MythicalSystemsFramework\Plugins;

/**
 * This class is used to handle plugin events.
 *
 * @since 1.0.0
 *
 * @version 1.0.0
 *
 * @category Plugins
 *
 * This class is inspired by Evenement.
 *
 * @see https://github.com/igorw/even  ement
 *
 * @license MIT
 *
 *  */
class PluginEvent
{
    protected array $listeners = [];

    protected array $onceListeners = [];

    /**
     * Adds a listener for the specified event.
     *
     * @param string $event the name of the event
     * @param callable $listener the listener function to be added
     *
     * @return static returns the current instance of the PluginEvent class
     */
    public function on(string $event, callable $listener): static
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    /**
     * Adds a listener for the specified event that will be triggered only once.
     *
     * @param string $event the name of the event
     * @param callable $listener the listener function to be added
     *
     * @return static returns the current instance of the PluginEvent class
     */
    public function once(string $event, callable $listener): static
    {
        if (!isset($this->onceListeners[$event])) {
            $this->onceListeners[$event] = [];
        }

        $this->onceListeners[$event][] = $listener;

        return $this;
    }

    /**
     * Removes a listener for the specified event.
     *
     * @param string $event the name of the event
     * @param callable $listener the listener function to be removed
     */
    public function removeListener(string $event, callable $listener): void
    {
        if (isset($this->listeners[$event])) {
            $index = array_search($listener, $this->listeners[$event], true);

            if ($index !== false) {
                unset($this->listeners[$event][$index]);

                if (count($this->listeners[$event]) === 0) {
                    unset($this->listeners[$event]);
                }
            }
        }

        if (isset($this->onceListeners[$event])) {
            $index = array_search($listener, $this->onceListeners[$event], true);

            if ($index !== false) {
                unset($this->onceListeners[$event][$index]);

                if (count($this->onceListeners[$event]) === 0) {
                    unset($this->onceListeners[$event]);
                }
            }
        }
    }

    /**
     * Removes all listeners for the specified event or all events if no event is specified.
     *
     * @param string|null $event the name of the event (optional)
     */
    public function removeAllListeners(?string $event = null): void
    {
        if ($event !== null) {
            unset($this->listeners[$event], $this->onceListeners[$event]);
        } else {
            $this->listeners = [];
            $this->onceListeners = [];
        }
    }

    /**
     * Removes all listeners for the specified event or all events if no event is specified.
     *
     * @param string|null $event the name of the event (optional)
     *
     * @return void
     */
    public function listeners(?string $event = null): array
    {
        if ($event === null) {
            $events = [];
            $eventNames = array_unique(
                array_merge(
                    array_keys($this->listeners),
                    array_keys($this->onceListeners)
                )
            );

            foreach ($eventNames as $eventName) {
                $events[$eventName] = array_merge(
                    $this->listeners[$eventName] ?? [],
                    $this->onceListeners[$eventName] ?? []
                );
            }

            return $events;
        }

        return array_merge(
            $this->listeners[$event] ?? [],
            $this->onceListeners[$event] ?? []
        );
    }

    /**
     * Emits the specified event and triggers all associated listeners.
     *
     * @param string $event the name of the event
     * @param array $arguments the arguments to be passed to the listeners (optional)
     */
    public function emit(string $event, array $arguments = []): void
    {
        $listeners = [];
        if (isset($this->listeners[$event])) {
            $listeners = array_values($this->listeners[$event]);
        }

        $onceListeners = [];
        if (isset($this->onceListeners[$event])) {
            $onceListeners = array_values($this->onceListeners[$event]);
        }

        if ($listeners !== []) {
            foreach ($listeners as $listener) {
                $listener(...$arguments);
            }
        }

        if ($onceListeners !== []) {
            unset($this->onceListeners[$event]);

            foreach ($onceListeners as $listener) {
                $listener(...$arguments);
            }
        }
    }

    /**
     * Get an instance of the PluginEvent class.
     */
    public static function getInstance(): PluginEvent
    {
        return new PluginEvent();
    }
}
