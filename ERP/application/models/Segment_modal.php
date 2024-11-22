<?php

class Segment_modal extends ERP_Model
{

    function save_segment()
    {

        $this->db->trans_start();
        $usergroup_assign = getPolicyValues('UGSE', 'All');

        $user_groups = $this->input->post('user_group');
        $segmentID = $this->input->post('segmentID');

        $data['description'] = trim($this->input->post('description') ?? '');
        $data['segmentCode'] = trim($this->input->post('segmentcode') ?? '');
        $data['masterID'] = trim($this->input->post('masterSegmentID') ?? '');
        $data['isShow'] = trim($this->input->post('isShow') ?? '');
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        if (trim($this->input->post('segmentID') ?? '') != '')
        {
            $this->db->where('segmentID', trim($this->input->post('segmentID') ?? ''));
            $this->db->update('srp_erp_segment', $data);

            if ($usergroup_assign == 1)
            {

                $res = update_segment_usergroups($segmentID, $user_groups);
            }


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Segment Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            }
            else
            {
                $this->session->set_flashdata('s', 'Segment Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('segmentID'));
            }
        }
        else
        {
            $checkExist = $this->db->query("select * from srp_erp_segment where companyID =  " . $this->common_data['company_data']['company_id'] . " AND segmentCode = '" . $this->input->post('segmentcode') . "'")->row_array();
            if (!empty($checkExist))
            {
                $this->session->set_flashdata('e', 'Segment Code already exists');
                return array('status' => false);
            }
            else
            {

                $this->load->library('sequence');
                $this->db->insert('srp_erp_segment', $data);
                $last_id = $this->db->insert_id();

                if ($usergroup_assign == 1)
                {
                    $res = update_segment_usergroups($last_id, $user_groups);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->session->set_flashdata('e', 'Segment Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                }
                else
                {
                    $this->session->set_flashdata('s', 'Segment Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }
    }

    function save_sub_segment()
    {

        $this->db->trans_start();
        $usergroup_assign = getPolicyValues('UGSE', 'All');

        $user_groups = $this->input->post('user_group');
        $subsegmentID = $this->input->post('subsegmentID');

        $data['description'] = trim($this->input->post('sub_description') ?? '');
        $data['segmentCode'] = trim($this->input->post('sub_segmentcode') ?? '');
        $data['isShow'] = trim($this->input->post('sub_Show') ?? '');

        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['masterID'] = $subsegmentID;

        $checkExist = $this->db->query("select * from srp_erp_segment where companyID =  " . $this->common_data['company_data']['company_id'] . " AND segmentCode = '" . $this->input->post('sub_segmentcode') . "'")->row_array();
        if (!empty($checkExist))
        {
            $this->session->set_flashdata('e', 'Segment Code already exists');
            return array('status' => false);
        }
        else
        {

            $this->load->library('sequence');
            $this->db->insert('srp_erp_segment', $data);
            $last_id = $this->db->insert_id();

            if ($usergroup_assign == 1)
            {
                $res = update_segment_usergroups($last_id, $user_groups);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Sub Segment Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            }
            else
            {
                $this->session->set_flashdata('s', 'Sub Segment Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function edit_segment()
    {

        $usergroup_assign = getPolicyValues('UGSE', 'All');

        $this->db->select('*');
        $this->db->where('segmentID', $this->input->post('segmentID'));
        $segment_details =  $this->db->get('srp_erp_segment')->row_array();

        if ($usergroup_assign == 1)
        {

            $str = array();

            $this->db->select('*');
            $this->db->where('segmentID', $this->input->post('segmentID'));
            $user_groups =  $this->db->get('srp_erp_segment_usergroups')->result_array();

            foreach ($user_groups as $group)
            {
                $str[] = (string)$group['userGroupID'];
            }

            $segment_details['user_groups'] = $str;

            return $segment_details;
        }
        else
        {
            return $segment_details;
        }
    }

    function update_segmentstatus()
    {
        $segmentID = $this->input->post('segmentID');
        $status = $this->input->post('chkedvalue');
        if ($status == 0)
        {
            $checkExist = $this->db->query(" SELECT EXISTS(
                SELECT * FROM information_schema.tables WHERE
                table_schema = (SELECT DATABASE()) AND 
                table_name = 'srp_segmentmaster' ) as result")->row_array();

            if ($checkExist['result'] == 1)
            {
                $isSegmentPulled = $this->db->query(" SELECT segmentID FROM srp_segmentmaster WHERE segmentID =  $segmentID ")->result_array();
                if ($isSegmentPulled)
                {
                    $this->session->set_flashdata('e', 'You cannot deactivate this account. This account has been linked with transactions.');
                    return true;
                }
            }
        }
        $data['status'] = ($this->input->post('chkedvalue'));
        $this->db->where('segmentID', $this->input->post('segmentID'));
        $result = $this->db->update('srp_erp_segment', $data);
        if ($result)
        {
            $this->session->set_flashdata('s', 'Records Updated Successfully');
            return true;
        }
    }

    /* Function added */
    function setDefaultSegment()
    {
        $isDefault = $this->input->post('chkdVal');
        $segmentID = $this->input->post('segmentID');
        $data['isDefault'] = 0;
        $this->db->where('companyID', current_companyID());
        $result = $this->db->update('srp_erp_segment', $data);
        if ($result)
        {
            $data['isDefault'] = $isDefault;
            $this->db->where('segmentID', $segmentID);
            $results = $this->db->update('srp_erp_segment', $data);
            if ($results)
            {
                return array('s', 'successfully updated');
            }
        }
    }
    /* End  Function*/
}
