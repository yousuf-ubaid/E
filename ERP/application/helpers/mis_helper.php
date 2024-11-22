<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('get_config_type_str')) {
    function get_config_type_str($type)
    {
        if($type == 1){
            return 'Header';
        }elseif($type == 2){
            return 'Total';
        }elseif($type == 3){
            return 'Group Total';
        }elseif($type == 4){
            return 'Group Group Total';
        }else{
            return $type;
        }
        
    }
}

if (!function_exists('get_config_type2_str')) {
    function get_config_type2_str($type)
    {
        if($type == 1){
            return 'Income';
        }elseif($type == 2){
            return 'Expense';
        }
        
    }
}

if (!function_exists('get_config_row_detail_value')) {
    function get_config_row_detail_value($value)
    {
        if($value == 1){
            return 'Plus ( + )';
        }else{
            return 'Minus ( - )';
        }
        
    }
}

if (!function_exists('get_config_control_buttons')) {
    function get_config_control_buttons($id)
    {
        $action = '';
        $action = '
            <button onclick="add_config_records(\'' .$id.'\')" class="btn btn-sm btn-danger"><i class="fa fa-plus"></i></button> &nbsp
            <button onclick="edit_config_record(\'' .$id.'\')" class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></button> &nbsp
            <button onclick="delete_config_records(\'' .$id.'\')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        ';
        
        return $action;
    }
}

if (!function_exists('get_config_row_delete_detail_button')) {
    function get_config_row_delete_detail_button($id)
    {
        $action = '';
        $action = '
            <button onclick="delete_config_detail(\'' .$id.'\')" class="btn btn-danger"><i class="fa fa-trash"></i></button>
        ';
        
        return $action;
    }
}

if (!function_exists('get_chart_of_account_control')) {
    function get_chart_of_account_control($id)
    {
        $action = '';
        $action = '
            <button onclick="delete_chartof_records(\'' .$id.'\')" class="btn btn-danger"><i class="fa fa-trash"></i></button>
        ';
        
        return $action;
    }
}


if (!function_exists('get_config_control_report_buttons')) {
    function get_config_control_report_buttons($id)
    {
        $action = '';
        $action = '
            <button onclick="edit_config_report(\'' .$id.'\')" class="btn btn-success"><i class="fa fa-pencil"></i> Edit</button> &nbsp
            <button onclick="delete_config_report(\'' .$id.'\')" class="btn btn-danger"><i class="fa fa-trash"></i></button>
        ';
        
        return $action;
    }
}

if (!function_exists('get_mis_report_details')) {
    function get_mis_report_details($id)
    {
        $CI = &get_instance();
       
        $CI->db->from('srp_erp_mis_report');
        $CI->db->where('id',$id);
        $record = $CI->db->get()->row_array();

        return $record;
    }
}

if (!function_exists('get_headers_for_report')) {
    function get_headers_for_report($id,$type = 1)
    {
        $CI = &get_instance();
        $base_arr = array();
       
        $CI->db->from('srp_erp_mis_report_config_rows');
        $CI->db->where('header_type1',$type);
        $CI->db->where('report_id',$id);
        $record = $CI->db->get()->result_array();

        $base_arr[''] = 'Select Header';

        foreach($record as $value){

            $type2 = get_config_type2_str($value['header_type2']);

            $base_arr[$value['id']] = $type2.' - '.$value['cat_description'];

        }

        return $base_arr;
    }
}

if (!function_exists('get_config_row_details')) {
    function get_config_row_details($config_id,$mapped_id = null)
    {
        $CI = &get_instance();
        $base_arr = array();
       
        $CI->db->from('srp_erp_mis_report_config_details');
        $CI->db->where('config_row_id',$config_id);
        if($mapped_id){
            $CI->db->where('mapped_row_id',$mapped_id);
        }
        $record = $CI->db->get()->row_array();

        // $base_arr[''] = 'Select Header';

        // foreach($record as $value){

        //     $type2 = get_config_type2_str($value['header_type2']);

        //     $base_arr[$value['id']] = $type2.' - '.$value['cat_description'];

        // }

        return $record;
    }
}

if (!function_exists('get_config_row')) {
    function get_config_row($config_id)
    {
        $CI = &get_instance();
        $base_arr = array();
       
        $CI->db->from('srp_erp_mis_report_config_rows');
        $CI->db->where('id',$config_id);
        $record = $CI->db->get()->row_array();

        return $record;
    }
}


if (!function_exists('get_config_row_detail')) {
    function get_config_row_detail($config_id,$type = 1)
    {
        $CI = &get_instance();
        $base_arr = array();
        
        $ex_config_row = get_config_row($config_id);

        if($ex_config_row){
            if($type == 1){
                return get_config_type2_str($ex_config_row['header_type2']);
            }elseif($type == 2){
                return $ex_config_row['cat_id'];
            }else{
                return $ex_config_row['cat_description'];
            }
            
        }
    }
}


if (!function_exists('get_config_rows_mapped_count')) {
    function get_config_rows_mapped_count($config_id)
    {
        $CI = &get_instance();
        $base_arr = array();
        
        $ex_config_row = get_config_row($config_id);
        $count = 0;

        if($ex_config_row && $ex_config_row['header_type1'] == 1){
            
            $CI->db->from('srp_erp_mis_report_config_chartofaccounts');
            $CI->db->where('config_row_id',$config_id);
            $count = $CI->db->get()->num_rows();

        }

        return $count;
    }
}











