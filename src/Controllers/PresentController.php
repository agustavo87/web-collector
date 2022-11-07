<?php

namespace AGustavo87\WebCollector\Controllers;

use AGustavo87\WebCollector\View;
use AGustavo87\WebCollector\ViewModels\Welcome as WelcomeView;

class PresentController extends Controller
{
    public function present(): View
    {
        $view = new WelcomeView($this->app->router());
        return $view;
    }

    public function getLinks()
    {
        //
    }
}