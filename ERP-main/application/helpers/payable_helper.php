<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('supplier_invoice_total_value')) {
    function supplier_invoice_total_value($id, $DecimalPlaces = 2)
    {
        $tax = 0;
        $CI =& get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('InvoiceAutoID', $id);
        $totalAmount = $CI->db->get('srp_erp_paysupplierinvoicedetail')->row('transactionAmount');
        $CI->db->select('taxPercentage');
        $CI->db->where('InvoiceAutoID', $id);
        $data_arr = $CI->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
        for ($i = 0; $i < count($data_arr); $i++) {
            $tax += (($data_arr[$i]['taxPercentage'] / 100) * $totalAmount);
        }
        $totalAmount += $tax;
        return number_format($totalAmount, $DecimalPlaces);
    }
}

if (!function_exists('get_all_purchase_order_active_list')) {
    function get_all_purchase_order_active_list($id, $DecimalPlaces = 2)
    {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->where('approvedYN', 1);
        $CI->db->from('srp_erp_purchaseordermaster as po`');
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('db_invoice_total_value')) {
    function db_invoice_total_value($id, $DecimalPlaces = 2)
    {
        $CI =& get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('debitNoteMasterAutoID', $id);
        $totalAmount = $CI->db->get('srp_erp_debitnotedetail')->row('transactionAmount');
        return number_format($totalAmount, $DecimalPlaces);
    }
}


if (!function_exists('payment_voucher_total_value')) {
    function payment_voucher_total_value($id, $DecimalPlaces = 2, $status = 1)
    {
        $tax = 0;
        $CI =& get_instance();
        /*$CI->db->select_sum('transactionAmount');
        $CI->db->where('payVoucherAutoId', $id);
        $totalAmount = $CI->db->get('srp_erp_paymentvoucherdetail')->row('transactionAmount');
        $CI->db->select('taxPercentage');
        $CI->db->where('payVoucherAutoId', $id);
        $data_arr = $CI->db->get('srp_erp_paymentvouchertaxdetails')->result_array();
        for ($i = 0; $i < count($data_arr); $i++) {
            $tax += (($data_arr[$i]['taxPercentage'] / 100) * $totalAmount);
        }
        $totalAmount += $tax;*/

        $totalAmount = $CI->db->query("SELECT
                (((IFNULL(addondet.taxPercentage, 0) / 100) * IFNULL(tyepdet.transactionAmount,0)) + IFNULL(det.transactionAmount, 0) - IFNULL(cus_inv.transactionAmount,0) - IFNULL(debitnote.transactionAmount,0) - IFNULL(income_amount.transactionAmount,0)) AS transactionAmount
                FROM
                    `srp_erp_paymentvouchermaster`
                LEFT JOIN (
                    SELECT
                        SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount,
                        payVoucherAutoId
                    FROM
                        srp_erp_paymentvoucherdetail
                    WHERE
                        srp_erp_paymentvoucherdetail.type NOT IN ('debitnote', 'SR','INGL')
                        AND srp_erp_paymentvoucherdetail.detailInvoiceType IS NULL
                    GROUP BY
                        payVoucherAutoId
                ) det ON (
                    `det`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
                )
                LEFT JOIN (
                    SELECT
                        SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount,
                        payVoucherAutoId
                    FROM
                        srp_erp_paymentvoucherdetail
                    WHERE
                        srp_erp_paymentvoucherdetail.detailInvoiceType = 'CUS'
                    GROUP BY
                        payVoucherAutoId
                ) cus_inv ON (
                    `cus_inv`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
                )
                LEFT JOIN (
                    SELECT
                        SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount,
                        payVoucherAutoId
                    FROM
                        srp_erp_paymentvoucherdetail
                    WHERE
                        srp_erp_paymentvoucherdetail.type = 'GL'
                    OR srp_erp_paymentvoucherdetail.type = 'Item'
                    GROUP BY
                        payVoucherAutoId
                ) tyepdet ON (
                    `tyepdet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
                )
                LEFT JOIN (
                    SELECT
                        SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount,
                        payVoucherAutoId
                    FROM
                        srp_erp_paymentvoucherdetail
                    WHERE
                        srp_erp_paymentvoucherdetail.type IN ( 'debitnote', 'SR')
                    GROUP BY
                        payVoucherAutoId
                ) debitnote ON (
                    `debitnote`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
                )  LEFT JOIN (
                    SELECT
                        SUM(transactionAmount) AS transactionAmount,
                        payVoucherAutoId
                    FROM
                        srp_erp_paymentvoucherdetail
                    WHERE
                        srp_erp_paymentvoucherdetail.type IN ('INGL')
                    GROUP BY
                        payVoucherAutoId
                ) income_amount ON (
                    `income_amount`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
                )
                LEFT JOIN (
                    SELECT
                        SUM(taxPercentage) AS taxPercentage,
                        payVoucherAutoId
                    FROM
                        srp_erp_paymentvouchertaxdetails
                    GROUP BY
                        payVoucherAutoId
                ) addondet ON (
                    `addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId
                )
                WHERE
                    `srp_erp_paymentvouchermaster`.`payVoucherAutoId` = $id
                ")->row('transactionAmount');/*AND `pvType` <> 'SC'*/


        if ($status) {
            return number_format($totalAmount, $DecimalPlaces);
        } else {
            return $totalAmount;
        }
    }
}

if (!function_exists('payment_match_total_value')) {
    function payment_match_total_value($id, $DecimalPlaces = 2, $status = 1)
    {
        $CI =& get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('matchID', $id);
        $totalAmount = $CI->db->get('srp_erp_pvadvancematchdetails')->row('transactionAmount');
        if ($status) {
            return number_format($totalAmount, $DecimalPlaces);
        } else {
            return $totalAmount;
        }
    }
}

if (!function_exists('receipt_match_total_value')) {
    function receipt_match_total_value($id, $DecimalPlaces = 2, $status = 1)
    {
        $CI =& get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('matchID', $id);
        $totalAmount = $CI->db->get('srp_erp_rvadvancematchdetails')->row('transactionAmount');
        if ($status) {
            return number_format($totalAmount, $DecimalPlaces);
        } else {
            return $totalAmount;
        }
    }
}

if (!function_exists('payable_confirm')) {
    function payable_confirm($con, $code, $autoID)
    {
        $status = '<center>';
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

if (!function_exists('load_supplier_invoice_action')) {
    function load_supplier_invoice_action($masterID, $ConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp, $isSytemGenerated, $totalRetension = null)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $masterID . ',"Supplier Invoice","BSI",' . $ConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip glyphicon-paperclip-btn" style="color: #4caf50;"></span> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $masterID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if ($ConfirmedYN != 1 && $isDeleted == 0) {
            if ($isSytemGenerated != 1) {
                $status .= '<li><a onclick=\'fetchPage("system/accounts_payable/erp_supplier_invoices",' . $masterID . ',"Edit Supplier Invoice","BSI");\'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            } else {
                $status .= '<li><a onclick=\'issystemgenerateddoc();\'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $approved == 0 && $ConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referbacksupplierinvoice(' . $masterID . ',' . $isSytemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'BSI\',\'' . $masterID . '\')"><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Payable/load_supplier_invoice_conformation/') . '/' . $masterID . '"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a></li>';

        if ($ConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="confirmSupplierInvoicefront(' . $masterID . ');"><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok" style="color: #4caf50;"></span> Confirm</a></li>';
            $status .= '<li><a onclick="delete_supplier_invoice(' . $masterID . ',\'Supplier Invoice\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a onclick="traceDocument(' . $masterID . ',\'BSI\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i> Trace Document</a></li>';
        }

        if ($approved == 1 && get_pv_rv_based_on_policy('PV')) {
            $status .= '<li><a onclick="open_payent_voucher_modal(' . $masterID . ')" title="Create Payment Voucher" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i> Create Payment Voucher</a></li>';
        }

        $retensionEnabled = getPolicyValues('RETO', 'All');
        if ($approved == 1 && $retensionEnabled == 1 && $totalRetension > 0) {
            $status .= '<li><a onclick="Retension_model(' . $masterID . ')" title="Create Retension Invoice" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i> Create Retension Invoice</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_supplier_invoice_action_buyback')) { /*get po action list*/
    function load_supplier_invoice_action_buyback($masterID, $ConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp, $isSytemGenerated)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $masterID . ',"Supplier Invoice","BSI",' . $ConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $masterID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($ConfirmedYN != 1 && $isDeleted == 0) {
            if ($isSytemGenerated != 1) {
                $status .= '<a onclick=\'fetchPage("system/accounts_payable/erp_supplier_invoices",' . $masterID . ',"Edit Supplier Invoice","BSI"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            } else {
                $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $ConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referbacksupplierinvoice(' . $masterID . ',' . $isSytemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'BSI\',\'' . $masterID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        //$status .= '<a target="_blank" href="' . site_url('Payable/load_supplier_invoice_conformation/') . '/' . $masterID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '<a target="_blank" onclick="load_printtemp(' . $masterID . ')" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($ConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmSupplierInvoicefront(' . $masterID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_supplier_invoice(' . $masterID . ',\'Supplier Invoice\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $outputs = get_pv_rv_based_on_policy('PV');
        if ($approved == 1) {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $masterID . ',\'BSI\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }

        if ($approved == 1 && $outputs) {
            $status .= ' &nbsp; | &nbsp; <a onclick="open_payent_voucher_modal(' . $masterID . ')" title="Create Payment Voucher" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i></a>';
        }

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('si_confirm')) {
    function si_confirm($con, $m_id = null)
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

if (!function_exists('supplier_invoice_action_approval')) {
    function supplier_invoice_action_approval($InvoiceAutoID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $SupplierInvoiceAttachments = $CI->lang->line('common_supplier_invoice_attachments');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $InvoiceAutoID . ',"' . $SupplierInvoiceAttachments . '","BSI");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; ';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $InvoiceAutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';

        } else {
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'BSI\',\'' . $InvoiceAutoID . '\',\'\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        // $status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Payable/load_supplier_invoice_conformation/') . '/' . $InvoiceAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('dn_confirm')) {
    function dn_confirm($con, $code, $autoID)
    {
        $status = '<center>';
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

if (!function_exists('pv_confirm')) {
    function pv_confirm($con, $code, $autoID)
    {
        $status = '<center>';
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

if (!function_exists('load_Debit_note_action')) {
    function load_Debit_note_action($dnID, $dnConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $dnID . ',"Debit Note","DN",' . $dnConfirmedYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $dnID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if ($dnConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick=\'fetchPage("system/accounts_payable/erp_debit_note",' . $dnID . ',"Edit Debit Note","DN");\'><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $approved == 0 && $dnConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referbackdn(' . $dnID . ');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'DN\',\'' . $dnID . '\')"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Payable/load_dn_conformation/') . '/' . $dnID . '"><span class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a></li>';

        if ($dnConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="confirmDebitNotefront(' . $dnID . ');"><span class="glyphicon glyphicon-ok" style="color: #4caf50;"></span> Confirm</a></li>';
            $status .= '<li><a onclick="delete_item(' . $dnID . ',\'Debit Note\');"><span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a onclick="traceDocument(' . $dnID . ', \'DN\')"><i class="fa fa-search" aria-hidden="true" style="color: #fdc45e"></i> Trace Document</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_pvm_action')) {
    function load_pvm_action($pvID, $pvConfirmedYN, $isDeleted, $confirmedByEmp, $createdUserID)
    {
        $CI =& get_instance();
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $pvID . ',"Payment Match","PVM",' . $pvConfirmedYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $pvID . ');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if ($pvConfirmedYN == 0 || ($pvConfirmedYN == 3 && $isDeleted == 0)) {
            $status .= '<li><a onclick=\'fetchPage("system/payment_voucher/erp_payment_match",' . $pvID . ',"Edit Payment Matching","PVM");\'><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            $status .= '<li><a onclick="delete_pvm_item(' . $pvID . ',\'Payment Voucher\');"><span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $pvConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referbackPaymentMatch(' . $pvID . ');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'PVM\',\'' . $pvID . '\')"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';
        $status .= '<li><a target="_blank" href="' . site_url('Payment_voucher/load_pv_match_conformation/') . '/' . $pvID . '"><span class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a></li>';

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_rvm_action')) {
    function load_rvm_action($pvID, $pvConfirmedYN, $isDeleted, $confirmedByEmp, $createdUserID, $matchinvoiceAutoID)
    {
        $CI =& get_instance();
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $pvID . ',"Receipt Matching","RVM",' . $pvConfirmedYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $pvID . ');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            if ($matchinvoiceAutoID) {
                $status .= '<li><a onclick=\'issystemgenerateddoc_rvm("Edit");\'><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            } else {
                $status .= '<li><a onclick=\'fetchPage("system/receipt_voucher/erp_receipt_match",' . $pvID . ',"Edit Receipt Matching","RVM");\'><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            }
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'RVM\',\'' . $pvID . '\')"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Receipt_voucher/load_rv_match_conformation/') . '/' . $pvID . '"><span class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a></li>';

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            if ($matchinvoiceAutoID) {
                $status .= '<li><a onclick="issystemgenerateddoc_rvm(\'Delete\');"><span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
            } else {
                $status .= '<li><a onclick="delete_rvm_item(' . $pvID . ',\'Receipt Voucher\');"><span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
            }
        } else {
            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $pvConfirmedYN == 1 && $isDeleted == 0) {
                if ($matchinvoiceAutoID) {
                    $status .= '<li><a onclick="issystemgenerateddoc_rvm(\'Refer Back\');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
                } else {
                    $status .= '<li><a onclick="referbackReceiptMatch(' . $pvID . ');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
                }
            }
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_pv_action')) {
    function load_pv_action($pvID, $pvConfirmedYN, $approved, $createdUserID, $documentID, $isDeleted, $bankGLAutoID, $paymentType, $pvtype, $confirmedByEmp, $isSytemGenerated, $sub_invoices = null, $customer_amount = null)
    {
        $printChequeBeforeApproval = getPolicyValues('CHA', 'All');
        if ($printChequeBeforeApproval == ' ' || $printChequeBeforeApproval == null) {
            $printChequeBeforeApproval = 0;
        }

        $CI =& get_instance();
        $CI->db->select('isCash');
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $isCash = $CI->db->get('srp_erp_chartofaccounts')->row_array();

        $CI->db->select('coaChequeTemplateID');
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $CI->db->where('companyID', current_companyID());
        $templateexist = $CI->db->get('srp_erp_chartofaccountchequetemplates')->row_array();

        $CI->db->select('COUNT(`srp_erp_chartofaccountchequetemplates`.`coaChequeTemplateID`) as templateCount');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $CI->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $CI->db->from('srp_erp_chartofaccountchequetemplates');
        $count = $CI->db->get()->row_array();

        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $pvID . ',"Payment Voucher","PV",' . $pvConfirmedYN . ');\'><i class="glyphicon glyphicon-paperclip"></i> Attachment</a></li>';

        if ($printChequeBeforeApproval == 1) {
            if ($isCash['isCash'] == 0 && !empty($templateexist) && $paymentType == 1) {
                $status .= '<li><a onclick=cheque_print_modal(' . $pvID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i class="fa fa-cc"></i> Cheque Print</a></li>';
            }
        } else {
            if (is_array($isCash) && $isCash['isCash'] == 0 && $approved == 1 && !empty($templateexist) && $paymentType == 1) {
                $status .= '<li><a onclick=cheque_print_modal(' . $pvID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i class="fa fa-cc"></i> Cheque Print</a></li>';
            }
        }

        if (is_array($isCash) && $isCash['isCash'] != 1 && $approved == 1 && $paymentType == 2) {
            $status .= '<li><a target="_blank" href="' . site_url('Payment_voucher/load_pv_bank_transfer/') . '/' . $pvID . '" ><i class="glyphicon glyphicon-file"></i> Bank Transfer</a></li>';
        }

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $pvID . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Re Open</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a onclick="sendemail_pv(' . $pvID . ')" title="Send Mail"><i class="fa fa-envelope"></i> Send Mail</a></li>';
        }

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            if ($documentID == "PV") {
                if ($isSytemGenerated != 1) {
                    $status .= '<li><a onclick=\'fetchPage("system/payment_voucher/erp_payment_voucher",' . $pvID . ',"Edit Payment Voucher","PV"); \'><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>';
                } else {
                    $status .= '<li><a onclick=\'issystemgenerateddoc(); \'><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>';
                }
            } else {
                if ($isSytemGenerated != 1) {
                    $status .= '<li><a onclick=\'fetchPage("system/sales/commision_payment_new",' . $pvID . ',"Edit Commission Payment","PV"); \'><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>';
                } else {
                    $status .= '<li><a onclick=\'issystemgenerateddoc(); \'><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>';
                }
            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $pvConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referbackgrv(' . $pvID . ',' . $isSytemGenerated . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'PV\',\'' . $pvID . '\',\'UOM\')" ><i class="glyphicon glyphicon-eye-open"></i> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Payment_voucher/load_pv_conformation/') . '/' . $pvID . '" ><i class="glyphicon glyphicon-print"></i> Print</a></li>';

        if ($sub_invoices) {
            $status .= '<li><a target="_blank" href="' . site_url('Payment_voucher/load_sub_pv_allocation/') . '/' . $pvID . '" ><i class="glyphicon glyphicon-print"></i> Allocation</a></li>';
        }

        if ($customer_amount > 0 && $approved == 1) {
            $status .= '<li><a onclick="generate_receipt_voucher(' . $pvID . ');"><i class="glyphicon glyphicon-send"></i> Generate Receipt Voucher</a></li>';
        }

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="delete_pv_item(' . $pvID . ',\'Payment Voucher\');"><i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}


if (!function_exists('load_pv_action_buyback')) {
    function load_pv_action_buyback($pvID, $pvConfirmedYN, $approved, $createdUserID, $documentID, $isDeleted, $bankGLAutoID, $paymentType, $pvtype, $confirmedByEmp, $isSytemGenerated)
    {
        $printChequeBeforeApproval = getPolicyValues('CHA', 'All');
        if ($printChequeBeforeApproval == ' ' || $printChequeBeforeApproval == null) {
            $printChequeBeforeApproval = 0;
        }

        $CI =& get_instance();
        $CI->db->select('isCash');
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $isCash = $CI->db->get('srp_erp_chartofaccounts')->row_array();

        $CI->db->select('coaChequeTemplateID');
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $CI->db->where('companyID', current_companyID());
        $templateexist = $CI->db->get('srp_erp_chartofaccountchequetemplates')->row_array();

        $CI->db->select('COUNT(`srp_erp_chartofaccountchequetemplates`.`coaChequeTemplateID`) as templateCount');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $CI->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $CI->db->from('srp_erp_chartofaccountchequetemplates');
        $count = $CI->db->get()->row_array();

        $CI->load->library('session');
        $status = '<span class="pull-right d-flex>';
        $status .= '<a onclick=\'attachment_modal(' . $pvID . ',"Payment Voucher","PV",' . $pvConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';

        if ($printChequeBeforeApproval == 1) {
            if ($isCash['isCash'] == 0 && !empty($templateexist) && $paymentType == 1) {
                $status .= '<a onclick=cheque_print_modal(' . $pvID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i title="Cheque Print" rel="tooltip" class="fa fa-cc" aria-hidden="true"></i></a>&nbsp;&nbsp;';
            }
        } else {
            if ($isCash['isCash'] == 0 && $approved == 1 && !empty($templateexist) && $paymentType == 1) {
                $status .= '<a onclick=cheque_print_modal(' . $pvID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i title="Cheque Print" rel="tooltip" class="fa fa-cc" aria-hidden="true"></i></a>&nbsp;&nbsp;';
            }
        }
        /*if($isCash['isCash'] ==0 && $approved==1 && !empty($templateexist) && $paymentType==1 ){
                $status .= '<a onclick=cheque_print_modal(' . $pvID . ','.$count['templateCount'].','.$templateexist['coaChequeTemplateID'].'); ><i title="Cheque Print" rel="tooltip" class="fa fa-cc" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }*/
        if ($isCash['isCash'] != 1 && $approved == 1 && $paymentType == 2) {
            $status .= '<a target="_blank" href="' . site_url('Payment_voucher/load_pv_bank_transfer/') . '/' . $pvID . '" ><span title="Bank Transfer Letter" rel="tooltip" class="glyphicon glyphicon-file"></span></a>&nbsp;&nbsp;';
        }
        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $pvID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            if ($documentID == "PV") {
                if ($isSytemGenerated != 1) {
                    $status .= '<a onclick=\'fetchPage("system/payment_voucher/erp_payment_voucher",' . $pvID . ',"Edit Payment Voucher","PV"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                } else {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                }

            } else {
                if ($isSytemGenerated != 1) {
                    $status .= '<a onclick=\'fetchPage("system/sales/commision_payment_new",' . $pvID . ',"Edit Commission Payment","PV"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                } else {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                }

            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $pvConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referbackgrv(' . $pvID . ',' . $isSytemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'PV\',\'' . $pvID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';

        //$status .= '<a target="_blank" href="' . site_url('Payment_voucher/load_pv_conformation_buyback/') . '/' . $pvID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '<a target="_blank" onclick="load_printtemp(' . $pvID . ')" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;<a onclick="delete_pv_item(' . $pvID . ',\'Payment Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('dn_action_approval')) { /*get po action list*/
    function dn_action_approval($debitNoteMasterAutoID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $DebitNoteAttachments = $CI->lang->line('common_debit_note_attachments'); /*Debit Note Attachments*/
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $debitNoteMasterAutoID . ',"' . $DebitNoteAttachments . '","DN");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $debitNoteMasterAutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'DN\',\'' . $debitNoteMasterAutoID . '\',\'\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;&nbsp;';
        }


        //$status .= '|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Payable/load_dn_conformation/') . '/' . $debitNoteMasterAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';


        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('pv_confirm')) {
    function pv_confirm($con)
    {
        $status = '<center>';
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

if (!function_exists('pv_action_approval')) { /*get po action list*/
    function pv_action_approval($PayVoucherAutoId, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $PayVoucherAutoId . ',"Payment Voucher","PV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>';
        if ($approved == 0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $PayVoucherAutoId . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        } else {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'PV\',\'' . $PayVoucherAutoId . '\',\'UOM\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Payment_voucher/load_pv_conformation/') . '/' . $PayVoucherAutoId . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('load_pv_action_som')) {
    function load_pv_action_som($pvID, $pvConfirmedYN, $approved, $createdUserID, $documentID, $isDeleted, $bankGLAutoID, $paymentType, $pvtype, $confirmedByEmp, $isSytemGenerated)
    {
        $CI =& get_instance();
        $CI->db->select('isCash');
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $isCash = $CI->db->get('srp_erp_chartofaccounts')->row_array();

        $CI->db->select('coaChequeTemplateID');
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $CI->db->where('companyID', current_companyID());
        $templateexist = $CI->db->get('srp_erp_chartofaccountchequetemplates')->row_array();

        $CI->db->select('COUNT(`srp_erp_chartofaccountchequetemplates`.`coaChequeTemplateID`) as templateCount');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $CI->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $CI->db->from('srp_erp_chartofaccountchequetemplates');
        $count = $CI->db->get()->row_array();

        $CI->load->library('session');
        $status = '<span class="pull-right d-flex">';
        $status .= '<a onclick=\'attachment_modal(' . $pvID . ',"Payment Voucher","PV",' . $pvConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';

        if ($isCash['isCash'] == 0 && $approved == 1 && !empty($templateexist) && $paymentType == 1) {
            $status .= '<a onclick=cheque_print_modal(' . $pvID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i title="Cheque Print" rel="tooltip" class="fa fa-cc" aria-hidden="true"></i></a>&nbsp;&nbsp;';
        }
        if ($isCash['isCash'] != 1 && $approved == 1 && $paymentType == 2) {
            $status .= '<a target="_blank" href="' . site_url('Payment_voucher/load_pv_bank_transfer/') . '/' . $pvID . '" ><span title="Bank Transfer Letter" rel="tooltip" class="glyphicon glyphicon-file"></span></a>&nbsp;&nbsp;';
        }
        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $pvID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            if ($documentID == "PV") {
                if ($isSytemGenerated != 1) {
                    $status .= '<a onclick=\'fetchPage("system/payment_voucher/erp_payment_voucher_suom",' . $pvID . ',"Edit Payment Voucher","PV"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                } else {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                }

            } else {
                if ($isSytemGenerated != 1) {
                    $status .= '<a onclick=\'fetchPage("system/sales/commision_payment_new",' . $pvID . ',"Edit Commission Payment","PV"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                } else {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
                }

            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $pvConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referbackgrv(' . $pvID . ',' . $isSytemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'PV\',\'' . $pvID . '\',\'SUOM\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';

        $status .= '<a target="_blank" href="' . site_url('Payment_voucher/load_pv_conformation_suom/') . '/' . $pvID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;<a onclick="delete_pv_item(' . $pvID . ',\'Payment Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('pv_action_approval_suom')) { /*get po action list*/
    function pv_action_approval_suom($PayVoucherAutoId, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $PayVoucherAutoId . ',"Payment Voucher","PV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>';
        if ($approved == 0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $PayVoucherAutoId . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        } else {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'PV\',\'' . $PayVoucherAutoId . '\',\'SUOM\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Payment_voucher/load_pv_conformation/') . '/' . $PayVoucherAutoId . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_supplier_invoice_action_suom')) { /*get po action list*/
    function load_supplier_invoice_action_suom($masterID, $ConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $masterID . ',"Supplier Invoice","BSI",' . $ConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $masterID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($ConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/accounts_payable/erp_supplier_invoices_suom",' . $masterID . ',"Edit Supplier Invoice","BSI"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $ConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referbacksupplierinvoice(' . $masterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'BSI\',\'' . $masterID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        $status .= '<a target="_blank" href="' . site_url('Payable/load_supplier_invoice_conformation/') . '/' . $masterID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($ConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmSupplierInvoicefront(' . $masterID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_supplier_invoice(' . $masterID . ',\'Supplier Invoice\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:rgb(209, 91, 71);"></span></a>';
        }

        if ($approved == 1) {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $masterID . ',\'BSI\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('set_chequeNo')) {
    function set_chequeNo($paymentType, $chequeNo, $modeOfPayment)
    {
        $status = '';
        if ($paymentType == 1 && $modeOfPayment == 2) {
            $status .= '<b>&nbsp;&nbsp; Cheque No : </b>' . $chequeNo;
        } else {
            $status .= '';
        }
        return $status;
    }
}

if (!function_exists('load_Debit_note_action_buyback')) {
    function load_Debit_note_action_buyback($dnID, $dnConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $dnID . ',"Debit Note","DN",' . $dnConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;';

        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $dnID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($dnConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/accounts_payable/erp_debit_note",' . $dnID . ',"Edit Debit Note","DN"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $dnConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referbackdn(' . $dnID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        $status .= '<a target="_blank" onclick="documentPageView_modal(\'DN\',\'' . $dnID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="load_printtemp_DN(' . $dnID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        // $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Payable/load_dn_conformation/') . '/' . $dnID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';

        if ($dnConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmDebitNotefront(' . $dnID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $dnID . ',\'Debit Note\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($approved == 1) {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $dnID . ', \'DN\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_rvm_action_buyback')) {
    function load_rvm_action_buyback($pvID, $pvConfirmedYN, $isDeleted, $confirmedByEmp, $createdUserID, $matchinvoiceAutoID)
    {
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $pvID . ',"Receipt Matching","RVM",' . $pvConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;';
        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $pvID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($pvConfirmedYN != 1 && $isDeleted == 0) {
            if ($matchinvoiceAutoID) {
                $status .= '<a onclick=\'issystemgenerateddoc_rvm("Edit"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            } else {
                $status .= '<a onclick=\'fetchPage("system/receipt_voucher/erp_receipt_match",' . $pvID . ',"Edit Receipt Matching","RVM"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            }


            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'RVM\',\'' . $pvID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            //$status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Receipt_voucher/load_rv_match_conformation/') . '/' . $pvID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="load_printtemp(' . $pvID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp; ';

            if ($matchinvoiceAutoID) {
                $status .= '<a onclick="issystemgenerateddoc_rvm(\'Delete\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            } else {
                $status .= '<a onclick="delete_rvm_item(' . $pvID . ',\'Receipt Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }

        } else {
            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $pvConfirmedYN == 1 && $isDeleted == 0) {
                if ($matchinvoiceAutoID) {
                    $status .= '<a onclick="issystemgenerateddoc_rvm(\'Refer Back\');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
                } else {
                    $status .= '<a onclick="referbackReceiptMatch(' . $pvID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
                }
            }


            $status .= '<a target="_blank" onclick="documentPageView_modal(\'RVM\',\'' . $pvID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
            //$status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Receipt_voucher/load_rv_match_conformation') . '/' . $pvID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
            $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="load_printtemp(' . $pvID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp; ';

        }
        $status .= '</span>';
        return $status;
    }
}
if (!function_exists('load_pvm_action_buyback')) {
    function load_pvm_action_buyback($pvID, $pvConfirmedYN, $isDeleted, $confirmedByEmp, $createdUserID)
    {
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $pvID . ',"Payment Match","PVM",' . $pvConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;';
        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $pvID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';

        }
        if ($pvConfirmedYN == 0 || $pvConfirmedYN == 3 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/payment_voucher/erp_payment_match",' . $pvID . ',"Edit Payment Matching","PVM"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            //$status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'PVM\',\'' . $pvID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
            //$status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target="_blank" href="' . site_url('Payment_voucher/load_pv_match_conformation/') . '/' . $pvID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="delete_pvm_item(' . $pvID . ',\'Payment Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $pvConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referbackPaymentMatch(' . $pvID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }
        $status .= '| &nbsp;<a target="_blank" onclick="documentPageView_modal(\'PVM\',\'' . $pvID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        //$status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Payment_voucher/load_pv_match_conformation') . '/' . $pvID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="load_printtemp(' . $pvID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp; ';

        $status .= '</span>';
        return $status;
    }
}
if (!function_exists('fetch_bsi_details')) {
    function fetch_bsi_details($comments, $suppliermastername, $bookingDate, $transactionCurrency, $invoiceType, $invoiceDueDate, $supplierInvoiceNo, $supplierInvoiceDate, $RefNo, $InvoiceAutoID)
    {
        $isRcmApplicable = isRcmApplicable('srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', $InvoiceAutoID);
        $rcmStatus = '';
        if ($isRcmApplicable == 1) {
            $rcmStatus .= '<span class="label label-danger" style="font-size: 9px;" title="Not Received" rel="tooltip">Reverse Charge Mechanism Activated</span>';
        }

        $status = '
        <b>Supplier Name : </b> '.$suppliermastername.' <br> <b>Document Date : </b> '.$bookingDate.' &nbsp;&nbsp; | &nbsp;&nbsp;<b>Invoice Due Date : </b> '.$invoiceDueDate.' <br>
        <b> Supplier Invoice No : </b> '.$supplierInvoiceNo.' &nbsp;&nbsp; | &nbsp;&nbsp;<b>Supplier Invoice Date : </b> '.$supplierInvoiceDate.'<br>
        <b> Type : </b> '.$invoiceType.' &nbsp;&nbsp; |&nbsp;&nbsp; <b> Ref No : </b> '.$RefNo.' <br> <b>Narration : </b> '.$comments.' 
        <br>' . $rcmStatus;

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