<?php


use Mpdf\Mpdf;

class Appraisal extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('appraisal_model');
        $this->load->helpers('appraisal');
    }

    public function get_departments()
    {
        $company_id = $this->input->post('company_id', TRUE);
        $departments_dataset = $this->appraisal_model->get_departments($company_id);
        $departments = array();
        //var_dump($departments_dataset);exit;
        foreach ($departments_dataset as $item) {
            $department['department_description'] = $item->department_description;
            $department['department_master_id'] = $item->department_master_id;
            $department['hod_name'] = $item->Ename1;
            $department['sub_departments'] = $this->appraisal_model->get_sub_departments($item->department_master_id);
            $department['logo'] = $item->DepartmentLogo ? $this->s3->createPresignedRequest($item->DepartmentLogo, '+24 hour') : '';
            array_push($departments, $department);
        }
        echo json_encode($departments);
    }

    public function get_department_logo(){
        $DepartmentMasterID = $this->input->post('department_id');
        $departmentLogo = $this->db->query("select * from srp_departmentmaster where DepartmentMasterID=$DepartmentMasterID")->row()->DepartmentLogo;
        if($departmentLogo==""){
            echo "/images/No_Image.png";
        }else{
            $departmentLogo = $this->s3->createPresignedRequest($departmentLogo, '+24 hour');
            echo $departmentLogo;
        }
    }

    public function save_department_approval_levels()
    {

    }

    public function insert_approval_setup()
    {
        $approval_type = $this->input->post('approval', TRUE);
        $approval_level = $this->input->post('approval_level', TRUE);
        $department_id = $this->input->post('department_id', TRUE);
        $company_id = $this->input->post('company_id', TRUE);
        $this->appraisal_model->insert_approval_setup($approval_type, $approval_level, $department_id, $company_id);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function add_sub_departments()
    {
        $company_id = $this->input->post('company_id', TRUE);
        $selected_department_id = $this->input->post('selected_department_id', TRUE);
        $sub_department_description = $this->input->post('sub_department_description', TRUE);
        $sub_department_code = $this->input->post('sub_department_code', TRUE);

        $this->appraisal_model->add_sub_departments($company_id, $selected_department_id, $sub_department_description, $sub_department_code);
    }

    public function edit_sub_departments()
    {
        $sub_department_id = $this->input->post('sub_department_id', TRUE);
        $sub_department_description = $this->input->post('sub_department_description', TRUE);
        $sub_department_code = $this->input->post('sub_department_code', TRUE);
        $this->appraisal_model->edit_sub_departments($sub_department_id, $sub_department_description, $sub_department_code);
        $data['status'] = "success";
        echo json_encode($data);
    }

    public function delete_sub_departments()
    {
        $sub_department_id = $this->input->post('sub_department_id', TRUE);
        $data['status'] = $this->appraisal_model->delete_sub_departments($sub_department_id);
        echo json_encode($data);
    }


    public function delete_sub_department_task()
    {
        $task_id = $this->input->post('task_id', TRUE);
        $this->appraisal_model->delete_sub_department_task($task_id);
        $data['status'] = "success";
        echo json_encode($data);
    }

    public function delete_corporate_goal()
    {
        $goal_id = $this->input->post('goal_id', TRUE);
        $data['status'] = $this->appraisal_model->delete_corporate_goal($goal_id);
        echo json_encode($data);
    }

    public function insert_corporate_objective()
    {
        $company_id = $this->input->post('company_id', TRUE);
        $objective = $this->input->post('objective', TRUE);

        $this->appraisal_model->insert_corporate_objective($company_id, $objective);
    }

    public function get_corporate_objectives()
    {
        $company_id = $this->input->post('company_id', TRUE);
        $objectives = $this->appraisal_model->get_corporate_objectives($company_id);
        echo json_encode($objectives);
    }

    public function update_corporate_objective()
    {
        $corporate_objective_id = $this->input->post('corporate_objective_id', TRUE);
        $objective = $this->input->post('objective', TRUE);
        $query_status = $this->appraisal_model->update_corporate_objective($corporate_objective_id, $objective);
        if ($query_status == 1) {
            $data['status'] = 'success';
            echo json_encode($data);
        } else {
            $data['status'] = 'failed';
            echo json_encode($data);
        }
    }

    public function delete_corporate_objective()
    {
        $corporate_objective_id = $this->input->post('corporate_objective_id', TRUE);
        $data['status'] = $this->appraisal_model->delete_corporate_objective($corporate_objective_id);
        echo json_encode($data);
    }

    public function is_objective_already_exist()
    {
        $objective = trim($this->input->post('objective', true));
        $id = $this->input->post('id', true);
        $data['status'] = $this->appraisal_model->is_objective_already_exist($objective, $id);
        echo json_encode($data);
    }

    public function insert_corporate_goal()
    {
        $goal_objectives_array = $this->input->post('goal_objective_array', true);
        $narration = $this->input->post('narration', true);
        $from_date = $this->input->post('from_date', true);
        $to_date = $this->input->post('to_date', true);
        $company_id = $this->input->post('company_id', true);
        $confirmed = $this->input->post('confirmed', true);
        $appraisal_type = $this->input->post('appraisal_type', true);
        $selected_template = $this->input->post('selected_template', true);

        $this->appraisal_model->insert_corporate_goal($narration, $from_date, $to_date, $company_id, $goal_objectives_array, $confirmed, $appraisal_type, $selected_template);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    function fetch_corporate_goal_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        //error_reporting(0);
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');

        $currentuserid = current_userID();
        if ($approvedYN == 0) {
            $this->datatables->select('srp_erp_apr_corporate_goal.document_id as document_id,srp_erp_apr_corporate_goal.confirmedYN as confirmedYN,DATE_FORMAT(srp_erp_apr_corporate_goal.to,\'' . $convertFormat . '\') as to_date,DATE_FORMAT(srp_erp_apr_corporate_goal.from,\'' . $convertFormat . '\') as from_date, DATE_FORMAT(srp_erp_apr_corporate_goal.created_at,\'' . $convertFormat . '\') as created_at, srp_erp_apr_corporate_goal.id as goal_id, srp_erp_apr_corporate_goal.narration as narration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID');

            $this->datatables->from('srp_erp_apr_corporate_goal');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_apr_corporate_goal.id AND srp_erp_documentapproved.approvalLevelID = srp_erp_apr_corporate_goal.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_apr_corporate_goal.currentLevelNo');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('CG'));
            $this->datatables->where_in('srp_erp_approvalusers.documentID', array('CG'));
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_apr_corporate_goal.is_deleted', 0);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_apr_corporate_goal.company_id', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            //   $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('approval_change', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
//
            $this->datatables->add_column('confirmedYN', '$1', 'corporate_goal_confirm_status(confirmedYN)');//corporate_goal_confirm_status(confirmedYN)
            $this->datatables->add_column('approved', '$1', 'cg_approval_drilldown(approvedYN,"CG",goal_id)');
            $this->datatables->add_column('edit', '$1', 'corporate_goal_approval_action(goal_id,approvalLevelID,approvedYN,documentApprovedID,goal_id,0)');
//            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            echo $this->datatables->generate();

        } else {
            $this->datatables->select('srp_erp_apr_corporate_goal.document_id as document_id,srp_erp_apr_corporate_goal.confirmedYN as confirmedYN,DATE_FORMAT(srp_erp_apr_corporate_goal.to,\'' . $convertFormat . '\') as to_date,DATE_FORMAT(srp_erp_apr_corporate_goal.from,\'' . $convertFormat . '\') as from_date, DATE_FORMAT(srp_erp_apr_corporate_goal.created_at,\'' . $convertFormat . '\') as created_at, srp_erp_apr_corporate_goal.id as goal_id, srp_erp_apr_corporate_goal.narration as narration,\'xxx\' as contractAutoID,\'xxx\'  as companyCode, \'xxx\'   as contractCode,  \'xxx\'   as  contractNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,  \'Dec-12\' AS contractDate, 2 as transactionCurrencyDecimalPlaces ,100 as transactionCurrency,\'200\' as total_value,\'200\' as transactionAmount ,\'CusName2\' as  customerName');
            //$this->datatables->select('*');
            //$this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
            //$this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
            $this->datatables->from('srp_erp_apr_corporate_goal');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_apr_corporate_goal.id AND srp_erp_documentapproved.approvalLevelID = srp_erp_apr_corporate_goal.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_apr_corporate_goal.currentLevelNo');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('CG'));
            $this->datatables->where_in('srp_erp_approvalusers.documentID', array('CG'));
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_apr_corporate_goal.is_deleted', 0);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_apr_corporate_goal.company_id', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            //   $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('approval_change', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
//
            $this->datatables->add_column('confirmedYN', '$1', 'corporate_goal_confirm_status(confirmedYN)');//corporate_goal_confirm_status(confirmedYN)
            $this->datatables->add_column('approved', '$1', 'cg_approval_drilldown(approvedYN,"CG",goal_id)');
            $this->datatables->add_column('edit', '$1', 'corporate_goal_approval_action(goal_id,approvalLevelID,approvedYN,documentApprovedID,goal_id,0)');
//            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            echo $this->datatables->generate();
        }

    }

    public function is_date_range_valid()
    {
        $from_date2 = $this->input->post('from_date', true);
        $to_date2 = $this->input->post('to_date', true);
        $company_id = $this->input->post('company_id', true);
        $start_two = datetime::createfromformat('Y-m-d', $from_date2);
        $end_two = datetime::createfromformat('Y-m-d', $to_date2);
        $corporate_goals = $this->appraisal_model->get_corporate_goals($company_id);
        $is_valid = true;

        if ($end_two < $start_two) {
            $data['status'] = 'invalid';
            echo json_encode($data);
            exit;
        }
        foreach ($corporate_goals as $item) {
            $start_one = datetime::createfromformat('Y-m-d H:i:s', $item->from);
            $end_one = datetime::createfromformat('Y-m-d H:i:s', $item->to);
            if ($start_one <= $end_two && $end_one >= $start_two) { //If the dates overlap
                $is_valid = false;
            }
        }
        if ($is_valid) {
            $data['status'] = 'valid';
        } else {
            $data['status'] = 'invalid';
        }
        echo json_encode($data);
    }

    function test()
    {

    }


    public function update_corporate_goal()
    {
        $new_goal_objective_array = $this->input->post('new_goal_objective_array', true);
        $edited_goal_objective_array = $this->input->post('edited_goal_objective_array', true);
        $id_list_for_delete = $this->input->post('id_list_for_delete', true);
        $narration = $this->input->post('narration', true);
        $from_date = $this->input->post('from_date', true);
        $to_date = $this->input->post('to_date', true);
        $company_id = $this->input->post('company_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $confirmed = $this->input->post('confirmed', true);
        $document_id = $this->input->post('document_id', true);
        $appraisal_type = $this->input->post('appraisal_type', true);
        $selected_template = $this->input->post('selected_template', true);

        $this->appraisal_model->update_corporate_goal($narration, $from_date, $to_date, $company_id, $new_goal_objective_array, $edited_goal_objective_array, $id_list_for_delete, $goal_id, $confirmed, $document_id, $appraisal_type, $selected_template);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function get_corporate_goals()
    {
        $company_id = $this->input->post('company_id', true);
        $corporate_goals = $this->appraisal_model->get_corporate_goals($company_id);
        echo json_encode($corporate_goals);
    }

    public function get_corporate_goals_for_dashboard()
    {
        $company_id = $this->input->post('company_id', true);
        $corporate_goals = $this->appraisal_model->get_corporate_goals_for_dashboard($company_id);
        echo json_encode($corporate_goals);
    }

    public function get_corporate_goal_details()
    {
        $goal_id = $this->input->post('goal_id', true);
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        echo json_encode($goal_details_and_objectives);
    }

    public function get_employee_tasks_for_employee_wise_performance_report()
    {
        $department_id = $this->input->post('department_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $employee_id = $this->input->post('employee_id', true);
        $employee_wise_performance_data = $this->appraisal_model->get_employee_tasks_for_employee_wise_performance_report($department_id, $goal_id, $employee_id);
        //var_dump($employee_wise_performance_data);exit;
        if ($employee_wise_performance_data == null) {
            $data = array();
            echo json_encode('failed');
        } else {
            echo json_encode($employee_wise_performance_data);
        }

    }

    public function performance_evaluation_summary()
    {
        $department_id = $this->input->post('department_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $employee_id = $this->input->post('employee_id', true);
        $employee_wise_performance_data = $this->appraisal_model->get_employee_tasks_for_employee_wise_performance_report($department_id, $goal_id, $employee_id);
//var_dump($employee_wise_performance_data);;exit;
        $number_of_tasks = 0;
        $number_of_completed_tasks = 0;
        if (sizeof($employee_wise_performance_data) > 0) {
            foreach ($employee_wise_performance_data as $item) {
                $number_of_tasks++;
                $is_approved_by_manager = $item['is_approved_by_manager'];
                $completion = $item['completion'];
                if ($is_approved_by_manager == 1 && $completion == 100) {
                    $number_of_completed_tasks++;
                }
            }
            $data['status'] = "success";
        } else {
            $data['status'] = "success";
        }
        if ($number_of_completed_tasks != 0) {
            $objective_based_task_completion_percentage_of_employee = round((($number_of_completed_tasks / $number_of_tasks) * 100), 2);
        } else {
            $objective_based_task_completion_percentage_of_employee = 0;
        }

        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        //var_dump($goal_details_and_objectives);
        $softskills_based_percentage_of_employee = $this->appraisal_model->fetch_employee_skills_performance_appraisal_for_summary_report($employee_id, $goal_details_and_objectives);
        $empwise_performance_remarks = $this->appraisal_model->empwise_performance_remarks($goal_id, $department_id, $employee_id);
        $company_id = current_companyID();
        $skill_performance_remarks = $this->appraisal_model->skill_performance_remarks($employee_id, $goal_details_and_objectives['goal_details'][0]->softskills_template_id, $company_id, $goal_id);
        $data['hod_id'] = 0;
        $data['appraisal_start_date'] = null;
        $data['appraisal_end_date'] = null;

        $hod_data = $this->appraisal_model->get_department_hod($department_id, $goal_id);
        $goal_details = $this->appraisal_model->get_corporate_goal_details($goal_id);

        $data['appraisal_start_date'] = $goal_details['goal_details'][0]->from;
        $data['appraisal_end_date'] = $goal_details['goal_details'][0]->to;
        if(!empty($hod_data)){
            $data['hod_id'] = $hod_data[0]['EIdNo'];
        }else{
            $data['hod_id']=null;
        }


        $data['objective_based_percentage_of_employee'] = $objective_based_task_completion_percentage_of_employee;
        $data['softskills_based_percentage_of_employee'] = $softskills_based_percentage_of_employee;

        //empwise remarks
        if ($empwise_performance_remarks != null) {
            $data['manager_comment'] = $empwise_performance_remarks->manager_comment;
            $data['suggested_reward'] = $empwise_performance_remarks->suggested_reward;
            $data['identified_training_needs'] = $empwise_performance_remarks->identified_training_needs;
            $data['special_remarks_from_hod'] = $empwise_performance_remarks->special_remarks_from_hod;
            $data['special_remarks_from_emp'] = $empwise_performance_remarks->special_remarks_from_emp;
        } else {
            $data['manager_comment'] = "";
            $data['suggested_reward'] = "";
            $data['identified_training_needs'] = "";
            $data['special_remarks_from_hod'] = "";
            $data['special_remarks_from_emp'] = "";
        }

        //softskills remarks
        if ($skill_performance_remarks != null) {
            $data['manager_comment_skill'] = $skill_performance_remarks->manager_comment;
            $data['suggested_reward_skill'] = $skill_performance_remarks->suggested_reward;
            $data['identified_training_needs_skill'] = $skill_performance_remarks->identified_training_needs;
            $data['special_remarks_from_hod_skill'] = $skill_performance_remarks->special_remarks_from_hod;
            $data['special_remarks_from_emp_skill'] = $skill_performance_remarks->special_remarks_from_emp;
        } else {
            $data['manager_comment_skill'] = "";
            $data['suggested_reward_skill'] = "";
            $data['identified_training_needs_skill'] = "";
            $data['special_remarks_from_hod_skill'] = "";
            $data['special_remarks_from_emp_skill'] = "";
        }

        echo json_encode($data);
    }


    public function insert_softskills_template()
    {
        $company_id = $this->input->post('company_id', true);
        $template_name = $this->input->post('template_name', true);
        $markingType = $this->input->post('markingType', true);
        $this->appraisal_model->insert_softskills_template($company_id, $template_name, $markingType);
        $data['status'] = "success";
        echo json_encode($data);
    }

    public function save_template_designation()
    {
        $selected_designations = $this->input->post('selected_designations');
        $template_id = $this->input->post('template_id');
        $res = $this->appraisal_model->save_template_designation($template_id, $selected_designations);
        echo json_encode($res);
    }

    public function load_selected_designations()
    {
        $template_id = $this->input->post('template_id');
        $q = $this->db->query("SELECT
	srp_designation.DesignationID,
	srp_designation.DesDescription
FROM
	srp_erp_apr_softskillstemplatedesignations st 
	join srp_designation on srp_designation.DesignationID=st.designationID
WHERE
	softskillTemplateID =$template_id");

        $data['selected_id_array'] = array();

        foreach ($q->result() as $row) {
            array_push($data['selected_id_array'], $row->DesignationID);
        }

        $data['selected_designations'] = $q->result();

        echo json_encode($data);

    }

    public function designations()
    {
        $query = $this->db->query("select * from srp_designation");
        echo json_encode($query->result());
    }

    public function get_softskills_templates()
    {
        $company_id = $this->input->post('company_id', true);
        echo json_encode($this->appraisal_model->get_softskills_templates($company_id));
    }

    public function insert_sub_performance_area()
    {
        $template_id = $this->input->post('template_id', true);
        $parent_id = $this->input->post('parent_id', true);
        $performance_area = $this->input->post('performance_area', true);
        $order = $this->input->post('order', true);
        echo json_encode($this->appraisal_model->insert_sub_performance_area($template_id, $parent_id, $performance_area, $order));
    }

    public function update_sub_performance_area()
    {
        $performance_area_id = $this->input->post('performance_area_id', true);
        $performance_area = $this->input->post('performance_area', true);
        $order = $this->input->post('order', true);

        echo json_encode($this->appraisal_model->update_sub_performance_area($performance_area_id, $performance_area, $order));
    }


    public function get_softskills_template_details()
    {
        $template_id = $this->input->post('template_id', true);
        echo json_encode($this->appraisal_model->get_softskills_template_details($template_id));
    }

    public function get_performance_area_details()
    {
        $performance_area_id = $this->input->post('performance_area_id', true);
        echo json_encode($this->appraisal_model->get_performance_area_details($performance_area_id));
    }

    public function update_performance_area()
    {
        $performance_area_id = $this->input->post('performance_area_id', true);
        $performance_area = $this->input->post('performance_area', true);
        $order = $this->input->post('order', true);
        $this->appraisal_model->update_performance_area($performance_area_id, $performance_area, $order);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function edit_template_name()
    {
        $template_id = $this->input->post('template_id', true);
        $template_name = $this->input->post('template_name', true);
        $res = $this->appraisal_model->edit_template_name($template_id, $template_name);
        echo json_encode($res);
    }

    public function delete_soft_skills_template()
    {
        $template_id = $this->input->post('template_id', true);
        $res = $this->appraisal_model->delete_soft_skills_template($template_id);
        echo json_encode($res);
    }

    public function delete_performance_area()
    {
        $performance_area_id = $this->input->post('performance_area_id', true);
        $this->appraisal_model->delete_performance_area($performance_area_id);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function insert_softskills_grades()
    {
        $template_id = $this->input->post('template_id', true);
        $grades_array = $this->input->post('grades_array', true);
        $this->appraisal_model->insert_softskills_grades($template_id, $grades_array);
        $data['status'] = "success";
        echo json_encode($data['status']);
    }


    public function get_next_number_for_pa()
    {
        $template_id = $this->input->post('template_id', true);
        $data['next_number'] = $this->appraisal_model->get_next_number_for_pa($template_id);
        echo json_encode($data);
    }


    public function get_next_number_for_subpa()
    {
        $template_id = $this->input->post('template_id', true);
        $parent_id = $this->input->post('parent_id', true);
        $data['next_number'] = $this->appraisal_model->get_next_number_for_subpa($template_id, $parent_id);
        echo json_encode($data);
    }

    public function is_index_exist()
    {
        $template_id = $this->input->post('template_id', true);
        $order = $this->input->post('order', true);
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_softskills_performance_area` where softskills_template_id=$template_id and parent_id=0 and `order`=$order");
        if ($query->num_rows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function is_subindex_exist()
    {
        $parent_id = $this->input->post('parent_id', true);
        $order = $this->input->post('order', true);
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_softskills_performance_area` where parent_id=$parent_id and `order`=$order");
        if ($query->num_rows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function insert_performance_area()
    {
        $template_id = $this->input->post('template_id', true);
        $performance_area = $this->input->post('performance_area', true);
        $order = $this->input->post('order', true);
        $this->appraisal_model->insert_performance_area($template_id, $performance_area, $order);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function get_employee_performance_header_details()
    {
        $goal_id = $this->input->post('goal_id', true);
        $department_id = $this->input->post('department_id', true);
        $emp_id = $this->input->post('emp_id', true);
        echo json_encode($this->appraisal_model->get_employee_performance_header_details($goal_id, $department_id, $emp_id));
    }

    public function get_employee_details()
    {
        $emp_id = $this->input->post('employee_id', true);
        $employee_details = $this->appraisal_model->get_employee_details($emp_id);

        echo json_encode($employee_details);
    }

    public function get_employee_departments_data()
    {
        $employee_id = $this->input->post('employee_id', true);
        echo json_encode($this->appraisal_model->get_employee_departments_data($employee_id));
    }

    public function department_appraisal()
    {
        $employee_id = current_userID();
        $department_ids = $this->appraisal_model->get_employee_departments($employee_id);
        $department_appraisals = $this->appraisal_model->department_appraisal($department_ids);
        $department_appraisals_new = array();
        $data['department_appraisals'] = array();
        foreach ($department_appraisals as $item) {
            $department_appraisals_new['goal_id'] = $item->goal_id;
            $department_appraisals_new['narration'] = $item->narration;
            $department_appraisals_new['document_id'] = $item->document_id;
            $department_appraisals_new['from'] = $item->from;
            $department_appraisals_new['created_date'] = $item->document_id;
            $department_appraisals_new['is_closed'] = $item->is_closed;
            $department_appraisals_new['department_appraisal_doc_id'] = $item->department_appraisal_doc_id ? $item->department_appraisal_doc_id : '<center>-</center>';
            $department_appraisals_new['DepartmentMasterID'] = $item->DepartmentMasterID;
            $department_appraisals_new['DepartmentDes'] = $item->DepartmentDes;
            $department_appraisals_new['completed_percentage'] = $this->appraisal_model->get_overall_completion_percentage($item->goal_id, $item->DepartmentMasterID);
            $date = DateTime::createFromFormat("Y-m-d H:i:s", $department_appraisals_new['from']);
            $department_appraisals_new['year'] = $date->format("Y");
            array_push($data['department_appraisals'], $department_appraisals_new);
        }
        echo json_encode($data);
    }

    public function get_performance_based_appraisals_by_department()
    {
        $employee_id = current_userID();
        $department_ids = $this->appraisal_model->get_employee_departments($employee_id);
        $department_appraisals = $this->appraisal_model->performance_based_department_appraisal($department_ids);
        echo json_encode($department_appraisals);
    }


    public function save_emp_softskills_grade()
    {
        $performance_id = $this->input->post('performance_id', true);
        $emp_id = $this->input->post('emp_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $grade_id = $this->input->post('grade_id', true);
        $this->appraisal_model->save_emp_softskills_grade($performance_id, $emp_id, $goal_id, $grade_id);
    }

    public function save_emp_softskills_grade_self_eval()
    {
        $performance_id = $this->input->post('performance_id', true);
        $emp_id = $this->input->post('emp_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $grade_id = $this->input->post('grade_id', true);
        $this->appraisal_model->save_emp_softskills_grade_self_eval($performance_id, $emp_id, $goal_id, $grade_id);
    }


    public function update_manager_comments()
    {
        $this->load->library('Approvals');
        $marking_type = $this->input->post('marking_type', true);
        //if($marking_type == 2){
            $career_and_training_action_plan = $this->input->post('career_and_training_action_plan', true) ? $this->input->post('career_and_training_action_plan', true) : null;
            $manager_assessment_undertaking = $this->input->post('manager_assessment_undertaking', true) ? $this->input->post('manager_assessment_undertaking', true) : null;
        //}
        $suggested_reward = $this->input->post('suggested_reward_input', true);
        $identified_training_needs = $this->input->post('identified_training_needs', true);
        $special_remarks_from_hod = $this->input->post('special_remarks_from_hod', true);
        $template_mapping_id = $this->input->post('template_mapping_id', true);
        $manager_comment = $this->input->post('manager_comment', true);
        $confirmed = $this->input->post('confirmed', true);
        $ratingID = $this->input->post('rating', true);

        $update_array = array(
            "suggested_reward" => $suggested_reward,
            "identified_training_needs" => $identified_training_needs,
            "special_remarks_from_hod" => $special_remarks_from_hod,
            "manager_comment" => $manager_comment,
            "confirmedYN" => $confirmed,
            "ratingID" => $ratingID,
            "career_and_training_action_plan" => $career_and_training_action_plan,
            "manager_assessment_undertaking" => $manager_assessment_undertaking
        );
        $this->db->where('id', $template_mapping_id);
        $this->db->update('srp_erp_apr_emp_softskills_template_mapping', $update_array);
        if ($confirmed == '1') {
            $row = $this->db->get_where('srp_erp_apr_emp_softskills_template_mapping', array('id' => $template_mapping_id))->row();
            $approvals_status = $this->approvals->CreateApproval('APR-SPE', $template_mapping_id, $row->document_id, 'Soft-Skills Based Performance', 'srp_erp_apr_emp_softskills_template_mapping', 'id', 0, current_date(true));
        }
        $data['status'] = 'success';
        echo json_encode($data);
    }

    










    
    public function fetch_employee_skills_performance_appraisal()
    {
        $config_goal_id = $this->input->post('config_goal_id', true);
        $emp_id = $this->input->post('emp_id', true);
        $manager_id =  $this->input->post('manager_id', true);
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($config_goal_id);
        //var_dump($goal_details_and_objectives);exit;
        $res = $this->appraisal_model->fetch_employee_skills_performance_appraisal($emp_id, $goal_details_and_objectives, $manager_id);
        echo json_encode($res);
    }



    
    public function fetch_employee_skills_performance_appraisal_self_eval()
    {
        $config_goal_id = $this->input->post('config_goal_id', true);
        $emp_id = $this->input->post('emp_id', true);
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($config_goal_id);
        //var_dump($goal_details_and_objectives);exit;
        $res = $this->appraisal_model->fetch_employee_skills_performance_appraisal_self_eval($emp_id, $goal_details_and_objectives);
        echo json_encode($res);
    }

    public function department_appraisal_details()
    {
        $department_id = $this->input->post('department_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $department_appraisal_header_id = $this->input->post('department_appraisal_header_id', true);
        $department_appraisals = $this->appraisal_model->department_appraisal_details($department_id, $goal_id, $department_appraisal_header_id);
        echo json_encode($department_appraisals);
    }

    public function generate_document_for_department_appraisal()
    {
        $department_id = $this->input->post('department_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $department_appraisals = $this->appraisal_model->generate_document_for_department_appraisal($department_id, $goal_id);
        echo json_encode($department_appraisals);
    }

    public function get_sub_departments_by_department_id()
    {
        $department_appraisal_header_id = $this->input->post('department_appraisal_header_id', true);
        $sub_departments = $this->appraisal_model->get_sub_departments_by_department_id($department_appraisal_header_id);
        echo json_encode($sub_departments);
    }

    public function get_department_employees()
    {
        $department_id = $this->input->post('department_id', true);
        $department_employees = $this->appraisal_model->get_department_employees($department_id);
        echo json_encode($department_employees);
    }

    public function get_all_employees()
    {
        $department_employees = $this->appraisal_model->get_all_employees();
        echo json_encode($department_employees);
    }

    public function get_employees_for_performance_apr()
    {
        $department_employees = $this->appraisal_model->get_employees_for_performance_apr();
        echo json_encode($department_employees);
    }

    public function insert_department_task()
    {
        $appraisal_sub_department_id = $this->input->post('appraisal_sub_department_id', true);
        $task_description = $this->input->post('task_description', true);
        $task_weight = $this->input->post('task_weight', true);
        $department_objective_id = $this->input->post('department_objective_id', true);
        $assigned_employee_id = $this->input->post('assigned_employee_id', true);
        $date_to_complete = $this->input->post('date_to_complete', true);
        $department_appraisal_header_id = $this->input->post('department_appraisal_header_id', true);
        $task_created_user_type = $this->input->post('task_created_user_type', true);


        $this->appraisal_model->insert_department_task($department_appraisal_header_id, $appraisal_sub_department_id, $task_description, $task_weight, $department_objective_id, $assigned_employee_id, $date_to_complete, $task_created_user_type);
        $data['status'] = 'success';
        echo json_encode($data);
    }


    public function edit_department_task()
    {

        $task_description = $this->input->post('task_description', true);
        $task_weight = $this->input->post('task_weight', true);
        $department_objective_id = $this->input->post('department_objective_id', true);
        $assigned_employee_id = $this->input->post('assigned_employee_id', true);
        $date_to_complete = $this->input->post('date_to_complete', true);
        $task_id = $this->input->post('task_id', true);


        //var_dump($date_to_complete);exit;

        $this->appraisal_model->edit_department_task($task_id, $task_description, $task_weight, $department_objective_id, $assigned_employee_id, $date_to_complete);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function get_percentage_details()
    {
        $department_id = $this->input->post('department_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $objective_id = $this->input->post('objective_id', true);
        $department_appraisal_header_id = $this->input->post('department_appraisal_header_id', true);
        $allocated_percentage_for_objective = $this->appraisal_model->allocated_percentage_for_objective($department_id, $goal_id, $objective_id);
        $used_percentage = $this->appraisal_model->get_used_percentage($department_id, $objective_id, $department_appraisal_header_id);
        if ($used_percentage == null) {
            $used_percentage = 0;
        }
        $allocated_percentage_for_department = $this->appraisal_model->allocated_percentage_for_department($department_id, $goal_id);
        $allocated_percentage_by_hundred = 100;
        $data['allocated_percentage_for_objective'] = $allocated_percentage_for_objective;
        $data['used_percentage'] = $used_percentage;
        $data['allocated_percentage_for_department'] = $allocated_percentage_for_department;
        $data['allocated_percentage_by_hundred'] = $allocated_percentage_by_hundred;
        $data['remaining_percentage'] = $allocated_percentage_by_hundred - $used_percentage;
        echo json_encode($data);
    }

    public function get_sub_department_tasks()
    {
        $sub_department_id = $this->input->post('sub_department_id', true);
        $department_appraisal_id = $this->input->post('department_appraisal_id', true);
        $sub_department_tasks = $this->appraisal_model->get_sub_department_tasks($sub_department_id, $department_appraisal_id);
        echo json_encode($sub_department_tasks);
    }

    public function approve_employee_performance_report()
    {
        $manager_comment = $this->input->post('manager_comment', true);
        $department_id = $this->input->post('department_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $employee_id = $this->input->post('employee_id', true);

        $suggested_reward = $this->input->post('suggested_reward', true);
        $identified_training_needs = $this->input->post('identified_training_needs', true);
        $special_remarks_from_hod = $this->input->post('special_remarks_from_hod', true);
        $status = $this->input->post('status', true);

        $data = $this->appraisal_model->approve_employee_performance_report($goal_id, $department_id, $employee_id, $manager_comment, $suggested_reward, $identified_training_needs, $special_remarks_from_hod, $status);
        echo json_encode($data);
    }

    public function get_employee_wise_softskills_performance()
    {
        $this->load->helpers('appraisal');
        $date_format_policy = date_format_policy();
        $companyid = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $search = $this->input->post('sSearch');
        $empID = current_userID();
        $convertFormat = convert_date_format_sql();
        $department_ids = $this->appraisal_model->get_employee_departments_array($empID);
        $company_reporting_currency = $this->common_data['company_data']['company_reporting_currency'];
        $company_reporting_DecimalPlaces = $this->common_data['company_data']['company_reporting_decimal'];
        $this->datatables->select("performance_appraisal.id as perfomance_appraisal_id,
        performance_appraisal.is_approved,
        performance_appraisal.document_id,
        corporate_goal.narration,        
        srp_employeesdetails.Ename1
        ");
        $this->datatables->join('srp_erp_apr_corporate_goal as corporate_goal', 'corporate_goal.id = performance_appraisal.goal_id');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = performance_appraisal.emp_id');
        $this->datatables->join('srp_erp_apr_department_appraisal_header', 'corporate_goal.id = srp_erp_apr_department_appraisal_header.goal_id');
        $this->datatables->from('srp_erp_apr_emp_softskills_template_mapping as performance_appraisal');
        $this->datatables->edit_column('total_value', '$1', '1');
        $this->datatables->where('performance_appraisal.company_id', $companyid);

        if (!empty($department_ids)) {
            $this->datatables->where_in('srp_erp_apr_department_appraisal_header.department_id', $department_ids);
        }

        $this->datatables->where('performance_appraisal.approvedYN', $approvedYN);
        $this->datatables->where('performance_appraisal.confirmedYN', 1);
        $this->datatables->where('performance_appraisal.is_confirmed_by_employee', 1);
        if ($search != "") {
            $this->datatables->where('performance_appraisal.document_id', $search);
        }
        $this->datatables->add_column('approved', '$1', 'confirm_aproval_EC("1","0")');
        $this->datatables->add_column('edit', '$1', 'skills_approval_action("0",perfomance_appraisal_id,"1")');
        echo $this->datatables->generate();
//        $this->datatables->generate();
//        echo $this->db->last_query();exit;
    }

    public function get_employee_wise_performance_approval()
    {

        $this->load->helpers('appraisal');
        $date_format_policy = date_format_policy();
        $companyid = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $empID = current_userID();
        $convertFormat = convert_date_format_sql();
        $department_ids = $this->appraisal_model->get_employee_departments($empID);
        $company_reporting_currency = $this->common_data['company_data']['company_reporting_currency'];
        $company_reporting_DecimalPlaces = $this->common_data['company_data']['company_reporting_decimal'];
        $department_arr = explode(",", $department_ids);
        $this->datatables->select("performance_appraisal.id as perfomance_appraisal_id,
        performance_appraisal.is_approved,
        performance_appraisal.approvedYN as approvedYN,
        performance_appraisal.document_id,
        corporate_goal.narration,
        srp_departmentmaster.DepartmentDes,
        srp_employeesdetails.Ename1
        ");
        // $this->datatables->join('(SELECT SUM(empCurrencyAmount) as transactionAmount,expenseClaimMasterAutoID,empCurrency FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID) det', '(det.expenseClaimMasterAutoID = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID)', 'left');
        $this->datatables->join('srp_departmentmaster ', 'srp_departmentmaster.DepartmentMasterID = performance_appraisal.department_id');
        $this->datatables->join('srp_erp_apr_corporate_goal as corporate_goal', 'corporate_goal.id = performance_appraisal.corporate_goal_id');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = performance_appraisal.emp_id');
        $this->datatables->from('srp_erp_apr_employee_performance_appraisal_header as performance_appraisal');
        //$this->datatables->add_column('Ec_detail', '<b>Claimed By Name : </b> $1 <br> <b>Claimed Date : </b> $2 <br><b>Description : </b> $3', 'claimedByEmpName,expenseClaimDate,comments');
        $this->datatables->edit_column('total_value', '$1', '1');
        $this->datatables->where('performance_appraisal.company_id', $companyid);
        $this->datatables->where('performance_appraisal.confirmedYN', '1');
        $this->datatables->where('performance_appraisal.is_approved', '1');

        if (trim($department_ids) != "") {
            $this->datatables->where_in('performance_appraisal.department_id', "$department_ids");
        }

        $this->datatables->where('performance_appraisal.approvedYN', $approvedYN);
        $this->datatables->add_column('approved', '$1', 'confirm_aproval_EC("1","0")');
        $this->datatables->add_column('edit', '$1', 'load_action_dialog(approvedYN,perfomance_appraisal_id,"1")');
        echo $this->datatables->generate();

    }


    public function get_hod_id_of_a_department()
    {
        $department_id = $this->input->post('department_id', true);
        $query = $this->db->get_where('srp_departmentmaster', array("DepartmentMasterID" => $department_id));
        $data['hod_details'] = $query->row();
        echo json_encode($data);
    }

    public function set_hod_id_of_a_department()
    {
        $hod_id = $this->input->post('hod_id', true);
        $department_id = $this->input->post('department_id', true);
        $update_array = array("hod_id" => $hod_id);
        $this->db->where('DepartmentMasterID', $department_id);
        $this->db->update('srp_departmentmaster', $update_array);
        $data['status'] = "success";
        $query = $this->db->get_where('srp_departmentmaster', array("DepartmentMasterID" => $department_id));
        $data['hod_details'] = $query->row();
        echo json_encode($data);
    }


    public function employee_wise_performance_approval_dialog()
    {
        $MasterID = $this->input->post('MasterID', true);
        $performance_appraisal = $this->appraisal_model->get_performance_appraisal_header_by_id($MasterID);
        $department_id = $performance_appraisal->department_id;
        $goal_id = $performance_appraisal->corporate_goal_id;
        $emp_id = $performance_appraisal->emp_id;

        $employee_wise_performance_data = $this->appraisal_model->get_employee_tasks_for_employee_wise_performance_report($department_id, $goal_id, $emp_id);
        //var_dump($performance_appraisal);exit;
        echo json_encode($employee_wise_performance_data);
    }


    public function employee_wise_softskills_approval_dialog()
    {
        $MasterID = $this->input->post('MasterID', true);
        $performance_appraisal = $this->appraisal_model->get_softskills_template_mapping($MasterID);
        $goal_id = $performance_appraisal->goal_id;
        $emp_id = $performance_appraisal->emp_id;
        //var_dump($performance_appraisal);exit;
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        $res = $this->appraisal_model->fetch_employee_skills_performance_appraisal($emp_id, $goal_details_and_objectives);
        echo json_encode($res);
    }

    function refer_back_employee_wise_performance()
    {
        $masterID = $this->input->post('masterID');
        $this->load->library('approvals');
        //$status = $this->approvals->approve_delete($masterID, 'APR-EWP');

        $row = $this->db->get_where('srp_erp_documentapproved', array('documentSystemCode' => $masterID, 'documentID' => 'APR-EWP'))->row();
        if ($row->approvedYN == 1) {
            $this->db->where('documentApprovedID', $row->documentApprovedID);
            $this->db->update('srp_erp_documentapproved', array('approvedYN' => 0, 'approvedEmpID' => ''));
            $this->db->where('id', $masterID);
            $this->db->update('srp_erp_apr_employee_performance_appraisal_header', array('approvedYN' => 0));
            $data['status'] = TRUE;
            $data['message'] = "Successfully Referred Back";
            echo json_encode($data);
        } else {
            $data['status'] = FALSE;
            $data['message'] = "Still Not Approved";
            echo json_encode($data);
        }


//        $document_status = document_status('APR-EWP', $masterID, 1);
//        if ($document_status['error'] == 1) {
//            die(json_encode(['e', $document_status['message']]));
//        }
//
//        $documentCode = $document_status['data']['docCode'];
//        $is_approved = $document_status['data']['approvalVal'];
//        if ($is_approved == 1) {
//            echo json_encode(['e', 'This document is already approved.<p>You can not refer back this.']);
//        } else {
//
//        }
    }

    public function get_sub_department_tasks_by_id()
    {
        $sub_department_task_id = $this->input->post('sub_department_task_id', true);
        $sub_department_task = $this->appraisal_model->get_sub_department_tasks_by_id($sub_department_task_id);
        echo json_encode($sub_department_task);
    }

    public function manager_review_save()
    {
        $status = $this->input->post('status', true);
        $task_id = $this->input->post('task_id', true);
        $data = $this->appraisal_model->manager_review_save($status, $task_id);
        echo json_encode($data);
    }

    public function get_employee_tasks()
    {
        $user_id = current_userID();
        //$user_id = 1244;
        $employee_tasks = $this->appraisal_model->get_employee_tasks($user_id);
        echo json_encode($employee_tasks);
    }

    public function is_template_already_using()
    {
        $template_id = $this->input->post('template_id', true);
        $query = $this->db->query("select * from srp_erp_apr_corporate_goal where softskills_template_id=$template_id and is_deleted!=1");
        if ($query->num_rows() > 0) {
            $data['status'] = true;

        } else {
            $data['status'] = false;
        }
        echo json_encode($data);
    }


    public function get_appraisal_wise_employee_tasks()
    {
        $user_id = current_userID();
        $employee_tasks = $this->appraisal_model->get_appraisal_wise_employee_tasks($user_id);
        echo json_encode($employee_tasks);
    }

    public function save_employee_skills_comment()
    {
        $template_mapping_id = $this->input->post('template_mapping_id', true);
        $comment = $this->input->post('comment', true);

        $begin_with_the_end_in_mind = $this->input->post('begin_with_the_end_in_mind', true) ? $this->input->post('begin_with_the_end_in_mind', true) : null;
        $miscellaneous_worth_mentioning = $this->input->post('miscellaneous_worth_mentioning', true) ? $this->input->post('miscellaneous_worth_mentioning', true) : null;
        $benchmark_objective_assessment = $this->input->post('benchmark_objective_assessment', true) ? $this->input->post('benchmark_objective_assessment', true) : null;
        
        $update_array = array(
            "special_remarks_from_emp" => $comment,
            "is_confirmed_by_employee" => 1,
            "begin_with_the_end_in_mind" => $begin_with_the_end_in_mind,
            "miscellaneous_worth_mentioning" => $miscellaneous_worth_mentioning,
            "benchmark_objective_assessment" => $benchmark_objective_assessment
        );
        $this->db->where(array("id" => $template_mapping_id));
        $result = $this->db->update('srp_erp_apr_emp_softskills_template_mapping', $update_array);
        if ($result) {
            echo json_encode(array('s', 'Updated Successfully'));
        } else {
            echo json_encode(array('e', 'Update Failed'));
        }
    }

    public function save_employee_comment()
    {
        $goal_id = $this->input->post('goal_id', true);
        $department_id = $this->input->post('department_id', true);
        $emp_id = $this->input->post('emp_id', true);
        $comment = $this->input->post('comment', true);
        $update_array = array(
            "special_remarks_from_emp" => $comment,
            "is_confirmed_by_employee" => 1,
        );
        $this->db->where(array("corporate_goal_id" => $goal_id,
            "department_id" => $department_id,
            "emp_id" => $emp_id));
        $result = $this->db->update('srp_erp_apr_employee_performance_appraisal_header', $update_array);
        if ($result) {
            echo json_encode(array('s', 'Updated Successfully'));
        } else {
            echo json_encode(array('e', 'Update Failed'));
        }

    }

    public function save_task_progress()
    {
        $task_progress = $this->input->post('task_progress', true);
        $task_id = $this->input->post('task_id', true);
        $this->appraisal_model->save_task_progress($task_progress, $task_id);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function load_appraisal_task_discussion()
    {
        $task_id = $this->input->post('task_id', true);
        $task_discussion = $this->appraisal_model->load_appraisal_task_discussion($task_id);
        echo json_encode($task_discussion);
    }

    public function send_message()
    {
        $task_id = $this->input->post('task_id', true);
        $message = $this->input->post('message', true);
        $this->appraisal_model->send_message($task_id, $message);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    function save_emp_performance_approval()
    {

        $system_code = trim($this->input->post('master_id') ?? '');
        $level_id = trim($this->input->post('level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $code = trim($this->input->post('code') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $data['status'] = FALSE;
                $data['message'] = "Document already approved";
                echo json_encode($data);
                exit;
            } else {
                $this->db->select('id');
                $this->db->where('id', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_apr_employee_performance_appraisal_header');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $data['status'] = FALSE;
                    $data['message'] = "Document already rejected";
                    echo json_encode($data);
                    exit;
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        $data['status'] = FALSE;
                        $data['message'] = "Validation Error";
                        echo json_encode($data);
                        exit;
                    } else {
                        $data['status'] = $this->appraisal_model->save_emp_performance_approval();
                        if ($data['status'] == true) {
                            $data['message'] = "Document Approved";
                        } else {
                            $data['message'] = "Document Approval Error";
                        }
                        echo json_encode($data);
                        exit;
                    }
                }
            }
        } else if ($status == 2) {
            $row = $this->db->get_where('srp_erp_documentapproved', array('documentSystemCode' => $system_code, 'documentID' => 'APR-SPE'))->row();
            if ($row->approvedYN == 1) {
                $this->db->where('documentApprovedID', $row->documentApprovedID);
                $this->db->update('srp_erp_documentapproved', array('approvedYN' => 0, 'approvedEmpID' => ''));
                $this->db->where('id', $system_code);
                $this->db->update('srp_erp_apr_emp_softskills_template_mapping', array('approvedYN' => 0));
                $data['status'] = TRUE;
                $data['message'] = "Successfully Referred Back";
                echo json_encode($data);
            } else {
                $data['status'] = FALSE;
                $data['message'] = "Still Not Approved";
                echo json_encode($data);
            }
        }

    }

    function save_corporate_goal_approval()
    {

        $system_code = trim($this->input->post('goal_id') ?? '');
        $level_id = trim($this->input->post('level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $code = trim($this->input->post('code') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                //$this->session->set_flashdata('w', 'Document already approved');
                $data['status'] = false;
                $data['message'] = 'Document already approved';
                echo json_encode($data);
                exit;
            } else {
                $this->db->select('id');
                $this->db->where('id', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_apr_corporate_goal');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
//                    $this->session->set_flashdata('w', 'Document already rejected');
                    $data['status'] = false;
                    $data['message'] = 'Document already rejected';
                    echo json_encode($data);
                    exit;
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comment', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $data['status'] = false;
                        $data['message'] = validation_errors();
                        echo json_encode($data);
                        exit;
                    } else {
                        echo json_encode($this->appraisal_model->save_corporate_goal_approval());
                        exit;
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('id');
            $this->db->where('id', trim($system_code));
            $this->db->where('confirmedYN', 2);
            //$this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_apr_corporate_goal');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
                exit;
            } else {
                $rejectYN = checkApproved($system_code, $code, $level_id);
                if (!empty($rejectYN)) {

                    $data['status'] = false;
                    $data['message'] = 'Document already approved';
                    echo json_encode($data);
                    exit;
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comment', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $data['status'] = false;
                        $data['message'] = validation_errors();
                        echo json_encode($data);
                    } else {
                        echo json_encode($this->appraisal_model->save_corporate_goal_approval());
                        exit;
                    }
                }
            }
        }
        echo $this->session->flashdata('w');
    }

    public function confirmation_referback_corporate_goal()
    {
        $goal_id = $this->input->post('goal_id', true);
        $data["status"] = $this->appraisal_model->confirmation_referback_corporate_goal($goal_id);
        echo json_encode($data);
    }

    public function regenerate_department_appraisal_with_newly_added_subdepartments()
    {
        $department_master_id = $this->input->post('department_id', true);
        $department_appraisal_id = $this->input->post('department_appraisal_id', true);
        $this->appraisal_model->regenerate_department_appraisal_with_newly_added_subdepartments($department_master_id, $department_appraisal_id);
        $data["status"] = "";
        echo json_encode($data);
    }

    public function overall_completion_percentages_for_dashboard()
    {
        $goal_id = $this->input->post('goal_id', true);
        $company_id = $this->input->post('company_id', TRUE);
        $departments_dataset = $this->appraisal_model->get_departments($company_id);
        $department_names = array();
        $completed_percentages = array();
        foreach ($departments_dataset as $item) {
            array_push($department_names, $item->department_description);
            $completed_percentage = $this->appraisal_model->overall_completion_percentages_for_dashboard($goal_id, $item->department_master_id);
            array_push($completed_percentages, $completed_percentage);
        }
        $data['department_names'] = $department_names;
        $data['completed_percentages'] = $completed_percentages;
        echo json_encode($data);
    }

    public function overall_allocated_percentages_for_dashboard()
    {
        $goal_id = $this->input->post('goal_id', true);
        // fetch goal id by year
        $company_id = $this->input->post('company_id', TRUE);
        $departments_dataset = $this->appraisal_model->get_departments($company_id);
        $allocated_percentages = array();
        foreach ($departments_dataset as $item) {
            $allocated_percentage['y'] = $this->appraisal_model->overall_allocated_percentage($goal_id, $item->department_master_id);
            $allocated_percentage['name'] = $item->department_description;
            $allocated_percentage['color'] = $this->appraisal_model->get_department_color_code($item->department_master_id);

            array_push($allocated_percentages, $allocated_percentage);
        }
        $data['allocated_percentages'] = $allocated_percentages;
        echo json_encode($data);
    }

    public function close_corporate_goal()
    {
        $goal_id = $this->input->post('goal_id', true);
        $this->appraisal_model->close_corporate_goal($goal_id);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function get_goal_closed_status()
    {
        $goal_id = $this->input->post('goal_id', true);
        $data['is_closed'] = $this->appraisal_model->get_goal_closed_status($goal_id);
        echo json_encode($data);
    }

    public function get_department_objectives()
    {
        $assigned_department_id = $this->input->post('assigned_department_id', true);
        $corporate_goal_id = $this->input->post('corporate_goal_id', true);
        $appraisal_header_id = $this->input->post('appraisal_header_id', true);

        $department_objectives = array();
        $department_objectives_array = $this->appraisal_model->get_department_objectives($assigned_department_id, $corporate_goal_id);
        foreach ($department_objectives_array as $row) {
            $objective_id = $row['objective_id'];
            $objective = array();
            $objective['description'] = $row['description'];
            $objective['objective_id'] = $row['objective_id'];
            $used_percentage = $this->appraisal_model->get_used_percentage($assigned_department_id, $objective_id, $appraisal_header_id);
            if ($used_percentage == null) {
                $used_percentage = 0;
            }
            $objective['used_percentage'] = (float)$used_percentage;
            array_push($department_objectives, $objective);
        }


        echo json_encode($department_objectives);
    }

    public function change_task_approval_status()
    {
        $task_id = $this->input->post('task_id', true);
        $status = $this->input->post('status', true);
        $this->appraisal_model->change_task_approval_status($task_id, $status);
        $data['status'] = 'success';
        echo json_encode($data);
    }

    public function get_department_employees_pagination()
    {
        $department_id = $this->input->post('department_id', true);
        $current_page = $this->input->post('current_page', true);
        $list_of_employee_ids = $this->input->post('list_of_employee_ids', true);
        if (!empty($list_of_employee_ids)) {
            $list_of_employee_ids = implode(", ", $list_of_employee_ids);
            $department_employees = $this->appraisal_model->get_department_employees_by_id_list($department_id, $list_of_employee_ids);
            //var_dump($department_employees);exit;
            $max_count = sizeof($department_employees);
            echo $this->create_pagination($current_page, $max_count, $department_employees);
        }
    }

    public function generate_evaluation_report_pdf()
    {

        //load the view and saved it into $html variable
        $department_id = $this->input->get('department_id', true);
        $goal_id = $this->input->get('goal_id', true);
        $employee_id = $this->input->get('employee_id', true);
//        $department_id = 31;
//        $goal_id = 64;
//        $employee_id = 1223;
        $employee_wise_performance_data = $this->appraisal_model->get_employee_tasks_for_employee_wise_performance_report($department_id, $goal_id, $employee_id);

        $number_of_tasks = 0;
        $number_of_completed_tasks = 0;
        if(!empty($employee_wise_performance_data)){
            foreach ($employee_wise_performance_data as $item) {
                $number_of_tasks++;
                $is_approved_by_manager = $item['is_approved_by_manager'];
                $completion = $item['completion'];
                if ($is_approved_by_manager == 1 && $completion == 100) {
                    $number_of_completed_tasks++;
                }
            }
        }
        if ($number_of_completed_tasks != 0) {
            $objective_based_task_completion_percentage_of_employee = round((($number_of_completed_tasks / $number_of_tasks) * 100), 2);
        } else {
            $objective_based_task_completion_percentage_of_employee = 0;
        }

        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        //var_dump($goal_details_and_objectives);
        $softskills_based_percentage_of_employee = $this->appraisal_model->fetch_employee_skills_performance_appraisal_for_summary_report($employee_id, $goal_details_and_objectives);

        $data['hod_id'] = 0;
        $data['appraisal_start_date'] = null;
        $data['appraisal_end_date'] = null;

        $hod_data = $this->appraisal_model->get_department_hod($department_id, $goal_id);
        $goal_details = $this->appraisal_model->get_corporate_goal_details($goal_id);

        //var_dump($goal_details['goal_details'][0]->from);exit;
        if(!empty($goal_details)) {
            $data['appraisal_start_date'] = $goal_details['goal_details'][0]->from ?? null;
            $data['appraisal_end_date'] = $goal_details['goal_details'][0]->to ?? null;
        }

        if(!empty($hod_data)){
            $data['hod_id'] = $hod_data[0]['hod_id'];
        }else{
            $data['hod_id'] = null;
        }


        $data['objective_based_percentage_of_employee'] = $objective_based_task_completion_percentage_of_employee;
        $data['softskills_based_percentage_of_employee'] = $softskills_based_percentage_of_employee;

        $data['employee_details'] = $this->appraisal_model->get_employee_details($employee_id);

        if($data['hod_id']!=null){
            $data['hod_details'] = $this->appraisal_model->get_employee_details($data['hod_id']);
        }else{
            $data['hod_details'][0]['Ename1'] = '-';
        }
        $department_details = $this->appraisal_model->get_department_details_by_id($department_id);
        $data['department_name'] = $department_details->DepartmentDes;
        $empwise_performance_remarks = $this->appraisal_model->empwise_performance_remarks($goal_id, $department_id, $employee_id);
        $company_id = current_companyID();
        $skill_performance_remarks = $this->appraisal_model->skill_performance_remarks(
            $employee_id,
            !empty($goal_details_and_objectives['goal_details']) && isset($goal_details_and_objectives['goal_details'][0]) ? $goal_details_and_objectives['goal_details'][0]->softskills_template_id : null,
            $company_id,
            $goal_id
        );
        //empwise remarks
        $data['manager_comment'] = $empwise_performance_remarks != null ? $empwise_performance_remarks->manager_comment : "";
        $data['suggested_reward'] = $empwise_performance_remarks != null ? $empwise_performance_remarks->suggested_reward : "";
        $data['identified_training_needs'] = $empwise_performance_remarks != null ? $empwise_performance_remarks->identified_training_needs : "";
        $data['special_remarks_from_hod'] = $empwise_performance_remarks != null ? $empwise_performance_remarks->special_remarks_from_hod : "";
        $data['special_remarks_from_emp'] = $empwise_performance_remarks != null ? $empwise_performance_remarks->special_remarks_from_emp : "";
        //softskills remarks
        $data['manager_comment_skill'] = $skill_performance_remarks != null ? $skill_performance_remarks->manager_comment : "";
        $data['suggested_reward_skill'] = $skill_performance_remarks != null ? $skill_performance_remarks->suggested_reward : "";
        $data['identified_training_needs_skill'] = $skill_performance_remarks != null ? $skill_performance_remarks->identified_training_needs : "";
        $data['special_remarks_from_hod_skill'] = $skill_performance_remarks != null ? $skill_performance_remarks->special_remarks_from_hod : "";
        $data['special_remarks_from_emp_skill'] = $skill_performance_remarks != null ? $skill_performance_remarks->special_remarks_from_emp : "";


        $html = $this->load->view('system/appraisal/reports/evaluation_summary_report_pdf', $data, true);


        //this the the PDF filename that user will get to download
        $emp_name = $data['employee_details'][0]['Ename1'];
        $pdfFilePath = "Evaluation Summary Report of " . $emp_name . '.pdf';

        //load mPDF library
        $this->load->library('m_pdf');

//        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
//        $this->m_pdf->pdf->WriteHTML($stylesheet);

        //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);

        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }


    /**
     * Creates the HTML for a page link.
     * @param int $page The page to create a link for
     * @param int $current_page The page the user is currently on
     * @return string HTML link to the given page
     */
    function make_page_link($page, $current_page, $department_employees)
    {
        $emp_id = $department_employees[$page - 1]->EmpID;
        if ($page == $current_page) {
            return '<li class="emp-li active" data-emp_id="' . $emp_id . '" onclick="emp_page_click.call(this)"><a>' . $page . '</a></li>';
        } else {
            //var_dump($department_employees);exit;

            return '<li class="emp-li" data-emp_id="' . $emp_id . '" onclick="emp_page_click.call(this)"><a>' . $page . '</a></li>';
        }
    }

    /**
     * Takes an array of page numbers to create links for and fills it with
     * additional entries where necessary (adding "..." or one intermediate page).
     * @param int[] $pages The pages to create links for
     * @param int $current_page The page the user is currently on
     * @param $department_employees
     * @return string The generated HTML displaying all page links
     */
    function page_list_to_links($pages, $current_page, $department_employees)
    {
        $output = '';
        $previous_page = 0;
        foreach ($pages as $page) {
            if ($page - $previous_page > 2) {
                $output .= '<li class="emp-li"><a>...</a></li>';
            } else if ($page - $previous_page === 2) {
                // Show the page instead of "..." if we're only hiding one page
                $output .= $this->make_page_link($page - 1, $current_page, $department_employees) . ' ';
            }
            $output .= $this->make_page_link($page, $current_page, $department_employees) . ' ';
            $previous_page = $page;
        }
        return $output;
    }

    /**
     * Creates the HTML to display pagination links.
     * @param int $current_page The page to create a link for
     * @param int $max_page The maximum page
     * @param $department_employees
     * @return string The generated HTML displaying all page links
     */
    function create_pagination($current_page, $max_page, $department_employees)
    {
        // Number of pages to show left & right of the current page
        $page_span = 2;

        // middle: [3] [4] *5* [6] [7]
        $middle_min = max($current_page - $page_span, 1);
        $middle_max = min($current_page + $page_span, $max_page);
        $pages = range($middle_min, $middle_max);

        if ($middle_min !== 1) {
            array_unshift($pages, 1);
        }
        if ($middle_max !== $max_page) {
            $pages[] = $max_page;
        }
        return $this->page_list_to_links($pages, $current_page, $department_employees);
    }

    function is_subdepartment_exist()
    {
        $sub_department_description = trim($this->input->post('sub_department_description', true));
        $sub_department_code = trim($this->input->post('sub_department_code', true));
        $department_id = $this->input->post('department_id', true);
        $query = $this->db->query("select * from srp_erp_apr_subdepartment where department_master_id=$department_id and (description='$sub_department_description' or code='$sub_department_code') and is_deleted=0");
        if ($query->num_rows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    function is_template_exist()
    {
        $template_name = trim($this->input->post('template_name', true));
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_softskills_master` where name='$template_name'");
        if ($query->num_rows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    function delete_grades()
    {
        $template_id = $this->input->post('template_id', true);
        $this->db->where('softskills_template_id', $template_id);
        $this->db->delete('srp_erp_apr_softskills_grades');
        $data['status'] = 'success';
        $data['message'] = '';
        echo json_encode($data);
    }

    function department_name_code_edit_validate()
    {
        $sub_department_description = trim($this->input->post('sub_department_description', true));
        $sub_department_code = trim($this->input->post('sub_department_code', true));
        $sub_department_id = $this->input->post('sub_department_id', true);
        $query = $this->db->query("select * from srp_erp_apr_subdepartment where id!=$sub_department_id and (description='$sub_department_description' or code='$sub_department_code')");
        if ($query->num_rows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    function is_cg_approval_setup_exist()
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_approvalusers` where documentID='CG'");
        if ($query->num_rows() > 0) {
            echo json_encode(array("status" => true));
        } else {
            echo json_encode(array("status" => false));
        }
    }

    function fetchAppraisalRating()
    {
        $this->datatables->select('erp_srp_apr_appraisal_rating.appraisalRatingID as appraisalRatingID,
        erp_srp_apr_appraisal_rating.ratedValue as ratedValue,
         erp_srp_apr_appraisal_rating.rating as rating,
          erp_srp_apr_appraisal_rating.description as description', false);
        $this->datatables->from('erp_srp_apr_appraisal_rating');
        $this->datatables->where('erp_srp_apr_appraisal_rating.isDeleted', 0);
        $this->datatables->add_column('action', '$1', 'appraisalRatingAction(appraisalRatingID)');
        echo $this->datatables->generate();
    }

    function getRatingDetails()
    {
        $id = $this->input->post('id');
        $row = $this->db->query("select * from erp_srp_apr_appraisal_rating where appraisalRatingID=$id")->row();
        echo json_encode($row);
    }

    function modifyRating()
    {
        $ratedValue = $this->input->post('ratedValue');
        $rating = $this->input->post('rating');
        $description = $this->input->post('description');
        $id = $this->input->post('id');
        $insert = array(
            'ratedValue' => $ratedValue,
            'rating' => $rating,
            'description' => $description,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date'],
            'timeStamp' => format_date_mysql_datetime()
        );
        $this->db->where('appraisalRatingID', $id);
        $res = $this->db->update('erp_srp_apr_appraisal_rating', $insert);
        if ($res) {
            $data['status'] = 'success';
            $data['message'] = 'Successfully modified rating definition.';
        } else {
            $data['status'] = 'fail';
            $data['message'] = 'Error.';
        }
        echo json_encode($data);
    }

    function deleteRatingDetails()
    {
        $id = $this->input->post('id');
        $insert = array(
            'isDeleted' => 1,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date'],
            'timeStamp' => format_date_mysql_datetime()
        );
        $this->db->where('appraisalRatingID', $id);
        $res = $this->db->update('erp_srp_apr_appraisal_rating', $insert);
        if ($res) {
            $data['status'] = 'success';
            $data['message'] = 'Successfully deleted rating definition.';
        } else {
            $data['status'] = 'fail';
            $data['message'] = 'Error.';
        }
        echo json_encode($data);
    }

    function saveRating()
    {
        $ratedValue = $this->input->post('ratedValue');
        $rating = $this->input->post('rating');
        $description = $this->input->post('description');
        $companyID = $this->common_data['company_data']['company_id'];
        $insert = array(
            'ratedValue' => $ratedValue,
            'rating' => $rating,
            'description' => $description,
            'companyID' => $companyID,
            'createdUserGroup' => $this->common_data['user_group'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdDateTime' => $this->common_data['current_date'],
            'timeStamp' => format_date_mysql_datetime()
        );
        $res = $this->db->insert('erp_srp_apr_appraisal_rating', $insert);
        if ($res) {
            $data['status'] = 'success';
            $data['message'] = 'Successfully created rating definition.';
        } else {
            $data['status'] = 'fail';
            $data['message'] = 'Error.';
        }
        echo json_encode($data);
    }

    function allCalenderEvents()
    {
        $currentuserid = current_userID();
        $companyID = $this->common_data['company_data']['company_id'];
        $query = $this->db->query("select * from srp_erp_apr_appraisal_task where employee_id=$currentuserid and company_id=$companyID");
        $all_list = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $item) {
                $item = array(
                    "id" => $item->id,
                    "title" => $item->task_description,
                    "color" => "#66CD00",
                    "start" => $item->date_to_complete,
                    "end" => $item->date_to_complete
                );
                array_push($all_list, $item);
            }
        }

        $department_employees = $this->appraisal_model->get_employees_for_performance_apr();
        $department_employee_ids = array();
        foreach ($department_employees as $emp) {
            $department_employee_ids[] = $emp->EIdNo;
        }
        $department_employee_ids_str = implode(", ", $department_employee_ids);
        if ($department_employee_ids_str != "") {
            $query2 = $this->db->query("select * from srp_erp_apr_appraisal_task where employee_id in ($department_employee_ids_str) and company_id=$companyID");
            if ($query2->num_rows() > 0) {
                foreach ($query2->result() as $item) {
                    $item = array(
                        "id" => $item->id,
                        "title" => $item->task_description,
                        "color" => "#005b96",
                        "start" => $item->date_to_complete,
                        "end" => $item->date_to_complete
                    );
                    array_push($all_list, $item);
                }
            }
        }

        echo json_encode($all_list);
    }

    function get_calendar_task_details()
    {
        $task_id = $this->input->post('task_id', true);
        $currentuserid = current_userID();
        $companyID = $this->common_data['company_data']['company_id'];
        $query = $this->db->query("select 
srp_erp_apr_appraisal_task.id as task_id,
srp_erp_apr_appraisal_task.task_description,
srp_erp_apr_appraisal_task.weight,
srp_erp_apr_appraisal_task.employee_id,
srp_erp_apr_appraisal_task.date_to_complete,
srp_erp_apr_appraisal_task.appraisal_sub_department_id,
srp_erp_apr_appraisal_task.completion,
srp_erp_apr_appraisal_task.manager_review,
srp_erp_apr_appraisal_task.appraisal_id,
srp_erp_apr_appraisal_task.is_approved_by_manager,
srp_erp_apr_appraisal_task.company_id,
srp_erp_apr_corporate_goal.is_closed as is_goal_closed
from srp_erp_apr_appraisal_task 
join srp_erp_apr_department_appraisal_header on srp_erp_apr_department_appraisal_header.id = srp_erp_apr_appraisal_task.appraisal_id
join srp_erp_apr_corporate_goal on srp_erp_apr_corporate_goal.id = srp_erp_apr_department_appraisal_header.goal_id
where srp_erp_apr_appraisal_task.id=$task_id and srp_erp_apr_appraisal_task.company_id=$companyID");
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $task = array();
            if ($row->employee_id == $currentuserid) {
                $task['is_own_task'] = 1;
            } else {
                $task['is_own_task'] = 0;
            }
            $task['task_id'] = $row->task_id;
            $task['task_description'] = $row->task_description;
            $task['weight'] = $row->weight;
            $task['employee_id'] = $row->employee_id;
            $task['date_to_complete'] = $row->date_to_complete;
            $task['appraisal_sub_department_id'] = $row->appraisal_sub_department_id;
            $task['completion'] = $row->completion;
            $task['manager_review'] = $row->manager_review;
            $task['is_approved_by_manager'] = $row->is_approved_by_manager;
            $task['appraisal_id'] = $row->appraisal_id;
            $task['is_goal_closed'] = $row->is_goal_closed;
            echo json_encode($task);
        }
    }

    function load_kpi_indicator()
    {
        $currentuserid = current_userID();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select('srp_erp_apr_appraisal_task.id as task_id,
        srp_erp_apr_appraisal_task.task_description as task_description,
        srp_erp_apr_appraisal_task.weight,
        srp_erp_apr_appraisal_task.employee_id,
        srp_erp_apr_appraisal_task.date_to_complete,
        srp_erp_apr_appraisal_task.appraisal_sub_department_id as appraisal_sub_department_id,
        srp_erp_apr_appraisal_task.completion as completion,
        srp_erp_apr_appraisal_task.manager_review,
        srp_departmentmaster.DepartmentLogo as DepartmentLogo', false);
        $this->datatables->from('srp_erp_apr_appraisal_task');
//        $this->datatables->join('srp_erp_apr_appraisal_sub_departments', 'srp_erp_apr_appraisal_sub_departments.id = srp_erp_apr_appraisal_task.appraisal_sub_department_id');
        $this->datatables->join('srp_erp_apr_subdepartment', 'srp_erp_apr_subdepartment.id = srp_erp_apr_appraisal_task.appraisal_sub_department_id');
        $this->datatables->join('srp_departmentmaster', 'srp_departmentmaster.DepartmentMasterID = srp_erp_apr_subdepartment.department_master_id');
        $this->datatables->where('srp_erp_apr_appraisal_task.employee_id', $currentuserid);
        $this->datatables->where('srp_erp_apr_appraisal_task.company_id', $companyID);
        $this->datatables->add_column('progress', '$1', 'kpiIndicatorProgress(completion)');
        $this->datatables->add_column('status', '$1', 'kpiIndicatorStatus(completion)');
        $this->datatables->add_column('department_logo', '$1', 'departmentLogo(DepartmentLogo)');
        $this->datatables->add_column('task_description_with_logo', '$1', 'task_description_with_logo(task_description,DepartmentLogo)');
        echo $this->datatables->generate();
    }

    function overall_completion_percentages_for_widget()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $department_employees = $this->appraisal_model->get_employees_for_performance_apr();
        $department_employee_ids = array();
        foreach ($department_employees as $emp) {
            $department_employee_ids[] = $emp->EIdNo;
        }
        $department_employee_ids_str = implode(", ", $department_employee_ids);
        if ($department_employee_ids_str != "") {
            $query2 = $this->db->query("select * from srp_erp_apr_appraisal_task where employee_id in ($department_employee_ids_str) and company_id=$companyID");

            $open = 0;
            $inProgress = 0;
            $completed = 0;
            if ($query2->num_rows() > 0) {
                foreach ($query2->result() as $item) {
                    $completion = $item->completion;
                    if ($completion == 0) {
                        $open++;
                    } elseif ($completion > 0 && $completion < 100) {
                        $inProgress++;
                    } elseif ($completion == 100) {
                        $completed++;
                    }
                }
            }

            $totalNumberOfTasks = $open + $inProgress + $completed;

            if($totalNumberOfTasks!=0){
                $openPercentage = ($open / $totalNumberOfTasks) * 100;
                $inProgressPercentage = ($inProgress / $totalNumberOfTasks) * 100;
                $completedPercentage = ($completed / $totalNumberOfTasks) * 100;
                $pieChartData = array(
                    array(
                        "count" =>$completed,
                        "name" => "Completed",
                        "y" => round($completedPercentage,2)
                    ),
                    array(
                        "count" =>$inProgress,
                        "name" => "In-Progress",
                        "y" => round($inProgressPercentage,2)
                    ),
                    array(
                        "count" =>$open,
                        "name" => "Open",
                        "y" => round($openPercentage,2)
                    )
                );
                echo json_encode($pieChartData);
            }else{
                echo json_encode(array());
            }


        }
    }

    function upload_department_logo()
    {
        $depID = $this->input->post('departmentID');
        if (isset($_FILES['files']['name']) && !empty($_FILES['files']['name'])) {
            $t = time();
            $imgData = $this->image_upload_s3($t);
            $department = array("DepartmentLogo"=>$imgData[1]);
            $this->db->where('DepartmentMasterID',$depID);
            $this->db->update('srp_departmentmaster',$department);
            $imageLink = $this->s3->createPresignedRequest($imgData[1], '+24 hour');
            $data['status'] = 1;
            $data['message'] = 'success';
            $data['link'] = $imageLink;
            echo json_encode($data);
        } else {
            $data['status'] = 0;
            $data['message'] = 'Error';
            echo json_encode($data);
        }
    }

    function image_upload_s3($ECode)
    {
        $fileName = str_replace(' ', '', strtolower($ECode)) . '_' . time();
        $file = $_FILES['files'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($file['error'] == 1) {
            die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)."]));
        }

        $allowed_types = 'gif|png|jpg|jpeg';
        $allowed_types = explode('|', $allowed_types);
        if (!in_array($ext, $allowed_types)) {
            die(json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
        }

        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if ($size > 1) {
            die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 1MB )"]));
        }

        $fileName = "images/users/$fileName.$ext";
        $s3Upload = $this->s3->upload($file['tmp_name'], $fileName);

        if (!$s3Upload) {
            return array('e', 'Employee image upload failed ');
        } else {
            return array('s', $fileName);
        }
    }
    
    public function print_pa_report(){

        $config_goal_id = $this->input->get('config_goal_id', true);
        $emp_id = $this->input->get('emp_id', true);
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($config_goal_id);
        $res['manager'] = $this->appraisal_model->fetch_employee_skills_performance_appraisal($emp_id, $goal_details_and_objectives);
        $res['employee'] = $this->appraisal_model->fetch_employee_skills_performance_appraisal_self_eval($emp_id, $goal_details_and_objectives);

        $html = $this->load->view('system/appraisal/reports/softskills_performance_report_print',$res,true);

        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => 'A5', 
            'default_font_size' => 9,
            'default_font'      => 'arial',
            'margin_left'       => 5,
            'margin_right'      => 5,
            'margin_top'        => 5,
            'margin_bottom'     => 10,
            'margin_header'     => 0,
            'margin_footer'     => 3,
            'orientation'       => 'P'  
        ]);

        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
        $stylesheet4 = file_get_contents('plugins/appraisal/styles.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($stylesheet4, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
    }

    function save_measurepoint(){
        $data = $this->appraisal_model->save_measurepoint();
        echo json_encode($data);
    }

    function save_measurepointText(){
        $data = $this->appraisal_model->save_measurepointText();
        echo json_encode($data);
    }

    function save_manager_measurepoint(){
        $goal_id = $this->input->post('goal_id', true);
        $data = $this->appraisal_model->save_manager_measurepoint($goal_id);
        echo json_encode($data);
    }

    public function save_emp_softskills_managerPoints()
    {
        $performance_id = $this->input->post('performance_id', true);
        $emp_id = $this->input->post('emp_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $grade_id = $this->input->post('grade_id', true);
        $this->appraisal_model->save_emp_softskills_managerPoints($performance_id, $emp_id, $goal_id, $grade_id);
    }

    public function save_emp_softskills_empPoints()
    {
        $performance_id = $this->input->post('performance_id', true);
        $emp_id = $this->input->post('emp_id', true);
        $goal_id = $this->input->post('goal_id', true);
        $grade_id = $this->input->post('grade_id', true);
        $this->appraisal_model->save_emp_softskills_empPoints($performance_id, $emp_id, $goal_id, $grade_id);
    }

    public function update_job_purpose(){
        $data = $this->appraisal_model->update_job_purpose();
        echo json_encode($data);
    }
}
