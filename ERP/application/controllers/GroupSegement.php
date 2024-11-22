<?php
class GroupSegement extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_segemnt_model');
        $this->load->helpers('group_management');
    }

    function fetch_segment_group()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->datatables->select('segmentCode,segmentID,description')
            ->where('groupID', $companyID)
            ->from('srp_erp_groupsegment');
        //$this->datatables->add_column('edit', '$1', 'editgroupcategory(partyCategoryID)');
        $this->datatables->add_column('edit', '<a onclick="load_duplicate_segment_group($1)"><span title="Replicate" rel="tooltip" class="glyphicon glyphicon-duplicate"></span></a>&nbsp;|&nbsp;<a onclick="link_group_segment($1)"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;<a onclick="edit_group_segment($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span></a>', 'segmentID');
        echo $this->datatables->generate();
    }

    function saveSegment()
    {

        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('segmentCode', 'Segment Code', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_segemnt_model->saveSegment());
        }
    }

    function edit_group_segment()
    {
        echo json_encode($this->Group_segemnt_model->edit_group_segment());
    }

    function load_company()
    {
        $data['groupSegmentID'] = $this->input->post('groupSegmentID');
        $html = $this->load->view('system/GroupSegment/ajax/ajax-erp_load_company', $data, true);
        echo $html;
    }

    function load_segment()
    {
        $data['companyID'] = $this->input->post('companyID');
        $data['groupSegmentID'] = $this->input->post('groupSegmentID');
        $html = $this->load->view('system/GroupSegment/ajax/erp_load_company_segments', $data, true);
        echo $html;
    }

    function fetch_segment_Details()
    {
        $groupSegmentID = $this->input->post('groupSegmentID');

        $this->datatables->select('groupSegmentDetailID,groupSegmentID,srp_erp_groupsegmentdetails.segmentID,srp_erp_groupsegmentdetails.companyID,companyGroupID,srp_erp_segment.segmentCode as segmentCode,srp_erp_segment.description as description,srp_erp_company.company_name as company_name');
        $this->datatables->from('srp_erp_groupsegmentdetails');
        $this->datatables->join('srp_erp_segment', 'srp_erp_groupsegmentdetails.segmentID = srp_erp_segment.segmentID');
        $this->datatables->join('srp_erp_company', 'srp_erp_groupsegmentdetails.companyID = srp_erp_company.company_id');
        $this->datatables->where('srp_erp_groupsegmentdetails.groupSegmentID', $groupSegmentID);
        //$this->datatables->where('srp_erp_groupchartofaccountdetails.companyGroupID', $grpid);
        $this->datatables->add_column('edit', '<a onclick="delete_segment_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupSegmentDetailID');
        echo $this->datatables->generate();
    }

    function save_segment_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        //$this->form_validation->set_rules('segmentID[]', 'Segment', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_segemnt_model->save_segment_link());
        }
    }

    function delete_segment_link()
    {
        echo json_encode($this->Group_segemnt_model->delete_segment_link());
    }

    function load_all_companies_segment()
    {
        $company = array();
        $groupSegmentID = $this->input->post('groupSegmentID');
        $comp = customer_company_link($groupSegmentID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupSegmentID'] = $groupSegmentID;
        $html = $this->load->view('system/GroupSegment/ajax/erp_load_company_segments', $data, true);
        echo $html;
    }

    function load_segment_header()
    {
        echo json_encode($this->Group_segemnt_model->load_segment_header());
    }
    function load_all_companies_duplicate()
    {
        $company = array();
        $groupSegmentID = $this->input->post('groupSegmentID');
        $data['extra'] = $this->Group_segemnt_model->fetch_segment_details();
        $comp = customer_company_link($groupSegmentID);
        foreach ($comp as $val)
        {
            $company[] = $val['companyID'];
        }
        $data['companyID'] = $company;
        $data['groupSegmentID'] = $groupSegmentID;
        $html = $this->load->view('system/GroupSegment/ajax/erp_load_company_segment_duplicate', $data, true);
        echo $html;
    }
    function save_segment_duplicate()
    {
        $this->form_validation->set_rules('checkedCompanies[]', 'Company', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array('e', validation_errors()));
        }
        else
        {
            echo json_encode($this->Group_segemnt_model->save_segment_duplicate());
        }
    }
}
