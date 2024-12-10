<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('company_policy_active')) {
    function company_policy_active($isActive, $AutoID)
    {
        $checked = '';
        if ($isActive) {
            $checked = 'checked';
        }

        $status = '<span style="text-align: center;">';
        $status .= '<input ' . $checked . ' onclick="companyPolicyActive(' . $AutoID . ',this)" type="checkbox" id="" name="isActive_' . $AutoID . '">';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('get_policy')) {
    function get_policy($fieldType, $companyPolicyMasterID, $companyValue, $documentID, $isCompanyLevel,$code)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $element = '';
        switch ($fieldType) {
            case 'select':
                if ($isCompanyLevel) {
                    $values = $CI->db->query("SELECT * FROM srp_erp_companypolicymaster_value WHERE companypolicymasterID='{$companyPolicyMasterID}' AND companyID='{$companyID}'")->result_array();
                } else {
                    $values = $CI->db->query("SELECT * FROM srp_erp_companypolicymaster_value WHERE companypolicymasterID='{$companyPolicyMasterID}'")->result_array();
                }


                if($code=='CVI' && $companyValue==1) {
                    $element.=' <div class="input-group" style="width: 30%;">';
                    $element .= '<select name="' . $companyPolicyMasterID . '" onchange="ChangePolicy(this)" id="' . $companyPolicyMasterID . '" class="form-control" data-type="' . $documentID . '">';
                }else{
                    $element.=' <div class="input-group">';
                    $element .= '<select name="' . $companyPolicyMasterID . '" onchange="ChangePolicy(this)" id="' . $companyPolicyMasterID . '" class="form-control" data-type="' . $documentID . '">';
                }
                /*      $element .= '<option></option>';*/
                foreach ($values as $value) {
                    $selected = $companyValue == $value['systemValue'] ? 'selected' : '';
                    $element .= "<option {$selected} value='{$value['systemValue']}'>{$value['value']}</option>";
                }
                $element .= ' </select>';

                if($code=='PC' && $companyValue==1){
                    $element .= '';
                    $element .='<div class="input-group-addon" style="border: none;"><button type="button" class="btn btn-primary btn-xs pull-right" onclick="addpasswordpolicy()"><i class="fa fa-cog"></i></button></div>';
                    $element .=' </div>';
                }

                if($code=='CVI' && $companyValue==1){
                    $element .= '';
                    $element .='<div class="input-group-addon" style="border: none;"><button type="button" class="btn btn-primary btn-xs pull-right" onclick="addUserGrptopolicy('.$companyPolicyMasterID.')"><i class="fa fa-cog"></i></button></div>';
                    $element .=' </div>';
                }

                break;
            case 'text':
                $element .= "<input name='{$companyPolicyMasterID}' value='{$companyValue}' onchange=\"ChangePolicy(this)\" id='{$companyPolicyMasterID}' class='form-control' data-type='{$documentID}'>";
                break;
            case 'checkbox':

                break;
            case 'radio':

                break;
        }

        return $element;
    }

}

if (!function_exists('hide_marks_marked_by_employee')) {
    function hide_marks_marked_by_employee()
    {
        $CI =& get_instance();
        $CI->db->select("companypolicymasterID");
        $CI->db->from('srp_erp_companypolicymaster');
        $CI->db->where('code', 'HSM');
        $masterid = $CI->db->get()->row_array();

        $CI->db->select("companyPolicyAutoID");
        $CI->db->from('srp_erp_companypolicy');
        $CI->db->where('companypolicymasterID', $masterid['companypolicymasterID']);
        $CI->db->where('value', 1);
        $template = $CI->db->get()->row_array();
        $value = 0;
        if (!empty($template)) {
            $value = 1;
        } else {
            $value = 0;
        }

        return $value;
    }
}

if (!function_exists('cost_based_on_itemmaster_wac')) {
    function cost_based_on_itemmaster_wac()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $CI->db->select("companypolicymasterID");
        $CI->db->from('srp_erp_companypolicymaster');
        $CI->db->where('code', 'COSTWAC');
        $masterid = $CI->db->get()->row_array();

        $CI->db->select("companyPolicyAutoID,value");
        $CI->db->from('srp_erp_companypolicy');
        $CI->db->where('companypolicymasterID', $masterid['companypolicymasterID']);
        $CI->db->where('companyID', $companyID);        
        // $CI->db->where('value', 1);
        $template = $CI->db->get()->row_array();
        $value = 0;
        if (!empty($template)) {
            $value = (int)$template['value'];                     
        } else {            
            $value = 1;
        }
        return $value;
    }
}

