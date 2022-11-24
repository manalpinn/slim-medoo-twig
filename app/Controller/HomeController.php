<?php

namespace App\Controller;



class HomeController
{
   // public static function index($app, $req, $rsp, array $args)
   // {
   //    $data = $app->db->select('tbl_customer', [
   //       'cust_code', 'cust_name', 'cust_city', 'working_area'
   //    ]);

   //    $firstLogin = isset($_SESSION['login']);
   //    unset($_SESSION['login']);

   //    return $app->view->render($rsp, 'home.twig', [
   //       "data" => $data,
   //       'login' => true,
   //       'firstLogin' => $firstLogin

   //    ]);
   // }

   public static function index($app, $req, $rsp, $args)
   {
      $username = $_SESSION['username'];

      $id = $app->db->get('tbl_users', '*', [
         "username" => $username
      ]);
      $data = $app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'working_area'
      ], [
         'cust_code',
         'cust_name',
         'cust_city',
         'working_area',
      ]);
      // var_dump($data);

      $app->view->render($rsp, 'home.twig', [
         'username' => $_SESSION['username'],
         'id'     => $id,
         'data'   => $data,
      ]);
   }

   public static function create($app, $req, $rsp, array $args)
   {

      $data = $app->db->insert('tbl_customer','tbl_agents',[
         'cust_code',
         'cust_name',
         'cust_city',
         'working_area',
         'agent_name',
         'phone_no'
      ]);

      return $rsp->withJson($data);

      $json_data = array(
         "draw" => intval($req->getParam('draw')),

      );

      echo json_encode($json_data);
   }



   public static function show($app, $req, $rsp, $args)
   {

      $data = $app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'working_area'
      ]);


      $columns = array(
         0 => 'cust_code',
      );

      $totaldata = count($data);

      $totalfiltered = $totaldata;
      $limit = $req->getParam('length');
      $start = $req->getParam('start');
      $order = $req->getParam('order');
      $order = $columns[$order[0]['column']];
      $dir = $req->getParam('order');
      $dir = $dir[0]['dir'];

      $conditions = [
         "LIMIT" => [$start, $limit]
      ];

      if (!empty($req->getParam('search')['value'])) {
         $search = $req->getParam('search')['value'];
         $conditions['tbl_customer.cust_name[~]'] = '%' . $search . '%';
      }

      $customer = $app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'working_area'
      ], $conditions);

      $data = array();

      if (!empty($customer)) {
         $no = $req->getParam('start') + 1;
         foreach ($customer as $c) {

            $datas['no'] = $no . '.';
            $datas['cust_name'] = $c['cust_name'];
            $datas['cust_city'] = $c['cust_city'];
            $datas['working_area'] = $c['working_area'];
            $datas['action'] = '
                                 <button type="button" class="btn btn-info item_hapus " data="' . $c['cust_code'] . '">View</button>
                                 <button type="button" class="btn btn-warning item_edit" data="' . $c['cust_code'] . '">Edit</button> 
                                 <button type="button" class="btn btn-danger item_hapus " data="' . $c['cust_code'] . '">Delete</button>';


            $data[] = $datas;
            $no++;
         }
      }

      $json_data = array(
         "draw"            => intval($req->getParam('draw')),
         "recordsTotal"    => intval($totaldata),
         "recordsFiltered" => intval($totalfiltered),
         "data"            => $data
      );

      echo json_encode($json_data);
   }
}
