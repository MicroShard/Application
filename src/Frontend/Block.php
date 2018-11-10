<?php

namespace Microshard\Application\Frontend;


use Microshard\Application\Exception\SystemException;

class Block
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var string
     */
    private $templateModule;

    /**
     * @var Block[]
     */
    private $children = [];

    /**
     * @param Layout $layout
     */
    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param string $templateModule
     * @param string $templatePath
     * @return Block
     */
    public function initTemplate(string $templateModule, string $templatePath): self
    {
        $this->templateModule = $templateModule;
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * @return Layout
     */
    protected function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * @return string
     * @throws SystemException
     */
    public function toHtml()
    {
        $html = '';
        if ($this->templateModule && $this->templatePath) {
            $fullTemplatePath = $this->getLayout()->getFullTemplatePath($this->templateModule, $this->templatePath);

            ob_start();
            include $fullTemplatePath;
            $html = ob_get_contents();
            ob_end_clean();
        } else {
            throw new SystemException('could not load template for block: ' . get_class($this));
        }

        return $html;
    }

    /**
     * @param string $alias
     * @param Block $block
     * @return Block
     */
    public function addChild(string $alias, Block $block): self
    {
        $this->children[$alias] = $block;
        return $this;
    }

    /**
     * @param string $alias
     * @return Block|null
     */
    public function getChild(string $alias): ?Block
    {
        return (isset($this->children[$alias])) ? $this->children[$alias] : null;
    }

    /**
     * @param string $alias
     * @return string
     * @throws SystemException
     */
    public function getChildHtml(string $alias): string
    {
        $child = $this->getChild($alias);
        return ($child) ? $child->toHtml() : '';
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function getUrl(string $path, array $params = []): string
    {
        $url = '/' . ltrim($path, '/');

        if (count($params) > 0) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * @param float $value
     * @return string
     */
    public function formatPrice(float $value): string
    {
        return number_format($value, 2, ',', '.') . ' €';
    }
}