<?php

class DataSync extends ERP_Controller
{
 
    function __construct()
    {
        parent::__construct();
        $this->load->model('Erp_data_sync_model'); 
        $this->load->library('erp_data_sync');
        $this->load->helper('erp_data_sync');
        $this->load->helper('receivable');
    }

    function process_data_sync(){
        //process data sync records
    
        $item_list = $this->Erp_data_sync_model->get_items_to_sync();

        if($item_list){
            $response = $this->erp_data_sync->send_data_to_ecommerce($item_list);
        }

    }

    function encrypt_d(){
        $id = '721d188f264213a73fc01248659d7b67298a71d7a66d4c31659aa25b01770f39db216ff7838ad95b7c0e4c87690fc505ecbf3815617ddf920718290025afda39BDYbz1fDB2cNQ5lLzywLO+A/V01ieVny9mXcnWaK3G0=';
        $this->encryption->initialize(array('driver' => 'mcrypt'));
    
        $res = $this->encryption->decrypt($id);

        print_r($res); exit;
    }

    //Sales Mapping Area
    function save_sales_mapping(){

        $this->form_validation->set_rules('client_header', 'Client Header', 'trim|required');
        // $this->form_validation->set_rules('invoice_type', 'Invoice Type', 'trim|required');
        $this->form_validation->set_rules('transaction_type', 'Transaction Type', 'trim|required');
    
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Erp_data_sync_model->set_ecommerce_mapping());
        }

    }

    //Get Mapping view according to id
    function edit_mapping_view(){
        $mapping_id = $this->input->post('mapping_id');
        $edit_temp = $this->input->post('edit');

        $erp_data = $this->Erp_data_sync_model->get_mapping_data_record($mapping_id);
        $data = $erp_data;

        if($edit_temp == 'new'){
            $html = $this->load->view('system/ecommerce/ajax/sales_client_data_edit_ui',$data);
        }else{
            $html = $this->load->view('system/ecommerce/ajax/sales_client_data_edit',$data);
        }
       

        return $html;
    }

    function delete_mapping(){

        $this->form_validation->set_rules('mapping_id', 'Mapping ID', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Erp_data_sync_model->destroy_ecommerce_mapping());
        }

    }

    function fetch_sales_mapping(){

        $this->datatables->select('srp_erp_ecommerce_sales_clientmapping.id as ID,srp_erp_segment.segmentCode as segmentCode,
                            srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_ecommerce_sales_clientmapping.erp_cr_dr as erp_cr_dr,
                            srp_erp_ecommerce_sales_clientmapping.client_sales_header,srp_erp_ecommerce_sales_clientmapping.erp_column_name,srp_erp_ecommerce_sales_clientmapping.erp_description as description,
                            srp_erp_ecommerce_sales_clientmapping.mapping_type as mapping_type,srp_erp_ecommerce_sales_clientmapping.control_acc as control_acc,srp_erp_ecommerce_sales_clientmapping.invoice_type as invoice_type')
                ->from('srp_erp_ecommerce_sales_clientmapping')
                ->join('srp_erp_segment', 'srp_erp_ecommerce_sales_clientmapping.erp_segment_id = srp_erp_segment.segmentID', 'left')
                ->join('srp_erp_chartofaccounts', 'srp_erp_ecommerce_sales_clientmapping.erp_gl_code = srp_erp_chartofaccounts.GLAutoID', 'left')
                ->where('srp_erp_ecommerce_sales_clientmapping.posting_id',$this->input->post('posting_id'));
        
        $this->datatables->edit_column('erp_c', '$1', 'erp_credit_debit(erp_cr_dr)');
        $this->datatables->edit_column('mapping_type', '$1', 'edit_datatable_mapping_type(mapping_type,invoice_type)');
        $this->datatables->edit_column('control_acc', '$1', 'get_control_acc(control_acc)');
        $this->datatables->add_column('delete', '<a onclick="edit_mapping($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-cog" style="color:green"></span></a>
        &nbsp &nbsp  <a onclick="delete_mapping($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a> ','ID');

        echo $this->datatables->generate();

    }

    function fetch_client_data(){
        
        $service_type = $this->input->post('service_type');
        $storeType = $this->input->post('storeType');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $storeType_arr = array();

        if($storeType){
            $storeType_arr = explode(',',$storeType);
        }

        $datefrom = date('Y-m-d',strtotime($datefrom)).' 00:00:00';
        $dateto = date('Y-m-d',strtotime($dateto)).' 23:59:59';;

        if(empty($storeType_arr)){
            $this->datatables->select('*,srp_erp_ecommerce_sales_clientdata.id as ID,srp_erp_ecommerce_sales_clientdata.erp_record_status as ERPstatus
                ,srp_erp_ecommerce_sales_clientdata.invoice_auto_id as invoiceID_Erp, srp_erp_ecommerce_sales_clientdata.total_bill  as adjusted_vendor')
                ->where('service_type',$service_type)
                ->where('date_time >=', $datefrom)
                ->where('date_time <=', $dateto)
                ->from('srp_erp_ecommerce_sales_clientdata');
        }else{
            $this->datatables->select('*,srp_erp_ecommerce_sales_clientdata.id as ID,srp_erp_ecommerce_sales_clientdata.erp_record_status as ERPstatus
                ,srp_erp_ecommerce_sales_clientdata.invoice_auto_id as invoiceID_Erp,srp_erp_ecommerce_sales_clientdata.total_bill  as adjusted_vendor')
                ->where('service_type',$service_type)
                ->where_in('store_id',$storeType_arr)
                ->where('date_time >=', $datefrom)
                ->where('date_time <=', $dateto)
                ->from('srp_erp_ecommerce_sales_clientdata');
        }
       

        $this->datatables->add_column('view', '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye" style="color:rgb(209, 91, 71);"></span>');
        // $this->datatables->add_column('de_model','<a onclick="double_entry_view($1)" class="btn btn-danger">Double Entry</a>','ID');
        $this->datatables->add_column('de_model','$1','erp_client_sales_de_manage(ERPstatus,invoiceID_Erp,ID)');
        $this->datatables->add_column('edit_ui','$1', 'erp_client_sales_actions(ERPstatus,invoiceID_Erp)');
        $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');

        echo $this->datatables->generate();
        
    }

    function fetch_processed_data(){

        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
        $processedYN = $this->input->post('processedYN');
        $posting_auto_id = $this->input->post('doc_id');
        $storeType = $this->input->post('storeType');

        $storeType_arr = array();

        if($storeType){
            $storeType_arr = explode(',',$storeType);
        }

        if($dateFrom && $dateTo){

            if(empty($storeType_arr)){

                $this->datatables->select('*,srp_erp_ecommerce_sales_clientdata.id as ID,srp_erp_ecommerce_sales_clientdata.erp_record_status as ERPstatus
                    ,srp_erp_ecommerce_sales_clientdata.invoice_auto_id as invoiceID_Erp')
                    ->where('posting_id',$posting_auto_id)
                    ->where('erp_record_status',$processedYN)
                    ->from('srp_erp_ecommerce_sales_clientdata');
                $this->datatables->add_column('view', '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye" style="color:rgb(209, 91, 71);"></span>');
                $this->datatables->add_column('de_model','<a onclick="double_entry_view($1)" class="btn btn-danger"><i class="fa fa-eye"></i> Entry</a>','ID');
                $this->datatables->add_column('edit_ui','$1', 'erp_client_sales_actions(ERPstatus,invoiceID_Erp)');
                $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');
        
                echo $this->datatables->generate();


            } else{

                $this->datatables->select('*,srp_erp_ecommerce_sales_clientdata.id as ID,srp_erp_ecommerce_sales_clientdata.erp_record_status as ERPstatus
                    ,srp_erp_ecommerce_sales_clientdata.invoice_auto_id as invoiceID_Erp')
                    ->where('posting_id',$posting_auto_id)
                    ->where('erp_record_status',$processedYN)
                    ->where_in('store_id',$storeType_arr)
                    ->from('srp_erp_ecommerce_sales_clientdata');
                $this->datatables->add_column('view', '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye" style="color:rgb(209, 91, 71);"></span>');
                $this->datatables->add_column('de_model','<a onclick="double_entry_view($1)" class="btn btn-danger"><i class="fa fa-eye"></i> Entry</a>','ID');
                $this->datatables->add_column('edit_ui','$1', 'erp_client_sales_actions(ERPstatus,invoiceID_Erp)');
                $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');
        
                echo $this->datatables->generate();


            }

          

        }

    }

    function fetch_client_data_posting(){

        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
        $service_type = $this->input->post('service_type');
        $mode_collection = $this->input->post('mode_collection');
        $mode_collection_arr = array();

        $service_type_by_name = srp_posting_service_type_get($service_type);

        if($mode_collection != 1){
            $mode_collection_arr[] = srp_posting_mode_collection_get($mode_collection);
        } else{
            $mode_collection_arr[] = 'CASH';
            $mode_collection_arr[] = 'CARD';
        }

        $this->datatables->select('*,srp_erp_ecommerce_sales_clientdata.id as ID,srp_erp_ecommerce_sales_clientdata.erp_record_status as ERPstatus
        ,srp_erp_ecommerce_sales_clientdata.invoice_auto_id as invoiceID_Erp')
            ->where('date_time >=', $dateFrom.' 00:00:00')
            ->where('date_time <=', $dateTo.' 23:59:59')
            ->where('service_type',$service_type_by_name)
            ->where_in('payment',$mode_collection_arr)
            ->where('erp_record_status',0)
            ->from('srp_erp_ecommerce_sales_clientdata');

        $this->datatables->add_column('view', '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-eye" style="color:rgb(209, 91, 71);"></span>');
        $this->datatables->add_column('de_model','<a onclick="double_entry_view($1)" class="btn btn-danger">Double Entry</a>','ID');
        $this->datatables->add_column('edit_ui','$1', 'erp_client_sales_actions(ERPstatus,invoiceID_Erp)');
        $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');

        echo $this->datatables->generate();
    }

    function load_double_entry(){

        $data = array();
        $clent_sales_id = $this->input->post('client_sales_id');

        $data['clent_order_detail'] = $this->Erp_data_sync_model->get_sales_client_record();
        $data['clent_order_cr_dr'] = $this->Erp_data_sync_model->get_sales_client_credit_debit();
        $data['clent_sales_id'] = $clent_sales_id;

        $view = $this->load->view('system/ecommerce/ajax/sales_double_entry_view',$data);

        return $view;
    }

    function load_double_entry_log_summary(){

        $data = array();
        $clent_sales_id = $this->input->post('client_sales_id');

        $order_details = $this->Erp_data_sync_model->get_sales_client_record();

        if($order_details){
           $order_id = $order_details['order'];
           $logs = $this->Erp_data_sync_model->get_order_process_log($order_id,$clent_sales_id);
           $data['processed_log'] =  $logs;
        }

        $data['clent_order_detail'] = $this->Erp_data_sync_model->get_sales_client_record();
        // $data['clent_order_cr_dr'] = $this->Erp_data_sync_model->get_sales_client_credit_debit();
        // $data['clent_sales_id'] = $clent_sales_id;

        $view = $this->load->view('system/ecommerce/ajax/sales_log_view',$data);

        return $view;
    }

    function load_order_edit_form(){
        
        $data = array();
        $clent_sales_id = $this->input->post('order_id');

        $data['clent_order_detail'] =  $this->Erp_data_sync_model->get_sales_client_record($clent_sales_id);
        $data['order_id'] = $clent_sales_id;

        $view = $this->load->view('system/ecommerce/ajax/order_edit',$data);

        return $view;

    }

    function load_double_entry_summary(){

        $data = array();
        $data_cr_dr = array('credit'=>0,'debit'=>0);
        $data_cr_dr_customer = array('credit'=>0,'debit'=>0);
        $data_cr_dr_3pl_vendor = array('credit'=>0,'debit'=>0);
        $data_cr_dr_3pl_customer = array('credit'=>0,'debit'=>0);
        $data_cr_dr_direct_receipt = array('credit'=>0,'debit'=>0);
        $data_cr_dr_jv = array('credit'=>0,'debit'=>0);
        $data_cr_dr_debit_note = array('credit'=>0,'debit'=>0);

        $data['clent_order_detail'] = $this->Erp_data_sync_model->get_sales_client_record();
        $client_credit_debit_vendor = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary(null,1,null,1);
        $client_credit_debit_customer = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary(null,2);
        $client_credit_debit_3pl_vendor = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary(null,3);
        $client_credit_debit_3pl_customer = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary(null,4);
        $client_credit_debit_direct_receipt = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary(null,5);
        $client_credit_debit_jv = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary(null,6);
        $client_credit_debit_note = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary(null,1,null,9);

        if(isset($client_credit_debit_vendor['data'])){
            foreach($client_credit_debit_vendor['data'] as $value){
                if($value['final_value'] < 0){
                    $data_cr_dr['credit'] += abs($value['final_value']);
                }else{
                    $data_cr_dr['debit'] += abs($value['final_value']);
                }
            }
        }
       
        if(isset($client_credit_debit_customer['data'])){
            foreach($client_credit_debit_customer['data'] as $value_customer){
                if($value_customer['final_value'] < 0){
                    $data_cr_dr_customer['credit'] += abs($value_customer['final_value']);
                }else{
                    $data_cr_dr_customer['debit'] += abs($value_customer['final_value']);
                }
            }
        }

        if(isset($client_credit_debit_3pl_vendor['data'])){
            foreach($client_credit_debit_3pl_vendor['data'] as $value_3pl_vendor){
                if($value_3pl_vendor['final_value'] < 0){
                    $data_cr_dr_3pl_vendor['credit'] += abs($value_3pl_vendor['final_value']);
                }else{
                    $data_cr_dr_3pl_vendor['debit'] += abs($value_3pl_vendor['final_value']);
                }
            }
        }

        if(isset($client_credit_debit_3pl_customer['data'])){
            foreach($client_credit_debit_3pl_customer['data'] as $value_3pl_customer){
                if($value_3pl_customer['final_value'] < 0){
                    $data_cr_dr_3pl_customer['credit'] += abs($value_3pl_customer['final_value']);
                }else{
                    $data_cr_dr_3pl_customer['debit'] += abs($value_3pl_customer['final_value']);
                }
            }
        }

        if(isset($client_credit_debit_direct_receipt['data'])){
            foreach($client_credit_debit_direct_receipt['data'] as $value_dr){
                if($value_dr['final_value'] < 0){
                    $data_cr_dr_direct_receipt['credit'] += abs($value_dr['final_value']);
                }else{
                    $data_cr_dr_direct_receipt['debit'] += abs($value_dr['final_value']);
                }
            }
        }

        if(isset($client_credit_debit_jv['data'])){
            foreach($client_credit_debit_jv['data'] as $value_jv){
                if($value_jv['final_value'] < 0){
                    $data_cr_dr_jv['credit'] += abs($value_jv['final_value']);
                }else{
                    $data_cr_dr_jv['debit'] += abs($value_jv['final_value']);
                }
            }
        }

        if(isset($client_credit_debit_note['data'])){
            foreach($client_credit_debit_note['data'] as $value_debit_note){
                if($value_debit_note['final_value'] < 0){
                    $data_cr_dr_debit_note['credit'] += abs($value_debit_note['final_value']);
                }else{
                    $data_cr_dr_debit_note['debit'] += abs($value_debit_note['final_value']);
                }
            }
        }

        $data['clent_order_cr_dr'] = $client_credit_debit_vendor;
        $data['data_cr_dr'] = $data_cr_dr;

        $data['clent_order_cr_dr_customer'] = $client_credit_debit_customer;
        $data['data_cr_dr_customer'] = $data_cr_dr_customer;

        $data['clent_order_cr_dr_3pl_vendor'] = $client_credit_debit_3pl_vendor;
        $data['data_cr_dr_3pl_vendor'] = $data_cr_dr_3pl_vendor;

        $data['clent_order_cr_dr_3pl_customer'] = $client_credit_debit_3pl_customer;
        $data['data_cr_dr_3pl_customer'] = $data_cr_dr_3pl_customer;
       
        $data['clent_order_cr_dr_direct_receipt'] = $client_credit_debit_direct_receipt;
        $data['data_cr_dr_direct_receipt'] = $data_cr_dr_direct_receipt;

        $data['clent_order_cr_dr_jv'] = $client_credit_debit_jv;
        $data['data_cr_dr_jv'] = $data_cr_dr_jv;

        $data['clent_order_cr_dr_debit_note'] = $client_credit_debit_note;
        $data['data_cr_dr_debit_note'] = $data_cr_dr_debit_note;
        
        $view = $this->load->view('system/ecommerce/ajax/sales_double_entry_summary',$data);

        return $view;

    }

    function load_double_entry_summary_all(){

        $data = array();

        $data['clent_order_detail'] = $this->Erp_data_sync_model->get_sales_client_record();
        $data['clent_order_cr_dr'] = $this->Erp_data_sync_model->get_sales_client_credit_debit_summary_all();


        $view = $this->load->view('system/ecommerce/ajax/sales_data_summary_all',$data);

        return $view;

    }

    function add_general_ledger(){

        $sales_id = $this->input->post('sales_id');

        $check_for_already_process = $this->Erp_data_sync_model->get_client_data_already_process($sales_id,'supplier');

        if($check_for_already_process){
            $this->session->set_flashdata('e', 'Sales record already been proccessed.');
            echo json_encode(FALSE);
            exit;
        }

        $response = $this->Erp_data_sync_model->set_general_ledger_records($sales_id);

        if($response && $response['status'] == 'success'){
            $this->session->set_flashdata('s', $response['message']);
            echo json_encode(TRUE);
        }else{
            $this->session->set_flashdata('w', isset($response['message']) ? $response['message'] : 'Something went wrong');
            echo json_encode(FALSE);
        }
       

    }
    
    function add_general_customer_3pl($type = null){

        $sales_id = $this->input->post('sales_id');

        $check_for_already_process = $this->Erp_data_sync_model->get_client_data_already_process($sales_id,'3PL_customer');

        if($check_for_already_process){
            $this->session->set_flashdata('e', '3PL Customer invoice already been proccessed.');
            echo json_encode(FALSE);
            exit;
        }

        $response = $this->Erp_data_sync_model->set_general_ledger_customer_3PL($sales_id);

        if($response && $response['status'] == 'success'){
            $this->session->set_flashdata('s', $response['message']);
            echo json_encode(TRUE);
        }else{
            $this->session->set_flashdata('e', isset($response['message']) ? $response['message'] : 'Something went wrong');
            echo json_encode(FALSE);
        }
       
    }

    function add_general_customer($type = null){

        $sales_id = $this->input->post('sales_id');

        $check_for_already_process = $this->Erp_data_sync_model->get_client_data_already_process($sales_id,'customer');

        if($check_for_already_process){
            $this->session->set_flashdata('w', 'Sales record already been proccessed.');
            echo json_encode(FALSE);
            exit;
        }

        $response = $this->Erp_data_sync_model->set_general_ledger_customer($sales_id);

        if($response && $response['status'] == 'success'){
            $this->session->set_flashdata('s', $response['message']);
            echo json_encode(TRUE);
        }else{
            $this->session->set_flashdata('w', isset($response['message']) ? $response['message'] : 'Something went wrong');
            echo json_encode(FALSE);
        }
       
    }

    function add_general_3PL_vendor(){

        $sales_id = $this->input->post('sales_id');

        $check_for_already_process = $this->Erp_data_sync_model->get_client_data_already_process($sales_id,'3PL_vendor');

        if($check_for_already_process){
            $this->session->set_flashdata('e', '3PL vendor invoice already been proccessed.');
            echo json_encode(FALSE);
            exit;
        }

        $response = $this->Erp_data_sync_model->set_general_ledger_records_3PL($sales_id);

        if($response && $response['status'] == 'success'){
            $this->session->set_flashdata('s', $response['message']);
            echo json_encode(TRUE);
        }else{
            $this->session->set_flashdata('w', isset($response['message']) ? $response['message'] : 'Something went wrong');
            echo json_encode(FALSE);
        }
    }

    function direct_invoice(){

        $sales_id = $this->input->post('sales_id');

        $client_data = $this->Erp_data_sync_model->get_sales_client_record($sales_id);
        $check_for_already_process = $this->Erp_data_sync_model->get_client_data_already_process($sales_id,'direct_invoice');
       

        if(empty($client_data)) {
            $this->session->set_flashdata('e', 'No client record has been found.');
            echo json_encode(FALSE);
            exit;
        }

        $RVdate = isset($client_data['date_time']) ? $client_data['date_time'] : '';
        $financearray_rec = get_financial_period_date_wise($RVdate);
        $financearray = $financearray_rec['companyFinancePeriodID'];
       
        if($check_for_already_process){
            $this->session->set_flashdata('e', 'Direct receipt voucher already been generated.');
            echo json_encode(FALSE);
            exit;
        }

        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        

        if ($financeyearperiodYN == 1) {
           
            $financePeriod = fetchFinancePeriod($financearray);

            if ($RVdate >= $financePeriod['dateFrom'] && $RVdate <= $financePeriod['dateTo']) {
            // if($financePeriod['dateFrom'] ) {
                $response = $this->Erp_data_sync_model->set_receiptvoucher_header($sales_id);
            } else {
                $response = array('status' => 'error' , 'message' => 'Receipt Voucher Date not between Financial period !');
                $this->session->set_flashdata('e', 'Receipt Voucher Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }else{
            $response = $this->Erp_data_sync_model->set_receiptvoucher_header($sales_id);
        }

       

        if($response && $response['status'] == 'success'){
            $this->session->set_flashdata('s', $response['message']);
            echo json_encode(TRUE);
        }else{
            $this->session->set_flashdata('w', isset($response['message']) ? $response['message'] : 'Something went wrong');
            echo json_encode(FALSE);
        }

    }

    function journel_voucher(){

        $sales_id = $this->input->post('sales_id');

        $client_data = $this->Erp_data_sync_model->get_sales_client_record($sales_id);
        $check_for_already_process = $this->Erp_data_sync_model->get_client_data_already_process($sales_id,'jv');

        $RVdate = isset($client_data['date_time']) ? $client_data['date_time'] : '';
        $financearray_rec = get_financial_period_date_wise($RVdate);
        $financearray = $financearray_rec['companyFinancePeriodID'];

        if($financearray_rec && $financearray_rec['isActive'] != 1){
            $this->session->set_flashdata('e', 'Journel Voucher Date not within Active Financial period.');
            return json_encode(FALSE);
            exit;
        }
       
        if($check_for_already_process){
            $this->session->set_flashdata('e', 'Journel Voucher already been generated.');
            echo json_encode(FALSE);
            exit;
        }

        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        

        if ($financeyearperiodYN == 1) {
           
            $financePeriod = fetchFinancePeriod($financearray);
            if ($RVdate >= $financePeriod['dateFrom'] && $RVdate <= $financePeriod['dateTo']) {
                
            // if($financePeriod['dateFrom'] ) {
                $response = $this->Erp_data_sync_model->set_journal_entry_header($sales_id);
            } else {
                $response = array('status' => 'error' , 'message' => 'Journel Voucher Date not between Financial period !');
                $this->session->set_flashdata('e', 'Journel Voucher Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }else{
            $response = $this->Erp_data_sync_model->set_journal_entry_header($sales_id);
        }

        if($response && $response['status'] == 'success'){
            $this->session->set_flashdata('s', $response['message']);
            echo json_encode(TRUE);
        }else{
            $this->session->set_flashdata('w', isset($response['message']) ? $response['message'] : 'Something went wrong');
            echo json_encode(FALSE);
        }
    }

    function load_client_setting(){

        echo json_encode($this->Erp_data_sync_model->get_clent_ecommerce_settings());

    }

    function update_client_setting(){

        echo json_encode($this->Erp_data_sync_model->set_clent_ecommerce_settings());

    }

    function save_data_mapping_posting(){

        echo json_encode($this->Erp_data_sync_model->save_data_mapping_posting());
    }

    function change_posting_method(){
        echo json_encode($this->Erp_data_sync_model->change_posting_method());
    }

    function change_posting_status(){
        echo json_encode($this->Erp_data_sync_model->change_posting_status());
    }

    function delete_posting_status(){
        echo json_encode($this->Erp_data_sync_model->delete_posting_status());
    }

    function delete_manual_posting_record(){
        echo json_encode($this->Erp_data_sync_model->delete_manual_posting_record());
    }

    function fetch_sales_posting(){
        $this->datatables->select('*,srp_erp_ecommerce_posting.status as ERPstatus,srp_erp_ecommerce_posting.service_type as service_type, 
        srp_erp_ecommerce_posting.id as id,srp_erp_ecommerce_posting.mode_collection as mode_collection')
            ->from('srp_erp_ecommerce_posting');
        $this->datatables->edit_column('status', '$1', 'srp_posting_status_get(ERPstatus)');
        $this->datatables->edit_column('service_type', '$1', 'srp_posting_service_type_get(service_type)');
        $this->datatables->edit_column('mode_collection', '$1', 'srp_posting_mode_collection_get(mode_collection)');
        $this->datatables->add_column('edit_ui', '<span style="display: flex; justify-content:center"> <a onclick="data_edit_posting($1)"><i class="fa fa-cog"></i></a> &nbsp &nbsp  &nbsp <a onclick="data_delete_posting($1)"><i class="fa fa-trash text-danger"></i></a></span>','id');
        $this->datatables->add_column('switch','$1','edit_make_active_switch(ERPstatus,id)');
        echo $this->datatables->generate();
    }

    function get_posting_action_log(){

        $data = array();
        $posting_id = $this->input->post('id');
        $data = array();

        $get_posting_details =  $this->Erp_data_sync_model->get_posting_data_from_posting($posting_id);

        if($get_posting_details){
            $doc_id = $get_posting_details['doc_id'];
            $data['processed_log'] = $this->Erp_data_sync_model->get_order_process_action_log($doc_id);
        }

        $view = $this->load->view('system/ecommerce/ajax/sales_posting_action_view',$data);

        return $view;


    }
  

    //////////////////////// Data posting ///////////////////

    function fetch_sales_sytem_postings(){

        $this->datatables->select('*, srp_erp_ecommerce_system_posting.id as id,
                srp_erp_ecommerce_system_posting.type as type,srp_erp_ecommerce_system_posting.status as status,
                srp_erp_ecommerce_system_posting.mode_collection as mode_collection,srp_erp_ecommerce_system_posting.service_type as service_type')
            ->from('srp_erp_ecommerce_system_posting');
            $this->datatables->edit_column('type', '$1', 'srp_posting_type_show(type)');
            $this->datatables->edit_column('status', '$1', 'srp_posting_manual_status_get(status)');
            $this->datatables->edit_column('service_type', '$1', 'srp_posting_service_type_get(service_type)');
            $this->datatables->edit_column('mode_collection', '$1', 'srp_posting_mode_collection_get(mode_collection)');
            $this->datatables->add_column('action','$1','srp_posting_action_button(id,status)');
      //  $this->datatables->add_column('edit_ui', '<a onclick="data_edit_posting($1)"><i class="fa fa-cog"></i></a> &nbsp <a onclick="data_edit_posting($1)"><i class="fa fa-trash text-danger"></i></a>','id');
      //  $this->datatables->add_column('switch','$1','edit_make_active_switch(ERPstatus)');
        echo $this->datatables->generate();

    }

    function confirm_daily_posting(){

        
        $this->form_validation->set_rules('dateFrom', 'Date From', 'trim|required'); 
        $this->form_validation->set_rules('dateTo', 'Date To', 'trim|required'); 
        $this->form_validation->set_rules('comments', 'Description', 'trim|required'); 
        $this->form_validation->set_rules('service_type', 'Service Type', 'trim|required'); 
        $this->form_validation->set_rules('mode_collection', 'Mode of Cash Collected', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Erp_data_sync_model->save_posting_date_for_execute());
        }

    }

    function run_daily_posting(){
        $this->form_validation->set_rules('id', 'Posting ID', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Erp_data_sync_model->process_daily_posting_list());
        }
    }

    function run_daily_posting_automate(){

        $this->form_validation->set_rules('id', 'Posting ID', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Erp_data_sync_model->process_daily_posting_list_automate());
        }

    }

    function edit_order_manage(){

        echo json_encode($this->Erp_data_sync_model->edit_order_manage());

    }

    function edit_order_filtered_all(){
        echo json_encode($this->Erp_data_sync_model->edit_order_filtered_all());
    }
}