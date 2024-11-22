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

class Api_wowfood extends REST_Controller
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
        $this->load->model('Pos_restaurant_model');
        $this->load->model('Api_wowfood_model');
//        $this->auth();
//        $this->set_limit();
//        $this->set_keyword();
//        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
////            $this->set_user();
////            $this->auth_user();
//        }
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

    public function get_all_restaurants_get()
    {
        $location_id = $this->input->get("location_id");

        $this->load->database('default');
        $query = $this->db->query("select * from `restaurantmaster` where locationID=$location_id and wowFoodYN=1");
        $restaurants = array();
        foreach ($query->result() as $row) {
            $restaurant = array();
            $restaurant['restaurantID'] = $row->restaurantID;
            $restaurant['companyID'] = $row->companyID;
            $restaurant['warehouseAutoID'] = $row->warehouseAutoID;
            $restaurant['restaurantName'] = $row->restaurantName;
            $restaurant['locationID'] = $row->locationID;
            $restaurant['location'] = $row->location;
            $restaurant['latitude'] = $row->latitude;
            $restaurant['longitude'] = $row->longitude;
            $restaurant['ownDeliveryYN'] = $row->ownDeliveryYN;
            $restaurant['wowFoodYN'] = $row->wowFoodYN;
            array_push($restaurants, $restaurant);
        }

        $final_output['message'] = 'Restaurants list retrieved.';
        $final_output['data'] = $restaurants;
        $this->response($final_output);
    }

    public function get_menu_categories_get()
    {
        //required libraries
        $this->load->library('s3');

        //url parameters
        $restaurant_id = $this->input->get("restaurant_id");
        $this->load->database('default');
        $query = $this->db->query("SELECT * FROM `restaurantmaster` where restaurantID=$restaurant_id");
        $restaurant = $query->row();
        $warehouse_id = $restaurant->warehouseAutoID;
        $config = $this->get_db_config($restaurant_id);
        $this->load->database($config, FALSE, TRUE);

        $menu_category_query = $this->db->query("SELECT
	srp_erp_pos_warehousemenucategory.autoID,
	srp_erp_pos_menucategory.menuCategoryDescription,
	srp_erp_pos_menucategory.image 
FROM
	srp_erp_pos_warehousemenumaster
	JOIN `srp_erp_pos_warehousemenucategory` ON srp_erp_pos_warehousemenumaster.warehouseMenuCategoryID = srp_erp_pos_warehousemenucategory.autoID
	JOIN srp_erp_pos_menucategory ON srp_erp_pos_warehousemenucategory.menuCategoryID = srp_erp_pos_menucategory.menuCategoryID 
WHERE
	srp_erp_pos_warehousemenucategory.warehouseID = $warehouse_id 
	AND srp_erp_pos_warehousemenumaster.isActive = 1 
	AND srp_erp_pos_warehousemenumaster.isDeleted = 0 
GROUP BY
	srp_erp_pos_warehousemenucategory.autoID");

        $menu_category_list = array();
        foreach ($menu_category_query->result() as $row) {
            $menu_category = array();
            $menu_category['warehouseMenuCategory'] = $row->autoID;
            $menu_category['menuCategoryDescription'] = $row->menuCategoryDescription;
            $menu_category['image'] = "http://" . $_SERVER['HTTP_HOST'] . base_url($row->image);
            array_push($menu_category_list, $menu_category);
        }

        $final_output['message'] = 'Menu category list retrieved.';
        $final_output['data'] = $menu_category_list;
        $this->response($final_output);

    }

    public function get_menu_list_get()
    {
        //required libraries
        $this->load->library('s3');

        //url parameters
        $restaurant_id = $this->input->get("restaurant_id");
        $warehouse_menu_category_id = $this->input->get("warehouse_menu_category_id");

        //get db config to specific restaurant
        $config = $this->get_db_config($restaurant_id);
        $this->load->database($config, FALSE, TRUE);

        $query = $this->db->query("SELECT
srp_erp_pos_warehousemenumaster.warehouseID,
srp_erp_pos_warehousemenumaster.warehouseMenuID,
	srp_erp_pos_menumaster.menuMasterID,
	srp_erp_pos_menumaster.menuMasterDescription,
	srp_erp_pos_menumaster.menuImage,
	srp_erp_pos_menumaster.sellingPrice
FROM
	srp_erp_pos_warehousemenumaster
	JOIN srp_erp_pos_menumaster ON srp_erp_pos_menumaster.menuMasterID = srp_erp_pos_warehousemenumaster.menuMasterID 
WHERE
	srp_erp_pos_warehousemenumaster.warehouseMenuCategoryID = '$warehouse_menu_category_id'
    AND isActive = 1 AND srp_erp_pos_menumaster.isDeleted = 0
    ");
        $menu_list = array();
        foreach ($query->result() as $row) {
            $menu = array();
            $menu['warehouseMenuID'] = $row->warehouseMenuID;
            $menu['menuMasterDescription'] = $row->menuMasterDescription;
            $menu['menuImage'] = "http://" . $_SERVER['HTTP_HOST'] . base_url($row->menuImage);
            $menu['sellingPrice'] = $row->sellingPrice;
            $menu['warehouseID'] = $row->warehouseID;
            array_push($menu_list, $menu);
        }

        $final_output['message'] = 'Menu list retrieved.';
        $final_output['data'] = $menu_list;
        $this->response($final_output);
    }

    public function get_locations_get()
    {
        $this->load->database('default');
        $query = $this->db->query("SELECT * FROM `srp_erp_locations`");
        $locations = array();
        foreach ($query->result() as $row) {
            $location = array();
            $location['id'] = $row->id;
            $location['description'] = $row->description;

            array_push($locations, $location);
        }
        $final_output['message'] = 'Locations retrieved.';
        $final_output['data'] = $locations;
        $this->response($final_output);
    }

    public function place_an_order_post()
    {
        $restaurant_id = $this->input->post("restaurant_id");
        $warehouse_id = $this->input->post('warehouseID');
        $customertelephoneno = $this->input->post('customertelephoneno');
        $CustomerName = $this->input->post('CustomerName');
        $customerEmail = $this->input->post('customerEmail');
        $customerAutoID = '';
        //set db by restaurant id
        $config = $this->get_db_config($restaurant_id);

        $company_id = $this->Api_wowfood_model->get_company_id_by_restaurant_id($restaurant_id);
        $company_details = $this->Api_wowfood_model->get_company_details($company_id);
        $shiftdetails = $this->Api_wowfood_model->get_srp_erp_pos_shiftdetails_employee($config, $warehouse_id);

        //currency details
        $currency_details = $this->Api_wowfood_model->get_company_currency_details($company_id);

        $SN = $this->Api_wowfood_model->generate_pos_invoice_no($warehouse_id, $company_id, $shiftdetails['empID']);
        $company_countryDet = $this->db->query("SELECT srp_erp_countrymaster.countryID , CountryDes, countryCode FROM `srp_erp_company` 
                                                    LEFT JOIN srp_erp_countrymaster on srp_erp_countrymaster.countryID = srp_erp_company.countryID WHERE company_id = $company_id ")->row_array();
        //var_dump($SN);exit;
        if(!empty($customertelephoneno)) {

            $iscustomertelexist = $this->db->query("SELECT posCustomerAutoID,customerEmail,CustomerAddress1,CustomerName FROM `srp_erp_pos_customermaster` where companyID = $company_id  AND customerTelephone = '$customertelephoneno'")->row_array();
            if (!empty($iscustomertelexist)) {
                $customerAutoID = $iscustomertelexist['posCustomerAutoID'];
                if ($customerEmail!=$iscustomertelexist['customerEmail'])
                {
                    $data_update_customer['customerEmail'] = $customerEmail;
                    $this->db->where('posCustomerAutoID', $customerAutoID);
                    $this->db->update('srp_erp_pos_customermaster', $data_update_customer);
                }
              /*  if($CustomerName!=$iscustomertelexist['CustomerName'])
                {
                    $data_update_customer['CustomerName'] = $CustomerName;
                    $this->db->where('posCustomerAutoID', $customerAutoID);
                    $this->db->update('srp_erp_pos_customermaster', $data_update_customer);
                }*/
            }else
            {
                $data_ins_customer['wareHouseAutoID'] = $warehouse_id;
                $data_ins_customer['CustomerName'] = $CustomerName;
                $data_ins_customer['customerCountryId'] = $company_countryDet['countryID'];
                $data_ins_customer['customerCountry'] = $company_countryDet['CountryDes'];
                $data_ins_customer['customerTelephone'] = $customertelephoneno;
                $data_ins_customer['customerCountryCode'] = $company_countryDet['countryCode'];
                $data_ins_customer['customerEmail'] = $customerEmail;
                $data_ins_customer['isActive'] = 1;
                $data_ins_customer['companyID'] = $company_id;
                $result = $this->db->insert('srp_erp_pos_customermaster', $data_ins_customer);
                if ($result) {
                    $id = $this->db->insert_id();
                    $customerAutoID = $id;
                } else {
                    $data['message'] = "Something Went Wrong.";
                    $this->response($data);
                }
            }


        }

        if ($shiftdetails == null) {
            $data['message'] = "Order cannot be placed";
            $this->response($data);
        } else {
            $empID = $shiftdetails['empID'];
            $CustomerType = 1;
            $SN = $this->Api_wowfood_model->generate_pos_invoice_no($warehouse_id, $company_id);
            $data['customerTypeID'] = $CustomerType;
            $data['documentSystemCode'] = '';
            $data['documentCode'] = '';
            $data['serialNo'] = $SN;
            $data['invoiceSequenceNo'] = $SN;
            $data['invoiceCode'] = $this->Api_wowfood_model->generate_pos_invoice_code($warehouse_id, $company_id, $shiftdetails['empID']);
            $data['customerID'] = '';
            $data['customerCode'] = '';

            $data['customerName'] = $CustomerName;
            $data['customerTelephone'] = $customertelephoneno;
            $data['customerID'] =$customerAutoID;

            $data['shiftID'] = null;

            if ($this->input->post('tabOrder') == 1) {
                $data['counterID'] = null;
                $data['isHold'] = 1;
                $data['tabUserID'] = null;
            } else {
                $data['counterID'] = null;
            }

            $data['menuSalesDate'] = format_date_mysql_datetime();
            $data['holdDatetime'] = format_date_mysql_datetime();
            $data['companyID'] = $company_id;
            $data['companyCode'] = $company_details['company_code'];

            $data['subTotal'] = '';
            $data['discountPer'] = '';
            $data['discountAmount'] = '';
            $data['netTotal'] = '';

            $data['wareHouseAutoID'] = $this->input->post('warehouseID');

            $data['segmentID'] = null;
            $data['segmentCode'] = null;

            $data['salesDay'] = date('l');
            $data['salesDayNum'] = date('w');


            $tr_currency = $currency_details['company_default_currency'];//$this->common_data['company_data']['company_default_currency'];
            $transConversion = $this->Api_wowfood_model->currency_conversion($tr_currency, $tr_currency, 0, $company_id);
//var_dump($transConversion);
            $data['transactionCurrencyID'] = $transConversion['currencyID'];
            $data['transactionCurrency'] = $transConversion['CurrencyCode'];
            $data['transactionExchangeRate'] = $transConversion['conversion'];
            $data['transactionCurrencyDecimalPlaces '] = $transConversion['DecimalPlaces'];

            $defaultCurrencyID = $currency_details['company_default_currencyID'];//$this->common_data['company_data']['company_default_currencyID'];
            $defaultConversion = $this->Api_wowfood_model->currency_conversionID($transConversion['currencyID'], $defaultCurrencyID, 0, $company_id);

            $data['companyLocalCurrencyID'] = $defaultCurrencyID;
            $data['companyLocalCurrency'] = $currency_details['company_default_currency'];//$this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = $defaultConversion['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $currency_details['company_default_decimal'];// $this->common_data['company_data']['company_default_decimal'];

            $repCurrencyID = $currency_details['company_reporting_currencyID'];//$this->common_data['company_data']['company_reporting_currencyID'];
            $transConversion = $this->Api_wowfood_model->currency_conversionID($transConversion['currencyID'], $repCurrencyID, 0, $company_id);

            $data['companyReportingCurrencyID'] = $repCurrencyID;
            $data['companyReportingCurrency'] = $currency_details['company_reporting_currency'];//$this->common_data['company_data']['company_reporting_currency'];
            $data['companyReportingExchangeRate'] = $transConversion['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $currency_details['company_reporting_decimal'];//$this->common_data['company_data']['company_reporting_decimal'];


            /*update the transaction currency detail for later use */
            $tr_currency = $currency_details['company_default_currency'];// $this->common_data['company_data']['company_default_currency'];
            $customerCurrencyConversion = $this->Api_wowfood_model->currency_conversion($tr_currency, $tr_currency, 0, $company_id);

            $data['customerCurrencyID'] = $customerCurrencyConversion['currencyID'];
            $data['customerCurrency'] = $customerCurrencyConversion['CurrencyCode'];
            $data['customerCurrencyExchangeRate'] = $customerCurrencyConversion['conversion'];
            $data['customerCurrencyDecimalPlaces'] = $customerCurrencyConversion['DecimalPlaces'];


            /*Audit Data */
            $data['createdUserGroup'] = '';//current_user_group();
            $data['createdPCID'] = '';//current_pc();
            $data['createdUserID'] = '';//current_userID();
            $data['createdUserName'] = '';//current_user();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['modifiedPCID'] = '';
            $data['modifiedUserID'] = '';
            $data['modifiedUserName'] = '';
            $data['modifiedDateTime'] = '';
            $data['timestamp'] = format_date_mysql_datetime();
            $data['is_sync'] = 0;
            $data['id_store'] = $warehouse_id;
            $data['isFromTablet'] = 0;
            $data['paymentMethod'] = 1;
            $data['wowFoodYN'] = 1;
            $invoiceID = $this->Api_wowfood_model->insert_srp_erp_pos_menusalesmaster($data);
            $data['menuSalesID'] = $invoiceID;
            //////////////////////////////////////////////////////////////////////////////////////////////////////////
            //set_session_invoiceID($invoiceID);
            $warehouseMenuIdArray = $this->input->post('warehouseMenuIdArray[]');

            $outlettemplateID = $this->db->query("SELECT outtemplatedet.outletTemplateMasterID as outletTemplateMasterID FROM
                                                `srp_erp_pos_outlettemplatedetail` outtemplatedet where outletID = $warehouse_id")->row('outletTemplateMasterID');


            foreach ($warehouseMenuIdArray as $item) {
                $item = json_decode($item);
                $output = $this->Pos_restaurant_model->get_warehouseMenu_specific($item->warehouseMenuId);
                $sellingPrice = getSellingPricePolicy($outlettemplateID, $output['pricewithoutTax'], $output['totalTaxAmount'], $output['totalServiceCharge']);
                /* Insert Menu */
                $data_item['menuSalesID'] = $invoiceID;
                $data_item['wareHouseAutoID'] = $warehouse_id;
                $data_item['menuID'] = $output['menuMasterID'];
                $data_item['menuCategoryID'] = $output['menuCategoryID'];
                $data_item['warehouseMenuID'] = $output['warehouseMenuID'];
                $data_item['warehouseMenuCategoryID'] = $output['warehouseMenuCategoryID'];
                $data_item['defaultUOM'] = 'each';
                $data_item['unitOfMeasure'] = 'each';
                $data_item['conversionRateUOM'] = 1;

                $data_item['menuCost'] = $output['menuCost'];
                $data_item['menuSalesPrice'] = $output['pricewithoutTax'];
                $data_item['qty'] = $item->qty;
                $data_item['discountPer'] = 0;
                $data_item['discountAmount'] = 0;

                /** KOT Kitchen order ticket detail */
                $parentMenuSalesItemID = 0;//$this->input->post('parentMenuSalesItemID');
                $data_item['kotID'] = 0;//$parentMenuSalesItemID > 0 ? 0 : $this->input->post('kotID');
                $data_item['kitchenNote'] = trim($this->input->post('kitchenNote') ?? '');
                $data_item['isOrderPending'] = -1;

                /** Add-on */
                $data_item['parentMenuSalesItemID'] = $parentMenuSalesItemID;

                /** Tax Calculation */
                $data_item['TAXpercentage'] = $output['TAXpercentage'];
                $data_item['TAXAmount'] = $output['TAXpercentage'] > 0 ? $output['sellingPrice'] * ($output['TAXpercentage'] / 100) : null;
                $data_item['taxMasterID'] = $output['taxMasterID'];

                $transCurrencyID = $this->Api_wowfood_model->getCurrencyID_byCurrencyCode($shiftdetails['transactionCurrency']);
                $data_item['transactionCurrencyID'] = $transCurrencyID;
                $data_item['transactionCurrency'] = $shiftdetails['transactionCurrency'];
                $data_item['transactionAmount'] = $sellingPrice;
                $data_item['transactionCurrencyDecimalPlaces'] = $shiftdetails['transactionCurrencyDecimalPlaces'];
                $data_item['transactionExchangeRate'] = $shiftdetails['transactionExchangeRate'];

                $reportingCurrencyID = $currency_details['company_reporting_currencyID'];//$this->common_data['company_data']['company_reporting_currencyID'];
                $conversion = $this->Api_wowfood_model->currency_conversionID($transCurrencyID, $reportingCurrencyID, $sellingPrice, $company_id);

                $data_item['companyReportingCurrency'] = $reportingCurrencyID;
                $data_item['companyReportingAmount'] = $conversion['convertedAmount'];
                $data_item['companyReportingCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                $data_item['companyReportingExchangeRate'] = $conversion['conversion'];

                $defaultCurrencyID = $currency_details['company_default_currencyID'];//$this->common_data['company_data']['company_default_currencyID'];
                $conversion = $this->Api_wowfood_model->currency_conversionID($transCurrencyID, $defaultCurrencyID, $sellingPrice, $company_id);

                $data_item['companyLocalCurrencyID'] = $defaultCurrencyID;
                $data_item['companyLocalCurrency'] = $currency_details['company_default_currency'];//$this->common_data['company_data']['company_default_currency'];
                $data_item['companyLocalAmount'] = $conversion['convertedAmount'];
                $data_item['companyLocalExchangeRate'] = $conversion['conversion'];
                $data_item['companyLocalCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                $data_item['companyID'] = $company_id;
                $data_item['companyCode'] = $company_details['company_code'];
                $data_item['revenueGLAutoID'] = $output['revenueGLAutoID'];
                $data_item['createdUserGroup'] = null;
                $data_item['createdPCID'] = null;
                $data_item['createdUserID'] = null;
                $data_item['createdDateTime'] = null;
                $data_item['createdUserName'] = null;
                $data_item['modifiedPCID'] = null;
                $data_item['modifiedUserID'] = null;
                $data_item['modifiedDateTime'] = null;
                $data_item['modifiedUserName'] = null;
                $data_item['timestamp'] = format_date_mysql_datetime();
                $data_item['id_store'] = $warehouse_id;


                /*Insert Menu */
                $code = $this->Api_wowfood_model->insert_srp_erp_pos_menusalesitems($data_item);
                //$this->updateNetTotalForInvoice($invoiceID);

            }
            $data['message'] = "Successfully placed the order.";
            $this->response($data);
        }


    }

    public function get_order_details_get()
    {
        $menuSalesID = $this->input->get("menuSalesID");
        $restaurant_id = $this->input->get("restaurant_id");

        //get db config to specific restaurant
        $config = $this->get_db_config($restaurant_id);

        $final_output['message'] = 'Order details retrieved.';
        $final_output['data'] = $this->Api_wowfood_model->get_order_details($config, $menuSalesID);
        $this->response($final_output);

    }


    private function get_db_config($restaurant_id)
    {
        $main_db = $this->load->database('default', TRUE);
        $query = $main_db->query("SELECT * FROM `restaurantmaster` where restaurantID=$restaurant_id");
        $restaurant = $query->row();
        $company_id = $restaurant->companyID;
        $query = $main_db->query("SELECT * FROM `srp_erp_company` where company_id=$company_id");
        $company_details = $query->row();
        $config['hostname'] = trim($this->encryption->decrypt($company_details->host));
        $config['username'] = trim($this->encryption->decrypt($company_details->db_username));
        $config['password'] = trim($this->encryption->decrypt($company_details->db_password));
        $config['database'] = trim($this->encryption->decrypt($company_details->db_name));
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
        return $config;
    }


}
