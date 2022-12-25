<?php

namespace AGustavo87\WebCollector\Responses;

use AGustavo87\WebCollector\Request;

class JSONResponse extends Response
{
    protected array $data;

    protected array $headers =  [
        'Content-Type' => 'application/json'
    ];

    public function __construct(Request $request, array $data, int $status, array $headers = [])
    {
        parent::__construct($request, $status, $headers);
        $this->data = $data;
    }

    public function build(): string
    {
        return json_encode($this->data);
    }

    public function commit(): static
    {
        $this->setMetaData();
        $body = $this->build();
        echo $body;
        return $this;
    }
}