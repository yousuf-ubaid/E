<?php defined('BASEPATH') or exit('No direct script access allowed');
class Segment extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Segment_modal');
    }

    // function load_segment(){
    //     $this->datatables->select("segmentID,companyID,companyCode,segmentCode,description,status,isDefault,masterID, CASE 
    //     WHEN EXISTS (
    //         SELECT 1 
    //         FROM srp_erp_segment s2
    //         WHERE s2.masterID = srp_erp_segment.segmentID
    //     ) THEN 'Has Sub Record' 
    //     ELSE 'No Sub Record' 
    //     END as masterIDStatus");
    //     $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
    //     $this->datatables->from('srp_erp_segment');
    //     $this->datatables->add_column('action', '$1', 'load_segment_action(segmentID)');
    //     $this->datatables->add_column('status', '$1', 'load_segment_status(segmentID,status)');
    //     $this->datatables->edit_column('default', '$1', 'loadDefaultWarehousechkbx(segmentID,isDefault)');
    //     echo $this->datatables->generate();
    // }

    public function load_segment_table()
    {
        // Retrieve filters from POST request
        $masterIDFilter = trim($this->input->post('filter_masterID') ?? '');

        // Initialize query filters
        $filters = array();

        if ($masterIDFilter)
        {
            // Convert the filter to an array if it's a comma-separated list
            $masterIDs = explode(',', $masterIDFilter);
            // Sanitize and format the array for SQL IN clause
            $masterIDs = array_map(function ($id)
            {
                return "'" . trim($id) . "'";
            }, $masterIDs);
            $masterIDs = implode(',', $masterIDs);

            $filters[] = "(s1.segmentID IN ($masterIDs) OR s1.masterID IN ($masterIDs))";
        }

        // Build the WHERE clause
        $where = 's1.companyID = ' . $this->common_data['company_data']['company_id'];
        if (!empty($filters))
        {
            $where .= ' AND ' . implode(' AND ', $filters);
        }

        // Fetch data from the database with recursive data
        $query = $this->db->select("
            s1.segmentID AS segmentID,
            s1.companyID,
            s1.companyCode,
            s1.segmentCode,
            s1.description,
            s1.status AS status,
            s1.isDefault AS isDefault,
            s1.masterID,
            CASE 
                WHEN s2.segmentID IS NOT NULL THEN CONCAT('<b>Segment Code:</b> ', s2.segmentCode, '<br><b>Description: </b>', s2.description)
                ELSE '-' 
            END AS masterSegmentInfo
        ")
            ->from('srp_erp_segment s1')
            ->join('srp_erp_segment s2', 's1.masterID = s2.segmentID', 'left')
            ->where($where)
            ->order_by('s1.segmentCode', 'ASC')
            ->get();

        $segments = $query->result_array();


        $data['segments'] = $this->buildRecursiveSegments($segments, 0);


        $this->load->view('system/segment_table_view', $data);
    }

    private function buildRecursiveSegments($segments, $parentId = 0)
    {
        $branch = array();

        foreach ($segments as $segment)
        {

            if ($segment['masterID'] == $parentId)
            {

                $children = $this->buildRecursiveSegments($segments, $segment['segmentID']);
                if ($children)
                {
                    $segment['children'] = $children;
                }
                $branch[] = $segment;
            }
        }

        return $branch;
    }

    public function load_segment()
    {
        $this->datatables->select("s1.segmentID AS segmentID,s1.companyID,s1.companyCode, s1.segmentCode,s1.description, s1.status AS status,s1.isDefault AS isDefault ,s1.masterID,
        CASE 
            WHEN s2.segmentID IS NOT NULL THEN CONCAT('<b>Segment Code:</b> ', s2.segmentCode, '<br><b>Description: </b>', s2.description)
            ELSE '-' 
        END AS masterSegmentInfo
        ");
        $this->datatables->from('srp_erp_segment s1');
        $this->datatables->join('srp_erp_segment s2', 's1.masterID = s2.segmentID', 'left');
        $this->datatables->where('s1.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('action', '$1', 'load_segment_action(segmentID)');
        $this->datatables->add_column('status', '$1', 'load_segment_status(segmentID,status)');
        $this->datatables->edit_column('default', '$1', 'loadDefaultWarehousechkbx(segmentID,isDefault)');
        echo $this->datatables->generate();
    }

    function save_segment()
    {
        if (!$this->input->post('segmentID'))
        {
            $this->form_validation->set_rules('segmentcode', 'Segment Code', 'trim|required|max_length[10]');
        }
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Segment_modal->save_segment());
        }
    }

    function edit_segment()
    {
        if ($this->input->post('segmentID') != "")
        {
            echo json_encode($this->Segment_modal->edit_segment());
        }
        else
        {
            echo json_encode(FALSE);
        }
    }

    function update_segmentstatus()
    {
        echo json_encode($this->Segment_modal->update_segmentstatus());
    }
    /* Function added */
    function setDefaultSegment()
    {
        echo json_encode($this->Segment_modal->setDefaultSegment());
    }
    /* End  Function */

    function checkForSubSegment()
    {
        $segmentID = $this->input->post('segmentID');

        $this->db->select('masterID');
        $this->db->from('srp_erp_segment');
        $this->db->where('segmentID', $segmentID);
        $query = $this->db->get();
        $status = $query->row_array();

        echo json_encode($status);
    }

    function getSubSegment()
    {
        $segmentID = $this->input->post('segmentID');

        $this->db->select('*');
        $this->db->from('srp_erp_segment');
        $this->db->where('masterID', $segmentID);
        $this->db->order_by('segmentCode', 'ASC');
        $query = $this->db->get();
        $subSegment = $query->result_array();

        echo json_encode($subSegment);
    }

    function save_sub_segment()
    {
        if (!$this->input->post('segmentID'))
        {
            $this->form_validation->set_rules('sub_segmentcode', 'Segment Code', 'trim|required|max_length[10]');
        }
        $this->form_validation->set_rules('sub_description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode($this->Segment_modal->save_sub_segment());
        }
    }
}
