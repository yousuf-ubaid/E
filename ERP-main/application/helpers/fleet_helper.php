<?php

if (!function_exists('load_vehicles'))
{
    function load_vehicles()
    {
        $CI = &get_instance();
        $CI->db->SELECT("fuelBodyID,description");
        $CI->db->FROM('fleet_fuel_body');
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Vehicle');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['fuelBodyID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('load_vehicle_color'))
{
    function load_vehicle_color()
    {
        $CI = &get_instance();
        $CI->db->SELECT("colourID,description");
        $CI->db->FROM('fleet_colour');
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Vehicle Color');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['colourID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('load_vehicle_brand'))
{
    function load_vehicle_brand()
    {
        $CI = &get_instance();
        $CI->db->SELECT("brandID,description");
        $CI->db->FROM('fleet_brand_master');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Brand');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['brandID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('load_vehicle_model'))
{
    function load_vehicle_model()
    {
        $CI = &get_instance();
        // $brand = trim($CI->input->post('vehicleMasterID'));

        $CI->db->SELECT("modelID,description");
        $CI->db->FROM('fleet_brand_model');
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Model');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['modelID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('load_asset_sub'))
{
    function load_asset_sub()
    {
        $CI = &get_instance();
        // $brand = trim($CI->input->post('vehicleMasterID'));

        $CI->db->SELECT("modelID,description");
        $CI->db->FROM('fleet_brand_model');
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Sub Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['modelID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

// if (!function_exists('load_asset_main'))
// {
//     function load_asset_main()
//     {
//         $CI = &get_instance();
//         $CI->db->SELECT("brandID,description");
//         $CI->db->FROM('fleet_brand_master');
//         $CI->db->WHERE('companyID', current_companyID());
//         $CI->db->order_by('description');
//         $data = $CI->db->get()->result_array();
//         $data_arr = array('' => 'Select Main Category');
//         if (isset($data))
//         {
//             foreach ($data as $row)
//             {
//                 $data_arr[trim($row['brandID'] ?? '')] = trim($row['description'] ?? '');
//             }
//         }
//         return $data_arr;
//     }
// }
if (!function_exists('load_asset_main'))
{
    function load_asset_main()
    {
        $CI = &get_instance();
        $CI->db->SELECT("brandID,description");
        $CI->db->FROM('fleet_brand_master');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Main Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['brandID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('load_asset_main_filter'))
{
    function load_asset_main_filter()
    {
        $CI = &get_instance();
        $CI->db->SELECT("brand_id,brand_description");
        $CI->db->FROM('fleet_vehiclemaster');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->order_by('brand_description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Main Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['brand_id'] ?? '')] = trim($row['brand_description'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('load_asset_sub_filter'))
{
    function load_asset_sub_filter()
    {
        $CI = &get_instance();
        $CI->db->SELECT("model_id,model_description");
        $CI->db->FROM('fleet_vehiclemaster');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->order_by('model_description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Main Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['model_id'] ?? '')] = trim($row['model_description'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('load_fuel_type'))
{
    function load_fuel_type()
    {
        $CI = &get_instance();
        $CI->db->SELECT("fuelTypeID,description");
        $CI->db->FROM('fleet_fuel_type');
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Fuel');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['fuelTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('vehicle_active_status'))
{
    function vehicle_active_status($active)
    {
        $status = '<center>';
        if ($active == 1)
        {
            $status .= '<span class="label" style="background-color:#8bc34a; color: #FFFFFF;">&nbsp;</span>';
        }
        elseif ($active == 0)
        {
            $status .= '<span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF;">&nbsp;</span>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_vehicle_master_action')) {
    function load_vehicle_master_action($vehicleMasterID, $VehicleNo) {
        $CI = &get_instance();
        $CI->load->library('session');

        $VehicleNo = "'" . $VehicleNo . "'";

        $CI->db->select('*');
        $CI->db->from('fleet_fuelusagedetails');
        $CI->db->where('vehicleMasterID', $vehicleMasterID);
        $datas = $CI->db->get()->row_array();

        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $action .= '<li>
                        <a href="#" onclick="fetchPage(\'system/Fleet_Management/load_Vehicle_edit_view\',' . $vehicleMasterID . ',\'Edit Asset\',' . $VehicleNo . ')">
                            <span class="glyphicon glyphicon-pencil" style="color: #116f5e;" title="Edit"></span> Edit
                        </a>
                    </li>';

        $action .= '<li>
                        <a href="#" onclick="fetchPage(\'system/Fleet_Management/fleet_saf_vehicleView\',' . $vehicleMasterID . ',\'View Details\')">
                            <span class="glyphicon glyphicon-eye-open" style="color: #03a9f4" title="View"></span> View Details
                        </a>
                    </li>';

        if (!$datas) {
            $action .= '<li>
                            <a href="#" onclick="delete_vehicle(' . $vehicleMasterID . ', ' . $VehicleNo . ')">
                                <span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);" title="Delete"></span> Delete
                            </a>
                        </li>';
        }

        $action .= '</ul></div>';

        return $action;
    }
}

if (!function_exists('action_vehicleMaster'))
{
    function action_vehicleMaster($vehicleMasterID, $VehicleNo)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $VehicleNo = "'" . $VehicleNo . "'";

        $CI->db->select('*');
        $CI->db->from('fleet_fuelusagedetails');
        $CI->db->where('vehicleMasterID', $vehicleMasterID);
        $datas = $CI->db->get()->row_array();

        if ($datas)
        {
            $action = '<a href="#"
                               onclick="fetchPage(\'system/Fleet_Management/load_Vehicle_edit_view\',' . $vehicleMasterID . ',\'Edit Asset  \',' . $VehicleNo . ')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;|&nbsp;&nbsp';

            $action .= '<a href="#"
                                   onclick="fetchPage(\'system/Fleet_Management/fleet_saf_vehicleView\',' . $vehicleMasterID . ',\'View Details\')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="View"></span></a>';


            return '<span class="pull-right">' . $action . '</span>';
        }
        else
        {
            $action = '<a href="#"
                               onclick="fetchPage(\'system/Fleet_Management/load_Vehicle_edit_view\',' . $vehicleMasterID . ',\'Edit Asset \',' . $VehicleNo . ')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;|&nbsp;&nbsp';

            $action .= '<a href="#"
                                   onclick="fetchPage(\'system/Fleet_Management/fleet_saf_vehicleView\',' . $vehicleMasterID . ',\'View Details\')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="View"></span></a>';

            $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_vehicle(' . $vehicleMasterID . ', ' . $VehicleNo . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

            return '<span class="pull-right">' . $action . '</span>';
        }
    }
}

if (!function_exists('action_fuelMaster'))
{
    function action_fuelMaster($fuelTypeID)
    {
        $CI = &get_instance();
        $CI->load->library('session');

        //  $data = $CI->db->query("SELECT * FROM srp_erp_bloodgrouptype")->result_array();
        $CI->db->select('*');
        $CI->db->from('fleet_vehiclemaster');
        $CI->db->where('fuelTypeID', $fuelTypeID);
        $datas = $CI->db->get()->row_array();
        if ($datas)
        {
            $action = '<a href="#"
                               onclick="fetchPage(\'system/Fleet_Management/fleet_saf_fuelMaster\',' . $fuelTypeID . ',\'\')">';

            $action .= '<a onclick="edit_fuel(' . $fuelTypeID . ')">';
            $action .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

            return '<span class="pull-right">' . $action . '</span>';
        }
        else
        {
            $action = '<a href="#"
                               onclick="fetchPage(\'system/Fleet_Management/fleet_saf_fuelMaster\',' . $fuelTypeID . ',\'\')">';

            $action .= '<a onclick="edit_fuel(' . $fuelTypeID . ')">';
            $action .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span>';
            $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_fuel(' . $fuelTypeID . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

            return '<span class="pull-right">' . $action . '</span>';
        }
    }
}


if (!function_exists('driver_active_status'))
{
    function driver_active_status($active)
    {
        $status = '<center>';
        if ($active == 1)
        {
            $status .= '<span class="label" style="background-color:#8bc34a; color: #FFFFFF;">&nbsp;</span>';
        }
        elseif ($active == 0)
        {
            $status .= '<span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF;">&nbsp;</span>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_driver_master_action')) {
    function load_driver_master_action($driverMasID, $driverName) {
        $CI = &get_instance();
        $CI->load->library('session');

        $driverName = "'" . $driverName . "'";

        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $action .= '<li>
                        <a href="#" onclick="fetchPage(\'system/Fleet_Management/load_Driver_edit_view\',' . $driverMasID . ',\'Edit Driver - \')">
                            <span class="glyphicon glyphicon-pencil" style="color: #116f5e" title="Edit"></span> Edit
                        </a>
                    </li>';

        $action .= '<li>
                        <a href="#" onclick="fetchPage(\'system/Fleet_Management/fleet_saf_driverView\',' . $driverMasID . ',\'View Details\')">
                            <span class="glyphicon glyphicon-eye-open" style="color: #03a9f4" title="View"></span> View Details
                        </a>
                    </li>';

        $action .= '<li>
                        <a href="#" onclick="delete_driver(' . $driverMasID . ', ' . $driverName . ')">
                            <span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);" title="Delete"></span> Delete
                        </a>
                    </li>';

        $action .= '</ul></div>';

        return $action;
    }
}

if (!function_exists('action_driverMaster'))
{
    function action_driverMaster($driverMasID, $driverName)
    {
        $driverName = "'" . $driverName . "'";
        // $action = '<a onclick="edit_driver(' . $driverMasID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        $action = '<a href="#"
                               onclick="fetchPage(\'system/Fleet_Management/load_Driver_edit_view\',' . $driverMasID . ',\'Edit Driver - \')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;|&nbsp;&nbsp';

        $action .= '<a href="#"
                                   onclick="fetchPage(\'system/Fleet_Management/fleet_saf_driverView\',' . $driverMasID . ',\'View Details\')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="View"></span></a>';


        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_driver(' . $driverMasID . ', ' . $driverName . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('load_bloodGroup'))
{
    function load_bloodGroup()
    {
        $CI = &get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_bloodgrouptype")->result_array();
        return $data;
    }
}

/* ----------------------  Transaction ------------------------*/

if (!function_exists('fetch_all_segment'))
{
    function fetch_all_segment()
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fuel_supplier_drop'))
{
    function fuel_supplier_drop()
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("supplierAutoID,supplierSystemCode,supplierName");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        $customer = $CI->db->get()->result_array();
        $customer_arr = array('' => $CI->lang->line('common_select_supplier'));
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['supplierAutoID'] ?? '')] =  trim($row['supplierSystemCode'] ?? '') . ' | ' . trim($row['supplierName'] ?? '');
            }
        }
        return $customer_arr;
    }
}

if (!function_exists('fetch_all_vehicle'))
{
    function fetch_all_vehicle()
    {
        $CI = &get_instance();
        $CI->db->SELECT("vehicleMasterID,vehicleCode,VehicleNo,fuel_type_description");
        $CI->db->FROM('fleet_vehiclemaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isActive', 1);
        $CI->db->order_by('vehicleCode');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Vehicle');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['vehicleMasterID'] ?? '')] = trim($row['vehicleCode'] ?? '') . '|' . trim($row['VehicleNo'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_all_drivers'))
{
    function fetch_all_drivers()
    {
        $CI = &get_instance();
        $CI->db->SELECT("driverMasID,driverCode,driverName");
        $CI->db->FROM('fleet_drivermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isActive', 1);
        $CI->db->order_by('driverCode');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Driver');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['driverMasID'] ?? '')] = trim($row['driverCode'] ?? '') . '|' . trim($row['driverName'] ?? '');
            }
        }
        return $data_arr;
    }
}


if (!function_exists('employee_drop'))
{
    function employee_drop()
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,Ename2");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);

        $customer = $CI->db->get()->result_array();
        $customer_arr = array('' => $CI->lang->line('common_select_employee'));
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }
        return $customer_arr;
    }
}

if (!function_exists('fetch_gl_categories'))
{
    function fetch_gl_categories()
    {
        $CI = &get_instance();
        $CI->db->SELECT("glConfigAutoID,glConfigDescription");
        $CI->db->from('fleet_glconfiguration');

        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['glConfigAutoID'] ?? '')] = trim($row['glConfigDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_fuel_usage_action')) {
    function load_fuel_usage_action($fuelusageID, $confirmedYN, $isDeleted, $approvedYN, $createdUserID) {
        $CI = &get_instance();
        $CI->load->library('session');
        
        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($isDeleted == 1) {
            $action .= '<li><a onclick="reOpen_fuel_usage(' . $fuelusageID . ');">
                            <span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);" title="Reopen"></span> Reopen</a></li>';
        }

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $action .= '<li><a onclick=\'fetchPage("system/Fleet_Management/fleet_saf_newFuelUsage",' . $fuelusageID . ',"Edit Fuel Usage","FU");\'>
                            <span class="glyphicon glyphicon-pencil" style="color: #116f5e" title="Edit"></span> Edit</a></li>';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) && $approvedYN == 0 && $confirmedYN == 1 && $isDeleted == 0) {
            $action .= '<li><a onclick="referbackFuelUsage(' . $fuelusageID . ');">
                            <span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);" title="Refer Back"></span> Refer Back</a></li>';
        }

        $action .= '<li><a target="_blank" onclick="documentPageView_modal(\'FU\',\'' . $fuelusageID . '\');">
                        <span class="glyphicon glyphicon-eye-open" style="color: #03a9f4" title="View"></span> View</a></li>';

        $action .= '<li><a target="_blank" href="' . site_url('Fleet/load_fleet_fuel_comfirmation') . '/' . $fuelusageID . '">
                        <span class="glyphicon glyphicon-print" style="color: #607d8b" title="Print"></span> Print</a></li>';

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $action .= '<li><a onclick="delete_document(' . $fuelusageID . ',\'Return\');">
                            <span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);" title="Delete"></span> Delete</a></li>';
        }
        $action .= '</ul></div>';

        return $action;
    }
}

if (!function_exists('action_fuel_usage'))
{
    function action_fuel_usage($fuelusageID, $confirmedYN, $isDeleted, $approvedYN, $createdUserID)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        if ($isDeleted == 1)
        {
            $status .= '<a onclick="reOpen_fuel_usage(' . $fuelusageID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($confirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '<a onclick=\'fetchPage("system/Fleet_Management/fleet_saf_newFuelUsage",' . $fuelusageID . ',"Edit Fuel Usage","FU"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approvedYN == 0 and $confirmedYN == 1 && $isDeleted == 0)
        {
            $status .= '<a onclick="referbackFuelUsage(' . $fuelusageID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'FU\',\'' . $fuelusageID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('Fleet/load_fleet_fuel_comfirmation') . '/' . $fuelusageID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        if ($confirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;|&nbsp;<a onclick="delete_document(' . $fuelusageID . ',\'Return\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';
        return $status;
    }

    if (!function_exists('fuel_usage_approval_action'))
    {
        function fuel_usage_approval_action($fuelusageID, $approvalLevelID, $approvedYN, $documentApprovedID, $documentID)
        {
            $status = '<span class="pull-right">';
            if ($approvedYN == 0)
            {
                $status .= '<a onclick=\'fetch_approval("' . $fuelusageID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
            }
            else
            {
                $status .= '<a target="_blank" onclick="PageView_modal(\'FU\',\'' . $fuelusageID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
            }

            // $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_transfer_view/') . '/' . $fuelusageID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

            $status .= '</span>';

            return $status;
        }
    }
}


/* ===================================  */

if (!function_exists('fetch_supplier_drop'))
{
    function fetch_supplier_drop($status = true, $IsActive = null)
    {
        $CI = &get_instance();
        $CI->db->SELECT("supplierAutoID,supplierName");
        $CI->db->FROM('srp_erp_suppliermaster');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->WHERE('masterApprovedYN', 1);
        $CI->db->order_by('supplierAutoID', 'ASC');
        if ($IsActive == 1)
        {
            $CI->db->where('isActive', 1);
        }
        $donor = $CI->db->get()->result_array();
        if ($status)
        {
            $supp_arr = array('' => 'Select Supplier');
        }
        else
        {
            $supp_arr = [];
        }
        if (isset($donor))
        {
            foreach ($donor as $row)
            {
                $supp_arr[trim($row['supplierAutoID'] ?? '')] = trim($row['supplierName'] ?? '');
            }
        }
        return $supp_arr;
    }
}
/* GL Configuration */

if (!function_exists('load_gl_config_table_action'))
{ /*get po action list*/
    function load_gl_config_table_action($glConfigAutoID)
    {
        $CI = &get_instance();
        $CI->load->library('session');

        $CI->db->select('*');
        $CI->db->from('fleet_fuelusagedetails');
        $CI->db->where('glConfigAutoID', $glConfigAutoID);
        $datas = $CI->db->get()->row_array();
        if (empty($datas))
        {
            $status = '<span class="pull-right">';
            $status .= '<a onclick="editGLconfig(' . $glConfigAutoID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp &nbsp|&nbsp &nbsp <a onclick="deleteGLconfig(' . $glConfigAutoID . ')"><span style="color:rgb(209, 91, 71);" title="Edit" rel="tooltip" class="glyphicon glyphicon-trash"></span></a>';
            $status .= '</span>';
            return $status;
        }
        else
        {
            $status = '<span class="pull-right">';
            $status .= '<a onclick="editGLconfig(' . $glConfigAutoID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
            $status .= '</span>';
            return $status;
        }
    }
}
if (!function_exists('load_all_assets'))
{
    function load_all_assets()
    {
        $CI = &get_instance();
        $CI->db->SELECT("faID,faCode,assetDescription");
        $CI->db->FROM('srp_erp_fa_asset_master');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->WHERE('approvedYN', 1);
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Asset');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['faID'] ?? '')] = trim($row['faCode'] ?? '') . ' | ' . trim($row['assetDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('load_all_maintenacecompany'))
{
    function load_all_maintenacecompany()
    {
        $CI = &get_instance();
        $CI->db->SELECT("maintenance_id,company_name,status");
        $CI->db->FROM('fleet_maintenance_company');
        $CI->db->WHERE('status', 1);
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Maintenace Company');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['maintenance_id'] ?? '')] = trim($row['company_name'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_all_assettypes'))
{
    function load_all_assettypes()
    {
        // Hardcoded asset types array
        $data_arr = array(
            '' => 'Select Asset Type',
            '1' => 'Asset',
            '2' => 'Component'
            // Add more asset types here if needed
        );

        return $data_arr;
    }
}


if (!function_exists('load_all_maintenacetype'))
{
    function load_all_maintenacetype()
    {
        $CI = &get_instance();
        $CI->db->SELECT("maintenanceTypeID,type");
        $CI->db->FROM('fleet_maintenancetype');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Maintenace Type');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['maintenanceTypeID'] ?? '')] = trim($row['type'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('load_all_maintenacecriteria'))
{
    function load_all_maintenacecriteria()
    {
        $CI = &get_instance();
        $CI->db->SELECT("maintenanceCriteriaID,maintenanceCriteria,status");
        $CI->db->WHERE('status', 1);
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->FROM('fleet_maintenance_criteria');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Maintenace Criteria');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['maintenanceCriteriaID'] ?? '')] = trim($row['maintenanceCriteria'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('all_maintenancecompany_drop'))
{
    function all_maintenancecompany_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status)
        {
            $supplier_arr = array('' => 'Select Maintenace Company');
        }
        else
        {
            $supplier_arr = [];
        }
        if (isset($supplier))
        {
            foreach ($supplier as $row)
            {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        return $supplier_arr;
    }
}
if (!function_exists('load_all_crew'))
{
    function load_all_crew()
    {
        $CI = &get_instance();
        $CI->db->SELECT("crewTypeID,Description,");
        $CI->db->FROM('fleet_maintenancecrewtype');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Maintenace Crew');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['crewTypeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_asset_master_action')) {
    function load_asset_master_action($vehicleMasterID, $assetSerialNo) {
        $CI = &get_instance();
        $CI->load->library('session');
        $assetSerialNo = "'" . $assetSerialNo . "'";

        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $CI->db->select('*');
        $CI->db->from('fleet_fuelusagedetails');
        $CI->db->where('vehicleMasterID', $vehicleMasterID);
        $datas = $CI->db->get()->row_array();

        $action .= '<li>
                        <a href="#" onclick="fetchPage(\'system/Fleet_Management/load_asset_edit_view\',' . $vehicleMasterID . ',\'Edit Asset \',' . $assetSerialNo . ')">
                            <span class="glyphicon glyphicon-pencil" style="color: #03a9f4"></span> Edit
                        </a>
                    </li>';

        $action .= '<li>
                        <a href="#" onclick="fetchPage(\'system/Fleet_Management/fleet_saf_assetView\',' . $vehicleMasterID . ',\'View Details\')">
                            <span class="glyphicon glyphicon-eye-open" style="color: #116f5e"></span> View
                        </a>
                    </li>';

        if (!$datas) {
            $action .= '<li>
                            <a href="#" onclick="delete_asset(' . $vehicleMasterID . ', ' . $assetSerialNo . ')">
                                <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" title="Delete" rel="tooltip"></span> Delete
                            </a>
                        </li>';
        }

        $action .= '</ul></div>';

        return $action;
    }
}

if (!function_exists('action_assetMaster'))
{
    function action_assetMaster($vehicleMasterID, $assetSerialNo)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $assetSerialNo = "'" . $assetSerialNo . "'";

        $CI->db->select('*');
        $CI->db->from('fleet_fuelusagedetails');
        $CI->db->where('vehicleMasterID', $vehicleMasterID);
        $datas = $CI->db->get()->row_array();

        if ($datas)
        {
            $action = '<a href="#"
                               onclick="fetchPage(\'system/Fleet_Management/load_asset_edit_view\',' . $vehicleMasterID . ',\'Edit Asset  \',' . $assetSerialNo . ')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;|&nbsp;&nbsp';

            $action .= '<a href="#"
                                   onclick="fetchPage("system/Fleet_Management/fleet_saf_assetView",' . $vehicleMasterID . ',\'View Details\')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="View"></span></a>';


            return '<span class="pull-right">' . $action . '</span>';
        }
        else
        {
            $action = '<a href="#"
                               onclick="fetchPage(\'system/Fleet_Management/load_asset_edit_view\',' . $vehicleMasterID . ',\'Edit Asset \',' . $assetSerialNo . ')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span> &nbsp;&nbsp;|&nbsp;&nbsp';

            $action .= '<a href="#"
                                   onclick="fetchPage(\'system/Fleet_Management/fleet_saf_assetView\',' . $vehicleMasterID . ',\'View Details\')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="View"></span></a>';

            $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_asset(' . $vehicleMasterID . ', ' . $assetSerialNo . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

            return '<span class="pull-right">' . $action . '</span>';
        }
    }
}
if (!function_exists('fa_asset_category_sub'))
{ /*get po action list*/
    function fa_asset_category_sub($id = null)
    {
        $CI = &get_instance();
        $companyId = $CI->common_data['company_data']['company_id'];


        $CI->db->SELECT("modelID,description");
        $CI->db->from('fleet_brand_model');
        // $CI->db->where('companyID', $companyId);
        $CI->db->where('brandID', $id);

        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['modelID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('fa_asset_sub'))
{ /*get po action list*/
    function fa_asset_sub($id = null)
    {
        $CI = &get_instance();
        $companyId = $CI->common_data['company_data']['company_id'];


        $CI->db->SELECT("model_id,model_description");
        $CI->db->from('fleet_vehiclemaster');
        // $CI->db->where('companyID', $companyId);
        $CI->db->where('brand_id', $id);

        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['model_id'] ?? '')] = trim($row['model_description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('asset_category'))
{
    function asset_category($id = null, $empty = true)
    {
        $CI = &get_instance();
        $company_code = $CI->common_data['company_data']['company_code'];
        $companyId = $CI->common_data['company_data']['company_id'];

        $itemCategoryID = $CI->db->query("SELECT brandID FROM `fleet_brand_master` WHERE `asset_type_id` = '1' AND `companyID` = '{$companyId}'")->row_array();


        $CI->db->SELECT("brandID,description");
        $CI->db->from('fleet_brand_master');
        $CI->db->where('companyID', $companyId);
        $CI->db->where('vehicleMasterID', $itemCategoryID['brandID']);

        $data = $CI->db->get()->result_array();
        if ($empty)
        {
            $data_arr = array('' => '');
        }
        else
        {
            $data_arr = array();
        }


        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['brandID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('fetch_asset_utilization'))
{
    function fetch_asset_utilization($id = FALSE, $state = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('vehicleMasterID, vehicleCode, assetSerialNo, manufacturedYear, vehDescription');
        $CI->db->from('fleet_vehiclemaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('asset_type_id', 1);
        $data = $CI->db->get()->result_array();

        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description'));
        }
        else
        {
            $data_arr = [];
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($id)
                {
                    // Return only vehicleCode if $id is TRUE
                    $data_arr[trim($row['vehicleMasterID'] ?? '')] = trim($row['vehicleCode'] ?? '');
                }
                else
                {
                    // Return the full string with vehicleCode, vehDescription, and assetSerialNo
                    $data_arr[trim($row['vehicleMasterID'] ?? '')] = trim($row['vehicleCode'] ?? '') . ' | ' . trim($row['vehDescription'] ?? '') . ' | ' . trim($row['assetSerialNo'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}


if (!function_exists('fetch_com_utilization'))
{
    function fetch_com_utilization($id = FALSE, $state = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('vehicleMasterID, vehicleCode, assetSerialNo, vehDescription');
        $CI->db->from('fleet_vehiclemaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('asset_type_id', 2);
        $data = $CI->db->get()->result_array();

        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description'));
        }
        else
        {
            $data_arr = [];
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($id)
                {
                    // Return only vehicleCode if $id is TRUE
                    $data_arr[trim($row['vehicleMasterID'] ?? '')] = trim($row['vehicleCode'] ?? '');
                }
                else
                {
                    // Return the full string with vehicleCode, vehDescription, and assetSerialNo
                    $data_arr[trim($row['vehicleMasterID'] ?? '')] = trim($row['vehicleCode'] ?? '') . ' | ' . trim($row['vehDescription'] ?? '') . ' | ' . trim($row['assetSerialNo'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_rig'))
{
    function fetch_rig($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('*');
        $CI->db->from('srp_erp_jobs_field_rig_masters');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['id'] ?? '') . ' | ' . trim($row['rig_hoist_name'] ?? '');
            }

            return $data_arr;
        }
    }
}
if (!function_exists('fetch_thread_utilization'))
{
    function fetch_thread_utilization($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('*');
        $CI->db->from('fleet_asset_utilization_status');
        $CI->db->where('related_to', 1);

        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['status'] ?? '');
            }

            return $data_arr;
        }
    }
}
if (!function_exists('fetch_physical_utilization'))
{
    function fetch_physical_utilization($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('*');
        $CI->db->from('fleet_asset_utilization_status');
        $CI->db->where('related_to', 2);

        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['status'] ?? '');
            }

            return $data_arr;
        }
    }
}


if (!function_exists('fetch_rig'))
{
    function fetch_rig($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('*');
        $CI->db->from('rigmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['idrigmaster'] ?? '')] = trim($row['idrigmaster'] ?? '') . ' | ' . trim($row['RigDescription'] ?? '');
            }

            return $data_arr;
        }
    }
}
if (!function_exists('fetch_thread_utilization'))
{
    function fetch_thread_utilization($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('*');
        $CI->db->from('fleet_asset_utilization_status');
        $CI->db->where('related_to', 1);

        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['status'] ?? '');
            }

            return $data_arr;
        }
    }
}
if (!function_exists('fetch_physical_utilization'))
{
    function fetch_physical_utilization($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('*');
        $CI->db->from('fleet_asset_utilization_status');
        $CI->db->where('related_to', 2);

        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['status'] ?? '');
            }

            return $data_arr;
        }
    }
}

if (!function_exists('fetch_status_utilization'))
{
    function fetch_status_utilization($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('*');
        $CI->db->from('fleet_asset_utilization_status');
        $CI->db->where('related_to', 3);

        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_description')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['status'] ?? '');
            }

            return $data_arr;
        }
    }
}
if (!function_exists('get_submission_status_label'))
{
    function get_submission_status_label($is_submitted)
    {
        if ($is_submitted == 1)
        {
            return '<span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">Submitted</span>';
        }
        else
        {
            return '<span class="label" style="background-color: rgba(255, 72, 49, 0.96);  color: #FFFFFF; font-size: 11px;">Draft</span>';
        }
    }
}

if (!function_exists('load_maintenance_action')) {
    function load_maintenance_action($maintenanceID, $is_submitted)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        
        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $action .= '<li><a onclick="attachment_modal(' . $maintenanceID . ',\'Maintenance\',\'MT\');">
                        <span class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment
                    </a></li>';
        
        $action .= '<li><a href="#" onclick="fetchPage(\'system/Fleet_Management/fleet_inspection_view\',' . $maintenanceID . ',\'View Details\')">
                        <span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View
                    </a></li>';

        if ($is_submitted == 0) {
            $action .= '<li><a onclick="fetchPage(\'system/Fleet_Management/fleet_utilization_new\',' . $maintenanceID . ',\'Edit Maintenance\');">
                            <span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit
                        </a></li>';

            $action .= '<li><a onclick="delete_maintenance(' . $maintenanceID . ');">
                            <span  class="glyphicon glyphicon-trash" style="color: red;"></span> Delete
                        </a></li>';
        }

        if ($is_submitted == 1) {
            $action .= '<li><a target="_blank" href="' . site_url('Fleet/load_fleet_inspection_comfirmation') . '/' . $maintenanceID . '">
                            <span  class="glyphicon glyphicon-print" style="color: #607d8b"></span> Print
                        </a></li>';
        }

        $action .= '</ul></div>';
        
        return $action;
    }
}

if (!function_exists('action_inspection'))
{
    function action_inspection($utiID, $is_submitted)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $action = '<span class="pull-right">';

        $action .= '<a onclick="attachment_modal(' . $utiID . ',\'Inspection\',\'UT\');">
        <span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip glyphicon-paperclip-btn"></span>
    </a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        $action .= '<a href="#"
    onclick="fetchPage(\'system/Fleet_Management/fleet_inspection_view\',' . $utiID . ',\'View Details\')"><span
         title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
         data-original-title="View"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        if ($is_submitted == 0)
        {
            $action .= '<a onclick=\'fetchPage("system/Fleet_Management/fleet_utilization_new",' . $utiID . ',"Edit Inspection","UT"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            // $action = '<a onclick="fleet_utilization_new/editUtilization(' .$utiID. ');"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            $action .= '<a onclick="delete_uti(' . $utiID . ',\'Return\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        // <<<<<<< Updated upstream
        //         if ($is_submitted == 1)
        //         {
        //             $action .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('Fleet/load_fleet_inspection_comfirmation') . '/' . $utiID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        //         }
        //         $action .= '&nbsp;|&nbsp;<a onclick="delete_uti(' . $utiID . ',\'Return\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        // =======

        if ($is_submitted == 1)
        {
            $action .= '<a target="_blank" href="' . site_url('Fleet/load_fleet_inspection_comfirmation') . '/' . $utiID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        }


        $action .= '</span>';
        return $action;
    }
}
if (!function_exists('load_asset_names_by_type'))
{
    function load_asset_names_by_type($asset_type = null)
    {
        $CI = &get_instance();

        // Initialize the array with a default option
        $data_arr = array('' => 'Select Asset Name');

        if ($asset_type !== null)
        {
            // Select vehicleMasterID and vehDescription from fleet_vehiclemaster
            $CI->db->select('vehicleMasterID, vehDescription');
            $CI->db->from('fleet_vehiclemaster');
            $CI->db->where('asset_type_id', $asset_type);
            $query = $CI->db->get();
            $result = $query->result_array();

            if (isset($result))
            {
                foreach ($result as $row)
                {
                    // Populate the array with vehicleMasterID as key and vehDescription as value
                    $data_arr[trim($row['vehicleMasterID'] ?? '')] = trim($row['vehDescription'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_assetstatus'))
{
    function load_assetstatus($assetStatus, $assetStatusDesc)
    {
        $status_badge = '<div>';
        if ($assetStatus == 7)
        {
            $status_badge .= '<span class="label label-success" style="font-size: 9px;" title="Ready" rel="tooltip">' . $assetStatusDesc . '</span>';
        }
        elseif ($assetStatus == 8)
        {
            $status_badge .= '<span class="label label-danger" style="font-size: 9px;" title="Down" rel="tooltip">' . $assetStatusDesc . '</span>';
        }
        elseif ($assetStatus == 9)
        {
            $status_badge .= '<span class="label label-warning" style="font-size: 9px;" title="In maintenance" rel="tooltip">' . $assetStatusDesc . '</span>';
        }
        elseif ($assetStatus == 10)
        {
            $status_badge .= '<span class="label label-info" style="font-size: 9px;" title="Due for maintenance" rel="tooltip">' . $assetStatusDesc . '</span>';
        }
        $status_badge .= '</div>';
        return $status_badge;
    }
}

if (!function_exists('load_inspection_templates'))
{
    function load_inspection_templates($utilizationDetailID)
    {
        // Get CodeIgniter instance
        $CI = &get_instance();

        // Prepare query to fetch inspection templates
        $CI->db->select("st.id, st.templateName");
        $CI->db->from('srp_erp_inspection_template_master st');
        $CI->db->where("FIND_IN_SET(st.id, (
            SELECT 
                inspectionTemplate 
            FROM 
                fleet_vehiclemaster vm 
            JOIN 
                fleet_asset_utilization_detail ud ON ud.asset_id = vm.vehicleMasterID
            WHERE 
                ud.id = {$CI->db->escape($utilizationDetailID)}
        ))");

        // Execute the query
        $output = $CI->db->get()->result_array();

        // Prepare array with default 'Select Template' option
        $data_arr = array('' => 'Select Template');

        // Process query results
        if (!empty($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['templateName'] ?? '');
            }
        }

        // Return the formatted array
        return $data_arr;
    }
}
