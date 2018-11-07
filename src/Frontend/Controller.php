<?php

namespace Application\Frontend;


class Controller
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @param Layout $layout
     */
    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return Layout
     */
    protected function getLayout(): Layout
    {
        return $this->layout;
    }
}