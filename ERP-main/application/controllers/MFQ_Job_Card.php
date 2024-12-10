<?php

class MFQ_Job_Card extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Job_Card_model');
        $this->load->model('MFQ_Job_model');
    }

    function fetch_material()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_material());
    }

    function fetch_material_by_id()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_material_by_id());
    }

    function fetch_overhead()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_overhead());
    }

    function fetch_machine()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_machine());
    }

    function fetch_labourtask()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_labourtask());
    }

    function fetch_jobcard_material_consumption()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_jobcard_material_consumption());
    }

    function fetch_jobcard_labour_task()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_jobcard_labour_task());
    }

    function fetch_jobcard_overhead_cost()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_jobcard_overhead_cost());
    }

    function fetch_jobcard_machine_cost(){
        echo json_encode($this->MFQ_Job_Card_model->fetch_jobcard_machine_cost());
    }

    function save_workprocess_jobcard()
    {
        $this->form_validation->set_rules('jobNo', 'Job No', 'trim|required');
        /* $this->form_validation->set_rules('bomID', 'BOM', 'trim|required');
         $this->form_validation->set_rules('mfqCustomerAutoID', 'Customer', 'trim|required');
         $this->form_validation->set_rules('mfqSegmentID', 'Segment', 'trim|required');
         $this->form_validation->set_rules('quotationRef', 'Quotation Reference ID', 'trim|required');*/
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

       /* $this->form_validation->set_rules('mfqItemID[]', 'Material Consumption', 'trim|required');
        $this->form_validation->set_rules('qtyUsed[]', 'Qty Used', 'trim|required');
        $this->form_validation->set_rules('unitCost[]', 'Unit Cost', 'trim|required');
        $this->form_validation->set_rules('markUp[]', 'Markup', 'trim|required');*/

        /* $this->form_validation->set_rules('labourTask[]', 'Labour Task', 'trim|required');
         $this->form_validation->set_rules('la_activityCode[]', 'Activity Code', 'trim|required');
         $this->form_validation->set_rules('la_segmentID[]', 'Segment', 'trim|required');
         $this->form_validation->set_rules('la_hourlyRate[]', 'Hour Rate', 'trim|required');
         $this->form_validation->set_rules('la_totalHours[]', 'Total Hours', 'trim|required');*/

        /*$this->form_validation->set_rules('overHeadID[]', 'Over head', 'trim|required');
        $this->form_validation->set_rules('oh_activityCode[]', 'Activity Code', 'trim|required');
        $this->form_validation->set_rules('oh_segmentID[]', 'Segment', 'trim|required');
        $this->form_validation->set_rules('oh_hourlyRate[]', 'Hour Rate', 'trim|required');
        $this->form_validation->set_rules('oh_totalHours[]', 'Total Hours', 'trim|required');*/


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_Card_model->save_workprocess_jobcard());
        }
    }

    function save_late_overhead_cost_job()
    {
        $this->form_validation->set_rules('jobNo', 'Job No', 'trim|required');
        
       // $this->form_validation->set_rules('description', 'Description', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_Card_model->save_late_overhead_cost_job());
        }
    }

    function delete_materialConsumption()
    {
        echo json_encode($this->MFQ_Job_Card_model->delete_materialConsumption());
    }

    function delete_labour_task()
    {
        echo json_encode($this->MFQ_Job_Card_model->delete_labour_task());
    }

    function delete_overhead_cost()
    {
        echo json_encode($this->MFQ_Job_Card_model->delete_overhead_cost());
    }

    function delete_machine_cost()
    {
        echo json_encode($this->MFQ_Job_Card_model->delete_machine_cost());
    }

    function load_data_from_bom()
    {
        echo json_encode($this->MFQ_Job_Card_model->load_data_from_bom());
    }

    function fetch_jobcard_print()
    {
        $data = array();
        $data["workProcessID"] = $this->input->post('workProcessID');
        if($this->input->post('type') == 1){
            $data["material"] = "";
            $data["overhead"] = "";
            $data["labourTask"] = "";
            $data["machine"] = "";
            $data["thiredparty"]= "";
        }else{
            $data["material"] = $this->MFQ_Job_Card_model->fetch_jobcard_material_consumption();
            $data["overhead"] = $this->MFQ_Job_Card_model->fetch_jobcard_overhead_cost();
            $data["labourTask"] = $this->MFQ_Job_Card_model->fetch_jobcard_labour_task();
            $data["machine"] = $this->MFQ_Job_Card_model->fetch_jobcard_machine_cost();
            $data["thiredparty"] = $this->MFQ_Job_Card_model->fetch_jobcard_thiredParty_cost();
        }

        $data["jobheader"] = $this->MFQ_Job_model->load_job_header();
        $data['estimateTotal'] = $this->MFQ_Job_model->get_bill_of_material_detail();

        $data["jobCardID"] = $this->input->post('jobCardID');
        $data["workFlowID"] = $this->input->post('workFlowID');
        $data["templateDetailID"] = $this->input->post('templateDetailID');
        $data["linkworkFlow"] = $this->input->post('linkworkFlow');
        $data["templateMasterID"] = $this->input->post('templateMasterID');
        $data["type"] = $this->input->post('type');

        if ($this->input->post('linkworkFlow')) {
            $data["prevJobCard"] = get_prev_job_card($this->input->post('workProcessID'), $this->input->post('workFlowID'), $this->input->post('linkworkFlow'), $this->input->post('templateDetailID'), $this->input->post('templateMasterID'));
        }
        $data["jobcardheader"] = get_job_cardID($this->input->post('workProcessID'), $this->input->post('workFlowID'), $this->input->post('templateDetailID'));
        $html = $this->load->view('system/mfq/ajax/job_card_print', $data,true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L',1,'L');
        }
    }

    function fetch_finish_goods()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_finish_goods());
    }

    function fetch_goods()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_goods());
    }

    function fetch_job_detail(){
        echo json_encode($this->MFQ_Job_Card_model->fetch_job_detail());
    }

    function get_unit_of_measure_conversion(){
        echo json_encode($this->MFQ_Job_Card_model->get_unit_of_measure_conversion());
    }

    function fetch_po_unit_cost(){
        echo json_encode($this->MFQ_Job_Card_model->fetch_po_unit_cost());
    }
    function delete_thirdparty_cost(){
        echo json_encode($this->MFQ_Job_Card_model->delete_thirdparty_cost());
    }

    function calculateDailyComputation_overhead(){
        echo json_encode($this->MFQ_Job_Card_model->calculateDailyComputation_overhead());
    }

    function calculateDailyComputation_machine(){
        echo json_encode($this->MFQ_Job_Card_model->calculateDailyComputation_machine());
    }

    function calculateDailyComputation_labourTask(){
        echo json_encode($this->MFQ_Job_Card_model->calculateDailyComputation_labourTask());
    }

    function calculateDailyComputation_materialCharge(){
        echo json_encode($this->MFQ_Job_Card_model->calculateDailyComputation_materialCharge());
    }

    function save_workprocess_jobcard_process_based()
    {
        $this->form_validation->set_rules('jobNo', 'Job No', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_Card_model->save_workprocess_jobcard_process_based());
        }
    }

    function fetch_bom_process_based()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_bom_process_based());
    }
    function fetch_finish_goods_jobcard()
    {
        echo json_encode($this->MFQ_Job_Card_model->fetch_finish_goods_jobcard());
    }

    function get_inventory_item_batch()
    {
        echo json_encode($this->MFQ_Job_Card_model->get_inventory_item_batch());
    }

    

}