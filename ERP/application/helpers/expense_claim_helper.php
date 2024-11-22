<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_EC_action')) { /* get EC action list */
    function load_EC_action($EcID, $ECConfirmedYN, $approved, $createdUserID, $addedForPayment, $addedToSalary)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $ExpenseClaim =  $CI->lang->line('common_expense_claim'); /* Expense Claim */
        $CI->load->library('session');

        $dropdownItems = '';

        $dropdownItems .= '<li><a onclick=\'attachment_modal(' . $EcID . ',"' . $ExpenseClaim . '","EC",' . $ECConfirmedYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color:#4caf50;"></span> Attachment</a></li>';

        if ($createdUserID == trim($CI->session->userdata("empID")) && $approved == 0 && $ECConfirmedYN == 1) {
            $dropdownItems .= '<li><a onclick="referbackclaim(' . $EcID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        if ($ECConfirmedYN != 1) {
            $dropdownItems .= '<li><a onclick=\'fetchPage("system/expenseClaim/expense_claim_new",' . $EcID . ',"Edit Expense Claim","EC");\'><span class="glyphicon glyphicon-pencil" style="color:#116f5e;"></span> Edit</a></li>';
            $dropdownItems .= '<li><a onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" target="_blank"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';
            $dropdownItems .= '<li><a href="' . site_url('ExpenseClaim/load_expense_claim_conformation/') . '/' . $EcID . '" target="_blank"><span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';
            $dropdownItems .= '<li><a onclick="delete_item(' . $EcID . ',\'Expense Claim\');"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($addedForPayment == 0 && $addedToSalary == 0 && $approved == 1) {
            $dropdownItems .= '<li><a onclick="reviseClaim(' . $EcID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Revise</a></li>';
        }

        if ($ECConfirmedYN == 1) {
            $dropdownItems .= '<li><a onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" target="_blank"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';
            $dropdownItems .= '<li><a href="' . site_url('ExpenseClaim/load_expense_claim_conformation/') . '/' . $EcID . '" target="_blank"><span class="glyphicon glyphicon-print" style="color:#607d8b;"></span> Print</a></li>';
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
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Purchase Order","PO");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp; ';
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
        $CI->db->select('contactPerson,addressID,isDefault,addressType,addressDescription');
        $CI->db->from('srp_erp_address');
        $CI->db->join('srp_erp_addresstype', 'srp_erp_addresstype.addressTypeID = srp_erp_address.addressTypeID');
        $CI->db->where('srp_erp_address.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_addresstype.addressTypeDescription', 'Ship To');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Ship');
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

if (!function_exists('load_claim_category_action')) { /*get po action list*/
    function load_claim_category_action($EcID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick="editClaimCategory('. $EcID .')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp &nbsp|&nbsp &nbsp <a onclick="deleteClaimCategory('. $EcID .')"><span style="color:rgb(209, 91, 71);" title="Edit" rel="tooltip" class="glyphicon glyphicon-trash"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_EC_approval_action')) { /*get po action list*/
    function load_EC_approval_action($EcID, $ECConfirmedYN, $approved, $createdUserID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $EcID . ',"Expense Claim","EC");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if ($approved == 0) {
            $status .= '<a target="_blank" onclick="fetch_approval(\'' . $EcID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }else{
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('confirm_ap_EC')) {
    function confirm_ap_EC($approved_status, $confirmed_status, $code, $autoID)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 ) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($confirmed_status == 3) {
                $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else if ($confirmed_status == 2) {
                $status .= '<a onclick="fetch_approval_user_modal_ec(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            } else {
                $status .= '<a onclick="fetch_approval_user_modal_ec(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                //$status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            }
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a onclick="fetch_approval_user_modal_ec(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
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

if (!function_exists('confirm_aproval_EC')) {
    function confirm_aproval_EC($approved_status, $confirmed_status, $code, $autoID)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 ||  $confirmed_status == 3) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            }  else if ($confirmed_status == 2) {
                //$status .= '<a onclick="fetch_approval_user_modal_ec(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                $status .= '<a href="#" class="label label-danger">&nbsp;</a>';
            } else {
                //$status .= '<a onclick="fetch_approval_user_modal_ec(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
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

if (!function_exists('load_Exp_claim_master_action')) {
    function load_Exp_claim_master_action($EcID, $ECConfirmedYN, $approved, $createdUserID)
    {
        $CI =& get_instance();
        
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $ExpenseClaimlang = $CI->lang->line('common_expense_claim'); // Expense Claim

        $actionDropdown = '<div class="btn-group" style="display: flex;justify-content: center;">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                                Actions <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $actionDropdown .= '<li><a onclick=\'attachment_modal(' . $EcID . ',"' . $ExpenseClaimlang . '","EC",' . $ECConfirmedYN . ');\' title="Attachment" rel="tooltip"><span class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment</a></li>';

        if ($createdUserID == trim($CI->session->userdata("empID")) && $approved == 0 && $ECConfirmedYN == 1) {
            $actionDropdown .= '<li><a onclick="referbackclaim(' . $EcID . ');" title="Refer Back" rel="tooltip"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        if ($ECConfirmedYN != 1) {
            $actionDropdown .= '<li><a onclick=\'fetchPage("system/expenseClaim/expense_claim_new_hrms",' . $EcID . ',"Edit Expense Claim","EC");\' title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            $actionDropdown .= '<li><a target="_blank" onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" title="View" rel="tooltip"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';
            $actionDropdown .= '<li><a target="_blank" href="' . site_url('ExpenseClaim/load_expense_claim_conformation/') . '/' . $EcID . '" title="Print" rel="tooltip"><span class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a></li>';
            $actionDropdown .= '<li><a onclick="delete_item(' . $EcID . ',\'Expense Claim\');" title="Delete" rel="tooltip"><span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($ECConfirmedYN == 1) {
            $actionDropdown .= '<li><a target="_blank" onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" title="View" rel="tooltip"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';
            $actionDropdown .= '<li><a target="_blank" href="' . site_url('ExpenseClaim/load_expense_claim_conformation/') . '/' . $EcID . '" title="Print" rel="tooltip"><span class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a></li>';
        }

        $actionDropdown .= '</ul></div>';
        
        return $actionDropdown;
    }
}

if (!function_exists('load_EC_action_hrms')) { /*get po action list*/
    function load_EC_action_hrms($EcID, $ECConfirmedYN, $approved, $createdUserID)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $ExpenseClaimlang =  $CI->lang->line('common_expense_claim');/*Expense Claim*/
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $EcID . ',"'.$ExpenseClaimlang.'","EC",' . $ECConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $ECConfirmedYN == 1) {
            $status .= '<a onclick="referbackclaim(' . $EcID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if ($ECConfirmedYN != 1) {
            $status .= '<a onclick=\'fetchPage("system/expenseClaim/expense_claim_new_hrms",' . $EcID . ',"Edit Expense Claim","EC"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" href="' . site_url('ExpenseClaim/load_expense_claim_conformation/') . '/' . $EcID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="delete_item(' . $EcID . ',\'Expense Claim\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($ECConfirmedYN == 1 ) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            $status .= '<a target="_blank" href="' . site_url('ExpenseClaim/load_expense_claim_conformation/') . '/' . $EcID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('authority_gl_drop')) {
    function authority_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("srp_erp_chartofaccounts.*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->join('srp_erp_companycontrolaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_companycontrolaccounts.GLAutoID');
        $CI->db->where('srp_erp_companycontrolaccounts.controlAccountType', 'TAX');
        $CI->db->where('srp_erp_chartofaccounts.companyID ', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('authority_gl_drop_without_control_accounts')) {
    function authority_gl_drop_without_control_accounts()
    {
        $CI =& get_instance();
        $CI->db->SELECT("srp_erp_chartofaccounts.*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        
        $CI->db->where('srp_erp_chartofaccounts.companyID ', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_chartofaccounts.controllAccountYN', 0);
        $CI->db->where('srp_erp_chartofaccounts.isActive', 1);
        $CI->db->where_in('subCategory', array("BSL", "BSA"));
        $CI->db->where_in('accountCategoryTypeID', array("3", "8")); // 3 - Other Current Asset // 8 - Other Current Liability
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_chart_of_accounts')) {
    function all_chart_of_accounts()
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('all_authority_drop')) {
    function all_authority_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("taxAuthourityMasterID,AuthorityName,authoritySystemCode");
        $CI->db->from('srp_erp_taxauthorithymaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => 'Select Authority');
        } else {
            $supplier_arr = [];
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['taxAuthourityMasterID'] ?? '')] = (trim($row['authoritySystemCode'] ?? '') ? trim($row['authoritySystemCode'] ?? '') . ' | ' : '') . trim($row['AuthorityName'] ?? '');
            }
        }

        return $supplier_arr;
    }
}




