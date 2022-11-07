<?php

namespace AGustavo87\WebCollector\Services;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Exception;

class DocumentManager
{
    protected ?DOMDocument $contents = null;
    protected DOMNodeList $result;
    protected Storage $store;
    protected $context;

    public function __construct(Storage $storage)
    {
        $this->store = $storage;
        $this->context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'user_agent' => 'Mozilla/5.0',
                'follow_location' => 1,
                'ignore_errors' => true,
            ]
        ]);
    }

    public function getContentFromUrl($url): DOMDocument
    {
        $url = $this->normalizeUrl($url);
        $this->setHtml($this->getUrl($url)['body']);
        return $this->contents;
    }

    public function normalizeUrl($url): string
    {
        if (strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }
        return $url;
    }

    public function setContentFromPath($path): self
    {
        $this->contents = new DOMDocument();
        $this->contents->preserveWhiteSpace = FALSE;
        $str = $this->store->get($path);
        @$this->contents->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
        return $this;
    }

    public function setContentFromUID($uid): self
    {
        $this->setContentFromPath($uid.'.html');
        return $this;
    }

    public function fetchAndStorePage($url, ?array $context = null)
    {
        if($context) {
            $this->setContext(stream_context_create($context));
        }
        $responseData = $this->getResponseData($url);
        $uid = uniqid('page_');
        // We store it so later can be analized
        $this->store->put($uid.'.html', $responseData['body']);
        return [$uid, $responseData];
    }

    public function query($xpath, ?DOMNode $context = null): self
    {
        if($this->contents == null) {
            throw new Exception("Can't execute query without contents.");
        }
        $domXPath = new DOMXPath($this->contents);
        $this->result = $domXPath->query($xpath, $context);
        return $this;
    }

    public function getElementsArray(): array
    {
        return $this->DOMNodesToArray($this->result);
    }

    public function result(): DOMNodeList
    {
        return $this->result;
    }


    public function getTags($url, $tag)
    {
        return $this->DOMNodesToArray(
            $this->getContentFromUrl($url)
                 ->getElementsByTagName($tag)
        );
    }

    public function DOMNodesToArray(DOMNodeList $elements): array
    {
        $count = 0;
        $result = [];
        foreach ($elements as $node) {
            $value =  trim(preg_replace('/\s+/', ' ', $node->nodeValue));
            if(strlen($value)) {
                $result[$count]['value'] = $value;
            }
            $result[$count]['name'] =  $node->nodeName;
            if ($node->hasAttributes()) {
                foreach ($node->attributes as $name => $attr) {
                    $result[$count]['attributes'][$name] =  $attr->value;
                }
            }
            $count++;
        }
        return $result;
    }

    public function getAttribute($url, $attr, $domain = NULL)
    {
        $result = [];
        $elements = $this->getContentFromUrl($url)
                         ->getElementsByTagName('*');
        foreach ($elements as $node) {
            if ($node->hasAttribute($attr)) {
                $value = $node->getAttribute($attr);
                if ($domain) {
                    if (stripos($value, $domain) !== FALSE) {
                        $result[] = trim($value);
                    }
                } else {
                    $result[] = trim($value);
                }
            }
        }
        return $result;
    }

    public function setHtml(string $html)
    {
        $this->contents = new DOMDocument();
        $this->contents->preserveWhiteSpace = FALSE;
        @$this->contents->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

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

    public function getUrl($url, bool $setContents = true)
    {
        $url = $this->normalizeUrl($url);
        $stream = fopen($url, 'r', false, $this->context);
        $body = stream_get_contents($stream);
        $headers = $this->parseHeaders($http_response_header);
        fclose($stream);
        $this->urlResponse =  [
            'headers'   => $headers,
            'body'      => $body
        ];
        if ($setContents) $this->setHtml($body);
        return $this->urlResponse;
    }

    public function getUrlData($url = null)
    {
        return $url ? $this->getUrl($url) : $this->urlResponse;
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
     * @return array
     */
    public function getResponseData($url): array
    {
        $urlData = $this->getUrlData($url);
        $urlData['cookies']  = $this->getCookieData($urlData['headers']);
        return $urlData;
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
