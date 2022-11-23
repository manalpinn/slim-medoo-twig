<?php

use App\Controller\HomeController;
use App\Controller\UserController;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();


    $app->get('/', function ($req, $rsp, array $args){
        return UserController::index($this, $req, $rsp, $args);
    });
    $app->post('/login', function ($req, $rsp, array $args){
        return UserController::login($this, $req, $rsp, $args);
    });
    $app->get('/login', function ( $req, $rsp, array $args){
        return  $this->view->render($rsp, 'login.twig',['register'=>true] ,$args);
    });

    $app->get('/logout', function ($req, $rsp, array $args){
        return  $this->view->render($rsp, 'home.twig',['logout'=>true] ,$args);
    });
    $app->post('/logout', function ($req, $rsp, array $args){
        session_destroy();
        return $rsp->withRedirect('/');
    });



    $app->get('/register', function ($req, $rsp, array $args){
        return UserController::showRegister($this, $req, $rsp, $args);
    });
    $app->post('/register', function ($req, $rsp, array $args){
        return UserController::register($this, $req, $rsp, $args);
    });

    // $app->post('/process', function ($req, $rsp, array $args){
    //     return UserController::($this, $req, $rsp, $args);
    // });


    
    $app->get('/index', function ($req, $rsp, array $args){
        return HomeController::index($this, $req, $rsp, $args);
    });


    $app->post('/create', function ($req, $rsp, array $args){
        return HomeController::create($this, $req, $rsp, $args);
    });
    

    
};
