<?php

namespace Application;

use Application\Exception\SystemException;
use Closure;


class Container
{

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Closure[]
     */
    protected $serviceDefinitions = [];

    /**
     * @var array
     */
    protected $hooks = [];

    /**
     * @var array
     */
    protected $services = [];

    /**
     * Container constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @param string $name
     * @param Closure $constructor
     * @return $this
     */
    public function addServiceDefinition(string $name, Closure $constructor): self
    {
        $this->serviceDefinitions[$name] = $constructor;
        return $this;
    }

    /**
     * @param string $serviceName
     * @param Closure $callback
     * @return Container
     */
    public function addServiceHook(string $serviceName, Closure $callback): self
    {
        if (!isset($this->hooks[$serviceName])){
            $this->hooks[$serviceName] = [];
        }
        $this->hooks[$serviceName][] = $callback;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $service
     * @return $this
     */
    private function addService(string $name, $service): self
    {
        $this->services[$name] = $service;
        if (isset($this->hooks[$name])){
            foreach ($this->hooks[$name] as $callback){
                $callback($service);
            }
        }
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     * @throws SystemException
     */
    public function getService($name)
    {
        if (!isset($this->services[$name])) {
            if (!isset($this->serviceDefinitions[$name])) {
                throw new SystemException('service not found');
            }
            $this->addService($name, $this->serviceDefinitions[$name]($this));
        }
        return $this->services[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasService($name): bool
    {
        return isset($this->serviceDefinitions[$name]) || isset($this->services[$name]);
    }
}