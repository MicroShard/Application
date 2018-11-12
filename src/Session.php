<?php

namespace Microshard\Application;

class Session
{
    const MESSAGE_NOTICE = 'notice';
    const MESSAGE_SUCCESS = 'success';
    const MESSAGE_ERROR = 'error';

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
    public function remove(string $key): self
    {
        unset($_SESSION[$key]);
        return $this;
    }

    /**
     * @param string $message
     * @param null|string $type
     * @return Session
     */
    public function addMessage(string $message, ?string $type = self::MESSAGE_NOTICE): self
    {
        $messages = $this->get('frontend_messages');
        if (!$messages) {
            $messages = [];
        }
        $messages[] = ['type' => $type, 'message' => $message];

        $this->set('frontend_messages', $messages);
        return $this;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        $messages = $this->get('frontend_messages');
        return $messages ?? [];
    }

    /**
     * @return Session
     */
    public function clearMessages(): self
    {
        $this->set('frontend_messages', []);
        return $this;
    }
}