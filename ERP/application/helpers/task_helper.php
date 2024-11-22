<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * Date: 4/17/2017
 * Time: 2:41 PM
 */

if (!function_exists('all_task_management_status')) {
    function all_task_management_status($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("documentID,description");
        $CI->db->FROM('srp_erp_task_documents');
        $CI->db->where('isActive', 1);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select a Document');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['documentID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

/*Load all tasks status*/
if (!function_exists('all_task_status_filter')) {
    function all_task_status_filter($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("statusID, description, documentID, shortorder");
        $CI->db->from('srp_erp_task_status');
        $CI->db->where('documentID', 2);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->order_by('shortorder', 'ASC'); // Added this line to order by shortorder
        $status = $CI->db->get()->result_array();
        if ($custom) {
            $status_arr = array('' => 'Select Status');
        } else {
            $status_arr = array('' => 'Status');
        }
        if (isset($status)) {
            foreach ($status as $row) {
                $status_arr[trim($row['statusID'] ?? '')] = (trim($row['description'] ?? ''));
            }
        }
        return $status_arr;
    }
}


if (!function_exists('all_task_status')) {
    function all_task_status($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("statusID, description, documentID, shortorder");
        $CI->db->from('srp_erp_task_status');
        $CI->db->where('documentID', 2);
        $CI->db->order_by('shortorder', 'ASC');
        $status = $CI->db->get()->result_array();
        $status_arr = ['' => 'Select Status'];
    
        foreach ($status as $row) {
            $status_arr[trim($row['statusID'] ?? '')] = trim($row['description'] ?? '');
        }

        return $status_arr;
    }
}

/*Load all categotrys for select2*/
if (!function_exists('load_all_categories')) {
    function load_all_categories($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("categoryID,description,documentID");
        $CI->db->FROM('srp_erp_task_categories');
        $CI->db->where('documentID', 2);
        $category = $CI->db->get()->result_array();
        if ($status) {
            $category_arr = array('' => 'Select Category');
        } else {
            $category_arr = array('' => 'Category');
        }
        if (isset($category)) {
            foreach ($category as $row) {
                $category_arr[trim($row['categoryID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $category_arr;
    }
}

/*Load all employees for select2*/
if (!function_exists('fetch_employees_by_company_multiple')) {
    function fetch_employees_by_company_multiple($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select('ed.EIdNo, ed.Ename2');
        $CI->db->from('srp_employeesdetails ed');
        // $CI->db->join('srp_erp_task_categories_assignee dep', 'ed.EIdNo = dep.inchageempID');
        $employee = $CI->db->get()->result_array();
        $employee_arr = [];
        if ($status) {
            // $employee_arr = array('' => 'Select Employee');
        } else {
            $employee_arr = [];
        }
        if (isset($employee)) {
            foreach ($employee as $row) {
                $employee_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }
        return $employee_arr;
    }
}


/*
    Fetch all Employees from Group of Companies
    @param status
*/
if (!function_exists('fetch_employees')) {
    function fetch_employees($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->SELECT("EIdNo,Ename2");
        $CI->db->FROM('srp_employeesdetails');
        $employee = $CI->db->get()->result_array();

        if ($status) {
            $employee_arr = array('' => 'Select Employee');
        } else {
            $employee_arr = $employee_arr = [];;
        }
        if (isset($employee)) {
            foreach ($employee as $row) {
                $employee_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }
        return $employee_arr;

    }
}




if (!function_exists('all_crm_groupMaster')) {
    function all_crm_groupMaster($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("groupID,groupName");
        $CI->db->FROM('srp_erp_task_usergroups');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
       if ($status) {

        } else {
            $CI->db->where('isAdmin', 0);
        }
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select a Group');
        } else {
            $data_arr = $employee_arr = [];;
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['groupID'] ?? '')] = trim($row['groupName'] ?? '');
            }
        }
        return $data_arr;
    }
}


/*Load all campaign types*/
if (!function_exists('load_all_employees_taskFilter')) {
    function load_all_employees_taskFilter($status = true)
    {
        $CI =& get_instance();
        $CI->db->select("empID,Ename2");
        $CI->db->from('srp_erp_task_assignees');
        $CI->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID');
        $CI->db->where('documentID', 2);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $types = $CI->db->get()->result_array();
        if ($status) {
            $types_arr = array('' => 'Select Employee');
        } else {
            $types_arr = array('' => 'Assignee');
        }
        if (isset($status)) {
            foreach ($types as $row) {
                $types_arr[trim($row['empID'] ?? '')] = (trim($row['Ename2'] ?? ''));
            }
        }
        return $types_arr;
    }
}

if (!function_exists('load_assigne_taskFilter')) {
    function load_assigne_taskFilter($status = true)
    {
        $CI =& get_instance();
        $CI->db->select("empID,Ename2");
        $CI->db->from('srp_erp_task_assignees');
        $CI->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID');
        $CI->db->where('documentID', 2);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $types = $CI->db->get()->result_array();
        if ($status) {
            $types_arr = array('' => 'Select Employee');
        } else {
            $types_arr = array('' => 'Assignee');
        }
        if (isset($status)) {
            foreach ($types as $row) {
                $types_arr[trim($row['empID'] ?? '')] = (trim($row['Ename2'] ?? ''));
            }
        }
        return $types_arr;
    }
}


if (!function_exists('load_employee_drop_crm')) {
    function load_employee_drop_crm()
    {
        $CI =& get_instance();
        $CI->db->SELECT(" srp_employeesdetails.EIdNo as employeeID, srp_employeesdetails.Ename2 as employeeName");
        $CI->db->FROM('srp_erp_task_assignees');
        $CI->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_task_assignees.empID');
        // $CI->db->where('activeYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
            $data_arr = array('' => 'Responsible Person');

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['employeeID'] ?? '')] = trim($row['employeeName'] ?? '');
            }
        }
        return $data_arr;
    }
}



if (!function_exists('crm_isGroupAdmin')) {
    function crm_isGroupAdmin()
    {
        $CI =& get_instance();
        $currentuserID = current_userID();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT adminYN FROM srp_erp_task_usergroupdetails WHERE empID = '{$currentuserID}' AND companyID = '{$companyID}'")->row_array();
        return $data;
    }
}


if (!function_exists('crm_isSuperAdmin')) {
    function crm_isSuperAdmin()
    {
        $CI =& get_instance();
        $currentuserID = current_userID();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT isSuperAdmin FROM srp_erp_task_users WHERE employeeID = '{$currentuserID}'  AND companyID = '{$companyID}'")->row_array();
        return $data;
    }
}

if (!function_exists('assign_task_department')) {
    function assign_task_department()
    {
        $CI =& get_instance();
        $CI->db->SELECT("DepartmentMasterID,DepartmentDes");
        $CI->db->FROM('srp_departmentmaster');
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
             $data_arr =  $data_arr = array('' => 'Select Department');

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['DepartmentMasterID'] ?? '')] = trim($row['DepartmentDes'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('assign_department_employee')) {
    function assign_department_employee()
    {

        // $hodfilter = department_hod_filter();
        $CI =& get_instance();
        $CI->db->SELECT("EIdNo,Ename2");
        $CI->db->FROM('srp_employeesdetails');
        // $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
            // $data_arr = array('' => 'Created By');
            $data_arr = array('' => 'Select Employee');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('isdefault')) {
    function isdefault($categoryID,$inchageempID,$isdefault)
    {
        $checked = '';
        if ($isdefault == 1) {
            $checked = 'checked';
        }
        $html = '<div style="text-align: center"><input ' . $checked . ' type="checkbox" id="activeYN" onclick="activateUser(this,' . $inchageempID . ')" name="activeYN" value="1" ></div>';
        return $html;

    }
}


if (!function_exists('isdefult')) {
    function isdefult($categoryID,$isdefault)
    {
        $checked = '';
        if ($isdefault == 1) {
            $checked = 'checked';
        }
        $html = '<div style="text-align: center"><input ' . $checked . ' type="checkbox" id="activeYN" onclick="activatecategory(this,' . $categoryID . ')" name="activeYN" value="1" ></div>';
        return $html;

    }
}

/*Load all campaign types*/
if (!function_exists('all_campaign_types')) {
    function all_campaign_types($status = true)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $types = $CI->db->query("SELECT categoryID,description FROM srp_erp_task_categories WHERE documentID = 1 AND ((companyID = '{$companyID}') OR (isdefault != 0)) ")->result_array();
        if ($status) {
            $types_arr = array('' => 'Select Type');
        } else {
            $types_arr = array('' => 'Category');
        }
        if (isset($status)) {
            foreach ($types as $row) {
                $types_arr[trim($row['categoryID'] ?? '')] = (trim($row['description'] ?? ''));
            }
        }
        return $types_arr;
    }
}

if (!function_exists('load_task_action')) {
    function load_task_action($taskID)
    {
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/crm/create_new_task",' . $taskID . ',"Edit Task","CRM"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        /*        $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'PVM\',\'' . $campaignID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';*/
        $status .= '<a onclick="delete_tasks(' . $taskID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        $status .= '</span>';
        return $status;
    }
}


