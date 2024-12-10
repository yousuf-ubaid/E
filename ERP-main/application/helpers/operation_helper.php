<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('check_contract_status')) { /*get op Contract Status*/
    function check_contract_status($contractStatus)
    {
        $status = '<center>';

            if ($contractStatus == 3) {
                $status .= '<span class="label label-danger">Expired</span>';
            } else if ($contractStatus == 1) {
                $status .= '<span class="label label-success">Active</span>';
            } elseif ($contractStatus == 2) {
                $status .= '<span class="label label-warning">Pending</span>';
            } else {
                $status .= '-';
            }

        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('check_contract_approved_status_master')) { /*get op Contract approval*/
    function check_contract_approved_status_master($approvedYN, $code, $autoID)
    {
        $status = '<center>';

        if ($approvedYN == 0) {
            $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        } elseif ($approvedYN == 1) {
            $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('check_ticket_approved_status_master')) { /*get op Contract approval*/
    function check_ticket_approved_status_master($approvedYN, $code, $autoID)
    {
        $status = '<center>';

        if ($approvedYN == 0) {
            $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        } elseif ($approvedYN == 1) {
            $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('check_contract_Detail_Type')) { /*get op Contract approval*/
    function check_contract_Detail_Type($TypeID)
    {
        $status = '<center>';

            if ($TypeID == 1) {
                $status .= '<span>Product</span>';
            }  else {
                $status .= '<span>Service</span>';
            }

        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('load_opcontract_action')) { /*get po action list*/
    function load_opcontract_action($contractUID,$productGLCode,$serviceGLCode,$approvedYN,$createdUserID,$confirmedByEmpID,$confirmedYN)
    {

        $CI =& get_instance();
        $CI->db->select('contractUID');
        $CI->db->from('contractdetails');
        $CI->db->where('contractUID', $contractUID);
        $data = $CI->db->get()->row_array();

        $CI =& get_instance();
        $CI->db->select('contractUID');
        $CI->db->from('contractmaster');
        $CI->db->where('contractUID', $contractUID);
        $CI->db->where('confirmedYN', 1);
        $appdata = $CI->db->get()->row_array();


        $status = '<span class="pull-right">';
        if($approvedYN==1){
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'OPCNT\',\'' . $contractUID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'openOPDetail("' . $contractUID . '","' . $productGLCode . '","' . $serviceGLCode . '"); \' title="Contract Details"><i class="fa fa-columns" style="color:orange"></i></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'openOPcalloff("' . $contractUID . '","' . $productGLCode . '","' . $serviceGLCode . '"); \' title="Call Off"><i class="fa fa-external-link" style="color:green"></i></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'fetchPage("system/operations/job_ticket_master",' . $contractUID . ',"View Jobs"); \' title="View Jobs"><i class="fa fa-ticket"></i></a>';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'OPCNT\',\'' . $contractUID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'openEditContract("' . $contractUID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'openOPDetail("' . $contractUID . '","' . $productGLCode . '","' . $serviceGLCode . '"); \' title="Contract Details"><i class="fa fa-columns" style="color:orange"></i></a>';
        }


        if(!empty($data) && empty($appdata)){
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick=\'confirmOpContract("' . $contractUID . '"); \' title="Confirm"><i class="fa fa-check"></i></a>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approvedYN == 0 and $confirmedYN == 1) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="referback_op_contract(' . $contractUID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_opcontract_detail_action')) { /*get po action list*/
    function load_opcontract_detail_action($ContractDetailID,$contractUID)
    {

        $CI =& get_instance();
        $CI->db->select('contractUID');
        $CI->db->from('contractmaster');
        $CI->db->where('contractUID', $contractUID);
        $CI->db->where('confirmedYN', 1);
        $data = $CI->db->get()->row_array();

        $status = '<span class="pull-right">';
        if(!empty($data)){
            //$status .= '<a onclick=\'openEditContractDetail("' . $ContractDetailID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        }else{
            $status .= '<a onclick=\'openEditContractDetail("' . $ContractDetailID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'deleteContractDetail("' . $ContractDetailID . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>';
        }



        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('contract_action_approval')) { /*get po action list*/
    function contract_action_approval($contractUID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a onclick=\'approveContract("' . $contractUID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'OPCNT\',\'' . $contractUID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_calloff_action')) { /*get po action list*/
    function load_calloff_action($calloffID,$contractUID)
    {

        $CI =& get_instance();
        /*$CI->db->select('contractUID');
        $CI->db->from('contractmaster');
        $CI->db->where('contractUID', $contractUID);
        $CI->db->where('confirmedYN', 1);
        $data = $CI->db->get()->row_array();*/

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'openEditcalloff("' . $calloffID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        $status .= '<a onclick=\'deleteCallOff("' . $calloffID . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_progressbar_calloff')) { /*get po action list*/
    function load_progressbar_calloff($calloffID,$percentage)
    {

        $CI =& get_instance();
        /*$CI->db->select('contractUID');
        $CI->db->from('contractmaster');
        $CI->db->where('contractUID', $contractUID);
        $CI->db->where('confirmedYN', 1);
        $data = $CI->db->get()->row_array();*/
        $percent=round($percentage, 2);
        $status = '<div class="progress">' . $percent . '%
  <div class="progress-bar" role="progressbar" style="width: ' . $percent . '%" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100"></div>
</div>';
        return $status;
    }
}

if (!function_exists('get_contract_detail')) { /*get po action list*/
    function get_contract_detail($contractUID)
    {

        $CI =& get_instance();
        $convertFormat = convert_date_format_sql();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->select('contractUID,contractmaster.clientID as clientID,ContractNumber,srp_erp_segment.description as Department,DATE_FORMAT(ContStartDate,\'' . $convertFormat . '\') AS ContStartDate,DATE_FORMAT(ContEndDate,\'' . $convertFormat . '\') AS ContEndDate,contracttype.description as conType,approvedYN,contractStatus,productGLCode,serviceGLCode,srp_erp_customermaster.customerName as customerName,srp_erp_customermaster.customerSystemCode as customerSystemCode,srp_erp_customermaster.customerCountry as customerCountry,contractmaster.ServiceLineCode as ServiceLineCode');
        $CI->db->from('contractmaster');
        $CI->db->where('contractUID', $contractUID);
        $CI->db->where('contractmaster.CompanyID', $companyID);
        $CI->db->join('srp_erp_segment','srp_erp_segment.segmentID = contractmaster.ServiceLineCode');
        $CI->db->join('contracttype','contracttype.contractTypeId = contractmaster.contractType');
        $CI->db->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = contractmaster.clientID');

        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('get_call_off_for_jobticket')) { /*get po action list*/
    function get_call_off_for_jobticket($contractUID,$status = TRUE)
    {

        $CI =& get_instance();
        $convertFormat = convert_date_format_sql();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->select('calloffID,description');
        $CI->db->from('op_calloff_master');
        $CI->db->where('contractUID', $contractUID);
        $CI->db->where('companyID', $companyID);
        $CI->db->where('isHold', 0);
        $data = $CI->db->get()->result_array();

        if ($status) {
            $calloff_arr = array('' => 'Select Call off');
        } else {
            $calloff_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $calloff_arr[trim($row['calloffID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $calloff_arr;
    }
}

if (!function_exists('total_prod_servc_tkt')) { /*get po action list*/
    function total_prod_servc_tkt($ticketidAtuto,$contractUID)
    {

        $CI =& get_instance();
        $convertFormat = convert_date_format_sql();
        $companyID = $CI->common_data['company_data']['company_id'];

        $totalP = $CI->db->query("SELECT IFNULL(sum(TotalCharges),0) as TotalCharge FROM product_service_details WHERE companyID='{$companyID}' AND ticketidAtuto = '{$ticketidAtuto}' AND contractUID = '{$contractUID}'  ")->row_array();
        $decimal = $CI->db->query("SELECT srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.DecimalPlaces FROM contractmaster LEFT JOIN srp_erp_currencymaster ON contractmaster.ContCurrencyID=srp_erp_currencymaster.currencyID  WHERE CompanyID='{$companyID}' AND contractUID = '{$contractUID}' ")->row_array();
        $CurCode=$decimal['CurrencyCode'];
        $Curdec=$decimal['DecimalPlaces'];
        $total   = $totalP['TotalCharge'];
        return $CurCode . ' ' . number_format($total, $Curdec);
    }
}

if (!function_exists('job_ticket_status_tkt')) { /*get po action list*/
    function job_ticket_status_tkt($ticketidAtuto,$contractUID,$ticketStatus)
    {

        $CI =& get_instance();

        $status = '<span class="pull-right">';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('job_ticket_action')) { /*get po action list*/
    function job_ticket_action($ticketidAtuto,$contractUID,$ticketStatus,$approvedYN,$confirmedYN,$operationLog,$callOffBase,$createdUserID,$confirmedByEmpID,$proformaConfirmationYN)
    {

        $CI =& get_instance();

        $CI =& get_instance();
        $CI->db->select('TicketproductID');
        $CI->db->from('product_service_details');
        $CI->db->where('ticketidAtuto', $ticketidAtuto);
        $data = $CI->db->get()->row_array();


        $status = '<span class="pull-right">';



        if(!empty($data) && $confirmedYN!=1){
            $status .= '<a onclick=\'openEditjobticket("' . $ticketidAtuto . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'OPJOB\',\'' . $ticketidAtuto . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'deletejobticket("' . $ticketidAtuto . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'addCrew("' . $ticketidAtuto . '"); \'><span title="Add Crew" rel="tooltip" class="glyphicon glyphicon-user"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'addOpUnit("' . $ticketidAtuto . '"); \'><span title="Add Asset Unit" rel="tooltip" class="glyphicon glyphicon-list"></span></a>';
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick=\'confirmOpJobTicket("' . $ticketidAtuto . '","' . $contractUID . '"); \' title="Confirm"><i class="fa fa-check"></i></a>';
        }else if($confirmedYN==1){
            $status .= '<a onclick=\'openEditjobticket("' . $ticketidAtuto . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'OPJOB\',\'' . $ticketidAtuto . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
           // $status .= '<a onclick=\'deletejobticket("' . $ticketidAtuto . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'addCrew("' . $ticketidAtuto . '"); \'><span title="Add Crew" rel="tooltip" class="glyphicon glyphicon-user"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'addOpUnit("' . $ticketidAtuto . '"); \'><span title="Add Asset Unit" rel="tooltip" class="glyphicon glyphicon-list"></span></a>';
            //$status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick=\'confirmOpJobTicket("' . $ticketidAtuto . '","' . $contractUID . '"); \' title="Confirm"><i class="fa fa-check"></i></a>';
        }else{
            $status .= '<a onclick=\'openEditjobticket("' . $ticketidAtuto . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'OPJOB\',\'' . $ticketidAtuto . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick=\'deletejobticket("' . $ticketidAtuto . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'addCrew("' . $ticketidAtuto . '"); \'><span title="Add Crew" rel="tooltip" class="glyphicon glyphicon-user"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick=\'addOpUnit("' . $ticketidAtuto . '"); \'><span title="Add Asset Unit" rel="tooltip" class="glyphicon glyphicon-list"></span></a>';
            //$status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick=\'confirmOpJobTicket("' . $ticketidAtuto . '","' . $contractUID . '"); \' title="Confirm"><i class="fa fa-check"></i></a>';
        }


        if($operationLog==1 && $callOffBase==1){
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick=\'open_operation_log("' . $ticketidAtuto . '","' . $contractUID . '"); \'><span title="Operation System Log" rel="tooltip" class="glyphicon glyphicon-file"></span></a>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approvedYN == 0 and $confirmedYN == 1) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="referbackjob(' . $ticketidAtuto . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if($approvedYN == 1 && $proformaConfirmationYN!=1){
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmProformaInvoice(' . $ticketidAtuto . ');" title="Confirm Proforma Invoice" rel="tooltip"><i class="fa fa-check"></i></a>';
        }

        if($proformaConfirmationYN==1){
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('Operation/load_proforma_invoices_print') . '/' . $ticketidAtuto . '"><span title="" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('job_eng_drop')) { /*get po action list*/
    function job_eng_drop()
    {

        $CI =& get_instance();
        $convertFormat = convert_date_format_sql();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->select('EIdNo,Ename2');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', $companyID);
        $CI->db->where('isDischarged !=', 1);
        $CI->db->where('isActive', 1);
        $data = $CI->db->get()->result_array();

        if (isset($data)) {
            foreach ($data as $row) {
                $emp_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }

        return $emp_arr;
    }
}

if (!function_exists('service_action_add')) { /*get po action list*/
    function service_action_add($ContractDetailID)
    {

        $CI =& get_instance();

        $status = '<span class="pull-right">';
        $status .= '<a class="btn btn-xs btn-success" onclick=\'addServiceProduct("' . $ContractDetailID . '",2); \'><i class="fa fa-plus"></i></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('product_action_add')) { /*get po action list*/
    function product_action_add($ContractDetailID)
    {

        $CI =& get_instance();

        $status = '<span class="pull-right">';
        $status .= '<a class="btn btn-xs btn-success" onclick=\'addServiceProduct("' . $ContractDetailID . '",1); \'><i class="fa fa-plus"></i></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('service_action_lastYN')) { /*get po action list*/
    function service_action_lastYN($ContractDetailID)
    {

        $CI =& get_instance();

        $status = '<span class="pull-right">';
        $status .= '<input type="checkbox" name="lastYN" id="lastYNProd_'.$ContractDetailID.'">';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('op_action_crew')) { /*get po action list*/
    function op_action_crew($ticketCrewID)
    {

        $CI =& get_instance();

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'deleteOpCrew("' . $ticketCrewID . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_employee_drop_tkt')) {
    function load_employee_drop_tkt($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("EIdNo,Ename2,EmpSecondaryCode");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->WHERE('empConfirmedYN', 1);
        $CI->db->WHERE('isDischarged', 0);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Crew');
        } else {
            $data_arr = []; //array('' => '');
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '') . ' - ' . trim($row['EmpSecondaryCode'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_asset_unit_drop_tkt')) {
    function load_asset_unit_drop_tkt($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("faID,assetDescription,faCode");
        $CI->db->FROM('srp_erp_fa_asset_master');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->WHERE('approvedYN', 1);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Asset');
        } else {
            $data_arr = []; //array('' => '');
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['faID'] ?? '')] = trim($row['faCode'] ?? '') . ' - ' . trim($row['assetDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('op_action_asset_unit')) { /*get po action list*/
    function op_action_asset_unit($unitMoreID)
    {

        $CI =& get_instance();

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'deleteOpAssetUnit("' . $unitMoreID . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('ticket_action_approval')) { /*get po action list*/
    function ticket_action_approval($ticketidAtuto, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a onclick=\'approveJob("' . $ticketidAtuto . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'OPJOB\',\'' . $ticketidAtuto . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('all_customer_drop_frm_contract')) { /*get po action list*/
    function all_customer_drop_frm_contract()
    {

        $CI =& get_instance();
        $convertFormat = convert_date_format_sql();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->select('contractmaster.clientID as id,srp_erp_customermaster.customerName as customerName');
        $CI->db->from('contractmaster');
        $CI->db->where('contractmaster.CompanyID', $companyID);
        $CI->db->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = contractmaster.clientID');
        $CI->db->group_by("contractmaster.clientID");
        $data = $CI->db->get()->result_array();
        if (isset($data)) {
            foreach ($data as $row) {
                $emp_arr[trim($row['id'] ?? '')] = trim($row['customerName'] ?? '');
            }
        }

        return $emp_arr;
    }
}


if (!function_exists('op_action_crew_group')) { /*get po action list*/
    function op_action_crew_group($groupID,$groupName)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'openEditcrewgroup("' . $groupID . '","' . $groupName . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick=\'deleteOpCrewGroup("' . $groupID . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('op_action_crew_member')) { /*get po action list*/
    function op_action_crew_member($crewmemberID)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'deleteOpCrewMember("' . $crewmemberID . '"); \'><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71)"></span></a>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('actionsupervisoryn')) {
    function actionsupervisoryn($crewmemberID,$supervisorYN)
    {

        $chek='';
        if($supervisorYN==1){
            $chek='checked';
        }

        $output = '<div style="text-align: center;">';
        $output .= '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="supervisorYN_'.$crewmemberID.'" type="checkbox" class="columnSelected supervisor" '.$chek.'  value="'.$crewmemberID.'" ><label for="checkbox">&nbsp;</label> </div></div></div>';
        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('load_employee_crew_grp_tkt')) {
    function load_employee_crew_grp_tkt($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("groupID,groupName");
        $CI->db->FROM('crewgroup');
        $CI->db->WHERE('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Crew Group');
        } else {
            $data_arr = []; //array('' => '');
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['groupID'] ?? '')] = trim($row['groupName'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('call_off_action_dilldown')) { /*get po action list*/
    function call_off_action_dilldown($calloffID,$description)
    {

        $CI =& get_instance();

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'calloffDD(' . $calloffID . '); \'>' . $description . '</a>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('op_action_checklist')) { /*get checklist action list*/
    function op_action_checklist($id,$companyID)
    {
        $status = '<div class="text-center">';
        $status .= '<a onclick=\'openChecklistTemp("' . $id . '","' . $companyID . '"); \'><i title="View" rel="tooltip" class="fa fa-eye"></i></a>';
        $status .= '</div>';
        return $status;
    }
}


if (!function_exists('op_checklist_active')) { /*get checklist action list*/
    function op_checklist_active($id)
    {
        $CI =& get_instance();
        $convertFormat = convert_date_format_sql();
        $companyID = $CI->common_data['company_data']['company_id'];

        $current_status =  $CI->db->query("SELECT status FROM srp_erp_op_checklist_master WHERE id='$id' AND companyID = '$companyID' ")->row()->status;
        if($current_status == '1'){
            $chk ="checked";
        }else{
            $chk ="unchecked";
        }

        $status = '<div class="text-center">';
        $status .= '<div class="form-group">
                        <div class="checkbox checbox-switch switch-success">
                            <label>
                                <input type="checkbox" name="" onchange="updateChecklistActive('.trim($id).')" '.$chk.' />
                                <span></span>
                            </label>
                        </div>
                    </div>';
        $status .= '</div>';
        return $status;
    }
}



