<?php

namespace App\Controller;



class HomeController
{
   public static function index($app, $req, $rsp, array $args)
   {
      $data = $app->db->select('tbl_customer', [
         'cust_code','cust_name', 'cust_city', 'working_area'
      ]);

      $firstLogin = isset($_SESSION['login']);
      unset($_SESSION['login']);

      return $app->view->render($rsp, 'home.twig', [
         "data" => $data,
         'login' => true,
         'firstLogin' => $firstLogin

      ]);
   }

   public static function create($app, $req, $rsp, array $args)
   {
      $data = $req->getParsedBody();
      $customer = $app->db->get('tbl_customer', ['cust_name'], [
         'cust_name' => $data['cust_name']
      ]);

      if (!$customer) {
         $lastData = $app->db->count("tbl_customer", "CUST_CODE");

         $lastData += 1;
         $cust_code = 'C' . str_pad($lastData, 5, '0', STR_PAD_LEFT);

         $result = $app->db->insert('tbl_customer', [
            'cust_code' => $cust_code,
            'cust_name' => $data['cust_name'],
            'cust_city' => $data['cust_city'],
            'working_area' => $data['working_area'],
         ]);

         if ($result) {
            $res = [
               'status' => 200,
               'data' => [
                  'cust_name' => $data['cust_name'],
                  'cust_city' => $data['cust_city'],
                  'working_area' => $data['working_area'],
               ],
               'message' => 'Student Created Successfully'
            ];
            echo json_encode($res);
            return;

            var_dump($res);
         } else {
            $res = [
               'status' => 500,
               'message' => 'Student Not Created'
            ];
            echo json_encode($res);
            return;
         }
      } else {
         $res = [
            'status' => 500,
            'message' => 'Data sudah ada'
         ];
         echo json_encode($res);
         return;
      }
   }
}
