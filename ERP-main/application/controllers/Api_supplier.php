<?php

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_supplier extends REST_Controller
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
            $this->load->model('Suppliermaster_model');

            $this->load->helper('Customer_helper');
            
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

    //-----------Supplier Master--------------//

    function save_supplier_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $name = $output['token']->username;

        $externalProductID = $this->post('externalProductID');
        $externalPrimaryKey = $this->post('externalPrimaryKey');
        $suppliercode = $this->post('suppliercode');
        $supplierName = $this->post('supplierName');
        $nameOnCheque = $this->post('nameOnCheque');
        //$liabilityAccount = $this->post('liabilityAccount');
        $partyCategoryID = $this->post('partyCategoryID');
        $supplierAddress1 = $this->post('supplierAddress1');
        $supplierAddress2 = $this->post('supplierAddress2');
        $liability = fetch_gl_account_desc(trim($this->post('liabilityAccount')));
        $suppliercountry = trim($this->post('suppliercountry'));
        $suppliercountryID = $this->db->query("SELECT CountryID FROM srp_erp_countrymaster WHERE CountryDes = '{$suppliercountry}'")->row('CountryID');
        $supplierTelephone = trim($this->post('supplierTelephone'));
        $supplierEmail = trim($this->post('supplierEmail'));
        $supplierUrl = trim($this->post('supplierUrl'));
        $supplierFax = trim($this->post('supplierFax'));
        $currency_code = explode('|', trim($this->post('currency_code')));
        $supplierCurrency = trim($this->post('supplierCurrency'));
        $supplierLocationID= trim($this->post('$supplierLocationID'));
        $customerCreditPeriod = $this->post('customerCreditPeriod');
        $customerCreditLimit = $this->post('customerCreditLimit');
        $customertaxgroup = $this->post('customertaxgroup');
        $vatIdNo = $this->post('vatIdNo');
        $vatEligible = $this->post('vatEligible');
        $vatNumber = $this->post('vatNumber');
        $vatPercentage = $this->post('vatPercentage');
        $masterConfirmedYN = $this->post('masterConfirmedYN');
    
                $data_ins_supplier['externalProductID'] = $externalProductID;
                $data_ins_supplier['externalPrimaryKey'] = $externalPrimaryKey;
                $data_ins_supplier['supplierSystemCode'] = $this->sequence->sequence_generator('SUP');
                $data_ins_supplier['secondaryCode'] = $suppliercode;
                $data_ins_supplier['supplierName'] = $supplierName;
                $data_ins_supplier['nameOnCheque'] = trim($nameOnCheque);
                $data_ins_supplier['liabilityAutoID'] = $liability['GLAutoID'];
                $data_ins_supplier['liabilitySystemGLCode'] = $liability['systemAccountCode'];
                $data_ins_supplier['liabilityGLAccount'] = $liability['GLSecondaryCode'];
                $data_ins_supplier['liabilityDescription'] = $liability['GLDescription'];
                $data_ins_supplier['liabilityType'] = $liability['subCategory'];
                $data_ins_supplier['partyCategoryID'] = trim($partyCategoryID);
                $data_ins_supplier['supplierAddress1'] = trim($supplierAddress1);
                $data_ins_supplier['supplierAddress2'] = trim($supplierAddress2);
                $data_ins_supplier['suppliercountryID'] = trim($suppliercountryID);
                $data_ins_supplier['supplierCountry'] = trim($suppliercountry);
                $data_ins_supplier['supplierTelephone'] = trim($supplierTelephone);
                $data_ins_supplier['supplierEmail'] = trim($supplierEmail);
                $data_ins_supplier['supplierUrl'] = trim($supplierUrl);
                $data_ins_supplier['supplierFax'] = trim($supplierFax);
                $data_ins_supplier['supplierCurrencyID'] = trim($supplierCurrency);
                $data_ins_supplier['supplierCurrency'] = $currency_code[0];
                $data_ins_supplier['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data_ins_supplier['supplierCurrency']);
                $data_ins_supplier['supplierLocationID'] = $supplierLocationID;
                $data_ins_supplier['supplierCreditPeriod'] = $customerCreditPeriod;
                $data_ins_supplier['supplierCreditLimit'] = $customerCreditLimit;
                $data_ins_supplier['taxGroupID'] = trim($customertaxgroup);
                $data_ins_supplier['vatIdNo'] = trim($vatIdNo);
                $data_ins_supplier['vatEligible'] = trim($vatEligible);
                $data_ins_supplier['vatNumber'] = trim($vatNumber);
                $data_ins_supplier['vatPercentage'] = trim($vatPercentage);
                $data_ins_supplier['isActive'] = 1;
                $data_ins_supplier['masterConfirmedYN'] = 1;//trim($masterConfirmedYN);
                $data_ins_supplier['companyID'] = $this->common_data['company_data']['company_id'];
                $data_ins_supplier['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_ins_supplier['createdUserGroup'] = $this->common_data['user_group'];
                $data_ins_supplier['createdPCID'] = $this->common_data['current_pc'];
                $data_ins_supplier['createdUserID'] = $this->common_data['current_userID'];
                $data_ins_supplier['createdUserName'] = $this->common_data['current_user'];
                $data_ins_supplier['createdDateTime'] = date('y-m-d H:i:s');
                $data_ins_supplier['modifiedPCID'] = $this->common_data['current_pc'];
                $data_ins_supplier['modifiedUserID'] = $userID;
                $data_ins_supplier['modifiedUserName'] = $name;
                $data_ins_supplier['modifiedDateTime'] = date('y-m-d H:i:s');
                $data_ins_supplier['timestamp'] = date('y-m-d H:i:s');

                $result = $this->db->insert('srp_erp_suppliermaster', $data_ins_supplier);
                if ($result) {
                    $id = $this->db->insert_id();
                    $supplierAutoID = $id;
                    $final_output['success'] = true;
                    $final_output['message'] = 'Supplier Inserted successfully';
                    $final_output['data'] = $id;
                    $this->response($final_output, REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'success' => FALSE,
                        'message' => 'Something Went Wrong.'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                }
    }
    function update_supplier_post()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $name = $output['token']->username;
        
        $this->db->trans_start();
        $liability = fetch_gl_account_desc(trim($this->post('liabilityAccount')));
        $currency_code = explode('|', trim($this->post('currency_code')));
        $supplierAutoID = trim($this->post('supplierAutoID'));

                $data_ins_supplier['externalProductID'] = $this->post('externalProductID');
                $data_ins_supplier['externalPrimaryKey'] = $this->post('externalPrimaryKey');
                $data_ins_supplier['secondaryCode'] = $this->post('suppliercode');
                $data_ins_supplier['supplierName'] = $this->post('supplierName');
                $data_ins_supplier['nameOnCheque'] = trim($this->post('nameOnCheque'));
                $data_ins_supplier['liabilityAutoID'] = $liability['GLAutoID'];
                $data_ins_supplier['liabilitySystemGLCode'] = $liability['systemAccountCode'];
                $data_ins_supplier['liabilityGLAccount'] = $liability['GLSecondaryCode'];
                $data_ins_supplier['liabilityDescription'] = $liability['GLDescription'];
                $data_ins_supplier['liabilityType'] = $liability['subCategory'];
                $data_ins_supplier['partyCategoryID'] = trim($this->post('partyCategoryID'));
                $data_ins_supplier['supplierAddress1'] = trim($this->post('supplierAddress1'));
                $data_ins_supplier['supplierAddress2'] = trim($this->post('supplierAddress2'));
                $data_ins_supplier['suppliercountryID'] = trim($this->post('suppliercountryID'));
                $data_ins_supplier['supplierCountry'] = trim($this->post('suppliercountry'));
                $data_ins_supplier['supplierTelephone'] = trim($this->post('supplierTelephone'));
                $data_ins_supplier['supplierEmail'] = trim($this->post('supplierEmail'));
                $data_ins_supplier['supplierUrl'] = trim($this->post('supplierUrl'));
                $data_ins_supplier['supplierFax'] = trim($this->post('supplierFax'));
                $data_ins_supplier['supplierCurrencyID'] = trim($this->post('supplierCurrency'));
                $data_ins_supplier['supplierCurrency'] = $currency_code[0];
                $data_ins_supplier['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data_ins_supplier['supplierCurrency']);
                $data_ins_supplier['supplierLocationID'] = $this->post('supplierLocationID');
                $data_ins_supplier['supplierCreditPeriod'] = $this->post('customerCreditPeriod');
                $data_ins_supplier['supplierCreditLimit'] = $this->post('customerCreditLimit');
                $data_ins_supplier['taxGroupID'] = trim($this->post('customertaxgroup'));
                $data_ins_supplier['vatIdNo'] = trim($this->post('vatIdNo'));
                $data_ins_supplier['vatEligible'] = trim($this->post('vatEligible'));
                $data_ins_supplier['vatNumber'] = trim($this->post('vatNumber'));
                $data_ins_supplier['vatPercentage'] = trim($this->post('vatPercentage'));
                $data_ins_supplier['isActive'] = 1;
                $data_ins_supplier['masterConfirmedYN'] = 1;//trim($masterConfirmedYN);
                $data_ins_supplier['companyID'] = $this->common_data['company_data']['company_id'];
                $data_ins_supplier['modifiedPCID'] = $this->common_data['current_pc'];
                $data_ins_supplier['modifiedUserID'] = $userID;
                $data_ins_supplier['modifiedUserName'] = $name;
                $data_ins_supplier['modifiedDateTime'] = date('y-m-d H:i:s');

        if (!empty($supplierAutoID)) {
            $this->db->where('supplierAutoID', trim($this->post('supplierAutoID')));
            $this->db->update('srp_erp_suppliermaster', $data_ins_supplier);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                 $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE,
                    'message' => 'Supplier : ' . $data_ins_supplier['SupplierName'] . ' Update Failed' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Supplier Details Updated Successfully.'
                ], REST_Controller::HTTP_OK);
            }
        }
        else{
            $this->response([
                'success' => FALSE,
                'message' => 'Supplier not Found.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    function view_supplier_get()
    {
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $this->load->database('default');

        $companyid = $this->common_data['company_data']['company_id'];
        $request_body = file_get_contents('php://input');

        $request_1 = json_decode($request_body);
        $customerCompanyID = isset($request_1->customerCompanyID) ? $request_1->customerCompanyID : null;
        $CompanyID_filter = '';
        if(!empty($customerCompanyID)){
            $customerCompany = array($customerCompanyID);
            $whereIN = "( " . join(",", $customerCompany) . " ) ";
            $CompanyID_filter = " srp_erp_suppliermaster.companyID IN " . $whereIN;
        }
        $supplier = array();
        $supplier = $this->db->query("SELECT srp_erp_suppliermaster.deletedYN as deletedYN,srp_erp_partycategories.categoryDescription as categoryDescription,supplierAutoID,supplierSystemCode,supplierName,secondaryCode,supplierName,supplierAddress1,supplierAddress2,supplierCountry,supplierTelephone,supplierEmail,supplierUrl,supplierFax,isActive,supplierCurrency,supplierEmail,supplierTelephone,supplierCurrencyID,cust.Amount as Amount,ROUND(cust.Amount, 2) as Amount_search,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,masterConfirmedYN,masterApprovedYN
                                        FROM srp_erp_suppliermaster
                                        LEFT JOIN srp_erp_partycategories ON srp_erp_suppliermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID
                                        LEFT JOIN (SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate)*-1 as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = 'SUP' AND subLedgerType=2 GROUP BY partyAutoID) cust ON cust.partyAutoID = srp_erp_suppliermaster.supplierAutoID
                                        WHERE " . $CompanyID_filter . " ORDER BY supplierAutoID DESC ")->result_array();
        $final_output['data'] = $supplier;
        $final_output['success'] = true;
        $final_output['message'] = 'Supplier details retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);

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


}
