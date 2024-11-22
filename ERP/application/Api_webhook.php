<?php
/**
 * Created by PhpStorm.
 * User: Safry
 * Date: 13-Mar-19
 * Time: 5:39 PM
 */

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
 *      VALUES (1, 'r4B1UiL920kan6@FugxT@q$ZQ%rha5ttA&D^c0V', 3, 5, 20171010, 13);
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
 *  4.5 [POST] Payment Voucher
 *      BASE_PATH/payment_voucher
 *
 * {
 * "document_date": "2019-03-22",
 * "cheque_date": "2019-05-22",
 * "user_id" : 1138,
 * "currency_id" : 2,
 * "erp_supplier_id" : 19,
 * "erp_chart_of_account_id" : 5122,
 * "reference" : "xxx",
 * "detail" :
 * [
 * {
 * "erp_chart_of_account_id" : 5136,
 * "amount" : 100,
 * "description" : "transaction 1"
 * },{
 * "erp_chart_of_account_id" : 5136,
 * "amount" : 150,
 * "description" : "transaction 2"
 * }
 * ]
 * }
 *
 *  4.6 [POST] Invoice
 *      BASE_PATH/invoice
 *
 * {
 * "document_date": "2019-03-22",
 * "cheque_date": "2019-05-22",
 * "user_id" : 1138,
 * "currency_id" : 2,
 * "erp_supplier_id" : 19,
 * "erp_chart_of_account_id" : 5122,
 * "reference" : "xxx",
 * "detail" :
 * [
 * {
 * "erp_chart_of_account_id" : 5136,
 * "amount" : 100,
 * "description" : "transaction 1"
 * },{
 * "erp_chart_of_account_id" : 5136,
 * "amount" : 150,
 * "description" : "transaction 2"
 * }
 * ]
 * }
 *
 * *  4.7 [POST] Receipt
 *      BASE_PATH/receipt
 *
 * {
 * "document_date": "2019-03-22",
 * "cheque_date": "2019-05-22",
 * "user_id" : 1138,
 * "currency_id" : 2,
 * "erp_supplier_id" : 19,
 * "erp_chart_of_account_id" : 5122,
 * "reference" : "xxx",
 * "detail" :
 * [
 * {
 * "erp_chart_of_account_id" : 5136,
 * "amount" : 100,
 * "description" : "transaction 1"
 * },{
 * "erp_chart_of_account_id" : 5136,
 * "amount" : 150,
 * "description" : "transaction 2"
 * }
 * ]
 * }
 *
 *
 * Functions
 * =========
 * 1.chart_of_account_get($limit,$keyword) to get chart of account [$limit {{no of records in the response}}, $keyword{{search this value}}]
 *
 *
 *
 * */

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_webhook extends REST_Controller
{
    private $company_info;
    private $company_id = 0;
    private $company_code;
    private $limit = 10;
    private $keyword;
    private $tmp_output;
    private $document_date;
    private $user_id;
    private $user_name;
    private $user_code;
    private $user;
    private $general_segment_id;
    private $general_segment_code;
    private $post;
    private $finance_year;
    private $finance_period;
    private $chart_of_account;

    private $error_in_validation = false;
    private $error_data;


    function __construct()
    {
        parent::__construct();
        $this->post = json_decode(file_get_contents('php://input'));
       
       
        $this->load->model('Api_erp_model');
        $this->load->model('Double_entry_model');
        $this->load->model('session_model');
        $this->load->model('Pos_kitchen_model');
        $this->load->library('sequence');
        $this->load->library('Approvals');
        $this->load->library('S3');
        $this->auth();
       // $this->set_limit();
       // $this->set_keyword();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($this->post->user_id)) {
                $this->set_user();
                $this->auth_user();
            }
        }
    }

    /** ---------------------------  INIT FUNCTIONS ---------------------------  */
    private function set_limit($limit = 10)
    {
        if (isset($_GET['limit']) && $_GET['limit'] > 0) {
            $limit = $_GET['limit'];
            /* make valid this => ?limit=10&search=HMS/BSA000010*/
        }
        $this->limit = $limit;
    }

    private function set_general_segment()
    {

        $this->general_segment_id = $this->Api_erp_model->get_default_segment($this->company_id);
        $this->general_segment_code = $this->Api_erp_model->get_segment_code($this->general_segment_id);
    }

    protected function auth()
    {
        if (isset($_SERVER['HTTP_SME_API_KEY'])) {
            $result = $this->db->select('*')
                ->from('keys')
                ->where('key', $_SERVER['HTTP_SME_API_KEY'])
                ->get()->row_array();

                //echo var_dump($result);
               // die();
            if (!empty($result)) {
                $tmpCompanyInfo = $this->db->select('*')
                    ->from('srp_erp_company')
                    ->where('company_id', $result['company_id'])
                    ->get()->row_array();
               $this->company_info = $tmpCompanyInfo;
            //  die();
                $this->company_code = $tmpCompanyInfo['company_code'];
                if (!empty($this->company_info)) {
                    $this->company_id = $this->company_info['company_id'];
                    $this->setDb();
                    $this->set_general_segment();
                    $this->set_common_data();
                    $this->company_info = $this->db->select('*')
                        ->from('srp_erp_company')
                        ->where('company_id', $this->company_id)
                        ->get()->row_array();
                    return true;
                } else {
                    echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Company ID not found'), 500);
                }
            } else {
                //return true;
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Invalid API Key'), 500);
                exit;
            }
        } else {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'API Key Not Found'), 500);
            exit;
        }
    }

    private function set_common_data()
    {
        $CI =& get_instance();
        $CI->common_data['status'] = TRUE;
        $CI->common_data['company_data']['company_id'] = $this->company_id;
        $CI->common_data['company_data'] = $this->company_info;
        $CI->common_data['company_policy'] = $CI->Session_model->fetch_company_policy($this->company_id);
        $CI->common_data['controlaccounts'] = 0;
        $CI->common_data['ware_houseID'] = 0;
        $CI->common_data['imagePath'] = '';
        $CI->common_data['current_pc'] = 'API';

        $CI->common_data['current_user'] = $this->user_name;
        $CI->common_data['current_userID'] = $this->user_id;
        $CI->common_data['current_userCode'] = $this->user_code;
        $CI->common_data['user_group'] = 0;

        $CI->common_data['isGroupUser'] = 0;
        $CI->common_data['current_date'] = date('Y-m-d h:i:s');
        $CI->common_data['timezoneID'] = null;
        $CI->common_data['timezoneDescription'] = null;
        $CI->common_data['emplangid'] = 0;
        $CI->common_data['emplanglocationid'] = 0;
    }


    
    private function set_keyword()
    {
        $query = isset($_GET['search']) ? $_GET['search'] : '';
        $this->keyword = $query;
    }

    private function castToInt()
    {

        if (isset($this->tmp_output) && !empty($this->tmp_output)) {
            $i = 0;
            $data = [];
            foreach ($this->tmp_output as $value) {
                $data[$i]['value'] = (int)$value['value'];
                $data[$i]['label'] = $value['label'];
                $i++;
            }
            if (!empty($data)) {
                $this->tmp_output = $data;
            }
        }

    }

    private function set_user()
    {
        if (!isset($this->post->user_id)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'user_id not found!'), 500);
            exit;
        }
        $this->user_id = $this->post->user_id;
        $this->user = $this->db->select('*')
            ->from('srp_employeesdetails')
            ->where('EIdNo', $this->user_id)->get()->row_array();
        if (!empty($this->user)) {
            $this->user_name = $this->user['Ename4'];
            $this->user_code = $this->user['ECode'];
        }
    }

    protected function setDb()
    {
        if (!empty($this->company_info)) {
            $config['hostname'] = trim($this->encryption->decrypt($this->company_info["host"]));
            $config['username'] = trim($this->encryption->decrypt($this->company_info["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($this->company_info["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($this->company_info["db_name"]));
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


    /** --------------------------- REQUEST --------------------------- */


public function  listner_get(){
    echo '{"success":"ok"}';
    
}

public function  items_get(){
    
    $CI =& get_instance();
        $CI->db->from('srp_erp_itemmaster');
       // $CI->db->where($where);
        $query = $CI->db->get();
        $data= $query->result_array();
        $output = array('type' => 'success', 'status' => 200, 'message' => 'all items', 'data' => $data);
      return   $this->response($output);
    
}


public function  items_post(){
    
    $CI =& get_instance();
        $CI->db->from('srp_erp_itemmaster');
       // $CI->db->where($where);
        $query = $CI->db->get();
        $data= $query->result_array();
        $output = array('type' => 'success', 'status' => 200, 'message' => 'all items', 'data' => $data);
      return   $this->response($output);
    
}










    public function payment_voucher_post()
    {
        $this->validate_post();
        /** Headers  */
        $date_format_policy = 'Y-m-d';
        $PVdate = input_format_date($this->post->document_date, $date_format_policy);
        $PVcheqDate = $this->post->cheque_date;
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);

        $glIDs = array(
            $this->post->erp_chart_of_account_id
        );
        foreach ($this->post->detail as $key => $detail) {
            $glIDs[] = $detail->erp_chart_of_account_id;
        }
        $gl_status = $this->Api_erp_model->check_gl_status($glIDs);
        if (!empty($gl_status)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'The linked ERP Chart of Account has been made inactive, activate it and try again', 'data' => $gl_status), 500);
            exit();
        }
        $data_master['PVbankCode'] = $this->post->erp_chart_of_account_id;
        $bank_detail = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $data_master['PVbankCode']);
        if (empty($bank_detail)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
            exit;
        }

        if (isset($this->post->reference)) {
            $RVchequeNo = $this->db->query("SELECT COUNT(payVoucherAutoId) as isexistcount FROM srp_erp_paymentvouchermaster WHERE
			                                companyID = {$this->company_id} AND referenceNo = '{$this->post->reference}' AND isDeleted=0")->row('isexistcount');
            if ($RVchequeNo > 0) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Reference No already exist'), 500);
                exit();
            }
        }

        $data_master['documentID'] = 'PV';
        $data_master['companyFinanceYearID'] = $this->get_finance_year_id();
        $data_master['companyFinancePeriodID'] = $this->get_finance_period_id();
        $data_master['PVdate'] = trim($PVdate);
        $data_master['PVNarration'] = $this->post->reference;
        $data_master['accountPayeeOnly'] = 0;
        $data_master['segmentID'] = $this->post->erp_segment_id;
        $data_master['segmentCode'] = $this->Api_erp_model->get_segment_code($this->post->erp_segment_id);
        $data_master['bankGLAutoID'] = $bank_detail->GLAutoID;
        $data_master['bankSystemAccountCode'] = $bank_detail->systemAccountCode;
        $data_master['bankGLSecondaryCode'] = $bank_detail->GLSecondaryCode;
        $data_master['bankCurrencyID'] = $bank_detail->bankCurrencyID;
        $data_master['bankCurrency'] = $this->Api_erp_model->get_currency_code($bank_detail->bankCurrencyID);
        $data_master['PVbank'] = $bank_detail->bankName;
        $data_master['PVbankBranch'] = $bank_detail->bankBranch;
        $data_master['PVbankSwiftCode'] = $bank_detail->bankSwiftCode;
        $data_master['PVbankAccount'] = $bank_detail->bankAccountNumber;
        $data_master['PVbankType'] = $bank_detail->subCategory;

        if ($bank_detail->isCash == 1) {
            $data_master['PVchequeNo'] = null;
            $data_master['PVchequeDate'] = null;
        } else {
            if (isset($this->post->paymentType) && $this->post->paymentType == 2 && isset($this->post->vouchertype) && $this->post->vouchertype == 'Supplier') {
                $data_master['PVchequeNo'] = null;
                $data_master['PVchequeDate'] = null;
            } else {
                $data_master['PVchequeNo'] = isset($this->post->cheque_no) ? $this->post->cheque_no : null;
                $data_master['PVchequeDate'] = isset($this->post->cheque_date) ? $this->post->cheque_date : null;
            }
        }

        $data_master['modeOfPayment'] = isset($bank_detail->isCash) && ($bank_detail->isCash == 1) ? 1 : 2;
        $data_master['pvType'] = 'Supplier';
        $data_master['referenceNo'] = trim_desc($this->post->reference);
        $data_master['transactionCurrencyID'] = $this->post->currency_id;
        $data_master['transactionCurrency'] = $this->Api_erp_model->get_currency_code($this->post->currency_id);
        $data_master['transactionExchangeRate'] = 1;
        $data_master['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_master['transactionCurrencyID']);
        $data_master['companyLocalCurrencyID'] = $this->company_info['company_default_currencyID'];
        $data_master['companyLocalCurrency'] = $this->company_info['company_default_currency'];
        $default_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['companyLocalCurrencyID']);
        $data_master['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data_master['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data_master['companyReportingCurrency'] = !empty($this->company_info['companyReportingCurrency']) ? $this->company_info['companyReportingCurrency'] : 0;
        $data_master['companyReportingCurrencyID'] = $this->company_info['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['companyReportingCurrencyID']);
        $data_master['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data_master['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $bank_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['bankCurrencyID']);
        $data_master['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data_master['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        $supplier_arr = $this->Api_erp_model->fetch_supplier_data($this->post->erp_supplier_id);
        $data_master['partyType'] = 'SUP';
        $data_master['partyID'] = $this->post->erp_supplier_id;
        $data_master['partyCode'] = $supplier_arr->supplierSystemCode;
        $data_master['partyName'] = $supplier_arr->supplierName;
        $data_master['partyAddress'] = $supplier_arr->supplierAddress1 . ' ' . $supplier_arr->supplierAddress2;
        $data_master['partyTelephone'] = $supplier_arr->supplierTelephone;
        $data_master['partyFax'] = $supplier_arr->supplierFax;
        $data_master['partyEmail'] = $supplier_arr->supplierEmail;
        $data_master['partyGLAutoID'] = $supplier_arr->liabilityAutoID;
        $data_master['partyGLCode'] = $supplier_arr->liabilitySystemGLCode;
        $data_master['partyCurrencyID'] = $supplier_arr->supplierCurrencyID;
        $data_master['partyCurrency'] = $supplier_arr->supplierCurrency;
        $data_master['partyExchangeRate'] = $data_master['transactionExchangeRate'];
        $data_master['partyCurrencyDecimalPlaces'] = $supplier_arr->supplierCurrencyDecimalPlaces;
        $data_master['companyCode'] = $this->company_info['company_code'];
        $data_master['companyID'] = $this->company_id;
        $data_master['createdPCID'] = $this->common_data['current_pc'];
        $data_master['createdUserID'] = $this->user_id;
        $data_master['createdDateTime'] = date('Y-m-d G:i:s');
        $data_master['createdUserName'] = $this->user['Ename3'];
        $data_master['PVcode'] = $this->sequence_generator('PV');


        /**confirmed*/
        $data_master['isSytemGenerated'] = 1;
        $data_master['confirmedYN'] = 1;
        $data_master['confirmedByEmpID'] = $this->user_id;
        $data_master['confirmedByName'] = $this->user['Ename4'];
        $data_master['confirmedDate'] = $this->common_data['current_date'];

        /** Approval */
        $data_master['approvedYN'] = 1;
        $data_master['approvedbyEmpID'] = $this->user_id;
        $data_master['approvedbyEmpName'] = $this->user['Ename4'];
        $data_master['approvedDate'] = $this->common_data['current_date'];


        $this->db->insert('srp_erp_paymentvouchermaster', $data_master);
        $pv_header_id = $this->db->insert_id();

        $this->common_data['current_userID'] = $this->user_id;
        $this->common_data['current_user'] = $this->user['Ename4'];
        $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data_master['documentID'], $pv_header_id, $data_master['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'payVoucherAutoId', 1, $data_master['PVdate']);
        $approval_levels = $this->approvals->maxlevel('PV');
        $number_of_levels = $approval_levels['levelNo'];
        for ($i = 1; $i <= $number_of_levels; $i++) {
            $this->approvals->approve_without_sending_email($pv_header_id, $i, 1, '', $data_master['documentID']);
        }

        /** PV Detail */

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate,partyCurrency,partyExchangeRate');
        $this->db->where('payVoucherAutoId', $pv_header_id);
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();


        foreach ($this->post->detail as $key => $detail) {

            $chart_of_account = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
            if (empty($chart_of_account)) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
                exit;
            }
            $data[$key]['GLAutoID'] = $detail->erp_chart_of_account_id; // input
            $data[$key]['systemGLCode'] = $chart_of_account->systemAccountCode;
            $data[$key]['GLCode'] = $chart_of_account->GLSecondaryCode;
            $data[$key]['GLDescription'] = $chart_of_account->GLDescription;
            $data[$key]['GLType'] = $chart_of_account->subCategory;

            /** from input */
            $data[$key]['transactionAmount'] = $detail->amount;
            $data[$key]['companyLocalAmount'] = ($detail->amount / $master_recode['companyLocalExchangeRate']);
            $data[$key]['companyReportingAmount'] = ($detail->amount / $master_recode['companyReportingExchangeRate']);
            $data[$key]['partyAmount'] = $master_recode['partyExchangeRate'] > 0 ? ($detail->amount / $master_recode['partyExchangeRate']) : 0;
            $data[$key]['description'] = $detail->description;


            /** General Segment */
            $data[$key]['segmentID'] = $detail->erp_segment_id;
            $data[$key]['segmentCode'] = $this->Api_erp_model->get_segment_code($detail->erp_segment_id);

            /** from Masters and common data  */
            $data[$key]['payVoucherAutoId'] = $pv_header_id; // done
            $data[$key]['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
            $data[$key]['transactionCurrency'] = $master_recode['transactionCurrency'];
            $data[$key]['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
            $data[$key]['partyCurrency'] = $master_recode['partyCurrency'];
            $data[$key]['partyExchangeRate'] = $master_recode['partyExchangeRate'];
            $data[$key]['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
            $data[$key]['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
            $data[$key]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
            $data[$key]['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
            $data[$key]['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
            $data[$key]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
            $data[$key]['type'] = 'GL';
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
        }

        $doubleEntries = $this->Double_entry_model->fetch_double_entry_payment_voucher_data($pv_header_id, 'PV');
        if (!empty($doubleEntries)) {
            /**Bank Ledger Entry*/
            if (!empty($doubleEntries['master_data'])) {
                $this->Api_erp_model->payment_voucher_bank_ledger($doubleEntries);
            }
            $this->Api_erp_model->payment_voucher_general_ledger($doubleEntries);
        }

        $data_master['document_id'] = $pv_header_id;
        $this->insert_document_entry($pv_header_id, 'PV');

        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully posted', 'data' => $data_master);
        $this->response($output);

    }


    public function chart_of_account_get($id = 0)
    {

        if (isset($_GET['currency_id']) && $_GET['currency_id'] > 0 && isset($_GET['is_bank']) && $_GET['is_bank'] == 1) {
            $this->db->select('GLAutoID as value, concat(IFNULL(systemAccountCode,\'-\')," | " ,IFNULL(GLDescription,\'-\'), " | ", IFNULL(subCategory,\'-\'), " | ", IFNULL(bankCurrencyCode,\'-\')) as label');
        } else {
            $this->db->select('GLAutoID as value, concat(IFNULL(systemAccountCode,\'-\')," | " ,IFNULL(GLDescription,\'-\'), " | ", IFNULL(subCategory,\'-\')) as label');
        }

        $this->db->from('srp_erp_chartofaccounts')
            ->where('companyID', $this->company_id, FALSE);

        if ($id > 0) {
            $this->db->where('GLAutoID', $id);
        } else {
            if (isset($this->keyword) && !empty($this->keyword)) {
                $this->db->where('(GLAutoID LIKE \'%' . $this->keyword . '%\' OR GLAutoID LIKE \'%' . $this->keyword . '%\' OR GLDescription LIKE \'%' . $this->keyword . '%\' OR systemAccountCode LIKE \'%' . $this->keyword . '%\' OR subCategory LIKE \'%' . $this->keyword . '%\' )', null, false);
            }
        }

        if (isset($_GET['is_bank']) && $_GET['is_bank'] == 0) {
            /* NOT BANK */
            $this->db->where('isBank', $_GET['is_bank']);
        } else if (isset($_GET['is_bank']) && $_GET['is_bank'] == 1) {
            /* ONLY BANKS */
            $this->db->where('isBank', $_GET['is_bank']);
            if (isset($_GET['currency_id']) && $_GET['currency_id'] > 0) {
                $this->db->where('bankCurrencyID', $_GET['currency_id']);
            }
        }

        if (isset($_GET['is_control_account']) && $_GET['is_control_account'] == 1) {
            $this->db->where('controllAccountYN', 1);
        } else {
            $this->db->where('controllAccountYN', 0);
        }


        $this->db->where('masterAccountYN', 0);
        $this->db->where('isActive', 1);


        $result = $this->db->limit($this->limit)->get()->result_array();
        $this->tmp_output = $result;
        $this->castToInt();
        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully retrieved', 'data' => $this->tmp_output);
        $this->response($output);
    }

    public function supplier_master_get($id = 0)
    {
        $this->db->select('supplierAutoID as value, concat(supplierSystemCode," | " ,supplierName, " | ", supplierName) as label')
            ->from('srp_erp_suppliermaster')
            ->where('companyID', $this->company_id, FALSE);

        if ($id > 0) {
            $this->db->where('supplierAutoID', $id);
        } else {
            if (isset($this->keyword) && !empty($this->keyword)) {
                $this->db->where('(supplierSystemCode LIKE \'%' . $this->keyword . '%\' OR supplierName LIKE \'%' . $this->keyword . '%\' OR supplierName LIKE \'%' . $this->keyword . '%\' OR supplierAutoID LIKE \'%' . $this->keyword . '%\')', null, false);
            }
        }

        $result = $this->db->limit($this->limit)->get()->result_array();
        $this->tmp_output = $result;
        $this->castToInt();
        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully retrieved', 'data' => $this->tmp_output);
        $this->response($output);

    }

    public function customer_master_get($id = 0)
    {
        $this->db->select('customerAutoID as value, concat(customerSystemCode," | " ,customerName, " | ", customerAddress1) as label')
            ->from('srp_erp_customermaster')
            ->where('companyID', $this->company_id, FALSE);


        if ($id > 0) {
            $this->db->where('customerAutoID', $id);
        } else {
            if (isset($this->keyword) && !empty($this->keyword)) {
                $this->db->where('(customerAutoID LIKE \'%' . $this->keyword . '%\' OR customerSystemCode LIKE \'%' . $this->keyword . '%\' OR customerName LIKE \'%' . $this->keyword . '%\' OR customerAddress1 LIKE \'%' . $this->keyword . '%\')', null, false);
            }
        }

        $result = $this->db->limit($this->limit)->get()->result_array();

        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully retrieved', 'data' => $result);
        $this->response($output);
    }


    public function user_get($id = 0)
    {
        $this->db->select('EIdNo as value, concat(IFNULL(Ename3,"")," | " ,IFNULL(Ename4,""), " | ", IFNULL(EEmail, "")) as label')
            ->from('srp_employeesdetails')
            ->where('Erp_companyID', $this->company_id, FALSE)
            ->where('isActive', 1);

        if ($id > 0) {
            $this->db->where('EIdNo', $id);
        } else {
            if (isset($this->keyword) && !empty($this->keyword)) {
                $this->db->where('(Ename3 LIKE \'%' . $this->keyword . '%\' OR Ename4 LIKE \'%' . $this->keyword . '%\' OR EEmail LIKE \'%' . $this->keyword . '%\')', null, false);
            }
        }

        $this->tmp_output = $this->db->limit($this->limit)->get()->result_array();
        $this->castToInt();
        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully retrieved', 'data' => $this->tmp_output);
        $this->response($output);
    }

    public function customer_invoice_post()
    {
        $this->validate_post();

        $glIDs = array();//$this->post->erp_chart_of_account_id
        foreach ($this->post->detail as $key => $detail) {
            $glIDs[] = $detail->erp_chart_of_account_id;
        }
        $gl_status = $this->Api_erp_model->check_gl_status($glIDs);
        if (!empty($gl_status)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'The linked ERP Chart of Account has been made inactive, activate it and try again', 'data' => $gl_status), 500);
            exit();
        }

        if (isset($this->post->reference_id)) {
            $RVchequeNo = $this->db->query("SELECT COUNT(invoiceAutoID) as isexistcount FROM srp_erp_customerinvoicemaster WHERE
			                                companyID = {$this->company_id} AND referenceNo = '{$this->post->reference_id}' AND isDeleted=0")->row('isexistcount');
            if ($RVchequeNo > 0) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Reference No already exist'), 500);
                exit();
            }
        }

        $customer_arr = $this->Api_erp_model->fetch_customer_data($this->post->erp_customer_id);

        $data_master['invoiceType'] = 'Standard';
        $data_master['invoiceDate'] = $this->post->document_date;
        $data_master['invoiceDueDate'] = $this->post->document_date;
        $data_master['invoiceDate'] = $this->post->document_date;
        $data_master['companyFinanceYearID'] = $this->get_finance_year_id();
        $data_master['companyFinanceYear'] = !empty($this->finance_year) ? $this->finance_year->beginingDate . ' - ' . $this->finance_year->endingDate : '';
        $data_master['FYBegin'] = !empty($this->finance_year) ? $this->finance_year->beginingDate : null;
        $data_master['FYEnd'] = !empty($this->finance_year) ? $this->finance_year->endingDate : null;
        $data_master['companyFinancePeriodID'] = $this->get_finance_period_id();
        $data_master['FYPeriodDateFrom'] = !empty($this->finance_period) ? $this->finance_period->dateFrom : null;
        $data_master['FYPeriodDateTo'] = !empty($this->finance_period) ? $this->finance_period->dateTo : null;
        $data_master['documentID'] = 'CINV';
        $data_master['customerID'] = $this->post->erp_customer_id;

        $data_master['customerSystemCode'] = $customer_arr->customerSystemCode;
        $data_master['customerName'] = $customer_arr->customerName;
        $data_master['customerAddress'] = $customer_arr->customerAddress1;
        $data_master['customerTelephone'] = $customer_arr->customerTelephone;
        $data_master['customerFax'] = $customer_arr->customerFax;
        $data_master['customerReceivableAutoID'] = $customer_arr->receivableAutoID;
        $data_master['customerReceivableSystemGLCode'] = $customer_arr->receivableSystemGLCode;
        $data_master['customerReceivableGLAccount'] = $customer_arr->receivableGLAccount;
        $data_master['customerReceivableDescription'] = $customer_arr->receivableDescription;
        $data_master['customerReceivableType'] = $customer_arr->receivableType;
        ///////////////////////////////////////////////////////////////////
        $data_master['transactionCurrency'] = $this->post->currency_id;
        $data_master['segmentID'] = $this->post->erp_segment_id;
        $data_master['segmentCode'] = $this->Api_erp_model->get_segment_code($this->post->erp_segment_id);
        $data_master['referenceNo'] = isset($this->post->reference_id) ? $this->post->reference_id : '';
        //$data_master['supplierInvoiceNo'] = isset($this->post->reference_number) ? $this->post->reference_number : '';
        $data_master['invoiceNarration'] = isset($this->post->reference) ? $this->post->reference : '';

        $data_master['transactionCurrencyID'] = $this->post->currency_id;
        $data_master['transactionCurrency'] = $this->Api_erp_model->get_currency_code($this->post->currency_id);
        $data_master['transactionExchangeRate'] = 1;
        $data_master['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($this->post->currency_id);
        $data_master['companyLocalCurrencyID'] = $this->company_info['company_default_currencyID'];
        $data_master['companyLocalCurrency'] = $this->company_info['company_default_currency'];
        $default_currency = currency_conversionID($this->post->currency_id, $this->company_info['company_default_currencyID']);
        $data_master['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data_master['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data_master['companyReportingCurrency'] = !empty($this->company_info['companyReportingCurrency']) ? $this->company_info['companyReportingCurrency'] : 0;
        $data_master['companyReportingCurrencyID'] = $this->company_info['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($this->post->currency_id, $data_master['companyReportingCurrencyID']);
        $data_master['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data_master['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        $data_master['customerCurrencyID'] = $customer_arr->customerCurrencyID;
        $data_master['customerCurrency'] = $customer_arr->customerCurrency;
        $customerCurrency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['customerCurrencyID']);
        $data_master['customerCurrencyExchangeRate'] = $customerCurrency['conversion'];
        $data_master['customerCurrencyDecimalPlaces'] = $customerCurrency['DecimalPlaces'];

        $data_master['modifiedPCID'] = $this->common_data['current_pc'];
        $data_master['modifiedUserID'] = $this->common_data['current_userID'];
        $data_master['modifiedUserName'] = $this->common_data['current_user'];
        $data_master['modifiedDateTime'] = $this->common_data['current_date'];

        $data_master['companyCode'] = $this->company_code;
        $data_master['companyID'] = $this->company_id;
        $data_master['createdUserGroup'] = $this->common_data['user_group'];
        $data_master['createdPCID'] = $this->common_data['current_pc'];
        $data_master['createdUserID'] = $this->user_id;
        $data_master['createdUserName'] = $this->user_name;
        $data_master['createdDateTime'] = $this->common_data['current_date'];
        $data_master['invoiceCode'] = $this->sequence_generator('CINV');

        /**confirmed*/
        $data_master['confirmedYN'] = 1;
        $data_master['isSytemGenerated'] = 1;
        $data_master['confirmedByEmpID'] = $this->user_id;
        $data_master['confirmedByName'] = $this->user['Ename4'];
        $data_master['confirmedDate'] = $this->common_data['current_date'];

        /** Approval */
        $data_master['approvedYN'] = 1;
        $data_master['approvedbyEmpID'] = $this->user_id;
        $data_master['approvedbyEmpName'] = $this->user['Ename4'];
        $data_master['approvedDate'] = $this->common_data['current_date'];

        $this->db->insert('srp_erp_customerinvoicemaster', $data_master);
        $header_id = $this->db->insert_id();

        $this->common_data['current_userID'] = $this->user_id;
        $this->common_data['current_user'] = $this->user['Ename4'];
        $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data_master['documentID'], $header_id, $data_master['invoiceCode'], 'Invoice', 'srp_erp_customerinvoicemaster', 'invoiceAutoID', 1, $data_master['invoiceDate']);

        $approval_levels = $this->approvals->maxlevel('CINV');
        $number_of_levels = $approval_levels['levelNo'];
        for ($i = 1; $i <= $number_of_levels; $i++) {
            $this->approvals->approve_without_sending_email($header_id, $i, 1, '', $data_master['documentID']);
        }

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('invoiceAutoID', $header_id);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $total_transaction_amount = 0;
        $total_companyLocalAmount = 0;
        $total_companyReportingAmount = 0;
        foreach ($this->post->detail as $key => $detail) {
            $chart_of_account = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
            if (empty($chart_of_account)) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
                exit;
            }

            $data[$key]['InvoiceAutoID'] = $header_id;
            $data[$key]['type'] = 'GL';
            $data[$key]['revenueGLAutoID'] = $detail->erp_chart_of_account_id;
            $data[$key]['revenueSystemGLCode'] = $chart_of_account->systemAccountCode;
            $data[$key]['revenueGLCode'] = $chart_of_account->GLSecondaryCode;
            $data[$key]['revenueGLDescription'] = $chart_of_account->GLDescription;
            $data[$key]['revenueGLType'] = $chart_of_account->subCategory;
            $data[$key]['segmentID'] = $detail->erp_segment_id;
            $data[$key]['segmentCode'] = $this->Api_erp_model->get_segment_code($detail->erp_segment_id);
            $data[$key]['description'] = $detail->description;
            $data[$key]['transactionAmount'] = round($detail->amount, $master['transactionCurrencyDecimalPlaces']);
            $total_transaction_amount += $data[$key]['transactionAmount'];

            $companyLocalAmount = 0;
            if ($master['companyLocalExchangeRate']) {
                $companyLocalAmount = (float)$data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            }
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $total_companyLocalAmount += $data[$key]['companyLocalAmount'];

            $companyReportingAmount = 0;
            if ($master['companyReportingExchangeRate']) {
                $companyReportingAmount = (float)$data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            }
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $total_companyReportingAmount += $data[$key]['companyReportingAmount'];

            $supplierAmount = 0;
            if ($master['customerCurrencyExchangeRate']) {
                $supplierAmount = (float)$data[$key]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            }

            $data[$key]['customerAmount'] = round($supplierAmount, $master['customerCurrencyDecimalPlaces']);
            //$data[$key]['customerCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
            $data[$key]['companyCode'] = $this->company_code;
            $data[$key]['companyID'] = $this->company_id;
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->user_id;
            $data[$key]['createdUserName'] = $this->user_name;
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_customerinvoicedetails', $data);
        }

        $update_trasaction_amounts = array(
            'transactionAmount' => $total_transaction_amount,
            'companyLocalAmount' => $total_companyLocalAmount,
            'companyReportingAmount' => $total_companyReportingAmount
        );
        $this->db->where('invoiceAutoID', $header_id);
        $this->db->update('srp_erp_customerinvoicemaster', $update_trasaction_amounts);

        //attachments
        if (sizeof($this->post->attachments) > 0) {
            foreach ($this->post->attachments as $attachment) {
                $s3_file_name_from_realmax = $this->company_code . '/' . $attachment->url;
                $file_size = $attachment->file_size;
                $ext_type = $attachment->ext_type;
                $attachment_record['documentID'] = 'CINV';
                $attachment_record['documentSystemCode'] = $header_id;
                $attachment_record['attachmentDescription'] = '';
                $attachment_record['myFileName'] = $s3_file_name_from_realmax;
                $attachment_record['fileType'] = trim($ext_type);
                $attachment_record['fileSize'] = trim($file_size);
                $attachment_record['timestamp'] = date('Y-m-d H:i:s');
                $attachment_record['companyID'] = $this->common_data['company_data']['company_id'];
                $attachment_record['companyCode'] = $this->common_data['company_data']['company_code'];
                $attachment_record['createdUserGroup'] = $this->common_data['user_group'];
                $attachment_record['modifiedPCID'] = $this->common_data['current_pc'];
                $attachment_record['modifiedUserID'] = $this->common_data['current_userID'];
                $attachment_record['modifiedUserName'] = $this->common_data['current_user'];
                $attachment_record['modifiedDateTime'] = $this->common_data['current_date'];
                $attachment_record['createdPCID'] = $this->common_data['current_pc'];
                $attachment_record['createdUserID'] = $this->common_data['current_userID'];
                $attachment_record['createdUserName'] = $this->common_data['current_user'];
                $attachment_record['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_documentattachments', $attachment_record);
            }
        }

        $doubleEntries = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($header_id, 'CINV');
        if (!empty($doubleEntries)) {
            $this->Api_erp_model->customer_invoice_general_ledger($doubleEntries);
        }

        $data_master['document_id'] = $header_id;
        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully posted', 'data' => $data_master);
        $this->insert_document_entry($header_id, 'BSI');
        $this->response($output);
    }

    public function invoice_post()
    {
        $this->validate_post();
        $supplier_arr = $this->Api_erp_model->fetch_supplier_data($this->post->erp_supplier_id);

        $glIDs = array();
        foreach ($this->post->detail as $key => $detail) {
            $glIDs[] = $detail->erp_chart_of_account_id;
        }
        $gl_status = $this->Api_erp_model->check_gl_status($glIDs);
        if (!empty($gl_status)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'The linked ERP Chart of Account has been made inactive, activate it and try again', 'data' => $gl_status), 500);
            exit();
        }
        if (isset($this->post->reference_id)) {
            $supplierrefno = $this->db->query("SELECT COUNT(InvoiceAutoID) as isexistcount FROM `srp_erp_paysupplierinvoicemaster` where 
	                                           companyID = $this->company_id AND RefNo = '{$this->post->reference_id}'")->row('isexistcount');
            if ($supplierrefno > 0) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Supplier invoice already created for this reference no'), 500);
                exit();
            }
        }

        $data_master['invoiceType'] = 'Standard';
        $data_master['bookingDate'] = $this->post->document_date;
        $data_master['invoiceDueDate'] = $this->post->document_date;
        $data_master['invoiceDate'] = $this->post->document_date;
        $data_master['companyFinanceYearID'] = $this->get_finance_year_id();
        $data_master['companyFinanceYear'] = !empty($this->finance_year) ? $this->finance_year->beginingDate . ' - ' . $this->finance_year->endingDate : '';
        $data_master['FYBegin'] = !empty($this->finance_year) ? $this->finance_year->beginingDate : null;
        $data_master['FYEnd'] = !empty($this->finance_year) ? $this->finance_year->endingDate : null;
        $data_master['companyFinancePeriodID'] = $this->get_finance_period_id();
        $data_master['FYPeriodDateFrom'] = !empty($this->finance_period) ? $this->finance_period->dateFrom : null;
        $data_master['FYPeriodDateTo'] = !empty($this->finance_period) ? $this->finance_period->dateTo : null;
        $data_master['documentID'] = 'BSI';
        $data_master['supplierID'] = $this->post->erp_supplier_id;
        $data_master['supplierCode'] = $supplier_arr->supplierSystemCode;
        $data_master['supplierName'] = $supplier_arr->supplierName;
        $data_master['supplierAddress'] = $supplier_arr->supplierAddress1;
        $data_master['supplierTelephone'] = $supplier_arr->supplierTelephone;
        $data_master['supplierFax'] = $supplier_arr->supplierFax;
        $data_master['supplierliabilityAutoID'] = $supplier_arr->liabilityAutoID;
        $data_master['supplierliabilitySystemGLCode'] = $supplier_arr->liabilitySystemGLCode;
        $data_master['supplierliabilityGLAccount'] = $supplier_arr->liabilityGLAccount;
        $data_master['supplierliabilityDescription'] = $supplier_arr->liabilityDescription;

        $data_master['supplierliabilityType'] = $supplier_arr->liabilityType;
        $data_master['transactionCurrency'] = $this->post->currency_id;
        $data_master['segmentID'] = $this->post->erp_segment_id;
        $data_master['segmentCode'] = $this->Api_erp_model->get_segment_code($this->post->erp_segment_id);
        $data_master['RefNo'] = isset($this->post->reference_id) ? $this->post->reference_id : '';
        $data_master['supplierInvoiceNo'] = isset($this->post->reference_number) ? $this->post->reference_number : '';
        $data_master['comments'] = isset($this->post->reference) ? $this->post->reference : '';
        $data_master['isSytemGenerated'] = 1;

        $data_master['transactionCurrencyID'] = $this->post->currency_id;
        $data_master['transactionCurrency'] = $this->Api_erp_model->get_currency_code($this->post->currency_id);
        $data_master['transactionExchangeRate'] = 1;
        $data_master['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($this->post->currency_id);
        $data_master['companyLocalCurrencyID'] = $this->company_info['company_default_currencyID'];
        $data_master['companyLocalCurrency'] = $this->company_info['company_default_currency'];
        $default_currency = currency_conversionID($this->post->currency_id, $this->company_info['company_default_currencyID']);
        $data_master['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data_master['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data_master['companyReportingCurrency'] = !empty($this->company_info['company_reporting_currency']) ? $this->company_info['company_reporting_currency'] : 0;
        $data_master['companyReportingCurrencyID'] = $this->company_info['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($this->post->currency_id, $data_master['companyReportingCurrencyID']);
        $data_master['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data_master['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data_master['supplierCurrencyID'] = $supplier_arr->supplierCurrencyID;
        $data_master['supplierCurrency'] = $supplier_arr->supplierCurrency;
        $supplierCurrency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['supplierCurrencyID']);
        $data_master['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data_master['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
        $data_master['modifiedPCID'] = $this->common_data['current_pc'];
        $data_master['modifiedUserID'] = $this->common_data['current_userID'];
        $data_master['modifiedUserName'] = $this->common_data['current_user'];
        $data_master['modifiedDateTime'] = $this->common_data['current_date'];
        $data_master['companyCode'] = $this->company_code;
        $data_master['companyID'] = $this->company_id;
        $data_master['createdUserGroup'] = $this->common_data['user_group'];
        $data_master['createdPCID'] = $this->common_data['current_pc'];
        $data_master['createdUserID'] = $this->user_id;
        $data_master['createdUserName'] = $this->user_name;
        $data_master['createdDateTime'] = $this->common_data['current_date'];
        $data_master['bookingInvCode'] = $this->sequence_generator('BSI');
        /**confirmed*/
        $data_master['confirmedYN'] = 1;
        $data_master['confirmedByEmpID'] = $this->user_id;
        $data_master['confirmedByName'] = $this->user['Ename4'];
        $data_master['confirmedDate'] = $this->common_data['current_date'];
        /** Approval */
        $data_master['approvedYN'] = 1;
        $data_master['approvedbyEmpID'] = $this->user_id;
        $data_master['approvedbyEmpName'] = $this->user['Ename4'];
        $data_master['approvedDate'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_paysupplierinvoicemaster', $data_master);
        $header_id = $this->db->insert_id();

        //Approval
        $this->common_data['current_userID'] = $this->user_id;
        $this->common_data['current_user'] = $this->user['Ename4'];
        $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data_master['documentID'], $header_id, $data_master['bookingInvCode'], 'Invoice', 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', 1, $data_master['invoiceDate']);
        $approval_levels = $this->approvals->maxlevel('BSI');
        $number_of_levels = $approval_levels['levelNo'];
        for ($i = 1; $i <= $number_of_levels; $i++) {
            $this->approvals->approve_without_sending_email($header_id, $i, 1, '', $data_master['documentID']);
        }

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $header_id);
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();
        foreach ($this->post->detail as $key => $detail) {
            $chart_of_account = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
            if (empty($chart_of_account)) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
                exit;
            }
            $data[$key]['InvoiceAutoID'] = $header_id;
            $data[$key]['GLAutoID'] = $detail->erp_chart_of_account_id;
            $data[$key]['systemGLCode'] = $chart_of_account->systemAccountCode;
            $data[$key]['GLCode'] = $chart_of_account->GLSecondaryCode;
            $data[$key]['GLDescription'] = $chart_of_account->GLDescription;
            $data[$key]['GLType'] = $chart_of_account->subCategory;
            $data[$key]['segmentID'] = $detail->erp_segment_id;
            $data[$key]['segmentCode'] = $this->Api_erp_model->get_segment_code($detail->erp_segment_id);
            $data[$key]['description'] = $detail->description;
            $data[$key]['transactionAmount'] = round($detail->amount, $master['transactionCurrencyDecimalPlaces']);
            $data[$key]['transactionExchangeRate'] = $master['transactionExchangeRate'];
            $companyLocalAmount = 0;
            if ($master['companyLocalExchangeRate']) {
                $companyLocalAmount = (float)$data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            }
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = 0;

            if ($master['companyReportingExchangeRate']) {
                $companyReportingAmount = (float)$data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            }

            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $supplierAmount = 0;
            if ($master['supplierCurrencyExchangeRate']) {
                $supplierAmount = (float)$data[$key]['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
            }

            $data[$key]['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
            $data[$key]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$key]['companyCode'] = $this->company_code;
            $data[$key]['companyID'] = $this->company_id;
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->user_id;
            $data[$key]['createdUserName'] = $this->user_name;
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);
        }
        //Updating master record with total values of companyLocalAmount,companyReportingAmount.
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
        $this->db->where('InvoiceAutoID', $header_id);
        $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();
        $transa_total_amount += $data_arr['transactionAmount'];
        $loca_total_amount += $data_arr['companyLocalAmount'];
        $rpt_total_amount += $data_arr['companyReportingAmount'];
        $supplier_total_amount += $data_arr['supplierAmount'];
        $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,taxPercentage');
        $this->db->where('InvoiceAutoID', $header_id);
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
        /*updating transaction amount using the query used in the master data table  */
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
            AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $header_id ";
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
        $this->db->where('InvoiceAutoID', $header_id);
        $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
        if (!empty($t_arr)) {
            $this->db->update_batch('srp_erp_paysupplierinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
        }
        //attachments
        if (sizeof($this->post->attachments) > 0) {
            foreach ($this->post->attachments as $attachment) {
                $s3_file_name_from_realmax = $this->company_code . '/' . $attachment->url;
                $file_size = $attachment->file_size;
                $ext_type = $attachment->ext_type;
                $attachment_record['documentID'] = 'BSI';
                $attachment_record['documentSystemCode'] = $header_id;
                $attachment_record['attachmentDescription'] = '';
                $attachment_record['myFileName'] = $s3_file_name_from_realmax;
                $attachment_record['fileType'] = trim($ext_type);
                $attachment_record['fileSize'] = trim($file_size);
                $attachment_record['timestamp'] = date('Y-m-d H:i:s');
                $attachment_record['companyID'] = $this->common_data['company_data']['company_id'];
                $attachment_record['companyCode'] = $this->common_data['company_data']['company_code'];
                $attachment_record['createdUserGroup'] = $this->common_data['user_group'];
                $attachment_record['modifiedPCID'] = $this->common_data['current_pc'];
                $attachment_record['modifiedUserID'] = $this->common_data['current_userID'];
                $attachment_record['modifiedUserName'] = $this->common_data['current_user'];
                $attachment_record['modifiedDateTime'] = $this->common_data['current_date'];
                $attachment_record['createdPCID'] = $this->common_data['current_pc'];
                $attachment_record['createdUserID'] = $this->common_data['current_userID'];
                $attachment_record['createdUserName'] = $this->common_data['current_user'];
                $attachment_record['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_documentattachments', $attachment_record);
            }
        }
        $doubleEntries = $this->Double_entry_model->fetch_double_entry_supplier_invoices_data($header_id, 'BSI');
        if (!empty($doubleEntries)) {
            $this->Api_erp_model->invoice_general_ledger($doubleEntries);
        }

        $data_master['document_id'] = $header_id;
        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully posted', 'data' => $data_master);
        $this->insert_document_entry($header_id, 'BSI');
        $this->response($output);
    }

    public function multiple_receipts_post()
    {

        $this->validate_multiple_post();
        $receipt_array = $this->post->data;
        $response_data = array();
        $this->db->trans_begin();
        foreach ($receipt_array as $receipt_n) {
            $glIDs = array(
                $receipt_n->erp_chart_of_account_id
            );
            foreach ($receipt_n->detail as $key => $detail) {
                $glIDs[] = $detail->erp_chart_of_account_id;
            }
            $gl_status = $this->Api_erp_model->check_gl_status($glIDs);
            if (!empty($gl_status)) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'The linked ERP Chart of Account has been made inactive, activate it and try again', 'data' => $gl_status), 500);
                exit();
            }
            foreach ($receipt_n->detail as $key => $detail) {
                $chart_of_account = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
                if (empty($chart_of_account)) {
                    echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
                    exit;
                }


            }

            if (isset($receipt_n->reference)) {
                $RVchequeNo = $this->db->query("SELECT COUNT(receiptVoucherAutoId) as isexistcount FROM srp_erp_customerreceiptmaster WHERE
			                                companyID = {$this->company_id} AND referanceNo = '{$receipt_n->reference}' AND isDeleted=0")->row('isexistcount');
                if ($RVchequeNo > 0) {
                    echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Reference No already exist'), 500);
                    exit();
                }
            }

            /*if (isset($this->post->cheque_no)) {
                $RVchequeNo = $this->db->query("SELECT COUNT(receiptVoucherAutoId) as isexistcount FROM `srp_erp_customerreceiptmaster` where
			                                companyID = $this->company_id AND RVchequeNo = '{$this->post->cheque_no}'")->row('isexistcount');
                if ($RVchequeNo > 0) {
                    echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Cheque number aready exist'), 500);
                    exit();
                }
            }*/

            $data_master['documentID'] = 'RV';
            $data_master['companyFinanceYearID'] = $this->get_rv_finance_year_id($receipt_n->document_date);
            $data_master['companyFinanceYear'] = !empty($this->finance_year) ? $this->finance_year->beginingDate . ' - ' . $this->finance_year->endingDate : '';
            $data_master['FYBegin'] = !empty($this->finance_year) ? $this->finance_year->beginingDate : null;
            $data_master['FYEnd'] = !empty($this->finance_year) ? $this->finance_year->endingDate : null;
            $data_master['companyFinancePeriodID'] = $this->get_finance_period_id();
            $data_master['FYPeriodDateFrom'] = !empty($this->finance_period) ? $this->finance_period->dateFrom : null;
            $data_master['FYPeriodDateTo'] = !empty($this->finance_period) ? $this->finance_period->dateTo : null;
            $data_master['RVdate'] = $receipt_n->document_date;
            $data_master['RVNarration'] = $receipt_n->comment;
            $data_master['segmentID'] = $receipt_n->erp_segment_id;
            $data_master['segmentCode'] = $this->Api_erp_model->get_segment_code($receipt_n->erp_segment_id);
            $this->set_chart_of_account($receipt_n->erp_chart_of_account_id);
            $data_master['bankGLAutoID'] = $receipt_n->erp_chart_of_account_id;
            $data_master['bankSystemAccountCode'] = $this->chart_of_account->systemAccountCode;
            $data_master['bankGLSecondaryCode'] = $this->chart_of_account->GLSecondaryCode;
            $data_master['bankCurrencyID'] = $this->chart_of_account->bankCurrencyID;
            $data_master['bankCurrency'] = $this->chart_of_account->bankCurrencyCode;
            $data_master['RVbank'] = $this->chart_of_account->bankName;
            $data_master['RVbankBranch'] = $this->chart_of_account->bankBranch;
            $data_master['RVbankSwiftCode'] = $this->chart_of_account->bankSwiftCode;
            $data_master['RVbankAccount'] = $this->chart_of_account->bankAccountNumber;
            $data_master['RVbankType'] = $this->chart_of_account->subCategory;
            $data_master['modeOfPayment'] = ($this->chart_of_account->isCash == 1 ? 1 : 2);
            $data_master['RVchequeNo'] = isset($receipt_n->cheque_no) ? $receipt_n->cheque_no : '';
            $data_master['RVchequeDate'] = isset($receipt_n->cheque_date) ? $receipt_n->cheque_date : '';
            $data_master['RvType'] = 'Direct';
            $data_master['referanceNo'] = $receipt_n->reference;
            $data_master['RVbankCode'] = $receipt_n->erp_chart_of_account_id;
            $data_master['customerName'] = $receipt_n->customer_name;
            $data_master['customerAddress'] = '';
            $data_master['customerTelephone'] = '';
            $data_master['customerFax'] = '';
            $data_master['customerEmail'] = '';
            $data_master['customerCurrency'] = $receipt_n->currency_id;
            $data_master['customerCurrencyID'] = $this->Api_erp_model->get_currency_code($receipt_n->currency_id);
            $data_master['customerCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data_master['customerCurrencyID']);
            $data_master['modifiedPCID'] = $this->common_data['current_pc'];
            $data_master['modifiedUserID'] = $this->user_id;
            $data_master['modifiedUserName'] = $this->user_name;
            $data_master['modifiedDateTime'] = $this->common_data['current_date'];
            $data_master['transactionCurrencyID'] = $receipt_n->currency_id;
            $data_master['transactionCurrency'] = $this->Api_erp_model->get_currency_code($receipt_n->currency_id);
            $data_master['transactionExchangeRate'] = 1;
            $data_master['transactionCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data_master['transactionCurrencyID']);
            $data_master['companyLocalCurrencyID'] = $this->company_info['company_default_currencyID'];
            $data_master['companyLocalCurrency'] = $this->company_info['company_default_currency'];
            $default_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['companyLocalCurrencyID']);
            $data_master['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data_master['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data_master['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data_master['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['companyReportingCurrencyID']);
            $data_master['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data_master['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $customer_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['customerCurrencyID']);
            $data_master['customerExchangeRate'] = $customer_currency['conversion'];
            $data_master['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
            $bank_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['bankCurrencyID']);
            $data_master['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
            $data_master['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
            $data_master['companyCode'] = $this->company_code;
            $data_master['companyID'] = $this->company_id;
            $data_master['createdUserGroup'] = 0;
            $data_master['createdPCID'] = $this->common_data['current_pc'];
            $data_master['createdUserID'] = $this->user_id;
            $data_master['createdUserName'] = $this->user_name;
            $data_master['createdDateTime'] = $this->common_data['current_date'];
            $data_master['RVcode'] = $this->sequence_generator('RV');
            /**confirmed*/
            $data_master['isSystemGenerated'] = 1;
            $data_master['confirmedYN'] = 1;
            $data_master['confirmedByEmpID'] = $this->user_id;
            $data_master['confirmedByName'] = $this->user['Ename4'];
            $data_master['confirmedDate'] = $this->common_data['current_date'];
            /** Approval */
            $data_master['approvedYN'] = 1;
            $data_master['approvedbyEmpID'] = $this->user_id;
            $data_master['approvedbyEmpName'] = $this->user['Ename4'];
            $data_master['approvedDate'] = $this->common_data['current_date'];
            //var_dump($data_master);exit;
            $this->db->insert('srp_erp_customerreceiptmaster', $data_master);
            $header_id = $this->db->insert_id();
            $this->common_data['current_userID'] = $this->user_id;
            $this->common_data['current_user'] = $this->user['Ename4'];
            $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data_master['documentID'], $header_id, $data_master['RVcode'], 'Receipt', 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 1, $data_master['RVdate']);
            $approval_levels = $this->approvals->maxlevel('RV');
            $number_of_levels = $approval_levels['levelNo'];
            for ($i = 1; $i <= $number_of_levels; $i++) {
                $this->approvals->approve_rv($header_id, $i, 1, '', $data_master['documentID']);
            }
            /** DETAIL  */
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID');
            $this->db->where('receiptVoucherAutoId', $header_id);
            $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();
            foreach ($receipt_n->detail as $key => $detail) {
                $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
                $data[$key]['receiptVoucherAutoId'] = $header_id;
                $data[$key]['GLAutoID'] = $detail->erp_chart_of_account_id;
                $data[$key]['systemGLCode'] = $chart_of_account_details->systemAccountCode;
                $data[$key]['GLCode'] = $chart_of_account_details->GLSecondaryCode;
                $data[$key]['GLDescription'] = $chart_of_account_details->GLDescription;
                $data[$key]['GLType'] = $chart_of_account_details->subCategory;
                $data[$key]['segmentID'] = $detail->erp_segment_id;
                $data[$key]['segmentCode'] = $this->Api_erp_model->get_segment_code($detail->erp_segment_id);
                $data[$key]['transactionAmount'] = $detail->amount;
                $data[$key]['companyLocalAmount'] = ($detail->amount / $master['companyLocalExchangeRate']);
                $data[$key]['companyReportingAmount'] = ($detail->amount / $master['companyReportingExchangeRate']);
                $data[$key]['customerAmount'] = 0;
                if ($master['customerExchangeRate']) {
                    $data[$key]['customerAmount'] = ($detail->amount / $master['customerExchangeRate']);
                }
                $data[$key]['description'] = $detail->description;
                $data[$key]['type'] = 'GL';
                $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
                $data[$key]['modifiedUserID'] = $this->user_id;
                $data[$key]['modifiedUserName'] = $this->user_name;
                $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];
                $data[$key]['companyCode'] = $this->company_code;
                $data[$key]['companyID'] = $this->company_id;
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->user_id;
                $data[$key]['createdUserName'] = $this->user_name;
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];
            }
            $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);
            $data_master_res = $data_master;
            $data_master_res['document_id'] = $header_id;
            $data_master_res['realmax_document_id'] = $receipt_n->realmax_document_id;
            array_push($response_data, $data_master_res);

            $doubleEntries = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($header_id, 'RV');
            if (!empty($doubleEntries)) {
                $this->Api_erp_model->receipt_double_entry($doubleEntries);
            }
            $this->insert_rv_document_entry($header_id, 'RV', $receipt_n->property_code, $receipt_n->unit_code);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $output = array('type' => 'failed', 'status' => 200, 'message' => 'failed to insert the record due to database error.');
        } else {
            $this->db->trans_commit();
            $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully posted');
        }
        $output['data'] = $response_data;
        $this->response($output);
    }

    public function receipt_post()
    {

        $this->validate_post();

        $glIDs = array(
        //            $this->post->bank_charge_chart_of_account_id ,
            $this->post->erp_chart_of_account_id
        );
        foreach ($this->post->detail as $key => $detail) {
            $glIDs[] = $detail->erp_chart_of_account_id;
        }
        $gl_status = $this->Api_erp_model->check_gl_status($glIDs);
        if (!empty($gl_status)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'The linked ERP Chart of Account has been made inactive, activate it and try again', 'data' => $gl_status), 500);
            exit();
        }
        if (isset($this->post->reference)) {
            $RVchequeNo = $this->db->query("SELECT COUNT(receiptVoucherAutoId) as isexistcount FROM srp_erp_customerreceiptmaster WHERE
			                                companyID = {$this->company_id} AND referanceNo = '{$this->post->reference}' AND isDeleted=0")->row('isexistcount');
            if ($RVchequeNo > 0) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Reference No already exist'), 500);
                exit();
            }
        }


        foreach ($this->post->detail as $key => $detail) {
            $chart_of_account = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
            if (empty($chart_of_account)) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
                exit;
            }
        }

        $data_master['documentID'] = 'RV';
        $data_master['companyFinanceYearID'] = $this->get_finance_year_id();
        $data_master['companyFinanceYear'] = !empty($this->finance_year) ? $this->finance_year->beginingDate . ' - ' . $this->finance_year->endingDate : '';
        $data_master['FYBegin'] = !empty($this->finance_year) ? $this->finance_year->beginingDate : null;
        $data_master['FYEnd'] = !empty($this->finance_year) ? $this->finance_year->endingDate : null;
        $data_master['companyFinancePeriodID'] = $this->get_finance_period_id();
        $data_master['FYPeriodDateFrom'] = !empty($this->finance_period) ? $this->finance_period->dateFrom : null;
        $data_master['FYPeriodDateTo'] = !empty($this->finance_period) ? $this->finance_period->dateTo : null;
        $data_master['RVdate'] = $this->post->document_date;
        $data_master['RVNarration'] = $this->post->comment;
        $data_master['segmentID'] = $this->post->erp_segment_id;
        $data_master['segmentCode'] = $this->Api_erp_model->get_segment_code($this->post->erp_segment_id);


        $this->set_chart_of_account($this->post->erp_chart_of_account_id);
        $data_master['bankGLAutoID'] = $this->post->erp_chart_of_account_id;
        $data_master['bankSystemAccountCode'] = $this->chart_of_account->systemAccountCode;
        $data_master['bankGLSecondaryCode'] = $this->chart_of_account->GLSecondaryCode;
        $data_master['bankCurrencyID'] = $this->chart_of_account->bankCurrencyID;
        $data_master['bankCurrency'] = $this->chart_of_account->bankCurrencyCode;
        $data_master['RVbank'] = $this->chart_of_account->bankName;
        $data_master['RVbankBranch'] = $this->chart_of_account->bankBranch;
        $data_master['RVbankSwiftCode'] = $this->chart_of_account->bankSwiftCode;
        $data_master['RVbankAccount'] = $this->chart_of_account->bankAccountNumber;
        $data_master['RVbankType'] = $this->chart_of_account->subCategory;
        $data_master['modeOfPayment'] = ($this->chart_of_account->isCash == 1 ? 1 : 2);


        $data_master['RVchequeNo'] = isset($this->post->cheque_no) ? $this->post->cheque_no : '';
        $data_master['RVchequeDate'] = isset($this->post->cheque_date) ? $this->post->cheque_date : '';
        $data_master['RvType'] = 'Direct';

        $data_master['referanceNo'] = $this->post->reference;
        $data_master['RVbankCode'] = $this->post->erp_chart_of_account_id;


        $data_master['customerName'] = $this->post->customer_name;
        $data_master['customerAddress'] = '';
        $data_master['customerTelephone'] = '';
        $data_master['customerFax'] = '';
        $data_master['customerEmail'] = '';
        $data_master['customerCurrency'] = $this->post->currency_id;
        $data_master['customerCurrencyID'] = $this->Api_erp_model->get_currency_code($this->post->currency_id);
        $data_master['customerCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data_master['customerCurrencyID']);

        $data_master['modifiedPCID'] = $this->common_data['current_pc'];
        $data_master['modifiedUserID'] = $this->user_id;
        $data_master['modifiedUserName'] = $this->user_name;
        $data_master['modifiedDateTime'] = $this->common_data['current_date'];

        $data_master['transactionCurrencyID'] = $this->post->currency_id;
        $data_master['transactionCurrency'] = $this->Api_erp_model->get_currency_code($this->post->currency_id);
        $data_master['transactionExchangeRate'] = 1;
        $data_master['transactionCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data_master['transactionCurrencyID']);

        $data_master['companyLocalCurrencyID'] = $this->company_info['company_default_currencyID'];
        $data_master['companyLocalCurrency'] = $this->company_info['company_default_currency'];
        $default_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['companyLocalCurrencyID']);
        $data_master['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data_master['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data_master['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data_master['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['companyReportingCurrencyID']);
        $data_master['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data_master['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $customer_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['customerCurrencyID']);
        $data_master['customerExchangeRate'] = $customer_currency['conversion'];
        $data_master['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
        $bank_currency = currency_conversionID($data_master['transactionCurrencyID'], $data_master['bankCurrencyID']);
        $data_master['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data_master['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        $data_master['companyCode'] = $this->company_code;
        $data_master['companyID'] = $this->company_id;
        $data_master['createdUserGroup'] = 0;
        $data_master['createdPCID'] = $this->common_data['current_pc'];
        $data_master['createdUserID'] = $this->user_id;
        $data_master['createdUserName'] = $this->user_name;
        $data_master['createdDateTime'] = $this->common_data['current_date'];
        $data_master['RVcode'] = $this->sequence_generator('RV');

        /**confirmed*/
        $data_master['isSystemGenerated'] = 1;
        $data_master['confirmedYN'] = 1;
        $data_master['confirmedByEmpID'] = $this->user_id;
        $data_master['confirmedByName'] = $this->user['Ename4'];
        $data_master['confirmedDate'] = $this->common_data['current_date'];

        /** Approval */
        $data_master['approvedYN'] = 1;
        $data_master['approvedbyEmpID'] = $this->user_id;
        $data_master['approvedbyEmpName'] = $this->user['Ename4'];
        $data_master['approvedDate'] = $this->common_data['current_date'];


        $this->db->insert('srp_erp_customerreceiptmaster', $data_master);
        $header_id = $this->db->insert_id();

        $this->common_data['current_userID'] = $this->user_id;
        $this->common_data['current_user'] = $this->user['Ename4'];
        $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data_master['documentID'], $header_id, $data_master['RVcode'], 'Receipt', 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 1, $data_master['RVdate']);
        $approval_levels = $this->approvals->maxlevel('RV');
        $number_of_levels = $approval_levels['levelNo'];
        for ($i = 1; $i <= $number_of_levels; $i++) {
            $this->approvals->approve_without_sending_email($header_id, $i, 1, '', $data_master['documentID']);
        }

        /** DETAIL  */
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID');
        $this->db->where('receiptVoucherAutoId', $header_id);
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();


        foreach ($this->post->detail as $key => $detail) {
            $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
        //            $chart_of_account = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $detail->erp_chart_of_account_id);
        //            if (empty($chart_of_account)) {
        //                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
        //                exit;
        //            }
            $data[$key]['receiptVoucherAutoId'] = $header_id;

            $data[$key]['GLAutoID'] = $detail->erp_chart_of_account_id;
            $data[$key]['systemGLCode'] = $chart_of_account_details->systemAccountCode;
            $data[$key]['GLCode'] = $chart_of_account_details->GLSecondaryCode;
            $data[$key]['GLDescription'] = $chart_of_account_details->GLDescription;
            $data[$key]['GLType'] = $chart_of_account_details->subCategory;


            $data[$key]['segmentID'] = $detail->erp_segment_id;
            $data[$key]['segmentCode'] = $this->Api_erp_model->get_segment_code($detail->erp_segment_id);

            $data[$key]['transactionAmount'] = $detail->amount;
            $data[$key]['companyLocalAmount'] = ($detail->amount / $master['companyLocalExchangeRate']);
            $data[$key]['companyReportingAmount'] = ($detail->amount / $master['companyReportingExchangeRate']);

            $data[$key]['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data[$key]['customerAmount'] = ($detail->amount / $master['customerExchangeRate']);
            }


            $data[$key]['description'] = $detail->description;
            $data[$key]['type'] = 'GL';
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->user_id;
            $data[$key]['modifiedUserName'] = $this->user_name;
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];


            $data[$key]['companyCode'] = $this->company_code;
            $data[$key]['companyID'] = $this->company_id;
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->user_id;
            $data[$key]['createdUserName'] = $this->user_name;
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];

        }

        $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);

        $data_master['document_id'] = $header_id;

        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully posted', 'data' => $data_master);

        $doubleEntries = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($header_id, 'RV');

        if (!empty($doubleEntries)) {
            $this->Api_erp_model->receipt_double_entry($doubleEntries);
        }
        $this->insert_document_entry($header_id, 'RV');

        $this->response($output);
    }

    public function document_reversal_post()
    {
        $_POST = json_decode(json_encode($this->post), true);
        $this->form_validation->set_rules('comments', 'comments', 'trim|required');
        $this->form_validation->set_rules('document_id', 'document_id', 'trim|required');
        $this->form_validation->set_rules('document_code', 'document_code', 'trim|required');
        $this->form_validation->set_rules('user_id', 'document_code', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            return $this->response(array('type' => 'error', 'status' => 500, 'message' => implode('  |  ', $errors)), 500);
        } else {
            $data = $this->Api_erp_model->reversing_approval_document($this->post, $this->company_id, $this->user_id, $this->user_name, $this->company_info);
            $statusCode = isset($data['status']) ? $data['status'] : 500;
            return $this->response($data, $statusCode);
        }
    }


    /** ---------------------------  PRIVATE FUNCTIONS --------------------------- */

    private function auth_user()
    {
        if (empty($this->user)) {
            $this->db->database;
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP user not found!'), 500);
            exit;
        }
    }

    private function set_chart_of_account($id)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('GLAutoID', $id);
        $CI->db->where('companyID', $this->company_id);
        $this->chart_of_account = $CI->db->get()->row();
        if (empty($this->chart_of_account)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'ERP Chart of Account not found!'), 500);
            exit;

        }
    }

    private function sequence_generator($documentID, $count = 0)
    {

        $CI = &get_instance();
        $code = '';
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $this->company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $this->company_id,
                'companyCode' => $this->company_info['company_code'],
                'createdUserGroup' => 0,
                'createdUserID' => $this->user_id,
                'createdUserName' => 'API',
                'createdPCID' => 'API',
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $this->user_id,
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$this->company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($this->company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    private function get_rv_finance_year_id($document_date)
    {
        isset($document_date) ? $this->document_date = $document_date : $this->document_date;
        $company_date = date('Y-m-d', strtotime($this->document_date));
        $result = $this->db->select('companyFinanceYearID')
            ->from('srp_erp_companyfinanceperiod')
            ->where('dateFrom<=', $company_date)
            ->where('dateTo>=', $company_date)
            ->where('companyID', $this->company_id)->get()->row('companyFinanceYearID');
        if ($result) {
            $this->finance_year = $this->Api_erp_model->get_finance_year($result);
        }
        return $result;
    }

    private function get_finance_year_id()
    {
        isset($this->post->document_date) ? $this->document_date = $this->post->document_date : $this->document_date;
        $company_date = date('Y-m-d', strtotime($this->document_date));
        $result = $this->db->select('companyFinanceYearID')
            ->from('srp_erp_companyfinanceperiod')
            ->where('dateFrom<=', $company_date)
            ->where('dateTo>=', $company_date)
            ->where('companyID', $this->company_id)->get()->row('companyFinanceYearID');
        if ($result) {
            $this->finance_year = $this->Api_erp_model->get_finance_year($result);
        }
        return $result;
    }

    private function get_finance_period_id()
    {
        $company_date = date('Y-m-d', strtotime($this->document_date));
        $result = $this->db->select('companyFinancePeriodID')
            ->from('srp_erp_companyfinanceperiod')
            ->where('dateFrom<=', $company_date)
            ->where('dateTo>=', $company_date)
            ->where('companyID', $this->company_id)->get()->row('companyFinancePeriodID');

        if ($result) {
            $this->finance_period = $this->Api_erp_model->get_finance_period($result);
        }
        return $result;
    }

    private function get_currency_decimal_place($currency_id)
    {
        $CI =& get_instance();
        $CI->db->SELECT("DecimalPlaces");
        $CI->db->FROM('srp_erp_companycurrencyassign');
        $CI->db->WHERE('currencyID', $currency_id);
        $CI->db->WHERE('companyID', $this->company_id);
        return $CI->db->get()->row('DecimalPlaces');

    }

    private function validate_post()
    {
        $this->error_data = [];

        if (!isset($this->post->property_code) || empty($this->post->property_code)) {
            $this->error_in_validation = true;
            $this->error_data[] = 'Property Code is missing.';
        }

        /*Single Unit - we can not validate */
        /*if (!isset($this->post->unit_code) || empty($this->post->unit_code)) {
            $this->error_in_validation = true;
            $this->error_data[] = 'Unit Code is missing.';
        }*/


        /** User exist for the current company */
        if (isset($this->post->user_id)) {
            $user = $this->db->select('EIdNo,ECode')->from('srp_employeesdetails')->where('EIdNo', $this->post->user_id)->where('Erp_companyID', $this->company_id)->get()->row();
            if (!$user) {
                $this->error_in_validation = true;
                $this->error_data[] = 'User not exist for this company';
            }
        }


        /** Chart of Account ID is exist for current Company */
        if (isset($this->post->erp_chart_of_account_id)) {
            $chart_of_account = $this->db->select('GLAutoID')->from('srp_erp_chartofaccounts')->where('GLAutoID', $this->post->erp_chart_of_account_id)->where('companyID', $this->company_id)->get()->row();
            if (!$chart_of_account) {
                $this->error_in_validation = true;
                $this->error_data[] = 'Chart of Account not available for this company';
            }
        }


        /** return error if validation false */
        if ($this->error_in_validation == true) {
            $this->response(['type' => 'error', 'status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Server Side validation error.', 'data' => $this->error_data], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }


    }

    private function validate_multiple_post()
    {
        $this->error_data = [];

        $receipt_array = $this->post->data;
        foreach ($receipt_array as $receipt_n) {
            if (!isset($receipt_n->property_code) || empty($receipt_n->property_code)) {
                $this->error_in_validation = true;
                $this->error_data[] = 'Property Code is missing.';
            }
            /** User exist for the current company */
            if (isset($receipt_n->user_id)) {
                $user = $this->db->select('EIdNo,ECode')->from('srp_employeesdetails')->where('EIdNo', $receipt_n->user_id)->where('Erp_companyID', $this->company_id)->get()->row();
                if (!$user) {
                    $this->error_in_validation = true;
                    $this->error_data[] = 'User not exist for this company';
                }
            }
            /** Chart of Account ID is exist for current Company */
            if (isset($receipt_n->erp_chart_of_account_id)) {
                $chart_of_account = $this->db->select('GLAutoID')->from('srp_erp_chartofaccounts')->where('GLAutoID', $receipt_n->erp_chart_of_account_id)->where('companyID', $this->company_id)->get()->row();
                if (!$chart_of_account) {
                    $this->error_in_validation = true;
                    $this->error_data[] = 'Chart of Account not available for this company';
                }
            }
            /** return error if validation false */
            if ($this->error_in_validation == true) {
                $this->response(['type' => 'error', 'status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Server Side validation error.', 'data' => $this->error_data], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

    }

    private function insert_rv_document_entry($id, $code, $property_code, $unit_code)
    {
        $data['property_code'] = $property_code;
        $data['unit_code'] = $unit_code;
        $data['document_code'] = $code;
        $data['document_id'] = $id;
        $data['created_at'] = $this->now();
        $data['created_by'] = $this->user_id;
        $data['company_id'] = $this->company_id;

        $this->db->insert('srp_erp_property_documents', $data);
    }

    private function insert_document_entry($id, $code)
    {
        $data['property_code'] = $this->post->property_code;
        $data['unit_code'] = $this->post->unit_code;
        $data['document_code'] = $code;
        $data['document_id'] = $id;
        $data['created_at'] = $this->now();
        $data['created_by'] = $this->user_id;
        $data['company_id'] = $this->company_id;

        $this->db->insert('srp_erp_property_documents', $data);
    }

    private function now()
    {
        return date("Y-m-d H:i:s");
    }

    public function journal_voucher_post()
    {
        $this->set_common_data();


        $jv_master_status = $this->Api_erp_model->save_journal_entry_header($this->post);
        if (!empty($jv_master_status['data']['JVMasterAutoId'])) {
            $this->insert_document_entry($jv_master_status['data']['JVMasterAutoId'], 'JV');
        }
        $this->response($jv_master_status);
    }

    public function segment_master_get($id = 0)
    {
        $this->db->select('segmentID as value, concat(segmentCode," | " ,description) as label')
            ->from('srp_erp_segment')
            ->where('companyID', $this->company_id, FALSE)
            ->where('status', 1);

        if ($id > 0) {
            $this->db->where('segmentID', $id);
        } else {
            if (isset($this->keyword) && !empty($this->keyword)) {
                $this->db->where('(segmentCode LIKE \'%' . $this->keyword . '%\' OR description LIKE \'%' . $this->keyword . '%\')', null, false);
            }
        }

        $this->tmp_output = $this->db->limit($this->limit)->get()->result_array();
        $this->castToInt();

        $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully retrieved', 'data' => $this->tmp_output);
        $this->response($output);
    }

    function save_customer_post()
    {
        if ($this->post == null) {
            return $this->response(array('type' => 'error', 'status' => 500, 'message' => "Request body is not properly formatted."), 500);
        }

        $partyCategoryID = $this->post->partyCategoryID;
        if (isset($this->post->customerAutoID)) {
            $customerAutoID = $this->post->customerAutoID;
        } else {
            $customerAutoID = "";
            $this->post->customerAutoID = "";
        }
        $customerCurrency = $this->post->customerCurrency;
        $customercode = $this->post->customercode;
        $customerName = $this->post->customerName;
        $customercountry = $this->post->customercountry;
        $receivableAccount = $this->post->receivableAccount;
        $customerTelephone = $this->post->customerTelephone;
        $IdCardNumber = $this->post->IdCardNumber;
        $customerEmail = $this->post->customerEmail;
        $customerFax = $this->post->customerFax;
        $customerCreditPeriod = $this->post->customerCreditPeriod;
        $customerCreditLimit = $this->post->customerCreditLimit;
        $customerUrl = $this->post->customerUrl;
        $customerAddress1 = $this->post->customerAddress1;
        $customerAddress2 = $this->post->customerAddress2;
        $isActive = $this->post->isActive;
        $masterID = $this->post->masterID;
        $rebateGL = $this->post->rebateGL;
        $rebatePercentage = $this->post->rebatePercentage;
        $externalProductID = $this->post->externalProductID;
        $externalPrimaryKey = $this->post->externalPrimaryKey;

        $companyID = $this->common_data['company_data']['company_id'];

        $is_valid_step1 = true;//required field validation.
        $error_string = "";
        if (!$customerAutoID) {
            if ($customerCurrency == "" || $customerCurrency == null) {
                $this->post->customerCurrency = $this->db->query("select company_default_currencyID from srp_erp_company where company_id=$companyID")->row()->company_default_currencyID;
                $customerCurrency = $this->post->customerCurrency;
            }
        } else {
            $query = $this->db->query("select * from srp_erp_customermaster where customerAutoID=$customerAutoID");
            if ($query->num_rows() > 0) {
                //ok
            } else {
                $is_valid_step1 = false;
                $error_string .= "No customer found for $customerAutoID id. ";
            }
        }
        if (!is_int($customerCurrency)) {
            $is_valid_step1 = false;
            $error_string .= " customerCurrency field should be an integer. ";
        }
        if ($customercode == "") {
            $is_valid_step1 = false;
            $error_string .= " Customer code is required. ";
        }
        if ($customerName == "") {
            $is_valid_step1 = false;
            $error_string .= " Customer name is required. ";
        }
        if ($customerTelephone == "") {
            $is_valid_step1 = false;
            $error_string .= " Customer telephone is required. ";
        }
        if ($customercountry == "") {
            $is_valid_step1 = false;
            $error_string .= " Customer country is required. ";
        }
        if ($customerCurrency == "") {
            $is_valid_step1 = false;
            $error_string .= " Customer currency is required. ";
        }


        if ($is_valid_step1) {
            $is_valid_step2 = true;
            $error_string2 = "";
            if (!$customerAutoID) {
                $customercode_validation = $this->db->query("SELECT * FROM `srp_erp_customermaster` where secondaryCode='$customercode' and companyID = $companyID");
                if ($customercode_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Customer code is already exist. ";
                }
                $customerTelephone_validation = $this->db->query("SELECT * FROM `srp_erp_customermaster` where customerTelephone='$customerTelephone' and companyID = $companyID");
                if ($customerTelephone_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Customer telephone number is already exist. ";
                }
                $customerCurrency_validation = $this->db->query("SELECT * FROM `srp_erp_companycurrencyassign` where currencyID=$customerCurrency and companyID=$companyID");
                if ($customerCurrency_validation->num_rows() == 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Invalid currency id. ";
                }

                if ($receivableAccount == "" || $receivableAccount == null) {
                    $this->post->receivableAccount = $this->db->query("select * from srp_erp_companycontrolaccounts where companyID=$companyID and controlAccountType='ARA'")->row()->GLAutoID;
                } else {
                    $receivableAccount_validation = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` where GLAutoID=$receivableAccount");
                    if ($receivableAccount_validation->num_rows() == 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " Invalid GL AUTO ID. ";

                    } else {
                        $receivableAccount_row = $receivableAccount_validation->row();
                        if ($receivableAccount_row->controllAccountYN != 1) {
                            $is_valid_step2 = false;
                            $error_string2 .= " Invalid account type. ";
                        }
                    }
                }

                if (!(($externalPrimaryKey == "" || $externalPrimaryKey == null) && ($externalProductID == "" || $externalProductID == null))) {
                    $PrimaryKeyProductID = $this->db->query("SELECT * FROM `srp_erp_customermaster` where externalProductID='$externalProductID' and externalPrimaryKey='$externalPrimaryKey'");
                    if ($PrimaryKeyProductID->num_rows() > 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " ExternalProductID and ExternalPrimaryKey cannot duplicate. ";
                    }
                }
            } else {
                $customercode_validation = $this->db->query("SELECT * FROM `srp_erp_customermaster` where secondaryCode='$customercode' and companyID = $companyID and customerAutoID!=$customerAutoID");
                if ($customercode_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Customer code is already exist. ";
                }
                $customerTelephone_validation = $this->db->query("SELECT * FROM `srp_erp_customermaster` where customerTelephone='$customerTelephone' and companyID = $companyID and customerAutoID!=$customerAutoID");
                if ($customerTelephone_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Customer telephone number is already exist. ";
                }
                $customerCurrency_validation = $this->db->query("SELECT * FROM `srp_erp_companycurrencyassign` where currencyID=$customerCurrency and companyID=$companyID");
                if ($customerCurrency_validation->num_rows() == 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Invalid currency id. ";
                }

                if ($receivableAccount == "" || $receivableAccount == null) {
                    $this->post->receivableAccount = $this->db->query("select * from srp_erp_companycontrolaccounts where companyID=$companyID and controlAccountType='ARA'")->row()->GLAutoID;
                } else {
                    $receivableAccount_validation = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` where GLAutoID=$receivableAccount");
                    if ($receivableAccount_validation->num_rows() == 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " Invalid GL AUTO ID. ";

                    } else {
                        $receivableAccount_row = $receivableAccount_validation->row();
                        if ($receivableAccount_row->controllAccountYN != 1) {
                            $is_valid_step2 = false;
                            $error_string2 .= " Invalid account type. ";
                        }
                    }
                }

                if (!(($externalPrimaryKey == "" || $externalPrimaryKey == null) && ($externalProductID == "" || $externalProductID == null))) {
                    $PrimaryKeyProductID = $this->db->query("SELECT * FROM `srp_erp_customermaster` where externalProductID='$externalProductID' and externalPrimaryKey='$externalPrimaryKey' and customerAutoID!=$customerAutoID");
                    if ($PrimaryKeyProductID->num_rows() > 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " ExternalProductID and ExternalPrimaryKey cannot duplicate. ";
                    }
                }
            }


            if ($is_valid_step2) {
                return $this->response($this->Api_erp_model->save_customer($this->post, $companyID));
            } else {
                return $this->response(array('type' => 'error', 'status' => 500, 'message' => $error_string2), 500);
            }


        } else {
            return $this->response(array('type' => 'error', 'status' => 500, 'message' => $error_string), 500);
        }


    }

    function save_supplier_post()
    {
        $partyCategoryID = $this->post->partyCategoryID;
        if (isset($this->post->supplierAutoID)) {
            $supplierAutoID = $this->post->supplierAutoID;
        } else {
            $supplierAutoID = "";
            $this->post->supplierAutoID = "";
        }
        $supplierCurrency = $this->post->supplierCurrency;
        $suppliercode = $this->post->suppliercode;
        $supplierName = $this->post->supplierName;
        $suppliercountry = $this->post->suppliercountry;
        $liabilityAccount = $this->post->liabilityAccount;
        $supplierTelephone = $this->post->supplierTelephone;
        $nameOnCheque = $this->post->nameOnCheque;

        $supplierEmail = $this->post->supplierEmail;
        $supplierFax = $this->post->supplierFax;
        $supplierCreditPeriod = $this->post->supplierCreditPeriod;
        $supplierCreditLimit = $this->post->supplierCreditLimit;
        $supplierUrl = $this->post->supplierUrl;
        $supplierAddress1 = $this->post->supplierAddress1;
        $supplierAddress2 = $this->post->supplierAddress2;
        $isActive = $this->post->isActive;
        $externalProductID = $this->post->externalProductID;
        $externalPrimaryKey = $this->post->externalPrimaryKey;

        $companyID = $this->common_data['company_data']['company_id'];

        $is_valid_step1 = true;//required field validation.
        $error_string = "";
        if (!$supplierAutoID) {
            if ($supplierCurrency == "" || $supplierCurrency == null) {
                $this->post->supplierCurrency = $this->db->query("select company_default_currencyID from srp_erp_company where company_id=$companyID")->row()->company_default_currencyID;
                $supplierCurrency = $this->post->supplierCurrency;
            }
        }
        if ($suppliercode == "") {
            $is_valid_step1 = false;
            $error_string .= " Supplier code is required. ";
        }
        if ($supplierName == "") {
            $is_valid_step1 = false;
            $error_string .= " Supplier name is required. ";
        }
        if ($supplierTelephone == "") {
            $is_valid_step1 = false;
            $error_string .= " Supplier telephone is required. ";
        }
        if ($suppliercountry == "") {
            $is_valid_step1 = false;
            $error_string .= " Supplier country is required. ";
        }
        if ($supplierCurrency == "") {
            $is_valid_step1 = false;
            $error_string .= " Supplier currency is required. ";
        }


        if ($is_valid_step1) {

            $is_valid_step2 = true;
            $error_string2 = "";
            if (!$supplierAutoID) {
                $suppliercode_validation = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` where secondaryCode='$suppliercode' and companyID = $companyID");
                if ($suppliercode_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Supplier code is already exist. ";
                }
                /* $supplierTelephone_validation = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` where supplierTelephone='$supplierTelephone' and companyID = $companyID");
                if ($supplierTelephone_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Supplier telephone number is already exist. ";
                } */

                if (!(($externalPrimaryKey == "" || $externalPrimaryKey == null) && ($externalProductID == "" || $externalProductID == null))) {
                    $PrimaryKeyProductID = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` where externalProductID='$externalProductID' and externalPrimaryKey='$externalPrimaryKey'");
                    if ($PrimaryKeyProductID->num_rows() > 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " ExternalProductID and ExternalPrimaryKey cannot duplicate. ";
                    }
                }

                $supplierCurrency_validation = $this->db->query("SELECT * FROM `srp_erp_companycurrencyassign` where currencyID=$supplierCurrency and companyID=$companyID");
                if ($supplierCurrency_validation->num_rows() == 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Invalid currency id. ";
                }

                if ($liabilityAccount == "" || $liabilityAccount == null) {
                    $this->post->liabilityAccount = $this->db->query("select * from srp_erp_companycontrolaccounts where companyID=$companyID and controlAccountType='APA'")->row()->GLAutoID;
                } else {
                    $liabilityAccount_validation = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` where GLAutoID=$liabilityAccount");
                    if ($liabilityAccount_validation->num_rows() == 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " Invalid GL AUTO ID. ";

                    } else {
                        $liabilityAccount_validation_row = $liabilityAccount_validation->row();
                        if ($liabilityAccount_validation_row->controllAccountYN != 1) {
                            $is_valid_step2 = false;
                            $error_string2 .= " Invalid account type. ";
                        }
                    }
                }
            } else {
                $suppliercode_validation = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` where secondaryCode='$suppliercode' and companyID = $companyID and supplierAutoID!=$supplierAutoID");
                if ($suppliercode_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Supplier code is already exist. ";
                }
                /* $supplierTelephone_validation = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` where supplierTelephone='$supplierTelephone' and companyID = $companyID and supplierAutoID!=$supplierAutoID");
                if ($supplierTelephone_validation->num_rows() > 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Supplier telephone number is already exist. ";
                } */

                if (!(($externalPrimaryKey == "" || $externalPrimaryKey == null) && ($externalProductID == "" || $externalProductID == null))) {
                    $PrimaryKeyProductID = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` where externalProductID='$externalProductID' and externalPrimaryKey='$externalPrimaryKey' and supplierAutoID!=$supplierAutoID");
                    if ($PrimaryKeyProductID->num_rows() > 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " ExternalProductID and ExternalPrimaryKey cannot duplicate. ";
                    }
                }
                //=================
                $supplierCurrency_validation = $this->db->query("SELECT * FROM `srp_erp_companycurrencyassign` where currencyID=$supplierCurrency and companyID=$companyID");
                if ($supplierCurrency_validation->num_rows() == 0) {
                    $is_valid_step2 = false;
                    $error_string2 .= " Invalid currency id. ";
                }

                if ($liabilityAccount == "" || $liabilityAccount == null) {
                    $this->post->liabilityAccount = $this->db->query("select * from srp_erp_companycontrolaccounts where companyID=$companyID and controlAccountType='APA'")->row()->GLAutoID;
                } else {
                    $liabilityAccount_validation = $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` where GLAutoID=$liabilityAccount");
                    if ($liabilityAccount_validation->num_rows() == 0) {
                        $is_valid_step2 = false;
                        $error_string2 .= " Invalid GL AUTO ID. ";

                    } else {
                        $liabilityAccount_validation_row = $liabilityAccount_validation->row();
                        if ($liabilityAccount_validation_row->controllAccountYN != 1) {
                            $is_valid_step2 = false;
                            $error_string2 .= " Invalid account type. ";
                        }
                    }
                }
            }

            if ($is_valid_step2) {
                return $this->response($this->Api_erp_model->save_supplier_master($this->post, $companyID));
            } else {
                return $this->response(array('type' => 'error', 'status' => 500, 'message' => $error_string2), 500);
            }


        } else {
            return $this->response(array('type' => 'error', 'status' => 500, 'message' => $error_string), 500);
        }


    }

    function receipt_bulk_post()
    {
        $this->db->trans_start();
        $inserted_arr = array();
        $receipt_array = $this->post->receipt;
        foreach ($receipt_array as $receipt_n) {
            if (isset($receipt_n->user_id)) {
                $user = $this->db->select('EIdNo,ECode')->from('srp_employeesdetails')->where('EIdNo', $receipt_n->user_id)->where('Erp_companyID', $this->company_id)->get()->row();
                if (!$user) {
                    $this->error_in_validation = true;
                    $this->error_data[] = 'User not exist for this company';
                }
            }
            if (isset($receipt_n->erp_segment_id)) {
                $user = $this->db->select('segmentID, segmentCode')->from('srp_erp_segment')->where('segmentID', $receipt_n->erp_segment_id)->where('companyID', $this->company_id)->get()->row();
                if (!$user) {
                    $this->error_in_validation = true;
                    $this->error_data[] = 'Segment not available for this company';
                }
            }

            if (isset($receipt_n->bank_id)) {
                $user = $this->db->select('GLAutoID, systemAccountCode, GLDescription')->from('srp_erp_chartofaccounts')
                    ->where('isBank', 1)
                    ->where('isActive', 1)
                    ->where('masterAccountYN', 0)
                    ->where('GLAutoID', $receipt_n->bank_id)
                    ->where('companyID', $this->company_id)->get()->row();
                if (!$user) {
                    $this->error_in_validation = true;
                    $this->error_data[] = 'The Bank Account is not active!(' . $receipt_n->bank_id . ')';
                }
            }

            foreach ($receipt_n->detail as $receipt_det) {
                if (isset($receipt_det->erp_chart_of_account_id)) {
                    $user = $this->db->select('GLAutoID, systemAccountCode')->from('srp_erp_chartofaccounts')
                        ->where('GLAutoID', $receipt_det->erp_chart_of_account_id)
                        ->where('companyID', $this->company_id)
                        ->where('isActive', 1)
                        ->where('deletedYN != 1')
                        ->get()->row();
                    if (!$user) {
                        $this->error_in_validation = true;
                        $this->error_data[] = 'GL code not available for this company!(' . $receipt_det->erp_chart_of_account_id . ')';
                    }
                }

                if (isset($receipt_det->erp_segment_id)) {
                    $user = $this->db->select('segmentID, segmentCode')->from('srp_erp_segment')->where('segmentID', $receipt_det->erp_segment_id)->where('companyID', $this->company_id)->get()->row();
                    if (!$user) {
                        $this->error_in_validation = true;
                        $this->error_data[] = 'Segment not available for this company';
                    }
                }
            }

            /** return error if validation false */
            if ($this->error_in_validation == true) {
                $this->response(['type' => 'error', 'status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Server Side validation error.', 'data' => $this->error_data], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        foreach ($this->post->receipt as $key => $receiptHeader) {
            $date = $receiptHeader->document_date;
            $this->db->SELECT("*");
            $this->db->FROM('srp_erp_companyfinanceyear');
            $this->db->WHERE('companyID', $this->company_id);
            $this->db->where("'{$date}' BETWEEN beginingDate AND endingDate");
            $financeYearDetails = $this->db->get()->row_array();
            if (empty($financeYearDetails)) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Finance period not found for the selected document date!'), 500);
                exit;
            }
            $this->db->SELECT("*");
            $this->db->FROM('srp_erp_companyfinanceperiod');
            $this->db->WHERE('companyID', $this->company_id);
            $this->db->where("'{$date}' BETWEEN dateFrom AND dateTo");
            $financePeriodDetails = $this->db->get()->row_array();
            if (empty($financePeriodDetails)) {
                echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Finance period not found for the selected document date!'), 500);
                exit;
            }

            $bank_detail = fetch_gl_account_desc($receiptHeader->bank_id);
            $data['documentID'] = 'RV';
            $data['companyFinanceYearID'] = $financeYearDetails['companyFinanceYearID'];
            $data['companyFinanceYear'] = $financeYearDetails['beginingDate'] . ' - ' . $financeYearDetails['endingDate'];
            $data['FYBegin'] = trim($financeYearDetails['beginingDate']);
            $data['FYEnd'] = trim($financeYearDetails['endingDate']);
            $data['companyFinancePeriodID'] = $financePeriodDetails['companyFinancePeriodID'];
            $data['RVdate'] = $receiptHeader->document_date;
            $data['RVNarration'] = str_replace('<br />', PHP_EOL, $receiptHeader->comment);
            $data['segmentID'] = $receiptHeader->erp_segment_id;
            $data['segmentCode'] = $this->Api_erp_model->get_segment_code($receiptHeader->erp_segment_id);
            $data['RVbankCode'] = $receiptHeader->bank_id;
            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
            $data['RVbank'] = $bank_detail['bankName'];
            $data['RVbankBranch'] = $bank_detail['bankBranch'];
            $data['RVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
            $data['RVbankAccount'] = $bank_detail['bankAccountNumber'];
            $data['RVbankType'] = $bank_detail['subCategory'];
            $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
            $data['RVchequeNo'] = $receiptHeader->cheque_no;
            if ($bank_detail['isCash'] == 0) {
                $paymentMode = 1;
                $data['paymentType'] = 1;
                if ($paymentMode == 1) {
                    $data['RVchequeDate'] = trim($receiptHeader->cheque_date);
                    $data['bankTransferDetails'] = null;
                } else {
                    $data['bankTransferDetails'] = $receiptHeader->cheque_no;
                    $data['RVchequeDate'] = null;
                }
            } else {
                $data['RVchequeDate'] = null;
            }
            $data['RvType'] = 'DirectIncome';
            $data['referanceNo'] = $receiptHeader->reference;
            $data['customerName'] = $receiptHeader->customer_name;
            $data['customerTelephone'] = $receiptHeader->customerTelephone;
            $data['customerID'] = '';
            $data['customerAddress'] = '';
            $data['customerFax'] = '';
            $data['customerEmail'] = '';
            $data['customerCurrencyID'] = $receiptHeader->currency_id;
            $data['customerCurrency'] = $this->Api_erp_model->get_currency_code($receiptHeader->currency_id);
            $data['customerCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data['customerCurrencyID']);
            $data['transactionCurrencyID'] = $receiptHeader->currency_id;
            $data['transactionCurrency'] = $data['customerCurrency'];
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data['transactionCurrency']);
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
            $data['customerExchangeRate'] = $customer_currency['conversion'];
            $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
            $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
            $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
            $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
            $data['companyCode'] = $this->company_code;
            $data['companyID'] = $this->company_id;
            $data['createdUserGroup'] = 0;
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->user_id;
            $data['createdUserName'] = $this->user_name;
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['isSystemGenerated'] = 1;

            $data['RVcode'] = $this->sequence_generator('RV');
            /**confirmed*/
            $data['confirmedYN'] = 1;
            $data['confirmedByEmpID'] = $this->user_id;
            $data['confirmedByName'] = $this->user['Ename4'];
            $data['confirmedDate'] = $this->common_data['current_date'];
            /** Approval */
            $data['approvedYN'] = 1;
            $data['approvedbyEmpID'] = $this->user_id;
            $data['approvedbyEmpName'] = $this->user['Ename4'];
            $data['approvedDate'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerreceiptmaster', $data);
            $header_id = $this->db->insert_id();

            $this->common_data['current_userID'] = $this->user_id;
            $this->common_data['current_user'] = $this->user['Ename4'];
            $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data['documentID'], $header_id, $data['RVcode'], 'Receipt', 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 1, $data['RVdate']);
            $approval_levels = $this->approvals->maxlevel('RV');
            $number_of_levels = $approval_levels['levelNo'];
            for ($i = 1; $i <= $number_of_levels; $i++) {
                $this->approvals->approve_without_sending_email($header_id, $i, 1, '', $data['documentID']);
            }

            /** DETAIL  */
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID');
            $this->db->where('receiptVoucherAutoId', $header_id);
            $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

            foreach ($receiptHeader->detail as $key => $receiptDetail) {
                $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, $receiptDetail->erp_chart_of_account_id);
                $data_detail[$key]['receiptVoucherAutoId'] = $header_id;

                $data_detail[$key]['GLAutoID'] = $receiptDetail->erp_chart_of_account_id;
                $data_detail[$key]['systemGLCode'] = $chart_of_account_details->systemAccountCode;
                $data_detail[$key]['GLCode'] = $chart_of_account_details->GLSecondaryCode;
                $data_detail[$key]['GLDescription'] = $chart_of_account_details->GLDescription;
                $data_detail[$key]['GLType'] = $chart_of_account_details->subCategory;
                $data_detail[$key]['segmentID'] = $receiptDetail->erp_segment_id;
                $data_detail[$key]['segmentCode'] = $this->Api_erp_model->get_segment_code($receiptDetail->erp_segment_id);
                $data_detail[$key]['discountPercentage'] = $receiptDetail->discount_percentage;
                $data_detail[$key]['discountAmount'] = trim(($receiptDetail->amount * $receiptDetail->discount_percentage) / 100);
                $data_detail[$key]['transactionAmount'] = ($receiptDetail->amount - $data_detail[$key]['discountAmount']);
                $data_detail[$key]['companyLocalAmount'] = (($receiptDetail->amount - $data_detail[$key]['discountAmount']) / $master['companyLocalExchangeRate']);
                $data_detail[$key]['companyReportingAmount'] = (($receiptDetail->amount - $data_detail[$key]['discountAmount']) / $master['companyReportingExchangeRate']);
                $data_detail[$key]['customerAmount'] = 0;
                if ($master['customerExchangeRate']) {
                    $data_detail[$key]['customerAmount'] = ($receiptDetail->amount / $master['customerExchangeRate']);
                }
                $data_detail[$key]['description'] = $receiptDetail->description;
                $data_detail[$key]['type'] = 'GL';
                $data_detail[$key]['modifiedPCID'] = $this->common_data['current_pc'];
                $data_detail[$key]['modifiedUserID'] = $this->user_id;
                $data_detail[$key]['modifiedUserName'] = $this->user_name;
                $data_detail[$key]['modifiedDateTime'] = $this->common_data['current_date'];
                $data_detail[$key]['companyCode'] = $this->company_code;
                $data_detail[$key]['companyID'] = $this->company_id;
                $data_detail[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data_detail[$key]['createdUserID'] = $this->user_id;
                $data_detail[$key]['createdUserName'] = $this->user_name;
                $data_detail[$key]['createdDateTime'] = $this->common_data['current_date'];
            }
            $this->db->insert_batch('srp_erp_customerreceiptdetail', $data_detail);
            $data['document_id'] = $header_id;
            $data['details'] = $data_detail;
            $inserted_arr[] = $data;
            unset($data);

            $doubleEntries = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($header_id, 'RV');
            if (!empty($doubleEntries)) {
                $this->Api_erp_model->receipt_double_entry($doubleEntries);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $output = array('type' => 'error', 'status' => 500, 'message' => 'record insert failed', 'data' => null);
            $this->response($output);
        } else {
            $this->db->trans_commit();
            $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully posted', 'data' => $inserted_arr);
            $this->response($output);
        }
    }

    function receipt_voucher_tmdone_post()
    {
        $this->db->trans_start();
        $receiptHeader = $this->post;

        $user = $this->db->select('segmentID, segmentCode')->from('srp_erp_segment')->where('segmentID', 1)->where('companyID', $this->company_id)->get()->row();
        if (!$user) {
            $this->error_in_validation = true;
            $this->error_data[] = 'Segment not available for this company';
        }
        if ($receiptHeader->COD_account != 1 && $receiptHeader->POS_account != 1) {
            $this->error_in_validation = true;
            $this->error_data[] = 'Bank Account is needed';
        }
        if ($receiptHeader->COD_account == 1 && $receiptHeader->POS_account == 1) {
            $this->error_in_validation = true;
            $this->error_data[] = 'Please select one Bank Account';
        }
        /** return error if validation false */
        if ($this->error_in_validation == true) {
            $this->response(['type' => 'error', 'status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Server Side validation error.', 'data' => $this->error_data], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        $glNotActive = $this->db->select('GLAutoID, systemAccountCode')->from('srp_erp_chartofaccounts')
            ->where('GLAutoID IN (123, 140, 141, 136, 117, 118, 124)')
            ->where('companyID', $this->company_id)
            ->where('(isActive = 0 OR deletedYN = 1)')
            ->get()->result_array();
        if (!$glNotActive) {
            $this->error_in_validation = true;
            $this->error_data[] = 'GL code not active!(' . join(', ', array_column($glNotActive, 'systemAccountCode')) . ')';
        }

        $date = $receiptHeader->document_date;
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_companyfinanceyear');
        $this->db->WHERE('companyID', $this->company_id);
        $this->db->where("'{$date}' BETWEEN beginingDate AND endingDate");
        $financeYearDetails = $this->db->get()->row_array();
        if (empty($financeYearDetails)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Finance period not found for the selected document date!'), 500);
            exit;
        }
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_companyfinanceperiod');
        $this->db->WHERE('companyID', $this->company_id);
        $this->db->where("'{$date}' BETWEEN dateFrom AND dateTo");
        $financePeriodDetails = $this->db->get()->row_array();
        if (empty($financePeriodDetails)) {
            echo $this->response(array('type' => 'error', 'status' => 500, 'message' => 'Finance period not found for the selected document date!'), 500);
            exit;
        }

        if ($receiptHeader->COD_account == 1) {
            $bank_id = 140; // (TMD/BSA000050)
            $description = 'COD Collection / Actual Collection';
        } else {
            $bank_id = 141; // (TMD/BSA000051)
            $description = 'POS Collection / Actual Collection';
        }
        $bank_detail = fetch_gl_account_desc($bank_id);
        $data['documentID'] = 'RV';
        $data['companyFinanceYearID'] = $financeYearDetails['companyFinanceYearID'];
        $data['companyFinanceYear'] = $financeYearDetails['beginingDate'] . ' - ' . $financeYearDetails['endingDate'];
        $data['FYBegin'] = trim($financeYearDetails['beginingDate']);
        $data['FYEnd'] = trim($financeYearDetails['endingDate']);
        $data['companyFinancePeriodID'] = $financePeriodDetails['companyFinancePeriodID'];
        $data['RVdate'] = $receiptHeader->document_date;
        $data['RVNarration'] = $description;
        $data['segmentID'] = 1;
        $data['segmentCode'] = $this->Api_erp_model->get_segment_code(1);
        $data['RVbankCode'] = $bank_id;
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['RVbank'] = $bank_detail['bankName'];
        $data['RVbankBranch'] = $bank_detail['bankBranch'];
        $data['RVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['RVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['RVbankType'] = $bank_detail['subCategory'];
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['RVchequeNo'] = null;
        $data['RVchequeDate'] = null;
        $data['RvType'] = 'DirectIncome';
        $data['referanceNo'] = $receiptHeader->reference;
        $data['customerName'] = $receiptHeader->customer_name;
        $data['customerTelephone'] = $receiptHeader->customerTelephone;
        $data['customerID'] = '';
        $data['customerAddress'] = '';
        $data['customerFax'] = '';
        $data['customerEmail'] = '';
        $data['customerCurrencyID'] = $receiptHeader->currency_id;
        $data['customerCurrency'] = $this->Api_erp_model->get_currency_code($receiptHeader->currency_id);
        $data['customerCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data['customerCurrencyID']);
        $data['transactionCurrencyID'] = $receiptHeader->currency_id;
        $data['transactionCurrency'] = $data['customerCurrency'];
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = $this->get_currency_decimal_place($data['transactionCurrencyID']);
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
        $data['customerExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        $data['companyCode'] = $this->company_code;
        $data['companyID'] = $this->company_id;
        $data['createdUserGroup'] = 0;
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = 1;
        $data['createdUserName'] = $this->user_name;
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['isSystemGenerated'] = 1;

        $data['RVcode'] = $this->sequence_generator('RV');
        /**confirmed*/
        $data['confirmedYN'] = 1;
        $data['confirmedByEmpID'] = 1;
        $data['confirmedByName'] = 'API';
        $data['confirmedDate'] = $this->common_data['current_date'];
        /** Approval */
        $data['approvedYN'] = 1;
        $data['approvedbyEmpID'] = 1;
        $data['approvedbyEmpName'] = 'API';
        $data['approvedDate'] = $this->common_data['current_date'];

        $this->db->insert('srp_erp_customerreceiptmaster', $data);
        $header_id = $this->db->insert_id();

        $this->common_data['current_userID'] = 1;
        $this->common_data['current_user'] = $this->user['Ename4'];
        $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data['documentID'], $header_id, $data['RVcode'], 'Receipt', 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 1, $data['RVdate']);
        $approval_levels = $this->approvals->maxlevel('RV');
        $number_of_levels = $approval_levels['levelNo'];
        for ($i = 1; $i <= $number_of_levels; $i++) {
            $this->approvals->approve_without_sending_email($header_id, $i, 1, '', $data['documentID']);
        }

        /** DETAILS Addition */
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID');
        $this->db->where('receiptVoucherAutoId', $header_id);
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        /* Discount GL = 136  (TMD/PLE000065)*/
        if ($receiptHeader->discount_amount != 0) {
            $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, 136);
            $data_detail['receiptVoucherAutoId'] = $header_id;
            $data_detail['GLAutoID'] = 136;
            $data_detail['systemGLCode'] = $chart_of_account_details->systemAccountCode;
            $data_detail['GLCode'] = $chart_of_account_details->GLSecondaryCode;
            $data_detail['GLDescription'] = $chart_of_account_details->GLDescription;
            $data_detail['GLType'] = $chart_of_account_details->subCategory;
            $data_detail['segmentID'] = 1;
            $data_detail['segmentCode'] = $this->Api_erp_model->get_segment_code(1);
            $data_detail['discountPercentage'] = 0;
            $data_detail['discountAmount'] = 0;
            $data_detail['transactionAmount'] = $receiptHeader->discount_amount;
            $data_detail['companyLocalAmount'] = ($receiptHeader->discount_amount / $master['companyLocalExchangeRate']);
            $data_detail['companyReportingAmount'] = ($receiptHeader->discount_amount / $master['companyReportingExchangeRate']);
            $data_detail['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data_detail['customerAmount'] = ($receiptHeader->discount_amount / $master['customerExchangeRate']);
            }
            $data_detail['description'] = 'Discounts';
            $data_detail['type'] = 'GL';
            $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            $data_detail['modifiedUserID'] = 1;
            $data_detail['modifiedUserName'] = $this->user_name;
            $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
            $data_detail['companyCode'] = $this->company_code;
            $data_detail['companyID'] = $this->company_id;
            $data_detail['createdPCID'] = $this->common_data['current_pc'];
            $data_detail['createdUserID'] = 1;
            $data_detail['createdUserName'] = $this->user_name;
            $data_detail['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerreceiptdetail', $data_detail);
            $data['discount'] = $data_detail;
        }

        /* Commission GL = 118 (TMD/PLI000007)*/
        if ($receiptHeader->commission_amount != 0) {
            $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, 118);
            $data_detail['receiptVoucherAutoId'] = $header_id;
            $data_detail['GLAutoID'] = 118;
            $data_detail['systemGLCode'] = $chart_of_account_details->systemAccountCode;
            $data_detail['GLCode'] = $chart_of_account_details->GLSecondaryCode;
            $data_detail['GLDescription'] = $chart_of_account_details->GLDescription;
            $data_detail['GLType'] = $chart_of_account_details->subCategory;
            $data_detail['segmentID'] = 1;
            $data_detail['segmentCode'] = $this->Api_erp_model->get_segment_code(1);
            $data_detail['discountPercentage'] = 0;
            $data_detail['discountAmount'] = 0;
            $data_detail['transactionAmount'] = $receiptHeader->commission_amount;
            $data_detail['companyLocalAmount'] = ($receiptHeader->commission_amount / $master['companyLocalExchangeRate']);
            $data_detail['companyReportingAmount'] = ($receiptHeader->commission_amount / $master['companyReportingExchangeRate']);
            $data_detail['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data_detail['customerAmount'] = ($receiptHeader->commission_amount / $master['customerExchangeRate']);
            }
            $data_detail['description'] = 'TM Done Commission';
            $data_detail['type'] = 'GL';
            $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            $data_detail['modifiedUserID'] = 1;
            $data_detail['modifiedUserName'] = $this->user_name;
            $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
            $data_detail['companyCode'] = $this->company_code;
            $data_detail['companyID'] = $this->company_id;
            $data_detail['createdPCID'] = $this->common_data['current_pc'];
            $data_detail['createdUserID'] = 1;
            $data_detail['createdUserName'] = $this->user_name;
            $data_detail['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerreceiptdetail', $data_detail);
            $data['commission'] = $data_detail;
        }

        /* Delivery GL = 117 (TMD/PLI000006)*/
        if ($receiptHeader->delivery_amount != 0) {
            $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, 117);
            $data_detail['receiptVoucherAutoId'] = $header_id;
            $data_detail['GLAutoID'] = 117;
            $data_detail['systemGLCode'] = $chart_of_account_details->systemAccountCode;
            $data_detail['GLCode'] = $chart_of_account_details->GLSecondaryCode;
            $data_detail['GLDescription'] = $chart_of_account_details->GLDescription;
            $data_detail['GLType'] = $chart_of_account_details->subCategory;
            $data_detail['segmentID'] = 1;
            $data_detail['segmentCode'] = $this->Api_erp_model->get_segment_code(1);
            $data_detail['discountPercentage'] = 0;
            $data_detail['discountAmount'] = 0;
            $data_detail['transactionAmount'] = $receiptHeader->delivery_amount;
            $data_detail['companyLocalAmount'] = ($receiptHeader->delivery_amount / $master['companyLocalExchangeRate']);
            $data_detail['companyReportingAmount'] = ($receiptHeader->delivery_amount / $master['companyReportingExchangeRate']);
            $data_detail['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data_detail['customerAmount'] = ($receiptHeader->delivery_amount / $master['customerExchangeRate']);
            }
            $data_detail['description'] = 'Delivery Charges';
            $data_detail['type'] = 'GL';
            $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            $data_detail['modifiedUserID'] = 1;
            $data_detail['modifiedUserName'] = $this->user_name;
            $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
            $data_detail['companyCode'] = $this->company_code;
            $data_detail['companyID'] = $this->company_id;
            $data_detail['createdPCID'] = $this->common_data['current_pc'];
            $data_detail['createdUserID'] = 1;
            $data_detail['createdUserName'] = $this->user_name;
            $data_detail['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerreceiptdetail', $data_detail);
            $data['delivery'] = $data_detail;
        }

        /* Bank Service GL = 124 (TMD/PLI000008)*/
        if ($receiptHeader->bank_service_amount != 0) {
            $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, 124);
            $data_detail['receiptVoucherAutoId'] = $header_id;
            $data_detail['GLAutoID'] = 124;
            $data_detail['systemGLCode'] = $chart_of_account_details->systemAccountCode;
            $data_detail['GLCode'] = $chart_of_account_details->GLSecondaryCode;
            $data_detail['GLDescription'] = $chart_of_account_details->GLDescription;
            $data_detail['GLType'] = $chart_of_account_details->subCategory;
            $data_detail['segmentID'] = 1;
            $data_detail['segmentCode'] = $this->Api_erp_model->get_segment_code(1);
            $data_detail['discountPercentage'] = 0;
            $data_detail['discountAmount'] = 0;
            $data_detail['transactionAmount'] = $receiptHeader->bank_service_amount;
            $data_detail['companyLocalAmount'] = ($receiptHeader->bank_service_amount / $master['companyLocalExchangeRate']);
            $data_detail['companyReportingAmount'] = ($receiptHeader->bank_service_amount / $master['companyReportingExchangeRate']);
            $data_detail['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data_detail['customerAmount'] = ($receiptHeader->bank_service_amount / $master['customerExchangeRate']);
            }
            $data_detail['description'] = 'Bank Charges';
            $data_detail['type'] = 'GL';
            $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            $data_detail['modifiedUserID'] = 1;
            $data_detail['modifiedUserName'] = $this->user_name;
            $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
            $data_detail['companyCode'] = $this->company_code;
            $data_detail['companyID'] = $this->company_id;
            $data_detail['createdPCID'] = $this->common_data['current_pc'];
            $data_detail['createdUserID'] = 1;
            $data_detail['createdUserName'] = $this->user_name;
            $data_detail['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerreceiptdetail', $data_detail);
            $data['bankService'] = $data_detail;
        }

        /* General Payable GL = 123 (TMD/BSA000041)*/
        if ($receiptHeader->general_payable_amount != 0) {
            $chart_of_account_details = $this->Api_erp_model->fetch_gl_account_desc($this->company_id, 123);
            $data_detail['receiptVoucherAutoId'] = $header_id;
            $data_detail['GLAutoID'] = 123;
            $data_detail['systemGLCode'] = $chart_of_account_details->systemAccountCode;
            $data_detail['GLCode'] = $chart_of_account_details->GLSecondaryCode;
            $data_detail['GLDescription'] = $chart_of_account_details->GLDescription;
            $data_detail['GLType'] = $chart_of_account_details->subCategory;
            $data_detail['segmentID'] = 1;
            $data_detail['segmentCode'] = $this->Api_erp_model->get_segment_code(1);
            $data_detail['discountPercentage'] = 0;
            $data_detail['discountAmount'] = 0;
            $data_detail['transactionAmount'] = $receiptHeader->general_payable_amount;
            $data_detail['companyLocalAmount'] = ($receiptHeader->general_payable_amount / $master['companyLocalExchangeRate']);
            $data_detail['companyReportingAmount'] = ($receiptHeader->general_payable_amount / $master['companyReportingExchangeRate']);
            $data_detail['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data_detail['customerAmount'] = ($receiptHeader->general_payable_amount / $master['customerExchangeRate']);
            }
            $data_detail['description'] = 'Vendor Payable';
            $data_detail['type'] = 'GL';
            $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            $data_detail['modifiedUserID'] = 1;
            $data_detail['modifiedUserName'] = $this->user_name;
            $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
            $data_detail['companyCode'] = $this->company_code;
            $data_detail['companyID'] = $this->company_id;
            $data_detail['createdPCID'] = $this->common_data['current_pc'];
            $data_detail['createdUserID'] = 1;
            $data_detail['createdUserName'] = $this->user_name;
            $data_detail['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerreceiptdetail', $data_detail);
            $data['generalPayable'] = $data_detail;
        }

        $update_data['confirmedByName'] = 'API';
        $update_data['approvedbyEmpName'] = 'API';
        $this->db->where('receiptVoucherAutoId', trim($header_id));
        $this->db->update('srp_erp_customerreceiptmaster', $update_data);

        $data['document_id'] = $header_id;
        $inserted_arr = $data;
        $doubleEntries = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($header_id, 'RV');
        if (!empty($doubleEntries)) {
            $this->Api_erp_model->receipt_double_entry($doubleEntries);
        }

        $data_app['companyID'] = $this->company_id;
        $data_app['companyCode'] = $this->company_code;
        $data_app['departmentID'] = 'RV';
        $data_app['documentID'] = 'RV';
        $data_app['documentSystemCode'] = $header_id;
        $data_app['documentCode'] = $data['RVcode'];
        $data_app['table_name'] = 'srp_erp_customerreceiptmaster';
        $data_app['table_unique_field_name'] = 'receiptVoucherAutoId';
        $data_app['documentDate'] = $data['RVdate'];
        $data_app['approvalLevelID'] = 1;
        $data_app['roleID'] = null;
        $data_app['approvalGroupID'] = 0;
        $data_app['roleLevelOrder'] = null;
        $data_app['docConfirmedDate'] = $this->common_data['current_date'];
        $data_app['docConfirmedByEmpID'] = 1;
        $data_app['approvedEmpID'] = 1;
        $data_app['approvedYN'] = 1;
        $data_app['approvedDate'] = $this->common_data['current_date'];
        $data_app['approvedPC'] = $this->common_data['current_pc'];
        $data_app['timeStamp'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_documentapproved', $data_app);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $output = array('type' => 'error', 'status' => 500, 'message' => 'record insert failed', 'data' => null);
            $this->response($output);
        } else {
            $this->db->trans_commit();
            $output = array('type' => 'success', 'status' => 200, 'message' => 'record successfully posted', 'data' => $inserted_arr);
            $this->response($output);
        }
    }

    function kitchenOrdersPrintList_post()
    {

        $kotID = $this->input->post('kotID');
        $warehouseID = $this->input->post('warehouseID');
        $companyID = $this->input->post('companyID');

        $pendingOrders = $this->Pos_kitchen_model->get_invoiceIDs_pendingOrders_API($kotID, $warehouseID, $companyID);

        $data['pendingOrders'] = $pendingOrders;
        $data['currentOrders'] = array();
        $data['kotID'] = $kotID;

        $show_price = is_show_price_in_KOT_print();

        $html = '';
        $response['itemList'] = array();
        $response['orders_id_list'] = array();
        if (!empty($pendingOrders)) {
            foreach ($pendingOrders as $pendingOrder) {
                array_push($response['orders_id_list'], $pendingOrder['menuSalesID']);
                $data['masters'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesmaster_kitchenStatus_API($pendingOrder['menuSalesID'], $warehouseID);
                $data['invoiceList'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesitems_invoiceID_kotAlarm_API($pendingOrder['menuSalesID'], $kotID, $warehouseID);
                $data['waiterName'] = $this->Pos_kitchen_model->getWaiterNameByMenuSalesID($pendingOrder['menuSalesID']);
                foreach ($data['invoiceList'] as $item) {
                    array_push($response['itemList'],$item['menuSalesItemID']);
                }
                $data['print'] = false;
                $data['newBill'] = false;
                if ($show_price) {
                    $html .= $this->load->view('system/pos/printTemplateKitchenOrder/restaurant-pos-dotmatric-printer-pdf-with-price', $data, true);
                } else {
                    $html .= $this->load->view('system/pos/printTemplateKitchenOrder/restaurant-pos-dotmatric-printer-pdf', $data, true);
                }
                $html .= "<br/>";
            }
            $html .= " <br/>.<br/><br/>";
        }
        $response['html_print'] = $html;
        $output = array('type' => 'success', 'status' => 200, 'message' => 'Pending kitchen orders.', 'data' => $response);
        $this->response($output);
    }

    public function updatePrintedOrders_post()
    {
        $idList = $this->input->post('idList', true);
        $itemList = $this->input->post('itemList', true);
        updateKOT_alarm($idList, $itemList);
        $output = array('type' => 'success', 'status' => 200, 'message' => 'Updated.', 'data' => "");
        $this->response($output);
    }

}
