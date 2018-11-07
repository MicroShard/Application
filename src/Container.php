<?php

namespace Application;

use Application\Exception\SystemException;
use Closure;


class Container
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Closure[]
     */
    private $serviceDefinitions = [];

    /**
     * @var array
     */
    private $hooks = [];

    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array
     */
    private $factories = [];

    /**
     * @var Closure[]
     */
    private $patterns = [];

    /**
     * @var Closure[]
     */
    private $constructorPatterns = [];

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
     * @return Container
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
     * @return Container
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

    /**
     * @param string $name
     * @param Closure $closure
     * @return Container
     */
    public function addPattern(string $name, Closure $closure): self
    {
        $this->patterns[$name] = $closure;
        return $this;
    }

    /**
     * @param string $name
     * @return Closure
     * @throws SystemException
     */
    private function getPattern(string $name): Closure
    {
        if (!isset($this->patterns[$name])) {
            throw new SystemException('unknown pattern: ' . $name);
        }
        return $this->patterns[$name];
    }

    /**
     * @param string $name
     * @param Closure $closure
     * @return Container
     */
    public function addConstructorPattern(string $name, Closure $closure): self
    {
        $this->constructorPatterns[$name] = $closure;
        return $this;
    }

    /**
     * @param string $name
     * @return Closure
     * @throws SystemException
     */
    private function getConstructorPattern(string $name): Closure
    {
        if (!isset($this->constructorPatterns[$name])) {
            throw new SystemException('unknown constructor pattern: ' . $name);
        }
        return $this->constructorPatterns[$name];
    }

    /**
     * @param string $class
     * @param string $patternName
     * @return mixed
     * @throws SystemException
     */
    public function applyConstructorPattern(string $class, string $patternName)
    {
        $pattern = $this->getConstructorPattern($patternName);
        return $pattern($class, $this);
    }

    /**
     * @param $service
     * @param string $patternName
     * @return Container
     * @throws SystemException
     */
    public function applyPattern($service, string $patternName): self
    {
        $pattern = $this->getPattern($patternName);
        $pattern($service, $this);
        return $this;
    }

    /**
     * @param string $factoryName
     * @param Closure $factory
     * @return Container
     */
    public function addFactory(string $factoryName, Closure $factory): self
    {
        $this->factories[$factoryName] = $factory;
        return $this;
    }

    /**
     * @param string $factoryName
     * @return mixed
     * @throws SystemException
     */
    public function fabricate(string $factoryName)
    {
        if (!isset($this->factories[$factoryName])) {
            throw new SystemException('unknown factory: ' . $factoryName);
        }
        return $this->factories[$factoryName]($this);
    }
}