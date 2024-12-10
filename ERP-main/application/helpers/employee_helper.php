<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('IS_OMAN_OIL', false);

if (!function_exists('load_monthly_addition_action')) {
    function load_monthly_addition_action($id, $monthType, $code, $isNonPayroll, $confirmedYN = 0, $isProcess = 0, $typeID = 0)
    {
        $CI =& get_instance();

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($monthType == 'A') {
            $page = 'addition';
            $t = 'MA';
        } else {
            $page = 'deduction';
            $t = 'MD';
        }

        $isNonPayroll = ($isNonPayroll == 'Y') ? 2 : 1;

        if($typeID == 1){
            $fetch = "fetchPage('system/hrm/emp_monthly_variable_salary_" . $page . "',".$id." ,'HRMS','', ".$isNonPayroll.")";
        }else{
            $fetch = "fetchPage('system/hrm/emp_monthly_salary_" . $page . "',".$id." ,'HRMS','', ".$isNonPayroll.")";
        }

        if ($confirmedYN == 1) {
            $status .= '<li>
                            <a href="#" onclick="' . $fetch . '">
                            <span class="glyphicon glyphicon-eye-open" style="color:#03a9f4"></span> View</a>
                        </li>';
        }

        if ($confirmedYN != 1) {
            $status .= '<li>
                            <a href="#" onclick="' . $fetch . '">
                            <span class="glyphicon glyphicon-pencil" style="color:#116f5e"></span> Edit</a>
                        </li>';

            $status .= '<li>
                            <a href="#" onclick="delete_details(' . $id . ', \'' . $code . '\');">
                            <span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span> Delete</a>
                        </li>';
        }

        $status .= '<li>
                        <a href="' . site_url('Employee/monthlyAD_print') . '/' . $t . '/' . $id . '/' . $code . '" target="_blank">
                        <span class="glyphicon glyphicon-print" style="color:#607d8b"></span> Print</a>
                    </li>';

        if ($isProcess == 0 && $confirmedYN == 1) {
            $status .= '<li>
                            <a href="#" onclick="referBackConformation(' . $id . ');">
                            <span class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span> Refer Back</a>
                        </li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('monthlyDeclarationsAction')) {
    function monthlyDeclarationsAction($id, $monthType, $code, $isNonPayroll, $confirmedYN = 0, $isProcess = 0,$typeID = 0)
    {
        $edit = '';
        $delete = '';
        $view = '';
        $referBack = '';
        if ($monthType == 'A') {
            $page = 'addition';
            $t = 'MA';
        } else {
            $page = 'deduction';
            $t = 'MD';
        }

        $isNonPayroll = ( $isNonPayroll == 'Y' )? 2 : 1;

        if($typeID == 1){
            $fetch = "fetchPage('system/hrm/emp_monthly_variable_salary_" . $page . "',".$id." ,'HRMS','', ".$isNonPayroll.")";
        }else{
            $fetch = "fetchPage('system/hrm/emp_monthly_salary_" . $page . "',".$id." ,'HRMS','', ".$isNonPayroll.")";
        }
        


        $print = '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="' . site_url('Employee/monthlyAD_print') . '/' . $t . '/' . $id . '/' . $code . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($confirmedYN != 1) {
            $code = "'" . $code . "'";
            $edit = '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_details(' . $id . ' , ' . $code . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        } elseif ($confirmedYN == 1) {
            $view = '<a onclick="' . $fetch . '"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>';
        }

        if ($isProcess == 0 && $confirmedYN == 1) {
            $referBack = '<a onclick="referBackConformation(' . $id . ')"><span style="color:#d15b47;" title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"></span></a>&nbsp;&nbsp;|&nbsp;';
        }

        return '<span class="pull-right">' . $referBack . '' . $view . '' . $edit . '' . $delete . '' . $print . ' </span>';
    }
}

if (!function_exists('des')) {
    function des($des)
    {
        return '<input type="text" name="description[]" class="trInputs"  value="' . $des . '">';
    }
}

$amount = 0;
if (!function_exists('monthlyAmount')) {
    function monthlyAmount($tAmount, $dPlaces, $empID, $localExchangeRate)
    {
        global $amount;
        $amount = $amount + 1;
        $val = ($tAmount == 0) ? '' : number_format($tAmount, $dPlaces);
        $str = '<input type="text" name="amount[]" class="trInputs number" id="amount_' . $amount . '"  value="' . $val . '"';
        $str .= ' onkeyup="empAmount(this, ' . $amount . ', \''.$localExchangeRate.'\')" onchange="formatAmount(this, '.$dPlaces.')">';
        return $str;
    }
}

if (!function_exists('action')) {
    function action($empID, $currency, $dPlace)
    {

        $details = '<span class="glyphicon glyphicon-trash traceIcon" onclick="removeEmpTB(this)" style="color:#d15b47;"></span>
                <input type="hidden" name="empHiddenID[]" class="recordTB_empID" value="' . $empID . '">
                <input type="hidden" name="empCurrencyCode[]" class="empCurrencyCode" value="' . $currency . '">
                <input type="hidden" name="empCurrencyDPlace[]" class="empCurrencyDPlace" value="' . $dPlace . '">';

        return '<div align="right" >' . $details . '</div>';
    }
}

$amountSpan = 0;
if (!function_exists('localAmount')) {
    function localAmount($localAmount, $dPlaces)
    {
        global $amountSpan;
        $amountSpan = $amountSpan + 1;
        return '<div align="right" class="localAmount" id="amountSpan_' . $amountSpan . '" >' . number_format($localAmount, $dPlaces) . '</div>';

    }
}

$exRateSpan = 0;
if (!function_exists('exRate')) {
    function exRate($exRate)
    {
        global $exRateSpan;
        $exRateSpan = $exRateSpan + 1;
        return '<div align="right" class="exRate" id="exRateSpan_' . $exRateSpan . '" >' . round($exRate, 6) . '</div>';

    }
}

if (!function_exists('load_leave_master_action')) {
    function load_leave_master_action($id, $des, $isExist, $isPaidLeave, $isAnnualLeave, $attachmentRequired, $isPlanApplicable, $reasonApplicableYN, $isSickLeave, $isShortLeave, $shortLeaveMaxHours, $shortLeaveMaxMins)
    {
        $des = "'" . $des . "'";
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($isSickLeave == 1) {
            $status .= '<li><a onclick="leaveSetup(' . $id . ', ' . $des . ', this)"><i class="fa fa-cogs" style="color:black"></i> Setup</a></li>';
        }
        
        if ($reasonApplicableYN == 1) {
            $status .= '<li><a onclick="viewReason(' . $id . ', ' . $des . ', this)"><i class="fa fa-file-text-o" style="color:black"></i> Reason</a></li>';
        }
        
        $status .= '<li><a onclick="edit_LeaveType(' . $id . ', ' . $des . ', ' . $isPaidLeave . ', ' . $isAnnualLeave . ', ' . $attachmentRequired . ', ' . $isPlanApplicable . ', ' . $reasonApplicableYN . ', ' . $isShortLeave . ', ' . $shortLeaveMaxHours . ', ' . $shortLeaveMaxMins . ', ' . $isSickLeave . ')"><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
        
        if ($isExist == null) {
            $status .= '<li><a onclick="delete_LeaveType(' . $id . ', ' . $des . ')"><span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span> Delete</a></li>';
        }

        $status .= '</ul></div>';
        
        return $status;
    }
}

if (!function_exists('leaveTypes_drop')) {
    function leaveTypes_drop($isPaidLeave = null)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $where2 = '';
        if ($isPaidLeave != null) {
            $where2 = 'AND isPaidLeave=1';
        }
        $leaveTypes = $CI->db->query("SELECT leaveTypeID, t1.description, policyDescription, isPaidLeave
                                      FROM srp_erp_leavetype t1
                                      JOIN srp_erp_leavepolicymaster t2 ON t1.policyID=t2.policyMasterID
                                      WHERE companyID={$companyID}  $where2 ")->result_array();
        return $leaveTypes;
        /*$i = 0;
        $arr = array('leaveTypeID'=>'', 'description'=>'Select', 'policy'=>'');
        if (isset($leaveTypes)) {
            foreach ($leaveTypes as $row) {
                $arr[$i] = array(
                    'leaveTypeID' => trim($row['leaveTypeID'] ?? ''),
                    'description' => trim($row['description'] ?? ''),
                    'policy' => trim($row['policy'] ?? '')
                );
                $i++;
            }
        }
        return $arr;*/
    }
}

if (!function_exists('isAlreadyExistInThisArray')) {
    function isAlreadyExistInThisArray($arr, $val, $no, $empID)
    {
        $CI =& get_instance();
        $j = 0;
        $returnVal = null;
        foreach ($arr as $row) {
            if ($row == $val && $no != $j) {
                $description = $CI->db->query("SELECT description FROM srp_erp_leavetype WHERE leaveTypeID={$row}")->row_array();
                $returnVal = $description['description'] . ' is more than one time added';
            } else {
                $isEntered = $CI->db->query("SELECT description FROM srp_erp_leaveentitled AS t1
                                             JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID
                                             WHERE empID={$empID} AND t2.leaveTypeID={$row}")->row_array();
                if (count($isEntered) > 0) {
                    $returnVal = $isEntered['description'] . ' is Already Exist ';
                }
            }
            $j++;
        }

        return ($returnVal != null) ? array('e', $returnVal) : array('s');
    }
}

if (!function_exists('load_Apply_for_leave_action')) {
    function load_Apply_for_leave_action($id, $code, $confirmedYN, $approvedYN, $requestForCancelYN, $cancelledYN)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('hrms_leave_management', $primaryLanguage);
        $leaveApp = $CI->lang->line('hrms_leave_management_leave_application'); /*Leave Application*/

        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                           <li><a onclick="attachment_modal(' . $id . ', \'' . $leaveApp . '\', \'LA\', ' . $confirmedYN . ')">
                               <span class="glyphicon glyphicon-paperclip" style="color:#4caf50"></span> Attachment</a>
                           </li>';

        if ($confirmedYN != 1) {
            $action .= '<li><a onclick="openLeaveDetails(' . $id . ', \'' . $code . '\')">
                            <span class="glyphicon glyphicon-pencil" style="color:#116f5e"></span> Edit</a>
                        </li>
                        <li><a onclick="delete_leave(' . $id . ', \'' . $code . '\')">
                            <span class="glyphicon glyphicon-trash" style="color:#d15b47"></span> Delete</a>
                        </li>';
        } elseif ($confirmedYN == 1) {
            $action .= '<li><a onclick="openLeaveDetails(' . $id . ', \'' . $code . '\')">
                            <span class="fa fa-fw fa-eye" style="color:#03a9f4"></span> View</a>
                        </li>';
        }

        if ($approvedYN == 0 && $confirmedYN == 1) {
            $action .= '<li><a onclick="refer_leave(' . $id . ', \'' . $code . '\')">
                            <span class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span> Refer Back</a>
                        </li>';
        }

        if ($approvedYN == 1 && $requestForCancelYN != 1) {
            $action .= '<li><a onclick="cancel_leave(' . $id . ', \'' . $code . '\')">
                            <span class="fa fa-ban fa-fw" style="color:#d15b47;"></span> Cancel</a>
                        </li>';
        }

        if ($approvedYN == 1 && $requestForCancelYN == 1 && $cancelledYN != 1) {
            $action .= '<li><a onclick="refer_leave_cancellation(' . $id . ', \'' . $code . '\')">
                            <span class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span> Refer Back Cancellation</a>
                        </li>';
        }

        $action .= '<li><a target="_blank" href="' . site_url('Employee/leave_print/') . '/' . $id . '/' . $code . '">
                        <span class="glyphicon glyphicon-print" style="color:#607d8b"></span> Print</a>
                    </li>';

        $action .= '</ul></div>';

        return $action;
    }
}

if (!function_exists('leaveApplicationAction')) {
    function leaveApplicationAction($id, $code, $confirmedYN, $approvedYN, $requestForCancelYN, $cancelledYN)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('hrms_leave_management', $primaryLanguage);
        $cancel = $edit = $delete =  $view = $referBack = '';
        $leaveApp =  $CI->lang->line('hrms_leave_management_leave_application');/*Leave Application*/
        $fetch = "openLeaveDetails($id, '" . $code . "')";
        $delete_Fn = "delete_leave($id, '" . $code . "')";
        $ref_fn = "refer_leave($id , '" . $code . "')";

        $att = '<a onclick=\'attachment_modal(' . $id . ',"'.$leaveApp.'","LA",'.$confirmedYN.');\'><span title="Attachment" rel="tooltip" ';
        $att .= 'class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        $print = '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="' . site_url('Employee/leave_print/') . '/' . $id . '/' . $code . '" >';
        $print .= '<span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($confirmedYN != 1) {
            $edit = '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="' . $delete_Fn . '"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" ';
            $delete .= 'style="color:#d15b47;"></span></a>';
        } elseif ($confirmedYN == 1) {
            $view = '<a onclick="' . $fetch . '"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>';
        }

        if ($approvedYN == 0 && $confirmedYN == 1) {
            $referBack = '<a onclick="' . $ref_fn . '"><span title="Refer Back" rel="tooltip" style="color:#d15b47;" ';
            $referBack .= 'class="glyphicon glyphicon-repeat"></span></a>&nbsp;&nbsp;|&nbsp;';
        }

        if($approvedYN == 1 && $requestForCancelYN != 1){
            $cancel_fn = "cancel_leave($id , '" . $code . "')";
            $cancel = '<a onclick="' . $cancel_fn . '" title="Cancel" rel="tooltip"><i class="fa fa-ban fa-fw"></i>';
            $cancel .= '</a>&nbsp;&nbsp;|&nbsp;';
        }

        if($approvedYN == 1 && $requestForCancelYN == 1 && $cancelledYN != 1){
            $ref_fn = "refer_leave_cancellation($id , '" . $code . "')";
            $referBack = '<a onclick="' . $ref_fn . '"><span title="Refer Back Cancellation" rel="tooltip" style="color:#d15b47;" ';
            $referBack .= 'class="glyphicon glyphicon-repeat"></span></a>&nbsp;&nbsp;|&nbsp;';
        }

        return '<span class="pull-right">'.$cancel.''.$att.'' . $referBack . '' . $view . '' . $edit . '' . $delete . '' . $print . ' </span>';
    }
}


if (!function_exists('wfh_ApplicationAction')) {
    function wfh_ApplicationAction($id, $code, $confirmedYN, $approvedYN)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();

        $cancel = $edit = $delete =  $view = $referBack = '';
        $wfhApp = "Work From Home Application";
        $fetch = "edit_WFH_document($id, '" . $code . "')";
        $delete_Fn = "delete_empWFH($id, '" . $code . "')";
        $ref_fn = "refer_back_confirmation($id , '" . $code . "')";
        $cancel_fn = "cancel_leave($id , '" . $code . "')";

        $att = '<a onclick=\'attachment_modal(' . $id . ',"'.$wfhApp.'","WFH",'.$confirmedYN.');\'><span title="Attachment" rel="tooltip" ';
        $att .= 'class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span></a>';

        $print = '<a target="_blank" href="' . site_url('Employee/load_WFH_request_conformation/') . '/' . $id . '/' . $code . '" >';
        $print .= '<span title="Print" rel="tooltip" class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a>';

        $dropdownItems = '';

        if ($confirmedYN != 1) {
            $edit = '<li><a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            $delete = '<li><a onclick="' . $delete_Fn . '"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
            $dropdownItems .= $edit . $delete;
        } elseif ($confirmedYN == 1) {
            $view = '<li><a onclick="documentPageView_modal(\'WFH\',' . $id . ')"><span title="View" rel="tooltip" class="fa fa-fw fa-eye" style="color: #03a9f4;"></span> View</a></li>';
            $dropdownItems .= $view;
        }

        if ($approvedYN == 0 && $confirmedYN == 1) {
            $referBack = '<li><a onclick="' . $ref_fn . '"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
            $dropdownItems .= $referBack;
        }

        $dropdownHTML = '
            <div class="btn-group" style="display: flex;justify-content: center;">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                    <li><a onclick=\'attachment_modal(' . $id . ',"'.$wfhApp.'","WFH",'.$confirmedYN.');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment</a></li>
                    ' . $dropdownItems . '
                    <li>' . $print . '</li>
                </ul>
            </div>';

        return $dropdownHTML;
    }
}


if (!function_exists('wfh_action_approval')) { 
    function wfh_action_approval($wfhID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
      
        $purchaseRequest = "WFH Request";

        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $wfhID . ',"'.$purchaseRequest.'","WFH");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $wfhID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'WFH\',\'' . $wfhID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
       
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('leavePolicy_drop')) {
    function leavePolicy_drop()
    {
        $CI =& get_instance();
        $leaveTypes = $CI->db->query("SELECT * FROM srp_erp_leavepolicymaster")->result_array();
        return $leaveTypes;
        /*$i = 0;
        $arr = array('leaveTypeID'=>'', 'description'=>'Select', 'policy'=>'');
        if (isset($leaveTypes)) {
            foreach ($leaveTypes as $row) {
                $arr[$i] = array(
                    'leaveTypeID' => trim($row['leaveTypeID'] ?? ''),
                    'description' => trim($row['description'] ?? ''),
                    'policy' => trim($row['policy'] ?? '')
                );
                $i++;
            }
        }
        return $arr;*/
    }
}

if (!function_exists('leave_action_approval')) { /*get po action list*/
    function leave_action_approval($leaveID, $approvalLevelID, $leaveCode)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick="load_emp_leaveDet(' . $leaveID . ', ' . $approvalLevelID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('fetch_emp_title')) {
    function fetch_emp_title()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("TitleID,TitleDescription");
        $CI->db->FROM('srp_titlemaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('TitleDescription');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_title')/*'Select a title'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['TitleID'] ?? '')] = trim($row['TitleDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_emp_religion')) {
    function fetch_emp_religion()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("RId,Religion");
        $CI->db->FROM('srp_religion');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('Religion');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_religion')/*'Select a Religion'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['RId'] ?? '')] = trim($row['Religion'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_emp_nationality')) {
    function fetch_emp_nationality()
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("NId,Nationality");
        $CI->db->FROM('srp_nationality');
        $CI->db->where('Erp_companyID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_nationality')/*'Select a Nationality'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['NId'] ?? '')] = trim($row['Nationality'] ?? '');
            }
        }
        return $data_arr;
    }
}


if (!function_exists('fetch_emp_departemtns_with_primary')) {
    function fetch_emp_departemtns_with_primary($departments = null,$notassigned = null,$empID = null)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        // $CI->db->SELECT("se.EmpID,se.DepartmentMasterID,sd.DepartmentDes,se.isPrimary,semp.ECode,semp.Ename1,semp.Ename2");
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_empdepartments as se');
        $CI->db->JOIN('srp_departmentmaster as sd','se.DepartmentMasterID = sd.DepartmentMasterID','left');
        $CI->db->JOIN('srp_employeesdetails as semp','se.EmpID = semp.EIdNo','left');
        $CI->db->where('se.Erp_companyID', $companyID);
        $CI->db->where('se.isActive', 1);
        $CI->db->where('se.isPrimary', 1);
        $CI->db->where('semp.isDischarged', 0);
        $CI->db->where('semp.isSystemAdmin', 0);
        $CI->db->where('semp.empConfirmedYN', 1);
        
        if($empID){
            $CI->db->where('semp.EIdNo', $empID);
        }
        // $where = '';//'(semp.isMobileCheckIn != "1")';

        // if($notassigned){
        //     $CI->db->where($where);
        // }
        
        // $departments_arr = explode(',',$departments);

        // if($departments_arr){
        //     $CI->db->where_in('se.DepartmentMasterID', $departments_arr);
        // }

        $data = $CI->db->get()->result_array();

        return $data;
    }
}

if (!function_exists('fetch_emp_manages_self_service')) {
    function fetch_emp_manages_self_service($empID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $data = $CI->db->query("SELECT emp.EIdNo as EmpID,emp.ECode,emp.Ename1,emp.Ename2,dpt.DepartmentMasterID,emp.isCheckin,emp.isMobileCheckIn
        FROM srp_employeesdetails AS emp
        LEFT JOIN srp_erp_employeemanagers AS mg ON mg.empID = emp.EIdNo AND mg.active=1
        LEFT JOIN srp_empdepartments AS dpt ON dpt.empID = emp.EIdNo AND dpt.isPrimary=1 
        WHERE emp.Erp_companyID={$companyID} AND emp.isMobileCheckIn !=1 AND emp.isSystemAdmin = 0 AND emp.isDischarged = 0 AND emp.empConfirmedYN = 1 AND emp.isCheckin IS NULL AND mg.managerID= ".current_userID()."")->result_array();
        
        return $data;
    }
}

if (!function_exists('fetch_emp_attendees_self_service')) {
    function fetch_emp_attendees_self_service($empID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_employee_attendees as se');
        $CI->db->where('se.companyID', $companyID);
        $CI->db->where('se.empID', current_userID());
        $All_attendees = $CI->db->get()->result_array();

      
        $all_attendees_array =[];
        if(count( $All_attendees)>0){
            foreach($All_attendees as $val){
                $all_attendees_array[]=$val['attendeeID'];
            }
        }

        $join = "('" . implode("','", $all_attendees_array) . "')";

        $wherein = ' AND emp.EIdNo IN ' . $join . '';

        $data = $CI->db->query("SELECT emp.EIdNo as EmpID,emp.ECode,emp.Ename1,emp.Ename2,emp.isCheckin,emp.isMobileCheckIn
        FROM srp_employeesdetails AS emp
        WHERE emp.Erp_companyID={$companyID} AND emp.isMobileCheckIn !=1 AND emp.isSystemAdmin = 0 AND emp.isDischarged = 0 AND emp.empConfirmedYN = 1 AND ( emp.isCheckin IS NULL OR  emp.isCheckin = 0 ) $wherein")->result_array();
        
        //  / LEFT JOIN srp_empdepartments AS dpt ON dpt.empID = emp.EIdNo AND dpt.isPrimary=1 

        return $data;
    }
}

if (!function_exists('fetch_emp_contract_ongoing')) {
    function fetch_emp_contract_ongoing($empID,$att_date)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("sc.empID,sc.dateFrom,sc.dateTo,sj.job_code,sj.job_name,sj.job_reference,sj.id as jobID,sec.contractCode,sec.contractCode,sec.referenceNo,sec.contractNarration");
        $CI->db->FROM('srp_erp_job_crewdetail as sc');
        $CI->db->join('srp_erp_jobsmaster as sj','sc.job_id = sj.id','left');
        $CI->db->join('srp_erp_contractmaster as sec','sj.contract_po_id = sec.contractAutoID','left');
        $CI->db->where('sc.empID', $empID);
        $CI->db->where('DATE(sc.dateFrom) <=', $att_date);
        $CI->db->where('DATE(sc.dateTo) >=', $att_date);
        $CI->db->where_in('sj.job_status', [2,4]);
        $data = $CI->db->get()->result_array();

        return $data;
    }
}

if (!function_exists('fetch_emp_shift_details')) {
    function fetch_emp_shift_details($empID,$detail_date = null)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        if($detail_date){
            $now = new DateTime($detail_date);
            $weekday = $now->format('N');
        }

        if($detail_date){
            $CI->db->SELECT("se.empID,shm.Description,se.shiftID,shmdetail.onDutyTime,shmdetail.offDutyTime");
        }else{
            $CI->db->SELECT("se.empID,shm.Description,se.shiftID");
        }
       
        $CI->db->FROM('srp_erp_pay_shiftemployees as se');
        $CI->db->join('srp_erp_pay_shiftmaster as shm','se.shiftID = shm.shiftID','left');
        // $CI->db->join('srp_erp_contractmaster as sec','sj.contract_po_id = sec.contractAutoID','left');
        $CI->db->where('se.empID', $empID);

        if($detail_date){
            $CI->db->join('srp_erp_pay_shiftdetails as shmdetail','se.shiftID = shmdetail.shiftID','left');
            $CI->db->where('shmdetail.dayID', $weekday);
            $CI->db->order_by('shmdetail.shiftDetailID', 'DESC');
        }
        
        
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('fetch_emp_shift_details_latest')) {
    function fetch_emp_shift_details_latest($empID)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("se.empID,shm.Description,se.shiftID");
        $CI->db->FROM('srp_erp_pay_shiftemployees as se');
        $CI->db->join('srp_erp_pay_shiftmaster as shm','se.shiftID = shm.shiftID','left');
        // $CI->db->join('srp_erp_contractmaster as sec','sj.contract_po_id = sec.contractAutoID','left');
        $CI->db->where('se.empID', $empID);
        $CI->db->order_by('se.autoID', 'DESC');
    
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('get_employee_defined_weekends')) {
    function get_employee_defined_weekends($from_date,$to_date,$emp_list)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $base_array = array();
        $weekend_days = array();

        foreach($emp_list as $employeeID){
            $emp_shift = fetch_emp_shift_details_latest($employeeID);

            if($emp_shift){
          
                $shiftID = $emp_shift['shiftID'];
                $shift_details = fetch_emp_shift_master_details($shiftID,1);
               
                if($shift_details){
                    foreach($shift_details as $shift){
                        if(isset($base_array[$employeeID]) && (!in_array($shift['weekDayNo'] , $base_array[$employeeID]))){
                            $base_array[$employeeID][] = $shift['weekDayNo'];
                        }elseif(!isset($base_array[$employeeID])){
                            $base_array[$employeeID][] = $shift['weekDayNo'];
                        }
                    }
                }
            }

            $now = strtotime($from_date);
            $end_date = strtotime($to_date);
       
            while (date("Y-m-d", $now) != date("Y-m-d", $end_date)) {
                $day_index = date("w", $now);
               
                if (isset($base_array[$employeeID]) && (in_array($day_index,$base_array[$employeeID]))) {
                    // Print or store the weekends here
                    $weekend_days[$employeeID][] = date('Y-m-d',strtotime("+1 days",$now));
                }

                $now = strtotime(date("Y-m-d", $now) . "+1 day");
            }

    
          
        }

        return $weekend_days;
       
    }
}


if (!function_exists('fetch_send_employee_weekend_arr')) {
    function fetch_send_employee_weekend_arr($weekend_date,$attendance_weekend)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $base_arr = array('isWeekEndDay' => 1, 
            'approvedComment'=> '',
            'approvedYN'=> '1',
            'confirmedYN'=> '1',
            'ID'=> '1',
            'empID'=> $attendance_weekend['empID'],
            'ECode'=> $attendance_weekend['ECode'],
            'empMachineID'=> $attendance_weekend['empMachineID'],
            'Ename2'=> $attendance_weekend['Ename2'],
            'floorDescription'=>  isset($attendance_weekend['floorDescription']) ? $attendance_weekend['floorDescription'] : $attendance_weekend['clockinFloorDescription'],
            'clockinFloorDescription'=>  isset($attendance_weekend['floorDescription']) ? $attendance_weekend['floorDescription'] : $attendance_weekend['clockinFloorDescription'],
            'clockoutFloorDescription'=>  isset($attendance_weekend['floorDescription']) ? $attendance_weekend['floorDescription'] : $attendance_weekend['clockoutFloorDescription'],
            'machineID'=> '',
            'floorID'=> $attendance_weekend['floorID'],
            'attendanceDate'=> $weekend_date,
            'PresentTypeDes'=>'Weekend',
            'presentTypeID'=> '7',
            'checkInDate'=> '',
            'checkOutDate'=> '',
            'isShiftNextDay'=> '0',
            'specialOThours'=> '0',
            'checkIn'=> '',
            'checkOut'=> '',
            'onDuty'=> '',
            'offDuty'=> '',
            'lateHours'=> '',
            'earlyHours'=> '',
            'OTHours'=> '',
            'mustCheck'=> '',
            'normalDay'=> '',
            'normalTime'=> '',
            'weekend'=> '',
            'holiday'=> '',
            'NDaysOT'=> '',
            'weekendOTHours'=> '',
            'holidayOTHours'=> '',
            'realTime'=> ''
        );

        return $base_arr;
    }
}



if (!function_exists('fetch_emp_shift_master_details')) {
    function fetch_emp_shift_master_details($shiftID,$onlyWeekends = null)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("details.dayID,details.weekDayNo,details.isWeekend");
        $CI->db->FROM('srp_erp_pay_shiftdetails as details');
        if($onlyWeekends){
            $CI->db->where('details.isWeekend', 1);
        }
        $CI->db->where('details.companyID', $companyID);

        $data = $CI->db->get()->result_array();

        return $data;
    }
}


if (!function_exists('fetch_emp_contract_details')) {
    function fetch_emp_contract_details($contractCode)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("scm.contractAutoID,scm.contractCode,scm.referenceNo,scm.contractNarration");
       // $CI->db->FROM('srp_erp_contractcrew as sc');
        $CI->db->FROM('srp_erp_contractmaster as scm');
        $CI->db->where('scm.contractCode', $contractCode);
    
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('fetch_emp_contract_details_primary')) {
    function fetch_emp_contract_details_primary($emp_id)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("scm.contractAutoID,scm.contractCode,scm.referenceNo,scm.contractNarration");
        $CI->db->FROM('srp_erp_contractcrew as sc');
        $CI->db->JOIN('srp_erp_contractmaster as scm','sc.contractAutoID = scm.contractAutoID','left');
        $CI->db->where('sc.empID', $emp_id);
        $CI->db->where('sc.isPrimary', 1);
    
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('fetch_emp_approved_no_pay')) {
    function fetch_emp_approved_no_pay($date,$presentTypeID = null)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_pay_empattendancereview as sc');
        $CI->db->where('approvedYN', 1);
        $CI->db->where('attendanceDate', $date);

        if($presentTypeID){
            $CI->db->where('presentTypeID', $presentTypeID);
        }
    
        $data = $CI->db->get()->result_array();

        return $data;
    }
}

if (!function_exists('fetch_emp_attandance_view_record')) {
    function fetch_emp_attandance_view_record($empViewID = null)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_pay_empattendancereview as sc');
        $CI->db->where('ID', $empViewID);
        $data = $CI->db->get()->row_array();

        return $data;
    }
}



if (!function_exists('fetch_emp_variable_attandance_value')) {
    function fetch_emp_variable_attandance_value($empViewID = null,$mothlyDeclarationID = null)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_pay_variable_empattendancereview as sc');
        $CI->db->where('empattendancereviewID', $empViewID);
        if($mothlyDeclarationID){
            $CI->db->where('monthlyDeclarationID', $mothlyDeclarationID);
        }
        $data = $CI->db->get()->row_array();

        return $data;
    }
}





if (!function_exists('get_shift_worked_emp')) {
    function get_shift_worked_emp($dateFrom,$dateTo,$shift_start,$shift_end,$att_date)
    {
        
        $attandance_date = date('Y-m-d',strtotime($att_date));
        $dateFrom_date = date('Y-m-d',strtotime($dateFrom));
        $dateTo_date = date('Y-m-d',strtotime($dateTo));

        $actual_shift_start = '';
        $actual_shift_end = $shift_end;

        $dateTo_time_temp = date('H:i:s',strtotime($dateTo));
        $actual_check_out = $dateTo_time_temp;

        if($dateFrom_date == $dateTo_date){
            //same date
            $dateFrom_time = date('H:i:s',strtotime($dateFrom));
            $dateTo_time = date('H:i:s',strtotime($dateTo));

            $actual_shift_start = $dateFrom_time;
            $actual_shift_end = $dateTo_time;

        }else{

            if($dateFrom_date == $attandance_date){
                $dateFrom_time = date('H:i:s',strtotime($dateFrom));

                if($shift_start < $dateFrom_time){
                    $actual_shift_start = $shift_start;
                }else {
                    $actual_shift_start = $dateFrom_time;
                }


            }elseif($dateTo_date == $attandance_date){
                $dateTo_time = date('H:i:s',strtotime($dateTo));

                if($shift_start < $dateTo_time){
                    $actual_shift_start = $shift_start;
                }else {
                    $actual_shift_start = $dateTo_time;
                }


            }else{

                $actual_shift_start = $shift_start;
                $actual_check_out = $shift_end;

            }

        }

        return array('start_time'=>$actual_shift_start,'end_time'=>$actual_shift_end,'actual_check_out' => $actual_check_out);
       
    }
}






if (!function_exists('fetch_emp_maritialStatus')) {
    function fetch_emp_maritialStatus($returnType=0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("maritialstatusID,description");
        $CI->db->FROM('srp_erp_maritialstatus');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_maritial_status')/*'Select a Maritial Status'*/);

        if( $returnType == 1){
            return $data;
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['maritialstatusID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_sysEmpContractType')) {
    function fetch_sysEmpContractType()
    {
        $CI =& get_instance();
        $CI->db->SELECT("employeeTypeID,employeeType");
        $CI->db->FROM('srp_erp_systememployeetype');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => ''); //Select a Employee Status
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['employeeTypeID'] ?? '')] = trim($row['employeeType'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_employee_details_columns')) {
    function fetch_employee_details_columns()
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_employeedetailreport');
        $data = $CI->db->get()->result_array();
        $data_arr = [];
        $d = [];
        if (isset($data)) {
            foreach ($data as $row) {
                $d[] = ($row['columnName']);
                $data_arr[trim($row['columnName'] ?? '')] = trim($row['columnTitle'] ?? '');
            }
        }

        //echo implode(', ', $d);

        return $data_arr;
    }
}

if (!function_exists('fetch_empContractType')) {
    function fetch_empContractType($drop = null)
    {
        $CI =& get_instance();
        $CI->db->SELECT("EmpContractTypeID, Description, employeeTypeID, period, probation_period");
        $CI->db->FROM('srp_empcontracttypes AS t1');
        $CI->db->JOIN('srp_erp_systememployeetype AS t2', 't1.typeID=t2.employeeTypeID');
        $CI->db->WHERE('Erp_CompanyID', current_companyID());
        $data = $CI->db->get()->result_array();

        if($drop == 'drop'){
            $data_arr = array('' => ''); //Select a Employee Status
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['EmpContractTypeID'] ?? '')] = trim($row['Description'] ?? '');
                }
            }
            return $data_arr;

        }else{
            return $data;
        }

    }
}

if (!function_exists('fetch_emp_blood_type')) {
    function fetch_emp_blood_type($returnType=0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("BloodTypeID,BloodDescription");
        $CI->db->FROM('srp_erp_bloodgrouptype');
        $data = $CI->db->get()->result_array();

        if($returnType ==1){
            return $data;
        }

        $data_arr = array('' => $CI->lang->line('common_select_a_blood_group')/*'Select a Blood Group'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['BloodTypeID'] ?? '')] = trim($row['BloodDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_emp_designation')) {
    function fetch_emp_designation()
    {
        $CI =& get_instance();
        $CI->db->SELECT("DesignationID,DesDescription");
        $CI->db->FROM('srp_designation');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->WHERE('isDeleted', 0);
        $CI->db->order_by('DesDescription');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Designation');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['DesignationID'] ?? '')] = trim($row['DesDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_emp_countries')) {
    function fetch_emp_countries()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("countryID,CountryDes");
        $CI->db->FROM('srp_countrymaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('CountryDes');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' =>$CI->lang->line('common_select_country')/* 'Select Country'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['countryID'] ?? '')] = trim($row['CountryDes'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_emp_departments')) {
    function fetch_emp_departments($select = null,$with_select = null)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_departmentmaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->WHERE('isActive', 1);
        $data = $CI->db->get()->result_array();

        $data_arr = array();
        
        if($with_select){
            $data_arr = array('' => 'Select Department');
        }
        if($select){
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['DepartmentMasterID'] ?? '')] = trim($row['DepartmentDes'] ?? '');
                }
            }

            return $data_arr;
        }
        
        return $data;
    }
}

if (!function_exists('empMaster_action')) {
    function empMaster_action($id, $empName, $type1=null)
    {

        $action = '<a onclick="edit_empDet(' . $id . ', \'' . $empName . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if( $type1 == null ){
            $action = '<span class="pull-right">' . $action . '</span>';
        }
        else{
            $action = '<a onclick="edit_empDet(' . $id . ', \'\')">' . $empName . '</a>';
        }
        return $action;
    }
}

if (!function_exists('empCodeGenerate')) {
    function empCodeGenerate($tibianType=null)
    {
        //Generate Employee Code
        $CI =& get_instance();
        $CI->load->library('sequence');

        if($tibianType == null){
            return $CI->sequence->sequence_generator('EMP');
        }

        return $CI->sequence->sequence_generator_employee($tibianType);
    }
}


if (!function_exists('empCodeGenerateTemp')) {
    function empCodeGenerateTemp($tibianType=null)
    {
        //Generate Employee Code
        $CI =& get_instance();
        $CI->load->library('sequence');

        if($tibianType == null){
            return $CI->sequence->sequence_generator_temp('EMP');
        }

        return $CI->sequence->sequence_generator_employee($tibianType);
    }
}

if (!function_exists('current_schMasterID')) {
    function current_schMasterID()
    {
        $CI =& get_instance();
        return trim($CI->common_data['company_data']['company_link_id']);
    }
}

if (!function_exists('current_schBranchID')) {
    function current_schBranchID()
    {
        $CI =& get_instance();
        return trim($CI->common_data['company_data']['branch_link_id']);
    }
}

if (!function_exists('action_religion')) {
    function action_religion($RId, $Religion, $usageCount)
    {
        $Religion = "'" . $Religion . "'";
        $action = '<a onclick="edit_religion(' . $RId . ', ' . $Religion . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_religion(' . $RId . ', ' . $Religion . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_country')) {
    function action_country($countryID, $CountryDes, $usageCount, $cityName = null)
    {
        $CountryDes = "'" . $CountryDes . "'";
        $action = '';
        if ($usageCount == 0) {
            $action .= '<a onclick="openCityModel(' . $countryID . ',\'' . $cityName . '\')">';
            $action .= '<span title="Add City" rel="tooltip" class="glyphicon glyphicon-cog" style="color:blue;"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;';
            $action .= '<a onclick="deleteCountry(' . $countryID . ', ' . $CountryDes . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_selectCountry')) {
    function action_selectCountry($countryID, $name, $code)
    {
        $outPut = '<div align="center">';
        $outPut .= '<input type="checkbox" name="countrySelChk[]" class="countrySelChk" style="margin: 0px"';
        $outPut .= 'value="' . $countryID . '" data-name="' . $name . '" data-code="' . $code . '">';
        $outPut .= '</div>';
        return $outPut;
    }
}

if (!function_exists('action_designation')) {
    function action_designation($DesignationID, $DesDescription, $usageCount)
    {
        $DesDescription = "'" . $DesDescription . "'";
        $action = '<a onclick="openJDDescription_modal(' . $DesignationID . ', ' . $DesDescription . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-file glyphicon-eye-open-btn"></span></a>&nbsp;&nbsp;<a onclick="edit_designation(' . $DesignationID . ', ' . $DesDescription . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp;<a onclick="delete_designation(' . $DesignationID . ', ' . $DesDescription . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_job_category')) {
    function action_job_category($ID, $DesDescription, $usageCount)
    {
        $usageCount=0;
        $DesDescription = "'" . $DesDescription . "'";
        $action = '';

        if ($usageCount == 0) {
            $action .= '<a onclick="deleteJobCategory(' . $ID . ', ' . $DesDescription . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_department')) {
    function action_department($departmentID, $depDescription, $isActive, $usageCount)
    {
        $depDescription = "'" . $depDescription . "'";
        $action = '<a onclick="edit_department(' . $departmentID . ', ' . $depDescription . ', ' . $isActive . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_department(' . $departmentID . ', ' . $depDescription . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_qualification')) {
    function action_qualification($certificateID, $description)
    {
        $description = "'" . $description . "'";
        $action = '<span class="glyphicon glyphicon-pencil editIcon" data-id="' . $certificateID . '" style="color:#3c8dbc"></span>';
        $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_Qualification(' . $certificateID . ', ' . $description . ')">';
        $action .= '<span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_floor')) {
    function action_floor($floorID, $depDescription, $isActive, $latitude, $longitude, $locationRadius, $usageCount)
    {
        $depDescription = "'" . $depDescription . "'";
        $action = '<a onclick="edit_floor(' . $floorID . ', ' . $depDescription . ', \'' . $latitude . '\', \'' . $longitude . '\', \'' . $locationRadius . '\', \'' . $isActive . '\')">';
        $action .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_floor(' . $floorID . ', ' . $depDescription . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('action_empDesignation')) {
    function action_empDesignation($EmpDesignationID, $DesDescription, $isMajor)
    {
        $DesDescription = "'" . $DesDescription . "'";

        $action = '<a onclick="edit_empDesignation(this)"><span class="glyphicon glyphicon-pencil"></span></a>';

        if( $isMajor != 21 ){
            $action .= '&nbsp; | &nbsp;';
            $action .= '<a onclick="delete_empDesignation(' . $EmpDesignationID . ', ' . $DesDescription . ')">';
            $action .= '<span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_empDepartment')) {
    function action_empDepartment($EmpDepartmentID, $DesDepartment)
    {
        /*$DesDepartment = "'" . $DesDepartment . "'";
        $action = '<a onclick="edit_empDepartments(' . $EmpDepartmentID . ', ' . $DesDepartment . ')"><span class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_empDepartments(' . $EmpDepartmentID . ', ' . $DesDepartment . ')">';
        $action .= '<span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';*/

        $DesDepartment = "'" . $DesDepartment . "'";
        $action = '<a onclick="delete_empDepartments(' . $EmpDepartmentID . ', ' . $DesDepartment . ')">';
        $action .= '<span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('department_status')) {
    function department_status($autoID, $status)
    {
        $checked = ($status == 1) ? 'checked' : '';
        //return '<input type="checkbox" class="switch-chk" id="status_'.$autoID.'" data-id="'.$autoID.'" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" '.$checked.'>';
        return '<input type="checkbox" class="switch-chk" id="status_' . $autoID . '" onchange="changeStatus(' . $autoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" ' . $checked . '>';
    }
}

if (!function_exists('action_docSetup')) {
    function action_docSetup($DocDesID, $DocDescription)
    {
        $DocDescription = "'" . $DocDescription . "'";
        $action = '<a onclick="edit_docSetup(' . $DocDesID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_docSetup(' . $DocDesID . ', ' . $DocDescription . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('leave_group_change_history_action')) {
    function leave_group_change_history_action($id, $adjustmentDone)
    {
        $action = '';
        switch($adjustmentDone){
            case 0:
                $action = '<a onclick="newLeaveAdjustment(' . $id . ')" class="action-div">Create</a>';
                //$action .= '&nbsp; | &nbsp; <a onclick="skipLeaveAdjustment(' . $id . ')" class="action-div">Skip</a>';
            break;

            case 1:
                $action = '<a onclick="getLeaveAdjustment(' . $id . ')" class="action-div">view</a>';
            break;
        }

        return '<div style="text-align: center; font-weight: bold;">' . $action . '</div>';

    }
}

if (!function_exists('mandatoryStatus')) {
    function mandatoryStatus($isMandatory)
    {
        return ($isMandatory == 1) ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
    }
}

if (!function_exists('allDocument_drop')) {
    function allDocument_drop($type = 0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("t1.DocDesID,DocDescription");
        $CI->db->FROM('srp_documentdescriptionmaster t1');
        $CI->db->WHERE('t1.Erp_companyID', current_companyID());
        $CI->db->order_by('DocDescription');
        $data = $CI->db->get()->result_array();


        if ($type == 0) {
            $data_arr = array('' => $CI->lang->line('common_select_document')/*'Select Document'*/);
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['DocDesID'] ?? '')] = trim($row['DocDescription'] ?? '');
                }
                return $data_arr;
            }
        } else {
            return $data;
        }

    }
}

if (!function_exists('emp_document_drop')) {
    function emp_document_drop()
    {
        $CI =& get_instance();
        $CI->db->select("t1.DocDesID,DocDescription,t1.systemTypeID,t3.issuedByType");
        $CI->db->from('srp_documentdescriptionmaster t1');
        $CI->db->join('srp_documentdescriptionsetup t2', 't1.DocDesID=t2.DocDesID');
        $CI->db->join('srp_erp_system_document_types t3', 't3.id=t1.systemTypeID');
        $CI->db->where('t1.Erp_companyID', current_companyID());
        $CI->db->where('t1.isDeleted', 0);
        $CI->db->where('FormType', 'EMP');
        $CI->db->order_by('DocDescription');
        $data = $CI->db->get()->result_array();
        return $data;
        $data_arr = array('' => 'Select Document');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['DocDesID'] ?? '')] = trim($row['DocDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('segment_drop')) {
    function segment_drop()
    {
        $CI =& get_instance();
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('srp_erp_segment.companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Segment');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('floors_drop')) {
    function floors_drop($isFromAttPulling=0,$check_isActive=1,$without_select = null)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('floorID, floorDescription');
        $CI->db->from('srp_erp_pay_floormaster');
        $CI->db->where('companyID', current_companyID());
        if($check_isActive == 1){
            $CI->db->where('isActive', 1);
        }
        $data = $CI->db->get()->result_array();
      

        $place_holder = (IS_OMAN_OIL == false)? $CI->lang->line('common_select_floor'): $CI->lang->line('common_Location');
        $data_arr = [''=>$place_holder];
        if($isFromAttPulling == 1) {
            $data_arr = array('' => $CI->lang->line('common_select_location')); /*'Select a Location'*/
        }

        if($without_select){
            $data_arr = array();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['floorID'] ?? '')] = trim($row['floorDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('expenseGL_drop')) {
    function expenseGL_drop($asResult = null)
    {

        $CI =& get_instance();
        $CI->db->select("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('subCategory ', 'PLE');
        $CI->db->where('approvedYN', 1);
        $CI->db->order_by('GLSecondaryCode');
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();

        if ($asResult != null) {
            $data_arr = array('' => '');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
                }
            }
            return $data_arr;
        } else {
            return $data;
        }

    }
}

if (!function_exists('monthly_additionDeductionGL_drop')) {
    function monthly_additionDeductionGL_drop($asResult = null)
    {

        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                        FROM srp_erp_chartofaccounts
                        WHERE masterAccountYN = 0 AND isBank = 0 AND isActive = 1 AND accountCategoryTypeID!=4
                        AND approvedYN = 1 AND companyID = {$companyID} ORDER BY GLSecondaryCode")->result_array();

        if ($asResult != null) {
            $data_arr = array('' => '');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
                }
            }
            return $data_arr;
        } else {
            return $data;
        }

    }
}


if (!function_exists('declaration_drop')) {
    function declaration_drop($AD, $isPayrollCategory,$isVariable = null,$linkType = null)
    {
        $CI =& get_instance();
        $CI->db->SELECT("monthlyDeclarationID, monthlyDeclaration, GLAutoID,GLSecondaryCode, GLDescription, salaryCategoryID");
        $CI->db->FROM('srp_erp_pay_monthlydeclarationstypes AS decType');
        $CI->db->JOIN('srp_erp_chartofaccounts AS chartAcc', 'chartAcc.GLAutoID=decType.expenseGLCode');
        $CI->db->WHERE('monthlyDeclarationType', $AD);
        if($isVariable){
            $CI->db->WHERE('decType.isVariable', $isVariable);
        }else{
            $CI->db->WHERE('decType.isVariable !=', 1);
        }
        
        if($linkType){
            $CI->db->WHERE_IN('decType.linkType', [2,3]);
        }
        $CI->db->WHERE('isPayrollCategory', $isPayrollCategory);
        $CI->db->WHERE('decType.companyID', current_companyID());
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('declaration_drop_MAC')) {
    function declaration_drop_MAC($AD, $isPayrollCategory, $isVariable = null, $linkType = null)
    {
        $CI =& get_instance();
        $CI->db->SELECT("monthlyDeclarationID, monthlyDeclaration, GLAutoID, GLSecondaryCode, GLDescription, salaryCategoryID");
        $CI->db->FROM('srp_erp_pay_monthlydeclarationstypes AS decType');
        $CI->db->JOIN('srp_erp_chartofaccounts AS chartAcc', 'chartAcc.GLAutoID = decType.expenseGLCode');
        $CI->db->JOIN('srp_erp_employeeclaimassign AS claimassign', 'claimassign.declarationTypeID = decType.monthlyDeclarationID', 'left');
        $CI->db->JOIN('srp_employeesdetails AS emptb', 'emptb.gradeID = claimassign.gradeID', 'left');
        $CI->db->WHERE('claimassign.isActive', 1); 
        $CI->db->WHERE('emptb.EIdNo', current_userID()); 
        $CI->db->WHERE('monthlyDeclarationType', $AD);

        if ($isVariable === null) {
            $CI->db->WHERE('decType.isVariable IS NULL');
        } else {
            $CI->db->WHERE('decType.isVariable !=', 1);
        }

        if ($linkType) {
            $CI->db->WHERE_IN('decType.linkType', [2, 3]);
        }

        $CI->db->WHERE('isPayrollCategory', $isPayrollCategory);
        $CI->db->WHERE('decType.companyID', current_companyID()); 

        return $CI->db->get()->result_array();
    }
}

// if (!function_exists('declaration_drop_MAC')) {
//     function declaration_drop_MAC($AD, $isPayrollCategory,$isVariable = null,$linkType = null)
//     {
//         $CI =& get_instance();
//         $CI->db->SELECT("monthlyDeclarationID, monthlyDeclaration, GLAutoID,GLSecondaryCode, GLDescription, salaryCategoryID");
//         $CI->db->FROM('srp_erp_pay_monthlydeclarationstypes AS decType');
//         $CI->db->JOIN('srp_erp_chartofaccounts AS chartAcc', 'chartAcc.GLAutoID=decType.expenseGLCode');
//         $CI->db->JOIN('srp_erp_employeeclaimassign AS claimassign', 'claimassign.declarationTypeID=decType.monthlyDeclarationID','left');
//         $CI->db->JOIN('srp_employeesdetails AS emptb', 'emptb.gradeID=claimassign.gradeID','left');
//         $CI->db->WHERE('emptb.EIdNo',current_userID());
//         $CI->db->WHERE('monthlyDeclarationType', $AD);
//         if($isVariable){
//             $CI->db->WHERE('decType.isVariable', $isVariable);
//         }else{
//             $CI->db->WHERE('decType.isVariable !=', 1);
//         }
        
//         if($linkType){
//             $CI->db->WHERE_IN('decType.linkType', [2,3]);
//         }
//         $CI->db->WHERE('isPayrollCategory', $isPayrollCategory);
//         $CI->db->WHERE('decType.companyID', current_companyID());
//         return $CI->db->get()->result_array();
//     }
// }

if (!function_exists('get_empApprovedView')) {
    function get_empApprovedView($dateFrom,$dateTo)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_pay_empattendancereview AS empView');
        //$CI->db->WHERE("DATE_FORMAT(empView.attendanceDate,'%Y-%m')", $dateMonth);
        $CI->db->WHERE("empView.attendanceDate >=", $dateFrom);
        $CI->db->WHERE("empView.attendanceDate <", $dateTo);
        $CI->db->WHERE('empView.companyID', current_companyID());
        // $CI->db->WHERE('empView.approvedYN', 1);
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('get_variable_payRecords')) {
    function get_variable_payRecords($empattendancereviewID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_pay_variable_empattendancereview AS empView');
        $CI->db->JOIN('srp_erp_pay_empattendancereview AS payEmp','empView.empattendancereviewID = payEmp.ID','left');
        $CI->db->WHERE("empView.empattendancereviewID", $empattendancereviewID);
        $CI->db->WHERE('empView.approvedYN', 1);
        $CI->db->WHERE('empView.value >', 0);
        $CI->db->WHERE('empView.pulledStatus !=',1);
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('get_attendance_review_records')) {
    function get_attendance_review_records($dateFrom,$dateTo,$presentTypeID = null,$approvedYN = null,$emplist = null)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_pay_empattendancereview AS empView');
        $CI->db->JOIN('srp_employeesdetails AS emp','empView.empID = emp.EIdNo','left');
        $CI->db->WHERE("emp.isNoPayAbsent", 1);      
        $CI->db->WHERE("empView.isWeekEndDay !=", 1);      

        if($emplist){
            $emp_arr = explode(',',$emplist);
            $CI->db->WHERE_IN("empView.empID", $emp_arr);
        }

        if($presentTypeID){
            $CI->db->WHERE_IN("empView.presentTypeID", [4]);           
        }

        if($approvedYN){
            $CI->db->WHERE("confirmedYN", 1);
            $CI->db->WHERE("approvedYN !=", 1);
        }else{
            $CI->db->WHERE("approvedYN", 1);
        }

        $CI->db->WHERE("empView.attendanceDate >=", $dateFrom);
        $CI->db->WHERE("empView.attendanceDate <=", $dateTo);

        return $CI->db->get()->result_array();
    }
}


if (!function_exists('get_salary_category')) {
    function get_salary_category($type = 3)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_pay_salarycategories AS sal');
        $CI->db->WHERE('sal.payrollCatID', $type);
        $CI->db->WHERE('sal.isPayrollCategory', 1);
        $CI->db->WHERE('sal.companyID', current_companyID());
        // $CI->db->WHERE('empView.approvedYN', 1);
        return $CI->db->get()->row_array();
    }
}



if (!function_exists('get_item_emp_image')) {
    function get_item_emp_image($empImage)        {

        //$path = base_url('images/item/');

        if ($empImage == '' || $empImage == NULL) {
            $img_item = base_url()."default.gif";
        } else {
            $img_item = base_url().$empImage;
        }

        $generatedHTML = "<center><img class='img-thumbnail' alt='No image' src='$img_item' style='width:50px;height: 50px;'><center>";
        return $generatedHTML;

    }
}

if (!function_exists('empImage')) {
    function empImage($imgPath)
    {
        $filePath = imagePath() . $imgPath;
        $emp_img = checkIsFileExists($filePath);
        return $emp_img;
    }
}

if (!function_exists('empImage_s3')) {
    function empImage_s3($imgPath, $gender, $male, $female)
    {
        if($imgPath == ''){
            $imgPath = ($gender == 1)? $male: $female;
        }
        elseif ($imgPath == 'images/users/male.png'){
            $imgPath = $male;
        }
        elseif ($imgPath == 'images/users/female.png'){
            $imgPath = $female;
        }
        else{
            $CI =& get_instance();
            $CI->load->library('s3');
            $imgPath = $CI->s3->createPresignedRequest($imgPath, '+1 hour');
        }
        return $imgPath;
    }
}

if (!function_exists('single_emp_image_s3_with_validation')) {
    function single_emp_image_s3_with_validation($imgPath, $gender)
    {
        $CI =& get_instance();
        $CI->load->library('s3');

        if( !empty($imgPath) ){
            $imgPath = $CI->s3->createPresignedRequest($imgPath, '+2 hour');
        }
        else{
            $imgPath = ($gender == 1)? 'male.png' : 'female.png';
            $imgPath = $CI->s3->createPresignedRequest("images/users/$imgPath", '+2 hour');
        }
        return $imgPath;
    }
}

if (!function_exists('multiple_emp_image_s3_with_validation')) {
    function multiple_emp_image_s3_with_validation($imgPath, $gender, $male, $female)
    {
        if($imgPath == ''){
            $imgPath = ($gender == 1)? $male: $female;
        }
        elseif ($imgPath == 'images/users/male.png'){
            $imgPath = $male;
        }
        elseif ($imgPath == 'images/users/female.png'){
            $imgPath = $female;
        }
        else{
            $CI =& get_instance();
            $CI->load->library('s3');

            if( !empty($imgPath) ){
                $imgPath = $CI->s3->createPresignedRequest($imgPath, '+1 hour');
            }
            else{
                $imgPath = ($gender == 1)? $male : $female;
            }
        }
        return $imgPath;
    }
}

if (!function_exists('empImageCheck')) {
    function empImageCheck($imageName, $empType)
    {
        $not_available = 0;

        if(!empty($imageName)){
            $filePath = imagePath() . $imageName;
            $ret = FALSE;
            if (file_exists(UPLOAD_PATH . '' . $filePath)) {
                $ret = TRUE;
            }

            if($ret == TRUE){
                return $filePath;
            }
            else{
                $not_available = 1;
            }
        }else{
            $not_available = 1;
        }

        if($not_available == 1){

            if($empType == 'signature'){
                $img = 'No_Image.png';
            }else{
                $img = ($empType == 2)? 'female.png' :'male.png';
            }

            return imagePath().$img;
        }

    }
}

if (!function_exists('attendanceType_drop')) {
    function attendanceType_drop($isMultiSelect=false)
    {
        $CI =& get_instance();
        $CI->db->select("PresentTypeID, PresentTypeDes");
        $CI->db->from('srp_sys_attpresenttype t1');
        $CI->db->join('srp_attpresenttype t2', 't1.PresentTypeID = SysPresentTypeID');
        $CI->db->where('Erp_companyID', current_companyID());
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => '');
        if($isMultiSelect){
            $data_arr = [];
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['PresentTypeID'] ?? '')] = trim($row['PresentTypeDes'] ?? '');
            }
        }

        return $data_arr; //return $data;
    }
}

if (!function_exists('system_attendanceTypes')) {
    function system_attendanceTypes()
    {
        $CI =& get_instance();
        $company_id = current_companyID();
        $data = $CI->db->query("SELECT * FROM srp_sys_attpresenttype AS t1 WHERE PresentTypeID NOT IN (
                                  SELECT SysPresentTypeID FROM srp_attpresenttype WHERE SysPresentTypeID=t1.PresentTypeID AND Erp_companyID = {$company_id}
                                ) ")->result_array();

        return $data;
    }
}


if (!function_exists('action_attendanceTypes')) {
    function action_attendanceTypes($AttPresentTypeID, $PresentTypeDes)
    {
        $action = '<a onclick="delete_attendanceTypes(' . $AttPresentTypeID . ', \'' . $PresentTypeDes . '\')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('action_attendanceMaster')) {
    function action_attendanceMaster($EmpAttMasterID, $isAttClosed, $attDate, $type,$pageType)
    {
        $action = '';

        if($type == 1){

            if ($isAttClosed == 1) {
                $action .= '<a onclick="open_attendanceDetailModal(' . $EmpAttMasterID . ', this)"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>';
              //  $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="' . site_url('Employee/attendance_print/') . '/' . $EmpAttMasterID . '/' . $attDate . '" >';
               // $action .= '<span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
            } else {
               // $action .= '<a onclick="open_attendanceDetailModal(' . $EmpAttMasterID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
                $action .= '<a onclick="open_attendanceDetailModalManualEmployee(' . $EmpAttMasterID . ',\'' . $pageType . '\',this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-cog"></span></a>';
                $action .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_attendanceMaster(' . $EmpAttMasterID . ')">';
                $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
    
            }

        }else{
            if ($isAttClosed == 1) {
                $action .= '<a onclick="open_attendanceDetailModal(' . $EmpAttMasterID . ', this)"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>';
              //  $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="' . site_url('Employee/attendance_print/') . '/' . $EmpAttMasterID . '/' . $attDate . '" >';
             //   $action .= '<span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
            } else {
                $action = '<a onclick="open_attendanceDetailModal(' . $EmpAttMasterID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
                $action .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_attendanceMaster(' . $EmpAttMasterID . ')">';
                $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
    
            }
        }
        
        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('action_workShift')) {
    function action_workShift($shiftID, $Description)
    {
        $action = '<a onclick="fetchPage(\'system/hrm/shift_config\',' . $shiftID . ',\'HRMS\', 0, \'' . $Description . '\')">';
        $action .= '<i title="View" rel="tooltip" class="fa fa-fw fa-eye" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        $action .= '<a onclick="edit_shift(' . $shiftID . ', \'' . $Description . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_shift(' . $shiftID . ',  \'' . $Description . '\')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('fetch_employeeShift')) {
    function fetch_employeeShift($shiftID)
    {
        $companyID = current_companyID();

        $CI =& get_instance();
        $query = $CI->db->query("SELECT EIdNo, ECode, IFNULL(Ename1,'') Ename1, IFNULL(Ename2,'') Ename2, IFNULL(Ename3,'') Ename3, IFNULL(Ename4,'') Ename4, EmpImage,shiftEmp.autoID as autoID
                                  FROM srp_employeesdetails AS empMaster
                                  JOIN srp_erp_pay_shiftemployees AS shiftEmp ON shiftEmp.empID=empMaster.EIdNo AND empMaster.Erp_companyID={$companyID}
                                  WHERE shiftID={$shiftID} AND companyID={$companyID}")->result_array();
        return $query;
    }
}

if (!function_exists('fetch_weekDays')) {
    function fetch_weekDays()
    {
        $CI =& get_instance();
        /* $companyID = current_companyID();
         * $query = $CI->db->query("SELECT masterTB.*, shiftDetailID,shiftID,onDutyTime,offDutyTime
                                 FROM srp_weekdays AS masterTB
                                 LEFT JOIN srp_erp_pay_shiftdetails AS detTB ON detTB.dayID = masterTB.DayID AND detTB.companyID = {$companyID}
                                 ORDER BY masterTB.DayID")->result_array();*/
        $query = $CI->db->query("SELECT * FROM srp_weekdays ORDER BY DayID")->result_array();
        return $query;
    }
}

if (!function_exists('fetch_shifts')) {
    function fetch_shifts($drop = null)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $query = $CI->db->query("SELECT shiftID, Description FROM srp_erp_pay_shiftmaster WHERE companyID={$companyID}")->result_array();

        if($drop){
            $base = array();
            foreach($query as $record){
                $base[''] = 'Select Shift';
                $base[$record['shiftID']] = $record['Description'];
            }

            return $base;
        }

        return $query;
    }
}


if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d H:i:s') {
        $dateFormat = DateTime::createFromFormat($format, $date);
        return $dateFormat && $dateFormat->format($format) === $date;
    }
}


if (!function_exists('makeTimeTextBox')) {
    function makeTimeTextBox($name, $h = null, $d = true)
    {
        if (is_array($h)) {
            $hours = str_pad($h['h'], 2, '0', STR_PAD_LEFT);
            $minutes = str_pad($h['m'], 2, '0', STR_PAD_LEFT);
        } /*else {
            $hours = str_pad($h, 2, '0', STR_PAD_LEFT);
            $minutes = str_pad($m, 2, '0', STR_PAD_LEFT);
        }*/
        $disabled = '';
        if ($d) {
            $disabled = 'disabled';
        }


        $txt = '<div class="" style="width: 55px">';
        $txt .= '<div class="input-group">';
        $txt .= '<span class="input-group-btn">';
        $txt .= '<input ' . $disabled . ' onchange="updatebothfields(this,\'' . $name . '\')"  type="text" name="h_' . $name . '" class="trInputs inputdisabled timeBox txtH number h_' . $name . '" style="width: 25px" value="' . $hours . '" ';
        $txt .= 'onkeyup="hoursValidate(this)"  >';
        $txt .= '</span>';
        $txt .= '<span style="font-size: 14px; font-weight: bolder"> : </span>';
        $txt .= '<span class="input-group-btn">';
        $txt .= '<input ' . $disabled . ' onchange="updatebothfields(this,\'' . $name . '\')"  type="text" name="m_' . $name . '" class="trInputs inputdisabled timeBox txtM number m_' . $name . '" style="width: 25px" value="' . $minutes . '" ';
        $txt .= 'onkeyup="minutesValidate(this)" onchange="minutesValidateChange(this)">';
        $txt .= '</span>';
        $txt .= '</div>';
        $txt .= '</div>';
        /*$txt = '<input type="text" name="'.$name.'" class="trInputs txtH" value="'.$h.' '.$m.' 02" style="width:50%"/> : ';
        $txt .= '<input type="text" name="'.$name.'" class="trInputs txtM" value="'.$h.' '.$m.' 20" style="width:50%"/>';*/

        echo $txt;
    }
}

if (!function_exists('action_nationality')) {
    function action_nationality($nid, $Nationality, $usageCount)
    {
        $Nationality = "'" . $Nationality . "'";
        $action = '<a onclick="edit_nationality(' . $nid . ', ' . $Nationality . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_nationality(' . $nid . ', ' . $Nationality . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('bankMasterData')) {
    function bankMasterData($bankID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("bankID, bankCode, bankName");
        $CI->db->FROM('srp_erp_pay_bankmaster');
        $CI->db->WHERE('bankID', $bankID);
        $CI->db->WHERE('companyID', current_companyID());
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('action_social_insurance')) {
    function action_social_insurance($sid, $socialInsurance, $employeeContribution, $employerContribution, $sortCode, $usageCount, $expenseGlAutoID, $liabilityGlAutoID, $isSlabApplicable, $SlabID)
    {
        $socialInsurance = "'" . $socialInsurance . "'";
        $glCodes = "{$expenseGlAutoID}_{$liabilityGlAutoID}";
        $companyID = current_companyID();

        $CI =& get_instance();
        $groupID = $CI->db->query("SELECT payGroupID FROM srp_erp_paygroupmaster WHERE socialInsuranceID='{$sid}' AND companyID={$companyID}")->row('payGroupID');

        $url = site_url('Employee/formulaDecode');
        $action = '<a onclick="formulaModalOpen('.$socialInsurance.', \'' . $groupID . '\', \''.$url.'\', \'\')">';
        $action .= '<span title="Formula" rel="tooltip" class="fa fa-superscript"></span></a></a>&nbsp;&nbsp; | &nbsp;&nbsp;';

        $action .= '<a onclick="edit_social_insurance(' . $sid . ', ' . $socialInsurance . ',\'' . $employeeContribution . '\',\'' . $employerContribution . '\',';
        $action .= '\'' . $sortCode . '\',\'' . $glCodes . '\',\'' . $isSlabApplicable . '\',\'' . $SlabID . '\')">';
        $action .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_social_insurance(' . $sid . ', ' . $socialInsurance . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('load_salary_declaration_action')) {
    function load_salary_declaration_action($masterID, $confirmYN, $approvedYN, $createdUserID, $docCode, $template = 1)
    {
        $CI =& get_instance();

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li>
                        <a href="#" onclick=\'attachment_modal(' . $masterID . ',"Salary Declaration","SD",' . $confirmYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color: #4caf50"></span> Attachment</a>
                    </li>';

        $status .= '<li>
                        <a href="#" onclick="view_modal(' . $masterID . ',' . $template . ')"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4"></span> View</a>
                    </li>';

        $status .= '<li>
                        <a href="#" onclick="print_SD(' . $masterID . ', \'' . $docCode . '\', ' . $template . ')"><span class="glyphicon glyphicon-print" style="color: #607d8b"></span> Print</a>
                    </li>';

        if ($confirmYN != 1) {
            $status .= '<li>
                            <a href="#" onclick="load_details(' . $masterID . ',' . $template . ')"><span class="glyphicon glyphicon-pencil" style="color: #116f5e"></span> Edit</a>
                        </li>';

            $status .= '<li>
                            <a href="#" onclick="delete_declaration(' . $masterID . ',\'Salary\');"><span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span> Delete</a>
                        </li>';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) && $approvedYN == 0 && $confirmYN == 1) {
            $status .= '<li>
                            <a href="#" onclick="referBackDeclaration(' . $masterID . ');"><span class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span> Refer Back</a>
                        </li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('common_td_action')) {
    function common_td_action($masterID, $confirmYN, $approvedYN, $createdUserID, $docCode, $template=1)
    {
        $status = '<span class="pull-right">';
        $CI =& get_instance();

        $status .= '<a onclick=\'attachment_modal(' . $masterID . ',"Salary Declaration","SD",' . $confirmYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a target="_blank" onclick="view_modal(' . $masterID . ','.$template.')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        $status .= '&nbsp;&nbsp; | &nbsp;&nbsp; <span title="Print" rel="tooltip" class="glyphicon glyphicon-print" onclick="print_SD('.$masterID.', \''.$docCode.'\', '.$template.')" style="color:#3c8dbc"></span>';

        if ($confirmYN != 1) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            $status .= '<a onclick="load_details(' . $masterID . ','.$template.')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_declaration(' . $masterID . ',\'Salary\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }
        if ($createdUserID == trim($CI->session->userdata("empID")) and $approvedYN == 0 and $confirmYN == 1) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            $status .= '<a onclick="referBackDeclaration(' . $masterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('common_td_action_variable')) {
    function common_td_action_variable($masterID, $confirmYN, $approvedYN, $createdUserID, $docCode, $template=1)
    {
        $CI =& get_instance();

        $dropdownItems = '';

        $dropdownItems .= '<li><a onclick=\'attachment_modal(' . $masterID . ',"Salary Variable Declaration","SVD",' . $confirmYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color:#4caf50;"></span> Attachment</a></li>';

        $dropdownItems .= '<li><a onclick="view_modal(' . $masterID . ','.$template.')"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';

        $dropdownItems .= '<li><a onclick="print_SD('.$masterID.', \''.$docCode.'\', '.$template.')" ><span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';

        if ($confirmYN != 1) {
            $dropdownItems .= '<li><a onclick="load_details(' . $masterID . ','.$template.')"><span class="glyphicon glyphicon-pencil" style="color:#116f5e;"></span> Edit</a></li>';
            $dropdownItems .= '<li><a onclick="delete_declaration(' . $masterID . ',\'Salary\');"><span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span> Delete</a></li>';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) && $approvedYN == 0 && $confirmYN == 1) {
            $dropdownItems .= '<li><a onclick="referBackDeclaration(' . $masterID . ');"><span class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span> Refer Back</a></li>';
        }

        $dropdownHTML = '
            <div class="btn-group" style="display: flex;justify-content: center;">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    Actions <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                    ' . $dropdownItems . '
                </ul>
            </div>';

        return $dropdownHTML;
    }
}

if (!function_exists('common_approval_action')) {
    function common_approval_action($docID, $masterID, $Level, $approved, $ApprovedID, $type, $docCode=null)
    {
        $status = ($type == 'edit')? '<span class="pull-right">' : '';

        if($docID == "SD"){//Salary Declaration
            $status .= '<a onclick=\'attachment_modal(' . $masterID . ',"Salary Declaration","SD",1);\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($approved == 0) {
            $str = ($type == 'edit')? '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span>' : $docCode;
            $status .= '<a onclick=\'fetch_approval("' . $masterID . '","' . $ApprovedID . '","' . $Level . '"); \'>'.$str.'</a>';
        }else{
            $str = ($type == 'edit')? '<span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span>' : $docCode;
            $status .= '<a target="_blank" onclick="documentPageView_modal(\''.$docID.'\',\'' . $masterID . '\')" >'.$str.'</a>';
        }
        $status .= ($type == 'edit')? '</span>' : '';

        return $status;
    }
}

if (!function_exists('load_salary_slab_action')) { /*get po action list*/
    function load_salary_slab_action($masterID)
    {
        $fetch = "fetchPage('system/hrm/create_new_slab','" . $masterID . "','HRMS')";
        $status = '<span class="pull-right">';
        $CI =& get_instance();
        //$status .= '<a target="_blank" onclick="documentPageView_modal(\'SD\',\'' . $masterID . '\')" ><span class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';

        $status .= '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('salary_categories')) {
    function salary_categories($type = array(), $isPayrollCat = null, $apiCompID = null)
    {
        $CI =& get_instance();
        $com = current_companyID();
        if(!empty($apiCompID)) {
            $com = $apiCompID;
        }

        $join = "('" . implode("','", $type) . "')";

        $where = 'companyID=' . $com . ' AND salaryCategoryType IN ' . $join . '';
        $where .= ($isPayrollCat != null)? ' AND isPayrollCategory = '.$isPayrollCat: '';

        $CI->db->select('srp_erp_pay_salarycategories.salaryDescription, srp_erp_pay_salarycategories.salaryCategoryID, salaryCategoryType')
            ->from('srp_erp_pay_salarycategories')
            ->where($where);
        $query = $CI->db->get();

        return $query->result_array();

    }
}


if (!function_exists('systemOT_drop')) {
    function systemOT_drop()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT ID, catDescription FROM srp_erp_pay_sys_overtimecategory ")->result_array();

        $data_arr = array('' => '');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['ID'] ?? '')] = trim($row['catDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('masterOT_drop')) {
    function masterOT_drop($asResult = null)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $data = $CI->db->query("SELECT ID, description FROM srp_erp_pay_overtimecategory WHERE companyID={$companyID}")->result_array();

        if ($asResult == null) {
            $data_arr = array('' => '');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['ID'] ?? '')] = trim($row['description'] ?? '');
                }
            }
            return $data_arr;
        } else {
            return $data;
        }

    }
}


if (!function_exists('action_payGroup')) {
    function action_payGroup($payGroupID, $description, $isGroupTotal)
    {
        $usageCount = 0;
        $url = site_url('Employee/formulaDecode');
        $action = '<a onclick="formulaModalOpen(\'' . $description . '\', \'' . $payGroupID . '\', \''.$url.'\', \'\')"><span title="Formula"';
        $action .= ' rel="tooltip" class="fa fa-superscript"></span></a>&nbsp;&nbsp; | &nbsp; ';
        $action .= '<a onclick="edit_paygroup(' . $payGroupID . ',\'' . $description . '\',\'' . $isGroupTotal . '\')"><span title="Edit" rel="tooltip" ';
        $action .= 'class="glyphicon glyphicon-pencil"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_paygroup(' . $payGroupID . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('get_OT_groupMasterDet')) {
    function get_OT_groupMasterDet($groupID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $data = $CI->db->query("SELECT groupDetailID, overTimeID, formula, glCode, description
                                FROM srp_erp_pay_overtimegroupdetails AS groupDet
                                LEFT JOIN srp_erp_pay_overtimecategory AS groupCat ON groupCat.ID = groupDet.overTimeID
                                WHERE groupDet.companyID={$companyID} AND groupDet.groupID={$groupID}")->result_array();

        return $data;
    }
}


if (!function_exists('slabsmaster')) {
    function slabsmaster()
    {
        $CI =& get_instance();
        $com = current_companyID();
        $where = 'companyID=' . $com . '';
        $CI->db->select('Description, slabsMasterID, documentSystemCode')
            ->from('srp_erp_slabsmaster')
            ->where($where);
        $query = $CI->db->get();
        return $query->result();

    }
}

if (!function_exists('action_payee')) {
    function action_payee($sid, $socialInsurance, $sortCode, $liabilityGlAutoID, $SlabID, $isNonPayroll)
    {
        $expenseGlAutoID = '';
        $socialInsurance = "'" . $socialInsurance . "'";
        $glCodes = "{$expenseGlAutoID}_{$liabilityGlAutoID}";
        $isSlabApplicable = '';
        $employeeContribution = '';
        $employerContribution = '';
        $usageCount = false;
        $companyID = current_companyID();
        $CI =& get_instance();

        $groupID = $CI->db->query("SELECT payGroupID FROM srp_erp_paygroupmaster WHERE payeeID='{$sid}' AND companyID={$companyID}")->row('payGroupID');
        $url = site_url('Employee/formulaDecode');
        $action = '<a onclick="formulaModalOpen('.$socialInsurance.', \'' . $groupID . '\', \'' . $url . '\', \'\')"><span title="Formula" rel="tooltip"';
        $action .= 'class="fa fa-superscript"></span></a>&nbsp; | &nbsp;';
        $action .= '<a onclick="edit_social_insurance(' . $sid . ', ' . $socialInsurance . ',\'' . $employeeContribution . '\',\'' . $employerContribution . '\'';
        $action .= ',\'' . $sortCode . '\',\'' . $glCodes . '\',\'' . $isSlabApplicable . '\',\'' . $SlabID . '\', \''.$isNonPayroll.'\')">';
        $action .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_social_insurance(' . $sid . ', ' . $socialInsurance . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('load_employee_contribution')) {
    function load_employee_contribution($employeeContribution, $employerContribution)
    {
        if ($employeeContribution >= 1 && $employerContribution == 0) {
            return $employeeContribution;
        } else if ($employeeContribution == 0 && $employerContribution >= 1) {
            return $employerContribution;
        }

    }
}

if (!function_exists('over_time_group')) {
    function over_time_group()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT * FROM `srp_erp_pay_overtimegroupmaster` WHERE `companyID` = '{$companyID}'")->result_array();
        $data_arr = array('' => 'Select Over Time Group');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['groupID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;

    }
}

if (!function_exists('defaultPayrollCategories_drop')) {
    function defaultPayrollCategories_drop($asResult = null)
    {
        $CI =& get_instance();
        $CI->db->select("id,description,isGLCodeRequired");
        $CI->db->from('srp_erp_defaultpayrollcategories');
        $data = $CI->db->get()->result_array();

        if ($asResult != null) {
            $data_arr = array('' => '');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
                }
            }
            return $data_arr;
        } else {
            return $data;
        }
    }
}

if (!function_exists('action_employee_type')) {
    function action_employee_type($EmpContractTypeID, $usageCount)
    {
        $action = '<a onclick="editEmployeeDetail(' . $EmpContractTypeID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="deleteEmployeeTypeMaster(' . $EmpContractTypeID . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }
        return '<span class="pull-right">' . $action . '</span>';

    }
}


if (!function_exists('make_dropDown')) {
    function make_dropDown($dropDownData, $selectedID, $isDisabled, $id)
    {
        $h_glCode = ''; $h_salCat = 0;
        $dropDown = '<select name="declarationID[]" id="groupDrop_'.$id.'" class="trInputs form-control" onchange="changeGLCode(this)" '.$isDisabled.'>';
        $dropDown .= '<option value="">Select Grouping Type</option>';
        
        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $decID = $rowDrop['monthlyDeclarationID'];
                $selected = ($selectedID == $decID)? 'selected' : '';
                if($selectedID == $decID){
                    $h_glCode = $rowDrop['GLAutoID'];
                    $h_salCat = $rowDrop['salaryCategoryID'];
                }
                $dropDown .= '<option value="'.$decID.'" '.$selected.' data-gl="'.$rowDrop['GLAutoID'].'" data-cat="'.$rowDrop['salaryCategoryID'].'">';
                $dropDown .= $rowDrop['monthlyDeclaration'].' | '.$rowDrop['GLSecondaryCode'].'</option>';
            }
        }

        $dropDown .= '</select>';
        $dropDown .= '<input type="hidden" name="h-glCode[]" class="h-glCode" value="'.$h_glCode.'">';
        $dropDown .= '<input type="hidden" name="h-category[]" class="h-categoryID" value="'.$h_salCat.'">';

        return $dropDown;
    }
}

if (!function_exists('make_employee_dropDown')) {
    function make_employee_dropDown($selectedID,$DepartmentMasterID)
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
            
            //var_dump($CI->db->last_query());exit;

       
        $dropDown = '<select id="select_hod_emp" name="select_hod_emp" class="form-control select2" onchange="selectHodForDepartment(this,' . $DepartmentMasterID . ')">';
        $dropDown .= '<option value="">Select Employee</option>';

        if( !empty($dropDownData) ){
            foreach($dropDownData as $keyDrop => $rowDrop){
                $empID = $rowDrop['EIdNo'];
                $selected = ($selectedID == $empID)? 'selected' : '';
                $dropDown .= '<option value="'.$empID.'" '.$selected.' data-cat="'.$empID.'">';
                $dropDown .= $rowDrop['EmpSecondaryCode'].' | '.$rowDrop['Ename2'].'</option>';
            }
        }

        $dropDown .= '</select>';

        return $dropDown;
    }
}


if (!function_exists('designation_status')) {
    function designation_status($DesignationID, $status)
    {
        $checked = ($status == 1) ? 'checked' : '';
        $isDisable = ($status == 1) ? 'disabled' : '';
        $tooltipText = ($status == 1) ? '' : 'Contractual Designation';
        $str = '<span class="bootstrap-switch-container" title="'.$tooltipText.'" rel="tooltip">';
        $str .= '<input type="checkbox" class="switch-chk" id="designation_status' . $DesignationID . '" onchange="changeDesignationStatus(this, ' . $DesignationID . ')"';
        $str .= 'data-size="mini" data-on-text="Yes" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="No" data-label-width="0" ' . $checked . ' '.$isDisable.'></span>';
        return  $str;
    }
}



if (!function_exists('designationActive_status')) {
    function designationActive_status($DesignationID, $status)
    {
        $checked = ($status == 1) ? 'checked' : '';
        $str = '<input type="checkbox" class="switch-chk" id="designationActive_status' . $DesignationID . '" onchange="changeActiveStatus(this, ' . $DesignationID . ')"';
        $str .= 'data-size="mini" data-on-text="Yes" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="No" data-label-width="0" ' . $checked . '>';
        return  $str;
    }
}

if (!function_exists('action_for_rep_manager')) {
    function action_for_rep_manager($managerAutoID, $name)
    {
        $managerName = "'" . $name . "'";
        $action = '';
        if($managerName){
            $action = '<a onclick="delete_reportingManagers(' . $managerAutoID . ', ' . $managerName . ')">';
            $action .= '<span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';
    }
}


if (!function_exists('manager_status')) {
    function manager_status($managerAutoID, $isActive)
    {
        $checked = ($isActive == 1) ? 'checked' : '';
        //$isDisable = ($isActive == 1) ? 'disabled' : '';
        $tooltipText = ($isActive == 1) ? 'Active' : 'Inavtive';
        $str = '<span class="bootstrap-switch-container" title="'.$tooltipText.'" rel="tooltip">';
        $str .= '<input type="checkbox" class="switch-chk" id="isActiveStatus' . $managerAutoID . '" onchange="changeActiveStatus_repManager(this, ' . $managerAutoID . ')"';
        $str .= 'data-size="mini" data-on-text="Yes" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="No" data-label-width="0" ' . $checked . '></span>';
        return  $str;
    }
}
if (!function_exists('isPrimary_manager')) {
    function isPrimary_manager($managerAutoID, $isPrimary)
    {
        $checked = ($isPrimary == 1) ? 'checked' : '';
        $isDisable = ($isPrimary == 1) ? 'disabled' : '';
        $tooltipText = ($isPrimary == 1) ? 'Primary Manager' : 'Not Primary Manager';
        $str = '<span class="bootstrap-switch-container" title="'.$tooltipText.'" rel="tooltip">';
        $str .= '<input type="checkbox" class="switch-chk" id="isprimarystatus' . $managerAutoID . '" onchange="changePrimaryStatus_repManager(this, ' . $managerAutoID . ')"';
        $str .= 'data-size="mini" data-on-text="Yes" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="No" data-label-width="0" ' . $checked . ' '.$isDisable.'></span>';
        return  $str;

    }
}


if(!function_exists('require_employeeDataStatus()')){
    function require_employeeDataStatus($empID, $isTibian='N'){
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT EDOJ, DateAssumed, payCurrencyID, segmentID, EmployeeConType, contractStartDate, contractEndDate,
                                  EPassportExpiryDate, EVisaExpiryDate, EmpDesignationId, leaveGroupID, managerID
                                  FROM srp_employeesdetails AS empTB
                                  LEFT JOIN(
                                      SELECT empID, managerID, CONCAT(ECode, '_' ,Ename2) AS managerName FROM  srp_erp_employeemanagers
                                      JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_employeemanagers.managerID
                                      WHERE empID={$empID} AND companyID={$companyID} AND active=1
                                  )  AS managersTB ON managersTB.empID = empTB.EIdNo
                                  WHERE Erp_companyID={$companyID} AND EIdNo={$empID} ")->row_array();

        $msg = '';

        $msg .= (trim($data['EDOJ'] ?? '') != '' && $data['EDOJ'] != null)? '' : 'date of join <br/>';
        if($isTibian == 'N') {
            $msg .= (trim($data['DateAssumed'] ?? '') != '' && $data['DateAssumed'] != null)? '' : 'assume date <br/>';
        }
        $msg .= (trim($data['payCurrencyID'] ?? '') != '' && $data['payCurrencyID'] != null && $data['payCurrencyID'] != 0)? '' : 'currency <br/>';
        $msg .= (trim($data['segmentID'] ?? '') != '' && $data['segmentID'] != null && $data['segmentID'] != 0)? '' : 'segment <br/>';
        $msg .= (trim($data['EmployeeConType'] ?? '') != '' && $data['EmployeeConType'] != null && $data['EmployeeConType'] != 0)? '' : 'Employee type <br/>';
        $msg .= (trim($data['EmpDesignationId'] ?? '') != '' && $data['EmpDesignationId'] != null && $data['EmpDesignationId'] != 0)? '' : 'designation <br/>';
        $msg .= (trim($data['leaveGroupID'] ?? '') != '' && $data['leaveGroupID'] != null && $data['leaveGroupID'] != 0)? '' : 'leave group <br/>';
        $msg .= (trim($data['managerID'] ?? '') != '' && $data['managerID'] != null && $data['managerID'] != 0)? '' : 'Reporting manager<br/>';


        $status = ($msg == '')? 's' : 'e';
        return [$status, $msg, $data];
    }
}

if (!function_exists('action_contractHistory')) {
    function action_contractHistory($contractID, $isContract)
    {

        $action = '';

        if($isContract != 2){
            $action = '<a onclick="delete_contract(' . $contractID . ')"><span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('isEmployeeConfirmed')) { 
    function isEmployeeConfirmed($empID)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("empConfirmedYN");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->where('Erp_companyID', $companyID);
        $CI->db->where('EIdNo', $empID);
        $data = $CI->db->get()->row('empConfirmedYN');

        return $data;
    }
}

if (!function_exists('fetch_employee_detail_tbl')) { 
    function fetch_employee_detail_tbl($empID,$empCode = null)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->where('Erp_companyID', $companyID);
        if($empCode){
            $CI->db->where('ECode', $empCode);
        }else{
            $CI->db->where('EIdNo', $empID);
        }
        
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('fetch_variable_declaration_emp')) { 
    function fetch_variable_declaration_emp($emp_id,$monthly_type)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->select('svs.*');
        $CI->db->where('svs.employeeNo',$emp_id);
        $CI->db->where('svs.salaryCategoryType','A');
        $CI->db->where('svs.monthlyDeclarationID',$monthly_type);
        $CI->db->where('svsmaster.approvedYN',1);
        $CI->db->from('srp_erp_variable_salarydeclarationdetails as svs');
        $CI->db->join('srp_erp_variable_salarydeclarationmaster as svsmaster','svs.declarationMasterID = svsmaster.salaryDeclarationMasterID','left');
        $CI->db->order_by('svs.declarationDetailID','DESC');
        $varible_values = $CI->db->get()->row_array();

        return $varible_values;
    }
}

if (!function_exists('clear_no_pay_deductions')) { 
    function clear_no_pay_deductions($monthlyDeductionMasterID)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->where('monthlyDeductionMasterID', $monthlyDeductionMasterID);
        $CI->db->FROM('srp_erp_pay_monthlydeductiondetail');
        $CI->db->where('companyID', $companyID);
        $data = $CI->db->delete();

        return $data;
    }
}

if (!function_exists('get_attandance_link_categories')) { 
    function get_attandance_link_categories($locaion = null)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $links = array();

        $CI->db->select('monthlyDeclaration,linkType,calType,monthlyDeclarationID,location');
        $CI->db->FROM('srp_erp_pay_monthlydeclarationstypes');
        $CI->db->where('companyID', $companyID);
        $CI->db->where_in('linkType', [2,3]);
        $data = $CI->db->get()->result_array();

        foreach($data as $val){

            if($val['location']){
                $location_arr = explode(',',$val['location']);

                if(in_array($locaion,$location_arr)){
                    $links[] = $val;
                }
            }

            
        }

        if($locaion){
            return $links;
        }else{
            return $data;
        }
        
    }
}

if (!function_exists('get_pay_monthlydeclarationstypes_by_id')) { 
    function get_pay_monthlydeclarationstypes_by_id($monthlyDeclarationID,$all = null)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        if($all){
            $CI->db->select('*');
        }else{
            $CI->db->select('monthlyDeclaration,linkType,calType,monthlyDeclarationID');
        }
        $CI->db->FROM('srp_erp_pay_monthlydeclarationstypes');
        $CI->db->where('monthlyDeclarationID', $monthlyDeclarationID);
        $data = $CI->db->get()->row_array();

        return $data;
    }
}




if (!function_exists('getEmployeesDeclaration')) { 
    function getEmployeesDeclaration($masterID,$isVariable = null)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $convertFormat = convert_date_format_sql();
        $isGroupAccess = getPolicyValues('PAC', 'All');

        $str = '';
        if($isGroupAccess == 1){
            $currentEmp = current_userID();
            $str = "JOIN (
                        SELECT empTB.groupID, employeeID FROM srp_erp_payrollgroupemployees AS empTB
                        JOIN srp_erp_payrollgroupincharge AS incharge ON incharge.groupID=empTB.groupID
                        WHERE empTB.companyID={$companyID} AND incharge.companyID={$companyID} AND empID={$currentEmp}
                    ) AS accTb ON accTb.employeeID=EIdNo";
        }

        if($isVariable){
            $employees = $CI->db->query("SELECT EIdNo, ECode, Ename2, DATE_FORMAT(EDOJ,'{$convertFormat}') AS EDOJ, transactionCurrencyDecimalPlaces AS dPlace
                                     FROM srp_employeesdetails AS empTB
                                     JOIN srp_erp_variable_salarydeclarationmaster AS declarationMaster
                                     ON declarationMaster.transactionCurrencyID = empTB.payCurrencyID AND salarydeclarationMasterID={$masterID}
                                     {$str}
                                     WHERE Erp_companyID={$companyID} AND isPayrollEmployee=1 AND empConfirmedYN=1  AND isDischarged=0")->result_array();
        }else{
            $employees = $CI->db->query("SELECT EIdNo, ECode, Ename2, DATE_FORMAT(EDOJ,'{$convertFormat}') AS EDOJ, transactionCurrencyDecimalPlaces AS dPlace
                                     FROM srp_employeesdetails AS empTB
                                     JOIN srp_erp_salarydeclarationmaster AS declarationMaster
                                     ON declarationMaster.transactionCurrencyID = empTB.payCurrencyID AND salarydeclarationMasterID={$masterID}
                                     {$str}
                                     WHERE Erp_companyID={$companyID} AND isPayrollEmployee=1 AND empConfirmedYN=1  AND isDischarged=0")->result_array();
        }
        
        return $employees;
    }
}

if (!function_exists('salaryCategories_drop')) { 
    function salaryCategories_drop($masterID,$isVariable = null)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        if($isVariable){
            $categories = $CI->db->query("SELECT catMaster.monthlyDeclarationID as salaryCategoryID,catMaster.salaryCategoryID as salaryCategory, catMaster.monthlyDeclaration as salaryDescription, catMaster.monthlyDeclarationType as salaryCategoryType 
            FROM srp_erp_pay_monthlydeclarationstypes AS catMaster
            JOIN srp_erp_variable_salarydeclarationmaster AS declarationMaster
            ON declarationMaster.isPayrollCategory = catMaster.isPayrollCategory AND declarationMaster.salarydeclarationMasterID = {$masterID}
            WHERE catMaster.companyID ={$companyID} AND catMaster.isVariable = '1' ")->result_array();
        }else{
            $categories = $CI->db->query("SELECT salaryCategoryID, salaryDescription, salaryCategoryType FROM srp_erp_pay_salarycategories AS catMaster
            JOIN srp_erp_salarydeclarationmaster AS declarationMaster
            ON declarationMaster.isPayrollCategory = catMaster.isPayrollCategory AND salarydeclarationMasterID={$masterID}
            WHERE catMaster.companyID ={$companyID}")->result_array();
        }
       
        return $categories;
    }
}

if (!function_exists('get_monthlyaddition_deduction_varibles')) { 
    function get_monthlyaddition_deduction_varibles($type = 'MA')
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        if($type == 'MA'){
            $categories = $CI->db->query("SELECT *
            FROM srp_erp_pay_monthlydeclarationstypes AS catMaster
            WHERE catMaster.companyID ={$companyID} AND catMaster.isVariable = '1' ")->result_array();
        }

        return $categories;
    }
}

if (!function_exists('OT_monthlyAction')) {
    function OT_monthlyAction($id, $confirmedYN,  $isProcess, $code)
    {
        $edit = '';
        $delete = '';
        $view = '';
        $referBack = '';

        $fetch = "fetchPage('system/hrm/OverTimeManagementSalamAir/over_time_monthly_addition_detail',".$id." ,'HRMS','')";


        /*$print = '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="' . site_url('Employee/monthlyAD_print/') . '/' . $id . '/' . $code . '" >';
        $print .= '<span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';*/

        $print = '';

        if ($confirmedYN != 1) {
            $code = "'" . $code . "'";
            $edit = '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_details(' . $id . ' , ' . $code . ')">';
            $delete .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        } elseif ($confirmedYN == 1) {
            $view = '<a onclick="' . $fetch . '"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>';
        }

        if ($isProcess == 0 && $confirmedYN == 1) {
            $referBack = '<a onclick="referBackConformation(' . $id . ')"><span style="color:#d15b47;" title="Refer Back" rel="tooltip"';
            $referBack .= ' class="glyphicon glyphicon-repeat"></span></a>&nbsp;&nbsp;|&nbsp;';
        }

        return '<span class="pull-right">' . $referBack . '' . $view . '' . $edit . '' . $delete . '' . $print . ' </span>';
    }
}

if (!function_exists('makeTimeTextBox_OT')) {
    function makeTimeTextBox_OT($i, $name, $rate_or_slab, $dPlaces, $isTotalBlock, $empID, $hours = null, $disabled = false)
    {
        $h = 0; $m = '00';
        if( $hours != null ){
            $h = floor($hours /60);
            $m = str_pad(($hours%60), 2, '0', STR_PAD_LEFT);
        }

        $nameConcat = $name . '_'.$i;
        $hourFn = 'onkeyup="calculateAmount(\''.$nameConcat.'\', \''.$rate_or_slab.'\', \''.$dPlaces.'\')"';
        $minutesFn = 'onkeyup="minutesValidate_OT(this, \''.$nameConcat.'\', \''.$rate_or_slab.'\', \''.$dPlaces.'\', \''.$isTotalBlock.'\')"';
        $minutesFn .= ' onchange="minutesValidateChange(this)"';

        if($isTotalBlock == 1){
            $hourFn = 'onchange="calculateBlockAmount(\'h\', \''.$nameConcat.'\', \''.$empID.'\', \''.$dPlaces.'\')"';
            $minutesFn = 'onkeyup="minutesValidateChange(this)" onchange="calculateBlockAmount(\'m\', \''.$nameConcat.'\', \''.$empID.'\', \''.$dPlaces.'\')"';
        }
        $disabled = ($rate_or_slab == 0)? 'disabled' : $disabled;


        $txt = '<div class="time-box-div">';
        $txt .= '<input type="text" name="h_' . $name . '[]" id="h_' . $nameConcat . '" class="trInputs number " value="' . $h . '" ' . $disabled;
        $txt .= ' style="width: 25px" '.$hourFn.'> : ';
        $txt .= '<input type="text" name="m_' . $name . '[]" id="m_' . $nameConcat.'" class="trInputs number" value="' . $m . '" ' . $disabled;
        $txt .= ' style="width: 25px" '.$minutesFn.' >';
        $txt .= '</div>';

        return $txt;
    }
}

if (!function_exists('fetch_fixed_element_master')) {
    function fetch_fixed_element_master()
    {
        $CI =& get_instance();
        $CI->db->SELECT("fixedElementID,fixedElementDescription");
        $CI->db->FROM('srp_erp_ot_fixedelements');
        $CI->db->WHERE('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['fixedElementID'] ?? '')] = trim($row['fixedElementDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('get_pendingEmpApprovalData')) {
    function get_pendingEmpApprovalData($empID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $pendingData = $CI->db->query("SELECT * FROM srp_erp_employeedatachanges WHERE companyID={$companyID}
                                       AND empID={$empID}  AND approvedYN!=1")->result_array();

        return $pendingData;
    }
}

if (!function_exists('get_pendingEmpApprovalReportingData')) {
    function get_pendingEmpApprovalReportingData($empID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $pendingData = $CI->db->query("SELECT * FROM srp_erp_employeedatachanges WHERE companyID={$companyID}
                                       AND empID={$empID}  AND `tableName`='srp_erp_employeemanagers'  AND (columnName='isprimary' OR columnName='active') AND approvedYN!=1")->result_array();

        return $pendingData;
    }
}

if (!function_exists('get_pendingEmpApprovaldepartmentData')) {
    function get_pendingEmpApprovaldepartmentData($empID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $departmentData = $CI->db->query("SELECT * FROM srp_erp_employeedatachanges WHERE companyID={$companyID}
                                       AND empID={$empID}  AND `tableName`='srp_empdepartments'  AND (columnName='isPrimary' OR columnName='isActive') AND approvedYN!=1")->result_array();

        return $departmentData;
    }
}

if (!function_exists('get_pendingbankprimaryData')) {
    function get_pendingbankprimaryData($empID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $departmentData = $CI->db->query("SELECT * FROM srp_erp_employeedatachanges WHERE companyID={$companyID}
                                       AND empID={$empID}  AND `tableName`='srp_erp_pay_salaryaccounts'  AND columnName='isPrimary' AND approvedYN!=1")->result_array();

        return $departmentData;
    }
}

if (!function_exists('get_pendingbankdetail')) {
    function get_pendingbankdetail($empID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $departmentData = $CI->db->query("SELECT * FROM srp_erp_employeedatachanges WHERE companyID={$companyID}
                                       AND empID={$empID}  AND `tableName`='srp_erp_pay_salaryaccounts' AND `columnName`!='isPrimary' AND approvedYN!=1")->result_array();

        return $departmentData;
    }
}

if (!function_exists('getEmployeesFixedElementDeclaration')) { 
    function getEmployeesFixedElementDeclaration($masterID)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $convertFormat = convert_date_format_sql();
        $employees = $CI->db->query("SELECT EIdNo, ECode, Ename2, DATE_FORMAT(EDOJ,'{$convertFormat}') AS EDOJ, transactionCurrencyDecimalPlaces AS dPlace
                                     FROM srp_employeesdetails AS empTB
                                     JOIN srp_erp_ot_fixedelementdeclarationmaster AS declarationMaster
                                     ON declarationMaster.transactionCurrencyID = empTB.payCurrencyID AND fedeclarationMasterID={$masterID}
                                     WHERE Erp_companyID={$companyID} AND isPayrollEmployee=1 AND empConfirmedYN=1 AND isDischarged=0")->result_array();
        return $employees;
    }
}

if (!function_exists('load_fixedElementDeclaration_action')) {
    function load_fixedElementDeclaration_action($masterID, $confirmYN, $approvedYN, $createdUserID)
    {
        $fetch = "fetchPage('system/hrm/OverTimeManagementSalamAir/fixed_element_declaration_new','" . $masterID . "','HRMS')";
        $status = '<span class="pull-right">';
        $CI =& get_instance();

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'FED\',\'' . $masterID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        if ($confirmYN != 1) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            $status .= '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_declaration(' . $masterID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
            //$status .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($createdUserID == trim($CI->session->userdata("empID")) and $approvedYN == 0 and $confirmYN == 1) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            $status .= '<a onclick="referbackDeclaration(' . $masterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_fixed_element_declaration_action_approval')) { /*get po action list*/
    function load_fixed_element_declaration_action_approval($masterID, $Level, $approved, $ApprovedID)
    {
        $fetch = "fetchPage('system/hrm/OverTimeManagementSalamAir/fixed_element_declaration_new','" . $masterID . "','HRMS')";
        $status = '<span class="pull-right">';
        $CI =& get_instance();
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $masterID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'FED\',\'' . $masterID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('OT_group_dropDown')) {
    function OT_group_dropDown()
    {
        $CI =& get_instance();
        $CI->db->SELECT("otGroupID,otGroupDescription");
        $CI->db->FROM('srp_erp_ot_groups');
        $CI->db->WHERE('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        //$data_arr = array('' => 'Select');
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['otGroupID'] ?? '')] = trim($row['otGroupDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('sso_slab_action')) {
    function sso_slab_action($masterID, $description)
    {
        $fetch = "fetchPage('system/hrm/create_new_sso_slab','" . $masterID . "','HRMS', '', '".$description."')";
        $status = '<span class="pull-right">';
        $status .= '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('get_employee_view_record')) {
    function get_employee_view_record($empID,$date)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT * FROM srp_erp_pay_empattendancereview WHERE companyID={$companyID}
                                AND attendanceDate='{$date}' AND empID = {$empID}")->row_array();
        return $data;
    }
}

if (!function_exists('get_employee_leave_record')) {
    function get_employee_leave_record($empID,$date)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT * FROM srp_erp_leavemaster WHERE companyID={$companyID}
                                AND startDate <= '{$date}' AND endDate >= '{$date}' AND empID = {$empID} AND approvedYN = 1")->row_array();
        return $data;
    }
}

if (!function_exists('get_discharge_reasons')) {
    function get_discharge_reasons()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select('id, dischargeReason');
        $CI->db->from('srp_erp_pay_dischargereason');
        // Assuming you need to filter by company ID, add WHERE clause as needed
        // $CI->db->where('Erp_companyID', current_companyID());
        $CI->db->order_by('dischargeReason');
        $query = $CI->db->get();

        $data_arr = array();

        if ($query->num_rows() > 0) {
            $data_arr[''] = $CI->lang->line('common_select_discharge_reason');
            foreach ($query->result_array() as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['dischargeReason'] ?? '');
            }
        } else {
            $data_arr[''] = $CI->lang->line('common_no_discharge_reasons_found');
        }

        return $data_arr;
    }
}


if (!function_exists('get_sso_slabDetails')) {
    function get_sso_slabDetails($masterID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT * FROM srp_erp_ssoslabdetails WHERE companyID={$companyID}
                                AND ssoSlabMasterID={$masterID}")->result_array();
        return $data;
    }
}

if (!function_exists('makeCopyBlock')) {
    function makeCopyBlock($class, $function)
    {
        return  '<span class="applyToAll '.$class.'">
                     <button class="btn btn-xs btn-default" type="button" onclick="'.$function.'(this)">
                            <i class="fa fa-arrow-circle-down arrowDown"></i>
                     </button>
                 </span>';
    }
}

if (!function_exists('ssoSlabsMaster')) {
    function ssoSlabsMaster()
    {
        $CI =& get_instance();
        $CI->db->select('ssoSlabMasterID, description')
            ->from('srp_erp_ssoslabmaster')
            ->where('companyID', current_companyID());
        $query = $CI->db->get();
        return $query->result();

    }
}

if (!function_exists('search_otElement')) {
    function search_otElement($arr, $searchingKey)
    {
        $keys = array_keys(array_column($arr, 'templateDetailID'), $searchingKey);
        $new_array = array_map(function ($k) use ($arr) {
            return $arr[$k];
        }, $keys);

        return (!empty($new_array[0]) && isset($new_array[0]['hourorDays'])) 
            ? trim($new_array[0]['hourorDays']) 
            : 0;
    }
}

if (!function_exists('search_otAmount')) {
    function search_otAmount($arr, $searchingKey)
    {
        $keys = array_keys(array_column($arr, 'templateDetailID'), $searchingKey);
        $new_array = array_map(function ($k) use ($arr) {
            return $arr[$k];
        }, $keys);

        return (!empty($new_array[0]) && isset($new_array[0]['transactionAmount'])) 
            ? trim($new_array[0]['transactionAmount']) 
            : 0;

    }
}

if (!function_exists('system_salary_cat_drop')) {
    function system_salary_cat_drop($sysType, $isData=null)
    {
        $companyID=current_companyID();
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $data = $CI->db->query("SELECT salaryCategoryID, salaryDescription 
                                FROM srp_erp_pay_salarycategories catTB
                                JOIN srp_erp_defaultpayrollcategories defTB ON defTB.id = catTB.payrollCatID
                                WHERE defTB.code = '{$sysType}' AND companyID= $companyID")->result_array();

        $data_arr = [];
        if($isData == null){
            $data_arr = array('' => $CI->lang->line('common_select_salary_category')/*'Select Salary Category'*/);
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['salaryCategoryID'] ?? '')] = trim($row['salaryDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('system_salary_cat_drop_nopay')) {
    function system_salary_cat_drop_nopay($type)
    {
        $companyID=current_companyID();
        $CI =& get_instance();
        $data = $CI->db->query("SELECT salaryCategoryID, salaryDescription FROM srp_erp_pay_salarycategories WHERE payrollCatID=3
                                AND companyID={$companyID} AND isPayrollCategory={$type}")->result_array();

        return $data;
    }
}

if (!function_exists('payGroupSalaryCategories_decode')) {
    function payGroupSalaryCategories_decode($SSO_data = array())
    {
        $formula = (is_array($SSO_data))? trim($SSO_data['formulaString'] ?? '') : $SSO_data;
        $payGroupCategories = (is_array($SSO_data))? trim($SSO_data['payGroupCategories'] ?? '') :'';
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        if(!empty($payGroupCategories)){
            global $globalFormula;
            $globalFormula = $formula;
            $formula = decode_payGroup($SSO_data);
        }


        $formula_arr = explode('|', $formula); // break the formula

        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) {  } //validate is a operand
                else {

                    $elementType = $formula_row[0];

                    if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $formulaDecode_arr[] = $catArr[1];
                    }
                }
            }

        }
        return $formulaDecode_arr;
    }
}

if (!function_exists('fetch_employeeStatusWise')) {
    function fetch_employeeStatusWise()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $statusCount = $CI->db->query("SELECT * FROM (
                                          SELECT ( SELECT COUNT(EIdNo) FROM srp_employeesdetails WHERE Erp_companyID={$companyID} AND isSystemAdmin!=1
                                          AND isDischarged=1 ) AS discharged, ( SELECT COUNT(EIdNo) FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                          AND isSystemAdmin!=1 AND isDischarged = 0 AND empConfirmedYN IS NULL ) AS notConfirmed, ( SELECT COUNT(EIdNo) AS empCount FROM
                                          srp_employeesdetails WHERE Erp_companyID={$companyID} AND isSystemAdmin!=1 AND empConfirmedYN=1 AND isDischarged=0 ) AS activeEmp
                                       ) AS t1")->row_array();
        return $statusCount;
    }
}

if (!function_exists('employeePagination')) {
    function employeePagination()
    {
        $CI =& get_instance();
        $CI->load->library("pagination");
        //$CI->load->library("s3");

        $data_pagination = $CI->input->post('data_pagination');
        $per_page = 10;
        $companyID = current_companyID();

        $count = $CI->db->query("SELECT COUNT(EIdNo) AS empCount FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                 AND isSystemAdmin != 1")->row('empCount');

        $isFiltered = 0;
        $searchKey_filter = '';
        $alpha_filter = '';
        $segment_filter = '';
        $designation_filter = '';
        $discharged_filter = '';
        $empStatus_filter = '';

        $searchKey = $CI->input->post('searchKey');
        $letter = $CI->input->post('letter');
        $designation = $CI->input->post('designation');
        $segment = $CI->input->post('segment');
        $empStatus = $CI->input->post('empStatus');


        if ($empStatus != '' && $empStatus != 'null') {
            if($empStatus == 2){
                $empStatus_filter = " AND isDischarged = 0 AND empConfirmedYN IS NULL ";
            }
            else if($empStatus == 1 || $empStatus == 0){
                $empStatus_filter = " AND isDischarged = " . $empStatus;
                if($empStatus == 0){
                    $empStatus_filter .= " AND empConfirmedYN = 1";
                }
            }
            $isFiltered = 1;
        }

        if (!empty($designation) && $designation != 'null') {
            $designation = array($CI->input->post('designation'));
            $whereIN = "( " . join("' , '", $designation) . " )";
            $designation_filter = " AND EmpDesignationId IN " . $whereIN;
            $isFiltered = 1;
        }

        if (!empty($segment) && $segment != 'null') {
            $segment = array($CI->input->post('segment'));
            $whereIN = "( " . join("' , '", $segment) . " )";
            $segment_filter = " AND segTB.segmentID IN " . $whereIN;
            $isFiltered = 1;
        }

        if($letter != null){
            $alpha_filter = ' AND ( Ename1 LIKE \''.$letter.'%\') ';
            $isFiltered = 1;
        }

        if($searchKey != ''){
            $searchKey_filter = " WHERE (empShtrCode LIKE '%$searchKey%' OR Ename3 LIKE '%$searchKey%' OR DesDescription LIKE '%$searchKey%' OR ";
            $searchKey_filter .= " description LIKE '%$searchKey%' OR doj LIKE '%$searchKey%' OR EEmail LIKE '%$searchKey%' OR genderStr LIKE '%$searchKey%'";
            $searchKey_filter .= " OR EcMobile LIKE '%$searchKey%' OR managerReporting LIKE '%$searchKey%' )";
            $isFiltered = 1;
        }

        $countFilter = 0;

        if($isFiltered == 1){
            $countFilterWhere = $designation_filter . $segment_filter . $discharged_filter . $alpha_filter. $empStatus_filter;
            $convertFormat = convert_date_format_sql();
            $countFilter = $CI->db->query("SELECT COUNT(EIdNo) AS empCount FROM(
                                               SELECT EIdNo, EmpSecondaryCode AS empShtrCode, ECode, Ename1, Ename2, Ename3, EmpShortCode, EEmail,
                                               CONCAT( EpAddress1, ' ', EpAddress2, ' ', EpAddress3 ) AS address, EpTelephone, EcPOBox, EcMobile, TitleDescription,
                                               segTB.description AS segment, DesDescription, NIC, managerReporting, employeeType,
                                               DATE_FORMAT(EDOJ, '{$convertFormat}') AS doj,  IF(isDischarged=1, 'Discharged', 'Active') AS empStatus,
                                               IF(Gender=1, 'Male', 'Female') AS genderStr, segmentCode, description
                                               FROM srp_employeesdetails AS t1
                                               JOIN srp_titlemaster ON TitleID=EmpTitleId
                                               LEFT JOIN srp_designation ON DesignationID=t1.EmpDesignationId
                                               LEFT JOIN srp_erp_segment AS segTB ON segTB.segmentID=t1.segmentID
                                               LEFT JOIN srp_nationality ON srp_nationality.NId=t1.Nid
                                               LEFT JOIN srp_erp_systememployeetype AS employeeType ON employeeType.employeeTypeID=t1.EmployeeConType
                                               LEFT JOIN (
                                                   SELECT empID, CONCAT( EmpSecondaryCode,' - ', Ename2 )AS managerReporting FROM srp_erp_employeemanagers
                                                   JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_employeemanagers.managerID
                                                   WHERE companyID={$companyID} AND active=1
                                               ) AS repotingManagerTB ON repotingManagerTB.empID=t1.EIdNo
                                               WHERE t1.Erp_companyID={$companyID} AND isSystemAdmin != 1 {$countFilterWhere}
                                           ) AS t1 $searchKey_filter ")->row('empCount');

        }



        $config = array();
        $config["base_url"] = "#employee-list";
        $config["total_rows"] =  ($isFiltered == 1) ? $countFilter : $count;
        $config["per_page"] = $per_page;
        $config["data_page_attr"] = 'data-emp-pagination';
        $config["uri_segment"] = 3;

        $CI->pagination->initialize($config);

        $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
        $employeeData = load_employee_data($page, $per_page);
        $dataCount = $employeeData['dataCount'];

        $data["empCount"] = $count;
        $data["employee_list"] = $employeeData['employee_list'];
        $data["pagination"] = $CI->pagination->create_links_employee_master();
        $data["per_page"] = $per_page;
        $thisPageStartNumber = ($page+1);
        $thisPageEndNumber = $page+$dataCount;

        if($isFiltered == 1){
            $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$countFilter} entries (filtered from {$count} total entries)";
        }else{
            $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$count} entries";
        }


        return $data;

    }
}

if (!function_exists('load_employee_data')) {
    function load_employee_data($page, $per_page)
    {
        $searchKey_filter = '';
        $alpha_filter = '';
        $segment_filter = '';
        $designation_filter = '';
        $empStatus_filter = '';

        $CI =& get_instance();
        $letter = $CI->input->post('letter');
        $searchKey = $CI->input->post('searchKey');
        $designation = $CI->input->post('designation');
        $segment = $CI->input->post('segment');
        $isDischarged = $CI->input->post('isDischarged');
        $empStatus = $CI->input->post('empStatus');

        if ($empStatus != '' && $empStatus != 'null') {
            if($empStatus == 2){
                $empStatus_filter = " AND isDischarged = 0 AND empConfirmedYN IS NULL ";
            }
            else if($empStatus == 1 || $empStatus == 0){
                $empStatus_filter = " AND isDischarged = " . $empStatus;
                if($empStatus == 0){
                    $empStatus_filter .= " AND empConfirmedYN = 1";
                }
            }
        }

        if (!empty($designation) && $designation != 'null') {
            $designation = array($CI->input->post('designation'));
            $whereIN = "( " . join("' , '", $designation) . " )";
            $designation_filter = " AND EmpDesignationId IN " . $whereIN;
        }

        if (!empty($segment) && $segment != 'null') {
            $segment = array($CI->input->post('segment'));
            $whereIN = "( " . join("' , '", $segment) . " )";
            $segment_filter = " AND t1.segmentID IN " . $whereIN;
        }

        if($letter != null){
            $alpha_filter = ' AND ( Ename1 LIKE \''.$letter.'%\') ';
        }

        if($searchKey != ''){
            $searchKey_filter = " WHERE (empShtrCode LIKE '%$searchKey%' OR Ename2 LIKE '%$searchKey%' OR DesDescription LIKE '%$searchKey%' OR ";
            $searchKey_filter .= " description LIKE '%$searchKey%' OR doj LIKE '%$searchKey%' OR EEmail LIKE '%$searchKey%' OR genderStr LIKE '%$searchKey%'";
            $searchKey_filter .= " OR EcMobile LIKE '%$searchKey%' OR managerReporting LIKE '%$searchKey%' )";
        }

        switch ($isDischarged) {
            case 'N':
                $discharged_filter = ' AND isDischarged != 1';
                break;

            case 'Y':
                $discharged_filter = ' AND isDischarged = 1';
                break;

            default:
                $discharged_filter = '';
        }

        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $where = "isSystemAdmin != 1 AND t1.Erp_companyID = " . $companyID . $designation_filter . $segment_filter . $discharged_filter . $alpha_filter. $empStatus_filter;

        $data = $CI->db->query("SELECT * FROM(
                                    SELECT EIdNo, EmpSecondaryCode AS empShtrCode, ECode, Ename1, Ename2, Ename3,Ename4, EmpShortCode, EEmail,
                                    CONCAT( EpAddress1, ' ', EpAddress2, ' ', EpAddress3 ) AS address, EpTelephone, EcPOBox, EcMobile, TitleDescription,
                                    segTB.description AS segment, DesDescription, NIC, managerReporting, employeeType, IFNULL(pendingDataTB.empID,0) AS pendingData,
                                    DATE_FORMAT(EDOJ, '{$convertFormat}') AS doj,  IF(isDischarged=1, 'Discharged', 'Active') AS empStatus,
                                    IF(Gender=1, 'Male', 'Female') AS genderStr, segmentCode, description, Gender, EmpImage, empConfirmedYN
                                    FROM srp_employeesdetails AS t1
                                    LEFT JOIN srp_designation ON DesignationID=t1.EmpDesignationId
                                    JOIN srp_titlemaster ON TitleID=EmpTitleId
                                    LEFT JOIN srp_erp_segment AS segTB ON segTB.segmentID=t1.segmentID
                                    LEFT JOIN srp_nationality ON srp_nationality.NId=t1.Nid
                                    LEFT JOIN srp_erp_systememployeetype AS employeeType ON employeeType.employeeTypeID=t1.EmployeeConType
                                    LEFT JOIN (
                                            SELECT empID, CONCAT( EmpSecondaryCode,' - ', Ename2 )AS managerReporting FROM srp_erp_employeemanagers
                                            JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_employeemanagers.managerID
                                            WHERE companyID={$companyID} AND active=1 AND isprimary=1
                                    ) AS repotingManagerTB ON repotingManagerTB.empID=t1.EIdNo
                                    LEFT JOIN (
                                        SELECT empID FROM srp_erp_employeedatachanges WHERE companyID={$companyID} AND approvedYN=0
                                        UNION 
                                        SELECT empID  FROM srp_erp_employeefamilydatachanges WHERE companyID={$companyID} AND approvedYN=0
                                        UNION 
                                        SELECT empID FROM srp_erp_family_details WHERE approvedYN=0
                                    ) AS pendingDataTB ON pendingDataTB.empID=t1.EIdNo
                                    WHERE {$where}
                                ) t1 {$searchKey_filter} ORDER BY Ename2 LIMIT {$page}, {$per_page}")->result_array();
        $employee_list = $data;
        $color = "#FF0";
        $isAuthenticate = emp_master_authenticate(); /** Check company policy on 'Employee Master Edit Approval' **/
        $returnData = '';
        if(!empty($employee_list)){
            $CI->load->library('s3');
            $male_img = $CI->s3->createPresignedRequest('images/users/male.png', '+1 hour');
            $female_img = $CI->s3->createPresignedRequest('images/users/female.png', '+1 hour');

            $counter = 0;

            foreach($employee_list as $key=>$empData){
                $empID = $empData['EIdNo'];

                $pendingDataDis = 'hidden';
                if($isAuthenticate == 0){
                    $pendingDataDis = ($empData['pendingData'] == 0)? 'hidden': '';
                }

                $empStatus = $empData['empStatus'];
                if($empStatus == 'Discharged'){
                    $label = 'danger';
                }else{
                    $empStatus = ($empData['empConfirmedYN'] != 1)? 'Not confirmed' : $empStatus;
                    $label = ($empData['empConfirmedYN'] != 1)? 'warning' : 'success';
                }

                $empImage = trim($empData['EmpImage'] ?? '');
                if($empImage == ''){
                    $empImage = ($empData['Gender'] == 1)? $male_img: $female_img;
                }
                elseif ($empImage == 'images/users/male.png'){
                    $empImage = $male_img;
                }
                elseif ($empImage == 'images/users/female.png'){
                    $empImage = $female_img;
                }
                else{
                    $empImage = $CI->s3->createPresignedRequest($empImage, '+1 hour');
                }

                $firstDivStyle = ($key==0)? ' style="margin-top: 1px;"' : '';
                $firstDivInput = ($key==0)? '<input id="first-in-emp-list" />' : '';

                 $hide_Name_with_Initials = getPolicyValues('HNWI', 'All');  /**used for hide initial from $empName*/
                 $hrms_flow = getPolicyValues('HRFW', 'All');                /**used for hide initial from $empName*/

                if($searchKey != ''){
                    $mailID = toolTip_empMaster($empData['EEmail'], 23, 20, $searchKey);
                    if($hide_Name_with_Initials == 1 || $hrms_flow == 'ASAAS'){
                        $empName = highlight_word( $empData['Ename4'], $searchKey, $color );
                    }else{
                        $empName = highlight_word( $empData['Ename2'], $searchKey, $color );
                    }

                    $empCode = highlight_word( $empData['empShtrCode'], $searchKey, $color );
                    $designationDes = highlight_word( $empData['DesDescription'], $searchKey, $color );
                    $DOJ = highlight_word( $empData['doj'], $searchKey, $color );
                    $managerReporting = highlight_word( $empData['managerReporting'], $searchKey, $color );
                    $segment = highlight_word( $empData['segment'], $searchKey, $color );
                    $genderStr = highlight_word( $empData['genderStr'], $searchKey, $color );
                    $mobileNo = highlight_word( $empData['EcMobile'], $searchKey, $color );
                }
                else{
                    $mailID = toolTip_empMaster($empData['EEmail'], 23, 20);
                    if($hide_Name_with_Initials == 1 || $hrms_flow == 'ASAAS'){
                        $empName = $empData['Ename4'];
                    }else{
                        $empName = $empData['Ename2'];
                    }
                    $empCode = $empData['empShtrCode'];
                    $designationDes = $empData['DesDescription'];
                    $DOJ = $empData['doj'];
                    $managerReporting = $empData['managerReporting'];
                    $segment = $empData['segment'];
                    $genderStr = $empData['genderStr'];
                    $mobileNo = $empData['EcMobile'];
                }

                $returnData .= $firstDivInput;

                if ($counter % 2 == 0) {
                    if ($counter > 0) {
                        $returnData .= '</div>'; // Close previous row div
                    }
                    $returnData .= '<div class="row">'; // Start a new row
                }

                $returnData .= '<div class="col-md-6">';
                $returnData .= '<div class="info-box">
                                    <span class="info-box-icon"><img src="'.$empImage.'" alt=""></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><strong>'.$empName.'</strong> <span class="pull-right label label-'.$label.' emp-status-label">'.$empStatus.'</span></span>
                                        <span class="info-box-text">'.$designationDes.'</span>
                                        <span class="info-box-text" style="margin-top: 5px"><i class="fa fa-envelope-o"></i> '.$empData['EEmail'].'</span>
                                        <span class="info-box-text"><i class="fa fa-phone"></i> '.$mobileNo.'</span>
                                        <span class="info-box-text pull-right"><i class="fa fa-edit" onclick="edit_empDet('.$empID.')"></i></span>
                                    </div>
                                </div>';
                $returnData .= '</div>';
                $counter++;
            }

            if ($counter % 2 != 0) {
                $returnData .= '</div>';
            }
            $returnData .= '</div>';
        }
        else{
            $returnData .= '<div class="candidate-description client-description applicants-content">No records</div>';
        }
        return [
            'dataCount' => count($employee_list),
            'employee_list' => $returnData
         ];
    }
}


if (!function_exists('toolTip_filter')) {
    function toolTip_filter($str, $maxLength, $outPutContentLength=20)
    {
        $str = trim($str);
        $outPut = $str;
        if(strlen($str) > $maxLength){
            $subStr = substr($str, 0, $outPutContentLength);
            //$outPut = '<b class="aplicant-detail mail-tool-tip" data-title="'.$outPut.'">'.$subStr.'<b class="more-tip">...</b></b>';
            $outPut = '<b class="aplicant-detail mail-tool-tip" title="'.$outPut.'">'.$subStr.'<b class="more-tip">...</b></b>';
        }else{
            $outPut = '<b class="aplicant-detail mail-tool" >'.$str.' </b>';
        }

        return $outPut;
    }
}

if (!function_exists('toolTip_empMaster')) {
    function toolTip_empMaster($str, $maxLength, $outPutContentLength, $searchKey='')
    {
        $str = trim($str);
        $outPut = $str;
        $color = '#FF0';

        if(strlen($str) > $maxLength){
            $subStr = substr($str, 0, $outPutContentLength);
            if($searchKey != '' ){
                if (strpos($str, $searchKey) !== false) {
                    $subStr = '<mark class="searchHighlight">' . $subStr . '</mark>';
                }
            }

            $outPut = '<b class="aplicant-detail mail-tool-tip" data-title="'.$outPut.'">'.$subStr.'<b class="more-tip">...</b></b>';

        }else{
            $str = ($searchKey != '' )? highlight_word( $str, $searchKey, $color ): $str;
            $outPut = '<b class="aplicant-detail mail-tool" >'.$str.' </b>';
        }

        return $outPut;
    }
}


if (!function_exists('fetch_employeeWiseSegment')) {
    function fetch_employeeWiseSegment()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT srp_erp_segment.segmentID, segmentCode, description, count(EIdNo) AS empCount
                                FROM srp_erp_segment
                                JOIN srp_employeesdetails AS empTB ON empTB.segmentID = srp_erp_segment.segmentID
                                AND Erp_companyID={$companyID} AND isSystemAdmin != 1
                                WHERE status=1 AND companyID={$companyID} GROUP BY empTB.segmentID")->result_array();

        return $data;
    }
}

if (!function_exists('highlight_word')) {
    function highlight_word($haystack, $needle, $color = '#FF0'){
        return preg_replace("/($needle)/i", sprintf('<mark style="background-color: %s" class="searchHighlight">$1</mark>', $color), $haystack ?? "" );
    }
}

if (!function_exists('fetch_employeeWiseDesignation')) {
    function fetch_employeeWiseDesignation()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT DesignationID, DesDescription, COUNT(EIdNo) AS empCount
                                FROM srp_employeesdetails AS t1
                                JOIN srp_designation ON DesignationID=t1.EmpDesignationId AND srp_designation.Erp_companyID={$companyID}
                                WHERE t1.Erp_companyID={$companyID} AND isSystemAdmin != 1 GROUP BY DesignationID")->result_array();

        return $data;
    }
}

if (!function_exists('employee_details')) {
    function employee_details($empID=null)
    {
        $CI =& get_instance();

        if($empID){
            $data = $CI->Employee_model->employee_details($empID);
        }
        else{
            $data = $CI->Employee_model->employee_details();
        }

        $CI->load->library('s3');
        if(!empty($data['thisEmpID'])){
            //$empImage = empImageCheck($data['EmpImage'], $data['Gender']);
            $empImage = (!empty($data['EmpImage']))? $data['EmpImage']: '-';
            if(!empty($empImage) ){
                $empImage = $CI->s3->createPresignedRequest($empImage, '+1 hour');
            }
            else{
                $img = ($data['Gender'] == 1)? 'images/users/male.png': 'images/users/female.png';
                $empImage = $CI->s3->createPresignedRequest($img, '+1 hour');
            }
            $data['EmpImage'] = $empImage;

            //$empSignature = empImageCheck($data['empSignature'], 'signature');
            $empSignature = $data['empSignature'];
            if(!empty($empSignature)){
                $empSignature = $CI->s3->createPresignedRequest($empSignature, '+1 hour');
            }
            else{
                $empSignature = $CI->s3->createPresignedRequest('images/users/No_Image.png', '+1 hour');
            }
            $data['empSignature'] = $empSignature;


            $managerID = $data['managerId'];

            if(!empty($managerID)){
                $companyID = current_companyID();
                $managerData = $CI->db->query("SELECT Ename2, EmpImage, Gender, DesDescription FROM srp_employeesdetails AS t1
                                           JOIN srp_designation AS t2 ON t2.DesignationID = t1.EmpDesignationId
                                           WHERE t1.Erp_CompanyID={$companyID} AND t2.Erp_companyID={$companyID}
                                           AND EIdNo={$managerID}")->row_array();

                //$managerImg = empImageCheck($managerData['EmpImage'], $managerData['Gender']);
                $managerImg = (!empty($managerData['EmpImage']))? $managerData['EmpImage']: '-';
                if(!empty($managerImg)){
                    $managerImg = $CI->s3->createPresignedRequest($managerImg, '+1 hour');
                }
                else{
                    $img = ($data['Gender'] == 1)? 'images/users/male.png': 'images/users/female.png';
                    $managerImg = $CI->s3->createPresignedRequest($img, '+1 hour');
                }

                $data['managerName'] =  $managerData['Ename2'];
                $data['managerImg'] =  $managerImg;
                $data['managerDesignation'] =  $managerData['DesDescription'];

            }

            $employeeConType = $data['EmployeeConType'];

            if(!empty($employeeConType)){
                $companyID = current_companyID();
                $employmentType= $CI->db->query("SELECT Description FROM srp_empcontracttypes WHERE Erp_CompanyID={$companyID}
                                               AND EmpContractTypeID={$employeeConType}")->row('Description');

                $data['employmentTypeDisplay'] =  $employmentType;

            }

            $join = $data['EDOJ_ORG'];
            $data['joinDate-display'] = (!empty($join))? date('M d, Y', strtotime($join)) : '';

            if((!empty($join))){
                $toDay = date('Y-m-d');

                if($toDay >= $join){
                    $toDay = new DateTime(date('Y-m-d'));
                    $join = new DateTime($join);

                    $interval = $toDay->diff($join);
                    $y = ($interval->y) ? $interval->y.'y' : '';
                    $m = ($interval->m) ? $interval->m.'m' : '';
                    $d = ($interval->d) ? $interval->d.'d' : '';

                    $periodDisplay = $y;
                    $periodDisplay .= ($periodDisplay != '' && $m != '') ? ' - '.$m : $m;
                    $periodDisplay .= ($periodDisplay != '' && $d != '') ? ' - '.$d : $d;


                    $data['period-display'] = $periodDisplay;
                }

            }

        }

        return $data;
    }


}

if (!function_exists('get_pendingFamilyApprovalData')) {
    function get_pendingFamilyApprovalData($empFamilyDetailsID, $isFromEmpMaster='')
    {
        $companyID = current_companyID();
        $CI =& get_instance();

        if($isFromEmpMaster == 'Y'){
            $pendingData = $CI->db->query("SELECT SUM(pendingCount) AS pendingCount FROM (
                                               SELECT COUNT(id) AS pendingCount FROM srp_erp_employeefamilydatachanges WHERE companyID={$companyID}
                                               AND empID={$empFamilyDetailsID} AND approvedYN!=1
                                               UNION ALL
                                               SELECT COUNT(empfamilydetailsID) AS pendingCount FROM srp_erp_family_details WHERE approvedYN!=1 AND
                                               empID={$empFamilyDetailsID}
                                           )AS t1 ")->row('pendingCount');

            return $pendingData;
        }

        $pendingData = $CI->db->query("SELECT * FROM srp_erp_employeefamilydatachanges WHERE companyID={$companyID}
                                       AND empfamilydetailsID={$empFamilyDetailsID}  AND approvedYN!=1")->result_array();

        return $pendingData;
    }
}

if (!function_exists('fetch_leavePlan')) {
  function fetch_leavePlan($empID=null,$leavaType=null, $filter=null)
    {
        $CI =& get_instance();
        $year = date('Y');
        $companyID = current_companyID();
        $leaveTypeFilter = '';

        if($leavaType){
            $leaveTypeFilter = " AND lMastre.leaveTypeID={$leavaType}";
        }
        // if($leavaType && $filter == 'leaveType'){
        //     $leaveTypeFilter = " AND lMastre.leaveTypeID={$leavaType}";
        // }
        
        $filterStr = ' AND (';
        $conditions = [];
        
        if (is_array($filter) && !empty($filter)) {
            if (in_array('approved', $filter)) {
                $conditions[] = "lMastre.approvedYN = 1";
            }
        
            if (in_array('draft', $filter)) {
                $conditions[] = "(lMastre.applicationType = 1 AND lMastre.approvedYN = 0 AND lMastre.confirmedYN = 0)";
            }

            if (in_array('planned', $filter)) {
                $conditions[] = "(lMastre.applicationType = 2)";
            }
       
            if (in_array('confirmed', $filter)) {
                $conditions[] = "(lMastre.applicationType = 1 AND lMastre.confirmedYN = 1 AND lMastre.approvedYN = 0 )";
            }

        
            if (!empty($conditions)) {
                $filterStr .= implode(' OR ', $conditions);
                $filterStr .= ')';
            }
        
            $leaveTypeFilter .= $filterStr;
        }
       



        if( $empID != null ){
            $result =  $CI->db->query("SELECT * FROM (
                                            SELECT leaveMasterID AS id, Ename2 AS text, DATE_FORMAT(startDate,'%d-%m-%Y') AS start_date, startDate, `comments` AS levComment,
                                            (DATEDIFF(endDate, startDate)+1) AS duration, 0 AS progress, documentCode, empID, '' AS parent, applicationType,
                                            IF(applicationType=1, 'Leave', 'Plan') AS typeText, DATE_FORMAT(endDate, '%Y-%m-%d') endDate2, approvedYN, confirmedYN,
                                            CASE
                                               WHEN (applicationType=2) THEN '#fda70a'
                                               WHEN (approvedYN=1) THEN '#166123'
                                               WHEN (confirmedYN=1) THEN '#13f358'
                                               ELSE '#61cde2'
                                            END AS color
                                            FROM srp_erp_leavemaster AS lMastre
                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMastre.empID AND Erp_companyID={$companyID} AND isDischarged = 0
                                            JOIN (
                                              SELECT empID AS rptEmpID FROM srp_erp_employeemanagers WHERE active=1 AND  managerID={$empID}
                                              AND companyID={$companyID}
                                            ) AS rptTB ON rptTB.rptEmpID = lMastre.empID
                                            WHERE companyID={$companyID} {$leaveTypeFilter} AND ( YEAR(startDate) >= YEAR(NOW()) AND YEAR(endDate) <= (YEAR(NOW())+1) )
                                            UNION
                                            SELECT leaveMasterID AS id, Ename2 AS text, DATE_FORMAT(startDate,'%d-%m-%Y') AS start_date, startDate, `comments` AS levComment,
                                            (DATEDIFF(endDate, startDate)+1) AS duration, 0 AS progress, documentCode, empID, '' AS parent, applicationType,
                                            IF(applicationType=1, 'Leave', 'Plan') typeText, DATE_FORMAT(endDate, '%Y-%m-%d') endDate2, approvedYN, confirmedYN,
                                            CASE
                                               WHEN (applicationType=2) THEN '#fda70a'
                                               WHEN (approvedYN=1) THEN '#166123'
                                               WHEN (confirmedYN=1) THEN '#13f358'
                                               ELSE '#61cde2'
                                            END AS color
                                            FROM srp_erp_leavemaster AS lMastre
                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMastre.empID AND Erp_companyID={$companyID} AND isDischarged = 0
                                            WHERE companyID={$companyID} AND lMastre.empID={$empID} {$leaveTypeFilter} AND ( YEAR(startDate) >= YEAR(NOW()) AND YEAR(endDate) <= (YEAR(NOW())+1) )
                                       	UNION
                                        SELECT
                                            leaveMasterID AS id,
                                            Ename2 AS text,
                                            DATE_FORMAT( startDate, '%d-%m-%Y' ) AS start_date,
                                            startDate,
                                            `comments` AS levComment,
                                            ( DATEDIFF( endDate, startDate ) + 1 ) AS duration,
                                            0 AS progress,
                                            documentCode,
                                            empID,
                                            '' AS parent,
                                            applicationType,
                                        IF
                                            ( applicationType = 1, 'Leave', 'Plan' ) typeText,
                                            DATE_FORMAT( endDate, '%Y-%m-%d' ) endDate2,
                                            approvedYN,
                                            confirmedYN,
                                        CASE
                                            
                                            WHEN ( applicationType = 2 ) THEN
                                            '#fda70a' 
                                            WHEN ( approvedYN = 1 ) THEN
                                            '#166123' 
                                            WHEN ( confirmedYN = 1 ) THEN
                                            '#13f358' ELSE '#61cde2' 
                                            END AS color 
                                        FROM
                                            srp_erp_leavemaster AS lMastre
                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMastre.empID 
                                            AND Erp_companyID = {$companyID} 
                                            AND isDischarged = 0
                                            {$leaveTypeFilter}
                                            JOIN (
                                            SELECT
                                                empID AS deptEmpID 
                                            FROM
                                                srp_empdepartments
                                                JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID = srp_empdepartments.DepartmentMasterID 
                                            WHERE
                                                srp_empdepartments.isActive = 1 
                                                AND srp_departmentmaster.hod_id = {$empID} 
                                                AND srp_empdepartments.Erp_companyID = {$companyID} 
                                              
                                            ) AS depTB ON depTB.deptEmpID = lMastre.empID 
                                        WHERE
                                            companyID = {$companyID} 
                                            AND (
                                                YEAR ( startDate ) >= YEAR ( NOW( ) ) 
                                            AND YEAR ( endDate ) <= ( YEAR ( NOW( ) ) + 1 ) 
                                        ))AS t1 ORDER BY t1.startDate")->result_array();


        } else {
            $result = $CI->db->query("SELECT leaveMasterID AS id, Ename2 AS text, DATE_FORMAT(startDate,'%d-%m-%Y') AS start_date, `comments` AS levComment,
            (DATEDIFF(endDate, startDate)+1) AS duration, 0 AS progress, documentCode, IF(applicationType=1, 'Leave', 'Plan') AS typeText,
            empID, '' AS parent, applicationType, DATE_FORMAT(endDate, '%Y-%m-%d') endDate2, approvedYN, confirmedYN,
            CASE
                WHEN (applicationType=2) THEN '#fda70a'
                WHEN (approvedYN=1) THEN '#166123'
                WHEN (confirmedYN=1) THEN '#13f358'
                ELSE '#61cde2'
            END AS color
            FROM srp_erp_leavemaster AS lMastre
            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMastre.empID AND Erp_companyID={$companyID}
            WHERE companyID={$companyID} AND (YEAR(startDate) >= YEAR(NOW()) AND YEAR(endDate) <= (YEAR(NOW())+1))
            ORDER BY startDate")->result_array(); // $filterQueryString
        }

        return $result;
    }
}


if (!function_exists('getLeaveApprovalSetup')) {
    function getLeaveApprovalSetup($isSetting = 'N',$input_companyId=null,$leaveGroupID = null)
    {
        if($input_companyId==null){
            $companyID = current_companyID();
        }else {
            $companyID = $input_companyId;
        }
        $empID = current_userID();
        $CI =& get_instance();
        $companies = $companyID;

        $appSystemValues = $CI->db->query("SELECT * FROM srp_erp_leavesetupsystemapprovaltypes")->result_array();

        if($isSetting == 'Y'){
            $arr = [ 0 => '' ];
            foreach($appSystemValues as $key=>$val){
                $arr[$val['id']] = $val['description'];
            }
            $appSystemValues = $arr;
        }

        $leaveStr = '';

        if($leaveGroupID){
            $leaveStr = "AND setupTB.leaveGroupID = '{$leaveGroupID}'";
        }

        $approvalLevel = $CI->db->query("SELECT approvalLevel FROM srp_erp_documentcodemaster WHERE documentID = 'LA' AND
                                         companyID IN ({$companies}) ")->row('approvalLevel');

        $approvalSetup = $CI->db->query("SELECT approvalLevel, approvalType, empID, systemTB.*
                                         FROM srp_erp_leaveapprovalsetup AS setupTB
                                         JOIN srp_erp_leavesetupsystemapprovaltypes AS systemTB ON systemTB.id = setupTB.approvalType
                                         WHERE companyID IN ({$companies}) {$leaveStr}  ORDER BY approvalLevel")->result_array();

        $approvalEmp = $CI->db->query("SELECT approvalLevel, empTB.empID
                                       FROM srp_erp_leaveapprovalsetup AS setupTB
                                       JOIN srp_erp_leaveapprovalsetuphremployees AS empTB ON empTB.approvalSetupID = setupTB.approvalSetupID
                                       WHERE setupTB.companyID IN ({$companies})  AND empTB.companyID IN ({$companies}) {$leaveStr} ")->result_array();

                                       
        
        $covering = $CI->db->query("SELECT empTB.coveringID,setupTB.currentLevelNo
                                       FROM srp_erp_leavemaster AS setupTB
                                       JOIN srp_erp_leave_covering_employee AS empTB ON empTB.leaveapplicationID = setupTB.leaveMasterID
                                       WHERE setupTB.companyID IN ({$companies}) AND setupTB.approvedYN=0 AND setupTB.confirmedYN=1  {$leaveStr} ")->result_array();

        if(!empty($approvalEmp)){
            $approvalEmp = array_group_by($approvalEmp, 'approvalLevel');
        }

        return [
            'appSystemValues' => $appSystemValues,
            'approvalLevel' => $approvalLevel,
            'approvalSetup' => $approvalSetup,
            'approvalEmp' => $approvalEmp,
            'covering' => $covering
        ];
    }
}

if (!function_exists('get_hrDocuments')) {
    function get_hrDocuments()
    {
        $companyID = current_companyID();
        $CI =& get_instance();

        $hrDocuments = $CI->db->query("SELECT id, documentDescription, documentFile FROM srp_erp_hrdocuments
                                       WHERE companyID={$companyID} ")->result_array();

        return $hrDocuments;
    }
}

if (!function_exists('grade_drop')) {
    function grade_drop($isDrop=true)
    {
        $CI =& get_instance();
        $CI->db->select('gradeID,gradeDescription');
        $CI->db->from('srp_erp_employeegrade');
        $CI->db->where('srp_erp_employeegrade.companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        $data_arr = [];
        if($isDrop == true){
            $data_arr = array('' => 'Select Grade');
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['gradeID'] ?? '')] = trim($row['gradeDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_degree')) {
    function fetch_degree($asResult = null, $type = 'academic')
    {
        $CI =& get_instance();
        $data = $CI->db->where('type', $type)
            ->get('srp_erp_degreetype')
            ->result_array();

        if ($asResult == null) {
            $data_arr = array('' => '');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['degreeTypeID'] ?? '')] = trim($row['degreeDescription'] ?? '');
                }
            }
            return $data_arr;
        } else {
            return $data;
        }

    }
}

if (!function_exists('sickLeave_setupData')) {
    function sickLeave_setupData()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $isNonSalaryProcess = getPolicyValues('NSP', 'All');

        $result = $CI->db->query("SELECT leaveTypeID, description, typeConfirmed, formulaPayTB.*
                                  FROM srp_erp_leavetype AS typeMaster
                                  LEFT JOIN (
                                      SELECT id, formulaString, t1.salaryCategoryID, leaveTypeID AS payID, isNonPayroll, salaryDescription
                                      FROM srp_erp_sickleavesetup AS t1
                                      LEFT JOIN (
                                        SELECT salaryCategoryID, salaryDescription FROM srp_erp_pay_salarycategories WHERE companyID='{$companyID}'
                                      ) AS t2 ON t2.salaryCategoryID = t1.salaryCategoryID
                                      WHERE companyID='{$companyID}'
                                  ) AS formulaPayTB ON formulaPayTB.payID=typeMaster.leaveTypeID
                                  WHERE companyID = '{$companyID}' AND isSickLeave =1")->result_array();
        if(!empty($result)){
            $tempArr = array_group_by($result, 'leaveTypeID');
            $result = [];  $i = 0;
            foreach($tempArr as $leaveTypeID=>$row){

                $count = count($row);
                foreach($row as $key=>$data){
                    $isNonPayroll = $data['isNonPayroll'];
                    if((empty($isNonPayroll))){
                        $isNonPayroll = ($key == 0)? 'N' : 'Y';
                    }

                    $result[$i] = $data;
                    $result[$i]['setupID'] = $leaveTypeID.'|'.$isNonPayroll;
                    $result[$i]['isNonPayroll'] = $isNonPayroll;

                    if($count == 1 && $isNonSalaryProcess==1){
                        $i++;
                        $isNonPayroll = ($isNonPayroll == 'Y')? 'N' : 'Y';
                        $result[$i] = $data;
                        $result[$i]['setupID'] = $leaveTypeID.'|'.$isNonPayroll;
                        $result[$i]['salaryCategoryID'] = '';
                        $result[$i]['salaryDescription'] = '';
                        $result[$i]['formulaString'] = '';
                        $result[$i]['isNonPayroll'] = $isNonPayroll;
                    }

                    $i++;
                }
            }
        }

        return $result;
    }
}

if (!function_exists('formulaBuilder_to_sql_simple_conversion')) {
    function formulaBuilder_to_sql_simple_conversion($formula, $apiCompID = null)
    {
        $salary_categories_arr = salary_categories(array('A', 'D'), '', $apiCompID);
        $formulaText = '';
        $salaryCatID = array();
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        $formula_arr = explode('|', $formula); // break the formula

        $n = 0;
        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand
                    $formulaText .= $formula_row;
                    $formulaDecode_arr[] = $formula_row;
                } else {

                    $elementType = $formula_row[0];

                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                    } else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $salaryCatID[$n]['ID'] = $catArr[1];

                        $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr) {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                        $formulaText .= $salaryDescription;

                        $salaryDescription_arr = explode(' ', $salaryDescription);
                        $salaryDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $salaryDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';

                        $n++;

                    }
                    else if ($elementType == '!') {
                        $subDetails = explode('!', $formula_row);
                        if ($subDetails[1] == 'FG') {
                            $formulaText .= 'Basic Pay';
                            $formulaDecode_arr[] = 'totFixPayment';

                        }
                        else if ($subDetails[1] == 'TW') {
                            $formulaText .= 'Total working days';
                            $formulaDecode_arr[] = 'totalWorkingDays';
                        }
                    }
                }
            }

        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_str = '';
        $select_str2 = '';
        $whereInClause = '';
        $separator = '';

        foreach ($salaryCatID as $key1 => $row) {
            $select_str .= $separator . 'IF(salDec.salaryCategoryID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'];
            $select_str2 .= $separator . 'SUM('.$row['cat'] .') AS ' . $row['cat'];
            $whereInClause .= $separator . ' ' . $row['ID'];
            $separator = ',';
        }

        return array(
            'formulaDecode' => $formulaDecode,
            'select_str' => $select_str,
            'select_str2' => $select_str2,
            'whereInClause' => $whereInClause,
        );
    }
}

if (!function_exists('payroll_group_master_action')) {
    function payroll_group_master_action($masterID, $description)
    {
        $fetch = "load_groupSetup(" . $masterID . ", '".$description."')";
        $status = '<span class="pull-right">';
        $status .= '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';
        $status .= '&nbsp; | &nbsp; <a onclick="delete_groupSetup(' . $masterID . ')">';
        $status .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" ></span>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('fetch_payroll_access_group')) {
    function fetch_payroll_access_group()
    {
        $CI =& get_instance();

        $CI->db->select('groupID,groupName');
        $CI->db->from('srp_erp_payrollgroups');
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();

        $data_arr = [];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['groupID'] ?? '')] = trim($row['groupName'] ?? '');

            }
        }

        return $data_arr;
    }
}

if (!function_exists('employee_list_by_segment')) {
    function employee_list_by_segment($dropDown=null, $status='', $isInitial=1)
    {
        $CI =& get_instance();
        $segmentID = $CI->input->post('segmentID');
        $currency_filter = $CI->input->post('currency_filter');
        $isContractRpt = $CI->input->post('isContractRpt');

        $CI->db->select('EIdNo, ECode, Ename2');
        $CI->db->from('srp_employeesdetails empTB');
        $CI->db->join('srp_erp_segment', 'srp_erp_segment.segmentID=empTB.segmentID');
        $CI->db->where('Erp_companyID', current_companyID());



        if($isContractRpt == 1){
            $CI->db->join('srp_erp_empcontracthistory con', 'con.empID = empTB.EIdNo')
                  ->where('isCurrent', 1)->where('con.companyID', current_companyID());
        }

        if($status !== ''){
            $CI->db->where('isDischarged ', $status);
        }

        if(!empty($segmentID)){
            $CI->db->where_in('empTB.segmentID', $segmentID);
        }

        if(!empty($currency_filter)){
            $CI->db->where('empTB.payCurrencyID', $currency_filter);
        }

        if($CI->input->post('isFromSalaryDeclaration')){
           $CI->db->where("isPayrollEmployee =1 AND empConfirmedYN=1");
        }


        if($isInitial != 1 && empty($segmentID)){
            $data = [];
        }else{
            $data = $CI->db->get()->result_array();
        }


        if($dropDown == null){
            return $data;
        }

        $data_arr = [];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') .' - '.trim($row['Ename2'] ?? '');

            }
        }

        return $data_arr;
    }
}

$checkList = [];
$count_payGroup_validation = 0;
if (!function_exists('payGroup_validation')) {
    function payGroup_validation($searchID,  $payGroupCategories)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $returnData = null;

        global $count_payGroup_validation;
        global $checkList;
        $count_payGroup_validation++;

        if($count_payGroup_validation > 100) {
            //If the recursive worked more than 100 times than terminate the function
            return ['w', 'Group validation function get terminated.<br/>Please call for software support.'];
        }

        $result = $CI->db->query("SELECT payGroupID, payGroupCategories FROM srp_erp_paygroupformula
                        WHERE companyID = {$companyID}
                        AND payGroupID IN ({$payGroupCategories})
                        AND (
                             payGroupCategories LIKE '%,{$searchID},%' OR payGroupCategories='{$searchID}' OR payGroupCategories
                             LIKE '{$searchID},%' OR payGroupCategories LIKE '%,{$searchID}'
                        )")->result_array();

        if(!empty($result)){
            return ['e', 'exist'];
        }
        else{
            $result = $CI->db->query("SELECT payGroupID, payGroupCategories FROM srp_erp_paygroupformula
                        WHERE companyID = {$companyID}
                        AND payGroupID IN ({$payGroupCategories}) AND payGroupCategories IS NOT NULL")->result_array();

            if(!empty($result)){
                foreach ($result as $row){

                    if( !is_array($checkList) ){
                        $checkList = [];
                    }

                    if(!in_array( $row['payGroupID'], $checkList)){
                        $checkList[] = $row['payGroupID'];

                        $return = payGroup_validation($searchID, $row['payGroupCategories']);

                        if($return[0] == 'e'){
                            $returnData = $return;
                            break;
                        }
                    }

                }
            }
        }

        if(!empty($returnData)){
            return $returnData;
        }

        return ['s', ''];

    }
}

if (!function_exists('floors_fetch')) {
    function floors_fetch()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('floorID, floorDescription');
        $CI->db->from('srp_erp_pay_floormaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isActive', 1);
        $data = $CI->db->get()->result_array();

        return $data;
    }
}

if (!function_exists('group_structure_type')) {
    function group_structure_type()
    {
        $CI =& get_instance();
        $CI->db->select('groupStructureTypeID,description');
        $CI->db->from('srp_erp_groupstructuretype');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Please Select');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['groupStructureTypeID'] ?? '')] = trim($row['description'] ?? '');

            }
        }
        return $data_arr;
    }
}

if (!function_exists('company_groupmaster_dropdown')) {
    function company_groupmaster_dropdown($parent)
    {
        $CI =& get_instance();
        $CI->db->select('companyGroupID,description');
        $CI->db->from('srp_erp_companygroupmaster');
        $CI->db->where('masterID', $parent);
        $data = $CI->db->get()->result_array();
        $data_arr = [];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['companyGroupID'] ?? '')] = trim($row['description'] ?? '');

            }
        }
        return $data_arr;
    }
}

if (!function_exists('group_company_dropdown')) {
    function group_company_dropdown()
    {
        $CI =& get_instance();

        $data = $CI->db->query("SELECT company_id,CONCAT(company_code,' | ',company_name) as company FROM srp_erp_company LEFT JOIN srp_erp_companygroupdetails On company_id=srp_erp_companygroupdetails.companyID WHERE companyID IS NULL AND confirmedYN=1")->result_array();
        $data_arr = [];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['company_id'] ?? '')] = trim($row['company'] ?? '');

            }
        }
        return $data_arr;
    }
}

if (!function_exists('final_settlement_data')) {
    function final_settlement_data($id)
    {
        $CI =& get_instance();

        $masterData = $CI->db->query("SELECT Ename2, ECode, ma.* FROM srp_erp_pay_finalsettlementmaster ma
                                     JOIN srp_employeesdetails ed ON ma.empID = ed.EIdNo WHERE masterID={$id}")->row_array();

        $data['masterData'] = $masterData;
        $empID = $masterData['empID'];

        $data['payroll'] = $CI->db->query("SELECT salaryDescription, SUM(amount) amount
                                FROM srp_erp_pay_salarydeclartion decl
                                JOIN srp_erp_pay_salarycategories cat ON cat.salaryCategoryID = decl.salaryCategoryID
                                WHERE employeeNo = '{$empID}' GROUP BY cat.salaryCategoryID")->result_array();

        $data['non_payroll'] = $CI->db->query("SELECT salaryDescription, SUM(amount) amount
                                FROM srp_erp_non_pay_salarydeclartion decl
                                JOIN srp_erp_pay_salarycategories cat ON cat.salaryCategoryID = decl.salaryCategoryID
                                WHERE employeeNo = '{$empID}' GROUP BY cat.salaryCategoryID")->result_array();
        return $data;
    }
}

if (!function_exists('fetch_final_settlement_items')) {
    function fetch_final_settlement_items()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT typeID, description, IF(isDedction = 1, 'D', 'A') iType
                                FROM srp_erp_pay_finalsettlementitems t ")->result_array();

        $data = array_group_by($data, 'iType');

        return $data;
    }
}
if (!function_exists('showLevelno')) {
    function showLevelno($documentID,$fromAmount, $toAmount,$levelNo,$approvalUserID)
    {
        $CI =& get_instance();
        $documentID_arr = explode(',', $documentID);
        $fromAmount = $fromAmount !== null ? $fromAmount : 0;
        $toAmount = $toAmount !== null ? $toAmount : 0;
       if($levelNo>0){
        if (in_array('EC', $documentID_arr)) {
            $CI->db->select('srp_erp_expenseclaimcategories.claimcategoriesDescription');
            $CI->db->from('srp_erp_expenseclaimcategories');
            $CI->db->join('srp_erp_approvalusers AP', 'AP.typeID = srp_erp_expenseclaimcategories.expenseClaimCategoriesAutoID');
            $CI->db->where('AP.companyID', current_companyID());
            $CI->db->where('AP.approvalUserID', $approvalUserID);
            $CI->db->where('AP.documentID', 'EC');
            $query = $CI->db->get();
            $cat = $query->row('claimcategoriesDescription'); 
            $cat = $cat !== null ? $cat : '-';
            
            $data = "<center>Level No - $levelNo<br>From Amount: $fromAmount<br>To Amount: $toAmount <br>Category: $cat</center>";
        }
        else{
            $data="<center>Level No - $levelNo </center>";
        }
          
       }else{
           $data="<center>No Approval</center>";
       }

        return $data;
    }
}

if (!function_exists('final_settlement_action')) {
    function final_settlement_action($masterID, $confirmYN, $approvedYN, $createdUserID, $cnEmpID, $documentCode, $pvID)
    {
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($approvedYN == 1 && $pvID > 0) {
            $status .= '<li><a onclick="documentPageView_modal(\'PV\', ' . $pvID . ')" title="Payment Voucher"><i class="fa fa-file" style="color: #4caf50;"></i> Payment Voucher</a></li>';
        }

        if ($confirmYN != 1) {
            $status .= '<li><a onclick="load_details(' . $masterID . ')" title="Edit"><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
        } else {
            $status .= '<li><a target="_blank" onclick="load_details(' . $masterID . ')" title="View"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';
        }

        $status .= '<li><a onclick="print_document(' . $masterID . ', \'' . $documentCode . '\')" title="Print"><span class="glyphicon glyphicon-print" style="color:#607d8b"></span> Print</a></li>';

        if (($createdUserID == current_userID() || $cnEmpID == current_userID()) && $approvedYN == 0 && $confirmYN == 1) {
            $status .= '<li><a onclick="referBackDeclaration(' . $masterID . ');" title="Refer Back"><span class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span> Refer Back</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('final_settlement_approval_action')) {
    function final_settlement_approval_action($masterID, $approvalLevelID, $docCode, $appYN, $type)
    {
        $status = ($type=='edit')?'<span class="pull-right">':'';
        if ($appYN == 1) {
            $str = ($type=='edit')?'<span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span>':$docCode;
            $status .= '<a onclick="load_approvalView(' . $masterID . ',' . $approvalLevelID . ',' . $appYN . ')">';
            $status .= $str.'</a> &nbsp; ';
            $status .= ' | &nbsp; <span title="Print" rel="tooltip" class="glyphicon glyphicon-print" 
            onclick="print_document_letter('.$masterID.', \''.$docCode.'\')" style="color:#3c8dbc"></span>&nbsp;';
        }else{
            $str = ($type=='edit')?'<span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span>':$docCode;
            $status .= '<a onclick="load_approvalView(' . $masterID . ',' . $approvalLevelID . ',' . $appYN . ')">';
            $status .= $str.'</a> &nbsp; ';
        }
        $status .= ($type=='code')?'</span>':'';

        return $status;
    }
}

if (!function_exists('finalSettlement_gl_config')) {
    function finalSettlement_gl_config()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT typeTB.typeID, typeTB.description, isDedction, 
                                GLID, GLSecondaryCode, GLDescription 
                                FROM srp_erp_pay_finalsettlementitems typeTB
                                LEFT JOIN (
                                    SELECT typeID, conf.GLID, chAcc.GLSecondaryCode, chAcc.GLDescription 
                                    FROM srp_erp_pay_finalsettlement_gl_config conf
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = conf.GLID
                                    WHERE conf.companyID = {$companyID}                                     
                                ) glConf ON glConf.typeID = typeTB.typeID 
                                WHERE isGLAssignable = 1 AND typeTB.typeID NOT IN ( 15 ) ORDER BY typeTB.typeID")->result_array();

        return $data;
    }
}

if (!function_exists('load_gratuity_setup_action')) {
    function load_gratuity_setup_action($id, $des, $provisionGL, $expenseGL)
    {
        $url = site_url('Employee/formulaDecode/GRATUITY');
        
        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                           
                           <li><a href="#" onclick="formulaModalOpen(\'' . $des . '\', \'' . $id . '\', \'' . $url . '\', \'gratuity-formula-' . $id . '\')">
                               <span class="fa fa-superscript" style="color:#607d8b"></span> Formula</a>
                           </li>
                           
                           <li><a href="#" onclick="edit_gratuity_master(' . $id . ', \'' . $des . '\', \'' . $expenseGL . '\', \'' . $provisionGL . '\')">
                               <span class="glyphicon glyphicon-pencil" style="color:#116f5e"></span> Edit</a>
                           </li>
                           
                           <li><a href="#" onclick="load_gratuity_details(' . $id . ')">
                               <span class="fa fa-fw fa-eye" style="color:#03a9f4"></span> View</a>
                           </li>
                           
                           <li><a href="#" onclick="delete_gratuity_master(' . $id . ')">
                               <span class="glyphicon glyphicon-trash" style="color:#d15b47"></span> Delete</a>
                           </li>
                           
                       </ul>
                   </div>';

        return $action;
    }
}

if (!function_exists('action_gratuity')) {
    function action_gratuity($id, $des, $provisionGL,$expenseGL)
    {

        $url = site_url('Employee/formulaDecode/GRATUITY');
        $action = '<a onclick="formulaModalOpen(\'' . $des . '\', \'' . $id . '\', \''.$url.'\', \'gratuity-formula-'.$id.'\')"><span title="Formula"';
        $action .= ' rel="tooltip" class="fa fa-superscript"></span></a> &nbsp;  | &nbsp; ';
        $action .= '<a onclick="edit_gratuity_master(' . $id . ',\'' . $des . '\',\'' . $expenseGL . '\',\'' . $provisionGL . '\')">';
        $action .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= ' &nbsp;  | &nbsp; <a onclick="load_gratuity_details(' . $id . ')">';
        $action .= '<span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>';
        $action .= ' &nbsp;  | &nbsp; <a onclick="delete_gratuity_master(' . $id . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('get_gratuity_slabDetails')) {
    function get_gratuity_slabDetails($masterID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT slb.id, slabTitle, startYear, endYear, form.formulaString 
                                FROM srp_erp_pay_gratuityslab slb
                                LEFT JOIN srp_erp_pay_gratuityformula form ON form.autoID = slb.id
                                AND form.masterType = 'GRATUITY-SLAB'
                                WHERE slb.companyID={$companyID} AND gratuityMasterID={$masterID}")->result_array();
        return $data;
    }
}

if (!function_exists('gratuity_drop')) {
    function gratuity_drop($isDrop=true)
    {
        $CI =& get_instance();
        $CI->db->select('gratuityID,gratuityDescription');
        $CI->db->from('srp_erp_pay_gratuitymaster');
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();

        $data_arr = [];
        if($isDrop == true){
            $data_arr = array('' => 'Select Gratuity');
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['gratuityID'] ?? '')] = trim($row['gratuityDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('employee_bank_drop')) {
    function employee_bank_drop($empID, $isDrop=false)
    {
        $CI =& get_instance();
        $data =  $CI->db->query("SELECT bnk.bankID, bankName, accountNo, accountHolderName, acc.id,
                                 acc.isActive, bnk.bankSwiftCode, branchName, brn.branchID
                                 FROM srp_erp_pay_salaryaccounts AS acc
                                 JOIN srp_erp_pay_bankmaster AS bnk ON bnk.bankID=acc.bankID
                                 JOIN srp_erp_pay_bankbranches AS brn ON brn.branchID=acc.branchID
                                 WHERE employeeNo = {$empID}")->result_array();

        if($isDrop === false){
            return $data;
        }

        $data_arr = ['' => 'Select Employee Bank'];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['bankName'] ?? '').' | '. trim($row['accountNo'] ?? '');
                //$data_arr[trim($row['id'] ?? '')] = trim($row['bankName'] ?? '').' | '.trim($row['branchName'] ?? '').' | '.trim($row['accountNo'] ?? '').' | '.trim($row['bankSwiftCode'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('emergency_contact_status')) {
    function emergency_contact_status($is, $status)
    {
        $checked = ($status == 1) ? 'checked' : '';
        $isDisable = ($status == 1) ? 'disabled' : '';
        $str = '<input type="checkbox" class="switch-chk" id="emergency_contact_status' . $is . '" onchange="change_emergency_contact_status(this, ' . $is . ')"';
        $str .= 'data-size="mini" data-on-text="Yes" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="No" data-label-width="0" ' . $checked . ' '.$isDisable.'>';
        return  $str;
    }
}

if (!function_exists('travel_frequency_drop')) {
    function travel_frequency_drop()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $data =  $CI->db->query("SELECT travelFrequencyID, frequencyDescription
                                 FROM srp_erp_travelfrequency                               
                                 WHERE companyID = {$companyID}")->result_array();


        $data_arr = ['' => 'Select Frequency'];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['travelFrequencyID'] ?? '')] = trim($row['frequencyDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('familyStatus_drop')) {
    function familyStatus_drop()
    {
        $CI =& get_instance();
        $CI->db->select('familyStatusID,description');
        $CI->db->from('srp_erp_familystatus');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Employment Status');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['familyStatusID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('emp_document_sys_type_drop')) {
    function emp_document_sys_type_drop()
    {
        $CI =& get_instance();
        $CI->db->select('id,description');
        $CI->db->from('srp_erp_system_document_types');
        $CI->db->order_by('description', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('action_emp_docs')) {
    function action_emp_docs($id, $file, $incCount, $docDesID, $docDescription)
    {
        $CI =& get_instance();
        $action = '';
        if($incCount > 0){
            $action .= '<button type="button" style="padding: 0px 2px;" onclick="load_inactiveDocs(' . $docDesID . ', \''.$docDescription.'\')">';
            $action .= '<span title="History" rel="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i></span>';
            $action .= '</button>';
        }

        if(!empty($id)) {

            $action .= ($action == '')? '': '&nbsp; | &nbsp; ';
            $action .= '<a onclick="editDocument(' . $id . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            $action .= '&nbsp; | &nbsp; <span title="Upload" rel="tooltip" onclick="documentUpload(' . $id . ', \''.$docDescription.'\')"><i class="fa fa-upload"></i></span>';

            if (!empty($file)) {
                /*$file = base_url() . 'documents/users/' . $file;
                $downLink = generate_encrypt_link_only($file);*/
                $downLink = $CI->s3->createPresignedRequest($file, '+1 hour');
                $action .= '&nbsp; | &nbsp; <span title="Download" rel="tooltip" onclick="downloadDoc(\'' . $downLink . '\')"><i class="fa fa-download"></i></span>';
            }
            $action .= '&nbsp; | &nbsp; <a onclick="removeDocument(' . $id . ', \'act\')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }
        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('action_emp_docs_history')) {
    function action_emp_docs_history($id, $file)
    {
        /*$file = base_url() . 'documents/users/' . $file;
        $downLink = generate_encrypt_link_only($file);*/
        $CI =& get_instance();
        $downLink = $CI->s3->createPresignedRequest($file, '+1 hour');
        $action = '<span title="Download" rel="tooltip" onclick="downloadDoc(\'' . $downLink . '\')"><i class="fa fa-download"></i></span>';
        $action .= '&nbsp; | &nbsp; <a onclick="removeDocument(' . $id . ', \'his\')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('generate_s3_link')) {
    function generate_s3_link($file, $desc)
    {
        $CI =& get_instance();
        $downLink = $CI->s3->createPresignedRequest($file, '+1 hour');
        $action = '<a rel="tooltip" href="' . $downLink . '" target="_blank">'.$desc.'</span>';
        return '<span class="">' . $action . '</span>';
    }
}

if (!function_exists('emp_docs_status')) {
    function emp_docs_status($id, $incCount)
    {

        if(!empty($id)) {
             $str = '<span class="label label-success">&nbsp;</span>';
        }
        else{
            $color = ($incCount > 0)? 'warning': 'danger';
            $str = '<span class="label label-'.$color.'">&nbsp;</span>';
        }
        return '<div style="text-align: center">'.$str.'</div>';
    }
}

if (!function_exists('drop_down_sso_and_payee')) {
    function drop_down_sso_and_payee($returnType=0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT ssoTB.socialInsuranceID AS autoID, Description AS des, 'S' AS type
                                FROM srp_erp_socialinsurancemaster ssoTB WHERE companyID = '{$companyID}'
                                UNION 
                                SELECT payeeMasterID AS autoID, description AS des, 'P' AS type
                                FROM srp_erp_payeemaster WHERE srp_erp_payeemaster.companyID ='{$companyID}'")->result_array();


        if($returnType == 1){
            return $data;
        }

        $data_arr = ['' => $CI->lang->line('common_select_type')];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['type'] ?? '').'-'.trim($row['autoID'] ?? '')] = trim($row['des'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('emp_document_sys_sub_type_data')) {
    function emp_document_sys_sub_type_data()
    {
        $CI =& get_instance();
        $CI->db->select('sub_id,system_type_id,description');
        $CI->db->from('srp_erp_system_document_sub_types');
        $CI->db->where('companyID', current_companyID());
        $CI->db->order_by('system_type_id', 'ASC');
        $CI->db->order_by('description', 'ASC');
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('emp_docs_full_description')) {
    function emp_docs_full_description($description, $sub_typesDes)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $lang_document = $CI->lang->line('common_document');
        $lang_type = $CI->lang->line('common_type');

        $str = "<b>{$lang_document}</b> : $description";

        if($sub_typesDes != ''){
            $str .= "<br/><b>{$lang_type}</b> : $sub_typesDes";
        }

        return $str;
    }
}

if (!function_exists('commission_scheme_drop')) {
    function commission_scheme_drop()
    {
        $CI =& get_instance();
        $CI->db->select('id,description');
        $CI->db->from('srp_erp_pay_commissionscheme');
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select scheme');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('salary_advance_action')) {
    function salary_advance_action($masterID, $confirmYN, $approvedYN, $createdUserID, $cnEmpID, $documentCode)
    {
        $dropdownItems = '';

        if ($confirmYN != 1) {
            $dropdownItems .= '<li><a onclick="load_details(' . $masterID . ')"><span class="glyphicon glyphicon-pencil" style="color:#116f5e;"></span> Edit</a></li>';
        } else {
            $dropdownItems .= '<li><a target="_blank" onclick="view_modal(' . $masterID . ')"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';
        }

        $dropdownItems .= '<li><a onclick="print_document(' . $masterID . ', \'' . $documentCode . '\')"><span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';

        if (($createdUserID == current_userID() || $cnEmpID == current_userID()) && $approvedYN == 0 && $confirmYN == 1) {
            $dropdownItems .= '<li><a onclick="referBack_document(' . $masterID . ')"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        if ($confirmYN != 1) {
            $dropdownItems .= '<li><a onclick="delete_document(' . $masterID . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        $dropdownHTML = '
            <div class="btn-group style="display: flex;justify-content: center;"">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    Actions <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                    ' . $dropdownItems . '
                </ul>
            </div>';

        return $dropdownHTML;
    }
}

if (!function_exists('salary_advance_approval_action')) {
    function salary_advance_approval_action($masterID, $approvalLevelID, $docCode, $appYN, $type)
    {
        $status = ($type=='edit')?'<span class="pull-right">':'';
        if ($appYN == 1) {
            $str = ($type=='edit')?'<span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span>':$docCode;
            $status .= '<a onclick="load_approvalView(' . $masterID . ',' . $approvalLevelID . ',' . $appYN . ')">';
            $status .= $str.'</a> &nbsp; &nbsp;';
        }else{
            $str = ($type=='edit')?'<span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span>':$docCode;
            $status .= '<a onclick="load_approvalView(' . $masterID . ',' . $approvalLevelID . ',' . $appYN . ')">';
            $status .= $str.'</a> &nbsp; &nbsp;';
        }
        $status .= ($type=='code')?'</span>':'';

        return $status;
    }
}

if (!function_exists('leave_encashment_action')) {
    function leave_encashment_action($masterID, $document_type, $confirmYN, $approvedYN, $createdUserID, $cnEmpID, $documentCode, $pvID)
    {

        $status = ' <span class="pull-right">';

        if($approvedYN == 1 && $pvID > 0){
            $status .= '<a onclick="documentPageView_modal(\'PV\', ' . $pvID . ')"><span title="Payment Voucher" rel="tooltip"><i class="fa fa-file"></i></span> &nbsp; | &nbsp; ';
        }

        if ($confirmYN != 1) {
            $status .= '<a onclick="load_details(' . $masterID . ', '.$document_type.')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';
        }else{
            $status .= '<a target="_blank" onclick="view_modal(' . $masterID . ', '.$document_type.')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        }

        $status .= '&nbsp; | &nbsp; <span title="Print" rel="tooltip" class="glyphicon glyphicon-print" onclick="print_document('.$masterID.', \''.$documentCode.'\')" style="color:#3c8dbc"></span>';
        if (($createdUserID == current_userID() or $cnEmpID == current_userID())  and $approvedYN == 0 and $confirmYN == 1) {
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="referBack_document(' . $masterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span></a>';
        }

        if ($confirmYN != 1) {
            $status .= '&nbsp; | &nbsp; <a onclick="delete_document(' . $masterID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash delete-icon"></span>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('fetch_emp_asset_category_drop')) {
    function fetch_emp_asset_category_drop()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select('id,assetType')->from('srp_erp_pay_assettype')->where('companyID', current_companyID());
        $data = $CI->db->order_by('assetType')->get()->result_array();

        $data_arr = ['' => $CI->lang->line('common_select_a_option')];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['assetType'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_emp_asset_condition_drop')) {
    function fetch_emp_asset_condition_drop()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select('id,description')->from('srp_erp_pay_assetcondition');
        $data = $CI->db->order_by('description')->get()->result_array();

        $data_arr = ['' => $CI->lang->line('common_select_a_option')];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('my_employee_drop')) {
    function my_employee_drop()
    {
        $CI =& get_instance();
        $company_id = current_companyID();
        $emp_id = current_userID();

        $arr = $CI->db->query("SELECT EIdNo AS emp_id, ECode, empTB.Ename2 AS emp_name
                        FROM srp_employeesdetails AS empTB
                        JOIN (
                           SELECT managerID, empID FROM srp_erp_employeemanagers
                           WHERE active = 1 AND managerID = {$emp_id} AND companyID = {$company_id}
                        ) AS man_tb ON man_tb.empID = empTB.EIdNo
                        WHERE empTB.Erp_companyID = {$company_id} AND isDischarged = 0")->result_array();
        return $arr;
    }
}

if (!function_exists('my_assognee_drop')) {
    function my_assognee_drop()
    {
        $CI =& get_instance();
        $company_id = current_companyID();
        $emp_id = current_userID();

        $arr = $CI->db->query("SELECT EIdNo AS emp_id, ECode, empTB.Ename2 AS emp_name
                        FROM srp_employeesdetails AS empTB
                        JOIN (
                           SELECT attendeeID, empID FROM srp_erp_employee_attendees
                           WHERE  empID = {$emp_id} AND companyID = {$company_id}
                        ) AS man_tb ON man_tb.attendeeID = empTB.EIdNo
                        WHERE empTB.Erp_companyID = {$company_id} AND isDischarged = 0")->result_array();
        return $arr;
    }
}

if (!function_exists('tibian_employeeType')) {
    function tibian_employeeType()
    {
        $CI =& get_instance();
        $CI->db->select("id,CONCAT(prefix, ' - ', description) AS description");
        $data = $CI->db->from('srp_erp_tibian_employeetype')->get()->result_array();

        $data_arr = array('' => 'Select type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('hr_letter_types')) {
    function hr_letter_types()
    {
        $CI =& get_instance();
        $CI->db->select("id, letter_type");
        $data = $CI->db->from('srp_erp_hr_letters')->get()->result_array();

        $data_arr = array('' => 'Select type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['letter_type'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('hr_letter_request_action')) {
    function hr_letter_request_action($masterID, $confirmYN, $approvedYN, $createdUserID, $cnEmpID, $documentCode)
    {

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $masterID . ',"Document Request","HDR",'.$confirmYN.');\'><span title="Attachment" rel="tooltip" ';
        $status .= 'class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        $status .= '<a target="_blank" onclick="view_modal(' . $masterID . ')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        if ($confirmYN != 1) {
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="load_details(' . $masterID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';
        }

        // if($approvedYN == 1){
            // $status .= '&nbsp; | &nbsp; <span title="Print" rel="tooltip" class="glyphicon glyphicon-print" onclick="print_document('.$masterID.', \''.$documentCode.'\')" style="color:#3c8dbc"></span>';
        // }

        if (($createdUserID == current_userID() or $cnEmpID == current_userID())  and $approvedYN == 0 and $confirmYN == 1) {
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="referBack_document(' . $masterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:#d15b47;"></span></a>';
        }

        if ($confirmYN != 1) {
            $status .= '&nbsp; | &nbsp; <a onclick="delete_document(' . $masterID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash delete-icon"></span>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('payee_emp_type_drop')) {
    function payee_emp_type_drop()
    {
        $data = get_instance()->db->query("SELECT * FROM srp_erp_payee_emptype")->result_array();
        $data_arr = array('' => 'Select type');
        if (!empty($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('visa_supplier_drop')) {
    function visa_supplier_drop($status = null)
    {
        $CI = &get_instance();
        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => 'Select Visa Compnay');
        } else {
            $supplier_arr = [];
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('visa_supplier_type_drop')) {
    function visa_supplier_type_drop($status = null)
    {
        $CI = &get_instance();
        if($status){
            $visaType  = array('1'=>'Own Company','2'=>'3rd Party Visa');
        }else{
            $visaType  = array(''=>'Select Visa Type','1'=>'Own Company','2'=>'3rd Party Visa');
        }
       
        return $visaType;
    }
}


if (!function_exists('sponser_drop')) {
    function sponser_drop()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $data =  $CI->db->query("SELECT sponsorID, sponsorName
                                 FROM srp_erp_sponsormaster                               
                                 WHERE companyID = {$companyID}")->result_array();


        $data_arr = ['' => 'Select Sponsor'];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['sponsorID'] ?? '')] = trim($row['sponsorName'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('hrPeriod_action')) {
    function hrPeriod_action($masterID, $description, $periodType)
    {
        $action = '<div style="text-align: center">';
        $action .= '<a onclick="load_periodDet('.$masterID.', \''.$description.'\', \''.$periodType.'\')"  title="View" rel="tooltip"> <i class="fa fa-eye"></i></a>';
        $action .= '&nbsp; | &nbsp; <a onclick="generate_next_hrPeriod('.$masterID.')" title="Next Period" rel="tooltip">';
        $action .= '<i class="fa fa-level-up" style="color:#d15b47;" ></i></a>';
        $action .= '&nbsp; | &nbsp; <a onclick="setup_access_group('.$masterID.', \''.$description.'\')" title="Assign Group" rel="tooltip">';
        $action .= '<i class="fa fa-plus-square-o" ></i></a>';

        /*$action .= '&nbsp; | &nbsp; <a onclick="delete_hr_periodMaster('.$masterID.')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;" ></span></a>';*/
        $action .= '</div>';

        return $action;
    }
}

if (!function_exists('makeTimeTextBox_2')) {
    function makeTimeTextBox_2($logId, $h = null, $d = true)
    {
        $hours = str_pad($h['h'], 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($h['m'], 2, '0', STR_PAD_LEFT);
        $disabled = ($d)? 'disabled': '';

        $txt = '<div class="" style="width: 55px">';
        $txt .= '<div class="input-group">';
        $txt .= '<span class="input-group-btn">';
        $txt .= '<input onchange="updateTotalDuration(this,\'' . $logId . '\')"  type="text" name="h_' . $logId . '" class="trInputs input_disabled timeBox txtH number " ';
        $txt .= 'style="width: 25px" value="' . $hours . '" onkeyup="hoursValidate(this)" id="h_' . $logId . '"  ' . $disabled . ' >';
        $txt .= '</span>';
        $txt .= '<span style="font-size: 14px; font-weight: bolder"> : </span>';
        $txt .= '<span class="input-group-btn">';
        $txt .= '<input onchange="updateTotalDuration(this,\'' . $logId . '\')"  type="text" name="m_' . $logId . '" class="trInputs input_disabled timeBox txtM number" ';
        $txt .= 'style="width: 25px" value="' . $minutes . '"  onkeyup="minutesValidate(this)" onchange="minutesValidateChange(this)" id="m_' . $logId . '" ' . $disabled . ' >';
        $txt .= '</span>';
        $txt .= '</div>';
        $txt .= '</div>';

        return $txt;
    }
}

if (!function_exists('remove_blank_values')) {
    function remove_blank_values($data)
    {
        return (trim($data) != '');
    }
}

if (!function_exists('calculate_tot_time')) {
    function calculate_tot_time($inputs)
    {

        /*$inputs =  [ '16:08:47' ,'17:16:13',
            '17:27:56', '22:41:27',
            '22:44:33', '23:09:01',
            '23:09:10', '07:02:35',
            '07:05:47'
        ];*/

        $length =  round( (count($inputs) / 2) );
        $i = 0;
        while($length > $i ){
            $n = ($i > 0)? ($i*2): $i;

            $output = array_slice($inputs, $n, 2);
            echo '<pre>';print_r($output); echo '</pre>';
            $i++;
        }
        return 0;
    }
}

if (!function_exists('attendance_summary_action')) {
    function attendance_summary_action($id, $count)
    {
        $action = '<a onclick="load_attendance_review('.$id.')" title="View" rel="tooltip"><i class="fa fa-eye"></i>';
        if ($count == 0) {
            $action .= '&nbsp | &nbsp;<a onclick="delete_attendance_review(' . $id . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        }
        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('request_letter_drop')) {
    function request_letter_drop()
    {

        $data = get_instance()->db->select('EIdNo, CONCAT_WS(\' - \', ECode, Ename2, DesDescription) AS empName ', false)
            ->from('srp_erp_signaturelist AS sList')
            ->join('srp_employeesdetails AS empTB', 'empTB.EIdNo=sList.empID')
            ->join('srp_designation AS desTB', 'empTB.EmpDesignationId=desTB.DesignationID')
            ->where('sList.companyID', current_companyID())->get()->result_array();

        $data_arr = array('' => 'Select signature');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['empName'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('attMappingMaster_drop')) {
    function attMappingMaster_drop()
    {
        $db2 = get_instance()->load->database('db2', TRUE);
        $data = $db2->select('machine_typeID, machine_name', false)
            ->from('attendance_machine_master')->get()->result_array();

        $data_arr = array('' => 'Select type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['machine_typeID'] ?? '')] = trim($row['machine_name'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('getMachineType')) {
    function getMachineType($id)
    {
        $db2 = get_instance()->load->database('db2', TRUE);
        $data = $db2->get_where('attendance_machine_master', ['machine_typeID'=>$id])->row('machine_name');
        return $data;
    }
}

if(!function_exists('machineConf_action')){
    function machineConf_action($id, $deviceID, $machineType){
        $usage = attendance_machine_usage($deviceID);

        $str = '<div class="pull-right">';
        if(empty($usage)){
            $edit_para = "{$id}, {$deviceID}, {$machineType}";
            $str .= '<a onclick="edit_config('.$edit_para . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            $str .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_config(' . $id . ', '.$deviceID.')">';
            $str .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
        }
        $str .= '</div>';

        return $str;
    }
}

if(!function_exists('attendance_machine_usage')){
    function attendance_machine_usage($deviceID){
        $companyID = current_companyID();

        $usage = get_instance()->db->query("SELECT autoID FROM srp_erp_pay_empattendancetemptable 
                          WHERE companyID={$companyID} AND device_id={$deviceID} LIMIT 1")->row('autoID');
        return $usage;
    }
}

if (!function_exists('isPrimary_department')) {
    function isPrimary_department($id, $status)
    {
        $checked = ($status == 1) ? 'checked' : '';
        $isDisable = ($status == 1) ? 'disabled' : '';
        $str = '<input type="checkbox" class="switch-chk" id="depPr_' . $id . '" onchange="primaryDepartment(this, ' . $id . ', ' . $checked . ')"';
        $str .= 'data-size="mini" data-on-text="Yes" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="No" data-label-width="0" ' . $checked . ' '.$isDisable.'>';
        return  $str;
    }
}

if (!function_exists('edit_split_salary')) {
    function edit_split_salary($splitSalaryMasterID, $ConfirmedYN, $approved, $isDeleted, $createdUserID, $confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('hrms_payroll', $primaryLanguage);
        $splitSalary = $CI->lang->line('hrms_payroll_split_salary');/*"Split Salary"*/
        $CI->load->library('session');

        $dropdownItems = '';

        $dropdownItems .= '<li><a onclick=\'attachment_modal(' . $splitSalaryMasterID . ',"'.$splitSalary.'","SS",' . $ConfirmedYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color:#4caf50;"></span> Attachment</a></li>';

        if ($isDeleted == 1) {
            $dropdownItems .= '<li><a onclick="reOpen_splitSalary(' . $splitSalaryMasterID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) && $approved == 0 && $ConfirmedYN == 1 && $isDeleted == 0) {
            $dropdownItems .= '<li><a onclick="referback_splitSalary(' . $splitSalaryMasterID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        if ($ConfirmedYN != 1 && $isDeleted == 0) {
            $dropdownItems .= '<li><a onclick=\'fetchPage("system/hrm/edit_split_salary",' . $splitSalaryMasterID . ',"Split Salary", "SS");\'><span class="glyphicon glyphicon-pencil" style="color:#116f5e;"></span> Edit</a></li>';
            $dropdownItems .= '<li><a onclick="documentPageView_modal(\'SS\',\'' . $splitSalaryMasterID . '\')"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';
            $dropdownItems .= '<li><a href="' . site_url('Employee/load_splitSalary_conformation/') . '/' . $splitSalaryMasterID . '" target="_blank"><span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';
            $dropdownItems .= '<li><a onclick="delete_split_salary(' . $splitSalaryMasterID . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($ConfirmedYN == 1 && $isDeleted == 0) {
            $dropdownItems .= '<li><a onclick="documentPageView_modal(\'SS\',\'' . $splitSalaryMasterID . '\')"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';
            $dropdownItems .= '<li><a href="' . site_url('Employee/load_splitSalary_conformation/') . '/' . $splitSalaryMasterID . '" target="_blank"><span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';
        }

        $dropdownHTML = '
            <div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                    ' . $dropdownItems . '
                </ul>
            </div>';

        return $dropdownHTML;
    }
}

if (!function_exists('edit_split_salary_details')) {
    function edit_split_salary_details($splitSalaryID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';


        $status .= '<a onclick=\'edit_split_salary_details(' . $splitSalaryID . ')\'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        $status .= '<a onclick="delete_split_salary_details(' . $splitSalaryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('splitSalary_action_approval')) { /*get po action list*/
    function splitSalary_action_approval($splitSalaryMasterID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PurchaseOrder1 = $CI->lang->line('common_purchase_order');/*"Purchase Order"*/
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $splitSalaryMasterID . ',"'.$PurchaseOrder1.'","SS");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $splitSalaryMasterID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'SS\',\'' . $splitSalaryMasterID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('employee_list_by_currency')) {
    function employee_list_by_currency($currency_filter=null)
    {
        $CI =& get_instance();

        $CI->db->select('EIdNo, ECode, Ename2');
        $CI->db->from('srp_employeesdetails empTB');
        $CI->db->join('srp_erp_segment', 'srp_erp_segment.segmentID=empTB.segmentID');
        $CI->db->where('Erp_companyID', current_companyID());
        $CI->db->where('isDischarged ', 0);
        $CI->db->where('isPayrollEmployee ', 1);

        if(!empty($currency_filter)){
            $CI->db->where('empTB.payCurrencyID', $currency_filter);
        }
        $data = $CI->db->get()->result_array();

        $data_arr = [];
        if (isset($data)) {
            $data_arr[''] = 'Select Employee';
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') .' - '.trim($row['Ename2'] ?? '');

            }
        }
        return $data_arr;
    }
}


// start: leave salary provision configuration

if (!function_exists('salary_provition_delete_action')) {
    function salary_provition_delete_action($Id)
    {
       
            //$action = '<a onclick="edit_model('. $Id .')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            //&nbsp;
            $action  = '<a onclick="delete_salary_provision_config(' . $Id . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
    

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('salary_categories_for_salaryProvision')) {
    function salary_categories_for_salaryProvision($id = FALSE, $state = TRUE)
    {
        $CI =& get_instance();
        $comId = current_companyID();

        $CI->db->select('salaryDescription, salaryCategoryID, salaryCategoryType')
                ->from('srp_erp_pay_salarycategories')
                ->where('companyID',$comId);
        $query = $CI->db->get()->result_array();

        if ($state == TRUE) {
            $data_arr = array('' => $CI->lang->line('common_select_salary_category')/*'Select Salary Category'*/);
        } else {
            $data_arr = [];
        }

        if (isset($query)) {
            foreach ($query as $row) {
                $data_arr[trim($row['salaryCategoryID'] ?? '')] = trim($row['salaryCategoryType'] ?? '') . ' | ' . trim($row['salaryDescription'] ?? '');
            }

            return $data_arr;
        }
    }

}

if (!function_exists('get_provisioned_record')) {
    function get_provisioned_record()
    {
        $CI =& get_instance();
        $comId = current_companyID();

        $provision = $CI->db->select('*')
                ->from('srp_erp_leave_salary_provision')
                ->where('companyID',$comId)
                ->where('isProvision',1)
                ->get()->row_array();
       
        return $provision;

    }

}


// end: leave salary provision configuration


if (!function_exists('get_jv_master_record_details')) {
    function get_jv_master_record_details($jvMasterDetailID,$noReversal = null)
    {
        $CI =& get_instance();
        $CI->db->select("*");
        if($noReversal){
            $CI->db->where('isReversal !=',1);
        }
        $data = $CI->db->where('JVMasterAutoId',$jvMasterDetailID)->from('srp_erp_jvdetail')->get()->result_array();

        return $data;
    }
}


if (!function_exists('get_all_reporting_structures')) {
    function get_all_reporting_structures($id = FALSE, $state = TRUE)
    {
        $CI =& get_instance();
        $comId = current_companyID();

        $data = $CI->db->select('*')
                ->from('srp_erp_reporting_structure_master')
                ->where('captureHRYN',1)
                ->get()->result_array();
        //echo '<pre>';print_r($data);exit;
        return $data;

    }

}


if (!function_exists('load_personal_application_action')) {
    function load_personal_application_action($paa_ID, $paaConfirmedYN, $approved, $createdUserID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PersonalAction =  'Personal Action';
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $paa_ID . ',"'.$PersonalAction.'","PAA",' . $paaConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $paaConfirmedYN == 1) {
            $status .= '<a onclick="referback_PAA(' . $paa_ID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if ($paaConfirmedYN != 1) {
            //$status .= '<a onclick="edit_model(\'PAA\',\'' . $paa_ID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'fetchPage("system/hrm/personal_application_new",' . $paa_ID . ',"Edit Personal Action","PAA"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PAA\',\'' . $paa_ID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" href="' . site_url('Employee/load_personal_action_conformation/') . '/' . $paa_ID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="delete_item(' . $paa_ID . ',\'Personal Action\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        // if($addedForPayment == 0 && $addedToSalary == 0 && $approved == 1){
        //     $status .= '<a onclick="reviseClaim(' . $paa_ID . ');"><span title="Revise" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';

        // }
        if ($paaConfirmedYN == 1 ) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PAA\',\'' . $paa_ID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            $status .= '<a target="_blank" href="' . site_url('Employee/load_personal_action_conformation/') . '/' . $paa_ID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('confirm_approval_personal_application')) {
    function confirm_approval_personal_application($approved_status, $confirmed_status, $code, $autoID)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 ) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($confirmed_status == 3) {
                $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else if ($confirmed_status == 2) {
                $status .= '<a onclick="fetch_approval_user_modal_pa(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            } else {
                $status .= '<a onclick="fetch_approval_user_modal_pa(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            }
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a onclick="fetch_approval_user_modal_pa(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }
        } elseif ($approved_status == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($approved_status == 5) {
            $status .= '<span class="label label-info">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('all_designation_drop')) {
    function all_designation_drop()
    {
        $CI = &get_instance();
        $CI->db->select("DesignationID,DesDescription");
        $CI->db->from('srp_designation');
        $CI->db->where('isDeleted', 0);
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $designations = $CI->db->get()->result_array();
        
        if ($designations) {
            $designations_arr = array('' => 'Select Designation');
            foreach ($designations as $row) {
                $designations_arr[trim($row['DesignationID'] ?? '')] = trim($row['DesDescription'] ?? '');
            }
        }else
        {
            return null;
        }

        return $designations_arr;
    }
}

if (!function_exists('employee_grade_drop')) {
    function employee_grade_drop()
    {
        $CI = &get_instance();
        $CI->db->select("gradeID, gradeDescription");
        $CI->db->from('srp_erp_employeegrade');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $grades = $CI->db->get()->result_array();
        
        if ($grades) {
            $grades_arr = array('' => 'Select Grade');
            foreach ($grades as $row) {
                $grades_arr[trim($row['gradeID'] ?? '')] = trim($row['gradeDescription'] ?? '');
            }
        }else
        {
            return null;
        }

        return $grades_arr;
    }
}

if (!function_exists('transferType')) {
    function transferType()
    {
        $CI = &get_instance();
        $CI->db->select("id, typeDescription");
        $CI->db->from('srp_erp_personal_action_selections');
        $CI->db->where('typeCode', 'type');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $grades = $CI->db->get()->result_array();
        
        if ($grades) {
            $transfer_type_arr = array('' => 'Select Type');
            foreach ($grades as $row) {
                $transfer_type_arr[trim($row['id'] ?? '')] = trim($row['typeDescription'] ?? '');
            }
        }else
        {
            return null;
        }

        return $transfer_type_arr;
    }
}

if (!function_exists('transferTerm')) {
    function transferTerm()
    {
        $CI = &get_instance();
        $CI->db->select("id, typeDescription");
        $CI->db->from('srp_erp_personal_action_selections');
        $CI->db->where('typeCode', 'term');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $grades = $CI->db->get()->result_array();
        
        if ($grades) {
            $transfer_term_arr = array('' => 'Select Term');
            foreach ($grades as $row) {
                $transfer_term_arr[trim($row['id'] ?? '')] = trim($row['typeDescription'] ?? '');
            }
        }else
        {
            return null;
        }

        return $transfer_term_arr;
    }
}

if (!function_exists('all_departments_drom')) {
    function all_departments_drom()
    {
        $CI = &get_instance();
        $CI->db->select("srp_empdepartments.DepartmentMasterID as DepartmentMasterID, DepartmentDes");
        $CI->db->from('srp_empdepartments');
        $CI->db->join('srp_departmentmaster', 'srp_empdepartments.DepartmentMasterID = srp_departmentmaster.DepartmentMasterID', 'left');
        $CI->db->where('srp_empdepartments.Erp_companyID', $CI->common_data['company_data']['company_id']);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $department_arr = array('' => 'Select department');
            foreach ($query->result_array() as $row) {
                $department_arr[trim($row['DepartmentMasterID'] ?? '')] = trim($row['DepartmentDes'] ?? '');
            }
            return $department_arr;
        } else {
            return null;
        }
    }
}


if (!function_exists('all_managers_drom')) {
    function all_managers_drom()
    {
        $CI = &get_instance();
        $CI->db->select("EIdNo, Ename2");
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_erp_employeemanagers', 'srp_employeesdetails.EIdNo = srp_erp_employeemanagers.managerID', 'left');
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $grades_arr = array('' => 'Select manager');
            foreach ($query->result_array() as $row) {
                $grades_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
            return $grades_arr;
        } else {
            return null;
        }
    }
}


if (!function_exists('all_location_drom')) {
    function all_location_drom()
    {
        $CI = &get_instance();
        $CI->db->select("locationID, locationName");
        $CI->db->from('srp_erp_location');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $locations_arr = array('' => 'Select location');
            foreach ($query->result_array() as $row) {
                $locations_arr[trim($row['locationID'] ?? '')] = trim($row['locationName'] ?? '');
            }
            return $locations_arr;
        } else {
            return null;
        }
    }
}
if (!function_exists('all_leaveGroup_drop')) {
    function all_leaveGroup_drop()
    {
        $CI = &get_instance();
        $CI->db->select("srp_employeesdetails.leaveGroupID as leaveGroupID, description");
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_erp_leavegroup', 'srp_employeesdetails.leaveGroupID = srp_erp_leavegroup.leaveGroupID', 'left');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $locations_arr = array('' => 'Select Leave Group');
            foreach ($query->result_array() as $row) {
                $locations_arr[trim($row['leaveGroupID'] ?? '')] = trim($row['description'] ?? '');
            }
            return $locations_arr;
        } else {
            return null;
        }
    }
}


if (!function_exists('confirm_aproval_paa')) {
    function confirm_aproval_paa($approved_status, $confirmed_status, $code, $autoID)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 ) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($confirmed_status == 3) {
                $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else if ($confirmed_status == 2) {
                $status .= '<a onclick="fetch_approval_user_modal_pa(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            } else {
                $status .= '<a onclick="fetch_approval_user_modal_pa(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            }
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a onclick="fetch_approval_user_modal_pa(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }
        } elseif ($approved_status == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($approved_status == 5) {
            $status .= '<span class="label label-info">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';
        return $status;
    }
}


if (!function_exists('load_PAA_approval_action')) { 
    function load_PAA_approval_action($id, $Level, $approved, $ApprovedID,$approval=1)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
      
        $purchaseRequest = "Personal Application";

        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $id . ',"'.$purchaseRequest.'","PAA");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $id . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        }else{
            //$status .= '<a target="_blank" href="' . site_url('Employee/load_personal_action_conformation/') . '/' . $id . '" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PAA\',\'' . $id . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
       
        $status .= '</span>';
        return $status;
    }
}



if (!function_exists('all_group_drop_PAA')) {
    function all_group_drop_PAA()
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_reporting_structure_details.id as id, srp_erp_reporting_structure_details.detail_code as code, srp_erp_reporting_structure_details.detail_description as descrpition");
        $CI->db->from('srp_erp_reporting_structure_details');
        $CI->db->join('srp_erp_reporting_structure_master ', 'srp_erp_reporting_structure_details.structureMasterID = srp_erp_reporting_structure_master.id');
        $CI->db->where('srp_erp_reporting_structure_master.systemTypeID', 10);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $group_arr = array('' => 'Select Leave Group');
            foreach ($query->result_array() as $row) {
                $group_arr[trim($row['id'] ?? '')] = trim($row['code'] ?? '') . ' | ' . trim($row['descrpition'] ?? '');
            }
            return $group_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('all_segment_arr_PAA')) {
    function all_segment_arr_PAA()
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_reporting_structure_details.id as id, srp_erp_reporting_structure_details.detail_code as code, srp_erp_reporting_structure_details.detail_description as descrpition");
        $CI->db->from('srp_erp_reporting_structure_details');
        $CI->db->join('srp_erp_reporting_structure_master ', 'srp_erp_reporting_structure_details.structureMasterID = srp_erp_reporting_structure_master.id');
        $CI->db->where('srp_erp_reporting_structure_master.systemTypeID', 1);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $segment_arr = array('' => 'Select Segment');
            foreach ($query->result_array() as $row) {
                $segment_arr[trim($row['id'] ?? '')] = trim($row['code'] ?? '') . ' | ' . trim($row['descrpition'] ?? '');
            }
            return $segment_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('all_sub_segment_arr_PAA')) {
    function all_sub_segment_arr_PAA()
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_reporting_structure_details.id as id, srp_erp_reporting_structure_details.detail_code as code, srp_erp_reporting_structure_details.detail_description as descrpition");
        $CI->db->from('srp_erp_reporting_structure_details');
        $CI->db->join('srp_erp_reporting_structure_master ', 'srp_erp_reporting_structure_details.structureMasterID = srp_erp_reporting_structure_master.id');
        $CI->db->where('srp_erp_reporting_structure_master.systemTypeID', 3);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $sub_segment_arr = array('' => 'Select Sub Segment');
            foreach ($query->result_array() as $row) {
                $sub_segment_arr[trim($row['id'] ?? '')] = trim($row['code'] ?? '') . ' | ' . trim($row['descrpition'] ?? '');
            }
            return $sub_segment_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('all_division_drop_PAA')) {
    function all_division_drop_PAA()
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_reporting_structure_details.id as id, srp_erp_reporting_structure_details.detail_code as code, srp_erp_reporting_structure_details.detail_description as descrpition");
        $CI->db->from('srp_erp_reporting_structure_details');
        $CI->db->join('srp_erp_reporting_structure_master ', 'srp_erp_reporting_structure_details.structureMasterID = srp_erp_reporting_structure_master.id');
        $CI->db->where('srp_erp_reporting_structure_master.systemTypeID', 2);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $division_arr = array('' => 'Select Division');
            foreach ($query->result_array() as $row) {
                $division_arr[trim($row['id'] ?? '')] = trim($row['code'] ?? '') . ' | ' . trim($row['descrpition'] ?? '');
            }
            return $division_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('all_company_drom')) {
    function all_company_drom()
    {
        $CI = &get_instance();
        $CI->db->select("company_id, company_code, company_name");
        $CI->db->from('srp_erp_company');
        //$CI->db->where('', '');
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $company_arr = array('' => 'Select Company');
            foreach ($query->result_array() as $row) {
                $company_arr[trim($row['company_id'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
            return $company_arr;
        } else {
            return null;
        }
    }
}

/**used in :  Employee master -> Employeement tab */
if (!function_exists('get_activity_codes')) {
    function get_activity_codes()
    {
        $CI = &get_instance();
        $comId = current_companyID();
        
        $CI->db->select("id, activity_code");
        $CI->db->from('srp_erp_activity_code_main');
        $CI->db->where('is_active', 1);
        $CI->db->where('company_id', $comId);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $activityCode_arr = array('' => 'Select Activity Code');
            foreach ($query->result_array() as $row) {
                $activityCode_arr[trim($row['id'] ?? '')] = trim($row['activity_code'] ?? '');
            }
            return $activityCode_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('all_employeeDrop_for_wfh')) {
    function all_employeeDrop_for_wfh()
    {
        $CI = &get_instance();
        $comId = current_companyID();
        
        $CI->db->select("EIdNo, ECode, Ename2");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', $comId);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $employeeDrop = array('' => 'Select Employee');
            foreach ($query->result_array() as $row) {
                $employeeDrop[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
            }
            return $employeeDrop;
        } else {
            return null;
        }
    }
}

if (!function_exists('employee_designation_for_wfh')) {
    function employee_designation_for_wfh()
    {
        $CI = &get_instance();
        $comId = current_companyID();
        $empID = current_userID();
        
        $CI->db->select("EmpDesignationId, srp_designation.DesDescription as DesDescription");
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID','left');
        $CI->db->where('srp_employeesdetails.Erp_companyID', $comId);
   
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $designations_arr = array('' => 'Select Designation');
            foreach ($query->result_array() as $row) {
                $designations_arr[trim($row['EmpDesignationId'] ?? '')] = trim($row['DesDescription'] ?? '');
            }
            return $designations_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_employee_designation_for_wfh')) {
    function get_employee_designation_for_wfh()
    {
        $CI = &get_instance();
        $comId = current_companyID();
        $empID = current_userID();
        
        $CI->db->select("EmpDesignationId");
        $CI->db->from('srp_employeesdetails');
        // $CI->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID','left');
        $CI->db->where('srp_employeesdetails.Erp_companyID', $comId);
        $CI->db->where('srp_employeesdetails.EIdNo', $empID);
   
        $designation = $CI->db->get()->row('EmpDesignationId');

        return $designation;

        
    }
}

/** start : almansoori chnges for personal application */

if (!function_exists('load_personal_action_types')) {
    function load_personal_action_types($is_common = null)
    {
        $CI = &get_instance();
        
        $CI->db->select("documentCategoryID, categoryDescription");
        $CI->db->from('srp_erp_system_document_categories');
        $CI->db->where('documentID', 'PAA');
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $employeeDrop = array('' => 'Select action type');
            foreach ($query->result_array() as $row) 
            {   
                if($is_common == 1 && $row['documentCategoryID'] <= 4)
                {   
                    $employeeDrop[trim($row['documentCategoryID'] ?? '')] = trim($row['categoryDescription'] ?? '');
                }else if($is_common == 0 && $row['documentCategoryID'] != 3)
                { 
                    $employeeDrop[trim($row['documentCategoryID'] ?? '')] = trim($row['categoryDescription'] ?? '');
                }
            }

            return $employeeDrop;

        } else {
            return null;
        }
    }
}

if (!function_exists('load_personal_application_action_mse')) {
    function load_personal_application_action_mse($paa_ID, $paaConfirmedYN, $approved, $createdUserID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PersonalAction =  'Personal Action';
        $CI->load->library('session');

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $paa_ID . ',"'.$PersonalAction.'","PAA",' . $paaConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        
        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $paaConfirmedYN == 1) {
            $status .= '<a onclick="referback_PAA_mse(' . $paa_ID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if ($paaConfirmedYN != 1) {
            //$status .= '<a onclick="edit_model(\'PAA\',\'' . $paa_ID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'fetchPage("system/hrm/personal_application_mse_new",' . $paa_ID . ',"Edit Personal Action / Payroll Authorization Form","PAA"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PAA\',\'' . $paa_ID . '\',\'' . $paaConfirmedYN . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" href="' . site_url('Employee/load_personal_action_conformation_mse/') . '/' . $paa_ID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="delete_item_mse(' . $paa_ID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        if ($paaConfirmedYN == 1 ) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PAA\',\'' . $paa_ID . '\',\'' . $paaConfirmedYN . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            $status .= '<a target="_blank" href="' . site_url('Employee/load_personal_action_conformation_mse/') . '/' . $paa_ID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('confirm_approval_personal_application_mse')) {
    function confirm_approval_personal_application_mse($approved_status, $confirmed_status, $code, $autoID, $typeId)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 ) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($confirmed_status == 3) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($confirmed_status == 2) {
                $status .= '<a onclick="fetch_approval_reject_user_modal_mse(' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else if ($confirmed_status == 2) {
                $status .= '<a onclick="fetch_approval_user_modal_pa_mse(\'' . $code . '\',' . $autoID . ','. $typeId .')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            } else {
                $status .= '<a onclick="fetch_approval_user_modal_pa_mse(\'' . $code . '\',' . $autoID . ','. $typeId .')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            }
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a onclick="fetch_approval_user_modal_pa_mse(\'' . $code . '\',' . $autoID . ','. $typeId .')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }
        } elseif ($approved_status == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($approved_status == 5) {
            $status .= '<span class="label label-info">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('confirm_user_approval_drilldown_mse')) {
    function confirm_user_approval_drilldown_mse($con, $code, $autoID)
    {
        $status = '<center>';
        if ($con == 0) {
            $status .= '<span class="label label-danger">Not Confirmed</span>';
        } elseif ($con == 1) {
            $status .= '<span class="label label-success">Confirmed</span>';
        } elseif ($con == 2) {
            $status .= '<span class="label label-warning">Rejected</span>';
        } elseif ($con == 3) {
            $status .= '<span class="label label-warning">Refered back</span>';
            /*            $status .= '<a onclick="approval_refer_back_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-warning"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';*/
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

// if (!function_exists('confirm_aproval_paa_mse')) {
//     function confirm_aproval_paa_mse($approved_status, $confirmed_status, $code, $autoID, $typeID)
//     {
//         $status = '<center>';
//         if ($approved_status == 0) {
//             if ($confirmed_status == 0 ) {
//                 $status .= '<span class="label label-danger">&nbsp;</span>';
//             } else if ($confirmed_status == 3) {
//                 $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
//             } else if ($confirmed_status == 2) {
//                 $status .= '<a onclick="fetch_approval_user_modal_pa_mse(\'' . $code . '\',' . $autoID . ','. $typeID .')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
//             } else {
//                 $status .= '<a onclick="fetch_approval_user_modal_pa_mse(\'' . $code . '\',' . $autoID . ','. $typeID .')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
//             }
//         } elseif ($approved_status == 1) {
//             if ($confirmed_status == 1) {
//                 $status .= '<a onclick="fetch_approval_user_modal_pa_mse(\'' . $code . '\',' . $autoID . ','. $typeID .')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
//             } else {
//                 $status .= '<span class="label label-success">&nbsp;</span>';
//             }
//         } elseif ($approved_status == 2) {
//             $status .= '<span class="label label-warning">&nbsp;</span>';
//         } elseif ($approved_status == 5) {
//             $status .= '<span class="label label-info">&nbsp;</span>';
//         } else {
//             $status .= '-';
//         }
//         $status .= '</center>';
//         return $status;
//     }
// }

if (!function_exists('load_PAA_approval_action_mse')) { 
    function load_PAA_approval_action_mse($id, $Level, $approved, $ApprovedID,$approval=1)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
      
        $purchaseRequest = "Personal Application";

        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $id . ',"'.$purchaseRequest.'","PAA");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $id . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PAA\', \'' . $id . '\', ' . $Level . ', \'' . $approved . '\');">';
            $status .= '<span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;| &nbsp;&nbsp;';

            $status .= '<a target="_blank" href="' . site_url('Employee/load_personal_action_conformation_mse/') . '/' . $id . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        }
       
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('get_document_auto_approval_mse')) {
    function get_document_auto_approval_mse($documentCode, $action_typeID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("levelNo");
        $CI->db->FROM('srp_erp_approvalusers');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('documentID', $documentCode);
        $CI->db->where('typeID', $action_typeID);
        $data = $CI->db->get()->row_array();
        if (!empty($data) && $data['levelNo'] == 0) {
            return 0;
        } elseif ($data['levelNo'] > 0) {
            return 1;
        } else {
            return 2;
        }
    }
}
/** end : almansoori chnges for personal application */


/**start : monthly allowance claim */
if (!function_exists('monthlyAdditionClaim_DeclarationsAction')) {
    function monthlyAdditionClaim_DeclarationsAction($id, $code, $confirmedYN = 0,$approvedYN=0, $isProcess = 0,$typeID = 0)
    {
        $edit = '';
        $delete = '';
        $view = '';
        $referBack = '';
        
        $t = 'MAC';
        $isNonPayroll = 1;

        $fetch = "fetchPage('system/hrm/monthly_allowance_claim_edit',".$id." ,'HRMS','', ".$isNonPayroll.")";
        $print = '<a target="_blank" href="' . site_url('Employee/monthly_allowance_print') . '/' . $t . '/' . $id . '/' . $code . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($confirmedYN != 1 && $approvedYN != 1) {
            $code = "'" . $code . "'";
            $edit = '<a onclick="' . $fetch . '"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;|&nbsp;';
            $delete = '<a onclick="delete_details(' . $id . ' , ' . $code . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>&nbsp;|&nbsp;';
            $view = '<a onclick="documentPageView_modal(\'MAC\',' . $id . ')"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>&nbsp;|&nbsp;';
        } elseif ($confirmedYN == 1) {
            $view = '<a onclick="documentPageView_modal(\'MAC\',' . $id . ')"><span title="View" rel="tooltip" class="fa fa-fw fa-eye"></span></a>&nbsp;|&nbsp;';
        }

        if ($isProcess == 0 && $confirmedYN == 1 && $approvedYN != 1) {
            $referBack = '<a onclick="referBackConformation(' . $id . ')"><span style="color:#d15b47;" title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"></span></a>&nbsp;|&nbsp;';
        }

        return '<span class="pull-right">' . $referBack . '' . $edit . '' . $delete . '' . $view . '' . $print . ' </span>';
    }
}

if (!function_exists('is_Payroll_Processed_For_Emp_Group')) {
    function is_Payroll_Processed_For_Emp_Group($empID, $payYear, $payMonth, $isNonPayroll, $months=null)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $headerDetailTB =  'srp_erp_payrollheaderdetails';
        $payrollMaster = 'srp_erp_payrollmaster';

        $processed_dates = array();
        if($months){
            foreach($months as $month){
                $lastDate = $CI->db->query("SELECT CONCAT(ECode,' - ', Ename2) AS empData
                    FROM {$payrollMaster} AS masterTB
                    JOIN {$headerDetailTB} AS detailTB ON detailTB.payrollMasterID = masterTB.payrollMasterID
                    AND EmpID IN ({$empID}) AND detailTB.companyID={$companyID}
                    WHERE masterTB.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$month}")->row_array();

                if($lastDate){
                    $processed_dates[] = $month;
                }
            }
        }
        //print_r($processed_dates);exit;
        // $lastDate = $CI->db->query("SELECT CONCAT(ECode,' - ', Ename2) AS empData
        //                              FROM {$payrollMaster} AS masterTB
        //                              JOIN {$headerDetailTB} AS detailTB ON detailTB.payrollMasterID = masterTB.payrollMasterID
        //                              AND EmpID IN ({$empID}) AND detailTB.companyID={$companyID}
        //                              WHERE masterTB.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}")->result_array();
        return $processed_dates;
    }
}


if (!function_exists('document_approval_drilldown_allowance_claim')) {
    function document_approval_drilldown_allowance_claim($approved_status, $confirmed_status, $code, $autoID)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 || $confirmed_status==null) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($confirmed_status == 2) {
                $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else if ($confirmed_status == 3) {
                //$status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                $status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            } else if($confirmed_status == 1){
                $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            }
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }
        } elseif ($approved_status == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($approved_status == 5) {
            $status .= '<span class="label label-info">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('load_MAC_approval_action')) {
    function load_MAC_approval_action($masterID, $approved, $level, $documentApprovedID, $isFromCancel = 0)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->lang->load('hrms_approvals', $primaryLanguage);
        $CI->load->library('session');

        $status = '<span class="pull-right">';

        $allowanceClaim = 'Monthly Allowance';
        $status .= '<a onclick="attachment_modal(' . $masterID . ', \'' . $allowanceClaim . '\', \'MAC\');">';
        $status .= '<span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if ($approved == 0) {
            $status .= '<a onclick="fetch_approval(\'' . $masterID . '\', \'' . $documentApprovedID . '\', ' . $level . ');">';
            $status .= '<span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'MAC\', \'' . $masterID . '\', ' . $level . ', \'' . $approved . '\');">';
            $status .= '<span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';

        return $status;
    }
}

/**end : monthly allowance claim */


if (!function_exists('action_airport_destination')) {
    function action_airport_destination($countryID, $destinationID, $cityID, $City)
    {
        //$CountryDes = "'" . $CountryDes . "'";
        $action = '';
        
            // $action .= '<a onclick="openCityModel(' . $countryID . ',\'' . $cityName . '\')">';
            // $action .= '<span title="Add City" rel="tooltip" class="glyphicon glyphicon-cog" style="color:blue;"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;';
            $action .= '<a onclick="deleteAirportDestination(' . $destinationID . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;';

        return '<span class="pull-right">' . $action . '</span>';

    }
}


if (!function_exists('leaveTypes_filter_drop')) {
    function leaveTypes_filter_drop()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
    
        $CI->db->SELECT("t1.leaveTypeID, t1.description");
        $CI->db->FROM('srp_erp_leavetype t1');
        $CI->db->where('companyID', $companyID);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $leaveType_filter_arr = array();
            foreach ($query->result_array() as $row) {
                $leaveType_filter_arr[trim($row['leaveTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
            return $leaveType_filter_arr;
        } else {
            return null;
        }
    }
}

/**HRMS - Grade */
if (!function_exists('load_allowances')) {
    function load_allowances()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
    
        $CI->db->SELECT("t1.monthlyDeclarationID as monthlyDeclarationID, t1.monthlyDeclaration as monthlyDeclaration");
        $CI->db->FROM('srp_erp_pay_monthlydeclarationstypes t1');
        $CI->db->where('companyID', $companyID);
        $CI->db->where('employeeClaimYN', 1);
        $query = $CI->db->get()->result_array();
        
        if ($query) {
            return $query;
        } else {
            return null;
        }
    }
}

/**personal action : almansoori */
if (!function_exists('load_allowances_for_personalAction_mse')) {
    function load_allowances_for_personalAction_mse()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $CI->db->SELECT("salaryCategoryID, salaryDescription");
        $CI->db->FROM('srp_erp_pay_salarycategories t1');
        $CI->db->where('companyID', $companyID);
        $CI->db->where('salaryCategoryType', 'A');
        $CI->db->where('salaryDescription !=', 'Basic Salary');
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $allowance_arr = array('' => 'select allowance');
            foreach ($query->result_array() as $row) {
                $allowance_arr[trim($row['salaryCategoryID'] ?? '')] = trim($row['salaryDescription'] ?? '');
            }
            return $allowance_arr;
        } else {
            return null;
        }
    }
}


if (!function_exists('load_currency_deop')) {
    function load_currency_deop()
    {
        $CI =& get_instance();

        $CI->db->SELECT("currencyID, CONCAT(CurrencyCode,' | ', CurrencyName) as currency");
        $CI->db->FROM('srp_erp_currencymaster');
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $currency_arr = array('' => 'select currency');
            foreach ($query->result_array() as $row) {
                $currency_arr[trim($row['currencyID'] ?? '')] = trim($row['currency'] ?? '');
            }
            return $currency_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('load_leavegroup_drop')) {
    function load_leavegroup_drop()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $CI->db->SELECT("leaveGroupID, description");
        $CI->db->FROM('srp_erp_leavegroup');
        $CI->db->WHERE('companyID',$companyID);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $leavegroup_arr = array('' => 'select Schedule');
            foreach ($query->result_array() as $row) {
                $leavegroup_arr[trim($row['leaveGroupID'] ?? '')] = trim($row['description'] ?? '');
            }
            return $leavegroup_arr;
        } else {
            return null;
        }
    }
}

if (!function_exists('load_department_arr')) {
    function load_department_arr()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $CI->db->SELECT("srp_departmentmaster.DepartmentMasterID as DepartmentMasterID, srp_departmentmaster.DepartmentDes as DepartmentDes");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->JOIN('srp_empdepartments','srp_employeesdetails.EIdNo = srp_empdepartments.EmpID');
        $CI->db->JOIN('srp_departmentmaster','srp_empdepartments.DepartmentMasterID = srp_departmentmaster.DepartmentMasterID','left');
        $CI->db->WHERE('srp_employeesdetails.Erp_companyID',$companyID);
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $department_arr = array('' => 'select Division');
            foreach ($query->result_array() as $row) {
                $department_arr[trim($row['DepartmentMasterID'] ?? '')] = trim($row['DepartmentDes'] ?? '');
            }
            return $department_arr;
        } else {
            return null;
        }
    }
}

/** added for bug fix - personal action email */
if (!function_exists('send_personal_action_approvalEmail')) {
    function send_personal_action_approvalEmail($mailData, $attachment = 0, $path = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $manual_email = array();
        $base_arr = array();

        $manual_email['companyID'] = $companyID;
        $manual_email['empName'] = isset($mailData['param']['empName']) ? $mailData['param']['empName'] : 'Unknown Employee';
        $manual_email['documentSystemCode'] = isset($mailData['documentSystemCode']) ? $mailData['documentSystemCode'] : '';
        $manual_email['documentCode'] = isset($mailData['documentCode']) ? $mailData['documentCode'] : '';
        $manual_email['documentID'] = isset($mailData['documentID']) ? $mailData['documentID'] : '';
        $manual_email['emailSubject'] = isset($mailData['emailSubject']) ? $mailData['emailSubject'] : 'No Subject';
        $manual_email['empEmail'] = isset($mailData['empEmail']) ? $mailData['empEmail'] : '';
        $manual_email["type"] = isset($mailData['type']) ? $mailData['type'] : 'unknown';
        $manual_email['emailBody'] = isset($mailData['param']['body']) ? $mailData['param']['body'] : 'No body content';
        $manual_email['empID'] = isset($mailData['empID']) ? $mailData['empID'] : '';

        $base_arr[] = $manual_email;

        $CI->load->library('email_manual');
        $res = $CI->email_manual->set_email_detail($base_arr);

        return true; 
    }
}