<?php

class Crm extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Crm_modal');
        $this->load->helper('crm_helper');
        $this->load->library('Pagination');

        $this->load->library('s3');

        ini_set('max_execution_time', 360);
        ini_set('memory_limit', '2048M');
    }

    function fetch_campaign_master()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "companyID = " . $companyid;
        $this->datatables->select('campaignID,srp_erp_crm_campaignmaster.name,srp_erp_crm_campaignmaster.description,srp_erp_crm_campaignmaster.objective,DATE_FORMAT(startDate,\'' . $convertFormat . '\') AS startDate,srp_erp_crm_status.description as statusDes ');
        $this->datatables->join('srp_erp_crm_status', 'srp_erp_crm_campaignmaster.status = srp_erp_crm_status.statusID');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_crm_campaignmaster');
        $this->datatables->add_column('edit', '$1', 'load_campaign_action(campaignID)');
        echo $this->datatables->generate();
    }

    function fetch_contact_master()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "companyID = " . $companyid;
        $this->datatables->select('contactID,CONCAT (firstName, lastName) as ContactName,organization,email,phoneMobile');
        //$this->datatables->join('srp_erp_crm_campaignmaster', 'srp_erp_crm_campaignmaster.campaignID = srp_erp_crm_contactmaster.contactID');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_crm_contactmaster');
        $this->datatables->add_column('edit', '$1', 'load_contact_action(contactID)');
        echo $this->datatables->generate();
    }

    function fetch_task_master()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "companyID = " . $companyid;
        $this->datatables->select('taskID,srp_erp_crm_task.subject,srp_erp_crm_task.description,srp_erp_crm_task.Priority,srp_erp_crm_status.description as statusDes,contactName ');
        $this->datatables->join('srp_erp_crm_status', 'srp_erp_crm_task.status = srp_erp_crm_status.statusID');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_crm_task');
        $this->datatables->add_column('edit', '$1', 'load_task_action(taskID)');
        echo $this->datatables->generate();
    }

    function save_campaign_header()
    {
        $userPermission = $this->input->post('userPermission');
        $groupid = $this->input->post('groupID');
        $assignemployees = $this->input->post('employees');
        $permissionemp = $this->input->post('employees_permission');
        $companyID = current_companyID();
        $currentuser [] = current_userID();
        $this->form_validation->set_rules('campaign_name', 'Campaign Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('objective', 'Objective', 'trim|required');
        $this->form_validation->set_rules('typeID', 'Type', 'trim|required');
        $this->form_validation->set_rules('statusID', 'Status', 'trim|required');
        $this->form_validation->set_rules('startdate', 'Start Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('end_date', 'End Date', 'trim|required|validate_date');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $startdate = strtotime($this->input->post('startdate'));
            $end_date = strtotime($this->input->post('end_date'));
            if ($end_date >= $startdate) {
                if ($userPermission == 3) {
                    if ($groupid == ' ' || empty($groupid)) {
                        echo json_encode(array('w', 'Please select a user group.'));
                    } elseif (empty($assignemployees) || $assignemployees == ' ') {
                        echo json_encode($this->Crm_modal->save_campaign_header());
                    } else {
                        $grouppermission = $this->db->query("select empID from srp_erp_crm_usergroupdetails where companyID = '{$companyID }' And groupMasterID = '{$groupid}'  AND empID IN (" . join(',', $assignemployees) . ") ")->result_array();
                        $grouppermissionresult = array_column($grouppermission, 'empID');
                        $resultdiff = array_diff($assignemployees, $grouppermissionresult);
                        if (!empty($resultdiff)) {
                            echo json_encode(array('w', 'Selected assignee not in the current user group'));
                        } else {
                            echo json_encode($this->Crm_modal->save_campaign_header());
                        }
                    }
                } elseif ($userPermission == 4) {
                    if (empty($permissionemp) || $permissionemp == ' ') {
                        echo json_encode(array('w', 'Please select visibility users'));
                    } elseif (empty($assignemployees) || $assignemployees == ' ') {
                        echo json_encode($this->Crm_modal->save_campaign_header());
                    } else {
                        $resultdifference = array_diff($assignemployees, $permissionemp);
                        if (!empty($resultdifference)) {
                            echo json_encode(array('w', 'Visibility permission not granted for some assignees'));
                        } else {
                            echo json_encode($this->Crm_modal->save_campaign_header());
                        }
                    }
                } elseif ($userPermission == 2) {
                    if (empty($assignemployees) || $assignemployees == ' ') {
                        echo json_encode($this->Crm_modal->save_campaign_header());
                    } else {
                        $resultdifferencerecord = array_diff($assignemployees, $currentuser);
                        if (!empty($resultdifferencerecord)) {
                            echo json_encode(array('w', 'Please select current user as assignee'));
                        } else {
                            echo json_encode($this->Crm_modal->save_campaign_header());
                        }
                    }
                } else {
                    echo json_encode($this->Crm_modal->save_campaign_header());
                }
            } else {
                echo json_encode(array('e', 'End Date should be greater than Start Date'));
            }
        }
    }

    function save_campaign_attendees()
    {
        $this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
        $this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('phoneMobile', 'Phone Mobile', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->save_campaign_attendees());
        }
    }

    function delete_campaign()
    {
        echo json_encode($this->Crm_modal->delete_campaign());
    }

    function delete_task()
    {
        echo json_encode($this->Crm_modal->delete_task());
    }

    function load_campaign_header()
    {
        echo json_encode($this->Crm_modal->load_campaign_header());
    }

    function load_campaign_attendees_header()
    {
        echo json_encode($this->Crm_modal->load_campaign_attendees_header());
    }

    function fetch_campaign_employee_detail()
    {
        echo json_encode($this->Crm_modal->fetch_campaign_employee_detail());
    }

    function save_organization_header()
    {
        $this->form_validation->set_rules('Name', 'Name', 'trim|required');
        //$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        //$this->form_validation->set_rules('telephoneNo', 'Phone No', 'trim|required');
        //$this->form_validation->set_rules('billingAddress', 'Billing Address', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->save_organization_header());
        }
    }

    function fetch_campaign_attendees_detail()
    {
        echo json_encode($this->Crm_modal->fetch_campaign_attendees_detail());
    }

    function delete_campaign_detail()
    {
        echo json_encode($this->Crm_modal->delete_campaign_detail());
    }

    function delete_campaign_attendees_detail()
    {
        echo json_encode($this->Crm_modal->delete_campaign_attendees_detail());
    }

    function save_task_header()
    {
        $searches = $this->input->post('related_search');
        $userPermission = $this->input->post('userPermission');
        $groupid = trim($this->input->post('groupID') ?? '');
        $assignemployees = $this->input->post('employees');
        $permissionemp = $this->input->post('multipleemployees');
        $companyID = $this->common_data['company_data']['company_id'];
        $this->form_validation->set_rules('subject', 'subject', 'trim|required');
        $this->form_validation->set_rules('categoryID', 'Category', 'trim|required');
        $this->form_validation->set_rules('priority', 'Priority', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('statusID', 'Status', 'trim|required');
        $this->form_validation->set_rules('startdate', 'Start Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('duedate', 'Due Date', 'trim|required|validate_date');
        
        $currentuser[] = current_userID();


        /*        foreach ($searches as $key => $search) {
                    $this->form_validation->set_rules("relatedTo[{$key}]", 'Related To', 'trim|required');
                    $this->form_validation->set_rules("relatedAutoID[{$key}]", 'Search', 'trim|required');
                }*/
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $startdate = strtotime($this->input->post('startdate'));
            $end_date = strtotime($this->input->post('duedate'));
            if ($end_date >= $startdate) {
                if ($userPermission == 3) {
                    if ($groupid == ' ' || empty($groupid)) {
                        echo json_encode(array('w', 'Please select a user group.'));
                    } elseif (empty($assignemployees) || isset($assignemployees)) {
                        echo json_encode($this->Crm_modal->save_task_header());
                    } else {
                        $grouppermission = $this->db->query("select empID from srp_erp_crm_usergroupdetails where companyID = '{$companyID }' And groupMasterID = '{$groupid}'  AND empID IN (" . join(',', $assignemployees) . ") ")->result_array();
                        $grouppermissionresult = array_column($grouppermission, 'empID');
                        $resultdiff = array_diff($assignemployees, $grouppermissionresult);
                        if (!empty($resultdiff)) {
                            echo json_encode(array('w', 'Selected assignee not in the current user group'));
                        } else {
                            echo json_encode($this->Crm_modal->save_task_header());
                        }
                    }
                } elseif ($userPermission == 4) {
                    if (empty($permissionemp) || $permissionemp == ' ') {
                        echo json_encode(array('w', 'Please select users to visibility'));
                    } elseif (empty($assignemployees) || $assignemployees == ' ') {
                        echo json_encode($this->Crm_modal->save_task_header());
                    } else {
                        $resultdifference = array_diff($assignemployees, $permissionemp);
                        if (!empty($resultdifference)) {
                            echo json_encode(array('w', 'Visibility permission not granted for some assignees'));
                        } else {
                            echo json_encode($this->Crm_modal->save_task_header());
                        }
                    }
                } elseif ($userPermission == 2) {
                    if (empty($assignemployees) || $assignemployees == ' ') {
                        echo json_encode($this->Crm_modal->save_task_header());
                    } else {
                        $resultdifferencerecord = array_diff($assignemployees, $currentuser);
                        if (!empty($resultdifferencerecord)) {
                            echo json_encode(array('w', 'Please select current user as assignee'));
                        } else {
                            echo json_encode($this->Crm_modal->save_task_header());
                        }
                    }
                } else {
                    echo json_encode($this->Crm_modal->save_task_header());
                }
            } else {
                echo json_encode(array('e', 'Due Date should be greater than Start Date'));
            }
        }
    }

    function load_task_header()
    {
        echo json_encode($this->Crm_modal->load_task_header());
    }

    function load_contact_header()
    {
        echo json_encode($this->Crm_modal->load_contact_header());
    }

    function load_organization_header()
    {
        echo json_encode($this->Crm_modal->load_organization_header());
    }

    function fetch_tasks_employee_detail()
    {
        echo json_encode($this->Crm_modal->fetch_tasks_employee_detail());
    }

    function fetch_document_relate_search()
    {
        echo json_encode($this->Crm_modal->fetch_document_relate_search());
    }

    function fetch_contact_relate_search()
    {
        echo json_encode($this->Crm_modal->fetch_contact_relate_search());
    }

    function update_task_edit_view_comment()
    {
        $this->form_validation->set_rules('taskcomment', 'Comment', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->update_task_edit_view_comment());
        }
    }

    function update_campaign_edit_view_comment()
    {
        $this->form_validation->set_rules('campaigncomment', 'Comment', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->update_campaign_edit_view_comment());
        }
    }

    function save_contact_header()
    {
        $this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
        $this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('sourceID', 'Source', 'trim|required');
        //$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        //$this->form_validation->set_rules('phoneMobile', 'Phone (Mobile)', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->save_contact_header());
        }
    }

    function load_taskManagement_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $category = trim($this->input->post('category') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $priority = trim($this->input->post('priority') ?? '');
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
            $filterCategory = " AND srp_erp_crm_task.categoryID = " . $category . "";
        }
        $filterStatus = '';
        if (isset($status) && !empty($status)) {
            $filterStatus = " AND status = " . $status . "";
        }
        $filterpriority = '';
        if (isset($priority) && !empty($priority)) {
            $filterpriority = " AND Priority = " . $priority . "";
        }
        $filterAssigneesID = '';
        if (isset($assigneesID) && !empty($assigneesID)) {
            $filterAssigneesID = " AND srp_erp_crm_assignees.empID = " . $assigneesID . "";
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
            $filtercreatedid = " AND srp_erp_crm_task.createdUserID = " . $employeeid . "";
        }
        if ($issuperadmin['isSuperAdmin'] == 1) {
            $where_admin = "srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filteropporunityID . $filterpipeLineDetailID . $filtercreatedid;


            $data['headercount'] = $this->db->query("SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID WHERE $where_admin GROUP BY srp_erp_crm_task.taskID ORDER BY taskID DESC ")->result_array();
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
            $data['headercountshowing'] = $this->db->query("SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID WHERE $where_admin GROUP BY srp_erp_crm_task.taskID ORDER BY taskID DESC LIMIT {$page},{$per_page} ")->result_array();
            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT 
                                                srp_erp_crm_task.taskID,
                                                srp_erp_crm_task.subject,
                                                srp_erp_crm_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_crm_status.description as statusDescription,
                                                srp_erp_crm_assignees.empID,
                                                srp_erp_crm_task.progress,
                                                srp_erp_crm_task.status,
                                                srp_erp_crm_task.Priority,
                                                isClosed,
                                                srp_erp_crm_task.createdUserID as createdUserIDtask,
                                                srp_erp_crm_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_crm_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_crm_task.documentSystemCode,
                                                srp_erp_crm_status.statusColor AS statusColor,
	                                            srp_erp_crm_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM 
                                                srp_erp_crm_task 
                                                LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID
                                                LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status 
                                                LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID 
                                                WHERE $where_admin 
                                                GROUP BY srp_erp_crm_task.taskID ORDER BY taskID DESC LIMIT {$page},{$per_page}")->result_array();
        } else {
            $where1 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID;

            $where2 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND (srp_erp_crm_documentpermission.permissionValue = $currentuserID or  srp_erp_crm_task.createdUserID = $currentuserID) AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID ;

            $where3 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND (srp_erp_crm_usergroupdetails.empID = $currentuserID or  srp_erp_crm_task.createdUserID = $currentuserID) AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID ;

            $where4 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND (srp_erp_crm_documentpermissiondetails.empID = $currentuserID or  srp_erp_crm_task.createdUserID = $currentuserID) AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filtercreatedid . $filterpipeLineDetailID ;


            $data['headercount'] = $this->db->query("SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID WHERE $where1 GROUP BY
	srp_erp_crm_task.taskID  UNION SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID WHERE $where2 GROUP BY
	srp_erp_crm_task.taskID  UNION SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY
	srp_erp_crm_task.taskID  UNION SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_crm_task.taskID ORDER BY taskID DESC")->result_array();
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
            $data['headercountshowing'] = $this->db->query("SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID WHERE $where1 GROUP BY
	srp_erp_crm_task.taskID  UNION SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID WHERE $where2 GROUP BY
	srp_erp_crm_task.taskID  UNION SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY
	srp_erp_crm_task.taskID  UNION SELECT srp_erp_crm_task.taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_crm_task.taskID ORDER BY taskID DESC LIMIT {$page},{$per_page}")->result_array();
            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT
                                                srp_erp_crm_task.taskID,
                                                srp_erp_crm_task.subject,
                                                srp_erp_crm_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_crm_status.description as statusDescription,
                                                srp_erp_crm_assignees.empID,
                                                srp_erp_crm_task.progress,
                                                srp_erp_crm_task.status,
                                                srp_erp_crm_task.Priority,
                                                isClosed,
                                                srp_erp_crm_task.createdUserID as createdUserIDtask,
                                                srp_erp_crm_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_crm_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_crm_task.documentSystemCode,
                                                srp_erp_crm_status.statusColor AS statusColor,
	                                            srp_erp_crm_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM srp_erp_crm_task 
                                                LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID 
                                                LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status 
                                                LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID
                                                LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID 
                                                WHERE $where1 
                                                GROUP BY
	                                            srp_erp_crm_task.taskID  
                                                UNION 
                                                SELECT srp_erp_crm_task.taskID,
                                                srp_erp_crm_task.subject,
                                                srp_erp_crm_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_crm_status.description as statusDescription,
                                                srp_erp_crm_assignees.empID,
                                                srp_erp_crm_task.progress,
                                                srp_erp_crm_task.status,
                                                srp_erp_crm_task.Priority,
                                                isClosed,
                                                srp_erp_crm_task.createdUserID as createdUserIDtask,
                                                srp_erp_crm_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_crm_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_crm_task.documentSystemCode,
                                                srp_erp_crm_status.statusColor AS statusColor,
	                                            srp_erp_crm_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM 
                                                srp_erp_crm_task 
                                                LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID 
                                                LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status 
                                                LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID 
                                                LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID 
                                                WHERE $where2 
                                                GROUP BY
	                                            srp_erp_crm_task.taskID 
                                                UNION 
                                                SELECT 
                                                srp_erp_crm_task.taskID,
                                                srp_erp_crm_task.subject,
                                                srp_erp_crm_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_crm_status.description as statusDescription,
                                                srp_erp_crm_assignees.empID,
                                                srp_erp_crm_task.progress,
                                                srp_erp_crm_task.status,
                                                srp_erp_crm_task.Priority,
                                                isClosed,
                                                srp_erp_crm_task.createdUserID as createdUserIDtask,
                                                srp_erp_crm_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_crm_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_crm_task.documentSystemCode,
                                                srp_erp_crm_status.statusColor AS statusColor,
	                                            srp_erp_crm_status.statusBackgroundColor AS statusBackgroundColor
                                                FROM srp_erp_crm_task
                                                LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID 
                                                LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status 
                                                LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID 
                                                LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID 
                                                LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue 
                                                WHERE $where3 
                                                GROUP BY
	                                            srp_erp_crm_task.taskID 
                                                UNION 
                                                SELECT 
                                                srp_erp_crm_task.taskID,
                                                srp_erp_crm_task.subject,
                                                srp_erp_crm_categories.description as categoryDescription,
                                                textColor,
                                                backGroundColor,
                                                visibility,
                                                DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,
                                                DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,
                                                srp_erp_crm_status.description as statusDescription,
                                                srp_erp_crm_assignees.empID,
                                                srp_erp_crm_task.progress,
                                                srp_erp_crm_task.status,
                                                srp_erp_crm_task.Priority,isClosed,
                                                srp_erp_crm_task.createdUserID as createdUserIDtask,
                                                srp_erp_crm_task.createdUserName AS createdUserName,
                                                DATE_FORMAT(srp_erp_crm_task.createdDateTime,'" . $convertFormat . "') AS createdDateTime,
                                                srp_erp_crm_task.documentSystemCode,
                                                srp_erp_crm_status.statusColor AS statusColor,
	                                            srp_erp_crm_status.statusBackgroundColor AS statusBackgroundColor 
                                                FROM srp_erp_crm_task 
                                                LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID 
                                                LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status 
                                                LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID 
                                                LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID 
                                                LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID 
                                                WHERE 
                                                $where4
                                                GROUP BY 
                                                srp_erp_crm_task.taskID 
                                                ORDER BY taskID DESC 
                                                LIMIT {$page},{$per_page}")->result_array();
        }
        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['view'] = $this->load->view('system/crm/ajax/load_task_master', $data, true);
        echo json_encode($data);
    }

    function load_contactManagement_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $issuperadmin = crm_isSuperAdmin();
        $employeeid = $this->input->post('createdby');
        $currentuserID = current_userID();
        $convertFormat = convert_date_format_sql();
        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((firstName Like CONCAT('%" . $text . "%')) OR (lastName Like CONCAT('%" . $text . "%')) OR (srp_erp_crm_contactmaster.email Like CONCAT('%" . $text . "%')))";
        }
        $search_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $search_sorting = " AND firstName Like '" . $sorting . "%'";
        }
        $filtercreatedid = '';
        if (isset($employeeid) && !empty($employeeid)) {
            $filtercreatedid = " AND srp_erp_crm_contactmaster.createdUserID = " . $employeeid . "";
        }

        $convertFormat = convert_date_format_sql();

        /*if ($issuperadmin['isSuperAdmin'] == 1) {*/
        $where_admin = "srp_erp_crm_contactmaster.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;
        $data['headercount'] = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 WHERE $where_admin GROUP BY srp_erp_crm_contactmaster.contactID ORDER BY contactID DESC ")->result_array();
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
        $data['headercountshowing'] = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 WHERE $where_admin GROUP BY srp_erp_crm_contactmaster.contactID ORDER BY contactID LIMIT {$page},{$per_page} ")->result_array();
        $dataCount = count($data['headercountshowing']);
        $thisPageEndNumber = $page + $dataCount;
        $data['header'] = $this->db->query("SELECT contactID,srp_erp_crm_contactmaster.documentSystemCode,srp_erp_crm_contactmaster.occupation,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.createdUserName as createdUserNamecrm,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'" . $convertFormat . "') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 WHERE $where_admin GROUP BY srp_erp_crm_contactmaster.contactID ORDER BY contactID desc LIMIT {$page},{$per_page} ")->result_array();
    /*$outputimg = [];*/
        /*else {

           $where1 = "srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_contactmaster.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;
           $where2 = "srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND  srp_erp_crm_contactmaster.companyID = " . $companyid . $search_string . $search_sorting;
           $where3 = "srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND  srp_erp_crm_contactmaster.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;
           $where4 = "srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 4 AND (srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " OR srp_erp_crm_contactmaster.createdUserID = " . $currentuserID . ") AND  srp_erp_crm_contactmaster.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;
           $data['headercount'] = $this->db->query("SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID WHERE $where1 GROUP BY contactID UNION SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID  WHERE $where2 GROUP BY contactID UNION SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY contactID UNION SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY contactID ORDER BY contactID")->result_array();

           $totalCount = count($data['headercount']);
           $data_pagination = $this->input->post('pageID');
           $per_page = 10;
           $config = array();
           $config["base_url"] =  "#Contacts-list";
           $config["total_rows"] =  $totalCount;
           $config["per_page"] = $per_page;
           $config["data_page_attr"] = 'data-emp-pagination';
           $config["uri_segment"] = 3;
           $this->pagination->initialize($config);
           $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
           $sentfunction = 'sentemailpagination';
           $data["empCount"] = $totalCount;
           $data["pagination"] = $this->pagination->create_links_employee_master();
           $data["per_page"] = $per_page;
           $thisPageStartNumber = ($page+1);
           $data['headercountshowing'] = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID WHERE $where1 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID  WHERE $where2 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY contactID ORDER BY contactID LIMIT {$page},{$per_page}")->result_array();
           $dataCount = count($data['headercountshowing']);
           $thisPageEndNumber = $page+$dataCount;
           $data['header'] = $this->db->query("SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID WHERE $where1 GROUP BY contactID UNION SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID  WHERE $where2 GROUP BY contactID UNION SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY contactID UNION SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_organizations.organizationID,srp_erp_crm_contactmaster.createdUserName AS createdUserNamecrm,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'".$convertFormat."') as createddatecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY contactID ORDER BY contactID desc LIMIT {$page},{$per_page}")->result_array();

       }*/
        $noimage = $this->s3->createPresignedRequest('images/item/no-image.png', '1 hour');
       // echo '<pre>';print_r($data['header']);

        //echo '<pre>';print_r($data['contactimage_up']);
        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['view'] = $this->load->view('system/crm/ajax/load_contact_master', $data, true);
        echo json_encode($data);
    }

    function load_organizationManagement_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $currentuserID = current_userID();
        $issuperadmin = crm_isSuperAdmin();
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $search_string = '';
        $convertFormat = convert_date_format_sql();
        $employeeid = $this->input->post('createdby');

        if (isset($text) && !empty($text)) {
            $search_string = " AND (Name Like '%" . $text . "%')";
        }
        $search_sorting = '';
        if (isset($sorting) && !empty($sorting)) {
            $search_sorting = " AND Name Like '" . $sorting . "%'";
        }
        $filtercreatedid = '';
        if (isset($employeeid) && !empty($employeeid)) {
            $filtercreatedid = " AND srp_erp_crm_organizations.createdUserID = " . $employeeid . "";
        }

        if ($issuperadmin['isSuperAdmin'] == 1) {
            $where_admin = "srp_erp_crm_organizations.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;


            $data['headercount'] = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations WHERE $where_admin ORDER BY organizationID DESC ")->result_array();
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
            $data['headercountshowing'] = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations WHERE $where_admin ORDER BY organizationID DESC LIMIT {$page},{$per_page} ")->result_array();
            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT organizationID,createdUserName,Name,email,organizationLogo,billingAddress,telephoneNo,DATE_FORMAT(createdDateTime,'" . $convertFormat . "') AS createdDate,documentSystemCode FROM srp_erp_crm_organizations WHERE $where_admin ORDER BY organizationID DESC LIMIT {$page},{$per_page}")->result_array();
        } else {
            $where1 = "srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_organizations.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;

            $where2 = "srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND  srp_erp_crm_organizations.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;

            $where3 = "srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND  srp_erp_crm_organizations.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;

            $where4 = "srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND  srp_erp_crm_organizations.companyID = " . $companyid . $search_string . $search_sorting . $filtercreatedid;


            $data['headercount'] = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID WHERE $where1 UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID WHERE $where2 UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4")->result_array();
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
            $data['headercountshowing'] = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID WHERE $where1 UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID WHERE $where2 UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 ORDER BY organizationID DESC LIMIT {$page},{$per_page}")->result_array();
            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT organizationID,Name,email,organizationLogo,billingAddress,telephoneNo,srp_erp_crm_organizations.createdUserName,DATE_FORMAT(srp_erp_crm_organizations.createdDateTime,'" . $convertFormat . "') AS createdDate,documentSystemCode FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID WHERE $where1 UNION SELECT organizationID,Name,email,organizationLogo,billingAddress,telephoneNo,srp_erp_crm_organizations.createdUserName,DATE_FORMAT(srp_erp_crm_organizations.createdDateTime,'" . $convertFormat . "') AS createdDate,documentSystemCode FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID WHERE $where2 UNION SELECT organizationID,Name,email,organizationLogo,billingAddress,telephoneNo,srp_erp_crm_organizations.createdUserName,DATE_FORMAT(srp_erp_crm_organizations.createdDateTime,'" . $convertFormat . "') AS createdDate,documentSystemCode FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 UNION SELECT organizationID,Name,email,organizationLogo,billingAddress,telephoneNo,srp_erp_crm_organizations.createdUserName,DATE_FORMAT(srp_erp_crm_organizations.createdDateTime,'" . $convertFormat . "') AS createdDate,documentSystemCode FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 ORDER BY organizationID DESC LIMIT {$page},{$per_page}")->result_array();
        }
        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data["view"] = $this->load->view('system/crm/ajax/load_organization_master', $data, true);
        echo json_encode($data);
    }

    function load_campaignManagement_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchCampaign') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $assigneesID = trim($this->input->post('assignee') ?? '');
        $currentuserID = current_userID();
        $issuperadmin = crm_isSuperAdmin();
        $search_string = '';
        $convertFormat = convert_date_format_sql();
        $employeeid = $this->input->post('createdby');
        if (isset($text) && !empty($text)) {
            $search_string = " AND name Like '%" . $text . "%'";
        }
        $filterStatus = '';
        if (isset($status) && !empty($status)) {
            $filterStatus = " AND status = " . $status . "";
        }
        $filterType = '';
        if (isset($type) && !empty($type)) {
            $filterType = " AND type = " . $type . "";
        }

        $filterAssigneesID = '';
        if (isset($assigneesID) && !empty($assigneesID)) {
            $filterAssigneesID = " AND srp_erp_crm_assignees.empID = " . $assigneesID . "";
        }

        $filtercreatedid = '';
        if (isset($employeeid) && !empty($employeeid)) {
            $filtercreatedid = " AND srp_erp_crm_campaignmaster.createdUserID = " . $employeeid . "";
        }
        if ($issuperadmin['isSuperAdmin'] == 1) {
            $where_admin = "srp_erp_crm_campaignmaster.companyID = " . $companyid . $search_string . $filterStatus . $filterType . $filterAssigneesID . $filtercreatedid;

            $data['headercount'] = $this->db->query("SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where_admin GROUP BY srp_erp_crm_campaignmaster.campaignID ORDER BY campaignID DESC ")->result_array();
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
            $data['headercountshowing'] = $this->db->query("SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where_admin GROUP BY srp_erp_crm_campaignmaster.campaignID ORDER BY campaignID DESC LIMIT {$page},{$per_page} ")->result_array();

            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT srp_erp_crm_campaignmaster.campaignID,srp_erp_crm_campaignmaster.name,srp_erp_crm_categories.description as categoryDescription,srp_erp_crm_categories.textColor as categoryTextColor,srp_erp_crm_categories.backGroundColor as categoryBackGroundColor,srp_erp_crm_status.description as statusDescription,srp_erp_crm_status.statusColor as statusTextColor,srp_erp_crm_status.statusBackgroundColor as statusBackGroundColor,DATE_FORMAT(startDate,'" . $convertFormat . "') AS startDate,DATE_FORMAT(endDate,'" . $convertFormat . "') AS endDate,srp_erp_crm_campaignmaster.status,srp_erp_crm_assignees.empID,isClosed,srp_erp_crm_campaignmaster.createdUserID as createdUserIDcampaign,srp_erp_crm_campaignmaster.createdUserName AS createdUsercampaign,DATE_FORMAT(srp_erp_crm_campaignmaster.createdDateTime,'" . $convertFormat . "') AS createdDateTimecampaign,srp_erp_crm_campaignmaster.documentSystemCode FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where_admin GROUP BY srp_erp_crm_campaignmaster.campaignID ORDER BY campaignID DESC LIMIT {$page},{$per_page}")->result_array();
        } else {
            $where1 = "srp_erp_crm_documentpermission.documentID = 1 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_campaignmaster.companyID = " . $companyid . $search_string . $filterStatus . $filterType . $filterAssigneesID . $filtercreatedid;

            $where2 = "srp_erp_crm_documentpermission.documentID = 1 AND srp_erp_crm_documentpermission.permissionID = 2 AND (srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " or srp_erp_crm_campaignmaster.createdUserID = " . $currentuserID . ")  AND  srp_erp_crm_campaignmaster.companyID = " . $companyid . $search_string . $filterStatus . $filterType . $filterAssigneesID . $filtercreatedid;

            $where3 = "srp_erp_crm_documentpermission.documentID = 1 AND srp_erp_crm_documentpermission.permissionID = 3 AND (srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " or srp_erp_crm_campaignmaster.createdUserID = " . $currentuserID . ") AND srp_erp_crm_campaignmaster.companyID = " . $companyid . $search_string . $filterStatus . $filterType . $filterAssigneesID . $filtercreatedid;

            $where4 = "srp_erp_crm_documentpermission.documentID = 1 AND srp_erp_crm_documentpermission.permissionID = 4 AND (srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " or srp_erp_crm_campaignmaster.createdUserID = " . $currentuserID . ") AND srp_erp_crm_campaignmaster.companyID = " . $companyid . $search_string . $filterStatus . $filterType . $filterAssigneesID . $filtercreatedid;


            $data['headercount'] = $this->db->query("SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where1 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where2 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_crm_campaignmaster.campaignID ORDER BY campaignID DESC")->result_array();
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
            $data['headercountshowing'] = $this->db->query("SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where1 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where2 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_crm_campaignmaster.campaignID ORDER BY campaignID DESC LIMIT {$page},{$per_page}")->result_array();

            $dataCount = count($data['headercountshowing']);
            $thisPageEndNumber = $page + $dataCount;
            $data['header'] = $this->db->query("SELECT srp_erp_crm_campaignmaster.campaignID,srp_erp_crm_campaignmaster.name,srp_erp_crm_categories.description as categoryDescription,srp_erp_crm_categories.textColor as categoryTextColor,srp_erp_crm_categories.backGroundColor as categoryBackGroundColor,srp_erp_crm_status.statusColor as statusTextColor,srp_erp_crm_status.statusBackgroundColor as statusBackGroundColor,srp_erp_crm_status.description as statusDescription,DATE_FORMAT(startDate,'" . $convertFormat . "') AS startDate,DATE_FORMAT(endDate,'" . $convertFormat . "') AS endDate,srp_erp_crm_campaignmaster.status,srp_erp_crm_assignees.empID,isClosed,srp_erp_crm_campaignmaster.createdUserID as createdUserIDcampaign,srp_erp_crm_campaignmaster.createdUserName AS createdUsercampaign,DATE_FORMAT(srp_erp_crm_campaignmaster.createdDateTime,'" . $convertFormat . "') AS createdDateTimecampaign,srp_erp_crm_campaignmaster.documentSystemCode FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where1 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID,srp_erp_crm_campaignmaster.name,srp_erp_crm_categories.description as categoryDescription,srp_erp_crm_categories.textColor as categoryTextColor,srp_erp_crm_categories.backGroundColor as categoryBackGroundColor,srp_erp_crm_status.statusColor as statusTextColor,srp_erp_crm_status.statusBackgroundColor as statusBackGroundColor,srp_erp_crm_status.description as statusDescription,DATE_FORMAT(startDate,'" . $convertFormat . "') AS startDate,DATE_FORMAT(endDate,'" . $convertFormat . "') AS endDate,srp_erp_crm_campaignmaster.status,srp_erp_crm_assignees.empID,isClosed,srp_erp_crm_campaignmaster.createdUserID as createdUserIDcampaign,srp_erp_crm_campaignmaster.createdUserName AS createdUsercampaign,DATE_FORMAT(srp_erp_crm_campaignmaster.createdDateTime,'" . $convertFormat . "') AS createdDateTimecampaign,srp_erp_crm_campaignmaster.documentSystemCode FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID WHERE $where2 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID,srp_erp_crm_campaignmaster.name,srp_erp_crm_categories.description as categoryDescription,srp_erp_crm_categories.textColor as categoryTextColor,srp_erp_crm_categories.backGroundColor as categoryBackGroundColor,srp_erp_crm_status.statusColor as statusTextColor,srp_erp_crm_status.statusBackgroundColor as statusBackGroundColor,srp_erp_crm_status.description as statusDescription,DATE_FORMAT(startDate,'" . $convertFormat . "') AS startDate,DATE_FORMAT(endDate,'" . $convertFormat . "') AS endDate,srp_erp_crm_campaignmaster.status,srp_erp_crm_assignees.empID,isClosed,srp_erp_crm_campaignmaster.createdUserID as createdUserIDcampaign,srp_erp_crm_campaignmaster.createdUserName AS createdUsercampaign,DATE_FORMAT(srp_erp_crm_campaignmaster.createdDateTime,'" . $convertFormat . "') AS createdDateTimecampaign,srp_erp_crm_campaignmaster.documentSystemCode FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY srp_erp_crm_campaignmaster.campaignID UNION SELECT srp_erp_crm_campaignmaster.campaignID,srp_erp_crm_campaignmaster.name,srp_erp_crm_categories.description as categoryDescription,srp_erp_crm_categories.textColor as categoryTextColor,srp_erp_crm_categories.backGroundColor as categoryBackGroundColor,srp_erp_crm_status.statusColor as statusTextColor,srp_erp_crm_status.statusBackgroundColor as statusBackGroundColor,srp_erp_crm_status.description as statusDescription,DATE_FORMAT(startDate,'" . $convertFormat . "') AS startDate,DATE_FORMAT(endDate,'" . $convertFormat . "') AS endDate,srp_erp_crm_campaignmaster.status,srp_erp_crm_assignees.empID,isClosed,srp_erp_crm_campaignmaster.createdUserID as createdUserIDcampaign,srp_erp_crm_campaignmaster.createdUserName AS createdUsercampaign,DATE_FORMAT(srp_erp_crm_campaignmaster.createdDateTime,'" . $convertFormat . "') AS createdDateTimecampaign,srp_erp_crm_campaignmaster.documentSystemCode FROM srp_erp_crm_campaignmaster LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_campaignmaster.campaignID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_crm_campaignmaster.campaignID ORDER BY campaignID DESC LIMIT {$page},{$per_page}")->result_array();
        }
        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data["view"] = $this->load->view('system/crm/ajax/load_campaign_management', $data, true);
        echo json_encode($data);
    }

    function load_taskManagement_editView()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $taskID = trim($this->input->post('taskID') ?? '');
        $this->db->select('srp_erp_crm_task.taskID,srp_erp_crm_task.isSubTaskEnabled as isSubTaskEnabled,srp_erp_crm_task.subject,srp_erp_crm_task.status,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,srp_erp_crm_task.Priority,srp_erp_crm_status.description as statusDescription,srp_erp_crm_task.description as taskDescription,srp_erp_crm_task.progress,visibility,DATE_FORMAT(srp_erp_crm_task.modifiedDateTime,\'' . $convertFormat . '\') AS updateDate,DATE_FORMAT(srp_erp_crm_task.starDate, \'%D-%b-%y %h:%i %p\') AS starDate,DATE_FORMAT(srp_erp_crm_task.DueDate, \'%D-%b-%y %h:%i %p\') AS DueDate,srp_erp_crm_task.createdUserName as createdbY,srp_erp_crm_task.status,DATE_FORMAT(srp_erp_crm_task.completedDate,\'' . $convertFormat . '\') AS completedDate, srp_employeesdetails.Ename2 as completedBy,srp_erp_crm_task.opportunityID,srp_erp_crm_task.pipelineStageID,srp_erp_crm_task.isClosed,srp_erp_crm_task.projectID,srp_erp_crm_project.projectName,srp_erp_crm_task.comment,srp_erp_crm_task.createdUserID as crtduser,srp_erp_crm_task.documentSystemCode as documentSystemCodetask');
        $this->db->from('srp_erp_crm_task');
        $this->db->join('srp_erp_crm_categories', 'srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID', 'LEFT');
        $this->db->join('srp_erp_crm_status', 'srp_erp_crm_status.statusID = srp_erp_crm_task.status', 'LEFT');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_task.completedBy', 'LEFT');
        $this->db->join('srp_erp_crm_project', 'srp_erp_crm_project.projectID = srp_erp_crm_task.projectID', 'LEFT');
        $this->db->where('taskID', $taskID);
        $this->db->where('srp_erp_crm_categories.documentID', 2);
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('srp_employeesdetails.Ename2,srp_erp_crm_assignees.empID');
        $this->db->from('srp_erp_crm_assignees');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_assignees.empID');
        $this->db->where('MasterAutoID', $taskID);
        $this->db->where('documentID', 2);
        $data['taskAssignee'] = $this->db->get()->result_array();


        $this->db->select('employeeID as isadmin');
        $this->db->from('srp_erp_crm_users');
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

        $this->load->view('system/crm/ajax/load_task_edit_view', $data);
    }

    function load_contactManagement_editView()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $contactID = trim($this->input->post('contactID') ?? '');

        $data['header'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(srp_erp_crm_contactmaster.modifiedDateTime,'" . $convertFormat . "') AS modifydate,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_link.MasterAutoID as masterOrganizationID,srp_erp_crm_contactmaster.createdUserName as contactCreadtedUser,srp_erp_crm_contactmaster.email as contactEmail,srp_erp_crm_contactmaster.fax as contactFax,srp_erp_crm_contactmaster.phoneHome as contactPhoneHome,srp_erp_crm_contactmaster.phoneMobile as contactPhoneMobile,srp_erp_crm_contactmaster.documentSystemCode as documentSystemCodecontact FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = srp_erp_crm_contactmaster.countryID WHERE contactID = " . $contactID . "")->row_array();

        $contactImage = $this->s3->createPresignedRequest('uploads/crm/profileimage/'. $data['header']['contactImage'], '1 hour');
        $data['contactimage'] = $contactImage;
        $data['noimage'] = $this->s3->createPresignedRequest('images/crm/icon-list-contact.png', '1 hour');

        $data['page'] = $this->input->post('page');
        $this->load->view('system/crm/ajax/load_contact_edit_view', $data);
    }

    function load_organizationManagement_editView()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $organizationID = trim($this->input->post('organizationID') ?? '');
        $this->db->select('*,CountryDes,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDate,DATE_FORMAT(modifiedDateTime,\'' . $convertFormat . '\') AS modifydate,srp_erp_crm_organizations.documentSystemCode as documentSystemCodeorganization');
        $this->db->where('organizationID', $organizationID);
        $this->db->from('srp_erp_crm_organizations');
        $this->db->join('srp_erp_countrymaster', 'srp_erp_countrymaster.countryID = srp_erp_crm_organizations.billingCountryID', 'LEFT');
        $data['header'] = $this->db->get()->row_array();
        $data['page'] = trim($this->input->post('page') ?? '');
        $org = $this->s3->createPresignedRequest('uploads/crm/organizationLogo/'. $data['header']['organizationLogo'], '1 hour');
        $data['org'] = $org;
        $data['noimage'] =$this->s3->createPresignedRequest('images/crm/organization.png', '1 hour');
        $this->load->view('system/crm/ajax/load_organization_edit_view', $data);
    }

    public function allCalenderEvents()
    {
        //$start = $this->input->get('start');
        //$end = $this->input->get('end');
        $event_array = array();
        $event_array2 = array();
        $companyID = current_companyID();

        $category = trim($this->input->post('category') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $employs = $this->input->post('employees');
        $issuperadmin = crm_isSuperAdmin();
        $isGroupAdmin = crm_isGroupAdmin();
        $permissiontype = $this->input->post('permissiontype');
        $currentuserID = current_userID();
        $filterCategory = '';
        if (isset($category) && !empty($category)) {
            $filterCategory = " AND srp_erp_crm_task.categoryID = " . $category . "";
        }

        $filterStatus = '';
        if (isset($status) && !empty($status)) {
            $filterStatus = " AND status = " . $status . "";
        }

        $employees = '';
        if (isset($employs) && !empty($employs)) {
            $employees = " AND srp_erp_crm_assignees.empID IN (" . join(',', $employs) . ")";
        }

        $where = "WHERE srp_erp_crm_task.companyID = " . $companyID . $filterCategory . $filterStatus . $employees;


        if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
            $sql2 = "select taskID,subject,starDate,DueDate,DATE_FORMAT(starDate,'%h:%i %p') AS StartTime,backGroundColor from srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID Left JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID AND srp_erp_crm_assignees.documentID=2 " . $where . " Group by taskID";
        } elseif ($permissiontype == 1) {
            $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}'";

            $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

            $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

            $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

            $sql2 = "select taskID,subject,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task1 GROUP BY taskID UNION select taskID,SUBJECT,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task2 GROUP BY taskID UNION select taskID,SUBJECT,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_task3 GROUP BY taskID UNION select taskID,SUBJECT,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_task4 GROUP BY taskID";
        } elseif ($permissiontype == 2) {
            $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

            $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

            $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

            $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

            $sql2 = "select taskID,subject,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task1 GROUP BY taskID UNION select taskID,SUBJECT,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task2 GROUP BY taskID UNION select taskID,SUBJECT,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_task3 GROUP BY taskID UNION select taskID,SUBJECT,starDate,DueDate,DATE_FORMAT( starDate, '%h:%i %p' ) AS StartTime,backGroundColor FROM srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_task4 GROUP BY taskID";
        }

        //$sql2 = "select taskID,subject,starDate,DueDate,DATE_FORMAT(starDate,'%h:%i %p') AS StartTime,backGroundColor from srp_erp_crm_task JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID  " . $where;
        // echo $sql2;
        $result2 = $this->db->query($sql2)->result_array();

        foreach ($result2 as $record2) {
            $record2['starDate'] = date('Y-m-d h:i:s', strtotime($record2['starDate']));
            $date = strtotime("-1 day", strtotime($record2['DueDate']));
            $record2['DueDate'] = date('Y-m-d h:i:s', $date);
            $event_array2[] = array(
                'id' => $record2['taskID'],
                'title' => $record2['subject'] . " " . $record2['StartTime'],
                'start' => $record2['starDate'],
                'end' => $record2['DueDate'],
                //'url' => 'fetchPage(\'system/crm/contact_management\',\'1\',\'Contact\')',
                'color' => $record2['backGroundColor'],
            );
        }
        $arr = array_merge($event_array2);

        /*print_r($arr);*/

        echo json_encode($arr);
    }

    function load_campaign_Management_editView()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $campaignID = trim($this->input->post('campaignID') ?? '');
        $this->db->select('srp_erp_crm_campaignmaster.campaignID,srp_erp_crm_campaignmaster.name,srp_erp_crm_campaignmaster.objective,srp_erp_crm_campaignmaster.description,srp_erp_crm_campaignmaster.comment,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,srp_erp_crm_status.description as statusDescription,DATE_FORMAT(srp_erp_crm_campaignmaster.modifiedDateTime,\'' . $convertFormat . '\') AS updateDate,DATE_FORMAT(srp_erp_crm_campaignmaster.startDate,\'' . $convertFormat . '\') AS startDate,DATE_FORMAT(srp_erp_crm_campaignmaster.endDate,\'' . $convertFormat . '\') AS endDate,DATE_FORMAT(srp_erp_crm_campaignmaster.completedDate,\'' . $convertFormat . '\') AS completedDate,srp_erp_crm_campaignmaster.createdUserName as createdbY, srp_employeesdetails.Ename2 as completedBy,srp_erp_crm_campaignmaster.status,srp_erp_crm_campaignmaster.isClosed,srp_erp_crm_campaignmaster.createdUserID as crtduser,srp_erp_crm_campaignmaster.documentSystemCode as documentSystemCodecamp');
        $this->db->where('campaignID', $campaignID);
        $this->db->from('srp_erp_crm_campaignmaster');
        $this->db->join('srp_erp_crm_categories', 'srp_erp_crm_categories.categoryID = srp_erp_crm_campaignmaster.type');
        $this->db->join('srp_erp_crm_status', 'srp_erp_crm_status.statusID = srp_erp_crm_campaignmaster.status');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_campaignmaster.completedBy', 'LEFT');
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('srp_employeesdetails.Ename2,srp_erp_crm_assignees.empID as empID');
        $this->db->where('MasterAutoID', $campaignID);
        $this->db->where('documentID', 1);
        $this->db->from('srp_erp_crm_assignees');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_assignees.empID');
        $data['taskAssignee'] = $this->db->get()->result_array();

        $this->db->select('firstName,lastName,isAttended');
        $this->db->where('campaignID', $campaignID);
        $this->db->from('srp_erp_crm_attendees');
        $data['taskAttendees'] = $this->db->get()->result_array();

        $this->db->select('employeeID as isadmin');
        $this->db->from('srp_erp_crm_users');
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

        $this->load->view('system/crm/ajax/load_campaign_edit_view', $data);
    }

    function delete_contact_master()
    {
        echo json_encode($this->Crm_modal->delete_contact_master());
    }

    function delete_organization_master()
    {
        echo json_encode($this->Crm_modal->delete_organization_master());
    }

    function campaign_attendess_marked()
    {
        echo json_encode($this->Crm_modal->campaign_attendess_marked());
    }

    function check_isEmail_campaign_type()
    {
        $typeID = trim($this->input->post('categoryID') ?? '');
        echo json_encode($this->Crm_modal->check_isEmail_campaign_type($typeID));
    }

    function load_campaign_multiple_attachemts()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $documentAutoID = trim($this->input->post('campaignID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 1 AND documentAutoID = " . $documentAutoID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_crm_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_all_attachments', $data);
    }

    function load_email_campaign_tab()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $campaignID = trim($this->input->post('campaignID') ?? '');

        $where = "companyID = " . $companyid . " AND campaignID = " . $campaignID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('campaignID,description,emailDescription');
        $this->db->from('srp_erp_crm_campaignmaster');
        $this->db->where($where);
        $data['header'] = $this->db->get()->row_array();

        $where_attendees = "companyID = " . $companyid . " AND campaignID = " . $campaignID . "";
        $this->db->select('CONCAT(firstName," ",lastName) as fullName,email');
        $this->db->from('srp_erp_crm_attendees');
        $this->db->where($where_attendees);
        $data['attendees'] = $this->db->get()->result_array();

        $this->load->view('system/crm/ajax/load_email_campaign_body', $data);
    }

    function load_task_multiple_attachemts()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $documentAutoID = trim($this->input->post('taskID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 2 AND documentAutoID = " . $documentAutoID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_crm_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_all_attachments', $data);
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
            $num = $this->db->get('srp_erp_crm_attachments')->result_array();


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
                $this->db->insert('srp_erp_crm_attachments', $data);
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
            $this->db->delete('srp_erp_crm_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    function load_contact_all_notes()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $contactID = trim($this->input->post('contactID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 6 AND contactID = " . $contactID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_crm_contactnotes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_contact_notes', $data);
    }

    function load_organization_all_notes()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $organizationID = trim($this->input->post('organizationID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 8 AND contactID = " . $organizationID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_crm_contactnotes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_organization_notes', $data);
    }

    function add_contact_notes()
    {
        $this->form_validation->set_rules('contactID', 'Contact ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->add_all_contact_notes());
        }
    }

    function add_organization_notes()
    {
        $this->form_validation->set_rules('organizationID', 'Organization ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->add_all_organization_notes());
        }
    }

    function load_contact_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $contactID = trim($this->input->post('contactID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 6  AND documentAutoID = " . $contactID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_crm_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_all_contact_attachements', $data);
    }

    function load_contact_all_tasks()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $contactID = trim($this->input->post('contactID') ?? '');

        $where = "srp_erp_crm_link.companyID = " . $companyid . " AND srp_erp_crm_link.relatedDocumentID = 6 AND srp_erp_crm_link.relatedDocumentMasterID = " . $contactID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_crm_task.taskID,srp_erp_crm_task.subject,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,\'%D-%b-%y %h:%i %p\') AS starDate,DATE_FORMAT(DueDate,\'%D-%b-%y %h:%i %p\') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,srp_erp_crm_task.documentSystemCode');
        $this->db->from('srp_erp_crm_link');
        $this->db->join('srp_erp_crm_task', 'srp_erp_crm_task.taskID = srp_erp_crm_link.MasterAutoID');
        $this->db->join('srp_erp_crm_categories', 'srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID');
        $this->db->join('srp_erp_crm_status', 'srp_erp_crm_status.statusID = srp_erp_crm_task.status');
        $this->db->join('srp_erp_crm_assignees', 'srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID', 'LEFT');
        $this->db->where($where);
        $this->db->order_by('taskID', 'desc');
        $data['tasks'] = $this->db->get()->result_array();
        $data['masterID'] = $contactID;
        $this->load->view('system/crm/ajax/load_contacts_tasks', $data);
    }

    function load_contact_all_opportunities()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $contactID = trim($this->input->post('contactID') ?? '');

        $where = "srp_erp_crm_link.companyID = " . $companyID . " AND srp_erp_crm_link.documentID = 4 AND srp_erp_crm_link.relatedDocumentID = 6 AND relatedDocumentMasterID = {$contactID}";
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_crm_opportunity.opportunityID,srp_erp_crm_status.description as statusDescription,srp_erp_crm_opportunity.opportunityName,statusColor,statusBackgroundColor,pipelineID,pipelineStageID,srp_erp_crm_opportunity.documentSystemCode');
        $this->db->from('srp_erp_crm_link');
        $this->db->join('srp_erp_crm_opportunity', 'srp_erp_crm_opportunity.opportunityID = srp_erp_crm_link.MasterAutoID', 'LEFT');
        $this->db->join('srp_erp_crm_status', 'srp_erp_crm_status.statusID = srp_erp_crm_opportunity.statusID', 'LEFT');
        $this->db->where($where);
        //$this->db->order_by('taskID', 'desc');
        $data['opportunity'] = $this->db->get()->result_array();
        $data['masterID'] = $contactID;
        $this->load->view('system/crm/ajax/load_contacts_opprtunity', $data);
    }

    function contact_image_upload()
    {
        $this->form_validation->set_rules('contactID', 'Contact ID is missing', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->contact_image_upload());
        }
    }

    function organization_image_upload()
    {
        $this->form_validation->set_rules('organizationID', 'Organization ID is missing', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->organization_image_upload());
        }
    }

    function load_taskRelated_fromLead()
    {
        echo json_encode($this->Crm_modal->load_taskRelated_fromLead());
    }

    function load_taskRelated_fromOpportunity()
    {
        echo json_encode($this->Crm_modal->load_taskRelated_fromOpportunity());
    }

    function load_taskRelated_fromProject()
    {
        echo json_encode($this->Crm_modal->load_taskRelated_fromProject());
    }

    function load_organization_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $organizationID = trim($this->input->post('organizationID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 8  AND documentAutoID = " . $organizationID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_crm_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_all_organization_attachements', $data);
    }

    function load_organization_all_tasks()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $organizationID = trim($this->input->post('organizationID') ?? '');

        $where = "srp_erp_crm_link.companyID = " . $companyid . " AND srp_erp_crm_link.relatedDocumentID = 8 AND srp_erp_crm_link.relatedDocumentMasterID = " . $organizationID . " and srp_erp_crm_link.documentID=2  ";
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_crm_task.taskID,srp_erp_crm_task.subject,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,\'%D-%b-%y %h:%i %p\') AS starDate,DATE_FORMAT(DueDate,\'%D-%b-%y %h:%i %p\') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,srp_erp_crm_task.documentSystemCode');
        $this->db->from('srp_erp_crm_link');
        $this->db->join('srp_erp_crm_task', 'srp_erp_crm_task.taskID = srp_erp_crm_link.MasterAutoID');
        $this->db->join('srp_erp_crm_categories', 'srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID');
        $this->db->join('srp_erp_crm_status', 'srp_erp_crm_status.statusID = srp_erp_crm_task.status');
        $this->db->join('srp_erp_crm_assignees', 'srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID', 'LEFT');
        $this->db->where($where);
        $this->db->group_by('taskID');
        $this->db->order_by('taskID', 'desc');

        $data['tasks'] = $this->db->get()->result_array();
        $data['masterID'] = $organizationID;
        $this->load->view('system/crm/ajax/load_organization_tasks', $data);
    }

    function load_organization_all_contacts()
    {
        $companyid = $this->common_data['company_data']['company_id'];

        $organizationID = trim($this->input->post('organizationID') ?? '');

        $where = "srp_erp_crm_link.documentID = 6 AND relatedDocumentID = 8 AND srp_erp_crm_link.companyID = " . $companyid . " AND  relatedDocumentMasterID = " . $organizationID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_crm_contactmaster.contactID,srp_erp_crm_contactmaster.firstName,srp_erp_crm_contactmaster.lastName,srp_erp_crm_contactmaster.phoneMobile,srp_erp_crm_contactmaster.email,srp_erp_crm_contactmaster.organization,srp_erp_crm_contactmaster.contactImage,srp_erp_crm_contactmaster.documentSystemCode');
        $this->db->where($where);
        $this->db->from('srp_erp_crm_link');
        $this->db->join('srp_erp_crm_contactmaster', 'srp_erp_crm_contactmaster.contactID = srp_erp_crm_link.MasterAutoID', 'LEFT');
        $this->db->order_by('srp_erp_crm_contactmaster.contactID', 'desc');
        $data['header'] = $this->db->get()->result_array();
        $data['masterID'] = $organizationID;

        $this->load->view('system/crm/ajax/load_organization_contacts', $data);
    }

    function settings_users()
    {
        $sys = $this->input->post('sys');
        $data['masterID'] = $this->input->post('masterID');
        $url = '';
        switch (trim($sys)) {
            case 'user':
                $url = 'system/crm/users_management';
                break;
            case 'usergroup':
                $url = 'system/crm/usergroup_management';
                break;

            case 'pipelines':
                $url = 'system/crm/pipelines_management';
                break;

            case 'pipelinedetail':
                $url = 'system/crm/pipelines_stages_management';
                break;

            case 'CampaignTypes':
                $url = 'system/crm/campaign_type_management';
                break;

            case 'documentStatus':
                $url = 'system/crm/document_status_management';
                break;
            case 'leadStatus':
                $url = 'system/crm/lead_status_management';
                break;
            case 'product':
                $url = 'system/crm/product_management';
                break;
            case 'categories':
                $url = 'system/crm/categories_management';
                break;

            case 'leadSource':
                $url = 'system/crm/source_management';
                break;
            case 'criterias':
                $url = 'system/crm/critiria';
                break;

            /*            case 'salestarget':
                            $url = 'system/crm/salesTarget_management';
                            break;*/

            default:
                $url = '';
        }

        $this->load->view($url, $data);
    }

    function srp_erp_add_users()
    {
        $this->form_validation->set_rules('employees[]', 'Employee', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->srp_erp_add_users());
        }
    }

    function fetch_crm_users()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select("srp_erp_crm_users.userID as useridcrm,employeeID,srp_erp_crm_users.employeeName as employeeName,emailID as emailID,srp_erp_crm_users.companyID,activeYN,isSuperAdmin,IFNULL(srp_erp_crm_usergroups.groupName,'-') as usergroup ");
        $this->datatables->from('srp_erp_crm_users');
        $this->datatables->join('srp_erp_crm_usergroupdetails', 'srp_erp_crm_usergroupdetails.userID = srp_erp_crm_users.userID', 'left');
        $this->datatables->join('srp_erp_crm_usergroups', 'srp_erp_crm_usergroups.groupID = srp_erp_crm_usergroupdetails.groupMasterID', 'left');
        $this->datatables->where('srp_erp_crm_users.companyID', $companyID);
        $this->datatables->add_column('edit', '<a onclick="delete_users($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'useridcrm');
        $this->datatables->add_column('activeYN', '$1', 'userAction(useridcrm,activeYN)');
        $this->datatables->add_column('isSuperAdmin', '$1', 'userActionSuperAdmin(useridcrm,isSuperAdmin)');
        echo $this->datatables->generate();
    }

    function delete_user()
    {
        echo json_encode($this->Crm_modal->delete_user());
    }


    function fetch_userDropdown()
    {
        $data_arr = array();
        $companyID = $this->common_data['company_data']['company_id'];
        /*        $employee = $this->db->query("SELECT EIdNo,Ename2,EEmail,DesDescription as designation FROM `srp_employeesdetails` LEFT JOIN srp_erp_crm_users ON EIdNo = employeeID LEFT JOIN `srp_designation`  on EmpDesignationId=DesignationID AND srp_designation.Erp_companyID={$companyID} WHERE srp_employeesdetails.Erp_companyID={$companyID} AND  userID IS NULL AND isDischarged !=1 ")->result_array();*/
        $employee = $this->db->query("SELECT
	EIdNo,Ename2,EEmail,company_name
FROM
	srp_employeesdetails LEFT JOIN srp_erp_company ON srp_employeesdetails.Erp_companyID = srp_erp_company.company_id
WHERE
	Erp_companyID IN (
		SELECT
			srp_erp_companygroupdetails.companyid
		FROM
			srp_erp_companygroupdetails

		WHERE
			srp_erp_companygroupdetails.companygroupID = (
				SELECT
					companygroupID
				FROM
					srp_erp_companygroupdetails
				WHERE
					srp_erp_companygroupdetails.companyID = $companyID
			)
	)")->result_array();
        if (isset($employee)) {
            foreach ($employee as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }
        echo form_dropdown('employees[]', $data_arr, '', 'class="form-control select2" id="employeesID"  multiple="" ');
    }

    function srp_erp_add_usergroup()
    {
        $this->form_validation->set_rules('groupName', 'User Group', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->srp_erp_add_usergroup());
        }
    }

    function fetch_crm_usergroups()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select("groupID,groupName,companyID,isAdmin");
        $this->datatables->from('srp_erp_crm_usergroups');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('edit', '<span class="pull-right"><a onclick="view_group($1)"><span title="View group" rel="tooltip" class="fa fa-users" style="color:rgb(6,2,2);"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_users($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'groupID');
        $this->datatables->add_column('isAdmin', '$1', 'groupAction(groupID,isAdmin)');
        echo $this->datatables->generate();
    }

    function delete_usergroup()
    {
        echo json_encode($this->Crm_modal->delete_usergroup());
    }

    function fetch_userassignedDropdown()
    {
        $data_arr = array();
        $groupID = $this->input->post('groupID');
        $companyID = $this->common_data['company_data']['company_id'];
        $qry = "SELECT srp_erp_crm_users.* FROM `srp_erp_crm_users` LEFT JOIN `srp_erp_crm_usergroupdetails` ON srp_erp_crm_users.userID = srp_erp_crm_usergroupdetails.userID AND  groupMasterID={$groupID} WHERE groupDetailID IS NULL AND srp_erp_crm_users.companyID = {$companyID} AND activeYN=1";
        $employee = $this->db->query($qry)->result_array();
        if (!empty($employee)) {
            foreach ($employee as $row) {
                $data_arr[trim($row['userID'] ?? '')] = trim($row['employeeName'] ?? '');
            }
        }
        echo form_dropdown('employees[]', $data_arr, '', 'class="form-control select2" id="employeesID"  multiple="" ');
    }

    function assign_employee_usergroup()
    {
        $this->form_validation->set_rules('employees[]', 'Employee', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->assign_employee_usergroup());
        }
    }

    function load_assigned_employee()
    {
        $groupID = $this->input->post('groupID');
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select("groupDetailID,employeeName");
        $this->datatables->from('srp_erp_crm_usergroupdetails');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->where('groupMasterID', $groupID);
        $this->datatables->add_column('edit', '<a onclick="delete_usersgroupdetail($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupDetailID');
        echo $this->datatables->generate();
    }

    function delete_usergroupdetail()
    {
        echo json_encode($this->Crm_modal->delete_usergroupdetail());
    }

    function save_pipleline()
    {
        $this->form_validation->set_rules('pipeLineName', 'Pipe Line Name', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->save_pipleline());
        }
    }

    function fetch_crm_pipeline()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select("pipeLineID,pipeLineName,opportunityYN,projectYN,leadYN");
        $this->datatables->from('srp_erp_crm_pipeline');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('opportunityYN', '$1', 'action_pipeline(opportunityYN)');
        $this->datatables->add_column('projectYN', '$1', 'action_pipeline(projectYN)');
        $this->datatables->add_column('leadYN', '$1', 'action_pipeline(leadYN)');
        $this->datatables->add_column('edit', '<a onclick="configuration_page(\'pipelinedetail\',$1)"><span title="View" rel="tooltip" class="fa fa-eye" data-original-title="Edit"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_pipeline($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'pipeLineID');
        echo $this->datatables->generate();
    }

    function fetch_crm_pipeline_stage()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $pipeLineID = $this->input->post('masterID');
        $this->datatables->select("pipeLineID,pipeLineDetailID,stageName,probability,sortOrder");
        $this->datatables->from('srp_erp_crm_pipelinedetails');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->where('pipeLineID', $pipeLineID);
        $this->datatables->add_column('edit', '<div class="hide updatediv" id="updatepipeline_$1"><button id="" type="button" onclick="pipelinestage_update($1);" class=" btn btn-primary btn-xs">Save</button>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<button id="" type="button" onclick="pipelinestage_cancel($1);" class=" btn btn-default btn-xs">Cancel</button></div>
<div class="canceldiv" id="editpipeline_$1"><a id=""  onclick="edit_pipeline($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_pipeline($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></div>', 'pipeLineDetailID');
        $this->datatables->add_column('stageName', '$1', 'pipelinestage_action(pipeLineDetailID,stageName,stagename,pipeLineID)');
        $this->datatables->add_column('probability', '$1', 'pipelinestage_action(pipeLineDetailID,probability,percentage,pipeLineID)');
        $this->datatables->add_column('sortOrder', '$1', 'pipelinestage_action(pipeLineDetailID,sortOrder,order,pipeLineID)');

        echo $this->datatables->generate();
    }

    function save_piplelineStage()
    {
        $this->form_validation->set_rules('stageName', 'Stage Name', 'trim|required');
        $this->form_validation->set_rules('probability', 'Probability', 'trim|required|numeric');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $filter = '';
            /*if update*/
            $pipeLineDetailID = $this->input->post('pipeLineDetailID');
            if (isset($pipeLineDetailID)) {
                $filter = "AND pipeLineDetailID !={$pipeLineDetailID}";
            }
            $companyID = $this->common_data['company_data']['company_id'];
            $probability = $this->input->post('probability');
            $masterID = $this->input->post('masterID');

            $validate = $this->db->query("SELECT sum(probability) as probability FROM `srp_erp_crm_pipelinedetails` WHERE companyID='{$companyID}' AND pipeLineID='{$masterID}' $filter")->row_array();
            if (!empty($validate)) {
                $valid = $validate['probability'] + $probability;

                $bal = 100 - $validate['probability'];
                if ($valid > 100) {
                    echo json_encode(array('e', 'Available probability to enter ' . $bal . ' %'));
                    exit;
                }
            }
            if ($probability > 100) {
                echo json_encode(array('e', 'Probability should not be greater than 100 %'));
                exit;
            }

            echo json_encode($this->Crm_modal->save_piplelineStage());
        }
    }

    function delete_pipelineDetail()
    {
        echo json_encode($this->Crm_modal->delete_pipelineDetail());
    }

    function delete_pipeline()
    {
        echo json_encode($this->Crm_modal->delete_pipeline());
    }

    function loadpipeline()
    {
        echo $this->Crm_modal->loadpipeline();
    }

    function activateUser()
    {
        $value = $this->input->post('value');
        $masterID = $this->input->post('masterID');

        $this->db->update('srp_erp_crm_users', array('activeYN' => $value), array('userID' => $masterID));
        echo json_encode(array('e', 'Successfully Updated'));
        exit;
    }

    function activateSuperAdmin()
    {
        $value = $this->input->post('value');
        $masterID = $this->input->post('masterID');

        $this->db->update('srp_erp_crm_users', array('isSuperAdmin' => $value), array('userID' => $masterID));
        echo json_encode(array('e', 'Successfully Updated'));
        exit;
    }

    function activateGroupAdmin()
    {
        $value = $this->input->post('value');
        $masterID = $this->input->post('masterID');

        $this->db->update('srp_erp_crm_usergroups', array('isAdmin' => $value), array('groupID' => $masterID));
        $this->db->update('srp_erp_crm_usergroupdetails', array('adminYN' => $value), array('groupMasterID' => $masterID));
        echo json_encode(array('e', 'Successfully Updated'));
        exit;
    }

    function show_opportunity_pipeline()
    {
        echo $this->Crm_modal->show_opportunity_pipeline();
    }

    function fetch_campaignTypes()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select("typeID,description");
        $this->datatables->from('srp_erp_crm_campaigntypes');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('description', '$1', 'xeditable_edit(typeID,description,campaignType)');
        $this->datatables->add_column('edit', '<a onclick="deleteCampaignType($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'typeID');
        echo $this->datatables->generate();
    }

    function srp_erp_save_campaignType()
    {
        $this->form_validation->set_rules('campaignType', 'Campaign Type', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->srp_erp_save_campaignType());
        }
    }

    function deleteCampaignType()
    {
        echo json_encode($this->Crm_modal->deleteCampaignType());
    }

    function fetch_doc_status()
    {

        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];
        $filter = "companyID = $companyID ";
        if ($masterID != '') {
            $filter .= "AND srp_erp_crm_status.documentID=$masterID";
        }

        $this->datatables->select("statusColor,statusBackgroundColor,statusID,srp_erp_crm_documents.description as document,srp_erp_crm_status.description as description");
        $this->datatables->from('srp_erp_crm_documents');
        $this->datatables->join('srp_erp_crm_status', 'srp_erp_crm_status.documentID = srp_erp_crm_documents.documentID', 'LEFT');
        $this->datatables->where($filter);
        $this->datatables->add_column('edit', '<a onclick="editDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
<a onclick="deleteDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'statusID');
        $this->datatables->add_column('color', '<div style="text-align: center">$1</div>', 'statuscolor(statusColor)');
        $this->datatables->add_column('backgroundColor', '<div style="text-align: center">$1</div>', 'statuscolor(statusBackgroundColor)');
        echo $this->datatables->generate();
    }

    function create_document_status()
    {
        $this->form_validation->set_rules('documentID', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('status', 'status', 'trim|required');
        $this->form_validation->set_rules('color', 'Status Color', 'trim|required');
        $this->form_validation->set_rules('backgroundColor', 'Background Color', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->create_document_status());
        }
    }

    function deleteDocumentStatus()
    {
        echo json_encode($this->Crm_modal->deleteDocumentStatus());
    }

    function srp_erp_leadStatus()
    {
        $this->form_validation->set_rules('leadStatus', 'leadStatus', 'trim|required');


        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->srp_erp_save_leadStatus());
        }
    }

    function fetch_leadTypes()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select("statusID,description");
        $this->datatables->from('srp_erp_crm_leadstatus');
        $this->datatables->where('companyID', $companyID);

        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="editleadstatus($1)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> |&nbsp;&nbsp;<a onclick="deleteLeadStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'statusID');
        echo $this->datatables->generate();
    }

    function deleteLeadStatus()
    {
        echo json_encode($this->Crm_modal->deleteLeadStatus());
    }

    function srp_erp_save_product()
    {
        $this->form_validation->set_rules('product', 'Product', 'trim|required');
        $this->form_validation->set_rules('defaultUnitOfMeasureID', 'Unit of Measure', 'trim|required');


        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->srp_erp_save_product());
        }
    }

    function fetch_product()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select("productID,productName,IFNULL(srp_erp_segment.description,'Not Available') as segmentID,srp_erp_unit_of_measure.UnitShortCode as UOMshort,IFNULL(subscriptionAmount,0) as subscriptionAmount,IFNULL(ImplementationAmount,0) as ImplementationAmount,IFNULL(otherAmount,0) as otherAmount,IFNULL(srp_erp_itemmaster.itemDescription,'-') as Itemdescription");
        $this->datatables->from('srp_erp_crm_products');
        $this->datatables->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_crm_products.itemAutoID', 'left');
        $this->datatables->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_crm_products.uomID', 'left');
        $this->datatables->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_crm_products.segmentID', 'left');
        $this->datatables->where('srp_erp_crm_products.companyID', $companyID);
        //$this->datatables->add_column('segmentID', 'productName', 'productName');
        //$this->datatables->add_column('subscriptionAmountalign', '<div class="pull-right"><b>$1</b></div>', 'xeditable_edit(productID,subscriptionAmount,productsubamt)');
        //$this->datatables->add_column('ImplementationAmountalign', '<div class="pull-right"><b>$1 </b></div>', 'xeditable_edit(productID,ImplementationAmount,impubamt)');
        //$this->datatables->add_column('otherAmountAmountalign', '<div class="pull-right"><b>$1 </b></div>', 'xeditable_edit(productID,otherAmount,otheramt)');
        $this->datatables->add_column('edit', '<span class="pull-right"><span style="color:#8B0000; font-size:13px;" onclick="link_item_master($1)"><span title="Link Item" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="deleteproduct($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a> |&nbsp;&nbsp;<a href="#" onclick="editproducts($1)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a>', 'productID');
        echo $this->datatables->generate();
    }

    function delete_product()
    {
        echo json_encode($this->Crm_modal->delete_product());
    }

    function get_alldocumentStatus()
    {
        $statusID = $this->input->post('statusID');
        $data = $this->db->query("select * from srp_erp_crm_status WHERE statusID={$statusID}")->row_array();
        echo json_encode($data);
    }

    function create_category_status()
    {
        $this->form_validation->set_rules('documentID', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('textColor', 'Text Color', 'trim|required');
        $this->form_validation->set_rules('backgroundColor', 'Background Color', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->create_category_status());
        }
    }

    function fetch_cat_status()
    {
        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];
        $filter = "companyID = $companyID ";
        if ($masterID != '') {
            $filter .= "AND srp_erp_crm_categories.documentID=$masterID";
        }

        $this->datatables->select("textColor,backGroundColor,categoryID,srp_erp_crm_documents.description as document,srp_erp_crm_categories.description as category");
        $this->datatables->from('srp_erp_crm_documents');
        $this->datatables->join('srp_erp_crm_categories', 'srp_erp_crm_categories.documentID = srp_erp_crm_documents.documentID', 'LEFT');
        $this->datatables->where($filter);
        $this->datatables->add_column('edit', '<a onclick="editDocumentStatus($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
<a onclick="deleteDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'categoryID');
        $this->datatables->add_column('textColor', '<div style="text-align: center">$1</div>', 'statuscolor(textColor)');
        $this->datatables->add_column('backGroundColor', '<div style="text-align: center">$1</div>', 'statuscolor(backGroundColor)');
        echo $this->datatables->generate();
    }

    function deleteCaegory()
    {
        echo json_encode($this->Crm_modal->deleteCaegory());
    }

    function get_all_categories()
    {
        $categoryID = $this->input->post('categoryID');
        $data = $this->db->query("select * from  srp_erp_crm_categories WHERE categoryID={$categoryID}")->row_array();
        echo json_encode($data);
    }

    function updateLeadStatus()
    {


        $name = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');
        $table = '';
        $col = '';
        $col = '';
        switch ($name) {
            case 'leadStatus':
                $table = 'srp_erp_crm_leadstatus';
                $col = 'description';
                $autoID = 'statusID';

                break;
            case 'campaignType':
                $table = 'srp_erp_crm_campaigntypes';
                $col = 'description';
                $autoID = 'typeID';

                break;
            case 'product':
                $table = 'srp_erp_crm_products';
                $col = 'productName';
                $autoID = 'productID';
                break;
            case 'productsubamt':
                $table = 'srp_erp_crm_products';
                $col = 'subscriptionAmount';
                $autoID = 'productID';
                break;
            case 'impubamt':
                $table = 'srp_erp_crm_products';
                $col = 'ImplementationAmount';
                $autoID = 'productID';
                break;
            case 'otheramt':
                $table = 'srp_erp_crm_products';
                $col = 'otherAmount';
                $autoID = 'productID';
                break;
        }
        $this->db->update($table, array($col => $value), array($autoID => $pk));
        return true;
    }

    function create_source()
    {
        $this->form_validation->set_rules('documentID', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');


        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->create_source());
        }
    }

    function fetch_crm_source()
    {
        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];
        $filter = "companyID = $companyID ";
        if ($masterID != '') {
            $filter .= "AND srp_erp_crm_source.documentID=$masterID";
        }
        $this->datatables->select("sourceID,srp_erp_crm_documents.description as document,srp_erp_crm_source.description as source");
        $this->datatables->from('srp_erp_crm_documents');
        $this->datatables->join('srp_erp_crm_source', 'srp_erp_crm_source.documentID = srp_erp_crm_documents.documentID', 'LEFT');
        $this->datatables->where($filter);
        $this->datatables->add_column('edit', '<a onclick="editDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
<a onclick="deleteDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'sourceID');
        echo $this->datatables->generate();
    }

    function get_source_data()
    {
        $sourceID = $this->input->post('sourceID');
        $data = $this->db->query("SELECT * FROM srp_erp_crm_source WHERE sourceID='{$sourceID}'")->row_array();
        echo json_encode($data);
    }

    function delete_source()
    {
        echo json_encode($this->Crm_modal->delete_source());
    }


    function save_sales_person_target()
    {
        $this->form_validation->set_rules('userID', 'Person', 'trim|required');
        $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('dateFrom', 'Date From', 'trim|required|validate_date');
        $this->form_validation->set_rules('dateTo', 'Date To', 'trim|required|validate_date');
        $this->form_validation->set_rules('targetValue', 'Target Value', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $dateTo = strtotime($this->input->post('dateTo'));
            $dateFrom = strtotime($this->input->post('dateFrom'));
            if ($dateTo > $dateFrom) {
                echo json_encode($this->Crm_modal->save_sales_person_target());
            } else {
                echo json_encode(array('e', 'Date To should be greater than Date From'));
            }
        }
    }

    function salesTarget_ManagementView()
    {

        $companyid = $this->common_data['company_data']['company_id'];
        $projectID = trim($this->input->post('projectID') ?? '');

        $where = "srp_erp_crm_salestarget.companyID = " . $companyid . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_crm_salestarget.salesTargetID, srp_erp_crm_project.projectName, srp_erp_crm_salestarget.projectID');
        $this->db->from('srp_erp_crm_salestarget');
        $this->db->join('srp_erp_crm_project', 'srp_erp_crm_project.projectID = srp_erp_crm_salestarget.projectID', 'LEFT');
        $this->db->where($where);
        $this->db->group_by("srp_erp_crm_salestarget.projectID");
        $data['header'] = $this->db->get()->result_array();

        $this->load->view('system/crm/ajax/load_salesTarget_management', $data);
    }

    function fetch_salesTarget_detail_table()
    {

        echo json_encode($this->Crm_modal->fetch_salesTarget_detail_table());
    }

    function delete_salesTarget_newPerson()
    {
        echo json_encode($this->Crm_modal->delete_salesTarget_newPerson());
    }

    function load_project_all_salesTarget()
    {

        $companyid = $this->common_data['company_data']['company_id'];
        $projectID = trim($this->input->post('projectID') ?? '');

        $where = "srp_erp_crm_salestarget.companyID = " . $companyid . " AND projectID = " . $projectID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_crm_salestarget.salesTargetID, employeeName, srp_erp_crm_salestarget.projectID,srp_erp_crm_salestarget.userID');
        $this->db->from('srp_erp_crm_salestarget');
        $this->db->join('srp_erp_crm_users', 'srp_erp_crm_salestarget.userID = srp_erp_crm_users.employeeID', 'LEFT');
        $this->db->where($where);
        $this->db->group_by("srp_erp_crm_salestarget.userID");
        $data['header'] = $this->db->get()->result_array();

        $this->load->view('system/crm/ajax/load_all_project_salesTarget', $data);
    }

    function send_email_campaign()
    {

        echo json_encode($this->Crm_modal->send_email_campaign());
    }

    function load_quotationManagement_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchCampaign') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $currentuserID = current_userID();
        $issuperadmin = crm_isSuperAdmin();
        $search_string = '';
        $convertFormat = convert_date_format_sql();
        if (isset($text) && !empty($text)) {
            $search_string = " AND name Like '%" . $text . "%'";
        }
        $filterStatus = '';
        if (isset($status) && $status != '') {
            if ($status == 2 || $status == 3) {
                if ($status == 2) {
                    $filterStatus = " AND confirmedYN = 1 AND approvedYN = 1";
                } elseif ($status == 3) {
                    $filterStatus = " AND confirmedYN = 1  AND approvedYN = 2";
                }
            } elseif ($status == 1) {
                $filterStatus = " AND confirmedYN = " . $status . " AND ((approvedYN IS NULL) or (approvedYN = 0))";
            } elseif ($status == 4) {
                    $filterStatus = " AND ((confirmedYN = 1 AND approvedYN = 3) or (confirmedYN = 1 AND approvedYN = 4) or (confirmedYN = 1 AND approvedYN = 5))";
            }
        }

        if ($issuperadmin['isSuperAdmin'] == 1) {
            $where_admin = "srp_erp_crm_quotation.companyID = " . $companyid . $search_string . $filterStatus;

            $data['header'] = $this->db->query("SELECT erp_contractAutoID,quotationAutoID,DATE_FORMAT(quotationDate,'" . $convertFormat . "') AS quotationDate,DATE_FORMAT(quotationExpDate,'" . $convertFormat . "') AS quotationExpDate,quotationCode, srp_erp_crm_organizations.Name as fullname,CurrencyCode,srp_erp_crm_quotation.confirmedYN,opportunityName,srp_erp_crm_quotation.opportunityID,srp_erp_crm_quotation.approvedYN FROM srp_erp_crm_quotation LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_quotation.customerID LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_crm_quotation.transactionCurrencyID LEFT JOIN srp_erp_crm_opportunity ON srp_erp_crm_opportunity.opportunityID = srp_erp_crm_quotation.opportunityID WHERE $where_admin ORDER BY srp_erp_crm_quotation.quotationAutoID DESC")->result_array();
            $data['page'] = "master";
        } else {
            $where_user = "srp_erp_crm_quotation.companyID = " . $companyid . $search_string . $filterStatus;

            $data['header'] = $this->db->query("SELECT erp_contractAutoID,quotationAutoID,DATE_FORMAT(quotationDate,'" . $convertFormat . "') AS quotationDate,DATE_FORMAT(quotationExpDate,'" . $convertFormat . "') AS quotationExpDate,quotationCode, srp_erp_crm_organizations.Name as fullname,CurrencyCode,srp_erp_crm_quotation.confirmedYN,opportunityName,srp_erp_crm_quotation.opportunityID,srp_erp_crm_quotation.approvedYN FROM srp_erp_crm_quotation LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_quotation.customerID LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_crm_quotation.transactionCurrencyID LEFT JOIN srp_erp_crm_opportunity ON srp_erp_crm_opportunity.opportunityID = srp_erp_crm_quotation.opportunityID WHERE $where_user ORDER BY srp_erp_crm_quotation.quotationAutoID DESC")->result_array();
            $data['page'] = "master";
        }
        $data['crmedittype']=$this->input->post('crmedittype');

        $this->load->view('system/crm/ajax/load_quotation_management', $data);
    }

    function save_quotation()
    {
        echo json_encode($this->Crm_modal->save_quotation());
    }

    function get_estimate_quotation_link()
    {
        echo json_encode($this->Crm_modal->get_estimate_quotation_link());
    }

    function load_quotation_autoGeneratedID()
    {
        echo json_encode($this->Crm_modal->load_quotation_autoGeneratedID());
    }

    function update_quotation_header()
    {
        $searches = $this->input->post('search');
        $productid = $this->input->post('productID');
        $itemautoid = $this->input->post('itemautoid');
        $quotationAutoID = $this->input->post('quotationAutoID');
        $qutdetailID = $this->input->post('qutdetailID');
        
        $confirmedYN = $this->input->post('confirmedYN');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required|validate_date');
        //$this->form_validation->set_rules('customerID', 'Organization', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        //$this->form_validation->set_rules('expiryDate', 'Expiry Date', 'trim|required|validate_date');

        /* if (isset($confirmedYN) && $confirmedYN == 1) {
            $this->form_validation->set_rules("contactpersonemail", "Person's Email", 'trim|required|valid_email');
        } */

        if (!empty($itemautoid) && !empty($productid)) {
            foreach ($itemautoid as $key => $search) {
                if (isset($qutdetailID) && $qutdetailID[$key]) {
                    $check_exist = $this->db->select('productID,itemAutoID')->from('srp_erp_crm_quotationdetails')->where(['contractAutoID' => $quotationAutoID,'contractDetailsAutoID' => $qutdetailID[$key]])->get()->row();
                    if (!empty($check_exist) && ($check_exist->productID == 0 || $check_exist->productID == '' || $check_exist->productID == null)) {
                        $num = ($key + 1);
                        $this->form_validation->set_rules("itemautoid[{$key}]", "Line {$num} Item", 'trim|required');
                    } elseif (!empty($check_exist) && ($check_exist->productID != 0 || $check_exist->productID != '' || $check_exist->productID != null)) {
                        $num = ($key + 1);
                        $this->form_validation->set_rules("productID[{$key}]", "Line {$num} Product", 'trim|required');
                    }
                } else {
                    $policy = (getPolicyValues('IFE', 'All')==1?getPolicyValues('IFE', 'All'):0);
                    if ($policy == 0) {
                        $num = ($key + 1);
                        $this->form_validation->set_rules("productID[{$key}]", "Line {$num} Product", 'trim|required');
                    } else {
                        $num = ($key + 1);
                        $this->form_validation->set_rules("itemautoid[{$key}]", "Line {$num} Item", 'trim|required');
                    }
                }
            }
        }
        
       


        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->save_quotation());
        }
    }

    function load_quotation_detail_item_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $contractAutoID = trim($this->input->post('quotationAutoID') ?? '');

            $convertFormat = convert_date_format_sql();
            $this->db->select('*,srp_erp_crm_products.productName,DATE_FORMAT(expectedDeliveryDate,"' . $convertFormat . '") AS expectedDeliveryDate');
            $this->db->from('srp_erp_crm_quotationdetails');
            $this->db->join('srp_erp_crm_products', 'srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID', 'LEFT');
            $this->db->where('srp_erp_crm_quotationdetails.companyID', $companyid);
            $this->db->where('srp_erp_crm_quotationdetails.contractAutoID', "$contractAutoID");
            $data['header'] = $this->db->get()->result_array();

            $data['masterrec'] = $this->db->query("SELECT srp_erp_crm_quotation.*,srp_erp_crm_organizations.billingAddress,srp_erp_crm_organizations.Name as organizationname,DATE_FORMAT(quotationDate,'" . $convertFormat . "') AS quotationDateformat,DATE_FORMAT(quotationExpDate,'" . $convertFormat . "') AS quotationExpDateformat FROM `srp_erp_crm_quotation` LEFT JOIN srp_erp_crm_organizations on srp_erp_crm_organizations.organizationID = srp_erp_crm_quotation.customerID  where srp_erp_crm_quotation.companyID = '{$companyid}' And quotationAutoID = '{$contractAutoID}'")->row_array();



        $this->load->view('system/crm/ajax/load_quotation_detail_table', $data);
    }

    function load_quotation_header()
    {
        echo json_encode($this->Crm_modal->load_quotation_header());
    }

    function fetch_productCode()
    {
        echo json_encode($this->Crm_modal->fetch_productCode());
    }


    function save_quotation_detail()
    {

        $searches = $this->input->post('search');

        foreach ($searches as $key => $search) {
            $this->form_validation->set_rules("productID[{$key}]", 'Product', 'trim|required');
           // $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Price', 'trim|required|greater_than[0]');
           // $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'UOM', 'trim|required');
          //  $this->form_validation->set_rules("quantityRequested[{$key}]", 'QTY', 'trim|required');
          //  $this->form_validation->set_rules("expectedDeliveryDate[{$key}]", 'Expected Delivery Date', 'trim|required');
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
            echo json_encode($this->Crm_modal->save_quotation_detail());
        }
    }

    function delete_quotation_detail()
    {
        echo json_encode($this->Crm_modal->delete_quotation_detail());
    }

    function quotation_print_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $quotationAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('quotationAutoID') ?? '');

        $where_header = "qut.companyID = '{$companyID}' AND qut.quotationAutoID = '{$quotationAutoID}'";
        $this->db->select('quotationCode,DATE_FORMAT(quotationDate,"' . $convertFormat . '") AS quotationDate,referenceNo, org.Name as organizationName,org.billingAddress as orgAddress,org.telephoneNo,CurrencyCode,DATE_FORMAT(quotationExpDate,"' . $convertFormat . '") AS quotationExpDate,quotationNarration,DATE_FORMAT(qut.confirmedDate,"' . $convertFormat . '") AS qutConfirmDate,qut.confirmedByName as qutConfirmedUser,termsAndConditions,qut.quotationPersonName,qut.quotationPersonNumber,qut.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlacesqut,qut.confirmedYN as confirmedYNqut,qut.approvedYN as approvedYN1,qut.approvalComment as approvalComment,
	concat(firstName,\' \',lastName) as contactfirstname,org.billingAddress as orgaddress,org.`Name` as Organizationname,quotationPersonEmail,qut.quotationNarration');
        $this->db->from('srp_erp_crm_quotation qut');
        $this->db->join('srp_erp_crm_organizations org', 'org.organizationID = qut.customerID', 'LEFT');
        $this->db->join('srp_erp_currencymaster cm', 'qut.transactionCurrencyID = cm.currencyID', 'LEFT');
        $this->db->join('srp_erp_crm_contactmaster contactmaster', 'contactmaster.contactID = qut.quotationPersonID', 'LEFT');
        $this->db->where($where_header);
        $data['master'] = $this->db->get()->row_array();

        // $where_detail = "srp_erp_crm_quotationdetails.companyID = '{$companyID}' AND srp_erp_crm_quotationdetails.contractAutoID = '{$quotationAutoID}'";
        // $this->db->select('srp_erp_crm_quotationdetails.*,IFNULL(DATE_FORMAT(srp_erp_crm_quotationdetails.expectedDeliveryDate,"' . $convertFormat . '"),\'-\' ) AS expectedDeliveryDateformated,CONCAT_WS(\' - \',IF(LENGTH(srp_erp_crm_products.productName),srp_erp_crm_products.productName,NULL),IF(LENGTH(srp_erp_crm_quotationdetails.`comment`),srp_erp_crm_quotationdetails.`comment`,NULL)) as productnamewithcomment');
        // $this->db->from('srp_erp_crm_quotationdetails');
        // $this->db->join('srp_erp_crm_products', 'srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID', 'LEFT');
        // $this->db->where($where_detail);
        // $data['detail'] = $this->db->get()->result_array();

        $data['detail'] = $this->db->query("SELECT srp_erp_crm_quotationdetails.*, 
                                            DATE_FORMAT(srp_erp_crm_quotationdetails.expectedDeliveryDate, '%M %d %Y') AS expectedDeliveryDateformated,
                                            IFNULL(srp_erp_crm_products.productName ,
                                            CONCAT(srp_erp_itemmaster.itemDescription,' - ',srp_erp_itemmaster.itemSystemCode)) as productnamewithcomment
                                            FROM `srp_erp_crm_quotationdetails`
                                            LEFT JOIN srp_erp_crm_products ON srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID
                                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_crm_quotationdetails.itemAutoID
                                            WHERE srp_erp_crm_quotationdetails.companyID = '{$companyID}' 
                                            AND contractAutoID = '{$quotationAutoID}'")->result_array();
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $html = $this->load->view('system/crm/crm_quotation_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function delete_crm_quotation()
    {
        echo json_encode($this->Crm_modal->delete_crm_quotation());
    }

    function load_opprtunity_BaseOrganization()
    {
        echo json_encode($this->Crm_modal->load_opprtunity_BaseOrganization());
    }

    function load_expenseClaimManagement_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchCampaign') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $currentuserID = current_userID();
        $issuperadmin = crm_isSuperAdmin();
        $search_string = '';
        $convertFormat = convert_date_format_sql();
        if (isset($text) && !empty($text)) {
            $search_string = " AND name Like '%" . $text . "%'";
        }
        $filterStatus = '';
        if (isset($status) && !empty($status)) {
            $filterStatus = " AND confirmedYN = " . $status . "";
        }

        if ($issuperadmin['isSuperAdmin'] == 1) {
            $where_admin = "srp_erp_expenseclaimmaster.companyID = " . $companyID . " AND isCRM = 1 " . $search_string . $filterStatus;

            $data['header'] = $this->db->query("SELECT expenseClaimMasterAutoID,expenseClaimCode,DATE_FORMAT(expenseClaimDate,'" . $convertFormat . "') AS expenseClaimDate,claimedByEmpName,comments,confirmedYN,approvedYN FROM srp_erp_expenseclaimmaster WHERE $where_admin ORDER BY srp_erp_expenseclaimmaster.expenseClaimMasterAutoID DESC")->result_array();

            $data['page'] = "master";
        } else {
            $where_user = "srp_erp_expenseclaimmaster.companyID = " . $companyID . " AND isCRM = 1 " . $search_string . $filterStatus;

            $data['header'] = $this->db->query("SELECT quotationAutoID,DATE_FORMAT(quotationDate,'" . $convertFormat . "') AS quotationDate,DATE_FORMAT(quotationExpDate,'" . $convertFormat . "') AS quotationExpDate,quotationCode, srp_erp_crm_organizations.Name as fullname,CurrencyCode,srp_erp_expenseclaimmaster.confirmedYN,opportunityName,srp_erp_expenseclaimmaster.opportunityID FROM srp_erp_expenseclaimmaster LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_expenseclaimmaster.customerID LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_expenseclaimmaster.transactionCurrencyID LEFT JOIN srp_erp_crm_opportunity ON srp_erp_crm_opportunity.opportunityID = srp_erp_expenseclaimmaster.opportunityID WHERE $where_user ORDER BY srp_erp_expenseclaimmaster.quotationAutoID DESC")->result_array();

            $data['page'] = "master";
        }

        $this->load->view('system/crm/ajax/load_expenseclaim_management', $data);
    }


    function delete_master_notes_allDocuments()
    {
        echo json_encode($this->Crm_modal->delete_master_notes_allDocuments());
    }

    function edit_master_notes_allDocuments()
    {
        echo json_encode($this->Crm_modal->edit_master_notes_allDocuments());
    }

    function attendees_excel_uplode()
    {
        $tmp_file = $this->input->post('filename');
        $campaignID = $this->input->post('campaignID');

        if (isset($_FILES['document_file']['size']) && $_FILES['document_file']['size'] > 0) {
            $filename = $_FILES["document_file"]["tmp_name"];
            $i = 0;
            $file = fopen($filename, "r");
            while (($getData = fgetcsv($file, 10000, ",")) !== false) {
                if ($i > 0) {
                    $data['campaignID'] = $campaignID;
                    $data['prefix'] = $getData[0];
                    $data['firstName'] = $getData[1];
                    $data['lastName'] = $getData[2];
                    $data['organization'] = $getData[3];
                    $data['email'] = $getData[4];
                    $data['phoneMobile'] = $getData[5];
                    $data['isImported'] = 1;
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_attendees', $data);
                }
                $i++;
            }
            fclose($file);
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Campaign Attendees Uploadded Succesfully.'));
            }
        } else {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Please Select CSV File .'));
        }
    }

    function convert_attendees_to_lead()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Attendees', 'required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->convert_attendees_to_lead());
        }
    }


    function load_taskManagement_project_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $category = trim($this->input->post('category') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $priority = trim($this->input->post('priority') ?? '');
        $assigneesID = trim($this->input->post('assignees') ?? '');
        $issuperadmin = crm_isSuperAdmin();
        $currentuserID = current_userID();
        $data['type'] = trim($this->input->post('type') ?? '');
        $opporunityID = trim($this->input->post('opporunityID') ?? '');
        $pipeLineDetailID = trim($this->input->post('pipeLineDetailID') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND subject Like '%" . $text . "%'";
        }
        $filterCategory = '';
        if (isset($category) && !empty($category)) {
            $filterCategory = " AND srp_erp_crm_task.categoryID = " . $category . "";
        }
        $filterStatus = '';
        if (isset($status) && !empty($status)) {
            $filterStatus = " AND status = " . $status . "";
        }
        $filterpriority = '';
        if (isset($priority) && !empty($priority)) {
            $filterpriority = " AND Priority = " . $priority . "";
        }
        $filterAssigneesID = '';
        if (isset($assigneesID) && !empty($assigneesID)) {
            $filterAssigneesID = " AND srp_erp_crm_assignees.empID = " . $assigneesID . "";
        }

        $filteropporunityID = '';
        if (isset($opporunityID) && !empty($opporunityID)) {
            $filteropporunityID = " AND projectID = " . $opporunityID . "";
        }

        $filterpipeLineDetailID = '';
        if (isset($pipeLineDetailID) && !empty($pipeLineDetailID)) {
            $filterpipeLineDetailID = " AND pipelineStageID = " . $pipeLineDetailID . "";
        }

        if ($issuperadmin['isSuperAdmin'] == 1) {
            $where_admin = "srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filteropporunityID . $filterpipeLineDetailID;

            $data['headercount'] = '';
            $data['header'] = $this->db->query("SELECT srp_erp_crm_task.taskID,srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.subject,srp_erp_crm_task.createdUserID as createdUserIDtask,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,statusBackgroundColor,visibility,DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,srp_erp_crm_task.projectID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID WHERE $where_admin GROUP BY srp_erp_crm_task.taskID ORDER BY taskID DESC ")->result_array();
        } else {
            $where1 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filterpipeLineDetailID . $filteropporunityID;

            $where2 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filterpipeLineDetailID . $filteropporunityID;

            $where3 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filterpipeLineDetailID . $filteropporunityID;

            $where4 = "srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_task.companyID = " . $companyid . $search_string . $filterCategory . $filterStatus . $filterpriority . $filterAssigneesID . $filterpipeLineDetailID . $filteropporunityID;

            $data['header'] = $this->db->query("SELECT srp_erp_crm_task.taskID,srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.createdUserName,srp_erp_crm_task.createdDateTime, srp_erp_crm_task.subject,srp_erp_crm_task.createdUserID as createdUserIDtask,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,statusBackgroundColor,visibility,DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,srp_erp_crm_task.projectID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID WHERE $where1 GROUP BY srp_erp_crm_task.taskID UNION SELECT srp_erp_crm_task.taskID,srp_erp_crm_task.createdUserName,srp_erp_crm_task.createdDateTime,srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.subject,srp_erp_crm_task.createdUserID as createdUserIDtask,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,statusBackgroundColor,visibility,DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,srp_erp_crm_task.projectID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID WHERE $where2 GROUP BY srp_erp_crm_task.taskID  UNION SELECT srp_erp_crm_task.taskID,srp_erp_crm_task.createdUserName,srp_erp_crm_task.createdDateTime,srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.subject,srp_erp_crm_task.createdUserID as createdUserIDtask,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,statusBackgroundColor,visibility,DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,srp_erp_crm_task.projectID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue WHERE $where3 GROUP BY srp_erp_crm_task.taskID UNION SELECT srp_erp_crm_task.taskID,srp_erp_crm_task.createdUserName,srp_erp_crm_task.createdDateTime,srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.subject,srp_erp_crm_task.createdUserID as createdUserIDtask,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,statusBackgroundColor,visibility,DATE_FORMAT(starDate,'%D-%b-%y %h:%i %p') AS starDate,DATE_FORMAT(DueDate,'%D-%b-%y %h:%i %p') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,srp_erp_crm_task.projectID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID WHERE $where4 GROUP BY srp_erp_crm_task.taskID ORDER BY taskID DESC")->result_array();
            $data['headercount'] = '';
            
        }
        $this->load->view('system/crm/ajax/load_task_master', $data);
    }

    function fetch_userDropdown_employee()
    {
        $data_arr = array();
        $companyID = $this->common_data['company_data']['company_id'];
        /*        $employee = $this->db->query("SELECT EIdNo,Ename2,EEmail,DesDescription as designation FROM `srp_employeesdetails` LEFT JOIN srp_erp_crm_users ON EIdNo = employeeID LEFT JOIN `srp_designation`  on EmpDesignationId=DesignationID AND srp_designation.Erp_companyID={$companyID} WHERE srp_employeesdetails.Erp_companyID={$companyID} AND  userID IS NULL AND isDischarged !=1 ")->result_array();*/
        $employee = $this->db->query("SELECT
            EIdNo,
            Ename2,
            EEmail,
            company_name 
        FROM
            srp_employeesdetails
            LEFT JOIN srp_erp_company ON srp_employeesdetails.Erp_companyID = srp_erp_company.company_id 
        WHERE
        Erp_companyID IN ( SELECT srp_erp_companygroupdetails.companyid FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companygroupID = ( SELECT companygroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = $companyID )) 
        AND NOT EXISTS ( SELECT * FROM srp_erp_crm_users WHERE srp_erp_crm_users.employeeID = srp_employeesdetails.EIdNo AND Erp_companyID = $companyID)")->result_array();
        
        if (isset($employee)) {
            foreach ($employee as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }
        
        echo form_dropdown('employees[]', $data_arr, '', 'class="form-control select2" id="employeesID"  multiple="" ');
    }

    function fetch_lead_status()
    {
        echo json_encode($this->Crm_modal->fetch_lead_status());
    }

    function fetch_product_details()
    {
        echo json_encode($this->Crm_modal->fetch_product_details());
    }

    function fetch_contacts_dashboard()
    {
        $companyid = current_companyID();
        $where_admin = "srp_erp_crm_contactmaster.companyID = " . $companyid;
        $data['header'] = $this->db->query("SELECT contactID,CONCAT(firstName,' ',lastName) as namecontactfull,
	srp_erp_crm_contactmaster.createdUserName,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.createdUserName as createdUserNamecrm,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 WHERE $where_admin GROUP BY srp_erp_crm_contactmaster.contactID ORDER BY contactID DESC ")->result_array();
        $this->load->view('system/crm/ajax/load_contact_master_crm', $data);
    }

    function fetch_contact_detail_dd()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $contactID = trim($this->input->post('contactID') ?? '');

        $data['header'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(srp_erp_crm_contactmaster.modifiedDateTime,'" . $convertFormat . "') AS modifydate,srp_erp_crm_organizations.Name as linkedorganization,srp_erp_crm_link.MasterAutoID as masterOrganizationID,srp_erp_crm_contactmaster.createdUserName as contactCreadtedUser,srp_erp_crm_contactmaster.email as contactEmail,srp_erp_crm_contactmaster.fax as contactFax,srp_erp_crm_contactmaster.phoneHome as contactPhoneHome,srp_erp_crm_contactmaster.phoneMobile as contactPhoneMobile FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = srp_erp_crm_contactmaster.countryID WHERE contactID = " . $contactID . "")->row_array();
        //echo $this->db->last_query();
        $this->load->view('system/crm/ajax/load_contact_edit_view_dd_dashboard', $data);
    }

    function fetch_organization_dashboard()
    {
        $companyid = current_companyID();
        $data['header'] = $this->db->query("SELECT * FROM `srp_erp_crm_organizations` where companyID = $companyid")->result_array();
        $this->load->view('system/crm/ajax/load_organization_dashboard_view', $data);
    }

    function load_contacts_dashboard()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $year = trim($this->input->post('year') ?? '');
        $employeeID = $this->input->post('employeeID');
        $datefrom = trim($this->input->post('datefrom') ?? '');
        $dateto = trim($this->input->post('dateto') ?? '');
        $convertFormat = convert_date_format_sql();


        $where_admin = "srp_erp_crm_contactmaster.companyID = " . $companyid;
        $data['header'] = $this->db->query("SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.createdUserName as createdUserNamecrm,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'" . $convertFormat . "') as createddatecontact,srp_erp_crm_contactmaster.documentSystemCode FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 WHERE $where_admin AND (DATE( srp_erp_crm_contactmaster.createdDateTime ) BETWEEN '$datefrom' AND '$dateto')  GROUP BY srp_erp_crm_contactmaster.contactID ORDER BY contactID DESC ")->result_array();
        $this->load->view('system/crm/ajax/load_contact_master_dashboard_dd', $data);
    }

    function load_organizationdd()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $datefrom = trim($this->input->post('datefrom') ?? '');
        $dateto = trim($this->input->post('dateto') ?? '');
        $search_string = '';
        $convertFormat = convert_date_format_sql();


        $where_admin = "srp_erp_crm_organizations.companyID = " . $companyid;
        $data['header'] = $this->db->query("SELECT organizationID,createdUserName,Name,email,organizationLogo,billingAddress,telephoneNo,DATE_FORMAT(createdDateTime,'" . $convertFormat . "') AS createdDate,srp_erp_crm_organizations.documentSystemCode FROM srp_erp_crm_organizations WHERE $where_admin AND (DATE( srp_erp_crm_organizations.createdDateTime ) BETWEEN '$datefrom' AND '$dateto') ORDER BY organizationID DESC ")->result_array();
        $this->load->view('system/crm/ajax/load_organization_master_dashboard_dd', $data);
    }

    function fetch_crm_pipeline_project_dd()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select("pipeLineID,pipeLineName,opportunityYN,projectYN,leadYN");
        $this->datatables->from('srp_erp_crm_pipeline');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('opportunityYN', '$1', 'action_pipeline(opportunityYN)');
        $this->datatables->add_column('projectYN', '$1', 'action_pipeline(projectYN)');
        $this->datatables->add_column('leadYN', '$1', 'action_pipeline(leadYN)');
        $this->datatables->add_column('edit', '<a onclick="piepelineview(\'pipelinedetail\',$1,\'$2\')"><span title="Stages" rel="tooltip" class="fa fa-eye" data-original-title="Edit"></span></a>&nbsp;&nbsp;&nbsp;', 'pipeLineID,pipeLineName');
        echo $this->datatables->generate();
    }

    function fetch_pipelicename()
    {
        echo json_encode($this->Crm_modal->fetch_pipelicename());
    }

    function load_subtask_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $taskid = trim($this->input->post('taskID') ?? '');
        $data['detail'] = $this->db->query("SELECT subtask.*,DATE_FORMAT(subtask.startDate,'" . $convertFormat . "') AS startDateTask,DATE_FORMAT(subtask.endDate,'" . $convertFormat . "') AS endDateTask FROM srp_erp_crm_subtasks subtask where subtask.companyID = '{$companyid}' And subtask.taskID = '{$taskid}' 	ORDER BY
	subtask.subTaskID DESC")->result_array();
        $this->load->view('system/crm/ajax/subtask_view', $data);
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
            $this->form_validation->set_rules("indays[{$key}]", 'In Days', 'trim|required');
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
            echo json_encode($this->Crm_modal->AddSubTaskDetail());
        }
    }

    function start_subtask()
    {
        $this->form_validation->set_rules('subtaskid', 'Sub Task ID', 'trim|required');
        $this->form_validation->set_rules('taskid', 'Task ID', 'trim|required');
        $companyid = current_companyID();
        $taskid = trim($this->input->post('taskid') ?? '');
        $subtasksessionstrat = $this->db->query("SELECT status FROM `srp_erp_crm_subtasksessions` where  companyID = $companyid  AND taskID = $taskid And `status` = 1")->row_array();
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if (!empty($subtasksessionstrat)) {
                echo json_encode(array('w', 'cannot start!'));
            } else {
                echo json_encode($this->Crm_modal->save_sub_task_Enable());
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
            echo json_encode($this->Crm_modal->stop_subtask());
        }
    }

    function load_subtask_details()
    {
        echo json_encode($this->Crm_modal->load_subtask_details());
    }

    function load_subtsk_status()
    {
        $this->form_validation->set_rules('subTaskID', 'Sub Task ID', 'trim|required');
        $this->form_validation->set_rules('taskID', 'Task ID', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->load_subtsk_status());
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

        $subtaskstatus = $this->db->query("SELECT `status` FROM `srp_erp_crm_subtasks` where companyID = $companyid ANd subTaskID = $subtaskid And taskID = $taskid")->row_array();


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
                echo json_encode($this->Crm_modal->save_subTask_status());
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


        $data['chat'] = $this->db->query("SELECT chat.*,empdetails.Ename2 as employeename,CONCAT( MONTHNAME( DATE_FORMAT(createdDateTime, '%Y-%m-%d' )),' ',(DATE_FORMAT( createdDateTime, '%d' ))) AS datemonth  FROM srp_erp_crm_chat chat LEFT JOIN srp_employeesdetails empdetails on empdetails.EIdNo = chat.empID where companyID = $companyid And taskID = $currenttaskid
And subTaskID = $subtaskid ORDER BY chatID ASC")->result_array();
        $this->load->view('system/crm/ajax/subtask_chat_windows', $data);
    }

    function sub_task_comment()
    {
        echo json_encode($this->Crm_modal->save_sub_task_comment());
    }

    function attachment_subTask()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $subtaskid = trim($this->input->post('subTaskID') ?? '');
        $data['subtaskid'] = $subtaskid;
        $where = "mastertbl.companyID = " . $companyid . " AND documentID = '10'  AND documentAutoID = " . $subtaskid . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('mastertbl.*');
        $this->db->from('srp_erp_crm_attachments mastertbl');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/subtask_attacchment', $data);
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
            $num = $this->db->get('srp_erp_crm_attachments')->result_array();
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
                $this->db->insert('srp_erp_crm_attachments', $data);
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

    function start_date_est_date_validation()
    {
        echo json_encode($this->Crm_modal->start_date_est_date_validation());
    }

    function end_date_est_date_validation()
    {
        echo json_encode($this->Crm_modal->end_date_est_date_validation());
    }

    function crm_task_close_ischk()
    {
        echo json_encode($this->Crm_modal->crm_task_close_ischk());
    }

    function crm_is_subtask_exist()
    {
        echo json_encode($this->Crm_modal->crm_is_subtask_exist());
    }

    function crm_subtask_task_dashboardrpt()
    {
        $data["details"] = $this->Crm_modal->get_crm_task_subtask_rpt();
        $data["type"] = "html";
        echo $html = $this->load->view('system/crm/ajax/load_subtask_task_rpt', $data, true);
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
srp_erp_crm_subtasks
where
companyID = $companyid
ANd taskID = $taskid
ANd subTaskID = $subtaskid")->row_array();
        echo json_encode($data);
    }

    function fetch_tasks_employee_detailsubtask()
    {
        echo json_encode($this->Crm_modal->fetch_tasks_employee_detailsubtask());
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
            echo json_encode($this->Crm_modal->update_subtaask_detail());
        }
    }

    function delete_subtask_attachments()
    {
/*         $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments/CRM/SubTask");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(false);
        } else {
            $this->db->delete('srp_erp_crm_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        }
 */

        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $result = $this->s3->delete($myFileName);
        /** end of AWS s3 delete object */
        if ($result) {
            $this->db->delete('srp_erp_crm_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    function load_subtask_detail_edit()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $taskid = trim($this->input->post('taskID') ?? '');
        $data['detail'] = $this->db->query("SELECT subtask.*,DATE_FORMAT(subtask.startDate,'" . $convertFormat . "') AS startDateTask,DATE_FORMAT(subtask.endDate,'" . $convertFormat . "') AS endDateTask FROM srp_erp_crm_subtasks subtask where subtask.companyID = '{$companyid}' And subtask.taskID = '{$taskid}'")->result_array();
        $this->load->view('system/crm/ajax/subtask_view_edit', $data);
    }

    function load_subtask_chats_edit()
    {
        $subtaskid = trim($this->input->post('subTaskID') ?? '');
        $currenttaskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data['subtaskid'] = $subtaskid;
        $data['taskID'] = $currenttaskid;


        $data['chat'] = $this->db->query("SELECT chat.*,empdetails.Ename2 as employeename,CONCAT( MONTHNAME( DATE_FORMAT(createdDateTime, '%Y-%m-%d' )),' ',(DATE_FORMAT( createdDateTime, '%d' ))) AS datemonth  FROM srp_erp_crm_chat chat LEFT JOIN srp_employeesdetails empdetails on empdetails.EIdNo = chat.empID where companyID = $companyid And taskID = $currenttaskid
And subTaskID = $subtaskid ORDER BY chatID ASC")->result_array();
        $this->load->view('system/crm/ajax/subtask_chat_windows_taskedit', $data);
    }

    function crm_campaigns()
    {
        echo json_encode($this->Crm_modal->crm_is_campaigns());
    }

    function crm_projects()
    {
        echo json_encode($this->Crm_modal->crm_projects());
    }

    function load_contacts_dashboard_contact_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $where_admin = "srp_erp_crm_contactmaster.companyID = " . $companyid;
        $data['header'] = $this->db->query("SELECT contactID,firstName,lastName,phoneMobile,srp_erp_crm_contactmaster.createdUserName as createdUserNamecrm,srp_erp_crm_contactmaster.email,organization,contactImage,srp_erp_crm_organizations.Name as linkedorganization,CONCAT(firstName,\" \",lastName) as namecontact,DATE_FORMAT(srp_erp_crm_contactmaster.createdDateTime,'" . $convertFormat . "') as createddatecontact,srp_erp_crm_contactmaster.documentSystemCode FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_link ON srp_erp_crm_link.MasterAutoID = srp_erp_crm_contactmaster.contactID AND srp_erp_crm_link.documentID = 6 LEFT JOIN srp_erp_crm_organizations ON srp_erp_crm_organizations.organizationID = srp_erp_crm_link.relatedDocumentMasterID AND srp_erp_crm_link.documentID = 6 WHERE $where_admin GROUP BY srp_erp_crm_contactmaster.contactID ORDER BY contactID DESC ")->result_array();
        $this->load->view('system/crm/ajax/load_contact_master_dashboard_dd', $data);
    }

    function load_dashboard_organizationdd_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $where_admin = "srp_erp_crm_organizations.companyID = " . $companyid;
        $data['header'] = $this->db->query("SELECT organizationID,createdUserName,Name,email,organizationLogo,billingAddress,telephoneNo,DATE_FORMAT(createdDateTime,'" . $convertFormat . "') AS createdDate,srp_erp_crm_organizations.documentSystemCode FROM srp_erp_crm_organizations WHERE $where_admin ORDER BY organizationID DESC ")->result_array();
        $this->load->view('system/crm/ajax/load_organization_master_dashboard_dd', $data);
    }

    function load_opportunity_all_product()
    {
        $companyid = $this->common_data['company_data']['company_id'];

        $opportunityid = trim($this->input->post('opportunityid') ?? '');
        $leadid = $this->db->query("select leadID from srp_erp_crm_opportunity WHERE companyID = $companyid AND opportunityID = $opportunityid")->row_array();


        $where = "srp_erp_crm_opportunityproducts.companyID = " . $companyid . " AND opportunityID = " . $opportunityid . "";
        $this->db->select('opportunityProductID,srp_erp_crm_products.productName,srp_erp_crm_opportunityproducts.productDescription,price,srp_erp_currencymaster.CurrencyCode as reportingcurrency,companyReportingCurrencyExchangeRate,companyReportingCurrencyDecimalPlaces,transactioncurrency.CurrencyCode as transactioncurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces,companylocalcurrency.CurrencyCode as currencycodelocal,companyLocalCurrencyExchangeRate,companyLocalCurrencyDecimalPlaces,srp_erp_crm_opportunityproducts.subscriptionAmount,srp_erp_crm_opportunityproducts.ImplementationAmount');
        $this->db->from('srp_erp_crm_opportunityproducts');
        $this->db->join('srp_erp_crm_products', 'srp_erp_crm_opportunityproducts.productID = srp_erp_crm_products.productID');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_crm_opportunityproducts.companyReportingCurrencyID = srp_erp_currencymaster.currencyID');
        $this->db->join('srp_erp_currencymaster transactioncurrency', 'srp_erp_crm_opportunityproducts.transactionCurrencyID = transactioncurrency.currencyID');
        $this->db->join('srp_erp_currencymaster companylocalcurrency', 'srp_erp_crm_opportunityproducts.companyLocalCurrencyID = companylocalcurrency.currencyID');
        $this->db->where($where);
        $this->db->order_by('opportunityProductID', 'desc');
        $data['header'] = $this->db->get()->result_array();
        $data['masterID'] = $opportunityid;


        $where1 = "srp_erp_crm_leadproducts.companyID = " . $companyid . " AND srp_erp_crm_leadproducts.leadID = '{$leadid['leadID']}'";
        $this->db->select('leadProductID,srp_erp_crm_products.productName,srp_erp_crm_leadproducts.productDescription,price,srp_erp_currencymaster.CurrencyCode as reportingcurrency,companyReportingCurrencyExchangeRate,companyReportingCurrencyDecimalPlaces,transactioncurrency.CurrencyCode as transactioncurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces,companylocalcurrency.CurrencyCode as currencycodelocal,companyLocalCurrencyExchangeRate,companyLocalCurrencyDecimalPlaces,srp_erp_crm_leadproducts.subscriptionAmount,srp_erp_crm_leadproducts.ImplementationAmount');
        $this->db->from('srp_erp_crm_leadproducts');
        $this->db->join('srp_erp_crm_products', 'srp_erp_crm_leadproducts.productID = srp_erp_crm_products.productID');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_crm_leadproducts.companyReportingCurrencyID = srp_erp_currencymaster.currencyID');
        $this->db->join('srp_erp_currencymaster transactioncurrency', 'srp_erp_crm_leadproducts.transactionCurrencyID = transactioncurrency.currencyID');
        $this->db->join('srp_erp_currencymaster companylocalcurrency', 'srp_erp_crm_leadproducts.companyLocalCurrencyID = companylocalcurrency.currencyID');
        $this->db->where($where1);
        $this->db->order_by('leadProductID', 'desc');

        $data['lead'] = $this->db->get()->result_array();


        $this->load->view('system/crm/ajax/load_opp_products', $data);
    }

    function add_opportunity_product()
    {
        $this->form_validation->set_rules('leadID', 'lead ID', 'trim|required');
        $this->form_validation->set_rules('productID', 'Product Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('price', 'Price', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->add_opportunity_product());
        }
    }

    function fetch_crm_closingcritiria()
    {
        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select("criteriaID,Description");
        $this->datatables->from('srp_erp_crm_closingcriterias');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('edit', '<a onclick="editDocumentStatuscritiria($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
<a onclick="deleteDocumentcritiria($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'criteriaID');
        echo $this->datatables->generate();
    }

    function create_criteria()
    {

        $this->form_validation->set_rules('description', 'Description', 'trim|required');


        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->create_criteria());
        }
    }

    function get_critiria_data()
    {
        $criteriaID = $this->input->post('criteriaID');
        $data = $this->db->query("SELECT * FROM srp_erp_crm_closingcriterias WHERE criteriaID='{$criteriaID}'")->row_array();
        echo json_encode($data);
    }

    function delete_critiria()
    {
        echo json_encode($this->Crm_modal->delete_critiria());
    }

    function pipeline_probabilitiy_cal()
    {
        echo json_encode($this->Crm_modal->pipeline_probabilitiy_cal());
    }

    function quotation_print_view_new()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $quotationAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('quotationAutoID') ?? '');
        $data['Genqutso'] = 0;
        $where_header = "qut.companyID = '{$companyID}' AND qut.quotationAutoID = '{$quotationAutoID}'";
        $this->db->select('quotationCode,DATE_FORMAT(quotationDate,"' . $convertFormat . '") AS quotationDate,referenceNo, org.Name as organizationName,org.billingAddress as orgAddress,org.telephoneNo,CurrencyCode,qut.quotationPersonEmail as email,DATE_FORMAT(quotationExpDate,"' . $convertFormat . '") AS quotationExpDate,quotationNarration,DATE_FORMAT(qut.confirmedDate,"' . $convertFormat . '") AS qutConfirmDate,qut.confirmedByName as qutConfirmedUser,qut.confirmedYN as confirmedYNqut,termsAndConditions,qut.quotationPersonNumber,qut.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlacesqut,concat(contact.firstName," ",contact.lastName) as quotationPersonName,qut.approvedYN as approvedYN1,qut.approvalComment as approvalComment,qut.quotationNarration');
        $this->db->from('srp_erp_crm_quotation qut');
        $this->db->join('srp_erp_crm_organizations org', 'org.organizationID = qut.customerID', 'LEFT');
        $this->db->join('srp_erp_currencymaster cm', 'qut.transactionCurrencyID = cm.currencyID', 'LEFT');
        $this->db->join('srp_erp_crm_contactmaster contact', 'contact.contactID = qut.quotationPersonID', 'LEFT');
        $this->db->where($where_header);
        $data['master'] = $this->db->get()->row_array();

        // $where_detail = "srp_erp_crm_quotationdetails.companyID = '{$companyID}' AND srp_erp_crm_quotationdetails.contractAutoID = '{$quotationAutoID}'";
        // $this->db->select('srp_erp_crm_quotationdetails.*,
        //                    IFNULL(DATE_FORMAT( srp_erp_crm_quotationdetails.expectedDeliveryDate, \'%d-%m-%Y\'),\'-\' ) AS expectedDeliveryDateformated,
        //                    CONCAT_WS(\' - \',IF(LENGTH(srp_erp_crm_products.productName),srp_erp_crm_products.productName,NULL),
        //                    IF(LENGTH(srp_erp_crm_quotationdetails.`comment`),srp_erp_crm_quotationdetails.`comment`,NULL)) as productnamewithcomment');
        // $this->db->from('srp_erp_crm_quotationdetails');
        // $this->db->join('srp_erp_crm_products', 'srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID', 'LEFT');
        // $this->db->order_by('contractDetailsAutoID', 'ASC');
        // $this->db->where($where_detail);
        // $data['detail'] = $this->db->get()->result_array();

        $data['detail'] = $this->db->query("SELECT srp_erp_crm_quotationdetails.*, 
                                            DATE_FORMAT(srp_erp_crm_quotationdetails.expectedDeliveryDate, '%M %d %Y') AS expectedDeliveryDateformated,
                                            IFNULL(srp_erp_crm_products.productName ,
                                            CONCAT(srp_erp_itemmaster.itemDescription,' - ',srp_erp_itemmaster.itemSystemCode)) as productnamewithcomment
                                            FROM `srp_erp_crm_quotationdetails`
                                            LEFT JOIN srp_erp_crm_products ON srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID
                                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_crm_quotationdetails.itemAutoID
                                            WHERE srp_erp_crm_quotationdetails.companyID = '{$companyID}' 
                                            AND contractAutoID = '{$quotationAutoID}'")->result_array();
        $data['logo'] = mPDFImage;
        $data['type'] = $this->input->post('type');
        /* if ($this->input->post('html')) {
             $data['logo'] = htmlImage;
         }*/
        $data['view'] = $this->load->view('system/crm/crm_quotation_print_new_view', $data, true);
        echo json_encode($data);
        /*if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }*/
    }

    function fetch_link_item()
    {
        $search = $_REQUEST["sSearch"];


        $this->datatables->select('itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,srp_erp_itemcategory.description as SubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode,\' - \',itemDescription) as disitem', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID');
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_crm_products WHERE srp_erp_crm_products.itemAutoID = srp_erp_itemmaster.itemAutoID AND companyID =' . $this->common_data['company_data']['company_id'] . ' )');
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);

        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        } else {
            $this->datatables->where('srp_erp_itemmaster.mainCategory IN ("Inventory","Non Inventory","Service")');
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
       /*if($search){
            $this->datatables->like('srp_erp_itemmaster.itemSystemCode', $search);
            $this->datatables->or_like('srp_erp_itemmaster.itemDescription', $search);
            $this->datatables->or_like('srp_erp_itemmaster.defaultUnitOfMeasure', $search);
            $this->datatables->or_like('srp_erp_itemmaster.seconeryItemCode', $search);
        }*/
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        //$this->datatables->add_column('edit', '<input id="selectItem_$1" value="$1" type="checkbox" onclick="ItemsSelectedSync(this)">', 'itemAutoID');
        //$this->datatables->add_column('edit', '<input id="selectItem_$1" value="$1" type="checkbox" onclick="ItemsSelectedSync(this)">', 'itemAutoID');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="linkItem_$1" name="linkItem" type="radio"  value="$1" class="radioChk" data-itemAutoID="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        echo $this->datatables->generate();
    }

    function link_item()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(false);
        } else {
            echo json_encode($this->Crm_modal->link_item());
        }
    }

    function quotation_organization_detail_fetch()
    {
        $companyid = current_companyID();
        $organizationID = $this->input->post('organizationID');

        $ContactPerson = $this->db->query("SELECT Link.MasterAutoID, CONCAT(firstName,' ',lastName) as fullname,contact.contactID as contactID FROM srp_erp_crm_link Link LEFT JOIN srp_erp_crm_contactmaster contact on contact.contactID = Link.MasterAutoID WHERE Link.companyID = '{$companyid}' AND relatedDocumentID = 8 AND Link.documentID = 6  AND relatedDocumentMasterID = '{$organizationID}'")->result_array();
        $data_arr = array('' => 'Select Contact Person');
        if (!empty($ContactPerson)) {
            foreach ($ContactPerson as $row) {
                $data_arr[trim($row['contactID'] ?? '')] = trim($row['fullname'] ?? '');
            }
        }
            echo form_dropdown('contactPersonID', $data_arr, '', 'class="form-control select2" onchange="fetch_contact_details(this.value,2,null,null)" id="contactPersonID"');
    }
    function fetch_contact_details()
    {
        $contactid = $this->input->post('contactid');
        $companyid = current_companyID();
        $data = $this->db->query("select email,phoneMobile from srp_erp_crm_contactmaster where companyID = '{$companyid}' And contactID = '{$contactid}'")->row_array();
        echo json_encode($data);
    }
    function load_quotation_details()
    {
        $this->form_validation->set_rules('QuotationAutoID', 'Quotation Auto ID', 'trim|required');
        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->load_quotation_details());
        }
    }
    function delete_quotation_detail_line()
    {
        echo json_encode($this->Crm_modal->delete_quotation_detail_line());
    }
    function itemtax()
    {
        echo json_encode($this->Crm_modal->itemtax());
    }
    function qut_voucher_details_exist()
    {
        $quotationAutoID= trim($this->input->post('quotationAutoID') ?? '');
        $companyid = current_companyID();
        $data = $this->db->query("select * from srp_erp_crm_quotationdetails WHERE companyID = '{$companyid}' AND contractAutoID= '{$quotationAutoID}' ")->result_array();
        echo json_encode($data);
    }
    function re_open_quotation_detail()
    {
        echo json_encode($this->Crm_modal->re_open_quotation_detail());
    }
    function details_generate_qu_so()
    {
        $this->form_validation->set_rules('QuotationAutoID', 'Quotation ID', 'required');
        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(false);
        } else {
            $data['detail_fetch'] = $this->Crm_modal->fetch_qu_so_det();
            $data['Genqutso'] = $this->input->post('Genqutso');

            $companyID = $this->common_data['company_data']['company_id'];
            $convertFormat = convert_date_format_sql();
            $quotationAutoID = $this->input->post('QuotationAutoID');
            $Genqutso = $this->input->post('Genqutso');

            $where_header = "qut.companyID = '{$companyID}' AND qut.quotationAutoID = '{$quotationAutoID}'";
            $this->db->select('quotationCode,DATE_FORMAT(quotationDate,"' . $convertFormat . '") AS quotationDate,referenceNo, org.Name as organizationName,org.billingAddress as orgAddress,org.telephoneNo,CurrencyCode,qut.quotationPersonEmail as email,DATE_FORMAT(quotationExpDate,"' . $convertFormat . '") AS quotationExpDate,quotationNarration,DATE_FORMAT(qut.confirmedDate,"' . $convertFormat . '") AS qutConfirmDate,qut.confirmedByName as qutConfirmedUser,qut.confirmedYN as confirmedYNqut,termsAndConditions,qut.quotationPersonNumber,qut.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlacesqut,concat(contact.firstName," ",contact.lastName) as quotationPersonName,qut.approvedYN as approvedYN1,qut.approvalComment as approvalComment,qut.quotationNarration');
            $this->db->from('srp_erp_crm_quotation qut');
            $this->db->join('srp_erp_crm_organizations org', 'org.organizationID = qut.customerID', 'LEFT');
            $this->db->join('srp_erp_currencymaster cm', 'qut.transactionCurrencyID = cm.currencyID', 'LEFT');
            $this->db->join('srp_erp_crm_contactmaster contact', 'contact.contactID = qut.quotationPersonID', 'LEFT');
            $this->db->where($where_header);
            $data['master'] = $this->db->get()->row_array();

            $where_detail = "srp_erp_crm_quotationdetails.companyID = '{$companyID}' AND srp_erp_crm_quotationdetails.contractAutoID = '{$quotationAutoID}'";
            $this->db->select('srp_erp_crm_quotationdetails.*,IFNULL(DATE_FORMAT( srp_erp_crm_quotationdetails.expectedDeliveryDate, \'%d-%m-%Y\'),\'-\' ) AS expectedDeliveryDateformated,CONCAT_WS(\' - \',IF(LENGTH(srp_erp_crm_products.productName),srp_erp_crm_products.productName,NULL),IF(LENGTH(srp_erp_crm_quotationdetails.`comment`),srp_erp_crm_quotationdetails.`comment`,NULL)) as productnamewithcomment');
            $this->db->from('srp_erp_crm_quotationdetails');
            $this->db->join('srp_erp_crm_products', 'srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID', 'LEFT');
            $this->db->order_by('contractDetailsAutoID', 'ASC');
            $this->db->where($where_detail);
            $data['detail'] = $this->db->get()->result_array();

            $data['logo'] = mPDFImage;
            $data['type'] = $this->input->post('type');
            $data['view'] = $this->load->view('system/crm/crm_quotation_print_new_view', $data, true);

            echo json_encode($data);
        }
    }
    function generate_so_qut()
    {
        $this->form_validation->set_rules('qutID', 'Quotation ID', 'required');
        $this->form_validation->set_rules('typeID', 'Type', 'required');

        if ($this->form_validation->run() == false) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Crm_modal->generate_so_qut());
        }
    }
    
}
