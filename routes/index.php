<?php

require_once 'libs/Request.php';
require_once 'libs/Route.php';

$router = new Route(new Request());
$router->get('/', function(){
    return "hello world";
});

$router->post('/login', function($request){
    $auth = new \App\Controller\AuthController();
    return $auth->login($request[0]->getBody());
});

$router->post('/registration', function($request) {
    $auth = new \App\Controller\AuthController();
    return $auth->registration($request[0]->getBody());
});

