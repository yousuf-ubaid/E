<?php

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_Sales_Order extends REST_Controller
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
            $this->load->model('Quotation_contract_model');
            $this->load->library('sequence');
            $this->load->library('Approvals_mobile');
            $this->load->library('JWT');
            $this->load->library('S3');
            $this->load->library('Approvals');
            $this->load->helpers('payable');

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


    //-----------Sales Order--------------//

    function fetch_Quotation_contract_get()
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
        $sSearch = $this->post('sSearch');
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $contractView = $this->post('contractView');
        $isAdvance = $this->post('isAdvance');

        $page_type = 0;
        if($isAdvance){
            $page_type=1;
        }

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        $status = $this->post('status');
        $contractType = $this->post('contractType');
        $customer_filter = '';

        if (!empty($customer)) {
            $customer = array($this->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }

        $contractType_filter = '';
        if (!empty($contractType)) {
            $contractType = explode(',', $this->post('contractType'));
            $whereIN = "( '" . join("' , '", $contractType) . "' )";
            $contractType_filter = " AND contractType IN " . $whereIN;
        }

        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 23:59:00')";
        }

        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }else if ($status == 5) {
                $status_filter = " AND (approvedYN = 5)";
            }else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( contractCode Like '%$search%' ESCAPE '!') OR ( contractType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (contractNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (contractDate Like '%$sSearch%') OR (contractExpDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_contractmaster.companyID = " . $companyid . $customer_filter . $date . $contractType_filter . $status_filter . $searches."";
        $Quotation_list = array();
        $Quotation_list = $this->db->query("SELECT srp_erp_contractmaster.referenceNo as referenceNo,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.confirmedByEmpID as confirmedByEmp,contractCode,contractNarration,srp_erp_customermaster.customerName as customerMasterName,documentID, closedYN, transactionCurrencyDecimalPlaces ,transactionCurrency,confirmedYN,approvedYN, contractType, srp_erp_contractmaster.createdUserID as createdUser,DATE_FORMAT(contractDate,'" . $convertFormat . "') AS contractDate ,DATE_FORMAT(contractExpDate,'" . $convertFormat . "') AS contractExpDate,det.transactionAmount as total_value,ROUND(det.transactionAmount,2) as detTransactionAmount,srp_erp_contractmaster.isDeleted as isDeleted,srp_erp_contractmaster.isSystemGenerated as isSystemGenerated
                                             FROM srp_erp_contractmaster
                                             LEFT JOIN (SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det ON (det.contractAutoID = srp_erp_contractmaster.contractAutoID)
                                             LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID
                                             WHERE ".$where." AND srp_erp_contractmaster.isAdvance = " .$page_type. "")->result_array();
        $final_output['data'] = $Quotation_list;
        $final_output['success'] = true;
        $final_output['message'] = 'Quotation list retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }
    function save_quotation_contract_header_post()
    {
        $date_format_policy = date_format_policy();
        $cntrctDate = $this->post('contractDate');
        $contractDate = input_format_date($cntrctDate, $date_format_policy);
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $cntrctEpDate = $this->post('contractExpDate');
        $contractExpDate = input_format_date($cntrctEpDate, $date_format_policy);
        $segment = explode('|', trim($this->post('segment')));
        $subDocumentReference = $this->post('subDocumentReference');
        
        $this->db->trans_start();
        $customer_arr = $this->Quotation_contract_model->fetch_customer_data(trim($this->post('customerID')));
        $currency_code = explode('|', trim($this->post('currency_code')));

        $data['contractType'] = trim($this->post('contractType'));
        $d_code = 'CNT';
        if ($data['contractType'] == 'Quotation') {
            $d_code = 'QUT';
        } elseif ($data['contractType'] == 'Sales Order') {
            $d_code = 'SO';
        }
        $segment = explode('|', trim($this->post('segment')));
        $data['documentID'] = $d_code;
        $data['contactPersonName'] = trim($this->post('contactPersonName'));
        $data['contactPersonNumber'] = trim($this->post('contactPersonNumber'));
        $data['RVbankCode'] = trim($this->post('RVbankCode'));
        $data['contractDate'] = trim($contractDate);
        $data['contractExpDate'] = trim($contractExpDate);
        $contractNarration = ($this->post('contractNarration'));
        $data['contractNarration'] = str_replace('<br />', PHP_EOL, $contractNarration);
        $data['referenceNo'] = trim($this->post('referenceNo'));
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        //$data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        //$data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        // $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['paymentTerms'] = trim($this->post('paymentTerms'));

        $data['customerAddress'] = trim($this->post('addressBoxEditH'));
        $data['customerEmail'] = trim($this->post('emailBoxEditH'));
        $data['customerTelephone'] = trim($this->post('contactNumberBoxEditH'));
        $data['customerWebURL'] = trim($this->post('customerUrlBoxEditH'));

        $dataCMJSON = null;

        $crTypes = explode('<table', $this->post('Note'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['Note'] = trim($notes);
        
        $data['transactionCurrencyID'] = trim($this->post('transactionCurrencyID'));
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
        $data['salesPersonID'] = $this->post('salesperson');
        $data['showImageYN'] = $this->post('showImageYN');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');

        if (trim($this->post('contractAutoID'))) {
            $masterID = $this->post('contractAutoID');
            $taxAdded = $this->db->query("SELECT contractAutoID FROM srp_erp_contractdetails WHERE contractAutoID = $masterID
                                            UNION
                                        SELECT contractAutoID FROM srp_erp_contracttaxdetails WHERE contractAutoID = $masterID")->row_array();
            if (empty($taxAdded)) {
                $isGroupBasedTax = getPolicyValues('GBT', 'All');
                if($isGroupBasedTax && $isGroupBasedTax == 1) {
                    $data['isGroupBasedTax'] = 1;
                }
            }
            //checked approved document
            $contract_details_app = $this->db->query("SELECT approvedYN FROM srp_erp_contractmaster WHERE contractAutoID = '$masterID'")->row_array();

            if($contract_details_app && $contract_details_app['approvedYN'] == 1){
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Contract Update Failed, Already approved document'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
            $this->db->where('contractAutoID', trim($this->post('contractAutoID')));
            $this->db->update('srp_erp_contractmaster', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Contract Update Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Contract Updated Successfully.',
                    'last_id' => $this->post('contractAutoID')
                ], REST_Controller::HTTP_OK);
                 }
        } else {
            $isGroupBasedTax = getPolicyValues('GBT', 'All');
            if($isGroupBasedTax && $isGroupBasedTax == 1) {
                $data['isGroupBasedTax'] = 1;
            }

            $this->load->library('sequence');
            $company_id = current_companyID();
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $company_id;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = date('y-m-d H:i:s');

            $financeYearID = $this->db->query("SELECT companyFinanceYearID FROM srp_erp_companyfinanceperiod WHERE companyID = {$company_id} 
                                                   AND '{$contractDate}' BETWEEN dateFrom AND dateTo")->row('companyFinanceYearID');

            $contr_year = date('Y', strtotime($contractDate));
            $contr_month = date('m', strtotime($contractDate));
            if($locationwisecodegenerate == 1){
                $contract_code = $this->sequence->sequence_generator_location($data['documentID'],$financeYearID,$this->common_data['emplanglocationid'],$contr_year,$contr_month);
            }else{
                $contract_code = $this->sequence->sequence_generator_fin($data['documentID'],$financeYearID,$contr_year,$contr_month);
            }
            $data['contractCode'] = $contract_code;

            $this->db->insert('srp_erp_contractmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Contract Saved Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Contract Saved Successfully.',
                    'last_id' => $last_id
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    function fetch_item_detail_table_get()
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
        $contractAutoID = isset($request_1->contractAutoID) ? $request_1->contractAutoID : null;
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($contractAutoID),'CNT', 'contractAutoID');
        $data = array();
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        if($isGroupByTax == 1) {
            $this->db->select("srp_erp_contractdetails.*,srp_erp_itemcategory.description as mainCategory,sub.description as subCategory,rev.systemAccountCode as revanuedes,cost.systemAccountCode as costdes, IFNULL(taxAmount, 0) as taxAmount, srp_erp_taxcalculationformulamaster.Description as taxDescription");
            $this->db->from('srp_erp_contractdetails');
            $this->db->join('srp_erp_itemcategory', 'srp_erp_itemcategory.itemCategoryID = srp_erp_contractdetails.mainCategoryID','left');
            $this->db->join('srp_erp_itemcategory as sub', 'sub.itemCategoryID = srp_erp_contractdetails.subcategoryID','left');
            $this->db->join('srp_erp_chartofaccounts as rev', 'rev.GLAutoID = srp_erp_contractdetails.revanueGLAutoID','left');
            $this->db->join('srp_erp_chartofaccounts as cost', 'cost.GLAutoID = srp_erp_contractdetails.costGLAutoID','left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
            $this->db->join('(SELECT
                    documentDetailAutoID,
                    taxDetailAutoID
                    FROM
                    `srp_erp_taxledger`
                    where 
                    companyID = '. current_companyID() .' 
                    AND documentID = \'CNT\'
                    AND documentMasterAutoID  = '.$contractAutoID.' 
                    GROUP BY
                    documentMasterAutoID)taxledger',' taxledger.documentDetailAutoID = srp_erp_contractdetails.contractDetailsAutoID','left');
            $this->db->where('contractAutoID', $contractAutoID);
            $data['detail'] = $this->db->get()->result_array();
        } else {
            $this->db->select("srp_erp_contractdetails.*,srp_erp_itemcategory.description as mainCategory,sub.description as subCategory,rev.systemAccountCode as revanuedes,cost.systemAccountCode as costdes, IFNULL(taxAmount, 0) as taxAmount, srp_erp_taxcalculationformulamaster.Description as taxDescription");
            $this->db->from('srp_erp_contractdetails');
            $this->db->join('srp_erp_itemcategory', 'srp_erp_itemcategory.itemCategoryID = srp_erp_contractdetails.mainCategoryID','left');
            $this->db->join('srp_erp_itemcategory as sub', 'sub.itemCategoryID = srp_erp_contractdetails.subcategoryID','left');
            $this->db->join('srp_erp_chartofaccounts as rev', 'rev.GLAutoID = srp_erp_contractdetails.revanueGLAutoID','left');
            $this->db->join('srp_erp_chartofaccounts as cost', 'cost.GLAutoID = srp_erp_contractdetails.costGLAutoID','left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_contractdetails.taxCalculationformulaID','left');
            $this->db->where('contractAutoID', $contractAutoID);
            $data['detail'] = $this->db->get()->result_array();
        }
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,transactionCurrencyID,showImageYN');
        $this->db->where('contractAutoID', $contractAutoID);
        $data['currency'] = $this->db->get('srp_erp_contractmaster')->row_array();

        $this->db->select("*");
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');
        $data['master'] = $this->db->get()->row_array();
        
        if($isGroupByTax == 1) {
            $companyID = current_companyID();
            $data['tax_detail'] =$this->db->query("SELECT
                                        srp_erp_contracttaxdetails.taxDescription,srp_erp_contracttaxdetails.taxDetailAutoID,taxleg.amount as amount
                                    FROM
                                    srp_erp_contracttaxdetails
                                    INNER JOIN (
                                        SELECT
                                            SUM(amount) as amount,taxDetailAutoID
                                        FROM
                                            srp_erp_taxledger
                                        WHERE
                                            documentID = 'CNT'
                                            AND documentMasterAutoID = $contractAutoID
                                        GROUP BY documentMasterAutoID,taxDetailAutoID
                                    ) taxleg ON srp_erp_contracttaxdetails.taxDetailAutoID = taxleg.taxDetailAutoID
                                    WHERE
                                        contractAutoID = $contractAutoID AND companyID = $companyID")->result_array();
        } else {
            $this->db->select('*');
            $this->db->where('contractAutoID', $contractAutoID);
            $this->db->from('srp_erp_contracttaxdetails');
            $data['tax_detail'] = $this->db->get()->result_array();
        }

        $this->response([
            'data' => $data,
            'success' => TRUE,
            'message' => 'Sales Order details Retrieved.'
        ], REST_Controller::HTTP_OK);
    }
    function fetch_quotation_segment_get()
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
        $contractID = isset($request_1->contractAutoID) ? $request_1->contractAutoID : null;
        $companyID = current_companyID();

        $data = $this->db->query("SELECT
	    segmentID,segmentCode
            FROM
	    `srp_erp_contractmaster`
	    where 
	    companyID =  $companyID
	    AND contractAutoID  = $contractID 
	    ")->row_array();
        $this->response([
            'data' => $data,
            'success' => TRUE,
            'message' => 'Quotation Segment details Retrieved.'
        ], REST_Controller::HTTP_OK);

    }
    function load_project_segmentBase_multiple_get()
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
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = isset($request_1->segment) ? $request_1->segment : null;
        $post_doc = isset($request_1->post_doc) ? $request_1->post_doc : null;
        $ex_segment = explode("|", $segment);
        $ex_segment = explode(" | ", $ex_segment[0]);
        $this->db->select('headerID as projectID, projectDescription as projectName');
        $this->db->from('srp_erp_boq_header');
        $this->db->where('companyID', $companyID);
        $str = '';
        if($post_doc != 'MR'){
            $this->db->where('segementID', $ex_segment[0]);
            $str = 'onchange="load_project_segmentBase_category(this,this.value)"';
        }
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('projectID[]', $data_arr, '', 'class="form-control select2 projectID" id="projectID" '.$str);
    }
    function save_item_order_detail_post()
    {
        $item_data = json_decode(file_get_contents('php://input'));
        $this->db->trans_start();
        if(!empty($item_data)){
            $date_time = date('Y-m-d H:i:s');
            $iteminput_data = [];
        foreach ($item_data as $row){
        $projectExist = project_is_exist();
        $itemAutoIDs = $row->itemAutoID;
        $uoms = $row->uom;
        $UnitOfMeasureID = $row->UnitOfMeasureID;
        $itemReferenceNo = $row->itemReferenceNo;
        $discount = $row->discount;
        $discount_amount = $row->discount_amount;
        $estimatedAmount = $row->estimatedAmount;
        $comment = $row->comment;
        $remarks = $row->remarks;
        $quantityRequested = $row->quantityRequested;
        $noOfItems = $row->noOfItems;
        $project_categoryID = $row->project_categoryID;
        $project_subCategoryID = $row->project_subCategoryID;
        // $itemAutoIDJoin = join(',', $itemAutoIDs);
        $text_type = $row->text_type;
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_contractmaster',trim($row->contractAutoID),'CNT', 'contractAutoID');

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($row->contractAutoID));
        $contract_master = $this->db->get()->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('contractAutoID', $row->contractAutoID);
        $tax_detail = $this->db->get('srp_erp_contracttaxdetails')->row_array();

        $this->db->trans_start();
        //foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data(trim($row->itemAutoID));
            $uom = explode('|', $uoms);
            // $data['contractAutoID'] = trim($row->contractAutoID);
            $itemDiscount = $row->discount_amount;
            $unitTransAmount = (trim($row->estimatedAmount) - $itemDiscount);
            $TransAmount = $unitTransAmount * trim($row->quantityRequested);

            // if ($projectExist == 1) {
                
                $projectID = $row->projectID;
                $projectCurrency = project_currency($row->projectID);
                $projectCurrencyExchangerate = currency_conversionID($contract_master['transactionCurrencyID'], $projectCurrency);
            // }
            $iteminput_data[] = [
            'contractAutoID' => trim($row->contractAutoID),
            'projectID' => $row->projectID,
            'projectExchangeRate' => $projectCurrencyExchangerate['conversion'],
            'project_categoryID' => $row->project_categoryID,
            'project_subCategoryID' => $row->project_subCategoryID,
            //}

            'itemAutoID' => trim($row->itemAutoID),
            'itemSystemCode' => $item_arr['itemSystemCode'],
            'itemDescription' => $item_arr['itemDescription'],
            'itemCategory' => $item_arr['mainCategory'],
            'unitOfMeasure' => trim($uom[0] ?? ''),
            'unitOfMeasureID' => trim($row->UnitOfMeasureID),
            'itemReferenceNo' => trim($row->itemReferenceNo),
            'defaultUOM' => $item_arr['defaultUnitOfMeasure'],
            'defaultUOMID' => $item_arr['defaultUnitOfMeasureID'],
            'conversionRateUOM' => conversionRateUOM_id(trim($row->UnitOfMeasureID), $item_arr['defaultUnitOfMeasureID']),
            'discountPercentage' => $row->discount,
            'discountAmount' => $row->discount_amount,
            'noOfItems' => trim($row->noOfItems),
            'requestedQty' => trim($row->quantityRequested),
            'unittransactionAmount' => $unitTransAmount,
            'transactionAmount' => $TransAmount,
            'companyLocalAmount' => ($TransAmount/$contract_master['companyLocalExchangeRate']),
            'companyReportingAmount' => ($TransAmount/$contract_master['companyReportingExchangeRate']),
            'customerAmount' => ($TransAmount/$contract_master['customerCurrencyExchangeRate']),
            'discountTotal' => ($itemDiscount * trim($row->quantityRequested)),
            'comment' => trim($row->comment),
            'remarks' => trim($row->remarks),
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => date('y-m-d H:i:s'),

            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdDateTime' => date('y-m-d H:i:s')
            ];
        }
        $this->db->insert_batch('srp_erp_contractdetails', $iteminput_data);
        $last_id = $this->db->insert_id();
            if(!empty($row->text_type)){
                if($isGroupByTax == 1){ 

                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$row->text_type);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            
                    $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                    $this->db->where('contractAutoID', $row->contractAutoID);
                    $inv_master = $this->db->get('srp_erp_contractmaster')->row_array();
            
                    $dataTax['contractAutoID'] = trim($row->contractAutoID);
                    $dataTax['taxFormulaMasterID'] = $row->text_type;
                    $dataTax['taxDescription'] = $master['Description'];
                    $dataTax['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
                    $dataTax['transactionCurrency'] = $inv_master['transactionCurrency'];
                    $dataTax['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
                    $dataTax['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
                    $dataTax['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
                    $dataTax['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
                    $dataTax['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
                    $dataTax['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
                    $dataTax['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
                    $dataTax['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

                    $total_doc_amount = trim($row->estimatedAmount) * trim($row->quantityRequested);
                    $discountAmount = $row->discount_amount * $row->quantityRequested;
                    tax_calculation_vat('srp_erp_contracttaxdetails',$dataTax,$row->text_type,'contractAutoID',trim($row->contractAutoID),$total_doc_amount,'CNT',$last_id,$discountAmount,1);
                }             
            }
        }
            if($isGroupByTax == 1) {
                $this->db->select('SUM(transactionAmount) as amount');
                $this->db->from('srp_erp_contractdetails');
                $this->db->where('contractAutoID', trim($row->contractAutoID));
                $total_doc_amount = $this->db->get()->row('amount');
                tax_calculation_update_vat('srp_erp_contracttaxdetails', 'contractAutoID', trim($row->contractAutoID), $total_doc_amount, 0, 'CNT');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Order Detail : Save Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Order Detail : Saved Successfully.',
                    'last_id' => $last_id
                ], REST_Controller::HTTP_OK);
            }
    }
    function load_contract_conformation_get()
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
        $contractID = isset($request_1->contractAutoID) ? $request_1->contractAutoID : null;
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($contractID);
        $ihtml = isset($request_1->html) ? $request_1->html : null;
        $approval = isset($request_1->approval) ? $request_1->approval : null;
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['approval'] = $approval;

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        if (!$ihtml) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($ihtml){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view', $data, true);

        // if ($ihtml) {
        //     echo $html;
        // } else {
            $this->load->library('pdf');
            //$this->load->view('system/quotation_contract/erp_contract_print', $data);
            $printlink = print_template_pdf($documentid['documentID'],'system/quotation_contract/erp_contract_print');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        //}
            $this->load->view($printlink, $data);
        // }
    }
    function fetch_documentID_get(){
        $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
        $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $this->load->database('default');

        $companyid = $this->common_data['company_data']['company_id'];
        $request_body = file_get_contents('php://input');

        $request_1 = json_decode($request_body);
        $documentSystemCode = isset($request_1->documentSystemCode) ? $request_1->documentSystemCode : null;
        
        $this->db->select('documentID');
        $this->db->where('contractAutoID', trim($documentSystemCode));
        $this->db->from('srp_erp_contractmaster');
        $documentID = $this->db->get()->row_array();
        echo json_encode($documentID['documentID']);
    }
    function contract_confirmation_post()
    {
        $this->db->select('contractDetailsAutoID');
        $this->db->where('contractAutoID', trim($this->post('contractAutoID')));
        $this->db->from('srp_erp_contractdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->response([
                'success' => FALSE ,
                'message' => 'There are no records to confirm this document!'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->db->select('contractAutoID');
            $this->db->where('contractAutoID', trim($this->post('contractAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_contractmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Document already confirmed'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $contractAutoID = trim($this->post('contractAutoID'));

                $this->load->library('Approvals');
                $this->db->select('documentID,contractType,contractCode,customerCurrencyExchangeRate,companyReportingExchangeRate, companyLocalExchangeRate ,contractAutoID,transactionCurrencyDecimalPlaces,contractDate');
                $this->db->where('contractAutoID', trim($this->post('contractAutoID')));
                $this->db->from('srp_erp_contractmaster');
                $c_data = $this->db->get()->row_array();

                $validate_code = validate_code_duplication($c_data['contractCode'], 'contractCode', $contractAutoID,'contractAutoID', 'srp_erp_contractmaster');
                if(!empty($validate_code)) {
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }

                $autoApproval= get_document_auto_approval($c_data['documentID']);
                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve($c_data['contractAutoID'], 'srp_erp_contractmaster','contractAutoID', $c_data['documentID'],$c_data['contractCode'], $c_data['contractDate']);
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals->CreateApproval($c_data['documentID'], $c_data['contractAutoID'], $c_data['contractCode'], $c_data['contractType'], 'srp_erp_contractmaster', 'contractAutoID', 1, $c_data['contractDate']);
                }else{
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Approval levels are not set for this document'
                    ], REST_Controller::HTTP_NOT_FOUND);
                    exit;
                }

                if ($approvals_status) {
                    $this->db->select_sum('transactionAmount');
                    $this->db->where('contractAutoID', trim($this->post('contractAutoID')));
                    $total = $this->db->get('srp_erp_contractdetails')->row('transactionAmount');
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => date('y-m-d H:i:s'),
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => round($total, $c_data['transactionCurrencyDecimalPlaces']),
                        'companyLocalAmount' => ($total / $c_data['companyLocalExchangeRate']),
                        'companyReportingAmount' => ($total / $c_data['companyReportingExchangeRate']),
                        'customerCurrencyAmount' => ($total / $c_data['customerCurrencyExchangeRate']),
                    );
                    $this->db->where('contractAutoID', trim($this->post('contractAutoID')));
                    $this->db->update('srp_erp_contractmaster', $data);
                    
                    if($autoApproval==0) {
                        $result = $this->save_quotation_contract_approval(0, $c_data['contractAutoID'], 1, 'Auto Approved');
                        if($result){
                            $this->response([
                                'success' => TRUE,
                                'message' => 'Approvals Created Successfully.',
                                'result' => $result
                            ], REST_Controller::HTTP_OK);
                        }
                    }else{
                        $this->response([
                            'success' => TRUE,
                            'message' => 'Approvals Created Successfully.'
                        ], REST_Controller::HTTP_OK);
                    }
                } else {
                    /*return false;*/
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'oops, something went wrong!.'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
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
}
