<?php
namespace SergeGogoEvent\Provider;

abstract class PubSubEventProvider
{
    public function __construct(public $event){}

    protected $suscribers = [];
    private $modified_suscribers = [];

    /**
     * Summary of publish
     * @param mixed $data
     * @return static
     */
    private function publish()
    {
        $eventName = $this->getEventName();
        $current = count($this->modified_suscribers) ? $this->modified_suscribers : $this->suscribers;
        if (array_key_exists($eventName, $current)){
            foreach ($current[$eventName] as $listener){
                (new $listener)->handle($this->event);
            }
            return $this;
        }
        throw new \Exception("Event {$eventName} hasn't been registered");
    }

    public function ignore($listener)
    {
        $this->modifySuscribers($listener);
        return $this;
    }

    /**
     * Summary of ignoreIf
     * @param mixed $mixed
     * if $mixed is a callable must return a value can evaluate as true or false
     * @param mixed $event
     * @return static
     */
    public function ignoreIf(mixed $condition, $listener)
    {
        $is_callable = is_callable($condition) ? call_user_func($condition, $this->event) : $condition;
        if ($is_callable){
            $this->modifySuscribers($listener);
        }
        return $this;
    }

    /**
     * Summary of dispatchOnly
     * @param mixed $listener
     * @throws \Exception
     * @return mixed
     */
    public function dispatchOnly($listener)
    {
        $eventName = $this->getEventName();
        if ($this->suscribers[$eventName]){
            $needed = array_search($listener, $this->suscribers[$eventName]);
            if ($needed === false){
                throw new \Exception("Listener not registred");
            }
            $ev = new $this->suscribers[$eventName][$needed];
            return (new $ev)->handle($this->event);
        }
    }

    /**
     * Summary of dispatchIf
     * @param mixed $condition
     * @return void
     */
    public function dispatchIf(mixed $condition)
    {
        $avaliated = is_callable($condition) ? call_user_func($condition, $this->event) : $condition;
        if ($avaliated){
            $this->dispatch();
        }
    }

    /**
     * Summary of suscribe
     * @param mixed $listener
     * @throws \Exception
     * @return static
     */
    public function suscribe($listener)
    {
        $eventName = $this->getEventName();
        if (!array_key_exists($eventName, $this->suscribers)){
            throw new \Exception("Event {$eventName} hasn't been registered");
        }else{
            if (!in_array($listener,  $this->suscribers[$eventName])){
                $this->suscribers[$eventName][] = $listener;
            }
        }
        return $this;
    }

    public function unsuscribe($listener)
    {
        $eventName = $this->getEventName();
        if ($this->suscribers[$eventName]){
            foreach($this->suscribers[$eventName] as $key => $ev){
                if (new $ev instanceof $listener){
                    unset($this->suscribers[$eventName][$key]);
                }
            }
        }
        return $this;
    }

    private function modifySuscribers($listener)
    {
        $eventName = $this->getEventName();
        if ($this->suscribers[$eventName]){
            foreach ($this->suscribers[$eventName] as $key => $ev){
                if ($ev == $listener){
                    $this->modified_suscribers[$eventName] = array_slice($this->suscribers[$eventName], $key, 1);
                    $this->modified_suscribers[$eventName] = array_diff($this->suscribers[$eventName], $this->modified_suscribers[$eventName]);
                }
            }
        }
        return $this->modified_suscribers;
    }

    public function dispatch()
    {
        $this->publish();
    }

    private function getEventName()
    {
        $reflectionClass = new \ReflectionClass($this->event);
        return $reflectionClass->getName();
    }
}