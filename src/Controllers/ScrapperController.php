<?php

namespace AGustavo87\WebCollector\Controllers;

use AGustavo87\WebCollector\{App, Request};
use AGustavo87\WebCollector\Responses\{JSONResponse, ViewResponse};
use AGustavo87\WebCollector\Services\{DocumentManager, Storage};
use AGustavo87\WebCollector\Services\HttpClient\Client as HTTPClient;

class ScrapperController extends Controller
{
    protected Storage $store;
    protected DocumentManager $DocumentManager;
    protected $defaults;

    public function __construct(Request $request, App $app)
    {
        parent::__construct($request, $app);
        $this->request = $request;
        $this->store = new Storage('pages');
        $this->DocumentManager = new DocumentManager(
            $this->store,
            new HTTPClient([
                'http' => [
                    'method' => 'GET',
                    'user_agent' => 'Mozilla/5.0',
                    'follow_location' => 1,
                    'ignore_errors' => true,
                ]
            ])
        );
        $this->defaults = $app->config('scrapper.defaults');
    }

    public function scrap(): ViewResponse
    {
        $url = $this->request->getParam('url', $this->defaults['url']);
        $tag = $this->request->getParam('tag', 'img');
        return new ViewResponse($this->request, 'scrap', [
            'tags' => $this->DocumentManager->getTags($url, $tag),
            'url' => $url,
            'tag' => $tag
        ]);
    }

    public function meta(): ViewResponse
    {
        $url = $this->request->getParam('url', $this->defaults['url']);
        return new ViewResponse($this->request, 'meta', [
            'data' => $this->DocumentManager->getUrlData($url)->toArray(),
            'url' => $url,
        ]);
    }

    public function stored(): ViewResponse
    {
        $uid = $this->request->getParam('uid', null);
        if($uid) {
            $body = $this->store->get($uid.'.html');
        }
        if(!$uid || !$body) {
            $body = 'Nothing found';
        }
        return new ViewResponse($this->request, 'raw', compact('body'));
    }

    public function grab(): ViewResponse
    {
        $url = $this->request->getParam('url', $this->defaults['url']);
        [$page_uid, $response] =  $this->DocumentManager->fetchAndStorePage($url);
        return new ViewResponse($this->request, 'grab', [
            'data' => [
                'cookie' => $response->cookies,
                'headers' => $response->headers
            ],
            'url' => $url,
            'page_uid'   => $page_uid,
        ]);
    }

    /**
     * This analizes with XPath a stored page
     *
     * @return JSONResponse
     */
    public function xpath(): JSONResponse
    {
        $page_uid = $this->request->getParam('page_uid', null);
        if(!$page_uid) {
            return new  JSONResponse($this->request, [
                'error'  => 'The page_uid is required',
            ], 400);
        }
        $xpath = $this->request->getParam('xpath', null);
        if(!$page_uid) {
            return new  JSONResponse($this->request, [
                'error'  => 'The xpath field is required',
            ], 400);
        }
        
        $elements = $this->DocumentManager
                         ->setContentFromUID($page_uid)
                         ->query($xpath)
                         ->getElementsArray();

        return new  JSONResponse($this->request, [
            'page_uid'  => $page_uid,
            'xpath'     => $xpath,
            'elements'  => $elements
        ], 200);
    }

    public function analize(): ViewResponse
    {
        return new ViewResponse($this->request, 'analize', [
            'page_uid' => $this->request->getParam('page_uid', ''),
            'xpath' => $this->request->getParam('xpath', '/')
        ]);
    }
}