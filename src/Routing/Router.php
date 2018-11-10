<?php

namespace Microshard\Application\Routing;


use Microshard\Application\Container;
use Microshard\Application\Exception\SystemException;
use Microshard\Application\Frontend\Response;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    const DEFAULT_ROUTE_404 = '404';
    const DEFAULT_ROUTE_500 = '500';
    const DEFAULT_ROUTE_INDEX = 'index';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * Router constructor.
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
     * @param string $name
     * @param Route $route
     * @return Route
     */
    public function addRoute(string $name, Route $route): Route
    {
        $this->routes[$name] = $route;
        return $route;
    }

    /**
     * @param string $name
     * @return Route|null
     */
    private function getRouteByName(string $name): ?Route
    {
        return (isset($this->routes[$name])) ? $this->routes[$name] : null;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Response $response
     * @throws SystemException
     */
    public function handleRequest(ServerRequestInterface $request, Response $response)
    {
        $dispatched = false;
        if ($request->getUri()->getPath() == '/' && ($route = $this->getRouteByName(self::DEFAULT_ROUTE_INDEX))) {
            $dispatched = true;
            $this->dispatch($route, $request, $response);
        } else {
            $dispatched = $this->checkRoutes($request, $response);
        }

        if (!$dispatched && ($route = $this->getRouteByName(self::DEFAULT_ROUTE_404))) {
            $this->dispatch($route, $request, $response);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param Response $response
     * @return bool
     * @throws SystemException
     */
    private function checkRoutes(ServerRequestInterface $request, Response $response): bool
    {
        $dispatched = false;
        $requestPath = $request->getUri()->getPath();
        foreach ($this->routes as $route) {
            if ($request->getMethod() != $route->getMethod()) {
                continue;
            }

            $routeParams = [];
            if ($route->match($requestPath,$routeParams)) {
                $dispatched = true;

                try {
                    $this->dispatch($route, $request, $response);
                } catch (\Exception $e) {
                    error_log($e);
                    if ($route = $this->getRouteByName(self::DEFAULT_ROUTE_500)) {
                        $this->dispatch($route, $request, $response);
                    }
                }
                break;
            }
        }
        return $dispatched;
    }

    /**
     * @param Route $route
     * @param ServerRequestInterface $request
     * @param Response $response
     * @return Router
     * @throws SystemException
     */
    private function dispatch(Route $route, ServerRequestInterface $request, Response $response): self
    {
        $controller = $this->getContainer()->getService($route->getControllerName());
        if (!method_exists($controller, $route->getActionName())) {
            throw new SystemException('invalid route');
        }

        $controller->{$route->getActionName()}($request, $response);
        return $this;
    }
}