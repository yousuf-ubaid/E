<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



if (!function_exists('loadstudentAction')) {
    function loadstudentAction()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $data = '<button class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button>';
        return $data;
    }
}

if (!function_exists('fetch_Stu_Name')) {
    function fetch_Stu_Name()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("stuId,name");
        $CI->db->FROM('srp_erp_sm_studentmaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('name');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_Name')/*'Select a Name'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['stuId'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_student_code')) {
    function fetch_student_code()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("stuID,student_code");
        $CI->db->FROM('srp_erp_sm_studentmaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('student_code');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_student_code')/*'Select a student_code'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['stuID'] ?? '')] = trim($row['student_code'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_category')) {
    function fetch_category()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("categoryId,category");
        $CI->db->FROM('srp_erp_sm_category');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('category');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_category')/*'Select a category'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['categoryId'] ?? '')] = trim($row['category'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_religion')) {
    function fetch_religion()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("religionId,religion");
        $CI->db->FROM('srp_erp_sm_religion');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('religion');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_religion')/*'Select a religion'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['religionId'] ?? '')] = trim($row['religion'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_nationality')) {
    function fetch_nationality()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("nationalityId,nationality");
        $CI->db->FROM('srp_erp_sm_nationality');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('nationality');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_nationality')/*'Select a nationality'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['nationalityId'] ?? '')] = trim($row['nationality'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_bloodgroup')) {
    function fetch_bloodgroup()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("bloodgroupId,bloodgroup");
        $CI->db->FROM('srp_erp_sm_bloodgroup');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('bloodgroup');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_bloodgroup')/*'Select a bloodgroup'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['bloodgroupId'] ?? '')] = trim($row['bloodgroup'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_admitted_year')) {
    function fetch_admitted_year()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("yearId,year");
        $CI->db->FROM('srp_erp_sm_year');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('year');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_admitted_year')/*'Select a admitted_year'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['yearId'] ?? '')] = trim($row['year'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_grade')) {
    function fetch_grade()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("gradeId,grade");
        $CI->db->FROM('srp_erp_sm_grade');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('grade');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_grade')/*'Select a grade'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['gradeId'] ?? '')] = trim($row['grade'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_group')) {
    function fetch_group()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("groupId,groupname");
        $CI->db->FROM('srp_erp_sm_group');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('groupname');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_group')/*'Select a group'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['groupId'] ?? '')] = trim($row['groupname'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_contact_person')) {
    function fetch_contact_person()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("parentID,contact_person,first_name");
        $CI->db->FROM('srp_erp_sm_parentmaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('contact_person');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_contact_person')/*'Select a contact_person'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['parentID'] ?? '')] = trim($row['contact_person'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_parental_status')) {
    function fetch_parental_status()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("parentID,maritial_status");
        $CI->db->FROM('srp_erp_sm_parentmaster');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('maritial_status');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_maritial_status')/*'Select a maritial_status'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['parentID'] ?? '')] = trim($row['maritial_status'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_country')) {
    function fetch_country()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("countryId,country");
        $CI->db->FROM('srp_erp_sm_country');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('country');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_country')/*'Select a country'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['countryId'] ?? '')] = trim($row['country'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_area')) {
    function fetch_area()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("areaId,area");
        $CI->db->FROM('srp_erp_sm_area');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('area');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_area')/*'Select a area'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['areaId'] ?? '')] = trim($row['area'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_journey_type')) {
    function fetch_journey_type()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("journeyId,journey_type");
        $CI->db->FROM('srp_erp_sm_journey_type');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('journey_type');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_journey_type')/*'Select a journey_type'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['journeyId'] ?? '')] = trim($row['journey_type'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_drop_location')) {
    function fetch_drop_location()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("locationId,location");
        $CI->db->FROM('srp_erp_sm_location');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('location');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_location')/*'Select a location'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['locationId'] ?? '')] = trim($row['location'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_travel_by')) {
    function fetch_travel_by()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("journeyId,journey_type");
        $CI->db->FROM('srp_erp_sm_journey_type');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('journey_type');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_travel_by')/*'Select a journey_type'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['journeyId'] ?? '')] = trim($row['journey_type'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('fetch_left_year')) {
    function fetch_left_year()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("yearId,year");
        $CI->db->FROM('srp_erp_sm_year');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('year');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_year')/*'Select a year'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['yearId'] ?? '')] = trim($row['year'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('all_students_drop')) {
    function all_students_drop($status = TRUE, $isDischarged = 0)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("stuID,student_code,name");
        $CI->db->from('srp_erp_sm_studentmaster');
        $CI->db->where('Erp_companyID', current_companyID());
        
        $student = $CI->db->get()->result_array();
        if ($status == TRUE) {
            $student_arr = array('' => $CI->lang->line('common_select_student'));/*'Select Student'*/
            if (isset($student)) {
                foreach ($student as $row) {
                    $student_arr[trim($row['stuID'] ?? '')] = trim($row['student_code'] ?? '') . ' | ' . trim($row['name'] ?? '');
                }
            }
        } else {
            $student_arr = $student;
        }

        return $student_arr;
    }
}
if (!function_exists('all_sponsor')) {
    function all_sponsor()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("sponsorId,sponsor");
        $CI->db->FROM('srp_erp_sm_sponsor');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('sponsor');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_sponsor')/*'Select a sponsor'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['sponsorId'] ?? '')] = trim($row['sponsor'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('all_status')) {
    function all_status()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("statusId,status");
        $CI->db->FROM('srp_erp_sm_status');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('status');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_status')/*'Select a status'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['statusId'] ?? '')] = trim($row['status'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('all_required')) {
    function all_required()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("requiredId,required");
        $CI->db->FROM('srp_erp_sm_required');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->order_by('required');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_a_required')/*'Select a required'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['requiredId'] ?? '')] = trim($row['required'] ?? '');
            }
        }
        return $data_arr;
    }
}
