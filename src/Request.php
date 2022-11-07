<?php

namespace AGustavo87\WebCollector;

class Request
{
    protected array $uri;
    protected array $getParams;
    protected array $headers;

    protected $type = 'GET';

    const DEFAULT_HOST = 'localhost:8000';

    const DEFAULT_SCHEMA = 'http';

    protected static array $defaultUriComponents = [
        'scheme' => 'http',
        'host' => 'localhost', 
        'port' => '8000', 
        'user' => null,
        'pass' => null,
        'path' => null,
        'query'  => null,
    ];

    protected function setGetParams(array $params)
    {
        $this->getParams = $params;
    }

    protected function setUri(array $uri)
    {
        $this->uri = $uri;
    }

    public static function fromCapture(): self
    {
        $request = new static();
        $request->capture();
        return $request;
    }

    public static function fromArray(array $params): static
    {
        $request = new static();
        $request->setType($params['type'] ?? 'GET');
        if ( isset($params['query']) ) {
            $queryArray = $params['query'];
            $params['query'] = http_build_query($params['query']);
            $request->setGetParams($queryArray);
        }
        $components = array_merge(self::$defaultUriComponents, $params);
        $request->setUri($components);
        return $request;
    }

    protected function setType($type)
    {
        $this->type = $type;
    }

    public function capture()
    {
        $this->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/');
        $this->type = $_SERVER['REQUEST_METHOD'];
        $this->getParams = array_merge($_GET, $_POST, json_decode(file_get_contents('php://input'), true) ?? []);
        $this->headers = getallheaders();
        return $this;
    }

    public function getPath(): string
    {
        return $this->uri['path'] ?? '/';
    }

    public function getParam($name, $default = null): ?string
    {
        return $this->getParams[$name] ?? $default;
    }

    public function getMethod()
    {
        return $this->type;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}