<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_prvr_action')) { /*get po action list*/
    function load_prvr_action($payVoucherAutoId,$reversed)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
            //$status .= '<a onclick="payment_revers(' . $payVoucherAutoId .');"><span title="Payment Revers" rel="tooltip" class="glyphicon glyphicon-repeat" "></span></a>&nbsp;&nbsp;';
            $status .= '<a onclick=\'fetch_payment_reversal_view("' . $payVoucherAutoId . '",' . $reversed . '); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('bank_accounts_drop')) {
    function bank_accounts_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("GLAutoID,bankName,bankBranch,bankSwiftCode,bankAccountNumber,subCategory,isCash");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 1);
        $CI->db->where('isCash', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select Bank Account');
        if (isset($bank)) {
            foreach ($bank as $row) {
                $type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['bankName'] ?? '') . ' | ' . trim($row['bankBranch'] ?? '') . ' | ' . trim($row['bankSwiftCode'] ?? '') . ' | ' . trim($row['bankAccountNumber'] ?? '') . ' | ' . trim($row['subCategory'] ?? '') . $type;
            }
        }

        return $bank_arr;
    }
}

if (!function_exists('get_total_amount')) {
    function get_total_amount($amount, $taxPercentage, $detailCurrency,$decimal)
    {
        $tax=($amount/100)*$taxPercentage;
        $total=$amount+$tax;
        $totalAmount= $detailCurrency.' : '. number_format($total, $decimal);;
        $status = '<span class="pull-right">' . $totalAmount . ' </span>';
        return $status;
    }
}

