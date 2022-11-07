<?php

namespace AGustavo87\WebCollector\ViewModels;

use AGustavo87\WebCollector\Router;
use AGustavo87\WebCollector\View;

class Welcome extends View
{
    protected Router $router;

    public function __construct(Router $router)
    {
        parent::__construct('welcome');
        $this->router = $router;
        $this->with(['routes' => $this->composeLinks()]);
    }

    protected function composeLinks()
    {
        $routes = $this->router->getRoutes(['GET'])['GET'];
        return $routes;
    }
}