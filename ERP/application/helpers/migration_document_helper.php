<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('action_migration_header')) {
    function action_migration_header($id,$validateStatus,$docType)
    {
        $status = '<span class="pull-right">';

        // if($validateStatus==1){
        //     $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $id . '\',' . $validateStatus . ')" ><span title="Post Data" rel="tooltip" class="glyphicon glyphicon-export"></span></a>';
        // }else{
           
        // }
        if($docType=='EM'){
            $status .= '<a onclick="fetchPage(\'system/migration/applicable_maigration_view_edit\',' . $id . ',\'Edit Migration\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_migration_recode(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }else{
            $status .= '<a onclick="fetchPage(\'system/migration/applicable_maigration_view\',' . $id . ',\'Edit Migration\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_migration_recode(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('status_migration_header')) {
    function status_migration_header($mStatus)
    {
        $status = '';
        if ($mStatus == 0) {
            $status .= '<span class="label" style="background-color:#D52323; color:#ffffff; font-size: 11px;">Validation Pending</span>';
        } else if ($mStatus == 1) {
            $status .= '<span class="label" style="background-color:#05e467; color:#ffffff; font-size: 11px;">Validated</span>';
        }  else {
            $status = '';
        }
        return $status;
    }
}

if (!function_exists('add_doc_type')) {
    function add_doc_type($docType,$docName)
    {
        $status = '';
        if (!$docType) {
            $status = '--';
        } else {
            $status = $docName;
        }
        return $status;
    }
}

if (!function_exists('make_Nationality_dropDown')) {
    function make_Nationality_dropDown($selectedID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("NId,Nationality");
        $CI->db->FROM('srp_nationality');
        $CI->db->where('Erp_companyID', $companyID);
        $dropDownData = $CI->db->get()->result_array();

       
        $dropDown = '<select id="" name="Nationality[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Nationality</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['NId'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['Nationality'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_emp_title_dropDown')) {
    function make_emp_title_dropDown($selectedID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("TitleID,TitleDescription");
        $CI->db->FROM('srp_titlemaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('TitleDescription');
        $dropDownData = $CI->db->get()->result_array();
       
        $dropDown = '<select id="" name="emp_title[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Title</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['TitleID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['TitleDescription'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_religion_dropDown')) {
    function make_religion_dropDown($selectedID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("RId,Religion");
        $CI->db->FROM('srp_religion');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('Religion');
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="religion[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Religion</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['RId'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['Religion'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_MaritialStatus_dropDown')) {
    function make_MaritialStatus_dropDown($selectedID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("maritialstatusID,description");
        $CI->db->FROM('srp_erp_maritialstatus');
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="MaritialStatus[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Maritial Status</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['maritialstatusID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['description'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_BloodGroup_dropDown')) {
    function make_BloodGroup_dropDown($selectedID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("BloodTypeID,BloodDescription");
        $CI->db->FROM('srp_erp_bloodgrouptype');
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="BloodGroup[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Blood Group</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['BloodTypeID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['BloodDescription'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_EpAddress4_dropDown')) {
    function make_EpAddress4_dropDown($selectedID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("countryID,CountryDes");
        $CI->db->FROM('srp_countrymaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('CountryDes');
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="ep_address4[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Country</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['countryID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['CountryDes'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_employeeConType_dropDown')) {
    function make_employeeConType_dropDown($selectedID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("EmpContractTypeID, Description, employeeTypeID, period, probation_period");
        $CI->db->FROM('srp_empcontracttypes AS t1');
        $CI->db->JOIN('srp_erp_systememployeetype AS t2', 't1.typeID=t2.employeeTypeID');
        $CI->db->WHERE('Erp_CompanyID', current_companyID());
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="employeeConType[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Contract Type</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['EmpContractTypeID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['Description'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_empCurrency_dropDown')) {
    function make_empCurrency_dropDown($selectedID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("srp_erp_companycurrencyassign.currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->join('srp_erp_companycurrencyassign', 'srp_erp_companycurrencyassign.currencyID = srp_erp_currencymaster.currencyID');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="empCurrency[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Currency</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['currencyID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['CurrencyCode'].' | '.$rowDrop['CurrencyName'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_empSegment_dropDown')) {
    function make_empSegment_dropDown($selectedID)
    {
        $CI =& get_instance();
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('srp_erp_segment.companyID', $CI->common_data['company_data']['company_id']);
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="empSegment[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Segment</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['segmentID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['segmentCode'].' | '.$rowDrop['description'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_designationID_dropDown')) {
    function make_designationID_dropDown($selectedID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("DesignationID,DesDescription");
        $CI->db->FROM('srp_designation');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->WHERE('isDeleted', 0);
        $CI->db->order_by('DesDescription');
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="designationID[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Designation</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['DesignationID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['DesDescription'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_department_dropDown')) {
    function make_department_dropDown($selectedID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('hrms_reports', $primaryLanguage);
        $CI->db->select('DepartmentMasterID, DepartmentDes');
        $CI->db->from('srp_departmentmaster');
        $CI->db->where('isActive', 1);
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="" name="items[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Department</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['DepartmentMasterID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['DepartmentDes'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_bank_dropDown')) {
    function make_bank_dropDown($selectedID,$id)
    {
        $CI = &get_instance();
        $CI->db->select('bankID, bankCode, bankName, bankSwiftCode');
        $CI->db->from('srp_erp_pay_bankmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $dropDownData = $CI->db->get()->result_array();
       
       
        $dropDown = '<select id="bank_'.$id.'" name="bank_id[]" class="form-control select2" onchange="fetch_banck_brach_em_migration('.$id.')">';
        $dropDown .= '<option value="">Select Bank</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['bankID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['bankCode'].' | '.$rowDrop['bankName'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_bank_branch_dropDown')) {
    function make_bank_branch_dropDown($selectedID,$bankID,$id)
    {
        $CI = &get_instance();
        $CI->db->select('branchID, branchCode, branchName');
        $CI->db->from('srp_erp_pay_bankbranches');
        $CI->db->where('bankID', $bankID);
        $dropDownData = $CI->db->get()->result_array();
       
     //   $this->db->query("SELECT branchID, branchCode, branchName FROM srp_erp_pay_bankbranches WHERE bankID={$bankID} ORDER BY branchName")->result_array();

        $dropDown = '<select id="branch_'.$id.'" name="branch_id[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Branch</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['branchID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['branchCode'].' | '.$rowDrop['branchName'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_leaveGroupID_dropDown')) {
    function make_leaveGroupID_dropDown($selectedID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $companyID = current_companyID();
        $dropDownData = $CI->db->query("SELECT leaveGroupID,description FROM `srp_erp_leavegroup` WHERE leaveGroupID=(select leaveGroupID from `srp_erp_leavegroupdetails` WHERE leaveGroupID=srp_erp_leavegroup.leaveGroupID group by leaveGroupID) AND companyID={$companyID}")->result_array();

        $dropDown = '<select id="" name="leaveGroupID[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select leave Group</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['leaveGroupID'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['description'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}



if (!function_exists('make_date_dropDown')) {
    function make_date_dropDown($value,$name)
    {
        $date_format_policy = date_format_policy();
       // $html = '<input  type="text" data-inputmask="alias:' . $date_format_policy . '" value="' . $value . '" id="" name="' . $name . '" >';
        $html = '<div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input  type="text" data-inputmask="alias:' . $date_format_policy . '" value="' . $value . '" id="" name="' . $name . '" ></div>';
        return $html;
    }
}

if (!function_exists('make_gender_dropDown')) {
    function make_gender_dropDown($selectedID)
    {
        $dropDownData=[["id"=>1,"name"=>"Male"],["id"=>2,"name"=>"Female"]];
        $dropDown = '<select id="" name="emp_gender[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Gender</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['id'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['name'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_payroll_dropDown')) {
    function make_payroll_dropDown($selectedID)
    {
        $dropDownData=[["id"=>1,"name"=>"Payroll"],["id"=>2,"name"=>"Non Payroll"]];
        $dropDown = '<select id="" name="payrollType[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Payroll Type</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['id'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['name'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}



if (!function_exists('make_isPayrollEmployee_dropDown')) {
    function make_isPayrollEmployee_dropDown($selectedID)
    {
        $dropDownData=[["id"=>1,"name"=>"Is Payroll Employee"]];
        $dropDown = '<select id="" name="isPayrollEmployee[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Payroll Type</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['id'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['name'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}

if (!function_exists('make_manager_dropDown')) {
    function make_manager_dropDown($selectedID)
    {
        $CI = &get_instance();
        
        $companyID=$CI->common_data['company_data']['company_id'];
        $dropDownData = $CI->db->query("SELECT EIdNo, Ename1, CONCAT(ECode,' _ ', Ename2) AS nameWithCode
                                  FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                  AND empConfirmedYN = 1 AND isDischarged = 0 
                                  ")->result_array();

        $dropDown = '<select id="" name="managerID[]" class="form-control select2" onchange="">';
        $dropDown .= '<option value="">Select Manager</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $ID = $rowDrop['EIdNo'];
                $selected = ($selectedID == $ID)? 'selected' : '';
                $dropDown .= '<option value="'.$ID.'" '.$selected.' data-cat="'.$ID.'">';
                $dropDown .= $rowDrop['nameWithCode'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}