<?php

namespace AGustavo87\WebCollector;

class View extends Response
{
    protected HTMLView $view;

    public function __construct(string $name, array $variables = [], array $headers = [])
    {
        parent::__construct(200, $headers);
        $this->view = new HTMLView($name, $variables);
    }

    public function with(array $variables): self
    {
        $this->view->with($variables);
        return $this;
    }
    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function build($variables = [])
    {
        return $this->view->build($variables);
    }

    public function commit(Request $request): static
    {
        $body = $this->build();
        $this->setMetaData();
        echo $body;
        return $this;
    }
}