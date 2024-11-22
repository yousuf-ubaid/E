<?php
/**
 *
 * ==========================================
 * Technical Documentation : Created By Safry
 * ==========================================
 *
 *
 *
 *  1. Update this db in central / main db
 *  ======================================
 *
 *      DROP TABLE IF EXISTS `keys`;
 * CREATE TABLE `keys`  (
 * `id` int(11) NOT NULL AUTO_INCREMENT,
 * `key` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
 * `level` int(2) NOT NULL,
 * `ignore_limits` tinyint(1) NOT NULL DEFAULT 0,
 * `date_created` int(11) NOT NULL,
 * `company_id` int(11) NULL DEFAULT 0,
 * PRIMARY KEY (`id`) USING BTREE
 * ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
 *
 *
 *
 *  2. Setup Key and the company_id
 *  ===============================
 *  example :
 *      INSERT INTO `central_db`.`keys`(`id`, `key`, `level`, `ignore_limits`, `date_created`, `company_id`)
 *      VALUES (1, 'r4B1UiL920kan6@FugxT@q$ZQ%rha5ttA&D^c0V', 3, 5, 20171010, $companyID);
 *
 *
 *
 *
 *  3. Headers
 *  ==========
 *      Key                     Value
 *      ---                     -----
 *      SME-API-KEY             {{Your secret key saved in central db key table}}
 *
 *
 *
 *
 *  4. Calling methods
 *  =================
 *
 *  BASE_PATH = 'http://localhost/gs_sme/index.php/api_property/';
 *
 *  4.1 [GET] Chart of account
 **      BASE_PATH/chart_of_account?limit=20&search=keyword
 *
 *
 *  4.2 [GET] Supplier master
 *      BASE_PATH/supplier_master?limit=20&search=keyword
 *
 *  4.3 [GET] Customer master
 *      BASE_PATH/customer_master?limit=20&search=keyword
 *
 *  4.4 [GET] Get User
 *      BASE_PATH/user/25
 *
 * Functions
 * =========
 * 1.chart_of_account_get($limit,$keyword) to get chart of account [$limit {{no of records in the response}}, $keyword{{search this value}}]
 *
 *
 *
 * */

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * Class Api_spur
 */
class Api_self extends REST_Controller
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
            $this->common_data['emplanglocationid'] = $this->user->location;
            $this->common_data['current_user'] = $this->user->name2;
            $this->common_data['company_data']['company_code'] = $this->company_info->company_code;
            $this->common_data['user_group'] = $this->company_info->usergroupID;
            $this->common_data['company_data']['company_default_currencyID'] = $this->company_info->local_currency;
            $this->common_data['company_data']['company_reporting_currency'] = $this->company_info->rpt_currency;
            $this->common_data['company_data']['default_segment'] = $this->company_info->default_segment;

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

    public static function document_status($docID, $masterID, $para = [])
    {
        $tableName = null;
        $masterColumn = null;
        $confirmColumn = 'confirmedYN';
        $approvalColumn = 'approvedYN';
        $documentCode = null;
        $companyColumn = 'companyID';
        $currLevelColumn = 'currentLevelNo';
        $documentDateColumn = 'createdDateTime';

        $isOn = (array_key_exists('isOn', $para)) ? $para['isOn'] : 0;

        switch ($docID) {
            case 'FS':
                $tableName = 'srp_erp_pay_finalsettlementmaster';
                $masterColumn = 'masterID';
                $documentCode = 'documentCode';
                break;

            case 'LO':
                $tableName = 'srp_erp_pay_emploan';
                $masterColumn = 'ID';
                $documentCode = 'loanCode';
                $currLevelColumn = 'currentApprovalLevel';
                break;

            case 'VD':
                $tableName = 'srp_erp_variablepaydeclarationmaster';
                $masterColumn = 'vpMasterID';
                $documentCode = 'documentCode';
                $documentDateColumn = 'documentDate';
                break;

            case 'DO':
                $tableName = 'srp_erp_deliveryorder';
                $masterColumn = 'DOAutoID';
                $documentCode = 'DOCode';
                $documentDateColumn = 'DODate';
                break;

            case 'SAR':
                $tableName = 'srp_erp_pay_salaryadvancerequest';
                $masterColumn = 'masterID';
                $documentCode = 'documentCode';
                $documentDateColumn = 'request_date';
                break;

            case 'LEC':
                $tableName = 'srp_erp_pay_leaveencashment';
                $masterColumn = 'masterID';
                $documentCode = 'documentCode';
                $documentDateColumn = 'encashment_date';
                break;

            case 'HDR':
                $tableName = 'srp_erp_hr_letterrequests';
                $masterColumn = 'request_id';
                $documentCode = 'documentCode';
                $documentDateColumn = 'request_date';
                break;

            case 'EC':
                $tableName = 'srp_erp_expenseclaimmaster';
                $masterColumn = 'expenseClaimMasterAutoID';
                $documentCode = 'expenseClaimCode';
                $documentDateColumn = 'expenseClaimDate';
                $currLevelColumn = 1;
                break;

            case 'BSI':
                $tableName = 'srp_erp_paysupplierinvoicemaster';
                $masterColumn = 'InvoiceAutoID';
                $documentCode = 'bookingInvCode';
                $documentDateColumn = 'bookingDate';
                $currLevelColumn = 1;
                break;

            default :
                return ['error' => 1, 'message' => 'Document ID not configured for status check.<br/>Please contact the system support team.'];
        }

        $ci =& get_instance();
        $companyID = $ci->company_id;

        $more_column = (array_key_exists('more_column', $para)) ? ", {$tableName}.*" : '';

        $ci->db->select("{$confirmColumn} AS confirmVal, {$approvalColumn} AS approvalVal, {$currLevelColumn} AS appLevel, 
        {$documentCode} AS docCode, {$documentDateColumn} AS createdDate {$more_column}");
        $ci->db->from("{$tableName}");
        $ci->db->where("{$masterColumn}", $masterID);
        $ci->db->where("{$companyColumn}", $companyID);
        $document_output = $ci->db->get()->row_array();

        if (empty($document_output)) {
            return ['error' => 1, 'message' => 'Document master record not found'];
        }

        if ($document_output['approvalVal'] == 1) {
            return ['error' => 1, 'message' => 'This document already approved.'];
        }

        if ($isOn == 1) { /* Is on refer back */
            if ($document_output['confirmVal'] == 0) {
                return ['error' => 1, 'message' => 'This document not confirmed yet'];
            }

            return [
                'error' => 0,
                'tableName' => $tableName,
                'masterColumn' => $masterColumn,
                'confirmColumn' => $confirmColumn,
                'approvalColumn' => $approvalColumn,
                'currLevelColumn' => $currLevelColumn,
                'data' => $document_output
            ];
        }

        if ($document_output['confirmVal'] == 1) {
            return ['error' => 1, 'message' => 'This document already confirmed'];
        }

        return ['error' => 0, 'message' => 'still not confirmed', 'data' => $document_output];
    }

    /** ---------------------------  LOGIN & GET TOKEN FUNCTIONS ---------------------------  */
    // Mobile APP Coped Code From SPUR-Mobile-BackEnd Repo
    public function login_post()
    {

        $this->load->model('Login_Model');
        $request_body = file_get_contents('php://input');

        $request_1 = json_decode($request_body);

        $username = isset($request_1->username) ? $request_1->username : null;
        $pwd = isset($request_1->password) ? ($request_1->password) : null;
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
        $session_data = $this->Login_Model->get_session_data_for_max_portal_open_link($empid['EIdNo'], $empid['Erp_companyID']);
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
        $token['company_address1'] = $session_data['company_address1'];
        $token['company_address2'] = $session_data['company_address2'];
        $token['company_city'] = $session_data['company_city'];
        $token['company_country'] = $session_data['company_country'];
        $token['company_phone'] = $session_data['company_phone'];
        $token['company_email'] = $session_data['company_email'];


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
;

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

    /** checked */
    public function switchCompany_get()
    {

        $compID = $this->get('companyId');
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companycode = $this->Auth_mobileUsers_Model->get_companyCode($compID);
        $company_name = $this->Auth_mobileUsers_Model->get_companyName($compID);
        $ECode = $output['token']->ECode;
        $name = $output['token']->name;
        $username = $output['token']->username;
        //********************************************************************************
        $token['Erp_companyID'] = $compID;
        $token['ECode'] = $ECode;
        $token['company_code'] = $companycode['company_code'];
        $token['company_name'] = $company_name['company_name'];
        $token['name'] = $name;
        $token['username'] = $username;
        $token['usergroupID'] = $output['token']->usergroupID;
        $token['current_pc'] = $output['token']->current_pc;
        $token['db_host'] = $output['token']->db_host;
        $token['db_username'] = $output['token']->db_username;
        $token['db_password'] = $output['token']->db_password;
        $token['db_name'] = $output['token']->db_name;
        $token['current_company_id'] = $compID;

        $date = new DateTime();
        $token['iat'] = $date->getTimestamp();
        $token['exp'] = $date->getTimestamp() + 60 * 60 * 5;
        $token['id'] = $userID;

        $this->load->library('JWT');
        $output['token'] = $this->jwt->encode($token, "token");

        $output['designation'] = $this->Auth_mobileUsers_Model->get_emp_designation($userID);

        $final_output['success'] = true;
        $final_output['message'] = 'company switched successfully';
        $final_output['data'] = $output;

        $this->Login_Model->set_default_db();

        $key['key'] = $output['token'];
        $key['level'] = 3;
        $key['ignore_limits'] = 56;
        $key['date_created'] = time();
        $key['company_id'] = $compID;
        $this->db->insert('keys', $key);

        $this->logout_post(false);
        $this->token = $output['token'];

        $this->setDb();
        $this->set_response($final_output, REST_Controller::HTTP_OK);

    }

    /** --------------------------- COPIED FROM MOBILE BACK END (handed over to Semira from this point ) ---------------------------*/

    public function approvals_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $approval['approvals'] = $this->Auth_mobileUsers_Model->get_approval_forEmp($userID, $companyID);
        $approval['leave'] = $this->Mobile_leaveApp_Model->get_leaveApprovals($userID, $companyID);
        $approval['exclaim'] = $this->Auth_mobileUsers_Model->fetch_expanse_claimApproval($userID, $companyID);
        $approval['customer_invoice'] = $this->Auth_mobileUsers_Model->get_DocApprovals($userID, $companyID,'CINV');


        if ($approval) {
            $final_output['success'] = true;
            $final_output['message'] = 'Profile retrieved successfully';
            $final_output['data'] = $approval;

            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = false;
            $final_output['message'] = 'No users were found';
            $final_output['data'] = null;

            $this->response($final_output, REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function getApprovals_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        if($this->get('docId')) {
            $documentCode = $this->get('docId');
        } else {
            $documentCode = 'all';
        }
        $approvalsDoc = $this->Auth_mobileUsers_Model->get_DocApprovals($userID, $companyID, $documentCode);

        if (sizeof($approvalsDoc) > 0) {
            $final_output['success'] = true;
            $final_output['message'] = 'Approvals retrieved successfully';
            $final_output['data'] = $approvalsDoc;
            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = true;
            $final_output['message'] = 'No approvals were found';
            $final_output['data'] = [];
            $this->response($final_output, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }

    }

    public function Approvalcontent_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;

        $documentCode = $this->get('docid');
        $table = $this->get('table');
        $feild = $this->get('feild');
        $fvalue = $this->get('fvalue');

        $approvalDoc['contents'] = $this->Auth_mobileUsers_Model->get_approvalDoc_content($documentCode, $table, $feild, $fvalue, $companyID);
        $approvalDoc['getID'] = $this->Auth_mobileUsers_Model->getApproval_docID($table, $feild, $fvalue);

        if ($approvalDoc) {
            $this->response($approvalDoc, REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No users were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function LeaveApprovalcontent_get()
    {

        $masterID = $this->get('masterid');
        $eid = $this->get('eid');
        $leaveType = $this->get('leavetype');
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];

        $leavegroup = $this->Mobile_leaveApp_Model->check_leavegroup($eid);

        $token_details = $this->jwt->decode($tokenKey, "token");
        $companyID = $token_details->Erp_companyID;

        $approvalDoc['leaveDet'] = $this->Mobile_leaveApp_Model->employeeLeave_details($masterID);
        $approvalDoc['empDet'] = $this->Mobile_leaveApp_Model->getemployeedetails($eid,$companyID);
        $approvalDoc['entitleDet'] = $this->Mobile_leaveApp_Model->employeeLeaveSummery($eid, $leaveType, $leavegroup["isMonthly"]);
        $approvalDoc['attachmentDet'] = $this->Mobile_leaveApp_Model->get_attachments($masterID,$companyID);

        if (sizeof($approvalDoc) > 0) {
            $final_output['success'] = true;
            $final_output['message'] = 'Leave aproval content retrieved successfully';
            $final_output['data'] = $approvalDoc;
            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = false;
            $final_output['message'] = 'No users were found';
            $final_output['data'] = null;
            $this->response($final_output, REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function update_approval_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        //        $userID = $output['token']->id;
        //        $name = $output['token']->name;
        //        $companyID = $output['token']->Erp_companyID;
        $userID = current_userID();
        $name = current_user();
        $companyID = current_companyID();

        $doc = $this->post('docId');
        $status = $this->post('status');
        $masterid = $this->post('masterid');
        $table = $this->post('table');
        $feild = $this->post('field');
        $level_id = $this->post('level');

        if($doc == 'LAC') {
            $postdata = $this->post();
            $return = $this->ApproveOther_Model->update_GL_approvals($userID, $name, $companyID,$postdata,$output['token']);

            $final_output['success'] = true;
            $final_output['message'] = $return['data'];
            $final_output['data'] = $return['email'];

            return $this->response($final_output, REST_Controller::HTTP_OK);
        }

        $confirmed = $this->db->query("SELECT confirmedYN FROM {$table} WHERE {$feild} = {$masterid} AND ( confirmedYN = 0 OR confirmedYN IS NULL)")->row_array();

        if(!empty($confirmed)) {
            $final_output['success'] = true;
            $final_output['message'] = 'Not Confirmed';
            $final_output['data'] = [];
            return $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            if($doc == 'PV') {
                $currentdate = current_date(false);
                $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
                $mastertbl = $this->db->query("SELECT PVdate,PVchequeDate FROM `srp_erp_paymentvouchermaster` where companyID = $companyID And payVoucherAutoId = $masterid ")->row_array();
                $mastertbldetail = $this->db->query("SELECT payVoucherAutoId  FROM `srp_erp_paymentvoucherdetail` where companyID = $companyID And type = 'Item' And payVoucherAutoId = $masterid")->row_array();
                if ($PostDatedChequeManagement == 1 && ($mastertbl['PVchequeDate'] != '' || !empty($mastertbl['PVchequeDate'])) && (empty($mastertbldetail['payVoucherAutoId']) || $mastertbldetail['payVoucherAutoId']==' ') && $status == 1) {
                    if ($mastertbl['PVchequeDate'] > $mastertbl['PVdate'] && ($currentdate < $mastertbl['PVchequeDate'])) {
                        $final_output['success'] = false;
                        $final_output['message'] = 'This is a post dated cheque document. you cannot approve this document before the cheque date.';
                        $final_output['data'] = [];
                        return $this->response($final_output, REST_Controller::HTTP_OK);
                    }
                }
            }
            if ($status == '1') {
                if($doc == 'EC') {
                    $approvedYN = $this->db->query("SELECT	approvedYN FROM {$table} WHERE {$feild} = {$masterid} AND `approvedYN` = 1")->row_array();
                } else {
                    $approvedYN = $this->Mobile_leaveApp_Model->checkApproved($masterid, $doc, $level_id, $companyID);
                }
                if ($approvedYN) {
                    $final_output['success'] = true;
                    $final_output['message'] = 'Already approved';
                    $final_output['data'] = [];
                    return $this->response($final_output, REST_Controller::HTTP_OK);
                } else {
                    $this->db->select($feild);
                    $this->db->where($feild, trim($masterid));
                    $this->db->where('confirmedYN', 2);
                    $this->db->from($table);
                    $po_approved = $this->db->get()->row_array();
                    if (!empty($po_approved)) {
                        $final_output['success'] = true;
                        $final_output['message'] = 'Already rejected';
                        $final_output['data'] = [];
                        return $this->response($final_output, REST_Controller::HTTP_OK);
                    } else {
                        $postdata = $this->post();
                        $return = $this->ApproveOther_Model->update_GL_approvals($userID, $name, $companyID,$postdata,$output['token']);

                        $final_output['success'] = true;
                        $final_output['message'] = $return['data'];
                        $final_output['data'] = $return['email'];

                        return $this->response($final_output, REST_Controller::HTTP_OK);
                    }
                }

            } else { //if rejected
                if($doc == 'EC') {
                    $approvedYN = $this->db->query("SELECT	approvedYN FROM {$table} WHERE {$feild} = {$masterid} AND `approvedYN` = 1")->row_array();
                } else {
                    $approvedYN = $this->Mobile_leaveApp_Model->checkApproved($masterid, $doc, $level_id, $companyID);
                }
                if ($approvedYN) {
                    $final_output['success'] = true;
                    $final_output['message'] = "Already approved";
                    $final_output['data'] = [];
                    return $this->response($final_output, REST_Controller::HTTP_OK);
                } else {
                    $this->db->select($feild);
                    $this->db->where($feild, trim($masterid));
                    $this->db->where('confirmedYN', 2);
                    $this->db->from($table);
                    $po_approved = $this->db->get()->row_array();
                    if (!empty($po_approved)) {
                        $final_output['success'] = true;
                        $final_output['message'] = "Already rejected";
                        $final_output['data'] = [];
                        return $this->response($final_output, REST_Controller::HTTP_OK);
                    } else {
                        $postdata = $this->post();
                        $return = $this->ApproveOther_Model->update_GL_approvals($userID, $name, $companyID,$postdata,$output['token']);
                        $final_output['success'] = true;
                        $final_output['message'] = $return['data'];
                        $final_output['data'] = $return['email'];

                        return $this->response($final_output, REST_Controller::HTTP_OK);
                    }
                }
            }
        }
    }

    public function saveExpenseClaimApproval_put()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $name = $output['token']->username;

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);


        $status = $request->status;
        $exMasterID = $request->ecMasterID;
        $comment = $request->comment;

        $data['res'] = $this->Auth_mobileUsers_Model->save_expense_Claim_approval($userID, $companyID, $companycode, $status, $name, $comment, $exMasterID);
        $this->set_response($data, REST_Controller::HTTP_OK);

    }

    //*****************************leave application**************

    public function check_leavegroup_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;

        $holydaysArr = array();

        $data['status'] = $this->Mobile_leaveApp_Model->check_leavegroup($userID);
        $holydays = $this->Mobile_leaveApp_Model->fetch_holyWeekenddays($companyID);
        $data['leaveTypes'] = $this->Mobile_leaveApp_Model->get_emp_leavetypes($userID, $companyID);
        $data['leaveDet'] = $this->Mobile_leaveApp_Model->getLeavedetails($userID, $companyID);

        $empList = $this->db->query("SELECT EIdNo, ECode, Ename2 FROM srp_employeesdetails AS empTB
                    JOIN srp_erp_employeemanagers AS mangerTB ON mangerTB.empID=empTB.EIdNo
                    WHERE Erp_companyID={$companyID} AND empConfirmedYN=1 AND isDischarged=0
                    AND isSystemAdmin=0 AND mangerTB.active=1 AND companyID={$companyID}
                    AND EIdNo != {$userID}
                    AND managerID = (
                        SELECT managerID FROM srp_erp_employeemanagers WHERE empID={$userID} AND active=1
                    )")->result_array();

        $data['coverEmployee'] = $empList;

        foreach ($holydays as $hs) {

            $date = date('Y,m,d', strtotime($hs['fulldate']));
            array_push($holydaysArr, $date);
        }
        $data['holydays'] = $holydaysArr;


        $this->set_response($data, REST_Controller::HTTP_OK);

    }

    /**
     * This function will send data for employee name dropdown list in Employee Leave Application form
     */
    function leave_form_initial_data_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $emp_id = $output['token']->id;
        $company_id = $output['token']->Erp_companyID;
        $leave_form_initial_data = array();
        $leave_form_initial_data['leaveApplicationEmployees'] = $this->Mobile_leaveApp_Model->leaveApplicationEmployee($emp_id, $company_id);
        $leave_form_initial_data['leaveTypes'] = $this->Mobile_leaveApp_Model->loadLeaveTypeDropDown($emp_id, $company_id);
        $leave_form_initial_data['coveringEmployees'] = $this->Mobile_leaveApp_Model->covering_employees($emp_id, $company_id);

        $query = $this->db->query("select * from srp_erp_leaveapprovalsetup where approvalType=4 and companyID =$company_id");
        if($query->num_rows()>0){
            $leave_form_initial_data['is_covering_employee_required']=1;
        }else{
            $leave_form_initial_data['is_covering_employee_required']=0;
        }

        $final_output['success'] = true;
        $final_output['message'] = '';
        $final_output['data'] = $leave_form_initial_data;

        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function do_upload_aws_S3_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;

        $Erp_companyID = $output['token']->Erp_companyID;
        $ECode = $output['token']->ECode;
        $name = $output['token']->name;
        $username = $output['token']->username;
        $usergroupID = $output['token']->usergroupID;
        $current_pc = $output['token']->current_pc;
        $current_company_id = $output['token']->current_company_id;


        $this->db->trans_start();
        $this->db->select('companyID');
        $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
        $num = $this->db->get('srp_erp_documentattachments')->result_array();
        $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
        $config['upload_path'] = realpath(APPPATH . '../attachments');
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
        $config['max_size'] = '5120'; // 5 MB
        $config['file_name'] = $file_name;

        /** call s3 library */
        $file = $_FILES['document_file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $cc = $companycode;
        $folderPath = !empty($cc) ? $cc . '/' : '';
        if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
            $s3Upload = true;
        } else {
            $s3Upload = false;
        }


        /** end of s3 integration */
        $data['documentID'] = trim($this->input->post('documentID') ?? '');
        $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
        $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
        $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
        $data['fileType'] = trim($ext);
        $data['fileSize'] = trim($file["size"]);
        $data['timestamp'] = date('Y-m-d H:i:s');
        $data['companyID'] = $companyID;
        $data['companyCode'] = $companycode;
        $data['createdUserGroup'] = $usergroupID;
        $data['modifiedPCID'] = $current_pc;
        $data['modifiedUserID'] = $userID;
        $data['modifiedUserName'] = $name;
        $data['modifiedDateTime'] = current_date();
        $data['createdPCID'] = $current_pc;
        $data['createdUserID'] = $userID;
        $data['createdUserName'] = $name;
        $data['createdDateTime'] = current_date();
        $this->db->insert('srp_erp_documentattachments', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $final_output['success'] = true;
            $final_output['message'] = 'Upload failed.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $attachment_links = $this->s3->createPresignedRequest($data['myFileName'], '1 hour');
            $this->db->trans_commit();
            $final_output['success'] = true;
            $final_output['message'] = 'Successfully uploaded.';
            $final_output['data'] = array(
                'id' =>  $last_id,
                'type' => $data['fileType'],
                'fileName' => $data['myFileName'],
                'link' => $attachment_links,
                'description' => $data['attachmentDescription']
            );
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function get_employee_leave_list_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $emp_id = $output['token']->id;
        $company_id = $output['token']->Erp_companyID;
        $date_range_start = $this->get('date_range_start');
        $date_range_end = $this->get('date_range_end');
        $status = $this->get('status');
        $final_output['success'] = true;
        $final_output['message'] = '';
        $final_output['data'] = $this->Mobile_leaveApp_Model->get_employee_leave_list($emp_id, $company_id, $date_range_start, $date_range_end, $status);
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    /**
     * This will return all approved and non approved documents
     * @param SME-API-KEY token
     * @param date_range_start date
     * @param date_range_end date
     * @param docId integer
     * @created Hasitha
     * @created at 2022-08-05
     */
    function get_employee_all_docs_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $emp_id = $output['token']->id;
        $company_id = $output['token']->Erp_companyID;
        $date_range_start = $this->get('date_range_start');
        $date_range_end = $this->get('date_range_end');
        $status = $this->get('status');
        $final_output['success'] = true;
        $final_output['message'] = '';

        $pending_approved_leave = $this->Mobile_leaveApp_Model->get_employee_leave_list($emp_id, $company_id, $date_range_start, $date_range_end, 'confirmed');
        $approved_leave = $this->Mobile_leaveApp_Model->get_employee_leave_list($emp_id, $company_id, $date_range_start, $date_range_end, 'approved');
        $get_customr_invoice  = $this->Auth_mobileUsers_Model->get_DocApprovals($emp_id, $company_id,'CINV');
        $get_customr_invoice_approved  = $this->Auth_mobileUsers_Model->get_DocApprovals($emp_id, $company_id,'CINV',1);


        $final_output['data_pending']['leave'] = $pending_approved_leave;
        $final_output['data_approved']['leave'] = $approved_leave;

        $final_output['data_pending']['cinv'] = $get_customr_invoice;
        $final_output['data_approved']['cinv'] = $get_customr_invoice_approved;

        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

     /**
     * Forget password area
     * @param email reset password email
     * @created Hasitha
     * @created at 2022-08-05
     */

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


    public function leave_employee_calculation_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $policyMasterID = $this->get('policyMasterID');
        $leaveTypeID = $this->get('leaveTypeID');
        $halfDay = $this->get('halfDay');
        $shortLV = $this->get('shortLV');
        $startDate = $this->get('startDate');
        $endDate = $this->get('endDate');
        $isAllowminus = $this->get('isAllowminus');
        $isCalenderDays = $this->get('isCalenderDays');
        $isCalenderDays = ($isCalenderDays == '' ? 0 : $isCalenderDays);

        $data['leavebalance'] = $this->Mobile_leaveApp_Model->employeeLeaveSummery($userID, $leaveTypeID, $policyMasterID);
        $entitleSpan = $data['leavebalance']['balance'];
        $entitleSpan = ($entitleSpan == '' ? 0 : $entitleSpan);

        $leave_data = $this->Mobile_leaveApp_Model->leaveEmployeeCalculation($policyMasterID, $companyID, $leaveTypeID, $halfDay, $shortLV, $startDate, $endDate, $isAllowminus, $isCalenderDays, $entitleSpan);

        if ($leave_data['error'] == 3) {
            $final_output['success'] = false;
            $final_output['message'] = $leave_data['message'];
        } else {
            $final_output['success'] = true;
            $final_output['message'] = '';
        }
        $final_output['data'] = $leave_data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    public function employee_leave_details_get()
    {
        $masterID = $this->get('masterID');
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $details_array = $this->Mobile_leaveApp_Model->employee_leave_details($masterID, $companyID);
        if ($details_array == null) {
            $final_output['success'] = false;
            $final_output['message'] = 'No record found for this id.';
            $final_output['data'] = [];
        } else {
            $final_output['success'] = true;
            $final_output['message'] = 'Leave details retrieved successfully.';
            $final_output['data'] = $details_array;
        }

        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    public function employee_leave_summery_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;

        $leaveType = $this->get('leavetype');
        $policyMasterID = $this->get('PMID');
        $data['leavebalance'] = $this->Mobile_leaveApp_Model->employeeLeaveSummery($userID, $leaveType, $policyMasterID);
        $data['validate_leave'] = $this->Mobile_leaveApp_Model->checkIscalander($companyID, $userID, $leaveType);

        $final_output['success'] = true;
        $final_output['message'] = '';
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    public function save_employeesLeave_post()
    {

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $name = $this->user->name2;

        $coveringEmp = $this->post('coveringEmp');
        $endDate = $this->post('endDate');
        $startDate = $this->post('startDate');//$request->;
        $coveringValidated = $this->post('coveringValidated');//$request->;
        $coveringAvailabilityValidated = $this->post('coveringAvailabilityValidated');//$request->;
        $available = $this->post('available');
        $comments = $this->post('comments');
        $attachmentDescription = $this->post('attachmentDescription');
        $leavetypeid = $this->post('leavetypeid');
        $halfday = $this->post('halfday');
        $confirmed = $this->post('confirmed');
        //$days = $this->post('days');date removed from here then set by another logic in the function.
        $shift = $this->post('shift');
        $policyid = $this->post('policyid');
        $shortLV = $this->post('shortLeave');

        $leavebalance_data['leavebalance'] = $this->Mobile_leaveApp_Model->employeeLeaveSummery($userID, $leavetypeid, $policyid);
        $validate_leave_data['validate_leave'] = $this->Mobile_leaveApp_Model->checkIscalander($companyID, $userID, $leavetypeid);
        $entitleSpan = $leavebalance_data['leavebalance']['balance'];
        $entitleSpan = ($entitleSpan == '' ? 0 : $entitleSpan);
        $isAllowminus = $validate_leave_data['validate_leave']["isAllowminus"];
        $isCalenderDays = $validate_leave_data['validate_leave']["isCalenderDays"];

        $leave_data = $this->Mobile_leaveApp_Model->leaveEmployeeCalculation($policyid, $companyID, $leavetypeid, $halfday, $shortLV, $startDate, $endDate, $isAllowminus, $isCalenderDays, $entitleSpan);

        if ($leave_data['error'] == 1 || $leave_data['error'] == 3) {
            $final_output['success'] = false;
            $final_output['message'] = $leave_data['message'];
            $data['res'] = "6";
            $data['docCode'] = "";
            $final_output['data'] = [];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
            exit;
        }

        //following two values are retrieved from leave calculation method.
        $appliedLeave = $leave_data['appliedLeave'];
        $workingDays = $leave_data['workingDays'];

        if ($isCalenderDays == 1) {
            $days = $appliedLeave;
            $workingDays = $appliedLeave;
            $nonWorkingDays = $days;
            $leaveAvailable = $entitleSpan;

        } else {

            $days = $workingDays;
            $nonWorkingDays = $appliedLeave;
            $leaveAvailable = $entitleSpan;

        }

        if ($policyid == 2) {
            /*if its hourly set value for hour and clear*/
            $hour = $days;
            $days = 0;
            $nonWorkingDays = 0;

            $dteStart = new DateTime($startDate);
            $dteEnd = new DateTime($endDate);
            $startDate = $dteStart->format('Y-m-d H:i:s');
            $endDate = $dteEnd->format('Y-m-d H:i:s');
        }

        $leaveexist = $this->db->query("select * from `srp_erp_leavemaster` WHERE  (approvedYN is null OR approvedYN=0) AND empID={$userID}")->row_array();
        $canApplyMultiple = $this->Mobile_leaveApp_Model->getPolicyValues('LP', 'All', $companyID);
        if (empty($leaveexist)) {

            //        --------------------------------Covering Emp Validations-------------------------------------------------------//


            $leaveCovering = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                              FROM srp_erp_leavemaster
                              WHERE companyID={$companyID} AND coveringEmpID={$userID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                              AND (
                                  ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                    OR
                                  ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                              )")->row_array();

            if (!empty($leaveCovering) && $coveringValidated == 0) {
                $msg = 'You have assigned as covering employee for leave application [' . $leaveCovering['documentCode'] . ']';
                $final_output['success'] = false;
                $final_output['message'] = $msg;

                $data['res'] = "5";
                $data['docCode'] = $leaveCovering['documentCode'];

                $final_output['data'] = $data;
                $this->set_response($final_output, REST_Controller::HTTP_OK);
                exit;

            }


            if (!empty($coveringEmp) && $coveringAvailabilityValidated == 0) {
                $leaveCoveringAvailability = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                              FROM srp_erp_leavemaster
                              WHERE companyID={$companyID} AND empID={$coveringEmp} AND (cancelledYN=0 OR cancelledYN IS NULL )
                              AND (
                                  ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                    OR
                                  ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                              )")->row_array();

                if (!empty($leaveCoveringAvailability)) {
                    $msg = 'Covering employee not available in this date range.<br/>' . $leaveCoveringAvailability['appType'] . ' [' . $leaveCoveringAvailability['documentCode'] . '] ';
                    $final_output['success'] = false;
                    $final_output['message'] = $msg;

                    $data['res'] = "6";
                    $data['docCode'] = "";

                    $final_output['data'] = $data;
                    $this->set_response($final_output, REST_Controller::HTTP_OK);
                    exit;

                }
            }


//        --------------------------------Covering Emp Validations-------------------------------------------------------


            $save_res = $this->Mobile_leaveApp_Model->save_employeesLeave($userID, $companyID, $companycode, $name, $available, $endDate, $leavetypeid, $startDate, $halfday, $confirmed, $coveringEmp, $days, $policyid, $comments, $attachmentDescription, $output['token'], $shift, $workingDays, $nonWorkingDays, $leaveAvailable);
            $data['leaveDet'] = $this->Mobile_leaveApp_Model->getLeavedetails($userID, $companyID);
        } else {

            if ($canApplyMultiple != 0) {
                $leaveCovering = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                              FROM srp_erp_leavemaster
                              WHERE companyID={$companyID} AND coveringEmpID={$userID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                              AND (
                                  ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                    OR
                                  ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                              )")->row_array();

                if (!empty($leaveCovering) && $coveringValidated == 0) {
                    $msg = 'You have assigned as covering employee for leave application [' . $leaveCovering['documentCode'] . ']';
                    $final_output['success'] = false;
                    $final_output['message'] = $msg;
                    $data['res'] = "5";
                    $data['docCode'] = "";
                    $final_output['data'] = $data;
                    $this->set_response($final_output, REST_Controller::HTTP_OK);
                    exit;

                }


                if (!empty($coveringEmp) && $coveringAvailabilityValidated == 0) {
                    $leaveCoveringAvailability = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                              FROM srp_erp_leavemaster
                              WHERE companyID={$companyID} AND empID={$coveringEmp} AND (cancelledYN=0 OR cancelledYN IS NULL )
                              AND (
                                  ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                    OR
                                  ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                              )")->row_array();

                    if (!empty($leaveCoveringAvailability)) {
                        $msg = 'Covering employee not available in this date range.<br/>' . $leaveCoveringAvailability['appType'] . ' [' . $leaveCoveringAvailability['documentCode'] . '] ';
                        $final_output['success'] = false;
                        $final_output['message'] = $msg;
                        $data['res'] = "6";
                        $data['docCode'] = "";

                        $final_output['data'] = $data;
                        $this->set_response($final_output, REST_Controller::HTTP_OK);
                        exit;

                    }
                }


//--------------------------------Covering Emp Validations-------------------------------------------------------

                $save_res = $this->Mobile_leaveApp_Model->save_employeesLeave($userID, $companyID, $companycode, $name, $available, $endDate, $leavetypeid, $startDate, $halfday, $confirmed, $coveringEmp, $days, $policyid, $comments, $attachmentDescription, $output['token'], $shift, $workingDays, $nonWorkingDays, $leaveAvailable);
                $data['leaveDet'] = $this->Mobile_leaveApp_Model->getLeavedetails($userID, $companyID);
            } else {
                $data['res'] = array('4');
                $data['leaveDet'] = $this->Mobile_leaveApp_Model->getLeavedetails($userID, $companyID);
            }


        }
//        var_dump($save_res);

        $data['email'] = $save_res['email'];
        $final_output['success'] = true;
        $final_output['message'] = "";
        $data['res'] = "6";
        $data['docCode'] = "";
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    public function leaveConfirm_put()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $user = $output['token']->name;
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;
        $companycode = $output['token']->company_code;


        $data['result'] = $this->Mobile_leaveApp_Model->leaveConfirm($user, $companyID, $userID, $companycode);
        $data['leaveDet'] = $this->Mobile_leaveApp_Model->getLeavedetails($userID, $companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function leaveDelete_put()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $data['result'] = $this->Mobile_leaveApp_Model->leaveDelete($companyID);
        $data['leaveDet'] = $this->Mobile_leaveApp_Model->getLeavedetails($userID, $companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function leaveReferBack_put()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $leaveMasterID = $request->id;

        $data = array(
            'confirmedYN' => 0,
            'confirmedDate' => null,
            'confirmedByEmpID' => null,
            'confirmedByName' => null,
        );

        $this->db->trans_start();

        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_leavemaster', $data);

        $this->db->where('companyID', $companyID);
        $this->db->where('departmentID', 'LA');
        $this->db->where('documentSystemCode', $leaveMasterID);
        $this->db->delete('srp_erp_documentapproved');


        /*** Delete accrual leave ***/
        $this->db->where('companyID', $companyID);
        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->delete('srp_erp_leaveaccrualmaster');

        $this->db->where('leaveaccrualMasterID', $leaveMasterID);
        $this->db->delete('srp_erp_leaveaccrualdetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $data["res"] = array('1');
        } else {
            $this->db->trans_rollback();
            $data["res"] = array('0');
        }

        $data['leaveDet'] = $this->Mobile_leaveApp_Model->getLeavedetails($userID, $companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    //*****************************leave application**************


    public function dashboard_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $data['widgets'] = $this->Auth_mobileUsers_Model->getAssignedDashboard($userID, $companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function getWidDashboard_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;
        $dbid = $this->get('dbid');

        $data['dashboard'] = $this->Auth_mobileUsers_Model->getAssignedDashboardWidget($userID, $companyID, $dbid);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }


    //************************** widgets****************************
    public function load_overall_performance_get()
    {

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;

        $beginingDate = "";
        $endDate = "";
        $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);


        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[0]["beginingDate"];
            $endDate = $lastTwoYears[0]["endingDate"];

        }

        $month = get_month_list_from_date($beginingDate, $endDate, "Y-m", "1 month", 'M');
        $month_arr = array();
        $OP_array = array();
        $series = array();
        foreach ($month as $row) {
            array_push($month_arr, $row);
        }

        $data["months"] = $month_arr;
        $data["months2"] = get_month_list_from_date($beginingDate, $endDate, "Y-m", "1 month", 'My');
        $data["totalRevenue"] = $this->Auth_mobileUsers_Model->getTotalRevenue($beginingDate, $endDate, $companyID);


        $data["netProfit"] = $this->Auth_mobileUsers_Model->getNetProfit($beginingDate, $endDate, $companyID);
        $op = $this->Auth_mobileUsers_Model->getOverallPerformance($beginingDate, $endDate, $month, $companyID);


        foreach ($op as $key => $row) {

            $new_arr = array();
            foreach ($row as $keyValue => $val) {
                array_push($new_arr, $val);
            }
            array_push($OP_array, $new_arr);

        }

        foreach ($op as $rows) {
            array_push($series, $rows["description"]);
        }

        $data["overallPerformance"] = $OP_array;
        $data["series"] = $series;

        $this->set_response($data, REST_Controller::HTTP_OK);

    }

    public function load_revenue_detail_analysis_get()
    {

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;

        $beginingDate = "";
        $endDate = "";
        $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[0]["beginingDate"];
            $endDate = $lastTwoYears[0]["endingDate"];
        }
        $data["totalRevenue"] = $this->Auth_mobileUsers_Model->getTotalRevenue($beginingDate, $endDate, $companyID);
        $data["revenueDetailAnalysis"] = $this->Auth_mobileUsers_Model->getRevenueDetailAnalysis($beginingDate, $endDate, $companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function load_performance_summary_get()
    {

        $performance = array();
        $performance_amount = array();
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;

        $beginingDate = "";
        $endDate = "";
        $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[0]["beginingDate"];
            $endDate = $lastTwoYears[0]["endingDate"];
        }
        $data = array();

        $per = $this->Auth_mobileUsers_Model->getPerformanceSummary($beginingDate, $endDate, $companyID);
        foreach ($per as $row) {
            array_push($performance, $row["description"]);
            array_push($performance_amount, $row["amount"]);
        }
        $data["per_labels"] = $performance;
        $data["performanceSummary"] = $performance_amount;
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    //************************** widgets****************************

    public function load_fast_moving_item_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;

        $beginingDate = "";
        $endDate = "";
        $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[0]["beginingDate"];
            $endDate = $lastTwoYears[0]["endingDate"];
        }
        $data = array();
        $limit = $this->get('FMI4');
        $data['FMI'] = $this->Auth_mobileUsers_Model->get_fastMovingItem($beginingDate, $endDate, $companyID, $limit);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function load_financial_position_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;

        $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[0]["beginingDate"];
            $endDate = $lastTwoYears[0]["endingDate"];
        }
        $data = array();
        $fetchall = $this->get('CP5');
        $data['BP'] = $this->Auth_mobileUsers_Model->get_bankPosition($companyID, $fetchall);
        $this->set_response($data, REST_Controller::HTTP_OK);

    }

    public function load_overdue_payable_receivable_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $data = array();
        $fetchall = $this->get('fetchall');
        $data['OD_payable'] = $this->Auth_mobileUsers_Model->get_overdue_payable($companyID, $fetchall);
        $data['OD_receivable'] = $this->Auth_mobileUsers_Model->fetch_overdue_receivable($companyID, $fetchall);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function load_postdated_cheque_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $data = array();
        $limit = $this->get('PDCG7');
        $data['cheque_given'] = $this->Auth_mobileUsers_Model->fetch_postdated_cheque_given($companyID, $limit);
        $data['cheque_rcd'] = $this->Auth_mobileUsers_Model->fetch_postdated_cheque_received($companyID, $limit);
        $this->set_response($data, REST_Controller::HTTP_OK);

    }

    public function load_Designation_head_count_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $data = array();
        $des_array = array();
        $desCount = array();

        $des_headCount = $this->Auth_mobileUsers_Model->fetch_Designation_head_count($companyID);

        foreach ($des_headCount as $drow) {
            array_push($des_array, $drow['DesDescription']);
            array_push($desCount, $drow['designationCount']);
        }

        $data['DesDescription'] = $des_array;
        $data['designationCount'] = $desCount;
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function load_to_do_list_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $data['todolist'] = $this->Auth_mobileUsers_Model->getToDoList($companyID, $userID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function load_new_members_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;

        $data['newmembers'] = $this->Auth_mobileUsers_Model->get_newmembers($companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function refer_back_empLeave_cancellation_get()
    {

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $current_userID = $output['token']->id;
        $current_username = $output['token']->username;
        $current_pc = $output['token']->current_pc;
        $company_code = $output['token']->company_code;
        $usergroupID = $output['token']->usergroupID;

        $leaveMasterID = trim($this->input->get('masterID'));
        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, coveringEmpID 
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND companyID={$companyID}")->row_array();

        if ($leave['cancelledYN'] == 1) {
            $final_output['success'] = true;
            $final_output['message'] = 'This document already cancelled. You can not refer backed this.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }

        $level = $leave['currentLevelNo'];
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];

        $setupData = getLeaveApprovalSetup('N', $companyID);
        $approvalEmp_arr = $setupData['approvalEmp'];
        $approvalLevel = $setupData['approvalLevel'];
        $emp_mail_arr = [];

        if ($level <= $approvalLevel) {

            $managers = $this->db->query("SELECT *, {$coveringEmpID} AS coveringEmp FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = 1;

            while ($x <= $approvalLevel) {
                if ($x > $level) { /* Proceed up to current approval level */
                    break;
                }

                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                if ($approvalType == 3) {
                    $nextApprovalEmp_arr = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : null;
                    if (!empty($nextApprovalEmp_arr)) {
                        foreach ($nextApprovalEmp_arr as $hrMangers) {
                            if (!in_array($hrMangers['empID'], $emp_mail_arr)) {
                                $emp_mail_arr[] = $hrMangers['empID'];
                            }
                        }
                    }
                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $nextApprovalEmpID = $managers[$managerType];
                        if (!in_array($nextApprovalEmpID, $emp_mail_arr) && $nextApprovalEmpID != '') {
                            $emp_mail_arr[] = $nextApprovalEmpID;
                        }
                    }
                }
                $x++;
            }
        }

        if (!empty($emp_mail_arr)) {
            $emp_mail_arr = implode(',', $emp_mail_arr);

            $empData = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                            AND EIdNo IN ({$emp_mail_arr})")->result_array();

            foreach ($empData as $eData) {

                $bodyData = 'Leave cancellation ' . $leave['documentCode'] . ' is refer backed.<br/> ';
                $param["empName"] = $eData["Ename2"];
                $param["body"] = $bodyData;

                $mailData = [
                    'approvalEmpID' => $eData["EIdNo"],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $eData["EEmail"],
                    'subject' => 'Leave cancellation refer backed',
                    'param' => $param
                ];

                //send_approvalEmail($mailData);

            }
        }


        $data = array(
            'requestForCancelYN' => null,
            'cancelRequestedDate' => null,
            'cancelRequestByEmpID' => null,
            'cancelRequestComment' => null,
        );

        $this->db->trans_start();

        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_leavemaster', $data);

        $this->db->where('companyID', $companyID);
        $this->db->where('departmentID', 'LA');
        $this->db->where('isCancel', 1);
        $this->db->where('documentSystemCode', $leaveMasterID);
        $this->db->delete('srp_erp_documentapproved');


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $final_output['success'] = true;
            $final_output['message'] = 'Leave Cancellation Referred Back Successfully.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $this->db->trans_rollback();
            $final_output['success'] = true;
            $final_output['message'] = 'Error in leave cancellation refer back.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    public function refer_back_empLeave_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $current_userID = $output['token']->id;
        $current_username = $output['token']->username;
        $current_pc = $output['token']->current_pc;
        $company_code = $output['token']->company_code;
        $usergroupID = $output['token']->usergroupID;

        $leaveMasterID = trim($this->input->get('masterID'));
        $leaveDet = $this->Employee_model->employeeLeave_details($leaveMasterID);

        if ($leaveDet['approvedYN'] == 1) {
            $final_output['success'] = true;
            $final_output['message'] = 'This document already approved. You can not refer backed this.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }

        $data = array(
            'confirmedYN' => 0,
            'confirmedDate' => null,
            'confirmedByEmpID' => null,
            'confirmedByName' => null,
        );

        $this->db->trans_start();

        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_leavemaster', $data);

        $this->db->where('companyID', $companyID);
        $this->db->where('departmentID', 'LA');
        $this->db->where('documentSystemCode', $leaveMasterID);
        $this->db->delete('srp_erp_documentapproved');


        /*** Delete accrual leave ***/
        $this->db->where('companyID', $companyID);
        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->delete('srp_erp_leaveaccrualmaster');

        $this->db->where('leaveMasterID', $leaveMasterID);
        $this->db->delete('srp_erp_leaveaccrualdetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $final_output['success'] = true;
            $final_output['message'] = 'Referred Back Successfully.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $this->db->trans_rollback();
            $final_output['success'] = true;
            $final_output['message'] = 'Error in refer back.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    public function test_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");


        $query = $this->db->query("UPDATE `srp_erp_leavemaster` SET `cancelledYN` = 0, `requestForCancelYN` = 1, `cancelRequestedDate` = '2019-10-07 17:09:23', `cancelRequestComment` = 'test', `cancelRequestByEmpID` = '1138', `currentLevelNo` = 1, `modifiedPCID` = 'Gears-Linux-001', `modifiedUserID` = '1138', `modifiedUserName` = 'hishamm', `modifiedDateTime` = '2019-10-07 17:09:23' WHERE `leaveMasterID` = '295'");
        echo "success";
    }

    public function cancel_leave_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $current_userID = $output['token']->id;
        $current_username = $output['token']->username;
        $current_pc = $output['token']->current_pc;

        $company_code = $output['token']->company_code;
        $usergroupID = $output['token']->usergroupID;

        $leaveMasterID = trim($this->input->get('masterID'));
        $comments = trim($this->input->get('comments'));

        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, coveringEmpID
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND companyID={$companyID}")->row_array();
        $level = 1;

        if ($leave['approvedYN'] != 1) {
            $final_output['success'] = true;
            $final_output['message'] = 'This document not confirmed yet.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);

        }

        if ($leave['cancelledYN'] == 1) {
            $final_output['success'] = true;
            $final_output['message'] = 'This document already canceled.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);

        }

        if ($leave['requestForCancelYN'] == 1) {

            $final_output['success'] = true;
            $final_output['message'] = 'This document already in cancel request.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }

        $this->db->trans_start();


        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];
        $setupData = getLeaveApprovalSetup('N', $companyID);
        $approvalEmp_arr = $setupData['approvalEmp'];
        $approvalLevel = $setupData['approvalLevel'];
        $isManagerAvailableForNxtApproval = 0;
        $nextLevel = null;
        $nextApprovalEmpID = null;
        $data_app = [];


        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($level <= $approvalLevel) {

            $managers = $this->db->query("SELECT *, {$coveringEmpID} AS coveringEmp FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $level;


            /**** Validate is there a manager available for next approval level ****/

            $i = 0;

            while ($x <= $approvalLevel) {

                $isCurrentLevelApproval_exist = 0;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                if ($approvalType == 3) {
                    $isCurrentLevelApproval_exist = 1;

                    if ($isManagerAvailableForNxtApproval == 0) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                        $isManagerAvailableForNxtApproval = 1;
                    }
                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $isCurrentLevelApproval_exist = 1;

                        if ($isManagerAvailableForNxtApproval == 0) {
                            $nextLevel = $x;
                            $nextApprovalEmpID = $managers[$managerType];
                            $isManagerAvailableForNxtApproval = 1;
                        }
                    }

                }

                if ($isCurrentLevelApproval_exist == 1) {
                    $data_app[$i]['companyID'] = $companyID;
                    $data_app[$i]['companyCode'] = $company_code;
                    $data_app[$i]['departmentID'] = 'LA';
                    $data_app[$i]['documentID'] = 'LA';
                    $data_app[$i]['documentSystemCode'] = $leaveMasterID;
                    $data_app[$i]['documentCode'] = $leave['documentCode'];
                    $data_app[$i]['isCancel'] = 1;
                    $data_app[$i]['table_name'] = 'srp_erp_leavemaster';
                    $data_app[$i]['table_unique_field_name'] = 'leaveMasterID';
                    $data_app[$i]['documentDate'] = current_date();
                    $data_app[$i]['approvalLevelID'] = $x;
                    $data_app[$i]['roleID'] = null;
                    $data_app[$i]['approvalGroupID'] = $usergroupID;
                    $data_app[$i]['roleLevelOrder'] = null;
                    $data_app[$i]['docConfirmedDate'] = current_date();
                    $data_app[$i]['docConfirmedByEmpID'] = $current_userID;
                    $data_app[$i]['approvedEmpID'] = null;
                    $data_app[$i]['approvedYN'] = 0;
                    $data_app[$i]['approvedDate'] = null;
                    $i++;
                }

                $x++;
            }

        }

        if (!empty($data_app)) {

            $this->db->insert_batch('srp_erp_documentapproved', $data_app);

            $upData = [
                'cancelledYN' => 0,
                'requestForCancelYN' => 1,
                'cancelRequestedDate' => current_date(),
                'cancelRequestComment' => $comments,
                'cancelRequestByEmpID' => $current_userID,
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => $current_pc,
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $current_username,
                'modifiedDateTime' => current_date()
            ];
            $this->db->where('leaveMasterID', $leaveMasterID);
            $update = $this->db->update('srp_erp_leavemaster', $upData);

            if ($update) {

                $leaveBalanceData = $this->Employee_model->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID'], $companyID);
                $balanceLeave = $leaveBalanceData['balance'];
                //$balanceLeave = ($balanceLeave > 0)?  ($balanceLeave - $leave['days']) : 0;

                if (is_array($nextApprovalEmpID)) {
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                            AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                foreach ($nxtEmpData_arr as $nxtEmpData) {

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = 'Leave application cancellation ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Cancellation Approval',
                        'param' => $param
                    ];

                    //send_approvalEmail($mailData);
                }

                $this->db->trans_commit();

                $final_output['success'] = true;
                $final_output['message'] = 'Leave cancellation approval created successfully.';
                $final_output['data'] = [];
                return $this->set_response($final_output, REST_Controller::HTTP_OK);


            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                $final_output['success'] = true;
                $final_output['message'] = $common_failed;
                $final_output['data'] = [];
                return $this->set_response($final_output, REST_Controller::HTTP_OK);

            }

        }
    }

    public function delete_empLeave_get()
    {
        $masterID = $this->input->get('masterID');
        $companyID = $this->company_id;
        $returned_message = $this->Mobile_leaveApp_Model->delete_empLeave($masterID, $companyID);
        $final_output['success'] = true;
        $final_output['message'] = $returned_message;
        $final_output['data'] = [];
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    public function users_delete()
    {
        $id = (int)$this->get('id');// $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        // Validate the id.
        if ($id <= 0) {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];
        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

    public function complete_todo_put()
    {
        $request_body = file_get_contents('php://input');
        $request = json_decode($request_body);
        $id = $request->autoid;
        $data['todo_update'] = $this->Auth_mobileUsers_Model->update_todolist($id);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }


    public function payroll_months_get()
    {
        $companyID = $this->company_id;
        $emp_id = $this->user_id;

        $data['success'] = true;
        $data['message'] = "";
        $data['data']['payRoll'] = $this->Auth_mobileUsers_Model->payrollMonth_dropDown_with_visible_date($companyID, $emp_id, 'N');
        $data['data']['nonPayRoll'] = $this->Auth_mobileUsers_Model->payrollMonth_dropDown_with_visible_date($companyID, $emp_id, 'Y');
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function payslip_get()
    {
        $id = trim($this->get('id'));
        $isNonPayroll = trim($this->get('isNonPayroll'));

        $records = $this->Auth_mobileUsers_Model->get_paySlip_profile($id, $isNonPayroll);

        if (empty($records)) {
            $data['success'] = false;
            $data['message'] = 'Records not found';
            $data['data'] = (object)null;
        } else {
            $data['success'] = true;
            $data['message'] = 'Success';
            $data['data'] = $records;
        }

        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function saveAttendance_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $data = $this->Auth_mobileUsers_Model->saveAttendance($userID, $companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function attendanceHistory_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;

        $data = $this->Auth_mobileUsers_Model->attendanceHistory($userID, $companyID);
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function segments_drop_get($is_return = 0)
    {
        $data = $this->db->select('segmentID,segmentCode,description')->from('srp_erp_segment')
            ->where('companyID', $this->company_id)->where('status', 1)->get()->result_array();

        if ($is_return) {
            return $data;
        }
        $rt_data['user_segment'] = $this->user->segmentID;
        $rt_data['segments'] = $data;

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function current_time($returnType = '')
    {
        switch ($returnType) {
            case 'd':
                return date('Y-m-d');
                break;
            case 't':
                return date('H:i:s');
                break;
            default :
                return
                    date('Y-m-d H:i:s');
        }
    }

    public function fetch_expense_claims_get()
    {
        $start_date = $this->get('date_range_start');
        $end_date = $this->get('date_range_end');
        $status = $this->get('status');

//        $str = "SELECT expenseClaimMasterAutoID AS id,empCurrencyAmount AS amount,empCurrency,empCurrencyID
//FROM srp_erp_expenseclaimdetails WHERE companyID = $this->company_id GROUP BY expenseClaimMasterAutoID";
        $str = "SELECT SUM(companyLocalAmount) AS transactionAmount, expenseClaimMasterAutoID, companyLocalCurrencyDecimalPlaces, companyLocalCurrency, companyLocalCurrencyID FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID";
//var_dump($str);exit;
        $this->db->select('masTB.expenseClaimMasterAutoID AS id , expenseClaimDate, expenseClaimCode, comments, segmentCode, confirmedYN, 
             confirmedByName, confirmedDate, approvedYN, approvedByEmpName, approvedDate, IFNULL(detTB.transactionAmount, 0) AS amount,detTB.companyLocalCurrencyID')
            ->from('srp_erp_expenseclaimmaster AS masTB')->where('claimedByEmpID', $this->user_id)->where('companyID', $this->company_id)
            ->join("({$str}) AS detTB", 'detTB.expenseClaimMasterAutoID=masTB.expenseClaimMasterAutoID', 'left')
            ->where("expenseClaimDate BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:00'");

        switch ($status) {
            case 1:
                $this->db->where('confirmedYN', 0);
                break;

            case 2:
                $this->db->where("confirmedYN=1 AND approvedYN=0");
                break;

            case 3:
                $this->db->where('approvedYN', 1);
                break;
        }

        $data = $this->db->order_by('masTB.expenseClaimMasterAutoID', 'DESC')->get()->result_array();
        //var_dump($this->db->last_query());exit;
        foreach ($data as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['confirmedYN'] = (int)$row['confirmedYN'];
            $data[$key]['approvedYN'] = (int)$row['approvedYN'];
//            $data[$key]['empCurrencyID'] = (int)$row['empCurrencyID'];
            $currencyID = (int)$row['companyLocalCurrencyID'];
            $currency = $this->db->query("SELECT CurrencyCode AS code, DecimalPlaces AS decimals, CurrencyName AS description 
                                           FROM srp_erp_currencymaster WHERE currencyID = {$currencyID}")->row_array();
            if ($currency != null) {
                $currency['decimals'] = (int)$currency['decimals'];
            }
            $data[$key]['currency'] = $currency;
            $data[$key]['amount'] = (float)$row['amount'];
        }

        $rt_data = [
            'success' => true,
            'message' => '',
            'data' => $data
        ];

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function update_expense_claim_post()
    {
        $segment_id = $this->post('segment_id');

        $segment_code = $this->db->get_where('srp_erp_segment', ['segmentID' => $segment_id])->row('segmentCode');

        $data['documentID'] = 'EC';
        //$data['expenseClaimCode'] = $this->sequence->sequence_generator('EC');
        $data['expenseClaimDate'] = $this->post('date');
        $data['comments'] = $this->post('description');
        $data['segmentID'] = $this->post('segment_id');
        $data['segmentCode'] = $segment_code;
        $data['companyID'] = $this->company_id;
        $data['companyCode'] = $this->company_info->company_code;

//        $data['createdUserGroup'] = $this->company_info->usergroupID;
        $data['modifiedPCID'] = $this->company_info->current_pc;
        $data['modifiedUserID'] = $this->user_id;
        $data['modifiedUserName'] = $this->user->name2;
        $data['modifiedDateTime'] = $this->current_time();
//        $data['timestamp'] = $data['createdDateTime'];
        $data['claimedByEmpID'] = $this->user_id;

        $this->db->where('expenseClaimMasterAutoID', $this->post('id'));
        $this->db->update('srp_erp_expenseclaimmaster', $data);

        $master_data = $this->fetch_expense_claim_get($this->post('id'));

        $master_data['id'] = (int)$master_data['id'];
        $master_data['segmentID'] = (int)$master_data['segmentID'];
        $master_data['confirmedYN'] = (int)$master_data['confirmedYN'];
        $master_data['confirmedByEmpID'] = (int)$master_data['confirmedByEmpID'];
        $master_data['approvedYN'] = (int)$master_data['approvedYN'];
        $master_data['approvedByEmpID'] = (int)$master_data['approvedByEmpID'];
        $rt_data = [
            'success' => true,
            'message' => 'Expense Claim Created Successfully.',
            'data' => $master_data
        ];

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function create_expense_claim_post()
    {
        $segment_id = $this->post('segment_id');

        $segment_code = $this->db->get_where('srp_erp_segment', ['segmentID' => $segment_id])->row('segmentCode');

        $company_id = $this->company_id;
        $company_code = $this->company_info->company_code;
        $usergroupID = $this->company_info->usergroupID;
        $user_id = $this->user_id;
        $name2 = $this->user->name2;
        $current_pc = $this->company_info->current_pc;
        $currentTime = $this->current_time();


        $data['documentID'] = 'EC';
        $data['expenseClaimCode'] = $this->sequence->sequence_generator_mobile('EC', '', $company_id, $company_code, $usergroupID, $user_id, $name2, $current_pc, $currentTime);
        $data['expenseClaimDate'] = $this->post('date');
        $data['comments'] = $this->post('description');
        $data['claimedByEmpName'] = $this->user->name2;
        $data['segmentID'] = $this->post('segment_id');
        $data['segmentCode'] = $segment_code;
        $data['companyID'] = $this->company_id;
        $data['companyCode'] = $this->company_info->company_code;

        $data['createdUserGroup'] = $this->company_info->usergroupID;
        $data['createdPCID'] = $this->company_info->current_pc;
        $data['createdUserID'] = $this->user_id;
        $data['createdUserName'] = $this->user->name2;
        $data['createdDateTime'] = $this->current_time();
        $data['timestamp'] = $data['createdDateTime'];
        $data['claimedByEmpID'] = $this->user_id;

        $this->db->insert('srp_erp_expenseclaimmaster', $data);
        $last_id = $this->db->insert_id();
        $master_data = $this->fetch_expense_claim_get($last_id);

        $master_data['id'] = (int)$master_data['id'];
        $master_data['segmentID'] = (int)$master_data['segmentID'];
        $master_data['confirmedYN'] = (int)$master_data['confirmedYN'];
        $master_data['confirmedByEmpID'] = (int)$master_data['confirmedByEmpID'];
        $master_data['approvedYN'] = (int)$master_data['approvedYN'];
        $master_data['approvedByEmpID'] = (int)$master_data['approvedByEmpID'];
        $rt_data = [
            'success' => true,
            'message' => 'Expense Claim Created Successfully.',
            'data' => $master_data
        ];

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function delete_expense_claim_detail_post()
    {
        $master_id = trim($this->post('masterID'));

        $this->db->trans_start();
        $this->db->where('expenseClaimDetailsID', $master_id)->delete('srp_erp_expenseclaimdetails');
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $rt_data = ['success' => true, 'message' => 'Document details deleted successfully'];
        } else {
            $rt_data = ['success' => false, 'message' => 'Error in document details delete process'];
        }

        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function delete_expense_claim_post()
    {
        $master_id = trim($this->post('masterID'));
        $master_data = Api_spur::document_status('EC', $master_id);

        if ($master_data['error'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => $master_data['message']
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $this->db->trans_start();
        $this->db->where('expenseClaimMasterAutoID', $master_id)->delete('srp_erp_expenseclaimdetails');
        $this->db->where('expenseClaimMasterAutoID', $master_id)->delete('srp_erp_expenseclaimmaster');
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $rt_data = ['success' => true, 'message' => 'Document deleted successfully'];
        } else {
            $rt_data = ['success' => false, 'message' => 'Error in document deleted process'];
        }

        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function delete_attachments_AWS_s3_get()
    {

        $attachmentID = $this->input->get('attachmentID');
        $query = $this->db->get_where("srp_erp_documentattachments", array("attachmentID" => $attachmentID));

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $file_name = $row->myFileName;

            /**AWS S3 delete object */
            $result = $this->s3->delete($file_name);
            /** end of AWS s3 delete object */

            if ($result) {
                $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
                $final_output['success'] = true;
                $final_output['message'] = 'File deleted.';
                $final_output['data'] = [];
                return $this->set_response($final_output, REST_Controller::HTTP_OK);
            } else {
                $final_output['success'] = true;
                $final_output['message'] = 'Failed to delete file.';
                $final_output['data'] = [];
                return $this->set_response($final_output, REST_Controller::HTTP_OK);
            }
        } else {
            $final_output['success'] = true;
            $final_output['message'] = 'File not found.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }

    }

    function claim_category_drop_get($is_return = 0)
    {
        $this->db->select('expenseClaimCategoriesAutoID AS id,glCode,claimcategoriesDescription AS cat_des')
            ->from('srp_erp_expenseclaimcategories')->where('companyID', $this->company_id);
        $data = $this->db->get()->result_array();

        if ($is_return) {
            return $data;
        }

        $rt_data['claim_category'] = $data;

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function currency_drop_get($is_return = 0)
    {
        $this->db->select("com_curr.currencyID,curr.CurrencyCode AS currencyCode, curr.CurrencyName AS currencyName, curr.DecimalPlaces AS decimalPlaces");
        $this->db->from('srp_erp_currencymaster AS curr');
        $this->db->join('srp_erp_companycurrencyassign AS com_curr', 'com_curr.currencyID = curr.currencyID');
        $data = $this->db->where('companyID', $this->company_id)->get()->result_array();

        if ($is_return) {
            return $data;
        }

        $rt_data['user_currency'] = $this->user->payCurrencyID;
        $rt_data['currency'] = $data;

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function fetch_expense_claim_get($id = null)
    {
        $master_id = (!empty($id)) ? $id : $this->get('masterID');
        $company_id = $this->company_id;

        //join this string to have total amount of an specific expense claim.
        $str = "SELECT SUM(companyReportingAmount) as transactionAmount,expenseClaimMasterAutoID,companyReportingCurrencyDecimalPlaces,companyReportingCurrency,companyReportingCurrencyID FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID";

        $data = $this->db->select('srp_erp_expenseclaimmaster.expenseClaimMasterAutoID AS id, 
        srp_erp_expenseclaimmaster.expenseClaimDate, 
        srp_erp_expenseclaimmaster.expenseClaimCode,
         srp_erp_expenseclaimmaster.comments, 
         srp_erp_expenseclaimmaster.segmentID, 
         srp_erp_expenseclaimmaster.segmentCode,
         srp_erp_expenseclaimmaster.confirmedYN,
         srp_erp_expenseclaimmaster.confirmedByEmpID,
         srp_erp_expenseclaimmaster.confirmedByName,
         srp_erp_expenseclaimmaster.confirmedDate,
         srp_erp_expenseclaimmaster.approvedYN,
         srp_erp_expenseclaimmaster.approvedByEmpID,
         srp_erp_expenseclaimmaster.approvedByEmpName,
         srp_erp_expenseclaimmaster.approvedDate,
         srp_erp_segment.description as segmentDesc,
         detTB.transactionAmount as amount,         
         detTB.companyReportingCurrencyDecimalPlaces,
         detTB.companyReportingCurrency,
         detTB.companyReportingCurrencyID,
         srp_erp_currencymaster.currencyName
         ')->from('srp_erp_expenseclaimmaster')->where('srp_erp_expenseclaimmaster.expenseClaimMasterAutoID', $master_id)
            ->join('srp_erp_segment', 'srp_erp_expenseclaimmaster.segmentID = srp_erp_segment.segmentID')->join("({$str}) AS detTB", 'detTB.expenseClaimMasterAutoID=srp_erp_expenseclaimmaster.expenseClaimMasterAutoID', 'left')
            ->join('srp_erp_currencymaster','srp_erp_currencymaster.CurrencyID = detTB.companyReportingCurrencyID', 'LEFT')
            ->where('srp_erp_expenseclaimmaster.companyID', $company_id)->get()->row_array();

        if (empty($id)) {
            $data['id'] = (int)$data['id'];
            $data['confirmedYN'] = (int)$data['confirmedYN'];
            $data['approvedYN'] = (int)$data['approvedYN'];
            $data['segmentID'] = (int)$data['segmentID'];
//            $data['empCurrencyID'] = (int)$data['empCurrencyID'];
            $data['amount'] = (float)$data['amount'];
            $data['currency'] = null;
            if(!empty($data['companyReportingCurrencyID'])){
                $data['currency'] = array("code"=>$data['companyReportingCurrency'],"decimals"=>(int)$data['companyReportingCurrencyDecimalPlaces'],"description"=>$data['currencyName']);
            }
            unset($data['empCurrency']);
            unset($data['companyReportingCurrencyDecimalPlaces']);
            unset($data['currencyName']);
            $master_data = $data;
            unset($data);
            $data['master_data'] = $master_data;

            $detail = $this->db->query("SELECT expenseClaimDetailsID, expenseClaimMasterAutoID, exp_cat.expenseClaimCategoriesAutoID, exp_cat.glCode, claimcategoriesDescription,
	                                exp_det.description, referenceNo, transactionCurrency, round(transactionAmount, transactionCurrencyDecimalPlaces) trAmount,
	                                exp_det.segmentID, srp_erp_segment.segmentCode, srp_erp_segment.description as segdescription,exp_det.transactionCurrencyID,
exp_det.transactionCurrency, CurrencyName, transactionCurrencyDecimalPlaces AS decimalPlace
	                                FROM srp_erp_expenseclaimdetails AS exp_det
	                                JOIN srp_erp_expenseclaimcategories AS exp_cat ON exp_det.expenseClaimCategoriesAutoID = exp_cat.expenseClaimCategoriesAutoID
	                                JOIN srp_erp_segment ON exp_det.segmentID = srp_erp_segment.segmentID
	                                JOIN srp_erp_currencymaster ON exp_det.transactionCurrencyID = srp_erp_currencymaster.currencyID
	                                WHERE expenseClaimMasterAutoID = {$master_id} AND exp_det.companyID = {$company_id} ")->result_array();

            $data['details'] = array();
            foreach ($detail as $key => $row) {
                $data['details'][$key]['expenseClaimDetailsID'] = (int)$row['expenseClaimDetailsID'];
                $data['details'][$key]['expenseClaimMasterAutoID'] = (int)$row['expenseClaimMasterAutoID'];
                $data['details'][$key]['description'] = $row['description'];
                $data['details'][$key]['referenceNo'] = $row['referenceNo'];
                $data['details'][$key]['transactionAmount'] = (float)$row['trAmount'];

                $data['details'][$key]['category']['categoryId'] = (int)$row['expenseClaimCategoriesAutoID'];
                $data['details'][$key]['category']['glCode'] = $row['glCode'];
                $data['details'][$key]['category']['description'] = $row['claimcategoriesDescription'];

                $data['details'][$key]['segment']['segmentId'] = (int)$row['segmentID'];
                $data['details'][$key]['segment']['segmentCode'] = $row['segmentCode'];
                $data['details'][$key]['segment']['segdescription'] = $row['segdescription'];

                $data['details'][$key]['currency']['currencyID'] = (int)$row['transactionCurrencyID'];
                $data['details'][$key]['currency']['currencyCode'] = $row['transactionCurrency'];
                $data['details'][$key]['currency']['currencyName'] = $row['CurrencyName'];
                $data['details'][$key]['currency']['decimalPlaces'] = (int)$row['decimalPlace'];
            }

            $data['attachments'] = $this->get_attachments('EC', $master_data['id'], $company_id);
            $data['approval_details']=$this->Mobile_leaveApp_Model->fetch_all_approval_users_modal($company_id, "EC", $master_data['id']);
            //var_dump($data['approval_details']);exit;
        }

        $rt_data = [
            'success' => true,
            'message' => '',
            'data' => $data
        ];

        if (!empty($id)) {
            return $data;
        }

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function get_attachments($docType, $masterID, $companyID)
    {
        $this->db->where('documentSystemCode', $masterID);
        $this->db->where('documentID', $docType);
        $this->db->where('companyID', $companyID);
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        //var_dump($this->db->last_query());exit;
        $result = '';
        $x = 1;
        $attachment_links = array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $attachment = array();

                $attachment['id'] = (int)$val['attachmentID'];
                $attachment['link'] = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');
                $attachment['type'] = $val['fileType'];
                $attachment['fileName'] = $val['myFileName'];
                $attachment['description'] = $val['attachmentDescription'];
//                if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
//                    //$result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
//                } else {
//                    //$result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
//                }
                array_push($attachment_links, $attachment);
                $x++;
            }
        } else {
            //$result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        return $attachment_links;
    }

    public function add_expense_claim_details_post()
    {
        $master_id = $this->post('masterID');
        $master_data = Api_spur::document_status('EC', $master_id);

        if ($master_data['error'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => $master_data['message']
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $data['expenseClaimMasterAutoID'] = $master_id;
        $data['expenseClaimCategoriesAutoID'] = $this->post('category_id');
        $data['description'] = trim($this->post('description'));
        $data['referenceNo'] = trim($this->post('reference'));
        $data['segmentID'] = $this->post('segment_id');

        $tr_curr_id = $this->post('currency_id');
        $local_curr = $this->company_info->local_currency;
        $rpt_curr = $this->company_info->rpt_currency;
        $conversion_local = Api_spur::currency_conversion($tr_curr_id, $local_curr);
        $conversion_rpt = Api_spur::currency_conversion($tr_curr_id, $rpt_curr);

        $data['transactionCurrencyID'] = $tr_curr_id;
        $data['transactionCurrency'] = $conversion_local['currencyCode'];
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = $conversion_local['decimalPlaces'];

        $amount = $this->post('amount');
        $amount = round($amount, $conversion_local['decimalPlaces']);
        $data['transactionAmount'] = $amount;


        $data['companyLocalCurrencyID'] = $local_curr;
        $data['companyLocalCurrency'] = $conversion_local['con_currencyCode'];
        $data['companyLocalExchangeRate'] = $conversion_local['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $conversion_local['con_decimalPlaces'];
        $data['companyLocalAmount'] = round(($amount / $conversion_local['conversion']), $conversion_local['con_decimalPlaces']);

        $data['companyReportingCurrencyID'] = $rpt_curr;
        $data['companyReportingCurrency'] = $conversion_rpt['con_currencyCode'];
        $data['companyReportingExchangeRate'] = $conversion_rpt['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $conversion_rpt['con_decimalPlaces'];
        $data['companyReportingAmount'] = round(($amount / $conversion_rpt['conversion']), $conversion_rpt['con_decimalPlaces']);


        $this->db->select('payCurrencyID,payCurrency')->where('EIdNo', $this->user_id)->from('srp_employeesdetails');
        $emp_curr = $this->db->get()->row('payCurrencyID');
        $conversion_emp = Api_spur::currency_conversion($tr_curr_id, $emp_curr);

        $data['empCurrencyID'] = $emp_curr;
        $data['empCurrency'] = $conversion_emp['con_currencyCode'];
        $data['empCurrencyExchangeRate'] = $conversion_emp['conversion'];
        $data['empCurrencyDecimalPlaces'] = $conversion_emp['con_decimalPlaces'];
        $data['empCurrencyAmount'] = round(($amount / $conversion_emp['conversion']), $conversion_emp['con_decimalPlaces']);

        $data['companyID'] = $this->company_id;
        $data['companyCode'] = $this->company_info->company_code;
        $data['createdUserGroup'] = $this->company_info->usergroupID;
        $data['createdPCID'] = $this->company_info->current_pc;
        $data['createdUserID'] = $this->user_id;
        $data['createdUserName'] = $this->user->name2;
        $data['createdDateTime'] = $this->current_time();
        $data['timestamp'] = $data['createdDateTime'];

        $this->db->insert('srp_erp_expenseclaimdetails', $data);
        $expense_claim_details_id = $this->db->insert_id();

        $query = $this->db->get_where('srp_erp_expenseclaimdetails', array('expenseClaimDetailsID' => $expense_claim_details_id));


        $expense_claim_details_data = $query->row_array();
        $expense_claim_details['expenseClaimDetailsID'] = (int)$expense_claim_details_data['expenseClaimDetailsID'];
        $expense_claim_details['expenseClaimMasterAutoID'] = (int)$expense_claim_details_data['expenseClaimMasterAutoID'];
        $expense_claim_details['expenseClaimCategoriesAutoID'] = (int)$expense_claim_details_data['expenseClaimCategoriesAutoID'];
        $expense_claim_details['description'] = $expense_claim_details_data['description'];
        $expense_claim_details['referenceNo'] = $expense_claim_details_data['referenceNo'];
        $expense_claim_details['segmentID'] = (int)$expense_claim_details_data['segmentID'];
        $expense_claim_details['transactionCurrencyID'] = (int)$expense_claim_details_data['transactionCurrencyID'];
        $expense_claim_details['transactionAmount'] = (float)$expense_claim_details_data['transactionAmount'];

        $rt_data = [
            'success' => true,
            'message' => 'Details added successfully',
            'data' => $expense_claim_details
        ];
        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function expense_claim_drops_get()
    {
        $data = [];
        if ($this->get('segment')) {
            $data['user_segment'] = (int)$this->user->segmentID;
            //
            $segment_data = $this->segments_drop_get(1);
            $segment_data_array = array();
            foreach ($segment_data as $item) {
                $segment_data_item = array();
                $segment_data_item['segmentID'] = (int)$item['segmentID'];
                $segment_data_item['segmentCode'] = $item['segmentCode'];
                $segment_data_item['description'] = $item['description'];
                array_push($segment_data_array, $segment_data_item);
            }
            $data['segment'] = $segment_data_array;
        }

        if ($this->get('currency')) {
            $data['user_currency'] = (int)$this->user->payCurrencyID;
            // $data['currency'] = $this->currency_drop_get(1);
            $currency_data = $this->currency_drop_get(1);
            $currency_data_array = array();
            foreach ($currency_data as $item) {
                $currency_data_item = array();
                $currency_data_item['currencyID'] = (int)$item['currencyID'];
                $currency_data_item['currencyCode'] = $item['currencyCode'];
                $currency_data_item['currencyName'] = $item['currencyName'];
                $currency_data_item['decimalPlaces'] = (int)$item['decimalPlaces'];
                array_push($currency_data_array, $currency_data_item);
            }
            $data['currency'] = $currency_data_array;
        }

        if ($this->get('ex_claim')) {
            //$data['claim_category'] = $this->claim_category_drop_get(1);
            $expense_claim_data = $this->claim_category_drop_get(1);
            $expense_claim_data_array = array();
            foreach ($expense_claim_data as $item) {
                $expense_claim_data_item = array();
                $expense_claim_data_item['id'] = (int)$item['id'];
                $expense_claim_data_item['glCode'] = $item['glCode'];
                $expense_claim_data_item['cat_des'] = $item['cat_des'];
                array_push($expense_claim_data_array, $expense_claim_data_item);
            }
            $data['claim_category'] = $expense_claim_data_array;
        }

        $final_output['success'] = true;
        $final_output['message'] = "";
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    public function confirm_expense_claim_post()
    {
        $master_id = trim($this->post('masterID'));
        $master_data = Api_spur::document_status('EC', $master_id, ['more_column' => 1]);

        if ($master_data['error'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => $master_data['message']
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $this->db->select('expenseClaimDetailsID')->where('expenseClaimMasterAutoID', $master_id);
        $detail = $this->db->from('srp_erp_expenseclaimdetails')->get()->row_array();

        if (empty($detail)) {
            $rt_data = ['success' => false, 'message' => 'There is no detail records found to confirm this document'];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }


        $this->db->select('managerID')->where([
            'empID' => $master_data['data']['claimedByEmpID'],
            'active' => 1
        ]);
        $manager_id = $this->db->from('srp_erp_employeemanagers')->get()->row('managerID');

        if (empty($manager_id)) {
            $rt_data = ['success' => false, 'message' => 'Reporting manager not available for this employee'];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $data = [
            'confirmedYN' => 1,
            'confirmedDate' => $this->current_time(),
            'confirmedByEmpID' => $this->user_id,
            'confirmedByName' => $this->user_name
        ];

        $this->db->where('expenseClaimMasterAutoID', $master_id)->update('srp_erp_expenseclaimmaster', $data);

        $documentData = $this->db->query("SELECT expenseClaimCode, claimedByEmpName FROM srp_erp_expenseclaimmaster WHERE expenseClaimMasterAutoID = {$master_id}")->row_array();


        $company_id = $this->company_id;
        $token_android = firebaseToken($manager_id, 'android', $company_id);
        $token_ios = firebaseToken($manager_id, 'apple', $company_id);

        $firebaseBody = $documentData['claimedByEmpName'] . " has applied for an expense claim.";
        $this->load->library('firebase_notification');

        if(!empty($token_android)) {
            $this->firebase_notification->sendFirebasePushNotification("New Expense Claim", $firebaseBody, $token_android, 2, $documentData['expenseClaimCode'], "EC", $master_id, "android");
        }
        if(!empty($token_ios)) {
            $this->firebase_notification->sendFirebasePushNotification("New Expense Claim", $firebaseBody, $token_ios, 2, $documentData['expenseClaimCode'], "EC", $master_id, "apple");
        }

        $rt_data = ['success' => true, 'message' => 'Approvals Created Successfully'];
        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function refer_back_expense_claim_post()
    {
        $master_id = trim($this->post('masterID'));
        $master_data = Api_spur::document_status('EC', $master_id, ['isOn' => 1]);

        if ($master_data['error'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => $master_data['message']
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $data = [
            'confirmedYN' => 0,
            'confirmedDate' => null,
            'confirmedByEmpID' => null,
            'confirmedByName' => null
        ];

        $this->db->where('expenseClaimMasterAutoID', $master_id)->update('srp_erp_expenseclaimmaster', $data);

        $documentData = $this->db->query("SELECT expenseClaimCode, claimedByEmpName, claimedByEmpID FROM srp_erp_expenseclaimmaster WHERE expenseClaimMasterAutoID = {$master_id}")->row_array();
        $this->db->select('managerID')->where(['empID' => $documentData['claimedByEmpID'], 'active' => 1]);
        $manager_id = $this->db->from('srp_erp_employeemanagers')->get()->row('managerID');

        $company_id = $this->company_id;
        $token_android = firebaseToken($manager_id, 'android', $company_id);
        $token_ios = firebaseToken($manager_id, 'apple', $company_id);

        $firebaseBody = $documentData['claimedByEmpName'] . " has referred back his expense claim.";

        $this->load->library('firebase_notification');
        if(!empty($token_android)) {
            $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approval Referred Back", $firebaseBody, $token_android, 6, $documentData['expenseClaimCode'], "EC", $master_id, "android");
        }
        if(!empty($token_ios)) {
            $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approval Referred Back", $firebaseBody, $token_ios, 6, $documentData['expenseClaimCode'], "EC", $master_id, "apple");
        }

        $rt_data = ['success' => true, 'message' => 'Document refer backed successfully'];
        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function fetch_attachments_get()
    {

        $documentSystemCode = $this->get('documentSystemCode');
        $documentID = $this->get('documentID');
        $confirmedYN = $this->get('confirmedYN');
        $companyID = $this->company_id;
        $this->db->where('documentSystemCode', $documentSystemCode);
        $this->db->where('documentID', $documentID);
        $this->db->where('companyID', $companyID);
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        //var_dump($this->db->last_query());exit;
        $result = '';
        $x = 1;
        $attachment_links = array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $attachment = array();
                $attachment['link'] = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');
                $attachment['type'] = $val['fileType'];
                $attachment['confirmedYN'] = $confirmedYN;
                if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
                    //$result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
                } else {
                    //$result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
                }
                array_push($attachment_links, $attachment);
                $x++;
            }
        } else {
            //$result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }

        $final_output['success'] = true;
        $final_output['message'] = '';
        $final_output['data'] = $attachment_links;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    public function update_employeesLeave_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;


        $empID = $userID;
        $leaveMasterID = $this->post('leaveMasterID');
        $leavetypeid = $this->post('leavetypeid');
        $coveringEmpID = $this->input->post('coveringEmp');
        $startDate = $this->post('startDate');
        $endDate = $this->post('endDate');
        $comments = $this->post('comments');
        $confirmed = $this->post('confirmed');
        $policyid = $this->post('policyid');
        $attachmentDescription = $this->post('attachmentDescription');
        $halfday = $this->post('halfday');
        $shift = $this->post('shift');
        $shortLV = $this->post('shortLeave');
        $leaveGroupID = $this->post('leaveGroupID');

        $leavebalance_data['leavebalance'] = $this->Mobile_leaveApp_Model->employeeLeaveSummery($userID, $leavetypeid, $policyid);
        $validate_leave_data['validate_leave'] = $this->Mobile_leaveApp_Model->checkIscalander($companyID, $userID, $leavetypeid);
        $entitleSpan = $leavebalance_data['leavebalance']['balance'];
        $entitleSpan = ($entitleSpan == '' ? 0 : $entitleSpan);
        $isAllowminus = $validate_leave_data['validate_leave']["isAllowminus"];
        $isCalenderDays = $validate_leave_data['validate_leave']["isCalenderDays"];

        $leave_data = $this->Mobile_leaveApp_Model->leaveEmployeeCalculation($policyid, $companyID, $leavetypeid, $halfday, $shortLV, $startDate, $endDate, $isAllowminus, $isCalenderDays, $entitleSpan);

        if ($leave_data['error'] == 3) {
            $final_output['success'] = false;
            $final_output['message'] = $leave_data['message'];
            $data['res'] = "6";
            $data['docCode'] = "";
            $data['email'] = null;
            $final_output['data'] = [];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
            exit;
        }

        //following two values are retrieved from leave calculation method.
        $appliedLeave = $leave_data['appliedLeave'];
        $workingDays = $leave_data['workingDays'];

        //number of days
//        if ($startDate == $endDate) {
//            //same day
//            $appliedLeave = 1;
//        } else {
//            $s_date = date_create($startDate);
//            $e_date = date_create($endDate);
//            $appliedLeave = date_diff($s_date, $e_date)->d + 1;//+1 is added for count the last day. date_diff function returns the difference but we want to count one more day.
//        }

        /*leave adjustment status for last leave group change of this employee */
        $adjustmentStatus = $this->db->query("SELECT adjustmentDone FROM srp_erp_leavegroupchangehistory WHERE empID={$empID} ORDER BY id DESC LIMIT 1")->row('adjustmentDone');
        if ($adjustmentStatus == 0) {
            die(json_encode(['e', 'Leave adjustment process was not processed for previous leave group change.<br/>
                                       Please process the leave adjustment and try again.']));
        }

        $leaveDet = $this->Mobile_leaveApp_Model->employeeLeave_details($leaveMasterID);


        if ($leaveDet['approvedYN'] == 1) {
            $final_output['success'] = false;
            $final_output['message'] = "This document already approved. You can not make changes on this.";
            $data['res'] = "";
            $data['docCode'] = "";
            $data['email'] = null;
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);

        }

        if ($leaveDet['confirmedYN'] == 1) {
            $final_output['success'] = false;
            $final_output['message'] = "This document already confirmed. You can not make changes on this.";
            $data['res'] = "";
            $data['docCode'] = "";
            $data['email'] = null;
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);

        }

        $leaveTypeID = $this->input->post('leaveTypeID');
        $applicationType = $this->input->post('applicationType');
        $canApplyMultiple = $this->Mobile_leaveApp_Model->getPolicyValues('LP', 'All', $companyID);

        if ($applicationType == 1 AND $canApplyMultiple == 0) {
            $leaveExist = $this->db->query("select leaveMasterID from srp_erp_leavemaster WHERE (approvedYN is null OR approvedYN=0)
                                                AND empID={$empID} AND applicationType=1 AND leaveTypeID={$leaveTypeID}")->row('leaveMasterID');
            if (!empty($leaveExist) && $leaveExist != $leaveMasterID) {
                $final_output['success'] = false;
                $final_output['message'] = "Employee has pending leave application in process.";
                $data['res'] = "";
                $data['docCode'] = "";
                $data['email'] = null;
                $final_output['data'] = [];
                return $this->set_response($final_output, REST_Controller::HTTP_OK);
            }
        }

        if ($applicationType == 2) {
            $isPlanApplicable = $this->db->query("SELECT isPlanApplicable FROM srp_erp_leavetype
                                                  WHERE leaveTypeID={$leaveTypeID}")->row('isPlanApplicable');

            if ($isPlanApplicable != 1) {
                $final_output['success'] = false;
                $final_output['message'] = "This leave type is not applicable for leave plan.";
                $data['res'] = "";
                $data['docCode'] = "";
                $data['email'] = null;
                $final_output['data'] = [];
                return $this->set_response($final_output, REST_Controller::HTTP_OK);
            }
        }

        /***Validate is there is a leave falling in this date range ***/
        //$companyID = current_companyID();

        $leaveApp = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                                          FROM srp_erp_leavemaster
                                          WHERE companyID={$companyID} AND empID={$empID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                                          AND (
                                              ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                                OR
                                              ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                                          )")->row_array();
//var_dump($this->db->last_query());exit;
        if (!empty($leaveApp) && $leaveApp['leaveMasterID'] != $leaveMasterID) {
            $final_output['success'] = false;
            $final_output['message'] = 'There is a ' . $leaveApp['appType'] . ' [' . $leaveApp['documentCode'] . '] already exist in this date range ';
            $data['res'] = "";
            $data['docCode'] = "";
            $data['email'] = null;
            $final_output['data'] = $data;
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }

        //Validate this employee assigned for a leave covering on this leave date
        $coveringValidated = $this->input->post('coveringValidated');
        $leaveCovering = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                                          FROM srp_erp_leavemaster
                                          WHERE companyID={$companyID} AND coveringEmpID={$empID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                                          AND (
                                              ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                                OR
                                              ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                                          )")->row_array();

        if (!empty($leaveCovering) && $coveringValidated == 0) {
//            $msg = 'You have assigned as covering employee for leave application [' . $leaveCovering['documentCode'] . ']';
//            $resData = [
//                'covering' => '1',
//                'requestType' => 'update',
//                'isConfirmed' => $this->input->post('isConfirmed')
//            ];
            $final_output['success'] = false;
            $final_output['message'] = 'You have assigned as covering employee for leave application [' . $leaveCovering['documentCode'] . ']';
            $data['res'] = "";
            $data['docCode'] = "";
            $data['email'] = null;
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }


        if ($this->input->post('isConfirmed') == 1) {
            //Check covering employee is in approval setup
            $isCovering = $this->db->query("SELECT approvalSetupID FROM srp_erp_leaveapprovalsetup WHERE companyID={$companyID} AND approvalType=4")->row('approvalSetupID');

            if (!empty($isCovering) && empty($coveringEmpID)) {
                'You have assigned as covering employee for leave application [' . $leaveCovering['documentCode'] . ']';
                die(json_encode(array('e', 'Covering employee is required')));
            }
        }


        $coveringAvailabilityValidated = $this->input->post('coveringAvailabilityValidated');

        //Validate covering employee leave get clash with this leave date
        if (!empty($coveringEmpID) && $coveringAvailabilityValidated == 0) {
            $leaveCoveringAvailability = $this->db->query("SELECT leaveMasterID, documentCode, IF(applicationType=1, 'leave application', 'leave plan') AS appType
                                          FROM srp_erp_leavemaster
                                          WHERE companyID={$companyID} AND empID={$coveringEmpID} AND (cancelledYN=0 OR cancelledYN IS NULL )
                                          AND (
                                              ( ( '{$startDate}' BETWEEN startDate AND endDate ) OR ( '{$endDate}' BETWEEN startDate AND endDate ) )
                                                OR
                                              ( ( startDate BETWEEN '{$startDate}' AND '{$endDate}' ) OR ( endDate BETWEEN '{$startDate}' AND '{$endDate}' ) )
                                          )")->row_array();

            if (!empty($leaveCoveringAvailability)) {
//                $msg = 'Covering employee not available in this date range.<br/>' . $leaveCoveringAvailability['appType'] . ' [' . $leaveCoveringAvailability['documentCode'] . '] ';
//                $resData = [
//                    'covering' => '2',
//                    'requestType' => 'update',
//                    'isConfirmed' => $this->input->post('isConfirmed')
//                ];
                $final_output['success'] = false;
                $final_output['message'] = 'Covering employee not available in this date range.<br/>' . $leaveCoveringAvailability['appType'] . ' [' . $leaveCoveringAvailability['documentCode'] . '] ';
                $data['res'] = "";
                $data['docCode'] = "";
                $data['email'] = null;
                $final_output['data'] = [];
                return $this->set_response($final_output, REST_Controller::HTTP_OK);
            }
        }


        if ($appliedLeave >= 0) {
            $empname = $this->user->name2;
            $res = $this->Mobile_leaveApp_Model->update_employeesLeave($leaveMasterID, $empID, $leavetypeid, $startDate, $endDate, $confirmed, $entitleSpan, $halfday, $shift, $comments
                , $isCalenderDays, $appliedLeave, $workingDays, $policyid, $applicationType, $coveringEmpID, $output, $attachmentDescription, $companyID, $empname);

            $data['email'] = $res['email'];
            $final_output['success'] = true;
            $final_output['message'] = "";
            $data['res'] = "6";
            $data['docCode'] = "";
            $final_output['data'] = $data;
            $this->set_response($final_output, REST_Controller::HTTP_OK);

        } else {

            $data['email'] = null;
            $final_output['success'] = false;
            $final_output['message'] = "Please check the start date and end date";
            $data['res'] = "6";
            $data['docCode'] = "";
            $final_output['data'] = [];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }


    public function delete_attachment_in_leave_form_post()
    {
        $attachmentID = $this->input->get('attachmentID');
        $query = $this->db->get_where("srp_erp_documentattachments", array("attachmentID" => $attachmentID));

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $file_name = $row->myFileName;

            /**AWS S3 delete object */
            $result = $this->s3->delete($file_name);
            /** end of AWS s3 delete object */

            if ($result) {
                $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
                $final_output['success'] = true;
                $final_output['message'] = "Successfully deleted.";
                $this->set_response($final_output, REST_Controller::HTTP_OK);
            } else {
                $final_output['success'] = false;
                $final_output['message'] = "Delete attempt was failed.";
                $this->set_response($final_output, REST_Controller::HTTP_OK);
            }
        } else {
            $final_output['success'] = true;
            $final_output['message'] = 'File not found.';
            $final_output['data'] = [];
            return $this->set_response($final_output, REST_Controller::HTTP_OK);
        }


    }

    function attendance_list_post()
    {

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $name = $output['token']->username;
        $request_type = "my";//$this->input->post('request-type');
        $from_date = $this->post("from_date");;//$this->input->post('from_date');
        $to_date = $this->post("to_date");;//$this->input->post('to_date');
        $date_format_policy_details = $this->session_model->fetch_company_policy($companyID);
        $date_format_policy = $date_format_policy_details['DF']['All'][0]["policyvalue"];

        $fromDate = input_format_date($from_date, $date_format_policy);
        $toDate = input_format_date($to_date, $date_format_policy);

        if ($fromDate > $toDate) {
            die(json_encode(['e', 'To date should be greater than from date']));
        }

        $emp_id = $userID;//current_userID();
        $att_type_arr = $this->post("att_type");//$this->input->post('att_type');
        $att_type_list = implode(',', $att_type_arr);

        if ($request_type == 'my_employee') {
            $emp_id_arr = $this->input->post('empID');
            $emp_id_list = implode(',', $emp_id_arr);

            $att_rec = $this->db->query("SELECT isWeekEndDay,approvedComment,approvedYN,empID,ECode, Ename2,empMachineID, floorDescription, ID, machineID, att_rev.floorID, attendanceDate, 
                                     presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, 
                                     DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours,normalDay,mustCheck,normalTime, weekend, holiday, NDaysOT, weekendOTHours, 
                                     holidayOTHours,realTime#, att_rev.empComment
                                     FROM srp_erp_pay_empattendancereview AS att_rev
                                     JOIN srp_employeesdetails ON att_rev.empID = srp_employeesdetails.EIdNo
                                     LEFT JOIN srp_erp_pay_floormaster ON srp_erp_pay_floormaster.floorID = att_rev.floorID 
                                     WHERE empID IN ({$emp_id_list}) AND attendanceDate BETWEEN '{$fromDate}' AND '{$toDate}' AND att_rev.companyID = {$companyID} AND confirmedYN=1
                                     AND EXISTS (
                                          SELECT manger_tb.empID FROM srp_erp_employeemanagers AS manger_tb WHERE active = 1 AND managerID = {$emp_id} 
                                          AND companyID = {$companyID} AND manger_tb.empID = att_rev.empID
                                     ) AND att_rev.presentTypeID IN ({$att_type_list})")->result_array();
        } else {
            $att_rec = $this->db->query("SELECT isWeekEndDay,approvedComment,approvedYN,empID,ECode, Ename2,empMachineID, floorDescription, ID, machineID, att_rev.floorID, attendanceDate, 
                                     presentTypeID, DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, 
                                     DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours,normalDay,mustCheck,normalTime, weekend, holiday, NDaysOT, weekendOTHours, 
                                     holidayOTHours,realTime#, att_rev.empComment
                                     FROM srp_erp_pay_empattendancereview AS att_rev
                                     JOIN srp_employeesdetails ON att_rev.empID = srp_employeesdetails.EIdNo
                                     LEFT JOIN srp_erp_pay_floormaster ON srp_erp_pay_floormaster.floorID = att_rev.floorID 
                                     WHERE empID = {$emp_id} AND attendanceDate BETWEEN '{$fromDate}' AND '{$toDate}' AND att_rev.companyID = {$companyID} AND confirmedYN=1
                                     AND att_rev.presentTypeID IN ({$att_type_list})")->result_array();
        }

        if (empty($att_rec)) {
            $final_output['success'] = true;
            $final_output['message'] = "No record found.";
            $final_output['data'] = [];
            $this->set_response($final_output, REST_Controller::HTTP_OK);

        } else {
            $attendance_data = array();
            foreach ($att_rec as $item) {
                $attendance_record = array();
                $attendance_record['attendanceDate'] = $item['attendanceDate'];
                $attendance_record['checkIn'] = $item['checkIn'];
                $attendance_record['checkOut'] = $item['checkOut'];
                $attendance_record['onDuty'] = $item['onDuty'];
                $attendance_record['offDuty'] = $item['offDuty'];
                $attendance_record['isWeekEndDay'] = (int)$item['isWeekEndDay'];
                $attendance_record['presentTypeID'] = (int)$item['presentTypeID'];
                $presentTypeID = $attendance_record['presentTypeID'];
                $attendance_record['presentTypeDes'] = $this->db->query("SELECT PresentTypeDes FROM srp_sys_attpresenttype WHERE PresentTypeID=$presentTypeID")->result_array()[0]['PresentTypeDes'];

                array_push($attendance_data, $attendance_record);
            }
            $data['att_rec'] = $att_rec;
            $data['is_edit'] = true;

            $final_output['success'] = true;
            $final_output['message'] = "Attendance list retrieved successfully";
            $final_output['data'] = $attendance_data;
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }

    }

    public function document_list_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $data['document_codes'] = $this->Mobile_leaveApp_Model->all_document_code_drop($companyID);
        $data['documents'] = $this->fetch_approvaldocuments($userID, $companyID);
        $final_output['success'] = true;
        $final_output['message'] = "Document list retrieved successfully.";
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function fetch_approvaldocuments($current_userID, $companyID)
    {
        $currentuser = $current_userID;

        $filterdoc = $this->input->post('Document');
        $filterdocval = explode(",", $filterdoc);


        $leaveapprovalsetup = $this->db->query("SELECT approvalSetupID FROM `srp_erp_leaveapprovalsetup` where companyID = '{$companyID}' ")->result_array();
        $lA = '';

        $setupData = $this->getLeaveApprovalSetup('N', $companyID);
        $approvalLevel = $setupData['approvalLevel'];
        $approvalSetup = $setupData['approvalSetup'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $empID = $currentuser;
        $x = 0;
        $str = 'CASE';
        while ($x < $approvalLevel) {
            $level = $x + 1;
            $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $level);
            $arr = array_map(function ($k) use ($approvalSetup) {
                return $approvalSetup[$k];
            }, $keys);

            $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
            if ($approvalType == 3) {
                /*$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '0';
                $str .= ' WHEN( currentLevelNo = '.$level.' ) THEN IF( \''.$empID.'\' = '.$hrManagerID.', 1, 0 ) ';*/

                $hrManagerID = (array_key_exists($level, $approvalEmp_arr)) ? $approvalEmp_arr[$level] : [];
                $hrManagerID = array_column($hrManagerID, 'empID');

                if (!empty($hrManagerID)) {
                    $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ';
                    foreach ($hrManagerID as $key => $hrManagerRow) {
                        $str .= ($key > 0) ? ' OR' : '';
                        $str .= ' ( \'' . $empID . '\' = ' . $hrManagerRow . ')';
                    }
                    $str .= ' , 1, 0 ) ';
                }
            } else {
                $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ' . $managerType . ' = ' . $empID . ', 1, 0 ) ';
            }


            $x++;
        }
        $str .= 'END AS isInApproval';

        if (!empty($leaveapprovalsetup)) {
            $lA = "UNION 
	SELECT
	`leaveMasterID` as DocumentAutoID,
	\"LA\" as DocumentID,
	`documentCode` as DocumentCode,
	t1.comments AS Narration,
	CONCAT( ECode, ' - ', empName ) AS suppliercustomer,
	 \" \" as currency,
	 \"0\" AS Amount,
	 	currentLevelNo AS LEVEL,
		companyID AS companyID,
		\"1\" AS decimalplaces,
	confirmedByName,
	DATE_FORMAT( confirmedDate, \"%b %D %Y\" ) AS date,
	\"\" documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	segmentcodedes
		
FROM
	(
SELECT
	*,{$str}
FROM
	(
	SELECT
		leaveMasterID,
		`documentCode`,
		`ECode`,
		`Ename2` AS `empName`,
		`approvedYN`,
		`lMaster`.`empID`,
		`currentLevelNo`,
		`repManager`,
		`coveringEmpID` AS `coveringEmp`,
		`startDate`,
		endDate,
		comments,
		lMaster.companyID,
		confirmedByName,
		confirmedDate,
		IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
		srp_erp_leavemaster AS lMaster
		JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMaster.empID
		LEFT JOIN ( SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers WHERE active = 1 ) AS repoManagerTB ON lMaster.empID = repoManagerTB.empID 
	    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = lMaster.segmentID
	WHERE
		lMaster.confirmedYN = 1 
		AND lMaster.approvedYN = '0' 
	) AS leaveData
	LEFT JOIN ( SELECT managerID AS topManager, empID AS topEmpID FROM srp_erp_employeemanagers WHERE active = 1 ) AS topManagerTB ON leaveData.repManager = topManagerTB.topEmpID 
	) AS t1 
WHERE
	`t1`.`isInApproval` = 1 
UNION 
SELECT
	`srp_erp_expenseclaimmaster`.`expenseClaimMasterAutoID` AS `DocumentAutoID`,
	`srp_erp_expenseclaimmaster`.`documentID` AS `DocumentID`,
	`expenseClaimCode` as DocumentCode,
	CONCAT(\" Description : \",comments,\" | Claimed Date : \",DATE_FORMAT( expenseClaimDate, '%d-%m-%Y' )) as Narration,
	`claimedByEmpName` as suppliercustomer,
	`det`.`empCurrency` AS `currency`,
	`det`.`transactionAmount` AS `Amount`,
		 \" \"  AS LEVEL,
		 srp_erp_expenseclaimmaster.companyID AS companyID,
	`det`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
	srp_erp_expenseclaimmaster.confirmedByName,
	DATE_FORMAT( srp_erp_expenseclaimmaster.confirmedDate, \"%b %D %Y\" ) AS date,
	\"\" as documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	
FROM
	`srp_erp_expenseclaimmaster`
	LEFT JOIN ( SELECT SUM( empCurrencyAmount ) AS transactionAmount, expenseClaimMasterAutoID, empCurrency,transactionCurrencyDecimalPlaces FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID ) det ON ( `det`.`expenseClaimMasterAutoID` = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID )
	JOIN `srp_erp_employeemanagers` ON `srp_erp_expenseclaimmaster`.`claimedByEmpID` = `srp_erp_employeemanagers`.`empID`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_expenseclaimmaster.segmentID
WHERE
	`srp_erp_expenseclaimmaster`.`confirmedYN` = 1 
	AND `srp_erp_expenseclaimmaster`.`approvedYN` = '0' 
	AND `srp_erp_employeemanagers`.`managerID` = '{$currentuser}'
	AND `srp_erp_employeemanagers`.`active` = 1 ";
        }

        $query = $this->db->query("SELECT *
FROM `srp_erp_company` AS `Company`
JOIN (SELECT
srp_erp_contractmaster.contractAutoID as DocumentAutoID,
`srp_erp_contractmaster`.`documentID` AS `DocumentID`,
`contractCode` as DocumentCode,
`contractNarration` as Narration,
`srp_erp_customermaster`.`customerName` AS `suppliercustomer`,
`transactionCurrency` as currency,
`det`.`transactionAmount` AS `Amount`,
srp_erp_contractmaster.currentLevelNo as Level,
srp_erp_contractmaster.companyID as companyID,
srp_erp_contractmaster.transactionCurrencyDecimalPlaces,
srp_erp_contractmaster.confirmedByName,
DATE_FORMAT(srp_erp_contractmaster.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode, '-' ) AS segmentcodedes
FROM
`srp_erp_contractmaster`
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, contractAutoID FROM srp_erp_contractdetails GROUP BY
contractAutoID ) det ON ( `det`.`contractAutoID` = srp_erp_contractmaster.contractAutoID )
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_contractmaster`.`customerID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_contractmaster`.`contractAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_contractmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_contractmaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster ON segmentmaster.segmentID = srp_erp_contractmaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` IN ( 'QUT', 'CNT', 'SO' )
AND `srp_erp_approvalusers`.`documentID` IN ( 'QUT', 'CNT', 'SO' )
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
GROUP BY
`srp_erp_documentapproved`.`documentSystemCode`
UNION
SELECT
srp_erp_customerinvoicemaster.invoiceAutoID AS DocumentAutoID,
srp_erp_customerinvoicemaster.documentID AS DocumentID,
invoiceCode as DocumentCode,
invoiceNarration as Narration,
srp_erp_customermaster.customerName AS suppliercustomer,
transactionCurrency as currency,
(
(
( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * ( ( IFNULL( det.transactionAmount, 0 ) - ( IFNULL( det.detailtaxamount,
0 ) ) ) )
) + IFNULL( det.transactionAmount, 0 )
) AS Amount,
approvalLevelID as Level,
srp_erp_customerinvoicemaster.companyID as companyID,
srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces,
srp_erp_customerinvoicemaster.confirmedByName,
DATE_FORMAT(srp_erp_customerinvoicemaster.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes


FROM
`srp_erp_customerinvoicemaster`
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, sum( totalafterTax ) AS detailtaxamount, invoiceAutoID
FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) det ON ( `det`.`invoiceAutoID` =
srp_erp_customerinvoicemaster.invoiceAutoID )
LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails GROUP BY
InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID )
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` =
`srp_erp_customerinvoicemaster`.`customerID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_customerinvoicemaster`.`invoiceAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_customerinvoicemaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_customerinvoicemaster`.`currentLevelNo`
LEFT join srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_customerinvoicemaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'CINV'
AND `srp_erp_approvalusers`.`documentID` = 'CINV'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
masterTbl.salesReturnAutoID AS DocumentAutoID,
masterTbl.documentID AS DocumentID,
salesReturnCode AS DocumentCode,
`comment` as Narration,
srp_erp_customermaster.customerName as suppliercustomer,
`transactionCurrency` as currency,
det.totalValue as Amount,
currentLevelNo as Level,
masterTbl.companyID as companyID,
masterTbl.transactionCurrencyDecimalPlaces,
masterTbl.confirmedByName,
DATE_FORMAT(masterTbl.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
\"-\" AS segmentcodedes
FROM
`srp_erp_salesreturnmaster` `masterTbl`
LEFT JOIN ( SELECT SUM( totalValue ) AS totalValue, salesReturnAutoID FROM srp_erp_salesreturndetails detailTbl GROUP BY
salesReturnAutoID ) det ON ( `det`.`salesReturnAutoID` = masterTbl.salesReturnAutoID )
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `masterTbl`.`customerID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`salesReturnAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
WHERE
`srp_erp_documentapproved`.`documentID` = 'SLR'
AND `srp_erp_approvalusers`.`documentID` = 'SLR'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
`srp_erp_salescommisionmaster`.`salesCommisionID` AS `DocumentAutoID`,
`srp_erp_salescommisionmaster`.`DocumentID` AS `DocumentID`,
`salesCommisionCode` as DocumentCode,
`Description` as Narration,
\"-\" as suppliercustomer,
`transactionCurrency` as currency,
`det2`.`transactionAmount` AS `Amount`,
srp_erp_salescommisionmaster.currentLevelNo as Level,
srp_erp_salescommisionmaster.companyID as companyID,
srp_erp_salescommisionmaster.transactionCurrencyDecimalPlaces,
srp_erp_salescommisionmaster.confirmedByName,
DATE_FORMAT(srp_erp_salescommisionmaster.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
\"-\" AS segmentcodedes
FROM
`srp_erp_salescommisionmaster`
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, salesCommisionID FROM srp_erp_salescommisiondetail
GROUP BY salesCommisionID ) det ON ( `det`.`salesCommisionID` = srp_erp_salescommisionmaster.salesCommisionID )
LEFT JOIN ( SELECT SUM( netCommision ) AS transactionAmount, salesCommisionID FROM srp_erp_salescommisionperson GROUP BY
salesCommisionID ) det2 ON ( `det2`.`salesCommisionID` = srp_erp_salescommisionmaster.salesCommisionID )
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_salescommisionmaster`.`salesCommisionID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_salescommisionmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_salescommisionmaster`.`currentLevelNo`
WHERE
`srp_erp_documentapproved`.`documentID` = 'SC'
AND `srp_erp_approvalusers`.`documentID` = 'SC'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'

UNION
SELECT
srp_erp_purchaserequestmaster.purchaseRequestID AS DocumentAutoID,
srp_erp_purchaserequestmaster.documentID as DocumentID,
purchaseRequestCode as DocumentCode,
narration as Narration,
\"-\" as suppliercustomer,
transactionCurrency as currency,
det.transactionAmount AS Amount,
approvalLevelID as level,
srp_erp_purchaserequestmaster.companyID as companyID,
srp_erp_purchaserequestmaster.transactionCurrencyDecimalPlaces,
srp_erp_purchaserequestmaster.confirmedByName,
DATE_FORMAT(srp_erp_purchaserequestmaster.confirmedDate, \"%b %D %Y\" ) as date,
srp_erp_documentapproved.documentApprovedID,

\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

FROM
`srp_erp_purchaserequestmaster`
LEFT JOIN ( SELECT SUM( totalAmount ) AS transactionAmount, purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP
BY purchaseRequestID ) det ON ( `det`.`purchaseRequestID` = srp_erp_purchaserequestmaster.purchaseRequestID )
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_purchaserequestmaster`.`purchaseRequestID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_purchaserequestmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_purchaserequestmaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_purchaserequestmaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'PRQ'
AND `srp_erp_approvalusers`.`documentID` = 'PRQ'
AND (
`srp_erp_approvalusers`.`employeeID` = '$current_userID'
OR (
`srp_erp_approvalusers`.`employeeID` = - 1
AND srp_erp_purchaserequestmaster.requestedEmpID IN (
SELECT
empmanagers.empID
FROM
srp_employeesdetails empdetail
JOIN srp_erp_employeemanagers empmanagers ON empdetail.EIdNo = empmanagers.empID
AND empmanagers.active = 1
WHERE
empmanagers.managerID = '$current_userID'
)
)
)
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
srp_erp_purchaseordermaster.purchaseOrderID AS DocumentAutoID,
srp_erp_purchaseordermaster.DocumentID AS DocumentID,
purchaseOrderCode as DocumentCode,
narration as Narration,
srp_erp_suppliermaster.supplierName AS suppliercustomer,
transactionCurrency as currency,
( det.transactionAmount - generalDiscountAmount ) AS Amount,
currentLevelNo as Level,
srp_erp_purchaseordermaster.companyID as companyID,
srp_erp_purchaseordermaster.transactionCurrencyDecimalPlaces,
srp_erp_purchaseordermaster.confirmedByName,
DATE_FORMAT(srp_erp_purchaseordermaster.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
FROM
`srp_erp_purchaseordermaster`
LEFT JOIN ( SELECT SUM( totalAmount ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY
purchaseOrderID ) det ON ( `det`.`purchaseOrderID` = srp_erp_purchaseordermaster.purchaseOrderID )
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` =
`srp_erp_purchaseordermaster`.`supplierID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_purchaseordermaster`.`purchaseOrderID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_purchaseordermaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_purchaseordermaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_purchaseordermaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'PO'
AND `srp_erp_approvalusers`.`documentID` = 'PO'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
`srp_erp_grvmaster`.`grvAutoID` AS `DocumentAutoID`,
`srp_erp_grvmaster`.`DocumentID` AS `DocumentID`,
`grvPrimaryCode` AS DocumentCode,
`grvNarration` AS Narration,
`srp_erp_suppliermaster`.`supplierName` AS `suppliercustomer`,
`transactionCurrency` AS currency,
( IFNULL( det.receivedTotalAmount, 0 ) + IFNULL( addondet.total_amount, 0 ) ) AS Amount,
srp_erp_grvmaster.currentLevelNo AS Level,
srp_erp_grvmaster.companyID AS `companyID`,
srp_erp_grvmaster.transactionCurrencyDecimalPlaces,
srp_erp_grvmaster.confirmedByName,
DATE_FORMAT(srp_erp_grvmaster.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

FROM
`srp_erp_grvmaster`
LEFT JOIN ( SELECT SUM( receivedTotalAmount ) AS receivedTotalAmount, grvAutoID FROM srp_erp_grvdetails GROUP BY
grvAutoID ) det ON ( `det`.`grvAutoID` = srp_erp_grvmaster.grvAutoID )
LEFT JOIN ( SELECT SUM( total_amount ) AS total_amount, grvAutoID FROM srp_erp_grv_addon GROUP BY grvAutoID ) addondet
ON ( `addondet`.`grvAutoID` = srp_erp_grvmaster.grvAutoID )
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_grvmaster`.`supplierID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_grvmaster`.`grvAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_grvmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_grvmaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_grvmaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'GRV'
AND `srp_erp_approvalusers`.`documentID` = 'GRV'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION

SELECT
stockReturnAutoID as DocumentAutoID,
srp_erp_stockreturnmaster.documentID as DocumentID,
stockReturnCode as DocumentCode,
IFNULL(srp_erp_stockreturnmaster.`comment`,'-') as Narration,
\"-\" as suppliercustomer,
\" \" as currency,
\" \" as Amount,
currentLevelNo as Level,
srp_erp_stockreturnmaster.companyID as companyID,
srp_erp_stockreturnmaster.transactionCurrencyDecimalPlaces as decimalplaces,
confirmedByName,
DATE_FORMAT(confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
\"-\" as segmentcodedes
FROM
`srp_erp_stockreturnmaster`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_stockreturnmaster`.`stockReturnAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockreturnmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockreturnmaster`.`currentLevelNo`
WHERE
`srp_erp_documentapproved`.`documentID` = 'SR'
AND `srp_erp_approvalusers`.`documentID` = 'SR'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
srp_erp_itemissuemaster.itemIssueAutoID AS DocumentAutoID,
srp_erp_itemissuemaster.documentID as DocumentID,
itemIssueCode as DocumentCode,
srp_erp_itemissuemaster.`comment` as Narration,
IFNULL( srp_erp_itemissuemaster.employeeName,'-') AS suppliercustomer,
companyLocalCurrency as currency,
det.totalValue as Amount,
currentLevelNo as Level,
srp_erp_itemissuemaster.companyID as companyID,
companyLocalCurrencyDecimalPlaces as decimalplaces,
srp_erp_itemissuemaster.confirmedByName as confirmname,
DATE_FORMAT(srp_erp_itemissuemaster.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
FROM
`srp_erp_itemissuemaster`
LEFT JOIN ( SELECT SUM( totalValue ) AS totalValue, itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY
itemIssueAutoID ) det ON ( `det`.`itemIssueAutoID` = srp_erp_itemissuemaster.itemIssueAutoID )
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_itemissuemaster`.`itemIssueAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_itemissuemaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_itemissuemaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_itemissuemaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'MI'
AND `srp_erp_approvalusers`.`documentID` = 'MI'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
srp_erp_materialrequest.mrAutoID AS DocumentAutoID,
srp_erp_materialrequest.documentID as DocumentID,
MRCode as DocumentCode,
srp_erp_materialrequest.`comment` as Narration,
srp_erp_materialrequest.employeeName AS suppliercustomer,
\" \" as currency,
\" \" as Amount,
currentLevelNo as Level,
srp_erp_materialrequest.companyID as companyID,
\" \" as transactionCurrencyDecimalPlaces,
srp_erp_materialrequest.confirmedByName,
DATE_FORMAT(srp_erp_materialrequest.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
FROM
`srp_erp_materialrequest`
LEFT JOIN ( SELECT SUM( qtyRequested ) AS qtyRequested, mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID )
det ON ( `det`.`mrAutoID` = srp_erp_materialrequest.mrAutoID )
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_materialrequest`.`mrAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_materialrequest`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_materialrequest`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_materialrequest.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'MR'
AND `srp_erp_approvalusers`.`documentID` = 'MR'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
stockTransferAutoID as DocumentAutoID,
srp_erp_stocktransfermaster.documentID as DocumentID,
stockTransferCode as DocumentCode,
`comment` as Narration,
\"-\" as suppliercustomer,
\" \" as currency,
\" \" as Amount,
srp_erp_stocktransfermaster.currentLevelNo as Level,
srp_erp_stocktransfermaster.companyID as companyID,
\" \" as transactionCurrencyDecimalPlaces,
srp_erp_stocktransfermaster.confirmedByName,
DATE_FORMAT(srp_erp_stocktransfermaster.confirmedDate, \"%b %D %Y\" ) as date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
FROM
`srp_erp_stocktransfermaster`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_stocktransfermaster`.`stockTransferAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stocktransfermaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stocktransfermaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_stocktransfermaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'ST'
AND `srp_erp_approvalusers`.`documentID` = 'ST'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
srp_erp_materialreceiptmaster.mrnAutoID AS DocumentAutoID,
srp_erp_materialreceiptmaster.documentID AS DocumentID,
mrnCode as DocumentCode,
IFNULL(`comment`,'-') as Narration,
srp_erp_materialreceiptmaster.employeeName AS suppliercustomer,
\" \" as currency,
\" \" as Amount,
approvalLevelID as Level,
srp_erp_materialreceiptmaster.companyID AS companyID,
\" \" as decimalplaces,
srp_erp_materialreceiptmaster.confirmedByName,
DATE_FORMAT( srp_erp_materialreceiptmaster.confirmedDate, \"%b %D %Y\" ) AS date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
FROM
`srp_erp_materialreceiptmaster`
LEFT JOIN ( SELECT SUM( qtyReceived ) AS qtyReceived, mrnAutoID FROM srp_erp_materialreceiptdetails GROUP BY mrnAutoID )
det ON ( `det`.`mrnAutoID` = srp_erp_materialreceiptmaster.mrnAutoID )
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_materialreceiptmaster`.`mrnAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_materialreceiptmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_materialreceiptmaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_materialreceiptmaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'MRN'
AND `srp_erp_approvalusers`.`documentID` = 'MRN'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
stockAdjustmentAutoID as DocumentAutoID,
srp_erp_stockadjustmentmaster.documentID as DocumentID,
stockAdjustmentCode as DocumentCode,
IFNULL(srp_erp_stockadjustmentmaster.`comment`,'-') as Narration,
\"-\" AS suppliercustomer,
\" \" as currency,
\" \" as Amount,
approvalLevelID as LEVEL,
srp_erp_stockadjustmentmaster.companyID as companyID,
\" \" as transactionCurrencyDecimalPlaces,
srp_erp_stockadjustmentmaster.confirmedByName,
DATE_FORMAT( srp_erp_stockadjustmentmaster.confirmedDate, \"%b %D %Y\" ) AS date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
FROM
`srp_erp_stockadjustmentmaster`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_stockadjustmentmaster`.`stockAdjustmentAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockadjustmentmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockadjustmentmaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_stockadjustmentmaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'SA'
AND `srp_erp_approvalusers`.`documentID` = 'SA'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
stockCountingAutoID as DocumentAutoID,
srp_erp_stockcountingmaster.documentID as DocumentID,
stockCountingCode as DocumentCode,
IFNULL(srp_erp_stockcountingmaster.`comment`,'-') AS Narration,
\"-\" AS suppliercustomer,
\" \" as currency,
\" \" as Amount,
approvalLevelID as LEVEL,
srp_erp_stockcountingmaster.companyID AS companyID,
\" \" AS decimalplaces,
srp_erp_stockcountingmaster.confirmedByName AS confirmname,
DATE_FORMAT( srp_erp_stockcountingmaster.confirmedDate, \"%b %D %Y\" ) AS date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
FROM
`srp_erp_stockcountingmaster`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_stockcountingmaster`.`stockCountingAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockcountingmaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockcountingmaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_stockcountingmaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'SCNT'
AND `srp_erp_approvalusers`.`documentID` = 'SCNT'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'

UNION
SELECT
`srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID` AS `DocumentAutoID`,
`srp_erp_paysupplierinvoicemaster`.`documentID` AS `DocumentID`,

bookingInvCode AS DocumentCode,
comments AS Narration,
srp_erp_suppliermaster.supplierName AS suppliercustomer,
transactionCurrency AS currency,

( IFNULL( addondet.transactionAmount, 0 ) + IFNULL( det.transactionAmount, 0 ) ) AS Amount,


`approvalLevelID` as LEVEL,
srp_erp_paysupplierinvoicemaster.companyID AS companyID,
srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces,
srp_erp_paysupplierinvoicemaster.confirmedByName,
DATE_FORMAT( srp_erp_paysupplierinvoicemaster.confirmedDate, \"%b %D %Y\" ) AS date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

FROM
`srp_erp_paysupplierinvoicemaster`
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail
GROUP BY InvoiceAutoID ) det ON ( `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID )
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM
srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` =
srp_erp_paysupplierinvoicemaster.InvoiceAutoID )
JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` =
`srp_erp_paysupplierinvoicemaster`.`supplierID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_paysupplierinvoicemaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_paysupplierinvoicemaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_paysupplierinvoicemaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'BSI'
AND `srp_erp_approvalusers`.`documentID` = 'BSI'
AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT

srp_erp_debitnotemaster.debitNoteMasterAutoID AS DocumentAutoID,
srp_erp_debitnotemaster.documentID AS DocumentID,
debitNoteCode AS DocumentCode,
IFNULL(comments,'-') as Narration,
IFNULL(`srp_erp_suppliermaster`.`supplierName` , '-' ) AS suppliercustomer,
`transactionCurrency` as currency,
`det`.`transactionAmount` AS Amount,

approvalLevelID as LEVEL,
srp_erp_debitnotemaster.companyID AS companyID,
srp_erp_debitnotemaster.transactionCurrencyDecimalPlaces AS decimalplaces,
srp_erp_debitnotemaster.confirmedByName AS confirmname,
DATE_FORMAT( srp_erp_debitnotemaster.confirmedDate, \"%b %D %Y\" ) AS date,
documentApprovedID,
\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
\"-\" as `segmentcodedes`
FROM
`srp_erp_debitnotemaster`
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, debitNoteMasterAutoID FROM srp_erp_debitnotedetail
GROUP BY debitNoteMasterAutoID ) det ON ( `det`.`debitNoteMasterAutoID` = srp_erp_debitnotemaster.debitNoteMasterAutoID
)
JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_debitnotemaster`.`supplierID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_debitnotemaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_debitnotemaster`.`currentLevelNo`
WHERE
`srp_erp_approvalusers`.`employeeID` = '$current_userID'
AND `srp_erp_documentapproved`.`documentID` = 'DN'
AND `srp_erp_approvalusers`.`documentID` = 'DN'
AND `srp_erp_documentapproved`.`approvedYN` = '0'
UNION
SELECT
srp_erp_paymentvouchermaster.payVoucherAutoId AS DocumentAutoID,
srp_erp_paymentvouchermaster.documentID AS DocumentID,
PVcode as DocumentCode,
IFNULL(PVNarration,'-') as Narration,
CASE
pvType
WHEN 'Direct' THEN
partyName
WHEN 'Employee' THEN
srp_employeesdetails.Ename2
WHEN 'Supplier' THEN
srp_erp_suppliermaster.supplierName
END AS suppliercustomer,
transactionCurrency as currency,
(
( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL(
det.transactionAmount, 0 ) - IFNULL( debitnote.transactionAmount, 0 ) - IFNULL( SR.transactionAmount, 0 )
) AS Amount,
approvalLevelID as LEVEL,
srp_erp_paymentvouchermaster.companyID as companyID,
transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
srp_erp_paymentvouchermaster.confirmedByName,
DATE_FORMAT( srp_erp_paymentvouchermaster.confirmedDate, \"%b %D %Y\" ) AS date,
documentApprovedID,

\"\" as `payrollYear`,
\"\" as `payrollMonth`,
\"\" as `bankGLAutoID`,
IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

FROM
`srp_erp_paymentvouchermaster`
LEFT JOIN (
SELECT
SUM( transactionAmount ) AS transactionAmount,
payVoucherAutoId
FROM
srp_erp_paymentvoucherdetail
WHERE
srp_erp_paymentvoucherdetail.type != \"debitnote\"
AND srp_erp_paymentvoucherdetail.type != \"SR\"
GROUP BY
payVoucherAutoId
) det ON ( `det`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
LEFT JOIN (
SELECT
SUM( transactionAmount ) AS transactionAmount,
payVoucherAutoId
FROM
srp_erp_paymentvoucherdetail
WHERE
srp_erp_paymentvoucherdetail.type = \"GL\"
OR srp_erp_paymentvoucherdetail.type = \"Item\"
GROUP BY
payVoucherAutoId
) tyepdet ON ( `tyepdet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail
WHERE srp_erp_paymentvoucherdetail.type = \"debitnote\" GROUP BY payVoucherAutoId ) debitnote ON (
`debitnote`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail
WHERE srp_erp_paymentvoucherdetail.type = \"SR\" GROUP BY payVoucherAutoId ) SR ON ( `SR`.`payVoucherAutoId` =
srp_erp_paymentvouchermaster.payVoucherAutoId )
LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, SUM( taxPercentage ) AS taxPercentage,
payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON (
`addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` =
`srp_erp_paymentvouchermaster`.`partyID`
LEFT JOIN `srp_employeesdetails` ON `srp_employeesdetails`.`EIdNo` = `srp_erp_paymentvouchermaster`.`partyID`
JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
`srp_erp_paymentvouchermaster`.`PayVoucherAutoId`
AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_paymentvouchermaster`.`currentLevelNo`
JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_paymentvouchermaster`.`currentLevelNo`
LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_paymentvouchermaster.segmentID
WHERE
`srp_erp_documentapproved`.`documentID` = 'PV'
AND `srp_erp_approvalusers`.`documentID` = 'PV'
AND `pvType` <> 'SC'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	UNION
	SELECT
	`srp_erp_creditnotemaster`.`creditNoteMasterAutoID` AS `DocumentAutoID`,
	`srp_erp_creditnotemaster`.`documentID` AS `DocumentID`,
	`creditNoteCode` as DocumentCode,
	IFNULL( `comments`,'-') as Narration,
	`srp_erp_customermaster`.`customerName` AS `suppliercustomer,`,
	`transactionCurrency` as currency,
	`det`.`transactionAmount` AS `Amount`,

	`approvalLevelID` as LEVEL,
	srp_erp_creditnotemaster.companyID as companyID,
	srp_erp_creditnotemaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	srp_erp_creditnotemaster.confirmedByName,
	DATE_FORMAT( srp_erp_creditnotemaster.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	\"-\" as `segmentcodedes`
	FROM
	`srp_erp_creditnotemaster`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, creditNoteMasterAutoID FROM
	srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID ) det ON ( `det`.`creditNoteMasterAutoID` =
	srp_erp_creditnotemaster.creditNoteMasterAutoID )
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` =
	`srp_erp_creditnotemaster`.`customerID`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_creditnotemaster`.`creditNoteMasterAutoID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_creditnotemaster`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_creditnotemaster`.`currentLevelNo`
	WHERE
	`srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`documentID` = 'CN'
	AND `srp_erp_approvalusers`.`documentID` = 'CN'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	UNION
	SELECT
	srp_erp_customerreceiptmaster.receiptVoucherAutoId AS DocumentAutoID,
	srp_erp_customerreceiptmaster.documentID AS DocumentID,
	RVcode as DocumentCode,
	RVNarration as Narration,
	IF
	( customerID IS NULL OR customerID = 0, srp_erp_customerreceiptmaster.customerName,
	srp_erp_customermaster.customerName ) AS suppliercustome,
	transactionCurrency as currency,
	(
	( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL(
	det.transactionAmount, 0 ) - IFNULL( Creditnots.transactionAmount, 0 )
	) AS Amount,
	approvalLevelID as LEVEL,

	srp_erp_customerreceiptmaster.companyID as companyID,
	transactionCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.confirmedByName,
	DATE_FORMAT( srp_erp_customerreceiptmaster.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

	FROM
	`srp_erp_customerreceiptmaster`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM
	srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type != \"creditnote\" GROUP BY receiptVoucherAutoId
	) det ON ( `det`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` =
	`srp_erp_customerreceiptmaster`.`customerID`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_customerreceiptmaster`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_customerreceiptmaster`.`currentLevelNo`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM
	srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type = \"creditnote\" GROUP BY receiptVoucherAutoId
	) Creditnots ON ( `Creditnots`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM
	srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON ( `addondet`.`receiptVoucherAutoId` =
	srp_erp_customerreceiptmaster.receiptVoucherAutoId )
	LEFT JOIN (
	SELECT
	SUM( transactionAmount ) AS transactionAmount,
	receiptVoucherAutoId
	FROM
	srp_erp_customerreceiptdetail
	WHERE
	srp_erp_customerreceiptdetail.type = \"GL\"
	OR srp_erp_customerreceiptdetail.type = \"Item\"
	GROUP BY
	receiptVoucherAutoId
	) tyepdet ON ( `tyepdet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_customerreceiptmaster.segmentID
	WHERE
	`srp_erp_documentapproved`.`documentID` = 'RV'
	AND `srp_erp_approvalusers`.`documentID` = 'RV'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'


	UNION
	SELECT
	`srp_erp_jvmaster`.`JVMasterAutoId` AS DocumentAutoID,
	`srp_erp_jvmaster`.`documentID` AS DocumentID,
	`JVcode` as DocumentCode,
	`JVNarration` as Narration,
	\"-\" AS suppliercustomer,
	`transactionCurrency` as currency,
	IFNULL( debamt.debitAmount, 0 ) AS Amount,
	`approvalLevelID` as LEVEL,
	srp_erp_jvmaster.companyID as companyID,
	transactionCurrencyDecimalPlaces,
	srp_erp_jvmaster.confirmedByName,
	DATE_FORMAT( srp_erp_jvmaster.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	\"-\" AS `segmentcodedes`

	FROM
	`srp_erp_jvmaster`
	LEFT JOIN ( SELECT SUM( debitAmount ) AS debitAmount, JVMasterAutoId FROM srp_erp_jvdetail GROUP BY JVMasterAutoId )
	debamt ON ( `debamt`.`JVMasterAutoId` = srp_erp_jvmaster.JVMasterAutoId )
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_jvmaster`.`JVMasterAutoId`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_jvmaster`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_jvmaster`.`currentLevelNo`
	WHERE
	`srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`documentID` = 'JV'
	AND `srp_erp_approvalusers`.`documentID` = 'JV'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	UNION
	SELECT
	`srp_erp_recurringjvmaster`.`RJVMasterAutoId` AS `DocumentAutoID`,
	`srp_erp_recurringjvmaster`.`documentID` AS `DocumentID`,
	`RJVcode` AS `DocumentCode`,
	`RJVNarration` as Narration,
	\"-\" as suppliercustomer,
	`transactionCurrency` as currency,
	IFNULL( debamt.debitAmount, 0 ) AS Amount,
	`approvalLevelID`as LEVEL,

	srp_erp_recurringjvmaster.companyID as companyID,
	transactionCurrencyDecimalPlaces,
	srp_erp_recurringjvmaster.confirmedByName,
	DATE_FORMAT( srp_erp_recurringjvmaster.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	\"-\" as `segmentcodedes`

	FROM
	`srp_erp_recurringjvmaster`
	LEFT JOIN ( SELECT SUM( debitAmount ) AS debitAmount, RJVMasterAutoId FROM srp_erp_recurringjvdetail GROUP BY
	RJVMasterAutoId ) debamt ON ( `debamt`.`RJVMasterAutoId` = srp_erp_recurringjvmaster.RJVMasterAutoId )
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_recurringjvmaster`.`RJVMasterAutoId`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_recurringjvmaster`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_recurringjvmaster`.`currentLevelNo`
	WHERE
	`srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`documentID` = 'RJV'
	AND `srp_erp_approvalusers`.`documentID` = 'RJV'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	UNION
	SELECT
	bankTransferAutoID AS DocumentAutoID,
	srp_erp_banktransfer.documentID AS DocumentID,
	bankTransferCode AS DocumentCode,
	narration AS Narration,
	\"-\" AS suppliercustomer,
	currency.CurrencyCode AS currency,
	round( transferedAmount, 2 ) AS Amount,
	approvalLevelID AS LEVEL,
	srp_erp_banktransfer.companyID AS companyID,
	currency.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	srp_erp_banktransfer.confirmedByName,
	DATE_FORMAT( srp_erp_banktransfer.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_banktransfer`
	LEFT JOIN `srp_erp_chartofaccounts` `a` ON `fromBankGLAutoID` = `a`.`GLAutoID`
	LEFT JOIN `srp_erp_chartofaccounts` `b` ON `toBankGLAutoID` = `b`.`GLAutoID`
	LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = srp_erp_banktransfer.fromBankCurrencyID
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_banktransfer`.`bankTransferAutoID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_banktransfer`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_banktransfer`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_banktransfer.segmentID
	WHERE
	`srp_erp_approvalusers`.`documentID` = 'BT'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`documentID` = 'BT'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	UNION
	SELECT
	masterTbl.voucherAutoID AS DocumentAutoID,
	masterTbl.documentID AS DocumentID,
	masterTbl.iouCode AS DocumentCode,
	masterTbl.narration AS Narration,
	masterTbl.empName AS suppliercustomer,
	transactionCurrency as currency,
	det.transactionAmount AS Amount,
	approvalLevelID as LEVEL,
	masterTbl.companyID as companyID,
	transactionCurrencyDecimalPlaces,
	masterTbl.confirmedByName,
	DATE_FORMAT( masterTbl.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_iouvouchers` `masterTbl`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, voucherAutoID FROM srp_erp_iouvoucherdetails
	detailTbl GROUP BY voucherAutoID ) det ON ( `masterTbl`.`voucherAutoID` = det.voucherAutoID )
	LEFT JOIN `srp_employeesdetails` `employee` ON `employee`.`EIdNo` = `masterTbl`.`empID`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`voucherAutoID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = masterTbl.segmentID
	WHERE
	`srp_erp_documentapproved`.`documentID` = 'IOU'
	AND `srp_erp_approvalusers`.`documentID` = 'IOU'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	UNION
	SELECT
	masterTbl.bookingMasterID AS DocumentAutoID,
	masterTbl.documentID AS DocumentID,
	masterTbl.bookingCode AS DocumentCode,
	masterTbl.`comments` as Narration,
	masterTbl.empName AS suppliercustomer,
	transactionCurrency as currency,
	det.transactionAmount AS Amount,
	approvalLevelID as LEVEL,
	masterTbl.companyID as companyID,
	masterTbl.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	masterTbl.confirmedByName,
	DATE_FORMAT( masterTbl.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

	FROM
	`srp_erp_ioubookingmaster` `masterTbl`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, bookingMasterID FROM srp_erp_ioubookingdetails
	detailTbl GROUP BY bookingMasterID ) det ON ( `masterTbl`.`bookingMasterID` = det.bookingMasterID )
	LEFT JOIN `srp_employeesdetails` `employee` ON `employee`.`EIdNo` = `masterTbl`.`empID`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`bookingMasterID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = masterTbl.segmentID
	WHERE
	`srp_erp_documentapproved`.`documentID` = 'IOUE'
	AND `srp_erp_approvalusers`.`documentID` = 'IOUE'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	UNION
	SELECT
	`faID` as DocumentAutoID,
	srp_erp_fa_asset_master.documentID as DocumentID,
	`faCode` as DocumentCode,
	CONCAT(srp_erp_fa_asset_master.assetDescription,\" | Asset Depreciation Date : \",DATE_FORMAT(
	srp_erp_fa_asset_master.dateDEP, '%Y-%m-%d'),\" | Asset Acquired Date : \",DATE_FORMAT(
	srp_erp_fa_asset_master.dateAQ, '%Y-%m-%d')) as Narration,
	\"-\" as suppliercustomer,
	srp_erp_fa_asset_master.transactionCurrency as currency,
	srp_erp_fa_asset_master.transactionAmount as Amount,
	`approvalLevelID` as LEVEL,
	`srp_erp_fa_asset_master`.`companyID` as companyID,
	transactionCurrencyDecimalPlaces,
	srp_erp_fa_asset_master.confirmedByName,
	DATE_FORMAT( srp_erp_fa_asset_master.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes


	FROM
	`srp_erp_fa_asset_master`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_fa_asset_master`.`faID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_fa_asset_master`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_fa_asset_master`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_fa_asset_master.segmentID
	WHERE
	`srp_erp_documentapproved`.`documentID` = 'FA'
	AND `srp_erp_approvalusers`.`documentID` = 'FA'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'

	UNION
	SELECT
	`depMasterAutoID` as DocumentAutoID,
	srp_erp_fa_depmaster.documentID as DocumentID,
	`depCode` as DocumentCode,
	CONCAT (IF(depType = 1, \"Adhoc Depreciation\", \"Monthly Depreciation\"),\" | Depreciation Date : \", DATE_FORMAT(
	srp_erp_fa_depmaster.depDate, '%Y-%m-%d')) as Narration ,
	\"-\" as suppliercustomer,
	srp_erp_fa_depmaster.transactionCurrency as currency,
	transactionAmount as Amount,
	`approvalLevelID` as LEVEL,
	`srp_erp_fa_depmaster`.`companyID` as companyID,
	transactionCurrencyDecimalPlaces,
	srp_erp_fa_depmaster.confirmedByName,
	DATE_FORMAT( srp_erp_fa_depmaster.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

	FROM
	`srp_erp_fa_depmaster`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_fa_depmaster`.`depMasterAutoID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_fa_depmaster`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_fa_depmaster`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_fa_depmaster.segmentID
	WHERE
	`srp_erp_documentapproved`.`documentID` = 'FAD'
	AND `srp_erp_approvalusers`.`documentID` = 'FAD'
	AND `srp_erp_documentapproved`.`approvedYN` = '0'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	UNION
	SELECT
	`payrollMasterID` as DocumentAutoID,
	t2.documentID as DocumentID,
	`t2`.`documentCode` AS `DocumentCode`,
	IFNULL(`narration`,'-') as Narration,
	\"-\" as suppliercustomer,
	\" \" as currency,
	\"\" as Amount,
	`approvalLevelID` as LEVEL,
	`t2`.`companyID` as companyID,
	\" \" as transactionCurrencyDecimalPlaces,
	t2.confirmedByName,
	DATE_FORMAT( t2.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	`payrollYear`,
	`payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_payrollmaster` AS `t2`
	JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `t2`.`payrollMasterID`
	AND `approve`.`approvalLevelID` = `t2`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `t2`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = t2.segmentID
	WHERE
	`approve`.`documentID` = 'SP'
	AND `ap`.`documentID` = 'SP'
	AND `ap`.`employeeID` = '$current_userID'
	AND `approve`.`approvedYN` = '0'
	UNION
	SELECT
	`payrollMasterID` as DocumentAutoID,
	t2.documentID as DocumentID,
	`t2`.`documentCode` AS `DocumentCode`,
	`narration` as Narration,
	\" \" as suppliercustomer,
	\" \" as currency,
	\" \" as Amount,
	approvalLevelID as Level,
	t2.companyID as companyID,
	\" \" as transactionCurrencyDecimalPlaces,
	t2.confirmedByName,
	DATE_FORMAT(t2.confirmedDate, \"%b %D %Y\" ) as date,
	documentApprovedID,
	\"\" as `payrollYear`,
	\"\" as `payrollMonth`,
	\"\" as `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_non_payrollmaster` AS `t2`
	JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `t2`.`payrollMasterID`
	AND `approve`.`approvalLevelID` = `t2`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `t2`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = t2.segmentID
	WHERE
	`approve`.`documentID` = 'SPN'
	AND `ap`.`documentID` = 'SPN'
	AND `ap`.`employeeID` ='$current_userID'
	AND `approve`.`approvedYN` = '0'
	UNION
	SELECT
	`b`.`bankRecAutoID` AS `DocumentAutoID`,
	`b`.`documentID` AS `DocumentID`,
	bankRecPrimaryCode as DocumentCode,
	concat('As Of Date : ',DATE_FORMAT( bankRecAsOf, \"%d/%m/%y\" ),' | Month : ',concat( MONTH, \"/\", YEAR ),' | ',
	b.description,' | Bank Name : ',bankName,' | GL Code : ',`c`.`systemAccountCode`,' | Account Number : ',
	`c`.`bankAccountNumber` ) AS Narration,
	\"-\" as suppliercustomer,
	\" \" as currency,
	\" \" as Amount,
	currentLevelNo AS LEVEL,
	b.companyID AS companyID,
	\" \" as transactionCurrencyDecimalPlaces,
	b.confirmedByName,
	DATE_FORMAT( b.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	`bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_bankrecmaster` AS `b`
	LEFT JOIN `srp_erp_documentapproved` AS `d` ON `d`.`documentSystemCode` = `b`.`bankRecAutoID`
	AND `d`.`approvalLevelID` = `b`.`currentLevelNo`
	LEFT JOIN `srp_erp_chartofaccounts` AS `c` ON `c`.`GLAutoID` = `b`.`bankGLAutoID`
	JOIN `srp_erp_approvalusers` AS `au` ON `au`.`levelNo` = `b`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = b.segmentID
	WHERE
	`d`.`documentID` = 'BRC'
	AND `au`.`documentID` = 'BRC'
	AND `au`.`employeeID` = '$current_userID'
	AND `d`.`approvedYN` = ''
	UNIOn
	SELECT
	`budgetTransferAutoID` as DocumentAutoID,
	srp_erp_budgettransfer.documentID as DocumentID,
	`srp_erp_budgettransfer`.`documentSystemCode` AS `DocumentCode`,
	CONCAT(\"Created Date : \",DATE_FORMAT( srp_erp_budgettransfer.documentDate, '%d-%m-%Y' ),' | Financial Year :
	',CONCAT( srp_erp_companyfinanceyear.beginingDate, ' - ', srp_erp_companyfinanceyear.endingDate ),\" | \"
	,`srp_erp_budgettransfer`.`comments`) AS Narration,
	\"-\" AS suppliercustomer,
	\" \" AS currency,
	\" \" AS Amount,
	`approvalLevelID` AS LEVEL,
	srp_erp_budgettransfer.companyID AS companyID,

	\" \" AS transactionCurrencyDecimalPlaces,
	srp_erp_budgettransfer.confirmedByName,
	DATE_FORMAT( srp_erp_budgettransfer.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_budgettransfer`
	JOIN `srp_erp_companyfinanceyear` ON `srp_erp_companyfinanceyear`.`companyFinanceYearID` =
	`srp_erp_budgettransfer`.`financeYearID`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_budgettransfer`.`budgetTransferAutoID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_budgettransfer`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_budgettransfer`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_budgettransfer.segmentID
	WHERE
	`srp_erp_documentapproved`.`approvedYN` = ''
	AND `srp_erp_documentapproved`.`documentID` = 'BDT'
	AND `srp_erp_approvalusers`.`documentID` = 'BDT'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	UNION
	SELECT
	salarydeclarationMasterID AS DocumentAutoID,
	srp_erp_salarydeclarationmaster.documentID AS DocumentID,
	srp_erp_salarydeclarationmaster.documentSystemCode AS DocumentCode,
	CONCAT(\"Date : \",DATE_FORMAT(srp_erp_salarydeclarationmaster.documentDate, '%d-%m-%Y'),' | Currency :
	',transactionCurrency, \" | \" ,Description) AS Narration,

	\"-\" AS suppliercustomer,
	\" \" AS currency,
	\" \" AS Amount,
	approvalLevelID AS LEVEL,

	srp_erp_salarydeclarationmaster.companyID AS companyID,
	srp_erp_salarydeclarationmaster.transactionCurrencyDecimalPlaces,
	srp_erp_salarydeclarationmaster.confirmedByName,
	DATE_FORMAT( srp_erp_salarydeclarationmaster.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	\"-\" AS segmentcodedes

	FROM
	`srp_erp_salarydeclarationmaster`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_salarydeclarationmaster`.`salarydeclarationMasterID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_salarydeclarationmaster`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` =
	`srp_erp_salarydeclarationmaster`.`currentLevelNo`
	WHERE
	`srp_erp_documentapproved`.`documentID` = 'SD'
	AND `srp_erp_approvalusers`.`documentID` = 'SD'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`approvedYN` = ''
	UNION
	SELECT
	`e_loan`.`ID` AS `DocumentAutoID`,
	`e_loan`.`documentID` AS `DocumentID`,
	`loanCode` as DocumentCode,
	IFNULL(loanDescription,'-') as Narration,

	CONCAT( IFNULL( Ename2, '' ) ) AS suppliercustomer,
	\" \" AS currency,
	\" \" AS Amount,
	`approvalLevelID` AS LEVEL,
	e_loan.companyID AS companyID,

	\" \" AS transactionCurrencyDecimalPlaces,
	e_loan.confirmedByName,
	DATE_FORMAT( e_loan.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_pay_emploan` AS `e_loan`
	JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `e_loan`.`ID`
	AND `approve`.`approvalLevelID` = `e_loan`.`currentLevelNo`
	JOIN `srp_employeesdetails` AS `emp` ON `emp`.`EIdNo` = `e_loan`.`empID`
	JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `e_loan`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = e_loan.segmentID
	WHERE
	`approve`.`documentID` = 'LO'
	AND `ap`.`documentID` = 'LO'
	AND `ap`.`employeeID` = '$current_userID'
	AND `approve`.`approvedYN` = ''
	UNION
	SELECT
	`masterID` as DocumentAutoID,
	fm.documentID as DocumentID,
	`fm`.`documentCode` AS `DocumentCode`,
	CONCAT('Emp Code : ',ECode,' | Emp Name: ',Ename2,' | ',narration) as Narration,
	\"-\" as suppliercustomer,
	\" \" AS currency,
	\" \" AS Amount,
	`approvalLevelID` AS LEVEL,
	fm.companyID AS companyID,

	\" \" AS transactionCurrencyDecimalPlaces,
	fm.confirmedByName,
	DATE_FORMAT( fm.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	\"-\" AS `segmentcodedes`
	FROM
	`srp_erp_pay_finalsettlementmaster` AS `fm`
	JOIN `srp_employeesdetails` `empTB` ON `empTB`.`EIdNo` = `fm`.`empID`
	JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `fm`.`masterID`
	AND `approve`.`approvalLevelID` = `fm`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `fm`.`currentLevelNo`
	WHERE
	`approve`.`documentID` = 'FS'
	AND `ap`.`documentID` = 'FS'
	AND `ap`.`employeeID` = '$current_userID'
	AND `approve`.`approvedYN` = ''
	UNION
	SELECT
	vpMasterID as DocumentAutoID,
	decMas.documentID as DocumentID,
	decMas.documentCode AS DocumentCode,
	CONCAT(\"Currency : \",crMas.CurrencyCode,\" | \",description) AS Narration,
	\"-\" as suppliercustomer,
	\" \" AS currency,
	\" \" AS Amount,
	approvalLevelID AS LEVEL,
	decMas.companyID AS companyID,
	\" \" AS transactionCurrencyDecimalPlaces,
	emp.Ename2 as confirmedByName,
	DATE_FORMAT( decMas.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	\"-\" AS `segmentcodedes`
	FROM
	`srp_erp_variablepaydeclarationmaster` AS `decMas`
	JOIN `srp_erp_documentapproved` AS `appTB` ON `appTB`.`documentSystemCode` = `decMas`.`vpMasterID`
	AND `appTB`.`approvalLevelID` = `decMas`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `decMas`.`currentLevelNo`
	JOIN `srp_erp_currencymaster` `crMas` ON `decMas`.`trCurrencyID` = `crMas`.`currencyID`
	LEFT JOIN srp_employeesdetails emp on emp.EIdNo = decMas.confirmedByEmpID
	WHERE
	`appTB`.`documentID` = 'VD'
	AND `srp_erp_approvalusers`.`documentID` = 'VD'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `appTB`.`approvedYN` = ''
	UNION
	SELECT
	`fuelusageID` as DocumentAutoID,
	DocumentID,
	`documentCode` as DocumentCode,
	narration AS Narration,
	`supplierName` as suppliercustomer,
	`transactionCurrency` as currency,
	`transactionAmount` as Amount,

	`approvalLevelID` AS LEVEL,
	companyID,
	transactionCurrencyDecimalPlaces,
	confirmedByName,
	DATE_FORMAT( confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	segmentcodedes
	FROM
	(
	SELECT
	documentApprovedID,

	`fleet_fuelusagemaster`.`approvedYN`,
	`fleet_fuelusagemaster`.`companyID`,
	`fleet_fuelusagemaster`.`confirmedByName`,
	`fleet_fuelusagemaster`.`confirmedDate`,
	fleet_fuelusagemaster.transactionCurrencyDecimalPlaces,
	`approvalLevelID`,
	narration,
	`fleet_fuelusagemaster`.`documentID`,
	`confirmedYN`,
	`fleet_fuelusagemaster`.`fuelusageID`,
	`fleet_fuelusagemaster`.`supplierAutoID`,
	`fleet_fuelusagemaster`.`documentCode`,
	`fleet_fuelusagemaster`.`documentDate`,
	`referenceNumber`,
	`transactionCurrency`,
	FORMAT( IFNULL( fleet_fuelusagedetails.transactionAmount, 0 ), transactionCurrencyDecimalPlaces ) AS
	transactionAmount,
	supplierName,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	fleet_fuelusagemaster
	LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = fleet_fuelusagemaster.supplierAutoID
	LEFT JOIN ( SELECT sum( fleet_fuelusagedetails.totalAmount ) AS transactionAmount, fuelusageID FROM
	fleet_fuelusagedetails GROUP BY fuelusageID ) fleet_fuelusagedetails ON fleet_fuelusagemaster.fuelusageID =
	fleet_fuelusagedetails.fuelusageID
	LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode =
	fleet_fuelusagemaster.fuelusageID
	AND approvalLevelID = currentLevelNo
	LEFT JOIN srp_erp_approvalusers ON levelNo = fleet_fuelusagemaster.currentLevelNo
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = fleet_fuelusagemaster.segmentID
	WHERE
	isDeleted != 1
	AND srp_erp_documentapproved.documentID = 'FU'
	AND srp_erp_approvalusers.documentID = 'FU'
	AND employeeID = '$current_userID'
	AND fleet_fuelusagemaster.approvedYN = 0
	) t
	UNION
	SELECT
	masterTbl.journeyPlanMasterID AS DocumentAutoID,
	masterTbl.documentID AS DocumentID,
	masterTbl.documentCode AS DocumentCode,
	CONCAT(\"Driver :\",driver.driverName,\" | \",\"Departure : \",depart.placeName,\" | Destination : \",arrive.placeName) as
	Narration,
	\"-\" as suppliercustomer,
	\"\" as currency,
	\"\" as Amount,
	`approvalLevelID` AS LEVEL,
	masterTbl.companyID,
	\" \" as transactionCurrencyDecimalPlaces,
	masterTbl.confirmedByName,
	DATE_FORMAT(masterTbl.confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	\"-\" AS `segmentcodedes`
	FROM
	`srp_erp_journeyplan_master` `masterTbl`
	LEFT JOIN ( SELECT MAX( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM
	srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) rout ON `rout`.`journeyPlanMasterID` =
	`masterTbl`.`journeyPlanMasterID`
	LEFT JOIN ( SELECT MIN( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM
	srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) routmin ON `routmin`.`journeyPlanMasterID` =
	`masterTbl`.`journeyPlanMasterID`
	LEFT JOIN `fleet_drivermaster` `driver` ON `driver`.`driverMasID` = `masterTbl`.`driverID`
	LEFT JOIN `fleet_vehiclemaster` `vehicalemaster` ON `vehicalemaster`.`vehicleMasterID` = `masterTbl`.`vehicleID`
	LEFT JOIN `srp_erp_journeyplan_routedetails` `arrive` ON `arrive`.`JP_RouteDetailsID` = `rout`.`JP_RouteDetailsID`
	LEFT JOIN `srp_erp_journeyplan_routedetails` `depart` ON `depart`.`JP_RouteDetailsID` =
	`routmin`.`JP_RouteDetailsID`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`masterTbl`.`journeyPlanMasterID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
	WHERE
	`srp_erp_documentapproved`.`documentID` = 'JP'
	AND `srp_erp_approvalusers`.`documentID` = 'JP'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	AND `srp_erp_documentapproved`.`approvedYN` = ''
	UNION
	SELECT
	`collectionAutoId` as DocumentAutoID,
	`documentCode` as DocumentID,
	`documentSystemCode` as DocumentCode,
	CONCAT(\"Donor Name : \",NAME,\" | \",narration) AS Narration,
	\"-\" as suppliercustomer,
	transactionCurrency AS currency,
	transactionAmount AS Amount,
	approvalLevelID AS LEVEL,

	companyID AS companyID,
	transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
	confirmedByName,
	DATE_FORMAT(confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	\"-\" AS `segmentcodedes`

	FROM
	(
	SELECT
	documentApprovedID,
	`srp_erp_ngo_donorcollectionmaster`.`approvedYN`,
	`srp_erp_ngo_donorcollectionmaster`.`confirmedDate`,
	`srp_erp_ngo_donorcollectionmaster`.`confirmedByName`,
	`srp_erp_ngo_donorcollectionmaster`.`companyID`,
	`srp_erp_ngo_donorcollectionmaster`.`transactionCurrencyDecimalPlaces`,
	`srp_erp_ngo_donorcollectionmaster`.`narration`,
	`approvalLevelID`,
	`srp_erp_ngo_donorcollectionmaster`.`documentCode`,
	`confirmedYN`,
	`srp_erp_ngo_donorcollectionmaster`.`collectionAutoId`,
	`srp_erp_ngo_donorcollectionmaster`.`documentSystemCode`,
	`srp_erp_ngo_donorcollectionmaster`.`documentDate`,
	`referenceNo`,
	`transactionCurrency`,
	`donorsID`,
	IFNULL( transactionAmount, 0 ) AS transactionAmount,
	NAME
	FROM
	srp_erp_ngo_donorcollectionmaster
	LEFT JOIN srp_erp_ngo_donors ON donorsID = contactID
	LEFT JOIN ( SELECT sum( transactionAmount ) AS transactionAmount, collectionAutoId FROM
	srp_erp_ngo_donorcollectiondetails GROUP BY collectionAutoId ) srp_erp_ngo_donorcollectiondetails ON
	srp_erp_ngo_donorcollectionmaster.collectionAutoId = srp_erp_ngo_donorcollectiondetails.collectionAutoId
	LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode =
	srp_erp_ngo_donorcollectionmaster.collectionAutoId
	AND approvalLevelID = currentLevelNo
	LEFT JOIN srp_erp_approvalusers ON levelNo = srp_erp_ngo_donorcollectionmaster.currentLevelNo
	WHERE
	isDeleted != 1
	AND srp_erp_documentapproved.documentID = 'DC'
	AND srp_erp_approvalusers.documentID = 'DC'
	AND employeeID = '$current_userID'
	AND srp_erp_ngo_donorcollectionmaster.approvedYN = 0
	ORDER BY
	collectionAutoId DESC
	) t UNION
	SELECT
	`budgetAutoID` as DocumentAutoID,
	srp_erp_budgetmaster.documentID as DocumentID,
	`srp_erp_budgetmaster`.`documentSystemCode` AS `DocumentCode`,
	CONCAT( \"Segment : \", `srp_erp_segment`.`description`, \" | Currency : \", transactionCurrency,\" | Financial Year :
	\",companyFinanceYear,\" | \",narration) AS Narration,
	\"-\" AS suppliercustomer,
	\"\" AS currency,
	\"\" AS Amount,
	approvalLevelID AS LEVEL,
	srp_erp_budgetmaster.companyID AS companyID,
	\"\" AS transactionCurrencyDecimalPlaces,
	confirmedByName,
	DATE_FORMAT( confirmedDate, \"%b %D %Y\" ) AS date,
	documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	`srp_erp_budgetmaster`
	LEFT JOIN `srp_erp_segment` ON `srp_erp_budgetmaster`.`segmentID` = `srp_erp_segment`.`segmentID`
	AND `srp_erp_budgetmaster`.`companyID` = `srp_erp_segment`.`companyID`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` =
	`srp_erp_budgetmaster`.`budgetAutoID`
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_budgetmaster`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_budgetmaster`.`currentLevelNo`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_budgetmaster.segmentID
	WHERE
    `srp_erp_budgetmaster`.`budgetType` = 1
	AND `srp_erp_documentapproved`.`approvedYN` = ''
	AND `srp_erp_documentapproved`.`documentID` = 'BD'
	AND `srp_erp_approvalusers`.`documentID` = 'BD'
	AND `srp_erp_approvalusers`.`employeeID` = '$current_userID'
	UNION
	SELECT
	`leaveMasterID` as DocumentAutoID,
	\"LA\" as DocumentID,
	`documentCode` as DocumentCode,
	t1.comments AS Narration,
	CONCAT( ECode, ' - ', empName ) AS suppliercustomer,
	\" \" as currency,
	\"0\" AS Amount,
	currentLevelNo AS LEVEL,
	companyID AS companyID,
	\"1\" AS decimalplaces,
	confirmedByName,
	DATE_FORMAT( confirmedDate, \"%b %D %Y\" ) AS date,
	\"\" documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	segmentcodedes

	FROM
	(
	SELECT
	*,CASE WHEN( currentLevelNo = 1 ) THEN IF( repManager = $current_userID, 1, 0 ) WHEN( currentLevelNo = 2 ) THEN IF( ( '$current_userID' =
	$current_userID) OR ( '$current_userID' = 1165) , 1, 0 ) END AS isInApproval
	FROM
	(
	SELECT
	leaveMasterID,
	`documentCode`,
	`ECode`,
	`Ename2` AS `empName`,
	`approvedYN`,
	`lMaster`.`empID`,
	`currentLevelNo`,
	`repManager`,
	`coveringEmpID` AS `coveringEmp`,
	`startDate`,
	endDate,
	comments,
	lMaster.companyID,
	confirmedByName,
	confirmedDate,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	FROM
	srp_erp_leavemaster AS lMaster
	JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMaster.empID
	LEFT JOIN ( SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers WHERE active = 1 AND companyID =
	'$companyID' ) AS repoManagerTB ON lMaster.empID = repoManagerTB.empID
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = lMaster.segmentID
	WHERE
	lMaster.companyID = '$companyID'
	AND lMaster.confirmedYN = 1
	AND lMaster.approvedYN = '0'
	) AS leaveData
	LEFT JOIN ( SELECT managerID AS topManager, empID AS topEmpID FROM srp_erp_employeemanagers WHERE companyID = '$companyID'
	AND active = 1 ) AS topManagerTB ON leaveData.repManager = topManagerTB.topEmpID
	) AS t1
	WHERE
	`t1`.`isInApproval` = 1
	UNION
	SELECT
	`srp_erp_expenseclaimmaster`.`expenseClaimMasterAutoID` AS `DocumentAutoID`,
	`srp_erp_expenseclaimmaster`.`documentID` AS `DocumentID`,
	`expenseClaimCode` as DocumentCode,
	CONCAT(\" Description : \",comments,\" | Claimed Date : \",DATE_FORMAT( expenseClaimDate, '%d-%m-%Y' )) as Narration,
	`claimedByEmpName` as suppliercustomer,
	`det`.`empCurrency` AS `currency`,
	`det`.`transactionAmount` AS `Amount`,
	\" \" AS LEVEL,
	srp_erp_expenseclaimmaster.companyID AS companyID,
	`det`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
	srp_erp_expenseclaimmaster.confirmedByName,
	DATE_FORMAT( srp_erp_expenseclaimmaster.confirmedDate, \"%b %D %Y\" ) AS date,
	\"\" as documentApprovedID,
	\"\" AS `payrollYear`,
	\"\" AS `payrollMonth`,
	\"\" AS `bankGLAutoID`,
	IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes

	FROM
	`srp_erp_expenseclaimmaster`
	LEFT JOIN ( SELECT SUM( empCurrencyAmount ) AS transactionAmount, expenseClaimMasterAutoID,
	empCurrency,transactionCurrencyDecimalPlaces FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID )
	det ON ( `det`.`expenseClaimMasterAutoID` = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID )
	JOIN `srp_erp_employeemanagers` ON `srp_erp_expenseclaimmaster`.`claimedByEmpID` =
	`srp_erp_employeemanagers`.`empID`
	LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_expenseclaimmaster.segmentID
	WHERE
	`srp_erp_expenseclaimmaster`.`confirmedYN` = 1
	AND `srp_erp_expenseclaimmaster`.`approvedYN` = '0'
	AND `srp_erp_employeemanagers`.`managerID` = '$current_userID'
	AND `srp_erp_employeemanagers`.`active` = 1

	UNION
	SELECT masterTbl.masterID AS DocumentAutoID, masterTbl.documentID AS DocumentID, masterTbl.documentCode AS
	DocumentCode,
	narration as Narration, \"-\" as suppliercustomer, cur_mas.CurrencyCode as currency, 0 as Amount, approvalLevelID AS
	LEVEL,
	masterTbl.companyID, trDPlace as transactionCurrencyDecimalPlaces, masterTbl.confirmedByName,
	DATE_FORMAT(masterTbl.confirmedDate, \"%b %D %Y\" ) AS date, documentApprovedID,
	\"\" AS `payrollYear`, masterTbl.document_type AS `payrollMonth`, \"\" AS `bankGLAutoID`, \"-\" AS `segmentcodedes`
	FROM srp_erp_pay_leaveencashment masterTbl
	JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.masterID
	JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.trCurrencyID
	AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
	JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo
	WHERE srp_erp_documentapproved.documentID = 'LEC' AND srp_erp_approvalusers.documentID = 'LEC'
	AND srp_erp_approvalusers.employeeID = '$current_userID' AND srp_erp_documentapproved.approvedYN = ''

	UNION
	SELECT masterTbl.masterID AS DocumentAutoID, masterTbl.documentID AS DocumentID, masterTbl.documentCode AS
	DocumentCode,
	narration as Narration, \"-\" as suppliercustomer, cur_mas.CurrencyCode as currency, request_amount as Amount,
	approvalLevelID AS LEVEL,
	masterTbl.companyID, trDPlace as transactionCurrencyDecimalPlaces, masterTbl.confirmedByName,
	DATE_FORMAT(masterTbl.confirmedDate, \"%b %D %Y\" ) AS date, documentApprovedID,
	\"\" AS `payrollYear`, \"\" AS `payrollMonth`, \"\" AS `bankGLAutoID`,\"-\" AS `segmentcodedes`
	FROM srp_erp_pay_salaryadvancerequest masterTbl
	JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.masterID
	JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.trCurrencyID
	AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
	JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo
	WHERE srp_erp_documentapproved.documentID = 'SAR' AND srp_erp_approvalusers.documentID = 'SAR'
	AND (
	srp_erp_approvalusers.employeeID = '$current_userID'
	OR (
	srp_erp_approvalusers.employeeID = - 1
	AND masterTbl.empID IN (
	SELECT empmanagers.empID
	FROM srp_employeesdetails empdetail
	JOIN srp_erp_employeemanagers empmanagers ON empdetail.EIdNo = empmanagers.empID
	AND empmanagers.active = 1 WHERE empmanagers.managerID = '$current_userID'
	)
	)
	)
	AND srp_erp_documentapproved.approvedYN = ''

	) t1 ON `t1`.`companyID` = `Company`.`company_id`
	GROUP BY `t1`.`DocumentAutoID`, `t1`.`documentID` ");


        return $query->result_array();
    }

    function getLeaveApprovalSetup($isSetting = 'N', $input_companyId = null)
    {
        if ($input_companyId == null) {
            $companyID = current_companyID();
        } else {
            $companyID = $input_companyId;
        }
        $CI =& get_instance();

        $appSystemValues = $CI->db->query("SELECT * FROM srp_erp_leavesetupsystemapprovaltypes")->result_array();

        if ($isSetting == 'Y') {
            $arr = [0 => ''];
            foreach ($appSystemValues as $key => $val) {
                $arr[$val['id']] = $val['description'];
            }
            $appSystemValues = $arr;
        }

        $approvalLevel = $CI->db->query("SELECT approvalLevel FROM srp_erp_documentcodemaster WHERE documentID = 'LA' AND
                                         companyID={$companyID} ")->row('approvalLevel');

        $approvalSetup = $CI->db->query("SELECT approvalLevel, approvalType, empID, systemTB.*
                                         FROM srp_erp_leaveapprovalsetup AS setupTB
                                         JOIN srp_erp_leavesetupsystemapprovaltypes AS systemTB ON systemTB.id = setupTB.approvalType
                                         WHERE companyID={$companyID} ORDER BY approvalLevel")->result_array();

        $approvalEmp = $CI->db->query("SELECT approvalLevel, empTB.empID
                                       FROM srp_erp_leaveapprovalsetup AS setupTB
                                       JOIN srp_erp_leaveapprovalsetuphremployees AS empTB ON empTB.approvalSetupID = setupTB.approvalSetupID
                                       WHERE setupTB.companyID={$companyID} AND empTB.companyID={$companyID}")->result_array();

        if (!empty($approvalEmp)) {
            $approvalEmp = array_group_by($approvalEmp, 'approvalLevel');
        }

        return [
            'appSystemValues' => $appSystemValues,
            'approvalLevel' => $approvalLevel,
            'approvalSetup' => $approvalSetup,
            'approvalEmp' => $approvalEmp
        ];
    }

    public function test_post()
    {
        $x = $this->Auth_mobileUsers_Model->get_approvals("1138", "13");
        $this->set_response($x, REST_Controller::HTTP_OK);

    }

    public function leaveApproval()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $UserID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $status = $this->post('status');
        $level = $this->post('level');
        $leaveMasterID = $this->post('leaveMasterID');
        $isFromCancelYN = $this->post('isFromCancelYN');
        $comments = $this->post('comments');

        //status = 1 -> approve
        //status= 2 -> referback
        if ($isFromCancelYN == 1) {
            // die( json_encode($this->Employee_model->leave_cancellation_approval()) );
        }
        echo json_encode($this->save_leaveApproval($companyID, $UserID, $status, $level, $comments, $leaveMasterID));
    }

    public function saveleaveApproval_put()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $UserID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companyCode = $output['token']->company_code;
        $userName = $output['token']->name;

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $status = $request->status;
        $leaveMasterID = $request->leaveMasterID;
        $comment = $request->comment;
        $level = $request->level;
        $appdate = $request->appdate;

        $data['result'] = $this->Mobile_leaveApp_Model->saveleaveApproval($UserID, $userName, $companyID, $status, $leaveMasterID, $comment, $companyCode, $level, $appdate);
        $this->set_response($data, REST_Controller::HTTP_OK);

    }

    public function update_expense_claim_details_post()
    {
        $master_id = $this->post('masterID');
        $master_data = Api_spur::document_status('EC', $master_id);

        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $current_userID = $output['token']->id;
        $current_username = $output['token']->username;
        $current_pc = $output['token']->current_pc;

        if ($master_data['error'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => $master_data['message']
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $data['expenseClaimMasterAutoID'] = $master_id;
        $data['expenseClaimCategoriesAutoID'] = $this->post('category_id');
        $data['description'] = trim($this->post('description'));
        $data['referenceNo'] = trim($this->post('reference'));
        $data['segmentID'] = $this->post('segment_id');

        $tr_curr_id = $this->post('currency_id');
        $local_curr = $this->company_info->local_currency;
        $rpt_curr = $this->company_info->rpt_currency;
        $conversion_local = Api_spur::currency_conversion($tr_curr_id, $local_curr);
        $conversion_rpt = Api_spur::currency_conversion($tr_curr_id, $rpt_curr);

        $data['transactionCurrencyID'] = $tr_curr_id;
        $data['transactionCurrency'] = $conversion_local['currencyCode'];
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = $conversion_local['decimalPlaces'];

        $amount = $this->post('amount');
        $amount = round($amount, $conversion_local['decimalPlaces']);
        $data['transactionAmount'] = $amount;


        $data['companyLocalCurrencyID'] = $local_curr;
        $data['companyLocalCurrency'] = $conversion_local['con_currencyCode'];
        $data['companyLocalExchangeRate'] = $conversion_local['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $conversion_local['con_decimalPlaces'];
        $data['companyLocalAmount'] = round(($amount / $conversion_local['conversion']), $conversion_local['con_decimalPlaces']);

        $data['companyReportingCurrencyID'] = $rpt_curr;
        $data['companyReportingCurrency'] = $conversion_rpt['con_currencyCode'];
        $data['companyReportingExchangeRate'] = $conversion_rpt['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $conversion_rpt['con_decimalPlaces'];
        $data['companyReportingAmount'] = round(($amount / $conversion_rpt['conversion']), $conversion_rpt['con_decimalPlaces']);


        $this->db->select('payCurrencyID,payCurrency')->where('EIdNo', $this->user_id)->from('srp_employeesdetails');
        $emp_curr = $this->db->get()->row('payCurrencyID');
        $conversion_emp = Api_spur::currency_conversion($tr_curr_id, $emp_curr);

        $data['empCurrencyID'] = $emp_curr;
        $data['empCurrency'] = $conversion_emp['con_currencyCode'];
        $data['empCurrencyExchangeRate'] = $conversion_emp['conversion'];
        $data['empCurrencyDecimalPlaces'] = $conversion_emp['con_decimalPlaces'];
        $data['empCurrencyAmount'] = round(($amount / $conversion_emp['conversion']), $conversion_emp['con_decimalPlaces']);
        $data['modifiedPCID'] = $current_pc;
        $data['modifiedUserID'] = $current_userID;
        $data['modifiedUserName'] = $current_username;
        $data['modifiedDateTime'] = current_date();

        $this->db->where('expenseClaimDetailsID', $this->post('id'));
        $this->db->update('srp_erp_expenseclaimdetails', $data);

        $expense_claim_details_id = $this->post('id');

        $query = $this->db->get_where('srp_erp_expenseclaimdetails', array('expenseClaimDetailsID' => $expense_claim_details_id));


        $expense_claim_details_data = $query->row_array();
        $expense_claim_details['expenseClaimDetailsID'] = (int)$expense_claim_details_data['expenseClaimDetailsID'];
        $expense_claim_details['expenseClaimMasterAutoID'] = (int)$expense_claim_details_data['expenseClaimMasterAutoID'];
        $expense_claim_details['expenseClaimCategoriesAutoID'] = (int)$expense_claim_details_data['expenseClaimCategoriesAutoID'];
        $expense_claim_details['description'] = $expense_claim_details_data['description'];
        $expense_claim_details['referenceNo'] = $expense_claim_details_data['referenceNo'];
        $expense_claim_details['segmentID'] = (int)$expense_claim_details_data['segmentID'];
        $expense_claim_details['transactionCurrencyID'] = (int)$expense_claim_details_data['transactionCurrencyID'];
        $expense_claim_details['transactionAmount'] = (float)$expense_claim_details_data['transactionAmount'];

        $rt_data = [
            'success' => true,
            'message' => 'Details Updated successfully',
            'data' => $expense_claim_details
        ];
        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function Approval_Details_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;
        $documentAutoID = $this->get('documentAutoID');
        $documentID = $this->get('documentID');

        $approval = $this->Auth_mobileUsers_Model->Approval_details($documentID, $documentAutoID, $companyID, $userID);

        if ($approval) {
            $final_output['success'] = true;
            $final_output['message'] = 'Profile retrieved successfully';
            $final_output['data'] = $approval;

            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = false;
            $final_output['message'] = 'No users were found';
            $final_output['data'] = null;

            $this->response($final_output, REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    function importpossaleswithitems_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $companyID = $output['token']->Erp_companyID;
        $userID = $output['token']->id;
        $date = $this->post('date');

        $approval = $this->Auth_mobileUsers_Model->posSalesWithItems_details($date, $companyID);

        if ($approval) {
            $final_output['success'] = true;
            $final_output['message'] = 'Data retrieved successfully';
            $final_output['data'] = $approval;

            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = false;
            $final_output['message'] = 'No details were found';
            $final_output['data'] = [];

            $this->response($final_output, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    function send_approvalEmail_post()
    {
        $request_body = file_get_contents('php://input');
        $request_1 = json_decode($request_body);

        $approvalEmpID = isset($request_1->approvalEmpID) ? $request_1->approvalEmpID : null;
        $documentCode = isset($request_1->documentCode) ? ($request_1->documentCode) : null;
        $toEmail = isset($request_1->toEmail) ? ($request_1->toEmail) : null;
        $subject = isset($request_1->subject) ? ($request_1->subject) : null;
        $param = isset($request_1->param) ? ($request_1->param) : null;

        $mailData = array(
            'approvalEmpID' => $approvalEmpID,
            'documentCode' => $documentCode,
            'toEmail' => $toEmail,
            'subject' => $subject,
            'param' => $param,
        );
        send_approvalEmail($mailData);


        $final_output['success'] = true;
        $final_output['message'] = 'Mail sent successfully';
        $final_output['data'] = [];
        $this->response($final_output, REST_Controller::HTTP_OK);
    }

    public function mobile_attendance_register_post()
    {
        if($this->user->isMobileCheckIn != 1){
            $rt_data = [
                'success' => false,
                'message' => 'You have not authorized for clock in/out with mobile device.'
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $company_id = $this->company_id;
        $user_id = $this->user_id;
        $currentPc = $this->company_info->current_pc;

        $timeZone = $this->db->query("SELECT description FROM srp_erp_timezonedetail JOIN srp_erp_company ON srp_erp_company.defaultTimezoneID = srp_erp_timezonedetail.detailID WHERE company_id = $company_id")->row('description');
        if($timeZone) {
            $date = new DateTime( 'now', new DateTimeZone($timeZone));
            $currentTime = $date->format('Y-m-d H:i:s');
        } else {
            $currentTime = $this->current_time();
        } 
        
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        $attData = $this->Auth_mobileUsers_Model->getMobileAttendanceLocations($user_id, $company_id, 1, $latitude, $longitude);

        $data = [
            'device_id'=> $attData['deviceID'], 'machineAutoID'=> 0, 'empMachineID'=> $attData['empMachineID'], 'attDate'=> $currentTime,
            'attTime'=> $currentTime, 'attDateTime'=> $currentTime, 'latitude'=> $latitude, 'longitude'=> $longitude,
            'uploadType'=> 3, 'companyID'=> $company_id, 'createdUserID'=> $currentPc, 'timestamp'=> $currentTime
        ];

        $this->db->insert('srp_erp_pay_empattendancetemptable', $data);

        $rt_data = [
            'success' => true,
            'message' => 'Attendance marked successfully.',
            'data' => [
                'att_status' => $this->mobile_attendance_status_get(true)
            ]
        ];

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function mobile_attendance_status_get($isCallBack=false){
        $company_id = $this->company_id;
        $user_id = $this->user_id;
        $date = $this->current_time('d');

        $rec_count = $this->db->query("SELECT COUNT(att.autoID ) AS rec_count
                        FROM srp_erp_pay_empattendancetemptable AS att
                        JOIN srp_erp_empattendancelocation AS empLocTB  ON empLocTB.empMachineID = att.empMachineID AND empLocTB.deviceID = att.device_id
                        WHERE att.companyID = {$company_id} AND empID = {$user_id} AND attDate = '{$date}'")->row('rec_count');
        $att_status = ($rec_count % 2);

        if($isCallBack){
            return $att_status;
        }

        $rt_data = [
            'success' => true,
            'message' => '',
            'data' => [
                'att_status' => $att_status
            ]
        ];

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function supplier_invoice_header_drops_get()
    {
        $data = [];
        if ($this->get('segment')) {
            $data['user_segment'] = (int)$this->user->segmentID;
            //
            $segment_data = $this->segments_drop_get(1);
            $segment_data_array = array();
            foreach ($segment_data as $item) {
                $segment_data_item = array();
                $segment_data_item['segmentID'] = (int)$item['segmentID'];
                $segment_data_item['segmentCode'] = $item['segmentCode'];
                $segment_data_item['description'] = $item['description'];
                array_push($segment_data_array, $segment_data_item);
            }
            $data['segment'] = $segment_data_array;
        }

        if ($this->get('currency')) {
            $data['user_currency'] = (int)$this->user->payCurrencyID;
            // $data['currency'] = $this->currency_drop_get(1);
            $currency_data = $this->currency_drop_get(1);
            $currency_data_array = array();
            foreach ($currency_data as $item) {
                $currency_data_item = array();
                $currency_data_item['currencyID'] = (int)$item['currencyID'];
                $currency_data_item['currencyCode'] = $item['currencyCode'];
                $currency_data_item['currencyName'] = $item['currencyName'];
                $currency_data_item['decimalPlaces'] = (int)$item['decimalPlaces'];
                array_push($currency_data_array, $currency_data_item);
            }
            $data['currency'] = $currency_data_array;
        }

        if ($this->get('employee')) {
            $data['current_employee'] = (int)current_userID();
            $emp_data = $this->employee_with_bank_data();
            $emp_data_array = array();
            foreach ($emp_data as $item) {
                $emp_data_item = array();
                $emp_data_item['EIdNo'] = (int)$item['EIdNo'];
                $emp_data_item['ECode'] = $item['ECode'];
                $emp_data_item['Ename2'] = $item['Ename2'];
                array_push($emp_data_array, $emp_data_item);
            }
            $data['employee'] = $emp_data_array;
        }

        if ($this->get('bank')) {
            $bank_acc = company_bank_account_drop(1);

            if($bank_acc) {
                foreach ($bank_acc as $key => $bank)
                {
                    $bank_acc[$key]['GLAutoID'] = (int)$bank['GLAutoID'];
                    $bank_acc[$key]['isCash'] = (int)$bank['isCash'];

                    $this->load->model('Auth_mobileUsers_Model');
                    $_POST['GLAutoID'] = $bank['GLAutoID'];
                    $_POST['chequeRegisterDetailID'] = $this->get('chequeRegisterDetailID');
                    $chequeNo = $this->Auth_mobileUsers_Model->fetch_cheque_number();

                    $chequeNo['master']['bankCheckNumber'] = (int)$chequeNo['master']['bankCheckNumber'];
                    $chequeNo['master']['isCash'] = (int)$chequeNo['master']['isCash'];

                    foreach ($chequeNo['detail'] as $key2 => $row) {
                        $chequeNo['detail'][$key2]['chequeRegisterDetailID'] = (int)$row['chequeRegisterDetailID'];
                        $chequeNo['detail'][$key2]['chequeNo'] = (int)$row['chequeNo'];
                    }
                    $bank_acc[$key]['cheque_details'] = $chequeNo;
                }
            }
            $data['bank'] = $bank_acc;
        }

        if ($this->get('supplier')) {
            $supplier_data = $this->supplier_drop_get();
            $expense_claim_data_array = array();
            foreach ($supplier_data as $item) {
                $expense_claim_data_item = array();
                $expense_claim_data_item['id'] = (int)$item['supplierAutoID'];
                $expense_claim_data_item['sup_code'] = $item['supplierSystemCode'];
                $expense_claim_data_item['sup_desc'] = $item['supplierName'];
                $expense_claim_data_item['sup_currency'] = (int)$item['supplierCurrencyID'];
                array_push($expense_claim_data_array, $expense_claim_data_item);
            }
            $data['supplier'] = $expense_claim_data_array;
        }

        if ($this->get('salesPerson')) {
            $companyID = current_companyID();
            $sales_person = $this->db->query("SELECT salesPersonID,SalesPersonName,SalesPersonCode,wareHouseLocation FROM srp_erp_salespersonmaster WHERE companyID = {$companyID}")->result_array();
            $sales_person_data_array = array();
            foreach ($sales_person as $sales) {
                $sales_person_data_arr = array();
                $sales_person_data_arr['salesPersonID'] = (int)$sales['salesPersonID'];
                $sales_person_data_arr['SalesPersonName'] = $sales['SalesPersonName'];
                $sales_person_data_arr['SalesPersonCode'] = $sales['SalesPersonCode'];
                $sales_person_data_arr['wareHouseLocation'] = $sales['wareHouseLocation'];
                array_push($sales_person_data_array, $sales_person_data_arr);
            }
            $data['salesPerson'] = $sales_person_data_array;
        }

        if ($this->get('customer')) {
            $supplier_data = $this->customer_drop_get();
            $expense_claim_data_array = array();
            foreach ($supplier_data as $item) {
                $expense_claim_data_item = array();
                $expense_claim_data_item['id'] = (int)$item['customerAutoID'];
                $expense_claim_data_item['cus_code'] = $item['customerSystemCode'];
                $expense_claim_data_item['cus_desc'] = $item['customerName'];
                $expense_claim_data_item['cus_country'] = $item['customerCountry'];
                $expense_claim_data_item['cus_currency'] = (int)$item['customerCurrencyID'];
                array_push($expense_claim_data_array, $expense_claim_data_item);
            }
            $data['customer'] = $expense_claim_data_array;
        }

        $chequeRegistryPolicy = $this->Mobile_leaveApp_Model->getPolicyValues('CRE', 'All', current_companyID());
        $data['chequeRegistryPolicy'] = (int)$chequeRegistryPolicy;

        $final_output['success'] = true;
        $final_output['message'] = "";
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function employee_with_bank_data(){
        $companyID = current_companyID();
        $data = $this->db->query("SELECT EIdNo, ECode, Ename2, bankAcc.*
                        FROM srp_employeesdetails AS empTB
                        LEFT JOIN (
                            SELECT empID, acc.bankID, accountNo, accountHolderName, bankName, bankSwiftCode 
                            FROM srp_erp_pay_bankmaster AS bnk 
                            JOIN (
                                SELECT employeeNo AS empID, bankID, accountNo, accountHolderName
                                FROM srp_erp_pay_salaryaccounts WHERE companyID = {$companyID} AND isActive = 1
                                GROUP BY employeeNo
                            ) AS acc ON acc.bankID=bnk.bankID
                        )  bankAcc ON bankAcc.empID = empTB.EIdNo
                        WHERE Erp_companyID = {$companyID} AND isPayrollEmployee = 1 AND isDischarged = 0")->result_array();

        return $data;
    }

    function supplier_drop_get()
    {
        $this->db->select("supplierAutoID, supplierSystemCode, supplierName, supplierCurrencyID");
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('masterApprovedYN', "1");
        $this->db->where('isActive', 1);
        $data = $this->db->where('companyID', current_companyID())->get()->result_array();
        return $data;
    }

    public function fetch_supplier_inovices_get()
    {
        $start_date = $this->get('date_range_start');
        $end_date = $this->get('date_range_end');
        $status = $this->get('status');

        $this->db->select('masTB.InvoiceAutoID AS InvoiceAutoID,
                                bookingInvCode AS bsiCode,
                                masTB.supplierID AS supplierId,
                                srp_erp_suppliermaster.supplierName AS supplierName,
                                bookingDate AS documentDate,
                                invoiceDueDate AS invoiceDueDate,
                                IFNULL(masTB.supplierInvoiceNo, \'-\' ) AS supInvNo,
                                invoiceDate AS supInvDate,
                                invoiceType AS type,
                                masTB.RefNo AS ref,
                                masTB.comments AS narration,
                                CASE
                                    WHEN (isDeleted = 1) THEN 5
                                    WHEN (confirmedYN = 3) THEN 4
                                    WHEN (approvedYN = 1 AND confirmedYN = 1) THEN 3
                                    WHEN (approvedYN = 0 AND confirmedYN = 1) THEN 2
                                    ELSE 1
                                END status,
                                CASE
                                    WHEN (invoiceType = "GRV Base") THEN "GRV Base"
                                    WHEN (invoiceType = "StandardPO") THEN "Direct PO"
                                    WHEN (invoiceType = "StandardItem") THEN "Direct Item"
                                    WHEN (invoiceType = "StandardExpense") THEN "Direct Expense"
                                    ELSE "Direct"
                                END documentTypeLabel,                            
                                ((((IFNULL( addondet.taxPercentage, 0 )/ 100)*(IFNULL( detTB.transactionAmount, 0 )-((IFNULL( generalDiscountPercentage, 0 )/ 100)* IFNULL( detTB.transactionAmount, 0 ))))+ IFNULL( detTB.transactionAmount, 0 ))-((IFNULL( generalDiscountPercentage, 0 )/ 100)* IFNULL( detTB.transactionAmount, 0 ))) AS total_value,
                                transactionCurrencyID')
            ->from('srp_erp_paysupplierinvoicemaster AS masTB')->where('masTB.companyID', $this->company_id)
            ->join("(SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) AS detTB", 'detTB.InvoiceAutoID = masTB.InvoiceAutoID', 'left')
            ->join("(SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID) AS addondet", 'addondet.InvoiceAutoID = masTB.InvoiceAutoID', 'left')
            ->join("srp_erp_suppliermaster", 'srp_erp_suppliermaster.supplierAutoID = masTB.supplierID', 'left')
            ->where("bookingDate BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:00'");
        switch ($status) {
            case 1:
                $this->db->where('confirmedYN', 0);
                break;
            case 2:
                $this->db->where("confirmedYN = 1 AND approvedYN = 0");
                break;
            case 3:
                $this->db->where('approvedYN', 1);
                break;
            case 4:
                $this->db->where('confirmedYN', 3);
                break;
            case 5:
                $this->db->where('isDeleted', 1);
                break;
        }
        $this->db->where('isDeleted', 0);
        $data = $this->db->order_by('masTB.InvoiceAutoID', 'DESC')->get()->result_array();

        foreach ($data as $key => $row) {
            $data[$key]['InvoiceAutoID'] = (int)$row['InvoiceAutoID'];
            $data[$key]['supplierId'] = (int)$row['supplierId'];
            $data[$key]['total_value'] = (float)$row['total_value'];
            $currencyID = (int)$row['transactionCurrencyID'];
            $currency = $this->db->query("SELECT CurrencyCode AS code, DecimalPlaces AS decimals, CurrencyName AS description
                                           FROM srp_erp_currencymaster WHERE currencyID = {$currencyID}")->row_array();
            if ($currency != null) {
                $currency['decimals'] = (int)$currency['decimals'];
            }
            $data[$key]['currency'] = $currency;
        }

        $rt_data = [
            'success' => true,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function create_supplier_invoice_post()
    {
        $invoiceID = $this->input->post('invoiceID');
        $company_id = $this->company_id;
        $date_format_policy_details = $this->session_model->fetch_company_policy($company_id);
        $date_format_policy = $date_format_policy_details['DF']['All'][0]["policyvalue"];
        $bookDate = $this->input->post('documentDate');
        $bookingDate = input_format_date($bookDate, $date_format_policy);
        $supplierID = $this->input->post('supplierID');
        $currencyID = $this->input->post('currencyID');
        $supplirInvDuDate = $this->input->post('invoiceDueDate');
        $supplierInvoiceDueDate = input_format_date($supplirInvDuDate, $date_format_policy);
        $supplierinvoice = $this->input->post('invoiceNo');
        $supplirinvoiceDate = $this->input->post('invoiceDate');
        $supplierinvoiceDate_new = input_format_date($supplirinvoiceDate, $date_format_policy);

        $financeYearDetails = $this->db->query("SELECT * FROM srp_erp_companyfinanceyear WHERE companyID = {$company_id} AND ('{$bookingDate}' BETWEEN beginingDate AND endingDate)")->row_array();
        $financePeriodDetails = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod WHERE companyID = {$company_id} AND ('{$bookingDate}' BETWEEN dateFrom AND dateTo)")->row_array();
        $supplier_arr = $this->db->query("SELECT * FROM srp_erp_suppliermaster WHERE supplierAutoID = {$supplierID}")->row_array();

        if(trim($this->input->post('invoiceType') ?? '') == 1) {
            $invoiceType = 'Standard';
        } else {
            $invoiceType = 'GRV Base';
        }

        $data['invoiceType'] = $invoiceType;
        $data['bookingDate'] = trim($bookingDate);
        $data['invoiceDueDate'] = trim($supplierInvoiceDueDate);
        $data['invoiceDate'] = trim($supplierinvoiceDate_new);
        $data['companyFinanceYearID'] = $financeYearDetails['companyFinanceYearID'];
        $data['companyFinanceYear'] = $financeYearDetails['beginingDate'] . ' - ' . $financeYearDetails['endingDate'];
        $data['FYBegin'] = trim($financeYearDetails['beginingDate'] ?? '');
        $data['FYEnd'] = trim($financeYearDetails['endingDate'] ?? '');
        $data['companyFinancePeriodID'] = trim($financePeriodDetails['companyFinancePeriodID'] ?? '');
        $data['documentID'] = 'BSI';
        $data['supplierID'] = trim($supplierID);
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['supplierInvoiceNo'] = trim($supplierinvoice);
        $data['supplierInvoiceDate'] = trim($supplierInvoiceDueDate);

        $segmentID = $this->input->post('segmentID');
        $segmentCode = $this->db->query("SELECT segmentCode FROM srp_erp_segment WHERE segmentID = {$segmentID}")->row('segmentCode');
        $data['segmentID'] = trim($segmentID);
        $data['segmentCode'] = trim($segmentCode);
        $data['RefNo'] = trim($this->input->post('referenceNo') ?? '');
        $data['comments'] = trim($this->input->post('Narration') ?? '');

        $data['transactionCurrencyID'] = $currencyID;
        $data['transactionCurrency'] = fetch_currency_code($data['transactionCurrencyID']);
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);

        $data['companyLocalCurrencyID'] = $this->company_info->local_currency;
        $data['companyLocalCurrency'] = $this->company_info->local_currency_code;
        $default_currency = $this->currency_conversion($data['transactionCurrencyID'], $data['companyLocalCurrencyID'], '', $company_id);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['decimalPlaces'];

        $data['companyReportingCurrency'] = $this->company_info->rpt_currency_code;
        $data['companyReportingCurrencyID'] = $this->company_info->rpt_currency;
        $reporting_currency = $this->currency_conversion($data['transactionCurrencyID'], $data['companyReportingCurrencyID'], '', $company_id);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['decimalPlaces'];

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = $this->currency_conversion($data['transactionCurrencyID'], $data['supplierCurrencyID'], '', $company_id);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['decimalPlaces'];

        if(!empty($invoiceID)) {
            if (!empty($supplierinvoice) || $supplierinvoice != '') {
                $q = "SELECT supplierInvoiceNo,supplierID FROM srp_erp_paysupplierinvoicemaster WHERE supplierID = '". $supplierID ."'  AND supplierInvoiceNo = '". $supplierinvoice ."' AND InvoiceAutoID != {$invoiceID}";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $rt_data = [
                        'success' => false,
                        'message' => 'Supplier Invoice Number already exist for the selected supplier.',
                        'data' => null
                    ];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }
            }
            $data['modifiedPCID'] = $this->company_info->current_pc;
            $data['modifiedUserID'] = $this->user_id;
            $data['modifiedUserName'] = $this->user->name2;
            $data['modifiedDateTime'] = $this->current_time();

            $this->db->where('InvoiceAutoID', trim($invoiceID));
            $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
            $master_data = $this->fetch_supplier_invoice_get($invoiceID);

            $rt_data = [
                'success' => true,
                'message' => 'Invoice Updated Successfully.',
                'data' => $master_data
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        } else {
            if (!empty($supplierinvoice) || $supplierinvoice != '') {
                $q = "SELECT supplierInvoiceNo,supplierID FROM srp_erp_paysupplierinvoicemaster WHERE supplierID = '". $supplierID ."'  AND supplierInvoiceNo = '". $supplierinvoice ."'";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $rt_data = [
                        'success' => false,
                        'message' => 'Supplier Invoice Number already exist for the selected supplier.',
                        'data' => null
                    ];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }
            }

            $data['companyCode'] = $this->company_info->company_code;
            $data['companyID'] = $company_id;
            $data['createdUserGroup'] = $this->company_info->usergroupID;
            $data['createdPCID'] = $this->company_info->current_pc;
            $data['createdUserID'] = $this->user_id;
            $data['createdUserName'] = $this->user->name2;
            $data['createdDateTime'] = $this->current_time();
            $data['bookingInvCode'] = 0;

            $this->db->insert('srp_erp_paysupplierinvoicemaster', $data);
            $last_id = $this->db->insert_id();
            $master_data = $this->fetch_supplier_invoice_get($last_id);

            $rt_data = [
                'success' => true,
                'message' => 'Invoice Created Successfully.',
                'data' => $master_data
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
    }

    public function fetch_supplier_invoice_get($id = null)
    {
        $master_id = (!empty($id)) ? $id : $this->get('masterID');

        $data = $this->db->select('masTB.InvoiceAutoID AS InvoiceAutoID,
                                bookingInvCode AS bsiCode,
                                masTB.supplierID AS supplierId,
                                srp_erp_suppliermaster.supplierName AS supplierName,
                                bookingDate AS documentDate,
                                invoiceDueDate AS invoiceDueDate,
                                IFNULL(masTB.supplierInvoiceNo, \'-\' ) AS supInvNo,
                                invoiceDate AS supInvDate,
                                generalDiscountAmount, 
                                generalDiscountPercentage,
                                invoiceType AS type,
                                masTB.RefNo AS ref,
                                masTB.comments AS narration,
                                CASE
                                    WHEN (isDeleted = 1) THEN 5
                                    WHEN (confirmedYN = 3) THEN 4
                                    WHEN (approvedYN = 1 AND confirmedYN = 1) THEN 3
                                    WHEN (approvedYN = 0 AND confirmedYN = 1) THEN 2
                                    ELSE 1
                                END status,    
                                CASE
                                    WHEN (invoiceType = "GRV Base") THEN "GRV Base"
                                    WHEN (invoiceType = "StandardPO") THEN "Direct PO"
                                    WHEN (invoiceType = "StandardItem") THEN "Direct Item"
                                    WHEN (invoiceType = "StandardExpense") THEN "Direct Expense"
                                    ELSE "Direct"
                                END documentTypeLabel,                             
                                ((((IFNULL( addondet.taxPercentage, 0 )/ 100)*(IFNULL( detTB.transactionAmount, 0 )-((IFNULL( generalDiscountPercentage, 0 )/ 100)* IFNULL( detTB.transactionAmount, 0 ))))+ IFNULL( detTB.transactionAmount, 0 ))-((IFNULL( generalDiscountPercentage, 0 )/ 100)* IFNULL( detTB.transactionAmount, 0 ))) AS total_value,
                                transactionCurrencyID')
            ->from('srp_erp_paysupplierinvoicemaster AS masTB')->where('masTB.InvoiceAutoID', $master_id)
            ->join("(SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) AS detTB", 'detTB.InvoiceAutoID = masTB.InvoiceAutoID', 'left')
            ->join("(SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID) AS addondet", 'addondet.InvoiceAutoID = masTB.InvoiceAutoID', 'left')
            ->join("srp_erp_suppliermaster", 'srp_erp_suppliermaster.supplierAutoID = masTB.supplierID', 'left')
            ->where('masTB.companyID', $this->company_id)->get()->row_array();

        $data['InvoiceAutoID'] = (int)$data['InvoiceAutoID'];
        $data['supplierId'] = (int)$data['supplierId'];
        $data['total_value'] = (float)$data['total_value'];
        $data['generalDiscountAmount'] = (float)$data['generalDiscountAmount'];
        $data['generalDiscountPercentage'] = (float)$data['generalDiscountPercentage'];
        $currencyID = (int)$data['transactionCurrencyID'];
        $currency = $this->db->query("SELECT CurrencyCode AS code, DecimalPlaces AS decimals, CurrencyName AS description FROM srp_erp_currencymaster WHERE currencyID = {$currencyID}")->row_array();
        if ($currency != null) {
            $currency['decimals'] = (int)$currency['decimals'];
        }
        $data['currency'] = $currency;

        $data['details'] = $this->get_BSI_details($master_id);
        $data['attachments'] = $this->get_attachments('BSI', $master_id, $this->company_id);
        $data['approval_details']=$this->Mobile_leaveApp_Model->fetch_all_approval_users_modal($this->company_id, "BSI", $master_id);

        $addon_data = $this->db->query("SELECT
	srp_erp_paysupplierinvoicetaxdetails.InvoiceAutoID,
	srp_erp_paysupplierinvoicetaxdetails.taxDetailAutoID,
	srp_erp_paysupplierinvoicetaxdetails.taxMasterAutoID,
	transactionAmount,
	taxDescription,
	taxShortCode,
	taxPercentage,
	systemGLCode,
	transactionCurrencyID AS currencyID,
	transactionCurrency AS currency,
	CurrencyName AS currencyDescription,
	transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces
FROM
	srp_erp_paysupplierinvoicetaxdetails
	LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_paysupplierinvoicetaxdetails.transactionCurrencyID
	WHERE 
    srp_erp_paysupplierinvoicetaxdetails.companyID =  {$this->company_id} 
	AND srp_erp_paysupplierinvoicetaxdetails.invoiceAutoID = {$master_id}")->result_array();

        $data['addon'] = array();
        if(!empty($addon_data)){
            foreach ($addon_data as $det){
                $tax_arr = array();
                foreach (array_keys($det) as $tax)
                switch ($det) {
                    CASE 'transactionAmount' :
                        $tax_arr[$tax] = (double)$det[$tax];
                        BREAK;

                    CASE 'InvoiceAutoID' :  CASE 'taxDetailAutoID' :  CASE 'taxMasterAutoID' :
                        $tax_arr[$tax] = (int)$det[$tax];
                        BREAK;

                    CASE 'currencyID' :
                        if(!empty($det[$tax])) {
                            $taxcurrency_arr['currencyID'] = (int)$det['currencyID'];
                            $taxcurrency_arr['code'] = $det['currency'];
                            $taxcurrency_arr['description'] = $det['currencyDescription'];
                            $taxcurrency_arr['decimalPlaces'] = (int)$det['transactionCurrencyDecimalPlaces'];
                        } else {
                            $taxcurrency_arr = null;
                        }
                        BREAK;

                    CASE 'currency' :
                    CASE 'currencyDescription' :
                    CASE 'transactionCurrencyDecimalPlaces' :
                    CASE 'currencyID' :
                        break;

                    DEFAULT :
                        $tax_arr[$tax] = $det[$tax];
                }
//                var_dump($addon_data);
                array_push($data['addon'], $tax_arr);
            }
        }

        if (!empty($id)) {
            return $data;
        }

        $rt_data = [
            'success' => true,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];



        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function get_BSI_details($documentAutoID)
    {
        $details = $this->db->query("SELECT
                    srp_erp_paysupplierinvoicedetail.InvoiceAutoID,
                    srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID,
                    srp_erp_paysupplierinvoicedetail.description,
                    type, itemAutoID,
                    GLAutoID, systemGLCode, GLDescription, GLCode, GLType,
                    srp_erp_paysupplierinvoicedetail.transactionAmount,
                    srp_erp_paysupplierinvoicedetail.segmentID,
                    transactionCurrencyID AS currencyID,
                    transactionCurrency AS CODE,
                    CurrencyName AS currencyDescription,
                    transactionCurrencyDecimalPlaces AS decimalPlaces,
                    srp_erp_segment.segmentCode,
                    srp_erp_segment.description AS segment,
                    srp_erp_paysupplierinvoicedetail.wareHouseAutoID, wareHouseCode, wareHouseLocation, wareHouseDescription,
                    defaultUOMID, defaultUOM, UnitDes, conversionRateUOMID,
                    srp_erp_paysupplierinvoicedetail.discountAmount,
                    srp_erp_paysupplierinvoicedetail.discountPercentage,
                    srp_erp_paysupplierinvoicedetail.requestedQty,
                    srp_erp_paysupplierinvoicedetail.unittransactionAmount
                FROM
                    srp_erp_paysupplierinvoicedetail
                    LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID
                    LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_paysupplierinvoicemaster.transactionCurrencyID
                    LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_erp_paysupplierinvoicedetail.segmentID
                    LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.defaultUOMID
                WHERE
                    srp_erp_paysupplierinvoicedetail.companyID = {$this->company_id} 
                    AND srp_erp_paysupplierinvoicedetail.InvoiceAutoID = {$documentAutoID}")->result_array();

        $x = array();
        foreach ($details AS $val){
            $a = array();
            $currency_arr = array();
            $segment_arr = array();
            $warehouse_arr = array();
            $uom_arr = array();
            $glCode_arr = array();
            $item_arr = array();
            foreach (array_keys($val) as $det) {
                switch ($det) {
                    CASE 'InvoiceAutoID' : CASE 'InvoiceDetailAutoID' :
                        $a[$det] = (int)$val[$det];
                        BREAK;

                    CASE 'currencyID' :
                        if(!empty($val[$det])) {
                            $currency_arr['currencyID'] = (int)$val['currencyID'];
                            $currency_arr['code'] = $val['CODE'];
                            $currency_arr['description'] = $val['currencyDescription'];
                            $currency_arr['decimalPlaces'] = (int)$val['decimalPlaces'];
                        } else {
                            $currency_arr = null;
                        }
                        BREAK;

                    CASE 'segmentID' :
                        if(!empty($val['segmentID'])) {
                            $segment_arr['segmentID'] = (int)$val['segmentID'];
                            $segment_arr['segmentCode'] = $val['segmentCode'];
                            $segment_arr['description'] = $val['segment'];
                        } else {
                            $segment_arr = null;
                        }
                        BREAK;

                    CASE 'wareHouseAutoID' :
                        if(!empty($val['wareHouseAutoID'])) {
                            $warehouse_arr['wareHouseAutoID'] = (int)$val['wareHouseAutoID'];
                            $warehouse_arr['wareHouseCode'] = $val['wareHouseCode'];
                            $warehouse_arr['wareHouseLocation'] = $val['wareHouseLocation'];
                            $warehouse_arr['wareHouseDescription'] = $val['wareHouseDescription'];
                        } else {
                            $warehouse_arr = null;
                        }
                        BREAK;

                    CASE 'type' :
                        if(trim($val['type'] ?? '') == 'GL') {
                            $item_arr = null;

                            $a[$det] = $val[$det];
                            $glCode_arr['GLAutoID'] = (int)$val['GLAutoID'];
                            $glCode_arr['systemAccountCode'] = $val['systemGLCode'];
                            $glCode_arr['GLSecondaryCode'] = $val['GLCode'];
                            $glCode_arr['GLDescription'] = $val['GLDescription'];
                            $glCode_arr['subCategory'] = $val['GLType'];
                        } else {
                            $glCode_arr = null;

                            $a[$det] = $val[$det];
                            $itemData = $this->db->query("SELECT
                            srp_erp_itemmaster.mainCategory as mainCategory,mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,
                            CONCAT( IFNULL(itemDescription,'empty'), ' - ', IFNULL(itemSystemCode,'empty'), ' - ', IFNULL(partNo,'empty')  , ' - ', IFNULL(seconeryItemCode,'empty')) AS 'Match',
                            revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,
                            srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.currentStock,companyLocalWacAmount,companyLocalSellingPrice,
                            isSubitemExist,
                            srp_erp_itemcategory.categoryTypeID,
                            srp_erp_itemmaster.secondaryUOMID as secondaryUOMID,
                            itemledgercurrent.currentstock AS itemledgstock 
                    FROM srp_erp_itemmaster
                    LEFT JOIN srp_erp_itemcategory ON srp_erp_itemmaster.mainCategoryID = srp_erp_itemcategory.itemCategoryID
                    LEFT JOIN (SELECT IF (mainCategory = 'Inventory',(TRIM(TRAILING '.' FROM TRIM(TRAILING 0 FROM(ROUND(SUM(transactionQTY / convertionRate), 4))))), ' ') AS currentstock, srp_erp_itemledger.itemAutoID FROM `srp_erp_itemledger`
                    LEFT JOIN srp_erp_itemmaster on srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
                                        GROUP BY srp_erp_itemledger.itemAutoID)itemledgercurrent on itemledgercurrent.itemAutoID = srp_erp_itemmaster.itemAutoID
                    WHERE srp_erp_itemmaster.itemAutoID = {$val['itemAutoID']}")->row_array();

                            $item_arr['itemAutoID'] = (int)$val['itemAutoID'];
                            $item_arr['requestedQty'] = (float)$val['requestedQty'];
                            $item_arr['unittransactionAmount'] = (double)$val['unittransactionAmount'];
                            $item_arr['mainCategory'] = $itemData['mainCategory'];
                            $item_arr['mainCategoryID'] = (int)$itemData['mainCategoryID'];
                            $item_arr['subcategoryID'] = (int)$itemData['subcategoryID'];
                            $item_arr['secondaryItemCode'] = $itemData['seconeryItemCode'];
                            $item_arr['subSubCategoryID'] = (int)$itemData['subSubCategoryID'];
                            $item_arr['revanueGLCode'] = $itemData['revanueGLCode'];
                            $item_arr['itemSystemCode'] = $itemData['itemSystemCode'];
                            $item_arr['costGLCode'] = $itemData['costGLCode'];
                            $item_arr['assteGLCode'] = $itemData['assteGLCode'];
                            $item_arr['defaultUnitOfMeasure'] = $itemData['defaultUnitOfMeasure'];
                            $item_arr['defaultUnitOfMeasureID'] = (int)$itemData['defaultUnitOfMeasureID'];
                            $item_arr['itemDescription'] = $itemData['itemDescription'];
                            $item_arr['currentStock'] = (float)$itemData['currentStock'];
                            $item_arr['companyLocalWacAmount'] = $itemData['companyLocalWacAmount'];
                            $item_arr['companyLocalSellingPrice'] = (float)$itemData['companyLocalSellingPrice'];
                            $item_arr['Match'] = $itemData['Match'];
                            $item_arr['isSubitemExist'] = $itemData['isSubitemExist'];
                            $item_arr['categoryTypeID'] = $itemData['categoryTypeID'];
                            $item_arr['secondaryUOMID'] = $itemData['secondaryUOMID'];
                            $item_arr['itemledgstock'] = $itemData['itemledgstock'];

                            $this->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion');
                            $this->db->from('srp_erp_unitsconversion');
                            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
                            $this->db->where('masterUnitID',$itemData['defaultUnitOfMeasureID']);
                            $this->db->where('srp_erp_unitsconversion.companyID',$this->company_id);
                            $item_arr['UOM'] = $this->db->get()->result_array();
                        }
                        BREAK;

                    CASE 'defaultUOMID' :
                        if(!empty($val['defaultUOMID'])) {
                            $uom_arr['UnitID'] = (int)$val['defaultUOMID'];
                            $uom_arr['UnitShortCode'] = $val['defaultUOM'];
                            $uom_arr['UnitDes'] = $val['UnitDes'];
                            $uom_arr['conversion'] = $val['conversionRateUOMID'];
                        } else {
                            $uom_arr = null;
                        }
                        BREAK;

                    CASE 'transactionAmount' :
                        $a[$det] = (double)$val[$det];
                        BREAK;

                    CASE 'discountPercentage' :
                        $a[$det] = (float)$val[$det];
                        BREAK;

                    CASE 'discountAmount' :
                        $a[$det] = (double)$val[$det];
                        BREAK;

                    CASE 'CODE' : CASE 'currencyDescription' : CASE 'decimalPlaces' :
                    CASE 'segmentCode' :  CASE 'segment' :
                    CASE 'itemAutoID' : CASE 'requestedQty' : CASE 'unittransactionAmount' :
                    CASE 'wareHouseCode' : CASE 'wareHouseLocation' : CASE 'wareHouseDescription' :
                    CASE 'defaultUOM' : CASE 'UnitDes' : CASE 'conversionRateUOMID' :
                    CASE 'GLAutoID' : CASE 'systemGLCode' : CASE 'GLCode' : CASE 'GLDescription' : CASE 'GLType' :
                        break;

                    DEFAULT :
                        $a[$det] = $val[$det];
                }
                $a['currency'] = $currency_arr;
                $a['segmentCode'] = $segment_arr;
                $a['warehouse'] = $warehouse_arr;
                $a['selectedUOM'] = $uom_arr;
                $a['GLCode'] = $glCode_arr;
                $a['itemCode'] = $item_arr;
            }
            array_push($x, $a);
        }
        return $x;
    }

    public function supplier_invoice_details_drops_get()
    {
        $data = [];
        if ($this->get('warehouse')) {
            $warehouse_data = $this->warehouse_drop_get(1);

            foreach ($warehouse_data as $key => $row) {
                $warehouse_data[$key]['wareHouseAutoID'] = (int)$row['wareHouseAutoID'];
            }
            $data['warehouse'] = $warehouse_data;
        }

        if ($this->get('GLCode')) {
            $data['GLCode'] = $this->Auth_mobileUsers_Model->fetch_all_gl_codes();
        }

        if ($this->get('GLSegment')) {
            $segment_data = $this->segments_drop_get(1);
            foreach ($segment_data as $key => $row) {
                $segment_data[$key]['segmentID'] = (int)$row['segmentID'];
            }
            $data['segment'] = $segment_data;
        }

        if ($this->get('taxType')) {
            $tax_data = $this->all_tax_drop_get(1, 2);
            foreach ($tax_data as $key => $row) {
                $tax_data[$key]['taxMasterAutoID'] = (int)$row['taxMasterAutoID'];
                $tax_data[$key]['taxPercentage'] = (float)$row['taxPercentage'];
            }
            $data['taxType'] = $tax_data;
        }

        $final_output['success'] = true;
        $final_output['message'] = "";
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function warehouse_drop_get($is_return = 0)
    {
        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_warehousemaster');
        $data = $this->db->get()->result_array();

        if ($is_return = 1) {
            return $data;
        }

        foreach ($data as $key => $row) {
            $data[$key]['wareHouseAutoID'] = (int)$row['wareHouseAutoID'];
        }
        $rt_data['warehouses'] = $data;

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function all_tax_drop_get($is_return = 0, $taxType = 2)
    {
        $this->db->SELECT("taxMasterAutoID,taxDescription,taxShortCode,taxPercentage");
        $this->db->FROM('srp_erp_taxmaster');
        $this->db->where('taxType', $taxType);
        $this->db->where('isActive', 1);
        $this->db->where('isApplicableforTotal', 0);
        $this->db->where('companyID', $this->company_id);
        $data = $this->db->get()->result_array();

        if ($is_return = 1) {
            return $data;
        }
        $rt_data['taxType'] = $data;

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function search_item_get()
    {
        if($this->get('item'))
        {
            $item = $this->get('item');
            $search_string = "%" . $item . "%";
            $data = $this->db->query('SELECT
                                                seconeryItemCode,
                                                itemSystemCode,
                                                defaultUnitOfMeasureID,
                                                itemDescription,
                                                srp_erp_itemmaster.itemAutoID,
                                                itemledgercurrent.currentstock AS currentStock 
                            FROM
                                srp_erp_itemmaster
                                LEFT JOIN srp_erp_itemcategory ON srp_erp_itemmaster.mainCategoryID = srp_erp_itemcategory.itemCategoryID
                                	LEFT JOIN (SELECT
    IF (
        mainCategory = \'Inventory\',
        (
        TRIM(
    TRAILING "."
    FROM
        TRIM(
            TRAILING 0
            FROM
                (
                    ROUND(
                            SUM(transactionQTY / convertionRate),
                        4
                    )
                )
        )
)   
        ),
        " "
    ) AS currentstock,
	srp_erp_itemledger.itemAutoID
FROM
	`srp_erp_itemledger`
	LEFT JOIN srp_erp_itemmaster on srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
	GROUP BY
	srp_erp_itemledger.itemAutoID)itemledgercurrent on itemledgercurrent.itemAutoID = srp_erp_itemmaster.itemAutoID
                            WHERE
                                ( itemSystemCode LIKE "' . $search_string . '" OR 
                                itemDescription LIKE "' . $search_string . '") 
                                AND srp_erp_itemmaster.companyCode = "' . $this->company_info->company_code . '"
                                AND isActive = "1" AND deletedYN != 1')->result_array();

            foreach ($data as $key => $row) {
                $this->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion');
                $this->db->from('srp_erp_unitsconversion');
                $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
                $this->db->where('masterUnitID',$row['defaultUnitOfMeasureID']);
                $this->db->where('srp_erp_unitsconversion.companyID',$this->company_id);
                $uom = $this->db->get()->result_array();
                $data[$key]['UOM'] = $uom;
            }
            $rt_data = [
                'success' => true,
                'message' => 'Data Retrieved Successfully!',
                'data' => $data
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
    }

    public function add_supplier_invoice_tax_post()
    {
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $text_type = $this->input->post('text_type');
        $percentage = $this->input->post('percentage');

        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $text_type);
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID','left');
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('taxDetailAutoID');
        $this->db->where('taxMasterAutoID', $master['taxMasterAutoID']);
        $this->db->where('invoiceAutoID', trim($InvoiceAutoID));
        $this->db->where('companyID', $this->company_id);
        $taxexsist = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->row_array();

        if(!empty($taxexsist)){
            $rt_data = [
                'success' => false,
                'message' => 'Tax type already exist!',
                'data' => null
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,companyLocalCurrency,companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,transactionCurrencyID,companyLocalCurrencyID,companyReportingCurrencyID');
        $this->db->where('InvoiceAutoID', trim($InvoiceAutoID));
        $inv_master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $data['invoiceAutoID'] = trim($InvoiceAutoID);
        $data['taxMasterAutoID'] = $master['taxMasterAutoID'];
        $data['taxDescription'] = $master['taxDescription'];
        $data['taxShortCode'] = $master['taxShortCode'];
        $data['supplierAutoID'] = $master['supplierAutoID'];
        $data['supplierSystemCode'] = $master['supplierSystemCode'];
        $data['supplierName'] = $master['supplierName'];
        $data['supplierCurrencyID'] = $master['supplierCurrencyID'];
        $data['supplierCurrency'] = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID'] = $master['GLAutoID'];
        $data['systemGLCode'] = $master['systemAccountCode'];
        $data['GLCode'] = $master['GLSecondaryCode'];
        $data['GLDescription'] = $master['GLDescription'];
        $data['GLType'] = $master['subCategory'];

        if((!empty($this->input->post('totalAfterDiscount'))) && (!empty($this->input->post('tax_amount'))))
        {
            $data['taxPercentage'] = ((($this->input->post('tax_amount')) / $this->input->post('totalAfterDiscount')) * 100);
            $data['transactionAmount'] = $data['taxPercentage'] * $this->input->post('afterdiscounttot');
        }else
        {
            $data['taxPercentage'] = 0;
            $data['transactionAmount'] = 0;
        }

        $data['companyLocalAmount'] = 0;
        $data['companyReportingAmount'] = 0;
        $data['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency'] = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

        $supplierCurrency = $this->currency_conversion($data['transactionCurrency'], $data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['decimalPlaces'];

        $data['companyCode'] = $this->company_info->company_code;
        $data['companyID'] = $this->company_id;
        $data['createdUserGroup'] = $this->company_info->usergroupID;
        $data['createdPCID'] = $this->company_info->current_pc;
        $data['createdUserID'] = $this->user_id;
        $data['createdUserName'] = $this->user->name2;
        $data['createdDateTime'] = $this->current_time();

        $this->db->insert('srp_erp_paysupplierinvoicetaxdetails', $data);
        $last_id = $this->db->insert_id();
        $master_data = $this->fetch_supplier_invoice_tax_get($InvoiceAutoID, $last_id);

        $rt_data = [
            'success' => true,
            'message' => 'Tax Added Successfully.',
            'data' => $master_data
        ];

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function fetch_supplier_invoice_tax_get($id = null, $taxDetailAutoID = null)
    {
        $master_id = (!empty($id)) ? $id : $this->get('masterID');

        $this->db->select('taxDetailAutoID,invoiceAutoID, taxMasterAutoID, taxDescription, taxShortCode, taxPercentage, transactionAmount as tax_amount');
        $this->db->where('invoiceAutoID', $master_id);
        if(!empty($taxDetailAutoID)) {
            $this->db->where('taxDetailAutoID', $taxDetailAutoID);
        }
        $data = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->row_array();

        $data['taxDetailAutoID'] = (int)$data['taxDetailAutoID'];
        $data['taxMasterAutoID'] = (int)$data['taxMasterAutoID'];
        $data['invoiceAutoID'] = (int)$data['invoiceAutoID'];
        $data['taxPercentage'] = (float)$data['taxPercentage'];
        $data['tax_amount'] = (float)$data['tax_amount'];

        $rt_data = [
            'success' => true,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];

        if (!empty($id)) {
            return $data;
        }

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    public function delete_supplier_invoice_tax_delete($taxDetailAutoID)
    {
        $this->db->trans_start();
        $this->db->where('taxDetailAutoID', $taxDetailAutoID)->delete('srp_erp_paysupplierinvoicetaxdetails');
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $rt_data = ['success' => true, 'message' => 'Tax detail deleted successfully', 'data' => null];
        } else {
            $rt_data = ['success' => false, 'message' => 'Error in Tax detail delete process', 'data' => null];
        }
        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function add_supplier_invoice_glDetails_post()
    {
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $invoiceDetailID = $this->input->post('invoiceDetailID');
        $GLAutoID = $this->input->post('GLAutoID');
        $segmentID = $this->input->post('segmentID');
        $amount = $this->input->post('amount');
        $discountPercentage = $this->input->post('discountPercentage');
        $netAmount = $this->input->post('netAmount');
        $description = $this->input->post('description');

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $segmentCode = $this->db->query("SELECT	segmentCode FROM srp_erp_segment WHERE segmentID = {$segmentID}")->row('segmentCode');

        $glDetails = $this->db->query("SELECT systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts WHERE GLAutoID = {$GLAutoID}")->row_array();

        $data['InvoiceAutoID'] = trim($InvoiceAutoID);
        $data['GLAutoID'] = $GLAutoID;
        $data['systemGLCode'] = $glDetails['systemAccountCode'];
        $data['GLCode'] = $glDetails['GLSecondaryCode'];
        $data['GLDescription'] = $glDetails['GLDescription'];
        $data['GLType'] = $glDetails['subCategory'];
        $data['segmentID'] = $segmentID;
        $data['segmentCode'] = $segmentCode;
        $data['description'] = $description;
        $data['discountPercentage'] = trim($discountPercentage);
        $data['discountAmount'] = trim(($amount * $discountPercentage)/100);
        $data['transactionAmount'] = round($amount-$data['discountAmount'], $master['transactionCurrencyDecimalPlaces']);
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];
        $companyLocalAmount =0;
        if($master['companyLocalExchangeRate'])
        {
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        }
        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $companyReportingAmount = 0;
        if($master['companyReportingExchangeRate'])
        {
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        }
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $supplierAmount = 0;
        if($master['supplierCurrencyExchangeRate']){
            $supplierAmount = $data['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
        }
        $data['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
        $data['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];

        if(!empty($invoiceDetailID)) {
            $data['modifiedPCID'] = $this->company_info->current_pc;
            $data['modifiedUserID'] = $this->user_id;
            $data['modifiedUserName'] = $this->user->name2;
            $data['modifiedDateTime'] = $this->current_time();

            $this->db->where('InvoiceDetailAutoID', trim($invoiceDetailID));
            $this->db->update('srp_erp_paysupplierinvoicedetail', $data);
            $master_data = $this->fetch_supplier_invoice_details_get($invoiceDetailID);

            $rt_data = [
                'success' => true,
                'message' => 'GL item Updated Successfully.',
                'data' => $master_data
            ];
        } else {
            $data['companyCode'] = $this->company_info->company_code;
            $data['companyID'] = $this->company_id;
            $data['createdUserGroup'] = $this->company_info->usergroupID;
            $data['createdPCID'] = $this->company_info->current_pc;
            $data['createdUserID'] = $this->user_id;
            $data['createdUserName'] = $this->user->name2;
            $data['createdDateTime'] = $this->current_time();

            $this->db->insert('srp_erp_paysupplierinvoicedetail', $data);
            $last_id = $this->db->insert_id();
            $master_data = $this->fetch_supplier_invoice_details_get($last_id);

            $rt_data = [
                'success' => true,
                'message' => 'GL item Created Successfully.',
                'data' => $master_data
            ];
        }
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function fetch_supplier_invoice_details_get($id=null)
    {
        $master_id = (!empty($id)) ? $id : $this->get('masterID');

        $data = $this->db->select('InvoiceDetailAutoID, InvoiceAutoID, GLAutoID, transactionAmount')
            ->from('srp_erp_paysupplierinvoicedetail')
            ->where('srp_erp_paysupplierinvoicedetail.companyID', $this->company_id)
            ->where('InvoiceDetailAutoID', $master_id)->get()->row_array();

        $data['InvoiceDetailAutoID'] = (int)$data['InvoiceDetailAutoID'];
        $data['InvoiceAutoID'] = (int)$data['InvoiceAutoID'];
        $data['GLAutoID'] = (int)$data['GLAutoID'];
        $data['transactionAmount'] = (float)$data['transactionAmount'];

        $rt_data = [
            'success' => true,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];

        if (!empty($id)) {
            return $data;
        }
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function add_supplier_invoice_items_post()
    {
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $invoiceDetailID = $this->input->post('invoiceDetailID');
        $itemAutoID = $this->input->post('itemAutoID');
        $warehouseID = $this->input->post('warehouseID');
        $UOMID = $this->input->post('UOMID');
        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $discountPercentage = $this->input->post('discountPercentage');
        $remark = $this->input->post('remark');

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,companyReportingCurrencyID,supplierCurrencyID,segmentCode,segmentID');
        $this->db->where('InvoiceAutoID', trim($InvoiceAutoID));
        $master_recode = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->company_id);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();

        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_chartofaccounts');
        $this->db->WHERE('GLAutoID', $ACA_ID['GLAutoID']);
        $this->db->where('companyID', $this->company_id);
        $ACA = $this->db->get()->row_array();

        if (!trim($invoiceDetailID)) {
            $this->db->select('itemDescription,itemSystemCode');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $warehouseID);
            $order_detail = $this->db->get()->row_array();

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm = $this->db->get()->row_array();
            if (!empty($order_detail) && $serviceitm['mainCategory'] == "Inventory") {
                $rt_data = [
                    'success' => false,
                    'message' => 'Supplier Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists. ',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        } else {
            $this->db->select('itemDescription,itemSystemCode');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('InvoiceDetailAutoID != ' , $invoiceDetailID);
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $warehouseID);
            $order_detail = $this->db->get()->row_array();
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm = $this->db->get()->row_array();
            if (!empty($order_detail) && $serviceitm['mainCategory'] == "Inventory") {
                $rt_data = [
                    'success' => false,
                    'message' => 'Supplier Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }

        $item_arr = $this->db->query("SELECT * FROM srp_erp_itemmaster WHERE companyID = {$this->company_id} AND itemAutoID = {$itemAutoID}")->row_array();
        $unitCode = $this->db->query("SELECT UnitShortCode FROM	srp_erp_unit_of_measure WHERE UnitID = {$UOMID}")->row('UnitShortCode');

        $data['InvoiceAutoID'] = trim($InvoiceAutoID);
        $data['itemAutoID'] = $itemAutoID;
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($unitCode);
        $data['unitOfMeasureID'] = $UOMID;
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOMID'] = $this->conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = $qty;
        $data['unittransactionAmount'] = $amount;
        $data['segmentID'] = $master_recode['segmentID'];
        $data['segmentCode'] = $master_recode['segmentCode'];
        $data['discountPercentage'] = $discountPercentage;
        $data['discountAmount'] = 0;
        if($discountPercentage > 0) {
            $data['discountAmount'] = ($qty * $amount) * ($discountPercentage /100);
        }
        $data['transactionAmount'] = (($data['unittransactionAmount'] * $data['requestedQty']) - $data['discountAmount']);
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = (($data['transactionAmount']) / $master_recode['companyLocalExchangeRate']);
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = (($data['transactionAmount']) / $master_recode['companyReportingExchangeRate']);
        $data['supplierCurrencyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
        $data['supplierAmount'] = (($data['transactionAmount']) / $master_recode['supplierCurrencyExchangeRate']);
        $data['description'] = $remark;
        $data['type'] = 'Item';
        $data['wareHouseAutoID'] = $warehouseID;

        $warehouseDet = $this->db->query("SELECT wareHouseCode, wareHouseDescription, wareHouseLocation FROM srp_erp_warehousemaster WHERE wareHouseAutoID = {$warehouseID}")->row_array();
        $data['wareHouseCode'] = trim($warehouseDet['wareHouseCode'] ?? '');
        $data['wareHouseLocation'] = trim($warehouseDet['wareHouseLocation'] ?? '');
        $data['wareHouseDescription'] = trim($warehouseDet['wareHouseDescription'] ?? '');

        if ($item_arr['mainCategory'] == 'Inventory') {
            $data['GLAutoID'] = $item_arr['assteGLAutoID'];
            $data['systemGLCode'] = $item_arr['assteSystemGLCode'];
            $data['GLCode'] = $item_arr['assteGLCode'];
            $data['GLDescription'] = $item_arr['assteDescription'];
            $data['GLType'] = $item_arr['assteType'];
        } else if ($item_arr['mainCategory'] == 'Fixed Assets') {
            $data['GLAutoID'] = $ACA_ID['GLAutoID'];
            $data['systemGLCode'] = $ACA['systemAccountCode'];
            $data['GLCode'] = $ACA['GLSecondaryCode'];
            $data['GLDescription'] = $ACA['GLDescription'];
            $data['GLType'] = $ACA['subCategory'];
        } else {
            $data['GLAutoID'] = $item_arr['costGLAutoID'];
            $data['systemGLCode'] = $item_arr['costSystemGLCode'];
            $data['GLCode'] = $item_arr['costGLCode'];
            $data['GLDescription'] = $item_arr['costDescription'];
            $data['GLType'] = $item_arr['costType'];
        }

        $comp_arr = array(
            'companyID' => $this->company_id,
            'companyCode' => $this->company_info->company_code,
            'createdUserGroup' => $this->company_info->usergroupID,
            'createdPCID' => $this->company_info->current_pc,
            'createdUserID' => $this->user_id,
            'createdUserName' => $this->user->name2,
            'createdDateTime' => $this->current_time()
        );

        if(!empty($invoiceDetailID)) {
            $data['modifiedPCID'] = $this->company_info->current_pc;
            $data['modifiedUserID'] = $this->user_id;
            $data['modifiedUserName'] = $this->user->name2;
            $data['modifiedDateTime'] = $this->current_time();

            $this->db->where('InvoiceDetailAutoID', trim($invoiceDetailID));
            $this->db->update('srp_erp_paysupplierinvoicedetail', $data);
            $master_data = $this->fetch_supplier_invoice_details_get($invoiceDetailID);

            /** update sub item master */
            $subData['uom'] = $data['unitOfMeasure'];
            $subData['uomID'] = $data['unitOfMeasureID'];
            $subData['InvoiceDetailAutoID'] = $invoiceDetailID;

            $this->load->model('Payable_modal');
            $this->Payable_modal->edit_sub_itemMaster_tmpTbl($qty, $item_arr['itemAutoID'], $data['InvoiceAutoID'], $invoiceDetailID, 'BSI', $data['itemSystemCode'], $subData, $comp_arr);

            $rt_data = [
                'success' => true,
                'message' => 'GL item Updated Successfully.',
                'data' => $master_data
            ];

        } else {
            $data['companyCode'] = $this->company_info->company_code;
            $data['companyID'] = $this->company_id;
            $data['createdUserGroup'] = $this->company_info->usergroupID;
            $data['createdPCID'] = $this->company_info->current_pc;
            $data['createdUserID'] = $this->user_id;
            $data['createdUserName'] = $this->user->name2;
            $data['createdDateTime'] = $this->current_time();

            $this->db->insert('srp_erp_paysupplierinvoicedetail', $data);
            $last_id = $this->db->insert_id();
            $master_data = $this->fetch_supplier_invoice_details_get($last_id);

            /** add sub item config*/
            if ($item_arr['isSubitemExist'] == 1) {
                $subData['uom'] = $data['unitOfMeasure'];
                $subData['uomID'] = $data['unitOfMeasureID'];
                $subData['pv_detailID'] = $last_id;

                $this->load->model('Payable_modal');
                $this->Payable_modal->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $InvoiceAutoID, $last_id, 'BSI', $item_arr['itemSystemCode'], $subData, $warehouseID);
            }

            /** End add sub item config*/
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', $this->company_id);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $data['wareHouseAutoID'],
                    'wareHouseLocation' => $data['wareHouseLocation'],
                    'wareHouseDescription' => $data['wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'barCodeNo' => $item_arr['barcode'],
                    'salesPrice' => $item_arr['companyLocalSellingPrice'],
                    'ActiveYN' => $item_arr['isActive'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->company_id,
                    'companyCode' => $this->company_info->company_code
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }

            $rt_data = [
                'success' => true,
                'message' => 'GL item Created Successfully.',
                'data' => $master_data
            ];
        }

        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function delete_supplier_invoice_detail_delete($invoiceDetailAutoID)
    {
        $this->db->trans_start();
        $this->db->where('InvoiceDetailAutoID', $invoiceDetailAutoID)->delete('srp_erp_paysupplierinvoicedetail');
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $rt_data = ['success' => true, 'message' => 'Invoice detail deleted successfully', 'data' => null];
        } else {
            $rt_data = ['success' => false, 'message' => 'Error in Invoice detail delete process', 'data' => null];
        }
        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function add_general_discount_bsi_post()
    {
        $discountPercentage=$this->input->post('discountPercentage');
        $discountableAmount=$this->input->post('discountableAmount');
        $InvoiceAutoID=$this->input->post('InvoiceAutoID');

        $discountAmount=($discountPercentage/100) * $discountableAmount;
        if($discountAmount>0){
            if($discountAmount<$discountableAmount){
                $data['generalDiscountAmount'] = (float)$discountAmount;
                $data['generalDiscountPercentage'] = (float)$discountPercentage;

                $this->db->where('InvoiceAutoID', trim($InvoiceAutoID));
                $result=$this->db->update('srp_erp_paysupplierinvoicemaster', $data);

                $data['InvoiceAutoID'] = (int)$InvoiceAutoID;
                if($result){
                    $rt_data = ['success' => true, 'message' => 'Discount Amount successfully added', 'data'=> $data];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }
            }else{
                $rt_data = ['success' => false, 'message' => 'Discount amount cannot be greater than Discount applicable amount', 'data' => null];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }else{
            $rt_data = ['success' => false, 'message' => 'Discount should be greater than zero', 'data' => null];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
    }

    function delete_general_discount_bsi_delete($InvoiceAutoID)
    {
        if($InvoiceAutoID){
                $data['generalDiscountAmount'] = 0;
                $data['generalDiscountPercentage'] = 0;

                $this->db->where('InvoiceAutoID', trim($InvoiceAutoID));
                $result=$this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                if($result){
                    $rt_data = ['success' => true,
                        'message' => 'Discount Amount Deleted Successfully',
                        'data'=> null
                    ];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }
        }else{
            $rt_data = [
                'success' => false,
                'message' => 'InvoiceAutoID is required',
                'data' =>  null
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
    }

    function confirm_supplier_invoice_post()
    {
        $master_id = trim($this->post('masterID'));
        $master_data = Api_spur::document_status('BSI', $master_id, ['more_column' => 1]);
        if ($master_data['error'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => $master_data['message'],
                'data' => []
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }

        $this->db->select('InvoiceAutoID');
        $this->db->where('InvoiceAutoID', trim($master_id));
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            $rt_data = [
                'success' => false,
                'message' => 'There are no records to confirm this document!',
                'data' => []
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        } else{
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($master_id));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $rt_data = [
                    'success' => false,
                    'message' => 'Document already confirmed!',
                    'data' => []
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            } else {
                $this->load->library('sequence_mobile');
                $companyData = array(
                    'companyID' => $this->company_id,
                    'companyCode' => $this->company_info->company_code,
                    'createdUserGroup' => $this->company_info->usergroupID,
                    'createdPCID' => $this->company_info->current_pc,
                    'createdUserID' => $this->user_id,
                    'createdUserName' => $this->user->name2,
                    'createdDate' => $this->current_time('d'),
                    'createdDateTime' => $this->current_time()
                );
                $this->db->trans_start();
                $system_id = trim($this->post('masterID'));
                $this->db->select('bookingInvCode,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('Sequence_mobile');
                $lenth=strlen($master_dt['bookingInvCode']);

                $emplocationid = $this->user->location;
                $locationwisecodegenerate = $this->Mobile_leaveApp_Model->getPolicyValues('LDG', 'All', $this->company_id);
                if($lenth == 1) {
                    if($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $this->user_id);
                        $this->db->where('Erp_companyID', $this->company_id);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location =='')) {
                            $rt_data = [
                                'success' => false,
                                'message' => 'Location is not assigned for current employee!',
                                'data' => []
                            ];
                            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                        }else {
                            if($emplocationid!='') {
                                $location = $this->sequence_mobile->sequence_generator_location('BSI',$master_dt['companyFinanceYearID'], $emplocationid,$master_dt['invYear'],$master_dt['invMonth'], '', $companyData);
                            }else {
                                $rt_data = [
                                    'success' => false,
                                    'message' => 'Location is not assigned for current employee!',
                                    'data' => []
                                ];
                                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                            }
                        }
                    }else {
                        $location = $this->sequence_mobile->sequence_generator_fin('BSI',$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth'], '', $companyData);
                    }
                    $invcod = array(
                        'bookingInvCode' => $location,
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $invcod);
                }

                $this->load->library('Approvals');
                $this->db->select('InvoiceAutoID, bookingInvCode,transactionCurrency,transactionExchangeRate,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth,bookingDate');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_data = $this->db->get()->row_array();
;
                $autoApproval= $this->get_document_auto_approval('BSI');

                if($autoApproval==0){
//                    $approvals_status = $this->approvals_mobile->auto_approve($master_data['InvoiceAutoID'], 'srp_erp_paysupplierinvoicemaster','InvoiceAutoID', 'BSI',$master_data['bookingInvCode'],$master_data['bookingDate'], $companyData);
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals_mobile->CreateApproval('BSI', $master_data['InvoiceAutoID'], $master_data['bookingInvCode'], 'Supplier Invoice', 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID',0,$master_data['bookingDate'], $companyData);
                }else{
                    $rt_data = [
                        'success' => false,
                        'message' => 'Approval levels are not set for this document!',
                        'data' => []
                    ];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }

                if ($approvals_status == 1) {
                    $transa_total_amount = 0;
                    $loca_total_amount = 0;
                    $rpt_total_amount = 0;
                    $supplier_total_amount = 0;
                    $t_arr = array();
                    $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(supplierAmount) as supplierAmount');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

                    $transa_total_amount += $data_arr['transactionAmount'];

                    $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,taxPercentage');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $tax_arr = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
                    for ($x = 0; $x < count($tax_arr); $x++) {
                        $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $transa_total_amount);
                        $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                        $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                        $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                        $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                        $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                    }
                    /*updating transaction amount using the query used in the master data table */

                    $r1 = "SELECT
srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
	`srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
	(srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0) as discountAmnt,
	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0)))
		) + IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0))
	) AS total_value
FROM
	`srp_erp_paysupplierinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		InvoiceAutoID
	FROM
		srp_erp_paysupplierinvoicedetail
	GROUP BY
		InvoiceAutoID
) det ON (
	`det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_paysupplierinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
)
WHERE
	`companyID` = {$this->company_id}
	AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $system_id ";
                    $totalValue = $this->db->query($r1)->row_array();
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->current_time('d'),
                        'confirmedByEmpID' => $this->user_id,
                        'confirmedByName' => $this->user->name2,
                        'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'], $totalValue['companyLocalCurrencyDecimalPlaces'])),
                        'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'], $totalValue['companyReportingCurrencyDecimalPlaces'])),
                        'supplierCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['supplierCurrencyExchangeRate'], $totalValue['supplierCurrencyDecimalPlaces'])),
                        'transactionAmount' => (round($totalValue['total_value'], $totalValue['transactionCurrencyDecimalPlaces'])),
                        'generalDiscountAmount' => ($totalValue['discountAmnt']),
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                    if (!empty($t_arr)) {
                        $this->db->update_batch('srp_erp_paysupplierinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                    }
                }else if($approvals_status==3){
                    $rt_data = [
                        'success' => false,
                        'message' => 'There are no users exist to perform approval for this document!',
                        'data' => []
                    ];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }else{
                    $rt_data = [
                        'success' => false,
                        'message' => 'Confirmation failed!',
                        'data' => []
                    ];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $rt_data = [
                        'success' => false,
                        'message' => 'Supplier Invoice Confirmed failed ' . $this->db->_error_message(),
                        'data' => []
                    ];
                    return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                } else {
                    $autoApproval= $this->get_document_auto_approval('BSI');

                    if($autoApproval==0) {
//                        $result = $this->Mobile_leaveApp_Model->save_supplier_invoice_approval(0, $master_data['InvoiceAutoID'], 1, 'Auto Approved');
//
//                        if($result){
//                            $rt_data = [
//                                'success' => true,
//                                'message' => 'Supplier Invoice Confirmed Successfully!'
//                            ];
//                            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
//                        }
                    }else{
                        $rt_data = [
                            'success' => true,
                            'message' => 'Supplier Invoice Confirmed Successfully!',
                            'data' => []
                        ];
                        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
                    }
                }
            }
        }
    }

    function get_document_auto_approval($documentCode)
    {
        $this->db->SELECT("levelNo");
        $this->db->FROM('srp_erp_approvalusers');
        $this->db->where('companyID', $this->company_id);
        $this->db->where('documentID', $documentCode);
        //$CI->db->where('employeeID', current_userID());
        $data = $this->db->get()->row_array();
        if(!empty($data) && $data['levelNo']==0){
            return 0;
        }elseif($data['levelNo']>0){
            return 1;
        }else{
            return 2;
        }
    }

    function  delete_supplier_invoice_delete($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $this->db->where('InvoiceAutoID', trim($id));
        $details = $this->db->get()->row_array();

        if ($details) {
            $rt_data = [
                'success' => false,
                'message' => 'Please delete all detail records before delete this document!',
                'data' => null
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        } else {
            $this->db->select('bookingInvCode');
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $this->db->where('InvoiceAutoID', trim($id));
            $master = $this->db->get()->row_array();

            if($master['bookingInvCode']=="0"){
                $this->db->where('InvoiceAutoID', trim($id));
                $this->db->delete('srp_erp_paysupplierinvoicemaster');
                $rt_data = [
                    'success' => true,
                    'message' => 'Invoice Deleted Successfully!',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }else{
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => $this->user_id,
                    'deletedDate' => current_date(),
                );
                $this->db->where('InvoiceAutoID', trim($id));
                $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                $rt_data = [
                    'success' => true,
                    'message' => 'Invoice Deleted Successfully!',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }
    }

    function  reopen_supplier_invoice_get()
    {
        $id = $this->get('InvoiceAutoID');
        $data = array(
            'isDeleted' => 0,
            'deletedEmpID' => null,
            'deletedDate' => null
        );
        $this->db->where('InvoiceAutoID', trim($id));
        $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

        $rt_data = [
            'success' => true,
            'message' => 'Supplier Invoice Re Opened Successfully!',
            'data' => null
        ];
        return $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function  refer_back_supplier_invoice_get()
    {
        $InvoiceAutoID = $this->get('InvoiceAutoID');
        $this->db->select('approvedYN,bookingInvCode');
        $this->db->where('InvoiceAutoID', trim($InvoiceAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $approved_inventory_payable_supplierinvoice = $this->db->get()->row_array();
        if (!empty($approved_inventory_payable_supplierinvoice)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_payable_supplierinvoice['bookingInvCode']));
        } else {
            $this->load->library('Approvals_mobile');
            $status = $this->approvals_mobile->approve_delete($InvoiceAutoID, 'BSI');
            if ($status == 1) {
                $rt_data = [
                    'success' => true,
                    'message' => 'Referred Back Successfully!',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            } else {
                $rt_data = [
                    'success' => false,
                    'message' => 'Error in refer back!',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }
    }

    function  getAllApprovals_get()
    {
        $approvalsDoc = $this->Auth_mobileUsers_Model->getAllApprovals();
        if (sizeof($approvalsDoc) > 0) {
            $final_output['success'] = true;
            $final_output['message'] = 'Approvals retrieved successfully';
            $final_output['data'] = $approvalsDoc;
            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = true;
            $final_output['message'] = 'No approvals were found';
            $final_output['data'] = [];
            $this->response($final_output, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    function  documentPageView_get()
    {
        $details = $this->Auth_mobileUsers_Model->fetch_documentPageView_details();
        if (sizeof($details) > 0) {
            $final_output['success'] = true;
            $final_output['message'] = 'Approvals retrieved successfully';
            $final_output['data'] = $details;
            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = false;
            $final_output['message'] = 'Document Not found';
            $final_output['data'] = null;
            $this->response($final_output, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    function generate_paymentVoucher_post()
    {
        $details = $this->Auth_mobileUsers_Model->generate_paymentVoucher_bsi();
        if ($details[0] == 's') {
            $final_output['success'] = true;
            $final_output['message'] = $details[1];
            $final_output['data'] = [];
            $this->response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = false;
            $final_output['message'] = $details[1];
            $final_output['data'] = null;
            $this->response($final_output, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    function payment_generate_bsi_drops_get()
    {
        $bank_arr = $this->Auth_mobileUsers_Model->supp_bank_drop();
        $final_output['success'] = true;
        $final_output['message'] = "Data Retrieved Successfully!";
        $final_output['data'] = $bank_arr;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function  delete_payment_voucher_delete($payVoucherAutoId)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $details = $this->db->get()->row_array();

        if ($details) {
            $rt_data = [
                'success' => false,
                'message' => 'Please delete all detail records before delete this document!',
                'data' => null
            ];
            return $this->set_response($rt_data, REST_Controller::HTTP_OK);
        } else {
            $this->db->select('PVcode');
            $this->db->from('srp_erp_paymentvouchermaster');
            $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
            $master = $this->db->get()->row_array();

            if($master['PVcode'] == "0"){
                $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
                $this->db->delete('srp_erp_paymentvouchermaster');
                $rt_data = [
                    'success' => true,
                    'message' => 'Payment Voucher Deleted Successfully!',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }else{
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
                $this->db->update('srp_erp_paymentvouchermaster', $data);
                $rt_data = [
                    'success' => true,
                    'message' => 'Payment Voucher Deleted Successfully!',
                    'data' => null
                ];
                return $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }
    }

    function create_paymentVoucher_header_post()
    {
        $date_format_policy = date_format_policy();
        $Pdte = $this->input->post('PVdate');
        $PVdate = input_format_date($Pdte, $date_format_policy);
        $PVchqDte = $this->input->post('PVchequeDate');
        $PVchequeDate = input_format_date($PVchqDte, $date_format_policy);
        $bank_detail = fetch_gl_account_desc($this->input->post('PVbankCode'));

        if ($PVchequeDate < $PVdate && $bank_detail['isCash'] == 0 && $this->input->post('paymentType') == 1) {
            $final_output['success'] = false;
            $final_output['message'] = "Cheque Date Cannot be less than Payment Voucher Date!";
            $final_output['data'] = null;
            $this->set_response($final_output, REST_Controller::HTTP_OK);

        } else {
            $data = $this->Auth_mobileUsers_Model->save_paymentVoucher_header();
            if($data[1] == 'e'){
                $final_output['success'] = false;
                $final_output['message'] = "Payment Voucher create failed!";
                $final_output['data'] = $data;
                $this->set_response($final_output, REST_Controller::HTTP_OK);
            } else {
                $final_output['success'] = true;
                $final_output['message'] = "Payment Voucher created Successfully!";
                $final_output['data'] = $data[2];
                $this->set_response($final_output, REST_Controller::HTTP_OK);
            }
        }
    }

    function invoice_list_pv_get()
    {
        $date_format_policy = date_format_policy();
        $Pdte = $this->input->get('PVdate');
        $PVdate = input_format_date($Pdte, $date_format_policy);
        $supplierID = $this->input->get('supplierID');
        $currencyID = $this->input->get('currencyID');

        $this->load->model('Payment_voucher_model');
        $data = $this->Payment_voucher_model->fetch_supplier_inv($supplierID, $currencyID, $PVdate);

        foreach ($data as $key=>$inv) {
            $data[$key]['InvoiceAutoID'] = (int)$inv['InvoiceAutoID'];
            $data[$key]['paymentTotalAmount'] = (double)$inv['paymentTotalAmount'];
            $data[$key]['DebitNoteTotalAmount'] = (double)$inv['DebitNoteTotalAmount'];
            $data[$key]['advanceMatchedTotal'] = (double)$inv['advanceMatchedTotal'];
            $data[$key]['transactionAmount'] = (double)$inv['transactionAmount'];
        }
        $final_output['success'] = true;
        $final_output['message'] = "Data Retrieved Successfully!";
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function add_payment_voucher_item_post()
    {
        $data = $this->Auth_mobileUsers_Model->add_payment_voucher_item();
        if($data[0] == 's') {
            $final_output['success'] = true;
            $final_output['message'] = $data[1];
            $final_output['data'] = $data[2];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = false;
            $final_output['message'] = $data[1];
            $final_output['data'] = null;
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function delete_payment_voucher_item_delete($payVoucherDetailAutoID)
    {
        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->where('srp_erp_paymentvoucherdetail.payVoucherDetailAutoID', trim($payVoucherDetailAutoID));
        $detail_arr = $this->db->get()->row_array();

        /** delete sub item in PV*/
        if ($detail_arr['isSubitemExist'] == 1) {
            $this->db->where('receivedDocumentID', 'PV');
            $this->db->where('receivedDocumentAutoID', $detail_arr['payVoucherAutoId']);
            $this->db->where('receivedDocumentDetailID', $detail_arr['payVoucherDetailAutoID']);
            $this->db->delete('srp_erp_itemmaster_subtemp');
        }
        /**end  delete sub item in PV*/

        if ($detail_arr['type'] == 'Invoice') {
            $company_id = current_companyID();
            $match_id = $detail_arr['InvoiceAutoID'];
            $number = $detail_arr['transactionAmount'];
            $status = 0;
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET paymentTotalAmount = (paymentTotalAmount -{$number}), paymentInvoiceYN = {$status} WHERE InvoiceAutoID = $match_id and companyID= $company_id");
        }
        $this->db->where('payVoucherDetailAutoID', trim($payVoucherDetailAutoID));
        $results = $this->db->delete('srp_erp_paymentvoucherdetail');

        if ($results) {
            $final_output['success'] = true;
            $final_output['message'] = 'Payment Voucher Detail Deleted Successfully!';
            $final_output['data'] = [];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function add_payment_voucher_tax_post()
    {
        $data = $this->Auth_mobileUsers_Model->add_payment_voucher_tax();
        if($data['status'] == '0') {
            $final_output['success'] = false;
            $final_output['message'] = $data['data'];
            $final_output['data'] = $data['view'];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = true;
            $final_output['message'] = $data['data'];
            $final_output['data'] = $data['view'];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function delete_payment_voucher_tax_delete($taxDetailAutoID)
    {
        $this->db->delete('srp_erp_paymentvouchertaxdetails', array('taxDetailAutoID' => trim($taxDetailAutoID)));
        $final_output['success'] = true;
        $final_output['message'] = 'Payment Voucher Tax Detail Deleted Successfully!';
        $final_output['data'] = [];
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function payment_view_document_view_get()
    {
        $data = $this->Auth_mobileUsers_Model->payment_view_document_view();
        $final_output['success'] = true;
        $final_output['message'] = 'Data Retreived Successfully!';
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function payment_view_document_list_view_get()
    {
        $data = $this->Auth_mobileUsers_Model->payment_view_document_list_view();
        $rt_data = [
            'success' => true,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function confirm_payment_voucher_post()
    {
        $data = $this->Auth_mobileUsers_Model->confirm_payment_voucher();
        if($data['error'] == 0) {
            $rt_data = [
                'success' => true,
                'message' => $data['message'],
                'data' => []
            ];
        } else {
            $rt_data = [
                'success' => false,
                'message' => $data['message'],
                'data' => []
            ];
        }

        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function refer_back_payment_voucher_post()
    {
        $master_id = $this->input->post('master_id');

        $this->db->select('approvedYN,PVcode,isSytemGenerated,confirmedYN');
        $this->db->where('payVoucherAutoId', trim($master_id));
        $this->db->from('srp_erp_paymentvouchermaster');
        $doc_status = $this->db->get()->row_array();
        if ($doc_status['approvedYN'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => 'The document already approved - ' . $doc_status['PVcode'],
                'data' => []
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
        else if ($doc_status['confirmedYN'] == 0) {
            $rt_data = [
                'success' => false,
                'message' => 'This document not confirmed yet.Please refresh the page and try again.',
                'data' => []
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
        else if ($doc_status['isSytemGenerated'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => 'This is System Generated Document,You Cannot Refer Back this document.',
                'data' => []
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
        else {
            $this->load->library('Approvals_mobile');
            $status = $this->approvals_mobile->approve_delete($master_id, 'PV');
            if ($status == 1) {
                $rt_data = [
                    'success' => true,
                    'message' => 'Document Referred Back Successfully!',
                    'data' => []
                ];
                $this->set_response($rt_data, REST_Controller::HTTP_OK);
            } else {
                $rt_data = [
                    'success' => false,
                    'message' => 'Error in refer back!' . $status,
                    'data' => []
                ];
                $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }
    }

    /** Receipt Voucher Codes*/
    function load_receipt_vouchers_get()
    {
        $data = $this->Auth_mobileUsers_Model->load_receipt_vouchers();
        $rt_data = [
            'success' => true,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function load_RV_header_dropdown_get()
    {
        $data = [];
        if ($this->get('segment')) {
            $data['user_segment'] = (int)$this->user->segmentID;
            //
            $segment_data = $this->segments_drop_get(1);
            $segment_data_array = array();
            foreach ($segment_data as $item) {
                $segment_data_item = array();
                $segment_data_item['segmentID'] = (int)$item['segmentID'];
                $segment_data_item['segmentCode'] = $item['segmentCode'];
                $segment_data_item['description'] = $item['description'];
                array_push($segment_data_array, $segment_data_item);
            }
            $data['segment'] = $segment_data_array;
        }

        if ($this->get('currency')) {
            $data['user_currency'] = (int)$this->user->payCurrencyID;
            // $data['currency'] = $this->currency_drop_get(1);
            $currency_data = $this->currency_drop_get(1);
            $currency_data_array = array();
            foreach ($currency_data as $item) {
                $currency_data_item = array();
                $currency_data_item['currencyID'] = (int)$item['currencyID'];
                $currency_data_item['currencyCode'] = $item['currencyCode'];
                $currency_data_item['currencyName'] = $item['currencyName'];
                $currency_data_item['decimalPlaces'] = (int)$item['decimalPlaces'];
                array_push($currency_data_array, $currency_data_item);
            }
            $data['currency'] = $currency_data_array;
        }

        if ($this->get('bank')) {
            $bank_acc = company_bank_account_drop(1);

            if($bank_acc) {
                foreach ($bank_acc as $key => $bank)
                {
                    $bank_acc[$key]['GLAutoID'] = (int)$bank['GLAutoID'];
                    $bank_acc[$key]['isCash'] = (int)$bank['isCash'];
                }
            }
            $data['bank'] = $bank_acc;
        }

        if ($this->get('customer')) {
            $supplier_data = $this->customer_drop_get();
            $expense_claim_data_array = array();
            foreach ($supplier_data as $item) {
                $expense_claim_data_item = array();
                $expense_claim_data_item['id'] = (int)$item['customerAutoID'];
                $expense_claim_data_item['cus_code'] = $item['customerSystemCode'];
                $expense_claim_data_item['cus_desc'] = $item['customerName'];
                $expense_claim_data_item['cus_country'] = $item['customerCountry'];
                $expense_claim_data_item['cus_currency'] = (int)$item['customerCurrencyID'];
                array_push($expense_claim_data_array, $expense_claim_data_item);
            }
            $data['customer'] = $expense_claim_data_array;
        }

        $final_output['success'] = true;
        $final_output['message'] = "";
        $final_output['data'] = $data;
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function customer_drop_get()
    {
        $this->db->select("customerAutoID, customerSystemCode, customerName, customerCountry, customerCurrencyID");
        $this->db->from('srp_erp_customermaster');
        $this->db->where('deletedYN != 1');
        $data = $this->db->where('companyID', current_companyID())->get()->result_array();
        return $data;
    }

    function create_receipt_header_post()
    {
        $data = $this->Auth_mobileUsers_Model->save_receipt_header();
        if($data['status'] == 'e'){
            $final_output['success'] = false;
            $final_output['message'] = "Receipt Voucher create failed!";
            $final_output['data'] = null;
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = true;
            $final_output['message'] = "Receipt Voucher created Successfully!";
            $final_output['data'] = $data['last_id'];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function create_receipt_item_post()
    {
        $data = $this->Auth_mobileUsers_Model->create_receipt_item();
        if($data[0] == 'e'){
            $final_output['success'] = false;
            $final_output['message'] = "Receipt Voucher create failed!";
            $final_output['data'] = null;
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = true;
            $final_output['message'] = "Receipt Voucher created Successfully!";
            $final_output['data'] = $data[2];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function delete_receipt_item_delete($id)
    {
        $company_id = current_companyID();
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherDetailAutoID', trim($id));
        $detail_arr = $this->db->get()->row_array();

        if ($detail_arr['type'] == 'Invoice') {
            $match_id = $detail_arr['invoiceAutoID'];
            $number = $detail_arr['transactionAmount'];
            $status = 0;
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET receiptTotalAmount = (receiptTotalAmount -{$number}), receiptInvoiceYN = {$status}  WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }
        /** update sub item master */
        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $detail_arr['receiptVoucherAutoId']);
        $this->db->where('soldDocumentDetailID', $detail_arr['receiptVoucherDetailAutoID']);
        $this->db->where('soldDocumentID', 'RV');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);
        /** end update sub item master */

        $this->db->where('receiptVoucherDetailAutoID', trim($id));
        $results = $this->db->delete('srp_erp_customerreceiptdetail');

        if ($results) {
            $final_output['success'] = true;
            $final_output['message'] = "Item Detail Deleted Successfully!";
            $final_output['data'] = [];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function create_receipt_general_tax_post()
    {
        $data = $this->Auth_mobileUsers_Model->save_receipt_general_tax();
        if($data['status'] == '0') {
            $final_output['success'] = false;
            $final_output['message'] = $data['data'];
            $final_output['data'] = null;
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = true;
            $final_output['message'] = $data['data'];
            $final_output['data'] = $data['view'];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function delete_general_tax_receipt_delete($taxID)
    {
        $this->db->delete('srp_erp_customerreceipttaxdetails', array('taxDetailAutoID' => trim($taxID)));
        $final_output['success'] = true;
        $final_output['message'] = "General Tax Deleted Successfully!";
        $final_output['data'] = [];
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function confirm_receipt_post()
    {
        $data = $this->Auth_mobileUsers_Model->confirm_receipt_voucher();
        if($data['error'] == 0) {
            $rt_data = [
                'success' => true,
                'message' => $data['message'],
                'data' => null
            ];
        } else {
            $rt_data = [
                'success' => false,
                'message' => $data['message'],
                'data' => null
            ];
        }
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function refer_back_receipt_post()
    {
        $master_id = $this->input->post('receiptVoucherAutoId');
        $this->db->select('approvedYN,RVcode,isSystemGenerated,confirmedYN');
        $this->db->where('receiptVoucherAutoId', trim($master_id));
        $this->db->from('srp_erp_customerreceiptmaster');
        $doc_status = $this->db->get()->row_array();

        if ($doc_status['approvedYN'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => 'The document already approved - ' . $doc_status['RVcode'],
                'data' => null
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
        else if ($doc_status['confirmedYN'] != 1) {
            $rt_data = [
                'success' => false,
                'message' => 'This document not confirmed yet.Please refresh the page and try again.',
                'data' => null
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
        else if ($doc_status['isSystemGenerated'] == 1) {
            $rt_data = [
                'success' => false,
                'message' => 'This is System Generated Document,You Cannot Refer Back this document.',
                'data' => null
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        }
        else {
            $this->load->library('Approvals_mobile');
            $status = $this->approvals_mobile->approve_delete($master_id, 'RV');
            if ($status == 1) {
                $rt_data = [
                    'success' => true,
                    'message' => 'Document Referred Back Successfully!',
                    'data' => null
                ];
                $this->set_response($rt_data, REST_Controller::HTTP_OK);
            } else {
                $rt_data = [
                    'success' => false,
                    'message' => 'Error in refer back!',
                    'data' => null
                ];
                $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }
    }

    function receipt_view_get()
    {
        $data = $this->Auth_mobileUsers_Model->receipt_view();
        $rt_data = [
            'success' => true,
            'message' => "Data Retrieved Successfully!",
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function delete_receipt_delete($receiptID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherAutoId', trim($receiptID));
        $datas = $this->db->get()->row_array();

        $this->db->select('RVcode');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('receiptVoucherAutoId', trim($receiptID));
        $master = $this->db->get()->row_array();

        if ($datas) {
            $rt_data = [
                'success' => false,
                'message' => "please delete all detail records before delete this document!",
                'data' => null
            ];
            $this->set_response($rt_data, REST_Controller::HTTP_OK);
        } else {
            if ($master['RVcode'] == "0") {
                $this->db->where('receiptVoucherAutoId', $receiptID);
                $results = $this->db->delete('srp_erp_customerreceiptmaster');
                if ($results) {
                    $this->db->where('receiptVoucherAutoId', $receiptID);
                    $this->db->delete('srp_erp_customerreceiptdetail');
                    $rt_data = [
                        'success' => true,
                        'message' => "Receipt Deleted Successfully!",
                        'data' => null
                    ];
                    $this->set_response($rt_data, REST_Controller::HTTP_OK);
                }
            } else {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('receiptVoucherAutoId', trim($receiptID));
                $this->db->update('srp_erp_customerreceiptmaster', $data);
                $rt_data = [
                    'success' => true,
                    'message' => "Receipt Deleted Successfully!",
                    'data' => null
                ];
                $this->set_response($rt_data, REST_Controller::HTTP_OK);
            }
        }
    }
    /** End Of Receipt Voucher Codes*/

    /** Customer Invoice API Codes*/
    function create_customer_invoice_header_post()
    {
        $data = $this->Auth_mobileUsers_Model->create_customer_invoice_header();
        if($data['status'] == 'error'){
            $final_output['success'] = false;
            $final_output['message'] = $data['message'];
            $final_output['data'] = null;
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        } else {
            $final_output['success'] = true;
            $final_output['message'] = $data['message'];
            $final_output['data'] = $data['view'];
            $this->set_response($final_output, REST_Controller::HTTP_OK);
        }
    }

    function fetch_customer_invoices_get()
    {
        $data = $this->Auth_mobileUsers_Model->fetch_customer_invoices();
        $rt_data = [
            'success' => true,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function delete_customerInvoice_delete($invoiceAutoID)
    {
        $data = $this->Auth_mobileUsers_Model->delete_customerInvoice($invoiceAutoID);
        $rt_data = [
            'success' => $data['type'],
            'message' => $data['message'],
            'data' => null
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function create_customer_invoice_item_post()
    {
        $data = $this->Auth_mobileUsers_Model->create_customer_invoice_item();
        $rt_data = [
            'success' => $data['type'],
            'message' => $data['message'],
            'data' => $data['detailID']
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function invoice_detail_direct_delete($invoiceDetailsAutoID)
    {
        $this->load->model('Invoice_model');
        $_POST['invoiceDetailsAutoID'] = $invoiceDetailsAutoID;
        $data = $this->Invoice_model->delete_item_direct();
        if($data) {
            $rt_data = [
                'success' => true,
                'message' => "Invoice Detail Deleted Successfully",
                'data' => null
            ];
        } else {
            $rt_data = [
                'success' => false,
                'message' => "Failed to Delete Invoice Detail. Please Try Again!",
                'data' => null
            ];
        }
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function create_customer_invoice_income_post()
    {
        $data = $this->Auth_mobileUsers_Model->create_customer_invoice_income();
        $rt_data = [
            'success' => $data['type'],
            'message' => $data['message'],
            'data' => $data['detailID']
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function create_customer_invoice_tax_post()
    {
        $data = $this->Auth_mobileUsers_Model->create_customer_invoice_tax();
        $rt_data = [
            'success' => $data['type'],
            'message' => $data['message'],
            'data' => $data['view']
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function delete_customerInvoice_tax_delete($taxDetailID)
    {
        $this->db->delete('srp_erp_customerinvoicetaxdetails',array('taxDetailAutoID' => trim($taxDetailID)));
        $rt_data = [
            'success' => true,
            'message' => 'Tax Detail Deleted Successfully!',
            'data' => null
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function customer_invoice_details_drops_get()
    {
        $data = [];
        if ($this->get('warehouse')) {
            $warehouse_data = $this->warehouse_drop_get(1);

            foreach ($warehouse_data as $key => $row) {
                $warehouse_data[$key]['wareHouseAutoID'] = (int)$row['wareHouseAutoID'];
            }
            $data['warehouse'] = $warehouse_data;
        }

        if ($this->get('GLCode')) {
            $data['GLCode'] = $this->Auth_mobileUsers_Model->fetch_all_gl_codes();
        }

        if ($this->get('GLSegment')) {
            $segment_data = $this->segments_drop_get(1);
            foreach ($segment_data as $key => $row) {
                $segment_data[$key]['segmentID'] = (int)$row['segmentID'];
            }
            $data['segment'] = $segment_data;
        }

        if ($this->get('taxType')) {
            $tax_data = $this->all_tax_drop_get(1, 1);
            foreach ($tax_data as $key => $row) {
                $tax_data[$key]['taxMasterAutoID'] = (int)$row['taxMasterAutoID'];
                $tax_data[$key]['taxPercentage'] = (float)$row['taxPercentage'];
            }
            $data['taxType'] = $tax_data;
        }

        if ($this->get('extraCharges')) {
            $extraCharges = all_discount_drop(2, '');
            foreach ($extraCharges as $key => $row) {
                $extraCharges[$key]['discountExtraChargeID'] = (int)$row['discountExtraChargeID'];
                $extraCharges[$key]['type'] = (int)$row['type'];
                $extraCharges[$key]['isChargeToExpense'] = (int)$row['isChargeToExpense'];
                $extraCharges[$key]['isTaxApplicable'] = (int)$row['isTaxApplicable'];
            }
            $data['extraCharges'] = $extraCharges;
        }

        if ($this->get('discount')) {
            $discount = all_discount_drop(1, '');
            foreach ($discount as $key => $row) {
                $discount[$key]['discountExtraChargeID'] = (int)$row['discountExtraChargeID'];
                $discount[$key]['type'] = (int)$row['type'];
                $discount[$key]['isChargeToExpense'] = (int)$row['isChargeToExpense'];
                $discount[$key]['isTaxApplicable'] = (int)$row['isTaxApplicable'];
            }
            $data['discount'] = $discount;
        }

        $rt_data = [
            'success' => true,
            'message' => '',
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function create_customer_invoice_extraCharges_post()
    {
        $this->load->model('Invoice_model');
        $data = $this->Invoice_model->save_inv_extra_detail();
        if($data) {
            if($data['last_id'])
            {
                $rt_data = [
                    'success' => true,
                    'message' => 'Extra Charge Save Successfully!',
                    'data' => $data['last_id']
                ];
            } else {
                $rt_data = [
                    'success' => false,
                    'message' => 'Extra Charge Already Exist!',
                    'data' => null
                ];
            }
        } else {
            $rt_data = [
                'success' => false,
                'message' => 'Extra Charge Save Failed!',
                'data' => null
            ];
        }
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function customerInvoice_extraCharge_delete($extraChargeID)
    {
        $this->load->model('Invoice_model');
        $_POST['extraChargeDetailID'] = $extraChargeID;
        $data = $this->Invoice_model->delete_extra_gen();
        $rt_data = [
            'success' => true,
            'message' => 'Extra Charge Deleted Successfully!',
            'data' => null
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function create_customer_invoice_discount_post()
    {
        $this->load->model('Invoice_model');
        $data = $this->Invoice_model->save_inv_discount_detail();
        if($data['status'] == true) {
            if(!empty($data['last_id']))
            {
                $rt_data = [
                    'success' => true,
                    'message' => 'Discount Save Successfully!',
                    'data' => $data['last_id']
                ];
            } else {
                $rt_data = [
                    'success' => false,
                    'message' => 'Discount Already Exist!',
                    'data' => null
                ];
            }
        } else {
            $rt_data = [
                'success' => false,
                'message' => 'Discount Save Failed!',
                'data' => null
            ];
        }
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function customerInvoice_discount_delete($discountID)
    {
        $this->load->model('Invoice_model');
        $_POST['discountDetailID'] = $discountID;
        $data = $this->Invoice_model->delete_discount_gen();
        $rt_data = [
            'success' => true,
            'message' => 'Discount Detail Deleted Successfully!',
            'data' => null
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function customer_invoice_view_get()
    {
        $data = $this->Auth_mobileUsers_Model->customer_invoice_view();
        $rt_data = [
            'success' => TRUE,
            'message' => 'Data Retrieved Successfully!',
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function confirm_customer_invoice_post()
    {
        $data = $this->Auth_mobileUsers_Model->confirm_customer_invoice();
        $rt_data = [
            'success' => $data['type'],
            'message' => $data['message'],
            'data' => null
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function refer_back_customer_invoice_post()
    {
        $data = $this->Auth_mobileUsers_Model->refer_back_customer_invoice();
        $rt_data = [
            'success' => $data['type'],
            'message' => $data['message'],
            'data' => null
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function generate_RV_from_CINV_post()
    {
        $data = $this->Auth_mobileUsers_Model->generate_RV_from_CINV();
        $rt_data = [
            'success' => $data['type'],
            'message' => $data['message'],
            'data' => null
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }

    function warehouse_qty_get()
    {
        $data = $this->Auth_mobileUsers_Model->warehouse_qty();
        $rt_data = [
            'success' => true,
            'message' => "Quantity Retrieved Successfully!",
            'data' => $data
        ];
        $this->set_response($rt_data, REST_Controller::HTTP_OK);
    }
}
