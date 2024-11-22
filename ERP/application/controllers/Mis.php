<?php

class Mis extends ERP_Controller
{
 
    function __construct()
    {
        parent::__construct();
        $this->load->model('Mis_model'); 
        $this->load->helper('mis');
        // $this->load->library('erp_data_sync');
        // $this->load->helper('receivable');
    }

    
    /*
        Fetch Mis report list
        @views mis/add_report
    */
    function fetch_mis_report_list(){
        
        $this->datatables->select('rows.*,rows.id as id')
            ->from('srp_erp_mis_report as rows');
        $this->datatables->add_column('view', '$1' , 'get_config_control_report_buttons(id)');
        // $this->datatables->edit_column('header_type1','$1','get_config_type_str(header_type1)');
        // $this->datatables->edit_column('header_type2','$1','get_config_type2_str(header_type2)');
        // $this->datatables->add_column('edit_ui','$1', 'erp_client_sales_actions(ERPstatus,invoiceID_Erp)');
        // $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');

        echo $this->datatables->generate();
    }

     /*
        Fetch Mis rows base configs
        @views mis/add_report
    */
    function fetch_mis_report_rows(){

        $report_id = $this->input->post('report_id');

        $this->db->order_by('sort_order');
        $this->datatables->select('rows.*,rows.id as id,rows.header_type1 as header_type1,rows.header_type2 as header_type2')
            ->where('rows.report_id',$report_id)
            ->from('srp_erp_mis_report_config_rows as rows');
        $this->datatables->add_column('view', '$1' , 'get_config_control_buttons(id)');
        $this->datatables->add_column('count', '$1' , 'get_config_rows_mapped_count(id)');
        $this->datatables->edit_column('header_type1','$1','get_config_type_str(header_type1)');
        $this->datatables->edit_column('header_type2','$1','get_config_type2_str(header_type2)');
        // $this->datatables->add_column('edit_ui','$1', 'erp_client_sales_actions(ERPstatus,invoiceID_Erp)');
        // $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');

        echo $this->datatables->generate();
    }

    /*
        Add Mis rows base configs
        @views mis/add_report
    */
    function add_report_posting(){

        $this->form_validation->set_rules('report_name', 'Report Name', 'trim|required'); 
        $this->form_validation->set_rules('mis_report_type', 'Report Type', 'trim|required'); 
        $this->form_validation->set_rules('doc_id', 'Documnet ID', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Mis_model->add_report_posting());
        }
    }

    /*
        Add Mis rows base configs
        @views mis/add_report
    */
    function add_config_field_setting(){

        $this->form_validation->set_rules('config_id', 'Config ID', 'trim|required'); 
        $this->form_validation->set_rules('cat_id', 'Category ID', 'trim|required'); 
        $this->form_validation->set_rules('cat_description', 'Category Description', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Mis_model->add_config_rows());
        }
        
    }

    /*
        Delete Mis rows base configs
        @views mis/add_report
    */
    function delete_config_row(){
        echo json_encode($this->Mis_model->delete_config_row());
    }

    /*
        Delete Mis rows base configs
        @views mis/add_report
    */
    function edit_config_row(){

        $config_row_id = $this->input->post('config_row_id');
        $data = array();
       

        $data['result'] = $config_details  = $this->Mis_model->get_config_row($config_row_id);
        $data['report_id'] = $config_details['report_id'];
        $data['type2_arr'] = array('1'=>"Income",'2'=>"Expense");
        $data['config_row_id'] = $config_row_id;

        return $this->load->view('system/mis/ajax/config_edit_view',$data);
        
        // return $html;

    }

     /*
       load configs added settings
        @views mis/add_report
    */
    function load_config_settings(){

        $config_row_id = $this->input->post('config_row_id');
        $report_type = $this->input->post('report_type');
        $data = array();
        $type = '';

        if($report_type == '1'){
            $type = 'PL';
        }elseif($report_type == '2'){
            $type = 'BS';
        }

        

        $config_rec = $this->Mis_model->get_config_row($config_row_id);
        

        if($config_rec && $config_rec['header_type1'] == 1){

            $chart_of_accounts = get_chart_of_accounts_masterID_drop($type);

            $data['chart_of_accounts'] = $chart_of_accounts;
            $data['config_row_id']  = $config_row_id;

            return $this->load->view('system/mis/ajax/config_chart_of_account',$data);

        }elseif($config_rec && $config_rec['header_type1'] == 2){

            $added_headers = get_headers_for_report($config_rec['report_id']);

            $data['added_reports'] = $added_headers;
            $data['config_row_id']  = $config_row_id;

            return $this->load->view('system/mis/ajax/total_header_set',$data);
        }elseif($config_rec && $config_rec['header_type1'] == 3){

            $added_headers = get_headers_for_report($config_rec['report_id'],2);

            $data['added_reports'] = $added_headers;
            $data['config_row_id']  = $config_row_id;

            return $this->load->view('system/mis/ajax/total_header_set',$data);
        }elseif($config_rec && $config_rec['header_type1'] == 4){

            $added_headers = get_headers_for_report($config_rec['report_id'],3);

            $data['added_reports'] = $added_headers;
            $data['config_row_id']  = $config_row_id;

            return $this->load->view('system/mis/ajax/total_header_set',$data);
        }


       
    }

    function fetch_mis_report_added_chart_of_accounts(){

        $config_row_id = $this->input->post('config_row_id');

        $this->datatables->select('rows.*,rows.id as id')
            ->where('config_row_id',$config_row_id)
            ->from('srp_erp_mis_report_config_chartofaccounts as rows');
        $this->datatables->add_column('view', '$1' , 'get_chart_of_account_control(id)');
        // $this->datatables->edit_column('header_type1','$1','get_config_type_str(header_type1)');
        // $this->datatables->edit_column('header_type2','$1','get_config_type2_str(header_type2)');
        // $this->datatables->add_column('edit_ui','$1', 'erp_client_sales_actions(ERPstatus,invoiceID_Erp)');
        // $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');

        echo $this->datatables->generate();

    }

    function add_config_chartofaccount(){

        $this->form_validation->set_rules('config_row_id', 'Config ID', 'trim|required'); 
        $this->form_validation->set_rules('selected_chart_of_account', 'Chart of Account', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Mis_model->add_config_chartofaccount());
        }

    }

    function add_config_detail(){

        $this->form_validation->set_rules('config_row_id', 'Config ID', 'trim|required'); 
        $this->form_validation->set_rules('added_headers', 'Header', 'trim|required'); 
        $this->form_validation->set_rules('plus_minus', 'Transaction Type', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Mis_model->add_config_detail());
        }

        
    }

    function fetch_config_details(){

        $config_row_id = $this->input->post('config_row_id');
        
        $this->datatables->select('rows.*,rows.id as id,rows.mapped_row_id as mapped_row_id,rows.value as value')
            ->where('config_row_id',$config_row_id)
            ->from('srp_erp_mis_report_config_details as rows');
        $this->datatables->add_column('view','$1','get_config_row_delete_detail_button(id)');
        $this->datatables->add_column('header_type_mapped','$1','get_config_row_detail(mapped_row_id,1)');
        $this->datatables->add_column('category_id','$1','get_config_row_detail(mapped_row_id,2)');
        $this->datatables->add_column('category_description','$1','get_config_row_detail(mapped_row_id,3)');
        $this->datatables->edit_column('value','$1', 'get_config_row_detail_value(value)');
        // $this->datatables->add_column('process','$1', 'erp_client_sales_process(ERPstatus)');

        echo $this->datatables->generate();

    }

    function delete_config_row_detail(){

        echo json_encode($this->Mis_model->delete_config_row_detail());

    }

    function delete_added_chart_of_account(){
        echo json_encode($this->Mis_model->delete_added_chart_of_account());
    }

}

