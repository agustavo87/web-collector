<?php

namespace AGustavo87\WebCollector\Controllers;

use AGustavo87\WebCollector\App;
use AGustavo87\WebCollector\JSONResponse;
use AGustavo87\WebCollector\View;
use AGustavo87\WebCollector\Request;
use AGustavo87\WebCollector\Services\DocumentManager;
use AGustavo87\WebCollector\Services\Storage;
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

    public function scrap(): View
    {
        $url = $this->request->getParam('url', $this->defaults['url']);
        $tag = $this->request->getParam('tag', 'img');
        return new View('scrap', [
            'tags' => $this->DocumentManager->getTags($url, $tag),
            'url' => $url,
            'tag' => $tag
        ]);
    }

    public function meta(): View
    {
        $url = $this->request->getParam('url', $this->defaults['url']);
        return new View('meta', [
            'data' => $this->DocumentManager->getUrlData($url)->toArray(),
            'url' => $url,
        ]);
    }

    public function stored(): View
    {
        $uid = $this->request->getParam('uid', null);
        if($uid) {
            $body = $this->store->get($uid.'.html');
        }
        if(!$uid || !$body) {
            $body = 'Nothing found';
        }
        return new View('raw', compact('body'));
    }

    public function grab(): View
    {
        $url = $this->request->getParam('url', $this->defaults['url']);
        [$page_uid, $response] =  $this->DocumentManager->fetchAndStorePage($url);
        return new View('grab', [
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
            return new  JSONResponse([
                'error'  => 'The page_uid is required',
            ], 400);
        }
        $xpath = $this->request->getParam('xpath', null);
        if(!$page_uid) {
            return new  JSONResponse([
                'error'  => 'The xpath field is required',
            ], 400);
        }
        
        $elements = $this->DocumentManager
                         ->setContentFromUID($page_uid)
                         ->query($xpath)
                         ->getElementsArray();

        return new  JSONResponse([
            'page_uid'  => $page_uid,
            'xpath'     => $xpath,
            'elements'  => $elements
        ], 200);
    }

    public function analize(): View
    {
        return new View('analize', [
            'page_uid' => $this->request->getParam('page_uid', ''),
            'xpath' => $this->request->getParam('xpath', '/')
        ]);
    }
}