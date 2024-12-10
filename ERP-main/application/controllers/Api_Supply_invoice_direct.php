<?php

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_Supply_invoice_direct extends REST_Controller
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
            $this->load->model('Payable_modal');
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


    //-----------Supply Invoice Direct--------------//

    function fetch_supplier_invoices_get()
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
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        
        $datefrom = isset($request_1->datefrom) ? $request_1->datefrom : null;
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = isset($request_1->dateto) ? $request_1->dateto : null;
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = isset($request_1->supplierPrimaryCode) ? $request_1->supplierPrimaryCode : null;
        $status = isset($request_1->status) ? $request_1->status : null;
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }

            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $sSearch = isset($request_1->sSearch) ? $request_1->sSearch : null;
        $searches='';
        $where = "srp_erp_paysupplierinvoicemaster.companyID=" . $companyid . $supplier_filter . $date . $status_filter . $searches .  "";
        $SupplyInvoice = array();
        $SupplyInvoice = $this->db->query("SELECT bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,confirmedYN,approvedYN,srp_erp_paysupplierinvoicemaster.createdUserID as createdUser,
        CASE WHEN invoiceType = 'GRV Base' THEN 'GRV Base' WHEN invoiceType = 'StandardPO' THEN 'PO Invoice' WHEN invoiceType = 'StandardItem' THEN 'Direct Item' WHEN invoiceType = 'Standard' THEN 'Direct Item' 	WHEN invoiceType = 'StandardExpense' THEN 'Direct Expense' ELSE invoiceType END AS invoiceType, srp_erp_suppliermaster.supplierName as suppliermastername,transactionCurrencyDecimalPlaces,((IFNULL( det.transactionAmount, 0 )) - (( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL( det.transactionAmount, 0 ) + IFNULL( taxAmount, 0 )) ))) + IFNULL(taxAmount, 0 ) AS total_value,((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))) as total_value_search,isDeleted,srp_erp_paysupplierinvoicemaster.confirmedByEmpID as confirmedByEmp,srp_erp_paysupplierinvoicemaster.isSytemGenerated as isSytemGenerated,IFNULL(srp_erp_paysupplierinvoicemaster.supplierInvoiceNo,'-') AS supplierInvoiceNo,IFNULL( DATE_FORMAT(invoiceDate,'$convertFormat'),'-') AS supplierInvoiceDate, srp_erp_paysupplierinvoicemaster.RefNo AS RefNo 
        FROM srp_erp_paysupplierinvoicemaster
            LEFT JOIN (SELECT SUM(transactionAmount) as transactionAmount,SUM(taxAmount) as taxAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det ON (det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)
            LEFT JOIN (SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet ON (addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)
            LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID
            WHERE ".$where. "")->result_array();
        $final_output['data'] = $SupplyInvoice;
        $final_output['success'] = true;
        $final_output['message'] = 'Supply Invoice list retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function save_supplier_invoice_header_post()
    {
        $this->db->trans_start();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $date_format_policy = date_format_policy();
        $bookDate = $this->post('bookingDate');
        $bookingDate = input_format_date($bookDate, $date_format_policy);
        $supplirInvDuDate = $this->post('supplierInvoiceDueDate');
        $supplierInvoiceDueDate = input_format_date($supplirInvDuDate, $date_format_policy);
        $supplierid = $this->post('supplierID');
        $supplierinvoice = $this->post('supplier_invoice_no');
        $po_id = trim($this->post('priamryPo'));

        $rcmApplicable = trim($this->post('rcmApplicable'));
        $rcmYN = trim($this->post('rcmYN'));

        $supplirinvoiceDate = $this->post('invoiceDate');
        $supplierinvoiceDate_new = input_format_date($supplirinvoiceDate, $date_format_policy);
        $invoiceautoid = $this->post('InvoiceAutoID');
        $currency_code = explode('|', trim($this->post('currency_code')));
        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->post('companyFinanceYear')));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($bookingDate);
            if (empty($financeYearDetails)) {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Finance period not found for the selected document date'
                ], REST_Controller::HTTP_NOT_FOUND);
                exit;
            } else {
                $FYBegin = $financeYearDetails['beginingDate'];
                $FYEnd = $financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails = get_financial_period_date_wise($bookingDate);

            if (empty($financePeriodDetails)) {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Finance period not found for the selected document date'
                ], REST_Controller::HTTP_NOT_FOUND);
                exit;
            } else {
                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        $supplier_arr = fetch_supplier_data(trim($this->post('supplierID')));
        $data['invoiceType'] = trim($this->post('invoiceType'));
        $data['bookingDate'] = trim($bookingDate);
        $data['invoiceDueDate'] = trim($supplierInvoiceDueDate);
        $data['invoiceDate'] = trim($supplierinvoiceDate_new);
        $data['companyFinanceYearID'] = trim($this->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->post('financeyear_period'));
        $data['documentID'] = 'BSI';
        $data['supplierID'] = trim($supplierid);
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
        $data['supplierInvoiceDate'] = trim($this->post('supplierInvoiceDueDate'));
        $data['transactionCurrency'] = trim($this->post('transactionCurrency'));

        if($po_id){
            $data['purchaseOrderIDMaster'] = $po_id;

            //fetch po detail
            if($po_id){
                $po_details = $this->fetch_po_details($po_id);
                if($po_details){
                    $data['purchaseOrderDetails'] = $po_details['purchaseOrderCode'].'|'.$po_details['supplierCode'].'|'.$po_details['referenceNumber'];
                }
            }
           
        }
        
        $data['segmentID'] = trim($this->post('segment'));
        $data['RefNo'] = trim($this->post('referenceno'));
        $comments = ($this->post('comments'));
        $data['comments'] = str_replace('<br />', PHP_EOL, $comments);
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

        $data['rcmApplicableYN'] =$rcmYN;
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');
        if (trim($this->post('InvoiceAutoID'))) {
            $this->db->where('InvoiceAutoID', trim($this->post('InvoiceAutoID')));
            $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

            if (!empty($supplierinvoice) || $supplierinvoice != '') {
                $q = "SELECT
                      supplierInvoiceNo,InvoiceAutoID
                FROM
                    srp_erp_paysupplierinvoicemaster
                WHERE
               InvoiceAutoID!='" . $invoiceautoid . "' AND supplierID = '" . $supplierid . "' AND  supplierInvoiceNo = '" . $supplierinvoice . "' ";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $this->db->trans_rollback();
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Supplier Invoice Number already exist for the selected supplier'
                    ], REST_Controller::HTTP_NOT_FOUND);
                } else {
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $this->response([
                            'success' => FALSE ,
                            'message' => 'Supplier Invoice : ' . $data['documentID'] . ' Update Failed ' . $this->db->_error_message()
                        ], REST_Controller::HTTP_NOT_FOUND);
                    } else {
                        $this->db->trans_commit();
                        $this->response([
                            'success' => TRUE,
                            'message' => 'Supplier Invoice : ' . $data['documentID'] . ' Updated Successfully.',
                            'last_id' => $this->post('InvoiceAutoID')
                        ], REST_Controller::HTTP_OK);
                    }
                }
            } else {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Supplier Invoice : ' . $data['documentID'] . ' Update Failed ' . $this->db->_error_message()
                    ], REST_Controller::HTTP_NOT_FOUND);
                } else {
                    $this->db->trans_commit();
                    $this->response([
                        'success' => TRUE,
                        'message' => 'Supplier Invoice : ' . $data['documentID'] . ' Updated Successfully.',
                        'last_id' => $this->post('InvoiceAutoID')
                    ], REST_Controller::HTTP_OK);
                }
            }
        } else {
            if (!empty($supplierinvoice) || $supplierinvoice != '') {
                $q = "SELECT
                    supplierInvoiceNo,supplierID
                FROM
                    srp_erp_paysupplierinvoicemaster
                WHERE
                 supplierID = '" . $supplierid . "'  AND supplierInvoiceNo = '" . $supplierinvoice . "'";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $this->db->trans_rollback();
                    $this->response([
                        'success' => FALSE ,
                        'message' => ' Supplier Invoice Number already exist for the selected supplier'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            }
            //$this->load->library('sequence');

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = date('y-m-d H:i:s');
            $data['bookingInvCode'] = 0;
            if ((trim($this->post('invoiceType')) == 'StandardItem') || (trim($this->post('invoiceType')) == 'Standard')) {
                $data['isGroupBasedTax'] = ((getPolicyValues('GBT', 'All') == 1) ? 1 : 0);
            }

            $this->db->insert('srp_erp_paysupplierinvoicemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Supplier Invoice   Saved Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Supplier Invoice Saved Successfully.',
                    'last_id' => $last_id
                ], REST_Controller::HTTP_OK);
            }


        }
    }
    function save_bsi_detail_multiple_post()
    {
        $item_data = json_decode(file_get_contents('php://input'));
        $this->db->trans_start();
        if(!empty($item_data)){
            $date_time = date('Y-m-d H:i:s');
            $iteminput_data = [];
        foreach ($item_data as $row){

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $row->InvoiceAutoID);
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();
        $isRcmDocument = isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', $row->InvoiceAutoID);
        $gl_codes = $row->gl_code;
        $amount = $row->amount;
        $projectExist = project_is_exist();
        $discountPercentage = $row->discountPercentage;
        $tax_type = $row->tax_type;
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $row->InvoiceAutoID, 'BSI', 'InvoiceAutoID');
            $segment = explode('|', $row->segment_gl);
            $gl_code = explode('|', $row->gl_code_des);
            $itemDiscount = trim(($row->amount * $row->discountPercentage) / 100);
            $Transactn_amount = round($row->amount - $itemDiscount, $master['transactionCurrencyDecimalPlaces']);
            $projectCurrency = project_currency($row->projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            if ($projectExist == 1) {
                
                $projectID = $row->projectID;
                $project_categoryID = $row->project_categoryID;
                $project_subCategoryID = $row->project_subCategoryID;
            }
            $companyLocalAmount = 0;
            if ($master['companyLocalExchangeRate']) {
                $companyLocalAmount = $Transactn_amount / $master['companyLocalExchangeRate'];
            }
            $companyReportingAmount = 0;
            if ($master['companyReportingExchangeRate']) {
                $companyReportingAmount = $Transactn_amount / $master['companyReportingExchangeRate'];
            }
            $supplierAmount = 0;
            if ($master['supplierCurrencyExchangeRate']) {
                $supplierAmount = $Transactn_amount / $master['supplierCurrencyExchangeRate'];
            }
        $iteminput_data[] = [
            'InvoiceAutoID' => trim($row->InvoiceAutoID),
            'GLAutoID' => $row->gl_code,
            'projectID' => $row->projectID,
            'projectExchangeRate' => $projectCurrencyExchangerate['conversion'],
            'project_categoryID' => $row->project_categoryID,
            'project_subCategoryID' => $row->project_subCategoryID,
            'systemGLCode' => trim($gl_code[0] ?? ''),
            'GLCode' => trim($gl_code[1] ?? ''),
            'GLDescription' => trim($gl_code[2] ?? ''),
            'GLType' => trim($gl_code[3] ?? ''),
            'segmentID' => trim($segment[0] ?? ''),
            'segmentCode' => trim($segment[1] ?? ''),
            'description' => $row->description,
            'discountPercentage' => trim($row->discountPercentage),
            'discountAmount' => $itemDiscount,
            'transactionAmount' => $Transactn_amount,
            'transactionExchangeRate' => $master['transactionExchangeRate'],
            'companyLocalAmount' => round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']),
            'companyLocalExchangeRate' => $master['companyLocalExchangeRate'],
            'companyReportingAmount' => round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']),
            'companyReportingExchangeRate' => $master['companyReportingExchangeRate'],
            'supplierAmount' => round($supplierAmount, $master['supplierCurrencyDecimalPlaces']),
            'supplierCurrencyExchangeRate' => $master['supplierCurrencyExchangeRate'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'companyID' => $this->common_data['company_data']['company_id'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdDateTime' => $date_time
        ];
        }
        $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $iteminput_data);
        $last_id = $this->db->insert_id();
            if (!empty($row->tax_type)) {
                if ($group_based_tax == 1) {
                    tax_calculation_vat(null, null, $row->tax_type, 'InvoiceAutoID', trim($row->InvoiceAutoID), $row->amount, 'BSI', $last_id, $itemDiscount, 1,$isRcmDocument);
                }
            }

        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->response([
                'success' => FALSE ,
                'message' => 'Supplier Invoice Detail : Saved Failed '
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->db->trans_commit();
            $this->response([
                'success' => TRUE,
                'message' => 'Supplier Invoice Detail : Saved Successfully.',
            ], REST_Controller::HTTP_OK);
        }

    }
    function save_general_discount_post()
    {
        $discountPercentage = $this->post('discountPercentage');
        $discounttotal = $this->post('discounttotal');

        $discountAmount = ($discountPercentage / 100) * $discounttotal;
        if ($discountAmount > 0) {
            if ($discountAmount < $discounttotal) {
                $data['generalDiscountAmount'] = $discountAmount;
                $data['generalDiscountPercentage'] = $discountPercentage;

                $this->db->where('InvoiceAutoID', trim($this->post('InvoiceAutoID')));
                $result = $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                if ($result) {
                    $this->response([
                        'success' => TRUE,
                        'message' => 'Discount Amount successfully added.',
                        'last_id' => $discounttotal
                    ], REST_Controller::HTTP_OK);
                }
            } else {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Discount amount cannot be greater than Discount applicable amount.'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response([
                'success' => FALSE ,
                'message' => 'Discount should be greater than zero. '
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    function fetch_supplier_invoice_detail_get()
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
        $InvoiceAutoID = isset($request_1->InvoiceAutoID) ? $request_1->InvoiceAutoID : null;
        $data = array();
        $companyID = current_companyID();
        $secondaryCode = getPolicyValues('SSC', 'All');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', trim($InvoiceAutoID), 'BSI', 'InvoiceAutoID');
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if ($secondaryCode == 1) {
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }

        if ($group_based_tax == 1) {
            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_unit_of_measure.UnitShortCode as secunitcode,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['ItemDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS DescriptionTax,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $this->db->where('type', 'GL');
            $data['detail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS DescriptionTax,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'PO');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');

            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['poDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

        } else {

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_unit_of_measure.UnitShortCode as secunitcode,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,IFNULL(taxAmount,0) as taxAmountLedger,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['ItemDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

            $this->db->select('*');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'GL');
            $data['detail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'PO');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $data['poDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

        }
        $this->db->select('*');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();

        $this->response([
            'data' => $data,
            'success' => TRUE,
            'message' => 'Supplier Invoice details Retrieved.'
        ], REST_Controller::HTTP_OK);
    }
    function load_supplier_invoice_conformation_get()
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
        $InvoiceAutoID = isset($request_1->InvoiceAutoID) ? $request_1->InvoiceAutoID : null;
        $approval = isset($request_1->approval) ? $request_1->approval : null;
        $approval = isset($request_1->ihtml) ? $request_1->ihtml : null;
        
        $InvoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($InvoiceAutoID);
        $data['extra'] = $this->Payable_modal->fetch_supplier_invoice_template_data($InvoiceAutoID);
        $printSize = $this->uri->segment(4);
        $data['approval'] = $approval;
        if (!$ihtml) {
            $data['signature'] = $this->Payable_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($ihtml){
            $data['logo']=htmlImage;
        }
        $data['html']=$ihtml;
        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN']= $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'BSI');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN']= $printHeaderFooterYN;
        $data['group_based_tax'] = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $InvoiceAutoID, 'BSI', 'InvoiceAutoID');
        $printlink = print_template_pdf('BSI','system/accounts_payable/erp_supplier_invoice_print');
        $data['isRcmDocument'] =  isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', $InvoiceAutoID);

        $html = $this->load->view($printlink, $data, true);

        if ($ihtml) {
            echo $html;
        } else {
            $this->load->library('pdf');

            if($printSize == 0 && $printSize!=null){
                $defaultpapersize='A5-L';
            }else{
                $defaultpapersize='A4';
            }



            $pdf = $this->pdf->printed($html, $defaultpapersize, $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }
    function supplier_invoice_confirmation_post()
    {
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $this->db->select('InvoiceAutoID');
        $this->db->where('InvoiceAutoID', trim($this->post('InvoiceAutoID')));
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            $this->response([
                'success' => FALSE ,
                'message' => 'There are no records to confirm this document! '
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($this->post('InvoiceAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->response([
                    'success' => TRUE,
                    'message' => 'Document already confirmed.'
                ], REST_Controller::HTTP_OK);
            } else {
                $this->db->trans_start();
                $system_id = trim($this->post('InvoiceAutoID'));
                $this->db->select('bookingInvCode,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                $lenth = strlen($master_dt['bookingInvCode']);
                if ($lenth == 1) {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            $this->response([
                                'success' => FALSE ,
                                'message' => 'Location is not assigned for current employee'
                            ], REST_Controller::HTTP_NOT_FOUND);
                        } else {
                            if ($emplocationid != '') {
                                $location = $this->sequence->sequence_generator_location('BSI', $master_dt['companyFinanceYearID'], $emplocationid, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                $this->response([
                                    'success' => FALSE ,
                                    'message' => 'Location is not assigned for current employee'
                                ], REST_Controller::HTTP_NOT_FOUND);
                            }
                        }
                    } else {
                        $location = $this->sequence->sequence_generator_fin('BSI', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($location, 'bookingInvCode', $system_id, 'InvoiceAutoID', 'srp_erp_paysupplierinvoicemaster');
                    if (!empty($validate_code)) {
                        $this->response([
                            'success' => FALSE ,
                            'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                        ], REST_Controller::HTTP_NOT_FOUND);
                    }

                    $invcod = array(
                       'bookingInvCode' => $location,
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $invcod);
                } else {
                    $validate_code = validate_code_duplication($master_dt['bookingInvCode'], 'bookingInvCode', $system_id, 'InvoiceAutoID', 'srp_erp_paysupplierinvoicemaster');
                    if (!empty($validate_code)) {
                        $this->response([
                            'success' => FALSE ,
                            'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                        ], REST_Controller::HTTP_NOT_FOUND);
                    }
                }
                $this->load->library('Approvals');
                $this->db->select('InvoiceAutoID, bookingInvCode,transactionCurrency,transactionExchangeRate,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth,bookingDate');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_data = $this->db->get()->row_array();

                $autoApproval = get_document_auto_approval('BSI');
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($master_data['InvoiceAutoID'], 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', 'BSI', $master_data['bookingInvCode'], $master_data['bookingDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('BSI', $master_data['InvoiceAutoID'], $master_data['bookingInvCode'], 'Supplier Invoice', 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', 0, $master_data['bookingDate']);
                } else {
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Approval levels are not set for this document'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }

                if ($approvals_status == 1) {
                    $transa_total_amount = 0;
                    $loca_total_amount = 0;
                    $rpt_total_amount = 0;
                    $supplier_total_amount = 0;
                    $t_arr = array();
                    $tra_tax_total = 0;
                    $loca_tax_total = 0;
                    $rpt_tax_total = 0;
                    $sup_tax_total = 0;
                    $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(supplierAmount) as supplierAmount');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

                    $transa_total_amount += $data_arr['transactionAmount'];
                    $loca_total_amount += $data_arr['companyLocalAmount'];
                    $rpt_total_amount += $data_arr['companyReportingAmount'];
                    $supplier_total_amount += $data_arr['supplierAmount'];

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
                        $tra_tax_total = $t_arr[$x]['transactionAmount'];
                        $sup_tax_total = $t_arr[$x]['supplierCurrencyAmount'];
                        $loca_tax_total = $t_arr[$x]['companyLocalAmount'];
                        $rpt_tax_total = $t_arr[$x]['companyReportingAmount'];
                    }
                    /*updating transaction amount using the query used in the master data table*/
                    $companyID = current_companyID();
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
        	`companyID` = $companyID
        	AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $system_id ";
                            $totalValue = $this->db->query($r1)->row_array();
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
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
                } else if ($approvals_status == 3) {
                    $this->response([
                        'success' => TRUE,
                        'message' => 'There are no users exist to perform approval for this document.'
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Confirmation failed.'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice Confirmed failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Supplier Invoice Confirmed failed ' . $this->db->_error_message()
                    ], REST_Controller::HTTP_NOT_FOUND);
                } else {
                    $autoApproval = get_document_auto_approval('BSI');

                    

                    $updatedBatchNumberArray=[];

                    if($itemBatchPolicy==1){

                        $this->db->select('*');
                        $this->db->where('InvoiceAutoID', trim($this->post('InvoiceAutoID')));
                        $this->db->from('srp_erp_paysupplierinvoicedetail');
                        $invoice_results = $this->db->get()->result_array();

                        $updatedBatchNumberArray=update_item_batch_number_details($invoice_results);

                    }

                     if ($autoApproval == 0) {
                        $result = $this->save_supplier_invoice_approval(0, $master_data['InvoiceAutoID'], 1, 'Auto Approved',$updatedBatchNumberArray);
                        if ($result) {
                            $this->db->trans_commit();
                            $this->response([
                                'success' => TRUE,
                                'message' => 'Supplier Invoice Confirmed Successfully.',
                                'last_id' => $InvoiceAutoID
                            ], REST_Controller::HTTP_OK);
                        }
                    } else {
                        $this->db->trans_commit();
                        $this->response([
                            'success' => TRUE,
                            'message' => 'Supplier Invoice Confirmed Successfully.',
                            'last_id' => $this->post('InvoiceAutoID')
                        ], REST_Controller::HTTP_OK);
                    }
                }
            }
        }
    }

//////////Supplier Invoice - GRV Based////////////////

function save_grv_base_items_post()
    {
        $this->db->trans_start();

        $amount = $this->post('amounts');
        $match = $this->post('match');
        $grvAutoID = $this->post('grvAutoID');
        $InvoiceAutoID = $this->post('InvoiceAutoID');
        $companyID = current_companyID();

        $this->db->select('bookingCurrencyExchangeRate, supplierCurrencyExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate');
        $this->db->where_in('match_supplierinvoiceAutoID', $match);
        $this->db->from('srp_erp_match_supplierinvoice');
        $match_data = $this->db->get()->result_array();

        for ($i = 0; $i < count($match_data); $i++) {
            $this->db->select('grvAutoID,grvType,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate, grvPrimaryCode ,grvDocRefNo,supplierliabilityAutoID,supplierliabilitySystemGLCode,supplierliabilityGLAccount,supplierliabilityType,supplierliabilityDescription,grvDate,grvNarration,segmentID,segmentCode,invoicedTotalAmount,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
            $this->db->from('srp_erp_grvmaster');
            $this->db->where('srp_erp_grvmaster.grvAutoID', $grvAutoID[$i]);
            $master_recode = $this->db->get()->row_array();

            $data[$i]['InvoiceAutoID'] = $this->post('InvoiceAutoID');
            $data[$i]['grvAutoID'] = $master_recode['grvAutoID'];
            $data[$i]['grvType'] = 'GRV Base';
            $data[$i]['match_supplierinvoiceAutoID'] = $match[$i];
            $data[$i]['grvPrimaryCode'] = $master_recode['grvPrimaryCode'];
            $data[$i]['grvDocRefNo'] = $master_recode['grvDocRefNo'];
            $data[$i]['grvDate'] = $master_recode['grvDate'];
            $data[$i]['segmentID'] = $master_recode['segmentID'];
            $data[$i]['segmentCode'] = $master_recode['segmentCode'];
            $data[$i]['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
            $data[$i]['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
            $data[$i]['GLCode'] = $master_recode['supplierliabilityGLAccount'];
            $data[$i]['GLDescription'] = $master_recode['supplierliabilityDescription'];
            $data[$i]['GLType'] = $master_recode['supplierliabilityType'];
            $data[$i]['description'] = $master_recode['grvNarration'];
            $transactionAmount = $amount[$i] / $match_data[$i]['bookingCurrencyExchangeRate'];
            $data[$i]['transactionAmount'] = round($transactionAmount, $master_recode['transactionCurrencyDecimalPlaces']);
            $data[$i]['transactionExchangeRate'] = $match_data[$i]['bookingCurrencyExchangeRate'];
            $companyLocalAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master_recode['companyLocalCurrencyDecimalPlaces']);
            $data[$i]['companyLocalExchangeRate'] = $match_data[$i]['companyLocalExchangeRate'];
            $companyReportingAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master_recode['companyReportingCurrencyDecimalPlaces']);
            $data[$i]['companyReportingExchangeRate'] = $match_data[$i]['companyReportingExchangeRate'];
            $supplierAmount = $data[$i]['transactionAmount'] / $match_data[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['supplierAmount'] = round($supplierAmount, $master_recode['supplierCurrencyDecimalPlaces']);
            $data[$i]['supplierCurrencyExchangeRate'] = $match_data[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime'] = date('y-m-d H:i:s');
            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = date('y-m-d H:i:s');
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $data[$i]['match_supplierinvoiceAutoID'];
            $number = $transactionAmount;
            $status = 0;

            $this->db->select('invoicedTotalAmount, bookingAmount');
            $this->db->from('srp_erp_match_supplierinvoice');
            $this->db->where('match_supplierinvoiceAutoID', $match_id);
            $inv_data = $this->db->get()->row_array();
            if ($inv_data['bookingAmount'] <= ($number + $inv_data['invoicedTotalAmount'])) {
                $status = 1;
            }

            $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount +{$number}) , supplierInvoiceYN = '{$status}'  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);

            $this->db->query("INSERT INTO srp_erp_taxledger (documentID, documentMasterAutoID, documentDetailAutoID, amount,taxGlAutoID,transferGLAutoID,taxMasterID,companyID,outputVatTransferGL,outputVatGL,isClaimable,rcmApplicableYN, taxFormulaMasterID, taxFormulaDetailID)
                                      SELECT 'BSI' AS documentID,
                                              InvoiceAutoID AS documentMasterAutoID, 
                                              InvoiceDetailAutoID AS documentDetailAutoID, 
                                              IFNULL(( TRIM( ROUND( (( taxledger.taxamount / matchsupplierinvoice.bookingAmount ) * srp_erp_paysupplierinvoicedetail.transactionAmount),4)) + 0) ,0)  +
                                              IFNULL(( TRIM( ROUND( (( taxledgerAddon.taxamount / matchsupplierInvoiceAddon.bookingAmount ) * srp_erp_paysupplierinvoicedetail.transactionAmount),4)) + 0 ) ,0) AS amount,
                                              taxledger.inputVatGLAccountAutoID AS taxGlAutoID,
                                              taxledger.taxGlAutoID AS transferGLAutoID,
                                              taxledger.taxMasterID AS taxMasterID,
                                              {$companyID} AS companyID,
                                              taxledger.outputVatTransferGL AS outputVatTransferGL,
                                              taxledger.outputVatGL AS outputVatGL,
                                              taxledger.isClaimable AS isClaimable,
                                              taxledger.rcmApplicableYN AS rcmApplicableYN,
                                              taxledger.taxFormulaMasterID,
                                              taxledger.taxFormulaDetailID
                                            FROM
	                                          `srp_erp_paysupplierinvoicedetail`
                                            LEFT JOIN ( SELECT match_supplierinvoiceAutoID,addonID,bookingAmount  FROM srp_erp_match_supplierinvoice WHERE isAddon = 0 ) matchsupplierinvoice ON srp_erp_paysupplierinvoicedetail.match_supplierinvoiceAutoID = matchsupplierinvoice.match_supplierinvoiceAutoID
                                            LEFT JOIN (SELECT match_supplierinvoiceAutoID,addonID,bookingAmount   FROM srp_erp_match_supplierinvoice WHERE isAddon = 1) matchsupplierInvoiceAddon ON matchsupplierInvoiceAddon.match_supplierinvoiceAutoID = srp_erp_paysupplierinvoicedetail.match_supplierinvoiceAutoID
                                            LEFT JOIN (
	                                          SELECT
	                                                 SUM( amount ) AS taxamount,
		                                             documentMasterAutoID,
		                                             taxMasterID,
		                                             documentDetailAutoID,
		                                             taxGlAutoID,
		                                             srp_erp_taxmaster.inputVatGLAccountAutoID,
		                                             outputVatTransferGL,
		                                             outputVatGL,
		                                             srp_erp_taxledger.isClaimable,
		                                             srp_erp_taxledger.rcmApplicableYN,
                                                     srp_erp_taxledger.taxFormulaMasterID,
                                                     srp_erp_taxledger.taxFormulaDetailID,
		                                             documentID
	                                                 FROM
		                                             srp_erp_taxledger
		                                             LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
	                                                 WHERE
		                                             documentID IN ( 'GRV' ) 
		                                             AND documentMasterAutoID IN ( SELECT grvAutoID FROM srp_erp_paysupplierinvoicedetail WHERE InvoiceAutoID = {$InvoiceAutoID} GROUP BY grvAutoID ) 
		                                             AND taxCategory = 2 
	                                                 GROUP BY
		                                             documentMasterAutoID 
                                            ) taxledger ON taxledger.documentMasterAutoID = srp_erp_paysupplierinvoicedetail.grvAutoID
                                            LEFT JOIN (
                                                     SELECT
                                                     SUM( amount ) AS taxamount,
                                                     documentMasterAutoID,
                                                     taxMasterID,
                                                     taxGlAutoID,
                                                     srp_erp_taxmaster.inputVatGLAccountAutoID,
                                                     outputVatTransferGL,
                                                     outputVatGL,
                                                     srp_erp_taxledger.isClaimable,
                                                     srp_erp_taxledger.rcmApplicableYN,
                                                     srp_erp_taxledger.documentDetailAutoID,
                                                     documentID
                                                     FROM
                                                     srp_erp_taxledger
                                                     LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                     LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.grvDetailsID = srp_erp_taxledger.documentDetailAutoID
                                                     WHERE
                                                     documentID IN ( 'GRV-ADD' ) 
                                                     AND documentMasterAutoID IN ( SELECT grvAutoID FROM srp_erp_paysupplierinvoicedetail WHERE InvoiceAutoID = {$InvoiceAutoID} GROUP BY grvAutoID ) 
                                                     AND taxCategory = 2 
                                                     GROUP BY
                                                     documentMasterAutoID,documentDetailAutoID) taxledgerAddon  ON taxledgerAddon.documentDetailAutoID = matchsupplierInvoiceAddon.addonID WHERE
	                                                 InvoiceAutoID = {$InvoiceAutoID} 
	                                                 AND grvType = 'GRV Base' 
	                                                 AND srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID NOT IN ( SELECT documentDetailAutoID AS documentDetailAutoID FROM `srp_erp_taxledger` WHERE `documentID` = 'BSI' GROUP BY documentDetailAutoID ) 
                                                     GROUP BY
	                                                 InvoiceDetailAutoID");

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Good Received note : Details Save Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Good Received note : ' . count($master_recode) . ' Item Details Saved Successfully.'
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'success' => FALSE ,
                'message' => 'Something went wrong!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    function fetch_grvdetail_get()
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
        $grvAutoID = isset($request_1->grvAutoID) ? $request_1->grvAutoID : null;
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $grv_detail_list = array();
        $grv_detail_list = $this->db->query("SELECT srp_erp_grvdetails.*,CONCAT_WS(' - ',IF(LENGTH(srp_erp_grvdetails.itemDescription),srp_erp_grvdetails.itemDescription,NULL),IF(LENGTH(srp_erp_grvdetails.comment),srp_erp_grvdetails.comment,NULL))as itemdes,srp_erp_itemmaster.isSubitemExist,CONCAT_WS(
	' - Part No : ',
IF
	( LENGTH( srp_erp_grvdetails.comment ), srp_erp_grvdetails.comment, NULL ),

IF
	( LENGTH( srp_erp_itemmaster.partNo ), srp_erp_itemmaster.partNo, NULL )
	) AS Itemdescriptionpartno,IFNULL( srp_erp_taxcalculationformulamaster.Description,' - ') AS Description,IFNULL( srp_erp_grvdetails.taxAmount, 0) AS taxAmount,".$item_code ."
    FROM srp_erp_grvdetails
    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID
    LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_grvdetails.taxCalculationformulaID
    WHERE grvAutoID = ".trim($grvAutoID))->result_array();
        $final_output['data'] = $grv_detail_list;
        $final_output['success'] = true;
        $final_output['message'] = 'Good Recieved Voucher details retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
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
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        
        
        $data['hideCost'] = getPolicyValues('HCG', 'All');
        $data['detail']=$this->db->query("SELECT
	    `srp_erp_purchaseorderdetails`.*, 
        IFNULL(srp_erp_taxcalculationformulamaster.Description,'-') as Description,
        IFNULL(srp_erp_taxcalculationformulamaster.taxCalculationformulaID,0) as taxCalculationformulaID,
        (TRIM( ROUND( requestedQty, 4 ) ) + 0 ) - ((TRIM( ROUND( IFNULL( supdetail.supqty, 0 ), 4 ) ) + 0 ) + TRIM( ROUND( IFNULL( grvdetail.grvqty, 0 ), 4 ) ) + 0 ) as qtybalance,
	    CONCAT_WS(
	    	' - Part No : ',
	    IF (
		LENGTH(
			srp_erp_purchaseorderdetails.`itemDescription`
		),
		`srp_erp_purchaseorderdetails`.`itemDescription`,
		NULL
    	),
        IF (
	    LENGTH(srp_erp_itemmaster.partNo),
	    `srp_erp_itemmaster`.`partNo`,
	    NULL
        )
	    ) AS Itemdescriptionpartno,$item_code
        FROM
	    `srp_erp_purchaseorderdetails`
        LEFT JOIN (
	    SELECT
	    	purchaseOrderDetailsID,
	    	SUM(requestedQty) AS supqty
	    FROM
	    	srp_erp_paysupplierinvoicedetail
	    WHERE
	    	purchaseOrderMastertID = '$purchaseOrderID'
	    GROUP BY
        purchaseOrderDetailsID
        ) supdetail ON `supdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
        LEFT JOIN (
	    SELECT
	    	purchaseOrderDetailsID,
	    	SUM(receivedQty) AS grvqty
	    FROM
	    	srp_erp_grvdetails
	    WHERE
	    	purchaseOrderMastertID = '$purchaseOrderID'
	    GROUP BY
	    purchaseOrderDetailsID
        ) grvdetail ON `grvdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
        LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID
        WHERE
        	`purchaseOrderID` = '$purchaseOrderID'
        AND (
        	`goodsRecievedYN` = 0
        	OR `goodsRecievedYN` IS NULL
        )
        GROUP BY
    	`purchaseOrderDetailsID`")->result_array();

        $companyID = current_companyID();
        $data['policy_po_cost_change'] = policy_allow_to_change_po_cost_in_grv();
        $this->db->SELECT("weightAutoID,bucketWeight");
        $this->db->FROM('srp_erp_buyback_bucketweight');
        $this->db->WHERE('companyID', $companyID);
        $data['bucketweightdrop'] =  $this->db->get()->result_array();
        $final_output['data'] = $data;
        $final_output['success'] = true;
        $final_output['message'] = 'Purchase Order details retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }

    function save_po_base_items_post()
    {
        $noofitems = $this->post('noofitems');
        $grossqty = $this->post('grossqty');
        $buckets = $this->post('buckets');
        $bucketweightID = $this->post('bucketweightID');
        $bucketweight = $this->post('bucketweight');
        $taxCalculationMasterID = $this->post('taxCalculationMasterID');

        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('srp_erp_purchaseorderdetails.*,ifnull(sum(srp_erp_grvdetails.receivedQty),0) AS receivedQty,ifnull(sum(srp_erp_paysupplierinvoicedetail.requestedQty),0) AS bsireceivedQty,srp_erp_purchaseordermaster.purchaseOrderCode');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->where_in('srp_erp_purchaseorderdetails.purchaseOrderDetailsID', $this->post('DetailsID'));
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
        $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->join('srp_erp_paysupplierinvoicedetail', 'srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->group_by("purchaseOrderDetailsID");
        $query = $this->db->get()->result_array();

        $purchaseOrderID = array_column($query, 'purchaseOrderID');

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription, jobID');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->post('grvAutoID')));
        $master = $this->db->get()->row_array();

        $qty = $this->post('qty');
        $amount = $this->post('amount');
        for ($i = 0; $i < count($query); $i++) {
            $this->db->select('purchaseOrderMastertID');
            $this->db->from('srp_erp_grvdetails');
            $this->db->where('purchaseOrderMastertID', $query[$i]['purchaseOrderID']);
            $this->db->where('grvAutoID', trim($this->post('grvAutoID')));
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $order_detail = $this->db->get()->result_array();
            $item_data = fetch_item_data($query[$i]['itemAutoID']);
            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
                if (empty($warehouseitems)) {
                    $item_id = array_search($query[$i]['itemSystemCode'], array_column($items_arr, 'itemSystemCode'));
                    if ((string)$item_id == '') {
                        $items_arr[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                        $items_arr[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                        $items_arr[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                        $items_arr[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                        $items_arr[$i]['barCodeNo']= $item_data['barcode'];
                        $items_arr[$i]['salesPrice']= $item_data['companyLocalSellingPrice'];
                        $items_arr[$i]['ActiveYN']= $item_data['isActive'];
                        $items_arr[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                        $items_arr[$i]['itemDescription'] = $query[$i]['itemDescription'];
                        $items_arr[$i]['unitOfMeasureID'] = $query[$i]['defaultUOMID'];
                        $items_arr[$i]['unitOfMeasure'] = $query[$i]['defaultUOM'];
                        $items_arr[$i]['currentStock'] = 0;
                        $items_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $items_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    }
                }
            }
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $serviceitm= $this->db->get()->row_array();

            if (!empty($order_detail) && $serviceitm['mainCategory']=="Inventory") {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'PO Details added already.'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
                $this->db->select('GLAutoID');
                $this->db->where('controlAccountType', 'ACA');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                $potaxamnt=($query[$i]['taxAmount']+$query[$i]['generalTaxAmount'])/$query[$i]['requestedQty'];
                $item_data = fetch_item_data($query[$i]['itemAutoID']);
                $data[$i]['purchaseOrderMastertID'] = $query[$i]['purchaseOrderID'];
                $data[$i]['purchaseOrderCode'] = $query[$i]['purchaseOrderCode'];
                $data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
                $data[$i]['grvAutoID'] = trim($this->post('grvAutoID'));
                $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
                $data[$i]['requestedQty'] = $query[$i]['requestedQty'];
               
                if(existTaxPolicyDocumentWise('srp_erp_grvmaster',trim($this->post('grvAutoID')),'GRV','grvAutoID')== 1 && $taxCalculationMasterID[$i]!=0){
                    $data[$i]['requestedAmount'] = $query[$i]['unitAmount']-($query[$i]['generalDiscountAmount']/$query[$i]['requestedQty']);
                }else{ 
                    $data[$i]['requestedAmount'] = $query[$i]['unitAmount']-($query[$i]['generalDiscountAmount']/$query[$i]['requestedQty'])+$potaxamnt;
                }
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['receivedQty'] = $qty[$i];
                $data[$i]['noOfItems'] = $noofitems[$i];
                $data[$i]['grossQty'] = $grossqty[$i];
                $data[$i]['noOfUnits'] = $buckets[$i];
                $data[$i]['deduction'] = $bucketweight[$i];
                $data[$i]['bucketWeightID'] = $bucketweightID[$i];
                $data[$i]['receivedAmount'] = $amount[$i];
                $data[$i]['receivedTotalAmount'] = ($data[$i]['receivedQty'] * $data[$i]['receivedAmount']);
                $data[$i]['fullTotalAmount'] = ($data[$i]['receivedQty'] * $data[$i]['receivedAmount']);
                $data[$i]['financeCategory'] = $item_data['financeCategory'];
                $data[$i]['itemCategory'] = trim($item_data['mainCategory'] ?? '');
                if ($data[$i]['itemCategory'] == 'Inventory') {

                    if(!empty($master['jobID'])) {
                        $companyID = current_companyID();
                        $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                        FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                            WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();
                        if ($glDetails) {
                            $data[$i]['BLGLAutoID'] = $glDetails['WIPGLAutoID'];
                            $data[$i]['BLSystemGLCode'] = $glDetails['systemAccountCode'];
                            $data[$i]['BLGLCode'] = $glDetails['GLSecondaryCode'];
                            $data[$i]['BLDescription'] = $glDetails['GLDescription'];
                            $data[$i]['BLType'] = $glDetails['subCategory'];
                        } else {
                            $data[$i]['BLGLAutoID'] = $item_data['assteGLAutoID'];
                            $data[$i]['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                            $data[$i]['BLGLCode'] = $item_data['assteGLCode'];
                            $data[$i]['BLDescription'] = $item_data['assteDescription'];
                            $data[$i]['BLType'] = $item_data['assteType'];
                        }
                    } else {
                        $data[$i]['BLGLAutoID'] = $item_data['assteGLAutoID'];
                        $data[$i]['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                        $data[$i]['BLGLCode'] = $item_data['assteGLCode'];
                        $data[$i]['BLDescription'] = $item_data['assteDescription'];
                        $data[$i]['BLType'] = $item_data['assteType'];
                    }
                    $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                    $data[$i]['PLDescription'] = $item_data['costDescription'];
                    $data[$i]['PLType'] = $item_data['costType'];

                } elseif ($data[$i]['itemCategory'] == 'Fixed Assets') {
                    $data[$i]['PLGLAutoID'] = NULL;
                    $data[$i]['PLSystemGLCode'] = NULL;
                    $data[$i]['PLGLCode'] = NULL;
                    $data[$i]['PLDescription'] = NULL;
                    $data[$i]['PLType'] = NULL;

                    $data[$i]['BLGLAutoID'] = $ACA_ID['GLAutoID'];
                    $data[$i]['BLSystemGLCode'] = $ACA['systemAccountCode'];
                    $data[$i]['BLGLCode'] = $ACA['GLSecondaryCode'];
                    $data[$i]['BLDescription'] = $ACA['GLDescription'];
                    $data[$i]['BLType'] = $ACA['subCategory'];
                } else {
                    if(!empty($master['jobID'])) {
                        $companyID = current_companyID();
                        $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                            FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                                WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();
        
                        if ($glDetails) {
                            $data[$i]['PLGLAutoID'] = $glDetails['WIPGLAutoID'];
                            $data[$i]['PLSystemGLCode'] = $glDetails['systemAccountCode'];
                            $data[$i]['PLGLCode'] = $glDetails['GLSecondaryCode'];
                            $data[$i]['PLDescription'] = $glDetails['GLDescription'];
                            $data[$i]['PLType'] = $glDetails['subCategory'];
                        } else {
                            $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                            $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                            $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                            $data[$i]['PLDescription'] = $item_data['costDescription'];
                            $data[$i]['PLType'] = $item_data['costType'];
                        }
                    } else {
                        $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                        $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                        $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                        $data[$i]['PLDescription'] = $item_data['costDescription'];
                        $data[$i]['PLType'] = $item_data['costType'];
                    }

                    $data[$i]['BLGLAutoID'] = '';
                    $data[$i]['BLSystemGLCode'] = '';
                    $data[$i]['BLGLCode'] = '';
                    $data[$i]['BLDescription'] = '';
                    $data[$i]['BLType'] = '';
                } 

                $data[$i]['addonAmount'] = 0;
                $data[$i]['addonTotalAmount'] = 0;
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['remarks'] = $query[$i]['remarks'];
                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
                $data[$i]['modifiedDateTime'] = date('y-m-d H:i:s');
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = date('y-m-d H:i:s');

                $po_data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
                $po_data[$i]['GRVSelectedYN'] = 1;
                if ($query[$i]['requestedQty'] <= (floatval($qty[$i]) + floatval($query[$i]['receivedQty'])+ floatval($query[$i]['bsireceivedQty']))) {
                    $po_data[$i]['goodsRecievedYN'] = 1;
                } else {
                    $po_data[$i]['goodsRecievedYN'] = 0;
                }
             }
        }

        if (!empty($items_arr)) {
            $items_arr = array_values($items_arr);
            $this->db->insert_batch('srp_erp_warehouseitems', $items_arr);
        }

        if (!empty($data)) {

            $this->db->insert_batch('srp_erp_grvdetails', $data);

           /** sub item add */
            $grvAutoID = trim($this->post('grvAutoID'));
            $output = $this->db->query("SELECT * FROM srp_erp_grvdetails INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID AND isSubitemExist = 1 WHERE grvAutoID = '" . $grvAutoID . "'")->result_array();
            if (!empty($output)) {
                foreach ($output as $item) {
                    if ($item['isSubitemExist'] == 1) {
                        $qty = $item['receivedQty'];
                        $subData['uom'] = $data[0]['unitOfMeasure'];
                        $subData['uomID'] = $data[0]['unitOfMeasureID'];
                        $subData['grv_detailID'] = $item['grvDetailsID'];
                        $this->add_sub_itemMaster_tmpTbl($qty, $item['itemAutoID'], $grvAutoID, $item['grvDetailsID'], 'GRV', $item['itemSystemCode'], $subData);
                    }
                }
            }

            /** End sub item add */

            $this->db->update_batch('srp_erp_purchaseorderdetails', $po_data, 'purchaseOrderDetailsID');
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Good Received note : Details Save Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
            $this->response([
                'success' => TRUE,
                'message' => 'Good Received note : ' . count($query) . ' Item Details Saved Successfully.'
            ], REST_Controller::HTTP_OK); 
            $this->db->trans_commit();

                $companyID = current_companyID();
                $grvAutoID =  trim($this->post('grvAutoID'));

                $grvTax = $this->db->query("SELECT
                                            srp_erp_purchaseorderdetails.taxCalculationformulaID,grvAutoID,
                                           ((srp_erp_grvdetails.receivedQty * unitAmount)+(srp_erp_grvdetails.receivedQty* IFNULL(srp_erp_purchaseorderdetails.discountAmount,0))) as totalAmount,
		                                   (srp_erp_grvdetails.receivedQty * IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)) as discountAmount, receivedTotalAmount,
                                            grvDetailsID
                                       FROM
                                       `srp_erp_grvdetails` 
                                       LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID = srp_erp_grvdetails.purchaseOrderDetailsID

                                        WHERE
                                        srp_erp_grvdetails.companyID = $companyID 
                                        AND grvAutoID = $grvAutoID")->result_array();



                $isRcmApplicable =  isRcmApplicable('srp_erp_purchaseordermaster', 'purchaseOrderID', $purchaseOrderID[0]);
                if(existTaxPolicyDocumentWise('srp_erp_grvmaster',trim($this->post('grvAutoID')),'GRV','grvAutoID')== 1){
                    if(!empty($grvTax)){
                        foreach($grvTax as $val){
                            if($val['taxCalculationformulaID']!=0){
                                tax_calculation_vat(null,null,$val['taxCalculationformulaID'],'grvAutoID',trim($this->post('grvAutoID')),$val['receivedTotalAmount'],'GRV',$val['grvDetailsID'],0,1,$isRcmApplicable);
                            }
                        }
                    }
                }


                $this->response([
                    'success' => TRUE,
                    'message' => 'Purchase Order Details Successfully added.'
                ], REST_Controller::HTTP_OK); 
            }
        } else {
            $this->response([
                'success' => FALSE ,
                'message' => 'PO Details added already.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
    function fetch_addons_get()
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
        $grvAutoID = isset($request_1->grvAutoID) ? $request_1->grvAutoID : null;
        $addons = array();
        $this->db->select('masterTBL.*,detailTBL.description');
        $this->db->where('masterTBL.grvAutoID', trim($grvAutoID));
        $this->db->from('srp_erp_grv_addon masterTBL');
        $this->db->Join('srp_erp_taxcalculationformulamaster detailTBL','detailTBL.taxCalculationformulaID = masterTBL.taxCalculationformulaID','Left');
        $addons = $this->db->get()->result_array();
        $final_output['data'] = $addons;
        $final_output['success'] = true;
        $final_output['message'] = 'AddOns retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }
    function save_addon_post()
    {
        $this->db->trans_start();
        $booking_code = explode('|', trim($this->post('booking_code')));
        $taxtype = trim($this->post('taxtype'));

        $projectExist = project_is_exist();
        $this->db->select('transactionCurrencyID,transactionCurrency,transactionExchangeRate, transactionCurrencyDecimalPlaces');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where('grvAutoID', trim($this->post('grvAutoID')));
        $master = $this->db->get()->row_array();
        $supplier_arr = fetch_supplier_data(trim($this->post('supplier')));
        $data['grvAutoID'] = trim($this->post('grvAutoID'));
        $data['supplierID'] = trim($this->post('supplier'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($this->post('projectID'));
            $projectCurrencyExchangerate = currency_conversionID($this->post('bookingCurrencyID'), $projectCurrency);
            $data['projectID'] = trim($this->post('projectID'));
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['isChargeToExpense'] = trim($this->post('isChargeToExpense'));
        if ($data['isChargeToExpense'] == 1) {
            $gl_code = explode('|', $this->post('glcode_dec'));
            $data['GLAutoID'] = trim($this->post('GLAutoID'));
            $data['systemGLCode'] = trim($gl_code[0] ?? '');
            $data['GLCode'] = trim($gl_code[1] ?? '');
            $data['GLDescription'] = trim($gl_code[2] ?? '');
            $data['GLType'] = trim($gl_code[3] ?? '');
        }
        $data['bookingCurrencyID'] = trim($this->post('bookingCurrencyID'));
        $data['bookingCurrency'] = trim($booking_code[0] ?? '');
        $data['bookingCurrencyExchangeRate'] = 1;
        $data['bookingCurrencyAmount'] = trim($this->post('total_amount'));
        $data['bookingCurrencyDecimalPlaces'] = fetch_currency_desimal($data['bookingCurrency']);
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $transaction_currency = currency_conversionID($data['bookingCurrencyID'], $data['transactionCurrencyID']);
        $data['transactionExchangeRate'] = $transaction_currency['conversion'];
        $data['transactionCurrencyDecimalPlaces'] = $transaction_currency['DecimalPlaces'];
        $data['total_amount'] = round(($data['bookingCurrencyAmount'] / $data['transactionExchangeRate']), $data['transactionCurrencyDecimalPlaces']);
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['bookingCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['bookingCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $supplier_currency = currency_conversionID($data['bookingCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplier_currency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplier_currency['DecimalPlaces'];
        $data['supplierCurrencyAmount'] = round(($data['bookingCurrencyAmount'] / $data['supplierCurrencyExchangeRate']), $data['supplierCurrencyDecimalPlaces']);
        $data['impactFor'] = trim($this->post('impactFor'));
        $data['paidBy'] = trim($this->post('paid_by'));
        $data['addonCatagory'] = trim($this->post('addonCatagory'));
        $data['narrations'] = trim($this->post('narrations'));
        $data['referenceNo'] = trim($this->post('referencenos'));
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = date('y-m-d H:i:s');
        $data['modifiedUserName'] = $this->common_data['current_user'];

        if (trim($this->post('id'))) {
            $this->db->where('id', trim($this->post('id')));
            $this->db->update('srp_erp_grv_addon', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'GRV Addon  : ' . $data['addonCatagory'] . ' Update Failed '
                ], REST_Controller::HTTP_NOT_FOUND);
                //$this->session->set_flashdata('e', 'GRV Addon  : ' . $data['addonCatagory'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                if($taxtype){
                    tax_calculation_vat(null,null,$taxtype,'id',$data['grvAutoID'] ,$data['bookingCurrencyAmount'],'GRV-ADD',trim($this->post('id')),0,0);
                }

                $this->session->set_flashdata('s', 'GRV Addon  : ' . $data['addonCatagory'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->post('supplierCodeSystem'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = date('y-m-d H:i:s');

            $this->db->insert('srp_erp_grv_addon', $data);
            $last_id = $this->db->insert_id();

            if($taxtype){
                tax_calculation_vat(null,null,$taxtype,'id',$data['grvAutoID'] ,$data['bookingCurrencyAmount'],'GRV-ADD',$last_id,0,0);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'GRV Addon : ' . $data['addonCatagory'] . ' Save Failed '
                ], REST_Controller::HTTP_NOT_FOUND);
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
                $this->response([
                    'success' => TRUE,
                    'message' => 'GRV Addon : ' . $data['addonCatagory'] . 'Saved Successfully.',
                    'last_id' => $last_id
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    // function load_grv_conformation_post()
    // {
    //     $grvAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->post('grvAutoID'));
    //     $data['extra'] = $this->Grv_modal->fetch_template_data($grvAutoID);
    //     $data['approval'] = $this->post('approval');
    //     $data['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_grvmaster',$grvAutoID,'GRV','grvAutoID')!=''?existTaxPolicyDocumentWise('srp_erp_grvmaster',$grvAutoID,'GRV','grvAutoID'):0);
    //     if (!$this->post('html')) {
    //         $data['signature'] = $this->Grv_modal->fetch_signaturelevel();
    //     } else {
    //         $data['signature'] = '';
    //     }
    //     $data['logo']=mPDFImage;
    //     if($this->post('html')){
    //         $data['logo']=htmlImage;
    //     }
    //     if ($this->post('html')) {
    //         $html = $this->load->view('system/grv/erp_grv_print', $data, true);
    //         echo $html;
    //     } else {
    //         $printlink = print_template_pdf('GRV','system/grv/erp_grv_print');
    //         $papersize = print_template_paper_size('GRV','A4-L');
    //         $pdfp = $this->load->view($printlink, $data, true);
    //         /*$html = $this->load->view('system/grv/erp_grv_print', $data, true);*/
    //         $this->load->library('pdf');
    //       /* echo '<pre>';print_r($papersize); echo '</pre>'; die();*/
    //         $pdf = $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN']);
    //         /*$pdf = $this->pdf->printed_mc($pdfp,$papersize,$data['extra']['master']['approvedYN'],1);*/
    //     }
    // }
    function fetch_addon_item($grvAutoID, $grvDetailsID)
    {
        $this->db->select_sum('total_amount');
        // $this->db->where('impactFor', 0);
        // $this->db->where('isChargeToExpense', 0);
        $this->db->where('grvAutoID', $grvAutoID);
        $this->db->where('impactFor', $grvDetailsID);
        return $this->db->get('srp_erp_grv_addon')->row('total_amount');
    }

    function grv_confirmation_post()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $system_code = trim($this->post('grvAutoID'));
        $companyID = current_companyID();
        $currentuser  = current_userID();
        //$isProductReference_completed = isProductReference_completed_document($system_code);
        $isProductReference_completed = isMandatory_completed_document($system_code, 'GRV');
        $this->db->select('grvAutoID');
        $this->db->where('grvAutoID', $system_code);
        $this->db->from('srp_erp_grvdetails');
        $record = $this->db->get()->result_array();

        if (empty($record)) {
            $this->response([
                'success' => FALSE ,
                'message' => 'There are no records to confirm this document! '
            ], REST_Controller::HTTP_NOT_FOUND);
        }
        else {
            if ($isProductReference_completed == 0) {

                if($itemBatchPolicy==1){
                    $this->db->select('*');
                    $this->db->where('grvAutoID', trim($this->input->post('grvAutoID') ?? ''));
                    $this->db->from('srp_erp_grvdetails');
                    $grvdetails_results = $this->db->get()->result_array();
                }

                $this->db->select('grvPrimaryCode,documentID,DATE_FORMAT(grvDate, "%Y") as invYear,DATE_FORMAT(grvDate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('grvAutoID', trim($system_code));
                $this->db->from('srp_erp_grvmaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                if ($master_dt['grvPrimaryCode'] == "0" || empty($master_dt['grvPrimaryCode'])) {
                    if($locationwisecodegenerate == 1)
                    {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location =='')) {
                            $this->response([
                                'success' => FALSE ,
                                'message' => 'Location is not assigned for current employee'
                            ], REST_Controller::HTTP_NOT_FOUND);
                        }else
                        {
                            if($locationemployee!='')
                            {
                                $codegeratorgrv = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'],$locationemployee,$master_dt['invYear'],$master_dt['invMonth']);
                            }else
                            {
                               $this->response([
                                    'success' => FALSE ,
                                    'message' => 'Location is not assigned for current employee'
                                ], REST_Controller::HTTP_NOT_FOUND);
                            }
                        }
                    }else
                    {
                        $codegeratorgrv = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($codegeratorgrv, 'grvPrimaryCode', $system_code,'grvAutoID', 'srp_erp_grvmaster');
                    if(!empty($validate_code)) {
                        $this->response([
                            'success' => FALSE ,
                            'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                        ], REST_Controller::HTTP_NOT_FOUND);
                    }

                    $pvCd = array(
                        'grvPrimaryCode' => $codegeratorgrv
                    );
                    $this->db->where('grvAutoID', trim($system_code));
                    $this->db->update('srp_erp_grvmaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['grvPrimaryCode'], 'grvPrimaryCode', $system_code,'grvAutoID', 'srp_erp_grvmaster');
                    if(!empty($validate_code)) {
                        $this->response([
                            'success' => FALSE ,
                            'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                        ], REST_Controller::HTTP_NOT_FOUND);
                        }
                }

                $this->load->library('Approvals');
                $this->db->select('grvAutoID, grvPrimaryCode, wareHouseLocation,grvDate');
                $this->db->where('grvAutoID', $system_code);
                $this->db->from('srp_erp_grvmaster');
                $grv_data = $this->db->get()->row_array();

                $autoApproval= get_document_auto_approval('GRV');

                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve($grv_data['grvAutoID'], 'srp_erp_grvmaster','grvAutoID', 'GRV',$grv_data['grvPrimaryCode'],$grv_data['grvDate']);
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals->CreateApproval('GRV', $grv_data['grvAutoID'], $grv_data['grvPrimaryCode'], 'Good Received note', 'srp_erp_grvmaster', 'grvAutoID',0,$grv_data['grvDate']);
                }else{
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Approval levels are not set for this document.'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }

                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        //'confirmedDate' => $this->common_data['current_date'],
                        'confirmedDate' => date('y-m-d H:i:s'),
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );
                    $this->db->where('grvAutoID', $system_code);
                    $this->db->update('srp_erp_grvmaster', $data);

                    $this->db->select_sum('total_amount');
                    $this->db->where('impactFor', 0);
                    $this->db->where('isChargeToExpense', 0);
                    $this->db->where('grvAutoID', $system_code);
                    $addon_total_amount = $this->db->get('srp_erp_grv_addon')->row('total_amount');

                    $this->db->select('grvDetailsID,receivedAmount,receivedQty,itemAutoID,receivedTotalAmount,addonTotalAmount');
                    $this->db->where('grvAutoID', $system_code);
                    $grvdetails = $this->db->get('srp_erp_grvdetails')->result_array();
                    if (!empty($grvdetails)) {
                        $grv_full_total = 0;
                        foreach ($grvdetails as $num => $values) {
                            $grv_full_total += $values['receivedTotalAmount'];
                        }
                        for ($i = 0; $i < count($grvdetails); $i++) {
                            $item = fetch_item_data($grvdetails[$i]['itemAutoID']);
                            $data_recode = calculation_addon($grv_full_total, $addon_total_amount, $grvdetails[$i]['receivedAmount'], $grvdetails[$i]['receivedQty']);
                            $addon_item_all = $this->fetch_addon_item($system_code, $grvdetails[$i]['grvDetailsID']);
                            $addon_item = ($addon_item_all / $grvdetails[$i]['receivedQty']);
                            $grv_details[$i]['grvDetailsID'] = $grvdetails[$i]['grvDetailsID'];
                            $grv_details[$i]['addonAmount'] = round(($addon_item + $data_recode['unit']), 3);
                            $grv_details[$i]['addonTotalAmount'] = round(($addon_item_all + $data_recode['full']), 3);
                            $grv_details[$i]['fullTotalAmount'] = round($addon_item_all + $data_recode['item_total'], 3);
                        }
                        $this->db->update_batch('srp_erp_grvdetails', $grv_details, 'grvDetailsID');
                    }

                    $autoApproval= get_document_auto_approval('GRV');
                    if($autoApproval==0) {
                        $result = $this->Grv_modal->save_grv_approval(0, $grv_data['grvAutoID'], 1, 'Auto Approved');
                        if($result){
                            if( $itemBatchPolicy==1){
                                $this->Grv_modal->hit_item_batch($grvdetails_results);
                            }
                            $this->response([
                                'success' => TRUE,
                                'message' => 'Document confirmed successfully '
                            ], REST_Controller::HTTP_OK);
                        }
                    }else{
                        if( $itemBatchPolicy==1){
                            $this->hit_item_batch($grvdetails_results);
                        }
                        $this->response([
                            'success' => TRUE,
                            'message' => 'Document confirmed successfully'
                        ], REST_Controller::HTTP_OK);
                    }
                } else if ($approvals_status == 3) {
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'There are no users exist to perform approval for this document.'
                    ], REST_Controller::HTTP_NOT_FOUND);
                    } else {
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'some went wrong!'
                    ], REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Please complete you sub item configuration, fill all the mandatory fields!.'
                ], REST_Controller::HTTP_NOT_FOUND);
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
