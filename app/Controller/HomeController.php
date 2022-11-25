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

      $data = $req->getParsedBody();

      $lastData = $app->db->count('tbl_customer', 'cust_code');
      
      $lastData += 5;
      $cust_code = 'C' . str_pad($lastData, 5, '0', STR_PAD_LEFT);
      // return var_dump($cust_code);
      $reg = $args['tambah'];
      // $app->db->debug()->insert('tbl_customers', $reg
      // );
      // return var_dump($reg);
      $ins = $app->db->insert('tbl_customer', [
         'CUST_CODE' => $cust_code,
         'cust_name' => $data['cust_name'],
         'cust_city' => $data['cust_city'],
         'working_area' => $data['working_area']
      ]);

      // return var_dump($ins);
      $json_data = array(
         "draw" => intval($req->getParam('draw')),
      );

      echo json_encode($json_data);
      // return $rsp->withRedirect('/index');
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
                                 <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#Detail"><i class="bi bi-info-circle-fill"></i></button> | 
                                 <button type="button" class="btn btn-warning item_edit" data="' . $c['cust_code'] . '"><i class="bi bi-pencil-square"></i></button> |
                                 <button type="button" class="btn btn-danger item_hapus" data="' . $c['cust_code'] . '"><i class="bi bi-trash-fill"></i></button>';


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


   public static function delete($app, $req, $rsp, $args)
   {
      $id = $args['data'];


      $app->db->delete('tbl_customer', [
         "cust_code" => $id
      ]);

      // return $rsp->withJson($del);
      $json_data = array(
         "draw"            => intval($req->getParam('draw')),
      );

      echo json_encode($json_data);
   }

   public static function ubah_modal($app, $req, $rsp, $args)
   {
      $cust_code = $args['data'];



      $select = $app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'working_area'
      ]);
      return $rsp->withJson($select);
      // return $rsp->withJson($data);

   }

   public static function ubah_data($app, $req, $rsp, $args)
   {

      $reg = $args['data'];
      // return var_dump($reg);
      $cust_code = $reg['cust_code'];
      $cust_name = $reg['cust_name'];
      $cust_city = $reg['cust_city'];
      $working_area = $reg['working_area'];

      $app->db->update('tbl_customer', [
         'cust_name' => $cust_name,
         'cust_city' => $cust_city,
         'working_area' => $working_area,
      ], [
         'cust_code' => $cust_code
      ]);
      $json_data = array(
         "draw"            => intval($req->getParam('draw')),
      );

      echo json_encode($json_data);
   }

   // public static function detail($app, $req, $rsp, $args)
   // {
   //   $id = $args['id'];
   //   $data = $app->db->get('customer', '*', ['CUST_CODE' => $id]);
   // //   return $app->get('renderer')->render($rsp, 'see.phtml', array('data' => $data));
   // }

   public static function export($app, $req, $rsp, $args)
   {
      $data =$app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'working_area'
      ]);


      $filename = "data" . date('Ymd') . ".xls";

      // Kodingam untuk export ke excel
      header("Content-type: application/vnd-ms-excel");
      header("Content-Disposition: attachment; filename=Data.xls");

      $app->view->render($rsp, 'export.twig',[
         'data' => $data
      ]);
   }
   
}
