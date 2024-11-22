<?php

if (!function_exists('get_field_list_by_company')) {
    function get_field_list_by_company($type = null , $parentID = null) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($type){
            $CI->db->where('type', $type);
        }
        if($parentID){
            $CI->db->where('mapping_field_well_id', $parentID);
        }
       
        return $CI->db->get()->result_array();
       
    }

}

if (!function_exists('get_rig_hoist_list_by_company')) {
    function get_rig_hoist_list_by_company() /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('type', 3);
        return $CI->db->get()->result_array();
       
    }

}

if (!function_exists('get_activity_category_list_by_company')) {
    function get_activity_category_list_by_company() /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('type', 4);
        return $CI->db->get()->result_array();
       
    }

}


if (!function_exists('get_jobs_master_detail')) {
    function get_jobs_master_detail($id) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();

        $CI->db->select('sj.*,sjf.field_name,sjw.well_name,sjrig.rig_hoist_name,sjw.well_type_op,sjw.well_no_op');
        $CI->db->from('srp_erp_jobsmaster as sj');
        $CI->db->join('srp_erp_jobs_field_rig_masters as sjf','sj.field_id = sjf.id','left');
        $CI->db->join('srp_erp_jobs_field_rig_masters as sjw','sj.well_id = sjw.id','left');
        $CI->db->join('srp_erp_jobs_field_rig_masters as sjrig','sj.rig_hoist_id = sjrig.id','left');
        $CI->db->where('sj.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('sj.id', $id);

        return $CI->db->get()->row_array();
       
    }

}

if (!function_exists('get_jobs_header_detail')) {
    function get_jobs_header_detail($id) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();

        $CI->db->select('cm.customerAutoID, cm.customerName,cm.customerSystemCode,cm.customerCountry,cm.customerTelephone');
        $CI->db->from('srp_erp_customermaster as cm');
        /*$CI->db->join('srp_erp_jobsmaster as jm','cm.customerAutoID = jm.id','left');
        $CI->db->join('srp_erp_job_dailyreport as dr','cm.customerAutoID = dr.id','left');*/
        $CI->db->where('cm.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('cm.customerAutoID', $id);

        return $CI->db->get()->row_array();
       
    }

}

if (!function_exists('get_date_range_checked')) {
    function get_date_range_checked($id) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();

        $CI->db->select('dateFrom, dateTo');
        $CI->db->from('srp_erp_job_activityshift');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('id', $id);

        return $CI->db->get()->row_array();
       
    }

}

if (!function_exists('get_job_code')) {
    function get_job_code($id = null)
    {
        $companyID = current_companyID();
        $CI =& get_instance();

        $job_code = $CI->db->query("SELECT job_code FROM srp_erp_jobsmaster
                                       WHERE companyID={$companyID} && id={$id} ")->row_array();

        return $job_code;
    }
}

if (!function_exists('get_jobs_master_with_contract_details')) {
    function get_jobs_master_with_contract_details($id) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();

        $CI->db->select('sj.*,contract.*');
        $CI->db->from('srp_erp_jobsmaster as sj');
        $CI->db->join('srp_erp_contractmaster as contract','sj.contract_po_id = contract.contractAutoID','left');
        $CI->db->where('sj.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('sj.id', $id);

        return $CI->db->get()->row_array();
       
    }

}

if (!function_exists('get_contract_list')) {
    function get_contract_list($customerID,$drop = true,$amendmentOpen = null) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('srp_erp_contractmaster.*,srp_erp_document_amendments.status as amendment_status');
        $CI->db->from('srp_erp_contractmaster');
        $CI->db->where('srp_erp_contractmaster.companyID', $CI->common_data['company_data']['company_id']);
        if($amendmentOpen){
            $CI->db->join('srp_erp_document_amendments','srp_erp_contractmaster.currentAmedmentID = srp_erp_document_amendments.id','left');
            $CI->db->where('srp_erp_contractmaster.customerID', $customerID);
            $CI->db->where('srp_erp_contractmaster.approvedYN', 1);

        }else{
            $CI->db->where('customerID', $customerID);
            $CI->db->where('srp_erp_contractmaster.approvedYN', 1);
        }
       
        $results = $CI->db->get()->result_array();

        if($drop){
           foreach($results as $contract){
                if(is_null($contract['currentAmedmentID']) || (!is_null($contract['currentAmedmentID']) && $contract['amendment_status'] == 1)){
                    $base_arr[$contract['contractAutoID']] = $contract['contractCode'].' | '.$contract['referenceNo'];
                }
           }
        }

        return $base_arr;
       
    }

}

if (!function_exists('get_contract_detail')) {
    function get_contract_detail($contractID) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_contractmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('contractAutoID', $contractID);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_data_attendancemaster_id_details')) {
    function get_data_attendancemaster_id_details($data_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_empattendancemaster');
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('EmpAttMasterID', $data_id);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}





if (!function_exists('get_job_item_detail')) {
    function get_job_item_detail($itemAutoID) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_itemmaster');
        $CI->db->where('itemAutoID', $itemAutoID);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('check_job_item_exists')) {
    function check_job_item_exists($itemAutoID,$job_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_itemdetail');
        $CI->db->where('itemAutoID', $itemAutoID);
        $CI->db->where('job_id', $job_id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_job_item_record')) {
    function get_job_item_record($id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_itemdetail');
        $CI->db->where('id', $id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_job_items_list')) {
    function get_job_items_list($job_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_itemdetail');
        $CI->db->where('job_id', $job_id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}






if (!function_exists('get_employee_details_job')) {
    function get_employee_details_job($empID) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', $empID);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_employee_added_crew_details')) {
    function get_employee_added_crew_details($job_id,$empID,$taskID = null) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_crewdetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($taskID){
            $CI->db->where('id', $taskID);
        }else{
            $CI->db->where('empID', $empID);
            $CI->db->where('job_id', $job_id);
            // $CI->db->where('is_job_completed', 0);
        }
       
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_job_added_asset_details')) {
    function get_job_added_asset_details($job_id,$faID,$taskID = null) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_assetsdetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($taskID){
            $CI->db->where('id', $taskID);
        }else{
            $CI->db->where('faID', $faID);
            $CI->db->where('job_id', $job_id);
            $CI->db->where('is_job_completed', 0);
        }
       
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}


if (!function_exists('get_field_list')) {
    function get_field_list($drop = null) 
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('type', 1);
        $results = $CI->db->get()->result_array();

        if($drop){
            $base_arr[''] = 'Select Field';
            foreach($results as $field){
                $base_arr[$field['id']] = $field['field_name'];
            }
        }

        return $base_arr;
       
    }

}

if (!function_exists('get_rig_hoist_list')) {
    function get_rig_hoist_list($drop = null) 
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('type', 3);
        $results = $CI->db->get()->result_array();

        if($drop){
            $base_arr[''] = 'Select Rig / Hoist';
            foreach($results as $field){
                $base_arr[$field['id']] = $field['rig_hoist_name'];
            }
        }

        return $base_arr;
       
    }

}

if (!function_exists('get_activity_category')) {
    function get_activity_category($drop = null) 
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('type', 4);
        $results = $CI->db->get()->result_array();

        if($drop){
            $base_arr[''] = 'Select Activity Category';
            foreach($results as $field){
                $base_arr[$field['id']] = $field['rig_hoist_name'];
            }
        }

        return $base_arr;
       
    }

}

if (!function_exists('get_well_list')) {
    function get_well_list($filed_id,$drop = null) 
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('type', 2);
        $CI->db->where('mapping_field_well_id', $filed_id);
        $results = $CI->db->get()->result_array();

        if($drop){
            foreach($results as $field){
                $base_arr[$field['id']] = $field['well_name'];
            }
        }

        return $base_arr;
       
    }

}

if (!function_exists('get_add_crew_actions')) {
    function get_add_crew_actions($empID = null,$designation = null) 
    {
        $CI =& get_instance();
        $html = '';
        $html .= '<a onclick=\'add_employee_job("'.trim($empID ?? '').'","'.trim($designation ?? '').'")\'><span title="Add" rel="tooltip" class="glyphicon glyphicon-plus"></span></a>';
        return $html;
       
    }

}
/*
if (!function_exists('get_add_crew_actions')) {
    function get_add_crew_actions($empID = null,$designation = null) 
    {
        $CI =& get_instance();
        $html = '';
        $html .= '<div class="text-center"><input type="checkbox" class="checkbox" value="'.trim($empID).'" name="crew_tbl_id[]"></div>';
        return $html;
       
    }

}
*/

if (!function_exists('getCheckBox')) {
    function getCheckBox($type,$checkStatus,$id) 
    {
        $CI =& get_instance();
        $html = '';
        $checked = '';

        if($checkStatus == 1){
            $checked = 'checked';
        }
       
        if($type == '1'){
            $html .= '<input type="checkbox" class="isStandby" '.$checked.' value="1" name="competency" onchange=\'change_check_box(this,'.trim($id).')\'>';
        }elseif($type == 2){
            $html .= '<input type="checkbox" class="isStandby" '.$checked.' value="1" name="training"  onchange=\'change_check_box(this,'.trim($id).')\'>';
        }elseif($type == 3){
            $html .= '<input type="checkbox" class="isStandby" '.$checked.' value="1" name="ssc" onchange=\'change_check_box(this,'.trim($id).')\'>';
        }elseif($type == 4){
            $html .= '<input type="checkbox" class="isStandby" '.$checked.' value="1" name="maintenance" onchange=\'change_check_box(this,'.trim($id).')\'>';
        }
        
        return $html;
       
    }

}

if (!function_exists('getCheckBoxActivity')) {
    function getCheckBoxActivity($empID = null) 
    {
        $CI =& get_instance();
        $html = '';

        //$html .= '<div class="text-center"><input type="checkbox" class="checkbox" value="'.$id.'" name="crew_tbl_id[]" ></div>';
        $html .= '<a onclick=\'crew_select_add("'.trim($empID).'")\'><span title="Add" rel="tooltip" class="glyphicon glyphicon-plus glyphicon-plus-btn"></span></a>';
        
        return $html;
       
    }

}

if (!function_exists('getCheckBoxActivityCrew')) {
    function getCheckBoxActivityCrew($empID = null) 
    {
        $CI =& get_instance();
        $html = '';

        //$html .= '<div class="text-center"><input type="checkbox" class="checkbox" value="'.$id.'" name="crew_tbl_id[]" ></div>';
        $html .= '<a onclick=\'saveCrewForActivity("'.trim($empID).'")\'><span title="Add" rel="tooltip" class="glyphicon glyphicon-plus glyphicon-plus-btn"></span></a>';
        
        return $html;
       
    }

}

if (!function_exists('getCheckBoxActivityStandard')) {
    function getCheckBoxActivityStandard($empID = null) 
    {
        $CI =& get_instance();
        $html = '';

        //$html .= '<div class="text-center"><input type="checkbox" class="checkbox" value="'.$id.'" name="crew_tbl_id[]" ></div>';
        $html .= '<a onclick=\'contract_item_select_add_billing("'.trim($empID).'")\'><span title="Add" rel="tooltip" class="glyphicon glyphicon-plus glyphicon-plus-btn"></span></a>';
        
        return $html;
       
    }

}





if (!function_exists('get_add_job_assets_actions')) {
    function get_add_job_assets_actions($faID = null) 
    {
        $CI =& get_instance();
        $html = '';
        $html .= '<a onclick=\'add_asset_for_job("'.trim($faID).'")\'><span title="Add" rel="tooltip" class="glyphicon glyphicon-plus"></span></a>';
        return $html;
       
    }

}

if (!function_exists('get_action_notify_ele')) {
    function get_action_notify_ele($value) 
    {
        $CI =& get_instance();
        $html = '';
        if($value == 1){
            $html .= '<div class="text-center"><span class="label label-success">&nbsp;</span></div>';
        }else{
            $html .= '<div class="text-center"><span class="label label-danger">&nbsp;</span></div>';
        }
        
        return $html;
       
    }

}

if (!function_exists('get_total_local_amount')) {
    function get_total_local_amount($id) 
    {
        $CI =& get_instance();
        $value_str = '';

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_itemdetail');
        $CI->db->join('srp_erp_jobsmaster','srp_erp_job_itemdetail.job_id = srp_erp_jobsmaster.id','left');
        $CI->db->where('srp_erp_job_itemdetail.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('job_id', $id);
     
        $results = $CI->db->get()->result_array();

        $total_item = 0;
        $transactionCurrency = '';
        foreach($results as $item){
            $transactionCurrency = $item['localCurrencyCode'];
            $total_item += $item['transactionAmount'];
        }

        $value_str = '<b>'.$transactionCurrency.'</b> '.number_format($total_item,2);

        return $value_str;
       
    }

}




if (!function_exists('get_action_job_table')) {
    function get_action_job_table($job_id,$config = null,$table = null) 
    {
        $CI =& get_instance();
        $html = '';

        if($config){
            if($table == 'srp_erp_job_shift_crewdetail'){
                $html .= '<a class="btn btn-danger" onclick=\'delete_added_record("'.$job_id.'","'.$table.'")\'><i class="fa fa-trash"></i> </a>';
            }else{
                $html .= '<a class="btn btn-default" onclick=\'edit_added_record("'.$job_id.'","'.$table.'")\'><i class="fa fa-pencil"></i> </a>&nbsp &nbsp';
                $html .= '<a class="btn btn-danger" onclick=\'delete_added_record("'.$job_id.'","'.$table.'")\'><i class="fa fa-trash"></i> </a>';
            }
            
        }else{

            // $html .= '<a class="btn btn-primary-new" onclick=\'fetchPage("system/sales/master/jobs_create","Jobs","Create Jobs","","'.trim($job_id).'")\'><i class="fa fa-cog"></i> Config </a>';
            $html .= '<a onclick=\'fetchPage("system/sales/master/jobs_create","Jobs","Create Jobs","","'.trim($job_id).'")\'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
            $html .= '<a target="_blank" onclick="documentPageView_modal(\'CJOB\',\'' . $job_id . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        
        }
        
        return $html;
       
    }

}

if (!function_exists('get_action_activity_table')) {
    function get_action_activity_table() 
    {
        $CI =& get_instance();
        $html = '';
        $html .= '<a class="btn btn-danger" onclick=""><i class="fa fa-trash"></i> </a>';
        
        return $html;
       
    }

}





if (!function_exists('get_job_invoice_status')) {
    function get_job_invoice_status($status = null,$id = null) 
    {
       
        $CI =& get_instance();

        //header
        $results = get_checklist_header_records($id,1);
        $is_pending = null;

        if(count($results) > 0 ){
            $is_pending = true;
        }

        $html = '';
        if($status == 1){
            $html .= '<div class="text-center"><span class="badge badge-success" style="padding:5px 10px;background-color:#696CFF;margin:10px;"> Schedule </span></div>';
        }elseif($status == 2){
            $html .= '<div class="text-center"><span class="badge badge-success" style="padding:5px 10px;background-color:#f49025;"> In Progress </span></div>';
        }elseif($status == 3){
            $html .= '<div class="text-center"><span class="badge badge-success" style="padding:5px 10px;background-color:#f96957;"> Hold </span></div>';
        }elseif($status == 4){
            $html .= '<div class="text-center"><span class="badge badge-success" style="padding:5px 10px;background-color:#2ad688;"> Completed </span></div>';
        }

        if($is_pending){
            $html .= '<div class="text-center"><span class="badge badge-success" style="padding:5px 10px;background-color:red;"><i class="fa fa-ban"></i> Checklist Pending </span></div>';
        }

        return $html;
       
    }

}

if (!function_exists('checklist_status_job')) {
    function checklist_status_job($completeYN)
    {
        $status = '';
        if ($completeYN==1) {
            $status .= '<div class="text-center"><span class="badge badge-success" style="padding:5px 10px;background-color:#696CFF;"> trying </span></div>';
        
        }else  {
            $status .= '<div class="text-center"><span class="badge badge-success" style="padding:5px 10px;background-color:#696CFF;"> nit trying me  </span></div>';
        }
        return $status;
    }
}





if (!function_exists('get_add_crew_status')) {
    function get_add_crew_status() 
    {
        $CI =& get_instance();
        $html = '';
        $html .= '<div class="text-center"><span class="label label-success">&nbsp;</span></div>';
        return $html;
       
    }

}

if (!function_exists('get_is_yes_no')) {
    function get_is_yes_no($value) 
    {
        $CI =& get_instance();

        if($value == 1){
            return 'Yes';
        }else{
            return 'No';
        }
    }

}





if (!function_exists('get_job_assets_status')) {
    function get_job_assets_status($faID) 
    {
        $CI =& get_instance();
        $html = '';
        $html .= '<div class="text-center"><span class="label label-success">&nbsp;</span></div>';
        return $html;
       
    }

}



if (!function_exists('getDateFromField')) {
    function getDateFromField($dateFrom = null,$faID = null) 
    {
        $CI =& get_instance();
        $default_date = '';
        if($dateFrom){
            $default_date = str_replace(' ','T',$dateFrom);
        }

        $html = '';

        if($faID){

            // $html .= '
            // <div class="input-group datepic">sd1
            //     <div class="input-group-addon">
            //     <input type="text"  class="form-control reportFromDate_x" value="'.$default_date.'" onchange="change_dateFrom_asset($(this))">
            // </div>
            // ';

            $html .= '
            <input type="text" class="form-control assestFromDate_Def" value="'.$default_date.'" onchange="change_dateFrom_asset($(this))">
            ';

        }else{
            // $html .= '
            //     <div class="input-group datepic">sdd
            //         <div class="input-group-addon">
            //         <input type="datetime-local" class="form-control" value="'.$default_date.'" onchange="change_dateFrom($(this))">
            //     </div>
            // ';

            $html .= '
            <input type="text" class="form-control assestFromDate_Def" value="'.$default_date.'" onchange="change_dateFrom($(this))">
            ';
        }
       
        return $html;
       
    }

}

if (!function_exists('getDateToField')) {
    function getDateToField($dateTo = null,$faID = null) 
    {
        $CI =& get_instance();

        $default_date = '';
        if($dateTo){
            $default_date = str_replace(' ','T',$dateTo);
        }

        $html = '';

        if($faID){

            // $html .= '
            //     <div class="input-group datepic">
            //         <div class="input-group-addon">
            //         <input type="datetime-local" id="toDate"  class="form-control" value="'.$default_date.'" onchange="change_dateTo_asset($(this))">
            //     </div>
            // ';

            $html .= '
            <input type="text" class="form-control assestoDate_Def" value="'.$default_date.'" onchange="change_dateTo_asset($(this))">
            ';

        }else{

            $html .= '
                <div class="input-group datepic">
                    <div class="input-group-addon">
                    <input type="datetime-local" id="toDate"  class="form-control" value="'.$default_date.'" onchange="change_dateTo($(this))">
                </div>
            ';

        }
       
        return $html;
       
    }

}

if (!function_exists('getDayWiseDifference')) {
    function getDayWiseDifference($dateFrom = null, $dateTo = null,$type = null) 
    {
        $CI =& get_instance();

        if($dateFrom && $dateTo){
            $dateFromT = str_replace(' ','T',trim($dateFrom));
            $dateToT = str_replace(' ','T',trim($dateTo));
                    
            $date1 = new DateTime($dateFromT);
            $date2 = new DateTime($dateToT);
    
            // The diff-methods returns a new DateInterval-object...
            $diff = $date2->diff($date1);

            $hours = $diff->h;
            $hours = $hours + ($diff->days*24);
            
            if($type == 'time'){
                return $diff->format('%H:%i:%s');
            }

            if($type == 'hours'){
                return $diff->format('%H');
            }

            if($type == 'hours_minute'){
                return $hours.'.'.$diff->format('%I Hours');
            }

            if($type == 'hours_minute_num'){
                return $hours.'.'.$diff->format('%I');
            }

            if($type == 'hours_minute_only'){
                return $hours.'.'.(($diff->format('%I')/60)*100);
            }

            // Call the format method on the DateInterval-object
            if($type){
                return $diff->format('%a|%h');
            }else{
                return $diff->format('%a Days %h Hours');
            }
            
    
        }else{
            return '';
        }
       
       
    }
}



if (!function_exists('getJobStatus')) {
    function getJobStatus($dateFrom = null,$dateTo = null) 
    {
        $CI =& get_instance();
        $today_date = date('Y-m-d H:i:s');
        $html = '';

        if($dateFrom && $dateTo){
            if(($today_date > $dateFrom) && ($today_date >= $dateTo)){
                //both are below 
                $html .= '
                    <span class="badge badge-success" style="padding:5px 10px;background-color:#2ad688;"> Completed </span>
                ';
            }elseif(($today_date > $dateFrom) && ($today_date < $dateTo)){
                //ongoing
                $html .= '
                    <span class="badge badge-primary" style="padding:5px 10px;background-color:#c74421;"> Ongoing </span>
                ';
            }elseif(($today_date < $dateFrom)){
                //ongoing
                $html .= '
                    <span class="badge badge-primary" style="padding:5px 10px;background-color:#782a74;"> Scheduled </span>
                ';
            }
        }
       
        return $html;
       
    }

}



if (!function_exists('get_added_actions')) {
    function get_added_actions($job_id,$table = null) 
    {
        $CI =& get_instance();
        $html = '';
        // $html .= '<button class="btn btn-danger" onclick="add_delete_crew()"><i class="fa fa-trash"></i> </button>';
        $html .= '<a onclick=\'delete_added_record("'.$job_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete" style="color:rgb(209, 91, 71);"></span></a>';
        
        if($table == 'srp_erp_job_activitydetail'){
            $html .= '&nbsp &nbsp <a onclick=\'edit_added_record("'.$job_id.'","activity")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn" data-original-title="Delete"></span></a>';
        }
        
        return $html;
    }

}

if (!function_exists('get_added_actions_shift')) {
    function get_added_actions_shift($shift_id,$table = null,$confirmedYN = null,$sales_order_id = null) 
    {
        $CI =& get_instance();
        $html = '';

        $html .= '<div style="text-left">';

        if($table == 'billing'){

            if($confirmedYN == 1){
                if($sales_order_id){
                    $html .= '<a onclick=\'view_sales_order("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-ok-circle" data-original-title="View Sales Order"></span></a>';
                } else {
                    $html .= '<a onclick=\'generate_sales_order("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-transfer" data-original-title="Sales Order"></span></a>';
                }
               
                $html .= '<a onclick=\'open_billing_detail_report("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';
                $html .= '<a onclick=\'print_billing("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>';

            }else{

                $html .= '<a onclick=\'add_billing_detail("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-plus" data-original-title="Add Billing"></span></a>';
               
                $html .= '&nbsp &nbsp;<a onclick=\'open_billing_detail_report("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'print_billing("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'edit_billing_detail("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit Billing"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'delete_added_record("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a>';
            }

            

        }else{
            $html .= '<a onclick=\'add_activity("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Activity"></span></a>';
            $html .= '&nbsp;&nbsp;<a onclick=\'delete_added_record("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a>';
        }
       
        // $html .= '<a class="btn btn-danger" onclick=\'delete_added_record("'.$job_id.'","'.$table.'")\'><i class="fa fa-trash"></i> </a>';
        return $html;
    }

}

if (!function_exists('get_added_actions_shift_standard')) {
    function get_added_actions_shift_standard($shift_id,$table = null,$confirmedYN = null,$sales_order_id = null) 
    {
        $CI =& get_instance();
        $html = '';

        $html .= '<div style="text-left">';

        if($table == 'billing'){

            if($confirmedYN == 1){
                if($sales_order_id){
                    $html .= '<a onclick=\'view_sales_order("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-ok-circle" data-original-title="View Sales Order"></span></a>';
                } else {
                    $html .= '<a onclick=\'generate_sales_order("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-transfer" data-original-title="Sales Order"></span></a>';
                }
               
                $html .= '<a onclick=\'open_billing_detail_report_standard("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';
                $html .= '<a onclick=\'print_billing_standard("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>';

            }else{

                $html .= '<a onclick=\'add_billing_detail_standard("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-plus" data-original-title="Add Billing"></span></a>';
               
                $html .= '&nbsp &nbsp;<a onclick=\'open_billing_detail_report_standard("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'print_billing_standard("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'edit_billing_detail("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit Billing"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'delete_added_record("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a>';
            }

            

        }else{
            $html .= '<a onclick=\'add_activity("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Activity"></span></a>';
            $html .= '&nbsp;&nbsp;<a onclick=\'delete_added_record("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a>';
        }
       
        // $html .= '<a class="btn btn-danger" onclick=\'delete_added_record("'.$job_id.'","'.$table.'")\'><i class="fa fa-trash"></i> </a>';
        return $html;
    }

}

if (!function_exists('get_added_actions_shift_modify')) {
    function get_added_actions_shift_modify($shift_id,$table = null,$confirmedYN = null,$sales_order_id = null) 
    {
        $CI =& get_instance();
        $html = '';

        $html .= '<div style="text-left">';

        if($table == 'billing'){

            if($confirmedYN == 1){
                if($sales_order_id){
                    $html .= '<a onclick=\'view_sales_order("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-ok-circle" data-original-title="View Sales Order"></span></a>';
                } else {
                    $html .= '<a onclick=\'generate_sales_order("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-transfer" data-original-title="Sales Order"></span></a>';
                }
               
                $html .= '<a onclick=\'open_billing_detail_report_modify("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';
                $html .= '<a onclick=\'print_billing_modify("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>';

            }else{

                $html .= '<a onclick=\'add_billing_detail_modify("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-plus" data-original-title="Add Billing"></span></a>';
               
                $html .= '&nbsp &nbsp;<a onclick=\'open_billing_detail_report_modify("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'print_billing_modify("'.$shift_id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'edit_billing_detail("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit Billing"></span></a>';
                $html .= '&nbsp &nbsp;<a onclick=\'delete_added_record("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a>';
            }

            

        }else{
            $html .= '<a onclick=\'add_activity("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Activity"></span></a>';
            $html .= '&nbsp;&nbsp;<a onclick=\'delete_added_record("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a>';
        }
       
        // $html .= '<a class="btn btn-danger" onclick=\'delete_added_record("'.$job_id.'","'.$table.'")\'><i class="fa fa-trash"></i> </a>';
        return $html;
    }

}


if (!function_exists('edit_standard_billing_qty')) {
    function edit_standard_billing_qty($id,$qty) 
    {
        $CI =& get_instance();
        $html = '';

        $html .= '
            
                <input type="number" id="" class="form-control" value="'.$qty.'" onchange="save_standard_billing_item_qty(this.value,' . $id . ')">
           
            ';
        return $html;
    }
}

if (!function_exists('edit_standard_billing_from_date')) {
    function edit_standard_billing_from_date($id,$date) 
    {
        $CI =& get_instance();
        $html = '';

        $html .= '
                <input type="text" class="form-control d_from_standard"  name="fromDate" value="'.$date.'" onchange="save_standard_billing_date(this.value,' . $id . ',1)">
            ';

            // $html .= '
            // <div class="input-group datepic">
            //     <div class="input-group-addon">
            //     <input type="datetime-local" id="fromDate" class="form-control" value="'.$date.'" onchange="save_standard_billing_date(this.value,' . $id . ',1)">
            // </div>
            // ';
        return $html;
    }

}

if (!function_exists('edit_standard_billing_to_date')) {
    function edit_standard_billing_to_date($id,$date) 
    {
        $CI =& get_instance();
        $html = '';

        $html .= '
                <input type="text" class="form-control d_from_standard"  name="fromDate" value="'.$date.'" onchange="save_standard_billing_date(this.value,' . $id . ',2)">
            ';

            // $html .= '
            // <div class="input-group datepic">
            //     <div class="input-group-addon">
            //     <input type="datetime-local" id="toDate" class="form-control" value="'.$date.'" onchange="save_standard_billing_date(this.value,' . $id . ',2)">
            // </div>
            // ';
        return $html;
    }

}

if (!function_exists('get_invoice_status')) {
    function get_invoice_status($shift_id,$table = null,$sales_id = null) 
    {
        $CI =& get_instance();
        $html = '';

        if($table == 'billing'){

            if($sales_id){

                $html .= '<span class="badge badge-success text-center pull-center" style="background-color:green;">Sales order Generated</span>';
      
            }

        }
        return $html;
    }

}

if (!function_exists('get_visitor_status')) {
    function get_visitor_status($status) 
    {
        $CI =& get_instance();
        $html = '';

        if($status == 0){
            $html .= '<span class="badge badge-success text-center pull-center" style="background-color:green;">Open</span>';
        }elseif($status == 1){
            $html .= '<span class="badge badge-success text-center pull-center" style="background-color:green;">View</span>';
        }elseif($status == 1){
            $html .= '<span class="badge badge-success text-center pull-center" style="background-color:green;">Reponded</span>';
        }
        
        return $html;
      
    }

}

if (!function_exists('get_visitor_log_actions')) {
    function get_visitor_log_actions($id) 
    {
        $CI =& get_instance();
        $html = '';

        $html .= '<a onclick=\'get_visitor_log_link("'.$id.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-envelope" data-original-title="Get Link"></span></a>';
        
        return $html;
      
    }

}




if (!function_exists('get_check_list_tbl')) {
    function get_check_list_tbl($check_list) 
    {
        $CI =& get_instance();
        $html = '';

        $checklist_arr = explode(',',$check_list);    

        foreach($checklist_arr as $check){
            $result = checkActivityConfirmation($check); //check is confirmed
            if(is_array($result) && $result['is_confirmed'] == '1'){
                $btnType="btn-success";
            }else{
                $btnType="btn-warning";
            }
            $html .= '<a class="btn '.$btnType.' size-xs mb-1 checklist-btn" onclick=\'load_checklist_edit("'.$check.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-list" data-original-title="View"></span> </a> &nbsp';
        }
       
        // $html .= '<a class="btn btn-danger" onclick=\'delete_added_record("'.$job_id.'","'.$table.'")\'><i class="fa fa-trash"></i> </a>';
        return $html;
    }

}

if (!function_exists('checkActivityConfirmation')) {
    function checkActivityConfirmation($id) 
    {
        $CI =& get_instance();

        $CI->db->select('is_confirmed');
        $CI->db->from('srp_erp_op_checklist_header');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('id', $id);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}


if (!function_exists('get_visitors_log_request')) {
    function get_visitors_log_request($id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_visitor_log_link');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('id', $id);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}


if (!function_exists('get_added_actions_reports')) {
    function get_added_actions_reports($shift_id,$table = null) 
    {
        $CI =& get_instance();
        $html = '';

        if($table == 'billing'){
            $html .= '<a class="mb-1" onclick=\'delete_added_record("'.$shift_id.'","srp_erp_job_billing_detail")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a> &nbsp';
        }else{
             $html .= '<a onclick=\'edit_dailyreport_detail("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span></a> &nbsp';
            $html .= '<a onclick=\'open_daily_job_report("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="Edit"></span></a> &nbsp';
            $html .= '<a onclick=\'print_daily_job_report("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a> &nbsp';
            $html .= '<a onclick=\'print_well_report("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a> &nbsp';
            $html .= '<a onclick=\'print_work_over_rig_daily_report("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a> &nbsp';
            //$html .= '<a onclick=\'print_work_over_rig_daily_report("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a> &nbsp';
            $html .= '<a onclick=\'delete_added_record("'.$shift_id.'","'.$table.'")\'><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="Delete"></span></a> &nbsp';
        }
        
        return $html;
    }

}



if (!function_exists('get_employee_added_job_task_details')) {
    function get_employee_added_job_task_details($empID,$taskID = null) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_crewdetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($taskID){
            $CI->db->where('id !=', $taskID);
        }
        $CI->db->where('empID', $empID);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_contract_crew_record')) {
    function get_contract_crew_record($id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_contractcrew');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('id', $id);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}


if (!function_exists('get_checklist_header_record')) {
    function get_checklist_header_record($header_id) 
    {
        $CI =& get_instance();      

        $CI->db->select('sch.*,scm.name as checklist_name,sjw.well_name,sjrig.rig_hoist_name,sjw.well_type_op,sjw.well_no_op');
        $CI->db->from('srp_erp_op_checklist_header as sch');
        $CI->db->join('srp_erp_op_checklist_master as scm','sch.master_id = scm.id','left');
        $CI->db->join('srp_erp_jobs_field_rig_masters as sjf','sch.field = sjf.id','left');
        $CI->db->join('srp_erp_jobs_field_rig_masters as sjw','sch.well = sjw.id','left');
        $CI->db->join('srp_erp_jobs_field_rig_masters as sjrig','sch.rig = sjrig.id','left');

        $CI->db->where('sch.companyID', $CI->common_data['company_data']['company_id']);
        if($header_id){
            $CI->db->where('sch.id', $header_id);
        }
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('update_table_record_field')) {
    function update_table_record_field($id,$value,$filed,$table) 
    {
        $CI =& get_instance();
        $data = array();

        $data[$filed] = $value;

        $res = $CI->db->where('id',$id)->update($table,$data);
        
        return TRUE;
       
    }

}



if (!function_exists('get_net_amount')) {
    function get_net_amount($header_id) 
    {
        $CI =& get_instance();

        $CI->db->select('SUM(total_amount) as total_amount');
        $CI->db->from('srp_erp_job_billing_detail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($header_id){
            $CI->db->where('billing_header', $header_id);
        }
        $results = $CI->db->get()->row_array();

        return number_format(($results['total_amount'] ?? 0),2);
       
    }

}

if (!function_exists('get_billing_detail_record')) {
    function get_billing_detail_record($header_id,$dateFrom=null,$dateTo=null) 
    {
        $CI =& get_instance();

        $CI->db->select('job.*,price.itemAutoID');
        $CI->db->from('srp_erp_job_billing_detail as job');
        $CI->db->join('srp_erp_contractdetails as price','job.price_id = price.contractDetailsAutoID','left');
        $CI->db->where('job.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('job.billing_header', $header_id);

        if($dateFrom && $dateTo){
            $CI->db->where('dateFrom >=', $dateFrom);
            $CI->db->where('dateTo <=', $dateTo);
        }
        
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_billing_detail_record_with_date_group')) {
    function get_billing_detail_record_with_date_group($header_id,$dateFrom=null,$dateTo=null) 
    {
        $CI =& get_instance();

        $CI->db->select('job.*,price.itemAutoID');
        $CI->db->from('srp_erp_job_billing_detail as job');
        $CI->db->join('srp_erp_contractdetails as price','job.price_id = price.contractDetailsAutoID','left');
        $CI->db->where('job.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('job.billing_header', $header_id);
        $CI->db->group_by('job.dateFrom');
        $CI->db->group_by('job.dateTo');

        // if($dateFrom && $dateTo){
        //     $CI->db->where('dateFrom >=', $dateFrom);
        //     $CI->db->where('dateTo <=', $dateTo);
        // }
        
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_defined_prices_for_contract')) {
    function get_defined_prices_for_contract($contract_id,$type = 2) 
    {
        $CI =& get_instance();

        $CI->db->select('details.*');
        $CI->db->from('srp_erp_contractdetails as details');
        $CI->db->where('details.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('details.contractAutoID', $contract_id);
        // $CI->db->where('details.pOrService', $type);

        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_defined_prices_for_contract_with_category_group')) {
    function get_defined_prices_for_contract_with_category_group($contract_id,$type = 2) 
    {
        $CI =& get_instance();

        $CI->db->select('details.*,cat.categoryName');
        $CI->db->from('srp_erp_contractdetails as details');
        $CI->db->join('srp_erp_op_contract_details_category_list as cat','cat.autoID = details.categoryGroupID','left');
        $CI->db->where('details.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('details.contractAutoID', $contract_id);
        // $CI->db->where('details.pOrService', $type);

        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}



if (!function_exists('get_job_defined_shifts')) {
    function get_job_defined_shifts($job_id,$dateFrom,$dateTo) 
    {
        $CI =& get_instance();

        $CI->db->select('shift.*');
        $CI->db->from('srp_erp_job_activityshift as shift');
        $CI->db->where('shift.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('shift.job_id', $job_id);
        $CI->db->where('dateFrom >=', $dateFrom);
        $CI->db->where('dateTo <=', $dateTo);
        
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}




if (!function_exists('get_billing_master_record')) {
    function get_billing_master_record($header_id) 
    {
        $CI =& get_instance();

        $CI->db->select('');
        $CI->db->from('srp_erp_job_billing');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($header_id){
            $CI->db->where('id', $header_id);
        }
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('check_daily_report_record')) {
    function check_daily_report_record($header_id) 
    {
        $CI =& get_instance();

        $CI->db->select('');
        $CI->db->from('srp_erp_job_dailyreport_detail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($header_id){
            $CI->db->where('activity_id', $header_id);
        }
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('check_activity_report_records')) {
    function check_activity_report_records($header_id) 
    {
        $CI =& get_instance();

        $CI->db->select('');
        $CI->db->from('srp_erp_job_activitydetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($header_id){
            $CI->db->where('shift_id', $header_id);
        }
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}



if (!function_exists('get_checklist_master_record')) {
    function get_checklist_master_record($template_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_checklist_master');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($template_id){
            $CI->db->where('id', $template_id);
        }
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_checklist_questions_details')) {
    function get_checklist_questions_details($shift_id) 
    {
        $CI =& get_instance();

        $results = $CI->db->query("
            select * from srp_erp_job_dailyreport_detail
            where activity_id IN (select id from srp_erp_job_activitydetail where shift_id = $shift_id )")->row_array();

        return $results;
       
    }

}

if (!function_exists('get_checklist_questions_response_details')) {
    function get_checklist_questions_response_details($master_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_checklist_details');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('header_id', $master_id);
      
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_checklist_questions_response')) {
    function get_checklist_questions_response($id,$header_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_checklist_details');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('header_id', $header_id);
        $CI->db->where('detail_id', $id);
      
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_checklist_added_to_contract')) {
    function get_checklist_added_to_contract($contract_id,$type) 
    {
        $CI =& get_instance();

        $CI->db->select('smc.*,som.name as checklist_name');
        $CI->db->from('srp_erp_op_module_contractchecklist as smc');
        $CI->db->join('srp_erp_op_checklist_master as som','som.id = smc.checklistID','left');
        $CI->db->where('smc.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('smc.callingCode', $type);
        $CI->db->where('smc.contractAutoID', $contract_id);
      
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_checklist_added_to_contract_for_add_users')) {
    function get_checklist_added_to_contract_for_add_users($contract_id) 
    {
        $CI =& get_instance();

        $CI->db->select('smc.*,som.name as checklist_name');
        $CI->db->from('srp_erp_op_module_contractchecklist as smc');
        $CI->db->join('srp_erp_op_checklist_master as som','som.id = smc.checklistID','left');
        $CI->db->where('smc.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('smc.contractAutoID', $contract_id);
        $CI->db->where('smc.callingCode !=', NULL);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_activity_crew_record')) {
    function get_activity_crew_record($emp_id,$shift_id,$job_id) 
    {
        $CI =& get_instance();

        $CI->db->select('smc.*');
        $CI->db->from('srp_erp_job_shift_crewdetail as smc');
        $CI->db->where('smc.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('smc.shift_id', $shift_id);
        $CI->db->where('smc.empID', $emp_id);
        $CI->db->where('smc.job_id', $job_id);
      
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}




if (!function_exists('get_activity_name')) {
    function get_activity_name($type) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('id', $type);
        $CI->db->where('type', 4);
      
        $results = $CI->db->get()->row_array();

        return isset($results['rig_hoist_name']) ? $results['rig_hoist_name'] : '';
       
    }

}

if (!function_exists('create_checklist_header_record')) {
    function create_checklist_header_record($checklistID,$contract_id,$job_id) 
    {
        $CI =& get_instance();
        $data = array();

        $job_master = get_jobs_master_detail($job_id);

        $contract_master = get_contract_detail($contract_id);

        $checklist_master = get_checklist_master_record($checklistID);

        $data['companyID'] =  $CI->common_data['company_data']['company_id'];
        $data['segment_id'] = $contract_master['segmentID'];
        $data['master_id'] = $checklistID;
        $data['doc_name'] = $checklist_master['name'];
        $data['doc_date'] = current_date();
        $data['active'] = 1;
        $data['job_id'] = $job_id;
        $data['rig'] = $job_master['rig_hoist_id'];
        $data['well'] = $job_master['well_id'];
        $data['field'] = $job_master['field_id'];

        $res = $CI->db->insert('srp_erp_op_checklist_header',$data);
        
        $last_id = $CI->db->insert_id();

        return $last_id;
       
    }

}

if (!function_exists('get_checklist_status_job')) {
    function get_checklist_status_job($job_id) 
    {
        $CI =& get_instance();

        $CI->db->select('is_confirmed');
        $CI->db->from('srp_erp_op_checklist_header');
        $CI->db->where('is_confirmed IS NULL');
        $CI->db->where('job_id', $job_id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_checklist_header_records')) {
    function get_checklist_header_records($job_id,$is_confirmed = null) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_checklist_header');
        if($is_confirmed){
            $CI->db->where('is_confirmed IS NULL');
        }
        $CI->db->where('job_id', $job_id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}


if (!function_exists('get_job_crew_list')) {
    function get_job_crew_list($job_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_crewdetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('job_id', $job_id);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_job_crew_record_id')) {
    function get_job_crew_record_id($id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_crewdetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('id', $id);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_job_activity_details')) {
    function get_job_activity_details($job_id,$shift_id = null,$drop = null) 
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_activitydetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('job_id', $job_id);
    
        if($shift_id){
            $CI->db->where('shift_id', $shift_id);
        }

        $results = $CI->db->get()->result_array();

        if($drop){
            $base_arr[0] = 'Select Activity';
            foreach($results as $activity){
                $base_arr[$activity['id']] = $activity['description'];
            }

            return $base_arr;
        }

        return $results;
       
    }

}

if (!function_exists('get_job_activity_details_between_date')) {
    function get_job_activity_details_between_date($job_id,$dateFrom = null,$dateTo = null,$except = null) 
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('srp_erp_job_activitydetail.*');
        $CI->db->from('srp_erp_job_activitydetail');
        $CI->db->where('srp_erp_job_activitydetail.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_job_activitydetail.job_id', $job_id);

        if($except){
            $CI->db->join('srp_erp_job_billing_detail', 'srp_erp_job_activitydetail.id = srp_erp_job_billing_detail.activity_id AND srp_erp_job_activitydetail.job_id = srp_erp_job_billing_detail.job_id','left');
            $CI->db->where('srp_erp_job_billing_detail.id IS NULL', null);
        }
       
        $CI->db->where('srp_erp_job_activitydetail.dateFrom >=', $dateFrom);
        $CI->db->where('srp_erp_job_activitydetail.dateTo <=', $dateTo);

        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_job_activity_for_id')) {
    function get_job_activity_for_id($job_id,$activity_id = null) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_activitydetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('job_id', $job_id);
        $CI->db->where('id', $activity_id);
        $results = $CI->db->get()->row_array();
        return $results;

    }

}

if (!function_exists('get_asset_added_job_details')) {
    function get_asset_added_job_details($faID,$taskID = null) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_assetsdetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        if($taskID){
            $CI->db->where('id !=', $taskID);
        }
        $CI->db->where('faID', $faID);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_fa_assets_details_job')) {
    function get_fa_assets_details_job($faID) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_fa_asset_master');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('faID', $faID);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_added_assets_details')) {
    function get_added_assets_details($faID,$job_id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_assetsdetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('faID', $faID);
        $CI->db->where('job_id', $job_id);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_added_assets_contractdetails')) {
    function get_added_assets_contractdetails($faID,$contract) 
    {
        $CI =& get_instance();

        $CI->db->select('assetRef');
        $CI->db->from('srp_erp_contractassets');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('faID', $faID);
        $CI->db->where('contractAutoID', $contract);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}


if (!function_exists('get_contract_pricelist')) {
    function get_contract_pricelist($job_id) 
    {
        $CI =& get_instance();
        $price_arr = array();

        $job_master = get_jobs_master_detail($job_id);

        if($job_master){

            $contract_id = $job_master['contract_po_id'];

            $CI->db->select('*');
            $CI->db->from('srp_erp_contractdetails');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $CI->db->where('contractAutoID', $contract_id);
            $CI->db->where('status', 1);
            $results = $CI->db->get()->result_array();

            $price_arr[0] = 'Select Price';
            foreach($results as $price){
                if($price['pOrService'] == 2){
                    $price_arr[$price['contractDetailsAutoID']] = $price['typeItemName'].' | Service'.' | '.$price['itemReferenceNo'];
                }else{
                    $price_arr[$price['contractDetailsAutoID']] = $price['typeItemName'].' | Product'.' | '.$price['itemReferenceNo'];
                }
               
            }

        }

        return $price_arr;
       
    }

}

if (!function_exists('get_added_price_details')) {
    function get_added_price_details($contractDetailsAutoID) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_contractdetails');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('contractDetailsAutoID', $contractDetailsAutoID);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_daily_report_master')) {
    function get_daily_report_master($id) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_dailyreport');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('id', $id);
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}

if (!function_exists('get_daily_report_master_total_hours')) {
    function get_daily_report_master_total_hours($job_id) 
    {
        $CI =& get_instance();

        $results = $CI->db->query("
            SELECT date_from.dateFrom,date_to.dateTo,main.job_id
            FROM srp_erp_job_dailyreport as main
            left join (
                Select dateFrom,id,job_id
                From srp_erp_job_dailyreport
                where job_id = '{$job_id}' AND companyID = {$CI->common_data['company_data']['company_id']}
                Order by dateFrom ASC
                limit 1
            ) as date_from ON date_from.id = main.id
            
            left join (
                Select dateTo,id,job_id
                From srp_erp_job_dailyreport
                where job_id = '{$job_id}' AND companyID = {$CI->common_data['company_data']['company_id']}
                Order by dateTo DESC
                limit 1
            ) as date_to ON date_to.job_id = date_from.job_id
            
            where main.job_id = '{$job_id}' and date_from.dateFrom IS NOT NULL and date_to.dateTo IS NOT NULL
        ")->row_array();

        return $results;
       
    }

}


if (!function_exists('get_daily_report_details')) {
    function get_daily_report_details($id) 
    {
        $CI =& get_instance();

        $CI->db->select('srp_erp_job_dailyreport_detail.*,srp_erp_job_activitydetail.isNpt,srp_erp_job_activitydetail.hours');
        $CI->db->from('srp_erp_job_dailyreport_detail');
        $CI->db->join('srp_erp_job_activitydetail','srp_erp_job_dailyreport_detail.activity_id = srp_erp_job_activitydetail.id','INNER');
        $CI->db->where('srp_erp_job_dailyreport_detail.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_job_dailyreport_detail.master_id', $id);
        $CI->db->order_by('srp_erp_job_activitydetail.dateFrom', 'ASC'); // Adding order by clause
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}



if (!function_exists('create_daily_report_header')) {
    function create_daily_report_header($master_id,$job_id) 
    {
        $CI =& get_instance();

        $daily_report_header = get_daily_report_master($master_id);

        $job_master = get_jobs_master_detail($job_id);

        if($daily_report_header){
            $date_from_date =  $daily_report_header['dateFrom'];
            $date_to_date =  $daily_report_header['dateTo'];

            $activity_list = get_activity_for_date($job_id,$date_from_date,$date_to_date);

            foreach($activity_list as $activity){

                $data = array();

                $time = getDayWiseDifference($activity['dateFrom'],$activity['dateTo'],'time');

                $data['job_id'] = $job_id;
                $data['master_id'] = $master_id;
                $data['description'] = $activity['description'];
                $data['supervisor_text'] = $activity['description'];
                $data['dateFrom'] = $activity['dateFrom'];
                $data['dateTo'] = $activity['dateTo'];
                $data['time'] = $time;
                $data['activity_id'] = $activity['id'];
                $data['companyID'] =  $activity['companyID'];
                $data['companyCode'] =  $activity['companyCode'];

                $res = $CI->db->insert('srp_erp_job_dailyreport_detail',$data);
        
                $last_id = $CI->db->insert_id();
                
            }

        }

        return True;
       
    }

}

if (!function_exists('get_activity_for_date')) {
    function get_activity_for_date($job_id,$dateFrom,$dateTo) 
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_job_activitydetail');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('dateFrom >=', $dateFrom);
        $CI->db->where('dateTo <=', $dateTo);
        $CI->db->where('job_id', $job_id);
        $results = $CI->db->get()->result_array();

        return $results;
       
    }

}

if (!function_exists('get_grouping_billing_detail')) {
    function get_grouping_billing_detail($billing_id,$price_id) 
    {
        $CI =& get_instance();

        $CI->db->select('srp_erp_job_billing_detail.*,SUM(srp_erp_job_billing_detail.qty) as qty,SUM(srp_erp_job_billing_detail.total_amount) as total_amount,
        srp_erp_contractdetails.itemReferenceNo,srp_erp_contractdetails.mainCategoryID,srp_erp_contractdetails.unitOfMeasure,srp_erp_contractdetails.unitOfMeasureID,srp_erp_contractdetails.itemAutoID');
        $CI->db->from('srp_erp_job_billing_detail');
        $CI->db->join('srp_erp_contractdetails','srp_erp_job_billing_detail.price_id = srp_erp_contractdetails.contractDetailsAutoID','left');
        $CI->db->where('srp_erp_job_billing_detail.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('billing_header', $billing_id);
        $CI->db->where('srp_erp_job_billing_detail.price_id', $price_id);
        $CI->db->group_by('srp_erp_job_billing_detail.price_id');
        $results = $CI->db->get()->row_array();

        return $results;
       
    }

}





function AddPlayTime($times) {
   
}


if (!function_exists('get_add_times')) {
    function get_add_times($times) 
    {
        $minutes = 0; //declare minutes either it gives Notice: Undefined variable
        // loop throught all the times
        foreach ($times as $time) {
            list($hour, $minute) = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }

        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        // returns the time already formatted
        return sprintf('%02d:%02d', $hours, $minutes);
    }

}



if (!function_exists('getEmployeeSchedule')) {
    function getEmployeeSchedule($empID) 
    {
        $CI =& get_instance();
        $html = '';
        $html .= '<button class="btn btn-primary-new text-center" onclick="add_get_employee_schedule('.$empID.')"><i class="fa fa-calendar"></i> </button>';
        return $html;
    }

}

if (!function_exists('fetch_checklist_contract_action_job')) {
    function fetch_checklist_contract_action_job($id,$checklist_id,$status)
    {
        $CI =& get_instance();
        $html = '';
        $html .= ' &nbsp;&nbsp;&nbsp;<a onclick="load_checklist_edit(' . $id . ');"><span title="view" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a> &nbsp;&nbsp;';
        if($status==1){
            $html .= '<a onclick="delete_job_manual_checklist(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        
        $html .= ' &nbsp;&nbsp;&nbsp; <a onclick="get_external_link_checklist('.$id.')"><span title="" rel="tooltip" class="glyphicon glyphicon glyphicon-envelope" data-original-title="Print"></span></a> &nbsp;&nbsp;';

        $html .= '';
        return $html;
    }
}

if (!function_exists('fetch_checklist_status_job')) {
    function fetch_checklist_status_job($completeYN)
    {
        $status = '';
        if ($completeYN==1) {
            $status .= '<div class="text-center"><span class="label label-success">&nbsp;</span></div>';
        
        }else  {
            $status .= '<div class="text-center"><span class="label label-danger">&nbsp;</span></div>';
        }
        return $status;
    }
}

if (!function_exists('get_crew_list_for_checlist_contract')) {
    function get_crew_list_for_checlist_contract($contractAutoID=null)
    {
        //$selectedID,$masterID
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select('con_crew.*,con_crew.empID as empID,con_crew.empDesignation as designation,con_crew.id as id,emp.EIdNo,emp.Ename2,emp.EmpSecondaryCode');
        $CI->db->where('con_crew.companyID',$companyID);
        $CI->db->where('con_crew.contractAutoID',$contractAutoID);
        $CI->db->from('srp_erp_contractcrew as con_crew');
        $CI->db->join('srp_employeesdetails as emp','emp.EIdNo = con_crew.empID','left');
        $dropDownData = $CI->db->get()->result_array();
        $base_arr =array();
        $users_arr = array();

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $value){
               $base_arr[$value['EIdNo']] = $value['EmpSecondaryCode'] . ' | '. $value['Ename2'];
            }
        }
        
        return $base_arr;
    }
}

if (!function_exists('checkVisibilitySection')) {
    function checkVisibilitySection($contractAutoID=null)
    {       
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("visibilityuserIDs,sectionCode");
        $CI->db->from('srp_erp_contractmaster');
        $CI->db->join('srp_erp_op_module_contractvisibility','srp_erp_contractmaster.contractAutoID = srp_erp_op_module_contractvisibility.contractAutoID','left');
        $CI->db->where('srp_erp_contractmaster.contractAutoID', $contractAutoID);
        $CI->db->where('srp_erp_contractmaster.companyID', $companyID);
        $vIDs = $CI->db->get()->result_array();
        
        return $vIDs;
    }
}

if (!function_exists('checkSystemAdmin')) {
    function checkSystemAdmin($userID=null)
    {
        $CI = &get_instance();

        $CI->db->select("isSystemAdmin");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', $userID);
        $res = $CI->db->get()->row_array();
        
        return $res;
    }
}










