<?php
  
if (!function_exists('get_automation_payment_master')) {
    function get_automation_payment_master($doc_id)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_ap_vendor_payments_master');
        $CI->db->where('doc_id',$doc_id);
        $data = $CI->db->get()->row_array();

        if($data){
            return $data;
        }

    }
}

if (!function_exists('get_automation_payment_master_by_id')) {
    function get_automation_payment_master_by_id($master_id,$record = null)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_ap_vendor_payments_master');
        $CI->db->where('id',$master_id);
        $data = $CI->db->get()->row_array();
       
        if($data){
            return $data;
        }

    }
}

if (!function_exists('payment_voucher_view')) {
    function payment_voucher_view($auto_id,$status)
    {
        $action = '<span>';
        if($status == 1){
            $action .= '<a class="btn btn-success" target="_blank" onclick="documentPageView_modal(\'PV\',\'' . $auto_id . '\')" ><i class="fa fa-eye"></i> Voucher</span></a>';
        }else{
            $action .= '<span class="badge badge-warning" style="background-color:red">Not Created</span>';
        }

        $action .= '</span>';

        return $action;
    }
}

if (!function_exists('fetch_modify_record')) {
    function fetch_modify_record($auto_id,$confirmedYN=null)
    {

        $action = '<span>';
        
        if($confirmedYN != 1){
            $action .= '<a onclick="modify_allocations(\'' . $auto_id . '\')" class="btn btn-primary"><i class="fa fa-cog"></i> Modify</a> &nbsp &nbsp <a class="btn btn-danger" onclick="delete_vendor_wise_allocations(\'' . $auto_id . '\')"><i class="fa fa-trash"></i></a>';
        }

        $action .= '</span>';

        return $action;

      
    }
}

if (!function_exists('fetch_view_record')) {
    function fetch_view_record($auto_id)
    {

        $action = '<span>';
    
        $action .= '<a onclick="load_payment_wise_invoices(\'' . $auto_id . '\')" style="color:black; display:ruby-text"><i class="fa fa-eye"></i></a>';
    

        $action .= '</span>';

        return $action;

      
    }
}


if (!function_exists('fetch_invoice_allocation_status')) {
    function fetch_invoice_allocation_status($status)
    {
        $action = '<span>';
        if($status == 1){
            $action .= '<span class="badge badge-success" style="background-color:green">Allocated</span>';
        }else{
            $action .= '<span class="badge badge-warning" style="background-color:red">Not Allocated</span>';
        }

        $action .= '</span>';

        return $action;
    }
}


if (!function_exists('fetch_confirmed')) {
    function fetch_confirmed($confirmedYN)
    {
        $action = '<div class="text-center">';
        if($confirmedYN == 1){
            $action .= '<span class="label label-success">&nbsp;</span>';
        }else{
            $action .= '<span class="label label-danger">&nbsp;</span>';
        }

        $action .= '</div>';

        return $action;
    }
}


if (!function_exists('fetch_invoice_view')) {
    function fetch_invoice_view($code,$id,$invoiceType)
    {   
        $action = '';
 
        if($invoiceType == 'debitnote'){
            $action .= '<a target="_blank" onclick="documentPageView_modal(\'DN\',\'' . $id . '\')" > '.$code.'</span></a>';
        }else{
            $action .= '<a target="_blank" onclick="documentPageView_modal(\'BSI\',\'' . $id . '\')" > '.$code.' </span></a>';
        }

        return $action;
    }
}





if (!function_exists('get_automation_payment_allocation_by_master_id')) {
    function get_automation_payment_allocation_by_master_id($master_id,$record = null)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_ap_vendor_payments');
        $CI->db->where('master_id',$master_id);

        if($record){
            $data = $CI->db->get()->result_array();
        }else{
            $data = $CI->db->get()->row_array();
        }

        if($data){
            return $data;
        }

    }
}

if (!function_exists('get_invoice_payment_allocation_by_payment_id')) {
    function get_invoice_payment_allocation_by_payment_id($payment_id)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_ap_vendor_invoice_allocation');
        $CI->db->where('payment_id',$payment_id);
        $CI->db->where('status',1);
        $CI->db->where('invoiceType','SupplierInvoice');
        $data = $CI->db->get()->result_array();

        if($data){
            return $data;
        }

    }
}

if (!function_exists('get_invoice_payment_allocation_by_debitnotes_payment_id')) {
    function get_invoice_payment_allocation_by_debitnotes_payment_id($payment_id)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_ap_vendor_invoice_allocation');
        $CI->db->where('payment_id',$payment_id);
        $CI->db->where('status',1);
        $CI->db->where('invoiceType','debitnote');
        $data = $CI->db->get()->result_array();

        if($data){
            return $data;
        }

    }
}





if (!function_exists('get_automation_report_config')) {
    function get_automation_report_config($doc_id)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_ap_automation_report_configs');
        $CI->db->where('doc_id',$doc_id);
        $data = $CI->db->get()->row_array();

        if($data){
            return $data;
        }

    }
}

if (!function_exists('fetch_supplier_data')) {
    function fetch_supplier_data($supplierID)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('supplierAutoID',$supplierID);
        $data = $CI->db->get()->row_array();

        if($data){
            return $data;
        }

    }
}

if (!function_exists('fetch_amount_with_currency')) {
    function fetch_amount_with_currency($amount,$currency_str)
    {

       $currency_detail = explode('|',$currency_str);
       $action = '';

       if(isset($currency_detail[1])){
            $action = "<span class='text-bold'>$currency_detail[1] </span> &nbsp <span class='pull-right'>".number_format($amount,2).'</span>';
       }else{
            $action = $amount;
       }
       
       return $action;
    }
}



if (!function_exists('get_currency_master_by_id')) {
    function get_currency_master_by_id($currencyID)
    {

        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('currencyID',$currencyID);
        $data = $CI->db->get()->row_array();

        if($data){
            return $data;
        }

    }
}

