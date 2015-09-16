<?php

namespace Presentation\Grids\Demo;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require __DIR__ . '/../bootstrap.php';

# Use only for web
$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

$controller = new Controller;
$method = str_replace('/', '', $_SERVER['SCRIPT_NAME'])?:'index';
echo $controller->{$method}();
