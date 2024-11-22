<?php

use mysql_xdevapi\Result;
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_Payment_Voucher extends REST_Controller
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


    //-----------Payment Voucher--------------//
    function fetch_payment_voucher_get()
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
        $datefrom = $this->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->post('supplierPrimaryCode');
        $status = $this->post('status');
        $collectionstatus = $this->post('collectionstatus');
        $supplier_filter = '';
        $collection_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( PVdate >= '" . $datefromconvert . " 00:00:00' AND PVdate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        if (!empty($collectionstatus)) {
            if ($collectionstatus == 1) {
                $collection_filter = " AND (collectedStatus = 1 AND approvedYN = 1)";
            } else if ($collectionstatus == 2) {
                $collection_filter = " AND (collectedStatus = 2 AND approvedYN = 1)";
            } else if ($collectionstatus == 3) {
                $collection_filter = " AND (collectedStatus = 0  AND approvedYN = 1)";
            }
        }
        $sSearch = $this->post('sSearch');
        $searches = '';
        $where = "srp_erp_paymentvouchermaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches . $collection_filter . "";
        $PaymentVoucher = array();
        $PaymentVoucher=$this->db->query("SELECT srp_erp_paymentvouchermaster.modeOfPayment as modeOfPayment, srp_erp_paymentvouchermaster.payVoucherAutoId as payVoucherAutoId,collectedStatus,PVNarration,PVcode,DATE_FORMAT(PVdate,'" . $convertFormat . "') AS PVdate,confirmedYN,approvedYN,srp_erp_paymentvouchermaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces, (((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,srp_erp_paymentvouchermaster.isDeleted as isDeleted,bankGLAutoID,case pvType when 'Direct' OR 'DirectItem' OR 'DirectExpense' then partyName when 'Employee' OR 'EmployeeExpense' OR 'EmployeeItem' then srp_employeesdetails.Ename2 when 'PurchaseRequest' then partyName when 'Supplier' OR 'SupplierAdvance' OR 'SupplierDebitNote' OR 'SupplierInvoice' OR 'SupplierItem' OR 'SupplierExpense' then srp_erp_suppliermaster.supplierName end as partyName,paymentType,srp_erp_paymentvouchermaster.confirmedByEmpID as confirmedByEmp,srp_erp_paymentvouchermaster.collectedStatus as collectedStatus,srp_erp_paymentvouchermaster.isSytemGenerated as isSytemGenerated, srp_erp_paymentvouchermaster.referenceNo AS referenceNo,PVchequeNo, CASE WHEN pvType = 'DirectItem' THEN 'Direct Item Payment' WHEN pvType = 'DirectExpense' THEN 'Direct Expense' WHEN pvType = 'SupplierInvoice' OR pvType = 'SupplierAdvance' THEN 'Supplier Invoice Payment' WHEN pvType = 'SupplierItem' THEN 'Supplier Item Payment' WHEN pvType = 'SupplierExpense' THEN 'Supplier Expense Payment' WHEN pvType = 'EmployeeExpense' THEN 'Employee Expense Payment' WHEN pvType = 'EmployeeItem' THEN 'Employee Item Payment' WHEN pvType = 'Direct' THEN 'Direct Item Payment' WHEN pvType = 'PurchaseRequest' THEN 'Purchase Request' ELSE `pvType`  END AS pvType
                                            FROM srp_erp_paymentvouchermaster
                                            LEFT JOIN (SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!='debitnote' AND srp_erp_paymentvoucherdetail.type!='SR' GROUP BY payVoucherAutoId) det ON det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId
                                            LEFT JOIN (SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type='GL' OR srp_erp_paymentvoucherdetail.type='Item' OR srp_erp_paymentvoucherdetail.type='PRQ'  GROUP BY payVoucherAutoId) tyepdet ON (tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)
                                            LEFT JOIN (SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type='debitnote' GROUP BY payVoucherAutoId) debitnote ON (debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)
                                            LEFT JOIN (SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type='SR' GROUP BY payVoucherAutoId) SR ON (SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)
                                            LEFT JOIN (SELECT SUM(taxPercentage) as taxPercentage ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet ON (addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)
                                            LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID
                                            LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID
                                            WHERE ".$where."")->result_array();
        //$this->datatables->where('pvType <>', 'SC');
        $final_output['data'] = $PaymentVoucher;
        $final_output['success'] = true;
        $final_output['message'] = 'Payment Voucher list retrieved.';
        $this->set_response($final_output, REST_Controller::HTTP_OK);
    }
    function save_paymentvoucher_header_post()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $this->post('PVdate');
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $this->post('PVchequeDate');
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $accountPayeeOnly = 0;
        if (!empty($this->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }
        $voucherType = $this->post('pvtype');
        $supplierdetails = explode('|', trim($this->post('SupplierDetails')));
        if ($financeyearperiodYN == 1) {
            $financeyr = explode(' - ', trim($this->post('companyFinanceYear')));
            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($PVdate);
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
            $financePeriodDetails = get_financial_period_date_wise($PVdate);

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
        $segment = explode('|', trim($this->post('segment')));
        $bank = explode('|', trim($this->post('bank')));
        $currency_code = explode('|', trim($this->post('currency_code')));
        $chequeRegister = getPolicyValues('CRE', 'All');

        $data['PVbankCode'] = trim($this->post('PVbankCode'));
        $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
        $data['documentID'] = 'PV';
        $data['companyFinanceYearID'] = trim($this->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->post('financeyear_period'));
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['PVdate'] = trim($PVdate);

        $narration = ($this->input->post('narration'));
        $data['PVNarration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['accountPayeeOnly'] = $accountPayeeOnly;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['PVbank'] = $bank_detail['bankName'];
        $data['PVbankBranch'] = $bank_detail['bankBranch'];
        $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['PVbankType'] = $bank_detail['subCategory'];
        $data['paymentType'] = $this->post('paymentType');
        $data['supplierBankMasterID'] = $this->post('supplierBankMasterID');
        if($PVcheqDate == null)
        {
            $data['PVchequeDate'] = null;
        }
        if ($bank_detail['isCash'] == 1) {
            $data['PVchequeNo'] = null;
            $data['chequeRegisterDetailID'] = null;
            $data['PVchequeDate'] = null;
        } else {
            if ($this->post('paymentType') == 2 && (($voucherType == 'Supplier') || ($voucherType == 'SupplierAdvance') || ($voucherType == 'SupplierDebitNote') || ($voucherType == 'SupplierInvoice') || ($voucherType == 'SupplierItem') || ($voucherType == 'SupplierExpense') || ($voucherType == 'Direct') || ($voucherType == 'DirectItem') || ($voucherType == 'DirectExpense') || ($voucherType == 'Employee') || ($voucherType == 'EmployeeExpense') || ($voucherType == 'EmployeeItem') || ($voucherType == 'PurchaseRequest'))) {
                $data['PVchequeNo'] = null;
                $data['chequeRegisterDetailID'] = null;
                $data['PVchequeDate'] = null;
            } else {
                if($chequeRegister==1) {
                    $data['chequeRegisterDetailID'] = trim($this->post('chequeRegisterDetailID'));
                    $data['PVchequeNo'] = $this->getchequeDetails($this->post('chequeRegisterDetailID'));
                }else{
                    $data['PVchequeNo'] = trim($this->post('PVchequeNo'));
                    $data['chequeRegisterDetailID'] = null;
                }
                $data['PVchequeDate'] = trim($PVchequeDate);
            }
        }
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['pvType'] = trim($this->post('pvtype'));
        $data['bankTransferDetails'] = trim($this->post('bankTransferDetails'));
        $data['referenceNo'] = trim_desc($this->post('referenceno'));
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
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        if ($data['pvType'] == 'Direct' || $data['pvType'] == 'DirectItem' || $data['pvType'] == 'DirectExpense') {
            $data['partyType'] = 'DIR';
            $data['partyName'] = trim($this->post('partyName'));
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
        }elseif ($data['pvType'] == 'PurchaseRequest') {
            $data['partyType'] = 'PRQ';
            $data['partyName'] = trim($this->post('partyName'));
            $data['partyID'] = trim($this->post('partyID'));
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
            if(!empty($this->post('partyID'))){
                $supplier_arr = $this->fetch_supplier_data($this->post('partyID'));
                $data['partyCode'] = $supplier_arr['supplierSystemCode'];
                $data['partyName'] = $supplier_arr['supplierName'];
                $data['partyAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
                $data['partyTelephone'] = $supplier_arr['supplierTelephone'];
                $data['partyFax'] = $supplier_arr['supplierFax'];
                $data['partyEmail'] = $supplier_arr['supplierEmail'];
                $data['partyGLAutoID'] = $supplier_arr['liabilityAutoID'];
                $data['partyGLCode'] = $supplier_arr['liabilitySystemGLCode'];
                $data['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                $data['partyCurrency'] = $supplier_arr['supplierCurrency'];
                $data['partyExchangeRate'] = $data['transactionExchangeRate'];
                $data['partyCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];
            }

        } elseif ($data['pvType'] == 'Employee' || $data['pvType'] == 'EmployeeExpense' || $data['pvType'] == 'EmployeeItem') {
            $emp_arr = $this->fetch_empyoyee($this->post('partyID'));
            $data['partyType'] = 'EMP';
            $data['partyID'] = trim($this->post('partyID'));
            $data['partyCode'] = $emp_arr['ECode'];
            $data['partyName'] = $emp_arr['Ename2'];
            $data['partyAddress'] = $emp_arr['EcAddress1'] . ' ' . $emp_arr['EcAddress2'] . ' ' . $emp_arr['EcAddress3'];
            $data['partyTelephone'] = $emp_arr['EpTelephone'];
            $data['partyFax'] = $emp_arr['EpFax'];
            $data['partyEmail'] = $emp_arr['EEmail'];
            $data['partyGLAutoID'] = '';
            $data['partyGLCode'] = '';
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
        } elseif ($data['pvType'] == 'Supplier' || $data['pvType'] == 'SupplierAdvance' || $data['pvType'] == 'SupplierDebitNote' || $data['pvType'] == 'SupplierInvoice' || $data['pvType'] == 'SupplierItem' || $data['pvType'] == 'SupplierExpense') 
        {
            $supplier_arr = fetch_supplier_data($this->post('partyID'));
            $data['partyType'] = 'SUP';
            $data['partyID'] = $this->input->post('partyID');
            $data['partyCode'] = $supplier_arr['supplierSystemCode'];
            $data['partyName'] = $supplier_arr['supplierName'];
            $data['partyAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
            $data['partyTelephone'] = $supplier_arr['supplierTelephone'];
            $data['partyFax'] = $supplier_arr['supplierFax'];
            $data['partyEmail'] = $supplier_arr['supplierEmail'];
            $data['partyGLAutoID'] = $supplier_arr['liabilityAutoID'];
            $data['partyGLCode'] = $supplier_arr['liabilitySystemGLCode'];
            $data['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
            $data['partyCurrency'] = $supplier_arr['supplierCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];
        } elseif ($data['pvType'] == 'SC') {
            $sales_rep = $this->fetch_sales_rep_data($this->post('partyID'));
            $data['partyType'] = 'SC';
            $data['partyID'] = $this->input->post('partyID');
            $data['partyCode'] = $sales_rep['SalesPersonCode'];
            $data['partyName'] = $sales_rep['SalesPersonName'];
            $data['partyAddress'] = $sales_rep['SalesPersonAddress'];
            $data['partyTelephone'] = $sales_rep['contactNumber'];
            $data['partyEmail'] = $sales_rep['SalesPersonEmail'];
            $data['partyGLAutoID'] = $sales_rep['receivableAutoID'];
            $data['partyGLCode'] = $sales_rep['receivableSystemGLCode'];
            $data['partyCurrencyID'] = $sales_rep['salesPersonCurrencyID'];
            $data['partyCurrency'] = $sales_rep['salesPersonCurrency'];
            $data['partyExchangeRate'] = 0;
            $data['partyCurrencyDecimalPlaces'] = $sales_rep['salesPersonCurrencyDecimalPlaces'];
        }
        $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
        $data['partyExchangeRate'] = $partyCurrency['conversion'];
        $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];

        if (trim($this->input->post('PayVoucherAutoId') ?? '')) {
            $this->db->where('payVoucherAutoId', trim($this->post('PayVoucherAutoId')));
            $this->db->update('srp_erp_paymentvouchermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Payment Voucher Update Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                if(!empty($data['chequeRegisterDetailID'])){
                    $this->update_cheque_detail($data['chequeRegisterDetailID'],$this->post('PayVoucherAutoId'));
                } else {
                    $this->delete_cheque_detail($this->post('PayVoucherAutoId'));
                }
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Payment Voucher Updated Successfully.',
                    'last_id' => $this->post('PayVoucherAutoId')
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->db->where('GLAutoID', $data['bankGLAutoID']);
            $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));
            //$this->load->library('sequence');
            $data['isGroupBasedTax'] =  ((getPolicyValues('GBT', 'All')==1)?1:0);
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = date('y-m-d H:i:s');
            $type = substr($data['pvType'], 0, 3);
            $data['PVcode'] = 0;
            $this->db->insert('srp_erp_paymentvouchermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Payment Voucher   Saved Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                if(!empty($data['chequeRegisterDetailID'])){
                    $this->update_cheque_detail($data['chequeRegisterDetailID'],$last_id);
                }
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Payment Voucher Saved Successfully.',
                    'last_id' => $last_id
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    function fetch_pv_direct_details_get()
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
        $payVoucherAutoId = isset($request_1->payVoucherAutoId) ? $request_1->payVoucherAutoId : null;
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,partyCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['currency'] = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $this->db->select('srp_erp_taxcalculationformulamaster.Description as taxdescription,srp_erp_paymentvoucherdetail.*,IFNULL(discountAmount, 0) as discountAmount, srp_erp_itemmaster.isSubitemExist,CONCAT_WS(
                    \' - Part No : \',
                IF
                    ( LENGTH( srp_erp_paymentvoucherdetail.`comment` ), `srp_erp_paymentvoucherdetail`.`comment`, NULL ),
                IF
                    ( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
                    ) AS Itemdescriptionpartno,srp_erp_purchaserequestmaster.purchaseRequestCode, srp_erp_expenseclaimmaster.expenseClaimCode, srp_erp_expenseclaimmaster.expenseClaimMasterAutoID, IFNULL( taxAmount, 0 ) AS taxAmount, 
                    srp_erp_purchaseordermaster.documentDate as PODate, '.$item_code_alias.' ');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_paymentvoucherdetail.prMasterID', 'left');
        $this->db->join('srp_erp_purchaseordermaster','srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_paymentvoucherdetail.purchaseOrderID','LEFT');
        $this->db->join('srp_erp_expenseclaimmaster', 'srp_erp_expenseclaimmaster.expenseClaimMasterAutoID = srp_erp_paymentvoucherdetail.expenseClaimMasterAutoID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster','srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paymentvoucherdetail.taxCalculationformulaID','left');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['detail'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        $this->response([
            'data' => $data,
            'success' => TRUE,
            'message' => 'Payment Voucher details Retrieved.'
        ], REST_Controller::HTTP_OK);
    }
    function save_direct_pv_detail_multiple_post()
    {
        $item_data = json_decode(file_get_contents('php://input'));
        $this->db->trans_start();
        if(!empty($item_data)){
            $date_time = date('Y-m-d H:i:s');
            $iteminput_data = [];
        foreach ($item_data as $row){
        $projectExist = project_is_exist();
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($row->payVoucherAutoId),'PV','payVoucherAutoId');
        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate');
        $this->db->where('payVoucherAutoId', $row->payVoucherAutoId);
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $gl_codes = $row->gl_code;
        $gl_code_des = $row->gl_code_des;
        $amount = $row->amount;
        $descriptions = $row->description;
        $segment_gls = $row->segment_gl;
        $projectID = $row->projectID;
        $discountPercentage = $row->discountPercentage;
        $ProjectCategory = $row->project_categoryID;
        $ProjectSubcategory = $row->project_subCategoryID;
        $gl_text_type = $row->gl_text_type;
        $item_text = $row->item_text;
        if(!empty($item_text)) {
            $gl_text_type = $item_text;
        }
        // foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $row->segment_gls);
            $gl_code = explode('|', $row->gl_code_des);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($row->projectID);
                $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                }
            $itemDiscount = trim(($row->amount * $row->discountPercentage) / 100);
            $Transactn_amount = round($row->amount - $itemDiscount, $master['transactionCurrencyDecimalPlaces']);
            $gl_code = fetch_gl_account_desc($row->gl_codes);
            $iteminput_data[] = [
            'payVoucherAutoId' => trim($row->payVoucherAutoId),
            'projectID' => $row->projectID,
            'projectExchangeRate' => $projectCurrencyExchangerate['conversion'],
            'project_categoryID' => $row->ProjectCategory,
            'project_subCategoryID' => $row->ProjectSubcategory,
            'systemGLCode' => trim($gl_code['systemAccountCode'] ?? ''),
            'GLCode' => trim($gl_code['GLSecondaryCode'] ?? ''),
            'GLDescription' => trim($gl_code['GLDescription'] ?? ''),
            'GLType' => trim($gl_code['subCategory'] ?? ''),
            'GLAutoID' => $row->gl_codes,
            'segmentID' => trim($segment[0] ?? ''),
            'segmentCode' => trim($segment[1] ?? ''),
            'transactionCurrencyID' => $master_recode['transactionCurrencyID'],
            'transactionCurrency' => $master_recode['transactionCurrency'],
            'transactionExchangeRate' => $master_recode['transactionExchangeRate'],
            'discountPercentage' => trim($row->discountPercentage),
            'discountAmount' => trim($itemDiscount),
            'transactionAmount' => $Transactn_amount,
            'companyLocalCurrencyID' => $master_recode['companyLocalCurrencyID'],
            'companyLocalCurrency' => $master_recode['companyLocalCurrency'],
            'companyLocalExchangeRate' => $master_recode['companyLocalExchangeRate'],
            'companyLocalAmount' => ($Transactn_amount / $master_recode['companyLocalExchangeRate']),
            'companyReportingCurrencyID' => $master_recode['companyReportingCurrencyID'],
            'companyReportingCurrency' => $master_recode['companyReportingCurrency'],
            'companyReportingExchangeRate' => $master_recode['companyReportingExchangeRate'],
            'companyReportingAmount' => ($Transactn_amount/ $master_recode['companyReportingExchangeRate']),
            'partyCurrency' => $master_recode['partyCurrency'],
            'partyExchangeRate' => $master_recode['partyExchangeRate'],
            'partyAmount' => ($Transactn_amount / $master_recode['partyExchangeRate']),
            'description' => $row->descriptions,
            'type' => 'GL',
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => date('y-m-d H:i:s'),
            'companyCode' => $this->common_data['company_data']['company_code'],
            'companyID' => $this->common_data['company_data']['company_id'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdDateTime' => date('y-m-d H:i:s'),
        ];
    $this->db->insert_batch('srp_erp_paymentvoucherdetail', $iteminput_data);
    $last_id = $this->db->insert_id();
            if($isGroupByTax == 1){ 
                if(!empty($row->gl_text_type)){
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$row->gl_text_type);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            
                    $dataTax['payVoucherAutoId'] = trim($row->payVoucherAutoId);
                    $dataTax['taxFormulaMasterID'] = $row->gl_text_type;
                    $dataTax['taxDescription'] = $master['Description'];
                    $dataTax['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                    $dataTax['transactionCurrency'] = $master_recode['transactionCurrency'];
                    $dataTax['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                    $dataTax['transactionCurrencyDecimalPlaces'] = $master_recode['transactionCurrencyDecimalPlaces'];
                    $dataTax['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                    $dataTax['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                    $dataTax['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                    $dataTax['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                    $dataTax['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                    $dataTax['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];

                    tax_calculation_vat('srp_erp_paymentvouchertaxdetails',$dataTax,$row->gl_text_type,'payVoucherAutoId',trim($row->payVoucherAutoId),$row->amount,'PV',$last_id,$itemDiscount,1);
                }             
            }  
        }      
        }
        // $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->response([
                'success' => FALSE ,
                'message' => 'Payment Voucher Detail :  Save Failed ' . $this->db->_error_message()
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->db->trans_commit();
            $this->response([
                'success' => TRUE,
                'message' => 'Payment Voucher Detail :  Saved Successfully.',
                'last_id' => $last_id
            ], REST_Controller::HTTP_OK);
        }

    }
    function load_pv_conformation_get()
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
        $payVoucherId = isset($request_1->payVoucherAutoId) ? $request_1->payVoucherAutoId : null;
        $ihtml = isset($request_1->html) ? $request_1->html : null;
        $approval = isset($request_1->approval) ? $request_1->approval : null;
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($payVoucherId);
        $doc_type = isset($request_1->doc_type) ? $request_1->doc_type : null;

        //Receipt reversal doc, will generate a corresponding PV documentation.
        //Fetch
        if($doc_type == 'RRVR'){
            $receipt_reversal = $this->Receipt_reversale_model->fetch_receipt_reversal_master($payVoucherAutoId);
           
            if($receipt_reversal && isset($receipt_reversal['payVoucherAutoId'])){
                $payVoucherAutoId = $receipt_reversal['payVoucherAutoId'];
            }else{
                return false;
            }
        }
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_template_data($payVoucherAutoId);

        $data['approval'] = $approval;
        $data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($payVoucherAutoId),'PV','payVoucherAutoId');
        $this->db->select('documentID');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_paymentvouchermaster');
        $documentid = $this->db->get()->row_array();

        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PV', $payVoucherAutoId);

        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $printFooterYN=1;
        $data['printFooterYN'] = $printFooterYN;

        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;


        if (!$ihtml) {
            $data['signature'] = $this->Payment_voucher_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo'] = mPDFImage;
        if ($ihtml) {
            $data['logo'] = htmlImage;
        }

        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_print', $data, true);
        // if ($ihtml) {
        //     echo $html;
        // } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        //}
    }

    function payment_confirmation_post()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocation = $this->common_data['emplanglocationid'];
        $this->db->select('payVoucherDetailAutoID');
        $this->db->where('payVoucherAutoId', trim($this->post('PayVoucherAutoId')));
        $this->db->from('srp_erp_paymentvoucherdetail');
        $results = $this->db->get()->result_array();
        $PayVoucherAutoId = $this->post('PayVoucherAutoId');
        $currentdate = current_date(false);
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
        $itemBatchPolicy = getPolicyValues('IB', 'All');

        $mastertbl = $this->db->query("SELECT PVdate,PVchequeDate FROM `srp_erp_paymentvouchermaster` where companyID = $companyID And payVoucherAutoId = $PayVoucherAutoId ")->row_array();
        $mastertbldetail = $this->db->query("SELECT payVoucherAutoId,type  FROM `srp_erp_paymentvoucherdetail` where companyID = $companyID And type = 'Item' And payVoucherAutoId = $PayVoucherAutoId")->row_array();

        if (empty($results)) {
            $this->response([
                'success' => FALSE ,
                'message' => 'There are no records to confirm this document!'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $pvid = $this->post('PayVoucherAutoId');
            $taxamnt = 0;
            $GL = $this->db->query("SELECT TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='GL' GROUP BY payVoucherAutoId")->row_array();

            if (empty($GL)) {
                $GL = 0;
            } else {
                $GL = $GL['transactionAmount'];
            }
            $Item = $this->db->query("SELECT TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='Item' GROUP BY payVoucherAutoId")->row_array();
            if (empty($Item)) {
                $Item = 0;
            } else {
                $Item = $Item['transactionAmount'];
            }
            $debitnote = $this->db->query("SELECT TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND (type='debitnote' OR type = 'SR') GROUP BY payVoucherAutoId")->row_array();
            if (empty($debitnote)) {
                $debitnote = 0;
            } else {
                $debitnote = $debitnote['transactionAmount'];
            }
            $Advance = $this->db->query("SELECT	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='Advance' GROUP BY payVoucherAutoId")->row_array();
            if (empty($Advance)) {
                $Advance = 0;
            } else {
                $Advance = $Advance['transactionAmount'];
            }
            $Invoice = $this->db->query("SELECT	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='Invoice' GROUP BY payVoucherAutoId")->row_array();
            if (empty($Invoice)) {
                $Invoice = 0;
            } else {
                $Invoice = $Invoice['transactionAmount'];
            }
            $tax = $this->db->query("SELECT	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( taxPercentage )), 4 )))))) AS taxPercentage  FROM srp_erp_paymentvouchertaxdetails WHERE payVoucherAutoId = $pvid GROUP BY payVoucherAutoId")->row_array();
            if (empty($tax)) {
                $tax = 0;
            } else {
                $tax = $tax['taxPercentage'];
                $taxamnt = (($Item + $GL) / 100) * $tax;
            }
            $totalamnt = ($Item + $GL + $Invoice + $Advance + $taxamnt) - $debitnote;
            if ($totalamnt < 0) {
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Grand total should be greater than 0.'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->select('PayVoucherAutoId');
                $this->db->where('PayVoucherAutoId', trim($this->post('PayVoucherAutoId')));
                $this->db->where('confirmedYN', 1);
                $this->db->from('srp_erp_paymentvouchermaster');
                $Confirmed = $this->db->get()->row_array();
                if (!empty($Confirmed)) {
                    $this->response([
                        'success' => FALSE ,
                        'message' => 'Document already confirmed'
                    ], REST_Controller::HTTP_NOT_FOUND);
                } else {
                    $PayVoucherAutoId = trim($this->post('PayVoucherAutoId'));
                    //$subItemNullCount = $this->db->query("SELECT count(srp_erp_itemmaster_subtemp.subItemAutoID) as countAll FROM srp_erp_paymentvoucherdetail LEFT JOIN srp_erp_itemmaster_subtemp ON srp_erp_itemmaster_subtemp.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemmaster_subtemp.itemAutoID WHERE payVoucherAutoId = '" . $PayVoucherAutoId . "'  AND ( srp_erp_paymentvoucherdetail.itemAutoID <> NULL OR srp_erp_paymentvoucherdetail.itemAutoID != ''  ) AND (srp_erp_itemmaster_subtemp.productReferenceNo = NULL OR srp_erp_itemmaster_subtemp.productReferenceNo = '') AND srp_erp_itemmaster.isSubitemExist=1 ")->row_array();
                    $subItemNullCount = $this->db->query("SELECT
                                        count(im.isSubitemExist) AS countAll
                                    FROM
                                        srp_erp_paymentvouchermaster masterTbl
                                    LEFT JOIN srp_erp_paymentvoucherdetail detailTbl ON masterTbl.payVoucherAutoId = detailTbl.payVoucherAutoId
                                    LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = detailTbl.itemAutoID
                                    LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = detailTbl.payVoucherDetailAutoID
                                    WHERE
                                        masterTbl.payVoucherAutoId = '" . $PayVoucherAutoId . "'
                                    AND im.isSubitemExist = 1
                                    AND (
                                        ISNULL(itemMaster.productReferenceNo )
                                        OR itemMaster.productReferenceNo = '')")->row_array();
                    $isProductReference_completed = isMandatory_completed_document($PayVoucherAutoId, 'PV');

                    if ($isProductReference_completed == 0) {
                        $this->db->select('documentID, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID,PVdate');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $master_dt = $this->db->get()->row_array();
                        $this->load->library('sequence');
                        if ($master_dt['PVcode'] == "0") {
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
                                    if ($emplocation != '') {
                                        $codegeratorpv = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $emplocation, $master_dt['invYear'], $master_dt['invMonth']);
                                    } else {
                                        $this->response([
                                            'success' => FALSE ,
                                            'message' => 'Location is not assigned for current employee'
                                        ], REST_Controller::HTTP_NOT_FOUND);
                                    }
                                }

                            } else {
                                $codegeratorpv = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                            }

                            $validate_code = validate_code_duplication($codegeratorpv, 'PVcode', $PayVoucherAutoId,'PayVoucherAutoId', 'srp_erp_paymentvouchermaster');
                            if(!empty($validate_code)) {
                                $this->response([
                                    'success' => FALSE ,
                                    'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                                ], REST_Controller::HTTP_NOT_FOUND);
                            }
                            $pvCd = array(
                                'PVcode' => $codegeratorpv
                            );
                            $this->db->where('PayVoucherAutoId', trim($this->post('PayVoucherAutoId')));
                            $this->db->update('srp_erp_paymentvouchermaster', $pvCd);
                        } else {
                            $validate_code = validate_code_duplication($master_dt['PVcode'], 'PVcode', $PayVoucherAutoId,'PayVoucherAutoId', 'srp_erp_paymentvouchermaster');
                            $this->response([
                                'success' => FALSE ,
                                'message' => 'The document Code Already Exist.(' . $validate_code . ')'
                            ], REST_Controller::HTTP_NOT_FOUND);
                        }
                        $this->load->library('approvals');
                        $this->db->select('documentID,PayVoucherAutoId, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID,PVdate');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $app_data = $this->db->get()->row_array();


                        $autoApproval = get_document_auto_approval('PV');

                        if ($autoApproval == 0) {
                            if($mastertbldetail['type']!='Item') {
                                if ($PostDatedChequeManagement == 1 && ($mastertbl['PVchequeDate'] != '' || !empty($mastertbl['PVchequeDate'])) && (empty($mastertbldetail['payVoucherAutoId']) || $mastertbldetail['payVoucherAutoId']==' ')) {
                                    if ($mastertbl['PVchequeDate'] > $mastertbl['PVdate']) {
                                        if ($currentdate >= $mastertbl['PVchequeDate']) {
                                            $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                                        } else {
                                            $this->response([
                                                'success' => FALSE ,
                                                'message' => 'This is a post dated cheque document. you cannot approve this document before the cheque date!'
                                            ], REST_Controller::HTTP_NOT_FOUND);
                                        }
                                    } else {
                                        $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                                    }

                                }else
                                {
                                    $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                                }
                            }
                            else {
                                $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                            }


                        } elseif ($autoApproval == 1) {
                            $approvals_status = $this->approvals->CreateApproval('PV', $app_data['PayVoucherAutoId'], $app_data['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 0, $app_data['PVdate'], 0, $app_data['PVdate']);
                        } else {
                            $this->response([
                                'success' => FALSE ,
                                'message' => 'Approval levels are not set for this document'
                            ], REST_Controller::HTTP_NOT_FOUND);
                            exit;
                        }

                        if ($approvals_status == 1) {
                            $autoApproval = get_document_auto_approval('PV');

                            $updatedBatchNumberArray=[];

                            if($itemBatchPolicy==1){

                                $this->db->select('*');
                                $this->db->where('payVoucherAutoId', trim($this->post('PayVoucherAutoId')));
                                $this->db->from('srp_erp_paymentvoucherdetail');
                                $invoice_results = $this->db->get()->result_array();

                                $updatedBatchNumberArray=update_item_batch_number_details($invoice_results);

                            }

                            if ($autoApproval == 0) {
                                $result = $this->save_pv_approval(0, $app_data['PayVoucherAutoId'], 1, 'Auto Approved',$updatedBatchNumberArray);
                                if ($result) {

                                    $this->db->trans_commit();
                                    $this->response([
                                        'success' => TRUE,
                                        'message' => 'Document confirmed successfully.', 'code' => $app_data['PVcode']
                                    ], REST_Controller::HTTP_OK);
                                }
                            } else {
                                $data = array(
                                    'confirmedYN' => 1,
                                    'confirmedDate' => date('y-m-d H:i:s'),
                                    'confirmedByEmpID' => $this->common_data['current_userID'],
                                    'confirmedByName' => $this->common_data['current_user']
                                );
                                $this->db->where('PayVoucherAutoId', trim($this->post('PayVoucherAutoId')));
                                $this->db->update('srp_erp_paymentvouchermaster', $data);
                                $this->response([
                                    'success' => TRUE,
                                    'message' => 'Document confirmed successfully.', 'code' => $app_data['PVcode']
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
                                'message' => 'oops, something went wrong!'
                            ], REST_Controller::HTTP_NOT_FOUND);
                        }
                    } else {
                        $this->response([
                            'success' => FALSE ,
                            'message' => 'Please complete sub item configuration, fill all the mandatory fields!'
                        ], REST_Controller::HTTP_NOT_FOUND);
                    }
                }
            }


        }
    }

//////////Payment Voucher - Invoice Based////////////////

function save_inv_base_items_post()
    {
        $item_data = json_decode(file_get_contents('php://input'));
        $this->db->trans_start();
        if(!empty($item_data)){
            $date_time = date('Y-m-d H:i:s');
            // print_r($item_data);
            // exit();
            // $iteminput_data = [];
        foreach ($item_data as $row){
        $InvoiceAutoID = $row->InvoiceAutoID;
        $settlementAmount =  $row->settlementAmount;
        $payVoucherAutoId = $row->payVoucherAutoId;
        //$this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID, supplierliabilitySystemGLCode, supplierliabilityGLAccount,companyReportingCurrency, supplierliabilityDescription , supplierliabilityType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode,RefNo,bookingDate,comments,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID, supplierliabilitySystemGLCode, supplierliabilityGLAccount,companyReportingCurrency, supplierliabilityDescription , supplierliabilityType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode,RefNo,bookingDate,comments,	(
            (
            (
            ( IFNULL( tax.taxPercentage, 0 ) / 100 ) * (
            ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) - (
            ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) 
            ) 
            ) 
            ) + ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) 
            ) - (
            ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * ( IFNULL( sid.transactionAmount, 0 ) + IFNULL( taxAmount, 0 ) ) 
            ) 
            ) AS transactionAmount,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount, IFNULL( SUM( taxAmount ), 0 ) AS taxAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid', 'srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID', 'left');
        $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax', 'tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID', 'left');
        $this->db->where_in('srp_erp_paysupplierinvoicemaster.InvoiceAutoID', $row->InvoiceAutoID);
        $master_recode = $this->db->get()->result_array();
        $amount = $row->amount;
        $am_arr = []; $inv_arr = []; $re_arr = [];
        //foreach($row->InvoiceAutoID as $key=>$row){
            $am_arr = $row->amount;
        //}
        
        for ($i = 0; $i < count($master_recode); $i++) {
            $invAutoID=$master_recode[$i]['InvoiceAutoID'];
            $due_amount = ($master_recode[$i]['transactionAmount'] - ($master_recode[$i]['paymentTotalAmount'] + $master_recode[$i]['DebitNoteTotalAmount'] + $master_recode[$i]['advanceMatchedTotal']));
            $data[$i]['payVoucherAutoId'] = $row->payVoucherAutoId;
            $data[$i]['InvoiceAutoID'] = $master_recode[$i]['InvoiceAutoID'];
            $data[$i]['type'] = 'Invoice';
            $data[$i]['bookingInvCode'] = $master_recode[$i]['bookingInvCode'];
            $data[$i]['referenceNo'] = $master_recode[$i]['RefNo'];
            $data[$i]['bookingDate'] = $master_recode[$i]['bookingDate'];
            $data[$i]['GLAutoID'] = $master_recode[$i]['supplierliabilityAutoID'];
            $data[$i]['systemGLCode'] = $master_recode[$i]['supplierliabilitySystemGLCode'];
            $data[$i]['GLCode'] = $master_recode[$i]['supplierliabilityGLAccount'];
            $data[$i]['GLDescription'] = $master_recode[$i]['supplierliabilityDescription'];
            $data[$i]['GLType'] = $master_recode[$i]['supplierliabilityType'];
            $data[$i]['description'] = $master_recode[$i]['comments'];
            $data[$i]['Invoice_amount'] = $master_recode[$i]['transactionAmount'];
            $data[$i]['due_amount'] = $due_amount;
            if (isset($data[$i]['due_amount']) && isset($am_arr[$invAutoID])) {
                $data[$i]['balance_amount'] = ($data[$i]['due_amount'] - (float)$am_arr[$invAutoID]);
            } else {
                $data[$i]['balance_amount'] = null; // or any default value
            }
            // $data[$i]['balance_amount'] = ($data[$i]['due_amount'] - (float)$am_arr[$invAutoID]);
            $data[$i]['transactionCurrencyID'] = $master_recode[$i]['transactionCurrencyID'];
            $data[$i]['transactionCurrency'] = $master_recode[$i]['transactionCurrency'];
            $data[$i]['transactionExchangeRate'] = $master_recode[$i]['transactionExchangeRate'];
            if (isset($am_arr[$invAutoID])) {
                $data[$i]['transactionAmount'] = (float)$am_arr[$invAutoID];
            } else {
                $data[$i]['transactionAmount'] = 0.0; // or any default value
            }
            // $data[$i]['transactionAmount'] = (float)$am_arr[$invAutoID];
            $data[$i]['companyLocalCurrencyID'] = $master_recode[$i]['companyLocalCurrencyID'];
            $data[$i]['companyLocalCurrency'] = $master_recode[$i]['companyLocalCurrency'];
            $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
            $data[$i]['companyReportingCurrencyID'] = $master_recode[$i]['companyReportingCurrencyID'];
            $data[$i]['companyReportingCurrency'] = $master_recode[$i]['companyReportingCurrency'];
            $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
            $data[$i]['partyCurrencyID'] = $master_recode[$i]['supplierCurrencyID'];
            $data[$i]['partyCurrency'] = $master_recode[$i]['supplierCurrency'];
            $data[$i]['partyExchangeRate'] = $master_recode[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['supplierCurrencyExchangeRate']);
            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime'] = $date_time;
            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = $date_time;
            if (isset($InvoiceAutoID[$i])) {
                $grv_m[$i]['InvoiceAutoID'] = $InvoiceAutoID[$i];
            } else {
                // Handle the case when the index $i is not set
                $grv_m[$i]['InvoiceAutoID'] = null; // or any default value
            }
            // $grv_m[$i]['InvoiceAutoID'] = $InvoiceAutoID[$i];
            if (
                isset($master_recode[$i]['paymentTotalAmount']) &&
                isset($am_arr[$invAutoID])
            ) {
                $grv_m[$i]['paymentTotalAmount'] = $master_recode[$i]['paymentTotalAmount'] + $am_arr[$invAutoID];
            } else {
                $grv_m[$i]['paymentTotalAmount'] = 0; // or any default value
            }
            // $grv_m[$i]['paymentTotalAmount'] = ($master_recode[$i]['paymentTotalAmount'] + $am_arr[$invAutoID]);
            $grv_m[$i]['paymentInvoiceYN'] = 0;
            if ($data[$i]['balance_amount'] <= 0) {
                $grv_m[$i]['paymentInvoiceYN'] = 1;
            }
        }
        $data_up_settlement['settlementTotal'] = $settlementAmount;
        }
    }
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->update('srp_erp_paymentvouchermaster', $data_up_settlement);

        if (!empty($data)) {
            $this->db->update_batch('srp_erp_paysupplierinvoicemaster', $grv_m, 'InvoiceAutoID');
            $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'success' => FALSE ,
                    'message' => 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->session->set_flashdata('s', 'Supplier Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . ');
                $this->db->trans_commit();
                $this->response([
                    'success' => TRUE,
                    'message' => 'Supplier Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . '
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'success' => FALSE ,
                'message' => 'Something went wrong!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
}
function fetch_detail_get()
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
        $payVoucherAutoId = isset($request_1->payVoucherAutoId) ? $request_1->payVoucherAutoId : null;
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $pvDetails = [];
        $this->db->select('*,'.$item_code_alias.'');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID','left');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $pvDetails = $this->db->get()->result_array();
        $this->response([
            'data' => $pvDetails,
            'success' => TRUE,
            'message' => 'Payment Voucher details Retrieved.'
        ], REST_Controller::HTTP_OK);
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
