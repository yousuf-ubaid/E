<?php
/**
 * Created by PhpStorm.
 * Date: 08-Nov-19
 * Time: 11:40 AM
 */

/**
 *
 * ==========================================
 * Technical Documentation 
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

class Api_pos extends REST_Controller
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
        $this->load->model('Api_pos_model');
        $this->load->model('Double_entry_model');
        $this->load->model('session_model');
        $this->load->library('sequence');
        $this->auth();
        $this->set_limit();
        $this->set_keyword();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//            $this->set_user();
//            $this->auth_user();
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

    function importpossaleswithitems_post()
    {
        $companyID = $this->company_id;
        $dateFrom = $this->post('dateFrom');
        $dateTo = $this->post('dateTo');
        $outletID = $this->post('outletID');
        $approval = $this->Api_pos_model->posSalesWithItems_details($dateFrom, $dateTo, $outletID, $companyID);
        if ($approval) {
            $final_output['success'] = true;
            $final_output['message'] = 'Data retrieved successfully';
            $final_output['data'] = $approval;
            $this->response($final_output);

        } else {
            $final_output['success'] = false;
            $final_output['message'] = 'No details were found';
            $final_output['data'] = [];
            $this->response($final_output);
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

}