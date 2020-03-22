<?php

$router = new \App\Core\Router();

$routes = [
    // News
    $router->map( 'GET', '/news', 'news#index'),
    $router->map( 'POST', '/news', 'news#create'),
    $router->map( 'GET', '/news/:id', 'news#search'),
    $router->map( 'POST', '/news/:id', 'news#update'),
    $router->map( 'DELETE', '/news/:id', 'news#delete'),
];

return $router->match();