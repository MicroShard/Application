<?php

namespace Application;


interface ModuleInterface
{
    /**
     * @param Container $container
     */
    public function setup(Container $container);
}