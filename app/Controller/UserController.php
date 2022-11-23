<?php

namespace App\Controller;

class UserController
{
   public static function index($app, $req, $rsp, array $args)
   {

      $users = $app->db->select('tbl_users', [
         'user_id', 'username', 'first_name', 'last_name', 'gender'
      ]);
      return $app->view->render($rsp, 'login.twig', $users);
   }

   public static function login($app, $req, $rsp, array $args)
   {
      $user = $app->db->get('tbl_users', [
         'username', 'password'
      ], ['username' => $req->getParsedBody()['username']]);
      
      if ($user) {
         $password = $user['password'];
         if ($password == md5($req->getParsedBody()['password'])) {
            $_SESSION['username'] = $user['username'];
         } else {
            return $app->view->render($rsp,'login.twig',[
               'password'=>true
            ]);        
         }
            
         $_SESSION['login'] = true;
         return $rsp->withRedirect('/index');
         

      } else {
         return $app->view->render($rsp,'login.twig',[
            'username'=>true
         ]);  
       }
   }




   public static function ShowRegister($app, $req, $rsp, array $args)
   {
      return $app->view->render($rsp, 'register.twig');
   }



   public static function register($app, $req,  $rsp, array $args)
   {
      $data = $req->getParsedBody();
      $user = $app->db->get('tbl_users', ['username'], [
         'username' => $data['username']
      ]);
      if (!$user) {
         $result = $app->db->insert('tbl_users', [
            'username' => $data['username'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'password' => md5($data['password'])
         ]);
         // die(var_dump($result));
         if (!$result) {
            //   $app->flash->addMessage('errors', 'gagal daftar ada sesuatu yang errors');
            return $app->view->render($rsp, 'register.twig',[
               'error'=>true
            ]);
         } else {
            $_SESSION['username'] = $data['username'];
         }
      } else {
         return $app->view->render($rsp, 'register.twig',[
            'error'=>true]);
         // return $rsp->withRedirect('/auth/register');
       }
      return $rsp->withRedirect('/login');
      //  else {
      //    // $app->flash->addMessage('errors', 'username telah terdaftar');
      //    return $app->view->render($rsp, 'register.twig',[
      //       'register'=>true
      //    ]);
      // }
   }
}
