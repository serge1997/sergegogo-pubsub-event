<?php
namespace SergeGogoEvent\Event;

abstract class PubSubEvent
{
    public function __construct()
    {
        $this->watchingBooted();
    }

    public function booted()
    {
    }

    private function watchingBooted()
    {
        $childClass = static::class;
        $reflection = new \ReflectionClass($childClass);
        $method = $reflection->getMethod('booted');
        $method->invoke($this);
    }
}