<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'Pie/Users', ['path' => '/users'], function (RouteBuilder $routes) {
    $routes->fallbacks('DashedRoute');
});
