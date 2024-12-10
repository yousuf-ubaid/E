<?php
class Group_segemnt_model extends ERP_Model
{

    function saveSegment()
    {
        $this->db->trans_start();
        $companyid = $this->common_data['company_data']['company_id'];
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp= $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;

        $data['description'] = trim($this->input->post('description') ?? '');
        $data['segmentCode'] = trim($this->input->post('segmentCode') ?? '');
        if (trim($this->input->post('segmentID') ?? '') != '')
        {
            $this->db->where('segmentID', trim($this->input->post('segmentID') ?? ''));
            $this->db->update('srp_erp_groupsegment', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                return array('e', 'Segment Update Failed');
            }
            else
            {
                //$this->session->set_flashdata('s', 'Segment Updated Successfully.');
                return array('s', 'Segment Updated Successfully');
            }
        }
        else
        {
            $checkExist = $this->db->query("select * from srp_erp_groupsegment where segmentCode = '" . $this->input->post('segmentCode') . "' AND groupID = $grpid")->row_array();
            if (!empty($checkExist))
            {
                return array('e', 'Segment Code already exists');
            }
            else
            {
                $data['groupID'] = trim($grpid);
                $this->db->insert('srp_erp_groupsegment', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    return array('e', 'Segment Save Failed');
                }
                else
                {
                    return array('s', 'Segment Saved Successfully');
                }
            }
        }
    }

    function edit_group_segment()
    {
        $this->db->select('*');
        $this->db->where('segmentID', $this->input->post('segmentID'));
        return $this->db->get('srp_erp_groupsegment')->row_array();
    }


    function save_segment_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $segmentID = $this->input->post('segmentID');
        $com = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;
        $results = $this->db->delete('srp_erp_groupsegmentdetails', array('companyGroupID' => $grpid, 'groupSegmentID' => $this->input->post('groupSegmentID')));
        foreach ($companyid as $key => $val)
        {
            if (!empty($segmentID[$key]))
            {
                $data['groupSegmentID'] = trim($this->input->post('groupSegmentID') ?? '');
                $data['segmentID'] = trim($segmentID[$key]);
                $data['companyID'] = trim($val);
                $data['companyGroupID'] = $grpid;

                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $results = $this->db->insert('srp_erp_groupsegmentdetails', $data);
            }
            //$last_id = $this->db->insert_id();
        }

        if ($results)
        {
            return array('s', 'Segment Link Saved Successfully');
        }
        else
        {
            return array('e', 'Segment Link Save Failed');
        }
    }

    function delete_segment_link()
    {
        $this->db->where('groupSegmentDetailID', $this->input->post('groupSegmentDetailID'));
        $result = $this->db->delete('srp_erp_groupsegmentdetails');
        return array('s', 'Record Deleted Successfully');
    }

    function load_segment_header()
    {
        $this->db->select('description');
        $this->db->where('segmentID', $this->input->post('groupSegmentID'));
        return $this->db->get('srp_erp_groupsegment')->row_array();
    }

    function fetch_segment_details()
    {
        $segmentID =  trim($this->input->post('groupSegmentID') ?? '');
        $data['segmentdetails'] = $this->db->query("SELECT 
                *
                FROM 
                srp_erp_groupsegment 

                where 
                segmentID = $segmentID ")->row_array();
        return $data;
    }

    function save_segment_duplicate()
    {
        $companyid = $this->input->post('checkedCompanies');
        $com = current_companyID();
        $grpid = $com;
        $masterGroupID = getParentgroupMasterID();
        $results = '';
        $comparr = array();

        foreach ($companyid as $val)
        {
            $i = 0;
            $this->db->select('groupSegmentDetailID');
            $this->db->where('groupSegmentID', $this->input->post('SegmentIDDuplicatehn'));
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $grpid);
            $linkexsist = $this->db->get('srp_erp_groupsegmentdetails')->row_array();

            $this->db->select('*');
            $this->db->where('segmentID', $this->input->post('SegmentIDDuplicatehn'));
            $CurrentCus = $this->db->get('srp_erp_groupsegment')->row_array();


            $this->db->select('segmentID');
            $this->db->where('segmentCode', $CurrentCus['segmentCode']);
            $this->db->where('companyID', $val);
            $CurrentCOAexsist = $this->db->get('srp_erp_segment')->row_array();

            if (!empty($CurrentCOAexsist))
            {
                $i++;
                $companyName = get_companyData($val);

                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Segment already exist" . " (" . $CurrentCus['description'] . ")"));
            }

            if ($i == 0)
            {
                if (empty($linkexsist))
                {

                    $data['isDefault'] = 0;
                    $data['isShow'] = 1;
                    $data['status'] = $CurrentCus['status'];
                    $data['segmentCode'] = $CurrentCus['segmentCode'];
                    $data['description'] = $CurrentCus['description'];
                    $data['companyID'] = $val;
                    $companyCode = get_companyData($val);
                    $data['companyCode'] = $companyCode['company_code'];



                    $this->db->insert('srp_erp_segment', $data);
                    $last_id = $this->db->insert_id();


                    $dataLink['groupSegmentID'] = trim($this->input->post('SegmentIDDuplicatehn') ?? '');
                    $dataLink['segmentID'] = trim($last_id);
                    $dataLink['companyID'] = trim($val);
                    $dataLink['companyGroupID'] = $masterGroupID;
                    $dataLink['createdPCID'] = $this->common_data['current_pc'];
                    $dataLink['createdUserID'] = $this->common_data['current_userID'];
                    $dataLink['createdUserName'] = $this->common_data['current_user'];
                    $dataLink['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupsegmentdetails', $dataLink);
                }
            }
            else
            {
                continue;
            }
        }

        if ($results)
        {
            return array('s', 'Segment Replicated Successfully', $comparr);
        }
        else
        {
            return array('e', 'Segment Replication not successful', $comparr);
        }
    }
}
