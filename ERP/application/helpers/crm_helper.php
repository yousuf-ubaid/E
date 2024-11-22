<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * Date: 4/17/2017
 * Time: 2:41 PM
 */
/*Load all campaign status*/
if (!function_exists('all_campaign_status')) {
    function all_campaign_status($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("statusID,description,documentID");
        $CI->db->from('srp_erp_crm_status');
        $CI->db->where('documentID', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
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

/*Load all tasks status*/
if (!function_exists('all_task_status')) {
    function all_task_status($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("statusID,description,documentID");
        $CI->db->from('srp_erp_crm_status');
        $CI->db->where('documentID', 2);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
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

/*Load all Opportunity status*/
if (!function_exists('all_opportunities_status')) {
    function all_opportunities_status($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("statusID,description,documentID");
        $CI->db->from('srp_erp_crm_status');
        $CI->db->where('documentID', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
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

/*Load all project status*/
if (!function_exists('all_project_status')) {
    function all_project_status($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("statusID,description,documentID");
        $CI->db->from('srp_erp_crm_status');
        $CI->db->where('documentID', 9);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
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

/*Load all campaign types*/
/*if (!function_exists('all_campaign_types')) {
    function all_campaign_types($status = true)
    {
        $CI =& get_instance();
        $CI->db->select("categoryID,description");
        $CI->db->from('srp_erp_crm_categories');
        $CI->db->where('documentID', 1);
        $types = $CI->db->get()->result_array();
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
}*/

/*Load all employees*/
if (!function_exists('fetch_employees_by_company')) {
    function fetch_employees_by_company()
    {
        $CI =& get_instance();
        $CI->db->SELECT("userID,employeeID,employeeName");
        $CI->db->FROM('srp_erp_crm_users');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('activeYN', 1);
        return $CI->db->get()->result_array();
    }
}

/*Load all employees for select2*/
if (!function_exists('fetch_employees_by_company_multiple')) {
    function fetch_employees_by_company_multiple($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->SELECT("userID,employeeName,employeeID");
        $CI->db->FROM('srp_erp_crm_users');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('activeYN', 1);
        $employee = $CI->db->get()->result_array();
        if ($status) {
            $employee_arr = array('' => 'Select Employee');
        } else {
            $employee_arr = [];
        }
        if (isset($employee)) {
            foreach ($employee as $row) {
                $employee_arr[trim($row['employeeID'] ?? '')] = trim($row['employeeName'] ?? '');
            }
        }
        return $employee_arr;
    }
}


/*
    Fetch all Employees from Group of Companies
    @param status
*/
if (!function_exists('fetch_employees_by_company_group')) {
    function fetch_employees_by_company_group($status = true)/*Load all Supplier*/
    {
        $company_list = Drop_down_group_of_companies(false);
        $company_arr = array();

        foreach($company_list as $key=>$value){
            $company_arr[] = $key;
        }

        $CI =& get_instance();
        $CI->db->SELECT("userID,employeeName,employeeID");
        $CI->db->FROM('srp_erp_crm_users');
        $CI->db->where_in('companyID', $company_arr);
        $CI->db->where('activeYN', 1);
        $employee = $CI->db->get()->result_array();

        if ($status) {
            $employee_arr = array('' => 'Select Employee');
        } else {
            $employee_arr = [];
        }
        if (isset($employee)) {
            foreach ($employee as $row) {
                $employee_arr[trim($row['employeeID'] ?? '')] = trim($row['employeeName'] ?? '');
            }
        }
        return $employee_arr;

    }
}

/*Load all countries for select2*/
if (!function_exists('load_all_countrys')) {
    function load_all_countrys($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->SELECT("countryID,countryShortCode,CountryDes");
        $CI->db->FROM('srp_erp_countrymaster');
        $countries = $CI->db->get()->result_array();
        $countries_arr = array('' => 'Select Country');
        if (isset($countries)) {
            foreach ($countries as $row) {
                $countries_arr[trim($row['countryID'] ?? '')] = trim($row['CountryDes'] ?? '');
            }
        }
        return $countries_arr;
    }
}

/*Load all categotrys for select2*/
if (!function_exists('load_all_categories')) {
    function load_all_categories($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("categoryID,description,documentID");
        $CI->db->FROM('srp_erp_crm_categories');
        $CI->db->where('documentID', 2);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
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

/*Load all campaign types*/
if (!function_exists('all_campaign_types')) {
    function all_campaign_types($status = true)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $types = $CI->db->query("SELECT categoryID,description FROM srp_erp_crm_categories WHERE documentID = 1 AND ((companyID = '{$companyID}') OR (isdefault != 0)) ")->result_array();
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

/*Load all campaign types*/
if (!function_exists('all_Opportunities_category')) {
    function all_Opportunities_category($status = true)
    {
        $CI =& get_instance();
        $CI->db->select("categoryID,description");
        $CI->db->from('srp_erp_crm_categories');
        $CI->db->where('documentID', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $types = $CI->db->get()->result_array();
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

/*Load Campaign Actions*/
if (!function_exists('load_campaign_action')) {
    function load_campaign_action($campaignID)
    {
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/crm/create_new_campaign",' . $campaignID . ',"Edit Campaign","CRM"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        /*        $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'PVM\',\'' . $campaignID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';*/
        $status .= '<a onclick="delete_campaign(' . $campaignID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

/*Load Tasks Actions*/
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

/*Load Contact Actions*/
if (!function_exists('load_contact_action')) {
    function load_contact_action($contactID)
    {
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/crm/create_contact",' . $contactID . ',"Edit Contact","CRM"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        /*        $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'PVM\',\'' . $campaignID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';*/
        $status .= '<a onclick="delete_contact(' . $contactID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        $status .= '</span>';
        return $status;
    }
}


/*Load all Campaigns for select2*/
if (!function_exists('load_all_campaigns')) {
    function load_all_campaigns($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->SELECT("campaignID,name");
        $CI->db->FROM('srp_erp_crm_campaignmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $campaigns = $CI->db->get()->result_array();
        $campaigns_arr = array('' => 'Select Campaign');
        if (isset($campaigns)) {
            foreach ($campaigns as $row) {
                $campaigns_arr[trim($row['campaignID'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        return $campaigns_arr;
    }
}

/*Load all organizations for select2*/
if (!function_exists('load_all_organizations')) {
    function load_all_organizations($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->SELECT("organizationID,Name");
        $CI->db->FROM('srp_erp_crm_organizations');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $organization = $CI->db->get()->result_array();
        if ($status) {
            $organization_arr = array('' => 'Select Organization');
        } else {
            $organization_arr = [];
        }
        if (isset($organization)) {
            foreach ($organization as $row) {
                $organization_arr[trim($row['organizationID'] ?? '')] = trim($row['Name'] ?? '');
            }
        }
        return $organization_arr;
    }
}

/*Load all campaign types*/
if (!function_exists('load_all_employees_campaignFilter')) {
    function load_all_employees_campaignFilter($status = true)
    {
        $CI =& get_instance();
        $CI->db->select("empID,Ename2");
        $CI->db->from('srp_erp_crm_assignees');
        $CI->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_assignees.empID');
        $CI->db->where('documentID', 1);
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


/*Load all campaign types*/
if (!function_exists('load_all_employees_taskFilter')) {
    function load_all_employees_taskFilter($status = true)
    {
        $CI =& get_instance();
        $CI->db->select("empID,Ename2");
        $CI->db->from('srp_erp_crm_assignees');
        $CI->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_assignees.empID');
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

/*Load all organizations for select2*/
if (!function_exists('lead_status')) {
    function lead_status($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->SELECT("statusID,description");
        $CI->db->FROM('srp_erp_crm_leadstatus');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $status = $CI->db->get()->result_array();
        if ($status) {
            $status_arr = array('' => 'Select Status');
        } else {
            $status_arr = [];
        }
        if (isset($status)) {
            foreach ($status as $row) {
                $status_arr[trim($row['statusID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $status_arr;
    }
}

if (!function_exists('all_crm_employees_drop')) {
    function all_crm_employees_drop($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("userID,employeeID,employeeName");
        $CI->db->FROM('srp_erp_crm_users');
        $CI->db->where('activeYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Employee');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['employeeID'] ?? '')] = trim($row['employeeName'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('all_crm_leadSource')) {
    function all_crm_leadSource($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("sourceID,description");
        $CI->db->FROM('srp_erp_crm_source');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('documentID', 6);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Source');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['sourceID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('crm_all_currency_new_drop')) {
    function crm_all_currency_new_drop($status = true)/*Load all currency*/
    {
        $CI =& get_instance();
        $CI->db->select("srp_erp_companycurrencyassign.currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->join('srp_erp_companycurrencyassign', 'srp_erp_companycurrencyassign.currencyID = srp_erp_currencymaster.currencyID');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $currency = $CI->db->get()->result_array();
        if ($status) {
            $currency_arr = array('' => 'Select Currency');
        } else {
            $currency_arr = [];
        }
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['currencyID'] ?? '')] = trim($row['CurrencyCode'] ?? '') . ' | ' . trim($row['CurrencyName'] ?? '');
            }
        }
        return $currency_arr;
    }
}

if (!function_exists('all_crm_product_master')) {
    function all_crm_product_master($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("productID,productName");
        $CI->db->FROM('srp_erp_crm_products');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Product');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['productID'] ?? '')] = trim($row['productName'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('all_crm_valueType')) {
    function all_crm_valueType($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("valueTypeID,description");
        $CI->db->FROM('srp_erp_crm_valutypes');
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Type');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['valueTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}


if (!function_exists('all_crm_pipelines')) {
    function all_crm_pipelines($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("pipeLineID,pipeLineName");
        $CI->db->FROM('srp_erp_crm_pipeline');
        $CI->db->where('opportunityYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Pipeline');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['pipeLineID'] ?? '')] = trim($row['pipeLineName'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('all_crm_project_pipelines')) {
    function all_crm_project_pipelines($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("pipeLineID,pipeLineName");
        $CI->db->FROM('srp_erp_crm_pipeline');
        $CI->db->where('projectYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Pipeline');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['pipeLineID'] ?? '')] = trim($row['pipeLineName'] ?? '');
            }
        }
        return $data_arr;
    }
}

/*Load all Accounts for select2*/
/*if (!function_exists('load_all_campaigns')) {
    function load_all_campaigns($status = true)/*Load all Supplier*/
/*{
    $CI =& get_instance();
    $CI->db->SELECT("campaignID,name");
    $CI->db->FROM('srp_erp_crm_campaignmaster');
    $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
    $campaigns = $CI->db->get()->result_array();
    $campaigns_arr = array('' => 'Select Campaign');
    if (isset($campaigns)) {
        foreach ($campaigns as $row) {
            $campaigns_arr[trim($row['campaignID'] ?? '')] = trim($row['name'] ?? '');
        }
    }
    return $campaigns_arr;
}
}*/

if (!function_exists('action_pipeline')) {
    function action_pipeline($YN)
    {
        if ($YN == 1) {
            $black = 'color:rgb(6,2,2);';
        } else {
            $black = 'color:rgb(203,203,203);';

        }
        return '<div style="text-align: center"><span title="Assign" rel="tooltip" class="glyphicon glyphicon-ok" style="' . $black . '"></span></div>';

    }
}

if (!function_exists('get_pipelineName')) {
    function get_pipelineName($masterID)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT pipeLineName FROM `srp_erp_crm_pipeline` WHERE companyID='{$companyID}' AND pipeLineID='{$masterID}'")->row_array();
        return $data;

    }
}
if (!function_exists('userAction')) {
    function userAction($userID, $activeYN)
    {
        $checked = '';
        if ($activeYN == 1) {
            $checked = 'checked';
        }
        $html = '<div style="text-align: center"><input ' . $checked . ' type="checkbox" id="activeYN" onclick="activateUser(this,' . $userID . ')" name="activeYN" value="1" ></div>';
        return $html;

    }
}

if (!function_exists('userActionSuperAdmin')) {
    function userActionSuperAdmin($userID, $activeYN)
    {
        $checked = '';
        if ($activeYN == 1) {
            $checked = 'checked';
        }
        $html = '<div style="text-align: center"><input ' . $checked . ' type="checkbox" id="isSuperAdmin" onclick="activateSuperAdmin(this,' . $userID . ')" name="isSuperAdmin" value="1" ></div>';
        return $html;

    }
}

if (!function_exists('groupAction')) {
    function groupAction($groupID, $activeYN)
    {
        $checked = '';
        if ($activeYN == 1) {
            $checked = 'checked';
        }
        $html = '<div style="text-align: center"><input ' . $checked . ' type="checkbox" id="activeYN" onclick="activateGroup(this,' . $groupID . ')" name="activeYN" value="1" ></div>';
        return $html;

    }
}

if (!function_exists('pipelinestage_action')) {
    function pipelinestage_action($pipeLineDetailID, $value, $name, $pipeLineID)
    {
        switch ($name) {

            case 'stagename':
                $html = '<div class="hideinput hide xxx_' . $pipeLineDetailID . '">
<input class="' . $name . '" type="text" value="' . $value . '" id="' . $name . '_' . $pipeLineDetailID . '" name="' . $name . '" >
</div>
<div class="showinput xx_' . $pipeLineDetailID . '" id="' . $name . '_1' . $pipeLineDetailID . '">' . $value . '</div>';
                break;
            case 'percentage':
                $html = '<div class="hideinput hide xxx_' . $pipeLineDetailID . '">
                <input class="' . $name . '" type="number" max="100" min="0" value="' . $value . '" id="' . $name . '_' . $pipeLineDetailID . '" name="' . $name . '" >
                </div>
                <div class="showinput xx_' . $pipeLineDetailID . '" id="' . $name . '_1' . $pipeLineDetailID . '">' . $value . '</div>';
                break;

            case 'order':
                $CI =& get_instance();
                $companyID = $CI->common_data['company_data']['company_id'];
                $sort = $CI->db->query("SELECT sortOrder FROM srp_erp_crm_pipelinedetails WHERE companyID='{$companyID}' AND pipeLineID='{$pipeLineID}'")->result_array();
                $select = '<div class="hideinput hide xxx_' . $pipeLineDetailID . '"><select class="" id="' . $name . '_' . $pipeLineDetailID . '" name="' . $name . '" >';
                if ($sort) {
                    foreach ($sort as $val) {
                        $selected = '';
                        if ($value == $val['sortOrder']) {
                            $selected = 'selected';
                        }
                        $select .= '<option ' . $selected . ' value="' . $val['sortOrder'] . '" >' . $val['sortOrder'] . '</option>';
                    }
                }

                $select .= '</select ></div>';

                $html = $select . '<div class="showinput xx_' . $pipeLineDetailID . '" id="' . $name . '_1' . $pipeLineDetailID . '">' . $value . '</div>';
                break;

        }
        return $html;
    }
    }

    if (!function_exists('all_crm_status')) {
        function all_crm_status($status = true)
        {
            $CI =& get_instance();
            $CI->db->SELECT("documentID,description");
            $CI->db->FROM('srp_erp_crm_documents');
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


    if (!function_exists('editCampaign')) {
        function editCampaign($typeID, $description)
        {

            return $description;
        }
    }
    if (!function_exists('statuscolor')) {
        function statuscolor($statuscolor)
        {

            return '<span class="label" style="background-color: '.$statuscolor.'">&nbsp;</span>';
        }
    }


    function xeditable_edit($statusID,$description,$col){

       $html= '<a href="#" data-type="text" data-url="'.site_url('Crm/updateLeadStatus').'" data-pk="'.$statusID.'"
data-name="'.$col.'" data-title="Description" class="xeditable " data-value="'.$description.'"> '.$description.' </a>';

       return $html;

    }

    if (!function_exists('all_crm_groupMaster')) {
        function all_crm_groupMaster($status = true)
        {
            $CI =& get_instance();
            $CI->db->SELECT("groupID,groupName");
            $CI->db->FROM('srp_erp_crm_usergroups');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
           if ($status) {

            } else {
                $CI->db->where('isAdmin', 0);
            }
            $data = $CI->db->get()->result_array();
            if ($status) {
                $data_arr = array('' => 'Select a Group');
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['groupID'] ?? '')] = trim($row['groupName'] ?? '');
                }
            }
            return $data_arr;
        }
    }

    if (!function_exists('crm_isSuperAdmin')) {
        function crm_isSuperAdmin()
        {
            $CI =& get_instance();
            $currentuserID = current_userID();
            $companyID = $CI->common_data['company_data']['company_id'];
            $data = $CI->db->query("SELECT isSuperAdmin FROM srp_erp_crm_users WHERE employeeID = '{$currentuserID}'  AND companyID = '{$companyID}'")->row_array();
            return $data;
        }
    }

    if (!function_exists('crm_isGroupAdmin')) {
        function crm_isGroupAdmin()
        {
            $CI =& get_instance();
            $currentuserID = current_userID();
            $companyID = $CI->common_data['company_data']['company_id'];
            $data = $CI->db->query("SELECT adminYN FROM srp_erp_crm_usergroupdetails WHERE empID = '{$currentuserID}' AND companyID = '{$companyID}'")->row_array();
            return $data;
        }
    }

    /*Load all employees for select2*/
    if (!function_exists('fetch_project_multiple')) {
        function fetch_project_multiple($status = true)/*Load all Supplier*/
        {
            $CI =& get_instance();
            $CI->db->SELECT("projectID,projectName");
            $CI->db->FROM('srp_erp_crm_project');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $project = $CI->db->get()->result_array();
            if ($status) {
                $project_arr = array('' => 'Select Project');
            } else {
                $project_arr = [];
            }
            if (isset($project)) {
                foreach ($project as $row) {
                    $project_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
                }
            }
            return $project_arr;
        }
    }


    /*Load all contacts*/
    if (!function_exists('all_contact_master')) {
        function all_contact_master($custom = true)
        {
            $CI =& get_instance();
            $CI->db->select("contactID, CONCAT(firstName, '',lastName) as fullName");
            $CI->db->from('srp_erp_crm_contactmaster');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $contact = $CI->db->get()->result_array();
            if ($custom) {
                $contact_arr = array('' => 'Select Contact');
            } else {
                $contact_arr = array('' => 'Contact');
            }
            if (isset($contact)) {
                foreach ($contact as $row) {
                    $contact_arr[trim($row['contactID'] ?? '')] = (trim($row['fullName'] ?? ''));
                }
            }
            return $contact_arr;
        }
    }

    // master search filter by user responsible
    if (!function_exists('all_crm_users_responsible')) {
        function all_crm_users_responsible($status = true)
        {
            $CI =& get_instance();
            $CI->db->SELECT("userID,employeeID,employeeName");
            $CI->db->FROM('srp_erp_crm_users');
            $CI->db->where('activeYN', 1);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $data = $CI->db->get()->result_array();
            if ($status) {
                $data_arr = array('' => 'User Responsible');
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['employeeID'] ?? '')] = trim($row['employeeName'] ?? '');
                }
            }
            return $data_arr;
        }
    }

    /*Load all leads for select2*/
    if (!function_exists('fetch_all_leads')) {
        function fetch_all_leads($status = true)
        {
            $CI =& get_instance();
            $CI->db->SELECT("leadID,firstName,lastName");
            $CI->db->FROM('srp_erp_crm_leadmaster');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $lead = $CI->db->get()->result_array();
            if ($status) {
                $lead_arr = array('' => 'Select Lead');
            } else {
                $lead_arr = [];
            }
            if (isset($lead)) {
                foreach ($lead as $row) {
                    $lead_arr[trim($row['leadID'] ?? '')] = trim($row['firstName'] ?? '')." ".trim($row['lastName'] ?? '');
                }
            }
            return $lead_arr;
        }
    }

    /*Load all CRM opportunities for select2*/
    if (!function_exists('fetch_all_opportunities')) {
        function fetch_all_opportunities($status = true)
        {
            $CI =& get_instance();
            $CI->db->SELECT("opportunityID,opportunityName");
            $CI->db->FROM('srp_erp_crm_opportunity');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $opportunity = $CI->db->get()->result_array();
            if ($status) {
                $opportunity_arr = array('' => 'Select Opportunity');
            } else {
                $opportunity_arr = [];
            }
            if (isset($opportunity)) {
                foreach ($opportunity as $row) {
                    $opportunity_arr[trim($row['opportunityID'] ?? '')] = trim($row['opportunityName'] ?? '');
                }
            }
            return $opportunity_arr;
        }
    }

    /*Load all employees for select2*/
    if (!function_exists('fetch_project_forProfile_converted')) {
        function fetch_project_forProfile_converted($status = true)/*Load all Supplier*/
        {
            $CI =& get_instance();
            $CI->db->SELECT("projectID,projectName");
            $CI->db->FROM('srp_erp_crm_project');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $CI->db->where('opportunityID !=', '');
            $project = $CI->db->get()->result_array();
            if ($status) {
                $project_arr = array('' => 'Select Project');
            } else {
                $project_arr = [];
            }
            if (isset($project)) {
                foreach ($project as $row) {
                    $project_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
                }
            }
            return $project_arr;
        }
    }
    if (!function_exists('all_projects_category')) {
        function all_projects_category($status = true)
        {
            $CI =& get_instance();
            $CI->db->select("categoryID,description");
            $CI->db->from('srp_erp_crm_categories');
            $CI->db->where('documentID', 9);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $types = $CI->db->get()->result_array();
            if ($status) {
                $types_arr = array('' => 'Select Category');
            } else {
                $types_arr = array('' => 'Select Category');
            }
            if (isset($status)) {
                foreach ($types as $row) {
                    $types_arr[trim($row['categoryID'] ?? '')] = (trim($row['description'] ?? ''));
                }
            }
            return $types_arr;
        }
    }

    if (!function_exists('all_projects_category_multiple')) {
        function all_projects_category_multiple()
        {
            $CI =& get_instance();
            $CI->db->select("categoryID,description");
            $CI->db->from('srp_erp_crm_categories');
            $CI->db->where('documentID', 9);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $types = $CI->db->get()->result_array();
            $types_arr = array();

            foreach ($types as $row) {
                $types_arr[trim($row['categoryID'] ?? '')] = (trim($row['description'] ?? ''));
            }
            return $types_arr;
        }
    }
    if (!function_exists('chk_assignee')) {
        function chk_assignee($taskid)
        {
            $CI =& get_instance();
            $currentuserID = current_userID();
            $companyID = $CI->common_data['company_data']['company_id'];
            $data = $CI->db->query("SELECT empID FROM `srp_erp_crm_assignees` WHERE companyID = '{$companyID}' AND documentID = 2 AND MasterAutoID = '{$taskid}' AND empID = '{$currentuserID}'")->row_array();
            return $data;
        }
    }
    if (!function_exists('generateUserIcon')) {
    function generateUserIcon($user_name){
        $nameArr = explode(" ", $user_name);
        $name_str = "";
        foreach ($nameArr as $name) {
            $name_str.= substr($name, 0, 1);
        }
        $icon = '<a class="user_icon" style="background-color:'.getColor().'">'.$name_str.'</a>';
        return $icon;
    }
    }

    if (!function_exists('getColor')) {
    function getColor(){
        $ColorArr = array("#A27BA7","#C72A3B","#DA6784","#0495C2","#0F3353","#6872FF","#488957","#FF59AC","#999999","#996855","#3C3636");
        $k = array_rand($ColorArr);
        return $ColorArr[$k];
    }
    }
    if (!function_exists('load_all_opportunities')) {
        function load_all_opportunities()
        {
            $CI =& get_instance();
            $CI->db->select("srp_erp_companycurrencyassign.currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
            $CI->db->from('srp_erp_currencymaster');
            $CI->db->join('srp_erp_companycurrencyassign', 'srp_erp_companycurrencyassign.currencyID = srp_erp_currencymaster.currencyID');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $currency = $CI->db->get()->result_array();
            $currency_arr = 'Select opportunities';
            if (isset($currency)) {
                foreach ($currency as $row) {
                    $currency_arr[trim($row['currencyID'] ?? '')] = trim($row['CurrencyCode'] ?? '') . ' | ' . trim($row['CurrencyName'] ?? '');
                }
            }
            return $currency_arr;
        }
    }
    if (!function_exists('send_Email_crm_mailbox')) {
        function send_Email_crm_mailbox($mailData, $attachment = 0, $last_id = 0)
        {
            $CI =& get_instance();

            $CI->load->library('email_manual');

            $approvalEmpID = $mailData['approvalEmpID'];
            $documentCode = $mailData['documentCode'];
            $toEmail = $mailData['toEmail'];

            $ccemail =  $mailData['ccEmail'];
            $bccEmail = $mailData['bccEmail'];

            $subject = $mailData['subject'];
           // $param = $mailData['param'];
            $message = $mailData['message'];
            $from = $mailData['from'];

            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            $config['wordwrap'] = TRUE;
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = $CI->config->item('email_smtp_host');
            $config['smtp_user'] = $CI->config->item('email_smtp_username');
            $config['smtp_pass'] = $CI->config->item('email_smtp_password');
            $config['smtp_crypto'] = 'tls';
            $config['smtp_port'] = '587';
            $config['crlf'] = "\r\n";
            $config['newline'] = "\r\n";
            $CI->load->library('email', $config);
            if (array_key_exists("from", $mailData)) {
                if(hstGeras==1){
                    $CI->email->from($CI->config->item('email_smtp_from'), $mailData['from']);
                }else{
                    $CI->email->from($CI->config->item('email_smtp_from'), $mailData['from']);
                }
            } else {
                if(hstGeras==1){
                    $CI->email->from($CI->config->item('email_smtp_from'), EMAIL_SYS_NAME);
                }else{
                    $CI->email->from($CI->config->item('email_smtp_from'), EMAIL_SYS_NAME);
                }
            }

            if (!empty($message)) {
                $CI->email->to($toEmail);
                if(!empty($ccemail))
                {
                    $CI->email->cc($ccemail);
                }
                if(!empty($bccEmail))
                {
                    $CI->email->bcc($bccEmail);
                }
                $CI->email->subject($subject);
                $CI->email->message($message);


                if ($attachment == 1) {

                    $CI->db->select("*");
                    $CI->db->from('srp_erp_crm_emailattachments');
                    $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
                    $CI->db->where('emailID', $last_id);
                    $pathlink = $CI->db->get()->result_array();

                    foreach($pathlink as $val)
                    {
                        $path = UPLOAD_PATH.base_url().'/attachments/crm/Crm_mailbox/crm_mailbox_attachments/'.$val['myFileName'];
                        $CI->email->attach($path);
                    }

                }
            }
            $result = $CI->email->send();
            $CI->email->clear(TRUE);

        }
    }
if (!function_exists('load_employee_drop_crm')) {
    function load_employee_drop_crm()
    {
        $CI =& get_instance();
        $CI->db->SELECT("userID,employeeID,employeeName");
        $CI->db->FROM('srp_erp_crm_users');
        $CI->db->where('activeYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
            $data_arr = array('' => 'Created By');

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['employeeID'] ?? '')] = trim($row['employeeName'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('all_crm_critirias')) {
    function all_crm_critirias()
    {
        $CI =& get_instance();
        $CI->db->select("criteriaID,Description");
        $CI->db->from('srp_erp_crm_closingcriterias');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $status = $CI->db->get()->result_array();

        $status_arr = array('' => 'Select a Criteria');

        if (isset($status)) {
            foreach ($status as $row) {
                $status_arr[trim($row['criteriaID'] ?? '')] = (trim($row['Description'] ?? ''));
            }
        }
        return $status_arr;
    }
}
if (!function_exists('opportunitytype')) {
    function opportunitytype()
    {
        $CI =& get_instance();
        $CI->db->SELECT("typeID,Description");
        $CI->db->FROM('srp_erp_crm_opportunitytypes');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select a Type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['typeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('get_all_crm_images')) {
    function get_all_crm_images($contactImagetesrt,$path,$type)
    {
        $CI =& get_instance();
        $CI->load->library('s3');
        if($type=="contact")
        {
            $noimage = $CI->s3->createPresignedRequest('images/crm/icon-list-contact.png', '+1 hour');
        }else if($type=="org")
        {
            $noimage = $CI->s3->createPresignedRequest('images/crm/organization.png', '+1 hour');
        }
        if($contactImagetesrt!='')
        {
            $crm_image = $CI->s3->createPresignedRequest($path.$contactImagetesrt , '+1 hour');
        }else
        {
            $crm_image = $noimage;
        }

        return $crm_image;
    }
}

