<?php

class MFQ_OverHead extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_OverHead_model');
    }

    function fetch_over_head()
    {
        $this->datatables->select("overHeadID,CONCAT(overHeadCode, ' - ', srp_erp_mfq_overhead.description) AS item_description, overHeadCode,srp_erp_mfq_overhead.description as description,UnitDes,GLDescription,srp_erp_mfq_segment.description as segmentDesc,srp_erp_mfq_overhead.rate as rate
        , ( CASE WHEN ( typeID = 1 ) THEN 'Overhead' WHEN ( typeID = 2 ) THEN 'Third Party Service' ELSE ' ' END ) AS service_type", false)
            ->from('srp_erp_mfq_overhead')
            ->join('srp_erp_unit_of_measure', 'unitOfMeasureID = UnitID','left')
            ->join('srp_erp_chartofaccounts', 'financeGLAutoID = GLAutoID', 'left')
            ->join('srp_erp_mfq_segment', 'srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segment.mfqSegmentID','left');
        $this->datatables->where('srp_erp_mfq_overhead.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('overHeadCategoryID', 1);
        if (!empty($this->input->post('type_filter'))) {
            $this->datatables->where('typeID', $this->input->post('type_filter'));
        }
//        $this->datatables->add_column('item_description', '$1 - $2', 'overHeadCode,description');
        $this->datatables->add_column('edit', '$1', 'editOverHead(overHeadID)');
        echo $this->datatables->generate();
    }

    function fetch_labour()
    {
        $search = $_REQUEST["sSearch"];
        $this->datatables->select('overHeadID,overHeadCode,srp_erp_mfq_overhead.description as description,UnitDes,rate,srp_erp_mfq_segment.description as segmentDesc,GLDescription', false)
            ->from('srp_erp_mfq_overhead')
            ->join('srp_erp_unit_of_measure', 'unitOfMeasureID = UnitID')
            ->join('srp_erp_chartofaccounts', 'financeGLAutoID = GLAutoID','left')
            ->join('srp_erp_mfq_segment', 'srp_erp_mfq_overhead.mfqSegmentID = srp_erp_mfq_segment.mfqSegmentID','left');
        $this->datatables->where('srp_erp_mfq_overhead.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('overHeadCategoryID', 2);
        if($search){
            $this->datatables->where("(srp_erp_mfq_overhead.description LIKE '%$search%' OR overHeadCode LIKE '%$search%' OR srp_erp_mfq_segment.description LIKE '%$search%')");
        }
        $this->datatables->add_column('item_description', '$1 - $2', 'overHeadCode,description');
        $this->datatables->add_column('edit', '$1', 'editLabour(overHeadID)');
        echo $this->datatables->generate();
    }

    function save_over_head()
    {
        $this->form_validation->set_rules('service_type', 'Service Type', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
       
        $this->form_validation->set_rules('mfqSegmentID', 'Segment', 'trim|required');

        $flowserve = getPolicyValues('MANFL', 'All');

        if($flowserve =='FlowServe' ){
            $this->form_validation->set_rules('from_date', 'From Date', 'trim|required');
            $this->form_validation->set_rules('to_date', 'To Date', 'trim|required');
        }else{
            if($flowserve != 'Micoda'){
                $this->form_validation->set_rules('financeGLAutoID', 'GL Code', 'trim|required');
            }
        }
        //$this->form_validation->set_rules('overHeadCategoryID', 'Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo $this->session->set_flashdata($msgtype = 'e', validation_errors());
            //echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_OverHead_model->save_over_head());
        }
    }

    function save_labour()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('rate', 'Rate', 'trim|required');
        //$this->form_validation->set_rules('overHeadCategoryID', 'Category', 'trim|required');
        $this->form_validation->set_rules('mfqSegmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('financeGLAutoID', 'GL Code', 'trim|required');

        $flowserve = getPolicyValues('MANFL', 'All');

        if($flowserve =='FlowServe'){
            $this->form_validation->set_rules('from_date', 'From Date', 'trim|required');
            $this->form_validation->set_rules('to_date', 'To Date', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array($msgtype = 'e', validation_errors()));

        } else {
            echo json_encode($this->MFQ_OverHead_model->save_labour());
        }
    }

    function editOverHead(){
        echo json_encode($this->MFQ_OverHead_model->editOverHead());
    }

    function fetch_itemrecord()
    {
        echo json_encode($this->MFQ_OverHead_model->fetch_itemrecord());
    }

    function fetch_related_uom_id()
    {
        echo json_encode($this->MFQ_OverHead_model->fetch_related_uom_id());
    }
}
