<?php

namespace AGustavo87\WebCollector\Responses;

use AGustavo87\WebCollector\Request;

class RedirectResponse extends Response
{
    public function __construct(Request $request, string $url)
    {
        parent::__construct($request, 303, [
            'Location' => $url
        ]);
    }
}