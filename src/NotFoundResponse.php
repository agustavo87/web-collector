<?php

namespace AGustavo87\WebCollector;

class NotFoundResponse extends Response
{
    public function __construct(?string $redirect = null)
    {
        parent::__construct(404);
        if ($redirect) {
            $this->headers = ['Location' => $redirect];
        }
    }
}