<?php

namespace AGustavo87\WebCollector;

class HTMLView
{
    protected string $name;
    protected array $variables = [];

    public function __construct(string $name, array $variables = [])
    {
        $this->name = $name;
        $this->with($variables);
    }

    public function with(array $variables): self
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    public function build($variables = [])
    {
        $variables = array_merge($this->variables, $variables);
        extract($variables);
        ob_start();
        include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR."$this->name.php";
        return ob_get_clean();
    }
}