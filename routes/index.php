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



// categories routes
$router->get('/categories', function() {
    $categories = new \App\Controller\CategoryController();
    return $categories->index();
});
$router->post('/categories/show', function($request) {
    $categories = new \App\Controller\CategoryController();
    return $categories->show($request[0]->getBody());
});
$router->post('/categories/store', function($request) {
    $categories = new \App\Controller\CategoryController();
    return $categories->store($request[0]->getBody());
});
$router->post('/categories/update', function($request) {
    $categories = new \App\Controller\CategoryController();
    return $categories->updateData($request[0]->getBody());
});
$router->post('/categories/delete', function($request) {
    $categories = new \App\Controller\CategoryController();
    return $categories->destroy($request[0]->getBody());
});
// end of categories


// products
$router->get('/products/index', function() {
    $products = new \App\Controller\ProductController();
    return $products->index();
});
$router->post('/products/store', function($request) {
    $products = new \App\Controller\ProductController();
    return $products->store($request[0]->getBody());
});
$router->post('/products/show', function($request) {
    $products = new \App\Controller\ProductController();
    return $products->show($request[0]->getBody());
});
$router->post('/products/update', function($request) {
    $products = new \App\Controller\ProductController();
    return $products->updateData($request[0]->getBody());
});
$router->post('/products/delete', function($request) {
    $categories = new \App\Controller\ProductController();
    return $categories->destroy($request[0]->getBody());
});
// end products




