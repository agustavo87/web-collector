<?php

namespace AGustavo87\WebCollector;

class Response
{
    protected ?int $status = null;
    protected array $headers =  [];

    public function __construct(int $status = 200, array $headers = [])
    {
        $this->status = $status;
        $this->headers = array_merge($this->headers, $headers);
    }

    public function setMetaData()
    {
        $this->commitStatus();
        $this->commitHeaders();
    }

    protected function commitStatus()
    {
        if ($this->status) {
            http_response_code($this->status);
        }
    }

    protected function commitHeaders()
    {
        foreach ($this->headers as $name => $value) {
            header("$name:$value");
        }
    }

    public function commit(Request $request): static
    {
        $this->setMetaData();
        return $this;
    }
}