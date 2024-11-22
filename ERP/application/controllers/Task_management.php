<?php

class Task_management extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Task_management_model');
        $this->load->helper('task_helper');
        $this->load->library('Pagination');
        $this->load->library('email_manual');


        $this->load->library('s3');

        ini_set('max_execution_time', 360);
        ini_set('memory_limit', '2048M');
    }

    
    function save_task_header() {
        $assignemployees = $this->input->post('employees');
    
        $this->form_validation->set_rules('subject', 'subject', 'trim|required');
        $this->form_validation->set_rules('categoryID', 'Category', 'trim|required');
        $this->form_validation->set_rules('priority', 'Priority', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('statusID', 'Status', 'trim|required');
        $this->form_validation->set_rules('startdate', 'Start Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('duedate', 'Due Date', 'trim|required|validate_date');

        // Custom validation for employees array
        if (empty($assignemployees)) {
            $this->form_validation->set_rules('employees', 'Assignee', 'required', array('required' => 'Assignee Required'));
        }
    
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $startdate = strtotime($this->input->post('startdate'));
            $end_date = strtotime($this->input->post('duedate'));
    
            if ($end_date >= $startdate) {
                echo json_encode($this->Task_management_model->save_task_header());
            } else {
                echo json_encode(array('e', 'Due Date should be greater than Start Date'));
            }
        }
    }
    
    function history_details_save()
    {
        echo json_encode($this->Task_management_model->save_history_details());
    }


    

    function fetch_tasks_employee_detail()
    {
        echo json_encode($this->Task_management_model->fetch_tasks_employee_detail());
    }

    function load_task_header()
    {
        echo json_encode($this->Task_management_model->load_task_header());
    }

    function delete_task_task()
    {
        echo json_encode($this->Task_management_model->delete_task());
    }

    function fetch_document_relate_search()
    {
        echo json_encode($this->Task_management_model->fetch_document_relate_search());
    }

    
    function load_task_multiple_attachemts()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $documentAutoID = trim($this->input->post('taskID') ?? '');

        $where = " documentID = 2 AND documentAutoID = " . $documentAutoID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_task_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/task_managment/load_all_attachments', $data);
    }

    
    function attachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_task_attachments')->result_array();


            $fileName = current_companyCode() . '_' . $this->input->post('document_name') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $file = $_FILES['document_file'];
            if ($file['error'] == 1) {
                die(json_encode(['status' => 0, 'type' => 'e','message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']));
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if (!in_array($ext, $allowed_types)) {
                die(json_encode(['status' => 0, 'type' => 'e','message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if ($size > 5) {
                die(json_encode(['status' => 0, 'type' => 'e','message' =>"The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
            }

            $path = "attachments/CRM/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die(json_encode(['status' => 0, 'type' => 'e','message' =>'Error in document upload location configuration']));
            }

            $this->db->trans_start();
                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                $data['myFileName'] = $path;
                $data['fileType'] = trim($ext);
            ;
                $data['fileSize'] = trim($file["size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_task_attachments', $data);
                $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully uploaded.'));
            }
        }
    }

    
    function attachement_upload_ss()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_task_attachments')->result_array();


            $fileName = current_companyCode() . '_' . $this->input->post('document_name') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $file = $_FILES['document_file'];
            if ($file['error'] == 1) {
                die(json_encode(['status' => 0, 'type' => 'e','message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']));
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if (!in_array($ext, $allowed_types)) {
                die(json_encode(['status' => 0, 'type' => 'e','message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if ($size > 5) {
                die(json_encode(['status' => 0, 'type' => 'e','message' =>"The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
            }

            $path = "attachments/CRM/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die(json_encode(['status' => 0, 'type' => 'e','message' =>'Error in document upload location configuration']));
            }

            $this->db->trans_start();
                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                $data['myFileName'] = $path;
                $data['fileType'] = trim($ext);
            ;
                $data['fileSize'] = trim($file["size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_task_attachments', $data);
                $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $fileName . ' uploaded.'));
            }
        }
    }

    
    function delete_crm_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $result = $this->s3->delete($myFileName);
        /** end of AWS s3 delete object */
        if ($result) {
            $this->db->delete('srp_erp_task_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    function load_taskRelated_fromLead()
    {
        echo json_encode($this->Task_management_model->load_taskRelated_fromLead());
    }

    function load_taskRelated_fromOpportunity()
    {
        echo json_encode($this->Task_management_model->load_taskRelated_fromOpportunity());
    }

    function load_taskRelated_fromProject()
    {
        echo json_encode($this->Task_management_model->load_taskRelated_fromProject());
    }

    
    function AddSubTaskDetail()
    {
        $Taskdescription = $this->input->post('Taskdescription');
        $companyid = current_companyID();
        $taskid = $this->input->post('Taskid');

        $date_format_policy = date_format_policy();
        foreach ($Taskdescription as $key => $val) {
            $this->form_validation->set_rules("Taskdescription[{$key}]", 'Task Description', 'trim|required');
            $this->form_validation->set_rules("estsubtaskdate[{$key}]", 'Est.Start Date', 'trim|required');
            $this->form_validation->set_rules("estsubtaskdateend[{$key}]", 'Est.End Date', 'trim|required');
            // $this->form_validation->set_rules("indays[{$key}]", 'In Days', 'trim|required');
            $this->form_validation->set_rules("inhrs[{$key}]", 'Hours', 'trim|required');
            $this->form_validation->set_rules("inmns[{$key}]", 'Minutes', 'trim|required');
            $this->form_validation->set_rules("inmns[{$key}]", 'Minutes', 'trim|required');
            $this->form_validation->set_rules("assign[{$key}]", 'Assignee', 'trim|required');
        }
        if ($this->form_validation->run() == false) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Task_management_model->AddSubTaskDetail());
        }
    }

    
    function load_subtask_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $taskid = trim($this->input->post('taskID') ?? '');
        $data['detail'] = $this->db->query("SELECT subtask.*,DATE_FORMAT(subtask.startDate,'" . $convertFormat . "') AS startDateTask,DATE_FORMAT(subtask.endDate,'" . $convertFormat . "') AS endDateTask FROM srp_erp_task_subtasks subtask where  subtask.taskID = '{$taskid}' 	ORDER BY
	subtask.subTaskID DESC")->result_array();
        $this->load->view('system/task_managment/subtask_view', $data);
    }


    
    function load_subtask_details()
    {
        echo json_encode($this->Task_management_model->load_subtask_details());
    }


    function start_subtask()
    {
        $this->form_validation->set_rules('subtaskid', 'Sub Task ID', 'trim|required');
        $this->form_validation->set_rules('taskid', 'Task ID', 'trim|required');
        $companyid = current_companyID();
        $taskid = trim($this->input->post('taskid') ?? '');
        $subtasksessionstrat = $this->db->query("SELECT status FROM `srp_erp_task_subtasksessions` where  companyID = $companyid  AND taskID = $taskid And `status` = 1")->row_array();
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if (!empty($subtasksessionstrat)) {
                echo json_encode(array('w', 'cannot start!'));
            } else {
                echo json_encode($this->Task_management_model->save_sub_task_Enable());
            }
        }
    }

    function stop_subtask()
    {
        $this->form_validation->set_rules('subtaskid', 'Sub Task ID', 'trim|required');
        $this->form_validation->set_rules('taskid', 'Task ID', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->stop_subtask());
        }
    }


    
    function save_subTask_status()
    {
        $this->form_validation->set_rules('subtaskID', 'Sub Task ID', 'trim|required');
        $this->form_validation->set_rules('TaskID', 'Task ID', 'trim|required');
        $this->form_validation->set_rules('statussubtask', 'Status', 'trim|required');
        $subtaskid = trim($this->input->post('subtaskID') ?? '');
        $status = trim($this->input->post('statussubtask') ?? '');
        $taskid = trim($this->input->post('TaskID') ?? '');
        $companyid = current_companyID();

        $subtaskstatus = $this->db->query("SELECT `status` FROM `srp_erp_task_subtasks` where companyID = $companyid ANd subTaskID = $subtaskid And taskID = $taskid")->row_array();


        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($subtaskstatus['status'] == 1 && $status == 0) {
                echo json_encode(array('e', 'Cannot change the on going status to not started!'));
            } elseif ($subtaskstatus['status'] == 2 && $status == 1) {
                echo json_encode(array('e', 'Cannot change the Completed status to on going!'));
            } elseif ($subtaskstatus['status'] == 2 && $status == 0) {
                echo json_encode(array('e', 'Cannot change the Completed status to not started !'));
            } else {
                echo json_encode($this->Task_management_model->save_subTask_status());
            }
        }
    }

    function load_subtask_chats()
    {
        $subtaskid = trim($this->input->post('subTaskID') ?? '');
        $currenttaskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data['subtaskid'] = $subtaskid;
        $data['taskID'] = $currenttaskid;


        $data['chat'] = $this->db->query("SELECT chat.*,empdetails.Ename2 as employeename,CONCAT( MONTHNAME( DATE_FORMAT(createdDateTime, '%Y-%m-%d' )),' ',(DATE_FORMAT( createdDateTime, '%d' ))) AS datemonth  FROM srp_erp_task_chat chat LEFT JOIN srp_employeesdetails empdetails on empdetails.EIdNo = chat.empID where companyID = $companyid And taskID = $currenttaskid
And subTaskID = $subtaskid ORDER BY chatID ASC")->result_array();
        $this->load->view('system/task_managment/subtask_chat_windows', $data);
    }

    function load_task_chats_comment()
    {
        $currenttaskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data['taskID'] = $currenttaskid;


        $data['chat'] = $this->db->query("
        SELECT 
            chat.*, 
            empdetails.Ename2 AS employeename,
            CONCAT( MONTHNAME( DATE_FORMAT(createdDateTime, '%Y-%m-%d')), ' ', (DATE_FORMAT(createdDateTime, '%d'))) AS datemonth  
        FROM 
            srp_erp_task_chat chat 
        LEFT JOIN 
            srp_employeesdetails empdetails ON empdetails.EIdNo = chat.empID 
        WHERE 
             taskID = $currenttaskid and subTaskID IS NULL
        ORDER BY 
            chatID ASC")->result_array();    
        $this->load->view('system/task_managment/task_chat_windows', $data);
    }

    function load_history_task()
    {
        $currenttaskid = trim($this->input->post('taskID') ?? '');
        $data['taskID'] = $currenttaskid;
    
        // Fetch history details from the database
        $historyDetailsRow = $this->db->query("
            SELECT
                documentMasterID,
                historyDetails
            FROM
                srp_erp_document_change_history 
            WHERE
                documentMasterID = $currenttaskid ")->row();
    
        // Check if the query returned any data
        if ($historyDetailsRow) {
            $historyDetails = json_decode($historyDetailsRow->historyDetails, true);
    
            $formattedData = [];
    
            // Check if history details contain 'createdUserName' and 'createdDateTime'
            if (isset($historyDetails['createdUserName']) && isset($historyDetails['createdDateTime'])) {
                $formattedData['createdUserData'] = [
                    'createdUserName' => $historyDetails['createdUserName'],
                    'createdDateTime' => $historyDetails['createdDateTime']
                ];
            }
    
            // Check if history details contain 'changes'
            if (isset($historyDetails['changes'])) {
                $formattedData['changeData'] = [];
    
                // Loop through each change
                foreach ($historyDetails['changes'] as $field => $change) {
                    // Handle the initial change (non-numeric keys)
                    if (
                        isset($change['changedBy']) &&
                        isset($change['changedDateTime']) &&
                        isset($change['old_value']) &&
                        isset($change['new_value'])
                    ) {
                        $formattedChange = [
                            'fieldName' => $field,
                            'changedBy' => $change['changedBy'],
                            'changedDateTime' => $change['changedDateTime'],
                            'oldValue' => $change['old_value'],
                            'newValue' => $change['new_value']
                        ];
                        $formattedData['changeData'][] = $formattedChange;
                    }
    
                    // Check if there are nested changes (numeric keys)
                    foreach ($change as $key => $nestedChange) {
                        if (is_numeric($key)) {
                            // Skip null values and add nested changes
                            if (
                                isset($nestedChange['changedBy']) &&
                                isset($nestedChange['changedDateTime']) &&
                                isset($nestedChange['old_value']) &&
                                isset($nestedChange['new_value'])
                            ) {
                                $formattedChange = [
                                    'fieldName' => $field,
                                    'changedBy' => $nestedChange['changedBy'],
                                    'changedDateTime' => $nestedChange['changedDateTime'],
                                    'oldValue' => $nestedChange['old_value'],
                                    'newValue' => $nestedChange['new_value']
                                ];
                                $formattedData['changeData'][] = $formattedChange;
                            }
                        }
                    }
                }
            }
    
            // Encode the formatted data to JSON and send it as response
            echo json_encode($formattedData);
        } else {
            // If no history details found for the given task ID, send an empty response
            echo json_encode([]);
        }
    }
    
    






    function save_task_comment()
    {
        echo json_encode($this->Task_management_model->save_task_comment());
    }

    
    function attachment_subTask()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $subtaskid = trim($this->input->post('subTaskID') ?? '');
        $data['subtaskid'] = $subtaskid;
        $where = "mastertbl.companyID = " . $companyid . " AND documentID = '10'  AND documentAutoID = " . $subtaskid . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('mastertbl.*');
        $this->db->from('srp_erp_task_attachments mastertbl');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/task_managment/subtask_attacchment', $data);
    }

    function start_date_est_date_validation()
    {
        echo json_encode($this->Task_management_model->start_date_est_date_validation());
    }

    function end_date_est_date_validation()
    {
        echo json_encode($this->Task_management_model->end_date_est_date_validation());
    }

    function crm_task_close_ischk()
    {
        echo json_encode($this->Task_management_model->crm_task_close_ischk());
    }

    function crm_is_subtask_exist()
    {
        echo json_encode($this->Task_management_model->crm_is_subtask_exist());
    }

    
    function crm_sub_task_detail_edit()
    {
        $subtaskid = $this->input->post('subTaskID');
        $taskid = $this->input->post('taskID');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("SELECT
                                        *,
                                    DATE_FORMAT( startDate, '%d-%m-%Y' ) as startdateSubtask,
                                    DATE_FORMAT( endDate, '%d-%m-%Y' ) as enddateSubtask

                                    FROM
                                    srp_erp_task_subtasks
                                    where
                                    companyID = $companyid
                                    ANd taskID = $taskid
                                    ANd subTaskID = $subtaskid")->row_array();
        echo json_encode($data);
    }

    function fetch_tasks_employee_detailsubtask()
    {
        echo json_encode($this->Task_management_model->fetch_tasks_employee_detailsubtask());
    }

    function update_subtaask_detail()
    {


        $this->form_validation->set_rules("Taskdescriptionedit", 'Task Description', 'trim|required');
        $this->form_validation->set_rules("estsubtaskdateedit", 'Est.Start Date', 'trim|required');
        $this->form_validation->set_rules("estsubtaskdateendedit", 'Est.End Date', 'trim|required');
        $this->form_validation->set_rules("indaysedit", 'In Days', 'trim|required');
        $this->form_validation->set_rules("inhrsedit", 'Hours', 'trim|required');
        $this->form_validation->set_rules("inmnsedit", 'Minutes', 'trim|required');
        $this->form_validation->set_rules("employeessubtaskedit[]", 'Assignee', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->update_subtaask_detail());
        }
    }


    
    function load_taskManagement_view()
    {
       
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $category = trim($this->input->post('category') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $priority = trim($this->input->post('priority') ?? '');
        $tasktype = trim($this->input->post('tasktype') ?? '');
        $assigneesID = trim($this->input->post('assignees') ?? '');
        $issuperadmin = crm_isSuperAdmin();
        $currentuserID = current_userID();
        $convertFormat = convert_date_format_sql();
        $opporunityID = trim($this->input->post('opporunityID') ?? '');
        $pipeLineDetailID = trim($this->input->post('pipeLineDetailID') ?? '');
        $data['type'] = trim($this->input->post('type') ?? '');
        $data['opportunityID'] = $opporunityID;
        $employeeid = $this->input->post('createdby');
        $search_string = '';
        
        if (isset($text) && !empty($text)) {
            $search_string = " AND subject Like '%" . $text . "%'";
        }
        $filterCategory = '';
        if (isset($category) && !empty($category)) {
            $filterCategory = " AND srp_erp_task_task.categoryID = " . $category . "";
        }
        $filterStatus = '';
        if (isset($status) && !empty($status)) {
            $filterStatus = " AND status = " . $status . "";
        }
        $filterpriority = '';
        if (isset($priority) && !empty($priority)) {
            $filterpriority = " AND Priority = " . $priority . "";
        }
        $filtertasktype = '';

            if (isset($tasktype) && !empty($tasktype)) {
                switch ($tasktype) {
                    case 1:
                        $filtertasktype = " AND srp_erp_task_task.createdUserID = " . $currentuserID;
                        break;
                    case 2:
                        $filtertasktype = " AND srp_erp_task_assignees.empID = " . $currentuserID;
                        break;
                    default:
                        break;
                }
            }

        $filterAssigneesID = '';
        if (isset($assigneesID) && !empty($assigneesID)) {
            $filterAssigneesID = " AND srp_erp_task_assignees.empID = " . $assigneesID . "";
        }

        $filteropporunityID = '';
        if (isset($opporunityID) && !empty($opporunityID)) {
            $filteropporunityID = " AND opportunityID = " . $opporunityID . "";
        }

        $filterpipeLineDetailID = '';
        if (isset($pipeLineDetailID) && !empty($pipeLineDetailID)) {
            $filterpipeLineDetailID = " AND pipelineStageID = " . $pipeLineDetailID . "";
        }

        $filtercreatedid = '';
        if (isset($employeeid) && !empty($employeeid)) {
            $filtercreatedid = " AND srp_erp_task_task.createdUserID = " . $employeeid . "";
        }
        if ($issuperadmin && $issuperadmin['isSuperAdmin'] == 1) {
            $where_admin = "srp_erp_task_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority .$filtertasktype .$filterAssigneesID . $filteropporunityID . $filterpipeLineDetailID . $filtercreatedid;

            $data['headercount'] = $this->db->query("SELECT srp_erp_task_task.taskID,srp_erp_task_task.createdUserID,srp_erp_task_assignees.empID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID WHERE $where_admin GROUP BY srp_erp_task_task.taskID ORDER BY taskID DESC ")->result_array();
            $totalCount = count($data['headercount']);
            $data_pagination = $this->input->post('pageID');
            $per_page = 10;
            $config = array();
            $config["base_url"] = "#employee-list";
            $config["total_rows"] = $totalCount;
            $config["per_page"] = $per_page;
            $config["data_page_attr"] = 'data-emp-pagination';
            $config["uri_segment"] = 3;
            $this->pagination->initialize($config);
            $page = (!empty($data_pagination)) ? (($data_pagination - 1) * $per_page) : 0;
            $data["empCount"] = $totalCount;
            $data["pagination"] = $this->pagination->create_links_employee_master();
            $data["per_page"] = $per_page;
            $thisPageStartNumber = ($page + 1);
            $data['headercountshowing'] = $this->db->query("SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID WHERE $where_admin GROUP BY srp_erp_task_task.taskID ORDER BY taskID DESC LIMIT {$page},{$per_page} ")->result_array();
            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT 
                                                srp_erp_task_task.taskID,
                                                srp_erp_task_task.subject,
                                                srp_erp_task_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_task_status.description as statusDescription,
                                                srp_erp_task_assignees.empID,
                                                srp_erp_task_task.progress,
                                                srp_erp_task_task.status,
                                                srp_erp_task_task.Priority,
                                                isClosed,
                                                srp_erp_task_task.createdUserID as createdUserIDtask,
                                                srp_erp_task_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_task_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_task_task.documentSystemCode,
                                                srp_erp_task_status.statusColor AS statusColor,
	                                            srp_erp_task_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM 
                                                srp_erp_task_task 
                                                LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID
                                                LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status 
                                                LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID 
                                                WHERE $where_admin 
                                                GROUP BY srp_erp_task_task.taskID ORDER BY taskID DESC LIMIT {$page},{$per_page}")->result_array();
        } else {
            $where1 = "srp_erp_task_documentpermission.documentID = 2 AND srp_erp_task_documentpermission.permissionID = 1 AND srp_erp_task_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority .$filtertasktype. $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID;

            $where2 = "srp_erp_task_documentpermission.documentID = 2 AND srp_erp_task_documentpermission.permissionID = 2 AND (srp_erp_task_documentpermission.permissionValue = $currentuserID or  srp_erp_task_task.createdUserID = $currentuserID) AND srp_erp_task_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority .$filtertasktype. $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID ;

            $where3 = "srp_erp_task_documentpermission.documentID = 2 AND srp_erp_task_documentpermission.permissionID = 3 AND (srp_erp_task_usergroupdetails.empID = $currentuserID or  srp_erp_task_task.createdUserID = $currentuserID) AND srp_erp_task_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filtertasktype .$filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID ;

            $where4 = "srp_erp_task_documentpermission.documentID = 2 AND srp_erp_task_documentpermission.permissionID = 4 AND (srp_erp_task_documentpermissiondetails.empID = $currentuserID or  srp_erp_task_task.createdUserID = $currentuserID) AND srp_erp_task_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority .$filtertasktype. $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID ;


            $data['headercount'] = $this->db->query("SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID WHERE $where1 GROUP BY
	srp_erp_task_task.taskID  UNION SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID WHERE $where2 GROUP BY
	srp_erp_task_task.taskID  UNION SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_usergroupdetails ON srp_erp_task_usergroupdetails.groupMasterID = srp_erp_task_documentpermission.permissionValue WHERE $where3 GROUP BY
	srp_erp_task_task.taskID  UNION SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermissiondetails ON srp_erp_task_documentpermissiondetails.documentPermissionID = srp_erp_task_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_task_task.taskID ORDER BY taskID DESC")->result_array();
            $totalCount = count($data['headercount']);
            $data_pagination = $this->input->post('pageID');
            $per_page = 10;
            $config = array();
            $config["base_url"] = "#employee-list";
            $config["total_rows"] = $totalCount;
            $config["per_page"] = $per_page;
            $config["data_page_attr"] = 'data-emp-pagination';
            $config["uri_segment"] = 3;
            $this->pagination->initialize($config);
            $page = (!empty($data_pagination)) ? (($data_pagination - 1) * $per_page) : 0;
            $data["empCount"] = $totalCount;
            $data["pagination"] = $this->pagination->create_links_employee_master();
            $data["per_page"] = $per_page;
            $thisPageStartNumber = ($page + 1);
            $data['headercountshowing'] = $this->db->query("SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID WHERE $where1 GROUP BY
	srp_erp_task_task.taskID  UNION SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID WHERE $where2 GROUP BY
	srp_erp_task_task.taskID  UNION SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_usergroupdetails ON srp_erp_task_usergroupdetails.groupMasterID = srp_erp_task_documentpermission.permissionValue WHERE $where3 GROUP BY
	srp_erp_task_task.taskID  UNION SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermissiondetails ON srp_erp_task_documentpermissiondetails.documentPermissionID = srp_erp_task_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_task_task.taskID ORDER BY taskID DESC LIMIT {$page},{$per_page}")->result_array();
            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT
                                                srp_erp_task_task.taskID,
                                                srp_erp_task_task.subject,
                                                srp_erp_task_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_task_status.description as statusDescription,
                                                srp_erp_task_assignees.empID,
                                                srp_erp_task_task.progress,
                                                srp_erp_task_task.status,
                                                srp_erp_task_task.Priority,
                                                isClosed,
                                                srp_erp_task_task.createdUserID as createdUserIDtask,
                                                srp_erp_task_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_task_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_task_task.documentSystemCode,
                                                srp_erp_task_status.statusColor AS statusColor,
	                                            srp_erp_task_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM srp_erp_task_task 
                                                LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID 
                                                LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status 
                                                LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID
                                                LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID 
                                                WHERE $where1 
                                                GROUP BY
	                                            srp_erp_task_task.taskID  
                                                UNION 
                                                SELECT srp_erp_task_task.taskID,
                                                srp_erp_task_task.subject,
                                                srp_erp_task_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_task_status.description as statusDescription,
                                                srp_erp_task_assignees.empID,
                                                srp_erp_task_task.progress,
                                                srp_erp_task_task.status,
                                                srp_erp_task_task.Priority,
                                                isClosed,
                                                srp_erp_task_task.createdUserID as createdUserIDtask,
                                                srp_erp_task_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_task_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_task_task.documentSystemCode,
                                                srp_erp_task_status.statusColor AS statusColor,
	                                            srp_erp_task_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM 
                                                srp_erp_task_task 
                                                LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID 
                                                LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status 
                                                LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID 
                                                LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID 
                                                WHERE $where2 
                                                GROUP BY
	                                            srp_erp_task_task.taskID 
                                                UNION 
                                                SELECT 
                                                srp_erp_task_task.taskID,
                                                srp_erp_task_task.subject,
                                                srp_erp_task_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_task_status.description as statusDescription,
                                                srp_erp_task_assignees.empID,
                                                srp_erp_task_task.progress,
                                                srp_erp_task_task.status,
                                                srp_erp_task_task.Priority,
                                                isClosed,
                                                srp_erp_task_task.createdUserID as createdUserIDtask,
                                                srp_erp_task_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_task_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_task_task.documentSystemCode,
                                                srp_erp_task_status.statusColor AS statusColor,
	                                            srp_erp_task_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM srp_erp_task_task
                                                LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID 
                                                LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status 
                                                LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID 
                                                LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID 
                                                LEFT JOIN srp_erp_task_usergroupdetails ON srp_erp_task_usergroupdetails.groupMasterID = srp_erp_task_documentpermission.permissionValue 
                                                WHERE $where3 
                                                GROUP BY
	                                            srp_erp_task_task.taskID 
                                                UNION 
                                                SELECT 
                                                srp_erp_task_task.taskID,
                                                srp_erp_task_task.subject,
                                                srp_erp_task_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_task_status.description as statusDescription,
                                                srp_erp_task_assignees.empID,
                                                srp_erp_task_task.progress,
                                                srp_erp_task_task.status,
                                                srp_erp_task_task.Priority,isClosed,
                                                srp_erp_task_task.createdUserID as createdUserIDtask,
                                                srp_erp_task_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_task_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_task_task.documentSystemCode,
                                                srp_erp_task_status.statusColor AS statusColor,
	                                            srp_erp_task_status.statusBackgroundColor AS statusBackgroundColor 
                                                FROM srp_erp_task_task 
                                                LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID 
                                                LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status 
                                                LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID 
                                                LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID 
                                                LEFT JOIN srp_erp_task_documentpermissiondetails ON srp_erp_task_documentpermissiondetails.documentPermissionID = srp_erp_task_documentpermission.documentPermissionID 
                                                WHERE 
                                                $where4
                                                GROUP BY 
                                                srp_erp_task_task.taskID 
                                                ORDER BY taskID DESC 
                                                LIMIT {$page},{$per_page}")->result_array();
        }
        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['view'] = $this->load->view('system/task_managment/load_main_task_master', $data, true);
        
        echo json_encode($data);
    }

    
    
    function load_taskManagement_employee_view()
    {
       
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $category = trim($this->input->post('category') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $priority = trim($this->input->post('priority') ?? '');
        $tasktype = trim($this->input->post('tasktype') ?? '');
        $assigneesID = trim($this->input->post('assignees') ?? '');
        $responsible = trim($this->input->post('responsible') ?? '');
        $issuperadmin = crm_isSuperAdmin();
        $currentuserID = current_userID();
        $convertFormat = convert_date_format_sql();
        $opporunityID = trim($this->input->post('opporunityID') ?? '');
        $pipeLineDetailID = trim($this->input->post('pipeLineDetailID') ?? '');
        $data['type'] = trim($this->input->post('type') ?? '');
        $data['opportunityID'] = $opporunityID;
        $employeeid = $this->input->post('createdby');
        $search_string = '';
        
        if (isset($text) && !empty($text)) {
            $search_string = " AND subject Like '%" . $text . "%'";
        }
        $filterCategory = '';
        if (isset($category) && !empty($category)) {
            $filterCategory = " AND srp_erp_task_task.categoryID = " . $category . "";
        }
        $filterStatus = '';
        if (isset($status) && !empty($status)) {
            $filterStatus = " AND status = " . $status . "";
        }
        $filterpriority = '';
        if (isset($priority) && !empty($priority)) {
            $filterpriority = " AND Priority = " . $priority . "";
        }
        $filtertasktype = '';

            if (isset($tasktype) && !empty($tasktype)) {
                switch ($tasktype) {
                    case 1:
                        $filtertasktype = " AND srp_erp_task_task.createdUserID = " . $currentuserID;
                        break;
                    case 2:
                        $filtertasktype = " AND srp_erp_task_assignees.empID = " . $currentuserID;
                        break;
                    case 3:
                        
                        break;
                    default:
                        
                        break;
                }
            }


        

        $filterAssigneesID = '';
        if (isset($assigneesID) && !empty($assigneesID)) {
            $filterAssigneesID = " AND srp_erp_task_assignees.empID = " . $assigneesID . "";
        }
        $filteruserresponsibe = '';
        if (isset($responsible) && !empty($responsible)) {
            $filteruserresponsibe = " AND srp_erp_task_assignees.empID = " . $responsible . "";
        }

        $filteropporunityID = '';
        if (isset($opporunityID) && !empty($opporunityID)) {
            $filteropporunityID = " AND opportunityID = " . $opporunityID . "";
        }

        $filterpipeLineDetailID = '';
        if (isset($pipeLineDetailID) && !empty($pipeLineDetailID)) {
            $filterpipeLineDetailID = " AND pipelineStageID = " . $pipeLineDetailID . "";
        }

        $filtercreatedid = '';
        if (isset($employeeid) && !empty($employeeid)) {
            $filtercreatedid = " AND srp_erp_task_task.createdUserID = " . $employeeid . "";
        }
            $where4 = "srp_erp_task_documentpermission.documentID = 2 AND srp_erp_task_documentpermission.permissionID IN (0, 4) " . $search_string . $filterCategory . $filterStatus . $filterpriority .$filtertasktype. $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID. $filteruserresponsibe ;

            $data['headercount'] = $this->db->query("SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermissiondetails ON srp_erp_task_documentpermissiondetails.documentPermissionID = srp_erp_task_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_task_task.taskID ORDER BY taskID DESC")->result_array();
            $totalCount = count($data['headercount']);
            $data_pagination = $this->input->post('pageID');
            $per_page = 10;
            $config = array();
            $config["base_url"] = "#employee-list";
            $config["total_rows"] = $totalCount;
            $config["per_page"] = $per_page;
            $config["data_page_attr"] = 'data-emp-pagination';
            $config["uri_segment"] = 3;
            $this->pagination->initialize($config);
            $page = (!empty($data_pagination)) ? (($data_pagination - 1) * $per_page) : 0;
            $sentfunction = 'sentemailpagination';
            $data["empCount"] = $totalCount;
            $data["pagination"] = $this->pagination->create_links_employee_master();
            $data["per_page"] = $per_page;
            $thisPageStartNumber = ($page + 1);
            $data['headercountshowing'] = $this->db->query("SELECT srp_erp_task_task.taskID FROM srp_erp_task_task LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.status LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID LEFT JOIN srp_erp_task_documentpermissiondetails ON srp_erp_task_documentpermissiondetails.documentPermissionID = srp_erp_task_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_task_task.taskID ORDER BY taskID DESC LIMIT {$page},{$per_page}")->result_array();
            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT
                                                    srp_erp_task_task.taskID,
                                                    srp_erp_task_task.subject,
                                                    srp_erp_task_categories.description AS categoryDescription,
                                                    textColor,
                                                    backGroundColor,
                                                    visibility,
                                                starDate,
                                                DueDate,
                                                srp_erp_task_documentpermission.permissionID,
                                                    srp_erp_task_status.description AS statusDescription,
                                                    srp_erp_task_assignees.empID,
                                                    srp_erp_task_task.progress,
                                                    srp_erp_task_task.STATUS,
                                                    srp_erp_task_task.Priority,
                                                    isClosed,
                                                    srp_erp_task_task.createdUserID AS createdUserIDtask,
                                                    srp_erp_task_task.createdUserName AS createdUserName,
                                                    DATE_FORMAT( srp_erp_task_task.createdDateTime, '" . $convertFormat . "' ) AS createdDateTime,
                                                    srp_erp_task_task.documentSystemCode,
                                                    srp_erp_task_status.statusColor AS statusColor,
                                                    srp_erp_task_status.statusBackgroundColor AS statusBackgroundColor 
                                                FROM
                                                    srp_erp_task_task
                                                    LEFT JOIN srp_erp_task_categories ON srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID
                                                    LEFT JOIN srp_erp_task_status ON srp_erp_task_status.statusID = srp_erp_task_task.
                                                    STATUS LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID
                                                    LEFT JOIN srp_erp_task_documentpermission ON srp_erp_task_documentpermission.documentAutoID = srp_erp_task_task.taskID 
                                                    left join srp_erp_task_documentpermissiondetails on srp_erp_task_documentpermissiondetails.documentAutoID = srp_erp_task_task.taskID  
                                                    
                                                    WHERE 

                                                ($where4) 
                                                AND (
                                                    srp_erp_task_task.createdUserID = $currentuserID 
                                                    OR srp_erp_task_assignees.empID = $currentuserID
                                                    OR srp_erp_task_documentpermissiondetails.empID = $currentuserID)
                                                GROUP BY 
                                                srp_erp_task_task.taskID 
                                                ORDER BY taskID DESC 
                                                LIMIT {$page},{$per_page}")->result_array();
        
        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['view'] = $this->load->view('system/task_managment/load_task_master_employee', $data, true);
        
        echo json_encode($data);
    }

    


    
    function load_taskManagement_editView()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $currentuser = current_userID();
        $taskID = trim($this->input->post('taskID') ?? '');
        $this->db->select('srp_erp_task_task.taskID,srp_erp_task_task.isSubTaskEnabled as isSubTaskEnabled,srp_erp_task_assignees.subassignee,srp_erp_task_task.subject,srp_erp_task_task.status,srp_erp_task_categories.description as categoryDescription,textColor,backGroundColor,srp_erp_task_task.Priority,srp_erp_task_status.description as statusDescription,srp_erp_task_task.description as taskDescription,srp_erp_task_task.progress,visibility,DATE_FORMAT(srp_erp_task_task.modifiedDateTime,\'' . $convertFormat . '\') AS updateDate,DATE_FORMAT(srp_erp_task_task.starDate, \'%D-%b-%y \') AS starDate,DATE_FORMAT(srp_erp_task_task.DueDate, \'%D-%b-%y \') AS DueDate,srp_erp_task_task.createdUserName as createdbY,srp_erp_task_task.status,DATE_FORMAT(srp_erp_task_task.completedDate,\'' . $convertFormat . '\') AS completedDate, srp_employeesdetails.Ename2 as completedBy,srp_erp_task_task.opportunityID,srp_erp_task_task.pipelineStageID,srp_erp_task_task.isClosed,srp_erp_task_task.projectID,srp_erp_crm_project.projectName,srp_erp_task_task.comment,srp_erp_task_task.createdUserID as crtduser,srp_erp_task_task.documentSystemCode as documentSystemCodetask');
        $this->db->from('srp_erp_task_task');
        $this->db->join('srp_erp_task_categories', 'srp_erp_task_categories.categoryID = srp_erp_task_task.categoryID', 'LEFT');
        $this->db->join('srp_erp_task_status', 'srp_erp_task_status.statusID = srp_erp_task_task.status', 'LEFT');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_task_task.completedBy', 'LEFT');
        $this->db->join('srp_erp_crm_project', 'srp_erp_crm_project.projectID = srp_erp_task_task.projectID', 'LEFT');
        $this->db->join(' (select MasterAutoID,empID, CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END AS subassignee FROM srp_erp_task_assignees WHERE MasterAutoID = ' . $taskID . ' AND empID = ' . $currentuser . ' ) as srp_erp_task_assignees', 'srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID', 'LEFT');

        $this->db->where('taskID', $taskID);
        $this->db->where('srp_erp_task_categories.documentID', 2);
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('srp_employeesdetails.Ename2,srp_erp_task_assignees.empID');
        $this->db->from('srp_erp_task_assignees');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID');
        $this->db->where('MasterAutoID', $taskID);
        $this->db->where('documentID', 2);
        $data['taskAssignee'] = $this->db->get()->result_array();


        $this->db->select('employeeID as isadmin');
        $this->db->from('srp_erp_task_users');
        $this->db->where('companyID', $companyid);
        $this->db->where('isSuperAdmin', 1);
        $data['superadmn'] = $this->db->get()->row_array();
        $data['tskass'] = 0;
        if (!empty($data['taskAssignee'])) {
            $employees = array();
            foreach ($data['taskAssignee'] as $val) {
                array_push($employees, $val['empID']);
            }

            if (in_array($this->common_data['current_userID'], $employees)) {
                $data['tskass'] = 1;
            } else {
                $data['tskass'] = 0;
            }
        } else {
            $data['tskass'] = 0;
        }
        $data['masterID'] = $this->input->post('masterID');

        $this->load->view('system/task_managment/load_task_edit_view', $data);
    }

    function update_task_edit_view_comment()
    {
        $this->form_validation->set_rules('taskcomment', 'Comment', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->update_task_edit_view_comment());
        }
    }

    function load_subtask_detail_edit()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $taskid = trim($this->input->post('taskID') ?? '');


        $data['detail'] = $this->db->query("SELECT subtask.*,DATE_FORMAT(subtask.startDate,'" . $convertFormat . "') AS startDateTask,DATE_FORMAT(subtask.endDate,'" . $convertFormat . "') AS endDateTask FROM srp_erp_task_subtasks subtask where  subtask.taskID = '{$taskid}'")->result_array();

        $this->load->view('system/task_managment/subtask_view', $data);
    }

    function load_subtask_chats_edit()
    {
        $subtaskid = trim($this->input->post('subTaskID') ?? '');
        $currenttaskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data['subtaskid'] = $subtaskid;
        $data['taskID'] = $currenttaskid;


        $data['chat'] = $this->db->query("SELECT chat.*,empdetails.Ename2 as employeename,CONCAT( MONTHNAME( DATE_FORMAT(createdDateTime, '%Y-%m-%d' )),' ',(DATE_FORMAT( createdDateTime, '%d' ))) AS datemonth  FROM srp_erp_task_chat chat LEFT JOIN srp_employeesdetails empdetails on empdetails.EIdNo = chat.empID where companyID = $companyid And taskID = $currenttaskid
And subTaskID = $subtaskid ORDER BY chatID ASC")->result_array();
        $this->load->view('system/task_managment/subtask_chat_windows_taskedit', $data);
    }

    
    function load_subtsk_status()
    {
        $this->form_validation->set_rules('subTaskID', 'Sub Task ID', 'trim|required');
        $this->form_validation->set_rules('taskID', 'Task ID', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->load_subtsk_status());
        }
    }

    function attachement_upload_subtask()
    {
        $this->form_validation->set_rules('subtaska_attachment_description', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $info = new SplFileInfo($_FILES["document_file"]["name"]);
          
            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_task_attachments')->result_array();
            $file_name = $this->input->post('document_name') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1)  . '.' . $info->getExtension();
            ;
          /*   $config['upload_path'] = realpath(APPPATH . '../attachments/CRM/SubTask');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;
 */



            $file = $_FILES['document_file'];
            if ($file['error'] == 1) {
                return array('e' ,'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).');
                exit();
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if (!in_array($ext, $allowed_types)) {
                return array('e' , "The file type you are attempting to upload is not allowed. ( .{$ext} )");
                exit();
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if ($size > 5) {
                return array('e' , "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )");
                exit();
            }

            $path = "attachments/CRM/SubTask/".$file_name;
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                return array('e' , "Error in document upload location configuration");
                exit();
            }

                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('subtaska_attachment_description') ?? '');
                $data['myFileName'] = $path;
                $data['fileType'] = trim($ext);
                $data['fileSize'] = trim($file["size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];

                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_task_attachments', $data);
                $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.'));
            }
        }
    }

    
    function delete_subtask_attachments()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $result = $this->s3->delete($myFileName);
        /** end of AWS s3 delete object */
        if ($result) {
            $this->db->delete('srp_erp_task_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    function sub_task_comment()
    {
        echo json_encode($this->Task_management_model->save_sub_task_comment());
    }



    
    function create_category_status()
    {
        // $this->form_validation->set_rules('documentID', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('textColor', 'Text Color', 'trim|required');
        $this->form_validation->set_rules('backgroundColor', 'Background Color', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->create_category_status());
        }
    }

    
    function fetch_task_master()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "companyID = " . $companyid;
        $this->datatables->select('taskID,srp_erp_task_task.subject,srp_erp_task_task.description,srp_erp_task_task.Priority,srp_erp_task_status.description as statusDes,contactName ');
        $this->datatables->join('srp_erp_task_status', 'srp_erp_task_task.status = srp_erp_task_status.statusID');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_task_task');
        $this->datatables->add_column('edit', '$1', 'load_task_action(taskID)');
        echo $this->datatables->generate();
    }


 

    function fetch_cat_status()
    {
        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];
        $filter = "companyID = $companyID ";
        if ($masterID != '') {
            $filter .= "AND srp_erp_task_categories.documentID=$masterID";
        }

        $this->datatables->select("textColor,sladays,backGroundColor,categoryID,srp_erp_task_documents.description as document,srp_erp_task_categories.description as category");
        $this->datatables->from('srp_erp_task_documents');
        $this->datatables->join('srp_erp_task_categories', 'srp_erp_task_categories.documentID = srp_erp_task_documents.documentID', 'LEFT');
        $this->datatables->where($filter);
        $this->datatables->add_column('edit', '<a onclick="editDocumentStatus($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
                                                <a onclick="deleteDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
                                                <a onclick="assignedepartment($1)"><span title="Assignee" rel="tooltip" class="glyphicon glyphicon-user" style=""></span></a>', 'categoryID');
        $this->datatables->add_column('textColor', '<div style="text-align: center">$1</div>', 'statuscolor(textColor)');
        $this->datatables->add_column('backGroundColor', '<div style="text-align: center">$1</div>', 'statuscolor(backGroundColor)');
        echo $this->datatables->generate();
    }

    function deleteCaegory()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $categoryID = $this->input->post('categoryID');
        $categoryassign = $this->db->query("SELECT taskID,categoryID  FROM  `srp_erp_task_task`  WHERE  companyID = $companyID and  categoryID = $categoryID")->row_array();


        if (!empty($categoryassign)) {
            echo json_encode(array('error' => 1, 'message' => 'This category already Assign in Task.'));
            exit;
        }else{
            echo json_encode($this->Task_management_model->deleteCaegory());
        }
       
    }

    function get_all_categories()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $categoryID = $this->input->post('categoryID');
        $categoryassign = $this->db->query("SELECT taskID,categoryID  FROM  `srp_erp_task_task`  WHERE  companyID = $companyID and  categoryID = $categoryID")->row_array();
        $result = $this->db->query("select * from  srp_erp_task_categories WHERE categoryID={$categoryID}")->row_array();
        $data = array(
            'category_data' => $result,
            'category_assign' => $categoryassign
        );
        echo json_encode($data);
    }

    function deleteDocumentStatus()
    {
        echo json_encode($this->Task_management_model->deleteDocumentStatus());
    }

    
    function fetch_doc_status()
    {

        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];
        $filter = "companyID = $companyID ";
        if ($masterID != '') {
            $filter .= "AND srp_erp_task_status.documentID=$masterID";
        }

        $this->datatables->select("statusColor,shortorder,statusBackgroundColor,statusID,srp_erp_task_documents.description as document,srp_erp_task_status.description as description");
        $this->datatables->from('srp_erp_task_documents');
        $this->datatables->join('srp_erp_task_status', 'srp_erp_task_status.documentID = srp_erp_task_documents.documentID', 'LEFT');
        $this->datatables->where($filter);
        $this->datatables->add_column('edit', '<a onclick="editDocumentStatus($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
                                                <a onclick="deleteDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'statusID');
        $this->datatables->add_column('color', '<div style="text-align: center">$1</div>', 'statuscolor(statusColor)');
        $this->datatables->add_column('backgroundColor', '<div style="text-align: center">$1</div>', 'statuscolor(statusBackgroundColor)');
        echo $this->datatables->generate();
    }

    function get_alldocumentStatus()
    {
        $statusID = $this->input->post('statusID');
        $data = $this->db->query("select * from srp_erp_task_status WHERE statusID={$statusID}")->row_array();
        echo json_encode($data);
    }

    function create_document_status()
    {
        // $this->form_validation->set_rules('documentID', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('status', 'status', 'trim|required');
        $this->form_validation->set_rules('color', 'Status Color', 'trim|required');
        $this->form_validation->set_rules('backgroundColor', 'Background Color', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->create_document_status());
        }
    }

    function Assigne_task_department()
    {
       
        
            $categoryID = $this->input->post('categoryID');
            $data = $this->db->query("select categoryID from  srp_erp_task_categories WHERE categoryID={$categoryID}")->row_array();
            echo json_encode($data);
            
        
        
    }

    function  get_details_hod()
    {
       
        
            $categoryID = $this->input->post('categoryID');
            $data = $this->db->query("select categoryID,inchageempID from srp_erp_task_categories_assignee  WHERE categoryID={$categoryID} ")->row_array();
            echo json_encode($data);
            
        
        
    }

    function get_department_details(){

        $categoryID = $this->input->post('categoryID');
        $data = $this->db->query("
                SELECT 
                ed.EIdNo, 
                ed.Ename2,
                dep.inchageempID,
                dep.isdefault,
                dep.categoryID
            FROM 
                srp_employeesdetails ed
            JOIN 
            srp_erp_task_categories_assignee dep ON ed.EIdNo = dep.inchageempID 
            WHERE 
                dep.categoryID = ?", array($categoryID))->result_array();
        echo json_encode($data);
    }

    function get_sdays(){
        $totaldays = 0;
        $companyID = $this->common_data['company_data']['company_id'];
        $categoryID = $this->input->post('categoryID');
        $startdate = $this->input->post('startdate');
        $sladays_query = $this->db->query("SELECT sladays FROM srp_erp_task_categories WHERE categoryID = ?", array($categoryID));
        $sladays_result = $sladays_query->result_array();
        if (!empty($sladays_result)) {
            $sladays = $sladays_result[0]['sladays'] ?? 0;
        
            $startdate_obj = new DateTime($startdate);
            $startdate_obj->modify("+$sladays days");
            $duedate = $startdate_obj->format('Y-m-d');

            $calenderDays = $this->db->query("SELECT SUM(IF(DATE(fulldate) IS NOT NULL, 1, 0)) AS nonworkingDays, 
        SUM(IF(DATE(fulldate) IS NOT NULL, 1, 0)) - SUM(IF(weekend_flag = 1 || holiday_flag = 1, 1, 0)) AS workingDays 
        FROM srp_erp_calender WHERE DATE(fulldate) BETWEEN DATE('{$startdate}') AND DATE('{$duedate}') AND companyID = {$companyID}")->row_array();

            $leavetotdays = $calenderDays['nonworkingDays'] - $calenderDays['workingDays'];
            $totaldays = $leavetotdays + $sladays;
        }

        echo json_encode(array('totaldays' => $totaldays));
    }
    
    function fetch_userDropdown_employee()
    {
        $data_arr = array();
        $employee = $this->db->query("SELECT EIdNo, Ename2 FROM srp_employeesdetails")->result_array();
        
        if ($employee) {
            foreach ($employee as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }
        $dropdown_html = form_dropdown('employees[]', $data_arr, '', 'class="form-control select2" id="employeesID"  multiple="" ');
        // Send dropdown HTML as response
        echo $dropdown_html;
    }
    

    function department_hod_filter() {
        $taskID = $this->input->post('taskID');
        $data = $this->db->query("
                SELECT 
                ed.EIdNo, 
                ed.Ename2,
                dep.empID,
                dep.MasterAutoID
            FROM 
            srp_employeesdetails ed
            JOIN 
                srp_erp_task_assignees dep ON ed.EIdNo = dep.empID 
            WHERE 
                dep.MasterAutoID = ?", array($taskID))->result_array();
        echo json_encode($data);
    }
    
   

    function savetaskhod()
    {
        $this->form_validation->set_rules('employees[]', 'Employee', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->save_assigneHod());
        }
    }

    function saveisdefault()
    {
        $this->form_validation->set_rules('categoryID', 'categoryID', 'trim|required');
        $this->form_validation->set_rules('isDefault', 'isdeaut miss', 'trim|required');
        $this->form_validation->set_rules('inchageempID', 'Employee aa', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Task_management_model->saveisdefault());
        }
    }

    
    function load_assigned_employee()
    {
        $categoryID = $this->input->post('categoryID');
        $this->datatables->select("categoryID, inchageempID ,isdefault, Ename2 as employeeName");
        $this->datatables->from('srp_erp_task_categories_assignee');
        $this->datatables->join('srp_employeesdetails', 'srp_erp_task_categories_assignee.inchageempID = srp_employeesdetails.EIdNo', 'LEFT');
        $this->datatables->where('categoryID', $categoryID);
        $this->datatables->add_column('isdefault', '$1', 'isdefault(categoryID,inchageempID,isdefault)');
        $this->datatables->add_column('edit', '<a onclick="delete_userdetail($1, $2)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'categoryID,inchageempID');
        echo $this->datatables->generate();
    }

    function activateUser()
    {
        $value = $this->input->post('value');
        $inchageempID = $this->input->post('inchageempID');

        $this->db->update('srp_erp_task_categories_assignee', array('isdefault' => $value), array('inchageempID' => $inchageempID));
        echo json_encode(array('e', 'Successfully Updated'));
        exit;
    }
    function delete_userdetail()
    {
        echo json_encode($this->Task_management_model->delete_userdetail());
    }


    
    
    function settings_users()
    {
        $sys = $this->input->post('sys');
        $data['masterID'] = $this->input->post('masterID');
        $url = '';
        switch (trim($sys)) {
           


            case 'documentStatus':
                $url = 'system/task_managment/document_status_management';
                break;
            case 'categories':
                $url = 'system/task_managment/categories_management';
                break;

        

            /*            case 'salestarget':
                            $url = 'system/crm/salesTarget_management';
                            break;*/

            default:
                $url = '';
        }

        $this->load->view($url, $data);
    }

  function taskcommentmail(){
        $emailtypeid =  $this->input->post('typeid');

   if ($emailtypeid == 1) {
                $taskid = $this->input->post('taskid');
                $email_query = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_assignees LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID WHERE MasterAutoID = ?", array($taskid));
                $email_results = $email_query->result_array();
        
                $email_query2 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_documentpermissiondetails LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_documentpermissiondetails.empID  WHERE documentAutoID = ?", array($taskid));
                $email_result2 = $email_query2->result_array();
        
                $email_query3 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_task LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_task.createdUserID  WHERE taskID = ?", array($taskid));
                $email_result3 = $email_query3->result_array();

            
                    $combined_emails = array_merge($email_results, $email_result2, $email_result3);
                    $unique_combined_emails = array_unique($combined_emails, SORT_REGULAR);
                    $final_emails1 = array_values($unique_combined_emails);
                
                $taskcode_query = $this->db->query("SELECT documentSystemCode FROM srp_erp_task_task WHERE taskID = ?", array($taskid));
                $taskcode_result = $taskcode_query->row_array();
                $taskcode = isset($taskcode_result['documentSystemCode']) ? $taskcode_result['documentSystemCode'] : '';
                $subject = $this->input->post('subject');
                $createduser =  $this->common_data['current_user'];

                $subjatectid  = $this->db->query("SELECT subject  FROM srp_erp_task_task WHERE taskID = ?", array($taskid));
                $tasksubjecte_result = $subjatectid->row_array();
                $tasksubject = isset($tasksubjecte_result['subject']) ? $tasksubjecte_result['subject'] : '';

                
                foreach ($final_emails1 as $email_data) {
                    $email = $email_data['EEmail'];
                    $employee_name = $email_data['Ename2'];

                $x = 0;
                $params[$x]["companyID"] = current_companyID();
                $params[$x]["documentID"] = '';
                $params[$x]["documentSystemCode"] = $taskcode;
                $params[$x]["documentCode"] = '';
                $params[$x]["type"] = 'task';
                $params[$x]["emailSubject"] = 'ilooops iAsk';
                $params[$x]["empEmail"] = $email;
                $params[$x]["empID"] = current_userID();
                $params[$x]["empName"] = $employee_name;
                $params[$x]["emailBody"] =

                    "<p>Dear {$employee_name} <br /><br /></p>
                    <p>Subject : <strong>{$tasksubject}</strong></p>
                    <p>{$createduser} has added a comment to the iask({$taskcode}) </p>
                
                    <p>&nbsp;</p>";
            
                if (!empty($params)) {
                    $this->email_manual->set_email_detail($params);
                     echo json_encode(['s', 'Successfully Email Sent']);
                }
            }
        }else{
            $taskid = $this->input->post('taskid');
            $email_query = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_assignees LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID WHERE MasterAutoID = ?", array($taskid));
            $email_results = $email_query->result_array();
    
            $email_query2 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_documentpermissiondetails LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_documentpermissiondetails.empID  WHERE documentAutoID = ?", array($taskid));
            $email_result2 = $email_query2->result_array();
    
            $email_query3 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_task LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_task.createdUserID  WHERE taskID = ?", array($taskid));
            $email_result3 = $email_query3->result_array();

        
                $combined_emails = array_merge($email_results, $email_result2, $email_result3);
                $unique_combined_emails = array_unique($combined_emails, SORT_REGULAR);
                $final_emails1 = array_values($unique_combined_emails);
            
            $taskcode_query = $this->db->query("SELECT documentSystemCode FROM srp_erp_task_task WHERE taskID = ?", array($taskid));
            $taskcode_result = $taskcode_query->row_array();
            $taskcode = isset($taskcode_result['documentSystemCode']) ? $taskcode_result['documentSystemCode'] : '';
            $subject = $this->input->post('subject');
            $createduser =  $this->common_data['current_user'];

            $subjatectid  = $this->db->query("SELECT subject  FROM srp_erp_task_task WHERE taskID = ?", array($taskid));
            $tasksubjecte_result = $subjatectid->row_array();
            $tasksubject = isset($tasksubjecte_result['subject']) ? $tasksubjecte_result['subject'] : '';

            
            foreach ($final_emails1 as $email_data) {
                $email = $email_data['EEmail'];
                $employee_name = $email_data['Ename2'];

            $x = 0;
            $params[$x]["companyID"] = current_companyID();
            $params[$x]["documentID"] = '';
            $params[$x]["documentSystemCode"] = $taskcode;
            $params[$x]["documentCode"] = '';
            $params[$x]["type"] = 'task';
            $params[$x]["emailSubject"] = 'ilooops iAsk';
            $params[$x]["empEmail"] = $email;
            $params[$x]["empID"] = current_userID();
            $params[$x]["empName"] = $employee_name;
            $params[$x]["emailBody"] =

                "<p>Dear {$employee_name} <br /><br /></p>
                <p>Subject : <strong>{$tasksubject}</strong></p>
                <p>{$createduser} has attached a document in the iask({$taskcode}) </p>
            
                <p>&nbsp;</p>";
        
            if (!empty($params)) {
                $this->email_manual->set_email_detail($params);
                echo json_encode(['s', 'Successfully Email Sent']);
            }
        }

        }

    }


    function taskmail()
    {
        $emailtypeid  = $this->input->post('emiletypeid');
        $taskid = $this->input->post('taskID');

    if ($emailtypeid == 2) {

        $email_query = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_assignees LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID WHERE MasterAutoID = ?", array($taskid));
        $email_results = $email_query->result_array();
 
        $email_query2 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_documentpermissiondetails LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_documentpermissiondetails.empID  WHERE documentAutoID = ?", array($taskid));
        $email_result2 = $email_query2->result_array();
 
        $email_query3 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_task LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_task.createdUserID  WHERE taskID = ?", array($taskid));
        $email_result3 = $email_query3->result_array();
        // Combine the results of all three queries into one array
            $combined_emails = array_merge($email_results, $email_result2, $email_result3);
            $unique_combined_emails = array_unique($combined_emails, SORT_REGULAR);
            $final_emails1 = array_values($unique_combined_emails);

        
        $taskcode_query = $this->db->query("SELECT documentSystemCode FROM srp_erp_task_task WHERE taskID = ?", array($taskid));
        $taskcode_result = $taskcode_query->row_array();
        $taskcode = isset($taskcode_result['documentSystemCode']) ? $taskcode_result['documentSystemCode'] : '';
    
        $assignees = $this->input->post('employees[]');
        $assignees_names = [];
    
        if (is_array($assignees) && !empty($assignees)) {
            foreach ($assignees as $assignee) {
                $assignee = $this->db->escape($assignee);
    
                $assignename_query = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo = $assignee");
                $assignename_result = $assignename_query->row_array();
    
                // Process the result as needed
                if ($assignename_result) {
                    $assignees_names[] = $assignename_result['Ename2'];
                } else {
                    // echo "No employee found with ID: " . $assignee;
                }
            }
        } else {
            // echo "No assignees provided.";
        }
    
        $assignename_str = implode(', ', $assignees_names);
        $subject = $this->input->post('subject');
        $createduser =  $this->common_data['current_user'];


        
        foreach ($final_emails1 as $email_data) {
            $email = $email_data['EEmail'];
            $employee_name = $email_data['Ename2'];
            $watchertask =    $email_result2['Ename2'];
        $x = 0;
        $params[$x]["companyID"] = current_companyID();
        $params[$x]["documentID"] = '';
        $params[$x]["documentSystemCode"] = $taskcode;
        $params[$x]["documentCode"] = '';
        $params[$x]["type"] = 'task';
        $params[$x]["emailSubject"] = 'ilooops iAsk';
        $params[$x]["empEmail"] = $email;
        $params[$x]["empID"] = current_userID();
        $params[$x]["empName"] = $employee_name;
        $params[$x]["emailBody"] =

            "<p>Dear {$employee_name} <br /><br /></p>
            <p>Subject : <strong>{$subject}</strong></p>
            <p>{$createduser} Created A New Task For {$assignename_str}  ({$taskcode}) </p>
            <p>{$createduser} has assigned {$watchertask} as watcher for the iAsk ({$taskcode}) </p>
        
            <p>&nbsp;</p>";
    
        if (!empty($params)) {
            $this->email_manual->set_email_detail($params);
    
            // echo json_encode(['s', 'Successfully Email Sent']);
        }
    }
        
    }else{
// type 2 emails and chnages sendings 
      
        $taskid = $this->input->post('taskID');
        $statusID = $this->input->post('statusID');
        $progress = $this->input->post('progress');
        $watcher = $this->input->post('multipleemployees[]');
        $assigne = $this->input->post('employees[]');
        $subjects = $this->input->post('subject');
        

        $exsistingrecord_query = $this->db->query("SELECT status, progress, srp_erp_task_assignees.empID FROM srp_erp_task_task LEFT JOIN srp_erp_task_assignees ON srp_erp_task_assignees.MasterAutoID = srp_erp_task_task.taskID WHERE taskID = ?", array($taskid));
        $exsistingrecord = $exsistingrecord_query->result_array();

        $status = $exsistingrecord[0]['status'];
        $statusname  = $this->db->query("SELECT description FROM srp_erp_task_status WHERE statusID = ?", array($status));
        $stausnamequery  = $statusname->row_array();
        $satatustname = isset($stausnamequery['description']) ? $stausnamequery['description'] : '';


        if ($exsistingrecord) {
          
            $status = $exsistingrecord[0]['status'];
            $progress1 = $exsistingrecord[0]['progress'];
        
            if ($statusID != $status) {
                $statusname_query = $this->db->query("SELECT description FROM srp_erp_task_status WHERE statusID = ?", array($statusID));
                $statusname_result = $statusname_query->row_array();
                $newsubject = isset($statusname_result['description']) ? $statusname_result['description'] : '';
            }
        
            if ($progress != $progress1) {
                $newprogress = $progress;
            }


            $multipleemployee_query = $this->db->query("SELECT empID FROM srp_erp_task_documentpermissiondetails WHERE documentAutoID = ?", array($taskid));
            $multipleemployee = $multipleemployee_query->result_array();
            $existing_empIDs = array_column($multipleemployee, 'empID');
            
            $watcher_array = $watcher ? $watcher : []; 
            $empIDs_equal = empty(array_diff($watcher_array, $existing_empIDs)) && empty(array_diff($existing_empIDs, $watcher_array));
            
            if ($empIDs_equal) {
                // var_dump($empIDs_equal); // This will be true (1) if they are equal, false (0) if they are not
            } else {
                // var_dump(1); // The values are not equal, so we output 1
                
                // Find the differing values
                $diff_watchers = array_diff($watcher_array, $existing_empIDs);
                $diff_existing = array_diff($existing_empIDs, $watcher_array);
                
                // Combine all differing values
                $differing_empIDs = array_merge($diff_watchers, $diff_existing);
            
                $watcher_names = [];
                
                if (!empty($differing_empIDs)) {
                    foreach ($differing_empIDs as $watchers) {
                        $watchers = $this->db->escape($watchers);
                        
                        $watchers_query = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo = $watchers");
                        $watchers_result = $watchers_query->row_array();
                        
                        // Process the result as needed
                        if ($watchers_result) {
                            $watcher_names[] = $watchers_result['Ename2'];
                        }
                    }
                    $watcher_str = implode(', ', $watcher_names);
                    
                }
            }


            $assigne_arry = $this->db->query("SELECT empID FROM srp_erp_task_assignees WHERE MasterAutoID = ?", array($taskid));
            $assignese = $assigne_arry->result_array();
            $existing_empIDs = array_column($assignese, 'empID');
            
            $assigne_array = $assigne ? $assigne : []; 
            $empIDs_equal = empty(array_diff($assigne_array, $existing_empIDs)) && empty(array_diff($existing_empIDs, $assigne_array));
            
            if ($empIDs_equal) {
                // var_dump($empIDs_equal); // This will be true (1) if they are equal, false (0) if they are not
            } else {
                // var_dump(1); // The values are not equal, so we output 1
                
                // Find the differing values
                $diff_watchers = array_diff($assigne_array, $existing_empIDs);
                $diff_existing = array_diff($existing_empIDs, $assigne_array);
                
                // Combine all differing values
                $differing_empIDs = array_merge($diff_watchers, $diff_existing);
            
                $assigne_names = [];
                
                if (!empty($differing_empIDs)) {
                    foreach ($differing_empIDs as $assignessss) {
                        $assignessss = $this->db->escape($assignessss);
                        
                        $assigne_query = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo = $assignessss");
                        $assigne_result = $assigne_query->row_array();
                        
                        // Process the result as needed
                        if ($assigne_result) {
                            $assigne_names[] = $assigne_result['Ename2'];
                        }
                    }
                    $assignee_str = implode(', ', $assigne_names);
                    
                }
            }
            
            
        }  

        

        $email_query = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_assignees LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID WHERE MasterAutoID = ?", array($taskid));
        $email_results = $email_query->result_array();

        $email_query2 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_documentpermissiondetails LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_documentpermissiondetails.empID  WHERE documentAutoID = ?", array($taskid));
        $email_result2 = $email_query2->result_array();

        $email_query3 = $this->db->query("SELECT srp_employeesdetails.EEmail,Ename2 FROM srp_erp_task_task LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_task_task.createdUserID  WHERE taskID = ?", array($taskid));
        $email_result3 = $email_query3->result_array();

        // Combine the results of all three queries into one array
            $combined_emails = array_merge($email_results, $email_result2, $email_result3);
            $unique_combined_emails = array_unique($combined_emails, SORT_REGULAR);
            $final_emails = array_values($unique_combined_emails);
    
        $taskcode_query = $this->db->query("SELECT documentSystemCode FROM srp_erp_task_task WHERE taskID = ?", array($taskid));
        $taskcode_result = $taskcode_query->row_array();
        $taskcode = isset($taskcode_result['documentSystemCode']) ? $taskcode_result['documentSystemCode'] : '';
    
        $assignees = $this->input->post('employees[]');
        $assignees_names = [];
    
        if (is_array($assignees) && !empty($assignees)) {
            foreach ($assignees as $assignee) {
                $assignee = $this->db->escape($assignee);
    
                $assignename_query = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo = $assignee");
                $assignename_result = $assignename_query->row_array();
    
                // Process the result as needed
                if ($assignename_result) {
                    $assignees_names[] = $assignename_result['Ename2'];
                } else {
                    // echo "No employee found with ID: " . $assignee;
                }
            }
        } else {
            // echo "No assignees provided.";
        }
       
        $assignename_str = implode(', ', $assignees_names);
        $subject = $this->input->post('subject');
        $currentuser = $this->common_data['current_user'];
            if (!empty($newsubject)) {
                foreach ($final_emails as $email_data) {

                    $email = $email_data['EEmail'];
                    $employee_name = $email_data['Ename2'];

                    $x = 0;
                    $params[$x]["companyID"] = current_companyID();
                    $params[$x]["documentID"] = '';
                    $params[$x]["documentSystemCode"] = $taskcode;
                    $params[$x]["documentCode"] = '';
                    $params[$x]["type"] = 'task';
                    $params[$x]["emailSubject"] = 'ilooops iAsk';
                    $params[$x]["empEmail"] =    $email;
                    $params[$x]["empID"] = current_userID();
                    $params[$x]["empName"] = $employee_name;
                
                    $params[$x]["emailBody"] =

                        "<p>Dear {$employee_name} <br /><br /></p>
                        <p>iAsk subject : <strong>{$subjects}</strong> <br /><br /></p>
                        <p>{$currentuser} Change the iAsk ({$taskcode}) Status $satatustname  to $newsubject </p>
                        <p>&nbsp;</p>";
            
                    if (!empty($params)) {
                        $this->email_manual->set_email_detail($params);
                        // echo json_encode(['s', 'Successfully Email Sent']);
                    }
                }
            }
          
            if (!empty($newprogress)) {
                foreach ($final_emails as $email_data) {

                    $email = $email_data['EEmail'];
                    $employee_name = $email_data['Ename2'];

                    $x = 0;
                    $params[$x]["companyID"] = current_companyID();
                    $params[$x]["documentID"] = '';
                    $params[$x]["documentSystemCode"] = $taskcode;
                    $params[$x]["documentCode"] = '';
                    $params[$x]["type"] = 'task';
                    $params[$x]["emailSubject"] = 'ilooops iAsk';
                    $params[$x]["empEmail"] =   $email;
                    $params[$x]["empID"] = current_userID();
                    $params[$x]["empName"] = $employee_name;
                
                    $params[$x]["emailBody"] =

                        "<p>Dear {$employee_name} <br /><br /></p>
                        <p>iAsk subject : <strong>{$subjects}</strong> <br /><br /></p>
                        <p>{$currentuser} Change the iAsk ({$taskcode})  Progress from $progress1% to $newprogress%  </p>
                        <p>&nbsp;</p>";
            
                    if (!empty($params)) {
                        $this->email_manual->set_email_detail($params);
                        // echo json_encode(['s', 'Successfully Email Sent']);
                    }
                }
        }
        if (!empty($watcher_str)) {
                foreach ($final_emails as $email_data) {

                    $email = $email_data['EEmail'];
                    $employee_name = $email_data['Ename2'];

                    $x = 0;
                    $params[$x]["companyID"] = current_companyID();
                    $params[$x]["documentID"] = '';
                    $params[$x]["documentSystemCode"] = $taskcode;
                    $params[$x]["documentCode"] = '';
                    $params[$x]["type"] = 'task';
                    $params[$x]["emailSubject"] = 'ilooops iAsk';
                    $params[$x]["empEmail"] =    $email;
                    $params[$x]["empID"] = current_userID();
                    $params[$x]["empName"] = $employee_name;
                
                    $params[$x]["emailBody"] =

                        "<p>Dear {$employee_name} <br /><br /></p>
                        <p>iAsk subject : <strong>{$subjects}</strong> <br /><br /></p>
                         <p> {$currentuser} has assigned $watcher_str as watcher for the iAsk ({$taskcode}) </p>
                        <p>&nbsp;</p>";
            
                    if (!empty($params)) {
                        $this->email_manual->set_email_detail($params);
                        // echo json_encode(['s', 'Successfully Email Sent']);
                    }
                }
            }

            if (!empty($assignee_str)) {
                    foreach ($final_emails as $email_data) {
                     var_dump($assignee_str);
                        $email = $email_data['EEmail'];
                        $employee_name = $email_data['Ename2'];
    
                        $x = 0;
                        $params[$x]["companyID"] = current_companyID();
                        $params[$x]["documentID"] = '';
                        $params[$x]["documentSystemCode"] = $taskcode;
                        $params[$x]["documentCode"] = '';
                        $params[$x]["type"] = 'task';
                        $params[$x]["emailSubject"] = 'ilooops iAsk';
                        $params[$x]["empEmail"] =    $email;
                        $params[$x]["empID"] = current_userID();
                        $params[$x]["empName"] = $employee_name;
                    
                        $params[$x]["emailBody"] =
    
                            "<p>Dear {$employee_name} <br /><br /></p>
                            <p>iAsk subject : <strong>{$subjects}</strong> <br /><br /></p>
                            <p> {$currentuser} has Assignee $assignee_str as Assignee for the iAsk({$taskcode})</p>
                            <p>&nbsp;</p>";
                
                        if (!empty($params)) {
                            $this->email_manual->set_email_detail($params);
                            // echo json_encode(['s', 'Successfully Email Sent']);
                        }
                    }
                }

        }
    }




}