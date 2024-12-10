<?php if (!defined('BASEPATH')) exit('No direct script access allowed');




if (!function_exists('load_budget_transfer_action')) {
    function load_budget_transfer_action($budgetTransferAutoID,$confirmedYN,$approvedYN)
    {
        $status = '<span class="pull-right">';
        if($confirmedYN !=1)
        {
            $status .= '<a onclick=\'fetchPage("system/budget/edit_budget_transfer_detail_page","' . $budgetTransferAutoID . '","Budget Transfer Detail","Budget Transfer Detail"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($confirmedYN == 1 && $approvedYN!=1) {
            $status .= '<a onclick="referbackbudget(' . $budgetTransferAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            //$status .= '<a onclick=\'fetchPage("system/budget/erp_budget_detail_page_view","' . $budgetTransferAutoID . '","Budget Detail ","Budget Transfer Detail"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open" ></span>';
        }
        $status .= '<a target="_blank" onclick="documentPageView_modal(\'BDT\',\'' . $budgetTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';


        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('load_budget_transfer_detail_action')) {
    function load_budget_transfer_detail_action($budgetTransferDetailAutoID)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="delete_budget_transfer_detail('.$budgetTransferDetailAutoID.')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('load_BDT_approval_action')) { /*get po action list*/
    function load_BDT_approval_action($budgetTransferAutoID, $ECConfirmedYN, $approved, $createdUserID,$Level)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a target="_blank" onclick="fetch_approval(\'' . $budgetTransferAutoID . '\','.$Level.')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'BDT\',\'' . $budgetTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('confirm_aproval_BD')) {
    function confirm_aproval_BD($approved_status, $confirmed_status, $code, $autoID)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 ||  $confirmed_status == 3) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            }  else if ($confirmed_status == 2) {
                $status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            } else {
                $status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            }
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a  class="label label-success">&nbsp;</a>';
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

if (!function_exists('load_BD_approval_action')) { /*get po action list*/
    function load_BD_approval_action($budgetTransferAutoID, $ECConfirmedYN, $approved, $createdUserID,$Level)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            //$status .= '<a target="_blank" onclick="fetch_approval(\'' . $budgetTransferAutoID . '\','.$Level.')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
            $status .= '<a onclick=\'fetchPage("system/budget/erp_budget_detail_page_approval","' . $budgetTransferAutoID . '","Budget Approval","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'BD\',\'' . $budgetTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}

