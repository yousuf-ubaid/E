<?php

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_purchaseRequest extends REST_Controller
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
            $this->load->library('sequence');
            $this->load->library('Approvals_mobile');
            $this->load->library('JWT');
            $this->load->library('S3');
            $this->load->library('Approvals');

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
            $this->common_data['company_data']['company_default_currency'] = $this->company_info->local_currency_code;;
            $this->common_data['company_data']['company_reporting_currency'] = $this->company_info->rpt_currency_code;
            $this->common_data['company_data']['company_reporting_currencyID'] = $this->company_info->rpt_currency;
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


    //-----------Purchase Request--------------//


    function save_purchase_request_header_post()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->post('expectedDeliveryDate'));
        $Pqrdate = trim($this->post('documentDate'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($Pqrdate, $date_format_policy);

        $segment = explode('|', trim($this->post('segment')));
        $currency_code = explode('|', trim($this->post('currency_code')));

        $data['documentID'] = 'PRQ';
        $data['projectID'] = trim($this->post('projectID'));
        $data['requestedEmpID'] = trim($this->post('requestedEmpID'));
        $data['requestedByName'] = trim($this->post('requestedByName'));
        $narration = ($this->post('narration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['transactionCurrency'] = trim($this->post('transactionCurrency'));
        $data['referenceNumber'] = trim($this->post('referenceNumber'));
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['jobNumber'] = trim($this->post('jobNumber'));
        $data['jobID'] = trim($this->post('workProcessID'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');
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
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = date('y-m-d H:i:s');
        $data['purchaseRequestCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_purchaserequestmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Purchase Request Save Failed.'
                ], REST_Controller::HTTP_NOT_FOUND);
                $this->db->trans_rollback();
                
            } else {
                update_warehouse_items();
                update_item_master();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Purchase Request Saved Successfully.',
                    'last_id' => $last_id
                ], REST_Controller::HTTP_OK); 
                $this->db->trans_commit();
            }
    }
    function save_purchase_request_detail_post()
    {
        $item_data = json_decode(file_get_contents('php://input'));
        $this->db->trans_start();
        if(!empty($item_data)){
            $date_time = date('Y-m-d H:i:s');
            $iteminput_data = [];
            foreach ($item_data as $row){
                $date_format_policy = date_format_policy();
                $expectedDeliveryDate = $row->expectedDeliveryDateDetail;
                $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);

                $item_arr = fetch_item_data($row->itemAutoID);
                $uomEx = explode('|', $row->uom);
                $itemdiscount = ($row->estimatedAmount / 100) * $row->discount;
                $itemunitAmount = ($row->estimatedAmount - $itemdiscount);
                
                    $iteminput_data[] = [
                        'purchaseRequestID' => $row->purchaseRequestID,
                        'expectedDeliveryDate' => $format_expectedDeliveryDate,
                        'itemAutoID' => $row->itemAutoID,
                        'itemSystemCode' => $item_arr['itemSystemCode'],
                        'itemType' => $item_arr['mainCategory'],
                        'itemDescription' => $item_arr['itemDescription'],
                        'unitOfMeasure' => trim($uomEx[0] ?? ''),
                        'unitOfMeasureID' => $row->UnitOfMeasureID,
                         'defaultUOM' => $item_arr['defaultUnitOfMeasure'],
                         'defaultUOMID' => $item_arr['defaultUnitOfMeasureID'],
                         'conversionRateUOM' => conversionRateUOM_id($row->UnitOfMeasureID, $item_arr['defaultUnitOfMeasureID']),
                         'discountPercentage' => $row->discount,
                         'discountAmount' => $itemdiscount,
                         'requestedQty' => $row->quantityRequested,
                         'unitAmount' => $itemunitAmount,
                         'totalAmount' => ($itemunitAmount * $row->quantityRequested),
                         'comment' => $row->comment,
                         'remarks' => '',
                         'companyID' => $this->common_data['company_data']['company_id'],
                         'companyCode' => $this->common_data['company_data']['company_code'],
                         'modifiedPCID' => $this->common_data['current_pc'],
                         'modifiedUserID' => $this->common_data['current_userID'],
                         'modifiedUserName' => $this->common_data['current_user'],
                         'modifiedDateTime' => $date_time,
                         'createdUserGroup' => $this->common_data['user_group'],
                         'createdPCID' => $this->common_data['current_pc'],
                         'createdUserID' => $this->common_data['current_userID'],
                         'createdUserName' => $this->common_data['current_user'],
                         'createdDateTime' => $date_time
                     ];

                }
                $this->db->insert_batch('srp_erp_purchaserequestdetails', $iteminput_data);
               
            }
            $last_id = $this->db->insert_id();
         $this->db->trans_complete();
         if ($this->db->trans_status() === FALSE) {
             $this->db->trans_rollback();
             $this->response([
                 'success' => FALSE ,
                 'message' => 'Purchase Order Details :  Save Failed'
             ], REST_Controller::HTTP_NOT_FOUND);
         } else {
             $this->db->trans_commit();
             $this->response([
                 'success' => TRUE,
                 'message' => 'Purchase Order Details :  Saved Successfully.',
                 'last_id' => $last_id
             ], REST_Controller::HTTP_OK); 
         }
        }

    function update_purchase_request_post()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->post('expectedDeliveryDate'));
        $Pqrdate = trim($this->post('documentDate'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($Pqrdate, $date_format_policy);

        $segment = explode('|', trim($this->post('segment')));
        $currency_code = explode('|', trim($this->post('currency_code')));

        $data['documentID'] = 'PRQ';
        $data['projectID'] = trim($this->post('projectID'));
        $data['requestedEmpID'] = trim($this->post('requestedEmpID'));
        $data['requestedByName'] = trim($this->post('requestedByName'));

        $narration = ($this->post('narration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);

        $data['transactionCurrency'] = trim($this->post('transactionCurrency'));
        $data['referenceNumber'] = trim($this->post('referenceNumber'));
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['jobNumber'] = trim($this->post('jobNumber'));
        $data['jobID'] = trim($this->post('workProcessID'));

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');
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

         if (trim($this->post('purchaseRequestID'))) {
             $this->db->where('purchaseRequestID', trim($this->post('purchaseRequestID')));
             $this->db->update('srp_erp_purchaserequestmaster', $data);
             $this->db->trans_complete();
             if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                 $this->response([
                    'success' => FALSE ,
                    'message' => 'Purchase Request Update Failed.'
                ], REST_Controller::HTTP_NOT_FOUND);
             } else {
                 update_warehouse_items();
                 update_item_master();
                 $this->db->trans_commit();
                 $this->response([
                    'success' => TRUE,
                    'message' => 'Purchase Request Updated Successfully.'
                ], REST_Controller::HTTP_OK); 

             }
         } else {
            
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Purchase Request Not Found.'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
    }
    function update_purchase_request_detail_post()
    {
        $this->db->trans_start();
        $item_arr = fetch_item_data(trim($this->post('itemAutoID')));
        $uom = explode('|', $this->post('uom'));
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->post('expectedDeliveryDateDetail'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $data['purchaseRequestID'] = trim($this->post('purchaseRequestID'));
        $data['itemAutoID'] = trim($this->post('itemAutoID'));
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['itemType'] = $item_arr['mainCategory'];
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->post('UnitOfMeasureID'));
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['discountPercentage'] = trim($this->post('discount'));
        $data['discountAmount'] = (trim($this->post('estimatedAmount')) / 100) * trim($this->post('discount'));
        $data['requestedQty'] = trim($this->post('quantityRequested'));
        $data['unitAmount'] = (trim($this->post('estimatedAmount')) - $data['discountAmount']);
        $data['totalAmount'] = ($data['unitAmount'] * trim($this->post('quantityRequested')));
        $data['comment'] = trim($this->post('comment'));
        $data['remarks'] = trim($this->post('remarks'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');

        if (trim($this->post('purchaseRequestDetailsID'))) {
            $this->db->where('purchaseRequestDetailsID', trim($this->post('purchaseRequestDetailsID')));
            $this->db->update('srp_erp_purchaserequestdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Purchase Order Details :  ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
                 }
                 else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Purchase Order Details :  ' . $data['itemSystemCode'] . ' Updated Successfully.'
                ], REST_Controller::HTTP_OK); 
                
            }
        }
    }
    
    function view_purchase_request_get()
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
            $CompanyID_filter = " srp_erp_purchaserequestdetails.companyID IN " . $whereIN;
        }
        $convertFormat = convert_date_format_sql();
        $purchaserequest = array();
        $purchaserequest = $this->db->query("SELECT *, DATE_FORMAT(expectedDeliveryDate,'" . $convertFormat . "') AS expectedDeliveryDate,srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,itemledgercurrent.currentstock AS itemledstock
                                        FROM srp_erp_purchaserequestdetails
                                        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID
                                        LEFT JOIN (SELECT IF (mainCategory = 'Inventory',  (SUM(transactionQTY/ convertionRate)), NULL) AS currentstock, srp_erp_itemledger.itemAutoID 
                           FROM srp_erp_itemledger
                           LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID 
                           WHERE srp_erp_itemledger.itemAutoID is not null
                           GROUP BY srp_erp_itemledger.itemAutoID 
                         )itemledgercurrent ON itemledgercurrent.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID
                                        WHERE " . $CompanyID_filter . " ORDER BY purchaseRequestID DESC ")->result_array();
        $final_output['data'] = $purchaserequest;
        $final_output['success'] = true;
        $final_output['message'] = 'Purchase Request details retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);

    }
    function view_purchase_request_detail_get()
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
        $purchaseRequestID = isset($request_1->purchaseRequestID) ? $request_1->purchaseRequestID : null;
        $CompanyID_filter = '';
        if(!empty($customerCompanyID)){
            $customerCompany = array($customerCompanyID);
            $whereIN = "( " . join(",", $customerCompany) . " ) ";
            $CompanyID_filter = " srp_erp_purchaserequestdetails.companyID IN " . $whereIN;
        }
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('purchaseRequestID', trim($purchaseRequestID));
        $this->db->from('srp_erp_purchaserequestmaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,CONCAT_WS(\' - Part No : \',IF(LENGTH(srp_erp_purchaserequestdetails.itemDescription),srp_erp_purchaserequestdetails.itemDescription,NULL),IF(LENGTH(srp_erp_itemmaster.partNo),srp_erp_itemmaster.partNo,NULL))as Itemdescriptionpartno,'.$item_code.'');
        $this->db->where('purchaseRequestID', trim($purchaseRequestID));
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID');
        $data['detail'] = $this->db->get()->result_array();
        $final_output['data'] = $data;
        $final_output['success'] = true;
        $final_output['message'] = 'Purchase Request details retrieved.';
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