<?php

require_once 'vendor/autoload.php';

use AGustavo87\WebCollector\{App, Request};

$app = new App(Request::fromCapture());
$app->run()->commit();