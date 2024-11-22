<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*Load all campaign status*/
if (!function_exists('get_all_company_details')) {
    function get_all_company_details($custom = true)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        
        $CI->db->select("*");
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', $companyID);
        $company_data   =  $CI->db->get()->row_array();

        return $company_data;
    }
}