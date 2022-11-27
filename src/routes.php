<?php

use App\Controller\HomeController;
use App\Controller\UserController;
use App\Middleware\Auth;
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
    $app->get('/log', function ( $req, $rsp, array $args){
        return  $this->view->render($rsp, 'login.twig',['register'=>true] ,$args);
    });

    
    $app->get('/logout', function ($req, $rsp, array $args){
        session_destroy();
        return $rsp->withRedirect('/');
    });



    $app->get('/reg', function ($req, $rsp, array $args){
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
    })->add(new Auth());


    $app->post('/create', function ($req, $rsp, array $args){
        $tambah = $req->getParsedBody();
        // return var_dump($tambah);
        return HomeController::create($this, $req, $rsp,  [
            'tambah' => $tambah
        ]);    });

    $app->get('/show', function ($req, $rsp, array $args){
        return HomeController::show($this, $req, $rsp, $args);
    });

    $app->get('/export', function ($req, $rsp, array $args){
        return HomeController::export($this, $req, $rsp, $args);
    });

    $app->post('/delete', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();

        return HomeController::delete($this, $request, $response,  [
            'data' => $data
        ]);
    });
    
    
    // $app->get('/detail', function ($req, $rsp, array $args){
    //     return HomeController::detail($this, $req, $rsp, $args);
    // });

    $app->get('/{cust_code}/select', function (Request $request, Response $response, array $args) use ($container) {
        $data = $args['cust_code'];
        
        return HomeController::ubah_modal($this, $request, $response,  [
            'data' => $data
        ]);
    });

   
    $app->post('/ubah', function (Request $request, Response $response, array $args) use ($container) {
        $data = $request->getParsedBody();
        return HomeController::ubah_data($this, $request, $response,  [
            'data' => $data
        ]);
    });
    

    $app->get('/{cust_code}/detail', function (Request $request, Response $response, array $args) use ($container) {
        $data = $args['cust_code'];
        
        return HomeController::detail($this, $request, $response,  [
            'data' => $data
        ]);
    });
};
