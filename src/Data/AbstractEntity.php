<?php

namespace Microshard\Application\Data;

abstract class AbstractEntity
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     * @return AbstractEntity
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $data
     * @return AbstractEntity
     */
    public function addData(array $data): self
    {
        foreach ($data as $key => $value){
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function getValue(string $key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * @param string $key
     * @param $value
     * @return AbstractEntity
     */
    protected function setValue(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }
}