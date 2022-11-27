<?php

namespace App\Controller;



class HomeController
{
   public static function index($app, $req, $rsp, $args)
   {
      $username = $_SESSION['username'];

      $firstLogin = isset($_SESSION['login']);
      unset($_SESSION['login']);


      $data = $app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'cust_country'
      ], [
         'cust_code',
         'cust_name',
         'cust_city',
         'cust_country'
      ]);
      // var_dump($data);
      $agents = $app->db->select('tbl_agents', ['agent_code', 'agent_name', 'working_agent', 'phone_agent']);

      $app->view->render($rsp, 'home.twig', [
         'username' => $_SESSION['username'],
         'data'   => $data,
         'agents' => $agents,
         'login' => true,
         'firstLogin' => $firstLogin
      ]);
   }

   public static function create($app, $req, $rsp, array $args)
   {

      $data = $req->getParsedBody();

      $lastData = $app->db->count('tbl_customer', 'cust_code');

      $lastData += 2;
      $cust_code = 'C' . str_pad($lastData, 5, '0', STR_PAD_LEFT);
      // return var_dump($cust_code);
      $reg = $args['tambah'];

      $app->db->insert('tbl_customer', [
         'CUST_CODE' => $cust_code,
         'cust_name' => $data['cust_name'],
         'cust_city' => $data['cust_city'],
         'cust_country' => $data['cust_country'],
         'agent_code' => $data['agent_code'],
      ]);

      $json_data = array(
         "draw" => intval($req->getParam('draw')),
      );

      echo json_encode($json_data);
   }



   public static function show($app, $req, $rsp, $args)
   {

      $data = $app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'cust_country'
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
         'cust_code', 'cust_name', 'cust_city', 'cust_country'
      ], $conditions);

      $data = array();

      if (!empty($customer)) {
         $no = $req->getParam('start') + 1;
         foreach ($customer as $c) {

            $datas['no'] = $no . '.';
            $datas['cust_name'] = $c['cust_name'];
            $datas['cust_city'] = $c['cust_city'];
            $datas['cust_country'] = $c['cust_country'];
            $datas['action'] = '
                                 <button type="button" class="btn btn-info item_detail" data="' . $c['cust_code'] . '"><i class="bi bi-info-circle-fill"></i></button> | 
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

      $json_data = array(
         "draw"            => intval($req->getParam('draw')),
      );

      echo json_encode($json_data);
   }

   public static function ubah_modal($app, $req, $rsp, $args)
   {
      $cust_code = $args['data'];



      $select = $app->db->select('tbl_customer', [
         'cust_code', 'cust_name', 'cust_city', 'cust_country', 'agent_code'
      ], [
         "cust_code" => $cust_code
      ]);
      return $rsp->withJson($select);

   }

   public static function ubah_data($app, $req, $rsp, $args)
   {

      $reg = $args['data'];
      // return var_dump($reg);
      $cust_code = $reg['cust_code'];
      $cust_name = $reg['cust_name'];
      $cust_city = $reg['cust_city'];
      $cust_country = $reg['cust_country'];
      $agent_code = $reg['agent_code'];

      $app->db->update('tbl_customer', [
         'cust_name' => $cust_name,
         'cust_city' => $cust_city,
         'cust_country' => $cust_country,
         'agent_code' => $agent_code
      ], [
         'cust_code' => $cust_code
      ]);
      // return var_dump($update);
      $json_data = array(
         "draw"            => intval($req->getParam('draw')),
      );

      echo json_encode($json_data);
   }


   public static function export($app, $req, $rsp, $args)
   {
      $data = $app->db->select('tbl_customer', ["[><]tbl_agents" => ["agent_code" => "agent_code"]], [
         'cust_code',
         'cust_name',
         'cust_city',
         'cust_country',
         'agent_name',
         'working_agent',
         'phone_agent'
      ]);


      $filename = "data" . date('Ymd') . ".xls";

      // Kodingam untuk export ke excel
      header("Content-type: application/vnd-ms-excel");
      header("Content-Disposition: attachment; filename=Data.xls");

      $app->view->render($rsp, 'export.twig', [
         'data' => $data
      ]);
   }

   public static function detail($app, $req, $rsp, $args)
   {
      $cust_code = $args['data'];



      $select = $app->db->select('tbl_customer', ["[><]tbl_agents" => ["agent_code" => "agent_code"]], [
         'cust_code',
         'cust_name',
         'cust_city',
         'cust_country',
         'agent_name',
         'working_agent',
         'phone_agent'
      ], [
         "cust_code" => $cust_code
      ]);

      return $rsp->withJson($select);
      // return $rsp->withJson($data);

   }
}
