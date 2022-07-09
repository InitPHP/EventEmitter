<?php
/**
 * EventEmitter.php
 *
 * This file is part of EventEmitter.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2022 Muhammet ŞAFAK
 * @license    ./LICENSE  MIT
 * @version    1.0
 * @link       https://www.muhammetsafak.com.tr
 */

namespace InitPHP\EventEmitter;

use \InvalidArgumentException;
use function is_string;
use function is_int;
use function is_array;
use function is_callable;
use function strtolower;
use function array_search;
use function array_unique;
use function array_merge;
use function array_keys;
use function ksort;
use function call_user_func_array;

class EventEmitter implements EventEmitterInterface
{

    /** @var array */
    protected $listeners = [];

    /** @var array */
    protected $onceListeners = [];

    /**
     * @inheritDoc
     */
    public function on($event, $listener, $priority = 100)
    {
        $this->addListener('listeners', $event, $listener, $priority);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function once($event, $listener, $priority = 100)
    {
        $this->addListener('onceListeners', $event, $listener, $priority);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeListener($event, $listener)
    {
        if(!is_string($event)){
            throw new InvalidArgumentException('$event must be a string.');
        }
        if(!is_callable($listener)){
            throw new InvalidArgumentException('$listener must be a callable.');
        }

        $event = strtolower($event);

        if(isset($this->listeners[$event])){
            foreach ($this->listeners[$event] as $key => $value) {
                if(($index = array_search($listener, $value, true)) === FALSE){
                    continue;
                }
                unset($this->listeners[$event][$key][$index]);
                if(empty($this->listeners[$event][$key])){
                    unset($this->listeners[$event][$key]);
                }
            }
        }

        if(isset($this->onceListeners[$event])){
            foreach ($this->onceListeners[$event] as $key => $value) {
                if(($index = array_search($listener, $value, true)) === FALSE){
                    continue;
                }
                unset($this->onceListeners[$event][$key][$index]);
                if(empty($this->onceListeners[$event][$key])){
                    unset($this->onceListeners[$event][$key]);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function removeAllListeners($event = null)
    {
        if($event === null){
            $this->listeners = [];
            $this->onceListeners = [];
            return;
        }
        if(!is_string($event)){
            throw new InvalidArgumentException('$event must be a string or null.');
        }
        $event = strtolower($event);
        if(isset($this->listeners[$event])){
            unset($this->listeners[$event]);
        }
        if(isset($this->onceListeners[$event])){
            unset($this->onceListeners[$event]);
        }
    }

    /**
     * @inheritDoc
     */
    public function listeners($event = null)
    {
        $events = [];
        if($event === null){
            $eventNames = array_unique(array_merge(array_keys($this->listeners), array_keys($this->onceListeners)));
        }else{
            if(!is_string($event)){
                throw new InvalidArgumentException('$event must be a string or null.');
            }
            $eventNames = [$event];
        }
        foreach ($eventNames as $eventName) {
            $event = strtolower($eventName);
            $listeners = isset($this->listeners[$event]) ? $this->listeners[$event] : [];
            foreach ($listeners as $values) {
                ksort($values);
                foreach ($values as $value) {
                    $events[] = $value;
                }
            }
            $listeners = isset($this->onceListeners[$event]) ? $this->onceListeners[$event] : [];
            foreach ($listeners as $values) {
                ksort($values);
                foreach ($values as $value) {
                    $events[] = $value;
                }
            }
        }
        return $events;
    }

    /**
     * @inheritDoc
     */
    public function emit($event, $arguments = [])
    {
        if(!is_string($event)){
            throw new InvalidArgumentException('$event must be a string.');
        }
        if(!is_array($arguments)){
            throw new InvalidArgumentException('$arguments must be an array.');
        }

        $listeners = $this->listeners($event);

        $event = strtolower($event);
        if(isset($this->onceListeners[$event])){
            unset($this->onceListeners[$event]);
        }

        if(!empty($listeners)){
            foreach ($listeners as $listener) {
                call_user_func_array($listeners, $arguments);
            }
        }
    }

    private function addListener($property, $event, $listener, $priority = 100)
    {
        if(!is_string($event)){
            throw new InvalidArgumentException('$event must be a string.');
        }
        if(!is_callable($listener)){
            throw new InvalidArgumentException('$listener must be a callable.');
        }
        if(!is_int($priority)){
            throw new InvalidArgumentException('$priority must be an integer.');
        }
        $event = strtolower($event);

        if(!isset($this->{$property}[$event])){
            $this->{$property}[$event] = [];
        }
        if(!isset($this->{$property}[$event][$priority])){
            $this->{$property}[$event][$priority] = [];
        }
        $this->{$property}[$event][$priority][] = $listener;
    }

}
