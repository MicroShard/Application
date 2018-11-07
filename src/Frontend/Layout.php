<?php

namespace Application\Frontend;


use Application\Container;
use Application\Exception\SystemException;

class Layout
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $blockDefinitions = [];

    /**
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
     * @param string $className
     * @return Layout
     */
    public function addBlockDefinition(string $name, string $className): self
    {
        $this->blockDefinitions[$name] = $className;
        return $this;
    }

    /**
     * @param string $name
     * @return Block
     * @throws SystemException
     */
    public function getBlock(string $name): Block
    {
        if (!isset($this->blockDefinitions[$name])) {
            throw new SystemException('unknown block: ' . $name);
        }

        $className = $this->blockDefinitions[$name];
        if (!class_exists($className)){
            throw new SystemException('block class not found: ' . $className);
        }

        return new $className($this);
    }

    /**
     * @param string $module
     * @param string $templatePath
     * @return string
     */
    public function getFullTemplatePath(string $module, string $templatePath): string
    {
        $templateDirParamName = sprintf('module.%s.template_dir', $module);
        if ($this->getContainer()->getConfiguration()->has($templateDirParamName)) {
            $templateDir = $this->getContainer()->getConfiguration()->get($templateDirParamName);
        } else {
            $dirParamName = sprintf('module.%s.dir', $module);
            $moduleDir = $this->getContainer()->getConfiguration()->get($dirParamName);
            $templateDir = $moduleDir . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates';
        }

        return rtrim($templateDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
            . ltrim($templatePath, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $name
     * @return Composition
     * @throws SystemException
     */
    public function getComposition(string $name): Composition
    {
        return $this->getContainer()->fabricate($name);
    }
}