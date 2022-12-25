<?php

namespace AGustavo87\WebCollector\Responses;

class RedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct(303, [
            'Location' => $url
        ]);
    }
}