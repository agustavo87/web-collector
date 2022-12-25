<?php

namespace AGustavo87\WebCollector\Responses;

use AGustavo87\WebCollector\{HTMLView, Request};

class ViewResponse extends Response
{
    protected HTMLView $view;

    public function __construct(Request $request, string $name, array $variables = [], array $headers = [])
    {
        parent::__construct($request, 200, $headers);
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

    public function commit(): static
    {
        $body = $this->build();
        $this->setMetaData();
        echo $body;
        return $this;
    }
}