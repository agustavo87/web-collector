<?php

namespace AGustavo87\WebCollector;

use AGustavo87\WebCollector\Services\Storage;

class App
{
    protected Request $request;
    protected Router $router;
    protected Configuration $config;
    protected Session $session;

    public function __construct(Request $request)
    {
        $this->config = new Configuration([
            dirname(__FILE__,2) . DIRECTORY_SEPARATOR . 'config',
            dirname(__FILE__,2) . DIRECTORY_SEPARATOR . 'config/sensible',
        ]);
        $this->request = $request;
        $this->router = new Router($this);
        $this->router->registerRoutes($this->config('routes'));
        $this->registerStores();
        $this->session = new Session(dirname(__FILE__,2) . DIRECTORY_SEPARATOR . 'storage/session');
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function config($path)
    {
        return ($this->config)($path);
    }

    protected function registerStores()
    {
        foreach ($this->config('storage.stores') as $store) {
            Storage::createDisk([
                'name'  => $store['name'],
                'root'  => $store['root']
            ]);
        }
    }

    public function run(): Response
    {
        return $this->router->handle($this->request);
    }
}
