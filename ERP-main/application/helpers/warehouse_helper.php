<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Na
 * Date: 4/17/2017
 * Time: 2:41 PM
 */
if (!function_exists('all_imanufacturing_glcode')) {
    function all_imanufacturing_glcode()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts coa');
        $CI->db->join('srp_erp_companycontrolaccounts controlaccounts', 'coa.GLAutoID = controlaccounts.GLAutoID');
        $CI->db->where('coa.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('controlaccounts.controlAccountType','WIP');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => ' Select GL Code');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = (trim($row['systemAccountCode'] ?? '') ) . ' | ' . trim($row['GLSecondaryCode'] ?? ''). ' | ' . trim($row['GLDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}