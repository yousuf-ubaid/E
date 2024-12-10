<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * Date: 3/7/2017
 * Time: 5:41 PM
 */


if (!function_exists('confirm_mfq')) {
    function confirm_mfq($con)
    {
        $status = '<div style="text-align: center">';
        if ($con == 0) {
            $status .= '<div class="actioniconWarning"><span class="glyphicon glyphicon-ok" style="color:rgb(255, 255, 255);" title="Not Confirmed"></span></div>';
        } elseif ($con == 1) {
            $status .= '<div class="actionicon"><span class="glyphicon glyphicon-ok" style="color:rgb(255, 255, 255);" title="Confirmed"></span></div>';
        } elseif ($con == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($con == 3) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</div>';
        return $status;
    }
}

if (!function_exists('col_category')) {
    function col_category($id, $description, $functionName, $tmpCriteria = null)
    {
        // $criteria = preg_replace("/[^A-Za-z0-9\-\']/", "", $tmpCriteria);
        $xCriteria = str_replace("\n", "", $tmpCriteria ?? '');
        $criteria = str_replace("\r", "", $xCriteria ?? '');
        $desc = !empty($description) ? $description : '<span style="color:#9da1a1">un-categorised</span>';
        $string = '<button class="btn-link" onclick="' . $functionName . '(' . $id . ',\'' . $criteria . '\')">' . $desc . '</button>';
        return $string;
    }
}
if (!function_exists('gender_ico')) {
    function gender_ico($gender)
    {
        if ($gender == 1) {
            $output = '<div style="font-size:14px;" class="text-center"><i class="fa fa-male" style="color:blueviolet;" aria-hidden="true"></i></div>';
        } else {
            $output = '<div style="font-size:14px;" class="text-center"><i class="fa fa-female" style="color:deeppink;" aria-hidden="true"></i></div>';
        }
        return $output;
    }
}

if (!function_exists('countryDiv')) {
    function countryDiv($country)
    {
        $countryImg = base_url() . 'images/flags/' . trim($country) . '.png';
        $output = '<div><img src="' . $countryImg . '" /> ' . $country . '</div>';
        return $output;
    }
}


if (!function_exists('fetch_bom_detail')) {
    function fetch_bom_detail($bomMasterID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('mfq', $primaryLanguage);
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_billofmaterial');
        $CI->db->where('bomMasterID', $bomMasterID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bom = $CI->db->get()->row_array();

        return $bom;
    }
}

if (!function_exists('fetch_bom_materialconsumptions')) {
    function fetch_bom_materialconsumptions($bomMasterID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('mfq', $primaryLanguage);
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_bom_materialconsumption');
        $CI->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_bom_materialconsumption.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID", "left");
        $CI->db->join('srp_erp_unit_of_measure', "srp_erp_mfq_itemmaster.defaultUnitOfMeasureID = srp_erp_unit_of_measure.UnitID", "left");
        $CI->db->join('srp_erp_itemmaster', "srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID", "left");
        $CI->db->where('bomMasterID', $bomMasterID);
      //  $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bom = $CI->db->get()->result_array();

        return $bom;
    }
}

if (!function_exists('fetch_bom_billOverHead')) {
    function fetch_bom_billOverHead($bomMasterID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('mfq', $primaryLanguage);
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_bom_overhead');
        $CI->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_bom_overhead.overheadID = srp_erp_mfq_overhead.overHeadID", "left");
        $CI->db->join('srp_erp_unit_of_measure', "srp_erp_mfq_bom_overhead.uomID = srp_erp_unit_of_measure.UnitID", "left");
        $CI->db->where('bomMasterID', $bomMasterID);
      //  $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bom = $CI->db->get()->result_array();

        return $bom;
    }
}

if (!function_exists('fetch_bom_billOfLabour')) {
    function fetch_bom_billOfLabour($bomMasterID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('mfq', $primaryLanguage);
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_bom_labourtask');
        $CI->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_bom_labourtask.labourTask = srp_erp_mfq_overhead.overHeadID", "left");
        $CI->db->join('srp_erp_unit_of_measure', "srp_erp_mfq_bom_labourtask.uomID = srp_erp_unit_of_measure.UnitID", "left");
        $CI->db->where('bomMasterID', $bomMasterID);
      //  $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bom = $CI->db->get()->result_array();

        return $bom;
    }
}


if (!function_exists('get_mfq_category_drop')) {
    function get_mfq_category_drop($parentID = 0, $categoryType = 1)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_category');
        $CI->db->where('masterID', $parentID);
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('categoryType', $categoryType);
        $CI->db->order_by('description');
        $output = $CI->db->get()->result_array();
        $result = array('-1' => 'Select');
        if (!empty($output)) {
            foreach ($output as $row) {
                $result[$row['itemCategoryID']] = $row['description'];
            }
        }
        return $result;
    }
}


if (!function_exists('edit_mfq_crew')) {
    function edit_mfq_crew($id, $isFromERP)
    {
        $status = '<span class="pull-right">';
        if ($isFromERP) {
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/crew/manage-crew\',' . $id . ',\'Edit Crew\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<span style="color:#8B0000; font-size:13px;" onclick="link_crew_master(' . $id . ')"><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/crew/manage-crew\',' . $id . ',\'Edit Crew\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('edit_mfq_customer')) {
    function edit_mfq_customer($id, $isFromERP)
    {
        $status = '<span class="pull-right">';
        if ($isFromERP) {
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/crew/manage-customer\',' . $id . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<span style="color:#8B0000; font-size:13px;"  onclick="link_customer_master(' . $id . ')"><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/crew/manage-customer\',' . $id . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('edit_mfq_segment')) {
    function edit_mfq_segment($id, $isFromERP)
    {
        $status = '<span class="pull-right">';
        if ($isFromERP) {
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
            $status .= '|&nbsp;';
            $status .= '<a onclick="fetchPage(\'system/mfq/master/manage-segment\',' . $id . ',\'Edit Segment\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<span style="color:#8B0000; font-size:13px;" onclick="link_segment_master(' . $id . ')" ><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= '|&nbsp;';
            $status .= '<a onclick="fetchPage(\'system/mfq/master/manage-segment\',' . $id . ',\'Edit Segment\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '|&nbsp;';
        $status .= '<a onclick="add_sub_segment(' . $id . ')"><span title="Add Sub Segment" rel="tooltip" class="glyphicon glyphicon-plus"></span></a>&nbsp;&nbsp;';
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('edit_mfq_warehouse')) {
    function edit_mfq_warehouse($id, $isFromERP, $warehouseAutoID)
    {
        $status = '<span class="pull-right">';
        if ($isFromERP || $warehouseAutoID) {
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/manage_warehouse\',' . $id . ',\'Edit Warehouse\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<span style="color:#8B0000; font-size:13px;" onclick="link_warehouse_master(' . $id . ')"><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/manage_warehouse\',' . $id . ',\'Edit Warehouse\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('edit_mfq_uom')) {
    function edit_mfq_uom($id, $isFromERP)
    {
        $status = '<span class="pull-right">';
        if ($isFromERP) {
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
            $status .= '<a onclick="fetchPage(\'system/mfq/master/manage-segment\',' . $id . ',\'Edit Segment\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<span style="color:#8B0000; font-size:13px;" ><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/master/manage-segment\',' . $id . ',\'Edit Segment\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('edit_mfq_item')) {
    function edit_mfq_item($id, $isFromERP, $itemAutoID, $categoryType)
    {
        $manufacturing_Flow = getPolicyValues('MANFL', 'All');
        $company = getPolicyValues('LNG', 'All'); 
       
        $status = '<span class="pull-right">';
        if ($isFromERP || $itemAutoID) {
            if(/*$manufacturing_Flow == 'GCC' && */$categoryType == 2 /*&& $company != 'FlowServe'*/){
                $status .= '<a onclick="qa_qc_add_model(' . $id . ')"><span title="QA / QC" rel="tooltip" class="fa fa-asterisk"></span></a>&nbsp;&nbsp;';
                $status .= ' &nbsp; | &nbsp; ';
            }
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/item-master/manage-item\',' . $id . ',\'Edit Crew\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        } else {
            if(/*$manufacturing_Flow == 'GCC' && */$categoryType == 2 /*&& $company != 'FlowServe'*/){
                $status .= '<a onclick="qa_qc_add_model(' . $id . ')"><span title="QA / QC" rel="tooltip" class="fa fa-asterisk"></span></a>&nbsp;&nbsp;';
                $status .= ' &nbsp; | &nbsp; ';
            }
            $status .= '<span style="color:#8B0000; font-size:13px;" onclick="link_item_master(' . $id . ')"><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/item-master/manage-item\',' . $id . ',\'Edit Crew\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('edit_mfq_asset')) {
    function edit_mfq_asset($id, $isFromERP)
    {
        $status = '<span class="pull-right">';
        if ($isFromERP) {
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/master/manage-machine\',' . $id . ',\'Edit Crew\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<span style="color:#8B0000; font-size:13px;"onclick="link_asset_master(' . $id . ')" ><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/mfq/master/manage-machine\',' . $id . ',\'Edit Crew\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('generateMFQ_SystemCode')) {
    function generateMFQ_SystemCode($tableName, $primaryKey, $companyIDCol = 'companyID', $documentID = null, $segmentID = null)
    {
        $CI = &get_instance();
        $CI->db->select_max('serialNo');
        $CI->db->from($tableName);
        $CI->db->where($companyIDCol, current_companyID());
        if($segmentID){
            $CI->db->where('mfqSegmentID', $segmentID);
        }
        //$CI->db->order_by($primaryKey, 'desc');
        $result = $CI->db->get()->row_array();

        if (!empty($result)) {
            $serialNo = $result['serialNo'] + 1;
            $systemCode = current_companyCode() . '/' . date('Y') . '/' . str_pad($serialNo, 5, '0', STR_PAD_LEFT);
        } else {
            $serialNo = 1;
            $systemCode = current_companyCode() . '/' . date('Y') . '/' . str_pad(1, 5, '0', STR_PAD_LEFT);
        }

        $output['serialNo'] = $serialNo;
        $output['systemCode'] = $systemCode;

        //update

        if($segmentID){
            $data_arr = array();
            $data_arr['serialNo'] = $serialNo;

            $CI->db->where($companyIDCol,current_companyID());
            if($segmentID){
                $CI->db->where('mfqSegmentID',$segmentID);
            }
            $CI->db->update($tableName,$data_arr);
        }
    

        return $output;
    }
}


if (!function_exists('get_overhead_categoryDrop')) {
    function get_overhead_categoryDrop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_overheadcategory');
        $CI->db->order_by('description');
        $output = $CI->db->get()->result_array();
        $result = array('' => 'Select');
        if (!empty($output)) {
            foreach ($output as $row) {
                $result[$row['overheadCategoryID']] = $row['description'];
            }
        }
        return $result;
    }
}

if (!function_exists('rfi_edit_action')) {
    function rfi_edit_action($rfiID,$rfiStatus)
    {
        $status = '<div style="text-align: center;">';
        $status .= '<a onclick=\'editRfi(' . $rfiID . ')\'><span class="glyphicon glyphicon-pencil"></span></a> &nbsp &nbsp';

        if($rfiStatus == 'Open'){
            $status .= '<a onclick=\'deleteRfi(' . $rfiID . ')\'><span class="glyphicon text-danger glyphicon-trash"></span></a>';
        }
       
        $status .= '</div>';
        return $status;
    }
}

if (!function_exists('rfi_type_action')) {
    function rfi_type_action($rfiType)
    {
       $rfiWord = '';
       switch ($rfiType) {
        case '1':
            $rfiWord = 'Stage Inspection';
            break;
        case '2':
            $rfiWord = 'Before Client Inspection';
            break;
        case '3':
            $rfiWord = 'Final Inspection / Functional Test';
            break;
        case '4':
            $rfiWord = 'Parts Inspection';
            break;
        case '5':
            $rfiWord = 'Client Inspection';
            break;
        case '6':
            $rfiWord = 'Load Out Inspection';
            break;
        default:
            $rfiWord = 'Other';
            break;
       }

       return $rfiWord;
    }
}

if (!function_exists('rfi_type_status')) {
    function rfi_type_status($val)
    {
        $status = '<div style="text-align: center;">';
        if($val == 'Open'){
            $status .= '<span class="badge badge-primary" style="background-color:#e78080; padding:5px;">Open</span>';
        }elseif($val == 'Submit'){
            $status .= '<span class="badge badge-success" style="background-color:#2d79e7; padding:5px;">Submitted</span>';
        } else  {
            $status .= '<span class="badge badge-danger" style="background-color:#82b552; padding:5px;">Closed</span>';
        }

        $status .= '</div>';
        return $status;

    }
}


if (!function_exists('job_stage_selection')) {
    function job_stage_selection()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_stage');
        $CI->db->where('status','1');
        $output = $CI->db->get()->result_array();
        
        $result = array('' => 'Select Stage');
        if (!empty($output)) {
            foreach ($output as $row) {
                $result[$row['stage_id']] = $row['stage_name'];
            }
        }

        return $result;
    }
}


if (!function_exists('editOverHead')) {
    function editOverHead($overHeadID)
    {
        $status = '<div style="text-align: center;">';
        $status .= '<a onclick=\'editOverHead(' . $overHeadID . ')\'><span class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '</div>';
        return $status;
    }
}

if (!function_exists('editLabour')) {
    function editLabour($overHeadID)
    {
        $status = '<div style="text-align: center;">';
        $status .= '<a onclick=\'editLabour(' . $overHeadID . ')\'><span class="glyphicon glyphicon-pencil"></span></a>';
        /*$status .= '&nbsp; | &nbsp;<a onclick=\'deleteLabour(' . $overHeadID . ')\'><span class="glyphicon glyphicon-trash text-red"></span></a>';*/
        $status .= '</div>';
        return $status;
    }
}

if (!function_exists('editBoM')) {
    function editBoM($bomID)
    {
        $status = '<div style="text-align: center">';
        $status .= '<a onclick="fetchPage(\'system/mfq/mfq_add_new_bill_of_material\',' . $bomID . ',\'Edit Bill of Material\',\'BOM\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp; | &nbsp;<a onclick="deleteBOM(' . $bomID . ');"><span class="glyphicon glyphicon-trash text-red"></span></a>';
        $status .= '</div>';

        return $status;
    }
}

if (!function_exists('editCustomerInquiry')) {
    function editCustomerInquiry($ciMasterID, $confirmedYN, $approvedYN)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $isEstimateCreated = $CI->db->query("SELECT estimateMasterID FROM `srp_erp_mfq_estimatemaster` WHERE ciMasterID = {$ciMasterID} AND companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
        $isEstimateDetCreated = $CI->db->query("SELECT srp_erp_mfq_estimatedetail.estimateMasterID FROM `srp_erp_mfq_estimatedetail` LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID WHERE srp_erp_mfq_estimatedetail.ciMasterID = {$ciMasterID} AND srp_erp_mfq_estimatedetail.companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
        $isReferback = '';
        if (empty($isEstimateCreated) && empty($isEstimateDetCreated)) {
            $isReferback = '&nbsp; | &nbsp; <a onclick="referbackCustomerInquiry_cus(' . $ciMasterID . ');"><span title="" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);" data-original-title="Refer Back"></span></a>';
        }


        $status = '<div style="text-align: center">';

        if ($confirmedYN == 1) {
            if ($approvedYN == 1) {
                $status .= '<a onclick="viewDocument(' . $ciMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>';
                if (empty($isEstimateCreated) && empty($isEstimateDetCreated)) {
                    $status .= '&nbsp; | &nbsp; <a onclick="createEstimate(' . $ciMasterID . ')" title="Create Estimate" rel="tooltip"><span class="fa fa-file-text"></span></a> ' . $isReferback . ' ';
                }
            } else {
                $status .= ' <a onclick="referbackCustomerInquiry(' . $ciMasterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp; | &nbsp;<a onclick="viewDocument(' . $ciMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>';
            }
        } else {
            $status .= '<a onclick="fetchPage(\'system/mfq/mfq_add_new_mfq\',' . $ciMasterID . ',\'Edit Customer Inquiry\',\'CI\');" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="viewDocument(' . $ciMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>';
        }
        $status .= '&nbsp;| <a onclick=\'attachment_modal_CI(' . $ciMasterID . ',"CUSTOMER INQUIRY","CI",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;| <a target="_blank" href="'.site_url('MFQ_CustomerInquiry/fetch_customer_inquiry_prints/').''. $ciMasterID . '"><span title="print" rel="tooltip" class="glyphicon glyphicon-print" data-original-title="Print"></span></a>&nbsp;&nbsp;';
        $status .= '</div>';


        return $status;
    }
}

if (!function_exists('editEstimate')) {
    function editEstimate($estimateMasterID, $confirmedYN, $estimateDetailID, $approvedYN, $jobID, $docApprovedYN, $isDeleted)
    {
        $PBM_policy = (getPolicyValues('PBM', 'All') == 1 ? getPolicyValues('PBM', 'All') : 0);

        $status = '<div style="text-align: center">';

        if ($confirmedYN == 1) {
            if ($estimateDetailID && $PBM_policy == 0) {
                if ($approvedYN) {
                    $status .= '<a onclick="viewDocument(' . $estimateMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>&nbsp;&nbsp;<a onclick="sendemail(' . $estimateMasterID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>&nbsp;&nbsp;<a onclick="load_jobOrder_view(' . $estimateMasterID . ',' . $jobID . ')" title="Job View" rel="tooltip"><i class="fa fa-book" aria-hidden="true"></i></a>';
                } else {
                    if ($docApprovedYN) {
                        $status .= '<a onclick="referbackEstimate(' . $estimateMasterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;<a onclick="sendemail(' . $estimateMasterID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>&nbsp;|&nbsp;<a onclick="viewDocument(' . $estimateMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>';
                    } else {
                        $status .= '<a onclick="referbackEstimate(' . $estimateMasterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;<a onclick="viewDocument(' . $estimateMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>';
                    }
                }
            } else {
                if ($approvedYN) {
                    $status .= '<a onclick="viewDocument(' . $estimateMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>&nbsp;&nbsp;<a onclick="createJob(' . $estimateMasterID . ')" title="Create Job" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i></a>&nbsp;&nbsp;<a onclick="createEstimateVersion(' . $estimateMasterID . ')" title="Create Revision" rel="tooltip"><i class="fa fa-repeat" aria-hidden="true"></i></a>&nbsp;&nbsp;<a onclick="sendemail(' . $estimateMasterID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
                } else {
                    $status .= '<a onclick="referbackEstimate(' . $estimateMasterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;<a onclick="viewDocument(' . $estimateMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>';
                }
            }
        } else if ($isDeleted == 1) {
            $status .= '<a onclick="viewDocument(' . $estimateMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>';
        } else {
            $status .= '<a onclick="fetchPage(\'system/mfq/mfq_add_new_estimate\',' . $estimateMasterID . ',\'Edit Estimate\',\'EST\');" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a onclick="viewDocument(' . $estimateMasterID . ')" title="View" rel="tooltip"><span class="fa fa-eye"></span></a>&nbsp;&nbsp;<a onclick="delete_estimate(' . $estimateMasterID . ')" title="Delete" rel="tooltip"><i class="fa fa-trash delete-icon"></i></a>';
        }
        $status .= '&nbsp;&nbsp;<a onclick=\'attachment_modal_EST(' . $estimateMasterID . ',"ESTIMATE","EST",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>';
        $status .= '&nbsp;&nbsp;<a onclick=\'viewProposal(' . $estimateMasterID .');\'><span title="Proposal Review" rel="tooltip" class="glyphicon glyphicon-file"></span></a>';
        $status .= '</div>';

        return $status;
    }
}

if (!function_exists('editJob')) {
    function editJob($workProcessID, $confirmedYN, $approvedYN, $isFromEstimate, $estimateMasterID = null, $linkedJobCard = null, $isDeleted = 1, $documentCode = "",$varianceYN = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        // $usageQtyUpdatePolicy = getPolicyValues('JUQ', 'All');
        $deleted = $CI->db->query("SELECT workProcessID FROM srp_erp_mfq_job WHERE linkedJobID = {$workProcessID} AND companyID = {$companyID}")->result_array();
        $documentID = "MFQ";
        if ($isFromEstimate == 1) {
            $documentID = "EST";
        }
        $status = '<div style="text-align: center">';
       
        if (is_null($linkedJobCard)) {
        
            if(empty($estimateMasterID)){
                $status .= '<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_job_create\',' . $workProcessID . ',\'Edit Job\',\'' . $documentID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;<a href="#" onclick="getWorkFlowStatus(' . $workProcessID . ')"><span title="Route Card" rel="tooltip" class="fa fa-cogs"></span></a></span>';
            }else{
                if ($isDeleted == 1) {
                    $status .= '<a onclick="load_jobOrder_view(' . $estimateMasterID . ',' . $workProcessID . ')" title="Job View" rel="tooltip"><i class="fa fa-book" aria-hidden="true"></i></a>';
                } else {
                    $status .= '<a href="#" onclick="createJob(' . $estimateMasterID . ',' . $workProcessID . ')"><i class="fa fa-file-text" aria-hidden="true" title="Generate Job" rel="tooltip"></i></a>&nbsp;<a onclick="load_jobOrder_view(' . $estimateMasterID . ',' . $workProcessID . ')" title="Job View" rel="tooltip"><i class="fa fa-book" aria-hidden="true"></i></a>';
                }
            }
        } else {
            $status .= '<a onclick="pulled_documents(' . $workProcessID . ',\'' . $documentCode . '\')"><span title="Pulled Documents" rel="tooltip"><i class="fa fa-tasks"></i></span></a>';
            $status .= '&nbsp;<a onclick="load_commInvoice_view(' . $workProcessID . ')" title="Commercial Invoice" rel="tooltip"><i class="fa fa-book" aria-hidden="true"></i></a>';
            $status .= '&nbsp;<a onclick="load_packingListInvoice_view(' . $workProcessID . ')" title="Packing List" rel="tooltip"><i class="fa fa-book" aria-hidden="true"></i></a>';
            $status .= '&nbsp;<a onclick="job_attachments(' . $workProcessID . ', \'JOB\', \'MFQ_JOB\', \'' . $confirmedYN . '\')"><span title="Attachments" rel="tooltip"><i class="glyphicon glyphicon-paperclip"></i></span></a>';
            if ($confirmedYN == 1) {
                if ($approvedYN == 1) {
                    $status .= '<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_job_create\',' . $workProcessID . ',\'Edit Job\',\'' . $documentID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;<a href="#" onclick="getWorkFlowStatus(' . $workProcessID . ')"><span title="Route Card" rel="tooltip" class="fa fa-cogs"></span></a></span>';
                } else {
                    $status .= ' <a onclick="referbackJob(' . $workProcessID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_job_create\',' . $workProcessID . ',\'Edit Job\',\'' . $documentID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;<a href="#" onclick="getWorkFlowStatus(' . $workProcessID . ')"><span title="Route Card" rel="tooltip" class="fa fa-cogs"></span></a></span>';
                }
            } else {
                $status .= '<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_job_create\',' . $workProcessID . ',\'Edit Job\',\'' . $documentID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;<a href="#" onclick="getWorkFlowStatus(' . $workProcessID . ')"><span title="Route Card" rel="tooltip" class="fa fa-cogs"></span></a>&nbsp;';
                // if($usageQtyUpdatePolicy == 1) {
                $status .= '<a href="#" onclick="updateUsageQty(' . $workProcessID . ')"><span title="Usage Qty" rel="tooltip" class="fa fa-arrow-up"></span></a></span>&nbsp;';
                // }
                $status .= '<a onclick="delete_sub_job(' . $workProcessID . ');"><i class="fa fa-trash delete-icon"></i></span></a>';
            }
            
            if($varianceYN != 1){
                $status .= '<a onclick="variance_documents(' . $workProcessID . ',\'' . $documentCode . '\')"><span title="Create Variance" rel="tooltip"><i class="fa fa-tasks"></i></span></a>';
            }
            
        }
        if ($approvedYN == 1) {
            $status .= '&nbsp;<a onclick="trace_mfq_document(' . $workProcessID . ',\'JOB\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a> ';
        }
        if (empty($deleted) && is_null($linkedJobCard) && $isDeleted != 1) {
            $status .= '&nbsp;<a onclick="delete_job(' . $workProcessID . ');"><i class="fa fa-trash delete-icon"></i></span></a>';
        }

        if($estimateMasterID){
            $status .= '&nbsp;&nbsp;<a onclick=\'attachment_modal_EST(' . $estimateMasterID . ',"ESTIMATE","EST",1);\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>';
        }

        $status .= '</div>';
        return $status;
    }
}


if (!function_exists('get_job_cardID')) {
    function get_job_cardID($workProcessID, $workFlowID, $templateDetailID)
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_mfq_jobcardmaster.*,ws.status");
        $CI->db->from('srp_erp_mfq_jobcardmaster');
        //$CI->db->join('srp_erp_mfq_workflowstatus', ' srp_erp_mfq_jobcardmaster.templateDetailID=srp_erp_mfq_workflowstatus.templateDetailID', 'LEFT');
        $CI->db->join('(SELECT * FROM srp_erp_mfq_workflowstatus WHERE jobID = ' . $workProcessID . ' AND templateDetailID = ' . $templateDetailID . ') ws', ' srp_erp_mfq_jobcardmaster.templateDetailID=ws.templateDetailID', 'LEFT');
        $CI->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $workProcessID);
        $CI->db->where('srp_erp_mfq_jobcardmaster.workFlowID', $workFlowID);
        $CI->db->where('srp_erp_mfq_jobcardmaster.templateDetailID', $templateDetailID);
        $output = $CI->db->get()->row_array();
        return $output;
    }
}

if (!function_exists('get_job_master')) {
    function get_job_master($workProcessID)
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_mfq_job.*,UnitDes,srp_erp_mfq_itemmaster.itemDescription,IFNULL(est.estimateCode,'') as estimateCode");
        $CI->db->from('srp_erp_mfq_job');
        $CI->db->join('srp_erp_mfq_itemmaster', ' srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID', 'LEFT');
        $CI->db->join('srp_erp_unit_of_measure', ' UnitID = defaultUnitOfMeasureID', 'LEFT');
        $CI->db->join('(SELECT IFNULL(estimateCode,"") as estimateCode,srp_erp_mfq_estimatedetail.estimateDetailID FROM srp_erp_mfq_estimatedetail LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatedetail.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID) est', 'est.estimateDetailID = srp_erp_mfq_job.estimateDetailID', 'LEFT');
        $CI->db->where('workProcessID', $workProcessID);
        $output = $CI->db->get()->row_array();
        return $output;
    }
}

if (!function_exists('all_bill_of_material_drop')) {
    function all_bill_of_material_drop($mfqItemID = null, $status = TRUE,$template = null)/*Load all Bom*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('mfq', $primaryLanguage);
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_billofmaterial');
        if(!empty($mfqItemID)){
            $CI->db->where('mfqItemID', $mfqItemID);
        }
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bom = $CI->db->get()->result_array();
        if ($status) {
            $bom_arr = array('' => $CI->lang->line('manufacturing_select_bom'));
        } else {
            $bom_arr = [];
        }
        if (isset($bom)) {
            if($template){
                foreach ($bom as $row) {
                    $bom_arr[trim($row['bomMasterID'] ?? '')] = trim($row['documentCode'] ?? '').' | '.trim($row['description'] ?? '');
                }
            }else{
                foreach ($bom as $row) {
                    $bom_arr[trim($row['bomMasterID'] ?? '')] = trim($row['documentCode'] ?? '').' | '.trim($row['description'] ?? '');
                }
            }
          
        }
        return $bom_arr;
    }
}

if (!function_exists('all_finish_goods_drop')) {
    function all_finish_goods_drop($status = TRUE)/*Load all Bom*/
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_itemmaster');
        $CI->db->where('itemType', 2);
        $CI->db->or_where('itemType', 3);
        $item = $CI->db->get()->result_array();
        if ($status) {
            $item_arr = array('' => 'Select Item');
        } else {
            $item_arr = [];
        }
        if (isset($item)) {
            foreach ($item as $row) {
                $item_arr[trim($row['mfqItemID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' - ' . trim($row['itemDescription'] ?? '');
            }
        }

        return $item_arr;
    }
}

if (!function_exists('get_finishedgoods_drop')) {
    function get_finishedgoods_drop($status = TRUE)
    {
        $where = ' (itemType=2 or itemType=3)';
        $CI = &get_instance();
        $CI->db->select("mfqItemID,itemSystemCode,itemDescription");
        $CI->db->from('srp_erp_mfq_itemmaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where($where);
        $output = $CI->db->get()->result_array();
        if ($status) {
            $result = array('' => 'Select Product');
        } else {
            $result = '';
        }
        if (!empty($output)) {
            foreach ($output as $row) {
                $result[$row['mfqItemID']] = $row['itemSystemCode'] . ' - ' . $row['itemDescription'];
            }
        }
        return $result;
    }
}


if (!function_exists('link_job_card_drop')) {
    function link_job_card_drop($templateMasterID = null, $templateDetailID = null, $status = TRUE, $defaulttype = null)/*Load all Bom*/
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_templatedetail');
        $CI->db->where('workFlowID', 1);
        $CI->db->where('templateMasterID', $templateMasterID);
        $CI->db->where('templateDetailID <', $templateDetailID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $job = $CI->db->get()->result_array();
        if ($status) {
            $job_arr = array('' => 'Select Job card');
        } else {
            $job_arr = [];
        }


        if (isset($job)) {
            foreach ($job as $row) {
                $job_arr[trim($row['templateDetailID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $job_arr;
    }
}


if (!function_exists('check_link_job_card')) {
    function check_link_job_card($templateMasterID = null, $templateDetailID = null)/*Load all Bom*/
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_templatedetail');
        $CI->db->where('workFlowID', 1);
        $CI->db->where('templateMasterID', $templateMasterID);
        $CI->db->where('templateDetailID <', $templateDetailID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $job = $CI->db->get()->result_array();
        return $job;
    }
}


if (!function_exists('get_prev_job_card')) {
    function get_prev_job_card($workProcessID, $workFlowID, $linkworkFlowID, $templateDetailID, $templateMasterID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from("srp_erp_mfq_customtemplatedetail");
        $CI->db->where("jobID", $workProcessID);
        $job = $CI->db->get()->result_array();
        $output = "";
        $output2 = "";
        if ($job) {

            $CI->db->select("jobcardID,srp_erp_mfq_customtemplatedetail.description,jobNo,quotationRef,srp_erp_mfq_jobcardmaster.description as jobDescription");
            $CI->db->from('srp_erp_mfq_jobcardmaster');
            $CI->db->join('srp_erp_mfq_customtemplatedetail', 'srp_erp_mfq_jobcardmaster.templateDetailID = srp_erp_mfq_customtemplatedetail.templateDetailID', 'inner');
            $CI->db->where('workProcessID', $workProcessID);
            $CI->db->where('srp_erp_mfq_jobcardmaster.workFlowID', $workFlowID);
            $CI->db->where('srp_erp_mfq_jobcardmaster.templateDetailID', $linkworkFlowID);
            $output = $CI->db->get()->row_array();

            $CI->db->select("jobcardID");
            $CI->db->from('srp_erp_mfq_customtemplatedetail');
            $CI->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.templateDetailID = srp_erp_mfq_customtemplatedetail.linkWorkFlow', 'inner');
            $CI->db->where('workProcessID', $workProcessID);
            $CI->db->where('srp_erp_mfq_jobcardmaster.workFlowID', $workFlowID);
            $CI->db->where('srp_erp_mfq_customtemplatedetail.templateDetailID <=', $templateDetailID);
            $CI->db->where('srp_erp_mfq_customtemplatedetail.templateMasterID', $templateMasterID);
            $output2 = $CI->db->get()->result_array();
            $output2 = array_column($output2, 'jobcardID');
        } else {
            $CI = &get_instance();
            $CI->db->select("jobcardID,srp_erp_mfq_templatedetail.description,jobNo,quotationRef,srp_erp_mfq_jobcardmaster.description as jobDescription");
            $CI->db->from('srp_erp_mfq_jobcardmaster');
            $CI->db->join('srp_erp_mfq_templatedetail', 'srp_erp_mfq_jobcardmaster.templateDetailID = srp_erp_mfq_templatedetail.templateDetailID', 'inner');
            $CI->db->where('workProcessID', $workProcessID);
            $CI->db->where('srp_erp_mfq_jobcardmaster.workFlowID', $workFlowID);
            $CI->db->where('srp_erp_mfq_jobcardmaster.templateDetailID', $linkworkFlowID);
            $output = $CI->db->get()->row_array();

            $CI->db->select("jobcardID");
            $CI->db->from('srp_erp_mfq_templatedetail');
            $CI->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.templateDetailID = srp_erp_mfq_templatedetail.linkWorkFlow', 'inner');
            $CI->db->where('workProcessID', $workProcessID);
            $CI->db->where('srp_erp_mfq_jobcardmaster.workFlowID', $workFlowID);
            $CI->db->where('srp_erp_mfq_templatedetail.templateDetailID <=', $templateDetailID);
            $CI->db->where('srp_erp_mfq_templatedetail.templateMasterID', $templateMasterID);
            $output2 = $CI->db->get()->result_array();
            $output2 = array_column($output2, 'jobcardID');
        }

        if ($output2) {
            $CI->db->select("SUM(materialCost) as materialCost,SUM(materialCharge) as materialCharge");
            $CI->db->from('srp_erp_mfq_jc_materialconsumption');
            $CI->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_jc_materialconsumption.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'inner');
            $CI->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID', 'inner');
            $CI->db->where_in('jobCardID', $output2);
            $result = $CI->db->get()->row_array();
            $data["materialConsumption"] = $result;
        } else {
            $data["materialConsumption"] = 0;
        }

        if ($output2) {
            $CI->db->select("SUM(totalValue) as totalValue");
            $CI->db->from('srp_erp_mfq_jc_labourtask');
            $CI->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_labourtask.labourTask', 'inner');
            $CI->db->where_in('jobCardID', $output2);
            $result = $CI->db->get()->row_array();
            $data["labourTask"] = $result;
        } else {
            $data["labourTask"] = 0;
        }

        if ($output2) {
            $CI->db->select("SUM(totalValue) as totalValue");
            $CI->db->from('srp_erp_mfq_jc_overhead');
            $CI->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overheadID', 'inner');
            $CI->db->where_in('jobCardID', $output2);
            $result = $CI->db->get()->row_array();
            $data["overheadCost"] = $result;
        } else {
            $data["overheadCost"] = 0;
        }

        if ($output2) {
            $CI->db->select("SUM(totalValue) as totalValue");
            $CI->db->from('srp_erp_mfq_jc_machine');
            $CI->db->join('srp_erp_mfq_fa_asset_master', 'srp_erp_mfq_jc_machine.mfq_faID = srp_erp_mfq_fa_asset_master.mfq_faID', 'inner');
            $CI->db->where_in('jobCardID', $output2);
            $result = $CI->db->get()->row_array();
            $data["machineCost"] = $result;
        } else {
            $data["machineCost"] = 0;
        }

        $data["jobcard"] = $output;

        return $data;
    }

    if (!function_exists('job_status')) {
        function job_status($status)
        {

            $status = $status ?? '';
            if ($status >= 0 && $status <= 25) {
                return '<div class="progress"><div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;color:black;font-weight:bold">' . round($status) . '%</div></div>';
            } else if ($status >= 25 && $status <= 50) {
                return '<div class="progress"><div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;font-weight:bold">' . round($status) . '%</div></div>';
            } else if ($status >= 50 && $status <= 75) {
                return '<div class="progress"><div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;font-weight:bold">' . round($status) . '%</div></div>';
            } else if ($status >= 75 && $status <= 100) {
                return '<div class="progress"><div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;font-weight:bold">' . round($status) . '%</div></div>';
            }
        }
    }

    if (!function_exists('fetch_ongoing_jobs')) {
        function fetch_ongoing_jobs()
        {
            $CI = &get_instance();
            $year = date('Y');
            $companyid = current_companyID();
            /* $sql = "SELECT jobID as id,documentCode as text,
     DATE_FORMAT(startDate,'%d-%m-%Y') as start_date,
     DATEDIFF(endDate, startDate) AS duration,
     ws.progress as progress,
     description,'#61cde2' as color
      FROM srp_erp_mfq_job LEFT JOIN (SELECT jobID,COUNT(*) as totCount,SUM(if(status = 1,1,0)) as completedCount,(SUM(if(status = 1,1,0))/COUNT(*)) * 100 as percentage,(SUM(if(status = 1,1,0))/COUNT(*)) * 1 as progress FROM srp_erp_mfq_workflowstatus GROUP BY jobID) ws ON ws.jobID = srp_erp_mfq_job.workProcessID WHERE ws.percentage < 100";*/

            $sql = "SELECT
	jobID AS id,
	documentCode AS text,
	DATE_FORMAT( startDate, '%d-%m-%Y' ) AS start_date,
	DATEDIFF( endDate, startDate ) AS duration,
	ws.progress AS progress,
	description,
	'#61cde2' AS color
FROM
	srp_erp_mfq_job
	LEFT JOIN (
SELECT
	jobID,
	COUNT( * ) AS totCount,
	SUM( IF ( STATUS = 1, 1, 0 ) ) AS completedCount,
	( SUM( IF ( STATUS = 1, 1, 0 ) ) / COUNT( * ) ) * 100 AS percentage,
	( SUM( IF ( STATUS = 1, 1, 0 ) ) / COUNT( * ) ) * 1 AS progress
FROM
	srp_erp_mfq_workflowstatus
GROUP BY
	jobID
	) ws ON ws.jobID = srp_erp_mfq_job.workProcessID
WHERE
    srp_erp_mfq_job.companyID = $companyid
	AND ws.percentage < 100
	UNION
	SELECT
	jobAutoID AS id,
	documentSystemCode AS text,
	DATE_FORMAT( documentDate, '%d-%m-%Y' ) AS start_date,
	DATEDIFF( documentDate, documentDate )+ 1 AS duration,
	completionPercenatage AS progress,
	narration,
	'#61cde2' AS color
FROM
	srp_erp_mfq_standardjob
WHERE
	companyID = $companyid
	AND completionPercenatage < 100";

            $result = $CI->db->query($sql)->result_array();
            echo json_encode($result);
        }
    }

    if (!function_exists('fetch_mfq_segment')) {
        function fetch_mfq_segment($id = TRUE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
        {
            $CI = &get_instance();
            $primarylanguage = getPrimaryLanguage();
            $CI->lang->load('common', $primarylanguage);
            $CI->db->select('segmentCode,description,mfqSegmentID');
            $CI->db->from('srp_erp_mfq_segment');
            $CI->db->where('status', 1);
            $CI->db->where('levelNo', 0);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $data = $CI->db->get()->result_array();
            if ($state == TRUE) {
                $data_arr = array('' => $CI->lang->line('common_select_segment')/*Select Segment*/);
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    if ($id) {
                        $data_arr[trim($row['mfqSegmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                    } else {
                        $data_arr[trim($row['mfqSegmentID'] ?? '') . '|' . trim($row['segmentCode'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                    }
                }
            }

            return $data_arr;
        }
    }

    if (!function_exists('fetch_bill_of_material')) {
        function fetch_bill_of_material($id = TRUE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
        {
            $CI = &get_instance();
            $primarylanguage = getPrimaryLanguage();
            $CI->lang->load('common', $primarylanguage);
            $CI->db->select('*');
            $CI->db->from('srp_erp_mfq_billofmaterial');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $data = $CI->db->get()->result_array();
            if ($state == TRUE) {
                $data_arr = array('' => 'Select bill of material'/*Select Segment*/);
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['bomMasterID'] ?? '')] = trim($row['documentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
            }

            return $data_arr;
        }
    }

    if (!function_exists('customerInquiryStatus')) {
        function customerInquiryStatus($quotationStatus, $masterID, $statusID)
        {
            $status = '<div style="text-align: center">';

            if ($quotationStatus == 0) {
                if ($statusID == 2) {
                    $status .= '<a onclick="" class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;"><span class="label">Open</span></a>';
                } else {
                    $status .= '<a onclick="decline_quotation(\'' . $masterID . '\')" class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;"><span class="label">Open</span></a>';
                }
            } else if ($quotationStatus == 1) {
                if ($statusID == 2) {
                    $status .= '<a onclick="" class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;"><span class="label">Submitted</span></a>';
                } else {
                    $status .= '<a onclick="decline_quotation(\'' . $masterID . '\')" class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;"><span class="label">Submitted</span></a>';
                }
            } else {
                $status .= '<span class="label" style="background-color:#58BBEE; color:#ffffff; font-size: 11px;">Declined</span>';
            }
            $status .= '</div>';

            return $status;
        }
    }

    if (!function_exists('get_customerinquiry_status')) {
        function get_customerinquiry_status($confirmedYN, $plannedDate, $isMailSent)
        {
            $status = '<div style="text-align: center">';

            if ($isMailSent == 1) {
                $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Submitted</span>';
            } else {
                if ($plannedDate < date('Y-m-d')) {
                    $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Overdue</span>';
                } else {
                    $status .= '<span class="label" style="background-color:#58BBEE; color:#ffffff; font-size: 11px;">Open</span>';
                }
            }
            $status .= '</div>';

            return $status;
        }
    }

    

    if (!function_exists('get_job_status')) {
        function get_job_status($confirmedYN, $deliveryNoteID = null, $invoiceAutoID = null, $expectedDeliveryDate = null)
        {
            $currentDate = date('d-m-Y');
            $status = '<div style="text-align: center">';
            if ($confirmedYN != 1) {
                $status .= '<span class="label" style="background-color:#58BBEE; color:#ffffff; font-size: 11px;">Open</span>';
            } else if ($invoiceAutoID) {
                $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Invoiced</span>';
            } else if ($deliveryNoteID) {
                $status .= '<span class="label" style="background-color:#75c1c1; color:#ffffff; font-size: 11px;">Delivered</span>';
            } else if (strtotime($expectedDeliveryDate) < strtotime($currentDate)) {
                $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Overdue</span>';
            } else {
                $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Closed</span>';
            }
            $status .= '</div>';

            return $status;
        }
    }


    if (!function_exists('get_customerinquiry_submission_status')) {
        function get_customerinquiry_submission_status($description, $statusColor, $statusBackgroundColor)
        {
            return '<span class="label" style="background-color:' . $statusBackgroundColor . '; color:' . $statusColor . '; font-size: 11px;">' . $description . '</span>';
        }
    }

    if (!function_exists('format_number')) {
        function format_number($amount = 0, $decimal_place = 2)
        {
            if (is_null($amount)) {
                $amount = 0;
            }
            if (is_null($decimal_place)) {
                $decimal_place = 2;
            }

            return number_format($amount, $decimal_place);
        }
    }


    if (!function_exists('round_percentage')) {
        function round_percentage($status)
        {
            return round($status);
        }
    }

    if (!function_exists('all_mfq_documents')) {
        function all_mfq_documents($status = true)
        {
            $CI = &get_instance();
            $CI->db->SELECT("documentID,description");
            $CI->db->FROM('srp_erp_mfq_documents');
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

    if (!function_exists('statuscolor')) {
        function statuscolor($statuscolor)
        {
            return '<span class="label" style="background-color: ' . $statuscolor . '">&nbsp;</span>';
        }
    }

    if (!function_exists('all_mfq_status')) {
        function all_mfq_status($documentID, $status = true)
        {
            $CI = &get_instance();
            $CI->db->SELECT("statusID,description");
            $CI->db->FROM('srp_erp_mfq_status');
            $CI->db->where('documentID', $documentID);
            //$CI->db->where('companyID', current_companyID());
            $data = $CI->db->get()->result_array();
            if ($status) {
                $data_arr = array('' => 'Select a Status');
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['statusID'] ?? '')] = trim($row['description'] ?? '');
                }
            }
            return $data_arr;
        }
    }


    if (!function_exists('mfq_status')) {
        function mfq_status($description, $statusColor, $statusBackgroundColor)
        {
            return '<span class="label" style="background-color:' . $statusBackgroundColor . '; color:' . $statusColor . '; font-size: 11px;">' . $description . '</span>';
        }
    }

    if (!function_exists('all_mfq_warehouse_drop')) {
        function all_mfq_warehouse_drop($status = true)
        {
            $CI = &get_instance();
            $CI->db->SELECT("mfqWarehouseAutoID,srp_erp_mfq_warehousemaster.warehouseDescription");
            $CI->db->FROM('srp_erp_mfq_warehousemaster');
            $CI->db->join('srp_erp_warehousemaster', "srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.wareHouseAutoID", "left");
            $CI->db->where('srp_erp_mfq_warehousemaster.companyID', current_companyID());
            $CI->db->where('srp_erp_mfq_warehousemaster.warehouseAutoID IS NOT NULL');
            $data = $CI->db->get()->result_array();
            if ($status) {
                $data_arr = array('' => 'Select a Warehouse');
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['mfqWarehouseAutoID'] ?? '')] = trim($row['warehouseDescription'] ?? '');
                }
            }
            return $data_arr;
        }
    }

    if (!function_exists('approval_action')) {
        function approval_action($autoID, $approvalLevelID, $approvedYN, $documentApprovedID, $documentID, $jobID = null, $finalApproval = null, $postingFinanceDate = null)
        {
            $status = '<span class="pull-right">';

            if($documentID == 'EST'){
                if ($approvedYN == 0) {
                    $status .= '<a onclick=\'fetch_approval("' . $autoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '","' . $jobID . '","' . $finalApproval . '","' . $postingFinanceDate . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
                } else {
                    $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $autoID . '\',\'' . $jobID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
                }

            }else if($documentID == 'ESTP'){

                if ($approvedYN == 0) {
                    $status .= '<a onclick=\'fetch_approval_propsal("' . $autoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '","' . $jobID . '","' . $finalApproval . '","' . $postingFinanceDate . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
                } else {
                    $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $autoID . '\',\'' . $jobID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
                }

            }else{
                if ($approvedYN == 0) {
                    $status .= '<a onclick=\'fetch_approval("' . $autoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '","' . $jobID . '","' . $finalApproval . '","' . $postingFinanceDate . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
                } else {
                    $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $autoID . '\',\'' . $jobID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
                }
            }
            
            $status .= '</span>';

            return $status;
        }
    }


    /*    if (!function_exists('customer_inquiry_approval_status')) {
            function customer_inquiry_approval_status($approved_status, $confirmed_status, $statusID, $autoID, $code)
            {
                $status = '<center>';
                if ($statusID == 3) {
                    $status .= '<span class="label" style="background-color:#ff851b; color:#ffffff; font-size: 11px;">Declined</span>';
                } else {
                    if ($approved_status == 0) {
                        if ($confirmed_status == 0 || $confirmed_status == 3) {
                            $status .= '<span class="label label-danger">Pending</span>';
                        } else if ($confirmed_status == 2) {
                            $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"> Pending ';
                            $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                        } else {
                            $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"> Pending ';
                            $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                        }
                    } elseif ($approved_status == 1) {
                        if ($confirmed_status == 1) {
                            $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"> Approved ';
                            $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                        } else {
                            $status .= '<span class="label label-success">&nbsp;</span>';
                        }
                    } elseif ($approved_status == 2) {
                        $status .= '<span class="label label-warning">&nbsp;</span>';
                    } elseif ($approved_status == 6) {
                        $fn = 'onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')"';
                        $status .= '<span class="label label-info cancel-pop-up" ' . $fn . '><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>';
                    } else {
                        $status .= '-';
                    }
                }
                $status .= '</center>';

                return $status;
            }
        }*/

    if (!function_exists('customer_inquiry_approval_status')) {
        function customer_inquiry_approval_status($statusID)
        {
            $status = '<center>';
            if ($statusID == 1) {
                $status .= '<a href="#" class="label label-danger"> Open </span>';
            } else if ($statusID == 2) {
                $status .= '<a href="#" class="label label-success"> Awarded </span>';
            } else if ($statusID == 3) {
                $status .= '<a href="#" class="label label-warning"> Lost </span>';
            }
            $status .= '</center>';

            return $status;
        }
    }

    if (!function_exists('estimate_approval_status')) {
        function estimate_approval_status($approved_status, $confirmed_status, $submissionStatus, $autoID, $code)
        {
            $status = '<center>';

            if ($approved_status == 0) {
                if ($confirmed_status == 0 && $submissionStatus == 6) {
                    $status .= '<span class="label label-warning">Revised</span>';
                } else if ($confirmed_status == 0 || $confirmed_status == 3) {
                    $status .= '<span class="label label-danger">Pending</span>';
                } else if ($confirmed_status == 2) {
                    $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-warning"> Rejected ';
                    $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                } else {
                    if ($submissionStatus == 6) {
                        $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-warning"> Revised ';
                        $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                    } else {
                        $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"> Pending ';
                        $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                    }
                }
            } elseif ($approved_status == 1) {
                if ($confirmed_status == 1) {
                    if ($submissionStatus == 6) {
                        $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-warning"> Revised ';
                        $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                    } else {
                        $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"> Approved ';
                        $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
                    }
                } else {
                    $status .= '<span class="label label-success">&nbsp;</span>';
                }
            } elseif ($approved_status == 2) {
                $status .= '<span class="label label-warning">&nbsp;</span>';
            } elseif ($approved_status == 6) {
                $fn = 'onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')"';
                $status .= '<span class="label label-info cancel-pop-up" ' . $fn . '><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>';
            } else {
                $status .= '-';
            }

            $status .= '</center>';

            return $status;
        }
    }

    if (!function_exists('approval_status')) {
        function approval_status($approvedYN, $confirmedYN = null)
        {
            $status = '<div style="text-align: center">';

            if ($approvedYN == 1) {
                $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Approved</span>';
            } else {
                $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Not Approved</span>';
            }
            $status .= '</div>';

            return $status;
        }
    }

    if (!function_exists('confirmation_status')) {
        function confirmation_status($confirmedYN)
        {
            $status = '<div style="text-align: center">';

            if ($confirmedYN == 1) {
                $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Confirmed</span>';
            } else {
                $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Not Confirmed</span>';
            }
            $status .= '</div>';

            return $status;
        }
    }

    if (!function_exists('get_all_mfq_template')) {
        function get_all_mfq_template()
        {
            $companyID = current_companyID();
            $CI = &get_instance();
            $CI->db->select("*");
            $CI->db->from('srp_erp_mfq_templatemaster');
            $CI->db->where('companyID', $companyID);
            $template = $CI->db->get()->result_array();
            return $template;
        }
    }

    if (!function_exists('all_mfq_month_drop')) {
        function all_mfq_month_drop($status = true)
        {
            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->FROM('srp_months');
            $data = $CI->db->get()->result_array();
            if ($status) {
                $data_arr = array('' => 'Select a Month');
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    if ($row['MonthId'] > 1) {
                        $data_arr[trim($row['MonthId'] ?? '')] = trim($row['MonthId'] ?? '') . " Months";
                    } else {
                        $data_arr[trim($row['MonthId'] ?? '')] = trim($row['MonthId'] ?? '') . " Month";
                    }
                }
            }
            return $data_arr;
        }
    }

    if (!function_exists('all_mfq_jobs_drop')) {
        function all_mfq_jobs_drop($status = TRUE)/*Load all Jobs*/
        {
            $CI = &get_instance();
            $primaryLanguage = getPrimaryLanguage();
            $CI->lang->load('procurement_approval', $primaryLanguage);
            $CI->db->select("workProcessID,documentCode");
            $CI->db->from('srp_erp_mfq_job job');
            $CI->db->join('srp_erp_mfq_estimatedetail estd', "estd.estimateDetailID = job.estimateDetailID","left");
            $CI->db->join('srp_erp_mfq_estimatemaster estm', "estd.estimateMasterID = estm.estimateMasterID","left");
            $CI->db->where('job.companyID', current_companyID());
            //            $CI->db->where('estm.orderStatus', 1);
            $CI->db->where('job.approvedYN != 1');
            $jobs = $CI->db->get()->result_array();
            if ($status) {
                $jobs_arr = array('' => $CI->lang->line('procurement_select_job')/* Select Job */);
            } else {
                $jobs_arr = [];
            }
            if (isset($jobs)) {
                foreach ($jobs as $row) {
                    $jobs_arr[trim($row['workProcessID'] ?? '')] = (trim($row['documentCode'] ?? ''));
                }
            }

            return $jobs_arr;
        }
    }

    if (!function_exists('all_contract_drop')) {
        function all_contract_drop($status = TRUE)/*Load all_contract_drop*/
        {
            $CI = &get_instance();
            $primaryLanguage = getPrimaryLanguage();
            $CI->db->select("contractAutoID,documentID,contractCode");
            $CI->db->from('srp_erp_contractmaster job');
           
            $CI->db->where('job.companyID', current_companyID());
            //            $CI->db->where('estm.orderStatus', 1);
            $CI->db->where('job.approvedYN',1);
            $jobs = $CI->db->get()->result_array();
            if ($status) {
                $jobs_arr = array('' => "Select Contract"/* Select Job */);
            } else {
                $jobs_arr = [];
            }
            if (isset($jobs)) {
                foreach ($jobs as $row) {
                    $jobs_arr[trim($row['contractAutoID'] ?? '')] = (trim($row['contractCode'] ?? '')). ' | ' . trim($row['documentID'] ?? '');
                }
            }

            return $jobs_arr;
        }
    }

    if (!function_exists('load_delivery_note_action')) {
        function load_delivery_note_action($poID, $jobID, $confirmedYN, $approved, $createdUserID)
        {
            $CI = &get_instance();
            $CI->load->library('session');
            $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                            Actions <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';
    
            $status .= '<li><a onclick=\'attachment_modal_DN(' . $poID . ',"Delivery Note","DN",' . $confirmedYN . ');\'><span style="color:#4caf50;" class="glyphicon glyphicon-paperclip"></span> Attachment</a></li>';
            $status .= '<li><a onclick=\'modal_JOB(' . $jobID . ');\'><span class="glyphicon glyphicon-exclamation-sign"></span> Job Attachments</a></li>';
    
            if ($confirmedYN != 1) {
                $status .= '<li><a onclick=\'fetchPage("system/mfq/mfq_delivery_note_create",' . $poID . ',"Edit Delivery Note","MFQ"); \'><span title="Edit" style="color:#116f5e;" class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
            }
    
            if ($createdUserID == trim($CI->session->userdata("empID")) && $approved == 0 && $confirmedYN == 1) {
                // Uncomment if needed: $status .= '<li><a onclick="referBack_delivery_note(' . $poID . ');"><span title="Refer Back" style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-repeat"></span> Refer Back</a></li>';
            }
    
            $status .= '<li><a target="_blank" onclick="view_delivery_note(\'' . $poID . '\')"><span title="View" style="color:#03a9f4;" class="glyphicon glyphicon-eye-open"></span> View</a></li>';
            $status .= '<li><a target="_blank" href="' . site_url('MFQ_DeliveryNote/load_deliveryNote_confirmation/') . $poID . '"><span title="Print" style="color:#607d8b;" class="glyphicon glyphicon-print"></span> Print</a></li>';
    
            if ($confirmedYN != 1) {
                $status .= '<li><a onclick="delete_delivery_note(' . $poID . ',\'Invoices\');"><span title="Delete" style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span> Delete</a></li>';
            }
    
            if ($confirmedYN == 1) {
                $status .= '<li><a onclick="referBackDeliveryNote(' . $poID . ');"><span title="Refer Back" style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-repeat"></span> Refer Back</a></li>';
                $status .= '<li><a onclick="trace_mfq_document(' . $poID . ',\'MDN\')" title="Trace Document"><i class="fa fa-search" style="color:#fdc45e;" aria-hidden="true"></i> Trace Document</a></li>';
            }
    
            $status .= '</ul></div>';
            return $status;
        }
    }    

    if (!function_exists('fetch_all_mfq_gl_codes')) {
        function fetch_all_mfq_gl_codes($code = NULL)
        {
            $CI = &get_instance();
            $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory,accountCategoryTypeID");
            $CI->db->from('srp_erp_chartofaccounts');
            if ($code) {
                $CI->db->where('subCategory', $code);
            }
            $CI->db->where('controllAccountYN', 0);
            $CI->db->WHERE('masterAccountYN', 0);
            // $CI->db->WHERE('accountCategoryTypeID !=', 4);
            //$CI->db->where('approvedYN', 1);
            $CI->db->where('isActive', 1);
            $CI->db->where('isBank', 0);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $CI->db->limit(425);
            $data = $CI->db->get()->result_array();
            $data_arr = array('' => 'Select GL Code');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['GLSecondaryCode'] ?? '') . ' | ' . htmlspecialchars(trim($row['GLDescription'] ?? ''), ENT_QUOTES) . ' | ' . trim($row['subCategory'] ?? '');
                }
            }

            return $data_arr;
        }
    }

    if (!function_exists('editCustomerInvoice')) {
        function editCustomerInvoice($invoiceAutoID, $confirmedYN, $approvedYN)
        {
            $status = '<span class="pull-right">';
            $status .= '<a target="_blank" onclick="viewDocument(\'' . $invoiceAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
            if ($confirmedYN != 1) {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<span class=""><a href="#" onclick="fetchPage(\'system/mfq/mfq_add_customer_invoice\',' . $invoiceAutoID . ',\'Edit Customer Invoice\',\'MCINV\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>';
            }
            if ($confirmedYN == 1) {
                $status .= '&nbsp; | &nbsp;<a onclick="referBackCustomerInovice(' . $invoiceAutoID . ');"><span title="" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);" data-original-title="Refer Back"></span></a>';
                $status .= '&nbsp; | &nbsp;<a onclick="trace_mfq_document(' . $invoiceAutoID . ',\'MCINV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a> ';
            }
            $status .= '&nbsp;| &nbsp;&nbsp;<a onclick=\'attachment_modal_MCINV(' . $invoiceAutoID . ',"INVOICE","MCINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>';
            $status .= '&nbsp;| &nbsp;&nbsp;<a onclick=\'attachment_pull_modal(' . $invoiceAutoID . ')\'><span title="Pull Attachment" rel="tooltip" class="fa fa-arrows"></span></a>';
            $status .= '</span>';
            return $status;
        }
    }

    if (!function_exists('usergroupstatus')) {
        function usergroupstatus($isActive)
        {
            return ($isActive == 1) ? '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Active</span>' : '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">In Active</span>';
        }
    }

    if (!function_exists('all_mfq_usergroup_drop')) {
        function all_mfq_usergroup_drop($status = true)
        {
            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->FROM('srp_erp_mfq_usergroups');
            $CI->db->where('companyID', current_companyID());
            $CI->db->where('isActive', 1);
            $data = $CI->db->get()->result_array();
            if ($status) {
                $data_arr = array('' => 'Select a Usergroup');
            } else {
                $data_arr = [];
            }
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['userGroupID'] ?? '')] = trim($row['description'] ?? '');
                }
            }
            return $data_arr;
        }
    }
    if (!function_exists('isdefaultstatus')) {
        function isdefaultstatus($isDefault)
        {
            return ($isDefault == 1) ? '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Default</span>' : '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Is Default</span>';
        }
    }

    if (!function_exists('getStandardDetail')) {
        function getStandardDetail()
        {
            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->FROM('srp_erp_mfq_standarddetailsmaster');
            $CI->db->where('companyID', current_companyID());
            $data = $CI->db->get()->result_array();
            return $data;
        }
    }

    if (!function_exists('generateSubJobCode')) {
        function generateSubJobCode()
        {
            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->FROM('srp_erp_mfq_standarddetailsmaster');
            $CI->db->where('companyID', current_companyID());
            $data = $CI->db->get()->result_array();
            return $data;
        }
    }

    if (!function_exists('get_specific_mfq_item')) {
        function get_specific_mfq_item($itemID)
        {
            $CI = &get_instance();
            $CI->db->SELECT("*");
            $CI->db->FROM('srp_erp_mfq_itemmaster');
            $CI->db->where('mfqItemID', $itemID);
            $data = $CI->db->get()->row_array();
            return $data;
        }
    }

    if (!function_exists('fetch_materialCertificate')) {
        function fetch_materialCertificate()
        {
            $CI = &get_instance();
            $CI->db->select("*");
            $CI->db->from('srp_erp_mfq_materialcertificatemaster');
            $CI->db->where('companyID', current_companyID());
            $certificate = $CI->db->get()->result_array();
            $certificateArr = [];
            if (isset($certificate)) {
                foreach ($certificate as $row) {
                    $certificateArr[trim($row['materialCertificateID'] ?? '')] = (trim($row['Description'] ?? ''));
                }
            }
            return $certificateArr;
        }
    }
}
if (!function_exists('editJobstandard')) {
    function editJobstandard($jobAutoID, $confirmedYN, $approvedYN)
    {
        $status = '<span class="pull-right">';
        /* $status .= '<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_add_standard_job_card.php\',' . $jobAutoID . ',\'Standard Job Card\',\'STJOB\')"><span title="Standard Job Card" rel="tooltip" class="fa fa-briefcase"></span></a></span>';*/

        if ($confirmedYN == 0 || $confirmedYN == 3 || $confirmedYN == 2) {
            $status .= '<a href="#" onclick="fetchPage(\'system/mfq/mfq_add_standard_job_card.php\',' . $jobAutoID . ',\'Standard Job Card\',\'STJOB\')"><span title="Standard Job Card" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        } else if (($confirmedYN == 1 && $approvedYN == 0)) {
            $status .= '<a onclick="referback_standardjobcard(' . $jobAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'STJOB\',\'' . $jobAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('MFQ_Job_standard/load_standardjobcard_print/') . $jobAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'STJOB\',\'' . $jobAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('MFQ_Job_standard/load_standardjobcard_print/') . $jobAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        }
        $status .= '</span>';
        return $status;
    }
}
if (!function_exists('approval_action_sj')) {
    function approval_action_sj($jobAutoID, $approvalLevelID, $approvedYN, $documentApprovedID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $jobAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'STJOB\',\'' . $jobAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('all_mfq_warehouse_drop_finih_goods')) {
    function all_mfq_warehouse_drop_finih_goods($type = null)
    {
        if ($type == 2) {
            $CI = &get_instance();
            $CI->db->SELECT("srp_erp_warehousemaster.wareHouseAutoID,`srp_erp_warehousemaster`.`wareHouseDescription`");
            $CI->db->FROM('srp_erp_warehousemaster');
            $CI->db->where('srp_erp_warehousemaster.companyID', current_companyID());
            $CI->db->where('warehouseType', 1);
            $data = $CI->db->get()->result_array();
            $data_arr = array('' => 'Select Warehouse');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseDescription'] ?? '');
                }
            }
        } else {
            $CI = &get_instance();
            $CI->db->SELECT("srp_erp_warehousemaster.wareHouseAutoID,`srp_erp_warehousemaster`.`wareHouseDescription`");
            $CI->db->FROM('srp_erp_warehousemaster');
            $CI->db->where('srp_erp_warehousemaster.companyID', current_companyID());
            $data = $CI->db->get()->result_array();
            $data_arr = array('' => 'Select Warehouse');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseDescription'] ?? '');
                }
            }
        }
        return $data_arr;
    }
}
if (!function_exists('confirmation_status_approved')) {
    function confirmation_status_approved($confirmedYN, $approvedYN)
    {
        $status = '<div style="text-align: center">';

        if ($confirmedYN == 1 && $approvedYN != 1) {
            $status .= '<center><span class="label" style="background-color:#ff661d; color:#ffffff; font-size: 11px;">Confirmed</span></center>';
        } else if ($confirmedYN == 1 && $approvedYN == 1) {
            //$status .= '<center><span class="label" style="background-color:rgba(255, 72, 49, 0.96); color:#ffffff; font-size: 11px;">Approved</span></center>';
            $status .= '<center><span class="label" style="background-color:#8bc34a; font-size: 11px;">Approved</span></center>';
        } else if (($confirmedYN == 3) || ($confirmedYN == 2)) {
            $status .= '<center><span class="label" style="background-color:#ff784f; font-size: 11px;">Referred Back</span></center>';
        } else {
            $status .= '<center><span class="label" style="background-color:rgba(255, 72, 49, 0.96); color:#ffffff; font-size: 11px;">Not Confirmed</span></center>';
        }
        $status .= '</div>';

        return $status;
    }

    if (!function_exists('load_employee_drop_mfq')) {
        function load_employee_drop_mfq($type = null)
        {
            $CI = &get_instance();
            $CI->db->SELECT("EIdNo,Ename2,EmpSecondaryCode");
            $CI->db->FROM('srp_employeesdetails');
            $CI->db->WHERE('Erp_companyID', current_companyID());
            $CI->db->WHERE('empConfirmedYN', 1);
            $CI->db->WHERE('isDischarged', 0);
            $data = $CI->db->get()->result_array();
            if ($type == 1) {
                $data_arr = array('' => 'Select Proposal Engineer');
            } else if ($type == 3) {
                $data_arr = [];
            } else {
                $data_arr = array('' => 'Select Responsible Person');
            }


            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '') . ' - ' . trim($row['EmpSecondaryCode'] ?? '');
                }
            }

            return $data_arr;
        }
    }

    if (!function_exists('load_crm_source_mfq')) {
        function load_crm_source_mfq($type)
        {
            $CI = &get_instance();
            $CI->db->SELECT("sourceID,description");
            $CI->db->FROM('srp_erp_mfq_customer_inquiry_source');
            $CI->db->WHERE('companyID', current_companyID());
            $data = $CI->db->get()->result_array();

            if ($type == true) {
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

    if (!function_exists('load_cus_inquiry_order_status')) {
        function load_cus_inquiry_order_status($type)
        {
            if($type==true){
                $data=array('' => 'Select Order Status', '1' => 'Open', '2' => 'In Progress', '3' => 'Lost', '4' => 'Awarded');
            }else{
                $data=array('1' => 'Open', '2' => 'In Progress', '3' => 'Lost', '4' => 'Awarded');
            }
            

            return $data;
        }
    }

    if (!function_exists('load_cus_inquiry_document_status')) {
        function load_cus_inquiry_document_status($type)
        {
            if($type==true){
                $flowserve = getPolicyValues('MANFL', 'All');
                if($flowserve == 'GCC'){
                    $data=array('' => 'Select Inquiry Status', '1' => 'Open', '2' => 'In Progress', '3' => 'Completed');
                }else{
                    $data=array('' => 'Select Document Status', '1' => 'Open', '2' => 'In Progress', '3' => 'Completed');
                }  
            }else{
                $data=array('1' => 'Open', '2' => 'In Progress', '3' => 'Completed');
            }
            

            return $data;
        }
    }

    if (!function_exists('load_cus_inquiry_rfq_status')) {
        function load_cus_inquiry_rfq_status($type)
        {
            $isGCC=getPolicyValues('MANFL','All');
           
            if($isGCC=='GCC'){
                if($type==true){
                    $data=array('' => 'Select RFQ Status', '1' => 'Tentative', '2' => 'Firm', '3' => 'Budget', '4' => 'Tender');
                }else{
                    $data=array('1' => 'Tentative', '2' => 'Firm', '3' => 'Budget');
                }
            }
            else{
                if($type==true){
                    $data=array('' => 'Select RFQ Status', '1' => 'Tentative', '2' => 'Firm', '3' => 'Budget');
                }else{
                    $data=array('1' => 'Tentative', '2' => 'Firm', '3' => 'Budget');
                }
            }
            

            return $data;
        }
    }

    if (!function_exists('load_cus_inquiry_category')) {
        function load_cus_inquiry_category($type)
        {
            $isGCC=getPolicyValues('MANFL','All');
           
            if($isGCC=='GCC'){
                $data=array('' => 'Select Category', '1' => 'Manufacturing', '2' => 'Toll Manufacturing', '3' => 'Trading', '4' => '3rd party');
            }
            else{
                $data=array('' => 'Select Category', '1' => 'ss Tank');
            }
           

            return $data;
        }
    }

    if (!function_exists('load_cus_inquiry_submission_status')) {
        function load_cus_inquiry_submission_status($type)
        {
            
            $data=array('' => 'Select Submission Status', '1' => 'REF');

            return $data;
        }
    }

    if (!function_exists('load_srm_country_mfq')) {
        function load_srm_country_mfq($type)
        {
            $CI = &get_instance();
            $CI->db->SELECT("countryID,CountryDes,countryShortCode");
            $CI->db->FROM('srp_countrymaster');
            $CI->db->WHERE('Erp_companyID', current_companyID());
            $data = $CI->db->get()->result_array();


            if ($type == true) {
                $data_arr = array('' => 'Select Operation');
            } else {
                $data_arr = [];
            }

            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['countryID'] ?? '')] = trim($row['countryShortCode'] ?? '').' - '.trim($row['CountryDes'] ?? '');
                }
            }

            return $data_arr;
        }
    }

    if (!function_exists('actualsubmissiondate')) {
        function actualsubmissiondate($ciMasterID, $deliveryDate, $estimateconf)
        {
            $CI = &get_instance();
            $companyid = current_companyID();
            $status = '<span class="">';
            if ($estimateconf == 0) {
                $status .= '<a href="#" data-type="combodate" data-placement="bottom" id="documentdate" data-url="' . site_url('MFQ_CustomerInquiry/actualsubmissiondate/') . '" data-pk="' . $ciMasterID . '"
                                                   data-name="documentDate" data-title="Document Date"
                                                   class="xEditableDate"
                                                   data-value="' . $deliveryDate . '"
                                                   data-related="_documetdate">
                                                  ' . $deliveryDate . '

                                                </a>';
            } else {
                $status .= '' . $deliveryDate . '';
            }


            $status .= '</span>';

            return $status;
        }
    }
}
if (!function_exists('link_job_card_drop_mfq')) {
    function link_job_card_drop_mfq($templateMasterID = null, $templateDetailID = null, $status = TRUE, $defaulttype = null)/*Load all Bom*/
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        /* $CI->db->select("*");
         $CI->db->from('srp_erp_mfq_templatedetail');
         $CI->db->where('workFlowID', 1);
         $CI->db->where('templateMasterID', $templateMasterID);
         $CI->db->where('templateDetailID <', $templateDetailID);
         $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
         $job = $CI->db->get()->result_array();*/
        $orderby = '';
        if ($defaulttype == 1) {
            $orderby .= 'ORDER BY templateDetailID DESC LIMIT 1';
        }

        $job = $CI->db->query("SELECT
	* 
FROM
	`srp_erp_mfq_templatedetail` 
WHERE
	`workFlowID` = 1 
	AND `templateMasterID` = $templateMasterID 
	AND `templateDetailID` < $templateDetailID 
	AND `companyID` = $companyID
	$orderby
	")->result_array();


        if ($defaulttype != 1) {

            if ($status) {
                $job_arr = array('' => 'Select Job card');
            } else {
                $job_arr = [];
            }
        }

        if (isset($job)) {
            foreach ($job as $row) {
                $job_arr[trim($row['templateDetailID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $job_arr;
    }
}
if (!function_exists('send_Email_mfq')) {
    function send_Email_mfq($mailData, $attachment = 0, $last_id = 0)
    {
        $CI = &get_instance();

        $CI->load->library('email_manual');

        $toEmail = $mailData['toEmail'];
        $subject = $mailData['subject'];
        $param = $mailData['param'];

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
            if (hstGeras == 1) {
                $CI->email->from($CI->config->item('email_smtp_from'), $mailData['from']);
            } else {
                $CI->email->from($CI->config->item('email_smtp_from'), $mailData['from']);
            }
        } else {
            if (hstGeras == 1) {
                $CI->email->from($CI->config->item('email_smtp_from'), EMAIL_SYS_NAME);
            } else {
                $CI->email->from($CI->config->item('email_smtp_from'), EMAIL_SYS_NAME);
            }
        }

        if (!empty($param)) {
            $CI->email->to($toEmail);
            $CI->email->subject($subject);
            $CI->email->message($CI->load->view('system/email_template/email_approval_template_log_manufacturing', $param, TRUE));
            if ($attachment == 1) {
                $CI->db->select("*");
                $CI->db->from('srp_erp_documentattachments');
                $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
                $CI->db->where('documentID', 'ESTMAIL');
                $CI->db->where('documentSystemCode', $last_id);
                $pathlink = $CI->db->get()->result_array();

                if (!empty($pathlink)) {
                    foreach ($pathlink as $val) {
                        $pathmanualattach = UPLOAD_PATH . base_url() . '/' . $val['myFileName'];
                        $CI->email->attach($pathmanualattach);
                    }
                }
            }
        }
        $CI->email->send();
        $CI->email->clear(TRUE);
    }
}

if (!function_exists('all_machine_gl_drop')) {
    function all_machine_gl_drop()
    {
        $where = "(subCategory = 'PLE' OR subCategory = 'PLI')";
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE($where);
        //$CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Cost GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('generate_job_SystemCode')) {
    function generate_job_SystemCode($segmentID, $segmentCode, $tableName)
    {
        $CI = &get_instance();
        $CI->db->select_max('serialNo');
        $CI->db->from($tableName);
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('mfqSegmentID', $segmentID);
        $result = $CI->db->get()->row_array();

        if (!empty($result)) {
            $serialNo = $result['serialNo'] + 1;
        } else {
            $serialNo = 1;
        }
        $systemCode = current_companyCode() . '/' . $segmentCode . '/' . 'JOB' . '/' . date('Y') . '/' . date('m') . '/' . str_pad($serialNo, 6, '0', STR_PAD_LEFT);

        $output['serialNo'] = $serialNo;
        $output['systemCode'] = $systemCode;

        return $output;
    }
}

if (!function_exists('load_main_job_status')) {
    function load_main_job_status($jobStatus)
    {
        $status = '';
        if ($jobStatus == 3) {
            $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Invoiced</span>';
        } else if ($jobStatus == 2) {
            $status .= '<span class="label" style="background-color:#75c1c1; color:#ffffff; font-size: 11px;">Delivered</span>';
        } else if ($jobStatus == 1) {
            $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Pending</span>';
        } else {
            $status = '';
        }
        return $status;
    }
}

if (!function_exists('status_yes_no')) {
    function status_yes_no($value)
    {
        $status = '<span class="text-center">';
        if ($value == 1) {
            $status .= '<span class="label label-success">Yes</span>';
        } else {
            $status .= '<span class="label label-danger">No</span>';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('fetch_mfq_documentSetup')) {
    function fetch_mfq_documentSetup($state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $CI->db->select('docSetupID,description');
        $CI->db->from('srp_erp_mfq_documentsetup');
        //        $CI->db->where('isMandatory', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => 'Select Document Type');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['docSetupID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('all_mfq_revenue_gl_drop')) {
    function all_mfq_revenue_gl_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('subCategory', "PLI");
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('all_mfq_erp_item_drop')) {
    function all_mfq_erp_item_drop()
    {
        $CI = &get_instance();
        $itemConfigPolicy = getPolicyValues('MIC', 'All');
        $CI->db->select('itemSystemCode,itemName,itemAutoID,seconeryItemCode');
        $CI->db->from('srp_erp_itemmaster');
        $CI->db->where('isActive', 1);
        if (!empty($itemConfigPolicy) && $itemConfigPolicy == 1) {
            $CI->db->where('isMfqItem', 1);
        }
        $CI->db->where('isActive', 1);
        $CI->db->where('masterApprovedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Items');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['seconeryItemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_bill_of_material_process_based_drop')) {
    function all_bill_of_material_process_based_drop($workFlowTemplateID = null, $mfqItemID = null, $status = TRUE)/*Load all Bom*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('mfq', $primaryLanguage);
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_billofmaterial');
        if (!empty($workFlowTemplateID)) {
            $CI->db->where('mfqProcessID', $workFlowTemplateID);
        } else {
            $CI->db->where('mfqItemID', $mfqItemID);
        }
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bom = $CI->db->get()->result_array();
        if ($status) {
            $bom_arr = array('' => $CI->lang->line('manufacturing_select_bom'));
        } else {
            $bom_arr = [];
        }
        if (isset($bom)) {
            foreach ($bom as $row) {
                $bom_arr[trim($row['bomMasterID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $bom_arr;
    }
}

if (!function_exists('editJob_process_based')) {
    function editJob_process_based($workProcessID, $confirmedYN, $approvedYN, $isFromEstimate, $estimateMasterID = null, $linkedJobCard = null, $isDeleted = 1, $documentCode = "")
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $usageQtyUpdatePolicy = getPolicyValues('JUQ', 'All');
        $deleted = $CI->db->query("SELECT workProcessID FROM srp_erp_mfq_job WHERE linkedJobID = {$workProcessID} AND companyID = {$companyID}")->result_array();
        $documentID = "MFQ";
        if ($isFromEstimate == 1) {
            $documentID = "EST";
        }
        $status = '<div style="text-align: center">';

        $status .= '<a onclick="pulled_documents_process_based(' . $workProcessID . ',\'' . $documentCode . '\')"><span title="Pulled Documents" rel="tooltip"><i class="fa fa-tasks"></i></span></a>';
        $status .= '&nbsp;|&nbsp;<a onclick="job_attachments(' . $workProcessID . ', \'JOB\', \'MFQ_JOB\', \'' . $confirmedYN . '\')"><span title="Attachments" rel="tooltip"><i class="glyphicon glyphicon-paperclip"></i></span></a>';
        if ($confirmedYN == 1) {
            if ($approvedYN == 1) {
                $status .= '<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_job_create_process_based\',' . $workProcessID . ',\'Edit Job\',\'' . $documentID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp; | &nbsp;<a href="#" onclick="getWorkFlowStatus(' . $workProcessID . ')"><span title="Route Card" rel="tooltip" class="fa fa-cogs"></span></a></span>';
            } else {
                $status .= '&nbsp;|&nbsp; <a onclick="referbackJob(' . $workProcessID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp; | &nbsp;<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_job_create_process_based\',' . $workProcessID . ',\'Edit Job\',\'' . $documentID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp; | &nbsp;<a href="#" onclick="getWorkFlowStatus(' . $workProcessID . ')"><span title="Route Card" rel="tooltip" class="fa fa-cogs"></span></a></span>';
            }
        } else {
            $status .= '<span class="pull-right"><a href="#" onclick="fetchPage(\'system/mfq/mfq_job_create_process_based\',' . $workProcessID . ',\'Edit Job\',\'' . $documentID . '\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp; | &nbsp;<a href="#" onclick="getWorkFlowStatus(' . $workProcessID . ')"><span title="Route Card" rel="tooltip" class="fa fa-cogs"></span></a> &nbsp; | &nbsp;';
            if ($usageQtyUpdatePolicy == 1) {
                $status .= '<a href="#" onclick="updateUsageQty_process_based(' . $workProcessID . ')"><span title="Usage Qty" rel="tooltip" class="fa fa-arrow-up"></span></a></span>&nbsp; | &nbsp;';
            }
            $status .= '<a onclick="delete_sub_job(' . $workProcessID . ');"><i class="fa fa-trash delete-icon"></i></span></a>';
        }

        // if (empty($deleted) && is_null($linkedJobCard) && $isDeleted != 1 && !is_null($estimateMasterID)) {
        //     $status .= '&nbsp; | &nbsp;<a onclick="delete_job(' . $workProcessID . ');"><i class="fa fa-trash delete-icon"></i></span></a>';
        // }
        $status .= '</div>';
        return $status;
    }
}

if (!function_exists('all_mfq_template_drop')) {
    function all_mfq_template_drop()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT templateMasterID,templateDescription FROM `srp_erp_mfq_templatemaster` where companyID =$companyID")->result_array();
        $data_arr = array('' => 'Select Template');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['templateMasterID'] ?? '')] = trim($row['templateDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('delivery_note_job_codes')) {
    function delivery_note_job_codes($deliverNoteID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("select srp_erp_mfq_job.* from srp_erp_mfq_job
join srp_erp_mfq_deliverynotedetail on srp_erp_mfq_deliverynotedetail.jobID=srp_erp_mfq_job.workProcessID
join srp_erp_mfq_deliverynote on srp_erp_mfq_deliverynote.deliverNoteID=srp_erp_mfq_deliverynotedetail.deliveryNoteID
where srp_erp_mfq_deliverynote.deliverNoteID=$deliverNoteID")->result_array();
        $docCodeList = array();
        foreach ($data as $item) {
            array_push($docCodeList, $item['documentCode']);
        }
        $docCodeListStr = implode(',', $docCodeList);
        $element = '<div>' . $docCodeListStr . '</div>';
        return $element;
    }
}

if (!function_exists('customer_invoice_job_codes')) {
    function customer_invoice_job_codes($invoiceAutoID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("select srp_erp_mfq_job.* from srp_erp_mfq_job
join srp_erp_mfq_deliverynotedetail on srp_erp_mfq_deliverynotedetail.jobID=srp_erp_mfq_job.workProcessID
join srp_erp_mfq_customerinvoicedetails on srp_erp_mfq_customerinvoicedetails.deliveryNoteDetID=srp_erp_mfq_deliverynotedetail.deliveryNoteDetailID
join srp_erp_mfq_customerinvoicemaster on srp_erp_mfq_customerinvoicemaster.invoiceAutoID=srp_erp_mfq_customerinvoicedetails.invoiceAutoID
where srp_erp_mfq_customerinvoicemaster.invoiceAutoID=$invoiceAutoID")->result_array();
        $docCodeList = array();
        foreach ($data as $item) {
            array_push($docCodeList, $item['documentCode']);
        }
        $docCodeListStr = implode(',', $docCodeList);
        $element = '<div>' . $docCodeListStr . '</div>';
        return $element;
    }
}


if (!function_exists('get_mfq_job')) {
    function get_mfq_job()
    {
        $CI =& get_instance();
        $q = "SELECT
                workProcessID,
                documentCode
            FROM
                srp_erp_mfq_job                 
            WHERE
                srp_erp_mfq_job.companyID = '" . current_companyID() . "' 
                and srp_erp_mfq_job.approvedYN = 1 
            ";
        $result = $CI->db->query($q)->result_array();
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['workProcessID'] ?? '')] = $row['documentCode'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_mfq_stage')) {
    function get_mfq_stage($job_id,$templateDetailID)
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_mfq_job_wise_stage.stage_id,stage_name,stage_progress,stage_remarks,assigneeID,estimated_date,actual_date,approved,srp_erp_mfq_job_wise_stage.weightage");
        $CI->db->from('srp_erp_mfq_job_wise_stage');
        $CI->db->join('srp_erp_mfq_stage', "srp_erp_mfq_job_wise_stage.stage_id = srp_erp_mfq_stage.stage_id", "inner");
        $CI->db->where('job_id', $job_id);
        $CI->db->where('templateDetailID', $templateDetailID);
        $CI->db->where('company_id', $CI->common_data['company_data']['company_id']);
        $job = $CI->db->get()->result_array();
        return $job;
    }
}

if (!function_exists('deleteStage')) {
    function deleteStage($stage_id)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $status = '<div style="text-align: center">';


        $status .= '<a onclick="addweihtage(' . $stage_id . ')" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-file"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_stage(' . $stage_id . ')" title="View" rel="tooltip"><span class="fa fa-trash"></span></a>';


        return $status;
    }
}


if (!function_exists('reserved_quantity_from_item_batch')) {
    function reserved_quantity_from_item_batch($mfqItemID,$estimated_qty,$batch_selected,$last_id)
    {
        $CI = &get_instance();

        $CI->db->select("itemAutoID");
        $CI->db->where('mfqItemID', $mfqItemID);
        $CI->db->from('srp_erp_mfq_itemmaster');
        $itemAutoID =  $CI->db->get()->row('itemAutoID');

       
        if($itemAutoID){

            $res = release_reserved_quantity($mfqItemID,$estimated_qty,$batch_selected,$last_id);

            $batch_arr = explode(',',$batch_selected ?? '');
            $selected_batch = array();
            $total_batch_qty = 0;
            $total_estimated_qty = $estimated_qty;

            foreach($batch_arr as $batch){

                $CI->db->select("*");
                $CI->db->where('batchNumber', $batch);
                $CI->db->where('itemMasterID', $itemAutoID);
                $CI->db->from('srp_erp_inventory_itembatch');
                $batch_current_detail =  $CI->db->get()->row_array();

                if($batch_current_detail){

                    $current_qty = $batch_current_detail['qtr'];
                    $total_batch_qty += $batch_current_detail['qtr'];

                    if($current_qty >= $estimated_qty){
                        $selected_batch[] = array('batchNumber'=>$batch,'reserved_qty'=> $estimated_qty);
                        break;
                    }else{
                        $selected_batch[] = array('batchNumber'=>$batch,'reserved_qty'=>$current_qty);
                    }

                    $estimated_qty = $estimated_qty - $current_qty;
        
                }

            }

           
            foreach($selected_batch as $batch){
                $data_item = array();

                $data_item['document_type'] = 'Job';
                $data_item['batchNumber'] = $batch['batchNumber'];
                $data_item['reserved_qty'] = $batch['reserved_qty'];
                $data_item['itemMasterID'] = $itemAutoID;
                $data_item['job_id'] = $last_id;
                $data_item['mfqItemID'] = $mfqItemID;

               
                $res = $CI->db->insert('srp_erp_mfq_inventory_batch_reserved',$data_item);

                $res = release_item_from_existing_batch($itemAutoID,$batch['batchNumber'],$batch['reserved_qty']);

            }



        }

        return TRUE;
       
    }
}


if (!function_exists('release_reserved_quantity')) {
    function release_reserved_quantity($mfqItemID,$estimated_qty,$batch_selected,$last_id)
    {
        $CI = &get_instance();
        $data = array();

        $CI->db->select("*");
        $CI->db->where('mfqItemID', $mfqItemID);
        $CI->db->where('job_id', $last_id);
        $CI->db->from('srp_erp_mfq_inventory_batch_reserved');
        $reserved_details_arr =  $CI->db->get()->result_array();


        foreach($reserved_details_arr as $reserved_details){
            if($reserved_details){

                $batchNumber = $reserved_details['batchNumber'];
                $itemAutoID = $reserved_details['itemMasterID'];
                $reserved_qty = $reserved_details['reserved_qty'];

                //get batch current details
                $CI->db->select("*");
                $CI->db->where('batchNumber', $batchNumber);
                $CI->db->where('itemMasterID', $itemAutoID);
                $CI->db->from('srp_erp_inventory_itembatch');
                $batch_current_detail =  $CI->db->get()->row_array();

                if($batch_current_detail){

                    $batch_current_stock = $batch_current_detail['qtr'];
                    $adjusted_stock = $batch_current_stock + $reserved_qty;
                    

                    $data['qtr'] = $adjusted_stock;

                    
                    $CI->db->where('batchNumber', $batchNumber);
                    $CI->db->where('itemMasterID', $itemAutoID);
                    $res = $CI->db->update('srp_erp_inventory_itembatch',$data);

                }


                $CI->db->where('mfqItemID', $mfqItemID);
                $CI->db->where('job_id', $last_id);
                $res = $CI->db->delete('srp_erp_mfq_inventory_batch_reserved');

                
            }
        }
        
        return true;
        
       
    }
}


if (!function_exists('release_item_from_existing_batch')) {
    function release_item_from_existing_batch($itemAutoID,$batchNumber,$reserved_qty)
    {
        $CI = &get_instance();
        
        //get batch current details
        $CI->db->select("*");
        $CI->db->where('batchNumber', $batchNumber);
        $CI->db->where('itemMasterID', $itemAutoID);
        $CI->db->from('srp_erp_inventory_itembatch');
        $batch_current_detail =  $CI->db->get()->row_array();

        if($batch_current_detail){
            
            $batch_current_stock = $batch_current_detail['qtr'];
            $adjusted_stock = $batch_current_stock - $reserved_qty;
            $data = array();

            $data['qtr'] = $adjusted_stock;

            $CI->db->where('batchNumber', $batchNumber);
            $CI->db->where('itemMasterID', $itemAutoID);
            $CI->db->update('srp_erp_inventory_itembatch',$data);
        
        }


    }
}

if (!function_exists('load_inco_terms_mfq')) {
    function load_inco_terms_mfq()
    {
        $CI =& get_instance();
        $q = "SELECT
                autoID,
                description
            FROM
                srp_erp_incotermsmaster                 
            WHERE
                companyID = '" . current_companyID() . "' 
            ";
        $result = $CI->db->query($q)->result_array();
        $incoTerms_arr = array(
            '' => 'Select Inco Term'
        );
        if (isset($result)) {
            foreach ($result as $row) {
                $incoTerms_arr[trim($row['autoID'] ?? '')] = $row['description'];
            }
        }
        return $incoTerms_arr;
    }
}

if (!function_exists('get_brand_arr')) {
    function get_brand_arr()
    {
        $CI =& get_instance();
        $q = "SELECT
                brandID,
                description 
            FROM
                srp_erp_mfq_brandmaster 
            WHERE
                status = 1 
                AND companyID = '" . current_companyID() . "'
            ";
        $result = $CI->db->query($q)->result_array();
        $brand_arr = array(
            '' => 'Select Brand'
        );
        if (isset($result)) {
            foreach ($result as $row) {
                $brand_arr[trim($row['brandID'] ?? '')] = $row['description'];
            }
        }
        return $brand_arr;
    }
}





