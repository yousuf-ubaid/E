<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('fetch_boq_approvals')) {
    function fetch_boq_approvals()
    {
        $CI =& get_instance();
        $currentUserID = current_userID();
        $companyID = current_companyID();
       $data =  $CI->db->query("SELECT headerID,approvalLevelID FROM `srp_erp_boq_header` 
        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_boq_header`.`headerID` 
	    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_boq_header`.`bdcurrentLevelNo`
	    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` =`srp_erp_boq_header`.`bdcurrentLevelNo`
        WHERE `srp_erp_documentapproved`.`documentID` IN ( 'PVE' ) AND `srp_erp_approvalusers`.`documentID` IN ( 'PVE') 
	    AND `srp_erp_approvalusers`.`employeeID` = '{$currentUserID}' AND `srp_erp_documentapproved`.`approvedYN` = '0' 
	    AND `srp_erp_boq_header`.`companyID` = '{$companyID}' AND `srp_erp_approvalusers`.`companyID` = '{$companyID}' 
        GROUP BY srp_erp_documentapproved.documentSystemCode")->row_array();
       return $data;
    }
}
if (!function_exists('get_all_project_teamrole')) {
    function get_all_project_teamrole()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $cateogry = $CI->db->query("SELECT roleID, roleDescription FROM `srp_erp_project_role` WHERE CompanyID = $companyID ")->result_array();
        $cateogry_arr = array('' => 'Select a Role');
        if (isset($cateogry)) {
            foreach ($cateogry as $row) {
                $cateogry_arr[trim($row['roleID'] ?? '')] = trim($row['roleDescription'] ?? '');
            }
        }

        return $cateogry_arr;
    }

}

if (!function_exists('get_all_asset_drop')) {
    function get_all_asset_drop($assetype)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $cateogry = $CI->db->query("SELECT faID,faCode,assetDescription FROM `srp_erp_fa_asset_master` WHERE CompanyID = $companyID AND assetType = $assetype AND confirmedYN = 1 AND approvedYN = 1")->result_array();
        $cateogry_arr = array('' => 'Select a Asset');
        if (isset($cateogry)) {
            foreach ($cateogry as $row) {
                $cateogry_arr[trim($row['faID'] ?? '')] = trim($row['faCode'] ?? '').'|'.trim($row['assetDescription'] ?? '');
            }
        }

        return $cateogry_arr;
    }
}
if (!function_exists('fetch_active_status_checklist')) {
    function fetch_active_status_checklist($isactive)
    {
        $status = '<center>';
            if($isactive == 1)
            {
                $status .= '<span class="label label-success"  style="font-size: 9px; width: 10%; "  title="Not Collected" rel="tooltip">Active</span>';
            }else
            {
                $status .= '<span class="label label-danger" style="font-size: 9px; width: 10%; padding: 0.2em 1.6em 0.3em;" title="Collected" rel="tooltip">In Active </span>';
            }
            $status .= '</center>';
            return $status;
    }
}
if (!function_exists('fetch_checklistactions')) {
    function fetch_checklistactions($checklistID)
    {

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/pm/create_checklist_detail",' . $checklistID . ',"Check List Detail","CHL"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';


        $status .= '<a onclick="delete_item(' . $checklistID . ',\'Receipt Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('check_label_text')) {
    function check_label_text($checklistmasterID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $query = $CI->db->query("SELECT IF(checklistDetailID!='',1,0) as checklistone, IF(checklistDetailID1!='',1,0) as checklisTwo,
                                                IF(checklistDetailID2!='',1,0) as checklistthree,IF(checklistDetailID3!='',1,0) as checklistfour FROM srp_erp_checklistcriteria 
                                        WHERE companyID = $companyID AND documentID = 'PM' AND checklistmasterID = $checklistmasterID")->row_array();
        return $query;
    }
}
if (!function_exists('check_list_temp')) {
    function check_list_temp()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $template = $CI->db->query("SELECT checklistID, checklistDescription FROM `srp_erp_checklistmaster` where companyID = $companyID AND isActive = 1 AND documentID = 'PM'")->result_array();
        $checklisttemp = array('' => 'Select a Template');
        if (isset($template)) {
            foreach ($template as $row) {
                $checklisttemp[trim($row['checklistID'] ?? '')] = trim($row['checklistDescription'] ?? '');
            }
        }

        return $checklisttemp;
    }
}
if (!function_exists('fetch_templateval_detail')) {
    function fetch_templateval_detail($criteriaID,$checklistdetailID,$documentchecklistID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $template = $CI->db->query("SELECT
criteriavalue
FROM
	`srp_erp_documentchecklistcriteriadetails`
	WHERE 
	companyID = 13
    AND documentchecklistID = $documentchecklistID
    AND criteriaID = $criteriaID
    AND checklistdetailID  = $checklistdetailID
")->row('criteriavalue');
      return $template;
    }
}
if (!function_exists('fetch_changerequestboardapproval')) {
    function fetch_changerequestboardapproval($requestID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $currentuserID = current_userID();
        $template = $CI->db->query("SELECT
	requestID,
	approvalLevelID 
FROM
	`srp_erp_changerequests`
	JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_changerequests`.`requestID` 
	AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_changerequests`.`currentLevelNo`
	JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_changerequests`.`currentLevelNo` 
WHERE
	`srp_erp_documentapproved`.`documentID` IN ( 'CR' ) 
	AND `srp_erp_approvalusers`.`documentID` IN ( 'CR' ) 
	AND `srp_erp_approvalusers`.`employeeID` = '$currentuserID' 
	AND `srp_erp_documentapproved`.`approvedYN` = '0' 
	AND `srp_erp_changerequests`.`companyID` = '$companyID'
	AND `srp_erp_approvalusers`.`companyID` = '$companyID' 
	AND requestID = $requestID
GROUP BY
	srp_erp_documentapproved.documentSystemCode")->row_array();
        return $template;
    }
}
if (!function_exists('fetch_boq_tempdetails')) {
    function fetch_boq_tempdetails($headerID,$tempElementKey,$tempkey,$tempElementSubKey)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $templatedet_value = $CI->db->query("SELECT
	fieldValue
FROM
	`srp_erp_pm_templateheader`
	LEFT JOIN srp_erp_pm_templatedetails on srp_erp_pm_templateheader.headerID = srp_erp_pm_templatedetails.headerID 
	where
	srp_erp_pm_templateheader.CompanyID  = $companyID 
	AND tempkey = '$tempkey'
	AND srp_erp_pm_templateheader.headerID = '$headerID'
	AND tempElementKey = '$tempElementKey'
	AND tempElementSubKey = '$tempElementSubKey'
")->row('fieldValue');
        return $templatedet_value;
    }
}
if (!function_exists('typeOfContract')) {
    function typeOfContract()
    {
        return [
            '' => 'Select Status', '1' => 'Cost to Cost', '2' => 'Charitable', '3' => 'Cost Plus', '4' => 'Normal Tendering'
        ];

    }
}

