<?php defined('BASEPATH') or exit('No direct script access allowed');

class Tracking extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        // $this->load->model('Grv_modal');
        // $this->load->helpers('grv');
        // $this->load->helpers('exceedmatch');
    }

    function load_user_location()
    {
        $sql = $this->db->query("SELECT
                a.lat AS lat,
                a.LONG AS lng,
                b.Ename1 AS content,
                b.Ename1 AS description,
                b.Ename1 AS title,
                max( a.created_date ) AS c_date,
                a.created_date,
                GROUP_CONCAT( a.lat ) AS lat_group,
                GROUP_CONCAT( a.LONG ) AS lng_group,
            
            IF
                (a.created_date > DATE_SUB(NOW(), INTERVAL '1' HOUR), 'Online', 'Offline' ) AS is_online,
                e.counterID,
            
            
            
                if(f.counterCode is null,'N/A',f.counterCode ) AS device_id 
            FROM
                `tab_locations` a
                LEFT OUTER JOIN srp_employeesdetails b ON a.user_id = b.EIdNo
                LEFT OUTER JOIN srp_erp_warehouse_users e on a.user_id = e.userID
                LEFT OUTER JOIN srp_erp_pos_counters f ON e.counterID = f.counterID 
                where   date(a.created_date)= date(now())
            group by f.counterCode  desc
        ");
              
        header('Content-Type:application/json');
      //  $this->response->setHeader('Content-Type', 'application/json');
        echo json_encode($sql->result_array());


       //   [{"lat":"6.9326517","lng":"79.84416","content":"Admin","description":"Admin","created_date":"2023-07-07 06:12:12"}]

          //  where created_date= now()

         // [{
      //           "lat": 35.6606376 + (0.01 * intervalNumber),
      //               "lng": -80.5048653 + (0.1 * intervalNumber),
      //               "content": "bca",
      //           "description":"first marker"
      //       }, {
      //           "lat": 42.6799504 + (0.01 * intervalNumber),
      //               "lng": -36.4949205 - (0.1 * intervalNumber),
      //               "content": "abc",
      //           "description": "second marker"
      //       }]
    }
}
