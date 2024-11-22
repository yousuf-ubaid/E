<?php

class MFQ_Costing_model extends ERP_Model
{
    function fetch_costing_entry_setup()
    {
        $companyID = current_companyID();
        $result = $this->db->query("SELECT costingID, 
            CASE
                WHEN categoryID = 1 THEN 'Material Consumption'
                WHEN categoryID = 2 THEN 'Labour Task'
                WHEN categoryID = 3 THEN 'Overhead Cost'
                WHEN categoryID = 4 THEN 'MFQ Machine'
                WHEN categoryID = 5 THEN 'Third Party Service'
            END AS category, categoryID, isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry FROM srp_erp_mfq_costingentrysetup WHERE companyID = {$companyID}")->result_array();
        return $result;
    }

    function enable_cost_entry()
    {
        $compID = current_companyID();
        $this->db->trans_start();
        $checkedVal = trim($this->input->post('checkedVal') ?? '');
        $costingID = trim($this->input->post('costingID') ?? '');

        $detail['isEntryEnabled'] = $checkedVal;
        //  $detail['deactivatedByEmpID'] = $this->common_data['current_userID'];
        //  $detail['deactivatedDate'] = $this->common_data['current_date'];
        $this->db->where('costingID', $costingID);
        $this->db->where('companyID', $compID);
        $this->db->update('srp_erp_mfq_costingentrysetup', $detail);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'For Entries :  Modification Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'For Entries :  Modified Successfully.');
        }
    }

    function enable_manual_cost_entry()
    {
        $compID = current_companyID();
        $this->db->trans_start();
        $checkedVal = trim($this->input->post('checkedVal') ?? '');
        $costingID = trim($this->input->post('costingID') ?? '');

        $detail['manualEntry'] = $checkedVal;
        //  $detail['deactivatedByEmpID'] = $this->common_data['current_userID'];
        //  $detail['deactivatedDate'] = $this->common_data['current_date'];
        $this->db->where('costingID', $costingID);
        $this->db->where('companyID', $compID);
        $this->db->update('srp_erp_mfq_costingentrysetup', $detail);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Manual Entry :  Modification Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Manual Entry :  Modified Successfully.');
        }
    }

    function enable_linkedDoc_cost_entry()
    {
        $this->db->trans_start();
        $compID = current_companyID();
        $this->db->trans_start();
        $checkedVal = trim($this->input->post('checkedVal') ?? '');
        $costingID = trim($this->input->post('costingID') ?? '');

        $detail['linkedDocEntry'] = $checkedVal;
        //  $detail['deactivatedByEmpID'] = $this->common_data['current_userID'];
        //  $detail['deactivatedDate'] = $this->common_data['current_date'];
        $this->db->where('costingID', $costingID);
        $this->db->where('companyID', $compID);
        $this->db->update('srp_erp_mfq_costingentrysetup', $detail);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Linked Document Entry :  Modification Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Linked Document Entry :  Modified Successfully.');
        }
    }

    function configure_item()
    {
        $this->db->trans_start();
        $selectedItemsSync = $this->input->post('selectedItemsSync');
        $itemIDs = join(',', $selectedItemsSync);
        $detail['isMfqItem'] = 1;
        $this->db->where('itemAutoID IN (' . $itemIDs . ')');
        $this->db->where('companyID', current_companyID());
        $this->db->update('srp_erp_itemmaster', $detail);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Item Configuration :  Modification Failed ' . $this->db->_error_message());
            return false;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'New Item Configured Successfully!');
            return true;
        }
    }

    function save_new_gl_configuration()
    {
        $itemCategory = $this->input->post('itemCategory');
        if($itemCategory == 'Inventory') {
            $glAutoID = $this->input->post('glAutoID_inv');
        } else {
            $glAutoID = $this->input->post('glAutoID_srv');
        }
        $configurationAutoID = $this->input->post('configurationAutoID');

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_postingconfiguration');
        if(!empty($configurationAutoID)) {
            $this->db->where('configurationAutoID != ' . $configurationAutoID);    
        }
        $this->db->where('configurationCode', $itemCategory);
        $this->db->where('companyID', current_companyID());
        $detail_validate = $this->db->get()->row_array();

        if($detail_validate) {
            return array ('e', 'GL Configuration already exist for the selected Item Category!');
        } else {
            $this->db->trans_start();
            $data['configurationCode'] = $itemCategory;
            $data['value'] = $glAutoID;

            if($configurationAutoID) {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('configurationAutoID', trim($configurationAutoID));
                $this->db->update('srp_erp_mfq_postingconfiguration', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'GL Configuration Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->db->trans_commit();
                    return array ('s', 'GL Configuration Updated Successfully!');
                }
            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_postingconfiguration', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array ('e', 'Failed to Save GL Configuration ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array ('s', 'GL Configuration Added Successfully!');
                }
            }
        }
    }
}