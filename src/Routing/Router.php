<?php

namespace Application\Routing;

use Monolith\Core\Container;
use Monolith\Core\EventManager\Context;
use Monolith\Core\SystemException;
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
     * @throws SystemException
     */
    public function handleRequest(ServerRequestInterface $request)
    {
        $this->getContainer()->getService('events')
            ->dispatchEvent('router.init', new Context());

        $dispatched = false;
        if ($request->getUri()->getPath() == '/') {
            $dispatched = true;
            $this->dispatch($this->getRouteByName(self::DEFAULT_ROUTE_INDEX), $request);
        } else {
            $dispatched = $this->checkRoutes($request);
        }

        if (!$dispatched) {
            $this->dispatch($this->getRouteByName(self::DEFAULT_ROUTE_404), $request);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     * @throws SystemException
     */
    private function checkRoutes(ServerRequestInterface $request): bool
    {
        $dispatched = false;
        $requestPath = $request->getUri()->getPath();
        foreach ($this->routes as $route) {
            if ($request->getMethod() != $route->getMethod()) {
                continue;
            }

            if (preg_match($route->getRoute(), $requestPath, $matches)) {
                $dispatched = true;

                try {
                    $this->dispatch($route, $request);
                } catch (\Exception $e) {
                    error_log($e);
                    $this->dispatch($this->getRouteByName(self::DEFAULT_ROUTE_500), $request);
                }
                break;
            }
        }
        return $dispatched;
    }

    /**
     * @param Route $route
     * @param ServerRequestInterface $request
     * @return Router
     * @throws SystemException
     */
    private function dispatch(Route $route, ServerRequestInterface $request): self
    {
        $controller = $this->getContainer()->getService($route->getControllerName());
        if (!method_exists($controller, $route->getActionName())) {
            throw new SystemException('invalid route');
        }

        $controller->{$route->getActionName()}($request);
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function redirect(string $path)
    {
        $location = $this->getContainer()->getConfiguration()->get('base_url');
        $location = rtrim($location, '/') . '/' . ltrim($path, '/');

        header("Location: $location");
        die();
    }
}