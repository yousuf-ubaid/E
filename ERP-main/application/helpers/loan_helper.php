<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if(!function_exists('convertIntType')){
    function convertIntType($type){

        if($type == 1){
            return 'Interest Based';
        }else{
            return 'Interest Free';
        }
    }
}

if(!function_exists('intPercentage')){
    function intPercentage($type, $per){

        if ($type == 'Interest Based') {
            return $per.' %';
        }
        else if ($type == 'Interest Free') {
            return '-';
        }
        else{
            return $per.'|'.$type;
        }
    }
}

if(!function_exists('createLoanEditView')){
    function createLoanEditView($loanID, $des, $isIntBased, $intPer, $GLCode, $isSalaryAdvance){

        $editValues = $loanID.",'".$des."',".$isIntBased.",".$intPer.",".$GLCode.",".$isSalaryAdvance;
        $delVal = $loanID.",'".$des."'";
        $editFun = 'onclick="editCat( '.$editValues.' )"';
        $delFun = 'onclick="delete_cat( '.$delVal.' )"';

        return '<spsn class="pull-right"><a '.$editFun.' ><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;
               | &nbsp;&nbsp;<a '.$delFun.' ><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';

    }
}

if(!function_exists('load_loan_types')){
    function load_loan_types(){

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("loanID , description")
            ->from("srp_erp_pay_loan_category")
            ->where('companyID', current_companyID());

        $types =  $CI->db->get()->result_array();

        $types_arr = array('' => $CI->lang->line('common_select_type')/*'Select Type'*/);
        if (isset($types)) {
            foreach ($types as $row) {
                $types_arr[trim($row['loanID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $types_arr;

    }
}

/* Function added */
if(!function_exists('load_loan_types_arr')){
    function load_loan_types_arr(){

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("loanID , description")
            ->from("srp_erp_pay_loan_category")
            ->where('companyID', current_companyID());

        $types =  $CI->db->get()->result_array();

       $types_arr = array('' => $CI->lang->line('common_select_type')/*'Select Type'*/);
        if (isset($types)) {
            foreach ($types as $row) {
                $types_arr[trim($row['loanID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $types_arr;

    }
}
/* End  Function */

if(!function_exists('convertLoanAmount')){
    function convertLoanAmount($amount, $dPlace=2){
        return  '<div align="right">'.format_number($amount, $dPlace).'</div>';
    }
}

if (!function_exists('load_Employee_load_action')) {
    function load_Employee_load_action($id, $confirmedYN, $approvedYN, $loanCode)
    {
        $CI = &get_instance();
        
        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';
                       
        if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
            $action .= '<li><a onclick="emp_loan_details(\'edit\', ' . $id . ')">
                            <span class="glyphicon glyphicon-pencil" style="color:#116f5e"></span> Edit</a>
                        </li>
                        <li><a onclick="emp_loan_delete(' . $id . ', \'' . $loanCode . '\')">
                            <span class="glyphicon glyphicon-trash" style="color:#d15b47"></span> Delete</a>
                        </li>';
        }

        if ($confirmedYN == 1) {
            $action .= '<li><a onclick="emp_loan_details(\'view\', ' . $id . ')">
                            <span class="fa fa-fw fa-eye" style="color:#03a9f4"></span> View</a>
                        </li>';
        }

        if ($confirmedYN == 1 && $approvedYN == 0) {
            $action .= '<li><a onclick="referBackConformation(' . $id . ', \'' . $loanCode . '\')">
                            <span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a>
                        </li>';
        }

        $action .= '<li><a target="_blank" href="' . site_url('loan/loan_print/') . '/' . $id . '/' . $loanCode . '">
                        <span class="glyphicon glyphicon-print"></span> Print</a>
                    </li>
                    <li><a target="_blank" href="' . site_url('loan/loan_request_print/') . '/' . $id . '/' . $loanCode . '">
                        <span class="glyphicon glyphicon-print"></span> Loan Request Print</a>
                    </li>';

        $action .= '</ul></div>';

        return $action;
    }
}

if(!function_exists('loanViewAction')){
    function loanViewAction($id, $confirmedYN, $approvedYN, $loanCode){
        $fType1 = "'view'";
        $fType2 = "'edit'";
        $edit = ''; $delete = ''; $view = ''; $referBack = '';
        $print = '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="'.site_url('loan/loan_print/').'/'.$id.'/'.$loanCode.'" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print glyphicon-print-btn"></span></a>';
        $print2 = '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="'.site_url('loan/loan_request_print/').'/'.$id.'/'.$loanCode.'" ><span title="Loan Request Print" rel="tooltip" class="glyphicon glyphicon-print glyphicon-print-btn"></span></a>';
        $loanCode = "'".$loanCode."'";

        if( $confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3){
            $edit   = '<a onclick="emp_loan_details('.$fType2.', '.$id.')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"></span></a>';
            $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="emp_loan_delete('.$id.','.$loanCode.')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if( $confirmedYN == 1 ){
            $view   = '<a onclick="emp_loan_details('.$fType1.', '.$id.')"><i title="View" rel="tooltip" class="fa fa-fw fa-eye"></i></a>';
        }
        if( $confirmedYN == 1 && $approvedYN == 0 ){
            $referBack = '<a onclick="referBackConformation('.$id.','.$loanCode.')"><span style="color:rgb(209, 91, 71);" title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat glyphicon-repeat-btn"></span></a>&nbsp;&nbsp;|&nbsp; ';
        }

        return '<span class="pull-right">'.$referBack.''.$view.''.$edit.''.$print.''.$print2.''.$delete.'</span>';
    }
}

if(!function_exists('loanSkip')){
    function loanSkip($id, $isSettled, $skippedID, $scheduleDate, $intNo,$amount){
        $action = '';
        if($isSettled != 1 && $skippedID == 0 ){
            $action = '<div align="center"><input type="checkbox" name="skipID[]" class="skipCheckbox"  value="'.$id.'" data-date="'.$scheduleDate.'" data-ramount="'.$amount.'" data-intno="'.$intNo.'"/></div>';
        }
        return $action;
    }
}

if(!function_exists('loanStatus')){
    function loanStatus($isSettled, $skippedID, $skipDes){
        if( $skippedID != 0 && $skippedID != null && $skippedID != ''){
            return '<a data-toggle="tooltip" data-original-title="'.$skipDes.'" href="#">
                        <div align="center"> <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> </div>
                    </a>';
        }else{
            $status = ( $isSettled == 1 )? 'success' : 'danger';
            return '<div align="center"> <span class="label label-'.$status.'" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> </div>';
        }


    }
}

if(!function_exists('loanConformStatus')){
    function loanConformStatus($confirmedYN){
        $status = '';
        switch( $confirmedYN ){
            case 0:
                $status = 'danger';
            break;

            case 1:
                $status = 'success';
            break;

            case 2:
                $status = 'warning';
            break;
        }

        return '<div align="center"> <span class="label label-'.$status.'" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> </div>';

    }
}

if (!function_exists('loan_conform')) { /*get po action list*/
    function loan_conform($con,$m_id = null){
        $status ='<center>';
        if($m_id){
            if ($con==0) {
                $status .='<a href="#" onclick="procu('.$m_id.')"><span class="label label-danger">&nbsp;</span></a>';
            }elseif($con==1) {
                $status .='<a href="#" onclick="procu('.$m_id.')"><span class="label label-success">&nbsp;</span></a>';
            }elseif($con==2) {
                $status .='<a href="#" onclick="procu('.$m_id.')"><span class="label label-warning">&nbsp;</span></a>';
            }else{
                $status .='-';
            }
        }else{
            if ($con==0) {
                $status .='<span class="label label-danger">&nbsp;</span>';
            }elseif($con==1) {
                $status .='<span class="label label-success">&nbsp;</span>';
            }elseif($con==2) {
                $status .='<span class="label label-warning">&nbsp;</span>';
            }else{
                $status .='-';
            }
        }

        $status .='</center>';
        return $status;
    }
}

if (!function_exists('loan_action_approval')) { /*get po action list*/
    function loan_action_approval($loanID, $documentApprovedID, $approvalLevelID, $loanCode, $appYN){
        $status ='<span class="pull-right">';
        if ($loanID==0){
            //$status .='<a onclick='fetch_approval("'.$poID.'","'.$ApprovedID.'","'.$Level.'");'><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            //$status .='<a onclick="load_emp_loanDet('.$loanID.')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        if($appYN == 1){
            $status .='<a onclick="load_emp_loanDet('.$loanID.','.$documentApprovedID.','.$approvalLevelID.','.$appYN.')"><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }else{
            $status .='<a onclick="load_emp_loanDet('.$loanID.','.$documentApprovedID.','.$approvalLevelID.','.$appYN.')"><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }


        $status .='</span>';
        return $status;
    }
}

if (!function_exists('loanCatGL_drop')) {
    function loanCatGL_drop()
    {

        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->WHERE('isBank', 0);
        $CI->db->WHERE('isActive', 1);
        $CI->db->WHERE('approvedYN', 1);
        $CI->db->WHERE('masterCategory', 'BS');
        $CI->db->WHERE('accountCategoryTypeID !=4');
        $CI->db->WHERE('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        return $data;
    }
}