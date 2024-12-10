<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Insurance_category_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }


    function save_insurance_category()
    {
        $this->db->trans_start();
        $description = $this->input->post('description');
        $insurancecategoryID = $this->input->post('insurancecategoryID');
        $companyID = $this->common_data['company_data']['company_id'];
        $data['description'] = trim_desc($description);

        if (trim($insurancecategoryID)) {
            $descexist = $this->db->query("SELECT insurancecategoryID FROM srp_erp_family_insurancecategory WHERE description='$description' AND insurancecategoryID !=$insurancecategoryID AND companyID = $companyID; ")->row_array();
        } else {
            $descexist = $this->db->query("SELECT insurancecategoryID FROM srp_erp_family_insurancecategory WHERE description='$description' AND companyID = $companyID; ")->row_array();
        }
        if (trim($insurancecategoryID)) {
            if ($descexist) {
                return array('e', 'Description Already Exist');
            } else {
                $this->db->where('insurancecategoryID', trim($insurancecategoryID));
                $this->db->update('srp_erp_family_insurancecategory', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Insurance Category Updating  Failed');
            } else {
                return array('s', 'Insurance Category Updated Successfully');
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createDate'] = $this->common_data['current_date'];
            if ($descexist) {
                return array('e', 'Description Already Exist');
            } else {
                $this->db->insert('srp_erp_family_insurancecategory', $data);
            }
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Insurance Category Save Failed');
            } else {
                return array('s', 'Insurance Category Saved Successfully');
            }
        }
    }

    function edit_insurance_category()
    {
        $this->db->select('*');
        $this->db->where('insurancecategoryID', trim($this->input->post('insurancecategoryID') ?? ''));
        $this->db->from('srp_erp_family_insurancecategory');
        return $this->db->get()->row_array();
    }

}
