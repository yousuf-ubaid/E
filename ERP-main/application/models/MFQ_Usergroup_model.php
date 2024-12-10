<?php

class MFQ_Usergroup_model extends ERP_Model
{
    function save_mfq_usergroup()
    {
        $Isdefault =  trim($this->input->post('Isdefault') ?? '');
        $usergrouptype = trim($this->input->post('usergrouptype') ?? '');
        $usergroupid = $this->input->post('userGroupID');
        $segmentmfq =  $this->input->post('segmentmfq');
        $companyid = current_companyID();

            if(isset($usergroupid))
            {
                $this->db->select('isDefault');
                $this->db->from('srp_erp_mfq_usergroups');
                $this->db->where('companyID', current_companyID());
                $this->db->where('isDefault', 1);
                $this->db->where('userGroupID!=', $usergroupid);
                $result = $this->db->get()->row_array();

            }else
            {
                $this->db->select('isDefault');
                $this->db->from('srp_erp_mfq_usergroups');
                $this->db->where('companyID', current_companyID());
                $this->db->where('isDefault', 1);
                $result = $this->db->get()->row_array();
            }




        if (!empty($result) && $Isdefault == 1) {
            return array('e', 'There is already default user group');
        } else {



            $this->db->set('groupType', $this->input->post('usergrouptype'));
            $this->db->set('segmentID', $this->input->post('segmentmfq'));

            if (!$this->input->post('userGroupID')) {
                $grouptype = $this->db->query("SELECT userGroupID,groupType FROM `srp_erp_mfq_usergroups` where companyID = '{$companyid}' AND groupType = '{$usergrouptype}'  AND groupType != '' AND segmentID = '{$segmentmfq}'")->result_array();
                if(!empty($grouptype))
                {
                    return array('e', 'Group Type Already Exists.');
                }else {
                    $this->db->set('description', $this->input->post('description'));
                    $this->db->set('isActive', $this->input->post('IsActive'));
                    $this->db->set('isDefault', $this->input->post('Isdefault'));
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('timestamp', current_date(true));
                    $this->db->insert('srp_erp_mfq_usergroups');
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        return array('e', 'User Group Saving Failed ' . $this->db->_error_message());
                    } else {
                        $this->db->trans_commit();
                        return array('s', 'User Group saved Successfully.');
                    }
                }
            } else {
                $grouptype = $this->db->query("SELECT userGroupID,groupType FROM `srp_erp_mfq_usergroups` where companyID = '{$companyid}' AND userGroupID !='{$usergroupid}' AND groupType = '{$usergrouptype}'  AND groupType != '' AND segmentID = '{$segmentmfq}' ")->result_array();
                if(!empty($grouptype))
                {
                    return array('e', 'Group Type Already Exists.');
                }else
                {
                    $data['userGroupID'] = $this->input->post('userGroupID');
                    $data['description'] = $this->input->post('description');
                    $data['isActive'] = $this->input->post('IsActive');
                    $data['isDefault'] = $this->input->post('Isdefault');
                    $data['companyID'] =current_companyID();
                    $this->db->set('timestamp', current_date(true));
                    $this->db->where('userGroupID', $this->input->post('userGroupID'));
                    $result = $this->db->update('srp_erp_mfq_usergroups', $data);
                    if ($result) {
                        return array('s', 'User Group Updated successfully');
                    } else {
                        return array('e', 'User Group Insertion Failed');
                    }

                }

            }
        }

    }

    function edit_mfq_usergroup()
    {
        $mfqusergroupID = $this->input->post('userGroupID');
        $data = $this->db->query("select * from srp_erp_mfq_usergroups where userGroupID=$mfqusergroupID")->row_array();
        return $data;
    }

    function link_employees()
    {
        $selectedItem = $this->input->post('selectedItemsSync[]');
        $usergroup = $this->input->post('userGroupID');
        $compID = current_companyID();
        $data = [];

        foreach ($selectedItem as $key => $vals) {
            $data[$key]['empID'] = $vals;
            $data[$key]['userGroupID'] = $usergroup;
            $data[$key]['companyID'] = $compID;

        }
        $result = $this->db->insert_batch('srp_erp_mfq_usergroupdetails', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Employee Added successfully !');
            return array('status' => true);
        } else {
            $this->session->set_flashdata('e', 'Employee Insertion Failed!');
            return array('status' => false);
        }
    }

    function delete_employees()
    {
        $employeeNavigation = $this->input->post('employeeNavigationID');
        $this->db->delete('srp_erp_mfq_usergroupdetails', array('employeeNavigationID' => $employeeNavigation));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error while deleting!');
        } else {
            $this->db->trans_commit();
            return array('s', 'Employee deleted successfully');
        }
    }

    function delete_group_detail()
    {
        $usergroupid = $this->input->post('userGroupID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_usergroupdetails');
        $this->db->where('userGroupID', $usergroupid);
        $results = $this->db->get()->row_array();
        if ($results) {
            return array('e', 'Please delete all the employees before deleting this user group.');
        } else {
            $this->db->delete('srp_erp_mfq_usergroups', array('userGroupID' => $usergroupid));
            return array('s', 'User Group deleted successfully.');
        }
    }
}




