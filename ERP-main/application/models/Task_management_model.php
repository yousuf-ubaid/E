<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_management_model extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
    }


    
    function save_task_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $company_code = $this->common_data['company_data']['company_code'];
        $status = trim($this->input->post('statusID') ?? '');
        $departmentid =$this->input->post('departmentid');
        $startdate = trim($this->input->post('startdate') ?? '');
        $duedate = trim($this->input->post('duedate') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $relatedAutoIDs = $this->input->post('relatedAutoID');
        $relatedTo = $this->input->post('relatedTo');
        $relatedToSearch = $this->input->post('related_search');
        $linkedFromOrigin = $this->input->post('linkedFromOrigin');
        $userPermission = $this->input->post('userPermission');
        if ($userPermission === null) {
            $userPermission = 0;
        }
        
        $employees = $this->input->post('employees');
        $employeesuserpermission = $this->input->post('multipleemployees');
        $progress = $this->input->post('progress');
        $isclosed = 0;
        $companyid = current_companyID();
        $groupid = trim($this->input->post('groupID') ?? '');
        $closedate = trim($this->input->post('closedate') ?? '');
        $closedateconvert = input_format_date($closedate, $date_format_policy);
        $issubtask = trim($this->input->post('issubtask') ?? '');
        $isclose = $this->db->query("SELECT * FROM `srp_erp_task_status` WHERE statusID = '{$status}' AND documentID = 2")->row_array();

        $startdateconverted = input_format_date($this->input->post('startdate'), $date_format_policy);

        $taskMasterID = trim($this->input->post('taskID') ?? '');
        
        if (($closedateconvert < $startdateconverted) && ($isclose['statusType'] == 1)) {
            return array('e', 'close date Cannot be less than statdate');
            exit();
        }

        $isclosed = 0;
        if ($isclose['statusType'] == 1) {
            $isclosed = 1;

        }
        $opportunityID = 0;
        if (!empty($this->input->post('opportunityID'))) {
            $opportunityID = trim($this->input->post('opportunityID') ?? '');
        }
        $projectID = 0;
        if (!empty($this->input->post('projectID'))) {
            $projectID = trim($this->input->post('projectID') ?? '');
        }
        $pipelineStageID = 0;
        if (!empty($this->input->post('pipelineStageID'))) {
            $pipelineStageID = trim($this->input->post('pipelineStageID') ?? '');
        }

        $format_startdate = null;
        if (isset($startdate) && !empty($startdate)) {
            $dteStart = new DateTime($startdate);
            $format_startdate = $dteStart->format('Y-m-d H:i:s');
        }
        $format_duedate = null;
        if (isset($duedate) && !empty($duedate)) {
            $dueStart = new DateTime($duedate);
            $format_duedate = $dueStart->format('Y-m-d H:i:s');
        }
        $assignTos = $this->input->post('employees');
        $taskMasterID = trim($this->input->post('taskID') ?? '');


        $data['subject'] = trim($this->input->post('subject') ?? '');
        $data['isSubTaskEnabled'] = trim($this->input->post('issubtask') ?? '');
        $data['categoryID'] = trim($this->input->post('categoryID') ?? '');
        $data['status'] = trim($this->input->post('statusID') ?? '');
        $data['isClosed'] = $isclosed;
        $data['opportunityID'] = $opportunityID;
        $data['projectID'] = $projectID;
        $data['departmentID'] = $departmentid;
        $data['pipelineStageID'] = $pipelineStageID;
        $data['starDate'] = $format_startdate;
        $data['DueDate'] = $format_duedate;
        $data['Priority'] = trim($this->input->post('priority') ?? '');
        $data['visibility'] = trim($this->input->post('userPermission') ?? '');
        $data['description'] = trim_desc($this->input->post('description'));
        if ($isclose['statusType'] == 1) {
            $data['completedDate'] = $closedateconvert;
            $data['completedBy'] = $this->common_data['current_userID'];
        } else {
            $data['completedDate'] = null;
            $data['completedBy'] = null;
        }

        if ($taskMasterID) {
            $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_task_subtasks where taskID = $taskMasterID ")->row_array();
            $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_task_subtasks where taskID = $taskMasterID  AND `status` = 2")->row_array();

            if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
                $subtaskpercentage = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
            } else {
                $subtaskpercentage = 0;
            }
            if ($issubtask == 1) {
                $data['progress'] = trim($this->input->post('progress') ?? '');
            } else {
                // $data['progress'] = trim($this->input->post('progress') ?? '');
            }


            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->delete('srp_erp_task_assignees', array('documentID' => 2, 'MasterAutoID' => $taskMasterID));
            if (isset($assignTos) && !empty($assignTos)) {
                foreach ($assignTos as $val) {

                    /*                    $this->db->select('empID,MasterAutoID,companyID');
                                        $this->db->from('srp_erp_task_assignees');
                                        $this->db->where('empID', $val);
                                        $this->db->where('MasterAutoID', $taskMasterID);
                                        $this->db->where('companyID', $companyID);
                                        $this->db->where('documentID', 2);
                                        $order_detail = $this->db->get()->row_array();
                                        $employeeDetail = fetch_employeeNo($order_detail['empID']);
                                        if (!empty($order_detail)) {
                                            return array('w', 'Employee : ' . $employeeDetail['ECode'] . ' ' . $employeeDetail['Ename2'] . '  already exists.');
                                        }*/

                    $data_detail['documentID'] = 2;
                    $data_detail['MasterAutoID'] = $taskMasterID;
                    $data_detail['empID'] = $val;
                    $data_detail['companyID'] = $companyID;
                    $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                    $data_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_task_assignees', $data_detail);
                }

            }
            
                   
            $catgoid = trim($this->input->post('categoryID') ?? '');
            $categoryname = $this->db->query("SELECT description FROM srp_erp_task_categories WHERE   categoryID = $catgoid")->row_array()['description'];

            $statud = trim($this->input->post('statusID') ?? '');
            $statuname = $this->db->query("SELECT description FROM srp_erp_task_status WHERE  statusID = $statud")->row_array()['description'];

                 
          
            
            $existingData = $this->db->get_where('srp_erp_document_change_history', array('documentMasterID' => $taskMasterID))->row_array();


            $visibility = $this->input->post('multipleemployees'); 
            $visbitlityemployeenames = array();
            
            if (!empty($visibility)) {
                foreach ($visibility as $empid) {
                    $employee = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo = $empid")->row_array();
                    
                    if ($employee) {
                        $visbitlityemployeenames[] = $employee['Ename2'];
                    }
                }
            }
            
            $visbitlityemployeenames = implode(', ', $visbitlityemployeenames);
            





            $empids = $this->input->post('employees'); 
            $employeenames = array();
            foreach ($empids as $empid) {
                
                $employee = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE  EIdNo = $empid")->row_array();
                
                if ($employee) {
                    $employeenames[] = $employee['Ename2'];
                }
            }
            $employeename = implode(', ', $employeenames); 

            if ($existingData) {
                $historyDetails = array(
                    'subject' => trim($this->input->post('subject') ?? ''),
                    'taskID' => $taskMasterID,
                    'categoryID' => $categoryname,
                    'description' => trim_desc($this->input->post('description')),
                    'progress' => trim($this->input->post('progress') ?? ''),
                    'priority' => trim($this->input->post('priority') ?? ''),
                    'statusID' => $statuname,
                    'startdate' => trim($this->input->post('startdate') ?? ''),
                    'duedate' => trim($this->input->post('duedate') ?? ''),
                    'closedate' => trim($this->input->post('closedate') ?? ''),
                    'employees' => trim_desc($employeename),
                    'userPermission' => $visbitlityemployeenames,
                );
            
                $existingHistoryDetails = json_decode($existingData['historyDetails'], true);
            
                if (isset($existingHistoryDetails['changes'])) {
                    $changes = $existingHistoryDetails['changes'];
                } else {
                    $changes = array();
                }
            
                foreach ($historyDetails as $key => $value) {
                    if (array_key_exists($key, $existingHistoryDetails) && $existingHistoryDetails[$key] != $historyDetails[$key]) {
                        $changeLog = array(
                            'old_value' => $existingHistoryDetails[$key],
                            'new_value' => $historyDetails[$key],
                            'changedBy' => $this->common_data['current_user'],
                            'changedDateTime' => $this->common_data['current_date']
                        );
            
                        // Append the change log as a numbered index under the main field
                        $changes[$key][] = $changeLog;
                        $existingHistoryDetails[$key] = $historyDetails[$key];
                    }
                }
            
                if (!empty($changes)) {
                    $existingHistoryDetails['changes'] = $changes;
                    $historyDetailsJSON = json_encode($existingHistoryDetails);
                    $historyData = array(
                        'historyDetails' => $historyDetailsJSON
                    );
            
                    $this->db->where('documentMasterID', $taskMasterID);
                    $update = $this->db->update('srp_erp_document_change_history', $historyData);
                }
            }
            if ($existingData) {
                $historyDetails = array(
                    'subject' => trim($this->input->post('subject') ?? ''),
                    'taskID' => $taskMasterID,
                    'categoryID' => $categoryname,
                    'description' => trim_desc($this->input->post('description')),
                    'progress' => trim($this->input->post('progress') ?? ''),
                    'priority' => trim($this->input->post('priority') ?? ''),
                    'statusID' => $statuname,
                    'startdate' => trim($this->input->post('startdate') ?? ''),
                    'duedate' => trim($this->input->post('duedate') ?? ''),
                    'closedate' => trim($this->input->post('closedate') ?? ''),
                    'employees' => trim_desc($employeename),
                    'userPermission' => $visbitlityemployeenames,
                );
            
                $existingHistoryDetails = json_decode($existingData['historyDetails'], true);
            
                if (isset($existingHistoryDetails['changes'])) {
                    $changes = $existingHistoryDetails['changes'];
                } else {
                    $changes = array();
                }
            
                foreach ($historyDetails as $key => $value) {
                    if (array_key_exists($key, $existingHistoryDetails) && $existingHistoryDetails[$key] != $historyDetails[$key]) {
                        $changeLog = array(
                            'old_value' => $existingHistoryDetails[$key],
                            'new_value' => $historyDetails[$key],
                            'changedBy' => $this->common_data['current_user'],
                            'changedDateTime' => $this->common_data['current_date']
                        );
            
                        // Append the change log as a numbered index under the main field
                        $changes[$key][] = $changeLog;
                        $existingHistoryDetails[$key] = $historyDetails[$key];
                    }
                }
            
                if (!empty($changes)) {
                    $existingHistoryDetails['changes'] = $changes;
                    $historyDetailsJSON = json_encode($existingHistoryDetails);
                    $historyData = array(
                        'historyDetails' => $historyDetailsJSON
                    );
            
                    $this->db->where('documentMasterID', $taskMasterID);
                    $update = $this->db->update('srp_erp_document_change_history', $historyData);
                }
            
                        
            

            } else {
                // If documentMasterID doesn't exist, create new history details
                $historyDetails = array(
                    'subject' => trim($this->input->post('subject') ?? ''),
                    'taskID' => $taskMasterID,
                    'categoryID' => $categoryname,
                    'description' => trim_desc($this->input->post('description')),
                    'progress' => trim($this->input->post('progress') ?? ''),
                    'priority' => trim($this->input->post('priority') ?? ''),
                    'statusID' => $statuname,
                    'startdate' => trim($this->input->post('startdate') ?? ''),
                    'duedate' => trim($this->input->post('duedate') ?? ''),
                    // 'issubtask' => trim($this->input->post('issubtask') ?? ''),
                    'closedate' => trim($this->input->post('closedate') ?? ''),
                    'employees' => trim_desc($employeename),
                    'userPermission' => $visbitlityemployeenames,
                    'createdUserName' => $this->common_data['current_user'],
                    'createdDateTime' => $this->common_data['current_date']
                );

                // Encode the historyDetails array into JSON
                $historyDetailsJSON = json_encode($historyDetails);

                // Prepare data for insertion
                $historyData = array(
                    'documentID' => 2, // Assuming 2 is the documentID for tasks
                    'documentMasterID' => $taskMasterID,
                    'historyDetails' => $historyDetailsJSON
                );

                // Insert data into srp_erp_document_change_history
                $insert = $this->db->insert('srp_erp_document_change_history', $historyData);

            }


    

            if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                $this->db->delete('srp_erp_task_link', array('documentID' => 2, 'MasterAutoID' => $taskMasterID));
                foreach ($relatedAutoIDs as $key => $itemAutoID) {
                    $data_link['documentID'] = 2;
                    $data_link['MasterAutoID'] = $taskMasterID;
                    $data_link['relatedDocumentID'] = $relatedTo[$key];
                    $data_link['relatedDocumentMasterID'] = $itemAutoID;
                    $data_link['searchValue'] = $relatedToSearch[$key];
                    $data_link['originFrom'] = $linkedFromOrigin[$key];
                    $data_link['companyID'] = $companyID;
                    $data_link['createdUserGroup'] = $this->common_data['user_group'];
                    $data_link['createdPCID'] = $this->common_data['current_pc'];
                    $data_link['createdUserID'] = $this->common_data['current_userID'];
                    $data_link['createdUserName'] = $this->common_data['current_user'];
                    $data_link['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_task_link', $data_link);
                }
            }


            $this->db->where('taskID', $taskMasterID);
            $update = $this->db->update('srp_erp_task_task', $data);
            if ($update) {
                $this->db->delete('srp_erp_task_documentpermission', array('documentID' => 2, 'documentAutoID' => $taskMasterID));
                $this->db->delete('srp_erp_task_documentpermissiondetails', array('documentID' => 2, 'documentAutoID' => $taskMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 2;
                $permission_master['documentAutoID'] = $taskMasterID;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_task_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employeesuserpermission) && !empty($employeesuserpermission)) {
                            foreach ($employeesuserpermission as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 2;
                                $permission_detail['documentAutoID'] = $taskMasterID;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_task_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Task Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Task Updated Successfully.', $taskMasterID);
            }
        } else {

            if ($issubtask != 1) {
                $data['progress'] = trim($this->input->post('progress') ?? '');
            }

            $this->load->library('sequence');
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-TSK');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_task_task ")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-TSK';
            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_task_task', $data);
            $last_id = $this->db->insert_id();


            if ($last_id) {
                if (isset($assignTos) && !empty($assignTos)) {
                    foreach ($assignTos as $val) {
                        $data_detail['documentID'] = 2;
                        $data_detail['MasterAutoID'] = $last_id;
                        $data_detail['empID'] = $val;
                        $data_detail['companyID'] = $companyID;
                        $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_detail['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_task_assignees', $data_detail);
                    }

                }
                if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                    foreach ($relatedAutoIDs as $key => $itemAutoID) {
                        $data_link['documentID'] = 2;
                        $data_link['MasterAutoID'] = $last_id;
                        $data_link['relatedDocumentID'] = $relatedTo[$key];
                        $data_link['relatedDocumentMasterID'] = $itemAutoID;
                        $data_link['searchValue'] = $relatedToSearch[$key];
                        $data_link['originFrom'] = $linkedFromOrigin[$key];
                        $data_link['companyID'] = $companyID;
                        $data_link['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link['createdPCID'] = $this->common_data['current_pc'];
                        $data_link['createdUserID'] = $this->common_data['current_userID'];
                        $data_link['createdUserName'] = $this->common_data['current_user'];
                        $data_link['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_task_link', $data_link);
                    }
                }
            }
            if ($userPermission == 2) {
                $permission_master['permissionValue'] = $this->common_data['current_userID'];
            } else if ($userPermission == 3) {
                $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
            }
            $permission_master['documentID'] = 2;
            $permission_master['documentAutoID'] = $last_id;
            $permission_master['permissionID'] = $userPermission;
            $permission_master['companyID'] = $companyID;
            $permission_master['createdUserGroup'] = $this->common_data['user_group'];
            $permission_master['createdPCID'] = $this->common_data['current_pc'];
            $permission_master['createdUserID'] = $this->common_data['current_userID'];
            $permission_master['createdUserName'] = $this->common_data['current_user'];
            $permission_master['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_task_documentpermission', $permission_master);
            $permission_id = $this->db->insert_id();
            if ($userPermission == 4) {
                if ($permission_id) {
                    if (isset($employeesuserpermission) && !empty($employeesuserpermission)) {
                        foreach ($employeesuserpermission as $val) {
                            $permission_detail['documentPermissionID'] = $permission_id;
                            $permission_detail['documentID'] = 2;
                            $permission_detail['documentAutoID'] = $last_id;
                            $permission_detail['empID'] = $val;
                            $permission_detail['companyID'] = $companyID;
                            $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                            $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                            $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                            $permission_detail['createdUserName'] = $this->common_data['current_user'];
                            $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_task_documentpermissiondetails', $permission_detail);
                        }
                    }
                }
            }
        }
        $dataupdatetask['progress'] =  trim($this->input->post('progress') ?? '');
        $this->db->where('taskID', $last_id);
        $this->db->update('srp_erp_task_task', $dataupdatetask);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Task Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Task Saved Successfully.', $last_id);

        }


    }

    function save_history_details() {
        $taskMasterID = trim($this->input->post('taskID') ?? '');
        $catgoid = trim($this->input->post('categoryID') ?? '');
        $categoryname = $this->db->query("SELECT description FROM srp_erp_task_categories WHERE  categoryID = $catgoid")->row_array()['description'];
        $statud = trim($this->input->post('statusID') ?? '');
        $statuname = $this->db->query("SELECT description FROM srp_erp_task_status WHERE  statusID = $statud")->row_array()['description'];
        $existingData = $this->db->get_where('srp_erp_document_change_history', array('documentMasterID' => $taskMasterID))->row_array();
        $visibility = $this->input->post('multipleemployees'); 
        $visbitlityemployeenames = array();
        
        if (!empty($visibility)) {
            foreach ($visibility as $empid) {
                $employee = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo = $empid")->row_array();
                
                if ($employee) {
                    $visbitlityemployeenames[] = $employee['Ename2'];
                }
            }
        }
        
        $visbitlityemployeenames = implode(', ', $visbitlityemployeenames);

        $savetype = $this->input->post('savetype');
        if ($savetype == 1){
            $taskMasterID = trim($this->input->post('taskID') ?? '');
            $gettaskepid = $this->db->query("SELECT empID FROM srp_erp_task_assignees WHERE MasterAutoID = $taskMasterID")->result_array();
        
            $employeenames = array();
            foreach ($gettaskepid as $empid_array) {
                $empid = $empid_array['empID'];
                $employee = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE EIdNo = $empid")->row_array();
        
                if ($employee) {
                    $employeenames[] = $employee['Ename2'];
                }
            }
            $employeename = implode(', ', $employeenames); 

        } else {
            $empids = $this->input->post('employees'); 
            $employeenames = array();
            foreach ($empids as $empid) {
                
                $employee = $this->db->query("SELECT Ename2 FROM srp_employeesdetails WHERE  EIdNo = $empid")->row_array();
                
                if ($employee) {
                    $employeenames[] = $employee['Ename2'];
                }
            }
            $employeename = implode(', ', $employeenames); 

        }

        if ($existingData) {
            $historyDetails = array(
                'subject' => trim($this->input->post('subject') ?? ''),
                'taskID' => $taskMasterID,
                'categoryID' => $categoryname,
                'description' => trim_desc($this->input->post('description')),
                'progress' => trim($this->input->post('progress') ?? ''),
                'priority' => trim($this->input->post('priority') ?? ''),
                'statusID' => $statuname,
                'startdate' => trim($this->input->post('startdate') ?? ''),
                'duedate' => trim($this->input->post('duedate') ?? ''),
                'closedate' => trim($this->input->post('closedate') ?? ''),
                'employees' => trim_desc($employeename),
                'userPermission' => $visbitlityemployeenames,
            );
        
            $existingHistoryDetails = json_decode($existingData['historyDetails'], true);

            $changes = $existingHistoryDetails['changes'] ?? [];
        
            foreach ($historyDetails as $key => $value) {
                if (array_key_exists($key, $existingHistoryDetails) && $existingHistoryDetails[$key] != $value) {
                    $changeLog = array(
                        'old_value' => $existingHistoryDetails[$key],
                        'new_value' => $value,
                        'changedBy' => $this->common_data['current_user'],
                        'changedDateTime' => $this->common_data['current_date']
                    );
        
                    // Append the change log as a numbered index under the main field
                    $changes[$key][] = $changeLog;
                    $existingHistoryDetails[$key] = $value;
                }
            }
        
            if (!empty($changes)) {
                $existingHistoryDetails['changes'] = $changes;
                $historyDetailsJSON = json_encode($existingHistoryDetails);
                $historyData = array(
                    'historyDetails' => $historyDetailsJSON
                );
        
                $this->db->where('documentMasterID', $taskMasterID);
                $this->db->update('srp_erp_document_change_history', $historyData);

                return ['s', 'Success'];
            }
        } else {
            // If documentMasterID doesn't exist, create new history details
            $historyDetails = array(
                'subject' => trim($this->input->post('subject') ?? ''),
                'taskID' => $taskMasterID,
                'categoryID' => $categoryname,
                'description' => trim_desc($this->input->post('description')),
                'progress' => trim($this->input->post('progress') ?? ''),
                'priority' => trim($this->input->post('priority') ?? ''),
                'statusID' => $statuname,
                'startdate' => trim($this->input->post('startdate') ?? ''),
                'duedate' => trim($this->input->post('duedate') ?? ''),
                'closedate' => trim($this->input->post('closedate') ?? ''),
                'employees' => trim_desc($employeename),
                'userPermission' => $visbitlityemployeenames,
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => $this->common_data['current_date']
            );

            // Encode the historyDetails array into JSON
            $historyDetailsJSON = json_encode($historyDetails);

            // Prepare data for insertion
            $historyData = array(
                'documentID' => 2, // Assuming 2 is the documentID for tasks
                'documentMasterID' => $taskMasterID,
                'historyDetails' => $historyDetailsJSON
            );

            // Insert data into srp_erp_document_change_history
           $this->db->insert('srp_erp_document_change_history', $historyData);

            return ['s', 'Success'];

        }

    }

    function fetch_tasks_employee_detail()
    {
        $this->db->select('AssingeeID,empID,MasterAutoID');
        $this->db->from('srp_erp_task_assignees');
        $this->db->where('MasterAutoID', $this->input->post('MasterAutoID'));
        $this->db->where('documentID', 2);
        return $this->db->get()->result_array();
    }

    
    function load_task_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $taskid = $this->input->post('taskID');
        $empid = current_userID();
        $this->db->select('*,DATE_FORMAT(starDate,\'' . $convertFormat . ' \') AS starDate,DATE_FORMAT(DueDate,\'' . $convertFormat . ' \') AS DueDate,DATE_FORMAT(completedDate,\'' . $convertFormat . '\') AS completedDatecovverted');
        $this->db->where('taskID', $this->input->post('taskID'));
        $data['header'] = $this->db->get('srp_erp_task_task')->row_array();

        $this->db->select('*');
        $this->db->where('documentID', 2);
        $this->db->where('MasterAutoID', $this->input->post('taskID'));
        $this->db->from('srp_erp_task_link');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('permissionID,permissionValue,srp_erp_task_documentpermissiondetails.empID');
        $this->db->from('srp_erp_task_documentpermission');
        $this->db->join('srp_erp_task_documentpermissiondetails', 'srp_erp_task_documentpermission.documentPermissionID = srp_erp_task_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_task_documentpermission.documentID', 2);
        $this->db->where('srp_erp_task_documentpermission.documentAutoID', $this->input->post('taskID'));
        $data['permission'] = $this->db->get()->result_array();

        $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_task_subtasks where taskID = $taskid")->row_array();
        $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_task_subtasks where taskID = $taskid  AND `status` = 2")->row_array();

        if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
            $data['subtaskpercentage'] = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
        } else {
            $data['subtaskpercentage'] = 0;
        }


        $assignpermission = $this->db->query("SELECT empID FROM `srp_erp_task_assignees` WHERE  documentID = 2 AND MasterAutoID = '{$taskid}' AND empID = '{$empid}'")->row_array();
        if (!empty($assignpermission)) {
            $data['assignpermission'] = array(
                'assigpermission' => 1,
                'empID' => $assignpermission['empID']
            );
        } else {
            $data['assignpermission'] = array(
                'assigpermission' => 0
            );
        }
        
        $permissionid = $this->db->query("SELECT permissionID FROM `srp_erp_task_documentpermission` where documentID = 2 AND documentAutoID = '{$taskid}'")->row_array();
        if (!empty($permissionid)) {
            $data['permissionid'] = 1;
        } else {
            $data['permissionid'] = 0;
        }

        return $data;
    }


    function delete_task()
    {
        $currentuser = current_userID();
        $company_id = current_companyID();
        $taskid = trim($this->input->post('taskID') ?? '');
        $createduser = $this->db->query("SELECT createdUserID FROM `srp_erp_task_task` where companyID = $company_id and taskID = $taskid")->row_array();
        $issuperadmin = crm_isSuperAdmin();
        $isgroupadmin = crm_isGroupAdmin();
        if ($issuperadmin['isSuperAdmin'] == 1 || $createduser['createdUserID'] == $currentuser || $isgroupadmin['adminYN'] == 1) {
            $this->db->delete('srp_erp_task_task', array('taskID' => trim($this->input->post('taskID') ?? '')));
            $this->db->delete('srp_erp_task_assignees', array('MasterAutoID' => trim($this->input->post('taskID') ?? '')));
            return array('s', 'Task deleted successfully');
        } else {
            return array('w', 'You do not have the permission to delete');
        }

    }

    
    function fetch_document_relate_search()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";
        $related_type = $_GET['t'];

        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';

        if (!empty($related_type)) {
            switch ($related_type) {
                case 2:
                    $data = $this->db->query('SELECT taskID,subject AS "Match" FROM srp_erp_task_task WHERE companyID = "' . $companyID . '" AND subject LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['taskID'], 'DoucumentAutoID' => $val['taskID'], 'relatedDoucumentID' => 2);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 4:
                    $data = $this->db->query('SELECT opportunityID,opportunityName AS "Match" FROM srp_erp_crm_opportunity WHERE companyID = "' . $companyID . '" AND opportunityName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['opportunityID'], 'DoucumentAutoID' => $val['opportunityID'], 'relatedDoucumentID' => 4);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 5:
                    $data = $this->db->query('SELECT leadID,CONCAT(firstName," ", lastName) AS "Match" FROM srp_erp_crm_leadmaster WHERE companyID = "' . $companyID . '" AND firstName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['leadID'], 'DoucumentAutoID' => $val['leadID'], 'relatedDoucumentID' => 5);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 6:
                    $data = $this->db->query('SELECT contactID,CONCAT(firstName," ", lastName) AS "Match" FROM srp_erp_crm_contactmaster WHERE companyID = "' . $companyID . '" AND firstName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['contactID'], 'DoucumentAutoID' => $val['contactID'], 'relatedDoucumentID' => 6);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 7:
                    echo "Your favorite color is green!";
                    break;
                case 8:
                    $data = $this->db->query('SELECT organizationID,Name AS "Match" FROM srp_erp_crm_organizations WHERE companyID = "' . $companyID . '" AND Name LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['organizationID'], 'DoucumentAutoID' => $val['organizationID'], 'relatedDoucumentID' => 8);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 9:
                    $data = $this->db->query('SELECT projectID,projectName AS "Match" FROM srp_erp_crm_project WHERE companyID = "' . $companyID . '" AND projectName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['projectID'], 'DoucumentAutoID' => $val['projectID'], 'relatedDoucumentID' => 9);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                default:
                    return '';
            }

        }

    }

    function load_taskRelated_fromLead()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('leadID,CONCAT(firstName, \' \', lastName) as fullname');
        $this->db->where('leadID', $this->input->post('leadID'));
        $this->db->from('srp_erp_crm_leadmaster');
        return $this->db->get()->row_array();

    }

    
    function load_taskRelated_fromOpportunity()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('opportunityID,opportunityName as fullname');
        $this->db->where('opportunityID', $this->input->post('opportunityID'));
        $this->db->from('srp_erp_crm_opportunity');
        return $this->db->get()->row_array();

    }


    
    function load_taskRelated_fromProject()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('projectID,projectName as fullname');
        $this->db->where('projectID', $this->input->post('projectID')[0]);
        $this->db->from('srp_erp_crm_project');
        return $this->db->get()->row_array();

    }


    
    function AddSubTaskDetail()
    {
        $date_format_policy = date_format_policy();
        $taskdescription = $this->input->post('Taskdescription');
        $esttaskstartdate = $this->input->post('estsubtaskdate');
        $esttaskenddate = $this->input->post('estsubtaskdateend');
        $indays = $this->input->post('indays');
        $inhrs = $this->input->post('inhrs');
        $inmins = $this->input->post('inmns');
        $assignTos = $this->input->post('assign');
        $Taskid = $this->input->post('Taskid');
        $companyid = current_companyID();
        $crmtask = $this->db->query("SELECT *,DATE_FORMAT(starDate, '%Y-%m-%d') as crmtaskstartdate,DATE_FORMAT(DueDate, '%Y-%m-%d') as crmtasksdeuedate FROM `srp_erp_task_task` WHERE  taskID = '{$Taskid}'")->row_array();
        $Taskdescription = $this->input->post('Taskdescription');
        foreach ($Taskdescription as $key => $val) {
            $startdate = input_format_date(trim($this->input->post('estsubtaskdate')[$key]), $date_format_policy);
            $estsubtaskdateend = input_format_date(trim($this->input->post('estsubtaskdateend')[$key]), $date_format_policy);
            if ($startdate > $estsubtaskdateend) {
                return array('e', 'Est. Start Date cannot be greater than Est. End Date');
                exit();
            }

            if ($startdate < $crmtask['crmtaskstartdate']) {
                return array('e', 'Est. Start Date cannot be less than Task Start Date.');
                exit();
            }

            
            if ($startdate > $crmtask['crmtasksdeuedate']) {
                return array('e', 'Est. Start Date cannot be greater than Task Due Date.');
                exit();
            }

            if ($estsubtaskdateend > $crmtask['crmtasksdeuedate']) {
                return array('e', 'Estimated End Date Cannot be greater than Task Due Date.');
                exit();
            }
            if ($startdate > $estsubtaskdateend) {
                return array('e', 'Est. Start Date cannot be greater than Est. End Date');
                exit();
            }

        }

        foreach ($taskdescription as $key => $val) {


            $data['taskDescription'] = $val;
            $data['taskID'] = $Taskid;
            $data['startDate'] = input_format_date($esttaskstartdate[$key], $date_format_policy);
            $data['endDate'] = input_format_date($esttaskenddate[$key], $date_format_policy);
            $data['estimatedDays'] = $indays[$key] ?? 0;
            $data['estimatedHours'] = ($inhrs[$key] * 60) + ($inmins[$key]);
            $data['companyID'] = $companyid;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_task_subtasks', $data);
            $last_id_sub = $this->db->insert_id();
            $assinees = ((explode(',', $assignTos[$key])));
            foreach ($assinees as $val) {
                $datasub['empID'] = $val;
                $datasub['documentID'] = 10;
                $datasub['MasterAutoID'] = $last_id_sub;
                $datasub['companyID'] = $companyid;
                $datasub['createdUserGroup'] = $this->common_data['user_group'];
                $datasub['createdPCID'] = $this->common_data['current_pc'];
                $datasub['createdUserID'] = $this->common_data['current_userID'];
                $datasub['createdDateTime'] = $this->common_data['current_date'];
                $datasub['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_task_assignees', $datasub);
            }
        }


        $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_task_subtasks where taskID = $Taskid ")->row_array();
        $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_task_subtasks where taskID = $Taskid  AND `status` = 2")->row_array();

        $dataupdatetask['progress'] =  trim($this->input->post('progress') ?? '');
        $this->db->where('taskID', $Taskid);
        $this->db->update('srp_erp_task_task', $dataupdatetask);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sub Task :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sub Task Added Successfully.', $last_id_sub,$dataupdatetask['progress']);
        }
    }


    
    function load_subtask_details()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $subtaskid = $this->input->post('subtaskid');
        $empid = current_userID();
        $assignpermission = $this->db->query("SELECT empID FROM `srp_erp_task_assignees` WHERE  documentID = 10 AND MasterAutoID = '{$subtaskid}' AND empID = '{$empid}'")->row_array();
        if (!empty($assignpermission)) {
            $data['assignpermission'] = 1;
        } else {
            $data['assignpermission'] = 0;
        }

        return $data;
    }


    
    function save_sub_task_Enable()
    {
        $subtaskid = trim($this->input->post('subtaskid') ?? '');
        $taskid = trim($this->input->post('taskid') ?? '');
        $companyid = current_companyID();
        $currentuser = current_userID();
        $this->db->trans_start();
        $subtask = $this->db->query("SELECT `status` FROM `srp_erp_task_subtasks` where  companyID = $companyid ANd subTaskID = $subtaskid And taskID = $taskid")->row_array();
        if ($subtask['status'] == 2) {
            return array('w', 'Sub Task Already Completed, cannot be start.');
        } else {
            $data['empID'] = $currentuser;
            $data['subTaskID'] = $subtaskid;
            $data['taskID'] = $taskid;
            $data['status'] = 1;
            $data['startDatetime'] = format_date_mysql_datetime();
            $data['companyID'] = $companyid;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_task_subtasksessions', $data);
            $last_id = $this->db->insert_id();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Sub task failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Sub Task Started successfully.', $last_id);

            }
        }


    }


    function stop_subtask()
    {
        $subtaskid = trim($this->input->post('subtaskid') ?? '');
        $taskid = trim($this->input->post('taskid') ?? '');
        $subtasksessionID = trim($this->input->post('subtasksession') ?? '');
        $companyid = current_companyID();
        $currentuser = current_userID();

        $dateTimestoped = format_date_mysql_datetime();


        /* $timestartintominutes = explode('-', $subtaskstarttime['starttime']);
         $convertedstarttimeminuts =  (($timestartintominutes[0]*60) + ($timestartintominutes[1]));


         $dateTimestopedTime = explode(':',$dateTimestoped[1]);
         $stopedtimeminutes = (($dateTimestopedTime[0]*60) + ($dateTimestopedTime[1]));*/

        /*  print_r($subtaskstarttime['starttime']);
          exit();*/
        $totaltimespentminutes = /*($stopedtimeminutes - $convertedstarttimeminuts);*/

        $timespent = $this->db->query("SELECT TIMESTAMPDIFF( MINUTE, startDatetime,'$dateTimestoped') AS timeminutes FROM srp_erp_task_subtasksessions subtasksession WHERE subtasksession.companyID = '{$companyid}'  AND subtasksession.taskID = '{$taskid}' AND subtasksession.subTaskID = '{$subtaskid}' AND subtasksession.sessionID = '{$subtasksessionID}' ")->row_array();
        $this->db->trans_start();
        $data['empID'] = $currentuser;
        $data['subTaskID'] = $subtaskid;
        $data['taskID'] = $taskid;
        $data['timeSpent'] = $timespent['timeminutes'];
        $data['status'] = 2;
        $data['endDateTime'] = format_date_mysql_datetime();
        $data['companyID'] = $companyid;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];

        $this->db->where('taskID', $taskid);
        $this->db->where('subTaskID', $subtaskid);
        $this->db->where('companyID', $companyid);
        $this->db->where('sessionID', $subtasksessionID);
        $subtaskstarttime =
            $this->db->update('srp_erp_task_subtasksessions', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sub task failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sub Task Stoped successfully.');

        }
    }

    
    function save_subTask_status()
    {
        $this->db->trans_start();
        $subtaskid = trim($this->input->post('subtaskID') ?? '');
        $taskid = trim($this->input->post('TaskID') ?? '');
        $companyid = current_companyID();
        $statussubtask = trim($this->input->post('statussubtask') ?? '');

        $this->db->select('sessionID');
        $this->db->where('companyID', $companyid);
        $this->db->where('subTaskID', $subtaskid);
        $this->db->where('taskID', $taskid);
        $this->db->where('status', 1);
        $this->db->from('srp_erp_task_subtasksessions');
        $record = $this->db->get()->result_array();
        if (!empty($record)) {
            return array('w', 'stop subtask session before change the status of this sub task!');
        } else {
            $data['status'] = $statussubtask;

            $this->db->where('companyID', $companyid);
            $this->db->where('subTaskID', $subtaskid);
            $this->db->where('taskID', $taskid);
            $this->db->update('srp_erp_task_subtasks', $data);


            $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_task_subtasks where taskID = $taskid ANd companyID  = $companyid")->row_array();
            $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_task_subtasks where taskID = $taskid ANd companyID  = $companyid AND `status` = 2")->row_array();

            if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
                $subtaskpercentage = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
            } else {
                if ($subtaskcount['totaltask'] = 1) 
                        {
                            // $subtaskpercentage = 20;
                        }
                        else
                        {
                            // $subtaskpercentage = 0;
                        }
                    }
           
            // // $dataupdatetask['progress'] =  trim($this->input->post('progress') ?? '');
            // $this->db->where('taskID', $taskid);
            // $this->db->update('srp_erp_task_task', $dataupdatetask);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Status Update Failed." . $this->db->_error_message());
            } else {
                return array('s', 'Status updated successfully!.');
            }
        }
    }

    
    function start_date_est_date_validation()
    {
        $date_format_policy = date_format_policy();
        $taskid = trim($this->input->post('taskID') ?? '');
        $startDate = $this->input->post('startDate');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $startDateconvert = input_format_date($startDate, $date_format_policy);


        $startdatevalidation = $this->db->query("select DATE_FORMAT(task.starDate ,'%Y-%m-%d') as startdatetASK from srp_erp_task_task task where companyID = '{$companyid}' And taskID = '{$taskid}' ")->row_array();

        if ($startDateconvert >= $startdatevalidation['startdatetASK']) {
            $data = 1;
        } else {
            $data = 0;
        }
        return $data;
    }

    function end_date_est_date_validation()
    {
        $date_format_policy = date_format_policy();
        $taskid = trim($this->input->post('taskID') ?? '');
        $endstartDate = $this->input->post('endstartDate');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $endDateconvert = input_format_date($endstartDate, $date_format_policy);

        $enddatevalidation = $this->db->query("select DATE_FORMAT( task.DueDate, '%Y-%m-%d') AS duedatetASK  from srp_erp_task_task task where companyID = '{$companyid}' And taskID = '{$taskid}'")->row_array();
        if ($endDateconvert <= $enddatevalidation['duedatetASK']) {
            $data = 1;
        } else {
            $data = 0;
        }
        return $data;
    }

    function crm_task_close_ischk()
    {
        $taskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $subtaskexist = $this->db->query("SELECT subTaskID FROM srp_erp_task_subtasks subtask left join srp_erp_task_task task on task.taskID = subtask.taskID WHERE subtask.companyID = '{$companyid}' AND subtask.taskID = '{$taskid}' And subtask.`status` != 2
	AND task.isSubTaskEnabled = 1")->result_array();
        if (!empty($subtaskexist)) {
            $data = 1;
        } else {
            $data = 0;
        }
        return $data;
    }

    function crm_is_subtask_exist()
    {
        $taskid = trim($this->input->post('taskID') ?? '');
        $statusid = trim($this->input->post('statusid') ?? '');
        $companyid = current_companyID();

        $data['closedstatus'] = $this->db->query("SELECT statusid,statusType FROM `srp_erp_task_status` where statusID = '{$statusid}' ")->row_array();


        $issubtaskexist = $this->db->query("SELECT subTaskID FROM srp_erp_task_subtasks subtask WHERE  subtask.taskID = '{$taskid}' And status!=2")->result_array();
        $data['taskdetail'] = $this->db->query("SELECT isClosed FROM srp_erp_task_task task WHERE task.taskID = '{$taskid}' ")->row_array();



        if (!empty($issubtaskexist) && $data['closedstatus']['statusType'] == 1 && $data['taskdetail']['isClosed'] == 0) {
            $data['isexist'] = 1;
        } else {
            $data['isexist'] = 0;
        }
        return $data;


    }

    
    function update_subtaask_detail()
    {
        $this->db->trans_start();

        $subtaskAutoid = $this->input->post('subtaskAutoid');
        $taskautoid = $this->input->post('taskautoid');
        $Taskdescription = $this->input->post('Taskdescriptionedit');
        $indays = $this->input->post('indaysedit');
        $inhrs = $this->input->post('inhrsedit');
        $inmns = $this->input->post('inmnsedit');
        $employeessubtask = $this->input->post('employeessubtaskedit');
        $companyid = current_companyID();

        $estsubtaskdate = $this->input->post('estsubtaskdateedit');
        $estsubtaskdateend = $this->input->post('estsubtaskdateendedit');
        $date_format_policy = date_format_policy();
        $format_estsubtaskdate = null;
        $crmtask = $this->db->query("SELECT *,DATE_FORMAT(starDate, '%Y-%m-%d') as crmtaskstartdate,DATE_FORMAT(DueDate, '%Y-%m-%d') as crmtasksdeuedate FROM `srp_erp_task_task` WHERE companyID = '{$companyid}' AND taskID = '{$taskautoid}'")->row_array();
        if (isset($estsubtaskdate) && !empty($estsubtaskdate)) {
            $format_estsubtaskdate = input_format_date($estsubtaskdate, $date_format_policy);
        }
        $format_estsubtaskdateend = null;
        if (isset($estsubtaskdate) && !empty($estsubtaskdateend)) {
            $format_estsubtaskdateend = input_format_date($estsubtaskdateend, $date_format_policy);
        }


        $startdate = input_format_date(trim($this->input->post('estsubtaskdateedit') ?? ''), $date_format_policy);
        $estsubtaskdateend = input_format_date(trim($this->input->post('estsubtaskdateendedit') ?? ''), $date_format_policy);
        if ($startdate > $estsubtaskdateend) {
            return array('e', 'Est. Start Date cannot be greater than Est. End Date');
            exit();
        }

        if ($crmtask['crmtaskstartdate'] > $startdate) {
            return array('e', 'Est. Start Date cannot be less than Task Start Date.');
            exit();
        }


        if ($startdate > $crmtask['crmtasksdeuedate']) {
            return array('e', 'Est. Start Date cannot be greater than Task Due Date.');
            exit();
        }

        if ($estsubtaskdateend > $crmtask['crmtasksdeuedate']) {
            return array('e', 'Estimated End Date Cannot be greater than Task Due Date.');
            exit();
        }
        if ($startdate > $estsubtaskdateend) {
            return array('e', 'Est. Start Date cannot be greater than Est. End Date');
            exit();
        }


        $data['taskDescription'] = $Taskdescription;
        $data['startDate'] = $format_estsubtaskdate;
        $data['endDate'] = $format_estsubtaskdateend;
        $data['estimatedDays'] = $indays;
        $data['estimatedHours'] = ($inhrs * 60) + ($inmns);
        $data['companyID'] = $companyid;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['modifiedUserName'] = $this->common_data['current_user'];

        $this->db->where('MasterAutoID', $subtaskAutoid);
        $this->db->where('documentID', 10);
        $this->db->delete('srp_erp_task_assignees');

        foreach ($employeessubtask as $val) {
            $datasub['empID'] = $val;
            $datasub['documentID'] = 10;
            $datasub['MasterAutoID'] = $subtaskAutoid;
            $datasub['companyID'] = $companyid;
            $datasub['createdUserGroup'] = $this->common_data['user_group'];
            $datasub['createdPCID'] = $this->common_data['current_pc'];
            $datasub['createdUserID'] = $this->common_data['current_userID'];
            $datasub['createdDateTime'] = $this->common_data['current_date'];
            $datasub['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_task_assignees', $datasub);
        }
        $this->db->where('taskID', $taskautoid);
        $this->db->where('subTaskID', $subtaskAutoid);
        $this->db->update('srp_erp_task_subtasks', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'subtask detail updated failes.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Sub Task Detail updated successfully.');
        }


    }

    function update_task_edit_view_comment()
    {

        $this->db->trans_start();
        $data['comment'] = trim($this->input->post('taskcomment') ?? '');
        $data['commentedUserID'] = $this->common_data['current_userID'];
        $this->db->where('taskID', trim($this->input->post('taskID') ?? ''));
        $this->db->update('srp_erp_task_task', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Comment Save Failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Comment Saved Successfully.');

        }
    }

    function load_subtsk_status()
    {
        $subtaskid = trim($this->input->post('subTaskID') ?? '');
        $taskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data = $this->db->query("SELECT `status` FROM `srp_erp_task_subtasks` where companyID = $companyid ANd subTaskID = $subtaskid And taskID = $taskid")->row_array();
        return $data;
    }

    
    function save_sub_task_comment()
    {

        /*   $time = new DateTime();
           echo $time->format("Y-m-d H:i:s P");
           $time = new DateTime(null, new DateTimeZone('Europe/London'));
           echo $time->format("Y-m-d H:i:s P");*/

        $subtaskid = trim($this->input->post('subtaskid') ?? '');
        $taskid = trim($this->input->post('taskid') ?? '');
        $commentsubtask = trim($this->input->post('commentsubtask') ?? '');
        $companyid = current_companyID();
        $currentuser = current_userID();
        $data['subTaskID'] = $subtaskid;
        $data['taskID'] = $taskid;
        $data['empID'] = $currentuser;
        $data['chatDescription'] = $commentsubtask;
        $data['companyID'] = $companyid;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = current_date();
        $data['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_task_chat', $data);
        $last_id = $this->db->insert_id();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            //return array('e', 'Sub task failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            //return array('s', 'Sub Task Started successfully.',$last_id);

        }

    }

    function save_task_comment()
    {

        /*   $time = new DateTime();
           echo $time->format("Y-m-d H:i:s P");
           $time = new DateTime(null, new DateTimeZone('Europe/London'));
           echo $time->format("Y-m-d H:i:s P");*/

        $taskid = trim($this->input->post('taskid') ?? '');
        $commenttask = trim($this->input->post('commenttask') ?? '');
        $companyid = current_companyID();
        $currentuser = current_userID();
        $data['taskID'] = $taskid;
        $data['empID'] = $currentuser;
        $data['chatDescription'] = $commenttask;
        $data['companyID'] = $companyid;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = current_date();
        $data['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_task_chat', $data);
        $last_id = $this->db->insert_id();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            //return array('e', 'Sub task failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            //return array('s', 'Sub Task Started successfully.',$last_id);

        }

    }




    function fetch_tasks_employee_detailsubtask()
    {
        $this->db->select('AssingeeID,empID,MasterAutoID');
        $this->db->from('srp_erp_task_assignees');
        $this->db->where('MasterAutoID', $this->input->post('MasterAutoID'));
        $this->db->where('documentID', 10);
        return $this->db->get()->result_array();
    }


    
    function create_category_status()
    {
        $this->db->trans_start();
 
        $data['documentID'] = $this->input->post('documentID');
        $data['description'] = $this->input->post('description');
        $data['sladays'] = $this->input->post('sldays');
        $data['textColor'] = $this->input->post('textColor');
        $data['backgroundColor'] = $this->input->post('backgroundColor');
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $categoryID = $this->input->post('categoryID');
        if ($categoryID == '') {
            $this->db->insert('srp_erp_task_categories', $data);
        } else {
            $this->db->update('srp_erp_task_categories', $data, array('categoryID' => $categoryID));
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Category save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Category saved successfully.');

        }
    }

    function deleteCaegory()
    {
        $this->db->delete('srp_erp_task_categories', array('categoryID' => trim($this->input->post('categoryID') ?? '')));
        return true;
    }

    function deleteDocumentStatus()
    {
        $statusid = $this->input->post('statusID');
        $companyid = current_companyID();

        $status = $this->db->query("SELECT statusID,leadStatusID,documentID FROM `srp_erp_task_status` where companyID = $companyid AND statusID = $statusid")->row_array();
        if ($status['documentID'] == 5) {
            $this->db->delete('srp_erp_crm_leadstatus', array('statusID' => $status['leadStatusID']));
        }

        $this->db->delete('srp_erp_task_status', array('statusID' => trim($this->input->post('statusID') ?? '')));
        return array('status' => 0, 'message' => 'Document Status Deleted Successfully!');


        /*$this->db->delete('srp_erp_task_status', array('statusID' => trim($this->input->post('statusID') ?? '')));
        return true;*/
    }

    function create_document_status()
    {
        $companyid = current_companyID();
        $docid = $this->input->post('documentID');
        if ($docid == 5) {
            $IsClosestatus = $this->input->post('IsClosestatuslead');
        } else if ($docid == 4) {
            $IsClosestatus = $this->input->post('IsClosestatusopportunities');
        } else if ($docid == 9) {
            $IsClosestatus = $this->input->post('IsClosestatusproject');
        } else {
            $IsClosestatus = $this->input->post('IsClosestatus');
        }


        $this->db->trans_start();
        $data['documentID'] = $this->input->post('documentID');
        $data['shortorder'] = $this->input->post('shortorder');
        $data['description'] = $this->input->post('status');
        $data['statusColor'] = $this->input->post('color');
        $data['statusBackgroundColor'] = $this->input->post('backgroundColor');
        $data['statusType'] = $IsClosestatus;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $statusID = $this->input->post('statusID');
        $documentstatusinsert = $this->db->query("SELECT statusType FROM `srp_erp_task_status` WHERE companyID = '{$companyid}' AND statusType = '{$IsClosestatus}' AND documentID = '{$docid}'")->row_array();
        $documentstatusupdate = $this->db->query("SELECT statusType FROM `srp_erp_task_status` WHERE companyID = '{$companyid}' AND statusType = '{$IsClosestatus}' AND documentID = '{$docid}' AND statusID != '{$statusID}' ")->row_array();


        if ($statusID == '') {
            if (($documentstatusinsert['statusType'] == 1)) {
                return array('e', 'Document status Is Already Closed.');
            }
            if (($documentstatusinsert['statusType'] == 2)) {
                return array('e', 'Document status Is Already Closed.');
            } else {
                if ($docid == 5) {
                    $dataleadstatus['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataleadstatus['description'] = $this->input->post('status');
                    $this->db->insert('srp_erp_crm_leadstatus', $dataleadstatus);
                    $data['leadStatusID'] = $this->db->insert_id();
                }
                $this->db->insert('srp_erp_task_status', $data);
            }


        } else {
            if (($documentstatusupdate['statusType'] == 1) || ($documentstatusupdate['statusType'] == 2)) {
                return array('e', 'Document status Is Already Closed.');
            } else {

                if ($docid == 5) {
                    $documentstatusupdateleadstaus = $this->db->query("SELECT statusType,leadStatusID FROM `srp_erp_task_status` WHERE companyID = '{$companyid}' AND documentID = '{$docid}' AND statusID = '{$statusID}'")->row_array();
                    $dataleadstatusup['description'] = $this->input->post('status');
                    $this->db->update('srp_erp_crm_leadstatus', $dataleadstatusup, array('statusID' => $documentstatusupdateleadstaus['leadStatusID']));
                }

                $this->db->update('srp_erp_task_status', $data, array('statusID' => $statusID));
            }

        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Document status save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Document status saved successfully.');

        }
    }
    function save_assigneHod()
    {
        $this->db->trans_start();
        
        $inchageempIDs = $this->input->post('employees');
        $categoryID = $this->input->post('categoryID');
        
        // Insert new records for each employee ID
        foreach ($inchageempIDs as $inchageempID) {
            $data = array(
                'inchageempID' => $inchageempID,
                'categoryID' => $categoryID
            );
            $result = $this->db->insert('srp_erp_task_categories_assignee', $data);
            
            if (!$result) {
                $this->db->trans_rollback();
                return array('e', 'Save failed: ' . $this->db->error()['message']);
            }
        }
        
        $this->db->trans_complete();
        return array('s', 'Records saved successfully.');
    }

    function delete_userdetail()
    {
        $inchageempIDs = $this->input->post('inchageempID');
        $categoryID = $this->input->post('categoryID');
    
        // Check if both categoryID and inchageempID are provided
        if ($categoryID && $inchageempIDs) {
          
            $this->db->where('categoryID', $categoryID);
            $this->db->where_in('inchageempID', $inchageempIDs);
            $this->db->delete('srp_erp_task_categories_assignee');
            return array('s', 'Records Delete successfully.');
        } else {
           
            return array('e', 'Delete failed: ' . $this->db->error()['message']);
        }
    }
    

    function saveisdefault()
    {
        $this->db->trans_start();
        
        $isdefault = $this->input->post('isDefault');
        $categoryID = $this->input->post('categoryID');
    
        // Perform the update query
        $this->db->set('isdefault', $isdefault);
        $this->db->where('categoryID', $categoryID);
        $this->db->update('srp_erp_task_categories_assignee');
    
        // Check if any rows were affected
        $rows_affected = $this->db->affected_rows();
        if ($rows_affected > 0) {
            echo "Update successful. $rows_affected row(s) updated.";
        } else {
            echo "No matching rows found for categoryID: $categoryID.";
        }
    
        $this->db->trans_complete();
    }
    


    

















}