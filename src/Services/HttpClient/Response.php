<?php

namespace AGustavo87\WebCollector\Services\HttpClient;

use AGustavo87\WebCollector\Arrayable;

class Response implements Arrayable
{
    public function __construct(
        public array $headers = [], 
        public string $body = '',
        public array $cookies = []) {
    }

    public function toArray(): array
    {
        return [
            'headers' => $this->headers,
            'body'  => $this->body,
            'cookies' => $this->cookies
        ];
    }
}