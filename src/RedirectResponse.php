<?php

namespace AGustavo87\WebCollector;

class RedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct(303, [
            'Location' => $url
        ]);
    }
}