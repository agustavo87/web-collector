<?php

namespace AGustavo87\WebCollector\Controllers;

use AGustavo87\WebCollector\Responses\ViewResponse;

use AGustavo87\WebCollector\ViewModels\Welcome as WelcomeView;

class PresentController extends Controller
{
    public function present(): ViewResponse
    {
        $view = new WelcomeView($this->app->router());
        return $view;
    }
}