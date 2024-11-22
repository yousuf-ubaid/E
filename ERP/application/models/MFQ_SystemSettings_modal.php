<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MFQ_SystemSettings_modal extends ERP_Model
{
    function create_document_status()
    {
        $this->db->trans_start();
        $data['documentID'] = $this->input->post('documentID');
        $data['description'] = $this->input->post('status');
        $data['statusColor'] = $this->input->post('color');
        $data['statusBackgroundColor'] = $this->input->post('backgroundColor');
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $statusID = $this->input->post('statusID');
        if ($statusID == '') {
            $this->db->insert('srp_erp_mfq_status', $data);
        } else {
            $this->db->update('srp_erp_mfq_status', $data, array('statusID' => $statusID));
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Document status save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Document status saved successfully.');

        }
    }

    function deleteDocumentStatus()
    {
        $this->db->delete('srp_erp_mfq_status', array('statusID' => trim($this->input->post('statusID') ?? '')));
        return true;
    }
}