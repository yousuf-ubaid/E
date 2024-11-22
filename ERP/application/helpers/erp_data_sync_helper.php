<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('getClientSalesHeaders')) {
    function getClientSalesHeaders($table='srp_erp_ecommerce_sales_clientdata')
    {
        $CI =& get_instance();
        $columns = array();

        $records = $CI->db->select('COLUMN_NAME')->from('INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME',$table)->get()->result_array();

        foreach($records as $value){
            $columns[] = $value['COLUMN_NAME'];
        }
        
        return $columns;
    }
}


if (!function_exists('getChartofAccounts')) {
    function getChartofAccounts()
    {
        $CI =& get_instance();
        $columns = array();

        $companyID = $CI->common_data['company_data']['company_id'];

        $details = $CI->db->query("SELECT srp_erp_chartofaccounts.*,companyReportingAmount,companyReportingCurrencyDecimalPlaces,	IF(glExist.existGLAutoID IS NOT NUll,1,0) as dataExist FROM srp_erp_chartofaccounts LEFT JOIN (SELECT SUM(companyReportingAmount) AS companyReportingAmount,GLAutoID,companyReportingCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE companyID={$companyID} GROUP BY srp_erp_generalledger.GLAutoID) gl ON (gl.GLAutoID =srp_erp_chartofaccounts.GLAutoID) LEFT JOIN (SELECT GLAutoID as existGLAutoID FROM checkchartofaccountgl WHERE companyID = {$companyID} GROUP BY GLAutoID)glExist ON glExist.existGLAutoID = srp_erp_chartofaccounts.GLAutoID WHERE srp_erp_chartofaccounts.companyID = {$companyID} GROUP BY srp_erp_chartofaccounts.GLAutoID")->result_array();

        foreach($details as $value){
            $gl_codes[ $value['systemAccountCode'] ] = $value['systemAccountCode'];
        }
        
        return $gl_codes;
    }
}


if (!function_exists('erp_credit_debit')) {
    function erp_credit_debit($erp_credit_debit)
    {
        $detail = ($erp_credit_debit == 'cr') ? 'Credit' : 'Debit';
        
        return $detail;
    }
}

if (!function_exists('srp_posting_status_get')) {
    function srp_posting_status_get($ERPstatus)
    {
        $detail = ($ERPstatus == 1) ? '<span class="badge badge-info" style="color:white; background-color:green; padding:5px">Active </span>' : '<span class="badge badge-danger" style="color:white; background-color:red; padding:5px">Inactive </span>';
        
        return $detail;
    }
}

if (!function_exists('srp_posting_service_type_get')) {
    function srp_posting_service_type_get($service_type)
    {
        $detail = '';

        $CI =& get_instance();

        $companyID = $CI->common_data['company_data']['company_id'];
        
        $response = $CI->db->from('srp_erp_ecommerce_service_type')
            ->where('companyId', $companyID)
            ->where('id', $service_type)
            ->get()
            ->row_array();
        
        if($response && isset($response['service_code'])){
            $detail = $response['service_code'];
        }

        // if($service_type == 1){
        //     $detail = 'TMDONE';
        // }else if($service_type == 2){
        //     $detail = 'MARKET PLACE';
        // }else if($service_type == 3){
        //     $detail = 'PICKUP';
        // }else if($service_type == 4){
        //     $detail = 'RECOVERY';
        // }
        
        return $detail;
    }
}

if (!function_exists('srp_posting_service_type_get_by_name')) {
    function srp_posting_service_type_get_by_name($service_type_name)
    {
        $detail = '';
        $service_type_name = trim($service_type_name);

        // if($service_type_name == 'TMDONE'){
        //     $detail = '1';
        // }else if($service_type_name == 'MARKET PLACE'){
        //     $detail = '2';
        // }else if($service_type_name == 'PICKUP'){
        //     $detail = '3';
        // }else if($service_type_name == 'RECOVERY'){
        //     $detail = '4';
        // }

        $CI =& get_instance();

        $companyID = $CI->common_data['company_data']['company_id'];
        
        $response = $CI->db->from('srp_erp_ecommerce_service_type')
            ->where('companyId', $companyID)
            ->where('service_code', $service_type_name)
            ->get()
            ->row_array();
        
        if($response && isset($response['id'])){
            $detail = $response['id'];
        }

        return $detail;
    }
}

if (!function_exists('srp_posting_invoice_type_get')) {
    function srp_posting_invoice_type_get($invoice_type)
    {
        $detail = '';

        if($invoice_type == 1){
            $detail = 'Supplier Invoice';
        }else if($invoice_type == 2){
            $detail = 'Customer Invoice';
        }else if($invoice_type == 8){
            $detail = 'Credit Note';
        }else if($invoice_type == 9){
            $detail = 'Debit Note';
        }
        
        return $detail;
    }
}

if (!function_exists('srp_posting_mode_collection_get')) {
    function srp_posting_mode_collection_get($mode_collection)
    {
        $detail = '';

        if($mode_collection == 1){
            $detail = 'ALL';
        }else if($mode_collection == 2){
            $detail = 'CASH';
        }else if($mode_collection == 3){
            $detail = 'CARD';
        }
        
        return $detail;
    }
}

if (!function_exists('srp_posting_mode_collection_get_by_name')) {
    function srp_posting_mode_collection_get_by_name($mode_collection)
    {
        $detail = '';

        if($mode_collection == 'ALL'){
            $detail = '1';
        }else if($mode_collection == 'CASH'){
            $detail = '2';
        }else if($mode_collection == 'CARD'){
            $detail = '3';
        }
        
        return $detail;
    }
}



if (!function_exists('erp_client_sales_actions')) {
    function erp_client_sales_actions($ERPstatus,$invoiceID_Erp = null)
    {
        $action = '<span class="d-flex pull-center">';
        if($ERPstatus == 1){
            // $action = '<a target="_blank" href="Double_entry/fetch_double_entry_supplier_invoices/'.$invoiceID_Erp.'/BSI" rel="tooltip" class="btn btn-success">View</a>';
            $action .= '<a target="_blank" onclick="documentPageView_modal(\'BSI\',\'' . $invoiceID_Erp . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }else{
            $action = '';
        }
        $action .= '</span>';
        return $action;

    }
    
}

if (!function_exists('erp_client_sales_de_manage')) {
    function erp_client_sales_de_manage($ERPstatus,$invoiceID_Erp,$ID)
    {
        $action = '<span class="d-flex pull-center">
                        <a onclick="double_entry_view(\''.$ID.'\')" class="btn btn-danger">
                            <i class="fa fa-check"></i> Entry</a> &nbsp';
        if($ERPstatus == 1){
            // $action = '<a target="_blank" href="Double_entry/fetch_double_entry_supplier_invoices/'.$invoiceID_Erp.'/BSI" rel="tooltip" class="btn btn-success">View</a>';
            $action .= '<a class="btn btn-success" target="_blank" onclick="documentPageView_modal(\'BSI\',\'' . $invoiceID_Erp . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span> View</a>&nbsp;&nbsp;';
        }else{
            $action .= '<a onclick="manage_edit_order(\''.$ID.'\')" class="btn btn-primary">
                            <i class="fa fa-cog"></i> Manage</a> &nbsp';
        }
        $action .= '<a onclick="view_history\''.$ID.'\')" class="btn btn-default">
                        <i class="fa fa-history"></i></a> </span>';
        return $action;

    }
    
}

if (!function_exists('erp_client_sales_process')) {
    function erp_client_sales_process($ERPstatus)
    {
        if($ERPstatus == 1){
            $action = '<span class="badge badge-info" style="color:white; background-color:green; padding:5px">GENERATED</span>';
        }else{
            $action = '<span class="badge badge-danger" style="color:white; background-color:red; padding:5px" >PENDING</span>';
        }
       
        return $action;

    }
    
}

if (!function_exists('fetch_all_gl_codes_ecommerce')) {
    function fetch_all_gl_codes_ecommerce($code = NULL, $category = NULL)
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory,accountCategoryTypeID");
        $CI->db->from('srp_erp_chartofaccounts');
        if ($code) {
            $CI->db->where('subCategory', $code);
        }
        if ($category) {
            $CI->db->where('subCategory !=', $category);
        }
       // $CI->db->where('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        // $CI->db->WHERE('accountCategoryTypeID !=', 4);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        // $CI->db->where('isBank', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }

    
    if (!function_exists('fetch_all_posting_data')) {
        function fetch_all_posting_data($data_id)
        {

            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->from('srp_erp_ecommerce_posting');
            $CI->db->where('id',$data_id);
            $data = $CI->db->get()->row_array();

            if($data){
                return $data;
            }

        }
    }

    
    if (!function_exists('edit_datatable_mapping_type')) {
        function edit_datatable_mapping_type($mapping_type,$invoice_type = null)
        {   
            $invoice_name = '';
            if($invoice_type){
                $invoice_name = srp_posting_invoice_type_get($invoice_type);
            }

            if($mapping_type == 1){
                $action = 'Vendor'.' - '.$invoice_name;
            }elseif($mapping_type == 2){
                $action = 'Customer'.' - '. $invoice_name;
            }elseif($mapping_type == 3){
                $action = '3PL Vendor';
            }elseif($mapping_type == 4){
                $action = '3pl Customer';
            }elseif($mapping_type == 5){
                $action = 'Direct Income Receipt Voucher';
            }elseif($mapping_type == 6){
                $action = 'Journel Voucher';
            }else{
                $action = '';
            }
           
            return $action;
    
        }
        
    }

    if (!function_exists('edit_make_active_switch')) {
        function edit_make_active_switch($status,$id)
        {
            if($status == 1){
               $action = '<input type="checkbox" checked data-toggle="toggle" style="width:20px;height:20px;cursor:pointer" onclick="change_posting_active_inactive(\''.$id.'\')">';
    
            }else{
                $action = '<input type="checkbox" data-toggle="toggle"  style="width:20px;height:20px;cursor:pointer" onclick="change_posting_active_inactive(\''.$id.'\')">';
              
            }
           
            return $action;
    
        }
        
    }

    if (!function_exists('get_control_acc')) {
        function get_control_acc($status)
        {
            if($status == 1){
                $action = '<span class="badge badge-success" style="padding:5px 10px;background-color:green;">Yes</span>';
            }else{
                $action = '<span class="badge badge-danger" style="padding:5px 10px;background-color:red;">No</span>';
            }
           
            return $action;
    
        }
        
    }

    if (!function_exists('srp_posting_type_show')) {
        function srp_posting_type_show($status)
        {
            if($status == 1){
                $action = 'Manual';
            }else{
                $action = 'Automatic';
            }

            return $action;
    
        }
        
    }

    if (!function_exists('srp_posting_manual_status_get')) {
        function srp_posting_manual_status_get($status)
        {
            if($status == 1){
                $action = '<span class="badge badge-success" style="padding:5px 10px;background-color:green;">Completed</span>';
            }else if($status == 3){
                $action = '<span class="badge badge-danger" style="padding:5px 10px;background-color:orange;">Running</span>';
            }else {
                $action = '<span class="badge badge-warn" style="padding:5px 10px;background-color:red;">Pending</span>';
            }
            
            return $action;
    
        }
        
    }

    if (!function_exists('srp_posting_action_button')) {
        function srp_posting_action_button($id,$actionstatus)
        {
          
            $action = '<span class="d-flex pull-center">';
            if($actionstatus == 1){
                $action = '
                    <a class="btn btn-default" onclick="show_processed_records(\''.$id.'\')"><i class="fa fa-eye"></i> View</a>
                    <a class="btn btn-success" onclick="action_posting_log(\''.$id.'\')"><i class="fa fa-rss"></i></a>
                    ';
            }else if($actionstatus == 3){
                $action = '
                    <a class="btn btn-success" onclick="action_posting_log(\''.$id.'\')"><i class="fa fa-rss"></i></a>
                    <a class="btn btn-default" onclick="show_processed_records(\''.$id.'\')"><i class="fa fa-eye"></i> View</a>
                    
                ';
            }
            else{
                $action .= '
                    <a class="btn btn-success" target="_blank" onclick="proceed_posting(\'' . $id . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-cog"></span> Run</a>&nbsp;&nbsp;
    
                        <a class="btn btn-default" onclick="edit_posting(\''.$id.'\')"><i class="fa fa-pencil-square-o"></i></a>
                        <a class="btn btn-danger" onclick="delete_posting(\''.$id.'\')"><i class="fa fa-trash"></i></a>
                 
                ';
            }
            $action .= '</span>';
            return $action;
    
        }
        
    }

    
    if (!function_exists('fetch_all_manual_posting_settings_data')) {
        function fetch_all_manual_posting_settings_data($settings_id)
        {

            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->from('srp_erp_ecommerce_system_posting');
            $CI->db->where('id',$settings_id);
            $data = $CI->db->get()->row_array();

            if($data){
                return $data;
            }

        }

    }

    

    if (!function_exists('get_supplier_master_by_secondary_code')) {
        function get_supplier_master_by_secondary_code($secondary_code)
        {

            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->from('srp_erp_suppliermaster');
            $CI->db->where('secondaryCode',$secondary_code);
            $data = $CI->db->get()->row_array();

            if($data){
                return $data;
            }

        }
    }

    if (!function_exists('get_supplier_master_by_system_code')) {
        function get_supplier_master_by_system_code($secondary_code)
        {

            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->from('srp_erp_suppliermaster');
            $CI->db->where('supplierSystemCode',$secondary_code);
            $data = $CI->db->get()->row_array();

            if($data){
                return $data;
            }

        }
    }

    if (!function_exists('get_customer_master_by_secondary_code')) {
        function get_customer_master_by_secondary_code($secondary_code)
        {

            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->from('srp_erp_customermaster');
            $CI->db->where('secondaryCode',$secondary_code);
            $data = $CI->db->get()->row_array();

            if($data){
                return $data;
            }

        }
    }

    if (!function_exists('get_chartofaccounts_by_code')) {
        function get_chartofaccounts_by_code($systemAccountCode)
        {

            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->from('srp_erp_chartofaccounts');
            $CI->db->where('systemAccountCode',$systemAccountCode);
            $data = $CI->db->get()->row_array();

            if($data){
                return $data;
            }

        }
    }

    
    if (!function_exists('get_chartofaccounts_by_secondarycode')) {
        function get_chartofaccounts_by_secondarycode($secondaryCode,$company_id)
        {

            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->from('srp_erp_chartofaccounts');
            $CI->db->where('GLSecondaryCode',$secondaryCode);
            $CI->db->where('companyID',$company_id);
            $data = $CI->db->get()->row_array();

            if($data){
                return $data;
            }

        }
    }


    
    if (!function_exists('get_clent_ecommerce_settings')) {
        function get_clent_ecommerce_settings()
        {

            $CI = &get_instance();

            $company_id = $CI->common_data['company_data']['company_id'];

            $response = $CI->db->from('srp_erp_ecommerce_settings')->where('company_id',$company_id)->get()->row_array();
    
            return $response;

        }
    }

    if (!function_exists('add_process_log_record')) {
        function add_process_log_record($doc_id,$document_type,$sales_id,$status,$message,$invoice_type = null,$posting_id = null)
        {

            $CI = &get_instance();
            $company_id = $CI->common_data['company_data']['company_id'];
            $order_detail = $CI->db->from('srp_erp_ecommerce_sales_clientdata')->where('id',$sales_id)->get()->row_array();

            $data = array();
            $data['document_id'] = $doc_id;
            $data['document_type'] = $document_type;
            $data['status'] = $status;
            $data['message'] = $message.' - By '.$CI->common_data['current_user'];
            $data['alert_color'] = ($status == 1) ? 'green' : 'red';
            $data['date'] = $CI->common_data['current_date'];
            $data['added_by'] = $CI->common_data['current_user'];
            $data['added_source'] = $CI->common_data['current_pc'];
            $data['sales_id'] = $sales_id;
            $data['company_id'] = $company_id;
            $data['invoice_type'] = $invoice_type;
            $data['posting_id'] = $posting_id;
            $data['order_ref'] = isset($order_detail['order']) ? $order_detail['order'] : '';   

            $response = $CI->db->insert('srp_erp_ecommerce_error_log',$data);

            return $response;

        }
    }


}


if (!function_exists('get_orders_according_to_filters')) {
    function get_orders_according_to_filters($datefrom,$dateto,$service_type,$store_ids,$apply_for_all)
    {
        $CI =& get_instance();
        $columns = array();

        $companyID = $CI->common_data['company_data']['company_id'];
        $datefrom = date('Y-m-d',strtotime($datefrom)).' 00:00:00';
        $dateto = date('Y-m-d',strtotime($dateto)).' 23:59:59';;

        if($apply_for_all == 1){

            $response = $CI->db->from('srp_erp_ecommerce_sales_clientdata')
                ->where('date_time >=', $datefrom)
                ->where('date_time <=', $dateto)
                ->where('service_type =', $service_type)
                ->get()
                ->result_array();

        }else{

            $response = $CI->db->from('srp_erp_ecommerce_sales_clientdata')
                ->where('date_time >=', $datefrom)
                ->where('date_time <=', $dateto)
                ->where('service_type =', $service_type)
                ->where_in('store_id',$store_ids)
                ->get()
                ->result_array();


        }
       

        return $response;
       
    }
}



if (!function_exists('service_type_get')) {
    function service_type_get($id = null)
    {
        $CI =& get_instance();
        $columns = array();

        $companyID = $CI->common_data['company_data']['company_id'];

        try{

            $response = $CI->db->from('srp_erp_ecommerce_service_type')
                ->where('status', 1)
                ->where('companyId', $companyID)
                ->get()
                ->result_array();

            if($id){

                foreach($response as $value){
                    $columns[$value['id']] = $value['service_name'];
                }

            }else{
                foreach($response as $value){
                    $columns[$value['service_code']] = $value['service_name'];
                }
    
            }
            

        }catch(Exception $e){
            return $columns;
        }
       

        return $columns;
       
    }
}

