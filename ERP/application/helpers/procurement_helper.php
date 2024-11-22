<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_po_action')) { /*get po action list*/
    function load_po_action($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID,$ismaxportalStatus=0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
       $PurchaseOrder = $CI->lang->line('common_purchase_order');/*"Purchase Order"*/
        $CI->load->library('session');
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';
        $status .= '<li><a onclick=\'attachment_modal(' . $poID . ',"'.$PurchaseOrder.'","PO",' . $POConfirmedYN . ');\'><span rel="tooltip" class="glyphicon glyphicon-paperclip"></span> Attachment</a></li>';
        if($isDeleted==1){
            $status .= '<li><a onclick="reOpen_contract(' . $poID .');"><span rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if($ismaxportalStatus==2){

            $CI->db->select('*');
            $CI->db->from('srp_erp_purchaseordermaster');
            $CI->db->where('purchaseOrderID', $poID);
            $po_all= $CI->db->get()->row_array();

            $CI->db->select('*');
            $CI->db->from('srp_erp_suppliermaster');
            $CI->db->where('supplierAutoID', $po_all['supplierID']);
            $CI->db->where('isSrmGenerated', 1);
            $supplier_srm_created = $CI->db->get()->row_array();
            
            if($supplier_srm_created){

                $CI->db->select('*');
                $CI->db->from('srp_erp_srm_suppliermaster');
                $CI->db->where('erpSupplierAutoID', $po_all['supplierID']);
                $supplier_srm = $CI->db->get()->row_array();
                $status .= '<li><a onclick=\'open_chat_model_max_portal(0,0,'.$supplier_srm['supplierAutoID'].',' . $poID . ',10,"PO");\' rel="tooltip"><i class="fa fa-comment-o" aria-hidden="true"></i> Chat With Max Portal</a></li>';
            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<li><a onclick="referbackprocument(' . $poID . ');"><span rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<li><a onclick=\'fetchPage("system/procurement/erp_purchase_order_new",' . $poID . ',"Edit Purchase Order","PO"); \'><span rel="tooltip" class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
            $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\')" ><span rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a></li>';
            $status .= '<li><a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation/') . '/' . $poID . '" ><span rel="tooltip" class="glyphicon glyphicon-print"></span> Print</a></li>';

            $status .= '<li><a onclick="delete_item(' . $poID . ',\'Purchase Order\');"><span rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a>';
        }
        if ($POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\')" ><span rel="tooltip" class="glyphicon glyphicon-eye-open"></span> View</a></li>';
            if ($approved != 5 && $isDeleted==0) {
                $status .= '<li><a onclick=\'po_close("' . $poID . '"); \'><i rel="tooltip" class="fa fa-times" aria-hidden="true"></i> Close</a></li>';
            }

            $status .= '<li><a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation/') . '/' . $poID . '" ><span rel="tooltip" class="glyphicon glyphicon-print"></span> Print</a></li>';


        }
        if ($POConfirmedYN == 1 && $approved==1 && $isDeleted==0) {
           $status .= '<li><a onclick="po_version_View_modal(\'PO\',\'' . $poID . '\')"><i rel="tooltip" class="fa fa-files-o" aria-hidden="true"></i> Add Version</a></li>';
        }
        if(($approved == 1)||($approved == 5))
        {
            $status .= '<li><a onclick="sendemail(' . $poID . ')" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i> Send Mail</a></li><li><a onclick="traceDocument(' . $poID . ',\'PO\')" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i> Trace Document</a></li>';
        }

        $status .= '</ul></div>';
        return $status;
    }
}

if (!function_exists('load_po_action_version')) { /*get po action list on version load*/
    function load_po_action_version($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID,$versionID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PurchaseOrder = $CI->lang->line('common_purchase_order');/*"Purchase Order"*/
        $CI->load->library('session');
        $status = '<span class="pull-right">';
       
        if ($POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a target="_blank" onclick="documentPageView_modal_version(\'PO\',\'' . $versionID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';

        }

        $status .= '</span>';
        return $status;
    }
}



if (!function_exists('load_uom_action')) { /*get po action list*/
    function load_uom_action($UnitID, $desc, $code)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetch_umo_detail_con(' . $UnitID . ',"' . $desc . '","' . $code . '");\'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('po_action_approval')) { /*get po action list*/
    function po_action_approval($poID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PurchaseOrder1 = $CI->lang->line('common_purchase_order');/*"Purchase Order"*/
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$PurchaseOrder1.'","PO");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('po_confirm')) { /*get po action list*/
    function po_confirm($con)
    {
        $status = '<center>';
/*        if ($m_id) {
            if ($con == 0) {
                $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-danger">&nbsp;</span></a>';
            } elseif ($con == 1) {
                $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-success">&nbsp;</span></a>';
            } elseif ($con == 2) {
                $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-warning">&nbsp;</span></a>';
            } else {
                $status .= '-';
            }
        } else {*/
            if ($con == 0) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($con == 1) {
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

if (!function_exists('sold_to')) {
    function sold_to()
    {
        $CI =& get_instance();
        $CI->db->select('contactPerson,addressID,isDefault,addressType');
        $CI->db->from('srp_erp_address');
        $CI->db->join('srp_erp_addresstype', 'srp_erp_addresstype.addressTypeID = srp_erp_address.addressTypeID');
        $CI->db->where('srp_erp_address.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_addresstype.addressTypeDescription', 'Sold To');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Sold');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['addressID'] ?? '')] = trim($row['addressType'] ?? '') . ' - ' . trim($row['contactPerson'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('ship_to')) {
    function ship_to()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('contactPerson,addressID,isDefault,addressType,addressDescription');
        $CI->db->from('srp_erp_address');
        $CI->db->join('srp_erp_addresstype', 'srp_erp_addresstype.addressTypeID = srp_erp_address.addressTypeID');
        $CI->db->where('srp_erp_address.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_addresstype.addressTypeDescription', 'Ship To');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_ship')/*'Select Ship'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['addressID'] ?? '')] = trim($row['addressType'] ?? '') . ' | ' . trim($row['contactPerson'] ?? ''). ' | ' . trim($row['addressDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}


if (!function_exists('invoice_to')) {
    function invoice_to()
    {
        $CI =& get_instance();
        $CI->db->select('contactPerson,addressID,isDefault,addressType');
        $CI->db->from('srp_erp_address');
        $CI->db->join('srp_erp_addresstype', 'srp_erp_addresstype.addressTypeID = srp_erp_address.addressTypeID');
        $CI->db->where('srp_erp_address.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_addresstype.addressTypeDescription', 'Send Invoice To');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Invoice');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['addressID'] ?? '')] = trim($row['addressType'] ?? '') . ' - ' . trim($row['contactPerson'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_address_po')) {
    function fetch_address_po($id)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_address');
        $CI->db->where('addressID', $id);
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('po_total_value')) {
    function po_total_value($id, $decimal = 2)
    {
        $CI =& get_instance();
        $CI->db->select_sum('totalAmount');
        $CI->db->where('purchaseOrderID', $id);
        $totalAmount = $CI->db->get('srp_erp_purchaseorderdetails')->row('totalAmount');
        return number_format($totalAmount, $decimal);
    }
}

if (!function_exists('po_Recived')) { /*get po action list*/
    function po_Recived($isReceived,$closedYN)
    {
        $status = '<center>';
        /*        if ($m_id) {
                    if ($con == 0) {
                        $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-danger">&nbsp;</span></a>';
                    } elseif ($con == 1) {
                        $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-success">&nbsp;</span></a>';
                    } elseif ($con == 2) {
                        $status .= '<a href="#" onclick="procu(' . $m_id . ')"><span class="label label-warning">&nbsp;</span></a>';
                    } else {
                        $status .= '-';
                    }
                } else {*/
        if ($isReceived == 0) {
            $status .= '<span class="label label-danger" style="font-size: 9px;" title="Not Received" rel="tooltip">Not Received</span>';
        } else if ($isReceived == 1) {
            $status .= '<span class="label label-warning" style="font-size: 9px;" title="Partially Received" rel="tooltip">Partially Received</span>';
        } elseif ($isReceived == 2) {
            $status .= '<span class="label label-success" style="font-size: 9px;" title="Fully Received" rel="tooltip">Fully Received</span>';
        }

        $status .= '</center>';
        return $status;
    }
}
if (!function_exists('load_po_action_buyback')) { /*get po action list*/
    function load_po_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PurchaseOrder = $CI->lang->line('common_purchase_order');/*"Purchase Order"*/
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$PurchaseOrder.'","PO",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if($isDeleted==1){
            $status .= '<a onclick="reOpen_contract(' . $poID .');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referbackprocument(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a onclick=\'fetchPage("system/procurement/erp_purchase_order_new_buyback",' . $poID . ',"Edit Purchase Order","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            //$status .= '<a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="load_printtemp(' . $poID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';

            $status .= '<a onclick="delete_item(' . $poID . ',\'Purchase Order\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            if ($approved != 5 && $isDeleted==0) {
                $status .= '<a onclick=\'po_close("' . $poID . '"); \'><i title="Close" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            }
            //$status .= '<a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;';
            $status .= '<a onclick="load_printtemp(' . $poID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;';


        }
        if($approved == 1)
            if($approved == 1)
        {
            $status .= '| &nbsp;<a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'PO\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }

        $status .= '</span>';
        return $status;
    }
}
if (!function_exists('po_action_approval_buyback')) { /*get po action list*/
    function po_action_approval_buyback($poID, $Level, $approved, $ApprovedID, $isRejected,$approval=1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PurchaseOrder1 = $CI->lang->line('common_purchase_order');/*"Purchase Order"*/
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$PurchaseOrder1.'","PO");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\',\'buy\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_details')) {
    function load_details($narration,$supliermastername,$expectedDeliveryDate,$transactionCurrency,$purchaseOrderType,$documentDate,$purchaseOrderID)
    {
       $isRcmApplicable = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$purchaseOrderID);
       $rcmStatus = '';
       if($isRcmApplicable == 1){
           $rcmStatus.= '<span class="label label-danger" style="font-size: 9px;" title="Not Received" rel="tooltip">Reverse Charge Mechanism Activated</span>';
        }

        $status='<b>Supplier Name : </b> '.$supliermastername.' <br> <b>PO Date : </b> '.$documentDate.' <br> <b>Exp Delivery Date : </b> '.$expectedDeliveryDate.'  <b>&nbsp;&nbsp; Type : </b> '.$purchaseOrderType.'<br><b>Narration : </b> '.$narration.'<br>'.$rcmStatus;
        return $status;
    }
}

if (!function_exists('load_po_action_logistic')) { /*get po action list*/
    function load_po_action_logistic($poID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$confirmedByEmpID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $PurchaseOrder = $CI->lang->line('common_purchase_order');/*"Purchase Order"*/
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"'.$PurchaseOrder.'","PO",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a onclick="referbackprocument(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        $status .= '<a onclick=\'fetchPage("system/logistics/erp_purchase_order_new_logistic",' . $poID . ',"Edit Purchase Order","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
            $status .= '<a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;';
        }

        if ($POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PO\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
            $status .= '<a target="_blank" href="' . site_url('Procurement/load_purchase_order_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('get_purchase_order_master_record')) {
    function get_purchase_order_master_record($purchaseOrderID)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->where('purchaseOrderID', $purchaseOrderID);
        $masterRecord = $CI->db->get('srp_erp_purchaseordermaster')->row_array();
        return $masterRecord;
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

if (!function_exists('add_po_version_code')) { /*get po action code*/
    function add_po_version_code($code, $versionNo)
    {
        if($versionNo ==0){
            $status = $code;
        }else{
            $status = $code.'(V'.$versionNo.')';
        }
        
       
        return $status;
    }
}



