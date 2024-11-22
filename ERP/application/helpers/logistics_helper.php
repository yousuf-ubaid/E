<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * Date: 28/2/2020
 * Time: 2:41 PM
 */

if (!function_exists('load_job_request_action')) { /*get job request action list*/
    function load_job_request_action($masterID, $createdUserID,$confirmedYN)
    {
        //var_dump($masterID);
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if($confirmedYN != 1)
        {
            $status .= '<a onclick=\'fetchPage("system/logistics/erp_job_request",' . $masterID . ',"Edit Job Request"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<a onclick="delete_job_request(' . $masterID . ',\'Job Request\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }


        $status .= '<a target="_blank" onclick="load_job_request_view(' . $masterID . ')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></a>';


        $status .= '</span>';
        return $status;
    }
}
/*Load all Logistic service type*/
if (!function_exists('all_logistic_servicetype_drop')) {
    function all_logistic_servicetype_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("serviceID, serviceType");
        $CI->db->from('srp_erp_logisticservicetypes');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        // $CI->db->where('deletedYN', 0);
        $serviceType = $CI->db->get()->result_array();
        if ($status) {
            $serviceType_arr = array('' => 'Select service type');
        } else {
            $serviceType_arr = [];
        }
        if (isset($serviceType)) {
            foreach ($serviceType as $row) {
                $serviceType_arr[trim($row['serviceID'] ?? '')] = trim($row['serviceType'] ?? '');
            }
        }

        return $serviceType_arr;
    }
}

/*Load all Logistic status */
if (!function_exists('all_logistic_status_drop')) {
    function all_logistic_status_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("statusID, statusDescription");
        $CI->db->from('srp_erp_logisticstatus');
        //$CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('type', 1);
        $sysstatus = $CI->db->get()->result_array();
        if ($status) {
            $sysstatus_arr = array('' => 'Select system status');
        } else {
            $sysstatus_arr = [];
        }
        if (isset($sysstatus)) {
            foreach ($sysstatus as $row) {
                $sysstatus_arr[trim($row['statusID'] ?? '')] = trim($row['statusDescription'] ?? '');
            }
        }

        return $sysstatus_arr;
    }

}

if (!function_exists('load_logisticDocument')) {
    function load_logisticDocument()
    {

        $CI =& get_instance();
        $CI->db->select("docID, description");
        $CI->db->from('srp_erp_logisticdocumentmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        //$CI->db->where('type', 1);
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Document Description');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['docID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;

    }
}

if (!function_exists('action_servicetype')) {
    function action_servicetype($serviceID, $serviceType)
    {
        $serviceType = "'" . $serviceType . "'";
        $action = '<a onclick="fetch_mandatorydocument(' . $serviceID . ', ' . $serviceType . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-cog"></span></a>';

        $action .= '&nbsp; | &nbsp;<a onclick="edit_servicetype(' . $serviceID . ', ' . $serviceType . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp; | &nbsp;<a onclick="load_servicetype_items_details('.$serviceID.')"><i
                                title="" rel="tooltip" class="fa fa-list" data-original-title="Add"></i></a>'; 
        $action .= '&nbsp; | &nbsp;<a onclick="delete_servicetype(' . $serviceID . ', ' . $serviceType . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';
    }
}

if (!function_exists('fetch_item_details')) {
    function fetch_item_details($type = 0, $state = TRUE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("servicetypeitems.serviceItemID AS serviceItemID, srp_erp_itemmaster.*");
        $CI->db->FROM('srp_erp_itemmaster');
        $CI->db->JOIN(' ( SELECT * FROM srp_erp_logisticservicetypeitems WHERE serviceID = 1 ) AS servicetypeitems ' ,'  `servicetypeitems`.`itemID` = `srp_erp_itemmaster`.`itemAutoID` ' , 'LEFT' );
        $CI->db->WHERE('mainCategory', 'Service');
        $CI->db->where('srp_erp_itemmaster.companyID', $CI->common_data['company_data']['company_id']);

        $data = $CI->db->get()->result_array();

        if ($state == TRUE) {
             $data_arr = array('' => 'Select Item');
        } else {
            $data_arr = [];
        }

        if ($type == 0) {
        
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['itemAutoID'] ?? '')] =   trim($row['itemSystemCode'] ?? '').' | '.trim($row['seconeryItemCode'] ?? '') .' | '.trim($row['itemDescription'] ?? '');
                }
                return $data_arr;

            }
        } else {
            return $data;
        }

    }
}

if (!function_exists('load_logistic_action')) {
    function load_logistic_action($id,$releasedDate,$type,$invoiceAutoID)
    {
        $action = '';
        if($type == 1)
        {
            if(!empty($invoiceAutoID) || ($invoiceAutoID!=''))
            {
                $action .= $releasedDate;
            }else
            {
                $action .= '<a onclick="generatecustomerinvoce('.$id.')">'.$releasedDate.'</a>';
            }
        }else
        {
            if($releasedDate!=0)
            {
                if(!empty($invoiceAutoID) || ($invoiceAutoID!=''))
                {
                    $action .= $releasedDate;
                }else
                {
                    $action .= '<a onclick="update_relasedate('.$id.')">'.$releasedDate.'</a>';
                }

            }else
            {
                $action .= '<a onclick="update_relasedate('.$id.')">Update</a>';
            }
        }
        return '<span class="pull-center">' . $action . '</span>';
    }
}
if (!function_exists('load_upload_actions')) {
    function load_upload_actions($invoiceAutoID,$invoiceCode)
    {
        $action = '';

        if($invoiceAutoID)
        {
            $action = '<a target="_blank" onclick="documentPageView_modal(\'CINV\','.$invoiceAutoID.')"><span title="Invoice" rel="tooltip" class="glyphicon glyphicon-list-alt"></span></a>';
        }else
        {
            $action = '-';
        }



        return '<span class="pull-center">' . $action . '</span>';
    }
}


if (!function_exists('action_status')) {
    function action_status($statusID, $description,$type)
    {
        $description = "'" . $description . "'";
        $action = '<a onclick="edit_status(' . $statusID . ','.$description.','.$type.')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_status(' . $statusID . ', ' . $description . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}
