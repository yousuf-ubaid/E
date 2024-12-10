<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-06-16
 * Time: 2:25 PM
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_paysheet_template_action')) {
    function load_paysheet_template_action($id, $confirmedYN, $templateDescription)
    {
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($confirmedYN != 1) {
            $status .= '<li><a onclick="templateLoad(' . $id . ', 1)"><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            $status .= '<li><a onclick="templateDelete(' . $id . ')"><span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
        } else {
            $status .= '<li><a onclick="referBackConformation(' . $id . ')"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
            $status .= '<li><a onclick="templateLoad(' . $id . ', 0)"><i class="fa fa-fw fa-eye" style="color: #03a9f4;"></i> View</a></li>';
            $status .= '<li><a onclick="templateClone(' . $id . ', \'' . $templateDescription . '\')"><i class="fa fa-copy" style="color: #4caf50;"></i> Clone</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('paySheetTemplate_drop')) {
    function paySheetTemplate_drop($isNonPayroll)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('templateID, documentCode, templateDescription');
        $CI->db->from('srp_erp_pay_template');
        $CI->db->where('companyID', current_companyID());
        if( $isNonPayroll != null ){
            $CI->db->where('isNonPayroll', $isNonPayroll);
        }
        $CI->db->where('confirmedYN', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('hrms_payroll_please_select_a_template')/*'Select a Template'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['templateID'] ?? '')] = trim($row['documentCode'] ?? '') . ' - ' . trim($row['templateDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('payrollCalender')) {
    function payrollCalender($year, $method = 0, $isNonPayroll='N')
    {
        $CI =& get_instance();
        $CI->db->select('monthDetailID, monthNo, monthName')
            ->from('srp_erp_pay_monthmaster')
            ->join('srp_erp_pay_monthdetails', 'srp_erp_pay_monthdetails.monthMasterID=srp_erp_pay_monthmaster.monthMasterID')
            ->where('srp_erp_pay_monthmaster.monthMasterID', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array();

        /*$processTB = ($isNonPayroll == 'N')? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';

        $companyID = $CI->common_data['company_data']['company_id'];
        $processedMonth = $CI->db->query("SELECT payrollMonth FROM {$processTB} WHERE payrollYear={$year} AND companyID={$companyID}")->result_array();

        if (isset($data)) {
            if ($method == 0) {
                $data_arr = array('' => 'Select a Month');
                foreach ($data as $row) {
                    $currentArr = array('payrollMonth' => trim($row['monthNo'] ?? ''));
                    if (!in_array($currentArr, $processedMonth)) {
                        $data_arr[trim($row['monthNo'] ?? '')] = trim($row['monthNo'] ?? '') . ' - ' . trim($row['monthName'] ?? ''); //monthDetailID
                    }
                }
            } else if ($method == 1) {
                $data_arr = array('' => 'Select a Month');
                foreach ($data as $row) {
                    $data_arr[trim($row['monthNo'] ?? '')] = trim($row['monthNo'] ?? '') . ' - ' . trim($row['monthName'] ?? ''); //monthDetailID
                }
            } else if ($method == 2) {
                $data_arr[0] = array('monthNo' => '', 'monthDescription' => 'Select a Month');
                $i = 1;
                foreach ($data as $row) {
                    $currentArr = array('payrollMonth' => trim($row['monthNo'] ?? ''));
                    if (!in_array($currentArr, $processedMonth)) {
                        $data_arr[$i] = array('monthNo' => trim($row['monthNo'] ?? ''), 'monthDescription' => trim($row['monthNo'] ?? '') . ' - ' . trim($row['monthName'] ?? '')); //monthDetailID
                        $i++;
                    }
                }
            }
        }*/

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['monthNo'] ?? '')] = trim($row['monthNo'] ?? '') . ' - ' . trim($row['monthName'] ?? ''); //monthDetailID
            }
        }
        return $data_arr;
    }
}

if (!function_exists('isPayrollProcessed')) {
    function isPayrollProcessed($deductionDate, $isPayrollCategory=null )
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $tableName = ($isPayrollCategory == '2')?'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

        $CI->db->select(" ( STR_TO_DATE( CONCAT( payrollYear,'-',payrollMonth,'-','01'),'%Y-%m-%d' ) + INTERVAL 1 MONTH ) AS nextDate, payrollYear, payrollMonth, confirmedYN")
            ->from($tableName)
            ->where('companyID', $companyID)
            ->order_by('nextDate', 'DESC')->limit(1);
        $maxPayrollDate = $CI->db->get()->row_array();

        if ($maxPayrollDate['nextDate'] > $deductionDate) {
            $isPayrollProcessed = array(
                'status' => 'Y',
                'year' => $maxPayrollDate['payrollYear'],
                'month' => $maxPayrollDate['payrollMonth']
            );
        } else {
            $isPayrollProcessed = array('status' => 'N');
        }
        return $isPayrollProcessed;
    }
}

if (!function_exists('load_non_payroll_action')) {
    function load_non_payroll_action($id, $confirmedYN, $approvedYN, $year, $month, $isNonPayroll, $templateID)
    {
        $fType1 = "'view'";
        $fType2 = "'edit'";
        $det = "'" . payrollMonthInName($year, $month) . "'";
        $det2 = payrollMonthInName($year, $month);

        if ($templateID == null) {
            $templateID = getDefault_template($isNonPayroll);
        }

        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $action .= '<li>
                        <a href="#" onclick="payroll_details(' . $fType1 . ', ' . $id . ')">
                            <i class="fa fa-fw fa-eye" style="color: #03a9f4"></i> View
                        </a>
                    </li>';

        if ($confirmedYN != 1) {
            $action .= '<li>
                            <a href="#" onclick="payroll_details(' . $fType2 . ', ' . $id . ')">
                                <span class="glyphicon glyphicon-pencil" style="color: #116f5e"></span> Edit
                            </a>
                        </li>';
        }

        if ($confirmedYN == 1 && $approvedYN != 1) {
            $action .= '<li>
                            <a href="#" onclick="referBackConformation(' . $id . ', ' . $det . ')">
                                <span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back
                            </a>
                        </li>';
        }

        $action .= '<li>
                        <a href="' . site_url('Template_paysheet/paySheet_print/') . '/' . $id . '/' . $templateID . '/' . $isNonPayroll . '/' . $det2 . '" target="_blank">
                            <span class="glyphicon glyphicon-print" style="color: #607d8b"></span> Print
                        </a>
                    </li>';

        if ($confirmedYN != 1) {
            $action .= '<li>
                            <a href="#" onclick="payroll_delete(' . $id . ')">
                                <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" title="Delete" rel="tooltip"></span> Delete
                            </a>
                        </li>';
        }

        $action .= '</ul></div>';

        return $action;
    }
}

if (!function_exists('paySheetAction')) {
    function paySheetAction($id, $confirmedYN, $approvedYN, $year, $month, $isNonPayroll, $templateID)
    {
        $fType1 = "'view'";
        $fType2 = "'edit'";
        $det = "'" . payrollMonthInName($year, $month) . "'";
        $det2 = payrollMonthInName($year, $month);

        $dropdownItems = '';

        if ($confirmedYN != 1) {
            $dropdownItems .= '<li><a onclick="payroll_details(' . $fType2 . ', ' . $id . ')"><span class="glyphicon glyphicon-pencil" style="color:#116f5e;"></span> Edit</a></li>';
            $dropdownItems .= '<li><a onclick="payroll_delete(' . $id . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($confirmedYN == 1 && $approvedYN != 1) {
            $dropdownItems .= '<li><a onclick="referBackConformation(' . $id . ' ,' . $det . ')"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        $dropdownItems .= '<li><a onclick="payroll_details(' . $fType1 . ', ' . $id . ')"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';

        if ($templateID == null) {
            $templateID = getDefault_template($isNonPayroll);
        }

        $dropdownItems .= '<li><a target="_blank" href="' . site_url('Template_paysheet/paySheet_print/') . '/' . $id . '/' . $templateID . '/' . $isNonPayroll . '/' . $det2 . '">';
        $dropdownItems .= '<span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';

        $dropdownHTML = '
            <div class="btn-group" style="display: flex; justify-content: center;">
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

if (!function_exists('payrollMonthInName')) {
    function payrollMonthInName($year, $month)
    {
        $date = $year . '-' . $month . '-01';
        return date('Y - F', strtotime($date));
    }
}

if (!function_exists('actionBankProcess')) {
    function actionBankProcess($bankTransferID, $confirmedYN, $documentCode, $isNonPayroll, $bankTransferType,$notificationYN)
    {
        $view = '';
        $edit = '';
        $delete = '';
        $referBack = '';
        $docCode = "'" . $documentCode . "'";
        $companyID = current_companyID();
        $CI =& get_instance();
        $paymentVoucher = $CI->db->select("bankTransferID,payVoucherAutoId,PVcode")->from('srp_erp_paymentvouchermaster')->where('companyID', $companyID)
                            ->where('bankTransferID', $bankTransferID)->get()->result_array();

        if ($confirmedYN != 1) {
            $edit = '<a onclick="load_bankTransfer(' . $bankTransferID . ', 1 )" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>';
            $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_bankTransfer(' . $bankTransferID . ',' . $docCode . ')">';
            $delete .= '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        } else if ($confirmedYN == 1) {

            $view = '<a onclick="load_bankTransfer('.$bankTransferID.', 0 )" title="View" rel="tooltip"><i class="fa fa-fw fa-eye"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;';
            if(!empty($paymentVoucher)){
                if(count($paymentVoucher)>1){
                    $view .= '<a target="_blank" onclick="load_payment_voucher(' . $bankTransferID . ')" >';
                    $view .= '<i class="fa fa-file"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; ';

                }else{
                    $CI->db->select("bankTransferID,payVoucherAutoId,PVcode")->from('srp_erp_paymentvouchermaster')
                        ->where('companyID', $companyID)->where('bankTransferID', $bankTransferID);
                    $pvCode = $CI->db->get()->row('payVoucherAutoId');

                    $view .= ' <a target="_blank" style="cursor: pointer;" title="Payment Voucher" rel="tooltip" onclick="documentPageView_modal(\'PV\',' . $pvCode. ')" >';
                    $view .= '<i class="fa fa-file"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;';
                }
            }
            $view .= '<a target="_blank" href="' . site_url('Template_paysheet/bankTransferCoverLetter_print/') . '/' . $bankTransferID . '/' . $isNonPayroll;
            $view .= '/' . $documentCode . '" >';
            $view .= '<span class="glyphicon glyphicon-print" title="Cover letter" rel="tooltip"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;';

            if($bankTransferType == 'SLIP'){
                $view .= '<a target="_blank" href="' . site_url('Template_paysheet/bank_slip_text/'). $bankTransferID . '/' . $isNonPayroll.'" >';
                $view .= '<i class="fa fa-file-text" title="SLIP - TEXT" rel="tooltip"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;';
                $view .= '<a target="_blank" href="' . site_url('Template_paysheet/bank_slip_excel/') . $bankTransferID . '/' . $isNonPayroll.'" >';
                $view .= '<i class="fa fa-file-excel-o" title="SLIP - Excel" rel="tooltip"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;';
            }

            if($bankTransferType == 'SLF'){
                $view .= '<a target="_blank" href="' . site_url('Template_paysheet/bank_slip_text_slf/'). $bankTransferID . '/' . $isNonPayroll.'" >';
                $view .= '<i class="fa fa-file-text" title="SLF - TEXT" rel="tooltip"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;';
            }

            elseif (in_array($bankTransferType, ['WPS', 'WPS2','WPS_MOL'])){
                if($isNonPayroll != 'Y'){
                    $view .= '<i class="fa fa-file-excel-o" title="WPS" rel="tooltip" onclick="validate_wps('.$bankTransferID.')"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;';
                }
            }

            if($notificationYN!=1)
            {
                $view .= '<a target="_blank" onclick="sendemail_payroll(' . $bankTransferID . ',\''.$isNonPayroll.'\')" >';
                $view .= '<i class="fa fa-envelope" title="Send Payslip Notification" rel="tooltip"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; ';
            }


            $view .= '<a target="_blank" href="' . site_url('Template_paysheet/bankTransfer_print/') . '/' . $bankTransferID . '/'.$isNonPayroll.'/'. $documentCode . '" >';
            $view .= '<span class="glyphicon glyphicon-print" title="Bank Transfer" rel="tooltip"></span></a>  &nbsp;&nbsp; | &nbsp;&nbsp;';
            $view .= '<a onclick="excelsheet(\''.site_url('Template_paysheet/bankTransfer_excel_tab/') . '/' . $bankTransferID . '/'.$isNonPayroll.'\',\''.site_url('Template_paysheet/bankTransfer_excel_single/') . '/' . $bankTransferID . '/'.$isNonPayroll.'\')" href="#" >';
            $view .= '<span class="fa fa-file-excel-o" style="color: green" title="Bank Transfer Excel" rel="tooltip"></span></a>';

        }

        return '<span class="pull-right">' . $referBack . '' . $view . '' . $edit . '' . $delete . ' </span>';

    }
}

if (!function_exists('displayTR')) {
    function displayTR($arr, $searchVal, $dPlace = 2)
    {
        foreach ($arr as $salDet) {
            if ($salDet['catID'] == $searchVal) {
                echo '<td align="right">' . number_format($salDet['amount'], $dPlace) . '</td>';
                return false;
            }
        }
        return '<td align="center">-</td>';
    }
}

if (!function_exists('search_paysheetEmpDetails')) {
    function search_paysheetEmpDetails($arr, $searchingKey)
    {
        $keys = array_keys(array_column($arr, 'catID'), $searchingKey);
        $new_array = array_map(function ($k) use ($arr) {
            return $arr[$k];
        }, $keys);

        return (!empty($new_array[0])) ? trim($new_array[0]['amount']) : 0;
    }
}


if (!function_exists('paysheet_action_approval')) { /*get po action list*/
    function paysheet_action_approval($payrollID, $approvalLevelID, $monthDet, $payrollCode, $appYN, $type)
    {
        $status = ($type=='edit')?'<span class="pull-right">':'';
        if ($appYN == 1) {
            $str = ($type=='edit')?'<span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span>':$payrollCode;
            $status .= '<a onclick="load_paysheetApproval(' . $payrollID . ',' . $approvalLevelID . ',\'' . $monthDet . '\',\'' . $payrollCode . '\',\'' . $appYN . '\')">';
            $status .= $str.'</a>';
        }else{
            $str = ($type=='edit')?'<span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span>':$payrollCode;
            $status .= '<a onclick="load_paysheetApproval(' . $payrollID . ',' . $approvalLevelID . ',\'' . $monthDet . '\',\'' . $payrollCode . '\',\'' . $appYN . '\')">';
            $status .= $str.'</a>';
        }

        /*$default_template = getDefault_template($isPayroll);

        if (!empty($default_template)) {
            $status .= '<a target="_blank" href="' . site_url('template_paySheet/paySheet_print/') . '/' . $payrollID . '/' . $default_template . '/' . $isPayroll;
            $status .= '/' . $payrollCode . '">';
            $status .= '<span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        }*/

        $status .= ($type=='code')?'</span>':'';

        return $status;
    }
}


if (!function_exists('template_status')) {
    function template_status($autoID, $status, $confirmedYN, $isNonPayroll)
    {
        if ($confirmedYN == 1) {
            $checked = ($status == 1) ? 'checked' : '';
            $isDisable = ($status == 1) ? 'disabled' : '';

            return '<input type="checkbox" class="switch-chk btn-sm" id="status_' . $autoID . '" onchange="changeStatus(' . $autoID . ', \''.$isNonPayroll.'\')"
                    data-size="mini" data-on-text="YES" data-handle-width="25" data-off-color="danger" data-on-color="success"
                    data-off-text="NO" data-label-width="0" ' . $checked . ' ' . $isDisable . '>';
        } else {
            return '-';
        }

    }
}

if (!function_exists('getDefault_template')) {
    function getDefault_template($isNonPayroll)
    {
        $CI =& get_instance();
        $company_ID = current_companyID();
        $templateID = $CI->db->query("SELECT templateID FROM srp_erp_pay_template WHERE companyID = {$company_ID}
                                      AND isDefault=1 AND isNonPayroll='{$isNonPayroll}'")->row('templateID');

        if (empty($templateID)) {
            $templateID = $CI->db->query("SELECT templateID FROM srp_erp_pay_template WHERE companyID = {$company_ID} AND
                                          isNonPayroll='{$isNonPayroll}'")->row('templateID');
        }

        return $templateID;
    }
}

if (!function_exists('get_payGroup')) {
    function get_payGroup($isAll=0)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $result = array();

        if($isAll == 0){
            $result = $CI->db->query("SELECT masterTB.description, masterTB.payGroupID, isGroupTotal
                                  FROM srp_erp_paygroupmaster AS masterTB
                                  LEFT JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID = masterTB.socialInsuranceID
                                  LEFT JOIN srp_erp_payeemaster AS payee_TB ON payee_TB.payeeMasterID = masterTB.payeeID
                                  JOIN srp_erp_paygroupformula AS formula ON formula.payGroupID=masterTB.payGroupID
                                  WHERE masterTB.companyID = {$companyID} AND isGroupTotal=0")->result_array();
        }
        else if($isAll == 1){
            $result = $CI->db->query("SELECT masterTB.description, masterTB.payGroupID, isGroupTotal
                                  FROM srp_erp_paygroupmaster AS masterTB
                                  JOIN srp_erp_paygroupformula AS formula ON formula.payGroupID=masterTB.payGroupID
                                  WHERE masterTB.companyID = {$companyID}")->result_array();
        }
        else if($isAll == 2){
            $result = $CI->db->query("SELECT masterTB.description, masterTB.payGroupID, isGroupTotal
                              FROM srp_erp_paygroupmaster AS masterTB
                              JOIN srp_erp_paygroupformula AS formula ON formula.payGroupID=masterTB.payGroupID
                              WHERE masterTB.companyID = {$companyID} AND (ssoCategories IS NULL OR ssoCategories='')
                              AND (payGroupCategories IS NULL OR payGroupCategories='') AND isGroupTotal=1")->result_array();
        }


        return $result;
    }
}


$globalFormula = '';
if (!function_exists('decode_payGroup')) {
    function decode_payGroup($formulaData, $decode_payGroup_count=0)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $payGroupCategories = $formulaData['payGroupCategories'];


        global $globalFormula;
        $decode_payGroup_count++;

        if($decode_payGroup_count > 1000){
            //If the recursive worked more than 200 times than terminate the function
            return ['e', 'Decode pay group function get terminated.<br/>'];
        }

        $result = $CI->db->query("SELECT masterTB.payGroupID, formulaString, payGroupCategories FROM srp_erp_paygroupmaster AS masterTB
                                  JOIN srp_erp_paygroupformula AS formula ON formula.payGroupID=masterTB.payGroupID
                                  WHERE masterTB.companyID = {$companyID} AND masterTB.payGroupID IN ($payGroupCategories)")->result_array();

        foreach($result as $row){
            $searchVal = '~'.$row['payGroupID'];
            $replaceVal = '|(|'.$row['formulaString'].'|)|';


            if(!empty( $row['payGroupCategories'] )){
                $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                $return = decode_payGroup($row, $decode_payGroup_count);
                if(is_array($return)){
                    if($return[0] == 'e'){
                        return $return;
                        break;
                    }
                }
            }
            else{
                $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                $payGroupCategories = null;
            }
        }

        return $globalFormula;
    }
}

if (!function_exists('formulaBuilder_to_sql')) {
    function formulaBuilder_to_sql($ssoRow, $salary_categories_arr, $payDateMin, $payGroupID, $getBalancePay='Y')
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $formula = trim($ssoRow['formulaString'] ?? '');
        $payGroupCategories = trim($ssoRow['payGroupCategories'] ?? '');
        $formulaText = '';
        $salaryCatID = array();
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        if(!empty($payGroupCategories)){
            global $globalFormula;
            $globalFormula = $formula;
            $decode_data = decode_payGroup($ssoRow);
            if(is_array($decode_data)){
                if($decode_data[0] == 'e'){
                    //If maximum recursive exceeded than return will be a array else string
                    return $decode_data;
                }
            }
            $formula = $decode_data;
        }

        $formula_arr = explode('|', $formula); // break the formula

        $n = 0;
        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand
                    $formulaText .= $formula_row;
                    $formulaDecode_arr[] = $formula_row;
                } else {

                    /********************************************************************************************
                     * If a amount remove '_' symbol and append in the formula
                     * if a salary category  remove '#' symbol and append in the formula
                     * else if it's a balance payment '!' because there is no MA or MD in SSO formula builder
                     ********************************************************************************************/

                    $elementType = $formula_row[0];

                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= ( is_numeric($numArr[1]) ) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = ( is_numeric($numArr[1]) ) ? $numArr[1] : $numArr[0];
                    }
                    else if ($elementType == '#') {
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
                    }
                    else if ($elementType == '!') {
                        /******************************************************************************************************
                         * To decide which slab range is applicable for the employee in the current month have
                         * to skip the balance SSO payment in the calculation
                         *
                         * If the formula decode for the decision then   $getBalancePay will be 'N'
                         ******************************************************************************************************/
                        if($getBalancePay == 'Y'){
                            $balanceData = $CI->db->query("SELECT *, SUM(balanceAmount) AS total_balanceAmount  FROM(
	                                                            SELECT empID, balanceAmount, DATE_FORMAT(dueDate ,'%Y-%m-01') AS dueDate2
	                                                            FROM srp_erp_pay_balancessopayment WHERE companyID={$companyID} AND payGroupID={$payGroupID}
	                                                       )AS balanceDataTB WHERE balanceDataTB.dueDate2 ='{$payDateMin}' GROUP BY empID ")->result_array();

                            $strBalance='';
                            if(!empty($balanceData)){
                                $strBalance .='(CASE ';
                                foreach($balanceData as $keyBalance=>$rowBalance){
                                    $strBalance .= ' WHEN (calculationTB.empID='.$rowBalance['empID'].') THEN '.$rowBalance['total_balanceAmount'].' ';
                                }
                                $strBalance .=' ELSE 0 END) ';
                            }else{
                                $strBalance='0';
                            }

                            $formulaDecode_arr[] = $strBalance;
                        }
                        else{
                            $formulaDecode_arr[] =  '0';
                        }
                    }
                    $n++;
                }
            }

        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_str2 = '';
        $whereInClause = '';
        foreach ($salaryCatID as $key1 => $row) {
            $separator = ($key1 > 0) ? ',' : '';
            $select_str2 .= $separator . 'IF(salCatID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
            $whereInClause .= $separator . ' ' . $row['ID'];
        }

        $whereInClause = ($whereInClause == '' ) ? '' : 'AND salCatID IN ('.$whereInClause.')';
        return array(
            'formulaDecode' => $formulaDecode,
            'select_str2' => $select_str2,
            'whereInClause' => $whereInClause,
        );
    }
}


if (!function_exists('payGroup_formulaBuilder_to_sql')) {
    function payGroup_formulaBuilder_to_sql($returnType ,$ssoRow = array(), $salary_categories_arr = array(), $payGroup_arr = array(), $payGroupID=null, $payDateMin=null)
    {
        $formula = (is_array($ssoRow))? trim($ssoRow['formulaString'] ?? '') : $ssoRow;
        $payGroupCategories = (is_array($ssoRow))? trim($ssoRow['payGroupCategories'] ?? '') :'';
        $formulaText = '';
        $salaryCatID = array();
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        if(!empty($payGroupCategories)){
            global $globalFormula;
            $globalFormula = $formula;
            $decode_data = decode_payGroup($ssoRow);
            if(is_array($decode_data)){
                if($decode_data[0] == 'e'){
                    //If maximum recursive exceeded than return will be a array else string
                    return $decode_data;
                }
            }
            $formula = $decode_data;

        }


        $formula_arr = is_string($formula) ? explode('|', $formula) : []; // break the formula
        $n = 0;

        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand
                    $formulaText .= ' '.$formula_row.' ';

                    $formulaDecode_arr[] = $formula_row;
                }
                else {

                    $elementType = $formula_row[0];

                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= ( is_numeric($numArr[1]) ) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = ( is_numeric($numArr[1]) ) ? $numArr[1] : $numArr[0];

                    }
                    else if ($elementType == '@') {
                        /*** SSO ***/
                        $SSO_Arr = explode('@', $formula_row);
                        $salaryCatID[$n]['ID'] = $SSO_Arr[1];
                        $salaryCatID[$n]['columnType'] = 'SSO';

                        $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $SSO_Arr[1]);
                        $new_array = array_map(function ($k) use ($payGroup_arr) {
                            return $payGroup_arr[$k];
                        }, $keys);

                        $ssoDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                        $formulaText .= $ssoDescription;

                        $ssoDescription_arr = explode(' ', $ssoDescription);
                        $ssoDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $ssoDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $ssoDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';

                    }
                    else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $salaryCatID[$n]['ID'] = $catArr[1];
                        $salaryCatID[$n]['columnType'] = 'SALARY_CAT';

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

                    }
                    else if ($elementType == '~') {
                        /*** Pay Group ***/
                        $SSO_Arr = explode('~', $formula_row);
                        $salaryCatID[$n]['ID'] = $SSO_Arr[1];
                        $salaryCatID[$n]['columnType'] = 'SSO';

                        $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $SSO_Arr[1]);
                        $new_array = array_map(function ($k) use ($payGroup_arr) {
                            return $payGroup_arr[$k];
                        }, $keys);

                        $ssoDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                        $formulaText .= $ssoDescription;

                        $ssoDescription_arr = explode(' ', $ssoDescription);
                        $ssoDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $ssoDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $ssoDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';

                    }
                    else if ($elementType == '!') {
                        $monthlyADArr = explode('!', $formula_row);

                        if (trim($monthlyADArr[1] ?? '') == '0') {
                            /*** Balance Payment ***/
                            $formulaText .= 'Balance Payment';
                            if( $payGroupID != null and $payDateMin != null){
                                $CI =& get_instance();
                                $companyID = current_companyID();
                                $balanceData = $CI->db->query("SELECT *, SUM(balanceAmount) AS total_balanceAmount  FROM(
	                                                            SELECT empID, balanceAmount, DATE_FORMAT(dueDate ,'%Y-%m-01') AS dueDate2
	                                                            FROM srp_erp_pay_balancessopayment WHERE companyID={$companyID} AND payGroupID={$payGroupID}
	                                                       )AS balanceDataTB WHERE balanceDataTB.dueDate2 ='{$payDateMin}' GROUP BY empID ")->result_array();

                                $strBalance='';
                                if(!empty($balanceData)){
                                    $strBalance .='(CASE ';
                                    foreach($balanceData as $keyBalance=>$rowBalance){
                                        $strBalance .= ' WHEN (calculationTB.empID='.$rowBalance['empID'].') THEN '.$rowBalance['total_balanceAmount'].' ';
                                    }
                                    $strBalance .=' ELSE 0 END) ';
                                }else{
                                    $strBalance='0';
                                }

                                $formulaDecode_arr[] = $strBalance;
                            }
                            else{
                                $formulaDecode_arr[] = '0';
                            }


                        }
                        else if ($monthlyADArr[1] == 'MA' || $monthlyADArr[1] == 'MD') {
                            /*** Monthly Addition or Monthly Deduction ***/
                            $formulaText .= ($monthlyADArr[1] == 'MA') ? 'Monthly Addition' : 'Monthly Deduction';
                            $MD_MD_Description = $monthlyADArr[1] . '_' . $n;

                            $formulaDecode_arr[] = 'SUM(' . $MD_MD_Description . ')';
                            $salaryCatID[$n]['cat'] = $monthlyADArr[1];
                            $salaryCatID[$n]['description'] = $MD_MD_Description;
                            $salaryCatID[$n]['columnType'] = 'MA|MD';
                        }
                        else if ($monthlyADArr[1] == 'FG') {
                            /*** Monthly Addition or Monthly Deduction ***/
                            $formulaText .= 'Basic Pay';
                            $MD_MD_Description = $monthlyADArr[1] . '_' . $n;

                            $formulaDecode_arr[] = 'SUM(' . $MD_MD_Description . ')';
                            $salaryCatID[$n]['cat'] = $monthlyADArr[1];
                            $salaryCatID[$n]['description'] = 'Basic Pay';
                            $salaryCatID[$n]['columnType'] = 'FG';
                        }
                        else if ($monthlyADArr[1] == 'TW') {
                            /*** Monthly Addition or Monthly Deduction ***/
                            $formulaText .= 'Total working days';
                            $MD_MD_Description = $monthlyADArr[1] . '_' . $n;

                            $formulaDecode_arr[] = 'SUM(' . $MD_MD_Description . ')';
                            $salaryCatID[$n]['cat'] = $monthlyADArr[1];
                            $salaryCatID[$n]['description'] = 'Total working days';
                            $salaryCatID[$n]['columnType'] = 'FG';
                        }
                    }

                    $n++;
                }
            }

        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_salaryCat_str = '';
        $select_group_str = '';
        $select_monthlyAD_str = '';
        $whereInClause = '';
        $where_MA_MD_Clause = array();
        $whereInClause_group = '';
        $separator_salCat_count = 0;
        $separator_group_count = 0;
        $separator_monthlyAD_count = 0;


        if($returnType == 'decode'){
            foreach ($salaryCatID as $key1 => $row) {
                $separator_salCat = ($separator_salCat_count > 0) ? ',' : '';
                $separator_group = ($separator_group_count > 0) ? ',' : '';
                $separator_monthlyAD = ($separator_monthlyAD_count > 0) ? ',' : '';

                if ($row['columnType'] == 'SALARY_CAT') {
                    $select_salaryCat_str .= $separator_salCat . 'IF(salCatID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
                    $whereInClause .= $separator_salCat . ' ' . $row['ID'];
                    $separator_salCat_count++;
                }
                if ($row['columnType'] == 'SSO') {
                    $select_group_str .= $separator_group . 'IF(detailTBID=' . $row['ID'] . ' AND fromTB=\'PAY_GROUP\', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
                    $whereInClause_group .= $separator_group . ' ' . $row['ID'];
                    $separator_group_count++;
                }
                if ($row['columnType'] == 'MA|MD') {
                    $select_monthlyAD_str .= $separator_monthlyAD . ' IF(calculationTB=\'' . $row['cat'] . '\', SUM(transactionAmount) , 0 ) AS ' . $row['description'] . '';

                    //array_push($where_MA_MD_Clause, array($row['cat']=>$row['cat']));
                    $where_MA_MD_Clause[] = $row['cat'];
                    $separator_monthlyAD_count++;
                }


            }

            $returnData = array(
                'formulaDecode' => $formulaDecode,
                'select_salaryCat_str' => $select_salaryCat_str,
                'select_group_str' => $select_group_str,
                'select_monthlyAD_str' => $select_monthlyAD_str,
                'whereInClause' => $whereInClause,
                'where_MA_MD_Clause' => $where_MA_MD_Clause,
                'whereInClause_group' => $whereInClause_group,
            );
        }
        else{
            $returnData = [$formulaText];
        }

        return $returnData;
    }
}


if (!function_exists('processed_bankTransferData_currencyWiseSum')) {
    function processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, $asArray=null)
    {
        $CI =& get_instance();
        if($isNonPayroll != 'Y'){
            $headerTB = 'srp_erp_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_banktransfer';
        }else{
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_non_banktransfer';
        }

        $result = $CI->db->query("SELECT  FORMAT(SUM(trans.transactionAmount) , trans.transactionCurrencyDecimalPlaces) trAmount, trans.transactionCurrency
                                       FROM {$bankTrTB} AS trans
                                       JOIN {$headerTB} AS header ON trans.empID = header.EmpID
                                       WHERE trans.payrollMasterID = {$payrollMasterID} AND header.payrollMasterID = {$payrollMasterID}
                                       AND bankTransferID = {$bankTransID} GROUP BY trans.transactionCurrency")->result_array();

        if( $asArray == 1 ){
            return $result;
        }
        else{
            $str = '';
            foreach($result as $keyCurr=>$currencySumRow){
                /*return '<div class="col-sm-12" align="right">
                            <div class="col-sm-2"> '.$currencySumRow['transactionCurrency'].':</div>
                            <div class="col-sm-10" align="right"> '.$currencySumRow['trAmount'].' </div>
                        </div>';*/

                $str .= $currencySumRow['transactionCurrency'].': '.$currencySumRow['trAmount'].' </br>';
            }

            return $str;
        }

    }
}


if (!function_exists('getNoPaySystemTableRecords')) {
    function getNoPaySystemTableRecords()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $result = $CI->db->query("SELECT *, masterTB.id AS masterID,srp_erp_pay_salarycategories.salaryDescription as salaryDescription, fromulaTB.id AS formulaTBID
                                  FROM srp_erp_nopaysystemtable AS masterTB
                                  JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID=masterTB.id AND companyID={$companyID}
                                  LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_pay_salarycategories.salaryCategoryID=fromulaTB.salaryCategoryID
                                  AND fromulaTB.companyID={$companyID}  ORDER BY masterTB.id desc")->result_array();

        return $result;
    }
}

if (!function_exists('getNoPaySystemTableDrop')) {
    function getNoPaySystemTableDrop()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT id, description, IF(isNonPayroll='Y', 2, 1) AS payType  FROM srp_erp_nopaysystemtable")->result_array();

        return $data;
    }
}


if (!function_exists('payrollYear_dropDown')) {
    function payrollYear_dropDown($isNonPayroll = NULL)
    {
        $companyID = current_companyID();
        $tableName = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $data = $CI->db->query("SELECT payrollYear, payrollMonth, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                    FROM {$tableName} WHERE companyID={$companyID} AND approvedYN=1 GROUP BY payrollYear ORDER BY payrollYear desc")->result_array();
        //$payroll_arr = array('' =>  $CI->lang->line('common_please_select'));
        //$payroll_arr = array('' =>  $CI->lang->line('common_please_select')/*'Please Select'*/);
        $payroll_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $payroll_arr[trim($row['payrollYear'] ?? '')] = trim($row['payrollYear'] ?? '');
            }
        }
        return $payroll_arr;
    }
}

if (!function_exists('travel_request_action_approval')) { /*get po action list*/
    function travel_request_action_approval($travelRequestlID, $approvalLevelID, $RequestCode, $appYN, $type)
    {
        $status = ($type=='edit')?'<span class="pull-right">':'';
        if ($appYN == 1) {
            $str = ($type=='edit')?'<span title="Approve" rel="tooltip" class="glyphicon glyphicon-eye-open"></span>':$RequestCode;
            $status .= '<a onclick="load_travelRequestApproval(' . $travelRequestlID . ',' . $approvalLevelID . ',\'' . $RequestCode . '\',\'' . $appYN . '\')">';
            $status .= $str.'</a>';
        }else{
            $str = ($type=='edit')?'<span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span>':$RequestCode;
            $status .= '<a onclick="load_travelRequestApproval(' . $travelRequestlID . ',' . $approvalLevelID . ',\'' . $RequestCode . '\',\'' . $appYN . '\')">';
            $status .= $str.'</a>';
        }

        $status .= ($type=='code')?'</span>':'';

        return $status;
    }
}
