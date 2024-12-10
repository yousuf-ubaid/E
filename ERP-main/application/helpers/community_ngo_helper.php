<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



if (!function_exists('send_requestEmail')) {
    function send_requestEmail($mailData)
    {
        $CI =& get_instance();
        $CI->load->library('email_manual');

        $toEmail = $mailData['toEmail'];
        $subject = $mailData['subject'];
        $param = $mailData['param'];

        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['wordwrap'] = TRUE;
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.sendgrid.net';
        $config['smtp_user'] = 'apikey';
        //$config['smtp_pass'] = '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';
        $config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $CI->load->library('email', $config);
        if (array_key_exists("from", $mailData)) {
            $CI->email->from('noreply@redberylit.com', $mailData['from']);
        } else {
            $CI->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
        }

        if (!empty($param)) {
            $CI->email->to($toEmail);
            $CI->email->subject($subject);
            $CI->email->message($CI->load->view('system/communityNgo/template/request_email_template', $param, TRUE));
        }

        $result = $CI->email->send();
        $CI->email->clear(TRUE);

    }
}


/*blood group dropdown*/
if (!function_exists('selectOnTab')) {
    function selectOnTab()
    {
        $data = '<script> 

 var tabindex = 1;

            $(\'input,select,textarea\').each(function() {
                if (this.type != "hidden") {
                    $(this).attr("tabindex", tabindex);
                    tabindex++;
                }
            });

// https://stackoverflow.com/a/50535297/2782670
            $(document).on(\'focus\', \'.select2\', function (e) {
                if (e.originalEvent) {
                    var s2element = $(this).siblings(\'select\');
                    s2element.select2(\'open\');
                    // Set focus back to select2 element on closing.
                    s2element.on(\'select2:closing\', function (e) {
                        s2element.select2(\'focus\');
                    });
                }
            });
            
</script>';
        return $data;
    }
}
/*blood group dropdown*/
if (!function_exists('load_bloodGroup')) {
    function load_bloodGroup()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_bloodgrouptype")->result_array();
        return $data;
    }
}

/*country dropdown*/
if (!function_exists('load_country')) {
    function load_country()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_countrymaster")->result_array();
        return $data;
    }
}

/*language dropdown*/
if (!function_exists('load_language')) {
    function load_language()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_lang_languages');
        return $CI->db->get()->result_array();

    }
}

/*school type dropdown*/
if (!function_exists('load_schoolTypes')) {
    function load_schoolTypes()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_schooltypes")->result_array();
        return $data;

    }
}

/*degree dropdown*/
if (!function_exists('load_degree')) {
    function load_degree()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_degreecategories")->result_array();
        return $data;
    }
}

/*university dropdown*/
if (!function_exists('load_university')) {
    function load_university()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_universities")->result_array();
        return $data;

    }
}

/*Job category dropdown*/
if (!function_exists('load_Jobcategories')) {
    function load_Jobcategories()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_jobcategories ")->result_array();
        return $data;

    }
}

/*Load all countries for select2*/
if (!function_exists('load_all_countries')) {
    function load_all_countries($status = true)
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

if (!function_exists('load_default_data')) {
    function load_default_data($status = true)
    {
        $CI =& get_instance();
        $company_id = current_companyID();

        $MainArea = $CI->db->query("SELECT * FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_regionmaster.stateID WHERE companyID = {$company_id} ")->row_array();

        if (empty($MainArea)) {
            $CountryID = '';
            $DD_ID = '';
            $DD_Description = '';
            $DistrictID = '';
            $ProvinceID = '';

        } else {
            $CountryID = $MainArea['countyID'];
            $DD_ID = $MainArea['stateID'];
            $DD_Description = $MainArea['Description'];
            $DistrictID = $MainArea['masterID'];

            //province
            $Province = $CI->db->query("SELECT masterID FROM srp_erp_statemaster WHERE countyID = {$CountryID} AND type = 2 AND stateID = {$DistrictID} ")->row_array();
            $ProvinceID = $Province['masterID'];
        }


        $data = array(
            "country" => $CountryID,
            "DD" => $DD_ID,
            "DD_Des" => $DD_Description,
            "district" => $DistrictID,
            "province" => $ProvinceID,

        );
        return $data;
    }
}

// ngo policies
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

/*region dropdown*/
if (!function_exists('load_region')) {
    function load_region()
    {
        $CI =& get_instance();
        $def_data = load_default_data();

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_statemaster');
        $CI->db->WHERE('countyID', $def_data['country']);
        $CI->db->WHERE('masterID', $def_data['DD']);
        $CI->db->WHERE('type', 4);
        $CI->db->WHERE('divisionTypeCode', 'MH');
        $CI->db->order_by('Description');
        $countries = $CI->db->get()->result_array();
        $countries_arr = array('' => 'Select Area/Mahalla');
        if (isset($countries)) {
            foreach ($countries as $row) {
                $countries_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $countries_arr;
    }
}

if (!function_exists('load_region_fo_members')) {
    function load_region_fo_members()
    {
        $CI =& get_instance();
        $def_data = load_default_data();

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_statemaster');
        $CI->db->WHERE('countyID', $def_data['country']);
        $CI->db->WHERE('masterID', $def_data['DD']);
        $CI->db->WHERE('type', 4);
        $CI->db->WHERE('divisionTypeCode', 'MH');
        $CI->db->order_by('Description');
        $countries = $CI->db->get()->result_array();
        //   $countries_arr = array('' => 'Select Area/Mahalla');
        $countries_arr = [];
        if (isset($countries)) {
            foreach ($countries as $row) {
                $countries_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $countries_arr;
    }
}

/*division dropdown*/
if (!function_exists('load_division')) {
    function load_division()
    {
        $CI =& get_instance();
        $def_data = load_default_data();

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_statemaster');
        $CI->db->WHERE('countyID', $def_data['country']);
        $CI->db->WHERE('masterID', $def_data['DD']);
        $CI->db->WHERE('type', 4);
        $CI->db->WHERE('divisionTypeCode', 'GN');
        $CI->db->order_by('Description');
        $countries = $CI->db->get()->result_array();
        $countries_arr = array('' => 'Select GS Division');
        if (isset($countries)) {
            foreach ($countries as $row) {
                $countries_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $countries_arr;
    }
}

if (!function_exists('load_division_for_member')) {
    function load_division_for_member()
    {
        $CI =& get_instance();
        $def_data = load_default_data();
        $countries_arr = [];

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_statemaster');
        $CI->db->WHERE('countyID', $def_data['country']);
        $CI->db->WHERE('masterID', $def_data['DD']);
        $CI->db->WHERE('type', 4);
        $CI->db->WHERE('divisionTypeCode', 'GN');
        $CI->db->order_by('Description');
        $countries = $CI->db->get()->result_array();
      //   $countries_arr = array('' => '');
        if (isset($countries)) {
            foreach ($countries as $row) {
                $countries_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $countries_arr;
    }
}


/*occupation type dropdown*/
if (!function_exists('load_occupationTypes')) {
    function load_occupationTypes()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_occupationtypes")->result_array();
        return $data;
    }
}

/*schools dropdown*/
if (!function_exists('load_ngoSchools')) {
    function load_ngoSchools()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_schools")->result_array();
        return $data;
    }
}

/*help category dropdown*/
if (!function_exists('load_help_category')) {
    function load_help_category()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_helpcategories")->result_array();
        return $data;
    }
}

/*school grades dropdown*/
if (!function_exists('load_grades')) {
    function load_grades()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_grades ORDER BY SortOrder ASC")->result_array();
        return $data;
    }
}

/*both help com member dropdown*/
if (!function_exists('load_both_help_member')) {
    function load_both_help_member()
    {
        $CI =& get_instance();
        $company_id = current_companyID();
        $data = $CI->db->query("SELECT DISTINCT memMaster.Com_MasterID,CName_with_initials,CNIC_No,C_Address FROM srp_erp_ngo_com_communitymaster memMaster INNER JOIN srp_erp_ngo_com_memberhelprequirements helpRq ON helpRq.Com_MasterID=memMaster.Com_MasterID INNER JOIN srp_erp_ngo_com_memberwillingtohelp helpWilling ON helpWilling.Com_MasterID=memMaster.Com_MasterID WHERE memMaster.companyID='{$company_id}' AND memMaster.isDeleted='0'")->result_array();

        return $data;
    }
}

/*get all titles*/
if (!function_exists('load_titles')) {
    function load_titles()
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

/*period type*/
if (!function_exists('all_periodType_drop')) {
    function all_periodType_drop()
    {
        $CI =& get_instance();

        $data = $CI->db->query("SELECT PeriodTypeID,Description FROM srp_erp_ngo_com_financialperiodtypes")->result_array();
        $data_arr = array('' => 'Select Period Type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['PeriodTypeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('periodType_without_Daily')) {
    function periodType_without_Daily()
    {
        $CI =& get_instance();

        $data = $CI->db->query("SELECT PeriodTypeID,Description FROM srp_erp_ngo_com_financialperiodtypes WHERE PeriodTypeID != 5 ")->result_array();
        $data_arr = array('' => 'Select Period Type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['PeriodTypeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_all_segment')) {
    function fetch_all_segment()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_gender')) {
    function load_gender()
    {
        $CI =& get_instance();
        $CI->db->SELECT("genderID,name");
        $CI->db->FROM('srp_erp_gender');
        $CI->db->order_by('genderID', 'ASC');
        $data = $CI->db->get()->result_array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['genderID'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('load_gender_for_collection')) {
    function load_gender_for_collection()
    {
        $CI =& get_instance();
        $CI->db->SELECT("genderID,name");
        $CI->db->FROM('srp_erp_gender');
        $CI->db->order_by('genderID', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('0' => 'Both');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['genderID'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_occupationType_drop')) {
    function fetch_occupationType_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("OccTypeID,Description");
        $CI->db->FROM('srp_erp_ngo_com_occupationtypes');
        $CI->db->order_by('OccTypeID', 'ASC');
        $Description = $CI->db->get()->result_array();

        if (isset($Description)) {
            foreach ($Description as $row) {
                $Description_arr[trim($row['OccTypeID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        return $Description_arr;
    }
}


if (!function_exists('load_collectionType')) {
    function load_collectionType()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_ngo_com_collectiontypes');
        return $CI->db->get()->result_array();

    }
}

/*issue items*/
if (!function_exists('load_prq_action')) { /*get po action list*/
    function load_prq_action($itemIssueAutoID, $POConfirmedYN, $isDeleted)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/CommunityNgo/ngo_hi_rental_item_issue_new",' . $itemIssueAutoID . ',"Edit Item Request","RTL"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="PageView_modal(\'RTL\',\'' . $itemIssueAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="delete_item(' . $itemIssueAutoID . ',\'Rental Item Request\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a target="_blank" onclick="PageView_modal(\'RTL\',\'' . $itemIssueAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="return_item_modal_old(\'' . $itemIssueAutoID . '\')" ><span title="Return" rel="tooltip" class="glyphicon glyphicon-backward"></span></a>';
            //  $status .= '<a target="_blank" onclick="return_item_modal(\'' . $itemIssueAutoID . '\')" ><span title="Return" rel="tooltip" class="glyphicon glyphicon-backward"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

/*item return status*/
if (!function_exists('return_status')) {
    function return_status($con)
    {
        $status = '<center>';
        if ($con == 0) {
            $status .= '<span class="label label-danger">Not Returned</span>';
        } elseif ($con == 1) {
            $status .= '<span class="label label-success">Returned</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}


if (!function_exists('member_active_status')) {
    function member_active_status($active)
    {
        $status = '<center>';
        if ($active == 1) {
            $status .= '<span class="label" style="background-color:#8bc34a; color: #FFFFFF;">&nbsp;</span>';
        } elseif ($active == 0) {
            $status .= '<span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF;">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('viewImage')) {
    function viewImage($Image)
    {
        $status = '<center>';
        if ($Image) {
            $status .= '<img class="align-left"
                 src="' . base_url('uploads/NGO/communitymemberImage/' . $Image) . '"
                 width="32" height="32">';

        } else {
            $status .= '<img class="align-left" src="' . base_url("images/crm/icon-list-contact.png") . '"
                                     alt="" width="32" height="32">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_com_member_action')) { /*get member action list*/
    function load_com_member_action($Com_MasterID, $isActive, $isDeleted, $MemberCode, $CName_with_initials)
    {
        $CI =& get_instance();
        $CI->load->library('session');

        $company_id = current_companyID();
        $page = $CI->db->query("SELECT createPageLink FROM srp_erp_templatemaster
                              LEFT JOIN srp_erp_templates ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                              WHERE srp_erp_templates.FormCatID = 530 AND companyID={$company_id}
                              ORDER BY srp_erp_templatemaster.FormCatID")->row('createPageLink');

        $status = '<span class="pull-right">';

        if ($isActive == 0) {
            $status .= '<a href="#"
                                   onclick="fetchPage(\'system/communityNgo/ngo_member_view\',' . $Com_MasterID . ',\'View Details - ' . $MemberCode . '\')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="View"></span></a>';
        } else {

            $status .= '<a href="#"
                                   onclick="fetchPage(\'system/communityNgo/ngo_member_view\',' . $Com_MasterID . ',\'View Details - ' . $MemberCode . ' \')"><span
                                        title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                        data-original-title="View"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a href="#"
                               onclick="fetchPage(\''.$page.'\',' . $Com_MasterID . ',\'Edit Member - ' . $MemberCode . ' \')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            $status .= '<a onclick="delete_communityMembers(' . $Com_MasterID . ')"><span
                                        title="Delete"
                                        rel="tooltip"
                                        class="glyphicon glyphicon-trash"
                                        style="color:#d15b47;"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('view_detail_modal')) {
    function view_detail_modal($Com_MasterID, $MemberCode, $CName_with_initials)
    {
        $data = '<a href="#"  onclick="fetchPage(\'system/communityNgo/ngo_member_view\',' . $Com_MasterID . ',\'View Details - ' . $MemberCode . ' | ' . $CName_with_initials . '\',\'NGO\')">' . $CName_with_initials . '</a>';
        return $data;
    }
}


if (!function_exists('all_member_drop')) {
    function all_member_drop($status = TRUE)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("Com_MasterID,MemberCode,CName_with_initials");
        $CI->db->from('srp_erp_ngo_com_communitymaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isDeleted', 0);
        $CI->db->where('isActive', 1);

        $customer = $CI->db->get()->result_array();
        if ($status == TRUE) {
            //   $customer_arr = array('' => 'Select Member');
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['Com_MasterID'] ?? '')] = trim($row['MemberCode'] ?? '') . ' | ' . trim($row['CName_with_initials'] ?? '');
                }
            }
        } else {
            $customer_arr = $customer;
        }
        return $customer_arr;
    }
}


if (!function_exists('all_member_drop_for_community')) {
    function all_member_drop_for_community($status = TRUE)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("Com_MasterID,MemberCode,CName_with_initials");
        $CI->db->from('srp_erp_ngo_com_communitymaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isDeleted', 0);

        $customer = $CI->db->get()->result_array();
        if ($status == TRUE) {
            $customer_arr = [];
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['Com_MasterID'] ?? '')] = trim($row['MemberCode'] ?? '') . ' | ' . trim($row['CName_with_initials'] ?? '');
                }
            }
        } else {
            $customer_arr = $customer;
        }
        return $customer_arr;
    }
}

if (!function_exists('fetch_rental_item_issue')) {

    function fetch_rental_item_issue()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data = $CI->db->query('SELECT rentalItemID,rentalItemType,itemAutoID,rentalItemCode,rentalItemDes,PeriodTypeID,defaultUnitOfMeasureID,defaultUnitOfMeasure,currentStock,RentalPrice,srp_erp_ngo_com_rentalitems.SortOrder,rentalStatus,faID FROM srp_erp_ngo_com_rentalitems  WHERE  srp_erp_ngo_com_rentalitems.companyID = "' . $companyID . '" AND srp_erp_ngo_com_rentalitems.rentalStatus = "1" AND srp_erp_ngo_com_rentalitems.isDeleted = "0" ')->result_array();
        return $data;
    }
}


if (!function_exists('other_attachments')) {
    function other_attachments()
    {
        echo '<div class="modal fade" id="other_attachment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="other_attachment_modal_label">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">&nbsp;</div>
                        <div class="col-md-10"><span class="pull-right">';

        echo form_open_multipart('', 'id="other_attachment_upload_form" class="form-inline"');

        echo '<div class="form-group">
                                <input type="text" class="form-control" id="other_attachmentDescription"
                                       name="other_attachmentDescription"
                                       placeholder="Description...">
                                    <!--Description-->
                                <input type="hidden" class="form-control" id="other_documentSystemCode"
                                       name="other_documentSystemCode">
                                <input type="hidden" class="form-control" id="other_documentID" name="other_documentID">
                                <input type="hidden" class="form-control" id="other_document_name" name="other_document_name">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
                                          class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                          class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="other_document_file" id="other_document_file"></span>
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="other_remove_id"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="other_attachment_upload()"><span
                                  class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                                </form></span>
                        </div>
                    </div>
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="other_attachment_modal_body" class="no-padding">
                        <tr class="danger">
                            <td colspan="5" class="text-center">No Attachment Found</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>';
    }
}

if (!function_exists('fetch_all_gl_codes')) {
    function fetch_all_gl_codes($code = NULL, $category = NULL)
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory,accountCategoryTypeID");
        $CI->db->from('srp_erp_chartofaccounts');
        if ($code) {
            $CI->db->where('subCategory', $code);
        }
        if ($category) {
            $CI->db->where('subCategory !=', $category);
        }
        $CI->db->where('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->WHERE('accountCategoryTypeID !=', 4);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('income_gl_drop')) {
    function income_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->select("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterCategory', 'PL');
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('accountCategoryTypeID !=', 4);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Supplier GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('receivable_gl_drop')) {
    function receivable_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->select("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterCategory', 'BS');
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('accountCategoryTypeID !=', 4);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Supplier GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}


/*community master */
if (!function_exists('fetch_comMaster_lead')) {

    function fetch_comMaster_lead()
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT Com_MasterID,CName_with_initials,CNIC_No,companyID,C_Address FROM srp_erp_ngo_com_communitymaster WHERE companyID='{$companyID}' AND isDeleted='0'")->result_array();
        return $data;
    }
}

/*fetch heads of family */
if (!function_exists('fetch_headsOf_family')) {

    function fetch_headsOf_family()
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data = $CI->db->query("SELECT Com_MasterID,CName_with_initials,CNIC_No,srp_erp_ngo_com_familymaster.companyID,C_Address FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID WHERE srp_erp_ngo_com_familymaster.companyID='{$companyID}' AND srp_erp_ngo_com_familymaster.isDeleted='0' ")->result_array();

        return $data;
    }
}

/*community member occupation */
if (!function_exists('fetch_memOccupation')) {
    function fetch_memOccupation()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data = $CI->db->query("SELECT Com_MasterID,MemJobID,JobCategoryID,gradeComID,companyID,IFNULL(WorkingPlace ,'')AS WorkingPlaceS,IFNULL(Address,'') AS AddressS FROM srp_erp_ngo_com_memjobs WHERE companyID='{$companyID}'")->result_array();

        return $data;
    }
}

/*Fam Ancestry */
if (!function_exists('fetch_family_ancestry')) {
    function fetch_family_ancestry()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT AncestryCatID,AncestryDes FROM srp_erp_ngo_com_ancestrycategory ")->result_array();

        return $data;
    }
}

/*Fam Economic Status */
if (!function_exists('fetch_fam_econStatus')) {
    function fetch_fam_econStatus()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT EconStateID,EconStateDes FROM srp_erp_ngo_com_familyeconomicstatemaster")->result_array();

        return $data;
    }
}

/*Fam existed hoses in enrolling */
if (!function_exists('fetch_house_exitInEnroll')) {
    function fetch_house_exitInEnroll()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data = $CI->db->query("SELECT hEnrollingID,FamilySystemCode,FamilyName,LeaderID FROM srp_erp_ngo_com_house_enrolling INNER JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID WHERE srp_erp_ngo_com_house_enrolling.companyID={$companyID} AND (FamHouseSt ='0' OR FamHouseSt IS NULL)")->result_array();

        return $data;
    }
}

/*Fam Ownership Type master */
if (!function_exists('fetch_house_house_ownership')) {
    function fetch_house_house_ownership()
    {
        $CI =& get_instance();

        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_house_ownership_master")->result_array();

        return $data;
    }
}

/*Fam house type master */
if (!function_exists('fetch_house_type_master')) {
    function fetch_house_type_master()
    {
        $CI =& get_instance();

        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_house_type_master")->result_array();

        return $data;
    }
}

if (!function_exists('load_houseOwnership')) {
    function load_houseOwnership()
    {
        $CI =& get_instance();

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_ngo_com_house_ownership_master');
        $CI->db->order_by('ownershipAutoID');
        $hsOwnerSp = $CI->db->get()->result_array();
        $hsOwnerSp_arr = array('' => 'Select Ownership Type');
        if (isset($hsOwnerSp)) {
            foreach ($hsOwnerSp as $row) {
                $hsOwnerSp_arr[trim($row['ownershipAutoID'] ?? '')] = trim($row['ownershipDescription'] ?? '');
            }
        }
        return $hsOwnerSp_arr;
    }
}

if (!function_exists('load_houseTypes')) {
    function load_houseTypes()
    {
        $CI =& get_instance();

        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_ngo_com_house_type_master');
        $CI->db->order_by('hTypeAutoID');
        $hsTypes = $CI->db->get()->result_array();
        $hsTypes_arr = array('' => 'Select House Type');
        if (isset($hsTypes)) {
            foreach ($hsTypes as $row) {
                $hsTypes_arr[trim($row['hTypeAutoID'] ?? '')] = trim($row['hTypeDescription'] ?? '');
            }
        }
        return $hsTypes_arr;
    }
}

/*donor project dropdown*/
if (!function_exists('fetch_project_com_drop')) {
    function fetch_project_com_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("ngoProjectID,projectName");
        $CI->db->FROM('srp_erp_ngo_projects');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->WHERE('masterID', 0);
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

if (!function_exists('fetch_familyMemAct_drop')) { /*fetch fetch_familyMem act drop*/
    function fetch_familyMemAct_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("companyID,Com_MasterID,CName_with_initials,HouseNo,C_Address");
        $CI->db->FROM('srp_erp_ngo_com_communitymaster');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->WHERE('isActive', '1');
        $CI->db->order_by('Com_MasterID', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Member');
        if (isset($data)) {
            foreach ($data as $row) {

                $data_arr[trim($row['Com_MasterID'] ?? '')] = trim($row['CName_with_initials'] ?? '') . ' | ' . trim($row['HouseNo'] ?? '') . ' | ' . trim($row['C_Address'] ?? '');

            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_familyMems_drop')) { /*fetch fetch_familyMems_drop*/
    function fetch_familyMems_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("companyID,Com_MasterID,CName_with_initials,HouseNo,C_Address");
        $CI->db->FROM('srp_erp_ngo_com_communitymaster');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->order_by('Com_MasterID', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Member');
        if (isset($data)) {
            foreach ($data as $row) {

                $data_arr[trim($row['Com_MasterID'] ?? '')] = trim($row['CName_with_initials'] ?? '') . ' | ' . trim($row['HouseNo'] ?? '') . ' | ' . trim($row['C_Address'] ?? '');

            }
        }
        return $data_arr;
    }
}

/*fetch Fam Relationship dropdown*/
if (!function_exists('fetch_family_relationship')) {
    function fetch_family_relationship()
    {
        $CI =& get_instance();
        $CI->db->SELECT("relationshipID,relationship,genderID");
        $CI->db->FROM('srp_erp_family_relationship');
        $CI->db->order_by('relationshipID', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Relationship');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['relationshipID'] ?? '')] = trim($row['relationship'] ?? '');
            }
        }
        return $data_arr;
    }
}

/*fetch community Relationship report dropdown*/
if (!function_exists('fetch_ngo_relationType_drop')) {
    function fetch_ngo_relationType_drop()
    {
        $CI =& get_instance();

        $data = $CI->db->query("SELECT relationshipID,relationship,genderID FROM srp_erp_family_relationship")->result_array();

        return $data;
    }
}

/*fetch Fam Relationship dropdown*/
if (!function_exists('fetch_com_gender')) {
    function fetch_com_gender()
    {
        $CI =& get_instance();
        $CI->db->SELECT("genderID,name");
        $CI->db->FROM('srp_erp_gender');
        $CI->db->order_by('genderID', 'ASC');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Gender');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['genderID'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        return $data_arr;
    }
}

/*fetch community Occupation report dropdown*/
if (!function_exists('fetch_ngo_memberType_drop')) {
    function fetch_ngo_memberType_drop()
    {
        $CI =& get_instance();

        $data = $CI->db->query("SELECT OccTypeID,Description FROM srp_erp_ngo_com_occupationtypes")->result_array();

        return $data;
    }
}

/*fetch all countries for select2*/
if (!function_exists('fetch_all_countries')) {
    function fetch_all_countries($status = true)/*Load all Supplier*/
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

if (!function_exists('fetch_com_beneficiary_types')) {
    function fetch_com_beneficiary_types()
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

/*Load all campaign statemaster*/
if (!function_exists('all_statemaster')) {
    function all_statemaster($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("stateID,Description");
        $CI->db->from('srp_erp_statemaster');
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

if (!function_exists('fetch_com_project_shortCode')) {
    function fetch_com_project_shortCode($beneficiaryID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("projectShortCode");
        $CI->db->FROM('srp_erp_ngo_beneficiaryprojects bp');
        $CI->db->join('srp_erp_ngo_projects pro', 'bp.projectID = pro.ngoProjectID');
        $CI->db->WHERE('bp.beneficiaryID', $beneficiaryID);
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('fetch_com_title')) {
    function fetch_com_title()
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

/*gender dropdown*/
if (!function_exists('drop_gender')) {
    function drop_gender()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT srp_erp_gender.genderID,srp_erp_gender.name FROM srp_erp_gender ")->result_array();
        return $data;
    }
}
/*marital status dropdown*/
if (!function_exists('drop_maritalstatus')) {
    function drop_maritalstatus()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_maritalstatus ")->result_array();
        return $data;
    }
}

/*no. of houses count */
if (!function_exists('load_totHouses')) {
    function load_totHouses()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data = $CI->db->query("SELECT COUNT(*) AS totHouseCount1  FROM srp_erp_ngo_com_house_enrolling LEFT JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID WHERE srp_erp_ngo_com_house_enrolling.companyID='" . $companyID . "' AND (srp_erp_ngo_com_house_enrolling.FamHouseSt = '0' OR srp_erp_ngo_com_house_enrolling.FamHouseSt = NULL) AND srp_erp_ngo_com_familymaster.isDeleted = '0'")->row_array();

        return $data;
    }
}

/*community rental warehouse setup */

if (!function_exists('comNgoWarehouseBinFilter')) {
    function comNgoWarehouseBinFilter()
    {
        $CI =& get_instance();
        $CI->db->SELECT("srp_erp_warehousemaster.*");
        $CI->db->FROM('srp_erp_warehousemaster');
        $CI->db->WHERE('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select a warehouse');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseCode'] . " |" . $row['wareHouseDescription']);
            }
        }
        return $data_arr;
    }
}

if (!function_exists('alter_CommitteePosition')) {
    function alter_CommitteePosition($CommitteePositionID, $CommitteePositionDes, $usageCount)
    {
        $posDescription = "'" . $CommitteePositionDes . "'";
        $action = '<a onclick="edit_CommitteePosition(' . $CommitteePositionID . ', ' . $posDescription . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_CommitteePosition(' . $CommitteePositionID . ', ' . $posDescription . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('alter_CommitteeMas')) {
    function alter_CommitteeMas($CommitteeID, $CommitteeDes, $isActive, $usageCount)
    {
        $CommitteeDs = "'" . $CommitteeDes . "'";

        if ($isActive == '1') {
            $action = '<a onclick=\'fetchPage("system/communityNgo/ngo_mo_committee_contents",' . $CommitteeID . ',"Committee - ' . $CommitteeDes . '","NGO"); \'><span title="Sub Committees" style="color:#009688;font-size:18px;" rel="tooltip" class="fa fa-sitemap fa-lg" data-original-title="Sub Committees"></span></a>';

        } else {
            $action = '<a><span title="Sub Committees - Inactive" style="color:#d9534f;font-size:18px;" rel="tooltip" class="fa fa-sitemap fa-lg" data-original-title="Sub Committees"></span></a>';

        }

        $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a class="CA_Alter_btn" onclick="editCommitteeMas(' . $CommitteeID . ', ' . $CommitteeDs . ', ' . $isActive . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($usageCount == 0) {
            $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="deleteCommitteeMas(' . $CommitteeID . ', ' . $CommitteeDs . ')">';
            $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        return '<span class="pull-right">' . $action . '</span>';

    }
}


/*fetch sub committees dropdown*/
if (!function_exists('fetch_subCommittees_drop')) {
    function fetch_subCommittees_drop()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data = $CI->db->query("SELECT CommitteeAreawiseID,CommitteeAreawiseDes FROM srp_erp_ngo_com_committeeareawise WHERE companyID='" . $companyID . "'")->result_array();

        return $data;
    }
}

if (!function_exists('active_Member')) {
    function active_Member($YN)
    {
        if ($YN == 1) {
            $clCode = 'color:rgb(6,2,2);';
        } else {
            $clCode = 'color:rgb(203,203,203);';

        }
        return '<div style="text-align: center"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-ok" style="' . $clCode . '"></span></div>';

    }
}

/*committee positions */
if (!function_exists('fetch_committee_postitn')) {

    function fetch_committee_postitn()
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_committeeposition WHERE companyID='{$companyID}' AND isDeleted='0'")->result_array();

        return $data;
    }
}

/*committee member service fetching */
if (!function_exists('cmtee_memServiceDel')) {
    function cmtee_memServiceDel($CmtMemServiceID, $value, $name, $CommitteeMemID)
    {

        $date_format_policy = date_format_policy();
        switch ($name) {

            case 'cmtmemservice':
                $html = '<div class="hideinput hide xxx_' . $CmtMemServiceID . '">
<input class="' . $name . '" type="text" value="' . $value . '" id="' . $name . '_' . $CmtMemServiceID . '" name="' . $name . '" >
</div>
<div class="showinput xx_' . $CmtMemServiceID . '" id="' . $name . '_1' . $CmtMemServiceID . '">' . $value . '</div>';
                break;
            case 'servicedate':
                $html = '<div class="hideinput hide xxx_' . $CmtMemServiceID . '">
                <input class="' . $name . '" onchange="' . $name . '_' . $CmtMemServiceID . '.val(this.value);" type="text" data-inputmask="alias:' . $date_format_policy . '" value="' . $value . '" id="' . $name . '_' . $CmtMemServiceID . '" name="' . $name . '" >
                </div>
                <div class="showinput xx_' . $CmtMemServiceID . '" id="' . $name . '_1' . $CmtMemServiceID . '">' . $value . '</div>';
                break;
            case 'order':

                $CI =& get_instance();
                $companyID = $CI->common_data['company_data']['company_id'];
                $sort = $CI->db->query("SELECT sortOrder FROM srp_erp_ngo_com_committeememberservices WHERE companyID='{$companyID}' AND CmtMemServiceID='{$CmtMemServiceID}'")->result_array();
                $select = '<div class="hideinput hide xxx_' . $CmtMemServiceID . '"><select class="" id="' . $name . '_' . $CmtMemServiceID . '" name="' . $name . '" >';
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

                $html = $select . '<div class="showinput xx_' . $CmtMemServiceID . '" id="' . $name . '_1' . $CmtMemServiceID . '">' . $value . '</div>';
                break;

        }
        return $html;
    }
}

/* families */
if (!function_exists('fetch_familyMaster')) {

    function fetch_familyMaster($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("FamMasterID,FamilySystemCode,FamilyName,CName_with_initials");
        $CI->db->FROM('srp_erp_ngo_com_familymaster');
        $CI->db->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_familymaster.LeaderID = srp_erp_ngo_com_communitymaster.Com_MasterID');
        $CI->db->WHERE('srp_erp_ngo_com_familymaster.isDeleted', '0');
        $CI->db->WHERE('srp_erp_ngo_com_familymaster.companyID', current_companyID());
        $CI->db->order_by('FamMasterID', 'ASC');
        $family = $CI->db->get()->result_array();
        if ($status) {
            $family_arr = array('' => 'Select Family');
        } else {
            $family_arr = [];
        }
        if (isset($family)) {
            foreach ($family as $row) {
                $family_arr[trim($row['FamMasterID'] ?? '')] = trim($row['FamilySystemCode'] ?? '') . ' |' . trim($row['CName_with_initials'] ?? '');
            }
        }
        return $family_arr;
    }
}

/*committees */
if (!function_exists('fetch_committeesMaster')) {

    function fetch_committeesMaster($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("CommitteeID,CommitteeDes");
        $CI->db->FROM('srp_erp_ngo_com_committeesmaster');
        $CI->db->WHERE('isDeleted', '0');
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->order_by('CommitteeID', 'ASC');
        $cmntMas = $CI->db->get()->result_array();
        if ($status) {
            $cmnt_arr = array('' => 'Select Committee');
        } else {
            $cmnt_arr = [];
        }
        if (isset($cmntMas)) {
            foreach ($cmntMas as $row) {
                $cmnt_arr[trim($row['CommitteeID'] ?? '')] = trim($row['CommitteeDes'] ?? '');
            }
        }
        return $cmnt_arr;
    }
}

if (!function_exists('load_committeesMem')) {
    function load_committeesMem()
    {
        $CI =& get_instance();
        $CI->db->SELECT("CommitteeAreawiseID,CommitteeID,CommitteeHeadID,Com_MasterID,CName_with_initials");
        $CI->db->FROM('srp_erp_ngo_com_committeeareawise');
        $CI->db->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_committeeareawise.CommitteeHeadID = srp_erp_ngo_com_communitymaster.Com_MasterID');
        $CI->db->WHERE('srp_erp_ngo_com_committeeareawise.isActive', '1');
        $CI->db->WHERE('srp_erp_ngo_com_committeeareawise.companyID', current_companyID());
        $CI->db->group_by('CommitteeHeadID');
        $commitHd = $CI->db->get()->result_array();

        $commitHead_arr = array('' => 'Select Committee Head');

        if (isset($commitHd)) {
            foreach ($commitHd as $row) {
                $commitHead_arr[trim($row['Com_MasterID'] ?? '')] = trim($row['CName_with_initials'] ?? '');
            }
        }
        return $commitHead_arr;
    }
}

if (!function_exists('active_famLog')) {
    function active_famLog($YN)
    {
        if ($YN == 1) {
            $clCode = 'color:rgb(6,2,2);';
        } else {
            $clCode = 'color:rgb(203,203,203);';

        }
        return '<div style="text-align: center"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-ok" style="' . $clCode . '"></span></div>';

    }
}

/* Permanent Sickness */
if (!function_exists('fetch_permanentSickness')) {

    function fetch_permanentSicknes($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("sickAutoID,sickDescription");
        $CI->db->FROM('srp_erp_ngo_com_permanent_sickness');
        $CI->db->order_by('sickAutoID', 'ASC');
        $sickGrp = $CI->db->get()->result_array();
        if ($status) {
            $sick_arr = array('' => 'Select Sickness');
        } else {
            $sick_arr = [];
        }
        if (isset($sickGrp)) {
            foreach ($sickGrp as $row) {
                $sick_arr[trim($row['sickAutoID'] ?? '')] = trim($row['sickDescription'] ?? '');
            }
        }
        return $sick_arr;
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

if (!function_exists('fetch_BloodGrpsDes')) {

    function fetch_BloodGrpsDes($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("BloodTypeID,BloodDescription");
        $CI->db->FROM('srp_erp_bloodgrouptype');
        $CI->db->order_by('BloodTypeID', 'ASC');
        $bloodGrp = $CI->db->get()->result_array();
        if ($status) {
            $blood_arr = array('' => 'Select Blood Group');
        } else {
            $blood_arr = [];
        }
        if (isset($bloodGrp)) {
            foreach ($bloodGrp as $row) {
                $blood_arr[trim($row['BloodTypeID'] ?? '')] = trim($row['BloodDescription'] ?? '');
            }
        }
        return $blood_arr;
    }
}

if (!function_exists('allEconState_drop')) {
    function allEconState_drop($type = 0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("t1.EconStateID,EconStateDes");
        $CI->db->FROM('srp_erp_ngo_com_familyeconomicstatemaster t1');
        $CI->db->order_by('EconStateID');
        $data = $CI->db->get()->result_array();


        if ($type == 0) {
            $data_arr = array('' => $CI->lang->line('CommunityNgo_fam_selEconState')/*'Select Economic Status'*/);
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['EconStateID'] ?? '')] = trim($row['EconStateDes'] ?? '');
                }
                return $data_arr;
            }
        } else {
            return $data;
        }

    }
}


if (!function_exists('fetch_com_countryMaster_code')) {
    function fetch_com_countryMaster_code($countryID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $CI->db->WHERE('countryID', $countryID);
        return $CI->db->get()->row('countryShortCode');
    }
}

if (!function_exists('fetch_com_stateMaster_name')) {
    function fetch_com_stateMaster_name($stateID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("shortCode");
        $CI->db->FROM('srp_erp_statemaster');
        $CI->db->WHERE('stateID', $stateID);
        return $CI->db->get()->row('shortCode');
    }
}

/*Zakat project proposal*/
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

/*ZAKAT AGE GROUPING */
if (!function_exists('fetch_fam_ageGroup')) {
    function fetch_fam_ageGroup()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_ngo_com_zakatagegroupmaster")->result_array();

        return $data;
    }
}

if (!function_exists('econ_status')) {
    function econ_status($benificiaryID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("EconStateID,EconStateDes");
        $CI->db->FROM('srp_erp_ngo_com_familyeconomicstatemaster');
        $CI->db->order_by('srp_erp_ngo_com_familyeconomicstatemaster.EconStateID', 'ASC');
        $econState = $CI->db->get()->result_array();

        $select = '<div><select class="" id="EconStateIDs" name="EconStateIDs[]">';
        $select .= '<option value=""></option>';
        foreach ($econState as $val) {

            $select .= '<option value="' . $val['EconStateID'] . '" >' . $val['EconStateDes'] . '</option>';
        }

        $select .= '</select ></div>';

        return $select;
    }
}

/* Notice Board */

/*region dropdown*/
if (!function_exists('report_load_region')) {
    function report_load_region()
    {
        $CI =& get_instance();
        $def_data = load_default_data();

        $companyID = $CI->common_data['company_data']['company_id'];

        $data='';
        if(!empty($def_data) && !empty($def_data['country'])){
            $data = $CI->db->query("SELECT * FROM srp_erp_statemaster WHERE countyID={$def_data['country']} AND masterID={$def_data['DD']} AND srp_erp_statemaster.type='4' AND divisionTypeCode='MH' ORDER BY Description")->result_array();

        }
        else{

            $data2Count = $CI->db->query("SELECT countryID FROM srp_erp_company WHERE srp_erp_company.company_id = {$companyID}")->row_array();
            $countryFil=$data2Count['countryID'];
            if(!empty($countryFil)){
                $countrysFil =$countryFil;
            }
            else{
                $countrysFil='';
            }
            $data = $CI->db->query("SELECT * FROM srp_erp_statemaster WHERE countyID={$countrysFil} AND srp_erp_statemaster.type='4' AND divisionTypeCode='MH' ORDER BY Description")->result_array();

        }

        return $data;
    }
}
/*division dropdown*/
if (!function_exists('load_divisionForUploads')) {
    function load_divisionForUploads()
    {
        $CI =& get_instance();
        $def_data = load_default_data();

        $companyID = $CI->common_data['company_data']['company_id'];

        $data='';
        if(!empty($def_data) && !empty($def_data['country'])) {
            $data = $CI->db->query("SELECT * FROM srp_erp_statemaster WHERE countyID={$def_data['country']} AND masterID={$def_data['DD']} AND srp_erp_statemaster.type='4' AND divisionTypeCode='GN' ORDER BY Description")->result_array();
        }
        else{

            $data2Count = $CI->db->query("SELECT countryID FROM srp_erp_company WHERE srp_erp_company.company_id = {$companyID}")->row_array();
            $countryFil=$data2Count['countryID'];
            if(!empty($countryFil)){
                $countrysFil =$countryFil;
            }
            else{
                $countrysFil='';
            }
            $data = $CI->db->query("SELECT * FROM srp_erp_statemaster WHERE countyID={$countrysFil} AND srp_erp_statemaster.type='4' AND divisionTypeCode='GN' ORDER BY Description")->result_array();

        }

        return $data;
    }
}

if (!function_exists('Notice_Type_drop')) {
    function Notice_Type_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_ngo_com_noticeboardmaster');
        $CI->db->order_by('NoticeTypeID');
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['NoticeTypeID'] ?? '')] = trim($row['NoticeType'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('Notice_Type_filter')) {
    function Notice_Type_filter()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_ngo_com_noticeboardmaster');
        $CI->db->order_by('NoticeTypeID');
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'All');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['NoticeTypeID'] ?? '')] = trim($row['NoticeType'] ?? '');
            }
        }
        return $data_arr;
    }
}

/*Load all campaign status*/
if (!function_exists('all_states')) {
    function all_states($custom = true)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data2Count = $CI->db->query("SELECT countryID FROM srp_erp_company WHERE srp_erp_company.company_id = {$companyID}")->row_array();
        $countryFil=$data2Count['countryID'];
        if(!empty($countryFil)){
            $countrysFil =$countryFil;
        }
        else{
            $countrysFil='';
        }

        $CI->db->select("stateID,Description");
        $CI->db->from('srp_erp_statemaster');
        $CI->db->where('countyID', $countrysFil);
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

/*Load all countries for select2*/
if (!function_exists('load_countries_compare')) {
    function load_countries_compare($status = true)
    {

        $CI =& get_instance();

        $companyID = $CI->common_data['company_data']['company_id'];

        $dataCount = $CI->db->query("SELECT srp_erp_statemaster.countyID FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_regionmaster.stateID=srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_regionmaster.companyID = {$companyID}")->row_array();

        if(empty($dataCount)){

            $data2Count = $CI->db->query("SELECT countryID FROM srp_erp_company WHERE srp_erp_company.company_id = {$companyID}")->row_array();
            $countryFil=$data2Count['countryID'];
            if(!empty($countryFil)){
                $countrysFil =$countryFil;
            }
            else{
                $countrysFil='';
            }
        }
        else{
            $countrysFil=$dataCount['countyID'];
        }
        $data = $CI->db->query("SELECT countryID,countryShortCode,CountryDes FROM srp_erp_countrymaster WHERE countryID={$countrysFil}")->result_array();

        return $data;
    }
}

