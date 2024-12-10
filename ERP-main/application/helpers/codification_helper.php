<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('codifictn_master_action')) {
    function codifictn_master_action($attributeID,$masterID)
    {

        $CI =& get_instance();
        $status = '<span class="pull-right">';
        if($masterID==0){
            $status .= '<a onclick=\'addSubAttribute("' . $attributeID . '"); \'><span title="Add Sub Attribute" rel="tooltip" class="glyphicon glyphicon-plus"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        $status .= '<a onclick=\'addAttrDetail("' . $attributeID . '","' . $masterID . '"); \'><span title="Add Attribute Detail" rel="tooltip" class="glyphicon glyphicon-align-justify" ></span></a>';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('codifictn_get_val_type')) {
    function codifictn_get_val_type($valueType)
    {

        $CI =& get_instance();
        $status = '<span>';
        if($valueType==0){
            $status .= '<label style="text-align: center;">Text</label>';
        }else{
            $status .= '<label style="text-align: center;">Numeric</label>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('mastr_dtl_val')) {
    function mastr_dtl_val($attributeDetailID)
    {

        $CI =& get_instance();
        $CI->db->select('detailDescription');
        $CI->db->from('srp_erp_itemcodificationattributedetails');
        $CI->db->where('attributeDetailID', $attributeDetailID);
        $data = $CI->db->get()->row_array();

        $status = '<span>';
        if(!empty($data)){
            $detailDescription=$data['detailDescription'];
            $status .= '<label style="text-align: center;">'.$detailDescription.'</label>';
        }else{
            $status .= '<label style="text-align: center;">-</label>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('codifictn_dtl_action')) {
    function codifictn_dtl_action($attributeDetailID,$attributeID,$masterID)
    {

        $CI =& get_instance();



        $CI->db->select('itemCodificationDetailID');
        $CI->db->from('srp_erp_itemmastercodification');
        $CI->db->where('attributeDetaiID', $attributeDetailID);
        $data = $CI->db->get()->row_array();
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'editAttributeDetail("' . $attributeDetailID . '"); \'><span title="Add Sub Attribute" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if(!empty($data)){
            //$status .= '<a onclick=\'editAttributeDetail("' . $attributeDetailID . '"); \'><span title="Add Sub Attribute" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            //$status .= '<a onclick=\'deleteAttrDetail("' . $attributeDetailID . '"); \'><span title="Delete" rel="tooltip" style="color:red;" class="glyphicon glyphicon-trash"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('codifictn_setup_action')) {
    function codifictn_setup_action($codificationSetupID,$confirmedYN)
    {

        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'openSetupDetail("' . $codificationSetupID . '"); \'><span title="Setup Details" rel="tooltip" class="glyphicon glyphicon-plus"></span></a>';
        if($confirmedYN==0){
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick=\'confirmSetup("' . $codificationSetupID . '"); \'><span title="Confirm Setup" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        }

        if($confirmedYN==1){
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick=\'assign_to_category("' . $codificationSetupID . '"); \'><span title="Assign category" rel="tooltip" class="glyphicon glyphicon-link"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('all_codification_master_drop')) {
    function all_codification_master_drop()
    {
        $CI =& get_instance();
        $CI->db->select('attributeID,attributeDescription');
        $CI->db->from('srp_erp_itemcodificationattributes');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Attribute');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['attributeID'] ?? '')] = trim($row['attributeDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('codifictn_asn_setup_action')) {
    function codifictn_asn_setup_action($itemCategoryID)
    {

        $CI =& get_instance();
        $status = '<span class="pull-right">';
        //$status .= '<a onclick=\'delete_cat_asn("' . $itemCategoryID . '"); \'><span title="Delete" rel="tooltip" style="color:red;" class="glyphicon glyphicon-trash"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_detail_cod_drop')) {
    function load_detail_cod_drop($attributeID,$attributeDetailID,$itemAutoID)
    {
        $CI =& get_instance();
        $CI->db->select('icd.attributeDetailID,icd.attributeID,icd.masterID,icd.detailDescription,srp_erp_itemmastercodification.attributeDetaiID as selecval');
        $CI->db->from('srp_erp_itemcodificationattributedetails icd');
        $CI->db->join('srp_erp_itemmastercodification', ' icd.attributeDetailID =  srp_erp_itemmastercodification.attributeDetaiID AND srp_erp_itemmastercodification.itemAutoID='.$itemAutoID.'','LEFT');
        $CI->db->WHERE('icd.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->WHERE('icd.attributeID', $attributeID);
        if($attributeDetailID>0){
            $CI->db->WHERE('icd.masterID', $attributeDetailID);
        }

        $data = $CI->db->get()->result_array();
        /*$data_arr = array('' => 'Select Value');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['attributeDetailID'] ?? '')] = trim($row['detailDescription'] ?? '');
            }
        }*/

        return $data;
    }
}





