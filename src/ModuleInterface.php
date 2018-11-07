<?php

namespace Application;


interface ModuleInterface
{
    /**
     * @param Container $container
     */
    public static function setup(Container $container);
}