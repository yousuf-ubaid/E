<?php

class Appraisal_model extends ERP_Model
{
    function save_emp_performance_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('master_id') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
            $code = trim($this->input->post('code') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['contractAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $code = trim($this->input->post('code') ?? '');

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $code = trim($this->input->post('code') ?? '');
            $approvals_status = $this->appraisal_model->approve_document($system_code, $level_id, $status, $comments, $code);
        }

        if ($approvals_status == 1) {
            $this->session->set_flashdata('s', 'Approval Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function save_corporate_goal_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('goal_id') ?? '');
            $level_id = trim($this->input->post('level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comment') ?? '');
            $code = trim($this->input->post('code') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['contractAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }

        $code = trim($this->input->post('code') ?? '');
//var_dump($comments);exit;
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $code = trim($this->input->post('code') ?? '');
            $approvals_status = $this->appraisal_model->approve_document($system_code, $level_id, $status, $comments, $code);
        }

        if ($status == 1) {
            $data['status'] = true;
            $data['message'] = "Successfully approved.";
        } else {
            $data['status'] = true;
            $data['message'] = "Successfully rejected.";
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data['status'] = false;
            $data['message'] = "Goal not approved due to an system error.";
            return $data;
        } else {
            $this->db->trans_commit();
            return $data;
        }
    }

    public function get_departments($company_id)
    {
        $query = $this->db->query("SELECT srp_departmentmaster.DepartmentMasterID AS department_master_id,
srp_departmentmaster.DepartmentDes AS department_description,
srp_employeesdetails.Ename1,
srp_departmentmaster.DepartmentLogo
FROM srp_departmentmaster 
LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_departmentmaster.hod_id
WHERE srp_departmentmaster.Erp_companyID=$company_id");
        return $query->result();
    }

    public function get_sub_departments($department_master_id)
    {
        $query = $this->db->query("SELECT srp_erp_apr_subdepartment.department_master_id,
srp_erp_apr_subdepartment.id AS sub_department_id,
srp_erp_apr_subdepartment.description AS sub_department_description,
srp_erp_apr_subdepartment.code AS sub_department_code
FROM srp_erp_apr_subdepartment WHERE srp_erp_apr_subdepartment.department_master_id=$department_master_id AND srp_erp_apr_subdepartment.is_deleted!=1");
        return $query->result();
    }

    public function save_template_designation($template_id, $selected_designations)
    {
        $this->db->trans_start();
        if ($selected_designations != "") {
            foreach ($selected_designations as $selected_designation) {
                $record = array(
                    "designationID" => $selected_designation,
                    "softskillTemplateID" => $template_id,
                    "isActive" => 1,
                    "company_id" => current_companyID(),
                    'created_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                    'created_by' => current_userID(),
                    'created_at' => current_date(true),
                    'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                    'modified_by' => current_userID(),
                    'modified_at' => current_date(true),
                    "timestamp" => current_date()
                );
                $q = $this->db->query("select * from srp_erp_apr_softskillstemplatedesignations where designationID=$selected_designation and softskillTemplateID=$template_id and isActive=1");
                if ($q->num_rows() == 0) {
                    $this->db->insert('srp_erp_apr_softskillstemplatedesignations', $record);
                } else {
                    $row = $q->row();
                    if ($row->isActive == '0') {
                        $this->db->where(array("designationID" => $selected_designation, "softskillTemplateID" => $template_id));
                        $this->db->update('srp_erp_apr_softskillstemplatedesignations', array('isActive' => 1));
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $data['status'] = 'failed';
                $data['message'] = 'Database error.';
            } else {
                $this->db->trans_commit();
                $data['status'] = 'success';
                $data['message'] = 'Successfully saved.';
            }
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Database error.';
        }

        return $data;
    }

    public function add_sub_departments($company_id, $selected_department_id, $sub_department_description, $sub_department_code)
    {
        $insert_array = array(
            'description' => $sub_department_description,
            'department_master_id' => $selected_department_id,
            'company_id' => $company_id,
            'created_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'created_by' => current_userID(),
            'created_at' => current_date(true),
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true),
            'code' => $sub_department_code
        );
        $res = $this->db->insert('srp_erp_apr_subdepartment', $insert_array);
        if ($res) {
            $data['status'] = "success";
        } else {
            $data['status'] = "failed";
        }
        echo json_encode($data);
    }

    public function edit_sub_departments($sub_department_id, $sub_department_description, $sub_department_code)
    {
        $update_array = array(
            'description' => $sub_department_description,
            'code' => $sub_department_code,
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true)
        );
        $this->db->where('id', $sub_department_id);
        $this->db->update('srp_erp_apr_subdepartment', $update_array);
    }

    public function update_corporate_objective($corporate_objective_id, $objective)
    {
        $update_array = array(
            'description' => $objective,
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true)
        );
        $this->db->where('id', $corporate_objective_id);
        return $this->db->update('srp_erp_apr_corporateobjectivemaster', $update_array);
    }

    public function delete_sub_departments($sub_department_id)
    {
        $check_existance_query = $this->db->query("SELECT * FROM `srp_erp_apr_appraisal_sub_departments` WHERE sub_department_id=$sub_department_id");
        if ($check_existance_query->num_rows() > 0) {
            return 'already_in_use';
        } else {
            $update_array = array(
                'is_deleted' => "1",
                'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                'modified_by' => current_userID(),
                'modified_at' => current_date(true)
            );
            $this->db->where('id', $sub_department_id);
            $this->db->update('srp_erp_apr_subdepartment', $update_array);
            return 'success';
        }

    }

    public function delete_sub_department_task($task_id)
    {
        $this->db->where('id', $task_id);
        $this->db->delete('srp_erp_apr_appraisal_task');
    }

    public function insert_softskills_grades($template_id, $grades_array)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id");
        $number_of_performance_area = $query->num_rows();
        $maximum_number_of_performance_areas = (100 / (int)$grades_array[1]['marks']);

        if ($maximum_number_of_performance_areas < $number_of_performance_area) {
            $delete_from = ($number_of_performance_area - $maximum_number_of_performance_areas) + 1;

            for ($j = $delete_from; $j <= $number_of_performance_area; $j++) {
                //var_dump($j);
                $this->db->where('order', $j);
                $this->db->delete('srp_erp_apr_softskills_performance_area');
            }
        }


        $this->db->where('softskills_template_id', $template_id);
        $this->db->delete('srp_erp_apr_softskills_grades');
        //index 0 is ignored. data starts in 1st index
        $t = null;
        for ($i = 1; $i <= (sizeof($grades_array) - 1); $i++) {
            $grade = $grades_array[$i]['grade'];
            $marks = $grades_array[$i]['marks'];
            $insert_array = array(
                "grade" => $grade,
                "marks" => $marks,
                "softskills_template_id" => $template_id,
                "precedence" => $i,
                "company_id" => current_companyID()
            );
            $this->db->insert('srp_erp_apr_softskills_grades', $insert_array);
            $t = $i;
        }
        $t++;
        $insert_array = array(
            "grade" => "Not Applicable",
            "marks" => "-",
            "softskills_template_id" => $template_id,
            "precedence" => $t,
            "company_id" => current_companyID()
        );
        $this->db->insert('srp_erp_apr_softskills_grades', $insert_array);
    }

    public function delete_corporate_goal($goal_id)
    {
        $check_status_query = $this->db->query("SELECT * FROM `srp_erp_apr_corporate_goal` WHERE id=$goal_id AND approvedYN=1");
        $confirm_status_query = $this->db->query("SELECT * FROM `srp_erp_apr_corporate_goal` WHERE id=$goal_id AND confirmedYN=1");
        if ($check_status_query->num_rows() > 0) {
            return 'approved';
        } else if ($confirm_status_query->num_rows() > 0) {
            return 'already_confirmed';
        } else {
            $update_array = array(
                'is_deleted' => 1,
                'deleteByEmpID' => current_userID(),
                'deletedDatetime' => current_date(true)
            );
            $this->db->trans_start();
            $this->db->where('id', $goal_id);
            $this->db->update('srp_erp_apr_corporate_goal', $update_array);
            $this->db->query("DELETE FROM `srp_erp_apr_corporate_goal_objective_mapping` WHERE corporate_goal_id=$goal_id");
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                # Something went wrong.
                $this->db->trans_rollback();
                return 'db_error';
            } else {
                # Everything is Perfect.
                # Committing data to the database.
                $this->db->trans_commit();
                return 'success';
            }
        }

    }

    public function delete_corporate_objective($corporate_objective_id)
    {
        $check_existance_query = $this->db->query("SELECT * FROM `srp_erp_apr_appraisal_task` WHERE department_objective_id=$corporate_objective_id");

        if ($check_existance_query->num_rows() > 0) {
            return 'already_in_use';
        } else {
            $update_array = array(
                'is_deleted' => 1,
                'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                'modified_by' => current_userID(),
                'modified_at' => current_date(true)
            );
            $this->db->where('id', $corporate_objective_id);
            $this->db->update('srp_erp_apr_corporateobjectivemaster', $update_array);

            //delete mapping records
            $this->db->where('corporate_objective_id', $corporate_objective_id);
            $this->db->delete('srp_erp_apr_corporate_goal_objective_mapping');
            return 'success';
        }
    }

    public function insert_corporate_objective($company_id, $objective)
    {
        $insert_array = array(
            'description' => $objective,
            'company_id' => $company_id,
            'created_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'created_by' => current_userID(),
            'created_at' => current_date(true),
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true)
        );
        $res = $this->db->insert('srp_erp_apr_corporateobjectivemaster', $insert_array);
        if ($res) {
            $data['status'] = "success";
        } else {
            $data['status'] = "failed";
        }
        echo json_encode($data);
    }

    public function get_corporate_objectives($company_id)
    {
        $query = $this->db->query("SELECT srp_erp_apr_corporateobjectivemaster.description AS corporate_objective_description,
srp_erp_apr_corporateobjectivemaster.id AS corporate_objective_id
FROM srp_erp_apr_corporateobjectivemaster WHERE srp_erp_apr_corporateobjectivemaster.company_id=$company_id AND srp_erp_apr_corporateobjectivemaster.is_deleted!=1");
        return $query->result();
    }

    public function insert_corporate_goal($narration, $from_date, $to_date, $company_id, $goal_objectives_array, $confirmed, $appraisal_type, $selected_template)
    {
        $this->load->library('Approvals');

        $date = strtotime($from_date);
        $from_date = date('Y-m-d H:i:s', $date);

        $date = strtotime($to_date);
        $to_date = date('Y-m-d H:i:s', $date);

        if ($confirmed == '1') {
            $confirmed_by = current_userID();
        } else {
            $confirmed_by = '0';
        }

        if ($appraisal_type == 'objective_based') {
            $selected_template = "";
        }

        //generating doucment id
        $this->load->library('sequence');
        $document_id = $this->sequence->sequence_generator('CG');

        $insert_array = array(
            'created_date' => date("Y-m-d H:i:s"),
            'from' => $from_date,
            'to' => $to_date,
            'narration' => $narration,
            'confirmedByEmpID' => $confirmed_by,
            'confirmedYN' => $confirmed,
            'company_id' => $company_id,
            'created_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'created_by' => current_userID(),
            'created_at' => current_date(true),
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true),
            'document_id' => $document_id,
            'appraisal_type' => $appraisal_type,
            'softskills_template_id' => $selected_template
        );
        $this->db->insert('srp_erp_apr_corporate_goal', $insert_array);
        $insertId = $this->db->insert_id();

        //creating approval document
        if ($confirmed == '1') {
            $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification('CG', $insertId, $document_id, 'Corporate Goal', 'srp_erp_apr_corporate_goal', 'id', 0, current_date(true));
        }

        if ($appraisal_type != 'performance_based') {
            if (is_array($goal_objectives_array) && sizeof($goal_objectives_array) > 0) {
                foreach ($goal_objectives_array as $item) {
                    $insert_array = array(
                        'corporate_goal_id' => $insertId,
                        'corporate_objective_id' => $item['corporate_objective_id'],
                        'weight' => $item['weight'],
                        'assigned_department_id' => $item['assigned_department_id'],
                        "company_id" => current_companyID()
                    );
                    if($item['assigned_department_id']!=""){
                        $this->db->insert('srp_erp_apr_corporate_goal_objective_mapping', $insert_array);
                    }
                }
            }
        }

    }

    public function update_corporate_goal($narration, $from_date, $to_date, $company_id, $new_goal_objective_array, $edited_goal_objective_array, $id_list_for_delete, $goal_id, $confirmed, $document_id, $appraisal_type, $selected_template)
    {
        $this->load->library('Approvals');
        $date = strtotime($from_date);

        $from_date = date('Y-m-d H:i:s', $date);

        $date = strtotime($to_date);
        $to_date = date('Y-m-d H:i:s', $date);

        if ($confirmed == '1') {
            $confirmed_by = current_userID();
        } else {
            $confirmed_by = '0';
        }

        if ($appraisal_type == 'objective_based') {
            $selected_template = "";
        }

        $update_array = array(
            'created_date' => date("Y-m-d H:i:s"),
            'from' => $from_date,
            'to' => $to_date,
            'narration' => $narration,
            'confirmedByEmpID' => $confirmed_by,
            'confirmedYN' => $confirmed,
            'company_id' => $company_id,
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true),
            'appraisal_type' => $appraisal_type,
            'softskills_template_id' => $selected_template
        );
        $this->db->where('id', $goal_id);
        $this->db->update('srp_erp_apr_corporate_goal', $update_array);

        //creating approval document
        if ($confirmed == '1') {
            $is_document_exist_for_document_id = $this->is_document_exist_for_document_id($document_id);
            if (!$is_document_exist_for_document_id) {
                $validate_code = validate_code_duplication($document_id, 'document_id', $goal_id,'id', 'srp_erp_apr_corporate_goal', 'company_id');
                if(!empty($validate_code)) {
                    $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    return array(false, 'error');
                }

                $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification('CG', $goal_id, $document_id, 'Corporate Goal', 'srp_erp_apr_corporate_goal', 'id', 0, current_date(true));

            }
        }

        if ($appraisal_type != 'performance_based') {
            if (is_array($new_goal_objective_array) && sizeof($new_goal_objective_array) > 0) {
                foreach ($new_goal_objective_array as $item) {
                    $insert_array = array(
                        'corporate_goal_id' => $goal_id,
                        'corporate_objective_id' => $item['corporate_objective_id'],
                        'weight' => $item['weight'],
                        'assigned_department_id' => $item['assigned_department_id'],
                        "company_id" => current_companyID()
                    );
                    if($item['assigned_department_id']!=""){
                        $this->db->insert('srp_erp_apr_corporate_goal_objective_mapping', $insert_array);
                    }
                }
            }

            if (is_array($edited_goal_objective_array) && sizeof($edited_goal_objective_array) > 0) {
                foreach ($edited_goal_objective_array as $item) {
                    $update_array = array(
                        'weight' => $item['weight'],
                        'assigned_department_id' => $item['assigned_department_id']
                    );
                    if($item['assigned_department_id']!=""){
                        $this->db->where('id', $item['goal_objective_mapping_id']);
                        $this->db->update('srp_erp_apr_corporate_goal_objective_mapping', $update_array);
                    }
                }
            }

            if (is_array($id_list_for_delete) && sizeof($id_list_for_delete) > 0) {
                foreach ($id_list_for_delete as $item) {
                    $this->db->where('id', $item);
                    $this->db->delete('srp_erp_apr_corporate_goal_objective_mapping');
                }
            }
        } else {
            $this->db->where('corporate_goal_id', $goal_id);
            $this->db->delete('srp_erp_apr_corporate_goal_objective_mapping');
        }


    }

    public function is_document_exist_for_document_id($document_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_documentapproved WHERE documentCode='$document_id'");
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_corporate_goals($company_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_corporate_goal WHERE srp_erp_apr_corporate_goal.company_id=$company_id AND srp_erp_apr_corporate_goal.is_deleted=0 ORDER BY srp_erp_apr_corporate_goal.created_at DESC");
        return $query->result();
    }

    public function get_corporate_goals_for_dashboard($company_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_corporate_goal WHERE srp_erp_apr_corporate_goal.company_id=$company_id AND srp_erp_apr_corporate_goal.is_deleted=0 AND srp_erp_apr_corporate_goal.approvedYN=1 ORDER BY srp_erp_apr_corporate_goal.created_at DESC");
        return $query->result();
    }

    public function is_objective_already_exist($objective, $id)
    {
        $query = $this->db->query("SELECT count(srp_erp_apr_corporateobjectivemaster.id) as count FROM srp_erp_apr_corporateobjectivemaster WHERE srp_erp_apr_corporateobjectivemaster.description='$objective' AND srp_erp_apr_corporateobjectivemaster.id!=$id AND srp_erp_apr_corporateobjectivemaster.is_deleted=0");
        $count = $query->row()->count;
        return $count;
    }

    public function get_corporate_goal_details($goal_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_corporate_goal
join srp_employeesdetails on srp_employeesdetails.EIdNo = srp_erp_apr_corporate_goal.modified_by
WHERE srp_erp_apr_corporate_goal.id=$goal_id");

        $query2 = $this->db->query("SELECT srp_erp_apr_corporate_goal_objective_mapping.id as objective_mapping_id,
srp_erp_apr_corporate_goal_objective_mapping.weight,
srp_erp_apr_corporateobjectivemaster.description as objective_description,
srp_erp_apr_corporateobjectivemaster.id as objective_master_id,
srp_departmentmaster.DepartmentMasterID,
srp_departmentmaster.DepartmentDes
FROM srp_erp_apr_corporate_goal_objective_mapping
JOIN srp_erp_apr_corporateobjectivemaster ON srp_erp_apr_corporate_goal_objective_mapping.corporate_objective_id=srp_erp_apr_corporateobjectivemaster.id
JOIN srp_departmentmaster ON srp_erp_apr_corporate_goal_objective_mapping.assigned_department_id=srp_departmentmaster.DepartmentMasterID
WHERE srp_erp_apr_corporate_goal_objective_mapping.corporate_goal_id=$goal_id");

        $data['goal_details'] = $query->result();
        $data['goal_objectives'] = $query2->result();

        return $data;
    }


    public function save_emp_softskills_grade_self_eval($performance_id, $emp_id, $goal_id, $grade_id)
    {
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id;
        $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");

        $data = array();
        if ($query->num_rows() > 0) {
            $res = $query->result();
            //var_dump($this->db->last_query());exit;
            $template_mapping_id = $res[0]->id;

            $update_array = array(
                "grade_id" => $grade_id,
                "modified_at" => current_date(true)
            );
            $this->db->where('emp_template_mapping_id', $template_mapping_id);
            $this->db->where('performance_area_item_id', $performance_id);
            $this->db->update('srp_erp_apr_emp_softskills_score_self_eval', $update_array);
            //var_dump($this->db->last_query());exit;

            //updating total after modifying score data.
            $total = $this->appraisal_model->update_total_of_emp_performance_emp_self($template_mapping_id);

            $data['status'] = 'success';
            $data['message'] = 'Changes have been saved.';
            $data['total'] = $total;
            echo json_encode($data);
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Failed to update.';
            echo json_encode($data);
        }
    }

    public function save_emp_softskills_grade($performance_id, $emp_id, $goal_id, $grade_id)
    {
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id;
        $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");

        $data = array();
        if ($query->num_rows() > 0) {
            $res = $query->result();
            //var_dump($this->db->last_query());exit;
            $template_mapping_id = $res[0]->id;

            $update_array = array(
                "grade_id" => $grade_id,
                "modified_at" => current_date(true)
            );
            $this->db->where('emp_template_mapping_id', $template_mapping_id);
            $this->db->where('performance_area_item_id', $performance_id);
            $this->db->update('srp_erp_apr_emp_softskills_score', $update_array);
            //var_dump($this->db->last_query());exit;

            //updating total after modifying score data.
            $total = $this->appraisal_model->update_total_of_emp_performance($template_mapping_id);

            $data['status'] = 'success';
            $data['message'] = 'Changes have been saved.';
            $data['total'] = $total;
            echo json_encode($data);
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Failed to update.';
            echo json_encode($data);
        }
    }

    public function get_template_id_by_template_mapping_id($template_mapping_id)
    {
        $query = $this->db->query("SELECT softskills_template_id FROM `srp_erp_apr_emp_softskills_template_mapping` WHERE id=$template_mapping_id");
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->softskills_template_id;
        } else {
            return null;
        }
    }

    public function update_total_of_emp_performance_emp_self($template_mapping_id)
    {
        $query = $this->db->query("SELECT srp_erp_apr_softskills_grades.marks FROM srp_erp_apr_emp_softskills_score_self_eval
JOIN srp_erp_apr_softskills_grades ON srp_erp_apr_softskills_grades.id=srp_erp_apr_emp_softskills_score_self_eval.grade_id
 WHERE emp_template_mapping_id=$template_mapping_id");
        $total = 0;
        $softskills_template_id = $this->get_template_id_by_template_mapping_id($template_mapping_id);
        $value_of_maximum_grade = $this->value_of_maximum_grade($softskills_template_id);
        foreach ($query->result() as $item) {
            if ((int)$item->marks != -1) {
                $total += (int)$item->marks;
            } else {
                $total += (int)$value_of_maximum_grade;
            }
        }
        $update_array = array(
            "total_score" => $total
        );
        return $total;
    }

    public function update_total_of_emp_performance($template_mapping_id)
    {
        $query = $this->db->query("SELECT srp_erp_apr_softskills_grades.marks FROM srp_erp_apr_emp_softskills_score
JOIN srp_erp_apr_softskills_grades ON srp_erp_apr_softskills_grades.id=srp_erp_apr_emp_softskills_score.grade_id
 WHERE emp_template_mapping_id=$template_mapping_id");
        $total = 0;
        $softskills_template_id = $this->get_template_id_by_template_mapping_id($template_mapping_id);
        $value_of_maximum_grade = $this->value_of_maximum_grade($softskills_template_id);
        foreach ($query->result() as $item) {
            if ((int)$item->marks != -1) {
                $total += (int)$item->marks;
            } else {
                $total += (int)$value_of_maximum_grade;
            }
        }

        $update_array = array(
            "total_score" => $total
        );
        $this->db->where('id', $template_mapping_id);
        $this->db->update('srp_erp_apr_emp_softskills_template_mapping', $update_array);
        return $total;
    }

    public function fetch_employee_skills_performance_appraisal_for_summary_report($emp_id, $goal_details_and_objectives)
    {
        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id ?? null;
        //var_dump($goal_details_and_objectives['goal_details'][0]->id);exit;
        if ($performance_template_id != null) {
            $goal_id = $goal_details_and_objectives['goal_details'][0]->id;
            $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");
            //var_dump($this->db->last_query());exit;
            if ($query->num_rows() > 0) {
                $res = $query->result();
                $template_mapping_id = $res[0]->id;

                //getting performance area items that belongs to this template
                $query1 = $this->db->query("SELECT srp_erp_apr_emp_softskills_score.performance_area_item_id,
srp_erp_apr_softskills_performance_area.performance_area,
srp_erp_apr_softskills_performance_area.id as performance_area_id,
srp_erp_apr_emp_softskills_score.grade_id,
srp_erp_apr_softskills_grades.precedence
FROM srp_erp_apr_emp_softskills_score 
JOIN srp_erp_apr_softskills_performance_area ON srp_erp_apr_softskills_performance_area.id=srp_erp_apr_emp_softskills_score.performance_area_item_id
LEFT JOIN srp_erp_apr_softskills_grades ON srp_erp_apr_softskills_grades.id=srp_erp_apr_emp_softskills_score.grade_id
WHERE emp_template_mapping_id=$template_mapping_id 
GROUP BY srp_erp_apr_emp_softskills_score.performance_area_item_id");

                $data = array();
                $data['performance_areas'] = array();

                $skills_grades_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_grades WHERE softskills_template_id=$performance_template_id");
                $data['skills_grades_list'] = $skills_grades_query->result_array();
                $total = 0;

                $value_of_maximum_grade = $this->value_of_maximum_grade($performance_template_id);
                //var_dump($value_of_maximum_grade);exit;
                foreach ($query1->result() as $performance_area_item) {
                    $performance_area = array();
                    $performance_area['grade_id'] = $performance_area_item->grade_id;

                    foreach ($data['skills_grades_list'] as $item) {
                        if ($item['id'] == $performance_area_item->grade_id) {
                            if ((int)$item['marks'] == -1) {
                                $total += $value_of_maximum_grade;
                            } else {
                                $total += $item['marks'];
                            }

                        }
                    }

                }

                return $total;
            } else {
                return 0;
            }
        }

    }

    public function value_of_maximum_grade($performance_template_id)
    {
        $query = $this->db->query("SELECT marks FROM `srp_erp_apr_softskills_grades` WHERE softskills_template_id=$performance_template_id ORDER BY marks DESC LIMIT 1");
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->marks;
        } else {
            return null;
        }
    }

    public function fetch_employee_skills_performance_appraisal_self_eval($emp_id, $goal_details_and_objectives)
    {
        $this->load->library('Approvals');
        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id;
        $goal_id = $goal_details_and_objectives['goal_details'][0]->id;
        $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");
        //var_dump($this->db->last_query());exit;
        if ($query->num_rows() > 0) {

            $res = $query->result();
            //var_dump($res[0]);exit;
            $template_mapping_id = $res[0]->id;

            //getting performance area items that belongs to this template
            $query1 = $this->db->query("SELECT srp_erp_apr_emp_softskills_score_self_eval.performance_area_item_id,
            srp_erp_apr_softskills_performance_area.measuredPoints,
srp_erp_apr_softskills_performance_area.performance_area,
srp_erp_apr_softskills_performance_area.id as performance_area_id,
srp_erp_apr_softskills_performance_area.parent_id,
srp_erp_apr_softskills_performance_area.order,
srp_erp_apr_emp_softskills_score_self_eval.grade_id,
srp_erp_apr_emp_softskills_score_self_eval.employeePoints,
srp_erp_apr_emp_softskills_score.managerPoints,
srp_erp_apr_softskills_grades.precedence,
srp_erp_apr_emp_softskills_score_self_eval.modified_at
FROM srp_erp_apr_emp_softskills_score_self_eval 
JOIN srp_erp_apr_softskills_performance_area ON srp_erp_apr_softskills_performance_area.id=srp_erp_apr_emp_softskills_score_self_eval.performance_area_item_id
LEFT JOIN srp_erp_apr_softskills_grades ON srp_erp_apr_softskills_grades.id=srp_erp_apr_emp_softskills_score_self_eval.grade_id
LEFT JOIN srp_erp_apr_emp_softskills_score ON srp_erp_apr_softskills_performance_area.id = srp_erp_apr_emp_softskills_score.performance_area_item_id
WHERE srp_erp_apr_emp_softskills_score_self_eval.emp_template_mapping_id=$template_mapping_id AND 
srp_erp_apr_emp_softskills_score.emp_template_mapping_id=$template_mapping_id
GROUP BY srp_erp_apr_emp_softskills_score_self_eval.performance_area_item_id");

 //echo '<pre>';print_r($query1->result_array());exit;

            $data = array();
            $data['performance_areas'] = array();

            $skills_grades_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_grades WHERE softskills_template_id=$performance_template_id");
            $data['skills_grades_list'] = $skills_grades_query->result_array();

            $last_update_time = "";

            $performance_areas_with_sub = array();
            foreach ($query1->result() as $performance_area_item) {

                $performance_area = array();
                $performance_area['description'] = $performance_area_item->performance_area;
                $performance_area['grade_id'] = $performance_area_item->grade_id;
                $performance_area['precedence'] = $performance_area_item->precedence;
                $performance_area['performance_area_id'] = $performance_area_item->performance_area_id;
                $performance_area['parent_id'] = $performance_area_item->parent_id;
                $performance_area['order'] = $performance_area_item->order;
                $performance_area['measuredPoints'] = $performance_area_item->measuredPoints;
                $performance_area['employeePoints'] = $performance_area_item->employeePoints;
                $performance_area['managerPoints'] = $performance_area_item->managerPoints;
                $performance_area_group['sub'] = null;
                $pid = $performance_area_item->parent_id;

                if($pid!=0){
                    $row2 = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE id=$pid")->row();
                    $performance_area_group = array();
                    $performance_area_group['description'] = $row2->performance_area;
                    $performance_area_group['grade_id'] = "";
                    $performance_area_group['precedence'] = "";
                    $performance_area_group['performance_area_id'] = $row2->id;
                    $performance_area_group['parent_id'] = $row2->parent_id;
                    $performance_area_group['order'] = $row2->order;
                    $performance_area_group['sub'] = array();

                    if(isset($performance_areas_with_sub[$row2->id])){
                        array_push($performance_areas_with_sub[$row2->id],$performance_area);
//                        array_push($data['performance_areas'], $performance_area_group);
                    }else{
                        $performance_areas_with_sub[$row2->id] = array();
                        array_push($performance_areas_with_sub[$row2->id],$performance_area);
//                        array_push($data['performance_areas'], $performance_area_group);
                    }

                }else{
                    array_push($data['performance_areas'], $performance_area);
                }

                $modified_at = $performance_area_item->modified_at;
                if ($last_update_time == "") {
                    $last_update_time = $modified_at;
                } else {
                    if ($modified_at > $last_update_time) {
                        $last_update_time = $modified_at;
                    }
                }

            }

            foreach ($performance_areas_with_sub as $item){
                $pid = $item[0]['parent_id'];
                $row2 = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE id=$pid")->row();
                $performance_area_group = array();
                $performance_area_group['description'] = $row2->performance_area;
                $performance_area_group['grade_id'] = "";
                $performance_area_group['precedence'] = "";
                $performance_area_group['performance_area_id'] = $row2->id;
                $performance_area_group['parent_id'] = $row2->parent_id;
                $performance_area_group['order'] = $row2->order;
                $subpa_sort = array();
                foreach ($item as $key => $row)
                {
                    $subpa_sort[$key] = $row['order'];
                }
                array_multisort($subpa_sort, SORT_ASC, $item);
                $performance_area_group['sub'] = $item;
                array_push($data['performance_areas'],$performance_area_group);
            }

            $pa_sort = array();
            foreach ($data['performance_areas'] as $key => $row)
            {
                $pa_sort[$key] = $row['order'];
            }
            array_multisort($pa_sort, SORT_ASC, $data['performance_areas']);
            $data['last_update_time'] = $last_update_time;

            $data['begin_with_the_end_in_mind'] = $res[0]->begin_with_the_end_in_mind;
            $data['miscellaneous_worth_mentioning'] = $res[0]->miscellaneous_worth_mentioning;
            $data['benchmark_objective_assessment'] = $res[0]->benchmark_objective_assessment;

            $data['career_and_training_action_plan'] = $res[0]->career_and_training_action_plan;
            $data['manager_assessment_undertaking'] = $res[0]->manager_assessment_undertaking;
            $data['suggested_reward'] = $res[0]->suggested_reward;
            $data['identified_training_needs'] = $res[0]->identified_training_needs;
            $data['special_remarks_from_hod'] = $res[0]->special_remarks_from_hod;
            $data['manager_comment'] = $res[0]->manager_comment;
            $data['special_remarks_from_emp'] = $res[0]->special_remarks_from_emp;
            $data['is_confirmed_by_employee'] = (int)$res[0]->is_confirmed_by_employee;
            $data['is_approved'] = (int)$res[0]->confirmedYN;
            $data['template_mapping_id'] = $template_mapping_id;
            $data['is_goal_closed'] = $goal_details_and_objectives['goal_details'][0]->is_closed;

            $markingType = $this->db->query("SELECT * FROM srp_erp_apr_softskills_master WHERE id=$performance_template_id")->row_array();
            $data['markingType'] = $markingType['markingType'];
            $data['job_purpose'] = $markingType['job_purpose']; // job purpose (template purpose)
            $data['template_id'] = $performance_template_id;    // template id

            //print_r($data['markingType']);exit;

            return $data;

        }
    }

    public function fetch_employee_skills_performance_appraisal($emp_id, $goal_details_and_objectives, $manager_id = null)
    {
        $this->load->library('Approvals');

        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id;
        $goal_id = $goal_details_and_objectives['goal_details'][0]->id;
        $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");
        if ($query->num_rows() > 0) {

            $res = $query->result();
            $template_mapping_id = $res[0]->id;

            //getting performance area items that belongs to this template
            $query1 = $this->db->query("SELECT srp_erp_apr_emp_softskills_score.performance_area_item_id,
srp_erp_apr_softskills_performance_area.performance_area,
srp_erp_apr_softskills_performance_area.id as performance_area_id,
srp_erp_apr_softskills_performance_area.parent_id,
srp_erp_apr_softskills_performance_area.order,
srp_erp_apr_softskills_performance_area.measuredPoints,
srp_erp_apr_emp_softskills_score.grade_id,
srp_erp_apr_emp_softskills_score.managerPoints,
srp_erp_apr_emp_softskills_score_self_eval.employeePoints,
srp_erp_apr_softskills_grades.precedence,
srp_erp_apr_emp_softskills_score.modified_at,
srp_erp_apr_softskills_master.markingType
FROM srp_erp_apr_emp_softskills_score 
JOIN srp_erp_apr_softskills_performance_area ON srp_erp_apr_softskills_performance_area.id=srp_erp_apr_emp_softskills_score.performance_area_item_id
LEFT JOIN srp_erp_apr_softskills_grades ON srp_erp_apr_softskills_grades.id=srp_erp_apr_emp_softskills_score.grade_id 
LEFT JOIN srp_erp_apr_softskills_master ON srp_erp_apr_softskills_master.id=srp_erp_apr_softskills_performance_area.softskills_template_id
LEFT JOIN srp_erp_apr_emp_softskills_score_self_eval ON srp_erp_apr_softskills_performance_area.id = srp_erp_apr_emp_softskills_score_self_eval.performance_area_item_id
WHERE srp_erp_apr_emp_softskills_score.emp_template_mapping_id=$template_mapping_id AND
srp_erp_apr_emp_softskills_score_self_eval.emp_template_mapping_id=$template_mapping_id 
GROUP BY srp_erp_apr_emp_softskills_score_self_eval.performance_area_item_id");

            $data = array();
            $data['performance_areas'] = array();

            $skills_grades_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_grades WHERE softskills_template_id=$performance_template_id");
            $data['skills_grades_list'] = $skills_grades_query->result_array();

            $last_update_time = "";

            $performance_areas_with_sub = array();
            foreach ($query1->result() as $performance_area_item) {

                $performance_area = array();
                $performance_area['description'] = $performance_area_item->performance_area;
                $performance_area['grade_id'] = $performance_area_item->grade_id;
                $performance_area['precedence'] = $performance_area_item->precedence;
                $performance_area['performance_area_id'] = $performance_area_item->performance_area_id;
                $performance_area['parent_id'] = $performance_area_item->parent_id;
                $performance_area['order'] = $performance_area_item->order;
                $performance_area['measuredPoints'] = $performance_area_item->measuredPoints;
                $performance_area['managerPoints'] = $performance_area_item->managerPoints;
                $performance_area['employeePoints'] = $performance_area_item->employeePoints;
                $performance_area['markingType'] = $performance_area_item->markingType;
                $performance_area_group['sub'] = null;
                $pid = $performance_area_item->parent_id;

                if($pid!=0){
                    $row2 = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE id=$pid")->row();
                    $performance_area_group = array();
                    $performance_area_group['description'] = $row2->performance_area;
                    $performance_area_group['grade_id'] = "";
                    $performance_area_group['precedence'] = "";
                    $performance_area_group['performance_area_id'] = $row2->id;
                    $performance_area_group['parent_id'] = $row2->parent_id;
                    $performance_area_group['order'] = $row2->order;
                    $performance_area_group['sub'] = array();

                    if(isset($performance_areas_with_sub[$row2->id])){
                        array_push($performance_areas_with_sub[$row2->id],$performance_area);
                    }else{
                        $performance_areas_with_sub[$row2->id] = array();
                        array_push($performance_areas_with_sub[$row2->id],$performance_area);
                    }

                }else{
                    array_push($data['performance_areas'], $performance_area);
                }

                $modified_at = $performance_area_item->modified_at;
                if ($last_update_time == "") {
                    $last_update_time = $modified_at;
                } else {
                    if ($modified_at > $last_update_time) {
                        $last_update_time = $modified_at;
                    }
                }

            }

            foreach ($performance_areas_with_sub as $item){
                $pid = $item[0]['parent_id'];
                $row2 = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE id=$pid")->row();
                $performance_area_group = array();
                $performance_area_group['description'] = $row2->performance_area;
                $performance_area_group['grade_id'] = "";
                $performance_area_group['precedence'] = "";
                $performance_area_group['performance_area_id'] = $row2->id;
                $performance_area_group['parent_id'] = $row2->parent_id;
                $performance_area_group['order'] = $row2->order;

                $subpa_sort = array();
                foreach ($item as $key => $row)
                {
                    $subpa_sort[$key] = $row['order'];
                }
                array_multisort($subpa_sort, SORT_ASC, $item);
                $performance_area_group['sub'] = $item;
                array_push($data['performance_areas'],$performance_area_group);
            }

            $pa_sort = array();
            foreach ($data['performance_areas'] as $key => $row)
            {
                $pa_sort[$key] = $row['order'];
            }
            array_multisort($pa_sort, SORT_ASC, $data['performance_areas']);
            $data['last_update_time'] = $last_update_time;

            $data['begin_with_the_end_in_mind'] = $res[0]->begin_with_the_end_in_mind;
            $data['miscellaneous_worth_mentioning'] = $res[0]->miscellaneous_worth_mentioning;
            $data['benchmark_objective_assessment'] = $res[0]->benchmark_objective_assessment;
            $data['career_and_training_action_plan'] = $res[0]->career_and_training_action_plan;
            $data['manager_assessment_undertaking'] = $res[0]->manager_assessment_undertaking;

            $data['suggested_reward'] = $res[0]->suggested_reward;
            $data['identified_training_needs'] = $res[0]->identified_training_needs;
            $data['special_remarks_from_hod'] = $res[0]->special_remarks_from_hod;
            $data['manager_comment'] = $res[0]->manager_comment;
            $data['special_remarks_from_emp'] = $res[0]->special_remarks_from_emp;
            $data['is_confirmed_by_employee'] = (int)$res[0]->is_confirmed_by_employee;
            $data['is_approved'] = (int)$res[0]->confirmedYN;
            $data['ratingID'] = (int)$res[0]->ratingID;

            $data['template_mapping_id'] = $template_mapping_id;
            $data['is_goal_closed'] = $goal_details_and_objectives['goal_details'][0]->is_closed;

             /**mpo based - marking type & job purpose*/
            $markingType = $this->db->query("SELECT * FROM srp_erp_apr_softskills_master WHERE id=$performance_template_id")->row_array();
            $data['markingType'] = $markingType['markingType']; // marking type
            $data['job_purpose'] = $markingType['job_purpose']; // job purpose (template purpose)
            $data['template_id'] = $performance_template_id;    // template id

            /**mpo based - employee details*/
            $companyID = current_companyID();
            $emp_query = "
               SELECT
                    CONCAT( srp_erp_company.company_code, ' | ', srp_erp_company.company_name ) AS companyName,
                    srp_employeesdetails.Ename2 AS empName,
                    srp_designation.DesDescription AS empDesignation,
                    srp_employeesdetails.EDOJ AS empDOJ,
                    srp_erp_location.locationName AS empLocation,
                    managerDetails.Ename2 AS managerName 
                FROM
                    srp_employeesdetails
                    LEFT JOIN srp_erp_company ON srp_employeesdetails.Erp_companyID = srp_erp_company.company_id
                    LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
                    LEFT JOIN srp_erp_location ON srp_employeesdetails.locationID = srp_erp_location.locationID
                    LEFT JOIN srp_erp_employeemanagers ON srp_employeesdetails.EIdNo = srp_erp_employeemanagers.empID
                    LEFT JOIN srp_employeesdetails AS managerDetails ON srp_erp_employeemanagers.managerID = managerDetails.EIdNo 
                WHERE
                    srp_employeesdetails.Erp_companyID = {$companyID} 
                    AND srp_employeesdetails.EIdNo = {$emp_id} 
                    AND srp_erp_employeemanagers.companyID = {$companyID}
                    AND srp_erp_employeemanagers.active = 1;
            ";
            $employee_details = $this->db->query($emp_query)->row_array();
            /**mpo based - manager details */
            $manager = $this->db->query("SELECT
                    srp_designation.DesDescription AS managerdesignation 
                FROM
                    srp_erp_employeemanagers
                    JOIN srp_employeesdetails ON srp_erp_employeemanagers.managerID = srp_employeesdetails.EIdNo 
                    LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID 
                WHERE
                    srp_erp_employeemanagers.empID = {$emp_id} 
                    AND srp_erp_employeemanagers.companyID = {$companyID} 
                    AND srp_erp_employeemanagers.active = 1
            ")->row_array();

            $data['employee_details'] = $employee_details;
            $data['manager'] = $manager;

            return $data;

        } else {
            $this->load->library('sequence');
            $document_id = $this->sequence->sequence_generator('APR-SPE');

            $insert_array = array(
                "emp_id" => $emp_id,
                "softskills_template_id" => $performance_template_id,
                "goal_id" => $goal_id,
                "total_score" => 0,
                "company_id" => current_companyID(),
                "document_id" => $document_id,
                "confirmedYN" => 0
            );
            $this->db->insert('srp_erp_apr_emp_softskills_template_mapping', $insert_array);
            $softskills_template_mapping_id = $this->db->insert_id();

            $skills_performance_area_list = $this->appraisal_model->skills_performance_area_list($performance_template_id);
            foreach ($skills_performance_area_list as $skills_performance_area) {



                //$performance_area_id = $skills_performance_area['id'];
                $performance_area_id = $skills_performance_area->id;
                $insert_array = array(
                    "performance_area_item_id" => $performance_area_id,
                    "grade_id" => null,
                    "emp_template_mapping_id" => $softskills_template_mapping_id,
                    "modified_at" => current_date(true)
                );
                $this->db->insert('srp_erp_apr_emp_softskills_score', $insert_array);
                $this->db->insert('srp_erp_apr_emp_softskills_score_self_eval', $insert_array);
            }

            $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");
            $res = $query->result();
            $template_mapping_id = $res[0]->id;

            //getting performance area items that belongs to this template
            $query1 = $this->db->query("SELECT srp_erp_apr_emp_softskills_score.performance_area_item_id,
srp_erp_apr_softskills_performance_area.performance_area,
srp_erp_apr_softskills_performance_area.id as performance_area_id,
srp_erp_apr_softskills_performance_area.parent_id,
srp_erp_apr_softskills_performance_area.order,
srp_erp_apr_softskills_performance_area.measuredPoints,
srp_erp_apr_emp_softskills_score.managerPoints,
srp_erp_apr_emp_softskills_score_self_eval.employeePoints,
srp_erp_apr_emp_softskills_score.grade_id,
srp_erp_apr_softskills_grades.precedence,
srp_erp_apr_emp_softskills_score.modified_at, 
srp_erp_apr_softskills_master.markingType
FROM srp_erp_apr_emp_softskills_score 
JOIN srp_erp_apr_softskills_performance_area ON srp_erp_apr_softskills_performance_area.id=srp_erp_apr_emp_softskills_score.performance_area_item_id
LEFT JOIN srp_erp_apr_softskills_grades ON srp_erp_apr_softskills_grades.id=srp_erp_apr_emp_softskills_score.grade_id 
LEFT JOIN srp_erp_apr_softskills_master ON srp_erp_apr_softskills_master.id=srp_erp_apr_softskills_performance_area.softskills_template_id
LEFT JOIN srp_erp_apr_emp_softskills_score_self_eval ON srp_erp_apr_softskills_performance_area.id = srp_erp_apr_emp_softskills_score_self_eval.performance_area_item_id
WHERE srp_erp_apr_emp_softskills_score.emp_template_mapping_id=$template_mapping_id AND 
srp_erp_apr_emp_softskills_score_self_eval.emp_template_mapping_id=$template_mapping_id 
GROUP BY srp_erp_apr_emp_softskills_score.performance_area_item_id");

            $data = array();
            $data['performance_areas'] = array();

            $skills_grades_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_grades WHERE softskills_template_id=$performance_template_id");
            $data['skills_grades_list'] = $skills_grades_query->result_array();

            $last_update_time = "";

            $performance_areas_with_sub = array();
            foreach ($query1->result() as $performance_area_item) {

                $performance_area = array();
                $performance_area['description'] = $performance_area_item->performance_area;
                $performance_area['grade_id'] = $performance_area_item->grade_id;
                $performance_area['precedence'] = $performance_area_item->precedence;
                $performance_area['performance_area_id'] = $performance_area_item->performance_area_id;
                $performance_area['parent_id'] = $performance_area_item->parent_id;
                $performance_area['order'] = $performance_area_item->order;
                $performance_area['measuredPoints'] = $performance_area_item->measuredPoints;
                $performance_area['managerPoints'] = $performance_area_item->managerPoints;
                $performance_area['employeePoints'] = $performance_area_item->employeePoints;
                $performance_area['markingType'] = $performance_area_item->markingType;
                $performance_area_group['sub'] = null;
                $pid = $performance_area_item->parent_id;

                if($pid!=0){
                    $row2 = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE id=$pid")->row();
                    $performance_area_group = array();
                    $performance_area_group['description'] = $row2->performance_area;
                    $performance_area_group['grade_id'] = "";
                    $performance_area_group['precedence'] = "";
                    $performance_area_group['performance_area_id'] = $row2->id;
                    $performance_area_group['parent_id'] = $row2->parent_id;
                    $performance_area_group['order'] = $row2->order;
                    $performance_area_group['sub'] = array();

                    if(isset($performance_areas_with_sub[$row2->id])){
                        array_push($performance_areas_with_sub[$row2->id],$performance_area);
//                        array_push($data['performance_areas'], $performance_area_group);
                    }else{
                        $performance_areas_with_sub[$row2->id] = array();
                        array_push($performance_areas_with_sub[$row2->id],$performance_area);
//                        array_push($data['performance_areas'], $performance_area_group);
                    }

                }else{
                    array_push($data['performance_areas'], $performance_area);
                }

                $modified_at = $performance_area_item->modified_at;
                if ($last_update_time == "") {
                    $last_update_time = $modified_at;
                } else {
                    if ($modified_at > $last_update_time) {
                        $last_update_time = $modified_at;
                    }
                }

            }

            foreach ($performance_areas_with_sub as $item){
                $pid = $item[0]['parent_id'];
                $row2 = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE id=$pid")->row();
                $performance_area_group = array();
                $performance_area_group['description'] = $row2->performance_area;
                $performance_area_group['grade_id'] = "";
                $performance_area_group['precedence'] = "";
                $performance_area_group['performance_area_id'] = $row2->id;
                $performance_area_group['parent_id'] = $row2->parent_id;
                $performance_area_group['order'] = $row2->order;
                $subpa_sort = array();
                foreach ($item as $key => $row)
                {
                    $subpa_sort[$key] = $row['order'];
                }
                array_multisort($subpa_sort, SORT_ASC, $item);
                $performance_area_group['sub'] = $item;
                array_push($data['performance_areas'],$performance_area_group);
            }

            $pa_sort = array();
            foreach ($data['performance_areas'] as $key => $row)
            {
                $pa_sort[$key] = $row['order'];
            }
            array_multisort($pa_sort, SORT_ASC, $data['performance_areas']);
            $data['last_update_time'] = $last_update_time;

            $data['begin_with_the_end_in_mind'] = $res[0]->begin_with_the_end_in_mind;
            $data['miscellaneous_worth_mentioning'] = $res[0]->miscellaneous_worth_mentioning;
            $data['benchmark_objective_assessment'] = $res[0]->benchmark_objective_assessment;
            $data['career_and_training_action_plan'] = $res[0]->career_and_training_action_plan;
            $data['manager_assessment_undertaking'] = $res[0]->manager_assessment_undertaking;

            $data['suggested_reward'] = $res[0]->suggested_reward;
            $data['identified_training_needs'] = $res[0]->identified_training_needs;
            $data['special_remarks_from_hod'] = $res[0]->special_remarks_from_hod;
            $data['manager_comment'] = $res[0]->manager_comment;
            $data['special_remarks_from_emp'] = $res[0]->special_remarks_from_emp;
            $data['is_confirmed_by_employee'] = (int)$res[0]->is_confirmed_by_employee;
            $data['is_approved'] = (int)$res[0]->confirmedYN;
            $data['ratingID'] = (int)$res[0]->ratingID;
            $data['template_mapping_id'] = $template_mapping_id;
            $data['is_goal_closed'] = $goal_details_and_objectives['goal_details'][0]->is_closed;

            
            $markingType = $this->db->query("SELECT * FROM srp_erp_apr_softskills_master WHERE id=$performance_template_id")->row_array();
            $data['markingType'] = $markingType['markingType']; // marking type
            $data['job_purpose'] = $markingType['job_purpose']; // job purpose (template purpose)
            $data['template_id'] = $performance_template_id;    // template id

            /**mpo based - employee details */
            $companyID = current_companyID();
            $emp_query = "
               SELECT
                    CONCAT( srp_erp_company.company_code, ' | ', srp_erp_company.company_name ) AS companyName,
                    srp_employeesdetails.Ename2 AS empName,
                    srp_designation.DesDescription AS empDesignation,
                    srp_employeesdetails.EDOJ AS empDOJ,
                    srp_erp_location.locationName AS empLocation,
                    managerDetails.Ename2 AS managerName 
                FROM
                    srp_employeesdetails
                    LEFT JOIN srp_erp_company ON srp_employeesdetails.Erp_companyID = srp_erp_company.company_id
                    LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
                    LEFT JOIN srp_erp_location ON srp_employeesdetails.locationID = srp_erp_location.locationID
                    LEFT JOIN srp_erp_employeemanagers ON srp_employeesdetails.EIdNo = srp_erp_employeemanagers.empID
                    LEFT JOIN srp_employeesdetails AS managerDetails ON srp_erp_employeemanagers.managerID = managerDetails.EIdNo 
                WHERE
                    srp_employeesdetails.Erp_companyID = {$companyID} 
                    AND srp_employeesdetails.EIdNo = {$emp_id} 
                    AND srp_erp_employeemanagers.companyID = {$companyID}
                    AND srp_erp_employeemanagers.active = 1;
            ";
            $employee_details = $this->db->query($emp_query)->row_array();
            /**mpo based - manager details */
            $manager = $this->db->query("SELECT
                    srp_designation.DesDescription AS managerdesignation 
                FROM
                    srp_erp_employeemanagers
                    JOIN srp_employeesdetails ON srp_erp_employeemanagers.managerID = srp_employeesdetails.EIdNo 
                    LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID 
                WHERE
                    srp_erp_employeemanagers.empID = {$emp_id} 
                    AND srp_erp_employeemanagers.companyID = {$companyID} 
                    AND srp_erp_employeemanagers.active = 1
            ")->row_array();

            $data['employee_details'] = $employee_details;
            $data['manager'] = $manager;

            return $data;

        }
    }

    public function skills_performance_area_list($template_id){
        $skills_performance_area_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id and parent_id=0");
        $pa_without_sub = array();
        foreach ($skills_performance_area_query->result() as $item){
            $id = $item->id;
            $sub_skills_performance = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id and parent_id=$id");
            if($sub_skills_performance->num_rows()==0){
                array_push($pa_without_sub,$item);
            }
        }
        $sub_skills_performance_all = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id and parent_id!=0")->result();
        $data = array_merge($pa_without_sub,$sub_skills_performance_all);
        return $data;
    }



    public function performance_based_department_appraisal($department_ids)
    {
        $query = $this->db->query("SELECT srp_erp_apr_corporate_goal.id as goal_id, srp_erp_apr_corporate_goal.document_id, srp_erp_apr_corporate_goal.narration, srp_erp_apr_corporate_goal.`from`, srp_erp_apr_corporate_goal.is_closed, srp_erp_apr_corporate_goal.created_date from srp_erp_apr_corporate_goal 
WHERE srp_erp_apr_corporate_goal.approvedYN='1' AND (srp_erp_apr_corporate_goal.appraisal_type='performance_based' OR srp_erp_apr_corporate_goal.appraisal_type='both')");

        return $query->result();
    }

    public function department_appraisal($department_ids)
    {
        $companyID = current_companyID();

        $departmentSql = '';
        if ($department_ids) {
            $departmentSql = " AND srp_erp_apr_corporate_goal_objective_mapping.assigned_department_id in ($department_ids)";
        }

        $qry = "SELECT * FROM srp_erp_apr_corporate_goal WHERE company_id = {$companyID} AND approvedYN='1'";
        $corporategl = $this->db->query($qry)->result_array();

        foreach ($corporategl as $goal) {
            $goal_id = $goal['id'];

            $qry1 = "SELECT assigned_department_id FROM srp_erp_apr_corporate_goal_objective_mapping WHERE corporate_goal_id = {$goal_id} AND company_id=$companyID group by assigned_department_id having assigned_department_id is not null";
            $corporateglmap = $this->db->query($qry1)->result_array();
            foreach ($corporateglmap as $map) {
                $department_id = $map['assigned_department_id'];
                $this->appraisal_model->generate_document_for_department_appraisal($department_id, $goal_id);
            }
        }

        $guery1 = $this->db->query("SELECT distinct(srp_erp_apr_corporate_goal.id) FROM srp_erp_apr_corporate_goal join srp_erp_apr_department_appraisal_header on srp_erp_apr_department_appraisal_header.goal_id=srp_erp_apr_corporate_goal.id order by srp_erp_apr_department_appraisal_header.id desc");
        $result_array = array();
        foreach ($guery1->result() as $goal) {
            $goal_id = $goal->id;
            $query = $this->db->query("SELECT DISTINCT srp_erp_apr_corporate_goal_objective_mapping.assigned_department_id,srp_erp_apr_corporate_goal.id as goal_id, srp_erp_apr_corporate_goal.document_id, srp_erp_apr_corporate_goal.narration, srp_erp_apr_corporate_goal.`from`, srp_erp_apr_corporate_goal.is_closed, srp_erp_apr_corporate_goal.created_date, srp_departmentmaster.DepartmentMasterID, srp_departmentmaster.DepartmentDes,
srp_erp_apr_department_appraisal_header.document_id as department_appraisal_doc_id
from srp_erp_apr_corporate_goal_objective_mapping 
LEFT JOIN srp_erp_apr_department_appraisal_header ON srp_erp_apr_department_appraisal_header.department_id=srp_erp_apr_corporate_goal_objective_mapping.assigned_department_id
JOIN srp_erp_apr_corporate_goal ON srp_erp_apr_corporate_goal.id = srp_erp_apr_department_appraisal_header.goal_id
JOIN srp_departmentmaster ON srp_erp_apr_corporate_goal_objective_mapping.assigned_department_id=srp_departmentmaster.DepartmentMasterID 
WHERE srp_erp_apr_department_appraisal_header.goal_id=$goal_id AND srp_erp_apr_corporate_goal.approvedYN='1' $departmentSql GROUP BY srp_erp_apr_corporate_goal_objective_mapping.assigned_department_id order by srp_erp_apr_department_appraisal_header.id desc");
            $result_array = array_merge($result_array, $query->result());
        }

        return $result_array;
    }

    public function get_department_objective_completion_percentage($department_id, $corporate_objective_id, $department_appraisal_header_id)
    {
        $query = $this->db->query("SELECT `completion` FROM `srp_erp_apr_appraisal_task` WHERE department_objective_id=$corporate_objective_id AND appraisal_id=$department_appraisal_header_id");
        $total = 0;
        $number_of_tasks = 0;
        foreach ($query->result() as $row) {
            $number_of_tasks++;
            $total += $row->completion;
        }
        if ($number_of_tasks != 0) {
            $total = $total / $number_of_tasks;
        }
        return $total;
    }

    public function department_appraisal_details($department_id, $goal_id, $department_appraisal_header_id)
    {
        $query = $this->db->query("SELECT
	objective_mapping.assigned_department_id,
	goal.id AS goal_id,
	goal.document_id,
	goal.narration,
	goal.`from`,
	goal.to,
	goal.is_closed,
	goal.created_date,
	srp_departmentmaster.DepartmentMasterID,
	srp_departmentmaster.DepartmentDes,
	objective_mapping.corporate_objective_id,
	objective_mapping.weight,
	srp_erp_apr_corporateobjectivemaster.description AS objective_description 
FROM
	srp_erp_apr_corporate_goal_objective_mapping AS objective_mapping
	JOIN srp_erp_apr_corporate_goal AS goal ON goal.id = objective_mapping.corporate_goal_id
	JOIN srp_departmentmaster ON objective_mapping.assigned_department_id = srp_departmentmaster.DepartmentMasterID
	JOIN srp_erp_apr_corporateobjectivemaster ON srp_erp_apr_corporateobjectivemaster.id = objective_mapping.corporate_objective_id 
WHERE
	objective_mapping.assigned_department_id = $department_id 
	AND objective_mapping.corporate_goal_id = $goal_id");
//var_dump($this->db->last_query());exit;
        $data = array();
        foreach ($query->result() as $row) {
            $corporate_objective_id = $row->corporate_objective_id;
            $used_percentage = $this->appraisal_model->get_used_percentage($department_id, $corporate_objective_id, $department_appraisal_header_id);
            $completed_percentage = $this->appraisal_model->get_department_objective_completion_percentage($department_id, $corporate_objective_id, $department_appraisal_header_id);

            $record = array();
            if ($used_percentage == null) {
                $record['used_percentage'] = "0";
            } else {
                $record['used_percentage'] = $used_percentage;
            }
            $record['assigned_department_id'] = $row->assigned_department_id;
            $record['goal_id'] = $row->goal_id;
            $record['document_id'] = $row->document_id;
            $record['narration'] = $row->narration;
            $record['from'] = $row->from;
            $record['to'] = $row->to;
            $record['is_closed'] = $row->is_closed;
            $record['created_date'] = $row->created_date;
            $record['DepartmentMasterID'] = $row->DepartmentMasterID;
            $record['DepartmentDes'] = $row->DepartmentDes;
            $record['corporate_objective_id'] = $row->corporate_objective_id;
            $record['weight'] = $row->weight;
            $record['objective_description'] = $row->objective_description;
            $record['completion_percentage'] = $completed_percentage;
            array_push($data, $record);
        }
        return $data;

    }

    public function get_overall_completion_percentage($corporate_goal_id, $assigned_department_id)
    {
        $department_objectives = $this->db->query("SELECT corporate_objective_id FROM `srp_erp_apr_corporate_goal_objective_mapping` WHERE corporate_goal_id=$corporate_goal_id AND assigned_department_id=$assigned_department_id");
        $task_completion_total_of_department = 0;

        //appraisal header id
        $appraisal_header_query = $this->db->query("SELECT id as appraisal_header_id FROM `srp_erp_apr_department_appraisal_header` WHERE goal_id=$corporate_goal_id AND department_id=$assigned_department_id");
        if ($appraisal_header_query->num_rows() > 0) {

            $appraisal_header_id = $appraisal_header_query->row()->appraisal_header_id;

        } else {
            $appraisal_header_id = 0;
        }

        $no_of_task = 0;
        foreach ($department_objectives->result() as $row) {
            $corporate_objective_id = $row->corporate_objective_id;
            $department_tasks = $this->db->query("SELECT * FROM `srp_erp_apr_appraisal_task` WHERE department_objective_id=$corporate_objective_id AND appraisal_id=$appraisal_header_id");
            $no_of_task += $department_tasks->num_rows();
            $task_completion_total_of_objective = 0;
            foreach ($department_tasks->result() as $row2) {
                $completion = $row2->completion;
                $task_completion_total_of_objective += $completion;
            }
            $task_completion_total_of_department += $task_completion_total_of_objective;
        }
        if ($task_completion_total_of_department != 0) {
            $overall_completion = ROUND(($task_completion_total_of_department / $no_of_task), 2);
        } else {
            $overall_completion = 0;
        }

        return $overall_completion;
    }

    public function overall_completion_percentages_for_dashboard($corporate_goal_id, $assigned_department_id)
    {
        $department_objectives = $this->db->query("SELECT corporate_objective_id FROM `srp_erp_apr_corporate_goal_objective_mapping` WHERE corporate_goal_id=$corporate_goal_id AND assigned_department_id=$assigned_department_id");
        $task_completion_total_of_department = 0;
        $no_of_task = 0;
        foreach ($department_objectives->result() as $row) {
            $corporate_objective_id = $row->corporate_objective_id;
            $department_tasks = $this->db->query("SELECT * FROM `srp_erp_apr_appraisal_task` WHERE department_objective_id=$corporate_objective_id");
            $task_completion_total_of_objective = 0;
            $no_of_task += $department_tasks->num_rows();
            foreach ($department_tasks->result() as $row2) {
                $completion = $row2->completion;
                $task_completion_total_of_objective += $completion;
            }
            $task_completion_total_of_department += $task_completion_total_of_objective;
        }
        if ($task_completion_total_of_department != 0) {
            $overall_completion = ROUND(($task_completion_total_of_department / $no_of_task), 2);
        } else {
            $overall_completion = 0;
        }
        return $overall_completion;
    }

    public function regenerate_department_appraisal_with_newly_added_subdepartments($department_master_id, $department_appraisal_id)
    {
        //this query fetches list of ids that not already exist in appraisal sub department list. which means those sub departments were created after initialize department appraisal.
        $query = $this->db->query("SELECT id FROM srp_erp_apr_subdepartment WHERE department_master_id=$department_master_id AND is_deleted=0 AND id NOT IN (SELECT sub_department_id FROM `srp_erp_apr_appraisal_sub_departments` WHERE department_appraisal_header_id=$department_appraisal_id)");
        foreach ($query->result() as $row) {
            $sub_department_id = $row->id;
            $insert_array = array(
                "department_appraisal_header_id" => $department_appraisal_id,
                "sub_department_id" => $sub_department_id,
                "company_id" => current_companyID()
            );
            $this->db->insert('srp_erp_apr_appraisal_sub_departments', $insert_array);
        }
    }

    public function generate_document_for_department_appraisal($department_id, $goal_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_department_appraisal_header WHERE department_id=$department_id AND goal_id=$goal_id");
        if ($query->num_rows() > 0) {
            $header_details = $query->row();
        } else {
            //generating department appraisal document.
            $this->load->library('sequence');
            $document_id = $this->sequence->sequence_generator('APR');
            $insert_array = array(
                'goal_id' => $goal_id,
                'department_id' => $department_id,
                'created_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                'created_by' => current_userID(),
                'created_at' => current_date(true),
                'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                'modified_by' => current_userID(),
                'modified_at' => current_date(true),
                'document_id' => $document_id,
                "company_id" => current_companyID()
            );
            $this->db->insert('srp_erp_apr_department_appraisal_header', $insert_array);
            $header_id = $this->db->insert_id();
            $query2 = $this->db->query("SELECT * FROM srp_erp_apr_department_appraisal_header WHERE department_id=$department_id AND goal_id=$goal_id");
            $header_details = $query2->row();
            $query3 = $this->db->query("SELECT * FROM srp_erp_apr_subdepartment WHERE department_master_id=$department_id AND is_deleted=0");
            $insert_list = array();
            foreach ($query3->result() as $item) {
                //generate record for each sub department.
                $insert_array = array(
                    'department_appraisal_header_id' => $header_id,
                    'sub_department_id' => $item->id,
                    "company_id" => current_companyID()
                );
                array_push($insert_list, $insert_array);
            }
            if (!empty($insert_list)) {
                $this->db->insert_batch('srp_erp_apr_appraisal_sub_departments', $insert_list);
            }
        }
        return $header_details;
    }

    public function get_sub_departments_by_department_id($department_appraisal_header_id)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_appraisal_sub_departments`
JOIN srp_erp_apr_subdepartment ON srp_erp_apr_subdepartment.id=srp_erp_apr_appraisal_sub_departments.sub_department_id
WHERE srp_erp_apr_appraisal_sub_departments.department_appraisal_header_id=$department_appraisal_header_id");
        return $query->result();
    }

    public function get_department_employees($department_id)
    {
        $this->db->select('srp_employeesdetails.*');
        $this->db->from('srp_employeesdetails');
        $this->db->join(
            'srp_empdepartments',
            'srp_empdepartments.EmpID = srp_employeesdetails.EIdNo',
            'inner'
        );
        $this->db->join(
            'srp_departmentmaster',
            'srp_departmentmaster.DepartmentMasterID = srp_empdepartments.DepartmentMasterID',
            'inner'
        );
        $this->db->where('srp_empdepartments.DepartmentMasterID', $department_id);
        $this->db->where('srp_employeesdetails.isDischarged !=', 1);
        $this->db->group_by('srp_employeesdetails.EIdNo');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_employees()
    {
        $Erp_companyID = current_companyID();
        $query = $this->db->query("SELECT * FROM `srp_employeesdetails`
JOIN srp_empdepartments ON srp_empdepartments.EmpID=srp_employeesdetails.EIdNo
JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID=srp_empdepartments.DepartmentMasterID
WHERE srp_employeesdetails.Erp_companyID=$Erp_companyID AND srp_employeesdetails.isDischarged!=1 GROUP BY srp_employeesdetails.EIdNo");

        return $query->result();
    }

    public function get_employees_for_performance_apr()
    {
        $Erp_companyID = current_companyID();
        $current_userID = $this->common_data['current_userID'];
        $row = $this->db->query("select isHRAdmin from srp_employeesdetails where EIdNo=$current_userID")->row();
        $isHRAdmin = $row->isHRAdmin;
        if ($isHRAdmin == 1) {
            $query = $this->db->query("SELECT * FROM `srp_employeesdetails`
JOIN srp_empdepartments ON srp_empdepartments.EmpID=srp_employeesdetails.EIdNo
JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID=srp_empdepartments.DepartmentMasterID
WHERE srp_employeesdetails.Erp_companyID=$Erp_companyID 
AND srp_employeesdetails.isDischarged!=1 
AND srp_employeesdetails.EIdNo NOT IN ($current_userID)
GROUP BY srp_employeesdetails.EIdNo");
            return $query->result();
        } else {
            $query = $this->db->query("SELECT * FROM `srp_employeesdetails`
JOIN srp_empdepartments ON srp_empdepartments.EmpID=srp_employeesdetails.EIdNo
JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID=srp_empdepartments.DepartmentMasterID
JOIN srp_erp_employeemanagers ON srp_erp_employeemanagers.empID = srp_employeesdetails.EIdNo
WHERE srp_employeesdetails.Erp_companyID=$Erp_companyID 
AND srp_employeesdetails.isDischarged!=1
AND srp_erp_employeemanagers.managerID=$current_userID
AND srp_erp_employeemanagers.active=1
AND srp_employeesdetails.EIdNo NOT IN ($current_userID)
GROUP BY srp_employeesdetails.EIdNo");
            return $query->result();
        }


    }

    public function get_department_employees_by_id_list($department_id, $list_of_employee_ids)
    {
        if (!empty($list_of_employee_ids)) {
            $query = $this->db->query("SELECT * FROM `srp_employeesdetails`
JOIN srp_empdepartments ON srp_empdepartments.EmpID=srp_employeesdetails.EIdNo
WHERE srp_empdepartments.DepartmentMasterID=$department_id AND srp_employeesdetails.EIdNo IN ($list_of_employee_ids)");
            return $query->result();
        }
        return false;
    }

    public function get_employee_departments($employee_id)
    {
        $query = $this->db->query("SELECT srp_empdepartments.DepartmentMasterID FROM srp_empdepartments WHERE srp_empdepartments.EmpID=$employee_id");
        $department_ids_string = "";
        $i = 1;
        foreach ($query->result() as $row) {
            $department_ids_string .= $row->DepartmentMasterID;
            if ($i < $query->num_rows()) {
                $department_ids_string .= ",";
            }
            $i++;
        }
        return $department_ids_string;
    }

    public function get_employee_departments_array($employee_id)
    {
        $query = $this->db->query("SELECT srp_empdepartments.DepartmentMasterID FROM srp_empdepartments WHERE srp_empdepartments.EmpID=$employee_id");
        $department_ids_array = array();

        foreach ($query->result() as $row) {
            $department_ids_array[] = (int)$row->DepartmentMasterID;

        }
        return $department_ids_array;
    }

    public function get_employee_departments_data($employee_id)
    {
        $this->db->select('srp_empdepartments.DepartmentMasterID, srp_departmentmaster.DepartmentDes');
        $this->db->from('srp_empdepartments');
        $this->db->join(
            'srp_departmentmaster',
            'srp_departmentmaster.DepartmentMasterID = srp_empdepartments.DepartmentMasterID',
            'inner'
        );
        $this->db->where('srp_empdepartments.EmpID', $employee_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function insert_department_task($department_appraisal_header_id, $appraisal_sub_department_id, $task_description, $task_weight, $department_objective_id, $assigned_employee_id, $date_to_complete, $task_created_user_type)
    {
        $date = strtotime($date_to_complete);
        $date_to_complete = date('Y-m-d', $date);

        $insert_array = array(
            'task_description' => $task_description,
            'weight' => $task_weight,
            'employee_id' => $assigned_employee_id,
            'date_to_complete' => $date_to_complete,
            'appraisal_sub_department_id' => $appraisal_sub_department_id,
            'completion' => '0',
            'manager_review' => 'pending',
            'department_objective_id' => $department_objective_id,
            'created_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'created_by' => current_userID(),
            'created_at' => current_date(true),
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true),
            'appraisal_id' => $department_appraisal_header_id,
            'created_user_type' => $task_created_user_type,
            "company_id" => current_companyID()
        );
        $this->db->insert('srp_erp_apr_appraisal_task', $insert_array);
    }

    public function edit_department_task($task_id, $task_description, $task_weight, $department_objective_id, $assigned_employee_id, $date_to_complete)
    {
        $date = strtotime($date_to_complete);
        $date_to_complete = date('Y-m-d', $date);

        $update_array = array(
            'task_description' => $task_description,
            'weight' => $task_weight,
            'employee_id' => $assigned_employee_id,
            'date_to_complete' => $date_to_complete,
            'department_objective_id' => $department_objective_id,
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true)
        );

        $this->db->where('id', $task_id);
        $this->db->update('srp_erp_apr_appraisal_task', $update_array);

        //var_dump($this->db->last_query());exit;
    }

    public function allocated_percentage_for_objective($department_id, $goal_id, $objective_id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_apr_corporate_goal_objective_mapping');

        if ($objective_id) {
            $this->db->where('corporate_objective_id', $objective_id);
        }
        if ($department_id) {
            $this->db->where('assigned_department_id', $department_id);
        }
        if ($goal_id) {
            $this->db->where('corporate_goal_id', $goal_id);
        }

        $query = $this->db->get();
        $row = $query->row();

        return $row ? $row->weight : null; // Return null if no record is found
    }

    public function get_used_percentage($department_id, $objective_id, $department_appraisal_header_id)
    {
        $this->db->select('SUM(srp_erp_apr_appraisal_task.weight) as total_weight');
        $this->db->from('srp_erp_apr_appraisal_task');
        $this->db->join(
            'srp_erp_apr_subdepartment',
            'srp_erp_apr_subdepartment.id = srp_erp_apr_appraisal_task.appraisal_sub_department_id',
            'inner'
        );

        if ($department_id) {
            $this->db->where('srp_erp_apr_subdepartment.department_master_id', $department_id);
        }
        if ($objective_id) {
            $this->db->where('srp_erp_apr_appraisal_task.department_objective_id', $objective_id);
        }
        if ($department_appraisal_header_id) {
            $this->db->where('srp_erp_apr_appraisal_task.appraisal_id', $department_appraisal_header_id);
        }

        $query = $this->db->get();
        $row = $query->row();

        return $row ? $row->total_weight : 0;
    }

    public function allocated_percentage_for_department($department_id, $goal_id)
    {
        $query = $this->db->query("SELECT SUM(weight) as allocated_weight_for_department FROM `srp_erp_apr_corporate_goal_objective_mapping` WHERE assigned_department_id = $department_id AND corporate_goal_id=$goal_id");
        $row = $query->row();
        return $row->allocated_weight_for_department;
    }

    public function insert_softskills_template($company_id, $template_name, $markingType)
    {
        $insert_array = array(
            "company_id" => $company_id,
            "name" => $template_name,
            "markingType" => $markingType,
            "company_id" => current_companyID()
        );
        $this->db->insert("srp_erp_apr_softskills_master", $insert_array);
    }

    public function get_performance_area_details($performance_area_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE id=$performance_area_id");
        return $query->result_array();
    }

    public function edit_template_name($template_id, $template_name)
    {
        $update_array = array(
            "name" => $template_name
        );
        $this->db->where('id', $template_id);
        $this->db->update('srp_erp_apr_softskills_master', $update_array);
        $data['status'] = 'success';
        $data['message'] = 'Successfully modified the name';
        return $data;
    }

    public function delete_soft_skills_template($template_id)
    {

        if ($this->is_soft_skills_template_deletable($template_id)) {
            $this->db->where('softskills_template_id', $template_id);
            $this->db->delete('srp_erp_apr_softskills_performance_area');

            $this->db->where('softskills_template_id', $template_id);
            $this->db->delete('srp_erp_apr_softskills_grades');

            $this->db->where('id', $template_id);
            $this->db->delete('srp_erp_apr_softskills_master');

            $data['status'] = 'success';
            $data['message'] = 'Successfully deleted the record.';
            return $data;
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Delete grades and performance areas before delete the template.';
            return $data;
        }
    }

    private function is_soft_skills_template_deletable($template_id)
    {
        $query = $this->db->get_where("srp_erp_apr_softskills_grades", array("softskills_template_id" => $template_id));
        $state1 = $query->num_rows() > 0 ? false : true;
        $query = $this->db->get_where("srp_erp_apr_softskills_performance_area", array("softskills_template_id" => $template_id));
        $state2 = $query->num_rows() > 0 ? false : true;
        if ($state1 && $state2) {
            return true;
        } else {
            return false;
        }
    }

    public function update_performance_area($performance_area_id, $performance_area, $order)
    {
        $update_array = array(
            "performance_area" => $performance_area,
            "order" => $order
        );

        $this->db->where('id', $performance_area_id);
        $this->db->update('srp_erp_apr_softskills_performance_area', $update_array);
    }

    public function delete_performance_area($performance_area_id)
    {
        $this->db->where('id', $performance_area_id);
        $this->db->delete('srp_erp_apr_softskills_performance_area');
        $this->db->where('parent_id', $performance_area_id);
        $this->db->delete('srp_erp_apr_softskills_performance_area');
    }


    public function get_softskills_template_details($template_id)
    {
        $skills_template_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_master WHERE id=$template_id");
        // var_dump($this->db->last_query());
        $data['skills_template_details'] = $skills_template_query->result_array();
        //print_r($data['skills_template_details']);exit;

        $skills_template_purpose = $this->db->query("SELECT job_purpose FROM srp_erp_apr_softskills_master WHERE id=$template_id");
        $data['job_purpose'] = $skills_template_purpose->row_array();

        $mrkType = $this->db->query("SELECT markingType FROM srp_erp_apr_softskills_master WHERE id=$template_id");
        $data['markingType'] = $mrkType->row_array('markingType');

        $skills_grades_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_grades WHERE softskills_template_id=$template_id");
        //var_dump($this->db->last_query());
        
        $data['skills_grades_list'] = $skills_grades_query->result_array();
        //print_r($data['skills_grades_list']);exit;

        $skills_performance_area_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id and parent_id=0 ORDER BY `order`");
        // var_dump($this->db->last_query());
        $skills_performance_area_result = $skills_performance_area_query->result_array();
        //echo '<pre>';print_r($skills_performance_area_result);exit;

        $data['skills_performance_area_list'] = array();
        foreach ($skills_performance_area_result as $item) {
            $data2['id'] = $item['id'];
            $data2['performance_area'] = $item['performance_area'];
            $data2['order'] = $item['order'];
            $data2['softskills_template_id'] = $item['softskills_template_id'];
            $data2['company_id'] = $item['company_id'];
            $data2['parent_id'] = $item['parent_id'];
            $id= $data2['id'];
            $sub_performance_area_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id AND parent_id=$id ORDER BY `order`");
            $data2['sub_performance_areas']=$sub_performance_area_query->result_array();
            
            array_push( $data['skills_performance_area_list'],$data2);
        }
        //echo '<pre>';print_r($data['skills_performance_area_list']);exit;

        $query = $this->db->query("select * from srp_erp_apr_corporate_goal where softskills_template_id=$template_id and is_deleted=0");
        if ($query->num_rows() > 0) {
            $data['is_already_using'] = 1;
        } else {
            $data['is_already_using'] = 0;
        }

        return $data;
    }

    public function insert_performance_area($template_id, $performance_area, $order)
    {
        $insert_array = array(
            "performance_area" => $performance_area,
            "order" => $order,
            "softskills_template_id" => $template_id,
            "company_id" => current_companyID(),
            "parent_id" => 0
        );
        $this->db->insert('srp_erp_apr_softskills_performance_area', $insert_array);
    }

    public function get_next_number_for_pa($template_id)
    {
        $pa_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id and parent_id=0 ORDER BY `order` desc limit 1");
        if ($pa_query->num_rows() == 0) {
            return 1;
        } else {
            $order = $pa_query->row()->order;
            return $order + 1;
        }

    }

    public function get_next_number_for_subpa($template_id, $parent_id)
    {
        $pa_query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_performance_area WHERE softskills_template_id=$template_id and parent_id=$parent_id ORDER BY `order` desc limit 1");
        if ($pa_query->num_rows() == 0) {
            return 1;
        } else {
            $order = $pa_query->row()->order;
            return $order + 1;
        }
    }

    public function get_softskills_templates($company_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_softskills_master WHERE company_id=$company_id");
        return $query->result_array();
    }

    public function get_employee_performance_header_details($goal_id, $department_id, $emp_id)
    {
        if (!$department_id) {
            return [];
        }

        $this->load->library('Approvals');

        $appraisal_id_query = $this->db->query("SELECT
	srp_erp_apr_department_appraisal_header.id AS appraisal_header_id 
FROM
	srp_erp_apr_department_appraisal_header 
WHERE
	department_id = $department_id 
	AND goal_id = $goal_id");


        if ($appraisal_header_id = $appraisal_id_query->num_rows() > 0) {
            $appraisal_header_id = $appraisal_id_query->row()->appraisal_header_id;
        }

        $query = $this->db->query("SELECT * FROM srp_erp_apr_employee_performance_appraisal_header 
WHERE corporate_goal_id = $goal_id
AND  department_id = $department_id
AND emp_id = $emp_id");

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {

            $this->load->library('sequence');
            $document_id = $this->sequence->sequence_generator('APR-EWP');

            $insert_array = array(
                "corporate_goal_id" => $goal_id,
                "department_id" => $department_id,
                "emp_id" => $emp_id,
                "department_appraisal_header_id" => $appraisal_header_id,
                "company_id" => $this->common_data['company_data']['company_id'],
                "document_id" => $document_id
            );
            $this->db->insert('srp_erp_apr_employee_performance_appraisal_header', $insert_array);
            $insertId = $this->db->insert_id();

            $query = $this->db->query("SELECT * FROM srp_erp_apr_employee_performance_appraisal_header 
WHERE corporate_goal_id = $goal_id
AND  department_id = $department_id
AND emp_id = $emp_id");

            $this->approvals->CreateApprovalWitoutEmailnotification('APR-EWP', $insertId, $document_id, 'Employee Wise Performance', 'srp_erp_apr_employee_performance_appraisal_header', 'id', 0, current_date(true));

            return $query->result_array();
        }

    }

    public function insert_approval_setup($approval_type, $approval_level, $department_id, $company_id)
    {

    }

    public function get_department_hod($department_id, $goal_id)
    {
        $hod_query = $this->db->query("select srp_employeesdetails.*,srp_departmentmaster.hod_id from srp_departmentmaster 
join srp_employeesdetails on srp_employeesdetails.EIdNo=srp_departmentmaster.hod_id
where srp_departmentmaster.DepartmentMasterID=$department_id");
        return $hod_query->result_array();
    }

    public function get_performance_appraisal_header_by_id($MasterID)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_employee_performance_appraisal_header` WHERE `id` = $MasterID");
        return $query->row();
    }

    public function get_softskills_template_mapping($MasterID)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_emp_softskills_template_mapping` WHERE `id` = $MasterID");
        //var_dump($this->db->last_query());exit;
        return $query->row();
    }

    public function get_employee_tasks_for_employee_wise_performance_report($department_id, $goal_id, $employee_id)
    {
        $this->db->select('srp_erp_apr_department_appraisal_header.id AS appraisal_header_id');
        $this->db->from('srp_erp_apr_department_appraisal_header');
        $this->db->where('department_id', $department_id);
        $this->db->where('goal_id', $goal_id);
        $appraisal_id_query = $this->db->get()->row_array();

        if ($appraisal_id_query) {
            $appraisal_header_id = $appraisal_id_query['appraisal_header_id'];
            $employee_wise_performance_query = $this->db->query("SELECT
	srp_erp_apr_appraisal_task.task_description,
	srp_erp_apr_appraisal_task.id AS task_id,
	srp_erp_apr_appraisal_task.weight,
	srp_erp_apr_appraisal_task.date_to_complete,
	srp_erp_apr_appraisal_task.manager_review,
	srp_erp_apr_appraisal_task.employee_id,
	srp_erp_apr_appraisal_task.`completion`,
	srp_employeesdetails.Ename1,
	srp_erp_apr_corporate_goal.is_closed,
	srp_erp_apr_corporate_goal.from,
	srp_erp_apr_corporate_goal.to,
	srp_erp_apr_appraisal_task.is_approved_by_manager,
	srp_erp_apr_corporateobjectivemaster.description,
	srp_departmentmaster.hod_id as hod_id
FROM
	srp_erp_apr_appraisal_task
	JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_apr_appraisal_task.employee_id
	JOIN srp_erp_apr_department_appraisal_header ON srp_erp_apr_department_appraisal_header.id = srp_erp_apr_appraisal_task.appraisal_id
	JOIN srp_erp_apr_corporate_goal ON srp_erp_apr_corporate_goal.id = srp_erp_apr_department_appraisal_header.goal_id 
	JOIN srp_erp_apr_corporateobjectivemaster ON srp_erp_apr_corporateobjectivemaster.id = srp_erp_apr_appraisal_task.department_objective_id	
	JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID=$department_id
WHERE
	srp_erp_apr_appraisal_task.appraisal_id = $appraisal_header_id 
	AND srp_erp_apr_appraisal_task.employee_id = $employee_id");

            return $employee_wise_performance_query->result_array();
        } else {
            return null;
        }

    }

    public function get_sub_department_tasks($sub_department_id, $department_appraisal_id)
    {
        $query = $this->db->query("SELECT
	srp_erp_apr_appraisal_task.task_description,
	srp_erp_apr_appraisal_task.id AS task_id,
	srp_erp_apr_appraisal_task.weight,
	srp_erp_apr_appraisal_task.date_to_complete,
	srp_erp_apr_appraisal_task.manager_review,
	srp_erp_apr_appraisal_task.employee_id,
	srp_erp_apr_appraisal_task.`completion`,
	srp_employeesdetails.Ename1,
	srp_erp_apr_corporate_goal.is_closed,
	srp_erp_apr_appraisal_task.is_approved_by_manager,
	srp_erp_apr_corporateobjectivemaster.description
FROM
	srp_erp_apr_appraisal_task
	JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_apr_appraisal_task.employee_id 
	JOIN srp_erp_apr_department_appraisal_header ON srp_erp_apr_department_appraisal_header.id = srp_erp_apr_appraisal_task.appraisal_id
	JOIN srp_erp_apr_corporate_goal ON srp_erp_apr_corporate_goal.id = srp_erp_apr_department_appraisal_header.goal_id
	JOIN srp_erp_apr_corporateobjectivemaster ON srp_erp_apr_corporateobjectivemaster.id = srp_erp_apr_appraisal_task.department_objective_id
WHERE
	srp_erp_apr_appraisal_task.appraisal_sub_department_id = $sub_department_id AND srp_erp_apr_appraisal_task.appraisal_id=$department_appraisal_id");

        $data = array();
        foreach ($query->result() as $row) {
            $current_record = array();
            $current_record['task_description'] = $row->task_description;
            $current_record['task_id'] = $row->task_id;
            $current_record['weight'] = $row->weight;
            $current_record['date_to_complete'] = $row->date_to_complete;
            $current_record['manager_review'] = $row->manager_review;
            $current_record['employee_id'] = $row->employee_id;
            $current_record['completion'] = $row->completion;
            $current_record['Ename1'] = $row->Ename1;
            $current_record['is_closed'] = $row->is_closed;
            $current_record['is_approved_by_manager'] = $row->is_approved_by_manager;
            $current_record['objective_description'] = $row->description;
            $query2 = $this->db->query("SELECT * FROM srp_erp_apr_task_discussion WHERE srp_erp_apr_task_discussion.task_id=$row->task_id ORDER BY id DESC LIMIT 1");
            $message_row = $query2->row_array();
            $current_record['message'] = $message_row['message'] ?? '';

            //checking employee performance details.
            $performance_appraisal_header_query = $this->db->query("SELECT * FROM srp_erp_apr_employee_performance_appraisal_header 
WHERE department_appraisal_header_id=$department_appraisal_id AND emp_id=$row->employee_id");
            if ($performance_appraisal_header_query->num_rows() > 0) {
                $current_record['employee_performance_approved'] = $performance_appraisal_header_query->row()->is_approved;
            } else {
                $current_record['employee_performance_approved'] = 0;
            }

            array_push($data, $current_record);
        }

        return $data;
    }

    public function approve_employee_performance_report($goal_id, $department_id, $employee_id, $manager_comment, $suggested_reward, $identified_training_needs, $special_remarks_from_hod, $status)
    {
        $this->load->library('Approvals');
        $update_array = array(
            "is_approved" => $status,
            "manager_comment" => $manager_comment,
            "approved_by" => current_userID(),
            "approved_datetime" => current_date(true),
            "suggested_reward" => $suggested_reward,
            "identified_training_needs" => $identified_training_needs,
            "special_remarks_from_hod" => $special_remarks_from_hod,
            "confirmedYN" => 1,
            "confirmedDate" => current_date(true),
            "confirmedByEmpID" => current_userID()
        );
        $this->db->where('corporate_goal_id', $goal_id);
        $this->db->where('department_id', $department_id);
        $this->db->where('emp_id', $employee_id);
        $db_response = $this->db->update('srp_erp_apr_employee_performance_appraisal_header', $update_array);

        $query = $this->db->get_where('srp_erp_apr_employee_performance_appraisal_header', array(
                'corporate_goal_id' => $goal_id,
                'department_id' => $department_id,
                'emp_id' => $employee_id
            )
        );

        $row = $query->row();
        $document_id = $row->document_id;
        $appraisal_header_id = $row->id;
        //approval document
        if ($status == '1') {
            $is_document_exist_for_document_id = $this->is_document_exist_for_document_id($document_id);
//var_dump($is_document_exist_for_document_id);exit;
            if (!$is_document_exist_for_document_id) {
                $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification('APR-EWP', $appraisal_header_id, $document_id, 'Corporate Goal', 'srp_erp_apr_employee_performance_appraisal_header', 'id', 0, current_date(true));
                //var_dump($approvals_status);exit;
            }
        }

        if ($db_response == true) {
            $data['status'] = 'success';
        } else {
            $data['status'] = 'database error';
        }
        return $data;
    }

    public function get_sub_department_tasks_by_id($sub_department_task_id)
    {
        $query = $this->db->query("SELECT
	srp_erp_apr_appraisal_task.task_description,
	srp_erp_apr_appraisal_task.id AS task_id,
	srp_erp_apr_appraisal_task.weight,
	srp_erp_apr_appraisal_task.date_to_complete,
	srp_erp_apr_appraisal_task.manager_review,
	srp_erp_apr_appraisal_task.employee_id,
	srp_erp_apr_appraisal_task.`completion`,
	srp_erp_apr_appraisal_task.department_objective_id,
	srp_employeesdetails.Ename1 
FROM
	srp_erp_apr_appraisal_task
	JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_apr_appraisal_task.employee_id 
WHERE
	srp_erp_apr_appraisal_task.id = $sub_department_task_id");

        return $query->result();
    }

    public function manager_review_save($status, $task_id)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_appraisal_task` where is_approved_by_manager=1 AND id=$task_id");
        if ($query->num_rows() > 0) {
            $update_array = array(
                'manager_review' => $status
            );
            $this->db->where('id', $task_id);
            if (!$this->db->update('srp_erp_apr_appraisal_task', $update_array)) {
                $error = $this->db->error(); // Has keys 'code' and 'message'
                $data['status'] = 'db_update_error';
            } else {
                $data['status'] = 'success';
            }
        } else {
            $data['status'] = 'not_approved_by_manager';
        }
        return $data;
    }

    public function get_manager_feedbacks($corporate_goal_id, $department_id, $emp_id)
    {
        $query1 = $this->db->query("SELECT * FROM `srp_erp_apr_employee_performance_appraisal_header` WHERE corporate_goal_id=$corporate_goal_id AND department_id=$department_id AND emp_id=$emp_id");
        if ($query1->num_rows() > 0) {
            $row = $query1->row_array();
            $data['manager_comment'] = $row['manager_comment'];
            $data['suggested_reward'] = $row['suggested_reward'];
            $data['identified_training_needs'] = $row['identified_training_needs'];
            $data['special_remarks_from_hod'] = $row['special_remarks_from_hod'];
            $data['special_remarks_from_emp'] = $row['special_remarks_from_emp'];
            $data['is_confirmed_by_employee'] = $row['is_confirmed_by_employee'];
        } else {
            $data['manager_comment'] = "";
            $data['suggested_reward'] = "";
            $data['identified_training_needs'] = "";
            $data['special_remarks_from_hod'] = "";
            $data['special_remarks_from_emp'] = "";
            $data['is_confirmed_by_employee'] = 0;
        }
        return $data;
    }

    
    public function get_appraisal_wise_employee_tasks($user_id)
    {
//        $query1 = $this->db->query("SELECT DISTINCT(srp_erp_apr_appraisal_task.appraisal_id) FROM `srp_erp_apr_appraisal_task`
//JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_apr_appraisal_task.employee_id
//WHERE srp_erp_apr_appraisal_task.employee_id=$user_id");

        $query1 = $this->db->query("SELECT DISTINCT(srp_erp_apr_department_appraisal_header.goal_id) FROM `srp_erp_apr_appraisal_task`
JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_apr_appraisal_task.employee_id
join srp_erp_apr_department_appraisal_header on srp_erp_apr_department_appraisal_header.id = srp_erp_apr_appraisal_task.appraisal_id 
WHERE srp_erp_apr_appraisal_task.employee_id=$user_id ");//group by srp_erp_apr_department_appraisal_header.goal_id

        $response = array();
        foreach ($query1->result() as $row1) {
            $gid = $row1->goal_id;
            $q2 = $this->db->query("SELECT DISTINCT(srp_erp_apr_appraisal_task.appraisal_id)
FROM
	`srp_erp_apr_appraisal_task`
	JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_apr_appraisal_task.employee_id
	JOIN srp_erp_apr_department_appraisal_header ON srp_erp_apr_department_appraisal_header.id = srp_erp_apr_appraisal_task.appraisal_id 
WHERE
	srp_erp_apr_department_appraisal_header.goal_id = $gid");

            //var_dump($this->db->last_query());exit;
            $element = array();
            $element['data'] = array();
            foreach ($q2->result() as $r2) {
                $appraisal_id = $r2->appraisal_id;
                $query = $this->db->query("SELECT
srp_erp_apr_corporate_goal.modified_by as goal_last_modified_by,
srp_erp_apr_corporate_goal.modified_at as goal_last_modified_at,
    srp_erp_apr_corporate_goal.appraisal_type,
	srp_erp_apr_corporate_goal.is_closed,
	srp_erp_apr_corporate_goal.id AS goal_id,
	srp_erp_apr_corporate_goal.narration,
	srp_erp_apr_corporate_goal.from as from_date,
	srp_erp_apr_corporate_goal.to as to_date,
	srp_erp_apr_corporate_goal.document_id as document_id,
	srp_erp_apr_corporate_goal.approvedDate,
	srp_erp_apr_appraisal_task.*,
	srp_employeesdetails.Ename1 ,
	srp_erp_apr_department_appraisal_header.id as appraisal_header_id,
	srp_erp_apr_department_appraisal_header.department_id
FROM
	`srp_erp_apr_appraisal_task`
	JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_apr_appraisal_task.employee_id
	JOIN srp_erp_apr_department_appraisal_header ON srp_erp_apr_department_appraisal_header.id = srp_erp_apr_appraisal_task.appraisal_id
	JOIN srp_erp_apr_corporate_goal ON srp_erp_apr_department_appraisal_header.goal_id = srp_erp_apr_corporate_goal.id 
WHERE
	srp_erp_apr_appraisal_task.employee_id = $user_id 
	AND srp_erp_apr_appraisal_task.appraisal_id = $appraisal_id");

                $data = array();
                foreach ($query->result() as $row) {

                    $element['name'] = $row->narration;
                    $element['goal_id'] = $row->goal_id;
                    $element['appraisal_header_id'] = $row->appraisal_header_id;
                    $element['appraisal_sub_department_id'] = $row->appraisal_sub_department_id;
                    $element['department_id'] = $row->department_id;
                    $from_date = date_create($row->from_date);
                    $element['from_date'] = date_format($from_date, "Y-m-d");
                    $to_date = date_create($row->to_date);
                    $element['to_date'] = date_format($to_date, "Y-m-d");
                    $element['is_closed'] = $row->is_closed;
                    $element['appraisal_type'] = $row->appraisal_type;
                    $element['document_id'] = $row->document_id;
                    $element['approvedDate'] = $row->approvedDate;


                    //loading manager feedbacks
                    $manager_feedbacks = $this->get_manager_feedbacks($element['goal_id'], $element['department_id'], $user_id);
                    $element['manager_comment'] = $manager_feedbacks['manager_comment'];
                    $element['suggested_reward'] = $manager_feedbacks['suggested_reward'];
                    $element['identified_training_needs'] = $manager_feedbacks['identified_training_needs'];
                    $element['special_remarks_from_hod'] = $manager_feedbacks['special_remarks_from_hod'];
                    $element['special_remarks_from_emp'] = $manager_feedbacks['special_remarks_from_emp'];
                    $element['is_confirmed_by_employee'] = (int)$manager_feedbacks['is_confirmed_by_employee'];
                    $closed_by_id = $row->goal_last_modified_by;
                    $closed_by_name = $this->db->query("select * from srp_employeesdetails where EIdNo=$closed_by_id")->row()->Ename1;
                    $element['closed_by'] = $closed_by_name;
                    $date = date_create($row->goal_last_modified_at);
                    $element['closed_at'] = date_format($date, "Y-m-d");


                    $current_record = array();
                    $current_record['id'] = $row->id;
                    $current_record['task_description'] = $row->task_description;
                    $current_record['weight'] = $row->weight;
                    $current_record['employee_id'] = $row->employee_id;
                    $date = date_create($row->date_to_complete);
                    $current_record['date_to_complete'] = date_format($date, "Y-m-d");
                    $current_record['appraisal_sub_department_id'] = $row->appraisal_sub_department_id;
                    $current_record['appraisal_sub_department'] = $this->get_subdepartment_by_id($row->appraisal_sub_department_id)->description;
                    $current_record['department'] = $this->get_department_details_by_id($row->department_id)->DepartmentDes;
                    $current_record['completion'] = $row->completion;
                    $current_record['manager_review'] = $row->manager_review;
                    $current_record['department_objective_id'] = $row->department_objective_id;
                    $current_record['appraisal_id'] = $row->appraisal_id;
                    $current_record['Ename1'] = $row->Ename1;
                    $current_record['is_closed'] = $row->is_closed;

                    $current_record['is_approved_by_manager'] = $row->is_approved_by_manager;
                    $query2 = $this->db->query("SELECT * FROM srp_erp_apr_task_discussion WHERE srp_erp_apr_task_discussion.task_id=$row->id ORDER BY id DESC LIMIT 1");
                    $message_row = $query2->row_array();
                    $current_record['message'] = $message_row['message'];

                    //checking employee performance details.
                    $performance_appraisal_header_query = $this->db->query("SELECT * FROM srp_erp_apr_employee_performance_appraisal_header 
WHERE department_appraisal_header_id=$appraisal_id AND emp_id=$row->employee_id");
                    if ($performance_appraisal_header_query->num_rows() > 0) {
                        $current_record['employee_performance_approved'] = $performance_appraisal_header_query->row()->is_approved;
                        $element['employee_performance_approved'] = $performance_appraisal_header_query->row()->is_approved;
                    } else {
                        $current_record['employee_performance_approved'] = 0;
                        $element['employee_performance_approved'] = 0;
                    }


                    array_push($element['data'], $current_record);

                }


            }
            array_push($response, $element);

//            if (sizeof($data) > 0) {
//                array_push($response, $data);
//            }
            //var_dump($element['name']);

        }
//exit;
//        echo json_encode($response);exit;
        //this is for handdle appeaisals only with softskills performance component.
        $except_ids_ar = array();
        foreach ($query1->result() as $row1) {
            $except_id = $row1->goal_id;
            array_push($except_ids_ar, $except_id);
        }


        $except_ids_str = implode(", ", $except_ids_ar);
//        var_dump($except_ids_str);exit;
        if ($except_ids_str == "") {
            $not_in_statement = "";
        } else {
            $not_in_statement = "WHERE srp_erp_apr_emp_softskills_template_mapping.goal_id NOT IN ( " . $except_ids_str . " )";
        }
        $query = $this->db->query("SELECT
srp_erp_apr_corporate_goal.modified_by as goal_last_modified_by,
srp_erp_apr_corporate_goal.modified_at as goal_last_modified_at,
	srp_erp_apr_emp_softskills_template_mapping.id AS template_mapping_id ,
	srp_erp_apr_corporate_goal.id AS goal_id,
	srp_erp_apr_corporate_goal.is_closed,
	srp_erp_apr_corporate_goal.from as from_date,
	srp_erp_apr_corporate_goal.to as to_date,
	srp_erp_apr_corporate_goal.document_id as document_id,
	srp_erp_apr_corporate_goal.narration AS narration,
	srp_erp_apr_corporate_goal.appraisal_type as appraisal_type,
	srp_erp_apr_corporate_goal.approvedDate
FROM
	srp_erp_apr_emp_softskills_template_mapping
	JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_apr_emp_softskills_template_mapping.emp_id
	JOIN srp_erp_apr_corporate_goal ON srp_erp_apr_corporate_goal.id = srp_erp_apr_emp_softskills_template_mapping.goal_id 
 $not_in_statement GROUP BY goal_id");
        //var_dump($this->db->last_query());exit;

        foreach ($query->result() as $row) {
            $element = array();
            $data = array();
            $element['name'] = $row->narration;
            $element['goal_id'] = $row->goal_id;
            $element['appraisal_header_id'] = "";
            $element['appraisal_sub_department_id'] = "";
            $element['department_id'] = "";
            $from_date = date_create($row->from_date);
            $element['from_date'] = date_format($from_date, "Y-m-d");
            $to_date = date_create($row->to_date);
            $element['to_date'] = date_format($to_date, "Y-m-d");
            $element['is_closed'] = $row->is_closed;
            $element['appraisal_type'] = $row->appraisal_type;
            $element['document_id'] = $row->document_id;
            $element['manager_comment'] = "";
            $element['suggested_reward'] = "";
            $element['identified_training_needs'] = "";
            $element['special_remarks_from_hod'] = "";
            $element['special_remarks_from_emp'] = "";
            $element['is_confirmed_by_employee'] = "";
            $element['data'] = null;
            $closed_by_id = $row->goal_last_modified_by;
            $closed_by_name = $this->db->query("select * from srp_employeesdetails where EIdNo=$closed_by_id")->row()->Ename1;
            $element['closed_by'] = $closed_by_name;
            $date = date_create($row->goal_last_modified_at);
            $element['closed_at'] = date_format($date, "Y-m-d");
            $element['approvedDate'] = $row->approvedDate;
            array_push($response, $element);
            //array_push($response, $data);
//            var_dump($element); exit;
        }

        //array_push($response, $data);
       // echo '<pre>';print_r($response);exit;
        return $response;
    }

    public function map_employee_skills_performance_appraisal($config_goal_id)
    {
        $softskills_designation_policy = softskills_designation_policy();
        if ($softskills_designation_policy == '1') {
            $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($config_goal_id);
            $softskills_template_id = $this->db->query("select softskills_template_id from srp_erp_apr_corporate_goal where id=$config_goal_id")->row()->softskills_template_id;
            $designations = $this->db->query("select DesignationID from srp_erp_apr_softskillstemplatedesignations where softskillTemplateID=$softskills_template_id")->result();
            foreach ($designations as $row) {
                $DesignationID = $row->DesignationID;
                $query = $this->db->query("select srp_employeesdetails.EIdNo from srp_employeesdetails
join srp_employeedesignation on srp_employeedesignation.EmpID=srp_employeesdetails.EIdNo
where srp_employeedesignation.DesignationID=$DesignationID
and srp_employeesdetails.isDeleted!=1
and srp_employeesdetails.isDischarged!=1");
                foreach ($query->result() as $emp) {
                    $this->appraisal_model->fetch_employee_skills_performance_appraisal($emp->EIdNo, $goal_details_and_objectives);
                }
            }
        } else {
            $current_companyID = current_companyID();
            $query = $this->db->query("SELECT * FROM `srp_employeesdetails` where Erp_companyID=$current_companyID and isDeleted!=1 and isDischarged!=1");
            foreach ($query->result() as $emp) {
                $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($config_goal_id);
                $this->appraisal_model->fetch_employee_skills_performance_appraisal($emp->EIdNo, $goal_details_and_objectives);
            }
        }
    }

    public function get_employee_tasks($user_id)
    {
        $query = $this->db->query("SELECT srp_erp_apr_appraisal_task.*,srp_employeesdetails.Ename1 FROM `srp_erp_apr_appraisal_task`
JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo=srp_erp_apr_appraisal_task.employee_id
WHERE srp_erp_apr_appraisal_task.employee_id=$user_id");
        $data = array();
        foreach ($query->result() as $row) {
            $current_record = array();
            $current_record['id'] = $row->id;
            $current_record['task_description'] = $row->task_description;
            $current_record['weight'] = $row->weight;
            $current_record['employee_id'] = $row->employee_id;
            $current_record['date_to_complete'] = $row->date_to_complete;
            $current_record['appraisal_sub_department_id'] = $row->appraisal_sub_department_id;
            $current_record['completion'] = $row->completion;
            $current_record['manager_review'] = $row->manager_review;
            $current_record['department_objective_id'] = $row->department_objective_id;
            $current_record['appraisal_id'] = $row->appraisal_id;
            $current_record['Ename1'] = $row->Ename1;
            $query2 = $this->db->query("SELECT * FROM srp_erp_apr_task_discussion WHERE srp_erp_apr_task_discussion.task_id=$row->id ORDER BY id DESC LIMIT 1");
            $message_row = $query2->row_array();
            $current_record['message'] = $message_row['message'];
            array_push($data, $current_record);
        }
        return $data;
    }

    public function save_task_progress($task_progress, $task_id)
    {
        $update_array = array(
            'completion' => $task_progress
        );
        $this->db->where('id', $task_id);
        $this->db->update('srp_erp_apr_appraisal_task', $update_array);
    }

    public function load_appraisal_task_discussion($task_id)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_apr_task_discussion WHERE srp_erp_apr_task_discussion.task_id=$task_id");
        return $query->result();
    }

    public function send_message($task_id, $message)
    {
        $insert_array = array(
            'task_id' => $task_id,
            'message' => $message,
            'user_id' => current_userID(),
            'datetime' => current_date(true),
            'uniqid' => uniqid(),
            "company_id" => current_companyID()
        );
        $this->db->insert('srp_erp_apr_task_discussion', $insert_array);
    }

    function approve_document($system_code, $level_id, $status, $comments, $documentCode)
    {
        $this->db->select('documentCode,approvedYN');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentID', $documentCode);
        $this->db->where('documentSystemCode', $system_code);
        $this->db->where('approvedYN', 2);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $approval_data = $this->db->get()->row_array();

        if (!empty($approval_data)) {
            $this->session->set_flashdata('w', $documentCode . 'Approval : ' . $approval_data['documentCode'] . ' This ' . $documentCode . ' has been rejected already! You cannot do approval for this..');
            return 3;
        } else {
            if ($level_id > 1) {
                $previousLevel = $level_id - 1;
                $isLast_where = array('documentID' => $documentCode, 'documentSystemCode' => $system_code, 'approvalLevelID' => $previousLevel);
                $this->db->select('approvedYN');
                $this->db->from('srp_erp_documentapproved');
                $this->db->where($isLast_where);
                $isLastLevelApproved = $this->db->get()->row_array();
                if ($isLastLevelApproved['approvedYN'] == 1) {
                    if ($status == 1) {

                        return $this->approve($system_code, $level_id, $status, $comments, $documentCode);
                    } elseif ($status == 2) {
                        return $this->reject($system_code, $level_id, $comments, $documentCode);
                    }

                } else {
                    $this->session->set_flashdata('w', $documentCode . ' `s Previous level Approval not Finished.');
                    return 5;
                }
            } else {
                if ($status == 1) {

                    return $this->approve($system_code, $level_id, $status, $comments, $documentCode);
                } elseif ($status == 2) {
                    return $this->reject($system_code, $level_id, $comments, $documentCode);
                }
            }
        }
    }

    public function confirmation_referback_corporate_goal($goal_id)
    {
        $query = $this->db->get_where('srp_erp_apr_corporate_goal', array("id" => $goal_id));
        $row = $query->row();
        if ($row->approvedYN == "1") {
            return 'already_approved';
        } else if ($row->approvedYN == "0") {
            $update_record = array(
                "confirmedYN" => 2
            );
            $this->db->where('id', $goal_id);
            $this->db->update('srp_erp_apr_corporate_goal', $update_record);
            $this->db->query("DELETE FROM srp_erp_documentapproved where documentID='CG' AND documentSystemCode='$goal_id'");
            return 'success';
        }

    }

    function reject($system_code, $level_id, $comments, $documentCode)
    {
        $this->db->trans_start();
        $data = $this->details($system_code, $documentCode);
        $rejectData = array(
            'documentID' => $data['documentID'],
            'systemID' => $system_code,
            'documentCode' => $data['documentCode'],
            'comment' => $comments,
            'rejectedLevel' => $level_id,
            'rejectByEmpID' => $this->common_data['current_userID'],
            'table_name' => $data['table_name'],
            'table_unique_field' => $data['table_unique_field_name'],
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdDateTime' => $this->common_data['current_date']
        );

        $this->db->insert('srp_erp_approvalreject', $rejectData);

        $this->db->trans_commit();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', $data['documentCode'] . ' Error In Approval Reject Process.');
            return 'e';
        } else {

            $delete_data = $this->approve_delete($system_code, $documentCode, false);

            if ($delete_data == 1) {
                // $this->emailRejectAlert($data['table_name'], $data['table_unique_field_name'], $system_code, $data['documentCode'], $documentCode);
                $this->session->set_flashdata('s', $data['documentCode'] . ' Approvals  Reject Process Successfully done.');
                return 3;
            } else {
                $this->session->set_flashdata('e', $data['documentCode'] . ' Approvals  Reject Process Failed.');
                return $delete_data;
            }
        }

    }

    function approve_delete($system_code, $documentCode, $status = true)
    {
        $this->db->trans_start();

        $data = $this->details($system_code, $documentCode);

        if ($status) {
            $confirmedYN = 3;
        } else {
            $confirmedYN = 2;
        }

        if (!empty($data)) {
            $where = array('documentID' => $documentCode, 'documentSystemCode' => $system_code);
            $this->db->where($where)->delete('srp_erp_documentapproved');

            if (!empty($data['table_unique_field_name']) && !empty($data['table_name'])) {
                $dataUpdate = array(
                    'confirmedYN' => $confirmedYN,
                    'confirmedByEmpID' => '',
                    'confirmedDate' => '',
                    'currentLevelNo' => 1
                );

                if (!in_array($documentCode, ['VD'])) {
                    $dataUpdate['confirmedByName'] = '';
                }

                $this->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
                $this->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

                if ($documentCode == 'FS') { /*If final settlement*/
                    $empID = $this->db->get_where('srp_erp_pay_finalsettlementmaster', ['masterID' => $system_code])->row('empID');
                    $upData = ['finalSettlementDoneYN' => 0, 'ModifiedPC' => current_pc(), 'ModifiedUserName' => current_employee(), 'Timestamp' => current_date()];
                    $this->db->where(['EIdNo' => $empID])->update('srp_employeesdetails', $upData);
                }

                $this->db->trans_commit();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', $data['documentCode'] . ' Error In Referred Back.');
                    return 'e';
                } else {
                    $this->session->set_flashdata('s', $data['documentCode'] . ' Referred Back Successfully.');
                    return 1;
                }
            } else {
                $this->db->trans_commit();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', $data['documentCode'] . ' Error In Referred Back.');
                    return 'e';
                } else {
                    $this->session->set_flashdata('s', $data['documentCode'] . ' Approvals Deleted Successfully.');
                    return 3;
                }
            }
        } else {
            $this->session->set_flashdata('e', $data['documentCode'] . ' Error In Referred Back.');
            return 'e1';
        }

    }

    function approve($system_code, $level_id, $status, $comments, $documentCode)
    {
        $maxlevel = $this->maxlevel($documentCode);
        $maxlevelNo = $maxlevel['levelNo'];
//var_dump($maxlevel);exit;
        $this->db->trans_start();

        $data = array(
            'approvedYN' => $status,
            'approvedEmpID' => current_userID(),
            'approvedComments' => $comments,
            'approvedDate' => $this->common_data['current_date'],
            'approvedPC' => $this->common_data['current_pc']
        );

        $this->db->where('documentSystemCode', $system_code);
        $this->db->where('documentID', $documentCode);
        $this->db->where('approvalLevelID', $level_id);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->update('srp_erp_documentapproved', $data);
        $data = $this->details($system_code, $documentCode);

        /* write my alert table*/
        $policy = getPolicyValues('SEN', 'All');
//        if ($policy == 1 || $policy == null) {
//            $this->emailAlert($documentCode, $level_id + 1, $system_code, $data['documentCode']);
//            if ($maxlevelNo == $level_id) {
//                $this->emailfinalAlert($data['table_name'], $data['table_unique_field_name'], $system_code, $data['documentCode'], $documentCode);
//            }
//        }
        /**/
        if ($data['approvedYN'] == 1) {
            if (!empty($data['table_unique_field_name']) && !empty($data['table_name'])) {
                $dataUpdate = array(
                    'approvedYN' => '1',
                    'approvedDate' => $this->common_data['current_date'],
                    'approvedbyEmpID' => $this->common_data['current_userID'],
                    'approvedComments' => $comments
                );

                if (!in_array($documentCode, ['VD'])) {
                    $dataUpdate['approvedbyEmpName'] = $this->common_data['current_user'];
                }

                $this->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
                $this->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', $data['documentCode'] . ' Error In Approval.');
                    return 'e';
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('s', $data['documentCode'] . ' Approved Successfully.');
                    $this->map_employee_skills_performance_appraisal($system_code);//$system_code=goal id
                    return 1;
                }

            } else {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', $data['documentCode'] . ' Error In Approval.');
                    return 'e';
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('s', $data['documentCode'] . ' Approved Successfully.');
                    $this->map_employee_skills_performance_appraisal($system_code);//$system_code=goal id
                    return 3;
                }
            }
        } else {
            /*update current level in master record*/
            $dataUpdate = array(
                'currentLevelNo' => $level_id + 1,
            );
            $this->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
            $this->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return 'e';
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', $data['documentCode'] . ' Approved Successfully.');
                $this->map_employee_skills_performance_appraisal($system_code);//$system_code=goal id
                return 2;
            }
        }

    }


    function details($system_code, $documentCode)
    {
        $this->db->select('documentID, documentCode, table_name, table_unique_field_name, approvedYN');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentSystemCode', $system_code);
        $this->db->where('documentID', $documentCode);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->order_by('approvalLevelID', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
//        $this->db->get()->row_array();
//        var_dump($this->db->last_query());exit;
    }

    function maxlevel($document)
    {
        $this->db->select_max('levelNo');
        $this->db->where('Status', 1);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('documentID', $document);
        $this->db->from('srp_erp_approvalusers');
        return $this->db->get()->row_array();
    }

    public function overall_allocated_percentage($goal_id, $department_master_id)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_corporate_goal_objective_mapping` WHERE corporate_goal_id=$goal_id AND assigned_department_id=$department_master_id");
        $total_allocated_weight = 0;
        foreach ($query->result() as $row) {
            $total_allocated_weight += $row->weight;
        }
        return $total_allocated_weight;
    }

    public function get_department_color_code($department_id)
    {
        $query = $this->db->query("SELECT color FROM srp_erp_apr_department_color_code WHERE department_id=$department_id");
        $row = $query->row_array();
        return $row['color'];
    }

    public function close_corporate_goal($goal_id)
    {
        $update_array = array(
            'id' => $goal_id,
            'is_closed' => 1,
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true)
        );
        $this->db->where('id', $goal_id);
        $this->db->update('srp_erp_apr_corporate_goal', $update_array);
    }

    public function get_goal_closed_status($goal_id)
    {
        $query = $this->db->get_where('srp_erp_apr_corporate_goal', array('id' => $goal_id));
        return $query->row()->is_closed;
    }

    public function get_department_objectives($assigned_department_id, $corporate_goal_id)
    {

        $query = $this->db->query("SELECT
	srp_erp_apr_corporateobjectivemaster.description,
	srp_erp_apr_corporateobjectivemaster.id AS objective_id 
FROM
	`srp_erp_apr_corporate_goal_objective_mapping`
	JOIN srp_erp_apr_corporateobjectivemaster ON srp_erp_apr_corporateobjectivemaster.id = srp_erp_apr_corporate_goal_objective_mapping.corporate_objective_id 
WHERE
	srp_erp_apr_corporate_goal_objective_mapping.assigned_department_id = $assigned_department_id 
	AND srp_erp_apr_corporate_goal_objective_mapping.corporate_goal_id = $corporate_goal_id");
        return $query->result_array();
    }

    public function change_task_approval_status($task_id, $status)
    {
        $record = array(
            "is_approved_by_manager" => $status,
            'modified_pc' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            'modified_by' => current_userID(),
            'modified_at' => current_date(true)
        );
        $this->db->where('id', $task_id);
        $this->db->update('srp_erp_apr_appraisal_task', $record);

    }

    public function get_employee_details($emp_id)
    {
        $query = $this->db->query("SELECT	
	srp_employeesdetails.ssoNo,
	srp_employeesdetails.Ename1,
	srp_employeesdetails.manPowerNo,
	srp_designation.DesDescription		
FROM
	`srp_employeesdetails`
	JOIN srp_designation ON srp_designation.DesignationID = srp_employeesdetails.EmpDesignationId 
WHERE
	srp_employeesdetails.EIdNo = $emp_id");
        return $query->result_array();
    }

    public function get_department_details_by_id($department_id)
    {
        $query = $this->db->query("SELECT * FROM srp_departmentmaster WHERE DepartmentMasterID=$department_id");
        $x = $query->row();
        return $x;
    }

    public function empwise_performance_remarks($goal_id, $department_id, $employee_id)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_employee_performance_appraisal_header` WHERE corporate_goal_id=$goal_id AND department_id=$department_id AND emp_id=$employee_id");
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    public function skill_performance_remarks($employee_id, $softskills_template_id, $company_id, $goal_id)
    {
        $query = $this->db->query("SELECT * FROM `srp_erp_apr_emp_softskills_template_mapping` where emp_id=$employee_id and softskills_template_id=$softskills_template_id and company_id=$company_id and goal_id=$goal_id");
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function get_subdepartment_by_id($subdepartment_id)
    {
        return $this->db->query("select * from srp_erp_apr_subdepartment where id=$subdepartment_id")->row();
    }

    function insert_sub_performance_area($template_id, $parent_id, $performance_area, $order)
    {
        $record = array(
            "performance_area" => $performance_area,
            "softskills_template_id" => $template_id,
            "parent_id" => $parent_id,
            "order" => $order,
            "company_id" => current_companyID()
        );
        $this->db->insert('srp_erp_apr_softskills_performance_area', $record);
        $data['status'] = 'success';
        $data['message'] = $this->lang->line('appraisal_successfully_saved');//'Successfully added sub performance area';
        return $data;
    }

    function update_sub_performance_area($performance_area_id, $performance_area,$order)
    {
        $record = array(
            "performance_area" => $performance_area,

            "order" => $order,

        );

        $this->db->where('id',$performance_area_id);
        $this->db->update('srp_erp_apr_softskills_performance_area', $record);
        $data['status'] = 'success';
        $data['message'] = $this->lang->line('appraisal_successfully_modified');
        return $data;
    }

    function save_measurepoint(){
        $id = trim($this->input->post('id') ?? '');
        $softskills_template_id = $this->input->post('template_id');

        $record = array(
            "measuredPoints" => $this->input->post('value'),
        );
        $this->db->where('id', $id);
        $this->db->update('srp_erp_apr_softskills_performance_area', $record);

        return array('s', 'Saved measured point');
    }

    function save_measurepointText(){
        $id = trim($this->input->post('id') ?? '');
        $softskills_template_id = $this->input->post('template_id');

        $record = array(
            "measuredPointsText" => $this->input->post('value'),
        );
        $this->db->where('id', $id);
        $this->db->update('srp_erp_apr_softskills_performance_area', $record);

        return array('s', 'Saved Text Answer');
    }

    function save_manager_measurepoint($goal_id){
        $performance_area_item_id = trim($this->input->post('id') ?? '');
        $softskills_template_id = $this->input->post('template_id');
        $emp_id = $this->input->post('emp_id');

        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id;
        $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");
        $res = $query->result();
        $emp_template_mapping_id = $res[0]->id;

        $record = array(
            "managerPoints" => $this->input->post('value'),
            "modified_at" => current_date(true)
        );
        $this->db->where('performance_area_item_id', $performance_area_item_id);
        $this->db->where('emp_template_mapping_id', $emp_template_mapping_id);
        $this->db->update('srp_erp_apr_emp_softskills_score', $record);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Saved manager measured point');
        } else {
            return array('e', 'Failed to update manager measured point');
        }
    }


    public function save_emp_softskills_managerPoints($performance_id, $emp_id, $goal_id, $grade_id)
    {
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id;
        $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");

        $data = array();
        if ($query->num_rows() > 0) {
            $res = $query->result();
            $template_mapping_id = $res[0]->id;
        
            $isexist = $this->db->query("SELECT id FROM srp_erp_apr_emp_softskills_score WHERE emp_template_mapping_id = $template_mapping_id AND performance_area_item_id = $performance_id ")->row_array();

            if(empty($isexist)){
                $insert_array = array(
                    "emp_template_mapping_id" => $template_mapping_id,
                    "performance_area_item_id" => $performance_id,
                    "managerPoints" => $grade_id,
                    "modified_at" => current_date(true)
                );
                $this->db->insert('srp_erp_apr_emp_softskills_score', $insert_array);
            }else{
                $update_array = array(
                    "managerPoints" => $grade_id,
                    "modified_at" => current_date(true)
                );
                $this->db->where('emp_template_mapping_id', $template_mapping_id);
                $this->db->where('performance_area_item_id', $performance_id);
                $this->db->update('srp_erp_apr_emp_softskills_score', $update_array);
                //var_dump($this->db->last_query());exit;
            }

            //updating total after modifying score data.
            $total = $this->appraisal_model->update_total_of_emp_performance($template_mapping_id);

            $data['status'] = 'success';
            $data['message'] = 'Changes have been saved.';
            $data['total'] = $total;
            echo json_encode($data);
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Failed to update.';
            echo json_encode($data);
        }
    }

    public function save_emp_softskills_empPoints($performance_id, $emp_id, $goal_id, $grade_id)
    {
        $goal_details_and_objectives = $this->appraisal_model->get_corporate_goal_details($goal_id);
        $performance_template_id = $goal_details_and_objectives['goal_details'][0]->softskills_template_id;
        $query = $this->db->query("SELECT * FROM srp_erp_apr_emp_softskills_template_mapping WHERE softskills_template_id=$performance_template_id AND emp_id=$emp_id AND goal_id=$goal_id");

        $data = array();
        if ($query->num_rows() > 0) {
            $res = $query->result();
            $template_mapping_id = $res[0]->id;
        
            $isexist = $this->db->query("SELECT id FROM srp_erp_apr_emp_softskills_score_self_eval WHERE emp_template_mapping_id = $template_mapping_id AND performance_area_item_id = $performance_id ")->row_array();

            if(empty($isexist)){
                $insert_array = array(
                    "emp_template_mapping_id" => $template_mapping_id,
                    "performance_area_item_id" => $performance_id,
                    "employeePoints" => $grade_id,
                    "modified_at" => current_date(true)
                );
                $this->db->insert('srp_erp_apr_emp_softskills_score_self_eval', $insert_array);
            }else{
                $update_array = array(
                    "employeePoints" => $grade_id,
                    "modified_at" => current_date(true)
                );
                $this->db->where('emp_template_mapping_id', $template_mapping_id);
                $this->db->where('performance_area_item_id', $performance_id);
                $this->db->update('srp_erp_apr_emp_softskills_score_self_eval', $update_array);
            }

            //updating total after modifying score data.
            $total = $this->appraisal_model->update_total_of_emp_performance($template_mapping_id);

            $data['status'] = 'success';
            $data['message'] = 'Changes have been saved.';
            $data['total'] = $total;
            echo json_encode($data);
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Failed to update.';
            echo json_encode($data);
        }
    }

    public function update_job_purpose(){
        $companyID = current_companyID();
        $job_purpose = $this->input->post('job_purpose');
        $id = $this->input->post('template_id');

        $data['job_purpose'] = $job_purpose;
        $data['company_id'] = $companyID;

        $this->db->where('id', $id);
        $this->db->update('srp_erp_apr_softskills_master', $data);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Purpose updated successfully.');
        } else {
            return array('e', 'Failed to update the purpose');
        }
    }
}
