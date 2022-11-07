<?php

namespace AGustavo87\WebCollector\Controllers;

use AGustavo87\WebCollector\App;
use AGustavo87\WebCollector\Request;

abstract class Controller
{
    protected Request $request;
    protected App $app;

    public function __construct(Request $request, App $app)
    {
        $this->request = $request;
        $this->app = $app;
    }
}