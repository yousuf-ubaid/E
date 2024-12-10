<?php

if (!function_exists('get_contract_employee')) {
    function get_contract_employee($dropdown = null)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        $results = $CI->db->get()->result_array();

        if($dropdown){
            $base_arr[] = 'Select Employee';
            foreach($results as $key => $value){
                $base_arr[$value['EIdNo']] = $value['ECode'] . ' | '. $value['Ename1'];
            }
        }else{
            $base_arr = $results;
        }

        return $base_arr;
    }
}

if (!function_exists('get_contract_employee_crew')) {
    function get_contract_employee_crew($dropdown = null)
    {
        $CI = &get_instance();
        $base_arr = array();
        $com = current_companyID();
        $CI->db->select("EIdNo, ECode,DesDescription, IFNULL(Ename2, '') AS employee, DepartmentDes");
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $CI->db->join(' (
                         SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                         JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                         WHERE departTB.Erp_companyID=' . $com . ' AND empDep.Erp_companyID=' . $com . ' AND empDep.isActive=1 GROUP BY EmpID
                     ) AS departTB', 'departTB.empID_Dep=srp_employeesdetails.EIdNo', 'left');
        $CI->db->where('srp_employeesdetails.Erp_companyID', $com);
        $CI->db->where('srp_employeesdetails.isDischarged', 0);
        $results = $CI->db->get()->result_array();
        
        if($dropdown){
            $base_arr[] = 'Select Employee';
            foreach($results as $key => $value){
                $base_arr[$value['EIdNo']] = $value['ECode'] . ' | '. $value['employee']. ' | '. $value['DepartmentDes']. ' | '. $value['DesDescription'];
            }
        }else{
            $base_arr = $results;
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_docType')) {
    function fetch_docType($status=true)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_doc_type');
        $CI->db->where('companyID', current_companyID());
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Doc Type');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['docAutoID'] ?? '')] = trim($row['docName'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_ticket_template')) {
    function fetch_ticket_template($status=true)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_ticket_template');
        $CI->db->where('companyID', current_companyID());
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Ticket Template');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['ticketCode'] ?? '')] = trim($row['ticketName'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_contract_type')) {
    function fetch_contract_type($status=true)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_contract_type');
        $CI->db->where('companyID', current_companyID());
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Contract Type');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['contractCode'] ?? '')] = trim($row['contractName'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('all_activity_type_drop')) {
    function all_activity_type_drop($status=true)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_activity_code_main');
        $CI->db->where('company_id', current_companyID());
        $CI->db->where('is_active', 1);
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Activity Type');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['id'] ?? '')] = trim($row['activity_code'] ?? '').' | '.trim($row['narration'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_group_to')) {
    function fetch_group_to($status,$type,$contractAutoID)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_module_group_to');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('groupType', $type);
        $CI->db->where('contractAutoID', $contractAutoID);
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Group To');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['groupAutoID'] ?? '')] = trim($row['groupName'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_group_to_category')) {
    function fetch_group_to_category($status,$contractAutoID)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_contract_details_category_list');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('contractID', $contractAutoID);
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Category');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['autoID'] ?? '')] = trim($row['categoryName'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_dalilTemplatey_report')) {
    function fetch_dalilTemplatey_report($status=true)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_module_dalilTemplatey_report');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('status', 1);
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Daily Report Template');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['temReportAutoID'] ?? '')] = trim($row['temName'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_section_visibility')) {
    function fetch_section_visibility($status=true)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_op_module_visibility_section');
        $CI->db->where('status', 1);
        $results = $CI->db->get()->result_array();

        if ($status) {
            $base_arr = array('' => 'Select Section');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
                $base_arr[trim($row['sectionCode'] ?? '')] = trim($row['sectionName'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('get_contract_assets')) {
    function get_contract_assets($dropdown = null,$assetID = null)
    {
        $CI =& get_instance();
        $base_arr = array();

        $CI->db->select('*');
        $CI->db->from('srp_erp_fa_asset_master');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('approvedYN', 1);
        
        if($assetID){
            $CI->db->where('faID', $assetID);
            return  $CI->db->get()->row_array();
        }
        
        $results = $CI->db->get()->result_array();
        if($dropdown){
            $base_arr[] = 'Select Assets';
            foreach($results as $key => $value){
                $base_arr[$value['faID']] = $value['faCode'] . ' | ' . $value['assetDescription'] . ' | ' . $value['faUnitSerialNo'];
            }
        }else{
            $base_arr = $results;
        }

        return $base_arr;
    }
}

/*
  Called Stack : 
     Quotation_contract_model : save_contract_job_crew
*/
if (!function_exists('get_contract_employee_detail')) {
    function get_contract_employee_detail($empID,$select = null)
    {
        $CI =& get_instance();
        if($select){
            $CI->db->select("$select");
        }else{
            $CI->db->select('*');
        }
       
        $CI->db->from('srp_employeesdetails as se');
        $CI->db->join('srp_designation as sd','sd.DesignationID = se.EmpDesignationId','left');
        $CI->db->where('EIdNo', $empID);
        $results = $CI->db->get()->row_array();

        return $results;
    }
}


if (!function_exists('get_contract_crew_detial')) {
    function get_contract_crew_detial($empID,$contractAutoID = null,$isPrimary = null)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_contractcrew');
        $CI->db->where('empID', $empID);
        if($contractAutoID){
            $CI->db->where('contractAutoID',$contractAutoID);
        }
        if($isPrimary){
            $CI->db->where('isPrimary',1);
        }
        $results = $CI->db->get()->row_array();

        return $results;
    }
}




if (!function_exists('get_contract_asset_detail')) {
    function get_contract_asset_detail($asset,$contractAutoID)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_contractassets');
        $CI->db->where('faID', $asset);
        $CI->db->where('contractAutoID', $contractAutoID);
        $results = $CI->db->get()->row_array();

        return $results;
    }
}



if (!function_exists('fetch_crew_action')) {
    function fetch_crew_action()
    {
        $CI =& get_instance();
        $html = '';
        $html .= ' <a onclick="edit_crew_line($(this))"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;';
        $html .= ' <a onclick="delete_crew_line($(this))"><span title="Delete" rel="tooltip" class="text-danger glyphicon glyphicon-trash"></span></a>';
        $html .= '';
        return $html;
    }
}

if (!function_exists('fetch_checklist_contract_action')) {
    function fetch_checklist_contract_action($id,$checklistID)
    {
        $CI =& get_instance();
        $html = '';

        $html .= '<a onclick="delete_contract_checklist(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $html .= '&nbsp;&nbsp;<a onclick="open_contract_checklist(' . $checklistID . ')"><span title="view" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
       // $html .= ' <a onclick="delete_crew_line($(this))"><span title="Delete" rel="tooltip" class="text-danger glyphicon glyphicon-trash"></span></a> &nbsp;&nbsp;';
        $html .= '';
        return $html;
    }
}

if (!function_exists('get_isprimary')) {
    function get_isprimary($isPrimary)
    {
        $CI =& get_instance();
        $html = '';

        if($isPrimary == 1){
            $html .= '<span class="badge badge-danger" style="padding:5px;background-color:red;color:white">Primary Contract</span>';
        }

        $html .= '';
        return $html;
    }
}



if (!function_exists('fetch_asset_action')) {
    function fetch_asset_action($masterID)
    {
        $CI =& get_instance();
        $html = '';
        $html .= ' <a onclick="edit_asset_line($(this))"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;';
       // $html .= ' <a onclick="delete_asset_line_contract(' . $masterID . ')"><span title="Delete" rel="tooltip" class="text-danger glyphicon glyphicon-trash"></span></a> &nbsp;&nbsp;';
        $html .= '<a onclick="delete_asset_line_contract(' . $masterID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $html .= '';
        return $html;
    }
}

if (!function_exists('make_contract_calling_dropDown')) {
    function make_contract_calling_dropDown($selectedID,$masterID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_op_module_calling_list');
        $CI->db->WHERE('status', 1);        
        $CI->db->WHERE('companyID', $companyID);
        $dropDownData = $CI->db->get()->result_array();

       
        $dropDown = '<select id="" name="select_calling" class="form-control select2" onchange="selectCallingUpdate(this,' . $masterID . ')">';
        $dropDown .= '<option value="">Select Calling</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $callingCode = $rowDrop['callingCode'];
                $selected = ($selectedID == $callingCode)? 'selected' : '';
                $dropDown .= '<option value="'.$callingCode.'" '.$selected.' data-cat="'.$callingCode.'">';
                $dropDown .= $rowDrop['callingName'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_contract_checklist_user_dropDown')) {
    function make_contract_checklist_user_dropDown()
    {
        //$selectedID,$masterID
        $CI = &get_instance();
        $companyID = current_companyID();
        $groupCompanyID = $CI->db->query(
            "SELECT companyGroupID 
             FROM srp_erp_companygroupdetails 
             WHERE srp_erp_companygroupdetails.companyID = $companyID"
            )->row('companyGroupID');


            if(!empty($groupCompanyID)){
                $companyList = $CI->db->query(
                    "SELECT companyID 
                     FROM srp_erp_companygroupdetails 
                     WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
                    )->result_array();
            }

            $CI->db->SELECT("srp_employeesdetails.EIdNo,srp_employeesdetails.Ename2,srp_employeesdetails.EmpSecondaryCode");
            
            if(!empty($groupCompanyID)) {

                $companyArray=[];
                if (count($companyList)>0) {
                    foreach($companyList as $val){
                        $companyArray[]=$val['companyID'];
                    }
                }

                $CI->db->FROM('srp_employeesdetails,srp_erp_companygroupdetails AS cmpTB');
                $CI->db->where_in('cmpTB.companyID',$companyArray);
                $CI->db->WHERE('srp_employeesdetails.empConfirmedYN', 1);
                $CI->db->group_by('srp_employeesdetails.EIdNo');
                //AND cmpTB.companyID =505
                //GROUP BY srp_employeesdetails.EIdNo 
            } else {
                $CI->db->FROM('srp_employeesdetails');
                $CI->db->WHERE('Erp_companyID', $companyID);
            }

        $CI->db->WHERE('srp_employeesdetails.empConfirmedYN', 1);        
        $CI->db->WHERE('srp_employeesdetails.isDischarged', 0);
        $dropDownData = $CI->db->get()->result_array();

        $base_arr =array();
        $users_arr = array();

        // if($selectedID){
        //     $users_arr = explode(",",$selectedID);
        // }

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $value){
               $base_arr[$value['EIdNo']] = $value['EmpSecondaryCode'] . ' | '. $value['Ename2'];
            }
        }

      //  $dorp = form_dropdown('select_user[]', $base_arr, $users_arr, 'class="form-control select_user" id="customerCode" multiple="multiple" onchange="selectChecklistUserUpdate(this,' . $masterID . ')"');

        return $base_arr;
    }
}



if (!function_exists('make_user_dropDown_visibility')) {
    function make_user_dropDown_visibility()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $groupCompanyID = $CI->db->query(
            "SELECT companyGroupID 
             FROM srp_erp_companygroupdetails 
             WHERE srp_erp_companygroupdetails.companyID = $companyID"
            )->row('companyGroupID');


            if(!empty($groupCompanyID)){
                $companyList = $CI->db->query(
                    "SELECT companyID 
                     FROM srp_erp_companygroupdetails 
                     WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
                    )->result_array();
            }

            $CI->db->SELECT("srp_employeesdetails.EIdNo,srp_employeesdetails.Ename2,srp_employeesdetails.EmpSecondaryCode");
            
            if(!empty($groupCompanyID)) {

                $companyArray=[];
                if (count($companyList)>0) {
                    foreach($companyList as $val){
                        $companyArray[]=$val['companyID'];
                    }
                }

                $CI->db->FROM('srp_employeesdetails,srp_erp_companygroupdetails AS cmpTB');
                $CI->db->where_in('cmpTB.companyID',$companyArray);
                $CI->db->WHERE('srp_employeesdetails.empConfirmedYN', 1);
                $CI->db->group_by('srp_employeesdetails.EIdNo');
                //AND cmpTB.companyID =505
                //GROUP BY srp_employeesdetails.EIdNo 
            } else {
                $CI->db->FROM('srp_employeesdetails');
                $CI->db->WHERE('Erp_companyID', $companyID);
            }
            $CI->db->WHERE('srp_employeesdetails.empConfirmedYN', 1);        
            $CI->db->WHERE('srp_employeesdetails.isDischarged', 0);
            $dropDownData = $CI->db->get()->result_array();

        if (isset($dropDownData)) {
            foreach ($dropDownData as $row) {
                $base_arr[trim($row['EIdNo'] ?? '')] = trim($row['EmpSecondaryCode'] ?? '').' | '.trim($row['Ename2'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('fetch_visibility_contract_action')) {
    function fetch_visibility_contract_action($id)
    {
        $CI =& get_instance();
        $html = '';

        $html .= '<a onclick="delete_contract_visibility(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
       // $html .= ' &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a onclick=""><span title="view" rel="tooltip" class="glyphicon glyphicon-zoom-in"></span></a> &nbsp;&nbsp;';
       // $html .= ' &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="edit_visibility_line(' . $id . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;';
        $html .= '';
        return $html;
    }
}

if (!function_exists('make_user_name_arr')) {
    function make_user_name_arr($id_text)
    {
        $CI =& get_instance();
        $user_arr =explode(",",$id_text);


        $user_name_arr =[];

        foreach($user_arr as $val){
            $CI->db->SELECT("srp_employeesdetails.EIdNo,srp_employeesdetails.Ename2,srp_employeesdetails.EmpSecondaryCode");
            $CI->db->FROM('srp_employeesdetails');
            $CI->db->WHERE('EIdNo', $val);
            $dropDownData = $CI->db->get()->row_array();

            $user_name_arr[]= $dropDownData['Ename2'];
        }

        $user_name_text = implode(" , ",$user_name_arr);

       
        $html = '';

        $html .= '<p>'. $user_name_text.'</p>';
        $html .= '';
        return $html;
    }
}

if (!function_exists('action_codes')) {
    function action_codes($action_codes)
    {
        $CI =& get_instance();
        $action_arr =explode(",",$action_codes);
        
        $html = '';
        foreach($action_arr as $val){
            
            if($val == 'Edit'){
                $html .= '<span class="label-primary mr-1" style="padding: 2px 10px;border-radius: 25px;font-size: 12px;">'. $val.'</span>';
            } else if($val == 'Delete'){
                $html .= '<span class="label-danger mr-1" style="padding: 2px 10px;border-radius: 25px;font-size: 12px;">'. $val.'</span>';
            } else if($val == 'Add'){
                $html .= '<span class="label-success mr-1" style="padding: 2px 10px;border-radius: 25px;font-size: 12px;">'. $val.'</span>';
            } else{
                $html .= '<span class="label-info mr-1" style="padding: 2px 10px;border-radius: 25px;font-size: 12px;">'. $val.'</span>';
            }
            
        }
        return $html;
    }
}

if (!function_exists('get_contract_master_with_amendments')) {
    function get_contract_master_with_amendments($contractAutoID)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_contractmaster');
        $CI->db->join('srp_erp_document_amendments','srp_erp_contractmaster.contractAutoID = srp_erp_document_amendments.docID','left');
        $CI->db->where('contractAutoID', $contractAutoID);
        $results = $CI->db->get()->row_array();

        return $results;
    }
}

