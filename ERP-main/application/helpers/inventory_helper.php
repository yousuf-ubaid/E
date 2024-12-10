<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('material_issue_total_value')) {
    function material_issue_total_value($id)
    {
        $CI =& get_instance();
        $CI->db->select_sum('qtyIssued');
        $CI->db->where('itemIssueAutoID', $id);
        $totalAmount = $CI->db->get('srp_erp_itemissuedetails')->row('qtyIssued');
        if ($totalAmount == '') {
            $totalAmount = 0;
        }
        return $totalAmount;
    }
}

if (!function_exists('load_material_issue_action')) {
    function load_material_issue_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $MaterialIssue = $CI->lang->line('helper_material_issue_attachment'); // Material Issue Attachment
        $EditMaterialIssue = $CI->lang->line('helper_edit_material_issue'); // Edit Material Issue
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $poID . ',"' . $MaterialIssue . '", "MI",' . $POConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/inventory/erp_material_issue",' . $poID . ',"' . $EditMaterialIssue . '","MI");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referbackgrv(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'MI\',\'' . $poID . '\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('Inventory/load_material_issue_conformation/') . '/' . $poID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $poID . ',\'Material issue\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a href="#" onclick="traceDocument(' . $poID . ', \'MI\');">
                        <i class="fa fa-search" style="color: #fdc45e;"></i> Trace Document</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_material_issue_action_mc')) {
    function load_material_issue_action_mc($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $MaterialIssue=$CI->lang->line('helper_material_issue_attachment');/*Material Issue Attachment*/
        $EditMaterialIssue=$CI->lang->line('helper_edit_material_issue');/*Edit Material Issue*/
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$MaterialIssue.'","MI",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_material_issue_mc",' . $poID . ',"'.$EditMaterialIssue.'","MI"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referbackgrv(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'MI\',\'' . $poID . '\',\'mc\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_material_issue_conformation_mc/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Material issue\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_stock_transfer_action')) {
    function load_stock_transfer_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $StockTransferAttachments = $CI->lang->line('helper_stock_transfer_attachments'); 
        $EditStockTransfer = $CI->lang->line('helper_edit_stock_transfer'); 
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $poID . ',"' . $StockTransferAttachments . '","ST",' . $POConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/inventory/erp_stock_transfer",' . $poID . ',"' . $EditStockTransfer . '","ST");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referbackgrv(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'ST\',\'' . $poID . '\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('Inventory/load_stock_transfer_conformation/') . '/' . $poID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $poID . ',\'Stock Transfer\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_stock_adjustment_action')) {
    function load_stock_adjustment_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $StockAdjustment = $CI->lang->line('helper_stock_adjustment_attachments');
        $EditStockAdjustment = $CI->lang->line('helper_edit_stock_adjustment');
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $poID . ',"' . $StockAdjustment . '","SA",' . $POConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/inventory/erp_stock_adjustment",' . $poID . ',"' . $EditStockAdjustment . '","SA");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referback_stock_adjustment(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'SA\',\'' . $poID . '\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('Inventory/load_stock_adjustment_conformation/') . '/' . $poID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $poID . ',\'Stock Adjustment\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}


if (!function_exists('curent_employee_drop')) {
    function curent_employee_drop($status = TRUE, $isDischarged = 0)
    {
        $curID = current_userID();
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        $CI->db->where('EIdNo',$curID);
        $CI->db->where('isPayrollEmployee', 1);
        if ($isDischarged == 1) {
            $CI->db->where('isDischarged !=1 ');
        }
        $customer = $CI->db->get()->result_array();
        $customer_arr = [];
        if ($status == TRUE) {
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                }
            }
        } else {
            $customer_arr = $customer;
        }

        return $customer_arr;
    }
}


if (!function_exists('mi_confirm')) {
    function mi_confirm($con, $m_id = null)
    {
        $status = '<center>';
        if ($m_id) {
            if ($con == 0) {
                $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-danger">&nbsp;</span></a>';
            } elseif ($con == 1) {
                $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-success">&nbsp;</span></a>';
            } elseif ($con == 2) {
                $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-warning">&nbsp;</span></a>';
            } else {
                $status .= '-';
            }
        } else {
            if ($con == 0) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } elseif ($con == 1) {
                $status .= '<span class="label label-success">&nbsp;</span>';
            } elseif ($con == 2) {
                $status .= '<span class="label label-warning">&nbsp;</span>';
            } else {
                $status .= '-';
            }
        }

        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('material_issue_action_approval')) {
    function material_issue_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $MaterialIssue=$CI->lang->line('helper_material_issue_attachment');/*Material Issue */
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"'.$MaterialIssue.'","MI");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        }else{
            $status .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'MI\',\'' . $AutoID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('material_issue_action_approval_mc')) {
    function material_issue_action_approval_mc($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $MaterialIssue=$CI->lang->line('helper_material_issue_attachment');/*Material Issue */
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"'.$MaterialIssue.'","MI");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        }else{
            $status .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'MI\',\'' . $AutoID . '\',\'mc\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('stock_transfer_action_approval')) {
    function stock_transfer_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $StockTransferAttachment=$CI->lang->line('helper_stock_transfer_attachments');/*Stock Transfer Attachments*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"'.$StockTransferAttachment.'","ST");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'ST\',\'' . $AutoID . '\',\'buy\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_transfer_conformation/') . '/' . $AutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('stock_return_action_approval')) {
    function stock_return_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $PurchasereturnAttachment=$CI->lang->line('helper_purchase_return_attachment');/*Purchase return Attachment*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"'.$PurchasereturnAttachment.'","SR");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'SR\',\'' . $AutoID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
            //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_return_conformation/') . '/' . $AutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('stock_adjustment_action_approval')) {
    function stock_adjustment_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $stockadjustmentAttachments=$CI->lang->line('helper_stock_adjustment_attachments');/*Stock Adjustment Attachments*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . '," '.$stockadjustmentAttachments.'","SA");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'SA\',\'' . $AutoID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


            //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_adjustment_conformation/') . '/' . $AutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('stock_adjustment_action_approval_buyback')) {
    function stock_adjustment_action_approval_buyback($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $stockadjustmentAttachments=$CI->lang->line('helper_stock_adjustment_attachments');/*Stock Adjustment Attachments*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . '," '.$stockadjustmentAttachments.'","SA");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'SA\',\'' . $AutoID . '\',\'buy\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_adjustment_conformation/') . '/' . $AutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_stock_return_action')) {
    function load_stock_return_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $PurchaseReturnAttachments = $CI->lang->line('helper_purchase_return_attachments');
        $EditPurchaseReturn = $CI->lang->line('helper_edit_purchase_return');
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $poID . ',"' . $PurchaseReturnAttachments . '", "SR",' . $POConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/inventory/erp_stock_return",' . $poID . ',"' . $EditPurchaseReturn . '","SR");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referback_stock_return(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'SR\',\'' . $poID . '\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('Inventory/load_stock_return_conformation/') . '/' . $poID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $poID . ',\'Stock Return\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}


if (!function_exists('load_sales_return_action')) {
    function load_sales_return_action($salesReturnAutoID, $confirmedYN, $approvedYN, $createdUserID, $isDeleted, $confirmedByEmpID) {
        $CI = &get_instance();
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $salesReturnAutoID . ',"Sales Return","SLR",' . $confirmedYN . ');\'><i class="glyphicon glyphicon-paperclip" style="color:#4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $salesReturnAutoID . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Re Open</a></li>';
        }

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick=\'fetchPage("system/inventory/erp_sales_return",' . $salesReturnAutoID . ',"Edit Sales Return","SLR"); \'><i class="glyphicon glyphicon-pencil" style="color:#116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) && $approvedYN == 0 && $confirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referback_sales_return(' . $salesReturnAutoID . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'SLR\',\'' . $salesReturnAutoID . '\')"><i class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></i> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Inventory/load_sales_return_conformation/') . '/' . $salesReturnAutoID . '"><i class="glyphicon glyphicon-print" style="color:#607d8b;"></i> Print</a></li>';

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="delete_item(' . $salesReturnAutoID . ',\'Stock Return\');"><i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        if ($approvedYN == 1) {
            $status .= '<li><a onclick="traceDocument(' . $salesReturnAutoID . ', \'SLR\')"><i class="fa fa-search" style="color:#fdc45e;"></i> Trace Document</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_inventory_catalogue_action')) {
    function load_inventory_catalogue_action($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmp, $closedYN)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $mrattachment=$CI->lang->line('helper_material_request_attachment');/*Material Request Attachment*/
        $EditMaterialRequestVoucher =$CI->lang->line('helper_edit_material_request');/*Edit Material Request*/
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$mrattachment.'","MR",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/inventory_catalogue_request",' . $poID . ',"'.$EditMaterialRequestVoucher.'","MIC"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referbackgrv(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'MIC\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_inventory_catalogue_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            //$status .= '&nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Material Request\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($approved == 1 && $isDeleted==0 && $closedYN == 0) {
            // $status .= '&nbsp;&nbsp;<a onclick=\'materialRequest_close("' . $poID . '"); \'><i title="Close" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a>';
        }
        if($approved==1){
            // $status .= '&nbsp;&nbsp;<a onclick="traceDocument(' . $poID . ', \'MR\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_material_request_action')) {
    function load_material_request_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp, $closedYN)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $mrattachment = $CI->lang->line('helper_material_request_attachment'); 
        $EditMaterialRequestVoucher = $CI->lang->line('helper_edit_material_request'); 

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $poID . ',"' . $mrattachment . '", "MR",' . $POConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/inventory/erp_material_request",' . $poID . ',"' . $EditMaterialRequestVoucher . '","MR");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referbackgrv(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'MR\',\'' . $poID . '\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('Inventory/load_material_request_conformation/') . '/' . $poID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $poID . ',\'Material Request\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        if ($approved == 1 && $isDeleted == 0 && $closedYN == 0) {
            $status .= '<li><a href="#" onclick=\'materialRequest_close("' . $poID . '"); \'>
                        <i class="fa fa-times" style="color: #f46a6a;"></i> Close</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a href="#" onclick="traceDocument(' . $poID . ', \'MR\');">
                        <i class="fa fa-search" style="color: #fdc45e;"></i> Trace Document</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}


if (!function_exists('load_material_request_employe_action')) {
    function load_material_request_employe_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp, $closedYN)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $mrattachment = $CI->lang->line('helper_material_request_attachment'); /* Material Request Attachment */
        $EditMaterialRequestVoucher = $CI->lang->line('helper_edit_material_request'); /* Edit Material Request */

        $dropdownItems = '';

        $dropdownItems .= '<li><a onclick=\'attachment_modal(' . $poID . ',"' . $mrattachment . '","MR",' . $POConfirmedYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color:#4caf50;"></span> Attachment</a></li>';

        if ($isDeleted == 1) {
            $dropdownItems .= '<li><a onclick="reOpen_contract(' . $poID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $dropdownItems .= '<li><a onclick=\'fetchPage("system/inventory/erp_material_request_employee",' . $poID . ',"' . $EditMaterialRequestVoucher . '","MR");\'><span class="glyphicon glyphicon-pencil" style="color:#116f5e;"></span> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0) {
            $dropdownItems .= '<li><a onclick="referbackgrv(' . $poID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        $dropdownItems .= '<li><a onclick="documentPageView_modal(\'MR\',\'' . $poID . '\')"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';

        $dropdownItems .= '<li><a href="' . site_url('Inventory/load_material_request_conformation/') . '/' . $poID . '" target="_blank"><span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $dropdownItems .= '<li><a onclick="delete_item(' . $poID . ',\'Material Request\');"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($approved == 1 && $isDeleted == 0 && $closedYN == 0) {
            $dropdownItems .= '<li><a onclick=\'materialRequest_close("' . $poID . '");\'><i class="fa fa-times" style="color:#f46a6a;"></i> Close</a></li>';
        }

        if ($approved == 1) {
            $dropdownItems .= '<li><a onclick="traceDocument(' . $poID . ', \'MR\')" title="Trace Document"><i class="fa fa-search" style="color:#fdc45e;"></i> Trace Document</a></li>';
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

if (!function_exists('material_request_action_approval')) {
    function material_request_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $MaterialIssue=$CI->lang->line('helper_material_issue_attachment');/*Material Issue */
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"Material Request","MR");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        }else{
            $status .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'MR\',\'' . $AutoID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}
if (!function_exists('load_stock_adjustment_action_buyback')) {
    function load_stock_adjustment_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $StockAdjustment=$CI->lang->line('helper_stock_adjustment_attachments');/*Stock Adjustment Attachments*/
        $EditStockAdjustment=$CI->lang->line('helper_edit_stock_adjustment');/*Edit Stock Adjustment*/
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$StockAdjustment.'","SA",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_adjustment_buyback",' . $poID . ',"'.$EditStockAdjustment.'","SA"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referback_stock_adjustment(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'SA\',\'' . $poID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

/*        $status .= '<a target="_blank" href="' . site_url('Inventory/load_stock_adjustment_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';*/
        $status .= '<a onclick="load_printtemp(' . $poID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Stock Adjustment\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
    if (!function_exists('load_stock_transfer_action_buyback')) {
        function load_stock_transfer_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted)
        {
            $CI =& get_instance();
            $primaryLanguage = getPrimaryLanguage();
            $CI->lang->load('helper', $primaryLanguage);
            $StockTransferAttachments=$CI->lang->line('helper_stock_transfer_attachments');/*Stock Transfer Attachments*/
            $EditStockTransfer=$CI->lang->line('helper_edit_stock_transfer');/*Edit Stock Transfer*/
            $CI->load->library('session');
            $status = '<span class="pull-right">';
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$StockTransferAttachments.'","ST",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            if($isDeleted==1){
                $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($POConfirmedYN != 1 && $isDeleted==0) {
                $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_transfer_buyback",' . $poID . ',"'.$EditStockTransfer.'","ST"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            }

            if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
                $status .= '<a onclick="referbackgrv(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            }

            $status .= '<a target="_blank" onclick="documentPageView_modal(\'ST\',\'' . $poID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
            //$status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_transfer_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
            $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="load_printtemp(' . $poID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

            if ($POConfirmedYN != 1 && $isDeleted==0) {
                $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Stock Transfer\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
            $status .= '</span>';
            return $status;
        }
    }
}
if (!function_exists('load_sales_return_action_buyback')) {
    function load_sales_return_action_buyback($salesReturnAutoID, $confirmedYN, $approvedYN, $createdUserID,$isDeleted,$confirmedByEmpID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $salesReturnAutoID . ',"Sales Return","SLR",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $salesReturnAutoID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($confirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_sales_return_buyback",' . $salesReturnAutoID . ',"Edit Sales Return","SLR"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approvedYN == 0 and $confirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referback_sales_return(' . $salesReturnAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'SLR\',\'' . $salesReturnAutoID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        //$status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_sales_return_conformation_buyback/') . '/' . $salesReturnAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="load_printtemp(' . $salesReturnAutoID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($confirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $salesReturnAutoID . ',\'Stock Return\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if($approvedYN==1){
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $salesReturnAutoID . ', \'SLR\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }
        $status .= '</span>';
        return $status;
    }

    if (!function_exists('load_stock_transfer_action_suom')) {
        function load_stock_transfer_action_suom($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID)
        {
            $CI =& get_instance();
            $primaryLanguage = getPrimaryLanguage();
            $CI->lang->load('helper', $primaryLanguage);
            $StockTransferAttachments=$CI->lang->line('helper_stock_transfer_attachments');/*Stock Transfer Attachments*/
            $EditStockTransfer=$CI->lang->line('helper_edit_stock_transfer');/*Edit Stock Transfer*/
            $CI->load->library('session');
            $status = '<span class="pull-right">';
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$StockTransferAttachments.'","ST",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            if($isDeleted==1){
                $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($POConfirmedYN != 1 && $isDeleted==0) {
                $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_transfer_suom",' . $poID . ',"'.$EditStockTransfer.'","ST"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            }

            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
                $status .= '<a onclick="referbackgrv(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            }

            $status .= '<a target="_blank" onclick="documentPageView_modal(\'ST\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_transfer_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

            if ($POConfirmedYN != 1 && $isDeleted==0) {
                $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Stock Transfer\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
            $status .= '</span>';
            return $status;
        }
    }
}

if (!function_exists('load_bulk_transfer_action')) {
    function load_bulk_transfer_action($stockTransferAutoID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $stockTransferAutoID . ',"Bulk Transfer Attachments","STB",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_bulk_transfer(' . $stockTransferAutoID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_transfer_bulk",' . $stockTransferAutoID . ',"Edit Bulk Transfer","STB") \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referback_bulkTransfer(' . $stockTransferAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'STB\',\'' . $stockTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_bulk_transfer_conformation/') . '/' . $stockTransferAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_bulk_transfer(' . $stockTransferAutoID . ',\'Bulk Transfer\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('all_sub_category_drop')) {
    function all_sub_category_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("itemCategoryID");
        $CI->db->FROM('srp_erp_itemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->WHERE('codePrefix', 'INV');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $masterID = $CI->db->get()->row('itemCategoryID');

        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_itemcategory');
        $CI->db->WHERE('masterID', $masterID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Sub Category');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('bulk_transfer_action_approval')) {
    function bulk_transfer_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $StockTransferAttachment=$CI->lang->line('helper_bulk_transfer_attachments');/*Bulk Transfer Attachments*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"'.$StockTransferAttachment.'","STB")\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'STB\',\'' . $AutoID . '\',\'\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_transfer_conformation/') . '/' . $AutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

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