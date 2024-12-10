<?php

class Group_financial_year_model extends ERP_Model
{

    function save_financial_year()
    {
        $this->db->trans_start();
        $x = 0;
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $data['beginingDate'] = trim($this->input->post('beginningdate') ?? '');
        $data['endingDate'] = trim($this->input->post('endingdate') ?? '');
        $data['comments'] = trim($this->input->post('comments') ?? '');
        $data['isActive'] = 1;
        $data['isClosed'] = 0;
        $data['groupID'] = $companyID;
        //$data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->insert('srp_erp_groupfinanceyear', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Financial Year Creation Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Financial Year Created.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function update_year_status()
    {
        $checked = trim($this->input->post('chkedvalue') ?? '');
        $data['isActive'] = $this->input->post('chkedvalue');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('groupFinanceYearID', $this->input->post('groupFinanceYearID'));
        $result = $this->db->update('srp_erp_groupfinanceyear', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Financial Year : Updated Successfully');
            return true;
        }
    }

    function update_year_current()
    {
        $this->db->trans_start();
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $this->db->where('groupID', $companyID);
        $this->db->update('srp_erp_groupfinanceyear', array('isCurrent' => 0));

        $data['isCurrent'] = 1;
        $data['isActive'] = 1;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('groupFinanceYearID', $this->input->post('groupFinanceYearID'));
        $this->db->update('srp_erp_groupfinanceyear', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Current Financial Year update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Current Financial Year Updated Successfully');
            $this->db->trans_commit();
            return array('status' => true);
        }
        return true;
    }

    function update_year_close()
    {
        $data['isActive'] = 0;
        $data['isCurrent'] = 0;
        $data['isClosed'] = $this->input->post('chkedvalue');
        $data['closedByEmpID'] = $this->common_data['current_userID'];
        $data['closedByEmpName'] = $this->common_data['current_user'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('groupFinanceYearID', $this->input->post('groupFinanceYearID'));
        $closedYear = $this->db->update('srp_erp_groupfinanceyear', $data);
        if ($closedYear) {
            $this->session->set_flashdata('s', 'Financial Year : Closed Successfully');
            return true;
        }
    }

}