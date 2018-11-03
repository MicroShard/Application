<?php

namespace Application\Frontend;


use Application\Routing\Router;

class Controller
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param Layout $layout
     * @param Router $router
     */
    public function __construct(Layout $layout, Router $router)
    {
        $this->layout = $layout;
        $this->router = $router;
    }

    /**
     * @return Layout
     */
    protected function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * @param string $path
     */
    protected function redirect(string $path)
    {
        $this->router->redirect($path);
    }
}