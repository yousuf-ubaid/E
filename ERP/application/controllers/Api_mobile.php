<?php

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_mobile extends REST_Controller
{
    private $company_info;
    private $company_id = 0;
    private $user_name;
    private $post;
    private $token = null;
    private $user;
    private $user_id = null;
    var $common_data = array();

    /*
     *
     * response
     *  "success": true,
     *  "message": "Profile retrieved successfully",
     *  "data": { }
     *
     *  token sample output
     *
     * */


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

            $this->load->library('sequence');
            $this->load->library('Approvals_mobile');
            $this->load->library('JWT');
            $this->load->library('S3');

            $output['token'] = $this->jwt->decode($tokenKey, "token");

            $company_curr = $this->db->select('company_logo,company_default_currencyID AS local_currency, company_default_currency AS local_currency_code,
                             company_reporting_currencyID AS rpt_currency, company_reporting_currency AS rpt_currency_code, companyPrintAddress, default_segment, 
                             defaultTimezoneID')
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
            $this->common_data['current_date'] = $last_access_date;
            $this->common_data['emplanglocationid'] = $this->user->location;
            $this->common_data['current_user'] = $this->user->name2;
            $this->common_data['company_data']['company_code'] = $this->company_info->company_code;
            $this->common_data['user_group'] = $this->company_info->usergroupID;
            $this->common_data['company_data']['company_default_currencyID'] = $this->company_info->local_currency;
            $this->common_data['company_data']['company_reporting_currencyID'] = $this->company_info->rpt_currency;
            $this->common_data['company_data']['default_segment'] = $this->company_info->default_segment;
            $this->common_data['company_data']['company_default_currency'] = $this->company_info->local_currency_code;
            $this->common_data['company_data']['company_reporting_currency'] = $this->company_info->rpt_currency_code;
            $this->common_data['company_data']['company_default_decimal'] = 2;
            // print_r($this->company_info); exit;

            return TRUE;

            /**
             * Decode JWT Authentication
             *
             *  $output['token'] will return
             *
             *      [id] => $current_userID
             *      [Erp_companyID] => $companyID
             *      [ECode] => $companyID
             *      [company_code] => HMS
             *      [name] => current_username
             *      [db_host] => 127.0.0.1
             *      [db_username] => root
             *      [db_password] => 123456
             *      [db_name] => database_name
             */

            /*$result = $this->db->select('*')
                ->from('keys')
                ->where('key', $_SERVER['HTTP_SME_API_KEY'])
                ->get()->row_array();
            if (!empty($result)) {
                $tmpCompanyInfo = $this->db->select('*')
                    ->from('srp_erp_company')
                    ->where('company_id', $result['company_id'])
                    ->get()->row_array();
                $this->company_info = $tmpCompanyInfo;
                $this->company_code = $tmpCompanyInfo['company_code'];
                if (!empty($this->company_info)) {
                    $this->company_id = $this->company_info['company_id'];
                    $this->setDb();
                    $this->company_info = $this->db->select('*')
                        ->from('srp_erp_company')
                        ->where('company_id', $this->company_id)
                        ->get()->row_array();

                    $this->set_limit();
                    $this->set_keyword();
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $this->set_user();
                        $this->auth_user();
                    }

                    return true;
                } else {
                    $output = array('success' => false, 'message' => 'Company ID not found');
                    echo $this->response($output, 200);
                }
            } else {
                $output = array('success' => false, 'message' => 'Invalid API Key');
                return $this->response($output, 401);
            }*/
        } else {

            if(isset($_SERVER['HTTP_SME_API_KEY_FORGETPASSWORD'])){
                //By pass token validations for non logged in users
                return $this->forgetpassword_post();
              
            }
              
            return $this->login_post();

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

    private function set_current_company()
    {
        if (isset($_SERVER['HTTP_SME_API_KEY'])) {
            $result = $this->jwt->decode($_SERVER['HTTP_SME_API_KEY'], "token");
            if (isset($result->id) && !empty($result->id)) {
                $this->company_id = $result->current_company_id;
            }
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

    public static function currency_conversion($base_cur, $con_currID, $amount = 0)
    {
        /*********************************************************************************************
         * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
         * If we want to know the reporting amount [ Reporting Currency => USD ]
         * So the currency_conversion functions 1st parameter will be the USD [what we looking for ]
         * And the 2nd parameter will be the OMR [what we already got]
         *
         * Ex :
         *    Transaction currency  =>  OMR     => $trCurrency  OR  $base_cur
         *    Transaction Amount    =>  1000/-  => $trAmount    OR  $amount
         *    Reporting Currency    =>  USD     => $reCurrency  OR  $con_currID
         *
         *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
         *    $conversionRate  = $conversionData['conversion'];
         *    $decimalPlace    = $conversionData['DecimalPlaces'];
         *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
         **********************************************************************************************/
        $data = array();
        $CI =& get_instance();
        if ($base_cur == $con_currID) {
            $data_arr = $CI->db->select('currencyID,CurrencyCode,DecimalPlaces,CurrencyName')
                ->from('srp_erp_companycurrencyassign')->where('currencyID', $base_cur)
                ->where('companyID', $CI->company_id)->get()->row_array();

            $data['currencyCode'] = $data_arr['CurrencyCode'];
            $data['currencyName'] = $data_arr['CurrencyName'];
            $data['decimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['con_currencyCode'] = $data_arr['CurrencyCode'];
            $data['con_currencyName'] = $data_arr['CurrencyName'];
            $data['con_decimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['conversion'] = 1;
            $data['convertedAmount'] = $amount;
        } else {
            $CI->db->select('cur.currencyID,conversion,CurrencyCode,CurrencyName,DecimalPlaces')
                ->from('srp_erp_companycurrencyconversion AS con')->where('con.masterCurrencyID', $base_cur)
                ->where('con.subCurrencyID', $con_currID)->where('con.companyID', $CI->company_id)
                ->join('srp_erp_currencymaster AS cur', 'cur.currencyID = con.subCurrencyID');
            $data_arr = $CI->db->get()->row_array();

            $base_cur_arr = $CI->db->get_where('srp_erp_currencymaster', ['currencyID' => $base_cur])->row_array();

            $data['currencyCode'] = $base_cur_arr['CurrencyCode'];
            $data['currencyName'] = $base_cur_arr['CurrencyName'];
            $data['decimalPlaces'] = $base_cur_arr['DecimalPlaces'];
            $data['con_currencyCode'] = $data_arr['CurrencyCode'];
            $data['con_currencyName'] = $data_arr['CurrencyName'];
            $data['con_decimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['conversion'] = round($data_arr['conversion'], 9);
            $data['convertedAmount'] = $amount * $data_arr['conversion'];
        }

        return $data;
    }

    public static function conversionRateUOM_id($subUnitID, $masterUnitID)
    {
        $CI =& get_instance();
        $CI->db->select('conversion');
        $CI->db->from('srp_erp_unitsconversion');
        $CI->db->where('masterUnitID', $masterUnitID);
        $CI->db->where('subUnitID', $subUnitID);
        $CI->db->where('companyID', $CI->company_id);

        return $CI->db->get()->row('conversion');
    }



    /** ---------------------------  LOGIN & GET TOKEN FUNCTIONS ---------------------------  */
    // Mobile APP Coped Code From SPUR-Mobile-BackEnd Repo
    public function login_post()
    {

        $this->load->model('Login_Model');
        $request_body = file_get_contents('php://input');

        $request_1 = json_decode($request_body);

        $username = isset($request_1->username) ? $request_1->username : null;
        $pwd = isset($request_1->password) ? MD5($request_1->password) : null;
        //$fireBase_token = isset($request_1->fireBase_token) ? $request_1->fireBase_token : null;
       // $device = isset($request_1->device) ? $request_1->device : null;

        $isValidUser = $this->Login_Model->get_users($username, $pwd);

     
        // if ($isValidUser["uname"] !== '0') {
        //     $output = array('success' => false, 'message' => 'Authentication fail');
        //     return $this->response($output);
        // }

       
        /*Auth success */
        $empid = $this->Login_Model->get_userID($username, $pwd);

        if(empty($empid)){
            $this->response(array('success'=>'FAILED','message' => 'Authentication Failed','data' => ''), 404);
        }
    

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
        $token['EIdNo'] = $empid['EIdNo'];
        $token['empCode'] = $session_data['empCode'];
        $token['EmpShortCode'] = $session_data['EmpShortCode'];
        $token['companyType'] = $session_data['companyType'];
        $token['companyID'] = $session_data['companyID'];
        $token['company_link_id'] = $session_data['company_link_id'];
        $token['branchID'] = $session_data['branchID'];
        $token['usergroupID'] = $session_data['usergroupID'];
        // $token['ware_houseID'] = $session_data['ware_houseID'];
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
        
        $token['counterCode']  = '';
        $token['counterName'] = '';
        $token['wareHouseID'] = $session_data['ware_houseID'];
    
      

        // get counter details
        // $counter_details = $this->get_warehouseuser_details($logData,$empid['EIdNo'],$empid['Erp_companyID']);

        // if($counter_details){
        //     $token['counterCode'] = $counter_details['counterCode'];
        //     $token['counterName'] = $counter_details['counterName'];
        //     $token['wareHouseID'] = $counter_details['wareHouseID'];
        // }else{
        //     $output = array('success' => false, 'message' => 'User not assigned to an counter');
        //     return $this->response($output);
        // }

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

    function get_warehouseuser_details($logData,$empid,$companyID){

        $this->company_info = (object) $logData;
        
        $this->setCompanyTimeZone( $logData['defaultTimezoneID'] );

        $this->setDb();

        $counter_details = $this->db->where('u.userID',$empid)
        ->where('u.companyID',$companyID)
        ->from('srp_erp_warehouse_users as u')
        ->join('srp_erp_pos_counters as p','u.counterID = p.counterID','left')
        ->get()->row_array();

        return $counter_details;

    }

    protected function update_login_time($logData){
        $this->company_info = (object) $logData;
        
        $this->setCompanyTimeZone( $logData['defaultTimezoneID'] );

        $this->setDb();

        $this->db->where(['EIdNo'=> $logData['empID']])->update('srp_employeesdetails', [
            'last_login'=> date('Y-m-d H:i:s')
        ]);
    }

    /** ---------------------------  INIT FUNCTIONS ---------------------------  */
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

    protected function setCompanyTimeZone($zoneID){
        $timeZone = $this->db->get_where('srp_erp_timezonedetail', ['detailID'=> $zoneID])->row('description');
        $timeZone = (empty($timeZone))? 'Asia/Colombo': $timeZone;
        date_default_timezone_set($timeZone);
    }

    /** ---------------------------  PRIVATE FUNCTIONS --------------------------- */

    public function profile_get()
    {

        $devID = $this->get('device');
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];

        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        //        $userType = $output['token']->userType;
        //        $isGroupUser = $output['token']->isGroupUser;
        //        $companyType = $output['token']->companyType;
        $userType = $this->db->query("SELECT userType FROM srp_employeesdetails WHERE EIdNo = {$userID}")->row('userType');
        $isGroupUser = 0;
        $companyType = 1;

        $companyID = $output['token']->Erp_companyID;
        $profile['designation'] = $this->Auth_mobileUsers_Model->get_emp_designation($userID);
        $this->user->image = $this->s3->createPresignedRequest($this->user->image, '+24 hour');
        $reportingmanager = $this->db->query("SELECT srp_employeesdetails.Ename1 AS reportingManager FROM srp_erp_employeemanagers LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_employeemanagers.managerID WHERE active = 1 AND empID = {$userID}")->row_array();
        $primaryDepartment = $this->db->query("SELECT DepartmentDes AS department FROM srp_empdepartments LEFT JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID = srp_empdepartments.DepartmentMasterID WHERE srp_empdepartments.isPrimary = 1 AND EmpID = {$userID}")->row_array();
        $this->user->ReportingManager = $reportingmanager['reportingManager'];
        $this->user->Department = $primaryDepartment['department'];
        $profile['user'] = $this->user;
        $defaultCompany = $this->Auth_mobileUsers_Model->get_emp_details($userID);
        $defaultCompany['location'] = $this->Auth_mobileUsers_Model->getMobileAttendanceLocation($userID, $companyID);
        $defaultCompany['locations'] = $this->Auth_mobileUsers_Model->getMobileAttendanceLocations($userID, $companyID);
        $profile['defaultCompany'] = $defaultCompany;
        $profile["companies"] = $this->Auth_mobileUsers_Model->get_companies($userID);
        $profile["current_company"] = $this->Auth_mobileUsers_Model->get_company($companyID);
        $profile["companyLocation"] = $this->Auth_mobileUsers_Model->get_companyLocation($userID, $companyID);
        $profile["userNavigation"] = $this->Auth_mobileUsers_Model->get_userNavigation($userType, $isGroupUser, $companyType);

        $this->Auth_mobileUsers_Model->save_deviceInfo($userID, $devID);
        if ($profile) {

            $final_output['success'] = true;
            $final_output['message'] = 'Profile retrieved successfully';
            $final_output['data'] = $profile;
            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'success' => FALSE,
                'message' => 'No users were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function logout_post($return = true)
    {
        $request_body = file_get_contents('php://input');
        $request_1 = json_decode($request_body);
        $fireBase_token = isset($request_1->fireBase_token) ? $request_1->fireBase_token : null;
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;

        $this->Login_Model->set_default_db();
        if (isset($_SERVER['HTTP_SME_API_KEY'])) {
            $tokenExist = $this->tokenExist();

            if ($tokenExist) {
                $this->db->where('key', $this->token);
                $result = $this->db->delete('keys');
                if ($result) {
                    if(!empty($fireBase_token)) {
                        $data['isLogged'] = 0;
                        $this->db->where('emp_id', $userID);
                        $this->db->where('player_id', $fireBase_token);
                        $this->db->update('srp_erp_devices', $data);
                    }
                    if ($return) {
                        $final_output['success'] = true;
                        $final_output['message'] = 'successfully logout!';
                        $final_output['data'] = NULL;

                        return $this->response($final_output, 200);
                    }
                } else {
                    if ($return) {
                        $final_output['success'] = true;
                        $final_output['message'] = 'this key is not exist or already deleted!';
                        $final_output['data'] = NULL;

                        return $this->response($final_output, 200);
                    }

                }
            }

        } else {
            $final_output['success'] = true;
            $final_output['message'] = 'token not found';
            $final_output['data'] = NULL;

            return $this->response($final_output, 200);
        }

    }

    private function tokenExist()
    {
        $this->Login_Model->set_default_db();
        $this->db->select('id');
        $this->db->where('key', $this->token);
        $this->db->from('keys');
        $r = $this->db->get()->row();
        if (!empty($r)) {
            return true;
        } else {
            return false;
        }

    }

 


    public function forgetpassword_post(){

        $this->load->model('Employee_model');

        $request_body = file_get_contents('php://input');

        $request_1 = json_decode($request_body);

        $email = isset($request_1->email) ? $request_1->email : null;

        // print_r($email); exit;

        // $email = $this->post('email');
        $data = array();

        if($email){
        
            $result = $this->Employee_model->getEmployeeMasterEmail($email);

            if($result){

                $PIN = rand(10000, 99999);
                $encryptValue = trim(sha1($PIN));
                $param['randNum'] = trim($encryptValue);
                $param['id'] = trim($result->empID);
                $param['autoID'] = trim($result->EidNo);

                $update = $this->Employee_model->setEmployeeMasterRandom($email,$encryptValue);

                if($update){

                    try{

                        $body = $this->load->view('system/email_template/email_template', $param, TRUE);

                        $email_response = send_custom_email($email, $body, 'Forgot Password');

                        $data['status'] = 'success';
                        $data['message'] = 'Successfully send the reset link';

                        return $this->response($data, 200);

                    }catch(Exception $e){

                        $data['status'] = 'error';
                        $data['message'] = 'Something went wrong';

                        // return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        return $this->response($data, 505);
                      
                    }
                }
                
            }else{  
               
                $data = array("status"=>"fail","message"=>"Email not consists with any account");
                return $this->response($data, 404);
            }

        }

    }

     /**
     * Change user password
     * @param SME-API-KEY token
     * 
     * @created Hasitha
     * @created at 2022-08-08
     */

    public function changepassword_post(){
        
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;
        $ecode = $output['token']->ECode;
        $username = $output['token']->username;
        

        $new_password = $this->post('newPassword');
        $old_password = $this->post('oldPassword');

        if(empty($new_password)) {
            $data = array("status"=>"fail","message"=>"Request is incomplete");
            return $this->set_response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $response = $this->Mobile_leaveApp_Model->changePassword($new_password,$old_password,$username);

        if($response['status'] == 'error'){
            return $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }else{
            return $this->set_response($response, REST_Controller::HTTP_OK);
        }

    }

   public function  items_post(){

        // $wareHouseAutoID=  $_POST['wareHouseAutoID'] ;
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        
        $CI =& get_instance();

        $wareHouseAutoID = $output['token']->wareHouseID;

        $query=$CI->db->query("
                SELECT
                    a.*,
                    b.itemTaxFormulaID,
                    b.taxFormulaID,
                    c.financeCategory,
                    c.mainCategory,
                    b.taxType,
                    c.itemImage,
                    IFNULL(c.itemCreditPrice,a.salesPrice) as itemCreditPrice
                from 
                srp_erp_warehouseitems a
                LEFT OUTER JOIN srp_erp_itemtaxformula b ON a.itemAutoID = b.itemAutoID
                LEFT JOIN srp_erp_itemmaster c ON a.itemAutoID = c.itemAutoID
                LEFT JOIN (
                    SELECT itemMasterID,rSalesPrice as itemCreditPrice
                    FROM srp_erp_item_master_pricing 
                    WHERE paymentMethod = 7
                ) as c ON a.itemAutoID = c.itemMasterID
                where  a.wareHouseAutoID='$wareHouseAutoID'
        ");
      
        $data= $query->result_array();

        $CI->load->library('s3');

        foreach($data as $key => $details){
            if($details['itemImage'] == 'no-image.png'){
                $path = 'images/item/' . $details['itemImage'];
            }else{
                $path = 'uploads/itemMaster/' . $details['itemImage'];
            }
            
            $img_item = $CI->s3->createPresignedRequest($path, '+48 hour');

            $data[$key]['itemImage'] = $img_item;
        }

        $output = array('type' => 'success', 'status' => 200, 'message' => 'all items', 'data' => $data);
        
        return   $this->response($output);
        
    }


    public function taxformula_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;

        $data=[];
    
        $CI =& get_instance();
        $query=  $CI->db->query("
            SELECT a.Description as itemName,a.taxType as itemTaxType,a.taxCalculationFormulaID as itemSystemCode,ROUND(SUM(b.taxPercentage),2) as itemTaxPercentage
            FROM srp_erp_taxcalculationformulamaster a
            INNER JOIN srp_erp_taxcalculationformuladetails as b ON a.taxCalculationFormulaID = b.taxCalculationFormulaID
            WHERE a.companyID = '{$companyID}' AND a.taxType = 1 
            GROUP BY a.taxCalculationFormulaID 
        ");

        $data= $query->result_array();
        $output = array('type' => 'success', 'status' => 200, 'message' => 'all taxes', 'data' => $data);
        return   $this->response($output);
    }


    public function company_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;
        $EIdNo = $output['token']->EIdNo;
        $wareHouseID = $output['token']->wareHouseID;

        $CI =& get_instance();
        $CI->db->select('srp_erp_company.*,srp_erp_pos_counters.counterCode');
        $CI->db->from('srp_erp_company');
        $CI->db->join('srp_erp_warehouse_users',"srp_erp_warehouse_users.wareHouseID = '{$wareHouseID}' AND srp_erp_warehouse_users.userID = '{$EIdNo}'",'left');
        $CI->db->join('srp_erp_pos_counters',"srp_erp_pos_counters.counterID = srp_erp_warehouse_users.counterID",'left');
        $CI->db->where('company_id', $companyID);
        $query = $CI->db->get();
        $data= $query->result_array();

        if($data){
            $CI->db->from('srp_erp_pos_invoice');
            $CI->db->where('companyID', $companyID);
            $CI->db->where('wareHouseAutoID', $wareHouseID);
            $invoice_sq = $CI->db->get()->num_rows();

            $data['invoice_seq_start'] = $invoice_sq + 1;
        }

        $output = array('type' => 'success', 'status' => 200, 'message' => 'all items', 'data' => $data);
        return   $this->response($output);
    }


    public function customer_post()
    {
       // $wareHouseAutoID=  $_POST['wareHouseAutoID'] ;
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;

        $CI =& get_instance();
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('companyID',$companyID);

        $query = $CI->db->get();
        $data= $query->result_array();
        $output = array('type' => 'success', 'status' => 200, 'message' => 'all items', 'data' => $data);
        return   $this->response($output);

    }

    public function getdenominations_post(){

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;
        $currencyCode  =  $_POST['currencyCode'] ;

        try {
            $CI =& get_instance();
            $denominations = $CI->db->where('currencyCode',$currencyCode)->from('srp_erp_currencydenomination')->get()->result_array();
            
            $output = array('type' => 'success', 'status' => 200, 'message' => 'denomination request', 'data' => $denominations);
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            
        }
       
    }


    public function location_post()
    {
       // $wareHouseAutoID=  $_POST['wareHouseAutoID'] ;
        $user_id=$_POST['user_id'];
        $device_id=$_POST['device_id'];
        $lat=$_POST['lat'];
        $long=$_POST['long'];
        $counterCode=$_POST['counterCode'];
        $CI =& get_instance();

        if(!empty($counterCode)){
            $device_id = $counterCode;
        }

        $data = array(
            'user_id'=>$user_id,
            'device_id'=>$device_id,
            'lat'=>$lat,
            'long'=>$long,
            'long'=>$long
        );

        $CI->db->insert('tab_locations',$data);

        if($CI->db->affected_rows() == 1){
            $output = array('type' => 'success', 'status' => 200, 'message' => 'all items', 'data' => $data);
            return   $this->response($output);
        }
        else{
                $output = array('type' => 'error', 'status' => 500, 'message' => 'someting went wrong', 'data' => $data);
            return   $this->response($output); 
        }
           
    }

    public function orders_send_post(){

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;
        $EIdNo = $output['token']->EIdNo;
        $wareHouseID = $output['token']->wareHouseID;
        

        $CI =& get_instance();
        $base = array();
        $CI->load->model('Pos_model');

        $CI->db->select('srp_erp_company.*,srp_erp_pos_counters.counterCode,srp_erp_pos_counters.counterID');
        $CI->db->from('srp_erp_company');
        $CI->db->join('srp_erp_warehouse_users',"srp_erp_warehouse_users.wareHouseID = '{$wareHouseID}' AND srp_erp_warehouse_users.userID = '{$EIdNo}'",'left');
        $CI->db->join('srp_erp_pos_counters',"srp_erp_pos_counters.counterID = srp_erp_warehouse_users.counterID",'left');
        $CI->db->where('company_id', $companyID);
        $query = $CI->db->get();
        $company_details= $query->row_array();

        try {

            $data_values = $data_decoded['data'];

            foreach($data_values as $value){

                $currencyCode = $value['invoiceCurrency'];
                $wareHouseID = $output['token']->wareHouseID;

                $currency_details = $CI->Pos_model->get_currency_details($currencyCode);
                $wareHouseDetails = $CI->Pos_model->get_wareHouse_details($wareHouseID);
                $invoiceDate = date('Y-m-d',strtotime($value['invoiceDate']));
                $invoiceDateTime = date('Y-m-d H:i:s',strtotime($value['invoiceDate']));
                
                $base['invoiceCode'] = $value['invoiceNumber'];
                $base['customerID'] = $value['invoiceCustomerID'];
                $base['counterID'] = $company_details['counterID'];
                $base['shiftID'] = $value['invoiceShiftID'];
                $base['invoiceDate'] = $invoiceDate;
                $base['subTotal'] = $value['invoiceSubTotal'];
                $base['discountAmount'] = $value['invoiceDiscountAmount'];
                $base['netTotal'] = $value['invoiceNetAmount'];
                $base['paidAmount'] = $value['invoicePaidAmount'];
                $base['balanceAmount'] = $value['invoiceBalanceAmount'];
                $base['cashAmount'] = $value['invoiceCashAmount'];
                $base['chequeAmount'] = $value['invoiceChequeAmount'];
                $base['chequeNo'] = $value['invoiceChequeAmount'];
                $base['chequeDate'] = $value['invoiceChequeDate'];
                $base['cardAmount'] = $value['invoiceCardAmount'];
                $base['creditNoteAmount'] = $value['invoiceCreditAmount'];
                $base['cardRefNo'] = $value['invoiceCardDetails'];
                $base['isCreditSales'] = ($value['invoiceCreditAmount'] > 0) ? 1 : 0;
                $base['creditSalesAmount'] = $value['invoiceCreditAmount'];
                $base['wareHouseAutoID'] = $wareHouseID;
                $base['transactionCurrencyID'] = $currency_details['currencyID'];
                $base['transactionCurrency'] = $currency_details['CurrencyCode'];
                $base['transactionCurrencyDecimalPlaces'] = $currency_details['DecimalPlaces'];
                $base['companyID'] = $companyID;
                $base['invoice_status'] = $value['invoiceStatus'];
                $base['createdUserID'] = $EIdNo;
                $base['tabID'] = $value['tabID'];
                $base['param'] = $data;

                $res = $CI->db->insert('srp_erp_pos_tab_invoice',$base);

                $item_detail = $value['detail'];

                foreach($item_detail as $item){

                    $item_ins_arr = array();

                    $item_ins_arr['invoiceCode'] = $item['invoiceNumber']; 
                    $item_ins_arr['itemSystemCode'] = $item['itemSystemCode']; 
                    $item_ins_arr['qty'] = $item['itemReqQuantity']; 
                    $item_ins_arr['price'] = $item['itemPrice']; 
                    $item_ins_arr['wacAmount'] = $item['itemWACAmount']; 
                    $item_ins_arr['taxCalculationformulaID'] = $item['invDetailTaxID']; 
                    $item_ins_arr['taxAmount'] = $item['invDetailTaxAmount']; 
                    $item_ins_arr['discountAmount'] = $item['invDetailDiscount']; 
                    $item_ins_arr['itemConditionType'] = ($item['type']) ? $item['type'] : 1; 
                    $item_ins_arr['discountPer'] = 5; 
                    $item_ins_arr['companyID'] = $companyID;
                    $item_ins_arr['transactionAmount'] = ((($item['itemReqQuantity'] * $item['itemPrice']) + $item['invDetailTaxAmount']) - $item['invDetailDiscount']);

                    $item_ins_arr['param'] = json_encode($item);

                    $res = $CI->db->insert('srp_erp_pos_tab_invoicedetail',$item_ins_arr);

                }

            }
    
            $output = array('type' => 'success', 'status' => 200, 'message' => 'Sales sync', 'data' => 'Recorded Successfully');
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
       

    }

    public function shiftdetails_send_post(){

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );

        $CI =& get_instance();
        $base = array();

        try {
            $base['param'] = $data;
            $CI->db->insert('srp_erp_pos_tab_shiftdetails',$base);
    
            $output = array('type' => 'success', 'status' => 200, 'message' => 'Sales Shift Recorded', 'data' => 'Recorded Successfully');
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    } 

    public function dayenditems_send_post(){

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );

        $CI =& get_instance();
        $base = array();

        try {
            $base['param'] = $data;
            $CI->db->insert('srp_erp_pos_tab_warehouseitems',$base);
    
            $output = array('type' => 'success', 'status' => 200, 'message' => 'Sales Items', 'data' => 'Recorded Successfully');
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }


    } 

    public function returns_send_post(){

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );

        $CI =& get_instance();
        $base = array();

        try {
            $base['param'] = $data;
            $CI->db->insert('srp_erp_pos_tab_salesreturn',$base);
    
            $output = array('type' => 'success', 'status' => 200, 'message' => 'Sales Items', 'data' => 'Recorded Successfully');
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }


    } 

    public function stock_transfer_post(){

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );

        $CI =& get_instance();
        $base = array();

        try {
            $base['param'] = $data;
            $CI->db->insert('srp_erp_pos_tab_stocktransfers',$base);
    
            $output = array('type' => 'success', 'status' => 200, 'message' => 'Sales Items', 'data' => 'Recorded Successfully');
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sales_report_post(){
        
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;

        $data = json_decode( file_get_contents('php://input'), true );

        $from_date= isset($data['from_date']) ? $data['from_date'] : null;
        $to_date= isset($data['to_date']) ? $data['to_date'] : null;
        $wareHouseID= isset($data['wareHouseID']) ? $data['wareHouseID'] : null;
        $base_arr = array();

        if(empty($from_date) || empty($to_date)){
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Date from and to needs to be selected');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        if(empty($wareHouseID)){
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Warehouse is not given');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $CI =& get_instance();
            $query_sales=  $CI->db->query("
                    SELECT inv.createdDateTime,inv.invoiceCode,inv.wareHouseDescription,cus.customerName,cus.customerTelephone,inv.subTotal,inv.discountAmount,inv.netTotal
                    FROM srp_erp_pos_invoice as inv
                    LEFT JOIN srp_erp_customermaster as cus ON cus.customerAutoID = inv.customerID 
                    WHERE inv.createdDateTime BETWEEN '{$from_date}' AND '{$to_date}' 
                    AND  inv.wareHouseAutoID  = '{$wareHouseID}' AND inv.companyID = '{$companyID}'
                ");
    
            $data_sales= $query_sales->result_array();
    
    
            $base_arr['sales_data'] = $data_sales; 


            $output = array('type' => 'success', 'status' => 200, 'message' => 'Report data', 'data' => $base_arr);
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function customer_send_post(){

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );
        $this->load->model('Customer_model');
        $this->load->helpers('customer');

        $CI =& get_instance();
        $base = array();

        try {

            $base['param'] = $data;
            
            $_POST['customercode'] = $data_decoded['code'];
            $_POST['customerName'] = $data_decoded['name'];
            $_POST['customerAddress1'] = $data_decoded['address'];
            $_POST['customerEmail'] = $data_decoded['email'];
            $_POST['customerTelephone'] = $data_decoded['telephone'];
            $_POST['currency_code'] = $this->common_data['company_data']['company_default_currency'];
            $_POST['customerCurrency'] = $this->common_data['company_data']['company_default_currencyID'];
            $_POST['partyCategoryID'] = 1;
            $_POST['receivableAccount'] = 3;

            $response = $this->Customer_model->save_customer();

            if($response){
                $base['param'] = $data;
                $CI->db->insert('srp_erp_pos_tab_customermaster',$base);
            }
    
            $output = array('type' => 'success', 'status' => 200, 'message' => 'Customer Add', 'data' => 'Recorded Successfully');
            return $this->set_response($output, REST_Controller::HTTP_OK);

        } catch (\Throwable $th) {
            $output_err = array('type' => 'fail', 'status' => 500, 'data' => 'Something went wrong');
            return $this->set_response($output_err, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }


    }

    public function warehouseList_post(){

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;

        $CI =& get_instance();
        $CI->db->select('wareHouseAutoID,wareHouseCode,wareHouseDescription,wareHouseLocation,warehouseAddress');
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyID',$companyID);
        $query = $CI->db->get()->result_array();

        $this->response($query, REST_Controller::HTTP_OK);

    }

    public function sendMaterial_post(){

        $this->load->model('Material_receipt_note_modal');
        $this->load->helpers('materialreceiptnote');
        $this->load->helpers('exceedmatch');

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );

        //get segment
        $CI =& get_instance();
        $CI->db->from('srp_erp_segment');
        $CI->db->where('companyID',$companyID);
        $CI->db->where('segmentCode','GEN');
        $segment = $query = $CI->db->get()->row_array();

        try {
            $data = $data_decoded['data'];

            foreach($data as $materialReceipt){

                //get warehouse details
                $CI->db->from('srp_erp_warehousemaster');
                $CI->db->where('companyID',$companyID);
              //  $CI->db->where('wareHouseAutoID',$materialReceipt['wareHouseID']);
                $CI->db->where('wareHouseCode',$materialReceipt['wareHouseCode']);
                $wareHoseDetails = $query = $CI->db->get()->row_array();

                $item_details = $materialReceipt['items'];

                //Check item all exists
                $item_details = $materialReceipt['items'];

                $response = $this->Material_receipt_note_modal->check_item_exists_in_warehouse($item_details,$wareHoseDetails['wareHouseAutoID']);

                if($response['status'] == 'error'){
                    $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }

                if(empty($wareHoseDetails)){
                    $this->response(array('status'=>'error','message'=>'Warehouse Code not available'), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                    continue;
                }

                //header section
                $_POST['receiptType'] = $materialReceipt['type'];
                $_POST['segment'] = $segment['segmentID'].'|'.$segment['segmentCode'];
                $_POST['issueRefNo'] = $materialReceipt['referenceNo'];
                $_POST['receivedDate'] = $materialReceipt['dateReceived'];
                $_POST['location'] = $wareHoseDetails['wareHouseAutoID'];
                $_POST['itemType'] = 'Inventory';
                $_POST['narration'] = $materialReceipt['naration'];
                $_POST['location_dec'] = $wareHoseDetails['wareHouseCode'].' | '.$wareHoseDetails['wareHouseLocation'].' | '.$wareHoseDetails['wareHouseDescription'];
                
                $financeYearDetails = get_financial_year($_POST['receivedDate']);

                if(empty($financeYearDetails)){
                    $response = array('status'=>'error','message'=>'Financial Period is not active');
                    $this->response($response, REST_Controller::HTTP_OK);
                }

                $_POST['companyFinanceYear'] = $financeYearDetails['beginingDate'].' - '.$financeYearDetails['endingDate'];
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];


                $header = $this->Material_receipt_note_modal->save_material_receipt_header();

                if($header){

                    $last_id = $header['last_id'];
                    $item_details = $materialReceipt['items'];

                    $search = array();
                    $itemAutoID = array();
                    $UnitOfMeasureID = array();
                    $currentWareHouseStockQty = array();
                    $a_segment = array();
                    $quantity_requested = array();
                    $unitCost = array();
                    $comment = array();
                    $uom = array();
                    $currentStock = array();


                    foreach($item_details as $item){

                        $_POST = array();

                        $CI->db->from('srp_erp_itemmaster');
                        $CI->db->where('companyID',$companyID);
                        $CI->db->where('seconeryItemCode',$item['secondaryItemCode']);
                        $itemDetailsMaster = $query = $CI->db->get()->row_array();

                          //get item details 
                        $CI->db->from('srp_erp_warehouseitems');
                        $CI->db->where('companyID',$companyID);
                        $CI->db->where('wareHouseAutoID',$wareHoseDetails['wareHouseAutoID']);
                        $CI->db->where('itemAutoID',$itemDetailsMaster['itemAutoID']);
                        $itemDetails = $query = $CI->db->get()->row_array();

                        if(empty($itemDetails)){
                            continue;
                        }

                        $search[] = $itemDetails['itemDescription'].' - '.$itemDetails['itemSystemCode'].'- -'.$itemDetailsMaster['seconeryItemCode'];
                        $itemAutoID[] = $itemDetails['itemAutoID'];
                        $UnitOfMeasureID[] = $itemDetails['unitOfMeasureID'];
                        $currentWareHouseStockQty[] = $itemDetails['currentStock'];
                        $a_segment[] = $segment['segmentID'].'|General';
                        $quantity_requested[] = $item['itemReceivedQty'];
                        $unitCost[] = $itemDetails['salesPrice'];
                        $comment[] = $item['comment'];
                        $mrnAutoID = $last_id;
                        $uom[] = $itemDetails['unitOfMeasure'].' | '.$itemDetails['unitOfMeasure'];
                        $currentStock[] = $itemDetailsMaster['currentStock'];

                    }

                    $_POST['search'] = $search;
                    $_POST['itemAutoID'] = $itemAutoID;
                    $_POST['currentStock'] = 0;
                    $_POST['UnitOfMeasureID'] = $UnitOfMeasureID;
                    $_POST['currentWareHouseStockQty'] = $currentWareHouseStockQty;
                    $_POST['a_segment'] = $a_segment;
                    $_POST['quantityRequested'] = $quantity_requested;
                    $_POST['unitCost'] = $unitCost;
                    $_POST['comment'] = $comment;
                    $_POST['mrnAutoID'] = $last_id;
                    $_POST['uom'] = $uom;

                    $res = $this->Material_receipt_note_modal->save_material_detail_multiple();

                    // confir documnet
                    $this->Material_receipt_note_modal->material_item_confirmation();
                    
                    $response = array('status'=>'success','message'=>'Successfully Completed');
                    $this->response($response, REST_Controller::HTTP_OK);

                }
                
            }

        } catch (\Throwable $th) {
              
            $response = array('status'=>'error','message'=>'Soemthing went wrong');
            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

        }
       
    }

    public function getSalesDate_post(){

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");

        $companyID = $output['token']->Erp_companyID;

        $data = file_get_contents('php://input');
        $data_decoded = json_decode( file_get_contents('php://input'), true );

        $date_from = $data_decoded['date_from'];
        $date_to = $data_decoded['date_to'];
        $wareHouseID =  $data_decoded['warehouseID'];

        $CI =& get_instance();
        $CI->db->select('srp_erp_pos_invoice.invoiceID,srp_erp_pos_invoice.documentSystemCode,srp_erp_pos_invoice.documentCode,srp_erp_pos_invoice.invoiceCode,srp_erp_pos_invoice.customerCode,srp_erp_pos_invoice.invoiceDate,srp_erp_pos_invoice.subTotal,srp_erp_pos_invoice.discountAmount,srp_erp_pos_invoice.netTotal,srp_erp_pos_invoice.paidAmount
        ,srp_erp_pos_invoice.balanceAmount,srp_erp_pos_invoice.cashAmount,srp_erp_pos_invoice.cardAmount,srp_erp_pos_invoice.creditNoteAmount,srp_erp_pos_invoice.creditNoteID,srp_erp_pos_invoice.creditSalesAmount,srp_erp_pos_invoice.wareHouseDescription,srp_erp_pos_invoice.transactionCurrency,srp_erp_pos_invoice.wareHouseCode,srp_erp_customermaster.secondaryCode as customerSecondaryCode,srp_erp_pos_invoice.createdUserID,srp_erp_pos_invoice.createdUserName');
        $CI->db->from('srp_erp_pos_invoice');
        $CI->db->join('srp_erp_customermaster',"srp_erp_customermaster.customerAutoID = srp_erp_pos_invoice.customerID",'left');
        $CI->db->where('srp_erp_pos_invoice.companyID',$companyID);
        $CI->db->where('wareHouseAutoID',$wareHouseID);
        $CI->db->where('invoiceDate >=', $date_from);
        $CI->db->where('invoiceDate <=', $date_to);
        $query = $CI->db->limit(10)->get();
        $invoices= $query->result_array();

        foreach($invoices as $key => $inv){
            $inv_details = $CI->db->select('srp_erp_pos_invoicedetail.itemSystemCode,srp_erp_itemmaster.seconeryItemCode,srp_erp_pos_invoicedetail.itemDescription,srp_erp_pos_invoicedetail.itemCategory,srp_erp_pos_invoicedetail.defaultUOM,srp_erp_pos_invoicedetail.qty,srp_erp_pos_invoicedetail.price,srp_erp_pos_invoicedetail.discountAmount,srp_erp_pos_invoicedetail.taxAmount,srp_erp_pos_invoicedetail.transactionCurrency,srp_erp_pos_invoicedetail.transactionAmount,srp_erp_pos_invoicedetail.isFoc,srp_erp_pos_invoicedetail.itemConditionType')
            ->from('srp_erp_pos_invoicedetail')
            ->join('srp_erp_itemmaster',"srp_erp_itemmaster.itemAutoID = srp_erp_pos_invoicedetail.itemAutoID",'left')
            ->where('invoiceID',$inv['invoiceID'])
            ->get()->result_array();
            $invoices[$key]['detail'] = $inv_details;
        }   

        if(empty($invoices)){
            $output = array('response'=>'success','message'=>'No Data','data' => $invoices);
        }else{
            $output = array('response'=>'success','message'=>'Data Retrived','data' => $invoices);
        }
        
        return $this->set_response($output, REST_Controller::HTTP_OK);

    }

    public function sendItemType_get(){
        $data = array('1'=> 'Good','2'=>'Damaged','3'=>'Broken');
        return   $this->response($data);
    }

}
