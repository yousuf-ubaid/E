<?php

use App\Exception\InvalidOperationException;

class Access_menu extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('Access_menu_model');
        $this->load->helpers('procurement');
        $this->load->helpers('grv');
        $this->load->helpers('loan_helper');
        $this->load->helper('template_paySheet');
        $this->load->helper('employee');
        $this->load->helper('pos');
        $this->load->helper('cookie');
        $this->load->helper('asset_management');
        $this->load->service('NavigationService');

    }

    function saveNavigationgroupSetup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $userGroupID = $this->input->post('userGroupID');

        $navigationID = $this->input->post('navigationID');

        $this->db->trans_start();


        /*delete*/
        $this->db->delete('srp_erp_navigationusergroupsetup', array('userGroupID' => $userGroupID));
        if (!empty($navigationID) && $navigationID != "") {

            $this->db->query("INSERT srp_erp_navigationusergroupsetup (companyID,userGroupID ,navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist) SELECT $companyID,$userGroupID,navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist FROM srp_erp_navigationmenus WHERE navigationMenuID IN ({$navigationID})");

        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Failed. please try again');
            echo json_encode(true);
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Access rights for this group has been updated successfully');
            echo json_encode(true);
        }

    }

    /*load navigation usergroup setup */

    function load_navigation_usergroup_setup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $userGroupID = $this->input->post('userGroupID');
        $data['data'] = false;
        if (!empty($userGroupID)) {
            $navigationMenuID = $this->db->query("SELECT navigationMenuID FROM `srp_erp_moduleassign` WHERE `companyID` = {$companyID}")->result_array();
            /*    $data['data'] = $this->db->query("SELECT srp_erp_navigationmenus.*, IFNULL(srp_erp_navigationusergroupsetup.navigationMenuID, 0) AS navID FROM srp_erp_navigationmenus LEFT JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID AND userGroupID={$userGroupID} ORDER BY levelNo , sortOrder")->result_array(); */
            if (!empty($navigationMenuID)) {
                $data['data'] = $this->db->query("SELECT srp_erp_navigationmenus.*, IFNULL(srp_erp_navigationusergroupsetup.navigationMenuID, 0) AS navID ,srp_erp_navigationusergroupsetup.Add_Edit,srp_erp_navigationusergroupsetup.Views,srp_erp_navigationusergroupsetup.Print_Excel FROM srp_erp_navigationmenus LEFT JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID AND userGroupID = {$userGroupID} WHERE srp_erp_navigationmenus.navigationMenuID NOT IN (SELECT srp_erp_navigationmenus.navigationMenuID FROM srp_erp_navigationmenus LEFT JOIN `srp_erp_moduleassign` ON srp_erp_navigationmenus.navigationMenuID = srp_erp_moduleassign.navigationMenuID AND companyID = '{$companyID}' WHERE masterID IS NULL AND srp_erp_moduleassign.moduleID IS NULL) ORDER BY levelNo , sortOrder")->result_array();
            }
        }

        $html = $this->load->view('system/navigation/ajax-erp_navigation_group_setup', $data, true);
        echo $html;
    }

    function fetch_group_access_employee()
    {
        $userGroup = $this->input->post('userGroup');
        $companyID = current_companyID();

        $filteruserGroup = $this->input->post('userGroup');
        $filtercompanyID = $this->input->post('companyID');


        $companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        if (!empty($companyGroup)) {

            $this->datatables->select("srp_employeesdetails.ECode AS empID, employeeNavigationID, srp_erp_usergroups.description as description,  Ename2 as emloyeeName, srp_erp_employeenavigation.userGroupID, CONCAT(IFNULL(company_code, ''), ' - ', IFNULL( company_name, '')) AS company, company_id", false);
            $this->datatables->from('srp_erp_companygroupdetails');
            $this->datatables->join('srp_erp_employeenavigation', 'srp_erp_companygroupdetails.companyID = srp_erp_employeenavigation.companyID', 'INNER');
            $this->datatables->join('srp_employeesdetails', 'empID = EIdNo', 'INNER');
            $this->datatables->join('srp_erp_usergroups', 'srp_erp_employeenavigation.userGroupID = srp_erp_usergroups.userGroupID', 'INNER');
            $this->datatables->join('srp_erp_company', 'company_id = srp_erp_employeenavigation.companyID', 'INNER');
            $this->datatables->where('companyGroupID', $companyGroup['companyGroupID']);
            if (isset($filtercompanyID) && $filtercompanyID != '') {
                $this->datatables->where('srp_erp_employeenavigation.companyID', $filtercompanyID);
            }
            if (isset($filteruserGroup) && $filteruserGroup != '') {
                $this->datatables->where('srp_erp_employeenavigation.userGroupID ', $filteruserGroup);
            }
            $this->datatables->add_column('edit', ' $1 ', 'edit_employee_nav_access(employeeNavigationID)');
            echo $this->datatables->generate();
        } else {

            $this->datatables->select("srp_employeesdetails.ECode AS empID, employeeNavigationID, srp_erp_usergroups.description as description, Ename2 as emloyeeName, srp_erp_employeenavigation.userGroupID, CONCAT(IFNULL(company_code, ''), ' - ', IFNULL( company_name, '')) AS company, company_id", false);
            $this->datatables->from('srp_erp_employeenavigation');
            $this->datatables->join('srp_employeesdetails', 'empID = EIdNo', 'INNER');
            $this->datatables->join('srp_erp_usergroups', 'srp_erp_employeenavigation.userGroupID = srp_erp_usergroups.userGroupID', 'INNER');
            $this->datatables->join('srp_erp_company', 'company_id = srp_erp_employeenavigation.companyID', 'INNER');
            $this->datatables->where('srp_erp_employeenavigation.companyID', $companyID);

            if (isset($filteruserGroup) && $filteruserGroup != '') {
                $this->datatables->where('srp_erp_employeenavigation.userGroupID ', $filteruserGroup);
            }
            $this->datatables->add_column('edit', ' $1 ', 'edit_employee_nav_access(employeeNavigationID)');
            echo $this->datatables->generate();

        }

    }

    function load_dropdown_unassigned_employees()
    {
        $data['emp'] = true;
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/navigation/ajax-erp_navigation_load_employees', $data, true);
        echo $html;
    }

    function load_userGroupdropDown()
    {
        $data['group'] = true;
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/navigation/ajax-erp_navigation_load_employees', $data, true);
        echo $html;
    }

    function loaduserGroupdropdown()
    {
        $data['groupID'] = true;
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/navigation/ajax-erp_navigation_load_employees', $data, true);
        echo $html;
    }

    function save_assigned_navigation_employees()
    {
        $this->form_validation->set_rules('empID[]', 'Employee', 'trim|required');
        $this->form_validation->set_rules('userGroup', 'User group', 'trim|required');
        $this->form_validation->set_rules('companyID', 'companyID', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {

            $employee = $this->input->post('empID');

            $userGroup = $this->input->post('userGroup');
            $companyID = $this->input->post('companyID');
            $x = 0;
            if ($employee) {
                foreach ($employee as $empID) {

                    $data[$x]['empID'] = $empID;
                    $data[$x]['userGroupID'] = $userGroup;
                    $data[$x]['companyID'] = $companyID;

                    $x++;
                    $detail = $this->db->query("SELECT approvalUserID FROM srp_erp_approvalusers WHERE employeeID={$empID} AND companyID={$companyID} ")->row_array();
                    if (!empty($detail)) {
                        $this->db->update('srp_erp_approvalusers', array('groupID' => $userGroup), array('approvalUserID' => $detail['approvalUserID']));
                    }

                }
            }

            $insert = $this->db->insert_batch('srp_erp_employeenavigation', $data);
            if ($insert) {
                $this->session->set_flashdata('s', 'Records Inserted Successfully.');
                echo json_encode(array('status' => true));
            } else {
                $this->session->set_flashdata('e', 'Failed. Please contact support team');
                echo json_encode(array('status' => false));

            }


        }
    }

    function delete_employee_navigation_access()
    {
        $this->db->where('employeeNavigationID', $this->input->post('employeeNavigationID'));
        $this->db->delete('srp_erp_employeenavigation');
        $this->session->set_flashdata('s', 'Employee navigation : deleted Successfully.');
        echo json_encode(true);
    }

    function load_navigation($companyIDTmp = null)
    {
        $isGroupUser = $this->input->post('isGroupUser');

        if ($isGroupUser == 1) {
            $companyID = $this->input->post('companyID');
            $eid = $this->input->post('eid');

            $db2 = $this->load->database('db2', TRUE);
            $db2->select('*');
            $db2->where("company_id", $companyID);
            $resultDb2 = $db2->get("srp_erp_company")->row_array();

            $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
            $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = FALSE;
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;
            $this->load->database($config, FALSE, TRUE);
            $company_detail = $this->Session_model->fetch_company_detail($companyID, trim($this->session->userdata("branchID")));

            $this->session->set_userdata('companyID', $company_detail['company_id']);
            $this->common_data['company_data'] = $company_detail;
            $this->session->set_userdata('companyType', 1);
            $eidno = $this->db->query("select * from srp_employeesdetails WHERE Erp_companyID={$companyID} AND EIdNo={$eid}")->row_array();

            $this->common_data['current_user'] = $eidno['Ename2'];
            $this->common_data['current_userID'] = $eidno['EIdNo'];
            $this->common_data['current_userCode'] = $eidno['ECode'];
            $this->common_data['user_group'] = $eidno['branchID'];

            $session_data = array(
                'empID' => $eidno['EIdNo'],
                'empCode' => $eidno['ECode'],
                'username' => $eidno['Ename2'],
                'loginusername' => $eidno['UserName'],
                'branchID' => $eidno['branchID'],
                'usergroupID' => $eidno['branchID'],
                'status' => TRUE
            );

            $this->session->set_userdata($session_data);

            //echo '<pre>'; print_r($companyID);exit;
            //echo current_companyID(); die();
            $db2->select('userGroupID,EidNo');
            $db2->where("companyID", $companyID);
            $db2->where("empID", $eid);
            $groupdetails = $db2->get("groupusercompanies")->row_array();
            if (isset($companyIDTmp) && $companyIDTmp != null) {
                $companyID = $companyIDTmp;
            } else {
                $companyID = $this->input->post('companyID');
            }

            $empID = current_userID();
            //$companyCode = $this->input->post('companyCode');

            $wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID))->get()->row('wareHouseID');
            $imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
            if ($imagePath_arr['isLocalPath'] == 1) {
                $imagePath = base_url() . 'images/users/';
            } else { // FOR SRP ERP USERS
                $imagePath = $imagePath_arr['imagePath'];
            }
            $company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();
            $this->session->set_userdata("ware_houseID", trim($wareHouseID));
            $this->session->set_userdata("company_code", trim($company['company_code'] ?? ''));
            $this->session->set_userdata("company_name", trim($company['company_name'] ?? ''));
            $this->session->set_userdata("company_logo", trim($company['company_logo'] ?? ''));
            $this->session->set_userdata("imagePath", trim($imagePath));
            $this->session->set_userdata("companyID", trim($companyID));
            $this->session->set_userdata("companyType", trim($this->input->post('companyType') ?? ''));

            $detail = "";
            $userGroupID = $groupdetails['userGroupID'];
            $Eid = $groupdetails['EidNo'];
            $eidno = $this->db->query("select EIdNo from srp_employeesdetails WHERE Erp_companyID={$companyID} AND isSystemAdmin=1")->row_array();
            $idno = $eidno['EIdNo'];
            $detail = $this->db->query("SELECT srp_erp_navigationusergroupsetup.* FROM srp_erp_employeenavigation INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID WHERE empID={$Eid} AND srp_erp_employeenavigation.companyID={$companyID} AND srp_erp_employeenavigation.userGroupID={$userGroupID} Order by levelNo,sortOrder ASC ")->result_array();

            $data['data'] = $detail;
            $data['companyID'] = $companyID;
            $data['companyType'] = $this->input->post('companyType');
            $status = false;
            $html = $this->load->view('system/navigation/ajax-srp_erp_navigation.php', $data, true);

            if (empty($detail)) {
                $status = true;
            } else {
                $keys = array_keys(array_column($detail, 'navigationMenuID'), 29);
                $new_array = array_map(function ($k) use ($detail) {
                    return $detail[$k];
                }, $keys);

                if (!$new_array) {
                    $status = true;
                }
                /*   echo  $revenue = array_search('navigationMenuID', array_column($detail, 29));
                     if(!$revenue){
                         $status = true;
                     }*/
            }
            // echo $html;

            echo json_encode(array('html' => $html, 'status' => $status));
        }
        else {
            if (isset($companyIDTmp) && $companyIDTmp != null) {
                $companyID = $companyIDTmp;
            } else {
                $companyID = $this->input->post('companyID');
            }

            $this->load->model('session_model');
            $this->session->unset_userdata(['subscription_expire_notification','subscription_dates']);
            $subscription_Exp = $this->session_model->check_subscription_status($companyID);
            if($subscription_Exp[0] == 'e') {
                http_response_code( 500 );
                die( $subscription_Exp[1] );
            }

            $empID = current_userID();
            //$companyCode = $this->input->post('companyCode');

            $wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID))->get()->row('wareHouseID');
            $imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
            if ($imagePath_arr['isLocalPath'] == 1) {
                $imagePath = base_url() . 'images/users/';
            } else { // FOR SRP ERP USERS
                $imagePath = $imagePath_arr['imagePath'];
            }
            if ($this->input->post('companyType') == 1) {
                $company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();
                $this->session->set_userdata("ware_houseID", trim($wareHouseID ?? ''));
                $this->session->set_userdata("company_code", trim($company['company_code'] ?? ''));
                $this->session->set_userdata("company_name", trim($company['company_name'] ?? ''));
                $this->session->set_userdata("company_logo", trim($company['company_logo'] ?? ''));
            } else {
                $company = $this->db->query("select * from srp_erp_companygroupmaster LEFT JOIN srp_erp_groupfinanceyear ON groupID = companyGroupID  WHERE companyGroupID={$companyID}")->row_array();
                $group = $this->db->query("SELECT * FROM srp_erp_groupfinanceyear WHERE isActive = 1 AND isCurrent = 1 AND groupID={$companyID}")->row_array();
                $this->session->set_userdata("company_name", trim($company['description'] ?? ''));
                $this->session->set_userdata("FYBeginingDate", trim($company['beginingDate'] ?? ''));
                $this->session->set_userdata("FYEndingDate", trim($company['endingDate'] ?? ''));
            }
            $this->session->set_userdata("imagePath", trim($imagePath));
            $this->session->set_userdata("companyID", trim($companyID));
            $this->session->set_userdata("companyType", trim($this->input->post('companyType') ?? ''));

            $detail = "";
            if ($this->input->post('companyType') == 1) {
                $detail = $this->db->query("SELECT srp_erp_navigationusergroupsetup.* 
                        FROM srp_erp_employeenavigation 
                        JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID 
                        WHERE empID={$empID} AND srp_erp_employeenavigation.companyID={$companyID} Order by levelNo,sortOrder ASC ")->result_array();
            }
            else {
                $sql = "SELECT grtSetup.* 
                        FROM srp_erp_companysubgroupnavigationsetup AS grtSetup
                        LEFT JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = grtSetup.navigationMenuID
                        LEFT JOIN srp_erp_companysubgroupmaster AS grpMas ON grtSetup.compaySubGroupID = grpMas.companySubGroupID
                        LEFT JOIN srp_erp_companysubgroupemployees AS grpEmp ON grpEmp.companySubGroupID = grpMas.companySubGroupID  
                        WHERE grpEmp.EmpID={$empID} AND companyGroupID={$companyID} AND isGroup = 1 Order by levelNo,sortOrder ASC";

                $detail = $this->db->query($sql)->result_array();
            }

            $data['data'] = $detail;
            $data['companyID'] = $companyID;
            $data['companyType'] = $this->input->post('companyType');
            $status = false;
            $html = $this->load->view('system/navigation/ajax-srp_erp_navigation.php', $data, true);

            if (empty($detail)) {
                $status = true;
            }
            else {
                $keys = array_keys(array_column($detail, 'navigationMenuID'), 29);
                $new_array = array_map(function ($k) use ($detail) {
                    return $detail[$k];
                }, $keys);

                if (!$new_array) {
                    $status = true;
                }
            }
            // echo $html;

            echo json_encode(array('html' => $html, 'status' => $status));
        }
    }

    function load_navigation_html($companyIDTmp = null)
    {
        if (isset($companyIDTmp) && $companyIDTmp != null) {
            $companyID = $companyIDTmp;
        } else {
            $companyID = $this->input->post('companyID');
        }


        $empID = current_userID();
        $companyCode = $this->input->post('companyCode');

        $wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID))->get()->row('wareHouseID');
        $imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
        if ($imagePath_arr['isLocalPath'] == 1) {
            $imagePath = base_url() . 'images/users/';
        } else { // FOR SRP ERP USERS
            $imagePath = $imagePath_arr['imagePath'];
        }
        $company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();

        $this->session->set_userdata("companyID", trim($companyID));
        $this->session->set_userdata("ware_houseID", trim($wareHouseID));
        $this->session->set_userdata("imagePath", trim($imagePath));
        $this->session->set_userdata("company_code", trim($company['company_code'] ?? ''));
        $this->session->set_userdata("company_name", trim($company['company_name'] ?? ''));
        $this->session->set_userdata("company_logo", trim($company['company_logo'] ?? ''));


        $detail = $this->db->query("SELECT srp_erp_navigationusergroupsetup.* FROM srp_erp_employeenavigation INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID WHERE empID={$empID} AND srp_erp_employeenavigation.companyID={$companyID} Order by levelNo,sortOrder ASC ")->result_array();
        //print_r($detail);
        $data['data'] = $detail;
        $data['companyID'] = $companyID;
        $status = false;
        $html = $this->load->view('system/navigation/ajax-srp_erp_navigation.php', $data, true);
        if (empty($detail)) {
            $status = true;
        } else {
            $keys = array_keys(array_column($detail, 'navigationMenuID'), 29);
            $new_array = array_map(function ($k) use ($detail) {
                return $detail[$k];
            }, $keys);

            if (!$new_array) {
                $status = true;
            }
            /*   echo  $revenue = array_search('navigationMenuID', array_column($detail, 29));
                 if(!$revenue){
                     $status = true;
                 }*/
        }
        // echo $html;
        echo $html;


    }

    function load_companyusergroup()
    {
        $companyID = current_companyID();
        $this->datatables->select("userGroupID,
companyID,
description,
isActive
", false);
        $this->datatables->from('srp_erp_usergroups');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('edit', ' $1 ', 'company_groupstatus(userGroupID,isActive,description)');
        echo $this->datatables->generate();
    }

    function save_company_usergroup()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $companyID = current_companyID();
            $description = trim($this->input->post('description') ?? '');
            $userGroupID = trim($this->input->post('userGroupID') ?? '');
            if ($userGroupID) {
                $data = array(
                    'description' => $description
                );
                $this->db->where('userGroupID', $userGroupID);
                $result = $this->db->update('srp_erp_usergroups', $data);
                if ($result) {
                    $this->session->set_flashdata('s', 'Records Updated Successfully.');
                    echo json_encode(array('status' => true));
                } else {
                    $this->session->set_flashdata('e', 'Failed. Please contact support team');
                    echo json_encode(array('status' => false));
                }
            } else {

                $valid = $this->db->query("SELECT * FROM srp_erp_usergroups WHERE companyID = {$companyID} AND description = \"" . $description . "\"")->row_array();

                if ($valid) {
                    $this->session->set_flashdata('e', 'Failed. Entered description already exist.');
                    echo exit(json_encode(array('status' => false)));
                }

                $data['companyID'] = $companyID;
                $data['description'] = $description;
                $data['isActive'] = 1;
                $insert = $this->db->insert('srp_erp_usergroups', $data);
                $userGroupID = $this->db->insert_id();
                if ($insert) {
                    $defaultWidgets = $this->db->query("select widgetID from srp_erp_widgetmaster where isDefault = -1")->result_array();
                    foreach ($defaultWidgets as $val) {
                        $widgetdata['companyID'] = current_companyID();
                        $widgetdata['userGroupID'] = $userGroupID;
                        $widgetdata['widgetID'] = $val['widgetID'];
                        $insertDefaultWidget = $this->db->insert('srp_erp_usergroupwidget', $widgetdata);
                    }
                    if ($insertDefaultWidget) {
                        $this->session->set_flashdata('s', 'Records Inserted Successfully.');
                        echo json_encode(array('status' => true));
                    }
                } else {
                    $this->session->set_flashdata('e', 'Failed. Please contact support team');
                    echo json_encode(array('status' => false));
                }
            }
        }
    }

    function update_companyUsergroup()
    {
        $userGroupID = $this->input->post('userGroupID');
        $status = $this->input->post('status');
        $update = $this->db->update('srp_erp_usergroups', array('isActive' => $status), array('userGroupID' => $userGroupID));
        $this->session->set_flashdata('s', 'Successfully records updated.');
        echo json_encode(array('status' => true));
        exit;

    }

    function loadWidet()
    {
        $data = array();
        $usergroupID = $this->input->post('usergroupID');
        //$data["widgets"] = $this->Access_menu_model->loadWidet();
        $data["widgets"] = $this->Access_menu_model->loadWidet($usergroupID);
        $path = 'system/widget/erp_company_user_group_widget';
        $this->load->view($path, $data);
    }

    function save_widget()
    {
        echo json_encode($this->Access_menu_model->save_widget());
        //return  $this->Access_menu_model->save_widget();
    }

    function deleteUserGroupID()
    {
        echo json_encode($this->Access_menu_model->deleteUserGroupID());
    }

    function load_user_group()
    {
        echo json_encode($this->Access_menu_model->load_user_group());
    }

    function update_emp_language()
    {

        echo json_encode($this->Access_menu_model->update_emp_language());

    }

    function update_emp_location()
    {

        echo json_encode($this->Access_menu_model->update_emp_location());

    }

    function control_staff_access()
    {
        $page_url = '';

        $this->load->model('Access_menu_model');
        echo json_encode($this->Access_menu_model->control_staff_access($page_url));
    }

    function getRedirectionToken()
    {
        $empID = current_userID();
        $this->load->helper('string');
        $gearserp = $this->load->database('gearserp', TRUE);
        $employee = $gearserp->query("SELECT * from users WHERE erpEmployeeID  = $empID")->row_array();
        $token = "";
        if ($employee) {
            $token = random_string('alnum', 16);
            $gearserp->where('erpEmployeeID', $empID);
            $gearserp->update('users', ['login_token' => $token]);


            echo json_encode(array('token'=>$token));
            exit;
        }
        echo json_encode(array('token' => false));
        exit;

    }
    function getusergroupcomapny()
    {
        echo json_encode($this->Access_menu_model->getusergroupcomapny());
    }

    function fetch_template_keyword() {
        echo json_encode($this->Access_menu_model->fetch_template_keyword());
    }

    function fetch_document_drill_down(){
        $data['title'] = 'Drill down';
        $data['extra'] = '';


        $this->load->helper('cookie');
        $this->load->view('include/header',$data);
        $this->load->view('include/top-mpr',$data);
        $this->load->view('include/common_drilldown');

        $this->load->view('include/footer');

    }

    /**
     * Add navigation master to session
     *
     * @return void
     */
    public function addNavigationMaster(): void
    {
        $masterId = $this->input->post('masterID');
        if ($masterId){
            $this->session->set_userdata('navigationMasterId', $masterId);
        }
        echo json_encode(['status' => true]);
    }

    /**
     * Get navigation secondary description
     *
     * @return void
     */
    public function getNavigationDescriptionSetup(): void
    {
        $navigation['data'] = $this->NavigationService->getAll();
        echo $this->load->view('system/navigation/ajax-erp_navigation_description_setup', $navigation, true);
    }

    /**
     * Get navigation secondary description
     *
     * @return void
     */
    public function saveNavigationDescriptionSetup(): void
    {
        $this->form_validation->set_rules('navigationMenuID[]', 'Id', 'trim|required');
        $this->form_validation->set_rules('secondaryDescription[]', 'Secondary Description', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            $this->sendResponse('e', validation_errors());
            return;
        }

        try {
            $this->NavigationService->saveNavigationSecondaryDescription(
                $this->input->post(NULL, TRUE)
            );
        } catch (InvalidOperationException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $this->sendResponse('s', 'Successfully updated');
    }
}
