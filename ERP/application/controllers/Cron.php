<?php

class Cron extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->company_info  = '';
    }

    function setting_up_company_connection($company_id = 635){
       
        $CI =& get_instance();

        $this->load->model('Session_model'); 

        $tmpCompanyInfo = $this->db->select('*')
            ->from('srp_erp_company')
            ->where('company_id', $company_id)
            ->get()->row_array();


        $this->company_info = $tmpCompanyInfo;
       
        if (!empty($this->company_info)) {
            
            $this->company_id = $this->company_info['company_id'];
           
            //Connect to the db
            $db_response = $this->setDb();
            
            $company_policy = $this->Session_model->fetch_company_policy($company_id);

          

            $this->common_data['company_data'] = $tmpCompanyInfo;
            $this->common_data['current_pc'] = '';//gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $this->common_data['current_date'] = date('Y-m-d h:i:s');
            $this->common_data['current_user'] = 'Admin';
            $this->common_data['current_user'] = 'Admin';

            $this->common_data['company_policy'] = $company_policy;

            $this->common_data['current_userID'] = 1;
            $this->common_data['user_group'] = 0;
            $this->common_data['emplanglocationid'] = 1;

            return array('type' => 'success', 'error_code' => 200, 'error_message' => 'Connected Successfully');

        } else {
            return array('type' => 'error', 'error_code' => 500, 'error_message' => 'Company ID not found');
        }
    }

    
    function check_cron_connection(){
        echo "\r\n"; 
        print_r('called'); exit;
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

    function process_posting_supplier($company_id = 635){

        // Setting up company connections
        $response = $this->setting_up_company_connection($company_id);

        if(empty($response) || ($response && $response['type'] == 'error')){
            echo "Soemthing went wrong";
        }

        $this->load->model('Erp_data_sync_model'); 
        $this->load->model('Invoice_model');
        $this->load->model('Srm_master_model');  
        $this->load->library('erp_data_sync');
        $this->load->helper('erp_data_sync');
        $this->load->helper('configuration');
        $this->load->helper('receivable');

        //Check for process to running
        $is_posting_record = $this->Erp_data_sync_model->check_process_running();

        if($is_posting_record){

            $posting_id = $is_posting_record['doc_id'];
            $posting_auto_id = $is_posting_record['id'];

            $posting_supplier_detail = $this->Erp_data_sync_model->get_cron_supplier_process_data($posting_id,1);

            if($posting_supplier_detail){

                foreach($posting_supplier_detail as $posting_detail){
                
                    if($posting_detail){
    
                        $posting_supplier_auto_id = $posting_detail['id'];
                        $supplier_id = $posting_detail['store_id'];
        
                        $res = $this->Erp_data_sync_model->update_cron_supplier_process($posting_supplier_auto_id);
    
                    }
        
                }
    
                foreach($posting_supplier_detail as $posting_detail){
                    
                    if($posting_detail){
    
                        $posting_supplier_auto_id = $posting_detail['id'];
                        $supplier_id = $posting_detail['store_id'];
        
                        $supplier_arr = array(array('store_id' => $supplier_id));
        
                        $res = $this->Erp_data_sync_model->process_daily_posting_supplier_list($supplier_arr,$posting_auto_id);
                        
                    }
        
                }

            }else{

                $res = $this->Erp_data_sync_model->set_action_system_posting($posting_id,'status',1);
                $res = $this->Erp_data_sync_model->set_action_system_posting($posting_id,'running_status_vendor',0);

                return TRUE;

            }

        }else {

            return TRUE;

        }

    }

    function process_posting_supplier_3pl($company_id = 635){

        // Setting up company connections
        $response = $this->setting_up_company_connection($company_id);

        if(empty($response) || ($response && $response['type'] == 'error')){
            echo "Soemthing went wrong";
        }

        $this->load->model('Erp_data_sync_model'); 
        $this->load->library('erp_data_sync');
        $this->load->model('Invoice_model'); 
        $this->load->helper('erp_data_sync');
        $this->load->helper('configuration');
        $this->load->helper('receivable');

        //Check for process to running
        $is_posting_record = $this->Erp_data_sync_model->check_process_running();

        

        if($is_posting_record){

            $posting_id = $is_posting_record['doc_id'];
            $posting_auto_id = $is_posting_record['id'];

            $posting_supplier_detail = $this->Erp_data_sync_model->get_cron_supplier_process_data($posting_id,2);

            if($posting_supplier_detail){

                foreach($posting_supplier_detail as $posting_detail){
                
                    if($posting_detail){
    
                        $posting_supplier_auto_id = $posting_detail['id'];
        
                        $res = $this->Erp_data_sync_model->update_cron_supplier_process($posting_supplier_auto_id);
    
                    }
        
                }
    
                foreach($posting_supplier_detail as $posting_detail){
                    
                    if($posting_detail){
    
                        $posting_supplier_auto_id = $posting_detail['id'];
                        $supplier_id = $posting_detail['3pl_company_id'];
        
                        $supplier_arr = array(array('3pl_company_id' => $supplier_id));
        
                        $res = $this->Erp_data_sync_model->process_daily_posting_supplier_3pl_list($supplier_arr,$posting_auto_id);
    
                    }
        
                }

            }else{

                // $res = $this->Erp_data_sync_model->set_action_system_posting($posting_id,'status',1);
                $res = $this->Erp_data_sync_model->set_action_system_posting($posting_id,'running_status_3pl',0);

                return TRUE;
            }
           
        }else {

            return TRUE;

        }

    }

    function customer_master_sync_data($company_id=505){

        $response = $this->setting_up_company_connection($company_id);

        if(empty($response) || ($response && $response['type'] == 'error')){
            echo "Soemthing went wrong";
        }

        $this->load->model('Customer_model');
        $data = $this->Customer_model->customer_sync_responce($company_id);

        foreach ($data as $key => $row) {
            //$row['type'] =="create" &&
            if( $row['callBackUrl'] !=null ){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $row['callBackUrl'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>$this->getBody($row['customerId']),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json', 
                        'Accept: application/json'
                    ),
                ));
    
                $response = curl_exec($curl);
        
                curl_close($curl);

                try{
                    if(json_decode($response)->Table){
                        if(json_decode($response)->Table[0]->status=="success"){
    
                            $this->Customer_model->customer_sync_update($row['id']);
    
                        }
                    }
                } catch(Exception $e){
                    $this->Customer_model->customer_sync_update($row['id'],2);
                }
               

            }

        }

    }

    function getBody($id){

        $data = $this->Customer_model->customer_sync($id);

        $jayParsedAry = [
            
           "Code"=>$data['customerSystemCode'],
           "Name"=>$data['customerName'],
           "ShortName"=>$data['customerName'],
           "CustomerType"=>$data['receivableType'],
           "PaymentMode"=>"Cash",
           "Email"=>$data['customerEmail'],
           "PhoneNo"=>$data['customerTelephone'],
           "Address"=>$data['customerAddress1'],
           "CurrencyId"=>$data['customerCurrencyID'],
           "CurrencyCode"=>$data['customerCurrency'],
           "Active"=>$data['isActive'],
        ];

        return json_encode($jayParsedAry);
    }


    function  get_token(){
        
        $client_id = 'CCB1-PS-21-00000056';
        // $client_secret = 'd2LtPZrwOrYVQCxyrnarjg==';
        $client_secret = 'm3pjmpaAOf2qhzvS3AjOJA==';
        // $url = 'https://uat2-pos.imonitor.center/connect/token';
        $url = 'https://mims.imonitor.center/connect/token';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'grant_type'    => 'client_credentials',
        ]));
       
       
        $response = curl_exec($ch);

        curl_close ($ch);
        $arr =json_decode($response);
        
        return $arr->access_token;

    } 

    public function sync_sales_data($company_id = 505){

        $response = $this->setting_up_company_connection($company_id);

        if(empty($response) || ($response && $response['type'] == 'error')){
            echo "Soemthing went wrong";
        }

        $access_token = $this->get_token();

        if(empty($access_token)){
            echo "Soemthing went wrong";
        }

        $this->load->model('Pos_model');
        $response_data = $this->Pos_model->sync_sales_data($company_id);
        $data = array();

        $invoice_list = $response_data['invoice_list'];
        $data['invoice_list'] = json_encode($invoice_list);
        unset($response_data['invoice_list']);

        $base_arr = array();
        $date = date('Y-m-d H:i:s');
        $data['batch_code'] = $batch_code = strtotime($date).'-CCB1-'.rand(11111,99999).rand(11111,99999);

        if($response_data && count($response_data) > 0){

             //update invoices for taking
            $res = $this->Pos_model->update_pos_invoice(1,$invoice_list);

            $base_arr['AppCode'] = 'POS-02';
            $base_arr['PropertyCode'] = 'CCB1';
            $base_arr['ClientID'] = 'CCB1-PS-21-00000056';
            $base_arr['ClientSecret'] = 'm3pjmpaAOf2qhzvS3AjOJA==';
            $base_arr['POSInterfaceCode'] = 'CCB1-PS-21-00000056';
            $base_arr['BatchCode'] = $batch_code;
            $base_arr['PosSales'] = $response_data;

        }else{
            echo 'No data to sync';
            exit;
        }

        $post_sales_body = json_encode($base_arr,JSON_UNESCAPED_SLASHES);
        $data['sales_data'] = $post_sales_body;
        
        //push data to progolf
        $batch_response_json = $this->push_data_to_progolf($access_token,$post_sales_body);

        //$batch_response_json = '{"batchCode":"1676462424-CCB1-5128034160","returnStatus":"SUCCESS","recordsReceived":1,"recordsImported":0,"errorDetails":"1676462424-CCB1-5128034160 includes 1-ExistingRecord(s).","defectiveRowNos":null}';
        $data['sales_response'] = $batch_response_json;

        $batch_response = json_decode($batch_response_json);
        
        if($batch_response && $batch_response->returnStatus == 'SUCCESS'){

            $data['status'] = $batch_response->returnStatus;
            $pos_res = $this->Pos_model->update_pos_invoice(2,$invoice_list,$batch_code);

        }else{

            $data['status'] = $batch_response->returnStatus;
            $pos_res = $this->Pos_model->update_pos_invoice(3,$invoice_list,$batch_code);

        }

        //update invoice batch records
        $pos_res = $this->Pos_model->sync_data_batch_details($data);

        return True;
        
    }

    function push_data_to_progolf($access_token,$post_body){

            $curl = curl_init();
            
           // $uat = 'https://uat2-pos.imonitor.center/api/possale/importpossaleswithitems';

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://mims.imonitor.center/api/possale/importpossaleswithitems',
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS =>$post_body,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$access_token,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;

    }

    function resend_fail_rfq_details($company_id=505){
        $response = $this->setting_up_company_connection($company_id);
        $this->Srm_master_model->resend_fail_rfq_details();

        return True;

    }

    function get_attandance_data($company_id=673){

        $response = $this->setting_up_company_connection($company_id);

        
        if(empty($response) || ($response && $response['type'] == 'error')){
            echo "Soemthing went wrong";
        }

        $this->load->model('Attendance_model');

        $this->Attendance_model->load_attendance_data_step();

        print_r($response); exit;


    }

    function set_attandance_location($company_id = 673){

        $response = $this->setting_up_company_connection($company_id);

        
        if(empty($response) || ($response && $response['type'] == 'error')){
            echo "Soemthing went wrong";
        }

        $this->load->model('Attendance_model');

        $this->Attendance_model->set_attendance_location();

        print_r($response); exit;


    }
 
    function set_process_general_pos($company_id = 645){

        
        $response = $this->setting_up_company_connection($company_id);

        
        if(empty($response) || ($response && $response['type'] == 'error')){
            echo "Soemthing went wrong";
        }

        $this->load->model('Pos_model');

        $this->Pos_model->set_processed_invoice_list($company_id);

        print_r($response); exit;

    }


}