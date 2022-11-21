<?php

namespace App\Controller;



class HomeController
{
   public static function index($app, $req, $rsp, array $args)
   {
      $data = $app->db->select('user_details', [
         'user_id', 'username', 'first_name', 'last_name', 'gender'
      ]);

      return $app->view->render($rsp, 'home.twig', [
         "data" => $data
      ]);
   }
}
