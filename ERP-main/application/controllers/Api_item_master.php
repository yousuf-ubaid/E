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
class Api_item_master extends REST_Controller
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


    //-----------Item Master--------------//

    function save_item_master()
    {
        $this->db->trans_start();
        $company_id=current_companyID();
        $ApprovalforItemMaster= getPolicyValues('AIM', 'All');

        if (!empty(trim($this->post('revanue')) && trim($this->post('revanue') != 'Select Revenue GL Account'))) {
            $revanue = explode('|', trim($this->post('revanue')));
        }
        $cost = explode('|', trim($this->post('cost')));
        $asste = explode('|', trim($this->post('asste')));
        $mainCategory = explode('|', trim($this->post('mainCategory')));
        $stockadjustment=explode('|', trim($this->post('stockadjustment')));
        $isactive = 0;
        $isSellThis = 1;
        $isBuyThis = 1;

        if (!empty($this->post('isActive'))) {
            $isactive = 1;
        }
        if (empty($this->post('sell_this'))) {
            $isSellThis = 0;
        }
        if (empty($this->post('buy_this'))) {
            $isBuyThis = 0;
        }

        $generatedtype = $this->post('generatedtype');
        $uom = explode('|', trim($this->post('uom')));
        $data['isActive'] = $isactive;
        $data['allowedtoSellYN'] = $isSellThis;
        $data['allowedtoBuyYN'] = $isBuyThis;
        $data['seconeryItemCode'] = trim($this->post('seconeryItemCode'));
        $data['secondaryUOMID'] = trim($this->post('secondaryUOMID'));
 /*       $data['itemName'] = clear_descriprions(trim($this->input->post('itemName') ?? ''));*/
        $data['itemName'] = $this->post('itemName');
      /*  $data['itemDescription'] = clear_descriprions(trim($this->input->post('itemDescription') ?? ''));*/
        $data['itemDescription'] = $this->post('itemDescription');
        $data['subcategoryID'] = trim($this->post('subcategoryID'));
        $data['subSubCategoryID'] = trim($this->post('subSubCategoryID'));
        $data['partNo'] = trim($this->post('partno'));
        $data['reorderPoint'] = trim($this->post('reorderPoint'));
        $data['maximunQty'] = trim($this->post('maximunQty'));
        $data['minimumQty'] = trim($this->post('minimumQty'));
        $data['defaultUnitOfMeasureID'] = trim($this->post('defaultUnitOfMeasureID'));
        $data['defaultUnitOfMeasure'] = trim($uom[0] ?? '');
        $data['comments'] = trim($this->post('comments'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalExchangeRate'] = 1;
        $data['companyLocalSellingPrice'] = trim($this->post('companyLocalSellingPrice'));
        $data['companyLocalPurchasingPrice'] = trim($this->post('companyLocalPurchasingPrice'));
        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyReportingSellingPrice'] = ($data['companyLocalSellingPrice'] / $data['companyReportingExchangeRate']);
        $data['companyReportingPurchasingPrice'] = ($data['companyLocalPurchasingPrice'] / $data['companyReportingExchangeRate']);

        $data['isSubitemExist'] = trim($this->post('isSubitemExist'));
        $data['subItemapplicableon'] = (($this->post('isSubitemExist') == 1)?$this->input->post('subItem') :1);

        if($this->post('revanueGLAutoID')){
            $data['mainCategory'] = trim($mainCategory[1] ?? '');
            if ($data['mainCategory'] == 'Fixed Assets') {
                $data['assteGLAutoID'] = trim($this->post('assteGLAutoID'));
                $data['faCostGLAutoID'] = trim($this->post('COSTGLCODEdes'));
                $data['faACCDEPGLAutoID'] = trim($this->post('ACCDEPGLCODEdes'));
                $data['faDEPGLAutoID'] = trim($this->post('DEPGLCODEdes'));
                $data['faDISPOGLAutoID'] = trim($this->post('DISPOGLCODEdes'));

                $data['costGLAutoID'] = '';
                $data['costSystemGLCode'] = '';
                $data['costGLCode'] = '';
                $data['costDescription'] = '';
                $data['costType'] = '';

                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['stockAdjustmentGLAutoID'] = trim($this->post('stockadjust'));
                $data['stockAdjustmentSystemGLCode'] = trim($stockadjustment[0] ?? '');
                $data['stockAdjustmentGLCode'] = trim($stockadjustment[1] ?? '');
                $data['stockAdjustmentDescription'] = trim($stockadjustment[2] ?? '');
                $data['stockAdjustmentType'] = trim($stockadjustment[3] ?? '');

            } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                $data['assteGLAutoID'] = '';
                $data['assteSystemGLCode'] = '';
                $data['assteGLCode'] = '';
                $data['assteDescription'] = '';
                $data['assteType'] = '';
                $data['revanueGLAutoID'] = trim($this->post('revanueGLAutoID'));
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['costGLAutoID'] = trim($this->post('costGLAutoID'));
                $data['costSystemGLCode'] = trim($cost[0] ?? '');
                $data['costGLCode'] = trim($cost[1] ?? '');
                $data['costDescription'] = trim($cost[2] ?? '');
                $data['costType'] = trim($cost[3] ?? '');

            } elseif ($data['mainCategory'] == 'Inventory') {
                $data['assteGLAutoID'] = trim($this->post('assteGLAutoID'));
                $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                $data['assteGLCode'] = trim($asste[1] ?? '');
                $data['assteDescription'] = trim($asste[2] ?? '');
                $data['assteType'] = trim($asste[3] ?? '');
                $data['revanueGLAutoID'] = trim($this->post('revanueGLAutoID'));
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['stockAdjustmentGLAutoID'] = trim($this->post('stockadjust'));
                $data['stockAdjustmentSystemGLCode'] = trim($stockadjustment[0] ?? '');
                $data['stockAdjustmentGLCode'] = trim($stockadjustment[1] ?? '');
                $data['stockAdjustmentDescription'] = trim($stockadjustment[2] ?? '');
                $data['stockAdjustmentType'] = trim($stockadjustment[3] ?? '');
                $data['costGLAutoID'] = trim($this->post('costGLAutoID'));
                $data['costSystemGLCode'] = trim($cost[0] ?? '');
                $data['costGLCode'] = trim($cost[1] ?? '');
                $data['costDescription'] = trim($cost[2] ?? '');
                $data['costType'] = trim($cost[3] ?? '');

            } else {
                $data['assteGLAutoID'] = trim($this->post('assteGLAutoID'));
                $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                $data['assteGLCode'] = trim($asste[1] ?? '');
                $data['assteDescription'] = trim($asste[2] ?? '');
                $data['assteType'] = trim($asste[3] ?? '');
                $data['revanueGLAutoID'] = trim($this->post('revanueGLAutoID'));
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['costGLAutoID'] = trim($this->post('costGLAutoID'));
                $data['costSystemGLCode'] = trim($cost[0] ?? '');
                $data['costGLCode'] = trim($cost[1] ?? '');
                $data['costDescription'] = trim($cost[2] ?? '');
                $data['costType'] = trim($cost[3] ?? '');
            }

        }

        if (trim($this->post('itemAutoID'))) {
            $itemauto=$this->post('itemAutoID');
            $barcode= $this->post('barcode');
            $barcodeexist=$this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' AND itemAutoID != '$itemauto' AND deletedYN = 0")->row_array();
            if($barcodeexist && !empty($barcode)){
                $this->session->set_flashdata('e', 'Barcode is already assigned.');
            }
            else
            {
                $itemAutoID=trim($this->post('itemAutoID'));
                $barcode = trim($this->post('barcode'));
                $bar=$this->db->query("SELECT * FROM `srp_erp_itemmaster` WHERE itemAutoID=$itemAutoID")->row_array();
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $bar['itemSystemCode'];
                }

                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->update('srp_erp_itemmaster', $data);

                if(($ApprovalforItemMaster== 0 || $ApprovalforItemMaster == NULL)){

                    $this->load->library('Approvals');
                   // $this->db->select('itemAutoID, itemSystemCode,modifiedDateTime');
                   // $this->db->where('itemAutoID', $itemAutoID);
                   // $this->db->from('srp_erp_itemmaster');
                    //$grv_data = $this->db->get()->row_array();
                    $approvals_status = $this->approvals->auto_approve($itemAutoID, 'srp_erp_itemmaster','itemAutoID', 'INV',$bar['itemSystemCode'],$this->common_data['current_date']);


                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
                }
                $this->db->trans_complete();
                $last_id = $this->post('itemAutoID');
                if ($this->db->trans_status() === FALSE) {
                   // $this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    //$this->lib_log->log_event('Item','Error','Item : ' .$data['itemSystemCode'].' - '. $data['itemName'] . ' Update Failed '.$this->db->_error_message(),'Item');
                    return array('e','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Update Failed ' . $this->db->_error_message());

                    /*return array('status' => false);*/
                } else {

                    //update to ecommerce system
                    // if($isSellThis == 1){
                    //     $this->Erp_data_sync_model->create_update_company_item_sync_record($last_id,'update');
                    // }
                    //////

                    update_warehouseitems($last_id,$data['barcode'],$data['isActive'],$data['companyLocalSellingPrice']);
                   // $this->session->set_flashdata('s', 'Item : ' . $data['itemName'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    //$this->lib_log->log_event('Item','Success','Item : ' . $data['companyCode'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Item');
                   // return array('status' => true, 'last_id' => $this->input->post('itemAutoID'),'barcode'=>$data['barcode']);
                    return array('s','Item : ' . $data['itemName'] . ' Updated Successfully.',$last_id,$data['barcode']);
                }
            }

        } else {
            $barcode= $this->post('barcode');
            $barcodeexist=$this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' AND deletedYN = 0")->row_array();
            if($barcodeexist && !empty($barcode)){
                $this->session->set_flashdata('e', 'Barcode is already exist.');
            }else
            {
                $uom = explode('|', trim($this->post('uom')));
                $this->load->library('sequence');
                // $this->db->select('codePrefix');
                // $this->db->where('itemCategoryID', $this->input->post('mainCategoryID'));
                // $code = $this->db->get('srp_erp_itemcategory')->row_array();
                $data['isActive'] = $isactive;
                $data['itemImage'] = 'no-image.png';
                $data['defaultUnitOfMeasureID'] = trim($this->post('defaultUnitOfMeasureID'));
                $data['defaultUnitOfMeasure'] = trim($uom[0] ?? '');
                $data['mainCategoryID'] = trim($this->post('mainCategoryID'));
                $data['mainCategory'] = trim($mainCategory[1] ?? '');
                $data['financeCategory'] = $this->finance_category($data['mainCategoryID']);
                $data['assteGLAutoID'] = trim($this->post('assteGLAutoID'));
                $data['faCostGLAutoID'] = trim($this->post('COSTGLCODEdes'));
                $data['faACCDEPGLAutoID'] = trim($this->post('ACCDEPGLCODEdes'));
                $data['faDEPGLAutoID'] = trim($this->post('DEPGLCODEdes'));
                $data['faDISPOGLAutoID'] = trim($this->post('DISPOGLCODEdes'));

                if ($data['mainCategory'] == 'Fixed Assets') {
                    $data['assteGLAutoID'] = trim($this->post('assteGLAutoID'));
                    $data['faCostGLAutoID'] = trim($this->post('COSTGLCODEdes'));
                    $data['faACCDEPGLAutoID'] = trim($this->post('ACCDEPGLCODEdes'));
                    $data['faDEPGLAutoID'] = trim($this->post('DEPGLCODEdes'));
                    $data['faDISPOGLAutoID'] = trim($this->post('DISPOGLCODEdes'));

                    $data['costGLAutoID'] = '';
                    $data['costSystemGLCode'] = '';
                    $data['costGLCode'] = '';
                    $data['costDescription'] = '';
                    $data['costType'] = '';
                } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                    $data['assteGLAutoID'] = '';
                    $data['assteSystemGLCode'] = '';
                    $data['assteGLCode'] = '';
                    $data['assteDescription'] = '';
                    $data['assteType'] = '';
                    $data['revanueGLAutoID'] = trim($this->post('revanueGLAutoID'));
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                        $data['revanueGLCode'] = trim($revanue[1] ?? '');
                        $data['revanueDescription'] = trim($revanue[2] ?? '');
                        $data['revanueType'] = trim($revanue[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($this->post('costGLAutoID'));
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');
                }

                else {
                    $data['assteGLAutoID'] = trim($this->post('assteGLAutoID'));
                    $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                    $data['assteGLCode'] = trim($asste[1] ?? '');
                    $data['assteDescription'] = trim($asste[2] ?? '');
                    $data['assteType'] = trim($asste[3] ?? '');
                    $data['revanueGLAutoID'] = trim($this->post('revanueGLAutoID'));
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                        $data['revanueGLCode'] = trim($revanue[1] ?? '');
                        $data['revanueDescription'] = trim($revanue[2] ?? '');
                        $data['revanueType'] = trim($revanue[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($this->post('costGLAutoID'));
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');
                }
                $data['companyLocalWacAmount'] = 0.00;
                $data['companyReportingWacAmount'] = 0.00;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['itemSystemCode'] = $this->sequence->sequence_generator(trim($mainCategory[0] ?? ''));
//check if itemSystemCode already exist
                $this->db->select('itemSystemCode');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemSystemCode', $data['itemSystemCode']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $codeExist = $this->db->get()->row_array();
                if(!empty($codeExist)){
                    //$this->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo-1)  WHERE documentID='{$mainCategory[0]}' AND companyID = '{$company_id}'");
                    $this->session->set_flashdata('w', 'Item System Code : ' . $codeExist['itemSystemCode'] . ' Already Exist ');
                    $this->db->trans_rollback();

                    return array('status' => false);

                }

                $barcode = trim($this->post('barcode'));
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $data['itemSystemCode'];
                }
                $this->db->insert('srp_erp_itemmaster', $data);
                $last_id = $this->db->insert_id();
                
                if(($ApprovalforItemMaster== 0 || $ApprovalforItemMaster == NULL)){

                    $this->load->library('Approvals');
                    //$this->db->select('itemAutoID, itemSystemCode,modifiedDateTime');
                    //$this->db->where('itemAutoID', $last_id);
                    //$this->db->from('srp_erp_itemmaster');
                    //$grv_data = $this->db->get()->row_array();
                    $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_itemmaster','itemAutoID', 'INV',$data['itemSystemCode'],$this->common_data['current_date']);

                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    //$this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    //return array('status' => false);
                    return array('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                } else {
                    //$this->session->set_flashdata('s', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.');
                    $this->db->trans_commit();

                    //update to ecommerce system
                    // if($isSellThis == 1){
                    //     $this->Erp_data_sync_model->create_update_company_item_sync_record($last_id,'create');
                    // }
                    //////

                    if($generatedtype == 'third')
                    {
                        $itemmaster = $this->db->query("SELECT CONCAT(itemDescription,'-',itemSystemCode,'-',partNo,'-',seconeryItemCode) as itemcode,defaultUnitOfMeasureID
                                                            FROM `srp_erp_itemmaster` where companyID  = $company_id AND itemAutoID = $last_id ")->row_array();

                        return array('s','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.',$last_id,$data['barcode'],$itemmaster['itemcode'],$itemmaster['defaultUnitOfMeasureID']);
                    }else
                    {
                        return array('s','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.',$last_id,$data['barcode']);
                    }

                   // return array('status' => true, 'last_id' => $last_id,'barcode'=>$data['barcode']);
                }
            }


        }
    }
    function save_item_post()
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
        // print_r($liabilityAccount);
        // exit();

        //$currency_code = explode('|', trim($this->post('currency_code')));
        $liability = fetch_gl_account_desc(trim($this->post('liabilityAccount')));
        // print_r($this->db->last_query()); 
        // exit();
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
                // echo($result);
                // exit();
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
                // echo($id);
                // exit();
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


        // echo('hello');
        // echo($customerAutoID);
        if (!empty($supplierAutoID)) {
            $this->db->where('supplierAutoID', trim($this->post('supplierAutoID')));
            $this->db->update('srp_erp_suppliermaster', $data_ins_supplier);
            // print_r($this->db->last_query());
            // exit();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                 $this->db->trans_rollback();
                //return array('status' => 'e', 'message' => 'Supplier : ' . $data_ins_supplier['SupplierName'] . ' Update Failed' . $this->db->_error_message());
                $this->response([
                    'success' => FALSE,
                    'message' => 'Supplier : ' . $data_ins_supplier['SupplierName'] . ' Update Failed' . $this->db->_error_message()
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->db->trans_commit();
                //return array('status' => 's', 'message' => 'Supplier Details Updated Successfully');
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
        $supplier = array();
        $supplier = $this->db->query("SELECT srp_erp_suppliermaster.deletedYN as deletedYN,srp_erp_partycategories.categoryDescription as categoryDescription,supplierAutoID,supplierSystemCode,supplierName,secondaryCode,supplierName,supplierAddress1,supplierAddress2,supplierCountry,supplierTelephone,supplierEmail,supplierUrl,supplierFax,isActive,supplierCurrency,supplierEmail,supplierTelephone,supplierCurrencyID,cust.Amount as Amount,ROUND(cust.Amount, 2) as Amount_search,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,masterConfirmedYN,masterApprovedYN
                                        FROM srp_erp_suppliermaster
                                        LEFT JOIN srp_erp_partycategories ON srp_erp_suppliermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID
                                        LEFT JOIN (SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate)*-1 as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = 'SUP' AND subLedgerType=2 GROUP BY partyAutoID) cust ON cust.partyAutoID = srp_erp_suppliermaster.supplierAutoID")->result_array();
       foreach ($supplier as $row) {
                print_r($row['deletedYN']);
                print_r($row['categoryDescription']);
                print_r($row['supplierAutoID']);
                print_r($row['supplierSystemCode']);
                print_r($row['secondaryCode']);
                print_r($row['supplierName']);
                print_r($row['supplierAddress1']);
                print_r($row['supplierAddress2']);
                print_r($row['supplierCountry']);
                print_r($row['supplierTelephone']);
                print_r($row['supplierEmail']);
                print_r($row['supplierUrl']);
                print_r($row['supplierFax']);
                print_r($row['isActive']);
                print_r($row['supplierCurrency']);
                print_r($row['supplierEmail']);
                print_r($row['supplierTelephone']);
                print_r($row['supplierCurrencyID']);
                print_r($row['Amount']);
                print_r($row['Amount_search']);
                print_r($row['partyCurrencyDecimalPlaces']);
                print_r($row['masterConfirmedYN']);
                print_r($row['masterApprovedYN']);
            }
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
