<?php

namespace Microshard\Application\Frontend;

abstract class Composition
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var Block
     */
    private $main;

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

    /**
     * @param Block $block
     * @return Composition
     */
    protected function setMainBlock(Block $block): self
    {
        $this->main = $block;
        return $this;
    }

    /**
     * @return Block
     */
    public function getMainBlock(): Block
    {
        return $this->main;
    }

    public abstract function build();

    /**
     * @return string
     */
    public abstract function toHtml(): string;
}