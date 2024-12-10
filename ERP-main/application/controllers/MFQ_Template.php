<?php

class MFQ_Template extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Template_model');
    }

    function fetch_workflow_template()
    {
        $this->datatables->select('srp_erp_mfq_workflowtemplate.workFlowTemplateID as workFlowTemplateID,srp_erp_mfq_workflowtemplate.workFlowID,description,pageNameLink,workFlowDescription', false)
            ->from('srp_erp_mfq_workflowtemplate')
            ->join('srp_erp_mfq_systemworkflowcategory', 'srp_erp_mfq_systemworkflowcategory.workFlowID = srp_erp_mfq_workflowtemplate.workFlowID');
        $this->datatables->add_column('edit', '<span class="pull-right"><a onclick="edit_work_flow_template($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>', 'workFlowTemplateID');
        echo $this->datatables->generate();

    }

    function fetch_template()
    {
        $this->datatables->select('templateMasterID,templateDescription,industryTypeDescription', false)
            ->from('srp_erp_mfq_templatemaster')
            ->join('srp_erp_mfq_industrytypes', 'srp_erp_mfq_industrytypes.industrytypeID = srp_erp_mfq_templatemaster.industryID')
            ->where('companyID', current_companyID());
        //$this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_template_create\',$1,\'Edit Workflow\',\'MFQ\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_workflow_master($1);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span> ', 'templateMasterID');

        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="link_item_master_mfq($1);">
            <span title="Assign Item" rel="tooltip" class="fa fa-level-down" ></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="#" onclick="fetchPage(\'system/mfq/mfq_template_create\',$1,\'Edit Workflow\',\'MFQ\')">
            <span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a onclick="delete_workflow_master($1);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span> ', 'templateMasterID');
        echo $this->datatables->generate();

    }

    function save_work_flow_template()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('workFlowID', 'Work Flow ID', 'trim|required');
        $this->form_validation->set_rules('pageNameLink', 'Page Link', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_Template_model->save_work_flow_template());
        }
    }

    function save_mfq_template_header()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('industryID', 'Industry', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_Template_model->save_mfq_template_header());
        }
    }

    function save_mfq_template_detail()
    {
        $this->form_validation->set_rules('workFlowTemplateID', 'Workflow', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('sortorder', 'Sortorder', 'trim|required');
        $sortorder = $this->input->post('sortorder');
        $templateMasterID = $this->input->post('templateMasterID');

        $result = $this->db->query("SELECT sortOrder FROM `srp_erp_mfq_templatedetail` where templateMasterID = $templateMasterID AND sortOrder = $sortorder")->row_array();

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if(!empty($result))
                {
                    echo json_encode(array('e', 'Sort Order already assigned.'));
                }else
                {
                    echo json_encode($this->MFQ_Template_model->save_mfq_template_detail());
                }
        }
    }

    function save_mfq_qa_criteria()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sortorder', 'trim|required');
        $this->form_validation->set_rules('inputType', 'Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_Template_model->save_mfq_qa_criteria());
        }
    }

    function edit_work_flow_template()
    {
        echo json_encode($this->MFQ_Template_model->edit_work_flow_template());
    }

    function load_template_master_header()
    {
        echo json_encode($this->MFQ_Template_model->load_template_master_header());
    }

    function load_workflow_design()
    {
        $data = array();
        $data["detail"] = $this->MFQ_Template_model->load_template_detail();
        $data["type"] = $this->input->post('type');
        $data["templateMasterID"] = $this->input->post('templateMasterID');
        $this->load->view('system/mfq/ajax/workflow_template', $data);
    }

    function load_workflow_process_design()
    {
        $data = array();
        $data["detail"] = $this->MFQ_Template_model->load_work_process_template_detail();
        $data["customDetail"] = $this->MFQ_Template_model->load_custom_work_process_template_detail();
        $data["type"] = $this->input->post('type');
        $this->load->view('system/mfq/ajax/workflow_process_template', $data);
    }

    function load_qa_specification()
    {
        $data = array();
        $data["detail"] = $this->MFQ_Template_model->load_qa_specification();
        $this->load->view('system/mfq/ajax/qa_specification', $data);
    }

    function get_workflow_template()
    {
        $data = array();
        $data['workFlowID'] = $this->input->post('workFlowID');
        $data['documentID'] = $this->input->post('documentID');
        $data['templateMasterID'] = $this->input->post('templateMasterID');
        $data['templateDetailID'] = $this->input->post('templateDetailID');
        if ($this->input->post('type') == 1) {
            $data['workProcessID'] = $this->input->post('templateMasterID');
            $data['mfqItemID'] = "";
        } else {
            $data['workProcessID'] = $this->input->post('workProcessID');
            $data['mfqItemID'] = $this->input->post('mfqItemID');

        }
        $data['linkWorkFlowID'] = $this->input->post('linkworkFlow');
        $data['type'] = $this->input->post('type');
        if(isset($_POST["tab"])){
            $data['tab'] = $this->input->post('tab');
        }

        $this->load->view($this->input->post('pageNameLink'), $data);
    }

    function attachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->MFQ_Template_model->attachement_upload());
        }
    }

    function load_attachments()
    {
        $data = $this->MFQ_Template_model->load_attachments();
        /*$data['workProcessID'] =  $this->input->post('workProcessID');
        $data['workFlowID'] =  $this->input->post('workFlowID');
        $data['documentID'] =  $this->input->post('documentID');
        $this->load->view('system/mfq/ajax/attachment_view', $data);*/
        echo json_encode($data);
    }

    function save_workprocess_crew()
    {
        $this->form_validation->set_rules('crewID[]', 'Crew', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->MFQ_Template_model->save_workprocess_crew());
        }
    }

    function save_workprocess_machine()
    {
        $this->form_validation->set_rules('mfq_faID[]', 'Machine', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->MFQ_Template_model->save_workprocess_machine());
        }
    }

    function fetch_crew()
    {
        echo json_encode($this->MFQ_Template_model->fetch_crew());
    }

    function fetch_machine()
    {
        echo json_encode($this->MFQ_Template_model->fetch_machine());
    }

    function fetch_workprocess_crew()
    {
        echo json_encode($this->MFQ_Template_model->fetch_workprocess_crew());
    }

    function fetch_workprocess_machine()
    {
        echo json_encode($this->MFQ_Template_model->fetch_workprocess_machine());
    }

    function delete_workprocess_attachment()
    {
        $this->load->library('s3');
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');

        /**AWS S3 delete object */
        $result = $this->s3->delete($myFileName);
        /** end of AWS s3 delete object */
        if ($result) {
            $this->db->delete('srp_erp_mfq_workflowattachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    function delete_workprocess_crew(){
        echo json_encode($this->MFQ_Template_model->delete_workprocess_crew());
    }

    function delete_workprocess_machine(){
        echo json_encode($this->MFQ_Template_model->delete_workprocess_machine());
    }

    function link_workflow(){
        echo json_encode($this->MFQ_Template_model->link_workflow());
    }

    function delete_workflow_detail()
    {
        $templateMasterID = $this->input->post('templateMasterID');
        $templateDetailID = $this->input->post('templateDetailID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_job');
        $this->db->where('workFlowTemplateID', trim($templateMasterID));
        $exist = $this->db->get()->row_array();
        if(!$exist){
            $result = $this->db->delete('srp_erp_mfq_templatedetail', array('templateDetailID' => trim($templateDetailID)));
            if ($result) {
                echo json_encode(array('s', 'Workflow deleted successfully.'));
            } else {
                echo json_encode(array('e', 'Workflow deleted failed.'));
            }
        }else{
            echo json_encode(array('e', 'You cannot delete this because it has been used in another template'));
        }
    }

    function delete_workflow_master()
    {
        $templateMasterID = $this->input->post('templateMasterID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_job');
        $this->db->where('workFlowTemplateID', trim($templateMasterID));
        $exist = $this->db->get()->row_array();
        if(!$exist){
            $result = $this->db->delete('srp_erp_mfq_templatemaster', array('templateMasterID' => trim($templateMasterID)));
            if ($result) {
                echo json_encode(array('s', 'Workflow deleted successfully.'));
            } else {
                echo json_encode(array('e', 'Workflow deleted failed.'));
            }
        }else{
            echo json_encode(array('e', 'You cannot delete this because it has been used in another template'));
        }
    }

    function load_custome_workflow_design()
    {
        $data = array();
        $data["detail"] = $this->MFQ_Template_model->load_template_detail();
        $data["templateMasterID"] = $this->input->post('templateMasterID');
        $this->load->view('system/mfq/ajax/customize_workflow_template', $data);
    }

    function fetch_workprocess_detail(){
        echo json_encode($this->MFQ_Template_model->fetch_workprocess_detail());
    }
    function check_link_job_card()
    {
        echo json_encode($this->MFQ_Template_model->check_link_job_card());
    }

    function load_workflow_process_design_process_based()
    {
        $data = array();
        $data["detail"] = $this->MFQ_Template_model->load_work_process_template_detail();
        $data["customDetail"] = $this->MFQ_Template_model->load_custom_work_process_template_detail();
        $data["type"] = $this->input->post('type');
        $this->load->view('system/mfq/ajax/workflow_process_template_process_based', $data);
    }

    function get_workflow_template_process_based()
    {
        $data = array();
        $data['workFlowID'] = $this->input->post('workFlowID');
        $data['documentID'] = $this->input->post('documentID');
        $data['templateMasterID'] = $this->input->post('templateMasterID');
        $data['templateDetailID'] = $this->input->post('templateDetailID');
        $data['workFlowTemplateID'] = $this->input->post('workFlowTemplateID');
        if ($this->input->post('type') == 1) {
            $data['workProcessID'] = $this->input->post('templateMasterID');
            $data['mfqItemID'] = "";
        } else {
            $data['workProcessID'] = $this->input->post('workProcessID');
            $data['mfqItemID'] = $this->input->post('mfqItemID');

        }
        $data['linkWorkFlowID'] = $this->input->post('linkworkFlow');
        $data['type'] = $this->input->post('type');
        if(isset($_POST["tab"])){
            $data['tab'] = $this->input->post('tab');
        }

        $this->load->view($this->input->post('pageNameLink'), $data);
    }

    function load_work_process_responsible(){

        $workProcessID = $this->input->post('workProcessID');
        $data = array();

        $job_details = $this->db->where('workProcessID',$workProcessID)->from('srp_erp_mfq_job')->get()->row_array();

        if($job_details && $job_details['workFlowTemplateID']){

            $_POST['templateMasterID'] = $job_details['workFlowTemplateID'];

            $data["detail"] = $this->MFQ_Template_model->load_template_detail();
            $data["workProcess"] = $this->MFQ_Template_model->get_setup_workflow_responsible();
            $data['workProcessID'] = $workProcessID;

        }

        $pageurl = 'system/mfq/ajax/mfq_job_responsible';

        $this->load->view($pageurl,$data);
    }

    function assign_stage_responsible(){
        echo json_encode($this->MFQ_Template_model->assign_stage_responsible());
    }

    function update_ownerID(){

        $workProcessID = $this->input->post('workProcessID');
        $ownerID = $this->input->post('ownerID');
        $data = array();

        if($ownerID){

            if($ownerID){
                $data['ownerID'] = $ownerID;
            }
    
            $this->db->where('workProcessID',$workProcessID)->update('srp_erp_mfq_job',$data);

        }

        return TRUE;

    }
}