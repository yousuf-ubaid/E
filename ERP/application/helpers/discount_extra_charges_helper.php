<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('discountType')) {
    function discountType($type)
    {

        $status = '<center>';
        if ($type == 1) {
            $status .= 'Discount';
        } elseif ($type == 2) {
            $status .= 'Extra Charges';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('glCodeDesc')) {
    function glCodeDesc($GLDescription,$systemAccountCode,$GLSecondaryCode)
    {
        $status = '';
        if (!empty($systemAccountCode)) {
            $status .= $systemAccountCode .' | ' .$GLDescription .' | ' .$GLSecondaryCode;
        }
        return $status;
    }
}

if (!function_exists('action_disc_and_extra')) {
    function action_disc_and_extra($discountExtraChargeID)
    {
        $CI =& get_instance();
        $companyID=current_companyID();
        $CI->db->SELECT("discountDetailID");
        $CI->db->FROM('srp_erp_customerinvoicediscountdetails_temp');
        $CI->db->WHERE('discountMasterAutoID', $discountExtraChargeID);
        $CI->db->where('companyID', $companyID);
        $discounttempid= $CI->db->get()->row_array();

        $CI->db->SELECT("extraChargeDetailID");
        $CI->db->FROM('srp_erp_customerinvoiceextrachargedetails_temp');
        $CI->db->WHERE('extraChargeMasterAutoID', $discountExtraChargeID);
        $CI->db->where('companyID', $companyID);
        $extratempid= $CI->db->get()->row_array();

        $CI->db->SELECT("extraChargeDetailID");
        $CI->db->FROM('srp_erp_customerinvoiceextrachargedetails');
        $CI->db->WHERE('extraChargeMasterAutoID', $discountExtraChargeID);
        $CI->db->where('companyID', $companyID);
        $extraid= $CI->db->get()->row_array();

        $CI->db->SELECT("discountDetailID");
        $CI->db->FROM('srp_erp_customerinvoicediscountdetails');
        $CI->db->WHERE('discountMasterAutoID', $discountExtraChargeID);
        $CI->db->where('companyID', $companyID);
        $discountid= $CI->db->get()->row_array();

        $status = '';
        if (empty($discounttempid) && empty($extratempid) && empty($extraid) && empty($discountid)) {
            $status .= '<spsn class="pull-right"><a onclick="open_discount_edit_model('.$discountExtraChargeID.')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a onclick="delete_discount_category('.$discountExtraChargeID.')"><span class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:rgb(209, 91, 71);"></span></a></span>';
        }else{
            $status .= '<spsn class="pull-right"><a onclick="open_discount_edit_model('.$discountExtraChargeID.')"><span class="glyphicon glyphicon-pencil"></span></a></span>';
        }
        return $status;
    }
}
