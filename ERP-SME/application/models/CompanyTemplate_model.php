<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class CompanyTemplate_model extends CI_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function saveTemplate()
    {
        $TempMasterID = $this->input->post('TempMasterID');
        $FormCatID = $this->input->post('FormCatID');
        $companyID = $this->input->post('companyID');

        $data = array('TempMasterID' => $TempMasterID);
        $this->db->where('FormCatID', $FormCatID,'companyID', $companyID)->update('srp_erp_templates', $data);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }


}