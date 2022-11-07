<?php

namespace AGustavo87\WebCollector;

class JSONResponse extends Response
{
    protected array $data;

    protected array $headers =  [
        'Content-Type' => 'application/json'
    ];

    public function __construct(array $data, int $status, array $headers = [])
    {
        parent::__construct($status, $headers);
        $this->data = $data;
    }

    public function build(): string
    {
        return json_encode($this->data);
    }

    public function commit(Request $request): static
    {
        $this->setMetaData();
        $body = $this->build();
        echo $body;
        return $this;
    }
}