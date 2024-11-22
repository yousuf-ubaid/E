<?php

class MFQ_Template_model extends ERP_Model
{
    function save_work_flow_template()
    {
        $this->db->trans_start();
        if (!$this->input->post('workFlowTemplateID')) {
            $this->db->set('description', $this->input->post('description'));
            $this->db->set('workFlowID', $this->input->post('workFlowID'));
            $this->db->set('pageNameLink', $this->input->post('pageNameLink'));
            $result = $this->db->insert('srp_erp_mfq_workflowtemplate');
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Workflow saved Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'workflow saved Successfully.', $last_id);
            }
        } else {
            $data['description'] = $this->input->post('description');
            $data['workFlowID'] = $this->input->post('workFlowID');
            $data['pageNameLink'] = $this->input->post('pageNameLink');
            $this->db->where('workFlowTemplateID', $this->input->post('workFlowTemplateID'));
            $result = $this->db->update('srp_erp_mfq_workflowtemplate', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Workflow saved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Workflow saved Successfully.');

            }
        }
    }

    function save_mfq_template_header()
    {
        if (!$this->input->post('templateMasterID')) {
            $headerdata = $this->db->query("SELECT * FROM srp_erp_mfq_templatemaster WHERE companyID = ".current_companyID()." AND isDefault = 1")->result_array();
            if(empty($headerdata) || $this->input->post('isDefault') == 0) {
                $this->db->set('templateDescription', $this->input->post('description'));
                $this->db->set('industryID', $this->input->post('industryID'));
                $this->db->set('companyID', current_companyID());
                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', current_date(true));
                $this->db->set('isDefault', $this->input->post('isDefault'));
                $result = $this->db->insert('srp_erp_mfq_templatemaster');
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Template header saved Failed ' . $this->db->_error_message());

                } else {
                    $this->db->trans_commit();
                    return array('s', 'Template header saved Successfully.', $last_id);
                }
            }else{
                return array('w', 'There can be only one default template');
            }
        } else {
            $headerdata = $this->db->query("SELECT * FROM srp_erp_mfq_templatemaster WHERE companyID = ".current_companyID()." AND isDefault = 1 AND templateMasterID !={$this->input->post('templateMasterID')}")->result_array();
            if(empty($headerdata) || $this->input->post('isDefault') == 0) {
                $data['templateDescription'] = $this->input->post('description');
                $data['industryID'] = $this->input->post('industryID');
                $data['modifiedPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);//gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data['modifiedUserID'] = current_userID();//$this->session->userdata("username");
                $data['modifiedUserName'] = current_user();//$this->session->userdata("username");
                $data['modifiedDateTime'] = current_date(true);
                $data['isDefault'] = $this->input->post('isDefault');
                $this->db->where('templateMasterID', $this->input->post('templateMasterID'));
                $result = $this->db->update('srp_erp_mfq_templatemaster', $data);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Template header saved Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Template header saved Successfully.', $this->input->post('templateMasterID'));

                }
            }else{
                return array('w', 'There can be only one default template');
            }
        }
    }

    function save_mfq_template_detail()
    {
        if (!$this->input->post('templateDetailID')) {
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_job');
            $this->db->where('workFlowTemplateID', trim($this->input->post('templateMasterID') ?? ''));
            $exist = $this->db->get()->row_array();
            if(!$exist){
                $workFlowTemplateID = explode('|', $this->input->post('workFlowTemplateID'));
                $this->db->set('description', $this->input->post('description'));
                $this->db->set('templateMasterID', $this->input->post('templateMasterID'));
                $this->db->set('workFlowTemplateID', $workFlowTemplateID[0]);
                $this->db->set('workFlowID', $workFlowTemplateID[1]);
                $this->db->set('sortOrder', $this->input->post('sortorder'));
                $this->db->set('companyID', current_companyID());
                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', current_date(true));
                $result = $this->db->insert('srp_erp_mfq_templatedetail');
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Template detail saved Failed ' . $this->db->_error_message());

                } else {
                    $this->db->trans_commit();
                    return array('s', 'Template detail saved Successfully.', $last_id);
                }
            }else{
                return array('e', 'You cannot add this because template has been pulled to job card');
            }
        } else {
            $workFlowTemplateID = explode('|', $this->input->post('workFlowTemplateID'));
            $data['description'] = $this->input->post('description');
            $data['templateMasterID'] = $this->input->post('templateMasterID');
            $data['workFlowTemplateID'] = $workFlowTemplateID[0];
            $data['workFlowID'] = $workFlowTemplateID[1];
            $data['sortOrder'] = $this->input->post('sortorder');
            $data['modifiedPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);//gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $data['modifiedUserID'] = current_userID();//$this->session->userdata("username");
            $data['modifiedUserName'] = current_user();//$this->session->userdata("username");
            $data['modifiedDateTime'] = current_date(true);
            $this->db->where('templateDetailID', $this->input->post('templateDetailID'));
            $result = $this->db->update('srp_erp_mfq_templatedetail', $data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Template detail saved Failed ' . $this->db->_error_message(),$this->input->post('templateDetailID'));
            } else {
                $this->db->trans_commit();
                return array('s', 'Template detail saved Successfully.');

            }
        }
    }

    function edit_work_flow_template()
    {
        $workFlowTemplateID = $this->input->post('workFlowTemplateID');
        $data = $this->db->query("select * from srp_erp_mfq_workflowtemplate where workFlowTemplateID=$workFlowTemplateID")->row_array();
        //echo $this->db->last_query();
        return $data;
    }

    function load_template_master_header()
    {
        $templateMasterID = $this->input->post('templateMasterID');
        $data = $this->db->query("select * from srp_erp_mfq_templatemaster where templateMasterID=$templateMasterID")->row_array();
        //echo $this->db->last_query();
        return $data;
    }

    function load_template_detail()
    {
        $templateMasterID = $this->input->post('templateMasterID');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT srp_erp_mfq_templatedetail.sortOrder,srp_erp_mfq_templatedetail.description,pageNameLink,srp_erp_mfq_templatedetail.workFlowID,documentID,templateDetailID,linkworkFlow,srp_erp_mfq_templatedetail.workFlowTemplateID FROM srp_erp_mfq_templatedetail LEFT JOIN srp_erp_mfq_workflowtemplate ON srp_erp_mfq_workflowtemplate.workFlowTemplateID = srp_erp_mfq_templatedetail.workFlowTemplateID LEFT JOIN srp_erp_mfq_systemworkflowcategory ON srp_erp_mfq_workflowtemplate.workFlowID = srp_erp_mfq_systemworkflowcategory.workFlowID  WHERE companyID = $companyID AND templateMasterID=$templateMasterID ORDER BY srp_erp_mfq_templatedetail.sortOrder")->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function load_work_process_template_detail()
    {
        $templateMasterID = $this->input->post('templateMasterID');
        $jobID = $this->input->post('workProcessID');
        $companyID = current_companyID();

        $manufature_flow = getPolicyValues('MANFL', 'All');
 

        $data = $this->db->query("SELECT srp_erp_mfq_templatedetail.sortOrder, srp_erp_mfq_templatedetail.workFlowTemplateID, srp_erp_mfq_templatedetail.description,pageNameLink,srp_erp_mfq_templatedetail.workFlowID,documentID,ws.status,srp_erp_mfq_templatedetail.templateDetailID,IFNULL(linkworkFlow,0) as linkworkFlow  FROM srp_erp_mfq_templatedetail LEFT JOIN srp_erp_mfq_workflowtemplate ON srp_erp_mfq_workflowtemplate.workFlowTemplateID = srp_erp_mfq_templatedetail.workFlowTemplateID LEFT JOIN srp_erp_mfq_systemworkflowcategory ON srp_erp_mfq_workflowtemplate.workFlowID = srp_erp_mfq_systemworkflowcategory.workFlowID LEFT JOIN (SELECT * FROM srp_erp_mfq_workflowstatus WHERE jobID = $jobID) ws ON ws.templateDetailID = srp_erp_mfq_templatedetail.templateDetailID  WHERE templateMasterID=$templateMasterID ORDER BY srp_erp_mfq_templatedetail.sortOrder")->result_array();
        //echo $this->db->last_query();

        if($manufature_flow == 'Micoda'){

            $responsible_details = $this->db->where('workProcessID',$jobID)->where('companyID',$companyID)->from('srp_erp_mfq_jobworkflow_rperson')->get()->result_array();
            $job_details = $this->db->where('workProcessID',$jobID)->from('srp_erp_mfq_job')->get()->row_array();

            $enable_phases = array();

            $user_id = current_userID();
            $ownerID = $job_details['ownerID'];

            if($ownerID != $user_id){

                foreach($responsible_details as $detail){

                    $res_employees = explode(',',$detail['responsible_emp']);
    
                    if(in_array($user_id,$res_employees)){
                        $enable_phases[] = $detail['phaseID'];
                    }
    
                }
    
    
                foreach($data as $key=>$value){
    
                    if(!in_array($value['templateDetailID'],$enable_phases)){
                        unset($data[$key]);
                    }
                 
                }

            }

         
        }

        return $data;
    }

    function load_custom_work_process_template_detail()
    {
        $templateMasterID = $this->input->post('templateMasterID');
        $jobID = $this->input->post('workProcessID');
        $data = $this->db->query("SELECT srp_erp_mfq_customtemplatedetail.sortOrder, srp_erp_mfq_customtemplatedetail.workFlowTemplateID, srp_erp_mfq_customtemplatedetail.description,pageNameLink,srp_erp_mfq_customtemplatedetail.workFlowID,documentID,ws.status,srp_erp_mfq_customtemplatedetail.templateDetailID,IFNULL(linkworkFlow,0) as linkworkFlow  FROM srp_erp_mfq_customtemplatedetail LEFT JOIN srp_erp_mfq_workflowtemplate ON srp_erp_mfq_workflowtemplate.workFlowTemplateID = srp_erp_mfq_customtemplatedetail.workFlowTemplateID LEFT JOIN srp_erp_mfq_systemworkflowcategory ON srp_erp_mfq_workflowtemplate.workFlowID = srp_erp_mfq_systemworkflowcategory.workFlowID LEFT JOIN (SELECT * FROM srp_erp_mfq_workflowstatus WHERE jobID = $jobID) ws ON ws.templateDetailID = srp_erp_mfq_customtemplatedetail.templateDetailID WHERE templateMasterID=$templateMasterID AND srp_erp_mfq_customtemplatedetail.jobID = {$jobID} ORDER BY srp_erp_mfq_customtemplatedetail.sortOrder")->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function workflow_exist()
    {
        $workFlowTemplateID = explode('|', $this->input->post('workFlowTemplateID'));
        $templateMasterID = $this->input->post('templateMasterID');
        $data = $this->db->query("SELECT * FROM srp_erp_mfq_templatedetail WHERE templateMasterID = $templateMasterID AND workFlowID=$workFlowTemplateID[1]")->row_array();
        return $data;
    }

    function save_mfq_qa_criteria()
    {
        //if (!$this->input->post('templateDetailID')) {
        $this->db->set('description', $this->input->post('description'));
        $this->db->set('templateID', $this->input->post('templateMasterID'));
        $this->db->set('workFlowID', $this->input->post('workFlowID'));
        $this->db->set('sortOrder', $this->input->post('sortOrder'));
        $this->db->set('inputType', $this->input->post('inputType'));
        /* $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
         $this->db->set('createdUserID', current_userID());
         $this->db->set('createdUserName', current_user());
         $this->db->set('createdDateTime', current_date(true));*/
        $result = $this->db->insert('srp_erp_mfq_criteria');
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Criteria saved Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Criteria saved Successfully.', $last_id);
        }
        //} else {
        /* $workFlowTemplateID = explode('|', $this->input->post('workFlowTemplateID'));
         $data['description'] = $this->input->post('description');
         $data['templateMasterID'] = $this->input->post('templateMasterID');
         $data['workFlowTemplateID'] = $workFlowTemplateID[0];
         $data['workFlowID'] = $workFlowTemplateID[1];
         $data['sortOrder'] = $this->input->post('sortorder');
         $data['modifiedPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);//gethostbyaddr($_SERVER['REMOTE_ADDR']);
         $data['modifiedUserID'] = current_userID();//$this->session->userdata("username");
         $data['modifiedUserName'] = current_user();//$this->session->userdata("username");
         $data['modifiedDateTime'] = current_date(true);
         $this->db->where('templateDetailID', $this->input->post('templateDetailID'));
         $result = $this->db->update('srp_erp_mfq_templatedetail', $data);
         if ($this->db->trans_status() === FALSE) {
             $this->db->trans_rollback();
             return array('e', 'Template detail saved Failed ' . $this->db->_error_message());
         } else {
             $this->db->trans_commit();
             return array('s', 'Template detail saved Successfully.');

         }*/
        // }
    }

    function load_qa_specification()
    {
        $workFlowID = $this->input->post('workFlowID');
        $templateMasterID = $this->input->post('templateMasterID');
        $data = $this->db->query("SELECT * FROM srp_erp_mfq_criteria WHERE templateID = $templateMasterID AND workFlowID=$workFlowID")->result_array();
        return $data;
    }

    function attachement_upload()
    {
        $this->load->library('s3');
        $this->db->trans_start();
        $file_name = 'MFQ_' . $this->input->post('documentID') . '_' . time();
        $config['upload_path'] = realpath(APPPATH . '../attachments/MFQ');
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $config['max_size'] = '5120'; // 5 MB
        $config['file_name'] = $file_name;

        /** call s3 library */
        $file = $_FILES['document_file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if(empty($ext)) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
            exit();
        }
        $cc = current_companyCode();
        $folderPath = !empty($cc) ? $cc . '/' : '';
        if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
            $s3Upload = true;
        } else {
            $s3Upload = false;
        }
        /** end of s3 integration */

//        $this->load->library('upload', $config);
//        $this->upload->initialize($config);
//        if (!$this->upload->do_upload("document_file")) {
//            return array('w', 'Upload failed ' . $this->upload->display_errors());
//        } else {
//            $upload_data = $this->upload->data();
            //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
        $data['workFlowID'] = trim($this->input->post('workFlowID') ?? '');
        $data['workProcessID'] = trim($this->input->post('workProcessID') ?? '');
        $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
        $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
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
        $this->db->insert('srp_erp_mfq_workflowattachments', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Upload failed ' . $this->db->_error_message(), 's3Upload' => $s3Upload);
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully Uploaded.', 's3Upload' => $s3Upload);
        }
//        }
    }

    function load_attachments(){
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $workflowID = trim($this->input->post('workFlowID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_workflowattachments');
        $this->db->where('workProcessID',$workProcessID);
        $this->db->where('workflowID',$workflowID);
        $this->db->order_by('attachmentID', 'desc');
        $result = $this->db->get()->result_array();

        if(!empty($result)) {
            $this->load->library('s3');
            foreach ($result as $key => $val) {
                $result[$key]['link'] = $this->s3->createPresignedRequest($val['myFileName'], '1 hour'); // s3 attachment link
            }
        }
        return $result;
    }

    function save_workprocess_machine(){
        $workProcessMachineID = $this->input->post('workProcessMachineID');
        $mfq_faID = $this->input->post('mfq_faID');
        if (!empty($mfq_faID)) {
            try {
                foreach ($mfq_faID as $key => $val){
                    $startTime = trim($this->input->post('startTime')[$key]);
                    $endTime = trim($this->input->post('endTime')[$key]);
                    $format_startdate = null;
                    if (isset($startTime) && !empty($startTime)) {
                        $dteStart = new DateTime($startTime);
                        $format_startdate = $dteStart->format('Y-m-d H:i:s');
                    }
                    $format_enddate = null;
                    if (isset($endTime) && !empty($endTime)) {
                        $dueStart = new DateTime($endTime);
                        $format_enddate = $dueStart->format('Y-m-d H:i:s');
                    }
                    if(!empty($workProcessMachineID[$key])){
                        $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key]);
                        $this->db->set('startTime', $format_startdate);
                        $this->db->set('endTime', $format_enddate);
                        $this->db->set('hoursSpent', $this->input->post('hoursSpent')[$key]);
                        $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('modifiedUserID', current_userID());
                        $this->db->set('modifiedUserName', current_user());
                        $this->db->set('modifiedDateTime', current_date(true));
                        $this->db->where('workProcessMachineID', $workProcessMachineID[$key]);
                        $result = $this->db->update('srp_erp_mfq_workprocessmachines');
                    }else{
                        $this->db->set('workProcessID', $this->input->post('workProcessID'));
                        $this->db->set('mfq_faID', $this->input->post('mfq_faID')[$key]);
                        $this->db->set('workFlowID', $this->input->post('workFlowID'));
                        $this->db->set('startTime', $format_startdate);
                        $this->db->set('endTime', $format_enddate);
                        $this->db->set('hoursSpent', $this->input->post('hoursSpent')[$key]);
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdUserName', current_user());
                        $this->db->set('createdDateTime', current_date(true));
                        $result = $this->db->insert('srp_erp_mfq_workprocessmachines');
                    }
                }
                $this->db->trans_commit();
                return array('s', 'Machine saved Successfully.');

            } catch (Exception $e) {
                $this->db->trans_rollback();
                return array('e', 'Machine saved Failed ' . $this->db->_error_message());
            }
        }
    }

    function save_workprocess_crew(){
        $workProcessCrewID = $this->input->post('workProcessCrewID');
        $crewID = $this->input->post('crewID');
        if (!empty($crewID)) {
            try {
                foreach ($crewID as $key => $val){
                    $startTime = trim($this->input->post('startTime')[$key]);
                    $endTime = trim($this->input->post('endTime')[$key]);
                    $format_startdate = null;
                    if (isset($startTime) && !empty($startTime)) {
                        $dteStart = new DateTime($startTime);
                        $format_startdate = $dteStart->format('Y-m-d H:i:s');
                    }
                    $format_enddate = null;
                    if (isset($endTime) && !empty($endTime)) {
                        $dueStart = new DateTime($endTime);
                        $format_enddate = $dueStart->format('Y-m-d H:i:s');
                    }

                    if(!empty($workProcessCrewID[$key])){
                        $this->db->set('crewID', $this->input->post('crewID')[$key]);
                        $this->db->set('startTime', $format_startdate);
                        $this->db->set('endTime', $format_enddate);
                        $this->db->set('hoursSpent', $this->input->post('hoursSpent')[$key]);
                        $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('modifiedUserID', current_userID());
                        $this->db->set('modifiedUserName', current_user());
                        $this->db->set('modifiedDateTime', current_date(true));
                        $this->db->where('workProcessCrewID', $workProcessCrewID[$key]);
                        $result = $this->db->update('srp_erp_mfq_workprocesscrew');
                    }else{

                        $this->db->set('workProcessID', $this->input->post('workProcessID'));
                        $this->db->set('crewID', $this->input->post('crewID')[$key]);
                        $this->db->set('startTime', $format_startdate);
                        $this->db->set('endTime', $format_enddate);
                        $this->db->set('hoursSpent', $this->input->post('hoursSpent')[$key]);
                        $this->db->set('workFlowID', $this->input->post('workFlowID'));
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdUserName', current_user());
                        $this->db->set('createdDateTime', current_date(true));
                        $result = $this->db->insert('srp_erp_mfq_workprocesscrew');
                    }
                }
                $this->db->trans_commit();
                return array('s', 'Crew saved Successfully.');

            } catch (Exception $e) {
                $this->db->trans_rollback();
                return array('e', 'Crew saved Failed ' . $this->db->_error_message());
            }
        }
    }

    function fetch_crew()
    {
        $dataArr = array();
        $dataArr2 = array();
        $comapnyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT crewID,EIdNo,ECode,Ename1,EpTelephone,EEmail,DesDescription,name as gender,IFNULL(Ename1,"") AS "Match" FROM srp_erp_mfq_crews LEFT JOIN srp_designation ON DesignationID = EmpDesignationId LEFT JOIN srp_erp_gender ON genderID = Gender  WHERE (ECode LIKE "' . $search_string . '" OR Ename1 LIKE "' . $search_string . '") AND srp_erp_mfq_crews.Erp_companyID = '.$comapnyID)->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['ECode'], 'crewID' => $val['crewID'], 'EpTelephone' => $val['EpTelephone'], 'EEmail' => $val['EEmail'], 'DesDescription' => $val['DesDescription'], 'gender' => $val['gender'],'EIdNo'=>$val['EIdNo']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_machine()
    {
        $dataArr = array();
        $dataArr2 = array();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT mfq_faID,faCode,assetDescription,partNumber,c1.description as faCat,c2.description as faSubCat,c3.description as faSubSubCat,CONCAT(IFNULL(assetDescription,""), " (" ,IFNULL(faCode,""),")") AS "Match" FROM srp_erp_mfq_fa_asset_master LEFT JOIN srp_erp_mfq_category c1 ON mfq_faCatID = c1.itemCategoryID LEFT JOIN srp_erp_mfq_category c2 ON mfq_faSubCatID = c2.itemCategoryID LEFT JOIN srp_erp_mfq_category c3 ON mfq_faSubSubCatID = c3.itemCategoryID WHERE (faCode LIKE "' . $search_string . '" OR assetDescription LIKE "' . $search_string . '")')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['faCode'], 'mfq_faID' => $val['mfq_faID'], 'assetDescription' => $val['assetDescription'], 'partNumber' => $val['partNumber'], 'faCat' => $val['faCat'], 'faSubCat' => $val['faSubCat'], 'faSubSubCat' => $val['faSubSubCat']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_workprocess_crew(){
        $convertFormat = convert_date_format_sql();
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $workflowID = trim($this->input->post('workFlowID') ?? '');
        $data = $this->db->query("SELECT srp_erp_mfq_workprocesscrew.crewID,EIdNo,ECode,Ename1,EpTelephone,EEmail,DesDescription,name as gender,workProcessCrewID,workProcessID,DATE_FORMAT(startTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endTime,'" . $convertFormat . " %h:%i %p') AS endTime,hoursSpent FROM srp_erp_mfq_workprocesscrew LEFT JOIN srp_erp_mfq_crews ON srp_erp_mfq_crews.crewID = srp_erp_mfq_workprocesscrew.crewID LEFT JOIN srp_designation ON DesignationID = EmpDesignationId LEFT JOIN srp_erp_gender ON genderID = Gender  WHERE workProcessID = $workProcessID AND workFlowID= $workflowID")->result_array();
        return $data;
    }

    function fetch_workprocess_machine(){
        $convertFormat = convert_date_format_sql();
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $workflowID = trim($this->input->post('workFlowID') ?? '');
        $sql = "SELECT srp_erp_mfq_workprocessmachines.mfq_faID,faCode,assetDescription,partNumber,c1.description as faCat,c2.description as faSubCat,c3.description as faSubSubCat,workProcessID,workProcessMachineID,DATE_FORMAT(startTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endTime,'" . $convertFormat . " %h:%i %p') AS endTime,hoursSpent FROM srp_erp_mfq_workprocessmachines LEFT JOIN srp_erp_mfq_fa_asset_master ON srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_workprocessmachines.mfq_faID LEFT JOIN srp_erp_mfq_category c1 ON mfq_faCatID = c1.itemCategoryID LEFT JOIN srp_erp_mfq_category c2 ON mfq_faSubCatID = c2.itemCategoryID LEFT JOIN srp_erp_mfq_category c3 ON mfq_faSubSubCatID = c3.itemCategoryID WHERE workProcessID = $workProcessID AND workFlowID= $workflowID";
       //echo $sql;
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    function delete_workprocess_crew()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('crewID');
        $this->db->from('srp_erp_mfq_workprocesscrew');
        $this->db->where('workProcessID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_workprocesscrew', array('workProcessCrewID' => $this->input->post('workProcessCrewID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function delete_workprocess_machine()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('mfq_faID');
        $this->db->from('srp_erp_mfq_workprocessmachines');
        $this->db->where('workProcessID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_workprocessmachines', array('workProcessMachineID' => $this->input->post('workProcessMachineID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function link_workflow(){
        $typestart = $this->input->post('typestart');
        $comapnyID = current_companyID();
        $templateMasterID = $this->input->post('templateMasterID');
        $templateDetailID = $this->input->post('templateDetailID');
        if($typestart == 1)
        {
            $result = $this->db->query("SELECT templateDetailID FROM `srp_erp_mfq_templatedetail` WHERE `workFlowID` = 1 AND `templateMasterID` = {$templateMasterID} AND `templateDetailID` < $templateDetailID AND `companyID` = $comapnyID ORDER BY templateDetailID DESC LIMIT 1")->row_array();
            $linkID =   $result['templateDetailID'];
        }else
        {
            $linkID = $this->input->post('linkID');
        }





        if (!empty($templateDetailID)) {
            try {
                $this->db->set('linkWorkFlow', $linkID);
                $this->db->where('templateDetailID', $templateDetailID);
                $result = $this->db->update('srp_erp_mfq_templatedetail');
                $this->db->trans_commit();
                return array('s', 'Link saved Successfully.');

            } catch (Exception $e) {
                $this->db->trans_rollback();
                return array('e', 'Link saved Failed ' . $this->db->_error_message());
            }
        }
    }

    function fetch_workprocess_detail(){
        $data = array();
        $convertFormat = convert_date_format_sql();
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $workflowID = trim($this->input->post('workFlowID') ?? '');
        $sql = "SELECT srp_erp_mfq_workprocesscrew.crewID,srp_erp_mfq_workprocesscrew.actualSpent as actualSpent,EIdNo,ECode,Ename1,EpTelephone,EEmail,DesDescription,name as gender,workProcessCrewID,workProcessID,DATE_FORMAT(startTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endTime,'" . $convertFormat . " %h:%i %p') AS endTime,hoursSpent FROM srp_erp_mfq_workprocesscrew LEFT JOIN srp_erp_mfq_crews ON srp_erp_mfq_crews.crewID = srp_erp_mfq_workprocesscrew.crewID LEFT JOIN srp_designation ON DesignationID = EmpDesignationId LEFT JOIN srp_erp_gender ON genderID = Gender  WHERE workProcessID = $workProcessID AND workFlowID= $workflowID";
        $data["crew"] = $this->db->query($sql)->result_array();

        $sql = "SELECT srp_erp_mfq_workprocessmachines.mfq_faID,faCode,assetDescription,partNumber,c1.description as faCat,c2.description as faSubCat,c3.description as faSubSubCat,workProcessID,workProcessMachineID,DATE_FORMAT(startTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endTime,'" . $convertFormat . " %h:%i %p') AS endTime,hoursSpent FROM srp_erp_mfq_workprocessmachines LEFT JOIN srp_erp_mfq_fa_asset_master ON srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_workprocessmachines.mfq_faID LEFT JOIN srp_erp_mfq_category c1 ON mfq_faCatID = c1.itemCategoryID LEFT JOIN srp_erp_mfq_category c2 ON mfq_faSubCatID = c2.itemCategoryID LEFT JOIN srp_erp_mfq_category c3 ON mfq_faSubSubCatID = c3.itemCategoryID WHERE workProcessID = $workProcessID AND workFlowID= $workflowID";
        //echo $sql;
        $data["machine"] = $this->db->query($sql)->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_workflowattachments');
        $this->db->where('workProcessID',$workProcessID);
        $this->db->where('workflowID',$workflowID);
        $this->db->order_by('attachmentID', 'desc');
        $data["attachment"] = $this->db->get()->result_array();
        if(!empty($data['attachment'])) {
             $this->load->library('s3');
            foreach ($data['attachment'] as $key=>$val) {
                $data['attachment'][$key]['link'] = $this->s3->createPresignedRequest($val['myFileName'], '1 hour'); // s3 attachment link
            }
        }

        return $data;
    }
    function check_link_job_card()
    {
        $templateMasterID = $this->input->post('templateMasterID');
        $templateDetailID = $this->input->post('templateDetailID');

        $this->db->select("*");
        $this->db->from('srp_erp_mfq_templatedetail');
        $this->db->where('workFlowID', 1);
        $this->db->where('templateMasterID', $templateMasterID);
        $this->db->where('templateDetailID <', $templateDetailID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $job = $this->db->get()->result_array();
        return $job;
    }

    function assign_stage_responsible(){

        $responsible = $this->input->post('responsible');
        $workProcessID = $this->input->post('workProcessID');
        $phaseID = $this->input->post('phaseID');
        $companyID = current_companyID();

        $job_details = $this->db->where('workProcessID',$workProcessID)->from('srp_erp_mfq_job')->get()->row_array();
        if($responsible){
            $responsible_empID = join(',',$responsible);
        }else{
            $responsible_empID = '';
        }
       

        $data = array();

        $data['workProcessID'] = $workProcessID;
        $data['responsible_emp'] = $responsible_empID;
        $data['phaseID'] = $phaseID;
        $data['companyID'] = $companyID;
        $data['createdDateTime'] = current_date(true);

        //check ex record
        $ex_record = $this->db->where('workProcessID',$workProcessID)->where('phaseID',$phaseID)->where('companyID',$companyID)->from('srp_erp_mfq_jobworkflow_rperson')->get()->row_array();

        if($ex_record){
            $this->db->where('workProcessID',$workProcessID)->where('phaseID',$phaseID)->where('companyID',$companyID)->update('srp_erp_mfq_jobworkflow_rperson',$data);

        }else{
            $this->db->insert('srp_erp_mfq_jobworkflow_rperson',$data);
        }

        return array('s', 'Saved Successfully.');

    }

    function get_setup_workflow_responsible(){

        $workProcessID = $this->input->post('workProcessID');
        $companyID = current_companyID();

        $phase_details = $this->db->where('workProcessID',$workProcessID)->where('companyID',$companyID)->from('srp_erp_mfq_jobworkflow_rperson')->get()->result_array();

        $base_arr = array();

        foreach($phase_details as $detail){

            $base_arr[$detail['phaseID']] = explode(',',$detail['responsible_emp']);
        }

        return $base_arr;

    }
}
