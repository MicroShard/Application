<?php

namespace Microshard\Application\Data;

class Collection implements \Iterator
{

    /**
     * @var array
     */
    private $items = [];

    /**
     * @return AbstractEntity[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param AbstractEntity $entity
     * @return Collection
     */
    public function addItem(AbstractEntity $entity): self
    {
        if (!in_array($entity, $this->items)) {
            $this->items[] = $entity;
        }
        return $this;
    }

    /**
     * @param AbstractEntity $item
     * @return $this
     */
    public function removeItem(AbstractEntity $item)
    {
        $index = array_search($item, $this->items);
        if ($index !== false) {
            unset($this->items[$index]);
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->getItems());
    }

    /**
     * @param int $index
     * @return AbstractEntity|null
     */
    public function getItemByIndex(int $index): ?AbstractEntity
    {
        return (isset($this->items[$index])) ? $this->items[$index] : null;
    }

    /**
     * @return AbstractEntity|bool
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * @return AbstractEntity|bool
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * @return int|null|string
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->key() != null;
    }

    public function rewind()
    {
        reset($this->items);
    }
}