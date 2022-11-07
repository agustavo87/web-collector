<?php

namespace AGustavo87\WebCollector;

class Router
{
    protected App $app;
    protected array $routes = [];

    public function __construct(App $app)
    {
        $this->app = $app;
    }
    
    /**
     * Supported methods
     */
    const HTTP_METHODS = ['GET', 'POST'];

    public function register(array $params)
    {
        $paths = array_filter(
            $params, 
            fn($key) => in_array($key, self::HTTP_METHODS), 
            ARRAY_FILTER_USE_KEY
        );
        foreach ($paths as $method => $path) {
            if (!key_exists($method, $this->routes)) {
                $this->routes[$method] = [];
            }
            $routeData = [
                'use' => $params['use'] ?? null,
                'call' => $params['call'] ?? null
            ];
            if( key_exists('title', $params)) {
                $routeData['title'] = $params['title'];
            }
            $this->routes[$method][$path] = $routeData;
        }
    }

    public function registerRoutes(array $routes)
    {
        foreach ($routes as $route) {
            $this->register($route);
        }
    }

    /**
     * Pass the Request to the corresponding route
     * Controller method or callable
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $method = $this->routes[$request->getMethod()];

        if (!key_exists($request->getMethod(), $this->routes)) {
            return $this->nullMethod();
        }

        $method = $this->routes[$request->getMethod()];
        
        if (!key_exists($request->getPath(), $method)) {
            return $this->pathNotFountd();
        }
        
        $params = $method[$request->getPath()];

        if($params['use'] == 'callable') {
            return $params['call']();
        }
        $controller = new $params['use']($request, $this->app);
        return call_user_func([$controller, $params['call']]);
    }

    protected function nullMethod()
    {
        return new NotFoundResponse();
    }

    protected function pathNotFountd()
    {
        return new NotFoundResponse();
    }

    /**
     * Get Filtered routes
     *
     * @param array $methods to filter
     * @param string|null $path to filter regular expression
     * @param string $title title to filter regular expression
     * @return void
     */
    public function getRoutes(
        array $methods = ['GET'],
        string $path = null,
        $title = "/.*/"
    ) {
        $routes = array_intersect_key($this->routes, array_flip($methods));
        if ($title) {
            $routes = array_map(function ($routesData) use($title) {
                return array_filter(
                    $routesData, 
                    function ($routeData) use ($title) {
                        if(!key_exists('title',  $routeData)) return false;
                        return preg_match($title, $routeData['title']);
                    }
                );
            }, $routes);
        }
        if ($path) {
            $routes = array_map(function ($routesData) use($path) {
                return array_filter(
                    $routesData,
                    function ($routeData, $routePath) use ($path) {
                        return preg_match($path, $routePath);
                    },
                    ARRAY_FILTER_USE_BOTH
                );
            }, $routes);
        }
        return $routes;
    }
}