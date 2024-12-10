<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_grv_action')) { /*get po action list*/
    function load_grv_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp, $documentID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $grvattachment = 'GRV' === $documentID ? $CI->lang->line('helper_grv_attachment') : 'SRN Attachment';
        $EditGoodsReceivedVoucher = 'GRV' === $documentID ? $CI->lang->line('helper_edit_goods_received_voucher') : 'Edit Service Received Note'; /*Edit Goods Received Voucher*/
        $deleteVoucher = 'GRV' === $documentID ? 'Good Received Voucher' : 'Service Received Note';
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a href="#" onclick=\'attachment_modal(' . $poID . ',"' . $grvattachment . '", "' . $documentID . '",' . $POConfirmedYN . ');\'>
                    <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a href="#" onclick="reOpen_contract(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Reopen</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick=\'fetchPage("system/grv/erp_grv_new",' . $poID . ',"' . $EditGoodsReceivedVoucher . '","' . $documentID . '");\'>
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="referbackgrv(' . $poID . ');">
                        <i class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a href="#" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\');">
                    <i class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></i> View</a></li>';

        $status .= '<li><a href="' . site_url('Grv/load_grv_conformation/') . '/' . $poID . '" target="_blank">
                    <i class="glyphicon glyphicon-print" style="color: #607d8b;"></i> Print</a></li>';

        $status .= '<li><a href="' . site_url('Grv/load_grv_mrir/') . '/' . $poID . '" target="_blank">
                    <i class="glyphicon glyphicon-book glyphicon-book-btn"></i> Inspection Report</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a href="#" onclick="delete_item(' . $poID . ',\'' . $deleteVoucher . '\');">
                        <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a href="#" onclick="traceDocument(' . $poID . ',\'' . $documentID . '\');">
                        <i class="fa fa-search" style="color: #fdc45e;"></i> Trace Document</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('grv_action_approval')) { /*get po action list*/
    function grv_action_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $GoodReceivedVoucher = $CI->lang->line('helper_good_received_voucher');/*Good Received Voucher*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$GoodReceivedVoucher.'","GRV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'GRV\',\'' . $poID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('grv_action_approval_buyback')) { /*get po action list*/
    function grv_action_approval_buyback($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $GoodReceivedVoucher = $CI->lang->line('helper_good_received_voucher');/*Good Received Voucher*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$GoodReceivedVoucher.'","GRV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'GRV\',\'' . $poID . '\',\'buy\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('grv_confirm')) { /*get po action list*/
    function grv_confirm($con)
    {
        $status = '<center>';
        /*        if ($m_id) {
                    if ($con == 0) {
                        $status .= '<a onclick="procu(' . $m_id . ')"><span class="label label-danger">&nbsp;</span></a>';
                    } elseif ($con == 1) {
                        $status .= '<a onclick="procu(' . $m_id . ')"><span class="label label-success">&nbsp;</span></a>';
                    } elseif ($con == 2) {
                        $status .= '<a onclick="procu(' . $m_id . ')"><span class="label label-warning">&nbsp;</span></a>';
                    } else {
                        $status .= '-';
                    }
                } else {*/
        if ($con == 0) {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        } elseif ($con == 1) {
            $status .= '<span class="label label-success">&nbsp;</span>';
        } elseif ($con == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } else {
            $status .= '-';
        }

        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('grv_total_value')) {
    function grv_total_value($id)
    {
        $CI =& get_instance();
        $CI->db->select_sum('receivedTotalAmount');
        $CI->db->where('grvAutoID', $id);
        $totalAmount = $CI->db->get('srp_erp_grvdetails')->row('receivedTotalAmount');
        $CI->db->select_sum('total_amount');
        $CI->db->where('grvAutoID', $id);
        $totalAmount += $CI->db->get('srp_erp_grv_addon')->row('total_amount');
        return number_format($totalAmount, 2);
    }
}

if (!function_exists('addon_catagory')) {
    function addon_catagory()
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get('srp_erp_addon_category')->result_array();
        $data_arr = array('' => 'Select Addon Category');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['description'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('isProductReference_completed')) {
    function isProductReference_completed($receivedDocumentDetailID)
    {
        $CI =& get_instance();
        $result = $CI->db->query("SELECT count(subItemAutoID) as countTotal FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentDetailID = '" . $receivedDocumentDetailID . "' AND (ISNULL(productReferenceNo) OR productReferenceNo='')")->row_array();

        return $result['countTotal'];

    }
}

if (!function_exists('isProductReference_completed_document')) {
    function isProductReference_completed_document($grvID)
    {
        $CI =& get_instance();
        $result = $CI->db->query("SELECT
                                    count(itemMaster.subItemAutoID) AS countTotal
                                FROM
                                    srp_erp_grvmaster grv
                                LEFT JOIN srp_erp_grvdetails grvDetail ON grvDetail.grvAutoID = grv.grvAutoID
                                LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = grvDetail.grvDetailsID
                                LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = itemMaster.itemAutoID
                                WHERE
                                    grv.grvAutoID = '" . $grvID . "'
                                AND ( ISNULL( itemMaster.productReferenceNo ) OR itemMaster.productReferenceNo = '' )
                                AND im.isSubitemExist = 1")->row_array();

        return $result['countTotal'];

    }
}


if (!function_exists('productReference_completed_document')) {
    function productReference_completed_document($pvID)
    {
        $CI =& get_instance();
        //$result = $CI->db->query("SELECT * FROM srp_erp_paymentvouchermaster pv JOIN srp_erp_paymentvoucherdetail pvd ON itemMaster.receivedDocumentAutoID = grv.grvAutoID WHERE grv.grvAutoID = '" . $grvID . "'  AND   (ISNULL(itemMaster.productReferenceNo) OR itemMaster.productReferenceNo='') ")->row_array();

        //return $result['countTotal'];

    }
}
if (!function_exists('load_grv_action_buyback')) { /*get po action list*/
    function load_grv_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('helper', $primaryLanguage);
        $grvattachment=$CI->lang->line('helper_grv_attachment');/*GRV Attachment*/
        $EditGoodsReceivedVoucher =$CI->lang->line('helper_edit_goods_received_voucher');/*Edit Goods Received Voucher*/
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$grvattachment.'","GRV",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/grv/erp_grv_new_buyback",' . $poID . ',"'.$EditGoodsReceivedVoucher.'","GRV"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referbackgrv(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'GRV\',\'' . $poID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        //$status .= '<a target="_blank" href="' . site_url('Grv/load_grv_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '<a onclick="load_printtemp(' . $poID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Good Received Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        if($approved==1){
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ', \'GRV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }

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
