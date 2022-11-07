<?php

namespace AGustavo87\WebCollector;

class Session
{
    /**
     * Construct a new Session Manager
     *
     * @param string $path where to save session data
     */
    public function __construct($path)
    {
        session_start([
            'save_path' => $path
        ]);
    }

    public function set($key, $value): self
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function get($key): ?string
    {
        return $_SESSION[$key] ?? null;
    }
}