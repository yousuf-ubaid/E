<?php
use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_purchaseOrder extends REST_Controller
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
            $this->load->model('Procurement_modal');
            $this->load->library('sequence');
            $this->load->library('Approvals_mobile');
            $this->load->library('JWT');
            $this->load->library('S3');
            $this->load->library('Approvals');
            $this->load->helpers('procurement');
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
            $this->common_data['company_data']['company_name'] = $this->company_info->company_name;
            $this->common_data['company_data']['company_address1'] = $this->company_info->company_address1;
            $this->common_data['company_data']['company_address2'] = $this->company_info->company_address2;
            $this->common_data['company_data']['company_city'] = $this->company_info->company_city;
            $this->common_data['company_data']['company_country'] = $this->company_info->company_country;
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
    public static function company_name()
    {
        return self::get_instance()->company_info->company_name;
    } 
    public static function company_address1()
    {
        return self::get_instance()->company_info->company_address1;
    }
    public static function company_address2()
    {
        return self::get_instance()->company_info->company_address2;
    }
    public static function company_country()
    {
        return self::get_instance()->company_info->company_country;
    }
    public static function company_city()
    {
        return self::get_instance()->company_info->company_city;
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

    //-----------Purchase Order--------------//

    function save_purchase_order_header_post()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->post('expectedDeliveryDate'));
        $POdate = trim($this->post('POdate'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($POdate, $date_format_policy);
        $rcmApplicable = trim($this->post('rcmApplicable'));
        $rcmYN = trim($this->post('rcmYN'));
        $segment = explode('|', trim($this->post('segment')));
        $currency_code = explode('|', trim($this->post('currency_code')));
        $supplier_arr = fetch_supplier_data(trim($this->post('supplierPrimaryCode')));
        $ship_data = fetch_address_po(trim($this->post('shippingAddressID')));
        $sold_data = fetch_address_po(trim($this->post('soldToAddressID')));
        $invoice_data = fetch_address_po(trim($this->post('invoiceToAddressID')));
        $data['documentID'] = 'PO';
        $data['documentTaxType'] = $this->post('documentTaxType');
        $narration = ($this->post('narration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['transactionCurrency'] = trim($this->post('transactionCurrency'));
        $data['supplierPrimaryCode'] = trim($this->post('supplierPrimaryCode'));
        $data['purchaseOrderType'] = trim($this->post('purchaseOrderType'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($this->post('projectID'));
            $projectCurrencyExchangerate = currency_conversionID($this->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = trim($this->post('projectID'));
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['referenceNumber'] = trim($this->post('referenceNumber'));
        $data['creditPeriod'] = trim($this->post('creditPeriod'));
        $data['soldToAddressID'] = trim($this->post('soldToAddressID'));
        $data['shippingAddressID'] = trim($this->post('shippingAddressID'));
        $data['invoiceToAddressID'] = trim($this->post('invoiceToAddressID'));
        $data['supplierID'] = $supplier_arr['supplierAutoID'];
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $paymentTerms = ($this->post('paymentTerms'));
        $data['paymentTerms'] = str_replace('<br />', PHP_EOL, $paymentTerms);
        $penaltyTerms = ($this->post('penaltyTerms'));
        $data['penaltyTerms'] = str_replace('<br />', PHP_EOL, $penaltyTerms);
        $deliveryTerms = ($this->post('deliveryTerms'));
        $data['deliveryTerms'] = str_replace('<br />', PHP_EOL, $deliveryTerms);
        $data['shippingAddressID'] = $ship_data['addressID'];
        $data['shippingAddressDescription'] = trim($this->post('shippingAddressDescription'));
        $data['shipTocontactPersonID'] = $ship_data['contactPerson'];
        $data['shipTocontactPersonTelephone'] = $ship_data['contactPersonTelephone'];
        $data['shipTocontactPersonFaxNo'] = $ship_data['contactPersonFaxNo'];
        $data['shipTocontactPersonEmail'] = $ship_data['contactPersonEmail'];
        $data['invoiceToAddressID'] = $invoice_data['addressID'];
        $data['invoiceToAddressDescription'] = $invoice_data['addressDescription'];
        $data['invoiceTocontactPersonID'] = $invoice_data['contactPerson'];
        $data['invoiceTocontactPersonTelephone'] = $invoice_data['contactPersonTelephone'];
        $data['invoiceTocontactPersonFaxNo'] = $invoice_data['contactPersonFaxNo'];
        $data['invoiceTocontactPersonEmail'] = $invoice_data['contactPersonEmail'];
        $data['soldToAddressID'] = $sold_data['addressID'];
        $data['soldToAddressDescription'] = $sold_data['addressDescription'];
        $data['soldTocontactPersonID'] = $sold_data['contactPerson'];
        $data['soldTocontactPersonTelephone'] = $sold_data['contactPersonTelephone'];
        $data['soldTocontactPersonFaxNo'] = $sold_data['contactPersonFaxNo'];
        $data['soldTocontactPersonEmail'] = $sold_data['contactPersonEmail'];
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');
        $data['contactPersonName'] = trim($this->post('contactperson'));
        $data['contactPersonNumber'] = trim($this->post('contactnumber'));
        $data['transactionCurrencyID'] = trim($this->post('transactionCurrencyID'));
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $crTypes = explode('<table', $this->post('Note'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['termsandconditions'] = $notes;
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = date('y-m-d H:i:s');
            $data['isGroupBasedTax'] = ((getPolicyValues('GBT', 'All')==1)?1:0);
            $data['rcmApplicableYN'] = ((getPolicyValues('GBT', 'All')==1)?$rcmYN:0);
            $data['purchaseOrderCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_purchaseordermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                //$this->lib_log->log_event('Purchase Order','Error','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->post('desc') . ' Save Failed '.$this->db->_error_message(),'Purchase Order');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Saved Successfully.',
                    'last_id' => $last_id
                ], REST_Controller::HTTP_OK); 
        }
    }

    function update_purchase_order_header_post()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->post('expectedDeliveryDate'));
        $POdate = trim($this->post('POdate'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($POdate, $date_format_policy);
        $rcmApplicable = trim($this->post('rcmApplicable'));
        $rcmYN = trim($this->post('rcmYN'));
        $segment = explode('|', trim($this->post('segment')));
        $currency_code = explode('|', trim($this->post('currency_code')));
        $supplier_arr = fetch_supplier_data(trim($this->post('supplierPrimaryCode')));
        $ship_data = fetch_address_po(trim($this->post('shippingAddressID')));
        $sold_data = fetch_address_po(trim($this->post('soldToAddressID')));
        $invoice_data = fetch_address_po(trim($this->post('invoiceToAddressID')));
        $data['documentID'] = 'PO';
        $data['documentTaxType'] = $this->post('documentTaxType');
        $narration = ($this->post('narration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['transactionCurrency'] = trim($this->post('transactionCurrency'));
        $data['supplierPrimaryCode'] = trim($this->post('supplierPrimaryCode'));
        $data['purchaseOrderType'] = trim($this->post('purchaseOrderType'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($this->post('projectID'));
            $projectCurrencyExchangerate = currency_conversionID($this->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = trim($this->post('projectID'));
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['referenceNumber'] = trim($this->post('referenceNumber'));
        $data['creditPeriod'] = trim($this->post('creditPeriod'));
        $data['soldToAddressID'] = trim($this->post('soldToAddressID'));
        $data['shippingAddressID'] = trim($this->post('shippingAddressID'));
        $data['invoiceToAddressID'] = trim($this->post('invoiceToAddressID'));
        $data['supplierID'] = $supplier_arr['supplierAutoID'];
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $paymentTerms = ($this->post('paymentTerms'));
        $data['paymentTerms'] = str_replace('<br />', PHP_EOL, $paymentTerms);
        $penaltyTerms = ($this->post('penaltyTerms'));
        $data['penaltyTerms'] = str_replace('<br />', PHP_EOL, $penaltyTerms);
        $deliveryTerms = ($this->post('deliveryTerms'));
        $data['deliveryTerms'] = str_replace('<br />', PHP_EOL, $deliveryTerms);
        $data['shippingAddressID'] = $ship_data['addressID'];
        $data['shippingAddressDescription'] = trim($this->post('shippingAddressDescription'));
        $data['shipTocontactPersonID'] = $ship_data['contactPerson'];
        $data['shipTocontactPersonTelephone'] = $ship_data['contactPersonTelephone'];
        $data['shipTocontactPersonFaxNo'] = $ship_data['contactPersonFaxNo'];
        $data['shipTocontactPersonEmail'] = $ship_data['contactPersonEmail'];
        $data['invoiceToAddressID'] = $invoice_data['addressID'];
        $data['invoiceToAddressDescription'] = $invoice_data['addressDescription'];
        $data['invoiceTocontactPersonID'] = $invoice_data['contactPerson'];
        $data['invoiceTocontactPersonTelephone'] = $invoice_data['contactPersonTelephone'];
        $data['invoiceTocontactPersonFaxNo'] = $invoice_data['contactPersonFaxNo'];
        $data['invoiceTocontactPersonEmail'] = $invoice_data['contactPersonEmail'];
        $data['soldToAddressID'] = $sold_data['addressID'];
        $data['soldToAddressDescription'] = $sold_data['addressDescription'];
        $data['soldTocontactPersonID'] = $sold_data['contactPerson'];
        $data['soldTocontactPersonTelephone'] = $sold_data['contactPersonTelephone'];
        $data['soldTocontactPersonFaxNo'] = $sold_data['contactPersonFaxNo'];
        $data['soldTocontactPersonEmail'] = $sold_data['contactPersonEmail'];
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');
        $data['contactPersonName'] = trim($this->post('contactperson'));
        $data['contactPersonNumber'] = trim($this->post('contactnumber'));
        $data['transactionCurrencyID'] = trim($this->post('transactionCurrencyID'));
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $crTypes = explode('<table', $this->post('Note'));
        $notes = implode('<table class="edit-tbl" ', $crTypes);
        $data['termsandconditions'] = $notes;
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
      if (trim($this->post('purchaseOrderID'))) {
         $isGroupBasedTaxYn =  existTaxPolicyDocumentWise('srp_erp_purchaseordermaster', trim($this->post('purchaseOrderID')), 'PO', 'purchaseOrderID');
         $data['rcmApplicableYN'] = (($isGroupBasedTaxYn==1)?$rcmYN:0); ;
          $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
          $this->db->update('srp_erp_purchaseordermaster', $data);
          $this->db->trans_complete();
          if ($this->db->trans_status() === FALSE) {
              $this->db->trans_rollback();
              $this->response([
                    'success' => FALSE ,
                    'message' => 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
          } else {
                update_warehouse_items();
                update_item_master();
              $this->db->trans_commit();
              $this->response([
                'success' => TRUE,
                'message' => 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Updated Successfully.'
            ], REST_Controller::HTTP_OK); 
        }
      } else {
        $this->response([
            'success' => FALSE ,
            'message' => 'Purchase Order Not Found.'
        ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    function save_purchase_order_detail_post()
    {
        $item_data = json_decode(file_get_contents('php://input'));
        $this->db->trans_start();
        if(!empty($item_data)){
            $date_time = date('Y-m-d H:i:s');
            $iteminput_data = [];
            foreach ($item_data as $row){
        $this->db->select('documentTaxType');
        $this->db->where('purchaseOrderID', $row->purchaseOrderID);
        $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $row->purchaseOrderID);
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();

                $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$row->purchaseOrderID,'PO','purchaseOrderID');
                $item_arr = fetch_item_data($row->itemAutoID);
                $uomEx = explode('|', $row->uom);
                $itemdiscount = ($row->estimatedAmount / 100) * $row->discount;
                $itemunitAmount = ($row->estimatedAmount - $itemdiscount);
                
        $iteminput_data[] = [
                'purchaseOrderID' => $row->purchaseOrderID,
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
                'GRVSelectedYN' => 0,
                'goodsRecievedYN' => 0,
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

            $this->db->insert_batch('srp_erp_purchaseorderdetails', $iteminput_data);
            $last_id = $this->db->insert_id();
            // print_r($last_id);
            if(!empty($row->text_type)){
                if($group_based_tax == 1){ 
                    $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', $row->purchaseOrderID);

                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$row->text_type);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
                 
                    $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                    $this->db->where('purchaseOrderID', $row->purchaseOrderID);
                    $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();
            
                    $dataTax['purchaseOrderAutoID'] = trim($row->purchaseOrderID);
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
                    $dataTax['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataTax['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataTax['createdUserGroup'] = $this->common_data['user_group'];
                    $dataTax['createdPCID'] = $this->common_data['current_pc'];
                    $dataTax['createdUserID'] = $this->common_data['current_userID'];
                    $dataTax['createdUserName'] = $this->common_data['current_user'];
                    $dataTax['createdDateTime'] = $date_time;

                    tax_calculation_vat('srp_erp_purchaseordertaxdetails',$dataTax,$row->text_type,'purchaseOrderAutoID',trim($row->purchaseOrderID),($row->estimatedAmount*$row->quantityRequested),'PO',$last_id,($itemdiscount * $row->quantityRequested),1, $isRcmDocument);
               
                }else {
                    $taxCat = $this->db->query("SELECT taxPercentage, taxCategory FROM srp_erp_taxmaster WHERE taxMasterAutoID = {$row->text_type}")->row_array();
                    if($taxCat['taxCategory'] == 2) {
                        $vatSubCat = $this->db->query("SELECT percentage 
                                                        FROM srp_erp_tax_vat_sub_categories
                                                            JOIN srp_erp_itemmaster ON srp_erp_itemmaster.taxVatSubCategoriesAutoID = srp_erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID 
                                                        WHERE taxMasterAutoID = {$row->text_type}
                                                            AND itemAutoID = {$itemAutoID}")->row('percentage');
                        if($vatSubCat) {
                            $data['taxAmount'] = (($itemunitAmount * $row->quantityRequested) / 100) * $vatSubCat;
                        } else {
                            $suppliertaxPercentage = $this->db->query("SELECT vatPercentage 
                                                FROM srp_erp_suppliermaster 
                                                JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
                                                WHERE purchaseOrderID = {$row->purchaseOrderID}")->row_array();
                            $data['taxAmount'] = (($itemunitAmount * $row->quantityRequested) / 100) * $suppliertaxPercentage['vatPercentage'];
                        }     
                    } else {
                        $data['taxAmount'] = (($itemunitAmount * $row->quantityRequested) / 100) * $taxCat['taxPercentage'];
                    }
                    $data['isVAT'] = 1;
                    $data['taxCalculationformulaID'] = $row->text_type;
                    $this->db->where('purchaseOrderDetailsID', trim($last_id));
                    $this->db->update('srp_erp_purchaseorderdetails', $data);
                }

            }

            if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,generalDiscountAmount,generalDiscountPercentage');
                $this->db->where('purchaseOrderID', $row->purchaseOrderID);
                $this->db->from('srp_erp_purchaseordermaster');
                $currency = $this->db->get()->row_array();

                $amount = $this->db->query("SELECT
                                                    SUM(totalAmount) as totalAmount
                                            FROM
                                                `srp_erp_purchaseorderdetails`
                                                JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
                                                LEFT JOIN `srp_erp_taxcalculationformulamaster` ON `srp_erp_taxcalculationformulamaster`.`taxCalculationformulaID` = `srp_erp_purchaseorderdetails`.`taxCalculationformulaID` 
                                            WHERE
                                                `purchaseOrderID` = '{$row->purchaseOrderID}'
                                                GROUP BY
                                                purchaseOrderID")->row_array();

                $disc_foottotal= (($currency['generalDiscountPercentage'] / 100)* $amount['totalAmount']);
                $taxtotal_amount = ($amount['totalAmount'] - $disc_foottotal);
                $taxtotal=($taxtotal_amount);
                if($group_based_tax == 1){ 
                    tax_calculation_update_vat('srp_erp_purchaseordertaxdetails','purchaseOrderAutoID',$row->purchaseOrderID,$taxtotal,$disc_foottotal,'PO');
                }else { 
                    $this->update_po_generaltax($row->purchaseOrderID,$taxtotal);
                }
            } 
        }
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


    function fetch_po_detail_table_get()
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
        $purchaseOrderID = isset($request_1->purchaseOrderID) ? $request_1->purchaseOrderID : null;
        
        $secondaryCode = getPolicyValues('SSC', 'All'); 

        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,generalDiscountAmount,generalDiscountPercentage,purchaseOrderType');
        $this->db->where('purchaseOrderID', trim($purchaseOrderID));
        $this->db->from('srp_erp_purchaseordermaster');
        $data['currency'] = $this->db->get()->row_array();
        $documentID = (($data['currency']['purchaseOrderType'] == 'PR') ? 'PO-PRQ':'PO');

        update_group_based_tax('srp_erp_purchaseordermaster','purchaseOrderAutoID',trim($purchaseOrderID),'srp_erp_purchaseordertaxdetails','purchaseOrderID',$documentID);

        $data['isRcmDocument'] =  isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', trim($purchaseOrderID));
        
        $companyID = current_companyID();
        $purchaseOrderID = trim($purchaseOrderID);
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($purchaseOrderID),'PO','purchaseOrderID');
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $poAutoID = trim($purchaseOrderID);
        $lineWiseTaxDescription = 'IF(isVAT = 1, srp_erp_taxmaster.taxDescription,srp_erp_taxcalculationformulamaster.Description) ';
        if($group_based_tax == 1){
            $lineWiseTaxDescription = 'srp_erp_taxcalculationformulamaster.Description';
        }
        $data['extra'] = $this->Procurement_modal->fetch_template_data($purchaseOrderID);
        $purchaseorderdetails = array();
        $purchase_filter = '';
        if(!empty($purchaseOrderID)){
            $purchaseOrder = array($purchaseOrderID);
            $whereIN = "( " . join(",", $purchaseOrder) . " ) ";
            $purchase_filter = " srp_erp_purchaseorderdetails.purchaseOrderID IN " . $whereIN;
        }
        if($group_based_tax == 1){ 

        $purchaseorderdetails = $this->db->query("SELECT srp_erp_purchaseorderdetails.*, CONCAT_WS(' - Part No : ',IF ( LENGTH( srp_erp_purchaseorderdetails.itemDescription ), srp_erp_purchaseorderdetails.itemDescription, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), srp_erp_itemmaster.partNo, NULL )) AS Itemdescriptionpartno,IFNULL(srp_erp_taxcalculationformulamaster.Description ,'-') AS lineTaxDesc ,taxledger.taxDetailAutoID,srp_erp_purchaserequestmaster.purchaseRequestCode,'.$item_code.'
                                        FROM srp_erp_purchaseorderdetails
                                        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID
                                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID
                                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_purchaseorderdetails.taxCalculationformulaID
                                        LEFT JOIN srp_erp_purchaserequestmaster ON srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaseorderdetails.prMasterID
                                        LEFT JOIN (SELECT
                                                    documentDetailAutoID,
                                                    taxDetailAutoID
                                                    FROM
                                                    srp_erp_taxledger
                                                    where 
                                                    companyID = '.$companyID.' 
                                                    AND documentID = 'PO'
                                                    AND documentMasterAutoID  = '.$purchaseOrderID.' 
                                                    GROUP BY documentMasterAutoID,documentDetailAutoID)taxledger ON  taxledger.documentDetailAutoID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID
                              WHERE " . $purchase_filter . " ORDER BY purchaseOrderDetailsID DESC ")->result_array();
       
        }else {

            $this->db->query("UPDATE srp_erp_purchaseordermaster 
                                  SET rcmApplicableYN = 0
                                  WHERE
	                              purchaseOrderID ={$poAutoID} 
                                  AND companyID ={$companyID}");
            $data['isRcmDocument'] = 0;
            $purchaseorderdetails = $this->db->query("SELECT srp_erp_purchaseorderdetails.*, CONCAT_WS(' - Part No : ',IF ( LENGTH( srp_erp_purchaseorderdetails.itemDescription ), srp_erp_purchaseorderdetails.itemDescription, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), srp_erp_itemmaster.partNo, NULL )) AS Itemdescriptionpartno,IFNULL(srp_erp_taxcalculationformulamaster.Description ,'-') AS lineTaxDesc ,taxledger.taxDetailAutoID,srp_erp_purchaserequestmaster.purchaseRequestCode,'.$item_code.'
                                            FROM srp_erp_purchaseorderdetails
                                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID
                                            LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID
                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_purchaseorderdetails.taxCalculationformulaID
                                            LEFT JOIN srp_erp_purchaserequestmaster ON srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaseorderdetails.prMasterID
                                            WHERE " . $purchase_filter . " ORDER BY purchaseOrderDetailsID DESC ")->result_array();
        }
  
        
        $purchaseOrderID=$purchaseOrderID;
        $companyID=current_companyID();
        $data['tax_detail'] =$this->db->query("SELECT
                                    srp_erp_purchaseordertaxdetails.taxDescription,srp_erp_purchaseordertaxdetails.taxDetailAutoID,taxleg.amount,
                                    srp_erp_purchaseordertaxdetails.purchaseOrderAutoID
                                FROM
                                    srp_erp_purchaseordertaxdetails
                                INNER JOIN (
                                    SELECT
                                        SUM(amount) as amount,taxDetailAutoID
                                    FROM
                                        srp_erp_taxledger
                                    WHERE
                                        documentID = 'PO'
                                    AND documentMasterAutoID = $purchaseOrderID
                                GROUP BY documentMasterAutoID,taxDetailAutoID
                                ) taxleg ON srp_erp_purchaseordertaxdetails.taxDetailAutoID = taxleg.taxDetailAutoID
                                WHERE
                                    purchaseOrderAutoID = $purchaseOrderID
                                AND companyID = $companyID ")->result_array();
        $data['group_based_tax'] = $group_based_tax;
        
        $final_output['data'] = $data;
        $final_output['success'] = true;
        $final_output['message'] = 'Purchase Order details retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
        
    }
    function fetch_purchase_order_detail_get()
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
        $purchaseOrderDetailsID = isset($request_1->purchaseOrderDetailsID) ? $request_1->purchaseOrderDetailsID : null;
        $podetailsID_filter = '';
        if(!empty($purchaseOrderDetailsID)){
            $podetails = array($purchaseOrderDetailsID);
            $whereIN = "( " . join(",", $podetails) . " ) ";
            $podetailsID_filter = " srp_erp_purchaseorderdetails.purchaseOrderDetailsID IN " . $whereIN;
        }
        
        $podetailsarray = array();
        $podetailsarray = $this->db->query("SELECT srp_erp_purchaseorderdetails.*,srp_erp_itemmaster.seconeryItemCode,itemledgercurrent.currentstock AS itemledstock
                                        FROM srp_erp_purchaseorderdetails
                                        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID
                                        LEFT JOIN (SELECT IF (mainCategory = 'Inventory',  (SUM(transactionQTY/ convertionRate)),' ') AS currentstock, srp_erp_itemledger.itemAutoID 
                            FROM srp_erp_itemledger
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID 
                            WHERE srp_erp_itemledger.itemAutoID is not null
                            GROUP BY srp_erp_itemledger.itemAutoID 
                          )itemledgercurrent ON itemledgercurrent.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID 
                                        WHERE " . $podetailsID_filter . " ORDER BY purchaseOrderDetailsID DESC ")->result_array();
        $final_output['data']= $podetailsarray;
        $final_output['success'] = true;
        $final_output['message'] = 'Purchase Order details retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
       
    }
    
    function update_purchase_order_detail_post()
    {
        $text_type = $this->post('text_type');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',trim($this->post('purchaseOrderID')),'PO','purchaseOrderID');
        $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', trim($this->post('purchaseOrderID')));

        $this->db->select('documentTaxType,purchaseOrderType');
        $this->db->where('purchaseOrderID', $this->post('purchaseOrderID'));
        $documentTaxType = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->post('purchaseOrderID'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();

        $this->db->select('totalAmount');
        $this->db->where('purchaseOrderDetailsID', $this->post('purchaseOrderDetailsID'));
        $po_detail = $this->db->get('srp_erp_purchaseorderdetails')->row_array();

        $this->db->trans_start();
        $item_arr = fetch_item_data(trim($this->post('itemAutoID')));
        $uom = explode('|', $this->post('uom'));
        $data['purchaseOrderID'] = trim($this->post('purchaseOrderID'));
        $data['itemAutoID'] = trim($this->post('itemAutoID'));
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
        $data['modifiedDateTime'] = date('Y-m-d H:i:s');

        if (trim($this->post('purchaseOrderDetailsID'))) {
            $this->db->where('purchaseOrderDetailsID', trim($this->post('purchaseOrderDetailsID')));
            $this->db->update('srp_erp_purchaseorderdetails', $data);
            $this->db->trans_complete();

            if(!empty($text_type)){

                if($group_based_tax == 1){

                    if($documentTaxType['documentTaxType']==1 && !empty($text_type)){
                       // $group_based_tax = existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID');

                        $this->db->select('*');
                        $this->db->where('taxCalculationformulaID',$text_type);
                        $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
                        $this->db->where('purchaseOrderID', $this->post('purchaseOrderID'));
                        $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();

                        $dataTax['purchaseOrderAutoID'] = trim($this->post('purchaseOrderID'));
                        $dataTax['taxFormulaMasterID'] = $text_type;
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
                        $dataTax['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataTax['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataTax['createdUserGroup'] = $this->common_data['user_group'];
                        $dataTax['createdPCID'] = $this->common_data['current_pc'];
                        $dataTax['createdUserID'] = $this->common_data['current_userID'];
                        $dataTax['createdUserName'] = $this->common_data['current_user'];
                        $dataTax['createdDateTime'] = date('Y-m-d H:i:s');

                        if($documentTaxType['purchaseOrderType']=='PR'){
                            tax_calculation_vat(null,null,$text_type,'purchaseOrderAutoID',trim($this->post('purchaseOrderID')),(trim($this->post('estimatedAmount')) * trim($this->post('quantityRequested'))),'PO-PRQ',trim($this->post('purchaseOrderDetailsID')),($data['discountAmount']*$data['requestedQty']),1, $isRcmDocument);
                        } else {

                             tax_calculation_vat('srp_erp_purchaseordertaxdetails',$dataTax,$text_type,'purchaseOrderAutoID',trim($this->post('purchaseOrderID')),(trim($this->post('estimatedAmount')) * trim($this->post('quantityRequested'))),'PO',trim($this->post('purchaseOrderDetailsID')),($data['discountAmount']*$data['requestedQty']),1, $isRcmDocument);
                        }

                    } else {
                        fetchExistsDetailTBL('PO', trim($this->post('purchaseOrderID')),trim($this->post('purchaseOrderDetailsID')),'srp_erp_purchaseordertaxdetails',1,$data['totalAmount']);
                    }

                }else {

                    $itemAutoID = trim($this->post('itemAutoID'));
                    $purchaseOrderID = trim($this->post('purchaseOrderID'));
                    $data['taxAmount'] = 0;
                    $taxCat = $this->db->query("SELECT taxPercentage, taxCategory FROM srp_erp_taxmaster WHERE taxMasterAutoID = {$text_type}")->row_array();
                    if($taxCat['taxCategory'] == 2) {
                        $vatSubCat = $this->db->query("SELECT percentage 
                                                    FROM srp_erp_tax_vat_sub_categories
                                                        JOIN srp_erp_itemmaster ON srp_erp_itemmaster.taxVatSubCategoriesAutoID = srp_erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID 
                                                    WHERE taxMasterAutoID = {$text_type}
                                                        AND itemAutoID = {$itemAutoID}")->row('percentage');
                        if($vatSubCat) {
                            $data['taxAmount'] = ($data['totalAmount'] / 100) * $vatSubCat;
                        } else {
                            $suppliertaxPercentage = $this->db->query("SELECT vatPercentage 
                                            FROM srp_erp_suppliermaster 
                                            JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
                                            WHERE purchaseOrderID = {$purchaseOrderID}")->row_array();
                            $data['taxAmount'] = ($data['totalAmount'] / 100) * $suppliertaxPercentage['vatPercentage'];
                        }
                    } else {
                        $data['taxAmount'] = ($data['totalAmount'] / 100) * $taxCat['taxPercentage'];
                    }
                    $data['isVAT'] = 1;
                    $data['taxCalculationformulaID'] = $text_type;
                    $this->db->where('purchaseOrderDetailsID', trim($this->post('purchaseOrderDetailsID')));
                    $this->db->update('srp_erp_purchaseorderdetails', $data);



                    $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,generalDiscountAmount,generalDiscountPercentage');
                    $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                    $this->db->from('srp_erp_purchaseordermaster');
                    $currency = $this->db->get()->row_array();
                    $purchaseOrderID =  trim($this->post('purchaseOrderID'));
                    $amount = $this->db->query("SELECT
                                            SUM(totalAmount) as totalAmount
                                        FROM srp_erp_purchaseorderdetails
                                        JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_purchaseorderdetails.itemAutoID
                                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID
                                        WHERE
                                            `purchaseOrderID` = '{$purchaseOrderID}'
                                            GROUP BY
                                            purchaseOrderID")->row_array();

                    $disc_foottotal= (($currency['generalDiscountPercentage'] / 100)* $amount['totalAmount']);
                    $taxtotal_amount = ($amount['totalAmount'] - $disc_foottotal);
                    $taxtotal=($taxtotal_amount);
                    if($documentTaxType['documentTaxType']==0 && !empty($tax_detail)){
                        $taxtotal_amount = ($amount['totalAmount'] - $disc_foottotal);
                        $taxtotal=($taxtotal_amount);
                        $this->update_po_generaltax($this->post('purchaseOrderID'),$taxtotal);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                                    'success' => FALSE ,
                                    'message' => 'Purchase Order Details :  ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message()
                                ], REST_Controller::HTTP_NOT_FOUND);
                return array('e', 'Purchase Order Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                $this->response([
                                    'success' => TRUE,
                                    'message' => 'Purchase Order Details :  ' . $data['itemSystemCode'] . ' Updated Successfully.'
                                ], REST_Controller::HTTP_OK); 

            }
        }
    }
    
    function load_purchase_order_conformation_post()
    {
        $purchaseOrderID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->post('purchaseOrderID'));
        $data['extra'] = $this->Procurement_modal->fetch_template_data($purchaseOrderID);
        $data['approval'] = 2;
        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID')!=''?existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID'):0);

        $printFooterYN = 1;
        $data['printFooterYN'] = $printFooterYN;
        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PO', $purchaseOrderID);

        $data['isRcmDocument'] =  isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', $purchaseOrderID);
        $data['type'] = $this->post('html');

        if (!$this->post('html')) {
            $data['signature'] = $this->Procurement_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo'] = mPDFImage;
        if ($this->post('html')) {
            $data['logo'] = htmlImage;
        }
        $printlink = print_template_pdf('PO', 'system/procurement/erp_purchase_order_print');

        $html = $this->load->view($printlink, $data, true);

        if ($this->input->post('html')) {
            if ($printlink == 'system/procurement/erp_purchase_order_mubadrah') {
                echo $this->load->view('system/procurement/erp_purchase_order_print', $data, true);
            } else {
                echo $html;
            }
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }
    function fetch_purchase_order_get()
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
        $date_format_policy = date_format_policy();
        $datefrom = isset($request_1->datefrom) ? $request_1->datefrom : null;
        $dateto = isset($request_1->dateto) ? $request_1->dateto : null;
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = isset($request_1->purchaseOrderID) ? $request_1->purchaseOrderID : null;
        $status = isset($request_1->status) ? $request_1->status : null;
        $isReceived = isset($request_1->isReceived) ? $request_1->isReceived : null;
        $segmentID = isset($request_1->segmentID) ? $request_1->segmentID : null;
        $supplier_filter = '';
        $segment_filter = '';
        $isReceived_filter = '';
        if (!empty($supplier)) {
            $supplier = array(isset($request_1->purchaseOrderID) ? $request_1->purchaseOrderID : null);
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        if (!empty($segmentID)) {
            $segmentID = array(isset($request_1->segmentID) ? $request_1->segmentID : null);
            $whereIN = "( " . join("' , '", $segmentID) . " )";
            $segment_filter = " AND segmentID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND (closedYN = 1)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        if ($isReceived != 'all') {
            if ($isReceived == 0) {
                $isReceived_filter = " AND (isReceived = 0 AND approvedYN = 5)";
            } else if ($isReceived == 1) {
                $isReceived_filter = " AND (isReceived = 1)";
            } else if ($isReceived == 2) {
                $isReceived_filter = " AND (isReceived = 2 )";
            } else if ($isReceived == 3) {
                $isReceived_filter = " AND (closedYN = 1)";
            }
        }
        $sSearch = isset($request_1->sSearch) ? $request_1->sSearch : null;
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( purchaseOrderCode Like '%$search%' ESCAPE '!') OR ( purchaseOrderType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (documentDate Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%')) ";
        }

        $where = "srp_erp_purchaseordermaster.companyID = " . $companyid . $supplier_filter . $segment_filter . $date . $status_filter . $searches . $isReceived_filter . "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,purchaseOrderCode,narration,srp_erp_suppliermaster.supplierName as supliermastername,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'$convertFormat') AS expectedDeliveryDate,transactionCurrency,purchaseOrderType ,srp_erp_purchaseordermaster.createdUserID as createdUser,srp_erp_purchaseordermaster.transactionAmount,transactionCurrencyDecimalPlaces,(det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as total_value,ROUND((det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0),2) as detTransactionAmount,isDeleted,DATE_FORMAT(documentDate,'$convertFormat') AS documentDate,documentDate AS documentDatepofilter,isReceived,closedYN,srp_erp_purchaseordermaster.confirmedByEmpID as confirmedByEmp");
        $this->datatables->join('(SELECT SUM(totalAmount)+ifnull(SUM(taxAmount),0) as transactionAmount,purchaseOrderID,IFNULL(SUM(discountAmount),0) as discountAmount  FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->join('(SELECT ifnull(SUM(amount),0) as gentaxamount,documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = "PO" AND documentDetailAutoID is null AND companyID=' . $companyid . '  GROUP BY documentMasterAutoID) gentax', '(gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->datatables->add_column('po_detail', '$1', 'load_details(narration,supliermastername,expectedDeliveryDate,transactionCurrency,purchaseOrderType,documentDate,purchaseOrderID)');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('edit', '$1', 'load_po_action(purchaseOrderID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->add_column('isReceivedlbl', '$1', 'po_Recived(isReceived,closedYN,' . $isReceived . ')');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
        
    }

    function confirmation_Inventory_check_get()
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
        $purchaseOrderID = isset($request_1->purchaseOrderID) ? $request_1->purchaseOrderID : null;

        $budegtControl = getPolicyValues('BDC', 'All');
        $bdcval = 0;
        $inventoryparr = array();
        $companyID=current_companyID();
        $noninventoryparr = array();

            $this->db->select('documentDate,segmentID,companyReportingExchangeRate');
            $this->db->where('purchaseOrderID', trim($purchaseOrderID));
            $this->db->from('srp_erp_purchaseordermaster');
            $mastr = $this->db->get()->row_array();
            $this->db->select('*');
            $this->db->where('purchaseOrderID', trim($purchaseOrderID));
            $this->db->from('srp_erp_purchaseorderdetails');
            $record = $this->db->get()->result_array();
            
            if (empty($record)) {
                $this->response([
                    'success' => FALSE,
                    'message' => 'There are no records to confirm this document!'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                foreach ($record as $rec) {
                    $item_arr = fetch_item_data($rec['itemAutoID']);
                    if ($item_arr['mainCategory'] == 'Inventory') {
                        $itemAutoID = $rec['itemAutoID'];

                        if($item_arr['maximunQty']==0){
                            array_push($noninventoryparr, array("itemname" => $item_arr['itemSystemCode'] . " - " . $item_arr['itemName'], "Poqty" => $rec['requestedQty']));
                            $bdcval++;
                        }
                    }else if ($item_arr['mainCategory'] == 'Service' || $item_arr['mainCategory'] == 'Non Inventory') {

                        $sgmnt = $mastr['segmentID'];
                        $costGLAutoID = $item_arr['costGLAutoID'];

                        $docdt = $mastr['documentDate'];

                        //get finance year details using PO document Date
                        $financeyr = $this->db->query("SELECT
                    	companyFinanceYearID,beginingDate,endingDate
                    FROM
                    	srp_erp_companyfinanceyear
                    WHERE
                    	'$docdt' BETWEEN beginingDate and endingDate
                    AND companyID=$companyID")->row_array();
                                            $finYear = $financeyr['companyFinanceYearID'];

                                            $bgtamnt = $this->db->query("SELECT
                    	SUM(IFNULL(srp_erp_budgetdetail.companyReportingAmount, 0)) AS amount
                    FROM
                    	`srp_erp_budgetdetail`
                    LEFT JOIN srp_erp_budgetmaster ON srp_erp_budgetmaster.budgetAutoID =  srp_erp_budgetdetail.budgetAutoID
                    WHERE
                    		GLAutoID = $costGLAutoID
                    AND srp_erp_budgetdetail.segmentID = $sgmnt
                    AND companyFinanceYearID = $finYear
                    AND approvedYN = 1
                    AND srp_erp_budgetdetail.companyID=$companyID")->row_array();
                        if(empty($bgtamnt['amount'])){
                            $glcod=fetch_gl_account_desc($costGLAutoID);
                            array_push($inventoryparr, array("itemname" => $item_arr['itemSystemCode'] . " - " . $item_arr['itemName'], "Glcode" => $glcod['GLSecondaryCode'].'-'.$glcod['GLDescription'], "poamount" => $rec['totalAmount']));
                            $bdcval++;
                        }

                    }
                }
                if ($bdcval > 0) {
                    //return array("exceeded", 'error', $inventoryparr, $noninventoryparr);
                    $this->response([
                        'success' => FALSE,
                        'message' => 'exceeded'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }else{
                    // return array("success", 'success', 0, 0);
                    $this->response([
                        'success' => TRUE,
                        'message' => 'success'
                    ], REST_Controller::HTTP_OK);
                }
            }
    }
    function purchase_order_confirmation_post()
    {   
        $companyID = current_companyID();
        $purchaseOrderID = trim($this->post('purchaseOrderID'));
        $amountBasedApproval = getPolicyValues('ABA', 'All');
        if($amountBasedApproval == 1) {
            $documentTotal = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, srp_erp_purchaseordermaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency,
            ( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                    FROM srp_erp_purchaseordermaster
                        LEFT JOIN ( SELECT SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                        LEFT JOIN (
                                SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                FROM srp_erp_taxledger 
                                WHERE documentID = 'PO' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                                GROUP BY documentMasterAutoID 
                        ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
                    WHERE
                        srp_erp_purchaseordermaster.purchaseOrderID = {$purchaseOrderID} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row_array();

            $poLocalAmount = $documentTotal['total_value'] /$documentTotal['companyLocalExchangeRate'];
            $amountApprovable = amount_based_approval('PO', $poLocalAmount);
            if($amountApprovable['type'] == 'e') {
                $this->response([
                    'success' => FALSE,
                    'message' => 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PO Value'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }

        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $currentuser = current_userID();
        $this->db->select('*');
        $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
        $this->db->from('srp_erp_purchaseorderdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->response([
                'success' => FALSE,
                'message' => 'There are no records to confirm this document!'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {

            $this->db->select('purchaseOrderID');
            $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_purchaseordermaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->response([
                    'success' => FALSE,
                    'message' => 'Document already confirmed'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {

                $budegtControl = getPolicyValues('BDC', 'All');
                $bdcval = 0;
                $inventoryparr = array();
                $noninventoryparr = array();
                if ($budegtControl == 1) {
                    $this->db->select('documentDate,segmentID,companyReportingExchangeRate,companyReportingCurrencyDecimalPlaces');
                    $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                    $this->db->from('srp_erp_purchaseordermaster');
                    $mastr = $this->db->get()->row_array();
                    foreach ($record as $rec) {
                        $item_arr = fetch_item_data($rec['itemAutoID']);
                        if ($item_arr['mainCategory'] == 'Inventory') {
                            $itemAutoID = $rec['itemAutoID'];

                            $itemlegqty = $this->db->query("SELECT IFNULL(SUM(transactionQTY/convertionRate),0) as qty FROM srp_erp_itemledger WHERE itemAutoID=$itemAutoID AND companyID=$companyID")->row_array();
                            $totqty = $itemlegqty['qty'] + $rec['requestedQty'];
                            if($item_arr['maximunQty']>0){
                                if ($totqty > $item_arr['maximunQty']) {
                                    array_push($noninventoryparr, array("itemname" => $item_arr['itemSystemCode'] . " - " . $item_arr['itemName'], "consumption" => $totqty, "budgetamount" => $item_arr['maximunQty']));
                                    $bdcval++;
                                }
                            }
                        }
                    }
                    $purchaseOrderID=$this->post('purchaseOrderID');
                    $records = $this->db->query("SELECT srp_erp_purchaseorderdetails.itemAutoID,SUM(totalAmount) AS totalAmount FROM srp_erp_purchaseorderdetails LEFT JOIN srp_erp_itemmaster ON srp_erp_purchaseorderdetails.itemAutoID = srp_erp_itemmaster.itemAutoID WHERE purchaseOrderID=$purchaseOrderID GROUP BY srp_erp_itemmaster.costGLAutoID")->result_array();
                    foreach ($records as $rec) {
                        $item_arr = fetch_item_data($rec['itemAutoID']);

                        if ($item_arr['mainCategory'] == 'Service' || $item_arr['mainCategory'] == 'Non Inventory') {
                            $costGLAutoID = $item_arr['costGLAutoID'];
                            $grvamnt = 0;
                            $bsiamnt = 0;
                            $dnamnt = 0;
                            $pvamnt = 0;
                            $budgetamount = 0;
                            $consumtionAmount = '';

                            $sgmnt = $mastr['segmentID'];
                            $docdt = $mastr['documentDate'];

                            //get finance year details using PO document Date
                            $financeyr = $this->db->query("SELECT companyFinanceYearID,beginingDate,endingDate FROM srp_erp_companyfinanceyear WHERE '$docdt' BETWEEN beginingDate and endingDate AND companyID=$companyID")->row_array();
                            $finYear = $financeyr['companyFinanceYearID'];
                            $beginingDate=$financeyr['beginingDate'];
                            $endingDate=$financeyr['endingDate'];

                            //get consumption amount
                            $cousumtnamnt = $this->db->query('SELECT SUM(companyReportingAmount) AS rptamnt FROM srp_erp_generalledger WHERE   companyID="' . $companyID . '" AND GLAutoID="' . $costGLAutoID . '" AND  segmentID="' . $sgmnt . '" AND documentDate BETWEEN "' . $beginingDate . '" AND "' . $endingDate . '" ')->row_array();
                            $consumtionAmount=($rec['totalAmount'] / $mastr['companyReportingExchangeRate']);
                            if(!empty($cousumtnamnt)){
                                $consumtionAmount=$cousumtnamnt['rptamnt']+ ($rec['totalAmount'] / $mastr['companyReportingExchangeRate']);
                            }

                            $bgtamnt = $this->db->query("SELECT SUM(IFNULL(srp_erp_budgetdetail.companyReportingAmount, 0)) AS amount FROM `srp_erp_budgetdetail` LEFT JOIN srp_erp_budgetmaster ON srp_erp_budgetmaster.budgetAutoID =  srp_erp_budgetdetail.budgetAutoID WHERE GLAutoID = $costGLAutoID AND srp_erp_budgetdetail.segmentID = $sgmnt AND companyFinanceYearID = $finYear AND approvedYN = 1 AND srp_erp_budgetdetail.companyID=$companyID")->row_array();

                            if (!empty($bgtamnt['amount'])) {
                                $budgetamount = $bgtamnt['amount']*-1;

                                if ($budgetamount == '') {
                                    $budgetamount = 0;
                                }
                                if ($consumtionAmount > $budgetamount) {
                                    $exceeded=$consumtionAmount-$budgetamount;
                                    $glcod=fetch_gl_account_desc($costGLAutoID);
                                    array_push($inventoryparr, array( "consumption" => round($consumtionAmount,$mastr['companyReportingCurrencyDecimalPlaces']), "glCode" => $glcod['GLSecondaryCode'].'-'.$glcod['GLDescription'], "budgetamount" => round($budgetamount,$mastr['companyReportingCurrencyDecimalPlaces']), "exceededamnt" => round($exceeded,$mastr['companyReportingCurrencyDecimalPlaces'])));
                                    $bdcval++;
                                }
                            }
                        }
                    }
                }
                if ($bdcval == 0) {
                    $this->load->library('Approvals');
                    $this->db->select('purchaseOrderCode,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseOrderID,transactionCurrencyDecimalPlaces,documentDate,DATE_FORMAT(documentDate, "%Y") as invYear,DATE_FORMAT(documentDate, "%m") as invMonth,documentID,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
                    $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                    $this->db->from('srp_erp_purchaseordermaster');
                    $po_data = $this->db->get()->row_array();
                    $docDate = $po_data['documentDate'];

                    $Comp = current_companyID();
                    $companyFinanceYearID = $this->db->query("SELECT
	                                                              period.companyFinanceYearID as companyFinanceYearID
                                                                  FROM
	                                                              srp_erp_companyfinanceperiod period
                                                                  WHERE
	                                                              period.companyID = $Comp
                                                                  AND '$docDate' BETWEEN period.dateFrom
                                                                  AND period.dateTo")->row_array();

                    if (empty($companyFinanceYearID['companyFinanceYearID'])) {
                        $companyFinanceYearID['companyFinanceYearID'] = NULL;
                    }

                    $this->load->library('sequence');
                    if ($po_data['purchaseOrderCode'] == "0" || empty($po_data['purchaseOrderCode'])) {
                        if ($locationwisecodegenerate == 1) {
                            $this->db->select('locationID');
                            $this->db->where('EIdNo', $currentuser);
                            $this->db->where('Erp_companyID', $companyID);
                            $this->db->from('srp_employeesdetails');
                            $location = $this->db->get()->row_array();
                            if ((empty($location)) || ($location == '')) {
                                //$this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                $this->response([
                                    'success' => FALSE,
                                    'message' => 'Location is not assigned for current employee'
                                ], REST_Controller::HTTP_NOT_FOUND);
                            } else {
                                if ($locationemployee != '') {
                                    $codegeratorpo = $this->sequence->sequence_generator_location($po_data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $locationemployee, $po_data['invYear'], $po_data['invMonth']);
                                } else {
                                    $this->response([
                                        'success' => FALSE,
                                        'message' => 'Location is not assigned for current employee'
                                    ], REST_Controller::HTTP_NOT_FOUND);
                                }
                            }
                        } else {
                            if($companyFinanceYearID['companyFinanceYearID'] == NULL) {
                                $this->response([
                                    'success' => FALSE,
                                    'message' => 'Financial Year Not generated For this Document Date!'
                                ], REST_Controller::HTTP_NOT_FOUND);

                            } else if($po_data['invYear'] == null) {
                                $this->response([
                                    'success' => FALSE,
                                    'message' => 'Document Year Not Found For this Document!'
                                ], REST_Controller::HTTP_NOT_FOUND);
                                
                            } else if ($po_data['invMonth'] == null){
                                $this->response([
                                    'success' => FALSE,
                                    'message' => 'Document Month Not Found For this Document!'
                                ], REST_Controller::HTTP_NOT_FOUND);
                            } else {
                                $codegeratorpo = $this->sequence->sequence_generator_fin($po_data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $po_data['invYear'], $po_data['invMonth']);
                            }
                        }

                        $validate_code = validate_code_duplication($codegeratorpo, 'purchaseOrderCode', $purchaseOrderID,'purchaseOrderID', 'srp_erp_purchaseordermaster');
                        if(!empty($validate_code)) {
                            $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                            $this->response([
                                'success' => FALSE,
                                'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                            ], REST_Controller::HTTP_NOT_FOUND);
                        }

                        $pvCd = array(
                            'purchaseOrderCode' => $codegeratorpo
                        );
                        $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                        $this->db->update('srp_erp_purchaseordermaster', $pvCd);
                    } else {
                        $validate_code = validate_code_duplication($po_data['purchaseOrderCode'], 'purchaseOrderCode', $purchaseOrderID,'purchaseOrderID', 'srp_erp_purchaseordermaster');
                        if(!empty($validate_code)) {
                            $this->response([
                                'success' => FALSE,
                                'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                            ], REST_Controller::HTTP_NOT_FOUND);
                        }
                    }
                    $this->load->library('Approvals');
                    $this->db->select('purchaseOrderCode,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseOrderID,transactionCurrencyDecimalPlaces,documentDate,generalDiscountPercentage');
                    $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                    $this->db->from('srp_erp_purchaseordermaster');
                    $po_master = $this->db->get()->row_array();

                    $autoApproval = get_document_auto_approval('PO');

                    if ($autoApproval == 0) {
                        $approvals_status = $this->approvals->auto_approve($po_master['purchaseOrderID'], 'srp_erp_purchaseordermaster', 'purchaseOrderID', 'PO', $po_master['purchaseOrderCode'], $po_master['documentDate']);
                    } elseif ($autoApproval == 1) {
                        $approvals_status = $this->approvals->CreateApproval('PO', $po_master['purchaseOrderID'], $po_master['purchaseOrderCode'], 'Purchase Order', 'srp_erp_purchaseordermaster', 'purchaseOrderID', 0, $po_master['documentDate']);
                    } else {
                        $this->response([
                            'success' => FALSE,
                            'message' => 'Approval levels are not set for this document'
                        ], REST_Controller::HTTP_NOT_FOUND);
                    }
                    if ($approvals_status == 1) {
                        $this->db->select('(SUM(totalAmount) + IFNULL( SUM(taxAmount),0) )AS totalAmount');
                        $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                        $po_total = $this->db->get('srp_erp_purchaseorderdetails')->row('totalAmount');
                        $discountVal = 0;
                        if ($po_master['generalDiscountPercentage'] > 0) {
                            $discountVal = ($po_master['generalDiscountPercentage'] / 100) * $po_total;
                        }
                        $this->db->select('totalAmount,purchaseOrderDetailsID');
                        $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                        $po_dtls = $this->db->get('srp_erp_purchaseorderdetails')->result_array();
                        foreach ($po_dtls as $val) {
                            $discountamnt = ($val['totalAmount'] / $po_total) * $discountVal;
                            $dataD = array(
                                'generalDiscountAmount' => $discountamnt,
                            );
                            $this->db->where('purchaseOrderDetailsID', trim($val['purchaseOrderDetailsID'] ?? ''));
                            $this->db->update('srp_erp_purchaseorderdetails', $dataD);
                        }
                       
                        $this->db->select('totalAmount,purchaseOrderDetailsID');
                        $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                        $po_dtls = $this->db->get('srp_erp_purchaseorderdetails')->result_array();
                        $purchaseOrderID=$this->post('purchaseOrderID');
                        $gentax = $this->db->query("SELECT ifnull(SUM(amount), 0) AS gentaxamount, documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = 'PO' AND documentDetailAutoID is null AND companyID = 13 AND documentMasterAutoID = $purchaseOrderID GROUP BY documentMasterAutoID")->row_array();
                        $lineTax=0;
                        if(!empty($gentax)){
                            foreach ($po_dtls as $val) {

                                $lineTax=($val['totalAmount']/$po_total)*$gentax['gentaxamount'];

                                $dataD = array(
                                    'generalTaxAmount' => $lineTax,
                                );
                                $this->db->where('purchaseOrderDetailsID', trim($val['purchaseOrderDetailsID'] ?? ''));
                                $this->db->update('srp_erp_purchaseorderdetails', $dataD);
                            }
                        }
                        $gentaxTot = $this->db->query("SELECT ifnull(SUM(amount), 0) AS gentaxamount, documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = 'PO' AND companyID = 13 AND documentMasterAutoID = $purchaseOrderID GROUP BY documentMasterAutoID")->row_array();
                        $taxtotamnt=$gentaxTot['gentaxamount'];
                        $autoApproval = get_document_auto_approval('PO');
                        if ($autoApproval == 0) {
                            $data = array(
                                'generalDiscountAmount' => $discountVal,
                                'transactionAmount' => round(($po_total - $discountVal)+$taxtotamnt, $po_data['transactionCurrencyDecimalPlaces']),
                                'companyLocalAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyLocalExchangeRate']), $po_data['companyLocalCurrencyDecimalPlaces']),
                                'companyReportingAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyReportingExchangeRate']), $po_data['companyReportingCurrencyDecimalPlaces']),
                                'supplierCurrencyAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['supplierCurrencyExchangeRate']), $po_data['supplierCurrencyDecimalPlaces']),
                                'isReceived' => 0,
                            );
                            $this->db->where('purchaseOrderID', trim($this->post('purchaseOrderID')));
                            $this->db->update('srp_erp_purchaseordermaster', $data);
                            $result = $this->save_purchase_order_approval(0, $po_master['purchaseOrderID'], 1, 'Auto Approved');
                            if ($result) {
                                $this->response([
                                    'success' => FALSE,
                                    'message' => 'Approvals Created Successfully '
                                ], REST_Controller::HTTP_NOT_FOUND);
                            }
                        } else {
                            $data = array(
                                'confirmedYN' => 1,
                                'approvedYN' => 0,
                                'confirmedDate' => date('y-m-d H:i:s'),
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user'],
                                'generalDiscountAmount' => $discountVal,
                                'transactionAmount' => round(($po_total - $discountVal)+$taxtotamnt, $po_data['transactionCurrencyDecimalPlaces']),
                                'companyLocalAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyLocalExchangeRate']), $po_data['companyLocalCurrencyDecimalPlaces']),
                                'companyReportingAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['companyReportingExchangeRate']), $po_data['companyReportingCurrencyDecimalPlaces']),
                                'supplierCurrencyAmount' => round(((($po_total - $discountVal)+$taxtotamnt) / $po_data['supplierCurrencyExchangeRate']), $po_data['supplierCurrencyDecimalPlaces']),
                                'isReceived' => 0,
                            );
                            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
                            $this->db->update('srp_erp_purchaseordermaster', $data);
                            $this->response([
                                'success' => TRUE,
                                'message' => 'Approvals Created Successfully'
                            ], REST_Controller::HTTP_OK);
                        }
                    } else {
                        $this->response([
                            'success' => FALSE,
                            'message' => 'Error'
                        ], REST_Controller::HTTP_NOT_FOUND);
                    }
                } else {
                    return array("exceeded", 'error', $inventoryparr, $noninventoryparr);
                    $this->response([
                        'success' => FALSE,
                        'message' => 'exceeded'
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

