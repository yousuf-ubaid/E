<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('reversing_fetch_all_gl_codes')) { 
    function reversing_fetch_all_gl_codes()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', current_companyID());
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


if (!function_exists('reversing_get_segemnt')) {
    function reversing_get_segemnt($segmentID = null)
    {

        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
     
        $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);
       
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}



if (!function_exists('reversing_get_gl_record')) {
    function reversing_get_gl_record($glAutoid = null)
    {

        $CI = &get_instance();
     
        $CI->db->select('*');
        $CI->db->from('srp_erp_generalledger');
        $CI->db->where('generalLedgerAutoID', $glAutoid);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('reversing_fetch_split_record')) {
    function reversing_fetch_split_record($document_code)
    {

        $CI = &get_instance();
     
        $CI->db->select('*');
        $CI->db->from('srp_erp_reversal_documentsplit');
        $CI->db->where('document_id', $document_code);
        $CI->db->where('company_id', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->row_array();

        return $data;
    }
}




?>