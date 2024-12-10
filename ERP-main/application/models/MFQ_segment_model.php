<?php

class MFQ_segment_model extends ERP_Model
{
    function add_segment()
    {
        $result = $this->db->query('INSERT INTO srp_erp_mfq_segment ( segmentID, companyID, companyCode, segmentCode, description, status) 
                                SELECT segmentID, companyID, companyCode, segmentCode, description, status FROM srp_erp_segment 
                                WHERE companyID = ' . current_companyID() . '  AND segmentID IN(' . join(",", $this->input->post('selectedItemsSync')) . ')');

        if ($result) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        }
    }

    function insert_segment()
    {

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_segment');
        $this->db->where('description', $this->input->post('description'));
        $this->db->where('companyID', current_companyID());
        $crew = $this->db->get()->row_array();

        if (!$crew) {
            $post = $this->input->post();
            unset($post['mfqSegmentID']);

            $post['companyID'] = current_companyID();
            $post['isFromERP'] = 0;

            $result = $this->db->insert('srp_erp_mfq_segment', $post);
            if ($result) {
                return array('error' => 0, 'message' => 'Segment successfully Added', 'code' => 1);
            } else {
                return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
            }
        } else {
            return array('error' => 1, 'message' => 'This Segment Description is already added to this company');
        }
    }

    function update_segment()
    {
        $mfqSegmentID = $this->input->post('mfqSegmentID');
        $post = $this->input->post();
        unset($post['mfqSegmentID']);
        $this->db->where('mfqSegmentID', $mfqSegmentID);
        $result = $this->db->update('srp_erp_mfq_segment', $post);
        //echo $this->db->last_query();
        if ($result) {
            return array('error' => 0, 'message' => 'Segment updated successfully', 'code' => 2);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }

    function get_srp_erp_mfq_segment()
    {
        $mfqSegmentID = $this->input->post('mfqSegmentID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_segment');
        $this->db->where('mfqSegmentID', $mfqSegmentID);
        $result = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $result;
    }
function link_segment()
{
    $result = $this->db->query("SELECT segmentID,companyID,companyCode,segmentCode,description,status FROM srp_erp_segment WHERE companyID= " . current_companyID() . " AND segmentID =" . $this->input->post('selectedItemsSync') . " ")->row_array();
    if($result)
    {
        $this->db->set('segmentID', $result["segmentID"]);
        $this->db->set('companyID', $result["companyID"]);
        $this->db->set('companyCode', $result["companyCode"]);
        $this->db->set('status', $result["status"]);
        $this->db->set('isFromErp', 1);
        $this->db->where('mfqSegmentID', $this->input->post('mfqSegmentID'));
        $update = $this->db->update('srp_erp_mfq_segment');
        if ($update) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        }
        else{
            $this->session->set_flashdata('e', 'Records adding failed');
            return array('status' => false);
        }
    }


}
function save_subsegment()
{
    $mfqsegmentID = $this->input->Post("mfqSegmentID_sub");
    $companyID = current_companyID();

    $data['levelNo'] = 1;
    $data['masterSegmentID'] = $mfqsegmentID;
    $data['companyID'] = $companyID;
    $data['companyCode'] = $this->common_data['company_data']['company_code'];
    $data['segmentCode'] = $this->input->Post("segmentCode");
    $data['description'] = $this->input->Post("description");
    $data['masterSegmentID'] =$mfqsegmentID;
    $data['status'] = 1;
    $result = $this->db->insert('srp_erp_mfq_segment', $data);
    if ($result) {
        return array('s','Sub Segment Added successfully');
    } else {
        return array('e', 'Sub Segment Added Failed');
    }
}
}
