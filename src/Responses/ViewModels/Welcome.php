<?php

namespace AGustavo87\WebCollector\Responses\ViewModels;

use AGustavo87\WebCollector\{Request, Router};
use AGustavo87\WebCollector\Responses\ViewResponse;

class Welcome extends ViewResponse
{
    protected Router $router;

    public function __construct(Request $request, Router $router)
    {
        parent::__construct($request, 'welcome');
        $this->router = $router;
        $this->with(['routes' => $this->composeLinks()]);
    }

    protected function composeLinks()
    {
        $routes = $this->router->getRoutes(['GET'])['GET'];
        return $routes;
    }
}