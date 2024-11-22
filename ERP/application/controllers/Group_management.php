<?php

class Group_management extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_management_model');

    }

    function load_companysubgroup()
    {
        $companyID = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyID);
        $grp= $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid=$companyID;


        $this->datatables->select("	srp_erp_companysubgroupmaster.companyGroupID,
	companySubGroupID,
	srp_erp_companysubgroupmaster.description AS subdescription,
	srp_erp_companygroupmaster.description AS groupdescription ", false);

        $this->datatables->join('srp_erp_companygroupmaster ', 'srp_erp_companysubgroupmaster.companyGroupID = srp_erp_companygroupmaster.companyGroupID');
        $this->datatables->from('srp_erp_companysubgroupmaster');

        $this->datatables->add_column('edit', '<a onclick="opensubgroupmodel($1)"><span title="Edit" class="glyphicon glyphicon-pencil"  rel="tooltip"></span></a>', 'companySubGroupID');

        /*$this->datatables->select("companySubGroupID,srp_erp_companysubgroupmaster.companyGroupID,srp_erp_companysubgroupmaster.description as subdescription,srp_erp_companygroupmaster.description as groupdescription", false);
        $this->datatables->join('srp_erp_companygroupdetails ', 'srp_erp_companysubgroupmaster.companyGroupID = srp_erp_companygroupdetails.companyGroupDetailID');
        $this->datatables->join('srp_erp_companygroupmaster ', 'srp_erp_companygroupdetails.companyGroupID = srp_erp_companygroupmaster.companyGroupID');
        $this->datatables->from('srp_erp_companysubgroupmaster');
        $this->datatables->where($where);
        $this->datatables->add_column('edit', '<a onclick="opensubgroupmodel($1)"><span title="Edit" class="glyphicon glyphicon-pencil"  rel="tooltip"></span></a>', 'companySubGroupID');*/
        //$this->datatables->add_column('edit', ' $1 ', 'company_groupstatus(userGroupID,isActive)');
        echo $this->datatables->generate();
    }

    function save_sub_group()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('companyGroupID', 'Main Group', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Group_management_model->save_sub_group());
        }
    }


    function fetch_sub_group_employees()
    {
        $userGroup = $this->input->post('subGroupID');
        $companyID = current_companyID();

        $filteruserGroup = $this->input->post('subGroupID');

        $this->datatables->select("subGroupEmpID,EmpID,srp_erp_companysubgroupemployees.companySubGroupID,srp_employeesdetails.ECode as ECode,srp_employeesdetails.Ename2 as Ename2,srp_erp_company.company_code as company_code,srp_erp_company.company_name as company_name,srp_erp_companysubgroupmaster.description as description", false);
        $this->datatables->from('srp_erp_companysubgroupemployees');
        $this->datatables->join('srp_employeesdetails', 'srp_erp_companysubgroupemployees.EmpID = srp_employeesdetails.EIdNo', 'INNER');
        $this->datatables->join('srp_erp_company', 'srp_employeesdetails.Erp_companyID = srp_erp_company.company_id', 'INNER');
        $this->datatables->join('srp_erp_companysubgroupmaster', 'srp_erp_companysubgroupemployees.companySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID', 'INNER');
        //$this->datatables->where('srp_erp_employeenavigation.companyID', $companyID);

        if (isset($filteruserGroup) && $filteruserGroup != '') {
            $this->datatables->where('srp_erp_companysubgroupemployees.companySubGroupID ', $filteruserGroup);
        }
        $this->datatables->add_column('edit', '<a  style="color: red" onclick="deleteemployee($1)"><span title="Edit" class="glyphicon glyphicon-trash"  rel="tooltip"></span></a>', 'subGroupEmpID');

        echo $this->datatables->generate();
    }

    function load_sub_group()
    {
        $data['groupID'] = $this->input->post('groupID');
        $data['All'] = $this->input->post('All');
        $html = $this->load->view('system/companyConfiguration/ajax-erp_load_sub_groups', $data, true);
        echo $html;
    }

    function load_dropdown_unassigned_employees(){
        $data['companyempGroupID'] = $this->input->post('companyempGroupID');
        $html = $this->load->view('system/companyConfiguration/ajax-erp_load_unassigned_employees', $data, true);
        echo $html;
    }

    function load_sub_group_employee()
    {
        $data['groupID'] = $this->input->post('groupID');
        $html = $this->load->view('system/companyConfiguration/ajax-erp_load_sub_groups_emp', $data, true);
        echo $html;
    }


    function save_assigned_sub_group_employees(){
        $this->form_validation->set_rules('empID[]', 'Employee', 'trim|required');
        $this->form_validation->set_rules('subGroupIDEmp', 'Sub Group', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {

            $employee = $this->input->post('empID');
            $userGroup = $this->input->post('subGroupIDEmp');
            $x = 0;
            if ($employee) {
                foreach ($employee as $empID) {

                    $data[$x]['EmpID'] = $empID;
                    $data[$x]['companySubGroupID'] = $userGroup;

                    $x++;
                }
            }
            $insert = $this->db->insert_batch('srp_erp_companysubgroupemployees', $data);
            if ($insert) {
                $this->session->set_flashdata('s', 'Records Inserted Successfully.');
                echo json_encode(array('status' => true));
            } else {
                $this->session->set_flashdata('e', 'Failed. Please contact support team');
                echo json_encode(array('status' => false));

            }
        }
    }

    function load_navigation_subgroup_setup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $userGroupID = $this->input->post('userGroupID');
        $mainGroupID = $this->input->post('mainGroupID');
        $data['data'] = false;
        if (!empty($userGroupID)) {
            $companyGroup=  $this->db->query("SELECT companyGroupID FROM `srp_erp_companysubgroupmaster` WHERE companySubGroupID=$userGroupID")->row_array();
          //  $companyGroup = $this->db->query("SELECT companyGroupID FROM `srp_erp_companygroupdetails` WHERE `companyGroupDetailID` = {$mainGroupID}")->row_array();
            $companyGroupID=$companyGroup['companyGroupID'];
            $parentID=$this->db->query("SELECT masterID FROM `srp_erp_companygroupmaster` WHERE companyGroupID=$companyGroupID")->row_array();
            $parntid=$parentID['masterID'];
            /*if (!empty($navigationMenuID)) {*/
                //$data['data'] = $this->db->query("SELECT srp_erp_navigationmenus.*, IFNULL(srp_erp_navigationusergroupsetup.navigationMenuID, 0) AS navID FROM srp_erp_navigationmenus LEFT JOIN srp_erp_companysubgroupnavigationsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID AND compaySubGroupID = {$userGroupID} WHERE srp_erp_navigationmenus.navigationMenuID NOT IN (SELECT srp_erp_navigationmenus.navigationMenuID FROM srp_erp_navigationmenus LEFT JOIN `srp_erp_moduleassign` ON srp_erp_navigationmenus.navigationMenuID = srp_erp_moduleassign.navigationMenuID AND companyID = '{$companyID}' WHERE masterID IS NULL AND moduleID IS NULL) AND srp_erp_navigationmenus.isGroup=1  ORDER BY levelNo , sortOrder")->result_array();
                $data['data'] = $this->db->query("SELECT
    srp_erp_navigationmenus.*, IFNULL(
        srp_erp_companysubgroupnavigationsetup.navigationMenuID,
        0
    ) AS navID
FROM
    srp_erp_navigationmenus
LEFT JOIN srp_erp_companysubgroupnavigationsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID
AND compaySubGroupID = {$userGroupID}
WHERE
    srp_erp_navigationmenus.navigationMenuID NOT IN (
       SELECT
            srp_erp_navigationmenus.navigationMenuID
        FROM
            srp_erp_navigationmenus where navigationMenuID not in
        (SELECT
    srp_erp_moduleassign.navigationMenuID
FROM
    srp_erp_moduleassign
WHERE
    companyID in (
        SELECT
            companyID
        FROM
            srp_erp_companygroupdetails
        WHERE
            parentID={$parntid}) GROUP BY srp_erp_moduleassign.navigationMenuID) AND
            srp_erp_navigationmenus.masterID IS NULL
    )
AND srp_erp_navigationmenus.isGroup = 1
ORDER BY
    levelNo,
    sortOrder")->result_array();


              //echo   $this->db->last_query();
           /* }*/
        }


        $html = $this->load->view('system/companyConfiguration/ajax-erp_navigation_sub_group_setup', $data, true);
        echo $html;
    }


    function saveNavigationgroupSetup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $compaySubGroupID = $this->input->post('compaySubGroupID');

        $navigationID = $this->input->post('navigationID');;

        $this->db->trans_start();
        /*delete*/
        $this->db->delete('srp_erp_companysubgroupnavigationsetup', array('compaySubGroupID' => $compaySubGroupID));
        if (!empty($navigationID) && $navigationID != "") {

            $this->db->query("INSERT srp_erp_companysubgroupnavigationsetup (compaySubGroupID,navigationMenuID ,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist) SELECT $compaySubGroupID,navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist FROM srp_erp_navigationmenus WHERE navigationMenuID IN ({$navigationID})");

        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Failed. please try again');
            echo json_encode(true);
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Access rights for this sub group has been updated successfully');
            echo json_encode(true);
        }

    }

    function load_company_sub_group(){
        echo json_encode($this->Group_management_model->load_company_sub_group());
    }

    function edit_sub_group()
    {
        $this->form_validation->set_rules('descriptionedit', 'Description', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Group_management_model->edit_sub_group());
        }
    }

    function deleteemployeeSubgroup(){


        $subGroupEmpID = trim($this->input->post('subGroupEmpID') ?? '');

        $this->db->trans_start();

        $this->db->where('subGroupEmpID', $subGroupEmpID)->delete('srp_erp_companysubgroupemployees');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo  json_encode(array('s', 'Records deleted successfully'));
        } else {
            $this->db->trans_rollback();
            echo  json_encode(array('e', 'Error in deleting process'));
        }
    }


}
