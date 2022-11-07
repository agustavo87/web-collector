<?php

require_once 'vendor/autoload.php';

use AGustavo87\WebCollector\App;
use AGustavo87\WebCollector\Request;

$request = Request::fromCapture();
$app = new App($request);
$app->run()->commit($request);