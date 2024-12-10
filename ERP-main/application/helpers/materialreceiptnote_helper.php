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

if (!function_exists('load_material_receipt_action')) {
    function load_material_receipt_action($mrnAutoID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $MaterialReceiptNoteAttachments = $CI->lang->line('helper_material_receipt_note_attachments');
        $EditMaterialReceiptNote = $CI->lang->line('helper_edit_material_receipt_note');
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $mrnAutoID . ',"' . $MaterialReceiptNoteAttachments . '","MRN",' . $POConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $mrnAutoID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/inventory/MaterialReceipt/erp_material_receipt",' . $mrnAutoID . ',"' . $EditMaterialReceiptNote . '","MRN");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referbackgrv(' . $mrnAutoID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'MRN\',\'' . $mrnAutoID . '\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('MaterialReceiptNote/load_material_receipt_conformation/') . '/' . $mrnAutoID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $mrnAutoID . ',\'Material Receipt Note\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_stock_transfer_action')) {
    function load_stock_transfer_action($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Stock Transfer","ST",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_transfer",' . $poID . ',"Edit Stock Transfer","ST"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
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

if (!function_exists('load_stock_adjustment_action')) {
    function load_stock_adjustment_action($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Stock Adjustment","SA",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_adjustment",' . $poID . ',"Edit Stock Adjustment","SA"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referback_stock_adjustment(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'SA\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a target="_blank" href="' . site_url('Inventory/load_stock_adjustment_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Stock Adjustment\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
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
        $MaterialRecieptNoteAttachment = $CI->lang->line('helper_material_reciept_note_attachment');/*Material Reciept Note Attachment*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"'.$MaterialRecieptNoteAttachment.'","MRN");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        }else{
            $status .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'MRN\',\'' . $AutoID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('stock_transfer_action_approval')) {
    function stock_transfer_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"Stock Transfer","ST");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $AutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'ST\',\'' . $AutoID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_transfer_conformation/') . '/' . $AutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('stock_return_action_approval')) {
    function stock_return_action_approval($AutoID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"Add Stock Return","SR");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
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
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $AutoID . ',"Add Stock Adjustment","SA");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
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

if (!function_exists('load_stock_return_action')) {
    function load_stock_return_action($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Purchase Return","SR",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_stock_return",' . $poID . ',"Edit Purchase Return","SR"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referback_stock_return(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'SR\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_stock_return_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Stock Return\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('load_sales_return_action')) {
    function load_sales_return_action($salesReturnAutoID, $confirmedYN, $approvedYN, $createdUserID,$isDeleted)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $salesReturnAutoID . ',"Sales Return","SLR",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $salesReturnAutoID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($confirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/inventory/erp_sales_return",' . $salesReturnAutoID . ',"Edit Sales Return","SLR"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approvedYN == 0 and $confirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referback_sales_return(' . $salesReturnAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'SLR\',\'' . $salesReturnAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('Inventory/load_sales_return_conformation/') . '/' . $salesReturnAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($confirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $salesReturnAutoID . ',\'Stock Return\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('isProductReference_completed_document')) {
    function isProductReference_completed_document_mrn($mrnID)
    {
        $CI =& get_instance();
        $result = $CI->db->query("SELECT
                                    count(itemMaster.subItemAutoID) AS countTotal
                                FROM
                                    srp_erp_materialreceiptmaster mrn
                                LEFT JOIN srp_erp_materialreceiptdetails mrnDetail ON mrnDetail.mrnAutoID = mrn.mrnAutoID
                                LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = mrnDetail.mrnDetailID
                                LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = itemMaster.itemAutoID
                                WHERE
                                    mrn.mrnAutoID = '" . $mrnID . "'
                                AND ( ISNULL( itemMaster.productReferenceNo ) OR itemMaster.productReferenceNo = '' )
                                AND im.isSubitemExist = 1")->row_array();

        return $result['countTotal'];

    }
}


if (!function_exists('get_material_receipt_note_detail')) {
    function get_material_receipt_note_detail($mrnAutoID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $result = $CI->db->query("SELECT * FROM srp_erp_materialreceiptmaster WHERE mrnAutoID = '{$mrnAutoID}' AND companyID = '{$companyID}'")->row_array();
        return $result;

    }
}
