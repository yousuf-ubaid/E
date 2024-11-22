<?php

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_job_portal extends REST_Controller
{
    private $company_info;
    private $company_id = 0;
    private $user_name;
    private $post;
    private $token = null;
    private $user;
    private $user_id = null;
    var $common_data = array();

    function __construct()
    {
        parent::__construct();
   
        if (isset($_SERVER['HTTP_SME_API_KEY'])) {

            $tokenKey = $_SERVER['HTTP_SME_API_KEY'];

            $this->token = $_SERVER['HTTP_SME_API_KEY'];
            $this->load->model('Login_Model');
            $this->post = json_decode(file_get_contents('php://input'));
            $this->load->model('Api_erp_model');
            $this->load->model('Auth_mobileUsers_Model');
            $this->load->model('Double_entry_model');
            $this->load->model('session_model');
            $this->load->model('Mobile_leaveApp_Model');
            $this->load->model('ApproveOther_Model');
            $this->load->model('Employee_model');
            $this->load->model('Payment_voucher_model');
            $this->load->model('Receipt_reversale_model');
            $this->load->library('sequence');
            $this->load->library('Approvals_mobile');
            $this->load->library('JWT');
            $this->load->library('S3');
            $this->load->library('Approvals');
            $this->load->helpers('payable');
            $this->load->model('Srm_master_model'); 
            $this->load->model('Api_job_portal_model'); 

            $output['token'] = $this->jwt->decode($tokenKey, "token");

            $company_curr = $this->db->select('company_logo,company_default_currencyID AS local_currency, company_default_currency AS local_currency_code,
                             company_reporting_currencyID AS rpt_currency, company_reporting_currency AS rpt_currency_code, companyPrintAddress, default_segment, 
                             defaultTimezoneID,company_name,company_address1,company_address2,company_city,company_country')
                ->from('srp_erp_company')->where('company_id', $output['token']->Erp_companyID)->get()->row_array();

            $this->company_info = (object)array_merge((array)$output['token'], $company_curr);

            if ($this->company_info->name === '0') {
                return FALSE;
            }

            $this->setCompanyTimeZone( $company_curr['defaultTimezoneID'] );

            /*****************************************************************************************************************
             *  Update last access date
             *  Here the reason of loading the db2 because of above loaded model/library (in some) files load the company DB
             *****************************************************************************************************************/
            $db2 = $this->load->database('db2', TRUE);
            $last_access_date = date('Y-m-d H:i:s');
            $db2->where('company_id', $output['token']->Erp_companyID)->update('srp_erp_company', [
                'last_access_date'=> $last_access_date
            ]);
            $this->setDb();
            $this->init_user();
            $this->set_current_company();
            $this->set_response($output['token'], REST_Controller::HTTP_OK);

            $this->common_data['company_policy'] = $this->session_model->fetch_company_policy($this->company_id);
            $this->common_data['company_data']['company_id'] = $this->company_id;
            $this->common_data['current_pc'] = $this->company_info->current_pc;
            $this->common_data['current_userID'] = $this->user->id;
            $this->common_data['emplanglocationid'] = $this->user->location;
            $this->common_data['current_user'] = $this->user->name2;
            $this->common_data['company_data']['company_code'] = $this->company_info->company_code;
            $this->common_data['user_group'] = $this->company_info->usergroupID;
            $this->common_data['company_data']['company_default_currencyID'] = $this->company_info->local_currency;
            $this->common_data['company_data']['company_default_currency'] = $this->company_info->local_currency_code;;
            $this->common_data['company_data']['company_reporting_currency'] = $this->company_info->rpt_currency_code;
            $this->common_data['company_data']['company_reporting_currencyID'] = $this->company_info->rpt_currency;
            $this->common_data['company_data']['default_segment'] = $this->company_info->default_segment;
            $this->common_data['company_data']['company_name'] = $this->company_info->company_name;
            $this->common_data['company_data']['company_address1'] = $this->company_info->company_address1;
            $this->common_data['company_data']['company_address2'] = $this->company_info->company_address2;
            $this->common_data['company_data']['company_city'] = $this->company_info->company_city;
            $this->common_data['company_data']['company_country'] = $this->company_info->company_country;
            $this->common_data['company_data']['company_logo'] = $this->company_info->company_logo;

            return TRUE;
        } else {
            if(isset($_SERVER['HTTP_SME_API_KEY_FORGETPASSWORD'])){
                //By pass token validations for non logged in users
                return $this->forgetpassword_post();
            }
            return $this->login_post();
        }
    }

    protected function setCompanyTimeZone($zoneID){
        $timeZone = $this->db->get_where('srp_erp_timezonedetail', ['detailID'=> $zoneID])->row('description');
        $timeZone = (empty($timeZone))? 'Asia/Colombo': $timeZone;
        date_default_timezone_set($timeZone);
    }
    protected function setDb()
    {
        if (!empty($this->company_info)) {
            $config['hostname'] = trim($this->company_info->db_host);
            $config['username'] = trim($this->company_info->db_username);
            $config['password'] = trim($this->company_info->db_password);
            $config['database'] = trim($this->company_info->db_name);
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = (ENVIRONMENT !== 'production');
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;
            $this->load->database($config, FALSE, TRUE);
        }
    }
    private function init_user()
    {
        if (isset($_SERVER['HTTP_SME_API_KEY'])) {
            $result = $this->jwt->decode($_SERVER['HTTP_SME_API_KEY'], "token");
            if (isset($result->id) && !empty($result->id)) {
                $this->db->select("EIdNo as id, ECode as code, Ename1 as name1, Ename2 as name2, Ename3 as name3, Ename4 as name4, EmpImage as image, Gender as gender, ZipCode as zipCode, 
                EpTelephone as telephone, EpFax as fax, EcMobile as mobile, EEmail as email, EDOB as dateOfBirth, EDOJ as dateOfJoin, EPassportNO as passportNo, segmentID, payCurrencyID, 
                isMobileCheckIn, mobileCreditLimit AS mobileCreditLimit, IFNULL(Nationality, '') AS Nationality, locationID AS location");
                $this->db->from("srp_employeesdetails");
                $this->db->where("EIdNo", $result->id);
                $this->user = $this->db->get()->row();
                $this->user->isMobileCheckIn = (int) $this->user->isMobileCheckIn;
                $this->user_id = $this->user->id;
                $this->user_name = $this->user->name2;
            }
        }
    }
    private function set_current_company()
    {
        if (isset($_SERVER['HTTP_SME_API_KEY'])) {
            $result = $this->jwt->decode($_SERVER['HTTP_SME_API_KEY'], "token");
            if (isset($result->id) && !empty($result->id)) {
                $this->company_id = $result->current_company_id;
            }
        }
    }

    public static function company_id()
    {
        return self::get_instance()->company_id;
    }

    public static function company_code()
    {
        return self::get_instance()->company_info->company_code;
    }

    public static function company_info()
    {
        return self::get_instance()->company_info;
    }

    public static function user_id()
    {
        return self::get_instance()->user_id;
    }

    public static function user_name()
    {
        return self::get_instance()->user->name2;
    }

    public static function user_info()
    {
        return self::get_instance()->user;
    }

    public function login_post()
    {

        $this->load->model('Login_Model');
        $request_body = file_get_contents('php://input');

        $request_1 = json_decode($request_body);

        $username = isset($request_1->username) ? $request_1->username : null;
        $pwd = isset($request_1->password) ? MD5($request_1->password) : null;
        $fireBase_token = isset($request_1->fireBase_token) ? $request_1->fireBase_token : null;
        $device = isset($request_1->device) ? $request_1->device : null;

        $isValidUser = $this->Login_Model->get_users($username, $pwd);

        if ($isValidUser["uname"] === '0') {
            $output = array('success' => false, 'message' => 'Authentication fail');
            return $this->response($output);
        }
    
        /*Auth success */
        $empid = $this->Login_Model->get_userID($username, $pwd);

        $token['id'] = $empid['EIdNo'];
        $token['Erp_companyID'] = $empid['Erp_companyID'];
        $token['ECode'] = $empid['Erp_companyID'];
        $token['company_code'] = $empid['company_code'];
        $token['name'] = $username;
        $token['username'] = $username;
        $token['db_host'] = trim($this->encryption->decrypt($empid["host"]));
        $token['db_username'] = trim($this->encryption->decrypt($empid["db_username"]));
        $token['db_password'] = trim($this->encryption->decrypt($empid["db_password"]));
        $token['db_name'] = trim($this->encryption->decrypt($empid["db_name"]));
        $token['current_company_id'] = $empid['Erp_companyID'];
        $token['current_pc'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);        
        //////
        $session_data = $this->Login_Model->get_session_data($empid['EIdNo'], $empid['Erp_companyID']);
        //var_dump($session_data['empCode']);exit;
        $token['empCode'] = $session_data['empCode'];
        $token['EmpShortCode'] = $session_data['EmpShortCode'];
        $token['companyType'] = $session_data['companyType'];
        $token['company_link_id'] = $session_data['company_link_id'];
        $token['branchID'] = $session_data['branchID'];
        $token['usergroupID'] = $session_data['usergroupID'];
        $token['ware_houseID'] = $session_data['ware_houseID'];
        $token['empImage'] = $session_data['empImage'];
        $token['imagePath'] = $session_data['imagePath'];
        $token['company_code'] = $session_data['company_code'];
        $token['company_name'] = $session_data['company_name'];
        $token['company_logo'] = $session_data['company_logo'];
        $token['emplangid'] = $session_data['emplangid'];
        $token['emplanglocationid'] = $session_data['emplanglocationid'];
        $token['isGroupUser'] = $session_data['isGroupUser'];
        $token['userType'] = $session_data['userType'];
        $token['status'] = $session_data['status'];


        $date = new DateTime();
        $token['iat'] = $date->getTimestamp();
        $token['exp'] = $date->getTimestamp() + 60 * 60 * 5;

        $this->load->library('jwt');
        $output['token'] = $this->jwt->encode($token, "token");

        $key['key'] = $output['token'];
        $key['level'] = 3;
        $key['ignore_limits'] = 56;
        $key['date_created'] = time();
        $key['company_id'] = $token['ECode'];

        $this->db->insert('keys', $key);

        if(!empty($fireBase_token))
        {
            $this->load->model('Auth_mobileUsers_Model');
            $this->Auth_mobileUsers_Model->save_firebase_token($fireBase_token, $device, $empid['EIdNo'], $empid['Erp_companyID']);
        }


        /******* Update login time to employee master *******/
        $logData = [
            'empID'=> $empid['EIdNo'], 'defaultTimezoneID'=> $empid['defaultTimezoneID'],
            'db_host'=> $token['db_host'], 'db_username'=> $token['db_username'], 
            'db_password'=> $token['db_password'], 'db_name'=> $token['db_name'],
        ];
        $this->update_login_time($logData);

        $final_output['success'] = true;
        $final_output['message'] = 'Logged in successfully';
        $final_output['data'] = $output;

        $this->response($final_output, 200);

    }
    protected function update_login_time($logData){
        $this->company_info = (object) $logData;
        
        $this->setCompanyTimeZone( $logData['defaultTimezoneID'] );

        $this->setDb();

        $this->db->where(['EIdNo'=> $logData['empID']])->update('srp_employeesdetails', [
            'last_login'=> date('Y-m-d H:i:s')
        ]);
    }

    public function get_country_data_post(){

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $this->load->database('default');

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            $detail = $this->Api_job_portal_model->get_country_data();
           
            if($detail['status']==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                $data['result'] = $detail['results'];
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                $data['result'] =  '';
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function get_designation_category_post(){

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $this->load->database('default');

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            $detail = $this->Api_job_portal_model->get_designation_category();
           
            if($detail['status']==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                $data['result'] = $detail['results'];
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                $data['result'] =  '';
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function get_designation_post(){

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $this->load->database('default');

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();

        $company_id = $orders['results']['dataMaster']['erp_company_id'];
        $categoryID= $orders['results']['dataMaster']['category_id'];
        
        try {

            $detail = $this->Api_job_portal_model->get_designation($categoryID, $companyID);
           
            if($detail['status']==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                $data['result'] = $detail['results'];
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                $data['result'] =  '';
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
   
}
