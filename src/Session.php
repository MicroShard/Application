<?php

namespace Application;

class Session
{
    /**
     * @return Session
     */
    public function start(): self
    {
        session_start();
        return $this;
    }

    /**
     * @param string $key
     * @return null|mixed
     */
    public function get(string $key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * @param string $key
     * @param $value
     * @return Session
     */
    public function set(string $key, $value): self
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return Session
     */
    public function uns(string $key): self
    {
        unset($_SESSION[$key]);
        return $this;
    }
}