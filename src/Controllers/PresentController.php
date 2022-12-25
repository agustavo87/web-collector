<?php

namespace AGustavo87\WebCollector\Controllers;

use AGustavo87\WebCollector\Responses\ViewResponse;
use AGustavo87\WebCollector\Responses\ViewModels\Welcome;

class PresentController extends Controller
{
    public function present(): ViewResponse
    {
        return new Welcome($this->request, $this->app->router());
    }
}