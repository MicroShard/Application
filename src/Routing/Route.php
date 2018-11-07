<?php

namespace Application\Routing;

class Route
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $controllerName;

    /**
     * @var string
     */
    private $actionName;

    /**
     * Route constructor.
     * @param string $method
     * @param string $route
     * @param string $controllerName
     * @param string $actionName
     */
    public function __construct(string $method, string $route, string $controllerName, string $actionName)
    {
        $this->method = $method;
        $this->route = $route;
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return Route
     */
    public function setRoute(string $route): Route
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Route
     */
    public function setMethod(string $method): Route
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @return Route
     */
    public function setTarget(string $controllerName, string $actionName): Route
    {
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        return $this;
    }

    /**
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @param string $requestPath
     * @param array $matches
     * @return bool
     */
    public function match(string $requestPath, array &$matches = []): bool
    {
        return (bool) preg_match($this->getRoute(), $requestPath, $matches);
    }
}