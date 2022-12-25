<?php

namespace AGustavo87\WebCollector\Responses;

use AGustavo87\WebCollector\Request;

class NotFoundResponse extends Response
{
    public function __construct(Request $request, ?string $redirect = null)
    {
        parent::__construct($request, 404);
        if ($redirect) {
            $this->headers = ['Location' => $redirect];
        }
    }
}