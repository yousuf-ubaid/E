<?php

class mfq_standard_details_modal extends ERP_Model
{

    function save_mfq_standard_details()
    {
        $companyID = current_companyID();
        $type = $this->input->post('type');
        $mfqStandardDetailMasterID = trim($this->input->post('mfqStandardDetailMasterID') ?? '');
        $this->db->trans_start();
        $data['Description'] = trim($this->input->post('description') ?? '');
        $data['typeID'] = trim($this->input->post('type') ?? '');

        if (!empty($mfqStandardDetailMasterID)) {
            $this->db->where('mfqStandardDetailMasterID', $mfqStandardDetailMasterID);
            $this->db->update('srp_erp_mfq_standarddetailsmaster', $data);
            $q = "SELECT
                    typeID
                FROM
                    srp_erp_mfq_standarddetailsmaster
                WHERE
                   typeID = '" . $type . "' AND mfqStandardDetailMasterID != '" . $mfqStandardDetailMasterID . "' AND companyID = $companyID ";
            $result = $this->db->query($q)->row_array();
            if ($result) {
                return array('e', 'Type is Already Exist');
            } else {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Standard Details Update Failed.');
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Standard Details Updated Successfully.');
                }
            }
        } else {
            $q = "SELECT
                    typeID
                FROM
                    srp_erp_mfq_standarddetailsmaster
                WHERE
                   typeID = '" . $type . "' AND companyID = $companyID ";
            $result = $this->db->query($q)->row_array();
            if ($result) {
                return array('e', 'Type is Already Exist');
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $this->db->insert('srp_erp_mfq_standarddetailsmaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Standard Details Created Successfully Created Failed.');
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Standard Details Created Successfully.', 'last_id' => $last_id);
                }
            }
        }

    }

    function delete_mfq_standard_details()
    {
        $mfqStandardDetailMasterID = $this->input->post('mfqStandardDetailMasterID');
        $this->db->delete('srp_erp_mfq_standarddetailsmaster', array('mfqStandardDetailMasterID' => $mfqStandardDetailMasterID));
        return array('s', 'Standard Details deleted successfully.');

    }

    function load_mfq_standard_details()
    {
        $mfqStandardDetailMasterID = trim($this->input->post('mfqStandardDetailMasterID') ?? '');

        return $this->db->query("select * from srp_erp_mfq_standarddetailsmaster where mfqStandardDetailMasterID = {$mfqStandardDetailMasterID} ")->row_array();

    }

}