<?php

/**
 * User: Hasitha
 * Date: 08/18/2022
 * @function sales_post get the sales orders according to there stores
 */

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api_ecommerce extends REST_Controller
{

    private $company_info;
    private $company_id = 0;

    function __construct()
    {
        parent::__construct();
        $this->auth();
        $this->load->model('Api_ecommerce_model'); 
        $this->load->model('Srm_master_model'); 
    }

    //////// configs /////////
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
                if (!empty($this->company_info)) {
                    $this->company_id = $this->company_info['company_id'];
                    $this->setDb();
                    return true;
                } else {
                    echo $this->response(array('type' => 'error', 'error_code' => 500, 'error_message' => 'Company ID not found'), 500);
                }
            }
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

    /**
     * User: Hasitha
     * Date: 08/18/2022
     * @function create supplier for order and customer also created using sent store id
    */

    public function supplier_create_post(){

        $supplier_details = json_decode( file_get_contents('php://input'), true );

        if(empty($supplier_details)){
            return $this->set_response(array('message'=>'Details not found'), REST_Controller::HTTP_NOT_FOUND);
        }

        $order_save_response = $this->Api_ecommerce_model->add_supplier_detail($supplier_details,$this->company_info,'insert');

        if($order_save_response['status'] == 'error'){
            return $this->set_response($order_save_response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }else{

            $customer_detail = $this->Api_ecommerce_model->add_customer_detail($supplier_details,$this->company_info,'insert');

            if($customer_detail['status'] == 'error'){
                return $this->set_response($customer_detail, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->set_response($order_save_response, REST_Controller::HTTP_OK);
        }

    }

    public function supplier_update_post(){

        $supplier_edit_details = json_decode( file_get_contents('php://input'), true );
        
        if(empty($supplier_edit_details)){
            return $this->set_response(array('message'=>'Details not found'), REST_Controller::HTTP_NOT_FOUND);
        }

        $order_save_response = $this->Api_ecommerce_model->update_supplier_detail($supplier_edit_details,$this->company_info,'update');

        if($order_save_response['status'] == 'error'){
            return $this->set_response($order_save_response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }else{
            return $this->set_response($order_save_response, REST_Controller::HTTP_OK);
        }

    }

    public function order_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $init_data = array();
        $err_arr = array();
        $company_id = $this->company_info['company_id'];
        $company_code = $this->company_info['company_code'];
        
        foreach($orders as $value){

            $order_id = $value['ORDER'];
            $store_id = '62198ca28cbfe50c400d';
            $order_code = $company_code.'/ORD/'.$order_id.'/'.rand(11111,99999).rand(11111,99999);
          
            $date =  $this->date_format($value['DATE_TIME']);
            $completed_date = $this->date_format($value['COMPLETED_TIME']);
            
            try{

                $order_ex = $this->Api_ecommerce_model->check_3rdparty_order_already_exists($order_id);
          
                $status = $this->check_for_exceptions($value,'STATUS');
                $store_id = $this->check_for_exceptions($value,'STORE_ID');

                if(empty($store_id)){
                    throw new Exception("Store ID can not be blank.");
                }


                if(isset($value['STATUS']) && $value['STATUS'] == 'COMPLETE'){

                    $int_data[] = [
                        'system_id' => $order_code,
                        'company_name' => $this->check_for_exceptions($value,'COMPANY_NAME'),
                        'service_type' => $this->check_for_exceptions($value,'SERVICE_TYPE'),
                        'store_id' =>  $this->check_for_exceptions($value,'STORE_ID'),
                        'store' => $this->check_for_exceptions($value,'STORE'),
                        'customer_id' => $this->check_for_exceptions($value,'CUSTOMER_ID'),
                        'customer' => $this->check_for_exceptions($value,'CUSTOMER'),//isset($value['Custome']) ? $value['Custome'] : 1,
                        'customer_tel' => $this->check_for_exceptions($value,'CUSTOMER_TEL'),
                        'order' => $this->check_for_exceptions($value,'ORDER'),
                        'zone' => $this->check_for_exceptions($value,'ZONE'),
                        'date_time' =>  $date,
                        'completed_time' => $completed_date,
                        'column1' => $this->check_for_exceptions($value,'COLUMN_1'),
                        'bank_id' => $this->check_for_exceptions($value,'BANK_ID'),
                        'bank_name' => $this->check_for_exceptions($value,'BANK_NAME'),
                        'payment' => $this->check_for_exceptions($value,'PAYMENT'),
                        'cr_dr' => $this->check_for_exceptions($value,'CR_DR'),
                        'status' => $this->check_for_exceptions($value,'STATUS'),
                        'currency' => $this->check_for_exceptions($value,'CURRENCY'),
                        'order_total' => $this->check_for_exceptions($value,'ORDER_TOTAL'),
                        'delivery_fee' => $this->check_for_exceptions($value,'DELIVERY_FEE'),
                        'actual_delivery_fee' => $this->check_for_exceptions($value,'ACTUAL_DELIVERY_FEE'),
                        'municipality_tax' => $this->check_for_exceptions($value,'MUNICIPALITY_TAX'),
                        'municipality_tax_vat' => $this->check_for_exceptions($value,'MUNICIPALITY_TAX_VAT'),
                        'tourism_tax' => $this->check_for_exceptions($value,'TOURISM_TAX'),
                        'tourism_tax_vat' => $this->check_for_exceptions($value,'TOURISM_TAX_VAT'),
                        'vat_on_order' => $this->check_for_exceptions($value,'VAT_ON_ORDER'),
                        'vat_delivery_fee' => $this->check_for_exceptions($value,'VAT_ON_DELIVERY_FEE'),
                        'total_bill' => $this->check_for_exceptions($value,'TOTAL_BILL'),
                        'discount' => $this->check_for_exceptions($value,'DISCOUNTS'),
                        'credit' => $this->check_for_exceptions($value,'CREDITS'),
                        'net_vendor_bill' => $this->check_for_exceptions($value,'NET_VENDOR_BILL'),
                        'net_collection' => $this->check_for_exceptions($value,'NET_COLLECTION'),
                        'adjustment_type' => $this->check_for_exceptions($value,'ADJUSTMENT_TYPE'),
                        'adjustment_reason' => $this->check_for_exceptions($value,'ADJUSTMENT_REASON'),
                        'total_adjustment' => $this->check_for_exceptions($value,'TOTAL_ADJUSTMENT'),
                        'tmdone_adjustment' => $this->check_for_exceptions($value,'TMDONE_ADJUSTMENT'),
                        'vendor_adjustment' => $this->check_for_exceptions($value,'VENDOR_ADJUSTMENT'),
                        'driver_adjustment' => $this->check_for_exceptions($value,'DRIVER_ADJUSTMENT'),
                        'gross_payable' => $this->check_for_exceptions($value,'GROSS_PAYABLE'),
                        'commission_percentage' => $this->check_for_exceptions($value,'COMMSSION'),
                        'fixed_commission' => $this->check_for_exceptions($value,'FIXED_COMMSSION'),
                        'commissionable_income' => $this->check_for_exceptions($value,'COMMISSIONABLE_INCOME'),
                        'tmdone_commission' => $this->check_for_exceptions($value,'TMDONE_COMMSSION'),
                        'vat_tmdone_commission' => $this->check_for_exceptions($value,'VAT_ON_TMDONE_COMMSSION'),
                        'bank_charges' => $this->check_for_exceptions($value,'BANK_CHARGES'),
                        'bank_charges_vat' => $this->check_for_exceptions($value,'BANK_CHARGE_VAT'),
                        'vendor_settlement' => $this->check_for_exceptions($value,'VENDOR_SETTLEMENT'),
                        'card_payment_reference' => isset($value['CARD_PAYMNET_REF']) ? $value['CARD_PAYMNET_REF'] : null,
                        'driver_name' => isset($value['DRIVER_NAME']) ? $value['DRIVER_NAME'] : null,
                        'driver_id' => isset($value['DRIVER_ID']) ? $value['DRIVER_ID'] : null,
                        'points_redeemed' => isset($value['POINTS_REDEEMED']) ? $value['POINTS_REDEEMED'] : null,
                        'cash_collected' => isset($value['CASH_COLLECTED']) ? $value['CASH_COLLECTED'] : null,
                        'credit_card' => isset($value['CREDIT_CARD']) ? $value['CREDIT_CARD'] : null,
                        'tm_credits' => isset($value['TM_CREDITS']) ? $value['TM_CREDITS'] : null,
                        '3pl_company_id' => $this->check_for_similar($value,'3PL_COMPANY_ID'),
                        'tm_done_driver_id' => isset($value['TM_DRIVER_ID']) ? $value['TM_DRIVER_ID'] : null,
                        'delivery_cost' => isset($value['DELIVERY_COST']) ? $value['DELIVERY_COST'] : null,
                        'drop_fee' => isset($value['DROP_FEE']) ? $value['DROP_FEE'] : null,
                        'receivable_balance' => isset($value['RECEIVABLE_BALANCE']) ? $value['RECEIVABLE_BALANCE'] : null,
                        'item_code' => isset($value['ITEM_CODE']) ? $value['ITEM_CODE'] : null,
                        'tablet_fee' => isset($value['TABLET_FEE']) ? $value['TABLET_FEE'] : null,
                        'renewal_fee' => isset($value['RENEWAL_FEE']) ? $value['RENEWAL_FEE'] : null,
                        'registration_fee' => isset($value['REGISTRATION_FEE']) ? $value['REGISTRATION_FEE'] : null,
                        'grouping' => isset($value['GROUPING']) ? $value['GROUPING'] : null,
                        'campaign_fee' => isset($value['CAMPAIGN_FEE']) ? $value['CAMPAIGN_FEE'] : null,
                        'refunds' => isset($value['REFUNDS']) ? $value['REFUNDS'] : null,
                        'other' => isset($value['OTHER']) ? $value['OTHER'] : null,
                        'erp_record_receive_date' => date('Y-m-d H:i:s'),
                        'companyId' => $company_id,
                        'companyCode' => $company_code,
                        'param'=>json_encode($value)
                    ];
                
                }

            } catch (\Exception $e){
                $err_arr[] = $value['ORDER'];
                $data['status'] = 'error';
                $data['message'] = $e->getMessage();
    
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        }

        try{
            
            $this->db->insert_batch('srp_erp_ecommerce_sales_clientdata', $int_data);

            $this->db->insert_batch('srp_erp_ecommerce_sales_clientdata_history', $int_data);
        
            $data['status'] = 'success';
            $data['System_Id'] = $order_code;
            $data['message'] = 'Successfully added to the system';

            return $this->set_response($data, REST_Controller::HTTP_OK);

        }catch (\Exception $e){

            $data['status'] = 'error';
            $data['message'] = 'Something went wrong';

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
        

    }


    public function getorder_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();

        $system_code = $orders['System_Id'];

        $order_detail = $this->Api_ecommerce_model->get_order_detail_by_systemcode($system_code);

        if($order_detail){

            unset($order_detail['param']);
            unset($order_detail['id']);
            unset($order_detail['customer_auto_id']);
            unset($order_detail['3pl_vendor_auto_id']);
            unset($order_detail['3pl_customer_auto_id']);
            unset($order_detail['direct_receipt_auto_id']);
            unset($order_detail['jv_auto_id']);
            unset($order_detail['invoice_auto_id']);

            return $this->set_response($order_detail, REST_Controller::HTTP_OK);
        }else{
            $data['status'] = 'error';
            $data['message'] = "No order exists, from this ID $system_code";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function order_update_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();

        $system_id = $orders['SYSTEM_ID'];

        if(empty($system_id)){
            $data['status'] = 'error';
            $data['message'] = "System id not present. Please attach the Id";

            return $this->set_response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        try {
            $response = $this->Api_ecommerce_model->update_order_detail($orders);

            if($response && $response['status'] == 'success'){
                $data['status'] = 'success';
                $data['System_Id'] =  $system_id;
                $data['message'] = "Successfully Updated.";
                return $this->set_response($data, REST_Controller::HTTP_OK);
            }else{
                $data['status'] = 'error';
                $data['System_Id'] =  $system_id;
                $data['message'] = isset($response['message']) ? $response['message'] : "Something went wrong";
    
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
        
 

    }

    public function date_format($date){

        $date_arr = explode(' ',$date);
        $date_time = $date_arr[1].' '.$date_arr[2];
        $time_in_24_hour_format  = date("H:i:s", strtotime("$date_time"));
        $final_date_time = date('Y-m-d' , strtotime($date_arr[0])).' '.$time_in_24_hour_format;

        return $final_date_time;

    }

    public function check_for_exceptions($arr , $index){

        if(isset($arr[$index])){
            return $arr[$index];
        }else{
            throw new Exception("Missing $index in values list");
        }

    }
    
    public function check_for_similar($arr,$index){

        // isset($value['3PL_COMPANY_ID']) ? $value['3PL_COMPANY_ID'] : null;

        if(isset($arr[$index])){
            return $arr[$index];
        }else{
            if($index == '3PL_COMPANY_ID'){
                if(isset($arr['PL_COMPANY_ID'])){
                    return $arr['PL_COMPANY_ID'];
                }
            }

            return null;
        }

    }

    public function order_settlement_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $init_data = array();
        $err_arr = array();
        $company_code = $this->company_info['company_code'];
        
        foreach($orders as $value){

            $order_id = $value['ORDER'];
            $order_code = $company_code.'/ORD/'.$order_id.'/'.rand(11111,99999).rand(11111,99999);
          
            $date =  $this->date_format($value['DATE_TIME']);
            $completed_date = $this->date_format($value['COMPLETED_TIME']);
            
            try{

                $order_ex = $this->Api_ecommerce_model->check_3rdparty_order_already_exists($order_id);
          
                $status = $this->check_for_exceptions($value,'STATUS');
                $store_id = $this->check_for_exceptions($value,'STORE_ID');

                if(empty($store_id)){
                    throw new Exception("Store ID can not be blank.");
                }


                if(isset($value['STATUS']) && $value['STATUS'] == 'COMPLETE'){

                    $int_data[] = [
                        'system_id' => $order_code,
                        'company_name' => $this->check_for_exceptions($value,'COMPANY_NAME'),
                        'service_type' => $this->check_for_exceptions($value,'SERVICE_TYPE'),
                        'store_id' =>  $this->check_for_exceptions($value,'STORE_ID'),
                        'store' => $this->check_for_exceptions($value,'STORE'),
                        'order' => $this->check_for_exceptions($value,'ORDER'),
                        'payment' => 'CASH',
                        'date_time' =>  $date,
                        'completed_time' => $completed_date,
                        'status' => $this->check_for_exceptions($value,'STATUS'),
                        'currency' => $this->check_for_exceptions($value,'CURRENCY'),
                        '3pl_company_id' => isset($value['3PL_COMPANY_ID']) ? $value['3PL_COMPANY_ID'] : null,
                        'item_code' => isset($value['ITEM_CODE']) ? $value['ITEM_CODE'] : null,
                        'tablet_fee' => (isset($value['TABLET_FEE']) && ($value['TABLET_FEE'] > 0)) ? $value['TABLET_FEE'] : null,
                        'tablet_fee_settlement' => (isset($value['TABLET_FEE']) && ($value['TABLET_FEE'] < 0 )) ? abs($value['TABLET_FEE']) : null,
                        'renewal_fee' => (isset($value['RENEWAL_FEE']) && ($value['RENEWAL_FEE'] > 0)) ? $value['RENEWAL_FEE'] : null,
                        'renewal_fee_settlement' => (isset($value['RENEWAL_FEE']) && ($value['RENEWAL_FEE'] < 0 )) ? abs($value['RENEWAL_FEE']) : null,
                        'registration_fee' => (isset($value['REGISTRATION_FEE']) && ($value['REGISTRATION_FEE'] > 0)) ? $value['REGISTRATION_FEE'] : null,
                        'registration_fee_settlement' => (isset($value['REGISTRATION_FEE']) && ($value['REGISTRATION_FEE'] < 0 )) ? abs($value['REGISTRATION_FEE']) : null,
                        'grouping' => isset($value['GROUPING']) ? $value['GROUPING'] : null,
                        'campaign_fee' => (isset($value['CAMPAIGN_FEE']) && ($value['CAMPAIGN_FEE'] > 0)) ? $value['CAMPAIGN_FEE'] : null,
                        'campaign_fee_settlement' => (isset($value['CAMPAIGN_FEE']) && ($value['CAMPAIGN_FEE'] < 0 )) ? abs($value['CAMPAIGN_FEE']) : null,
                        'refunds' => isset($value['REFUNDS']) ? $value['REFUNDS'] : null,
                        'other' => isset($value['OTHER']) ? $value['OTHER'] : null,
                        'erp_record_receive_date' => date('Y-m-d H:i:s'),
                        'param'=>json_encode($value)
                    ];

                }          

            } catch (\Exception $e){
                $err_arr[] = $value['ORDER'];
                $data['status'] = 'error';
                $data['message'] = $e->getMessage();
    
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        }

        try{
            
            $this->db->insert_batch('srp_erp_ecommerce_sales_clientdata', $int_data);
        
            $data['status'] = 'success';
            $data['System_Id'] = $order_code;
            $data['message'] = 'Settlement successfully added to the system';

            return $this->set_response($data, REST_Controller::HTTP_OK);

        }catch (\Exception $e){

            $data['status'] = 'error';
            $data['message'] = 'Something went wrong';

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function order_settlement_update_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();

        $system_id = $orders['SYSTEM_ID'];

        if(empty($system_id)){
            $data['status'] = 'error';
            $data['message'] = "System id not present. Please attach the Id";

            return $this->set_response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        try {
            $response = $this->Api_ecommerce_model->update_order_settlement_detail($orders);

            if($response && $response['status'] == 'success'){
                $data['status'] = 'success';
                $data['System_Id'] =  $system_id;
                $data['message'] = "Successfully Updated.";
                return $this->set_response($data, REST_Controller::HTTP_OK);
            }else{
                $data['status'] = 'error';
                $data['System_Id'] =  $system_id;
                $data['message'] = isset($response['message']) ? $response['message'] : "Something went wrong";
    
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    //vendor poratal apis
    // 1
    public function submit_supplier_rfq_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            $order_detail = $this->Srm_master_model->update_vendor_submit_rfq($orders['results']['dataSub'],$orders['results']['dataCat'],$orders['results']['dataMaster']);

            if($order_detail==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    

    public function rfq_change_request_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            $date =date("Y/m/d h:i:s");

            $format_date=$this->date_format( $date);

            $order_detail = $this->Srm_master_model->update_supplier_rfq_change_request($orders['dataMaster'],$format_date);


            if($order_detail==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    //2

    public function save_vendor_company_request_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            // $date =date("Y/m/d h:i:s");

            // $format_date=$this->date_format( $date);

            $order_detail = $this->Srm_master_model->save_vendor_company_request_details($orders['results']['dataMaster'],$orders['results']['dataSub'],$orders['results']['dataCat'],$orders['results']['dataOther'],$orders['results']['type']);


            if($order_detail==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function save_vendor_company_request_reject_document_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            $date =date("Y/m/d h:i:s");

            $format_date=$this->date_format( $date);

            $order_detail = $this->Srm_master_model->save_vendor_company_request_reject_document($orders['dataMaster'],$format_date);


            if($order_detail==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function find_company_details_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            $date =date("Y/m/d h:i:s");

            $format_date=$this->date_format( $date);

            $company_detail = $this->Srm_master_model->find_company_details_for_vendor_portal($orders['dataMaster']);


            if($company_detail){
                $data['status'] = true;
                $data['result']=$company_detail;
                $data['message'] = "data find successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['result']="";
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    //3

    public function re_upload_vendor_reject_document_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            // $date =date("Y/m/d h:i:s");

            // $format_date=$this->date_format( $date);

            $order_detail = $this->Srm_master_model->re_upload_vendor_reject_document($orders['results']['dataMaster']);

            if($order_detail==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    //4
    public function save_supplier_chat_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            // $date =date("Y/m/d h:i:s");

            // $format_date=$this->date_format( $date);

            $order_detail = $this->Srm_master_model->save_supplier_chat($orders['results']['dataMaster']);

            if($order_detail==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    //5

    public function save_supplier_line_refer_back_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            // $date =date("Y/m/d h:i:s");

            // $format_date=$this->date_format( $date);

            $order_detail = $this->Srm_master_model->save_supplier_line_refer_back($orders['results']['dataMaster']);

            if($order_detail==true){
                $data['status'] = true;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['message'] = "Try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function save_supplier_grv_post(){

        $orders = json_decode( file_get_contents('php://input'), true );

        $data = array();
        $err_arr = array();
        
        try {

            $order_detail = $this->Srm_master_model->save_supplier_grv($orders['results']['dataMaster'],$orders['results']['dataSub']);

            if($order_detail['status']==true){
                $data['status'] = true;
                $data['result'] = $order_detail;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['result'] = $order_detail;
                $data['message'] = "try Again";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function save_supplier_invoice_post(){
            $this->load->library('jwt');
         $tokenKey = $_SERVER['HTTP_SME_API_KEY'];
         $output['token'] = $this->jwt->decode($tokenKey, "token");
        $userID = $output['token']->id;
        $companyID = $output['token']->Erp_companyID;
        $companycode = $output['token']->company_code;
        $this->load->database('default');

        $orders = json_decode( file_get_contents('php://input'), true );


        $data = array();
        $err_arr = array();

        //print_r($this->common_data['current_user']);exit;
        
        try {

            // $company_curr = $this->db->select('*')
            // ->from('srp_erp_company')->where('company_id', $companyID)->get()->row_array();

            // $company_info = (object)array_merge((array)$output['token'], $company_curr);

            $order_detail = $this->Srm_master_model->save_supplier_invoice($orders['results']['dataMaster'],$orders['results']['dataSub']);

            if($order_detail['status']==true){
                $data['status'] = true;
                $data['result'] = $order_detail;
                $data['message'] = "update successfully";
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data['status'] = false;
                $data['result'] = $order_detail;
                $data['message'] = 'try';
                return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }    

        }catch(Exception $e){
            $data['status'] = 'error';
            $data['message'] = "Something went wrong";

            return $this->set_response($data, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    }


}