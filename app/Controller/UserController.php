<?php

namespace App\Controller;

class UserController
{
   public static function index($app, $req, $rsp, array $args)
   {

      $users = $app->db->select('user_details', [
         'user_id', 'username', 'first_name', 'last_name', 'gender'
      ]);
      return $app->view->render($rsp, 'login.twig', $users);
   }

   public static function login($app, $req, $rsp, array $args)
   {
      $user = $app->db->get('user_details', [
         'username', 'password'
      ], ['username' => $req->getParsedBody()['username']]);
      // die(var_dump($req->getParsedBody()));
      if ($user) {
         $password = $user['password'];
         if ($password == md5($req->getParsedBody()['password'])) {
            $_SESSION['username'] = $user['username'];
         } else {
            echo "asdf";
         }
         return $rsp->withRedirect('/index');
      } else {
         echo "sdfghjkaddhadv";
      }
      
   }

   public static function ShowRegister ($app, $req, $rsp, array $args) {
      return $app->view->render($rsp, 'register.twig') ;
   }


   public static function register($app, $req,  $rsp, array $args)
  {
    $data = $req->getParsedBody();
   //  die(var_dump($data));
    $user = $app->db->select('user_details', ['username'], [
      'username' => $data['username']
    ]);
    if (!$user) {
      $result = $app->db->insert('user_details', [
        'username' => $data['username'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'gender' => $data['gender'],
        'password' => md5($data['password'])
      ]);
      if (!$result) {
      //   $app->flash->addMessage('errors', 'gagal daftar ada sesuatu yang errors');
      } else {
        $_SESSION['username'] = $data['username'];
      }
      // dd($result);
    } else {
      // $app->flash->addMessage('errors', 'username telah terdaftar');
    }      
    return $rsp->withRedirect('/index');
  }
   
}
