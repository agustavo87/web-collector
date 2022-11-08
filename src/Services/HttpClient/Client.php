<?php

namespace AGustavo87\WebCollector\Services\HttpClient;

class Client
{
    protected $context;
    protected Response $lastResponse;

    public function __construct(array $context)
    {
        $this->context = stream_context_create($context);
    }

    public function normalizeUrl($url): string
    {
        if (strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }
        return $url;
    }

    /**
     * Set context to be used in http requests
     *
     * @param resource|array $context - A resouce or a context options to create a stream context
     * @return self
     */
    public function setContext($context): self
    {
        if(!is_resource($context)) {
            if(!is_array($context)) {
                throw new \Exception('Document Manager: A resource or array resource context options required to operate with requests');
            }
            $context = stream_context_create($context);
        }
    
        $this->context = $context;
        return $this;
    }

    public function getUrl($url): Response
    {
        $url = $this->normalizeUrl($url);
        $stream = fopen($url, 'r', false, $this->context);
        $body = stream_get_contents($stream);
        $headers = $this->parseHeaders($http_response_header);
        $cookies = $this->getCookieData($headers);
        fclose($stream);
        $this->lastResponse = new Response($headers, $body, $cookies);
        return $this->lastResponse;
    }

    public function getUrlData($url = null): Response
    {
        return $url ? $this->getUrl($url) : $this->lastResponse;
    }

    protected function parseHeaders($headers)
    {
        $orderedHeaders = [];
        $orderedHeaders[] = array_shift($headers); // take Protocol and status
        foreach ($headers as $headerData) {
            $headerData = explode(':', $headerData,2);
            if(count($headerData) > 1) {
                [$name, $value] = $headerData;
                $name = trim($name);
                $value = trim($value);
                if(isset($orderedHeaders[$name])) {
                    if (!is_array($orderedHeaders[$name])) {
                        $orderedHeaders[$name] = [$orderedHeaders[$name]];
                    }
                    $orderedHeaders[$name][] = $value;
                } else {
                    $orderedHeaders[$name] = $value;
                }
            } else {
                $orderedHeaders[] = trim($headerData[0]);
            }
        }
        return $orderedHeaders;
    }

    /**
     * Returns 'body', 'cookies', and 'headers' of the response of a url.
     *
     * @param string $url
     * @return Response
     */
    public function getResponseData($url): Response
    {
        return $this->getUrl($url);
    }

    public function getCookieData($headers)
    {
        if(!key_exists('Set-Cookie', $headers)) return [];
        $cookiesData = !is_array($headers['Set-Cookie']) ? [$headers['Set-Cookie']] : $headers['Set-Cookie'];
        
        $cookies = [];
        foreach ($cookiesData as $cookieData) {
            $cookieData = explode(';', $cookieData);
            $cookieData = array_map(fn ($c) => explode('=', $c, 2), $cookieData );
            [$cookieName, $cookieValue] = array_shift($cookieData);
            $cookieName = trim($cookieName);
            $cookieValue = trim($cookieValue);
            $composedCookie = [
                'name' => $cookieName,
                'value' => $cookieValue
            ];
            foreach ($cookieData as $data) {
                if(count($data) > 1) {
                    [$name, $value] = $data;
                    $composedCookie[trim($name)] = trim($value);
                } else {
                    if (!key_exists('params', $composedCookie)) $composedCookie['params'] = [];
                    $composedCookie['params'][] = trim($data[0]);
                }
            }
            if(!isset($cookies[$cookieName])) $cookies[$cookieName] = [];
            $cookie[$cookieName][] = $composedCookie;
        }
        return $cookie;
    }
}
