<?php

namespace Application\Events;


use Application\Container;
use Application\Exception\SystemException;

class EventManager
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $observers = [];

    /**
     * EventManager constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    private function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param string $eventName
     * @param string $observerName
     * @return $this
     */
    public function observeEvent(string $eventName, string $observerName): self
    {
        if (!isset($this->observers[$eventName])) {
            $this->observers[$eventName] = [];
        }
        $this->observers[$eventName][] = $observerName;
        return $this;
    }

    /**
     * @param string $eventName
     * @param Context $context
     * @return $this
     * @throws SystemException
     */
    public function dispatchEvent(string $eventName, Context $context): self
    {
        if (isset($this->observers[$eventName])) {
            foreach ($this->observers[$eventName] as $observerName) {
                $observer = $this->getContainer()->getService($observerName);
                if (!$observer) {
                    throw new SystemException('observer not found: ' . $observerName);
                }
                if (!method_exists($observer, 'handleEvent')){
                    throw new SystemException('invalid observer: ' . $observerName);
                }
                $observer->handleEvent($context);
            }
        }
        return $this;
    }
}