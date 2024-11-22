<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * Date: 8/25/2017
 * Time: 2:41 PM
 */
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
$newurl = explode("/", $_SERVER['SCRIPT_NAME']);
$actual_link = "$protocol$_SERVER[HTTP_HOST]/$newurl[1]/uploads/NGO/beneficiaryProjectImage/";
$ngo_link = "$protocol$_SERVER[HTTP_HOST]/$newurl[1]/uploads/NGO/beneficiaryImage/";
define("benHNImage", $actual_link);
define("NGOImage", $ngo_link);
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

/*Load all campaign status*/
if (!function_exists('all_states')) {
    function all_states($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("stateID,Description");
        $CI->db->from('srp_erp_statemaster');
        $CI->db->where('countyID', 203);
        $states = $CI->db->get()->result_array();
        $states_arr = array('' => 'Select Province');
        if (isset($states)) {
            foreach ($states as $row) {
                $states_arr[trim($row['stateID'] ?? '')] = (trim($row['Description'] ?? ''));
            }
        }
        return $states_arr;
    }
}

/*Load company country codes*/
if (!function_exists('all_country_codes')) {
    function all_country_codes()
    {
        $CI =& get_instance();
        $CI->db->select("countryCode");
        $CI->db->from('srp_erp_countrymaster');
        $CI->db->where('countryCode !=', '');
        $CI->db->order_by('countryCode', 'ASC');
        $countryCode = $CI->db->get()->result_array();
        $countryCode_arr = array('' => 'Country Code');
        if (isset($countryCode)) {
            foreach ($countryCode as $row) {
                $countryCode_arr[trim($row['countryCode'] ?? '')] = (trim($row['countryCode'] ?? ''));
            }
        }
        return $countryCode_arr;
    }
}
/*donor dropdown*/
if (!function_exists('fetch_contact_donor_drop')) {
    function fetch_contact_donor_drop()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT contactID,name,currencyID FROM srp_erp_ngo_donors WHERE srp_erp_ngo_donors.Com_MasterID IS NULL AND companyID='{$companyID}' ")->result_array();

        return $data;
    }
}
/*donor project dropdown*/
if (!function_exists('fetch_ngo_donors_drop')) {
    function fetch_ngo_donors_drop($status = true)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $donor = $CI->db->query("SELECT contactID,name,currencyID FROM srp_erp_ngo_donors WHERE srp_erp_ngo_donors.Com_MasterID IS NULL AND companyID='{$companyID}' ")->result_array();
        if ($status) {
            $donor_arr = array('' => 'Select Donor');
        } else {
            $donor_arr = [];
        }
        if (isset($donor)) {
            foreach ($donor as $row) {
                $donor_arr[trim($row['contactID'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        return $donor_arr;
    }
}
/*donor project dropdown*/
if (!function_exists('fetch_project_donor_drop')) {
    function fetch_project_donor_drop($status = false)
    {
        $CI =& get_instance();
        $CI->db->SELECT("ngoProjectID,projectName");
        $CI->db->FROM('srp_erp_ngo_projects pro');

        if ($status == true) {
            $CI->db->WHERE('pro.companyID', current_companyID());
            $CI->db->WHERE('masterID', 0);
            $CI->db->JOIN('srp_erp_ngo_projectowners pr', 'pr.projectID = pro.ngoProjectID');
            $CI->db->WHERE('isAdd', 1);
            $CI->db->WHERE('employeeID', $CI->common_data['current_userID']);
        } else {
            $CI->db->WHERE('companyID', current_companyID());
            $CI->db->WHERE('masterID', 0);
        }
        $CI->db->order_by('ngoProjectID', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['ngoProjectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('donor_collection_action')) {
    function donor_collection_action($bankTransferAutoID, $approvalLevelID, $approvedYN, $documentApprovedID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $bankTransferAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'DC\',\'' . $bankTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        // $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_transfer_view/') . '/' . $bankTransferAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('fetch_collection_drop')) {
    function fetch_collection_drop()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("select commitmentAutoId,documentSystemCode from srp_erp_ngo_commitmentmasters WHERE confirmedYN =1 AND companyID='{$companyID}'   ")->result_array();

        $data_arr = array('' => 'Select a commitment');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['commitmentAutoId'] ?? '')] = trim($row['documentSystemCode'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_emp_title_ngo')) {
    function fetch_emp_title_ngo()
    {
        $CI =& get_instance();
        $CI->db->SELECT("TitleID,TitleDescription");
        $CI->db->FROM('srp_titlemaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('TitleDescription');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select a title');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['TitleID'] ?? '')] = trim($row['TitleDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_beneficiary_types')) {
    function fetch_beneficiary_types()
    {
        $CI =& get_instance();
        $CI->db->SELECT("beneficiaryTypeID,description");
        $CI->db->FROM('srp_erp_ngo_benificiarytypes');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->order_by('description');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select a type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['beneficiaryTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}


/*Load all NGO Documents */
if (!function_exists('load_all_ngo_documents')) {
    function load_all_ngo_documents()
    {
        $CI =& get_instance();
        $CI->db->SELECT("documentID,description");
        $CI->db->FROM('srp_erp_ngo_documents');
        $CI->db->WHERE('isActive', 1);
        $documents = $CI->db->get()->result_array();
        $documents_arr = array('' => 'Select a Document');
        if (isset($documents)) {
            foreach ($documents as $row) {
                $documents_arr[trim($row['documentID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $documents_arr;
    }
}

if (!function_exists('action_ngo_docMaster')) {
    function action_ngo_docMaster($DocDesID, $DocDescription)
    {
        $DocDescription = "'" . $DocDescription . "'";
        $action = '<a onclick="edit_docSetup(' . $DocDesID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_doc_ngoMaster(' . $DocDesID . ', ' . $DocDescription . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_ngo_docSetup')) {
    function action_ngo_docSetup($DocDesID, $DocDescription)
    {
        $DocDescription = "'" . $DocDescription . "'";
        $action = '<a onclick="edit_docSetup(' . $DocDesID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_doc_ngoSetup(' . $DocDesID . ', ' . $DocDescription . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

/*Load all NGO Documents */
if (!function_exists('load_all_ngo_upload_documentsTypes')) {
    function load_all_ngo_upload_documentsTypes()
    {
        $CI =& get_instance();
        $CI->db->SELECT("DocDesID,DocDescription");
        $CI->db->FROM('srp_erp_ngo_documentdescriptionmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $documentsTypes = $CI->db->get()->result_array();
        $documentsTypes_arr = array('' => 'Select a Document');
        if (isset($documentsTypes)) {
            foreach ($documentsTypes as $row) {
                $documentsTypes_arr[trim($row['DocDesID'] ?? '')] = trim($row['DocDescription'] ?? '');
            }
        }
        return $documentsTypes_arr;
    }
}

if (!function_exists('ngo_mandatoryStatus')) {
    function ngo_mandatoryStatus($isMandatory)
    {
        return ($isMandatory == 1) ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
    }
}

/*Load all NGO Documents */
if (!function_exists('load_all_ngo_documentsBeneficiarySetup')) {
    function load_all_ngo_documentsBeneficiarySetup()
    {
        $CI =& get_instance();
        $CI->db->SELECT("documentID,description");
        $CI->db->FROM('srp_erp_ngo_documents');
        $CI->db->WHERE('isActive', 1);
        $CI->db->WHERE('documentID', 5);
        $documents = $CI->db->get()->result_array();
        $documents_arr = array('' => 'Select a Document');
        if (isset($documents)) {
            foreach ($documents as $row) {
                $documents_arr[trim($row['documentID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $documents_arr;
    }
}

if (!function_exists('fetch_ngo_stateMaster_name')) {
    function fetch_ngo_stateMaster_name($stateID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("shortCode");
        $CI->db->FROM('srp_erp_statemaster');
        $CI->db->WHERE('stateID', $stateID);
        return $CI->db->get()->row('shortCode');
    }
}

if (!function_exists('fetch_ngo_countryMaster_code')) {
    function fetch_ngo_countryMaster_code($countryID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $CI->db->WHERE('countryID', $countryID);
        return $CI->db->get()->row('countryShortCode');
    }
}

if (!function_exists('fetch_ngo_project_shortCode')) {
    function fetch_ngo_project_shortCode($beneficiaryID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("projectShortCode");
        $CI->db->FROM('srp_erp_ngo_beneficiaryprojects bp');
        $CI->db->join('srp_erp_ngo_projects pro', 'bp.projectID = pro.ngoProjectID');
        $CI->db->WHERE('bp.beneficiaryID', $beneficiaryID);
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('fetch_ngo_status')) {
    function fetch_ngo_status($documentID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("statusID,description");
        $CI->db->FROM('srp_erp_ngo_status');
        $CI->db->WHERE('documentID', $documentID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $status = $CI->db->get()->result_array();
        $status_arr = array('' => 'Select');
        if (isset($status)) {
            foreach ($status as $row) {
                $status_arr[trim($row['statusID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $status_arr;
    }
}
if (!function_exists('fetch_ngo_contractor')) {
    function fetch_ngo_contractor()
    {
        $CI =& get_instance();
        $CI->db->SELECT("supplierAutoID,supplierName,supplierSystemCode");
        $CI->db->FROM('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $contractor = $CI->db->get()->result_array();
        $contractor_arr = array('' => 'Select Contractor');
        if (isset($contractor)) {
            foreach ($contractor as $row) {
                $contractor_arr[trim($row['supplierAutoID'] ?? '')] =   trim($row['supplierSystemCode'] ?? ''). ' | ' .trim($row['supplierName'] ?? '');
            }
        }
        return $contractor_arr;
    }
}

if (!function_exists('confirmation_status_operation_ngo')) {
    function confirmation_status_operation_ngo($confirmedYN)
    {
        $status = '<div style="text-align: center">';

        if ($confirmedYN == 1) {
            $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Confirmed</span>';
        } else if ($confirmedYN == 2) {
            $status .= '<span class="label" style="background-color:#ff784f; color:#ffffff; font-size: 11px;">Refferd Back</span>';
        } else {
            $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Not Confirmed</span>';
        }
        $status .= '</div>';

        return $status;
    }
}
if (!function_exists('projectproposal_action')) {
    function projectproposal_action($bankTransferAutoID, $approvalLevelID, $approvedYN, $documentApprovedID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $bankTransferAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PRP\',\'' . $bankTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        // $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_transfer_view/') . '/' . $bankTransferAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('approval_status')) {
    function approval_status($approvedYN)
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
if (!function_exists('fetch_ngo_ethnicity')) {
    function fetch_ngo_ethnicity()
    {
        $CI =& get_instance();
        $CI->db->SELECT("ethnicityID,description");
        $CI->db->FROM('srp_erp_ethnicitymaster');
        $ethnicity = $CI->db->get()->result_array();
        $ethnicity_arr = array('' => 'Select');
        if (isset($ethnicity)) {
            foreach ($ethnicity as $row) {
                $ethnicity_arr[trim($row['ethnicityID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $ethnicity_arr;
    }
}
if (!function_exists('fetch_ngo_schoolMakthab')) {
    function fetch_ngo_schoolMakthab($type)
    {
        $CI =& get_instance();
        $CI->db->SELECT("schoolComID,schoolComDes");
        $CI->db->FROM('srp_erp_ngo_com_schools');
        if ($type == 1) {
            $CI->db->where('type', 1);
        } else {
            $CI->db->where('type', 2);
        }
        $school = $CI->db->get()->result_array();
        $school_arr = array('' => 'Select');
        if (isset($school)) {
            foreach ($school as $row) {
                $school_arr[trim($row['schoolComID'] ?? '')] = trim($row['schoolComDes'] ?? '');
            }
        }
        return $school_arr;
    }
}

if (!function_exists('fetch_ngo_occupationMaster')) {
    function fetch_ngo_occupationMaster()
    {
        $CI =& get_instance();
        $CI->db->SELECT("OccTypeID,Description");
        $CI->db->FROM('srp_erp_ngo_com_occupationtypes');
        $occupation = $CI->db->get()->result_array();
        $occupation_arr = array('' => 'Select');
        if (isset($occupation)) {
            foreach ($occupation as $row) {
                $occupation_arr[trim($row['OccTypeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $occupation_arr;
    }
}

if (!function_exists('fetch_ngo_damageTypeMaster')) {
    function fetch_ngo_damageTypeMaster($damageCategory, $damageSubCategory, $staus = false)
    {
        $CI =& get_instance();
        if ($staus == true) {
            $CI->db->SELECT("damageTypeID,Description");
            $CI->db->FROM('srp_erp_ngo_damagetypemaster');
            $CI->db->where('damageCategory', $damageCategory);
            $CI->db->where('damageSubCategory', $damageSubCategory);
            $damageTypeMaster = $CI->db->get()->result_array();
            if (isset($damageTypeMaster)) {
                foreach ($damageTypeMaster as $row) {
                    $damageTypeMaster_arr[trim($row['damageTypeID'] ?? '')] = trim($row['Description'] ?? '');
                }
            }
        } else {
            $CI->db->SELECT("damageTypeID,Description");
            $CI->db->FROM('srp_erp_ngo_damagetypemaster');
            $CI->db->where('damageCategory', $damageCategory);
            $CI->db->where('damageSubCategory', $damageSubCategory);
            $damageTypeMaster = $CI->db->get()->result_array();
            $damageTypeMaster_arr = array('' => 'Select');
            if (isset($damageTypeMaster)) {
                foreach ($damageTypeMaster as $row) {
                    $damageTypeMaster_arr[trim($row['damageTypeID'] ?? '')] = trim($row['Description'] ?? '');
                }
            }
        }

        return $damageTypeMaster_arr;
    }
}

if (!function_exists('fetch_ngo_damageItemCategories')) {
    function fetch_ngo_damageItemCategories()
    {
        $CI =& get_instance();
        $CI->db->SELECT("damageItemCategoryID,Description");
        $CI->db->FROM('srp_erp_ngo_damageditemcategories');
        $damageItemCategories = $CI->db->get()->result_array();
        $damageItemCategories_arr = array('' => 'Select');
        if (isset($damageItemCategories)) {
            foreach ($damageItemCategories as $row) {
                $damageItemCategories_arr[trim($row['damageItemCategoryID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $damageItemCategories_arr;
    }
}
if (!function_exists('fetch_ngo_business_activity')) {
    function fetch_ngo_business_activity()
    {
        $CI =& get_instance();
        $CI->db->SELECT("businessID,Description");
        $CI->db->FROM('srp_erp_ngo_business_activity');
        $businessActivity = $CI->db->get()->result_array();
        $businessActivity_arr = array('' => 'Select');
        if (isset($businessActivity)) {
            foreach ($businessActivity as $row) {
                $businessActivity_arr[trim($row['businessID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $businessActivity_arr;
    }
}
if (!function_exists('fetch_ngo_mosqueMaster')) {
    function fetch_ngo_mosqueMaster()
    {
        $CI =& get_instance();
        $CI->db->SELECT("mosqueID,Description");
        $CI->db->FROM('srp_erp_ngo_mosque');
        $mosqueMaster = $CI->db->get()->result_array();
        $mosqueMaster_arr = array('' => 'Select');
        if (isset($mosqueMaster)) {
            foreach ($mosqueMaster as $row) {
                $mosqueMaster_arr[trim($row['mosqueID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $mosqueMaster_arr;
    }
}

if (!function_exists('fetch_ngo_damageTypeMaster_multiple')) {
    function fetch_ngo_damageTypeMaster_multiple($damageCategory, $damageSubCategory)
    {
        $CI =& get_instance();
        $CI->db->SELECT("damageTypeID,Description");
        $CI->db->FROM('srp_erp_ngo_damagetypemaster');
        $CI->db->where('damageCategory', $damageCategory);
        $CI->db->where('damageSubCategory', $damageSubCategory);
        $damageTypeMaster = $CI->db->get()->result_array();
        if (isset($damageTypeMaster)) {
            foreach ($damageTypeMaster as $row) {
                $damageTypeMaster_arr[trim($row['damageTypeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $damageTypeMaster_arr;
    }
}

if (!function_exists('fetch_ngo_jobcategories')) {
    function fetch_ngo_jobcategories()
    {
        $CI =& get_instance();
        $CI->db->SELECT("JobCategoryID,JobCatDescription");
        $CI->db->FROM('srp_erp_ngo_com_jobcategories');
        $jobcategories = $CI->db->get()->result_array();
        $jobcategories_arr = array('' => 'Select');
        if (isset($jobcategories)) {
            foreach ($jobcategories as $row) {
                $jobcategories_arr[trim($row['JobCategoryID'] ?? '')] = trim($row['JobCatDescription'] ?? '');
            }
        }
        return $jobcategories_arr;
    }
}

if (!function_exists('fetch_ngo_insurancetypemaster')) {
    function fetch_ngo_insurancetypemaster($propertyType)
    {
        $CI =& get_instance();
        $CI->db->SELECT("insuranceTypeMasterID,description");
        $CI->db->FROM('srp_erp_ngo_insurancetypemaster');
        $CI->db->where('propertyType', $propertyType);
        $insurancetypeMaster = $CI->db->get()->result_array();
        $insurancetypeMaster_arr = array('' => 'Select');
        if (isset($insurancetypeMaster)) {
            foreach ($insurancetypeMaster as $row) {
                $insurancetypeMaster_arr[trim($row['insuranceTypeMasterID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $insurancetypeMaster_arr;
    }
}
if (!function_exists('fetch_ngo_economicstatusmaster')) {
    function fetch_ngo_economicstatusmaster()
    {
        $CI =& get_instance();
        $CI->db->SELECT("economicStatusID,shortCode");
        $CI->db->FROM('srp_erp_ngo_economicstatusmaster');
        $economicstatusMaster = $CI->db->get()->result_array();
        $economicstatusMaster_arr = array('' => 'Select');
        if (isset($economicstatusMaster)) {
            foreach ($economicstatusMaster as $row) {
                $economicstatusMaster_arr[trim($row['economicStatusID'] ?? '')] = trim($row['shortCode'] ?? '');
            }
        }
        return $economicstatusMaster_arr;
    }
}
if (!function_exists('fetch_ngo_beneficiary_familydetail_types')) {
    function fetch_ngo_beneficiary_familydetail_types()
    {
        $CI =& get_instance();
        $CI->db->SELECT("typeID,Description");
        $CI->db->FROM('srp_erp_ngo_beneficiaryfamilydetailtypes');
        $familydetailTypes = $CI->db->get()->result_array();
        $familydetailTypes_arr = array('' => 'Select');
        if (isset($familydetailTypes)) {
            foreach ($familydetailTypes as $row) {
                $familydetailTypes_arr[trim($row['typeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $familydetailTypes_arr;
    }
}

if (!function_exists('fetch_ngo_policies')) {
    function fetch_ngo_policies($policyCode)
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->SELECT("policyCode");
        $CI->db->FROM('srp_erp_ngo_policies');
        $CI->db->WHERE('companyID', $companyID);
        $CI->db->WHERE('value', 1);
        $CI->db->WHERE('policyCode', $policyCode);
        return $CI->db->get()->row('policyCode');
    }
}
if (!function_exists('fetch_ngo_buildingtypemaster')) {
    function fetch_ngo_buildingtypemaster($buildingCategory, $status = false)
    {
        $CI =& get_instance();
        if ($status == true) {
            $CI->db->SELECT("buildingTypeID,Description");
            $CI->db->FROM('srp_erp_ngo_buildingtypemaster');
            $CI->db->where('buildingCategory', $buildingCategory);
            $buildingtypeMaster = $CI->db->get()->result_array();
            if (isset($buildingtypeMaster)) {
                foreach ($buildingtypeMaster as $row) {
                    $buildingtypeMaster_arr[trim($row['buildingTypeID'] ?? '')] = trim($row['Description'] ?? '');
                }
            }
        } else {
            $CI->db->SELECT("buildingTypeID,Description");
            $CI->db->FROM('srp_erp_ngo_buildingtypemaster');
            $CI->db->where('buildingCategory', $buildingCategory);
            $buildingtypeMaster = $CI->db->get()->result_array();
            $buildingtypeMaster_arr = array('' => 'Select');
            if (isset($buildingtypeMaster)) {
                foreach ($buildingtypeMaster as $row) {
                    $buildingtypeMaster_arr[trim($row['buildingTypeID'] ?? '')] = trim($row['Description'] ?? '');
                }
            }
        }


        return $buildingtypeMaster_arr;
    }
}
if (!function_exists('fetch_ngo_houseconditionmaster')) {
    function fetch_ngo_houseconditionmaster()
    {
        $CI =& get_instance();
        $CI->db->SELECT("houseConditionID,description");
        $CI->db->FROM('srp_erp_ngo_houseconditionmaster');
        $houseconditionMaster = $CI->db->get()->result_array();
        $houseconditionMaster_arr = array('' => 'Select');
        if (isset($houseconditionMaster)) {
            foreach ($houseconditionMaster as $row) {
                $houseconditionMaster_arr[trim($row['houseConditionID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $houseconditionMaster_arr;
    }
}
if (!function_exists('fetch_ngo_incomesourcemaster')) {
    function fetch_ngo_incomesourcemaster()
    {
        $CI =& get_instance();
        $CI->db->SELECT("incomeSourceID,description");
        $CI->db->FROM('srp_erp_ngo_incomesourcemaster');
        $incomesourceMaster = $CI->db->get()->result_array();
        $incomesourceMaster_arr = array('' => 'Select');
        if (isset($incomesourceMaster)) {
            foreach ($incomesourceMaster as $row) {
                $incomesourceMaster_arr[trim($row['incomeSourceID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $incomesourceMaster_arr;
    }
}
if (!function_exists('fetch_project_donor_drop_damage_assestment')) {
    function fetch_project_donor_drop_damage_assestment()
    {
        $CI =& get_instance();
        $CI->db->SELECT("ngoProjectID,projectName");
        $CI->db->FROM('srp_erp_ngo_projects');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->WHERE('masterID', 0);
        $CI->db->WHERE('projectShortCode', 'DA');
        $CI->db->order_by('ngoProjectID', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['ngoProjectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('ownlandavailablestatus_pp')) {
    function ownlandavailablestatus_pp($ownLandAvailable)
    {
        $status = '<div style="text-align: center">';

        if ($ownLandAvailable == 1) {
            $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Available</span>';
        } else {
            $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Not Available</span>';
        }
        $status .= '</div>';

        return $status;
    }
}
if (!function_exists('qualifiedstatusbeneficiary')) {
    function qualifiedstatusbeneficiary($qualifiedstatus)
    {
        $status = '<div style="text-align: center">';

        if ($qualifiedstatus == 1) {
            $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Qualified</span>';
        } else {
            $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Not Qualified</span>';
        }
        $status .= '</div>';

        return $status;
    }
}
if (!function_exists('proposalsatus')) {
    function proposalsatus($proposalstatus)
    {
        $status = '<div style="text-align: center">';

        if ($proposalstatus == 1) {
            $status .= '<span class="label" style="background-color:#ef4b4b; color:#ffffff; font-size: 11px;">Start</span>';
        } else if ($proposalstatus == 2) {
            $status .= '<span class="label" style="background-color:#fbc665; color:#ffffff; font-size: 11px;">Pending</span>';
        } else if ($proposalstatus == 3) {
            $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Closed</span>';
        }
        $status .= '</div>';

        return $status;
    }
}
if (!function_exists('coverted_project_proposal_drop')) {
    function coverted_project_proposal_drop($status = false)
    {
        $CI =& get_instance();
        $CI->db->SELECT("proposalID,proposalName,documentSystemCode");
        $CI->db->FROM('srp_erp_ngo_projectproposals');

       // $CI->db->where('type', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('confirmedYN', 1);

        if ($status == true) {
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $CI->db->where('closedYN', 1);
            $CI->db->where('projectSubID', 0);
            $CI->db->where('type', 1);
            $CI->db->where('approvedYN', 1);
            $CI->db->where('confirmedYN', 1);
        } else {
            $CI->db->where('approvedYN', 1);
            $CI->db->where('confirmedYN', 1);
        }
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Proposal To Convert');

        foreach ($data as $row) {
            $data_arr[trim($row['proposalID'] ?? '')] = trim($row['documentSystemCode'] ?? '') . ' | ' . trim($row['proposalName'] ?? '');
        }


        return $data_arr;
    }
}
if (!function_exists('project_stages_drop')) {
    function project_stages_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("defaultStageID,description,percentage");
        $CI->db->FROM('srp_erp_ngo_defaultprojectstages');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Project Stage');

        foreach ($data as $row) {
            $data_arr[trim($row['defaultStageID'] ?? '')] = trim($row['description'] ?? '');
        }
        return $data_arr;
    }
}
if (!function_exists('project_status')) {
    function project_status($status)
    {
        if ($status >= 0 && $status <= 25) {
            return '<div class="progress"><div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;color:black;font-weight:bold"> ' . round($status) . '%</div></div>';
        } else if ($status >= 25 && $status <= 50) {
            return '<div class="progress"><div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;font-weight:bold">' . round($status) . '%</div></div>';
        } else if ($status >= 50 && $status <= 75) {
            return '<div class="progress"><div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;font-weight:bold">' . round($status) . '%</div></div>';
        } else if ($status >= 75 && $status <= 100) {
            return '<div class="progress"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' . round($status) . '" aria-valuemin="0" aria-valuemax="100" style="width:' . round($status) . '%;font-weight:bold">' . round($status) . '%</div></div>';
        }
    }
}
if (!function_exists('claimed_status')) {
    function claimed_status($claimed)
    {
        $status = '<div style="text-align: center">';

        if ($claimed == 1) {
            $status .= '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Claimed</span>';
        } else {
            $status .= '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">Not Claimed</span>';
        }
        $status .= '</div>';

        return $status;
    }
}
if (!function_exists('all_employee_dropngo')) {
    function all_employee_dropngo($status = TRUE, $isDischarged = 0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        $CI->db->where('isDischarged !=1 ');
        $customer = $CI->db->get()->result_array();
        if ($status == TRUE) {
            $customer_arr = array('' => $CI->lang->line('common_select_employee'));/*'Select Employee'*/
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                }
            }
        } else {
            $customer_arr = $customer;
        }

        return $customer_arr;
    }
}


/* Vehicle Master */
if (!function_exists('fetch_vehicleMaster')) {

    function fetch_vehicleMaster($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("vehicleAutoID,vehicleDescription");
        $CI->db->FROM('srp_erp_ngo_com_vehicles_master');
        $CI->db->order_by('vehicleAutoID', 'ASC');
        $vehiGrp = $CI->db->get()->result_array();
        if ($status) {
            $vehi_arr = array('' => 'Select Vehicle');
        } else {
            $vehi_arr = [];
        }
        if (isset($vehiGrp)) {
            foreach ($vehiGrp as $row) {
                $vehi_arr[trim($row['vehicleAutoID'] ?? '')] = trim($row['vehicleDescription'] ?? '');
            }
        }
        return $vehi_arr;
    }
}

if (!function_exists('fetch_propertyDmg_types')) {
    function fetch_propertyDmg_types()
    {
        $CI =& get_instance();
        $CI->db->SELECT("publicPropertyID,publicPropertyDescription");
        $CI->db->FROM('srp_erp_ngo_publicproperty_types');
        $CI->db->where('masterID IS NULL ');
        $CI->db->order_by('publicPropertyID');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select a type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['publicPropertyID'] ?? '')] = trim($row['publicPropertyDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('selectedImagespp')) {
    function selectedImagespp($benID)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT
	beneficiaryImage
FROM
	`srp_erp_ngo_beneficiaryimages`
	where 
	companyID = {$companyID} 
	AND isSelectedforPP = 1
	AND beneficiaryID = {$benID}")->result_array();

        return $data;
    }
}
if (!function_exists('get_all_operationngo_images')) {
    function get_all_operationngo_images($imagename,$path,$type = null)
    {
        $CI =& get_instance();
        $CI->load->library('s3');
        $image = $CI->s3->createPresignedRequest($path.$imagename , '1 hour');
        return $image;
    }
}