<?php

namespace AGustavo87\WebCollector\Services;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Exception;
use AGustavo87\WebCollector\Services\HttpClient\{
    Client as HTTPClient, 
    Response
};

class DocumentManager
{
    protected ?DOMDocument $contents = null;
    protected DOMNodeList $result;

    public function __construct(
        protected Storage $store,
        protected HTTPClient $http
        ) {
    }

    public function getContentFromUrl($url): DOMDocument
    {
        $this->setHtml($this->http->getUrl($url)->body);
        return $this->contents;
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
            $this->http->setContext($context);
        }
        $responseData = $this->http->getResponseData($url);
        $uid = uniqid('page_');
        // We store it so later can be analized
        $this->store->put($uid.'.html', $responseData->body);
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
        $this->http->setContext($context);
        return $this;
    }

    public function getUrl($url, bool $setContents = true): Response
    {
        $response =  $this->http->getUrl($url);
        if ($setContents) $this->setHtml($response->body);
        return $response;
    }

    public function getUrlData($url = null): Response
    {
        return $this->http->getUrlData($url);
    }

    /**
     * Returns 'body', 'cookies', and 'headers' of the response of a url.
     *
     * @param string $url
     * @return Response
     */
    public function getResponseData($url): Response
    {
        $response =  $this->http->getResponseData($url);
        $this->setHtml($response->body);
        return $response;
    }

    public function getCookieData($headers)
    {
        return $this->http->getCookieData($headers);
    }
}
