<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_stock_counting_action')) {
    function load_stock_counting_action($scntID, $SCNTConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $scntID . ',"Stock Counting Attachments","SCNT",' . $SCNTConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $scntID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($SCNTConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/inventory/erp_stock_counting",' . $scntID . ',"Edit Stock Counting","SCNT");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $SCNTConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referback_stock_counting(' . $scntID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'SCNT\',\'' . $scntID . '\',\'SCNT\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('StockCounting/load_stock_counting_conformation/') . '/' . $scntID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        if ($SCNTConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $scntID . ',\'Stock Adjustment\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('stock_counting_action_approval')) {
    function stock_counting_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . '," Stock Counting Attachments","SCNT");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'SCNT\',\'' . $AutoID . '\',\'SCNT\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('stock_counting_action_approval_suom')) {
    function stock_counting_action_approval_suom($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . '," Stock Counting Attachments","SCNT");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'SCNT\',\'' . $AutoID . '\',\'SCNTsuom\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('load_stock_counting_action_suom')) {
    function load_stock_counting_action_suom($scntID, $SCNTConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $scntID . ',"Stock Counting Attachments","SCNT",' . $SCNTConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $scntID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($SCNTConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_counting_suom",' . $scntID . ',"Edit Stock Counting","SCNT"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $SCNTConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referback_stock_counting(' . $scntID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'SCNT\',\'' . $scntID . '\',\'SCNTsuom\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a target="_blank" href="' . site_url('StockCounting/load_stock_counting_conformation_suom/') . '/' . $scntID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($SCNTConfirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $scntID . ',\'Stock Adjustment\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}