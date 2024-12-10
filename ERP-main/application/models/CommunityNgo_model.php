<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class CommunityNgo_model extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
        $this->load->helper('community_ngo_helper');
    }

    /* Starting Hish */

    //Area Master Details

    function new_sub_division()
    {
        $divisionTypeCode = trim($this->input->post('divisionTypeCode') ?? '');
        $divisionNo = trim($this->input->post('divisionNo') ?? '');
        $stateID = trim($this->input->post('hd_sub_division_stateID') ?? '');
        $countyID = trim($this->input->post('hd_sub_division_countryID') ?? '');
        $description = trim($this->input->post('sub_division_description') ?? '');
        $district = trim($this->input->post('hd_sub_division_districtID') ?? '');
        $shortCode = trim($this->input->post('sub_division_shortCode') ?? '');

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('divisionTypeCode', $divisionTypeCode);
        $this->db->where('Description', $description);
        $this->db->where('type', 4);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (isset($isExist)) {
            return array('e', 'Sub Division is already Exists');
        }

        // check short Code already exist
        $this->db->select('stateID,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('divisionTypeCode', $divisionTypeCode);
        $this->db->where('shortCode', $shortCode);
        $this->db->where('type', 4);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (isset($isExist)) {
            return array('e', 'Short Code is already Exists.');
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 4;
        $data['masterID'] = $district;
        $data['divisionTypeCode'] = $divisionTypeCode;
        $data['divisionNo'] = $divisionNo;

        if ($stateID) {
            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Sub Division Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Sub Division Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Sub Division is created successfully.');
            } else {
                return array('e', 'Error in Sub Division Creating');
            }
        }
    }

    function new_beneficiary_division()
    {
        $stateID = trim($this->input->post('hd_division_stateID') ?? '');
        $countyID = trim($this->input->post('hd_division_countryID') ?? '');
        $description = trim($this->input->post('division_description') ?? '');
        $district = trim($this->input->post('hd_division_districtID') ?? '');
        $shortCode = trim($this->input->post('division_shortCode') ?? '');
        $divisionTypeCode = trim($this->input->post('divisionTypeCode') ?? '');

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('Description', $description);
        $this->db->where('divisionTypeCode', $divisionTypeCode);
        $this->db->where('type', 3);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $divisionisExist = $this->db->get()->row_array();
        if (isset($divisionisExist)) {
            return array('e', 'Division is already Exists');
        }

        // check short Code already exist
        $this->db->select('stateID,Description,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('shortCode', $shortCode);
        $this->db->where('divisionTypeCode', $divisionTypeCode);
        $this->db->where('type', 3);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $division_shortCodeisExist = $this->db->get()->row_array();
        if (isset($division_shortCodeisExist)) {
            return array('e', 'Short Code is already Exists.');
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 3;
        $data['masterID'] = $district;
        $data['divisionTypeCode'] = $divisionTypeCode;

        if ($stateID) {
            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Division Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Division Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Division is created successfully.');
            } else {
                return array('e', 'Error in Division Creating');
            }
        }

    }

    function load_ngo_area_setupDetail()
    {
        $stateID = trim($this->input->post('stateID') ?? '');
        $data = $this->db->query("select * FROM srp_erp_statemaster WHERE stateID = {$stateID} ")->row_array();
        return $data;
    }

    /*End Area*/

    /*Community Members*/

    function delete_community_members()
    {

        $companyID = current_companyID();
        $Com_MasterID = trim($this->input->post('Com_MasterID') ?? '');

        $isExistInFamily = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' ")->row('Com_MasterID');

        if (empty($isExistInFamily)) {

            $isExistInCustomer = $this->db->query("SELECT communityMemberID FROM srp_erp_customermaster WHERE companyID={$companyID} AND communityMemberID = '$Com_MasterID' ")->row('communityMemberID');

            if (empty($isExistInCustomer)) {

                $data = array('isDeleted' => 1,
                    'modifiedUserID' => current_userID(),
                    'modifiedUserName' => current_user(),
                    'modifiedDateTime' => date('Y-m-d'),
                    'modifiedPCID' => current_pc()
                );

                $this->db->where('Com_MasterID', $Com_MasterID);
                $this->db->update('srp_erp_ngo_com_communitymaster', $data);

                //member jobs
                $this->db->where('Com_MasterID', $Com_MasterID)->delete('srp_erp_ngo_com_memjobs');

                // member language
                $this->db->where('Com_MasterID', $Com_MasterID)->delete('srp_erp_ngo_com_memberlanguage');

                //member qualification
                $this->db->where('Com_MasterID', $Com_MasterID)->delete('srp_erp_ngo_com_qualifications');

                return array('error' => 0, 'message' => 'Successfully member deleted');

            } else {
                return array('error' => 1, 'message' => 'The member cannot be deleted! Member has already been used in customer details');
            }

        } else {
            return array('error' => 1, 'message' => 'The member cannot be deleted! Member has already been used in family details');
        }
    }

    function save_communityMember()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $Com_MasterID = trim($this->input->post('Com_MasterID') ?? '');

        $this->db->trans_start();
        $data['TitleID'] = trim($this->input->post('TitleID') ?? '');
        $data['CFullName'] = trim($this->input->post('CFullName') ?? '');
        $data['CName_with_initials'] = trim($this->input->post('CName_with_initials') ?? '');
        $data['OtherName'] = trim($this->input->post('OtherName') ?? '');
        if ($this->input->post('CNIC_No')) {
            $data['IsNIC_NoYes'] = 1;
        } else {
            $data['IsNIC_NoYes'] = 0;
        }
        $data['CNIC_No'] = trim($this->input->post('CNIC_No') ?? '');
        $data['Age'] = ($this->input->post('Age_hidden'));
        $CDOB = trim($this->input->post('CDOB') ?? '');
        $date_format_policy = date_format_policy();
        $data['CDOB'] = (!empty($CDOB)) ? input_format_date($CDOB, $date_format_policy) : null;

        $data['GenderID'] = trim($this->input->post('GenderID') ?? '');
        $data['BloodGroupID'] = trim($this->input->post('BloodGroupID') ?? '');
        $data['jammiyahDivisionID'] = trim($this->input->post('jammiyahDivisionID') ?? '');
        $data['districtDivisionID'] = trim($this->input->post('districtDivisionID') ?? '');
        $data['districtID'] = trim($this->input->post('districtID') ?? '');
        $data['provinceID'] = trim($this->input->post('provinceID') ?? '');
        $data['countyID'] = trim($this->input->post('countyID') ?? '');
        $data['RegionID'] = trim($this->input->post('RegionID') ?? '');
        $data['HouseNo'] = trim($this->input->post('HouseNo') ?? '');
        $data['GS_Division'] = trim($this->input->post('GS_Division') ?? '');
        $data['GS_No'] = trim($this->input->post('GS_No') ?? '');
        $data['P_Address'] = trim($this->input->post('P_Address') ?? '');
        $data['C_Address'] = trim($this->input->post('C_Address') ?? '');
        $data['TP_Mobile'] = trim($this->input->post('TP_Mobile') ?? '');
        $data['CountryCodePrimary'] = trim($this->input->post('CountryCodePrimary') ?? '');
        $data['AreaCodePrimary'] = trim($this->input->post('AreaCodePrimary') ?? '');
        $data['TP_home'] = trim($this->input->post('TP_home') ?? '');
        $data['CountryCodeSecondary'] = trim($this->input->post('CountryCodeSecondary') ?? '');
        $data['AreaCodeSecondary'] = trim($this->input->post('AreaCodeSecondary') ?? '');
        $data['EmailID'] = trim($this->input->post('EmailID') ?? '');
        $data['IsAbroad'] = trim($this->input->post('IsAbroad') ?? '');
        $data['CountryOfResidentID'] = trim($this->input->post('CountryOfResidentID') ?? '');
        $data['CurrentStatus'] = trim($this->input->post('CurrentStatus') ?? '');
        $data['IsSchoolCompleted'] = trim($this->input->post('IsSchoolCompleted') ?? '');
        $data['CompletedYear'] = trim($this->input->post('CompletedYear') ?? '');
        $data['SchoolAttended'] = trim($this->input->post('SchoolAttended') ?? '');
        $data['isVoter'] = trim($this->input->post('isVoter') ?? '');
        $data['isConverted'] = trim($this->input->post('isConverted') ?? '');
        $data['ConvertedYear'] = trim($this->input->post('ConvertedYear') ?? '');
        $data['ConvertedPlace'] = trim($this->input->post('ConvertedPlace') ?? '');

        if ($Com_MasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('Com_MasterID', trim($this->input->post('Com_MasterID') ?? ''));
            $update = $this->db->update('srp_erp_ngo_com_communitymaster', $data);
            if ($update) {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return array('e', 'Member Update Failed '
                    );

                } else {
                    $this->db->trans_commit();

                    return array('s', 'Member Updated Successfully.', $Com_MasterID);
                }
            }
        } else {

            $company_code = $this->common_data['company_data']['company_code'];
            $serial = $this->db->query("select IF ( isnull(MAX(SerialNo)), 1, (MAX(SerialNo) + 1) ) AS SerialNo FROM `srp_erp_ngo_com_communitymaster` WHERE companyID={$companyID}")->row_array();

            $data['SerialNo'] = $serial['SerialNo'];
            $data['MemberCode'] = 'MEM';;
            $data['MemberCode'] = ($company_code . '/' . 'MEM' . str_pad($data['SerialNo'], 6,
                    '0', STR_PAD_LEFT));

            $data['companyID'] = $companyID;
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_com_communitymaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return array('e', 'Member Save Failed ', $last_id);
            } else {
                $this->db->trans_commit();

                return array('s', 'Member Saved Successfully.', $last_id);
            }
        }
    }

    function save_communityMemberStatus()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $Com_MasterID = trim($this->input->post('Com_MasterID') ?? '');

        $this->db->trans_start();
        $deactivatedDate = trim($this->input->post('deactivatedDate') ?? '');
        $date_format_policy = date_format_policy();
        $data['deactivatedDate'] = (!empty($deactivatedDate)) ? input_format_date($deactivatedDate, $date_format_policy) : null;
        $data['DeactivatedFor'] = trim($this->input->post('DeactivatedFor') ?? '');
        $data['isActive'] = trim($this->input->post('isComActiveId') ?? '');
        $data['deactivatedComment'] = trim($this->input->post('deactivatedComment') ?? '');

        if ($Com_MasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('Com_MasterID', trim($this->input->post('Com_MasterID') ?? ''));
            $update = $this->db->update('srp_erp_ngo_com_communitymaster', $data);
            if ($update) {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return array('e', 'Inactivated Failed'
                    );

                } else {
                    $this->db->trans_commit();

                    return array('s', 'Successfully Inactivated.', $Com_MasterID);
                }
            }
        } else {
        }
    }

    function load_member()
    {
        $convertFormat = convert_date_format_sql();

        $Com_MasterID = $this->input->post('Com_MasterID');
        $data = $this->db->query("select * ,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,DATE_FORMAT(deactivatedDate,'{$convertFormat}') AS deactivatedDate from srp_erp_ngo_com_communitymaster WHERE Com_MasterID={$Com_MasterID} ")->row_array();

        return $data;
    }

    /*Start Member Qualification*/
    function saveQualification()
    {
        $Com_MasterID = $this->input->post('Com_MasterID');
        $DegreeID = $this->input->post('DegreeID');
        $UniversityID = $this->input->post('UniversityID');
        $gradeComID = $this->input->post('gradeComID');
        $CurrentlyReading = $this->input->post('CurrentlyReading');
        $Year = $this->input->post('Year');
        $Remarks = $this->input->post('Remarks');

        $data = array(
            'Com_MasterID' => $Com_MasterID,
            'DegreeID' => $DegreeID,
            'UniversityID' => $UniversityID,
            'gradeComID' => $gradeComID,
            'Remarks' => $Remarks,
            'CurrentlyReading' => $CurrentlyReading,
            'Year' => $Year,
            'companyID' => current_companyID(),
            'createdPCID' => current_pc(),
            'CreatedUserName' => current_user(),
            'createdUserID' => current_userID(),
            'createdDateTime' => current_date()
        );

        $this->db->insert('srp_erp_ngo_com_qualifications', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Record inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function updateQualification()
    {
        $hidden_id = $this->input->post('hidden-id');
        $DegreeID = $this->input->post('DegreeID');
        $UniversityID = $this->input->post('UniversityID');
        $gradeComID = $this->input->post('gradeComID');
        $CurrentlyReading = $this->input->post('CurrentlyReading');
        $Year = $this->input->post('Year');
        $Remarks = $this->input->post('Remarks');

        $data = array(
            'DegreeID' => $DegreeID,
            'UniversityID' => $UniversityID,
            'gradeComID' => $gradeComID,
            'Remarks' => $Remarks,
            'CurrentlyReading' => $CurrentlyReading,
            'Year' => $Year,
            'companyID' => current_companyID(),
            'ModifiedPCID' => current_pc(),
            'ModifiedUserName' => current_user(),
            'modifiedUserID' => current_userID()
        );

        $this->db->where('QualificationID', $hidden_id)->update('srp_erp_ngo_com_qualifications', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Record updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function deleteQualification()
    {
        $hidden_id = $this->input->post('hidden-id');

        $this->db->where('QualificationID', $hidden_id)->delete('srp_erp_ngo_com_qualifications');
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Record deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }
    }

    /*Start Member Occupation*/
    function saveOccupation()
    {
        $Com_MasterID = $this->input->post('Com_MasterID');
        $OccTypeID = $this->input->post('OccTypeID');
        $Address = $this->input->post('Address');
        $DFrom = $this->input->post('DateFrom');
        $date_format_policy = date_format_policy();
        $DateFrom = (!empty($DFrom)) ? input_format_date($DFrom, $date_format_policy) : null;

        $DTo = trim($this->input->post('DateTo') ?? '');
        $date_format_policy = date_format_policy();
        $DateTo = (!empty($DTo)) ? input_format_date($DTo, $date_format_policy) : null;
        $companyID = current_companyID();

        if ($OccTypeID == 1) {
            $school = $this->input->post('schoolComID');
            $schoolTypeID = $this->input->post('schoolTypeID');
            $Grade = $this->input->post('gradeComID');
            $Medium = $this->input->post('MediumID');
            $isActive = $this->input->post('isActive');

            if ($isActive == 1) {
                $query = $this->db->query("UPDATE srp_erp_ngo_com_memjobs SET isActive = '0' WHERE companyID='" . $companyID . "'  AND Com_MasterID = '" . $Com_MasterID . "'");
            }

            $data = array(
                'Com_MasterID' => $Com_MasterID,
                'OccTypeID' => $OccTypeID,
                'schoolComID' => $school,
                'schoolTypeID' => $schoolTypeID,
                'gradeComID' => $Grade,
                'LanguageID' => $Medium,
                'Address' => $Address,
                'DateFrom' => $DateFrom,
                'DateTo' => $DateTo,
                'isActive' => $isActive,
                'companyID' => current_companyID(),
                'createdPCID' => current_pc(),
                'CreatedUserName' => current_user(),
                'createdUserID' => current_userID(),
                'createdDateTime' => current_date()
            );

        } else {
            $JobCategoryID = $this->input->post('JobCategoryID');
            $specializationID = $this->input->post('specializationID');
            $jobDescription = $this->input->post('jobDescription');
            $WorkingPlace = $this->input->post('WorkingPlace');
            $isPrimary = $this->input->post('isPrimary');

            $data = array(
                'Com_MasterID' => $Com_MasterID,
                'OccTypeID' => $OccTypeID,
                'JobCategoryID' => $JobCategoryID,
                'specializationID' => $specializationID,
                'jobDescription' => $jobDescription,
                'WorkingPlace' => $WorkingPlace,
                'Address' => $Address,
                'DateFrom' => $DateFrom,
                'DateTo' => $DateTo,
                'isPrimary' => $isPrimary,
                'companyID' => current_companyID(),
                'createdPCID' => current_pc(),
                'CreatedUserName' => current_user(),
                'createdUserID' => current_userID(),
                'createdDateTime' => current_date()
            );
        }

        $this->db->insert('srp_erp_ngo_com_memjobs', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Record inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function check_primaryOcc_exist()
    {
        $Com_MasterID = $this->input->post('Com_MasterID');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT MemJobID FROM srp_erp_ngo_com_memjobs WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' AND isPrimary = '1' ")->row('MemJobID');

        if (empty($isExist)) {
            return 'Not_exist';
        } else {
            return 'Exist';
        }
    }

    function updateOccupation()
    {
        $hidden_id = $this->input->post('hidden-id-occ');
        $Com_MasterID = $this->input->post('Com_MasterID');
        $OccTypeID = $this->input->post('OccTypeID');
        $Address = $this->input->post('Address');

        $DFrom = $this->input->post('DateFrom');
        $date_format_policy = date_format_policy();
        $DateFrom = (!empty($DFrom)) ? input_format_date($DFrom, $date_format_policy) : null;

        $DTo = trim($this->input->post('DateTo') ?? '');
        $date_format_policy = date_format_policy();
        $DateTo = (!empty($DTo)) ? input_format_date($DTo, $date_format_policy) : null;
        $companyID = current_companyID();

        if ($OccTypeID == 1) {
            $school = $this->input->post('schoolComID');
            $schoolTypeID = $this->input->post('schoolTypeID');
            $Grade = $this->input->post('gradeComID');
            $Medium = $this->input->post('MediumID');
            $isActive = $this->input->post('isActive');

            if ($isActive == 1) {
                $query = $this->db->query("UPDATE srp_erp_ngo_com_memjobs SET isActive = '0' WHERE companyID='" . $companyID . "'  AND Com_MasterID = '" . $Com_MasterID . "'");
            }

            $data = array(
                'OccTypeID' => $OccTypeID,
                'schoolComID' => $school,
                'schoolTypeID' => $schoolTypeID,
                'gradeComID' => $Grade,
                'LanguageID' => $Medium,
                'Address' => $Address,
                'DateFrom' => $DateFrom,
                'DateTo' => $DateTo,
                'isActive' => $isActive,
                'companyID' => current_companyID(),
                'ModifiedPCID' => current_pc(),
                'ModifiedUserName' => current_user(),
                'modifiedUserID' => current_userID()
            );
        } else {
            $JobCategoryID = $this->input->post('JobCategoryID');
            $specializationID = $this->input->post('specializationID');
            $jobDescription = $this->input->post('jobDescription');
            $WorkingPlace = $this->input->post('WorkingPlace');
            $isPrimary = $this->input->post('isPrimary');

            $data = array(
                'OccTypeID' => $OccTypeID,
                'JobCategoryID' => $JobCategoryID,
                'specializationID' => $specializationID,
                'jobDescription' => $jobDescription,
                'WorkingPlace' => $WorkingPlace,
                'Address' => $Address,
                'DateFrom' => $DateFrom,
                'DateTo' => $DateTo,
                'isPrimary' => $isPrimary,
                'companyID' => current_companyID(),
                'ModifiedPCID' => current_pc(),
                'ModifiedUserName' => current_user(),
                'modifiedUserID' => current_userID()
            );
        }

        $this->db->where('MemJobID', $hidden_id)->update('srp_erp_ngo_com_memjobs', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Record updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function deleteOccupation()
    {
        $hidden_id = $this->input->post('hidden-id');

        $this->db->where('MemJobID', $hidden_id)->delete('srp_erp_ngo_com_memjobs');
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Record deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }
    }

    /*End Member Occupation*/

    /*upload member image*/
    function member_image_upload()
    {
        $this->db->trans_start();
        $output_dir = "uploads/NGO/communitymemberImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/communitymemberImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Member_' . trim($this->input->post('MemberID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['CImage'] = $fileName;

        $this->db->where('Com_MasterID', trim($this->input->post('MemberID') ?? ''));
        $this->db->update('srp_erp_ngo_com_communitymaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed.");
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded Successfully.');
        }
    }
    //end of community Master

    /*Starting of Collection setup*/

    function save_collection_setup()
    {
        $CollectionMasterID = $this->input->post('CollectionMasterID');

        $this->db->trans_start();

        $CollectionDes = $this->input->post('CollectionDes');
        $CollectionTypeID = $this->input->post('CollectionTypeID');
        $genderID = $this->input->post('genderID');
        $revenueGLAutoID = $this->input->post('revenueGLAutoID');
        $CashGL_des = $this->input->post('CashGL_des');
        $receivableAutoID = $this->input->post('receivableAutoID');
        $receivableGL_des = $this->input->post('receivableGL_des');

        if ($CollectionMasterID) {

            foreach ($revenueGLAutoID as $key => $gl_code) {

                $data[$key]['CollectionDes'] = $CollectionDes[$key];
                $data[$key]['CollectionTypeID'] = $CollectionTypeID[$key];
                $data[$key]['genderID'] = $genderID[$key];

                $gl_Cash = explode('|', $CashGL_des[$key]);
                $data[$key]['revenueGLAutoID'] = $revenueGLAutoID[$key];
                $data[$key]['revenueSystemGLCode'] = trim($gl_Cash[0] ?? '');
                $data[$key]['revenueGLCode'] = trim($gl_Cash[1] ?? '');
                $data[$key]['revenueGLDescription'] = trim($gl_Cash[2] ?? '');
                $data[$key]['revenueGLType'] = trim($gl_Cash[3] ?? '');

                $gl_receivable = explode('|', $receivableGL_des[$key]);
                $data[$key]['receivableAutoID'] = $receivableAutoID[$key];
                $data[$key]['receivableSystemGLCode'] = trim($gl_receivable[0] ?? '');
                $data[$key]['receivableGLAccount'] = trim($gl_receivable[1] ?? '');
                $data[$key]['receivableDescription'] = trim($gl_receivable[2] ?? '');
                $data[$key]['receivableType'] = trim($gl_receivable[3] ?? '');

                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$key]['modifiedPCID'] = current_pc();
                $data[$key]['modifiedUserID'] = current_userID();
                $data[$key]['modifiedUserName'] = current_date();
                $data[$key]['modifiedDateTime'] = current_user();


                $this->db->where('CollectionMasterID', trim($CollectionMasterID));
                $this->db->update('srp_erp_ngo_com_collectionmaster', $data[$key]);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Collection setup : Updated Failed ');
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Collection setup :  Updated Successfully.');
                }
            }

        } else {

            foreach ($revenueGLAutoID as $key => $gl_code) {

                $data[$key]['CollectionDes'] = $CollectionDes[$key];
                $data[$key]['CollectionTypeID'] = $CollectionTypeID[$key];
                $data[$key]['genderID'] = $genderID[$key];

                $gl_Cash = explode('|', $CashGL_des[$key]);
                $data[$key]['revenueGLAutoID'] = $revenueGLAutoID[$key];
                $data[$key]['revenueSystemGLCode'] = trim($gl_Cash[0] ?? '');
                $data[$key]['revenueGLCode'] = trim($gl_Cash[1] ?? '');
                $data[$key]['revenueGLDescription'] = trim($gl_Cash[2] ?? '');
                $data[$key]['revenueGLType'] = trim($gl_Cash[3] ?? '');

                $gl_receivable = explode('|', $receivableGL_des[$key]);
                $data[$key]['receivableAutoID'] = $receivableAutoID[$key];
                $data[$key]['receivableSystemGLCode'] = trim($gl_receivable[0] ?? '');
                $data[$key]['receivableGLAccount'] = trim($gl_receivable[1] ?? '');
                $data[$key]['receivableDescription'] = trim($gl_receivable[2] ?? '');
                $data[$key]['receivableType'] = trim($gl_receivable[3] ?? '');

                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$key]['createdPCID'] = current_pc();
                $data[$key]['createdUserID'] = current_userID();
                $data[$key]['createdDateTime'] = current_date();
                $data[$key]['createdUserName'] = current_user();
                $data[$key]['timestamp'] = current_date();

            }

            $this->db->insert_batch('srp_erp_ngo_com_collectionmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Collection setup : Saved Failed ');
            } else {
                $this->db->trans_commit();
                return array('s', 'Collection setup :  Saved Successfully.');
            }
        }
    }

    function delete_collection_entry()
    {
        $isUsed = $this->checkCollectionIsUsed();

        if (empty($isUsed)) {
            $output = $this->db->delete('srp_erp_ngo_com_collectionmaster', array('CollectionMasterID' => trim($this->input->post('CollectionMasterID') ?? '')));
            if ($output) {
                return array('s', 'Collection Setup : Deleted Successfully.');
            } else {
                return array('e', 'Error in deleting process');
            }
        } else {
            return array('e', 'Collection Setup : Cannot be deleted.');
        }
    }

    function checkCollectionIsUsed()
    {
        $query = $this->db->where('CollectionMasterID', ($this->input->post('CollectionMasterID')))
            ->select('CollectionMasterID')
            ->from('srp_erp_ngo_com_collectiondetails')
            ->get();
        return $query->row();
    }

    function fetch_collection_details()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_com_collectiondetails');
        $this->db->where('CollectionMasterID', $this->input->post('CollectionMasterID'));
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $CollectionDetailID = $this->input->post('CollectionDetailID');

        $isExist = $this->db->query("SELECT CollectionDetailID FROM srp_erp_ngo_com_collectionmemsetup WHERE companyID={$companyID} AND CollectionDetailID = '$CollectionDetailID' ")->row('CollectionDetailID');

        if (isset($isExist)) {
            return array('e', 'Collection Setup : Cannot be deleted.');
        } else {
            $output = $this->db->delete('srp_erp_ngo_com_collectiondetails', array('CollectionDetailID' => trim($this->input->post('CollectionDetailID') ?? '')));
            if ($output) {
                return array('s', 'Collection Setup : Deleted Successfully.');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    function save_collection_detail()
    {

        $CollectionDetailID = $this->input->post('CollectionDetailID');

        $this->db->trans_start();

        $CollectionMasterID = $this->input->post('CollectionMasterID');
        $CollectionType = $this->input->post('CollectionType');

        $companyFinanceYearID = $this->input->post('companyFinanceYearID');
        $companyFinanceYear = $this->input->post('companyFinanceYear');
        $PeriodTypeID = $this->input->post('PeriodTypeID');
        $PeriodType_des = $this->input->post('PeriodType_des');
        $transactionCurrencyID = $this->input->post('transactionCurrencyID');
        $transactionCurrency = $this->input->post('transactionCurrency');
        $segmentID = $this->input->post('segmentID');
        $segment_des = $this->input->post('segment_des');

        if ($CollectionType != 3) {
            $Amount = $this->input->post('Amount');
            $data['Amount'] = $Amount;
        }
        $data['CollectionMasterID'] = $CollectionMasterID;

        $data['companyFinanceYearID'] = $companyFinanceYearID;
        $data['companyFinanceYear'] = $companyFinanceYear;
        $pt_des = explode('|', $PeriodType_des);
        $data['PeriodTypeID'] = $PeriodTypeID;
        $data['PeriodDescription'] = trim($pt_des[0] ?? '');

        $data['transactionCurrencyID'] = $transactionCurrencyID;
        $tc_des = explode('|', $transactionCurrency);
        $data['transactionCurrency'] = trim($tc_des[0] ?? '');
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);

        $seg_des = explode('|', $segment_des);
        $data['segmentID'] = $segmentID;
        $data['segmentCode'] = trim($seg_des[0] ?? '');
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        if ($CollectionDetailID) {

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedUserName'] = current_date();
            $data['modifiedDateTime'] = current_user();

            $this->db->where('CollectionDetailID', trim($CollectionDetailID));
            $this->db->update('srp_erp_ngo_com_collectiondetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Collection setup : Updated Failed ', $CollectionDetailID);
            } else {
                $this->db->trans_commit();
                return array('s', 'Collection setup :  Updated Successfully.', $CollectionDetailID);
            }

        } else {

            $companyID = $this->common_data['company_data']['company_id'];
            $isExist = $this->db->query("SELECT CollectionDetailID FROM srp_erp_ngo_com_collectiondetails WHERE companyID={$companyID} AND CollectionMasterID = {$CollectionMasterID} AND companyFinanceYearID = '$companyFinanceYearID' ")->row('CollectionDetailID');

            if (isset($isExist)) {
                return array('e', 'Collection setup : Already available');
            } else {

                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = current_date();
                $data['createdUserName'] = current_user();
                $data['timestamp'] = current_date();

                $this->db->insert('srp_erp_ngo_com_collectiondetails', $data);
                $last_id = $this->db->insert_id();

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Collection setup : Saved Failed ', $last_id);
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Collection setup :  Saved Successfully.', $last_id);
                }
            }
        }
    }

    /*Customer config*/
    function save_customer_config()
    {
        try {
            $ConfigAutoID = $this->input->post('ConfigAutoID');
            $liability = fetch_gl_account_desc(trim($this->input->post('receivableAutoID') ?? ''));
            $data['receivableAutoID'] = $liability['GLAutoID'];
            $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
            $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
            $data['receivableDescription'] = $liability['GLDescription'];
            $data['receivableType'] = $liability['subCategory'];
            $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');

            $datetime = date('Y-m-d');
            if (!$ConfigAutoID) {
                /*Add*/

                $companyID = current_companyID();;
                $isExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_customerconfig WHERE companyID={$companyID} ")->row_array();

                if (!isset($isExist)) {
                    $data['companyID'] = current_companyID();
                    $data['createdPCID'] = current_pc();
                    $data['createdUserID'] = current_userID();
                    $data['createdDateTime'] = $datetime;
                    $data['createdUserName'] = current_user();
                    $data['timestamp'] = $datetime;

                    $result = $this->db->insert('srp_erp_ngo_com_customerconfig', $data);

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        return array('error' => 1, 'message' => 'Error while updating');

                    } else {
                        $this->db->trans_commit();
                        return array('error' => 0, 'message' => 'Successfully created');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Already available');
                }

            } else {
                /* Update */
                $data['modifiedUserID'] = current_userID();
                $data['modifiedUserName'] = current_user();
                $data['modifiedDateTime'] = $datetime;
                $data['modifiedPCID'] = current_pc();
                $this->db->where('ConfigAutoID', $this->input->post('ConfigAutoID'));
                $result = $this->db->update('srp_erp_ngo_com_customerconfig', $data);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('error' => 1, 'message' => 'Error while updating');

                } else {
                    $this->db->trans_commit();
                    return array('error' => 0, 'message' => 'Successfully updated');
                }
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => 'Error while updating');
        }
    }

    function save_collection_members()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $CollectionMasterID = $this->input->post('CollectionMasterID');
        $CollectionDetailID = $this->input->post('Collection_DetailID');

        $Com_MasterIDArr = $this->input->post('Com_MasterID');
        $Amount = $this->input->post('Amount');

        if (empty($Com_MasterIDArr)) {
        } else {

            //setup details
            $CollectionDetails = $this->db->query("SELECT * FROM srp_erp_ngo_com_collectiondetails INNER JOIN srp_erp_ngo_com_financialperiodtypes ON srp_erp_ngo_com_financialperiodtypes.PeriodTypeID = srp_erp_ngo_com_collectiondetails.PeriodTypeID WHERE srp_erp_ngo_com_collectiondetails.companyID = {$companyID} AND srp_erp_ngo_com_collectiondetails.CollectionMasterID = {$CollectionMasterID} AND srp_erp_ngo_com_collectiondetails.CollectionDetailID = {$CollectionDetailID}")->row_array();

            $companyFinanceYearID = $CollectionDetails{'companyFinanceYearID'};
            $companyFinanceYear = $CollectionDetails{'companyFinanceYear'};
            $PeriodTypeID = $CollectionDetails{'PeriodTypeID'};
            $transactionCurrencyID = $CollectionDetails{'transactionCurrencyID'};
            $transactionCurrency = $CollectionDetails{'transactionCurrency'};
            $transactionCurrencyDecimalPlaces = $CollectionDetails{'transactionCurrencyDecimalPlaces'};
            $segmentID = $CollectionDetails{'segmentID'};
            $segmentCode = $CollectionDetails{'segmentCode'};

            $a = 0;
            foreach ($Com_MasterIDArr as $key => $Com_MasterID) {

                $isCustomerExist = $this->db->query("SELECT * FROM srp_erp_customermaster WHERE companyID={$companyID} AND communityMemberID = '$Com_MasterIDArr[$key]' ")->row_array();

                if (isset($isCustomerExist)) {
                    $customerAutoID = $isCustomerExist['customerAutoID'];
                    $customerSystemCode = $isCustomerExist['customerSystemCode'];
                    $customerName = $isCustomerExist['customerName'];
                    $customerAddress = $isCustomerExist['customerAddress1'] . ' ' . $isCustomerExist['customerAddress2'];
                    $customerTelephone = $isCustomerExist['customerTelephone'];
                    $customerEmail = $isCustomerExist['customerEmail'];

                } else {

                    //community member details
                    $MemberDetails = $this->db->query("SELECT * FROM srp_erp_ngo_com_communitymaster WHERE srp_erp_ngo_com_communitymaster.companyID = {$companyID} AND srp_erp_ngo_com_communitymaster.Com_MasterID = {$Com_MasterIDArr[$key]} ")->row_array();
                    $MemberCode = $MemberDetails{'MemberCode'};
                    $CName_with_initials = $MemberDetails{'CName_with_initials'};
                    $P_Address = $MemberDetails{'P_Address'};
                    $C_Address = $MemberDetails{'C_Address'};

                    $Membercountry = $this->db->query("SELECT CountryDes FROM srp_erp_countrymaster WHERE countryID = {$MemberDetails{'countyID'}} ")->row_array();
                    $Country = $Membercountry{'CountryDes'};

                    $Telephone = $MemberDetails{'TP_Mobile'};
                    $EmailID = $MemberDetails{'EmailID'};

                    //customer acc details
                    $CusConfigDetails = $this->db->query("SELECT * FROM srp_erp_ngo_com_customerconfig WHERE srp_erp_ngo_com_customerconfig.companyID = {$companyID} AND isActive = 1 ")->row_array();
                    $customer_receivableAutoID = $CusConfigDetails{'receivableAutoID'};
                    $customer_receivableSystemGLCode = $CusConfigDetails{'receivableSystemGLCode'};
                    $customer_receivableGLAccount = $CusConfigDetails{'receivableGLAccount'};
                    $customer_receivableDescription = $CusConfigDetails{'receivableDescription'};
                    $customer_receivableType = $CusConfigDetails{'receivableType'};
                    $partyCategoryID = $CusConfigDetails{'partyCategoryID'};

                    // save member as customer
                    $this->load->library('sequence');
                    $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
                    $data['secondaryCode'] = trim($MemberCode);
                    $data['customerName'] = trim($CName_with_initials);
                    $data['customerTelephone'] = trim($Telephone);
                    $data['customerEmail'] = trim($EmailID);
                    $data['customerAddress1'] = trim($C_Address);
                    $data['customerAddress2'] = trim($P_Address);
                    $data['customerCountry'] = trim($Country);
                    $data['partyCategoryID'] = trim($partyCategoryID);
                    $data['receivableAutoID'] = trim($customer_receivableAutoID);
                    $data['receivableSystemGLCode'] = trim($customer_receivableSystemGLCode);
                    $data['receivableGLAccount'] = trim($customer_receivableGLAccount);
                    $data['receivableDescription'] = trim($customer_receivableDescription);
                    $data['receivableType'] = trim($customer_receivableType);
                    $data['customerCurrencyID'] = trim($transactionCurrencyID);
                    $data['customerCurrency'] = trim($transactionCurrency);
                    $data['customerCurrencyDecimalPlaces'] = trim($transactionCurrencyDecimalPlaces);
                    $data['communityMemberID'] = trim($Com_MasterIDArr[$key]);
                    $data['isActive'] = 1;
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];

                    $this->db->insert('srp_erp_customermaster', $data);

                    $customerAutoID = $this->db->insert_id();
                    $customerSystemCode = $data['customerSystemCode'];
                    $customerName = $data['customerName'];
                    $customerAddress = $data['customerAddress1'] . ' ' . $data['customerAddress2'];
                    $customerTelephone = $data['customerTelephone'];
                    $customerEmail = $data['customerEmail'];
                }

                // company finance year details
                $company_finance_year = $this->db->query("SELECT * FROM srp_erp_companyfinanceyear WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} AND isActive = 1 AND isCurrent = 1 ")->row_array();

                $ValidPeriodArr = array();

                if ($PeriodTypeID == '1') { //Monthly

                    //finance year periods
                    $company_finance_year_period = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} ORDER BY companyFinancePeriodID ASC  ")->result_array();

                    foreach ($company_finance_year_period as $row_periods) {
                        $ValidPeriodArr[] = array(
                            'companyFinancePeriodID' => $row_periods['companyFinancePeriodID'],
                            'invoiceDate' => $row_periods['dateFrom'],
                            'customerInvoiceDate' => $row_periods['dateFrom'],
                            'invoiceDueDate' => $row_periods['dateTo'],
                        );
                    }

                } else if ($PeriodTypeID == '2') { //Quarterly

                    $company_finance_year_period = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} ORDER BY companyFinancePeriodID ASC  ")->result_array();

                    foreach ($company_finance_year_period as $row_periods) {

                        $MonthID = date('m', strtotime($row_periods['dateFrom']));

                        if ($MonthID == 1 || $MonthID == 4 || $MonthID == 7 || $MonthID == 10) {
                            $ValidPeriodArr[] = array(
                                'companyFinancePeriodID' => $row_periods['companyFinancePeriodID'],
                                'invoiceDate' => $row_periods['dateFrom'],
                                'customerInvoiceDate' => $row_periods['dateFrom'],
                                'invoiceDueDate' => $row_periods['dateTo'],
                            );
                        }
                    }

                } else if ($PeriodTypeID == '3') { //Twice a Year

                    $company_finance_year_period = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} ORDER BY companyFinancePeriodID ASC  ")->result_array();

                    foreach ($company_finance_year_period as $row_periods) {

                        $MonthID = date('m', strtotime($row_periods['dateFrom']));

                        if ($MonthID == 1 || $MonthID == 6) {
                            $ValidPeriodArr[] = array(
                                'companyFinancePeriodID' => $row_periods['companyFinancePeriodID'],
                                'invoiceDate' => $row_periods['dateFrom'],
                                'customerInvoiceDate' => $row_periods['dateFrom'],
                                'invoiceDueDate' => $row_periods['dateTo'],
                            );
                        }
                    }
                } else if ($PeriodTypeID == '6') { //One time

                    $company_finance_year_period = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} ORDER BY companyFinancePeriodID ASC LIMIT 1")->row_array();

                    $ValidPeriodArr[] = array(
                        'companyFinancePeriodID' => $company_finance_year_period['companyFinancePeriodID'],
                        'invoiceDate' => $this->common_data['current_date'],
                        'customerInvoiceDate' => $this->common_data['current_date'],
                        'invoiceDueDate' => $company_finance_year['endingDate'],
                    );

                } else { //yearly

                    $company_finance_year_period = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} ORDER BY companyFinancePeriodID ASC LIMIT 1 ")->row_array();

                    $ValidPeriodArr[] = array(
                        'companyFinancePeriodID' => $company_finance_year_period['companyFinancePeriodID'],
                        'invoiceDate' => $company_finance_year['beginingDate'],
                        'customerInvoiceDate' => $company_finance_year['beginingDate'],
                        'invoiceDueDate' => $company_finance_year['endingDate'],
                    );
                }

                if (!empty($ValidPeriodArr)) {

                    foreach ($ValidPeriodArr as $ValidPeriodID) {
                        //master details
                        $CollectionMaster = $this->db->query("SELECT * FROM srp_erp_ngo_com_collectionmaster INNER JOIN srp_erp_ngo_com_collectiontypes ON srp_erp_ngo_com_collectiontypes.TypeID = srp_erp_ngo_com_collectionmaster.CollectionTypeID  WHERE srp_erp_ngo_com_collectionmaster.companyID = {$companyID} AND srp_erp_ngo_com_collectionmaster.CollectionMasterID = {$CollectionMasterID} ")->row_array();

                        if (!empty($CollectionMaster)) {

                            $revenueGLAutoID = $CollectionMaster{'revenueGLAutoID'};
                            $revenueSystemGLCode = $CollectionMaster{'revenueSystemGLCode'};
                            $revenueGLCode = $CollectionMaster{'revenueGLCode'};
                            $revenueGLDescription = $CollectionMaster{'revenueGLDescription'};
                            $revenueGLType = $CollectionMaster{'revenueGLType'};

                            $receivableAutoID = $CollectionMaster{'receivableAutoID'};
                            $receivableSystemGLCode = $CollectionMaster{'receivableSystemGLCode'};
                            $receivableGLAccount = $CollectionMaster{'receivableGLAccount'};
                            $receivableDescription = $CollectionMaster{'receivableDescription'};
                            $receivableType = $CollectionMaster{'receivableType'};

                            $Description = $CollectionMaster{'Description'};
                            $CollectionDes = $CollectionMaster{'CollectionDes'};

                            $invoice_data_exists = array(

                                "invoiceType" => 'Direct',
                                "documentID" => 'CINV',
                                "invoiceDate" => $ValidPeriodID['invoiceDate'],
                                "invoiceDueDate" => $ValidPeriodID['invoiceDueDate'],
                                "customerInvoiceDate" => $ValidPeriodID['customerInvoiceDate'],
                                "invoiceNarration" => $CollectionDes,
                                "companyFinanceYearID" => $companyFinanceYearID,
                                "companyFinanceYear" => $companyFinanceYear,
                                "FYBegin" => $company_finance_year['beginingDate'],
                                "FYEnd" => $company_finance_year['endingDate'],
                                "companyFinancePeriodID" => $ValidPeriodID['companyFinancePeriodID'],
                                "customerID" => $customerAutoID,
                                "customerSystemCode" => $customerSystemCode,
                                "customerName" => $customerName,
                                "customerAddress" => $customerAddress,
                                "customerTelephone" => $customerTelephone,
                                "customerEmail" => $customerEmail,
                                "customerReceivableAutoID" => $receivableAutoID,
                                "transactionCurrencyID" => $transactionCurrencyID,
                                "customerCurrencyID" => $transactionCurrencyID,
                                "segmentID" => $segmentID,
                                "companyID" => $this->common_data['company_data']['company_id'],

                            );

                            $query_exists = $this->db->get_where('srp_erp_customerinvoicemaster', $invoice_data_exists);
                            $res_query_check_invoice = $query_exists->row();

                            if (empty($res_query_check_invoice)) {

                                $year = date('Y', strtotime($ValidPeriodID['invoiceDate']));
                                $month_name = date("F", strtotime($ValidPeriodID['invoiceDate']));
                                $month = strtoupper(substr($month_name, 0, 3));

                                $this->load->library('sequence');
                                $dataINV['invoiceType'] = 'Direct';
                                $dataINV['documentID'] = 'CINV';
                                $dataINV['invoiceDate'] = $ValidPeriodID['invoiceDate'];
                                $dataINV['invoiceDueDate'] = $ValidPeriodID['invoiceDueDate'];
                                $dataINV['customerInvoiceDate'] = $ValidPeriodID['customerInvoiceDate'];
                                $dataINV['invoiceCode'] = $this->sequence->sequence_generator($dataINV['documentID']);
                                $dataINV['referenceNo'] = $CollectionDes . ' - ' . $month . '/' . $year;
                                $dataINV['invoiceNarration'] = $CollectionDes;
                                $dataINV['companyFinanceYearID'] = $companyFinanceYearID;
                                $dataINV['companyFinanceYear'] = $companyFinanceYear;
                                $dataINV['FYBegin'] = $company_finance_year['beginingDate'];
                                $dataINV['FYEnd'] = $company_finance_year['endingDate'];
                                $dataINV['companyFinancePeriodID'] = $ValidPeriodID['companyFinancePeriodID'];
                                $dataINV['customerID'] = $customerAutoID;
                                $dataINV['customerSystemCode'] = $customerSystemCode;
                                $dataINV['customerName'] = $customerName;
                                $dataINV['customerAddress'] = $customerAddress;
                                $dataINV['customerTelephone'] = $customerTelephone;
                                $dataINV['customerEmail'] = $customerEmail;
                                $dataINV['customerReceivableAutoID'] = $receivableAutoID;
                                $dataINV['customerReceivableSystemGLCode'] = $receivableSystemGLCode;
                                $dataINV['customerReceivableGLAccount'] = $receivableGLAccount;
                                $dataINV['customerReceivableDescription'] = $receivableDescription;
                                $dataINV['customerReceivableType'] = $receivableType;
                                $dataINV['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');

                                $dataINV['transactionCurrencyID'] = $transactionCurrencyID;
                                $dataINV['transactionCurrency'] = $transactionCurrency;
                                $dataINV['transactionExchangeRate'] = 1;
                                $dataINV['transactionCurrencyDecimalPlaces'] = $transactionCurrencyDecimalPlaces;

                                $dataINV['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                                $dataINV['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                                $default_currency = currency_conversionID($dataINV['transactionCurrencyID'], $dataINV['companyLocalCurrencyID']);
                                $dataINV['companyLocalExchangeRate'] = $default_currency['conversion'];
                                $dataINV['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];

                                $dataINV['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                                $dataINV['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                                $reporting_currency = currency_conversionID($dataINV['transactionCurrencyID'], $dataINV['companyReportingCurrencyID']);
                                $dataINV['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                                $dataINV['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

                                $dataINV['customerCurrency'] = $transactionCurrency;
                                $dataINV['customerCurrencyID'] = $transactionCurrencyID;
                                $dataINV['customerCurrencyExchangeRate'] = 1;
                                $dataINV['customerCurrencyDecimalPlaces'] = $transactionCurrencyDecimalPlaces;

                                $dataINV['segmentID'] = $segmentID;
                                $dataINV['segmentCode'] = $segmentCode;
                                $dataINV['companyCode'] = $this->common_data['company_data']['company_code'];
                                $dataINV['companyID'] = $this->common_data['company_data']['company_id'];

                                $dataINV['createdUserGroup'] = $this->common_data['user_group'];
                                $dataINV['createdPCID'] = $this->common_data['current_pc'];
                                $dataINV['createdUserID'] = $this->common_data['current_userID'];
                                $dataINV['createdUserName'] = $this->common_data['current_user'];
                                $dataINV['createdDateTime'] = $this->common_data['current_date'];

                                $this->db->insert('srp_erp_customerinvoicemaster', $dataINV);
                                $invoiceAutoID = $this->db->insert_id();

                                if ($invoiceAutoID) {

                                    //insert into invoice details

                                    $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
                                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                                    $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

                                    $dataINV_Detail['invoiceAutoID'] = trim($invoiceAutoID);
                                    $dataINV_Detail['revenueGLAutoID'] = $revenueGLAutoID;
                                    $dataINV_Detail['revenueSystemGLCode'] = $revenueSystemGLCode;
                                    $dataINV_Detail['revenueGLCode'] = $revenueGLCode;
                                    $dataINV_Detail['revenueGLDescription'] = $revenueGLDescription;
                                    $dataINV_Detail['revenueGLType'] = $revenueGLType;
                                    $dataINV_Detail['segmentID'] = $segmentID;
                                    $dataINV_Detail['segmentCode'] = $segmentCode;
                                    $dataINV_Detail['transactionAmount'] = round($Amount[$a], $master['transactionCurrencyDecimalPlaces']);
                                    $companyLocalAmount = $dataINV_Detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                                    $dataINV_Detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                                    $companyReportingAmount = $dataINV_Detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                                    $dataINV_Detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                                    $customerAmount = $dataINV_Detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                                    $dataINV_Detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
                                    $dataINV_Detail['description'] = trim($Description);
                                    $dataINV_Detail['type'] = 'GL';
                                    $dataINV_Detail['companyCode'] = $this->common_data['company_data']['company_code'];
                                    $dataINV_Detail['companyID'] = $this->common_data['company_data']['company_id'];
                                    $dataINV_Detail['createdUserGroup'] = $this->common_data['user_group'];
                                    $dataINV_Detail['createdPCID'] = $this->common_data['current_pc'];
                                    $dataINV_Detail['createdUserID'] = $this->common_data['current_userID'];
                                    $dataINV_Detail['createdUserName'] = $this->common_data['current_user'];
                                    $dataINV_Detail['createdDateTime'] = $this->common_data['current_date'];

                                    $this->db->insert('srp_erp_customerinvoicedetails', $dataINV_Detail);
                                    $invoiceDetailsAutoID = $this->db->insert_id();

                                    $this->invoice_confirmation($invoiceAutoID);
                                }

                            } else {
                                return array('e', 'Invoice Detail : Already Created.');
                            }
                        }
                    }
                }
                $a++;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice : Create Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice : Created Successfully.');
            }
        }
    }

    function invoice_confirmation($invoiceAutoID)
    {

        $this->db->trans_start();

        $this->db->select('invoiceDetailsAutoID');
        $this->db->where('invoiceAutoID', trim($invoiceAutoID));
        $this->db->from('srp_erp_customerinvoicedetails');
        $results = $this->db->get()->result_array();

        if (empty($results)) {
        } else {
            $this->db->select('invoiceAutoID');
            $this->db->where('invoiceAutoID', trim($invoiceAutoID));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_customerinvoicemaster');
            $Confirmed = $this->db->get()->row_array();

            if (!empty($Confirmed)) {
            } else {
                $this->load->library('approvals');
                $this->db->select('invoiceAutoID, invoiceCode, documentID,transactionCurrency, transactionExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate,customerCurrencyExchangeRate');
                $this->db->where('invoiceAutoID', trim($invoiceAutoID));
                $this->db->from('srp_erp_customerinvoicemaster');
                $master_data = $this->db->get()->row_array();

                $approvals_status = $this->Create_INV_Approval($master_data['documentID'], $master_data['invoiceAutoID'], $master_data['invoiceCode'], 'Invoice', 'srp_erp_customerinvoicemaster', 'invoiceAutoID');

                if ($approvals_status == 1) {

                    $companyID = current_companyID();
                    $invautoid = $invoiceAutoID;
                    $r1 = "SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
	`srp_erp_customerinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
	`srp_erp_customerinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster`.`customerCurrencyExchangeRate` AS `customerCurrencyExchangeRate`,
	`srp_erp_customerinvoicemaster`.`customerCurrencyDecimalPlaces` AS `customerCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,

	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value

FROM
	`srp_erp_customerinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
WHERE
	`companyID` = $companyID
and srp_erp_customerinvoicemaster.invoiceAutoID= $invautoid ";
                    $totalValue = $this->db->query($r1)->row_array();

                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => (round($totalValue['total_value'], $totalValue['transactionCurrencyDecimalPlaces'])),
                        'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'], $totalValue['companyLocalCurrencyDecimalPlaces'])),
                        'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'], $totalValue['companyReportingCurrencyDecimalPlaces'])),
                        'customerCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['customerCurrencyExchangeRate'], $totalValue['customerCurrencyDecimalPlaces'])),
                    );

                    $this->db->where('invoiceAutoID', trim($invoiceAutoID));
                    $this->db->update('srp_erp_customerinvoicemaster', $data);

                    // confirm appoval
                    $this->save_invoice_approval($invoiceAutoID);

                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        }
    }


    function maxlevel($document)
    {
        $this->db->select_max('levelNo');
        $this->db->where('Status', 1);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('documentID', $document);
        $this->db->from('srp_erp_approvalusers');
        return $this->db->get()->row_array();
    }


    function Create_INV_Approval($document, $documentID, $documentCode, $documentName, $table_name = '', $table_unique_field_name = '', $autoApprove = 0)
    {

        $maxlevel = $this->maxlevel($document);

        if (!empty($maxlevel["levelNo"])) {
            $data_app = array();
            for ($i = 1; $i <= $maxlevel["levelNo"]; $i++) {
                $data_app[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data_app[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_app[$i]['departmentID'] = $document;
                $data_app[$i]['documentID'] = $document;
                $data_app[$i]['documentSystemCode'] = $documentID;
                $data_app[$i]['documentCode'] = $documentCode;
                $data_app[$i]['table_name'] = $table_name;
                $data_app[$i]['table_unique_field_name'] = $table_unique_field_name;
                $data_app[$i]['documentDate'] = $this->common_data['current_date'];
                $data_app[$i]['approvalLevelID'] = $i;
                $data_app[$i]['roleID'] = null;
                $data_app[$i]['approvalGroupID'] = $this->common_data['user_group'];
                $data_app[$i]['roleLevelOrder'] = null;
                $data_app[$i]['docConfirmedDate'] = $this->common_data['current_date'];
                $data_app[$i]['docConfirmedByEmpID'] = $this->common_data['current_userID'];
                $data_app[$i]['approvedEmpID'] = null;
                $data_app[$i]['approvedYN'] = 0;
                $data_app[$i]['approvedDate'] = null;
            }

            $this->db->insert_batch('srp_erp_documentapproved', $data_app);

            if (trim($table_name) != '' && trim($table_unique_field_name) != '') {
                $data = array(
                    'confirmedYN' => '1',
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                );

                $this->db->where($table_unique_field_name, $documentID);
                $this->db->update($table_name, $data);

                // $this->session->set_flashdata('s', '' . $documentName . ' : ' . $documentCode . ' Approved Successfully.');
            }
            // $this->session->set_flashdata('s', 'Approval Created : ' . $documentName . ' : ' . $documentCode . ' Successfully.');
            return 1;
        } else {
            if ($autoApprove == 1) {
                if (trim($table_name) != '' && trim($table_unique_field_name) != '') {
                    $data = array(
                        'confirmedYN' => '1',
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'approvedYN' => '1',
                        'approvedDate' => $this->common_data['current_date'],
                        'approvedbyEmpID' => $this->common_data['current_userID'],
                        'approvedbyEmpName' => $this->common_data['current_user'],
                    );

                    $this->db->where($table_unique_field_name, $documentID);
                    $this->db->update($table_name, $data);


                    //  $this->session->set_flashdata('s', '' . $documentName . ' : ' . $documentCode . ' Approved Successfully.');
                    return 1;
                } else {
                    //  $this->session->set_flashdata('w', 'There are no users exist to perform ' . $documentName . ' : ' . $documentCode . ' approval for this company.');
                    return 3;
                }
            } else {
                // $this->session->set_flashdata('w', 'There are no users exist to perform ' . $documentName . ' : ' . $documentCode . ' approval for this company.');
                return 3;
            }

        }
    }

    /*confirm approval*/

    function save_invoice_approval($invoiceAutoID)
    {
        $this->load->library('approvals');
        $system_id = trim($invoiceAutoID);
        $level_id = '1';
        $status = '1';
        $comments = '';

        $sql = "SELECT
	(
		srp_erp_warehouseitems.currentStock - srp_erp_customerinvoicedetails.requestedQty
	) AS stockDiff,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_warehouseitems.currentStock as availableStock
FROM
	`srp_erp_customerinvoicedetails`
JOIN `srp_erp_warehouseitems` ON `srp_erp_customerinvoicedetails`.`itemAutoID` = `srp_erp_warehouseitems`.`itemAutoID`
AND `srp_erp_customerinvoicedetails`.`wareHouseAutoID` = `srp_erp_warehouseitems`.`wareHouseAutoID`
JOIN `srp_erp_itemmaster` ON `srp_erp_customerinvoicedetails`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID`

WHERE
	`srp_erp_customerinvoicedetails`.`invoiceAutoID` = '$system_id'
AND `srp_erp_warehouseitems`.`companyID` = " . current_companyID() . "
HAVING
	`stockDiff` < 0";
        $items_arr = $this->db->query($sql)->result_array();
        if ($status != 1) {
            $items_arr = [];
        }
        if (!$items_arr) {
            $approvals_status = $this->approve_document($system_id, $level_id, $status, $comments, 'CINV');
            if ($approvals_status == 1) {

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($system_id, 'CINV');

                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                    $generalledger_arr[$i]['documentType'] = '';
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['invoiceDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                    $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                    $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }

                if (!empty($generalledger_arr)) {
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }

                $this->db->select_sum('transactionAmount');
                $this->db->where('invoiceAutoID', $system_id);
                $total = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];

                $this->db->where('invoiceAutoID', $system_id);
                $this->db->update('srp_erp_customerinvoicemaster', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        } else {
        }
    }


    function approve_document($system_code, $level_id, $status, $comments, $documentCode)
    {
        $this->db->select('documentCode,approvedYN');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentID', $documentCode);
        $this->db->where('documentSystemCode', $system_code);
        $this->db->where('approvedYN', 2);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $approval_data = $this->db->get()->row_array();

        if (!empty($approval_data)) {
            return 3;
        } else {
            if ($level_id > 1) {
                $previousLevel = $level_id - 1;
                $isLast_where = array('documentID' => $documentCode, 'documentSystemCode' => $system_code, 'approvalLevelID' => $previousLevel);
                $this->db->select('approvedYN');
                $this->db->from('srp_erp_documentapproved');
                $this->db->where($isLast_where);
                $isLastLevelApproved = $this->db->get()->row_array();
                if ($isLastLevelApproved['approvedYN'] == 1) {
                    if ($status == 1) {
                        return $this->approve($system_code, $level_id, $status, $comments, $documentCode);
                    }

                } else {
                    return 5;
                }
            } else {
                if ($status == 1) {
                    return $this->approve($system_code, $level_id, $status, $comments, $documentCode);
                }
            }
        }
    }

    function approve($system_code, $level_id, $status, $comments, $documentCode)
    {
        $maxlevel = $this->maxlevel($documentCode);

        $this->db->trans_start();

        $data = array(
            'approvedYN' => $status,
            'approvedEmpID' => current_userID(),
            'approvedComments' => $comments,
            'approvedDate' => $this->common_data['current_date'],
            'approvedPC' => $this->common_data['current_pc']
        );

        $this->db->where('documentSystemCode', $system_code);
        $this->db->where('documentID', $documentCode);
        $this->db->where('approvalLevelID', $level_id);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->update('srp_erp_documentapproved', $data);
        $data = $this->details($system_code, $documentCode);

        if ($data['approvedYN'] == 1) {
            if (!empty($data['table_unique_field_name']) && !empty($data['table_name'])) {
                $dataUpdate = array(
                    'approvedYN' => '1',
                    'approvedDate' => $this->common_data['current_date'],
                    'approvedbyEmpID' => $this->common_data['current_userID'],
                    'approvedbyEmpName' => $this->common_data['current_user']
                );

                $this->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
                $this->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return 'e';
                } else {
                    $this->db->trans_commit();
                    return 1;
                }

            } else {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return 'e';
                } else {
                    $this->db->trans_commit();
                    return 3;
                }
            }
        } else {
            /*update current level in master record*/
            $dataUpdate = array(
                'currentLevelNo' => $level_id + 1,
            );
            $this->db->where(trim($data['table_unique_field_name'] ?? ''), $system_code);
            $this->db->update(trim($data['table_name'] ?? ''), $dataUpdate);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return 'e';
            } else {
                $this->db->trans_commit();
                return 2;
            }
        }

    }

    function details($system_code, $documentCode)
    {
        $this->db->select('documentID, documentCode, table_name, table_unique_field_name, approvedYN');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentSystemCode', $system_code);
        $this->db->where('documentID', $documentCode);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->order_by('approvalLevelID', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }



    /*end collections*/

    /*Start of Issue Items*/
    function fetch_member_details()
    {
        $this->db->select('*');
        $this->db->where('Com_MasterID', $this->input->post('Com_MasterID'));
        return $this->db->get('srp_erp_ngo_com_communitymaster')->row_array();
    }

    function save_item_request_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $expectedReturnDate = trim($this->input->post('expectedReturnDate') ?? '');
        $Pqrdate = trim($this->input->post('issueDate') ?? '');
        $format_expectedReturnDate = input_format_date($expectedReturnDate, $date_format_policy);
        $format_POdate = input_format_date($Pqrdate, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));


        $data['documentID'] = 'RTL';
        $data['requestedMemberID'] = trim($this->input->post('requestedMemberID') ?? '');
        $data['requestedMemberName'] = trim($this->input->post('requestedMemberName') ?? '');
        $data['narration'] = trim_desc($this->input->post('narration'));
        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency') ?? '');
        $data['expectedReturnDate'] = $format_expectedReturnDate;
        $data['issueDate'] = $format_POdate;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];

        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];


        if (trim($this->input->post('itemIssueAutoID') ?? '')) {
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
            $this->db->update('srp_erp_ngo_com_itemissuemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item Request Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Item Request Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('itemIssueAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['itemIssueCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_ngo_com_itemissuemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item Request Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Item Request Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_item_request_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expectedReturnDate,\'' . $convertFormat . '\') AS expectedReturnDate,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->from('srp_erp_ngo_com_itemissuemaster');
        return $this->db->get()->row_array();
    }

    function fetch_item_req_detail_table()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->from('srp_erp_ngo_com_itemissuemaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*,DATE_FORMAT(expectedReturnDate,\'' . $convertFormat . '\') AS expectedReturnDate');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function load_item_request_date()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expectedReturnDate,\'' . $convertFormat . '\') AS expectedReturnDate, expectedReturnDate AS MySQLexpectedReturnDate,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate, issueDate AS MySQLissueDate');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->from('srp_erp_ngo_com_itemissuemaster');
        return $this->db->get()->row_array();
    }


    function save_item_issue_details()
    {
        $rentalItemID = $this->input->post('rentalItemID');
        $itemIssueDetailAutoID = $this->input->post('itemIssueDetailAutoID');
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $rentalItemTypes = $this->input->post('rentalItemType');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $faIDs = $this->input->post('faID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $currentStock = $this->input->post('currentStock');
        $comment = $this->input->post('comment');
        $expReturnDate = $this->input->post('expectedReturnDateDetail');
        $no_of_days = $this->input->post('no_of_days');

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            $companyID = current_companyID();
            $item_arr = $this->db->query("SELECT * FROM srp_erp_ngo_com_rentalitems WHERE rentalItemID ={$rentalItemID[$key]} AND companyID = {$companyID}")->row_array();

            if ($rentalItemTypes[$key] == 1) {
                $itemTypeDes = 'Products / Goods';
            } else {
                $itemTypeDes = 'Fixed assets';
            }

            $date_format_policy = date_format_policy();
            $expectedReturnDate = $expReturnDate[$key];
            $format_expectedReturnDate = input_format_date($expectedReturnDate, $date_format_policy);

            $data['itemIssueAutoID'] = $itemIssueAutoID;
            $data['expectedReturnDate'] = $format_expectedReturnDate;
            $data['no_of_days'] = $no_of_days[$key];
            $data['rentalItemID'] = $rentalItemID[$key];
            $data['rentalItemType'] = $rentalItemTypes[$key];
            $data['itemTypeDes'] = $itemTypeDes;
            $data['itemAutoID'] = $itemAutoID;
            $data['faID'] = $faIDs[$key];
            $data['itemSystemCode'] = $item_arr['rentalItemCode'];

            $data['itemDescription'] = $item_arr['rentalItemDes'];
            $data['unitOfMeasure'] = $item_arr['defaultUnitOfMeasure'];
            $data['unitOfMeasureID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['unitAmount'] = ($estimatedAmount[$key]);

            $qty = $data['requestedQty'];
            $amount = $data['unitAmount'];
            $days = $data['no_of_days'];

            if ($qty == 0 || $qty == '' || $qty == null) {
                if ($days == 0 || $days == '' || $days == null) {
                    $data['totalAmount'] = 0;
                } else {
                    $data['totalAmount'] = ($amount * $days);
                }
            } else {
                if ($days == 0 || $days == '' || $days == null) {
                    $data['totalAmount'] = ($amount * $qty);
                } else {
                    $data['totalAmount'] = ($amount * $qty * $days);
                }
            }
            $data['comment'] = $comment[$key];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_com_itemissuedetails', $data);

            $this->db->select('currentStock');
            $this->db->where('rentalItemID', trim($rentalItemID[$key]));
            $this->db->from('srp_erp_ngo_com_rentalitems');
            $currentStock_data = $this->db->get()->row_array();

            // update current stock
            $dataC = array(
                'currentStock' => $currentStock_data['currentStock'] - $quantityRequested[$key],
            );
            $this->db->where('rentalItemID', trim($rentalItemID[$key]));
            $this->db->update('srp_erp_ngo_com_rentalitems', $dataC);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Issue Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Issue Details :  Saved Successfully.');
        }
    }

    function update_item_issue_detail()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $expectedReturnDate = trim($this->input->post('expectedReturnDateDetail') ?? '');
        $format_expectedReturnDate = input_format_date($expectedReturnDate, $date_format_policy);

        $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['faID'] = trim($this->input->post('faID') ?? '');
        $data['no_of_days'] = trim($this->input->post('no_of_daysEdit') ?? '');
        $data['expectedReturnDate'] = $format_expectedReturnDate;

        $data['requestedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['unitAmount'] = trim($this->input->post('estimatedAmount') ?? '');

        $qty = $data['requestedQty'];
        $amount = $data['unitAmount'];
        $days = $data['no_of_days'];

        if ($qty == 0 || $qty == '' || $qty == null) {
            if ($days == 0 || $days == '' || $days == null) {
                $data['totalAmount'] = 0;
            } else {
                $data['totalAmount'] = ($amount * $days);
            }
        } else {
            if ($days == 0 || $days == '' || $days == null) {
                $data['totalAmount'] = ($amount * $qty);
            } else {
                $data['totalAmount'] = ($amount * $qty * $days);
            }
        }

        $data['comment'] = trim($this->input->post('comment') ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('itemIssueDetailAutoID') ?? '')) {
            $this->db->where('itemIssueDetailAutoID', trim($this->input->post('itemIssueDetailAutoID') ?? ''));
            $this->db->update('srp_erp_ngo_com_itemissuedetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Item Issue Detail : Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Item Issue Detail :  Updated Successfully.');

            }
        }
    }

    function fetch_item_issue_detail_edit()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(srp_erp_ngo_com_itemissuedetails.expectedReturnDate,\'' . $convertFormat . '\') AS expectedReturnDate');
        $this->db->where('itemIssueDetailAutoID', trim($this->input->post('itemIssueDetailAutoID') ?? ''));
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $this->db->join('srp_erp_ngo_com_itemissuemaster', 'srp_erp_ngo_com_itemissuemaster.itemIssueAutoID = srp_erp_ngo_com_itemissuedetails.itemIssueAutoID');
        return $this->db->get()->row_array();
    }

    function delete_itemissuemaster()
    {

        $this->db->select('*');
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before deleting this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date'],
            );
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
            $this->db->update('srp_erp_ngo_com_itemissuemaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;

        }
    }

    function delete_item_issue_detail()
    {

        $this->db->select('rentalItemID,requestedQty');
        $this->db->where('itemIssueDetailAutoID', trim($this->input->post('itemIssueDetailAutoID') ?? ''));
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $rentalID_data = $this->db->get()->row_array();

        $this->db->select('currentStock');
        $this->db->where('rentalItemID', trim($rentalID_data['rentalItemID'] ?? ''));
        $this->db->from('srp_erp_ngo_com_rentalitems');
        $currentStock_data = $this->db->get()->row_array();

        // update current stock
        $data = array(
            'currentStock' => $currentStock_data['currentStock'] + $rentalID_data['requestedQty'],
        );
        $this->db->where('rentalItemID', trim($rentalID_data['rentalItemID'] ?? ''));
        $this->db->update('srp_erp_ngo_com_rentalitems', $data);

        $this->db->delete('srp_erp_ngo_com_itemissuedetails', array('itemIssueDetailAutoID' => trim($this->input->post('itemIssueDetailAutoID') ?? '')));
        return true;
    }

    function fetch_template_data($itemIssueAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('itemIssueAutoID,transactionCurrency,transactionCurrencyDecimalPlaces,itemIssueCode, DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,requestedMemberName,narration,DATE_FORMAT(expectedReturnDate,\'' . $convertFormat . '\') AS expectedReturnDate,confirmedByName,confirmedYN,DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,segmentCode,isReturned,DATE_FORMAT(ReturnedDate,\'' . $convertFormat . '\') AS ReturnedDate');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_ngo_com_itemissuemaster');
        $data['master'] = $this->db->get()->row_array();

        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('itemSystemCode,itemDescription,unitOfMeasure,requestedQty,unitAmount,comment,totalAmount,no_of_days,DATE_FORMAT(expectedReturnDate,\'' . $convertFormat . '\') AS expectedReturnDate');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $itemIssueAutoID);
        $this->db->where('documentID', 'PRQ');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function rental_issue_confirmation()
    {
        $this->db->select('itemIssueAutoID');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {
            $this->db->select('itemIssueAutoID');
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_ngo_com_itemissuemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {

                $this->load->library('approvals');
                $this->db->select('itemIssueCode,companyReportingExchangeRate,companyLocalExchangeRate ,itemIssueAutoID,transactionCurrencyDecimalPlaces');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                $this->db->from('srp_erp_ngo_com_itemissuemaster');
                $po_data = $this->db->get()->row_array();

                $this->db->select_sum('totalAmount');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                $po_total = $this->db->get('srp_erp_ngo_com_itemissuedetails')->row('totalAmount');
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                    'transactionAmount' => round($po_total, $po_data['transactionCurrencyDecimalPlaces']),
                    'companyLocalAmount' => ($po_total / $po_data['companyLocalExchangeRate']),
                    'companyReportingAmount' => ($po_total / $po_data['companyReportingExchangeRate']),
                    'isReturned' => 0,
                );
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID') ?? ''));
                $this->db->update('srp_erp_ngo_com_itemissuemaster', $data);
                $this->session->set_flashdata('s', $po_data['itemIssueCode'] . ' Confirmed Successfully');

// insert into invoice master table
                $this->create_invoice(trim($this->input->post('itemIssueAutoID') ?? ''));

                return true;
            }
        }
    }

    function create_invoice($itemIssueAutoID)
    {

        $this->db->select('*');
        $this->db->where('itemIssueAutoID', trim($itemIssueAutoID));
        $this->db->from('srp_erp_ngo_com_itemissuemaster');
        $itemM_data = $this->db->get()->row_array();

        $companyID = $this->common_data['company_data']['company_id'];
        $companyFinanceYearID = $this->common_data['company_data']['companyFinanceYearID'];
        $companyFinanceYear = $this->common_data['company_data']['companyFinanceYear'];

        $isCustomerExist = $this->db->query("SELECT * FROM srp_erp_customermaster WHERE companyID={$companyID} AND communityMemberID = {$itemM_data['requestedMemberID']} ")->row_array();

        if (isset($isCustomerExist)) {
            $customerAutoID = $isCustomerExist['customerAutoID'];
            $customerSystemCode = $isCustomerExist['customerSystemCode'];
            $customerName = $isCustomerExist['customerName'];
            $customerAddress = $isCustomerExist['customerAddress1'] . ' ' . $isCustomerExist['customerAddress2'];
            $customerTelephone = $isCustomerExist['customerTelephone'];
            $customerEmail = $isCustomerExist['customerEmail'];
            $customerreceivableAutoID = $isCustomerExist{'receivableAutoID'};
            $customerreceivableSystemGLCode = $isCustomerExist{'receivableSystemGLCode'};
            $customerreceivableGLAccount = $isCustomerExist{'receivableGLAccount'};
            $customerreceivableDescription = $isCustomerExist{'receivableDescription'};
            $customerreceivableType = $isCustomerExist{'receivableType'};
        } else {

            //community member details
            $MemberDetails = $this->db->query("SELECT * FROM srp_erp_ngo_com_communitymaster WHERE srp_erp_ngo_com_communitymaster.companyID = {$companyID} AND srp_erp_ngo_com_communitymaster.Com_MasterID = {$itemM_data['requestedMemberID']} ")->row_array();

            $MemberCode = $MemberDetails{'MemberCode'};
            $CName_with_initials = $MemberDetails{'CName_with_initials'};
            $P_Address = $MemberDetails{'P_Address'};
            $C_Address = $MemberDetails{'C_Address'};

            $Membercountry = $this->db->query("SELECT CountryDes FROM srp_erp_countrymaster WHERE countryID = {$MemberDetails{'countyID'}} ")->row_array();
            $Country = $Membercountry{'CountryDes'};

            $Telephone = $MemberDetails{'TP_Mobile'};
            $EmailID = $MemberDetails{'EmailID'};

            //customer acc details
            $CusConfigDetails = $this->db->query("SELECT * FROM srp_erp_ngo_com_customerconfig WHERE srp_erp_ngo_com_customerconfig.companyID = {$companyID} AND isActive = 1 ")->row_array();
            $receivableAutoID = $CusConfigDetails{'receivableAutoID'};
            $receivableSystemGLCode = $CusConfigDetails{'receivableSystemGLCode'};
            $receivableGLAccount = $CusConfigDetails{'receivableGLAccount'};
            $receivableDescription = $CusConfigDetails{'receivableDescription'};
            $receivableType = $CusConfigDetails{'receivableType'};
            $partyCategoryID = $CusConfigDetails{'partyCategoryID'};

            // save member as customer
            $this->load->library('sequence');
            $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
            $data['secondaryCode'] = trim($MemberCode);
            $data['customerName'] = trim($CName_with_initials);
            $data['customerTelephone'] = trim($Telephone);
            $data['customerEmail'] = trim($EmailID);
            $data['customerAddress1'] = trim($C_Address);
            $data['customerAddress2'] = trim($P_Address);
            $data['customerCountry'] = trim($Country);
            $data['partyCategoryID'] = trim($partyCategoryID);
            $data['receivableAutoID'] = trim($receivableAutoID);
            $data['receivableSystemGLCode'] = trim($receivableSystemGLCode);
            $data['receivableGLAccount'] = trim($receivableGLAccount);
            $data['receivableDescription'] = trim($receivableDescription);
            $data['receivableType'] = trim($receivableType);
            $data['customerCurrencyID'] = trim($itemM_data['transactionCurrencyID'] ?? '');
            $data['customerCurrency'] = trim($itemM_data['transactionCurrency'] ?? '');
            $data['customerCurrencyDecimalPlaces'] = trim($itemM_data['transactionCurrencyDecimalPlaces'] ?? '');
            $data['communityMemberID'] = trim($itemM_data['requestedMemberID'] ?? '');
            $data['isActive'] = 1;
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customermaster', $data);

            $customerAutoID = $this->db->insert_id();
            $customerSystemCode = $data['customerSystemCode'];
            $customerName = $data['customerName'];
            $customerAddress = $data['customerAddress1'] . ' ' . $data['customerAddress2'];
            $customerTelephone = $data['customerTelephone'];
            $customerEmail = $data['customerEmail'];
            $customerreceivableAutoID = $data{'receivableAutoID'};
            $customerreceivableSystemGLCode = $data{'receivableSystemGLCode'};
            $customerreceivableGLAccount = $data{'receivableGLAccount'};
            $customerreceivableDescription = $data{'receivableDescription'};
            $customerreceivableType = $data{'receivableType'};
        }


        $company_finance_year = $this->db->query("SELECT * FROM srp_erp_companyfinanceyear WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} AND isActive = 1 AND isCurrent = 1 ")->row_array();

        $company_finance_period = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} AND isActive = 1 AND isCurrent = 1 ")->row_array();

        $this->db->select('*');
        $this->db->where('itemIssueAutoID', trim($itemIssueAutoID));
        $item_issue_details = $this->db->get('srp_erp_ngo_com_itemissuedetails')->result_array();

        if (!empty($item_issue_details)) {
            foreach ($item_issue_details as $Item_det) {

                // get rental item details from srp_erp_ngo_com_rentalitems
                $retntal_item_mas = $this->db->query("SELECT * FROM srp_erp_ngo_com_rentalitems  WHERE companyID = {$companyID} AND rentalItemID = {$Item_det['rentalItemID']} ")->row_array();

                $year = date('Y', strtotime($itemM_data['issueDate']));
                $month_name = date("F", strtotime($itemM_data['issueDate']));
                $month = strtoupper(substr($month_name, 0, 3));

                // insert into invoice master
                $this->load->library('sequence');
                $dataINV['invoiceType'] = 'Direct';
                $dataINV['documentID'] = 'CINV';
                $dataINV['invoiceDate'] = $itemM_data['issueDate'];
                $dataINV['invoiceDueDate'] = $itemM_data['expectedReturnDate'];
                $dataINV['customerInvoiceDate'] = $itemM_data['issueDate'];
                $dataINV['invoiceCode'] = $this->sequence->sequence_generator($dataINV['documentID']);
                $dataINV['referenceNo'] = $itemM_data['narration'] . ' - ' . $month . '/' . $year;
                $dataINV['invoiceNarration'] = $itemM_data['narration'];
                $dataINV['companyFinanceYearID'] = $companyFinanceYearID;
                $dataINV['companyFinanceYear'] = $companyFinanceYear;
                $dataINV['FYBegin'] = $company_finance_year['beginingDate'];
                $dataINV['FYEnd'] = $company_finance_year['endingDate'];
                $dataINV['companyFinancePeriodID'] = $company_finance_period['companyFinancePeriodID'];
                $dataINV['customerID'] = $customerAutoID;
                $dataINV['customerSystemCode'] = $customerSystemCode;
                $dataINV['customerName'] = $customerName;
                $dataINV['customerAddress'] = $customerAddress;
                $dataINV['customerTelephone'] = $customerTelephone;
                $dataINV['customerEmail'] = $customerEmail;
                $dataINV['customerReceivableAutoID'] = $customerreceivableAutoID;
                $dataINV['customerReceivableSystemGLCode'] = $customerreceivableSystemGLCode;
                $dataINV['customerReceivableGLAccount'] = $customerreceivableGLAccount;
                $dataINV['customerReceivableDescription'] = $customerreceivableDescription;
                $dataINV['customerReceivableType'] = $customerreceivableType;
                $dataINV['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');

                $dataINV['transactionCurrencyID'] = $itemM_data['transactionCurrencyID'];
                $dataINV['transactionCurrency'] = $itemM_data['transactionCurrency'];
                $dataINV['transactionExchangeRate'] = 1;
                $dataINV['transactionCurrencyDecimalPlaces'] = $itemM_data['transactionCurrencyDecimalPlaces'];

                $dataINV['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $dataINV['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($dataINV['transactionCurrencyID'], $dataINV['companyLocalCurrencyID']);
                $dataINV['companyLocalExchangeRate'] = $default_currency['conversion'];
                $dataINV['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];

                $dataINV['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $dataINV['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($dataINV['transactionCurrencyID'], $dataINV['companyReportingCurrencyID']);
                $dataINV['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $dataINV['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

                $dataINV['customerCurrency'] = $itemM_data['transactionCurrencyID'];
                $dataINV['customerCurrencyID'] = $itemM_data['transactionCurrency'];
                $dataINV['customerCurrencyExchangeRate'] = 1;
                $dataINV['customerCurrencyDecimalPlaces'] = $itemM_data['transactionCurrencyDecimalPlaces'];

                $dataINV['segmentID'] = $itemM_data['segmentID'];
                $dataINV['segmentCode'] = $itemM_data['segmentCode'];
                $dataINV['companyCode'] = $this->common_data['company_data']['company_code'];
                $dataINV['companyID'] = $this->common_data['company_data']['company_id'];

                $dataINV['createdUserGroup'] = $this->common_data['user_group'];
                $dataINV['createdPCID'] = $this->common_data['current_pc'];
                $dataINV['createdUserID'] = $this->common_data['current_userID'];
                $dataINV['createdUserName'] = $this->common_data['current_user'];
                $dataINV['createdDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_customerinvoicemaster', $dataINV);
                $invoiceAutoID = $this->db->insert_id();

                $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces,transactionCurrencyID');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

                // insert into invoice details
                $dataINV_Detail['invoiceAutoID'] = trim($invoiceAutoID);
                $dataINV_Detail['itemAutoID'] = $Item_det['itemAutoID'];
                $dataINV_Detail['itemSystemCode'] = $Item_det['itemSystemCode'];
                $dataINV_Detail['itemDescription'] = $Item_det['itemDescription'];

                $dataINV_Detail['unitOfMeasure'] = $Item_det['unitOfMeasure'];
                $dataINV_Detail['unitOfMeasureID'] = $Item_det['unitOfMeasureID'];
                $dataINV_Detail['defaultUOM'] = $Item_det['defaultUOM'];
                $dataINV_Detail['defaultUOMID'] = $Item_det['defaultUOMID'];
                $dataINV_Detail['conversionRateUOM'] = $Item_det['conversionRateUOM'];
                $dataINV_Detail['requestedQty'] = $Item_det['conversionRateUOM'];
                $dataINV_Detail['unittransactionAmount'] = round($Item_det['unitAmount'], $master['transactionCurrencyDecimalPlaces']);
                $dataINV_Detail['transactionAmount'] = round($Item_det['totalAmount'], $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $dataINV_Detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                $dataINV_Detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $dataINV_Detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                $dataINV_Detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $dataINV_Detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $dataINV_Detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $dataINV_Detail['type'] = 'Item';
                $item_data = fetch_item_data($dataINV_Detail['itemAutoID']);
                $dataINV_Detail['segmentID'] = $master['segmentID'];
                $dataINV_Detail['segmentCode'] = $master['segmentCode'];

                $dataINV_Detail['expenseGLAutoID'] = $item_data['costGLAutoID'];
                $dataINV_Detail['expenseGLCode'] = $item_data['costGLCode'];
                $dataINV_Detail['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
                $dataINV_Detail['expenseGLDescription'] = $item_data['costDescription'];
                $dataINV_Detail['expenseGLType'] = $item_data['costType'];

                $dataINV_Detail['revenueGLAutoID'] = $retntal_item_mas['revanueGLAutoID'];
                $dataINV_Detail['revenueGLCode'] = $retntal_item_mas['revanueGLCode'];
                $dataINV_Detail['revenueSystemGLCode'] = $retntal_item_mas['revanueSystemGLCode'];
                $dataINV_Detail['revenueGLDescription'] = $retntal_item_mas['revanueDescription'];
                $dataINV_Detail['revenueGLType'] = $retntal_item_mas['revanueType'];

                $dataINV_Detail['assetGLAutoID'] = $item_data['assteGLAutoID'];
                $dataINV_Detail['assetGLCode'] = $item_data['assteGLCode'];
                $dataINV_Detail['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
                $dataINV_Detail['assetGLDescription'] = $item_data['assteDescription'];
                $dataINV_Detail['assetGLType'] = $item_data['assteType'];

                $dataINV_Detail['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
                $dataINV_Detail['itemCategory'] = $item_data['mainCategory'];

                $dataINV_Detail['companyID'] = $this->common_data['company_data']['company_id'];
                $dataINV_Detail['companyCode'] = $this->common_data['company_data']['company_code'];
                $dataINV_Detail['createdUserGroup'] = $this->common_data['user_group'];
                $dataINV_Detail['createdPCID'] = $this->common_data['current_pc'];
                $dataINV_Detail['createdUserID'] = $this->common_data['current_userID'];
                $dataINV_Detail['createdUserName'] = $this->common_data['current_user'];
                $dataINV_Detail['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails', $dataINV_Detail);
                $invoiceDetailsAutoID = $this->db->insert_id();

                $this->invoice_confirmation($invoiceAutoID);
            }
        }
    }

    function return_item_confirmation()
    {

        $this->db->trans_start();

        $itemIssueAutoID = trim($this->input->post('itemIssueAutoID') ?? '');
        $isReturned = trim($this->input->post('isReturned') ?? '');

        $date_format_policy = date_format_policy();
        $ReturnedDate_re = trim($this->input->post('ReturnedDate') ?? '');
        $ReturnedDate = input_format_date($ReturnedDate_re, $date_format_policy);

        $data = array(
            'isReturned' => $isReturned,
            'ReturnedDate' => $ReturnedDate,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date'],
        );
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->update('srp_erp_ngo_com_itemissuemaster', $data);

        //update rental item current stock
        $this->db->select('rentalItemID,requestedQty');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $rentalID_data = $this->db->get()->result_array();

        if (!empty($rentalID_data)) {
            foreach ($rentalID_data as $Item_det) {

                $this->db->select('currentStock');
                $this->db->where('rentalItemID', trim($Item_det['rentalItemID'] ?? ''));
                $this->db->from('srp_erp_ngo_com_rentalitems');
                $currentStock_data = $this->db->get()->row_array();

                // update current stock
                $data = array(
                    'currentStock' => $currentStock_data['currentStock'] + $Item_det['requestedQty'],
                );
                $this->db->where('rentalItemID', trim($Item_det['rentalItemID'] ?? ''));
                $this->db->update('srp_erp_ngo_com_rentalitems', $data);

            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();

            return array('e', 'save failed');
        } else {

            $this->db->trans_commit();

            return array('s', 'Item returned successfully');
        }
    }

    function fetch_item_for_return()
    {

        $this->db->select('*');
        $this->db->from('srp_erp_ngo_com_itemissuedetails');
        $this->db->where('itemIssueAutoID', $this->input->post('itemIssueAutoID'));
        $data['detail'] = $this->db->get()->result_array();
        return $data;

    }

    function loademail()
    {
        $current_user = $this->common_data['current_userID'];
        $this->db->select('*');
        $this->db->where('EIdNo', $current_user);
        $this->db->from('srp_employeesdetails');
        return $this->db->get()->row_array();

    }

    function send_request_email()
    {
        $sender_name = trim($this->input->post('sender_name') ?? '');
        $email = trim($this->input->post('email') ?? '');
        $subject = trim($this->input->post('subject') ?? '');
        $message = trim($this->input->post('message') ?? '');

        $param = array();
        $param["empName"] = 'Sir/Madam';

        $param["body"] = $message . "<br><br> 
                                Regards <br>
                                " . $sender_name . " (" . $email . ")<br>";

        $mailData = [
            'toEmail' => 'support@kancoders.com',
            'subject' => $subject,
            'param' => $param
        ];

        send_requestEmail($mailData);

        return array('s', 'Email Sent Successfully.');

    }

    /* end of hish */

//access
    /* Starting Moufi */

    /*Family Master*/

    function get_FamHgender($LeaderID)
    {

        $query = $this->db->where('Com_MasterID', ($LeaderID))
            ->select('Com_MasterID,srp_erp_ngo_com_communitymaster.GenderID,srp_erp_gender.name')
            ->from('srp_erp_ngo_com_communitymaster')
            ->join('srp_erp_gender', 'srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID', 'inner')
            ->get();
        $leadGen = $query->row();
        if (!empty($leadGen)) {
            echo "<option value='" . $leadGen->GenderID . "' selected='selected' >" . $leadGen->name . "</option>";
        }
    }

    function get_FamHaddress($LeaderID)
    {

        $query = $this->db->where('Com_MasterID', ($LeaderID))
            ->select('Com_MasterID,HouseNo,C_Address')
            ->from('srp_erp_ngo_com_communitymaster')
            ->get();
        $leadAdr = $query->row();
        if (!empty($leadAdr)) {
            echo "<option value='" . $leadAdr->Com_MasterID . "' selected='selected' >" . $leadAdr->C_Address . "</option>";
        }
    }

    function get_FamArea($LeaderID)
    {

        $query = $this->db->where('Com_MasterID', ($LeaderID))
            ->select('Com_MasterID,RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description')
            ->from('srp_erp_ngo_com_communitymaster')
            ->join('srp_erp_statemaster', 'srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.RegionID', 'inner')
            ->get();
        $leadAdr = $query->row();
        if (!empty($leadAdr)) {
            echo "<option value='" . $leadAdr->RegionID . "' selected='selected' >" . $leadAdr->Description . "</option>";
        }
    }

    function get_FamHouseNo($LeaderID)
    {

        $query = $this->db->where('Com_MasterID', ($LeaderID))
            ->select('Com_MasterID,HouseNo,C_Address')
            ->from('srp_erp_ngo_com_communitymaster')
            ->get();
        $leadAdr = $query->row();
        if (!empty($leadAdr)) {
            echo "<option value='" . $leadAdr->Com_MasterID . "' selected='selected' >" . $leadAdr->HouseNo . "</option>";
        }
    }

    function nicDup_Check()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        if ($this->input->post('editFamMasID') == "" || $this->input->post('editFamMasID') == null) {

            $qNicTest = $this->db->query('SELECT LedgerNo FROM srp_erp_ngo_com_familymaster WHERE companyID="' . $companyID . '" AND LedgerNo="' . $this->input->post('ledger_num') . '"');
        } else {


            $qNicTest = $this->db->query('SELECT LedgerNo FROM srp_erp_ngo_com_familymaster WHERE companyID="' . $companyID . '" AND FamMasterID !="' . $this->input->post('editFamMasID') . '" AND LedgerNo="' . $this->input->post('ledger_num') . '"');
        }
        $resNicTest = $qNicTest->row();
        if (!empty($resNicTest)) {
            return array('error' => 1, 'message' => 'The reference number already exist');

        }

    }

    function nicDup_Check1($ledger_num, $editFamMasID)
    {

        $companyID = $this->common_data['company_data']['company_id'];

        if ($editFamMasID == "" || $editFamMasID == null) {
            $qNicTest = $this->db->query('SELECT LedgerNo FROM srp_erp_ngo_com_familymaster WHERE companyID="' . $companyID . '" AND LedgerNo="' . $ledger_num . '"');
        } else {
            $qNicTest = $this->db->query('SELECT LedgerNo FROM srp_erp_ngo_com_familymaster WHERE companyID="' . $companyID . '" AND FamMasterID !="' . $editFamMasID . '" AND LedgerNo="' . $ledger_num . '"');
        }
        $resNicTest = $qNicTest->row();
        if (!empty($resNicTest)) {
            return array('s', 'Member Detail : Records inserted successfully. ');
        }

    }

    function nicDup_Check2()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $output = $this->db->delete('srp_erp_ngo_com_universities', array('UniversityID' => trim($this->input->post('editFamMasID') ?? '')));
        if ($output) {
            return array('error' => 0, 'message' => 'Successfully NGO University deleted');
        } else {
            return array('error' => 1, 'message' => 'Error while updating');
        }

    }

    /*new Relationship category*/
    function new_RelationCat()
    {
        $Relatn = trim($this->input->post('Relatn') ?? '');
        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT relationshipID FROM srp_erp_family_relationship WHERE relationship = '$Relatn' ")->row('relationshipID');

        if (isset($isExist)) {
            return array('e', 'This Relationship is already Exists');
        } else {

            $data = array(
                'relationship' => $Relatn,

            );

            $this->db->insert('srp_erp_family_relationship', $data);
            if ($this->db->affected_rows() > 0) {
                $relationshipID = $this->db->insert_id();
                return array('s', 'Relationship is created successfully.', $relationshipID);
            } else {
                return array('e', 'Error in Relationship Creating');
            }
        }
    }

    //house enrolling

    function get_hEjammiyahDivisionID($FamHouseSt, $housesExitId, $LeaderID)
    {

        if ((!empty($FamHouseSt) && $FamHouseSt == '1') && (!empty($housesExitId))) {
            $query = $this->db->where('hEnrollingID', ($housesExitId))
                ->select('Com_MasterID,jammiyahDivisionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description')
                ->from('srp_erp_ngo_com_house_enrolling')
                ->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID', 'inner')
                ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID', 'inner')
                ->join('srp_erp_statemaster', 'srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.jammiyahDivisionID', 'inner')
                ->get();
            $leadAdh = $query->row();
        } else {
            $query = $this->db->where('Com_MasterID', ($LeaderID))
                ->select('Com_MasterID,jammiyahDivisionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description')
                ->from('srp_erp_ngo_com_communitymaster')
                ->join('srp_erp_statemaster', 'srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.jammiyahDivisionID', 'inner')
                ->get();
            $leadAdh = $query->row();
        }
        if (!empty($leadAdh)) {
            echo "<option value='" . $leadAdh->jammiyahDivisionID . "' selected='selected' >" . $leadAdh->Description . "</option>";
        }
    }

    function get_hEGS_Division($FamHouseSt, $housesExitId, $LeaderID)
    {

        if ((!empty($FamHouseSt) && $FamHouseSt == '1') && (!empty($housesExitId))) {
            $query = $this->db->where('hEnrollingID', ($housesExitId))
                ->select('Com_MasterID,GS_Division,srp_erp_statemaster.stateID,srp_erp_statemaster.Description')
                ->from('srp_erp_ngo_com_house_enrolling')
                ->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID', 'inner')
                ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID', 'inner')
                ->join('srp_erp_statemaster', 'srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.GS_Division', 'inner')
                ->get();
            $leadAdh = $query->row();
        } else {
            $query = $this->db->where('Com_MasterID', ($LeaderID))
                ->select('Com_MasterID,GS_Division,srp_erp_statemaster.stateID,srp_erp_statemaster.Description')
                ->from('srp_erp_ngo_com_communitymaster')
                ->join('srp_erp_statemaster', 'srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.GS_Division', 'inner')
                ->get();
            $leadAdh = $query->row();
        }

        if (!empty($leadAdh)) {
            echo "<option value='" . $leadAdh->GS_Division . "' selected='selected' >" . $leadAdh->Description . "</option>";
        }
    }

    function get_hEGS_No($FamHouseSt, $housesExitId, $LeaderID)
    {

        if ((!empty($FamHouseSt) && $FamHouseSt == '1') && (!empty($housesExitId))) {
            $query = $this->db->where('hEnrollingID', ($housesExitId))
                ->select('Com_MasterID,GS_No')
                ->from('srp_erp_ngo_com_house_enrolling')
                ->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID', 'inner')
                ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID', 'inner')
                ->get();
            $leadAdh = $query->row();
        } else {
            $query = $this->db->where('Com_MasterID', ($LeaderID))
                ->select('Com_MasterID,GS_No')
                ->from('srp_erp_ngo_com_communitymaster')
                ->get();
            $leadAdh = $query->row();
        }

        if (!empty($leadAdh)) {
            echo "<option value='" . $leadAdh->GS_No . "' selected='selected' >" . $leadAdh->GS_No . "</option>";
        }
    }

    function get_hEC_Address($FamHouseSt, $housesExitId, $LeaderID)
    {

        if ((!empty($FamHouseSt) && $FamHouseSt == '1') && (!empty($housesExitId))) {
            $query = $this->db->where('hEnrollingID', ($housesExitId))
                ->select('Com_MasterID,HouseNo,C_Address')
                ->from('srp_erp_ngo_com_house_enrolling')
                ->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID', 'inner')
                ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID', 'inner')
                ->get();
            $leadAdh = $query->row();
        } else {
            $query = $this->db->where('Com_MasterID', ($LeaderID))
                ->select('Com_MasterID,HouseNo,C_Address')
                ->from('srp_erp_ngo_com_communitymaster')
                ->get();
            $leadAdh = $query->row();
        }

        if (!empty($leadAdh)) {
            echo "<option value='" . $leadAdh->C_Address . "' selected='selected' >" . $leadAdh->C_Address . "</option>";
        }

    }

    function get_hERegionID($FamHouseSt, $housesExitId, $LeaderID)
    {

        if ((!empty($FamHouseSt) && $FamHouseSt == '1') && (!empty($housesExitId))) {
            $query = $this->db->where('hEnrollingID', ($housesExitId))
                ->select('Com_MasterID,RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description')
                ->from('srp_erp_ngo_com_house_enrolling')
                ->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID', 'inner')
                ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID', 'inner')
                ->join('srp_erp_statemaster', 'srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.RegionID', 'inner')
                ->get();
            $leadAdh = $query->row();
        } else {
            $query = $this->db->where('Com_MasterID', ($LeaderID))
                ->select('Com_MasterID,RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description')
                ->from('srp_erp_ngo_com_communitymaster')
                ->join('srp_erp_statemaster', 'srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.RegionID', 'inner')
                ->get();
            $leadAdh = $query->row();
        }

        if (!empty($leadAdh)) {
            echo "<option value='" . $leadAdh->RegionID . "' selected='selected' >" . $leadAdh->Description . "</option>";
        }
    }

    function get_hEHouseNo($FamHouseSt, $housesExitId, $LeaderID)
    {

        if ((!empty($FamHouseSt) && $FamHouseSt == '1') && (!empty($housesExitId))) {
            $query = $this->db->where('hEnrollingID', ($housesExitId))
                ->select('Com_MasterID,HouseNo,C_Address')
                ->from('srp_erp_ngo_com_house_enrolling')
                ->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID', 'inner')
                ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID', 'inner')
                ->get();
            $leadAdh = $query->row();
        } else {
            $query = $this->db->where('Com_MasterID', ($LeaderID))
                ->select('Com_MasterID,HouseNo,C_Address')
                ->from('srp_erp_ngo_com_communitymaster')
                ->get();
            $leadAdh = $query->row();
        }

        if (!empty($leadAdh)) {
            echo "<option value='" . $leadAdh->HouseNo . "' selected='selected' >" . $leadAdh->HouseNo . "</option>";
        }
    }

    function load_ngoHouseExitDel()
    {

        $housesExitId = $this->input->post('housesExitId');
        $data = $this->db->query("SELECT *  FROM srp_erp_ngo_com_house_enrolling WHERE hEnrollingID={$housesExitId} ")->row_array();

        return $data;
    }

    function load_ngoHouseHeader()
    {

        $FamMasterID = $this->input->post('FamMasterID');
        $data = $this->db->query("SELECT *,srp_erp_ngo_com_house_enrolling.FamMasterID,srp_erp_ngo_com_familymaster.LeaderID  FROM srp_erp_ngo_com_house_enrolling INNER JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_house_enrolling.FamMasterID WHERE srp_erp_ngo_com_house_enrolling.FamMasterID={$FamMasterID} ")->row_array();

        return $data;
    }

    function save_familyHouseEnroll()
    {

        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $hEnrollingID = $this->input->post('hEnrollingID');
        $FamMasterID = $this->input->post('FamMasterID2');

        $FamHouseCn = $this->input->post('FamHouseCn');

        $data['FamMasterID'] = $this->input->post('FamMasterID2');
        $data['FamHouseSt'] = $this->input->post('FamHouseSt');
        $data['Link_hEnrollingID'] = $this->input->post('housesExitId');
        $data['ownershipAutoID'] = $this->input->post('ownershipAutoID');
        $data['hESizeInPerches'] = $this->input->post('hESizeInPerches');
        $data['hTypeAutoID'] = $this->input->post('hTypeAutoID');
        $data['isHmElectric'] = $this->input->post('isHmElectric');
        $data['isHmWaterSup'] = $this->input->post('isHmWaterSup');
        $data['isHmToilet'] = $this->input->post('isHmToilet');
        $data['isHmBathroom'] = $this->input->post('isHmBathroom');
        $data['isHmTelephone'] = $this->input->post('isHmTelephone');
        $data['isHmKitchen'] = $this->input->post('isHmKitchen');

        if ($hEnrollingID == '') {

            $data['companyID'] = $companyID;

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];

            $this->db->insert('srp_erp_ngo_com_house_enrolling', $data);
            $last_id = $this->db->insert_id();
            //end of adding

        } else {

            $last_id = $hEnrollingID;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->update('srp_erp_ngo_com_house_enrolling', $data, array('hEnrollingID' => $hEnrollingID));

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Community Family House Enrolling Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Community Family House Enrolling Added Successfully.', $last_id);

        }

    }


    function get_memMoveState($editFamMasID, $edit_femMm)
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_familydetails.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_familydetails.companyID = " . $companyID . $deleted;

        if (!empty($edit_femMm)) {

            $checkIsLead = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE $where AND FamDel_ID='" . $edit_femMm . "' AND FamMasterID='" . $editFamMasID . "' AND srp_erp_ngo_com_familydetails.isLeader='1'")->row_array();

            $quMem = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE $where AND FamDel_ID='" . $edit_femMm . "' AND FamMasterID='" . $editFamMasID . "'");
            $thnMem = $quMem->row();


            $checkIsIn = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE $where AND FamDel_ID !='" . $edit_femMm . "' AND srp_erp_ngo_com_familydetails.isLeader ='1' AND Com_MasterID='" . $thnMem->Com_MasterID . "'")->row_array();


            $quMemOnce = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE $where AND FamDel_ID='" . $edit_femMm . "' AND FamMasterID='" . $editFamMasID . "' AND isMove='0'");
            $thnMemOnce = $quMemOnce->row();

            $checkIsOnce = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE $where AND FamDel_ID !='" . $edit_femMm . "' AND Com_MasterID='" . $thnMemOnce->Com_MasterID . "'")->row_array();

        }

        echo json_encode(
            array(
                "checkIsLead" => $checkIsLead['Com_MasterID'],
                "checkIsIn" => $checkIsIn['Com_MasterID'],
                "checkIsOnce" => $checkIsOnce['Com_MasterID'],
            )
        );

    }

    function save_familyMaster()
    {

        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $FamMasterID = $this->input->post('FamMasterID');
        $date_format_policy = date_format_policy();
        $FamilyAddedDate = $this->input->post('FamilyAddedDate');
        $data['FamilyAddedDate'] = input_format_date($FamilyAddedDate, $date_format_policy);
        $LeaderID = $this->input->post('LeaderID');
        $data['LedgerNo'] = $this->input->post('LedgerNo');
        $data['FamilyName'] = $this->input->post('FamilyName');
        $data['FamAncestory'] = $this->input->post('FamAncestory');
        $data['AncestryCatID'] = $this->input->post('AncestryCatID');
        $data['ComEconSteID'] = $this->input->post('ComEconSteID');
        $data['monthlyExpenses'] = $this->input->post('monthlyExpenses');
        $data['femExpensesRemark'] = $this->input->post('femExpensesRemark');
        $data['femHelpNeedId'] = $this->input->post('femHelpNeedId');
        $data['femNeededHelp'] = $this->input->post('femNeededHelp');

        $isCommExist = $this->db->query("SELECT LeaderID FROM srp_erp_ngo_com_familymaster WHERE companyID={$companyID} AND LeaderID = '$LeaderID' AND isDeleted='0' ")->row('LeaderID');

        $querAct = $this->db->query('SELECT isActive FROM srp_erp_ngo_com_communitymaster WHERE companyID="' . $companyID . '" AND Com_MasterID="' . $LeaderID . '" AND isActive = "1" ');
        $leadAct = $querAct->row();

        if (($FamMasterID == '' || $FamMasterID == NULL) && isset($isCommExist)) {
            return array('e', 'The head of the family is already Exists');
        } elseif (($FamMasterID == '' || $FamMasterID == NULL) && empty($leadAct)) {
            return array('i', 'The person is inactive! could not allow to be assigned.');
        } else {
            if ($FamMasterID == '') {
                $serial = $this->db->query("select IF ( isnull(MAX(FamilySerialNo)), 1, (MAX(FamilySerialNo) + 1) ) AS FamilySerialNo FROM `srp_erp_ngo_com_familymaster` WHERE companyID={$companyID}")->row_array();

                $data['FamilySerialNo'] = $serial['FamilySerialNo'];
                $data['FamilyCode'] = 'FAM';
                $data['FamilySystemCode'] = ($company_code . '/' . 'FAM' . str_pad($data['FamilySerialNo'], 6,
                        '0', STR_PAD_LEFT));
                $data['LeaderID'] = $this->input->post('LeaderID');
                $data['FamilyName'] = $this->input->post('FamilyName');

                $data['companyID'] = $companyID;

                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserName'] = $this->common_data['current_user'];

                $this->db->insert('srp_erp_ngo_com_familymaster', $data);
                $last_id = $this->db->insert_id();

                //add leader as family member
                $qchkMove = $this->db->query('SELECT FamDel_ID,Com_MasterID,isMove FROM srp_erp_ngo_com_familydetails WHERE companyID="' . $companyID . '" AND isDeleted = "0" AND Com_MasterID="' . $LeaderID . '" AND isMove = "0" ');
                $rchkMove = $qchkMove->result();
                if (!empty($rchkMove)) {
                    foreach ($rchkMove as $rowM) {

                        $query = $this->db->query("UPDATE srp_erp_ngo_com_familydetails SET isMove='1' WHERE companyID='" . $companyID . "' AND FamDel_ID = '" . $rowM->FamDel_ID . "'");

                    }
                }

                $data2['FamMasterID'] = $last_id;
                $data2['Com_MasterID'] = $this->input->post('LeaderID');
                $data2['isLeader'] = 1;
                $data2['FamMemAddedDate'] = input_format_date($FamilyAddedDate, $date_format_policy);

                $data2['companyID'] = $companyID;

                $data2['createdPCID'] = $this->common_data['current_pc'];
                $data2['createdUserID'] = $this->common_data['current_userID'];
                $data2['createdDateTime'] = $this->common_data['current_date'];
                $data2['createdUserName'] = $this->common_data['current_user'];

                $this->db->insert('srp_erp_ngo_com_familydetails', $data2);
                //end of adding

            } else {

                $LeaderID = $this->input->post('LeaderID');
                if (isset($LeaderID)) {

                    $data['LeaderID'] = $this->input->post('LeaderID');

                }
                $last_id = $FamMasterID;
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = $this->common_data['current_user'];

                $this->db->update('srp_erp_ngo_com_familymaster', $data, array('FamMasterID' => $FamMasterID));

            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return array('e', 'Community Family Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();

                return array('s', 'Community Family Added Successfully.', $last_id);

            }
        }
    }

    function load_ngoFamilyHeader()
    {
        $convertFormat = convert_date_format_sql();

        $FamMasterID = $this->input->post('FamMasterID');
        $data = $this->db->query("select FamilyName,FamMasterID,DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate,LeaderID,FamAncestory,ComEconSteID,AncestryCatID,LedgerNo,monthlyExpenses,femExpensesRemark,femHelpNeedId,femNeededHelp from srp_erp_ngo_com_familymaster WHERE FamMasterID={$FamMasterID} ")->row_array();

        return $data;
    }

//add new community
    function save_communityMem_detail()
    {

        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];

        $FamMasterID = $this->input->post('FamMasterID');

        $nameWithIni = $this->input->post('nameWithIni');
        $TitleID = $this->input->post('TitleID');
        $nameWithFull = $this->input->post('nameWithFull');
        $genderID = $this->input->post('genderID');
        $newMemDOB = $this->input->post('newMemDOB');
        $NewRelatnID = $this->input->post('NewRelatnID');

        $FamMemAddedDate = date('Y-m-d');
        $date_format_policy = date_format_policy();


        $query = $this->db->where('FamMasterID', ($FamMasterID))
            ->select('LeaderID')
            ->from('srp_erp_ngo_com_familymaster')
            ->get();
        $leadDel = $query->row();

        $queryDel = $this->db->where('Com_MasterID', ($leadDel->LeaderID))
            ->select('countyID,provinceID,districtID,districtDivisionID,jammiyahDivisionID,RegionID,GS_Division,GS_No,P_Address,C_Address,TP_home,TP_Mobile,CountryCodePrimary,AreaCodePrimary,HouseNo')
            ->from('srp_erp_ngo_com_communitymaster')
            ->get();
        $leadDel = $queryDel->row();
        if (!empty($leadDel)) {

            $countyID = $leadDel->countyID;
            $provinceID = $leadDel->provinceID;
            $districtID = $leadDel->districtID;
            $districtDivisionID = $leadDel->districtDivisionID;
            $jammiyahDivisionID = $leadDel->jammiyahDivisionID;
            $RegionID = $leadDel->RegionID;
            $GS_Division = $leadDel->GS_Division;
            $GS_No = $leadDel->GS_No;
            $P_Address = $leadDel->P_Address;
            $C_Address = $leadDel->C_Address;
            $TP_home = $leadDel->TP_home;
            $TP_Mobile = $leadDel->TP_Mobile;
            $CountryCodePrimary = $leadDel->CountryCodePrimary;
            $AreaCodePrimary = $leadDel->AreaCodePrimary;
            $HouseNo = $leadDel->HouseNo;
        }

        foreach ($nameWithIni as $key => $newMemId) {

            $serial = $this->db->query("select IF ( isnull(MAX(SerialNo)), 1, (MAX(SerialNo) + 1) ) AS SerialNo FROM `srp_erp_ngo_com_communitymaster` WHERE companyID={$companyID}")->row_array();

            $data['SerialNo'] = $serial['SerialNo'];
            $data['MemberCode'] = 'MEM';;
            $data['MemberCode'] = ($company_code . '/' . 'MEM' . str_pad($data['SerialNo'], 6,
                    '0', STR_PAD_LEFT));

            $data['CName_with_initials'] = $nameWithIni[$key];

            $data['TitleID'] = $TitleID[$key];
            $data['CFullName'] = $nameWithFull[$key];
            $data['GenderID'] = $genderID[$key];

            $data['CDOB'] = input_format_date($newMemDOB[$key], $date_format_policy);

            //Get the current UNIX timestamp.
            $now = time();
//Get the timestamp of the person's date of birth.
            $dob = strtotime(input_format_date($newMemDOB[$key], $date_format_policy));

//Calculate the difference between the two timestamps.
            $difference = $now - $dob;

//There are 31556926 seconds in a year.
            $memAge = floor($difference / 31556926);

            $data['Age'] = $memAge . '' . 'yrs';
            $data['countyID'] = $countyID;
            $data['provinceID'] = $provinceID;
            $data['districtID'] = $districtID;
            $data['districtDivisionID'] = $districtDivisionID;
            $data['jammiyahDivisionID'] = $jammiyahDivisionID;
            $data['RegionID'] = $RegionID;
            $data['GS_Division'] = $GS_Division;
            $data['GS_No'] = $GS_No;
            $data['P_Address'] = $P_Address;
            $data['C_Address'] = $C_Address;
            $data['TP_home'] = $TP_home;
            $data['TP_Mobile'] = $TP_Mobile;
            $data['CountryCodePrimary'] = $CountryCodePrimary;
            $data['AreaCodePrimary'] = $AreaCodePrimary;
            $data['HouseNo'] = $HouseNo;

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_ngo_com_communitymaster', $data);
            $last_id = $this->db->insert_id();


            //assign to family details
            $datFm['FamMasterID'] = $FamMasterID;

            $datFm['Com_MasterID'] = $last_id;

            $datFm['relationshipID'] = $NewRelatnID[$key];

            $datFm['FamMemAddedDate'] = input_format_date($FamMemAddedDate, $date_format_policy);

            $datFm['modifiedPCID'] = $this->common_data['current_pc'];
            $datFm['modifiedUserID'] = $this->common_data['current_userID'];
            $datFm['modifiedUserName'] = $this->common_data['current_user'];
            $datFm['modifiedDateTime'] = $this->common_data['current_date'];

            $datFm['companyID'] = $this->common_data['company_data']['company_id'];

            $datFm['createdPCID'] = $this->common_data['current_pc'];
            $datFm['createdUserID'] = $this->common_data['current_userID'];
            $datFm['createdUserName'] = $this->common_data['current_user'];
            $datFm['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_ngo_com_familydetails', $datFm);

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();

            return array('e', 'save failed');
        } else {

            $this->db->trans_commit();

            return array('s', 'Records inserted successfully');
        }

    }

    function save_famMembers_detail()
    {

        $this->db->trans_start();

        $FamMasterID = $this->input->post('FamMasterID');

        $Com_MasterID = $this->input->post('Com_MasterID');

        $FamMemAddedDate = $this->input->post('FamMemAddedDate');
        $memRelaID = $this->input->post('memRelaID');
        $date_format_policy = date_format_policy();

        foreach ($Com_MasterID as $key => $femMemId) {

            $chkExist = $this->db->query("select * from srp_erp_ngo_com_familydetails WHERE FamMasterID=$FamMasterID AND Com_MasterID=$Com_MasterID[$key] ")->row_array();

            if (empty($chkExist)) {
                $data[$key]['FamMasterID'] = $FamMasterID;

                $data[$key]['Com_MasterID'] = $Com_MasterID[$key];
                $data[$key]['relationshipID'] = $memRelaID[$key];

                $data[$key]['FamMemAddedDate'] = input_format_date($FamMemAddedDate, $date_format_policy);

                $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
                $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
                $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
                $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];

                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];

                //check def mem

                $chknMove = $this->db->query('SELECT FamDel_ID,Com_MasterID,isMove FROM srp_erp_ngo_com_familydetails WHERE Com_MasterID="' . $Com_MasterID[$key] . '" AND isMove = "0"');
                $chkMove = $chknMove->row();
                if (!empty($chkMove)) {

                    $isMove = 1;
                } else {
                    $isMove = 0;
                }
                $data[$key]['isMove'] = $isMove;

            } else {
                return array('e', 'The member already exist');
            }

        }

        $this->db->insert_batch('srp_erp_ngo_com_familydetails', $data);
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();

            return array('e', 'save failed');
        } else {

            $this->db->trans_commit();

            return array('s', 'Records inserted successfully');
        }

    }

    function update_comFem_member_details()
    {

        $this->db->trans_start();

        $FamMasterID = $this->input->post('FamMasterID');
        $FamDel_ID = $this->input->post('FamDel_ID');

        $Com_MasterID = $this->input->post('Com_MasterID');
        $relationshipID = $this->input->post('relationshipID');
        $FamMemAddedDate = $this->input->post('FamMemAddedDate');
        $isMoveId = $this->input->post('isMoveId');

        $companyID = $this->common_data['company_data']['company_id'];

        $date_format_policy = date_format_policy();
        $master = $this->db->query("select * from srp_erp_ngo_com_familymaster WHERE FamMasterID=$FamMasterID")->row_array();

        if ($isMoveId == '0') {
            //add def mem check
            $qchkMove = $this->db->query('SELECT FamDel_ID,Com_MasterID,isMove FROM srp_erp_ngo_com_familydetails WHERE companyID="' . $companyID . '" AND Com_MasterID="' . $Com_MasterID . '"');
            $rchkMove = $qchkMove->result();
            if (!empty($rchkMove)) {
                foreach ($rchkMove as $rowM) {

                    $query = $this->db->query("UPDATE srp_erp_ngo_com_familydetails SET isMove='1' WHERE companyID='" . $companyID . "' AND FamDel_ID = '" . $rowM->FamDel_ID . "'");

                }
            }
        }

        $data['Com_MasterID'] = $Com_MasterID;
        $data['relationshipID'] = $relationshipID;
        $data['FamMemAddedDate'] = input_format_date($FamMemAddedDate, $date_format_policy);
        $data['isMove'] = $isMoveId;

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];


        $this->db->update('srp_erp_ngo_com_familydetails', $data,
            array('FamDel_ID' => $FamDel_ID));
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Member Detail : update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();

            return array('status' => FALSE);
        } else {
            $this->session->set_flashdata('s', 'Member Detail : Records inserted successfully.');
            $this->db->trans_commit();

            return array('s', 'Member Detail : Records inserted successfully. ');
        }

    }

    function delete_family_master()
    {

        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');

        $isFamInChk = $this->db->query("SELECT FamMasterID FROM srp_erp_ngo_com_familydetails WHERE FamMasterID = '$FamMasterID' ")->row('FamMasterID');

        if (empty($isFamInChk)) {

            $data = array('isDeleted' => 1, 'deletedDate' => $this->common_data['current_date'],
                'deletedEmpID' => $this->common_data['current_userID']);

            $this->db->where('FamMasterID', $FamMasterID);
            $output = $this->db->update('srp_erp_ngo_com_familymaster', $data);
            if ($output) {
                return array('error' => 0, 'message' => 'Successfully family master deleted');
            } else {
                return array('error' => 1, 'message' => 'Error while updating');
            }
        } else {
            return array('error' => 1, 'message' => 'The family master cannot be deleted! This has already been used in Family Member Details');
        }

    }

    function familyCreate_confirmation()
    {
        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');

        $data['fam_memRela'] = $this->db->query("SELECT FamDel_ID ,srp_erp_ngo_com_familydetails.FamMasterID ,srp_erp_ngo_com_familydetails.Com_MasterID ,srp_erp_ngo_com_familymaster.LeaderID FROM `srp_erp_ngo_com_familydetails` INNER JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_familydetails.Com_MasterID  INNER JOIN srp_erp_family_relationship ON srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID WHERE srp_erp_ngo_com_familydetails.FamMasterID=$FamMasterID")->result_array();

        if (!empty($data['fam_memRela'])) {

            $data = array('confirmedYN' => 1, 'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user']);

            $this->db->where('FamMasterID', $FamMasterID);
            $this->db->update('srp_erp_ngo_com_familymaster', $data);

            return array('s', 'Confirmed Successfully. ');
        } else {
            return array('w', 'The family cannot be confirmed! relationship not assign for head of the family. ');

        }

    }

    function fetch_commFamily_confirmation($FamMasterID)
    {
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("select FamMasterID,confirmedByEmpID ,confirmedByName ,DATE_FORMAT(confirmedDate,'.$convertFormat. %h:%i:%s') AS confirmedDate, confirmedYN,FamMasterID ,FamilyCode ,FamilySystemCode ,FamilySerialNo,FamilyName, DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate,LedgerNo,CName_with_initials,CFullName,srp_erp_ngo_com_communitymaster.HouseNo,C_Address,CurrentStatus,name,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,CNIC_No,TP_Mobile,TP_home,EmailID,P_Address,GS_Division,GS_No,CImage,srp_erp_ngo_com_communitymaster.SerialNo AS SerialNos,areac.stateID,areac.Description AS arDescription,divisionc.stateID,divisionc.Description AS diviDescription,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus from srp_erp_ngo_com_familymaster LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID=srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus WHERE FamMasterID=$FamMasterID ")->row_array();
        $data['family_mem'] = $this->db->query("select FamDel_ID ,srp_erp_ngo_com_familydetails.FamMasterID ,srp_erp_ngo_com_familydetails.Com_MasterID,srp_erp_ngo_com_familydetails.isMove,CFullName,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB ,DATE_FORMAT(FamMemAddedDate,'{$convertFormat}') AS FamMemAddedDate,CName_with_initials,srp_erp_ngo_com_communitymaster.isActive,srp_erp_ngo_com_communitymaster.DeactivatedFor,relationship,srp_erp_gender.name,CurrentStatus,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus from `srp_erp_ngo_com_familydetails` LEFT JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familydetails.Com_MasterID LEFT JOIN srp_erp_family_relationship ON srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus WHERE FamMasterID=$FamMasterID ORDER BY srp_erp_ngo_com_familydetails.FamDel_ID ASC ")->result_array();

        $data['famHouseEnrl'] = $this->db->query("SELECT hEnr.hEnrollingID,hEnr.companyID AS companyID,fm.FamilySystemCode,fm.FamilyName,hEnr.FamHouseSt,hEnr.Link_hEnrollingID,cm.CName_with_initials,cm.C_Address,onrSp.ownershipDescription,tpMas.hTypeDescription,hEnr.hESizeInPerches,hEnr.isHmElectric,hEnr.isHmWaterSup,hEnr.isHmToilet,hEnr.isHmBathroom,hEnr.isHmTelephone,hEnr.isHmKitchen FROM srp_erp_ngo_com_house_enrolling hEnr 
LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.FamMasterID = hEnr.FamMasterID 
LEFT JOIN srp_erp_ngo_com_house_ownership_master onrSp ON onrSp.ownershipAutoID = hEnr.ownershipAutoID
LEFT JOIN srp_erp_ngo_com_house_type_master tpMas ON tpMas.hTypeAutoID = hEnr.hTypeAutoID
LEFT JOIN srp_erp_ngo_com_communitymaster cm ON cm.Com_MasterID = fm.LeaderID
WHERE hEnr.FamMasterID=$FamMasterID ORDER BY hEnr.hEnrollingID")->row_array();

        return $data;

    }

//end of family adding

    /*OP community */

    function save_comBeneficiary()
    {
        $this->load->library('sequence');
        $companyID = $this->common_data['company_data']['company_id'];
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $projectID = trim($this->input->post('projectID') ?? '');
        $contactID = trim($this->input->post('contactID') ?? '');
        $templateType = trim($this->input->post('templateType') ?? '');
        $Com_MasterID = trim($this->input->post('Com_MasterID') ?? '');
        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');

        $date_format_policy = date_format_policy();
        //$beneficiarySystemCode = $this->sequence->sequence_generator('BEN');

        $this->db->select('phoneCountryCodePrimary,phonePrimary');
        $this->db->where('phoneCountryCodePrimary', trim($this->input->post('countryCodePrimary') ?? ''));
        $this->db->where('phonePrimary', trim($this->input->post('phonePrimary') ?? ''));
        $this->db->where('companyID', $companyID);
        if ($benificiaryID) {
            $this->db->where('benificiaryID !=', $benificiaryID);
        }
        $this->db->from('srp_erp_ngo_beneficiarymaster');
        $recordExist = $this->db->get()->row_array();
        if (!empty($recordExist)) {
            return array('w', 'Primary Phone Number is already Exist.');
            exit();
        }

        $registeredDate = trim($this->input->post('registeredDate') ?? '');
        $dateOfBirth = trim($this->input->post('dateOfBirth') ?? '');
        $format_registeredDate = null;
        if (isset($registeredDate) && !empty($registeredDate)) {
            $format_registeredDate = input_format_date($registeredDate, $date_format_policy);
        }
        $format_dateOfBirth = null;
        if (isset($dateOfBirth) && !empty($dateOfBirth)) {
            $format_dateOfBirth = input_format_date($dateOfBirth, $date_format_policy);
        }
        $this->db->trans_start();
        $data['secondaryCode'] = trim($this->input->post('secondaryCode') ?? '');
        $data['benificiaryType'] = trim($this->input->post('benificiaryType') ?? '');
        $data['projectID'] = $projectID;
        $data['Com_MasterID'] = trim($this->input->post('Com_MasterID') ?? '');
        $data['titleID'] = trim($this->input->post('emp_title') ?? '');
        $data['FamMasterID'] = trim($this->input->post('FamMasterID') ?? '');
        //  $data['EconStateID'] = trim($this->input->post('EconStateID') ?? '');
        $data['fullName'] = trim($this->input->post('fullName') ?? '');
        $data['nameWithInitials'] = trim($this->input->post('nameWithInitials') ?? '');
        $data['registeredDate'] = $format_registeredDate;
        $data['dateOfBirth'] = $format_dateOfBirth;
        $data['nameWithInitials'] = trim($this->input->post('nameWithInitials') ?? '');
        $data['email'] = trim($this->input->post('email') ?? '');
        $data['phoneCountryCodePrimary'] = trim($this->input->post('countryCodePrimary') ?? '');
        $data['phoneAreaCodePrimary'] = trim($this->input->post('phoneAreaCodePrimary') ?? '');
        $data['phonePrimary'] = trim($this->input->post('phonePrimary') ?? '');
        $data['phoneCountryCodeSecondary'] = trim($this->input->post('countryCodeSecondary') ?? '');
        $data['phoneAreaCodeSecondary'] = trim($this->input->post('phoneAreaCodeSecondary') ?? '');
        $data['phoneSecondary'] = trim($this->input->post('phoneSecondary') ?? '');
        $data['countryID'] = trim($this->input->post('countryID') ?? '');
        $data['province'] = trim($this->input->post('province') ?? '');
        $data['district'] = trim($this->input->post('district') ?? '');
        $data['division'] = trim($this->input->post('division') ?? '');
        $data['subDivision'] = trim($this->input->post('subDivision') ?? '');
        $data['postalCode'] = trim($this->input->post('postalcode') ?? '');
        $data['address'] = trim($this->input->post('address') ?? '');
        $data['familyDescription'] = trim($this->input->post('familyDescription') ?? '');

        if ($templateType == 'helpAndNest') {
            $data['ownLandAvailable'] = trim($this->input->post('ownLandAvailable') ?? '');
            $data['ownLandAvailableComments'] = trim($this->input->post('ownLandAvailableComments') ?? '');
            $data['NIC'] = trim($this->input->post('nationalIdentityCardNo') ?? '');
            $data['totalSqFt'] = trim($this->input->post('totalSqFt') ?? '');
            $data['totalCost'] = trim($this->input->post('totalCost') ?? '');
            $data['familyMembersDetail'] = trim($this->input->post('familyDetail') ?? '');
            $data['reasoninBrief'] = trim($this->input->post('reasoninBrief') ?? '');
        }

        if ($benificiaryID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('benificiaryID', $benificiaryID);
            $update = $this->db->update('srp_erp_ngo_beneficiarymaster', $data);
            if ($update) {
                $projectTable = $this->db->query("select projectID,beneficiaryID from srp_erp_ngo_beneficiaryprojects  WHERE projectID={$projectID} AND beneficiaryID = {$benificiaryID}")->row_array();

                if (empty($projectTable)) {
                    $data_project['projectID'] = $projectID;
                    $data_project['beneficiaryID'] = $benificiaryID;
                    $data_project['companyID'] = $companyID;
                    $data_project['createdUserGroup'] = $this->common_data['user_group'];
                    $data_project['createdPCID'] = $this->common_data['current_pc'];
                    $data_project['createdUserID'] = $this->common_data['current_userID'];
                    $data_project['createdUserName'] = $this->common_data['current_user'];
                    $data_project['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_ngo_beneficiaryprojects', $data_project);
                }

                if ($contactID != '') {
                    $projectWiseDocumentTable = $this->db->query("select * FROM srp_erp_ngo_documentdescriptionforms WHERE beneficiaryID = {$benificiaryID}")->result_array();

                    if (!empty($projectWiseDocumentTable)) {
                        foreach ($projectWiseDocumentTable as $doc) {
                            $dataDocument['DocDesSetID'] = $doc['DocDesSetID'];
                            $dataDocument['DocDesID'] = $doc['DocDesID'];
                            $dataDocument['beneficiaryID'] = $benificiaryID;
                            $dataDocument['projectID'] = $projectID;
                            $dataDocument['FileName'] = $doc['FileName'];
                            $dataDocument['companyID'] = current_companyID();
                            $dataDocument['CreatedPC'] = current_pc();
                            $dataDocument['CreatedUserName'] = current_employee();
                            $dataDocument['CreatedDate'] = current_date();
                            $this->db->insert('srp_erp_ngo_documentdescriptionforms', $dataDocument);
                        }
                    }
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return array('e', 'Beneficiary Update Failed ' /*. $this->db->_error_message()*/);

                } else {
                    $this->db->trans_commit();

                    return array('s', 'Beneficiary Updated Successfully.', $benificiaryID);
                }
            }
        } else {
            $isComBenExist = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_beneficiaryfamilydetails WHERE Com_MasterID = '$Com_MasterID' ")->row('Com_MasterID');

            // if (!empty($isComBenExist)) {
            // return array('e', 'The community member is already Exists');
            //  exit();
            //  } else {
            //$data['systemCode'] = $beneficiarySystemCode;
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_beneficiarymaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($last_id) {
                $projectTable = $this->db->query("select projectID,beneficiaryID from srp_erp_ngo_beneficiaryprojects  WHERE projectID={$projectID} AND beneficiaryID = {$last_id}")->row_array();

                if (empty($projectTable)) {
                    $data_project['projectID'] = $projectID;
                    $data_project['beneficiaryID'] = $last_id;
                    $data_project['companyID'] = $companyID;
                    $data_project['createdUserGroup'] = $this->common_data['user_group'];
                    $data_project['createdPCID'] = $this->common_data['current_pc'];
                    $data_project['createdUserID'] = $this->common_data['current_userID'];
                    $data_project['createdUserName'] = $this->common_data['current_user'];
                    $data_project['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_ngo_beneficiaryprojects', $data_project);

                }

                if ($Com_MasterID != NULL || $Com_MasterID != '') {

                    $queryFam = $this->db->query("SELECT FamMasterID FROM srp_erp_ngo_com_familydetails WHERE Com_MasterID ={$Com_MasterID} ");
                    $rowFam = $queryFam->row();
                    if (!empty($rowFam) || $rowFam != NULL) {
                        $queryFamAdd = $this->db->query("SELECT FamMasterID,srp_erp_ngo_com_familydetails.Com_MasterID,CFullName,relationshipID,CDOB,GenderID,CNIC_No FROM srp_erp_ngo_com_familydetails INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familydetails.Com_MasterID WHERE FamMasterID ={$rowFam->FamMasterID} ");
                        $rFamAdd = $queryFamAdd->result();
                        foreach ($rFamAdd as $resFamAdd) {
                            $data = array(
                                "beneficiaryID" => $last_id,
                                "Com_MasterID" => $resFamAdd->Com_MasterID,
                                "name" => $resFamAdd->CFullName,
                                "relationship" => $resFamAdd->relationshipID,
                                "DOB" => $resFamAdd->CDOB,
                                "gender" => $resFamAdd->GenderID,
                                "idNO" => $resFamAdd->CNIC_No,
                                "createdUserID" => current_userID(),
                                "createdPCid" => current_pc(),
                                "timestamp" => $this->common_data['current_date']
                            );
                            $result = $this->db->insert('srp_erp_ngo_beneficiaryfamilydetails', $data);
                        }
                    }

                }
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Beneficiary Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Beneficiary Saved Successfully.', $last_id);
            }

            //  }
        }
    }

    function save_comBeneficiary_familyDel()
    {
        $data = array(
            "beneficiaryID" => trim($this->input->post('benificiaryID') ?? ''),
            "Com_MasterID" => trim($this->input->post('Com_MasterIDs') ?? ''),
            "name" => trim($this->input->post('name') ?? ''),
            "relationship" => trim($this->input->post('relationshipType') ?? ''),
            "nationality" => trim($this->input->post('nationality') ?? ''),
            "DOB" => format_date_mysql_datetime(trim($this->input->post('DOB') ?? '')),
            "gender" => trim($this->input->post('gender') ?? ''),
            "idNO" => trim($this->input->post('idNO') ?? ''),
            "createdUserID" => current_userID(),
            "createdPCid" => current_pc(),
            "timestamp" => $this->common_data['current_date']
        );
        $result = $this->db->insert('srp_erp_ngo_beneficiaryfamilydetails', $data);
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'Family detail added successfully'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error, Insert Error, Please contact your system support team'));
        }
    }

    function fetch_comBeneficiary_familyDel($benificiaryID)
    {
        $this->db->select("*,srp_erp_ngo_beneficiaryfamilydetails.name as name,r.relationship as relationshipDesc,c.Nationality as countryName,g.name as genderDesc");
        $this->db->from("srp_erp_ngo_beneficiaryfamilydetails");
        $this->db->join("srp_erp_family_relationship r", "r.relationshipID=srp_erp_ngo_beneficiaryfamilydetails.relationship", "left");
        $this->db->join("srp_nationality c", "c.NId = srp_erp_ngo_beneficiaryfamilydetails.nationality", "left");
        $this->db->join("srp_erp_gender g", "g.genderID = srp_erp_ngo_beneficiaryfamilydetails.gender", "left");
        $this->db->where("beneficiaryID", $benificiaryID);
        $output = $this->db->get()->result_array();

        return $output;
    }

    function delete_comBeneficiary_familyDel()
    {
        $this->db->delete('srp_erp_ngo_beneficiaryfamilydetails', array('empfamilydetailsID' => trim($this->input->post('empfamilydetailsID') ?? '')));
        return array('s', 'Deleted Successfully');
    }

    function comBeneficiary_confirmed()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $projectID = trim($this->input->post('projectID') ?? '');
        $this->db->select('beneficiaryProjectID');
        $this->db->where('beneficiaryID', $benificiaryID);
        $this->db->where('projectID', $benificiaryID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_ngo_beneficiaryprojects');
        $checkProjectConfirmed = $this->db->get()->row_array();

        $isInUse = $this->db->query("SELECT
	ddm.DocDescription as documentName
FROM
	srp_erp_ngo_documentdescriptionsetup dds
	INNER JOIN srp_erp_ngo_documentdescriptionmaster ddm ON dds.DocDesID = ddm.DocDesID
WHERE dds.DocDesID
	NOT IN (
		SELECT
			DocDesID
		FROM
			srp_erp_ngo_documentdescriptionforms  ddf
		WHERE
			ddf.projectID = {$projectID} AND ddf.beneficiaryID = {$benificiaryID}
	) AND dds.projectID = {$projectID} AND ngoDocumentID = 5 AND isMandatory = 1")->result_array();

        if (!empty($checkProjectConfirmed)) {
            return array('e', 'Beneficiary already confirmed');
        } else if (!empty($isInUse)) {
            return array('e', 'Mandatory Documents are need to upload');
        } else {

            $checkisConfirmedProjectExist = $this->db->query("select beneficiaryProjectID from srp_erp_ngo_beneficiaryprojects WHERE beneficiaryID={$benificiaryID} AND confirmedYN = 1 ")->row_array();

            if (empty($checkisConfirmedProjectExist)) {
                $benGeneratedID = $this->comBeneficiary_systemCode_generator($benificiaryID);
                $data = array(
                    'systemCode' => $benGeneratedID,
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']);
                $this->db->where('benificiaryID', $benificiaryID);
                $this->db->update('srp_erp_ngo_beneficiarymaster', $data);
            }

            $data_project['confirmedYN'] = 1;
            $data_project['confirmedDate'] = date('Y-m-d H:i:s');
            $data_project['confirmedByEmpID'] = $this->common_data['current_user'];
            $data_project['confirmedByName'] = $this->common_data['current_user'];
            $this->db->where('projectID', $projectID);
            $this->db->where('beneficiaryID', $benificiaryID);
            $this->db->update('srp_erp_ngo_beneficiaryprojects', $data_project);
            return array('s', 'Beneficiary : Confirmed Successfully. ');
        }
    }

    function load_comBeneficiary_header()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(registeredDate,\'' . $convertFormat . '\') AS registeredDate,DATE_FORMAT(dateOfBirth,\'' . $convertFormat . '\') AS dateOfBirth');
        $this->db->where('benificiaryID', $this->input->post('benificiaryID'));
        $this->db->from('srp_erp_ngo_beneficiarymaster');
        return $this->db->get()->row_array();
    }

    function comBeneficiary_systemCode_generator($benificiaryID)
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $benificiaryDetail = $this->db->query("SELECT benificiaryID,district,countryID,province,division,subDivision FROM srp_erp_ngo_beneficiarymaster WHERE benificiaryID = " . $benificiaryID . "")->row_array();

        if (!empty($benificiaryDetail['countryID'] && $benificiaryDetail['province'] && $benificiaryDetail['district'] && $benificiaryDetail['division'] && $benificiaryDetail['subDivision'])) {
            $benificiarySubDivision = $this->db->query("SELECT count(subDivision) as divisionTotal FROM srp_erp_ngo_beneficiarymaster WHERE companyID = " . $companyID . " AND subDivision = {$benificiaryDetail['subDivision']} AND confirmedYN = 1")->row_array();

            if ($benificiarySubDivision) {
                $subdivisionAreaCount = $benificiarySubDivision['divisionTotal'] + 1;
            } else {
                $subdivisionAreaCount = 1;
            }
            $countryCode = fetch_com_countryMaster_code($benificiaryDetail['countryID']);
            $provinceCode = fetch_com_stateMaster_name($benificiaryDetail['province']);
            $districtCode = fetch_com_stateMaster_name($benificiaryDetail['district']);
            $divisionCode = fetch_com_stateMaster_name($benificiaryDetail['division']);
            $subDivisionCode = fetch_com_stateMaster_name($benificiaryDetail['subDivision']);

            $benificiaryGeneratedCode = $countryCode . "/" . $provinceCode . "/" . $districtCode . "/" . $divisionCode . "/" . $subDivisionCode . "/" . $subdivisionAreaCount;

            return $benificiaryGeneratedCode;

        } else {
        }

    }

    function fetch_ngoSub_projectsForCom()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('ngoProjectID,description');
        $this->db->from('srp_erp_ngo_projects');
        $this->db->where('masterID', $this->input->post("ngoProjectID"));
        $this->db->where('srp_erp_ngo_projects.companyID', $companyID);
        $master = $this->db->get()->result_array();
        return $master;

    }

    function fetch_comBeneficiary_search()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";
        $projectID = $_GET['projectID'];
        $dataArr = array();
        $dataArr2 = array();

        if (!empty($search_string)) {
            $data = $this->db->query('SELECT benificiaryID,secondaryCode,benificiaryType,titleID,nameWithInitials,fullName,email,countryID,province,division,subDivision,district,postalCode,address,phoneCountryCodePrimary,phoneAreaCodePrimary,phonePrimary,DATE_FORMAT(registeredDate,\'' . $convertFormat . '\') AS registeredDate,DATE_FORMAT(dateOfBirth,\'' . $convertFormat . '\') AS dateOfBirth,CONCAT(fullName," ", systemCode) AS "Match" FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_ngo_beneficiaryprojects bp ON bm.benificiaryID = bp.beneficiaryID LEFT JOIN srp_erp_ngo_com_familymaster fm ON bm.FamMasterID = fm.FamMasterID WHERE bm.companyID = "' . $companyID . '" AND bm.confirmedYN = 1 AND bp.projectID != ' . $projectID . ' AND (fullName LIKE "' . $search_string . '" OR systemCode LIKE "' . $search_string . '") ')->result_array();
            //echo $this->db->last_query();
            if (!empty($data)) {
                foreach ($data as $val) {
                    $dataArr[] = array(
                        'benificiaryID' => $val["benificiaryID"],
                        'value' => $val["Match"],
                        'secondaryCode' => $val['secondaryCode'],
                        'benificiaryType' => $val['benificiaryType'],
                        'titleID' => $val['titleID'],
                        //  'econState' => $val['EconStateID'],
                        'dateOfBirth' => $val['dateOfBirth'],
                        'registeredDate' => $val['registeredDate'],
                        'nameWithInitials' => $val['nameWithInitials'],
                        'fullName' => $val['fullName'],
                        'email' => $val['email'],
                        'countryID' => $val['countryID'],
                        'province' => $val['province'],
                        'division' => $val['division'],
                        'subDivision' => $val['subDivision'],
                        'district' => $val['district'],
                        'postalCode' => $val['postalCode'],
                        'address' => $val['address'],
                        'phoneCountryCodePrimary' => $val['phoneCountryCodePrimary'],
                        'phoneAreaCodePrimary' => $val['phoneAreaCodePrimary'],
                        'phonePrimary' => $val['phonePrimary']
                    );
                }
                $dataArr2['suggestions'] = $dataArr;
            }
            return $dataArr2;
        }
    }

    function new_comBeneficiary_province()
    {
        $stateID = trim($this->input->post('hd_province_stateID') ?? '');
        $countyID = trim($this->input->post('hd_province_countryID') ?? '');
        $description = trim($this->input->post('province_description') ?? '');
        $shortCode = trim($this->input->post('province_shortCode') ?? '');

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('Description', $description);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (!empty($isExist)) {
            return array('e', 'Province is already Exists.');
            exit();
        }

        // check short Code already exist
        $this->db->select('stateID,Description,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('shortCode', $shortCode);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (!empty($isExist)) {
            return array('e', 'Short Code is already Exists.');
            exit();
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 1;

        if ($stateID) {

            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Province Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Province Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Province is created successfully.');
            } else {
                return array('e', 'Error in Province Creating');
            }
        }


    }

    function new_comBeneficiary_district()
    {
        $stateID = trim($this->input->post('hd_district_stateID') ?? '');
        $countyID = trim($this->input->post('hd_district_countryID') ?? '');
        $description = trim($this->input->post('district_description') ?? '');
        $shortCode = trim($this->input->post('district_shortCode') ?? '');
        $province = trim($this->input->post('hd_district_provinceID') ?? '');

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $province);
        $this->db->where('Description', $description);
        $this->db->where('type', 2);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();

        if (isset($isExist)) {
            return array('e', 'District is already Exists');
            exit();
        }

        // check short Code already exist
        $this->db->select('stateID,Description,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $province);
        $this->db->where('shortCode', $shortCode);
        $this->db->where('type', 2);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();

        if (isset($isExist)) {
            return array('e', 'Short Code is already Exists.');
            exit();
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 2;
        $data['masterID'] = $province;
        if ($stateID) {
            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'District Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'District Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'District is created successfully.');
            } else {
                return array('e', 'Error in District Creating');
            }
        }

    }

    function new_comBeneficiary_division()
    {
        $stateID = trim($this->input->post('hd_division_stateID') ?? '');
        $countyID = trim($this->input->post('hd_division_countryID') ?? '');
        $description = trim($this->input->post('division_description') ?? '');
        $district = trim($this->input->post('hd_division_districtID') ?? '');
        $shortCode = trim($this->input->post('division_shortCode') ?? '');

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('Description', $description);
        $this->db->where('type', 3);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $divisionisExist = $this->db->get()->row_array();
        if (isset($divisionisExist)) {
            return array('e', 'Division is already Exists');
        }

        // check short Code already exist
        $this->db->select('stateID,Description,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('shortCode', $shortCode);
        $this->db->where('type', 3);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $division_shortCodeisExist = $this->db->get()->row_array();
        if (isset($division_shortCodeisExist)) {
            return array('e', 'Short Code is already Exists.');
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 3;
        $data['masterID'] = $district;

        if ($stateID) {
            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Division Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Division Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Division is created successfully.');
            } else {
                return array('e', 'Error in Division Creating');
            }
        }

    }

    function new_comBeneficiary_type()
    {
        $type = trim($this->input->post('type') ?? '');
        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT beneficiaryTypeID FROM srp_erp_ngo_benificiarytypes WHERE companyID={$companyID} AND description='$type' ")->row('beneficiaryTypeID');

        if (isset($isExist)) {
            return array('e', 'This Beneficiary Type is already Exists');
        } else {

            $data = array(
                'description' => $type,
                'companyID' => current_companyID(),
                'createdUserGroup' => $this->common_data['user_group'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => $this->common_data['current_date']
            );
            $this->db->insert('srp_erp_ngo_benificiarytypes', $data);
            if ($this->db->affected_rows() > 0) {
                $titleID = $this->db->insert_id();
                return array('s', 'Beneficiary Type is created successfully.', $titleID);
            } else {
                return array('e', 'Error in Beneficiary Type Creating');
            }
        }

    }

    function load_beneficiary_documents()
    {
        $companyID = current_companyID();
        $benificiaryID = $this->input->post('benificiaryID');
        $projectID = $this->input->post('projectID');
        return $this->db->query("SELECT MASTER.DocDesID AS DcoumentAutoID,DocDesFormID, DocDescription, isMandatory, FileName
                                 FROM srp_erp_ngo_documentdescriptionmaster master
                                 JOIN srp_erp_ngo_documentdescriptionsetup setup ON setup.DocDesID = master.DocDesID
                                 INNER JOIN srp_erp_ngo_documentdescriptionforms forms ON forms.DocDesID = master.DocDesID
                                 AND beneficiaryID = {$benificiaryID} AND forms.projectID = {$projectID}
                                 WHERE master.companyID={$companyID} AND isDeleted=0 GROUP BY MASTER.DocDesID")->result_array();
    }

    function beneficiary_province()
    {
        $masterid = trim($this->input->post('masterid') ?? '');
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $masterid);
        $this->db->where(' type', 1);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }

    function beneficiary_area()
    {
        $masterid = trim($this->input->post('masterid') ?? '');
        $this->db->select('stateID,Description');
        $this->db->where('masterID', $masterid);
        $this->db->where(' type', 2);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }

    function beneficiary_division()
    {
        $masterid = trim($this->input->post('masterid') ?? '');
        $this->db->select('stateID,Description');
        $this->db->where('masterID', $masterid);
        $this->db->where(' type', 3);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }

    function beneficiary_sub_division()
    {
        $masterid = trim($this->input->post('masterid') ?? '');
        $this->db->select('stateID,Description');
        $this->db->where('masterID', $masterid);
        $this->db->where(' type', 4);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }

    function delete_comBeneficiary_master()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $this->db->where('documentID', 5);
        $this->db->where('documentAutoID', $benificiaryID);
        $this->db->delete('srp_erp_ngo_notes');

        $this->db->delete('srp_erp_ngo_beneficiarymaster', array('benificiaryID' => trim($this->input->post('benificiaryID') ?? '')));

        return TRUE;
    }

    function add_comBeneficiary_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $data['documentID'] = 5;
        $data['documentAutoID'] = $benificiaryID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_ngo_notes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Beneficiary Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();

            return array('s', 'Beneficiary Note Added Successfully.');

        }
    }

    function comBeneficiary_imgUpload()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $output_dir = "uploads/NGO/beneficiaryImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/beneficiaryImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Beneficiary_' . $companyID . '_' . trim($this->input->post('benificiaryID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['benificiaryImage'] = $fileName;

        $this->db->where('benificiaryID', trim($this->input->post('benificiaryID') ?? ''));
        $this->db->update('srp_erp_ngo_beneficiarymaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');
        }
    }

    function comBeneficiary_imgUpload_helpNest()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $output_dir = "uploads/NGO/beneficiaryProjectImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/beneficiaryProjectImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'HelpAndNest_' . $companyID . '_' . trim($this->input->post('benificiaryID') ?? '') . '_' . time() . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['helpAndNestImage'] = $fileName;

        $this->db->where('benificiaryID', trim($this->input->post('benificiaryID') ?? ''));
        $this->db->update('srp_erp_ngo_beneficiarymaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded Successfully.');
        }
    }

    function comBeneficiary_imgUpload_helpNest_two()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $output_dir = "uploads/NGO/beneficiaryProjectImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/beneficiaryProjectImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'HelpAndNest1_' . $companyID . '_' . trim($this->input->post('benificiaryID') ?? '') . '_' . time() . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['helpAndNestImage1'] = $fileName;

        $this->db->where('benificiaryID', trim($this->input->post('benificiaryID') ?? ''));
        $this->db->update('srp_erp_ngo_beneficiarymaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded Successfully.');
        }
    }


    function delete_comBen_masterNote_allDocument()
    {
        $this->db->where('notesID', $this->input->post('notesID'));
        $results = $this->db->delete('srp_erp_ngo_notes');
        return true;
    }

    function load_comBeneficiary_header_helpNest($benificiaryID)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,smpro.Description as provinceName,smdis.Description as districtName,smdiv.Description as divisionName,smsubdiv.Description as subDivisionName,DATE_FORMAT(registeredDate,\'' . $convertFormat . '\') AS registeredDate,DATE_FORMAT(dateOfBirth,\'' . $convertFormat . '\') AS dateOfBirth');
        $this->db->where('benificiaryID', $benificiaryID);
        $this->db->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_beneficiarymaster.FamMasterID', 'inner');
        $this->db->join('srp_erp_statemaster smpro', 'smpro.stateID=srp_erp_ngo_beneficiarymaster.province', 'left');
        $this->db->join('srp_erp_statemaster smdis', 'smdis.stateID=srp_erp_ngo_beneficiarymaster.district', 'left');
        $this->db->join('srp_erp_statemaster smdiv', 'smdiv.stateID=srp_erp_ngo_beneficiarymaster.division', 'left');
        $this->db->join('srp_erp_statemaster smsubdiv', 'smsubdiv.stateID=srp_erp_ngo_beneficiarymaster.subDivision', 'left');
        $this->db->from('srp_erp_ngo_beneficiarymaster');
        return $this->db->get()->row_array();
    }

    function load_comBeneficiary_multipleImages()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $this->db->select('beneficiaryImageID,isSelectedforPP,beneficiaryImage');
        $this->db->where('beneficiaryID', $benificiaryID);
        return $this->db->get('srp_erp_ngo_beneficiaryimages')->result_array();
    }


    function upload_comBeneficiary_multipleImage()
    {
        $companyID = current_companyID();
        $benificiaryID = $this->input->post('benificiaryID');

        $path = UPLOAD_PATH_POS . 'uploads/ngo/beneficiaryImage/'; // imagePath();
        if (!file_exists($path)) {
            mkdir("documents/ngo", 007);
            mkdir("documents/ngo/beneficiaryImage", 007);
        }
        $fileName = str_replace(' ', '', strtolower($benificiaryID)) . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = '5120'; // 5 MB
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload("doc_file")) {
            return array('e', 'Upload failed ' . $this->upload->display_errors());
        } else {

            $data = array(
                'beneficiaryID' => $benificiaryID,
                'beneficiaryImage' => $this->upload->data('file_name'),
                'companyID' => current_companyID(),
                'createdPCID' => current_pc(),
                'CreatedUserName' => current_employee(),
                'createdUserID' => current_userID(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_ngo_beneficiaryimages', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Image successfully uploaded');
            } else {
                return array('e', 'Error in Image upload');
            }

        }
    }

    function delete_comBeneficiary_multipleImage()
    {
        $beneficiaryImageID = trim($this->input->post('beneficiaryImageID') ?? '');
        $this->db->where('beneficiaryImageID', $beneficiaryImageID)->delete('srp_erp_ngo_beneficiaryimages');

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Image deleted successfully');
        } else {
            return array('e', 'Error in Image delete !');
        }

    }

    function update_comBeneficiary_multipleImage()
    {

        $this->db->trans_start();
        $beneficiaryImageID = trim($this->input->post('beneficiaryImageID') ?? '');
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $totlaCount = $this->db->query("select beneficiaryImageID FROM srp_erp_ngo_beneficiaryimages WHERE beneficiaryID ={$benificiaryID} AND isSelectedforPP = 1")->result_array();

            if (count($totlaCount) >= 2) {
                return array('e', 'Only Two Images Can be Default Image.');
                exit();
            }
        }

        $data['isSelectedforPP'] = $status;
        $this->db->where('beneficiaryImageID', $beneficiaryImageID);
        $this->db->update('srp_erp_ngo_beneficiaryimages', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Default Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Default Status Updated Successfully.');
        }
    }

    function fetch_comBeneficiary_multipleImages($benificiaryID)
    {
        $this->db->select("beneficiaryImageID,beneficiaryImage,isSelectedforPP");
        $this->db->from("srp_erp_ngo_beneficiaryimages");
        $this->db->where("beneficiaryID", $benificiaryID);
        $this->db->where("isSelectedforPP", 1);
        $output = $this->db->get()->result_array();

        return $output;
    }


    function comEditable_update($tableName, $pkColumn)
    {
        $column = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');
        switch ($column) {
            case 'DOB_O':
            case 'dateAssumed_O':
            case 'endOfContract_O':
            case 'SLBSeniority_O':
            case 'WSISeniority_O':
            case 'passportExpireDate_O':
            case 'VisaexpireDate_O':
            case 'coverFrom_O':
                $value = format_date_mysql_datetime($value);
                break;
        }

        $table = $tableName;
        $data = array($column => $value);
        $this->db->where($pkColumn, $pk);
        $result = $this->db->update($table, $data);
        //echo $this->db->last_query();
        return $result;
    }

    function save_comBeneficiary_doc()
    {
        $companyID = current_companyID();
        $benificiaryID = $this->input->post('benificiaryID');
        $projectID = $this->input->post('projectID');
        $documentID = $this->input->post('document');

        //Check is there is a document with this document ID for this employee
        /*        $where = array('DocDesID' => $documentID, 'PersonID' => $empID, 'PersonType' => 'E');
                $isExisting = $this->db->where($where)->select('DocDesID')->from('srp_documentdescriptionforms')->get()->row('DocDesID');*/

        $isExisting = $this->db->query("SELECT DocDesFormID FROM srp_erp_ngo_documentdescriptionforms WHERE beneficiaryID = {$benificiaryID} AND DocDesID = {$documentID} AND companyID = $companyID")->row_array();

        if (!empty($isExisting)) {
            return ['e', 'This document has been updated already.<br/>Please delete the document and try again.'];
        }


        $path = UPLOAD_PATH_POS . 'documents/ngo/'; // imagePath();
        if (!file_exists($path)) {
            mkdir("documents/ngo", 007);
        }
        $fileName = str_replace(' ', '', strtolower($benificiaryID)) . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|pdf|xls|xlsx|xlsxm|txt';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        //doc_file is  => $_FILES['doc_file']['name'];

        if (!$this->upload->do_upload("doc_file")) {
            return array('e', 'Upload failed ' . $this->upload->display_errors());
        } else {

            //Get document Setup ID
            $setUpID = $this->db->query("SELECT DocDesSetupID FROM srp_erp_ngo_documentdescriptionsetup WHERE DocDesID={$documentID}
                                       AND companyID={$companyID} ")->row('DocDesSetupID');

            $data = array(
                'DocDesSetID' => $setUpID,
                'DocDesID' => $documentID,
                'beneficiaryID' => $benificiaryID,
                'projectID' => $projectID,
                'FileName' => $this->upload->data('file_name'),
                'companyID' => current_companyID(),
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_erp_ngo_documentdescriptionforms', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Document successfully uploaded');
            } else {
                return array('e', 'Error in document upload');
            }

        }
    }

    function delete_comBeneficiary_doc()
    {
        $DocDesFormID = trim($this->input->post('DocDesFormID') ?? '');
        $this->db->where('DocDesFormID', $DocDesFormID)->delete('srp_erp_ngo_documentdescriptionforms');

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Document deleted successfully');
        } else {
            return array('e', 'Error in document delete function');
        }

    }


    function get_FamMemCatch($benificiaryIDs)
    {

        $queryc = $this->db->query("SELECT DISTINCT FamMasterID FROM srp_erp_ngo_beneficiarymaster WHERE srp_erp_ngo_beneficiarymaster.benificiaryID={$benificiaryIDs} ");
        $rowc = $queryc->row();

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_beneficiaryfamilydetails.Com_MasterID  FROM srp_erp_ngo_beneficiaryfamilydetails INNER JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiaryfamilydetails.beneficiaryID=srp_erp_ngo_beneficiarymaster.benificiaryID WHERE srp_erp_ngo_beneficiaryfamilydetails.beneficiaryID={$benificiaryIDs} ");
        $rowFP = $queryFP->result();
        $memFem = array();
        foreach ($rowFP as $resFP) {

            $memFem[] = $resFP->Com_MasterID;

        }

        $in_memFem = "'" . implode("', '", $memFem) . "'";

        $queryFmP = $this->db->query("SELECT srp_erp_ngo_com_familydetails.Com_MasterID,srp_erp_ngo_com_communitymaster.CName_with_initials FROM srp_erp_ngo_com_familydetails INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_familydetails.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID  WHERE srp_erp_ngo_com_familydetails.FamMasterID={$rowc->FamMasterID} AND srp_erp_ngo_com_familydetails.Com_MasterID NOT IN ($in_memFem) ORDER BY srp_erp_ngo_com_familydetails.Com_MasterID DESC ");

        $rowFmP = $queryFmP->result();
        echo "<option></option>";
        foreach ($rowFmP as $leadFem) {

            if (!empty($rowFmP)) {
                echo "<option value='" . $leadFem->Com_MasterID . "'>" . $leadFem->CName_with_initials . "</option>";
            }
        }
    }

    /*end of community beneficiary*/

    /*community rental WH setup */

    public
    function defWHControl()
    {

        $companyID = current_companyID();
        $query = $this->db->query("SELECT RentWarehouseID FROM srp_erp_ngo_com_rentwarehouse  WHERE companyID='" . $companyID . "' AND isDeleted='0' AND isDefault='1' ");
        $res = $query->result();

        if (!empty($res)) {
            echo "Exists";
        }

    }

    public
    function saveRentalWH_master()
    {

        $rentWareHid = $this->input->post('rentWareHid');
        $isWHdefault = $this->input->post('isWHdefault');

        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_rentwarehouse  t1 WHERE t1.companyID='" . $companyID . "' AND t1.wareHouseAutoID = '" . $rentWareHid . "'")->result_array();

        if (empty($isExist)) {

            $this->db->trans_start();

            if (!empty($isWHdefault)) {
                $thisWHdef = $isWHdefault;

                if ($isWHdefault == 1) {

                    //add def wh check
                    $qchkDef = $this->db->query('SELECT RentWarehouseID,isDefault FROM srp_erp_ngo_com_rentwarehouse WHERE companyID="' . $companyID . '"  AND isDeleted="0" AND isDefault="1"');
                    $rchkDef = $qchkDef->result();
                    if (!empty($rchkDef)) {
                        foreach ($rchkDef as $rowd) {

                            $query = $this->db->query("UPDATE srp_erp_ngo_com_rentwarehouse SET isDefault='0' WHERE companyID='" . $companyID . "' AND RentWarehouseID = '" . $rowd->RentWarehouseID . "'");
                        }
                    }
                } else {
                }

            } else {
                $thisWHdef = 0;
            }

            $data_setup = array(
                'wareHouseAutoID' => $rentWareHid,
                'isDefault' => $thisWHdef,
                'companyID' => current_companyID(),
                'createdPCID' => current_pc(),
                'CreatedUserName' => current_employee(),
                'createdUserID' => current_userID(),
                'createdDateTime' => current_date()
            );


            $this->db->insert('srp_erp_ngo_com_rentwarehouse', $data_setup);


            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {
            $existItems = '';
            foreach ($isExist as $row) {
                $existItems .= '</br>';
            }
            return array('e', 'Following warehouse items are already Exists ' . $existItems);
        }

    }

    function fetchEdit_RentWh($crid)
    {

        $seledit = $this->db->query('SELECT * FROM srp_erp_ngo_com_rentwarehouse WHERE RentWarehouseID="' . $crid . '"');
        $resedit = $seledit->result();

        foreach ($resedit as $rows) {
            echo json_encode(
                array(
                    "editrentWareHid" => $rows->wareHouseAutoID,
                    "vowel" => $rows->isDefault,

                )
            );
        }
    }

    function delete_rentalWH()
    {
        $hidden_id = $this->input->post('hidden-id');

        $this->db->trans_start();
        $data = array(
            'isDeleted' => '1',
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        //    $this->db->where('DocDesID', $hidden_id)->update('srp_documentdescriptionmaster', $data);
        $this->db->where('RentWarehouseID', $hidden_id)->delete('srp_erp_ngo_com_rentwarehouse');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    function edit_rentalWH()
    {
        $editrentWareHid = $this->input->post('editrentWareHid');
        $isWHdefault = $this->input->post('vowel');
        $setupID = $this->input->post('hidden-id');

        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT RentWarehouseID FROM srp_erp_ngo_com_rentwarehouse WHERE wareHouseAutoID='$editrentWareHid'
                                     AND RentWarehouseID !={$setupID} AND companyID=" . current_companyID())->row_array();

        if (empty($isExist)) {

            $this->db->trans_start();

            if ($isWHdefault == 1) {

                //add def wh check
                $qchkDef = $this->db->query('SELECT RentWarehouseID,isDefault FROM srp_erp_ngo_com_rentwarehouse WHERE companyID="' . $companyID . '"  AND isDeleted="0" AND isDefault="1"');
                $rchkDef = $qchkDef->result();
                if (!empty($rchkDef)) {
                    foreach ($rchkDef as $rowd) {

                        $query = $this->db->query("UPDATE srp_erp_ngo_com_rentwarehouse SET isDefault='0' WHERE companyID='" . $companyID . "' AND RentWarehouseID = '" . $rowd->RentWarehouseID . "'");

                    }
                }
            } else {

            }

            $data_setup = array(
                'wareHouseAutoID' => $editrentWareHid,
                'isDefault' => $isWHdefault,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date']

            );

            $setupWhere = array(
                'RentWarehouseID' => $setupID

            );
            $this->db->where($setupWhere)->update('srp_erp_ngo_com_rentwarehouse', $data_setup);


            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in update process');
            }
        } else {
            return array('e', 'This rental warehouse is already Exists');
        }
    }

    /*end of  rental WH setup */

    /*community rental item setup */
    function get_rentTypeDrop($rentTypeID)
    {

        $companyID = $this->common_data['company_data']['company_id'];
        if ($rentTypeID == '1') {

            $querywHM = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_rentwarehouse.wareHouseAutoID  FROM srp_erp_ngo_com_rentwarehouse INNER JOIN srp_erp_warehousemaster ON srp_erp_ngo_com_rentwarehouse.wareHouseAutoID=srp_erp_warehousemaster.wareHouseAutoID WHERE srp_erp_ngo_com_rentwarehouse.companyID={$companyID} AND srp_erp_ngo_com_rentwarehouse.isDeleted='0' ");
            $rowwHM = $querywHM->result();
            $RENTwhm = array();
            foreach ($rowwHM as $reswHM) {

                $RENTwhm[] = $reswHM->wareHouseAutoID;

            }

            $COMwhmRnt = "'" . implode("', '", $RENTwhm) . "'";

            $queryGds = $this->db->query("SELECT * FROM srp_erp_warehouseitems WHERE companyID='" . $companyID . "' AND wareHouseAutoID IN ($COMwhmRnt) ORDER BY warehouseItemsAutoID DESC ");

            $rowGds = $queryGds->result();
            echo "<option></option>";
            foreach ($rowGds as $rentGoods) {

                if (!empty($rentGoods)) {
                    echo "<option value='" . $rentGoods->warehouseItemsAutoID . "'>" . $rentGoods->itemDescription . "</option>";
                }
            }

        } else {

            $queryAst = $this->db->query("SELECT * FROM srp_erp_fa_asset_master WHERE companyID='" . $companyID . "' ORDER BY faID DESC ");

            $rowAst = $queryAst->result();
            echo "<option></option>";
            foreach ($rowAst as $rentAssent) {

                if (!empty($rentAssent)) {
                    echo "<option value='" . $rentAssent->faID . "'>" . $rentAssent->assetDescription . "</option>";
                }
            }

        }

    }

    function get_rentDetails($rentTypeID, $descriptionID)
    {

        $companyID = $this->common_data['company_data']['company_id'];
        if ($rentTypeID == '1') {

            $queryv = $this->db->query("SELECT revanueGLAutoID,revanueSystemGLCode,revanueGLCode,revanueDescription,revanueType FROM srp_erp_warehouseitems  INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID=srp_erp_warehouseitems.itemAutoID WHERE srp_erp_warehouseitems.companyID='" . $companyID . "' AND warehouseItemsAutoID ='" . $descriptionID . "' ");

            $queryRv = $queryv->result();
            echo "<option></option>";

            foreach ($queryRv as $rentRv) {

                if (!empty($queryRv)) {
                    echo "<option value='" . $rentRv->revanueGLAutoID . "' selected>" . $rentRv->revanueSystemGLCode . '|' . $rentRv->revanueGLCode . ' ' . $rentRv->revanueDescription . ' ' . $rentRv->revanueType . "</option>";

                }
            }
        } else {
            $queryv = $this->db->query("SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory FROM srp_erp_chartofaccounts WHERE companyID='" . $companyID . "' AND subCategory ='PLI' AND controllAccountYN='0' AND masterAccountYN='0' AND isBank='0' AND isActive='1' AND approvedYN='1'");

            $queryRv = $queryv->result();
            echo "<option></option>";

            foreach ($queryRv as $rentRv) {

                if (!empty($queryRv)) {
                    echo "<option value='" . $rentRv->GLAutoID . "'>" . $rentRv->systemAccountCode . '|' . $rentRv->GLSecondaryCode . ' ' . $rentRv->GLDescription . ' ' . $rentRv->subCategory . "</option>";
                }
            }
        }


    }

    function get_rentOtrDetails($rentTypeID, $descriptionID)
    {

        $companyID = $this->common_data['company_data']['company_id'];
        if ($rentTypeID == '1') {

            $qryRent = $this->db->query("SELECT srp_erp_warehouseitems.unitOfMeasureID,srp_erp_warehouseitems.currentStock,srp_erp_warehouseitems.unitOfMeasure FROM srp_erp_warehouseitems  INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID=srp_erp_warehouseitems.itemAutoID WHERE srp_erp_warehouseitems.companyID='" . $companyID . "' AND warehouseItemsAutoID ='" . $descriptionID . "' ")->row_array();
            $unitOfMeasureID = $qryRent['unitOfMeasureID'];
            $currentStock = $qryRent['currentStock'];

        } else {
            $unitOfMeasureID = '';
            $currentStock = 1;

        }

        echo json_encode(
            array(
                "defUnitOfMeasureID" => $unitOfMeasureID,
                "currentStck" => $currentStock,

            )
        );


    }

    public function saveRentalItm_master()
    {

        $description = $this->input->post('descriptionID');
        $rentTypeID = $this->input->post('rentTypeID');
        $rentPeriodID = $this->input->post('rentPeriodID');
        $defUnitOfMeasureID = $this->input->post('defUnitOfMeasureID');
        $rentalPrice = $this->input->post('rentalPrice');
        // $maximunRntQty = $this->input->post('maximunRntQty');
        // $minimumRntQty = $this->input->post('minimumRntQty');
        $currentStck = $this->input->post('currentStck');
        $revanueRentGLID = $this->input->post('revanueRentGLID');
        $isRequired = $this->input->post('isRequired');

        $companyID = current_companyID();

        if ($rentTypeID == '1') {
            $isExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_rentalitems  t1
                                     JOIN srp_erp_warehouseitems t2 ON t1.warehouseItemsAutoID=t2.warehouseItemsAutoID
                                     WHERE t1.companyID='" . $companyID . "' AND t1.warehouseItemsAutoID = '" . $description . "'")->result_array();

        } else {
            $isExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_rentalitems  t1
                                     JOIN srp_erp_fa_asset_master t2 ON t1.faID=t2.faID
                                     WHERE t1.companyID='" . $companyID . "' AND t1.faID = '" . $description . "'")->result_array();
        }

        if (empty($isExist)) {

            $this->db->trans_start();

            $qryRentMx = $this->db->query("select IF ( isnull(MAX(SortOrder)), 1, (MAX(SortOrder) + 1) ) AS SortOrder FROM `srp_erp_ngo_com_rentalitems` WHERE companyID={$companyID} AND isDeleted=0 ")->row_array();


            $selRgl = $this->db->query('SELECT systemAccountCode,GLSecondaryCode,GLDescription,subCategory FROM srp_erp_chartofaccounts WHERE GLAutoID="' . $revanueRentGLID . '"');
            $resRgl = $selRgl->row();
            if (!empty($resRgl)) {
                $systemAccountCode = $resRgl->systemAccountCode;
                $GLSecondaryCode = $resRgl->GLSecondaryCode;
                $GLDescription = $resRgl->GLDescription;
                $subCategory = $resRgl->subCategory;

            } else {
                $systemAccountCode = '';
                $GLSecondaryCode = '';
                $GLDescription = '';
                $subCategory = '';
            }

            $ITMuNIT = $this->db->query('SELECT UnitShortCode FROM srp_erp_unit_of_measure WHERE UnitID="' . $defUnitOfMeasureID . '"');
            $itmUnit = $ITMuNIT->row();
            if (!empty($itmUnit)) {
                $unitShort = $itmUnit->UnitShortCode;
            } else {
                $unitShort = '';

            }

            if (!empty($isRequired)) {
                $thisRequired = $isRequired;
            } else {
                $thisRequired = 0;
            }


            if ($rentTypeID == '1') {

                $selRw = $this->db->query('SELECT itemAutoID,itemSystemCode,itemDescription FROM srp_erp_warehouseitems WHERE warehouseItemsAutoID="' . $description . '"');
                $resRw = $selRw->row();

                $data_setup = array(
                    'rentalItemType' => $rentTypeID,
                    'warehouseItemsAutoID' => $description,
                    'itemAutoID' => $resRw->itemAutoID,
                    'rentalItemCode' => $resRw->itemSystemCode,
                    'rentalItemDes' => $resRw->itemDescription,
                    'rentalStatus' => $thisRequired,
                    'PeriodTypeID' => $rentPeriodID,
                    'defaultUnitOfMeasureID' => $defUnitOfMeasureID,
                    'defaultUnitOfMeasure' => $unitShort,
                    'RentalPrice' => $rentalPrice,
                    // 'minimumQty' => $maximunRntQty,
                    //'maximunQty' => $minimumRntQty,
                    'currentStock' => $currentStck,
                    'revanueGLAutoID' => $revanueRentGLID,
                    'revanueSystemGLCode' => $systemAccountCode,
                    'revanueGLCode' => $GLSecondaryCode,
                    'revanueDescription' => $GLDescription,
                    'revanueType' => $subCategory,
                    'SortOrder' => $qryRentMx['SortOrder'],
                    'companyID' => current_companyID(),
                    'createdPCID' => current_pc(),
                    'CreatedUserName' => current_employee(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => current_date()
                );


            } elseif ($rentTypeID == '2') {

                $selRw = $this->db->query('SELECT faCode,assetDescription FROM srp_erp_fa_asset_master WHERE faID="' . $description . '"');
                $resRw = $selRw->row();

                $data_setup = array(
                    'rentalItemType' => $rentTypeID,
                    'faID' => $description,
                    'rentalItemCode' => $resRw->faCode,
                    'rentalItemDes' => $resRw->assetDescription,
                    'rentalStatus' => $thisRequired,
                    'PeriodTypeID' => $rentPeriodID,
                    'defaultUnitOfMeasureID' => $defUnitOfMeasureID,
                    'defaultUnitOfMeasure' => $unitShort,
                    'RentalPrice' => $rentalPrice,
                    //'minimumQty' => $maximunRntQty,
                    //'maximunQty' => $minimumRntQty,
                    'currentStock' => $currentStck,
                    'revanueGLAutoID' => $revanueRentGLID,
                    'revanueSystemGLCode' => $systemAccountCode,
                    'revanueGLCode' => $GLSecondaryCode,
                    'revanueDescription' => $GLDescription,
                    'revanueType' => $subCategory,
                    'SortOrder' => $qryRentMx['SortOrder'],
                    'companyID' => current_companyID(),
                    'createdPCID' => current_pc(),
                    'CreatedUserName' => current_employee(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => current_date()
                );
            }

            $this->db->insert('srp_erp_ngo_com_rentalitems', $data_setup);


            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {
            $existItems = '';
            foreach ($isExist as $row) {
                $existItems .= '</br>';
            }
            return array('e', 'Following rental items are already Exists ' . $existItems);
        }

    }

    function fetchEdit_Item($crid)
    {

        $seledit = $this->db->query('SELECT * FROM srp_erp_ngo_com_rentalitems WHERE rentalItemID="' . $crid . '"');
        $resedit = $seledit->result();

        foreach ($resedit as $rows) {
            echo json_encode(
                array(
                    "editrentTypeID" => $rows->rentalItemType,
                    "edit_descriptionID" => $rows->rentalItemDes,
                    "edit_rentPeriodID" => $rows->PeriodTypeID,
                    "edit_defUnitOfMeasureID" => $rows->defaultUnitOfMeasureID,
                    "edit_rentalPrice" => $rows->RentalPrice,
                    "edit_currentStck" => $rows->currentStock,
                    // "edit_minimumRntQty" => $rows->minimumQty,
                    // "edit_maximunRntQty" => $rows->maximunQty,
                    "edit_revanueRentGLID" => $rows->revanueGLAutoID,
                    //  "edit_sortOrder" => $rows->SortOrder,
                    "rentalStatus" => $rows->rentalStatus

                )
            );
        }
    }

    function delete_rentalItm()
    {
        $hidden_id = $this->input->post('hidden-id');

        $this->db->trans_start();
        $data = array(
            'isDeleted' => '1',
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        //    $this->db->where('DocDesID', $hidden_id)->update('srp_documentdescriptionmaster', $data);
        $this->db->where('rentalItemID', $hidden_id)->delete('srp_erp_ngo_com_rentalitems');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    function edit_rentalItm()
    {
        $editrentTypeID = $this->input->post('editrentTypeID');
        $descriptionID = $this->input->post('edit_descriptionID');
        $edit_rentPeriodID = $this->input->post('edit_rentPeriodID');
        $defUnitOfMeasureID = $this->input->post('edit_defUnitOfMeasureID');
        $RentalPrice = $this->input->post('edit_rentalPrice');
        $currentStock = $this->input->post('edit_currentStck');
        // $minimumRntQty = $this->input->post('edit_minimumRntQty');
        // $maximunRntQty = $this->input->post('edit_maximunRntQty');
        $revanueRentGLID = $this->input->post('edit_revanueRentGLID');
        $isRequired = $this->input->post('vowel');

        $setupID = $this->input->post('hidden-id');


        $isExist = $this->db->query("SELECT rentalItemID FROM srp_erp_ngo_com_rentalitems WHERE warehouseItemsAutoID='$descriptionID'
                                     AND rentalItemID!={$setupID} AND companyID=" . current_companyID())->row_array();

        // if (empty($isExist)) {
        $selRgl = $this->db->query('SELECT systemAccountCode,GLSecondaryCode,GLDescription,subCategory FROM srp_erp_chartofaccounts WHERE GLAutoID="' . $revanueRentGLID . '"');
        $resRgl = $selRgl->row();
        if (!empty($resRgl)) {
            $systemAccountCode = $resRgl->systemAccountCode;
            $GLSecondaryCode = $resRgl->GLSecondaryCode;
            $GLDescription = $resRgl->GLDescription;
            $subCategory = $resRgl->subCategory;

        } else {
            $systemAccountCode = '';
            $GLSecondaryCode = '';
            $GLDescription = '';
            $subCategory = '';
        }

        $this->db->trans_start();

        $data_setup = array(
            // 'warehouseItemsAutoID' => $descriptionID,
            'rentalStatus' => $isRequired,
            'PeriodTypeID' => $edit_rentPeriodID,
            'revanueGLAutoID' => $revanueRentGLID,
            'defaultUnitOfMeasureID' => $defUnitOfMeasureID,
            'currentStock' => $currentStock,
            'RentalPrice' => $RentalPrice,
            // 'minimumQty' => $minimumRntQty,
            // 'maximunQty' => $maximunRntQty,
            'revanueSystemGLCode' => $systemAccountCode,
            'revanueGLCode' => $GLSecondaryCode,
            'revanueDescription' => $GLDescription,
            'revanueType' => $subCategory,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']

        );


        $setupWhere = array(
            'rentalItemID' => $setupID

        );
        $this->db->where($setupWhere)->update('srp_erp_ngo_com_rentalitems', $data_setup);


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Updated Successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in update process');
        }
        // } else {
        // return array('e', 'This rental setup is already Exists');
        //}
    }

    /*end of  rental item setup */
    /*Start of Committee Positions Master */
    function saveCommitteePosition()
    {
        $CommitteePosition = $this->input->post('CommitteePosition[]');

        $data = array();
        foreach ($CommitteePosition as $key => $de) {
            $data[$key]['CommitteePositionDes'] = $de;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdDateTime'] = current_date();

        }

        $this->db->insert_batch('srp_erp_ngo_com_committeeposition', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editCommitteePosition()
    {
        $CommitteePosition = $this->input->post('CommitteePositionDes');
        $hidden_id = $this->input->post('hidden-id');

        $data = array(

            'CommitteePositionDes' => $CommitteePosition,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );

        $this->db->where('CommitteePositionID', $hidden_id)->update('srp_erp_ngo_com_committeeposition', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }

    }

    function deleteCommitteePosition()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT CommitteePositionID FROM srp_erp_ngo_com_committeemembers WHERE CommitteePositionID={$hidden_id}")->row('CommitteePositionID');

        if (isset($isInUse)) {
            return array('e', 'This Committee Position is in use</br>You can not delete this');
        } else {
            $this->db->where('CommitteePositionID', $hidden_id)->delete('srp_erp_ngo_com_committeeposition');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    /*End of Committee Positions Master */

    /*Start of Committee Master */
    function saveCommitteeMas()
    {
        $description = $this->input->post('Committee[]');

        $date_format_policy = date_format_policy();
        $current_date = date("Y-m-d");;

        $data = array();
        $data2 = array();
        foreach ($description as $de) {
            $data['CommitteeDes'] = $de;
            $data['isActive'] = 1;
            $data['companyID'] = current_companyID();
            $data['createdPCID'] = current_pc();
            $data['CreatedUserName'] = current_employee();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = current_date();

            $this->db->insert('srp_erp_ngo_com_committeesmaster', $data);

            $last_id = $this->db->insert_id();

            //add sub committee
            $data2['CommitteeID'] = $last_id;
            $data2['CommitteeAreawiseDes'] = $de . '- ' . '1';
            $data2['startDate'] = input_format_date($current_date, $date_format_policy);

            $data2['companyID'] = current_companyID();
            $data2['createdPCID'] = current_pc();
            $data2['CreatedUserName'] = current_employee();
            $data2['createdUserID'] = current_userID();
            $data2['createdDateTime'] = current_date();


            $this->db->insert('srp_erp_ngo_com_committeeareawise', $data2);
            //end of adding
        }


        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editCommitteeMas()
    {
        $description = $this->input->post('CommitteeDes');
        $status = $this->input->post('status');
        $hidden_id = $this->input->post('hidden-id');

        $data = array(
            'isActive' => $status,
            'CommitteeDes' => $description,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );

        $this->db->where('CommitteeID', $hidden_id)->update('srp_erp_ngo_com_committeesmaster', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }

    }

    function deleteCommitteeMas()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT CommitteeID FROM srp_erp_ngo_com_committeeareawise WHERE CommitteeID={$hidden_id}")->row('CommitteeID');

        if (isset($isInUse)) {
            return array('e', 'This Committee is used in sub committee !</br>You can not delete this');
        } else {

            $isInCmtMem = $this->db->query("SELECT CommitteeID FROM srp_erp_ngo_com_committeemembers WHERE CommitteeID={$hidden_id}")->row('CommitteeID');

            if (isset($isInCmtMem)) {
                return array('e', 'This Committee is used in committee members !</br>You can not delete this');
            } else {
                $isInCmtMemSer = $this->db->query("SELECT CommitteeID FROM srp_erp_ngo_com_committeememberservices INNER JOIN srp_erp_ngo_com_committeemembers ON srp_erp_ngo_com_committeememberservices.CommitteeMemID=srp_erp_ngo_com_committeemembers.CommitteeMemID WHERE CommitteeID={$hidden_id}")->row('CommitteeID');

                if (isset($isInCmtMemSer)) {
                    return array('e', 'This Committee is used in member service !</br>You can not delete this');
                } else {
                    $this->db->where('CommitteeID', $hidden_id)->delete('srp_erp_ngo_com_committeesmaster');
                    if ($this->db->affected_rows() > 0) {
                        return array('s', 'Records deleted successfully');
                    } else {
                        return array('e', 'Error in deleting process');
                    }

                }
            }
        }
    }

    function fetch_subCommittees_list($CommitteeID)
    {

        //var_dump(ahhshs);
        $CommitteeMems = 'CommitteeMems';
        $query = $this->db->where('srp_erp_ngo_com_committeeareawise.CommitteeID', ($CommitteeID))
            ->select('srp_erp_ngo_com_committeeareawise.CommitteeID,CommitteeAreawiseID,CommitteeAreawiseDes')
            ->from('srp_erp_ngo_com_committeeareawise')
            ->join('srp_erp_ngo_com_committeesmaster', 'srp_erp_ngo_com_committeesmaster.CommitteeID=srp_erp_ngo_com_committeeareawise.CommitteeID', 'inner')
            ->get();
        $subCmt = $query->result();
        $html = '';
        if (!empty($subCmt)) {
            $html .= '<div><ul id="list" class="nav nav-list left-sidenav">';
            foreach ($subCmt as $rowCmt) {

                $html .= '<li class="' . $rowCmt->CommitteeAreawiseID . '">
                <a onclick="redirect_cmtMemPage(1,' . $rowCmt->CommitteeAreawiseID . ' ,' . $rowCmt->CommitteeID . ')">' . $rowCmt->CommitteeAreawiseDes . ' <i
                    class="fa fa-chevron-right pull-right"></i></a></li>';

            }
            $html .= '</ul>
          
            </div>';
        }

        return $html;


    }

    function get_comMaserHd($areaSubCmnt)
    {

        $companyID = current_companyID();

        $query = $this->db->where('companyID', ($companyID))
            ->where('RegionID', ($areaSubCmnt))
            ->select('Com_MasterID,CName_with_initials')
            ->from('srp_erp_ngo_com_communitymaster')
            ->get();
        $areaCom = $query->result();
        echo '<option></option>';
        if (!empty($areaCom)) {
            foreach ($areaCom as $rowCm) {
                echo "<option value='" . $rowCm->Com_MasterID . "'>" . $rowCm->CName_with_initials . "</option>";
            }
        }
    }

    public function saveCommittee_sub()
    {

        $date_format_policy = date_format_policy();

        $CommitteeID = $this->input->post('CommitteeID');
        $subCmtDesc = $this->input->post('subCmtDesc');
        $areaSubCmnt = $this->input->post('areaSubCmnt');
        $subCmtHead = $this->input->post('subCmtHead');

        $subCmtAddedDate = $this->input->post('subCmtAddedDate');
        $subCmtExrDate = $this->input->post('subCmtExrDate');

        $isRequired = $this->input->post('isRequired');
        $subCmtRemark = $this->input->post('subCmtRemark');

        $companyID = current_companyID();
        $dataPos = $this->db->query("SELECT CommitteePositionID FROM srp_erp_ngo_com_committeeposition WHERE companyID='{$companyID}' AND isDeleted='0'")->row_array();

        $isExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_committeeareawise  t1
                                     WHERE t1.companyID='" . $companyID . "' AND t1.CommitteeID='" . $CommitteeID . "' AND  t1.CommitteeAreawiseDes = '" . $subCmtDesc . "'")->result_array();

        if (empty($isExist)) {

            $isAreaExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_committeeareawise  t1
                                     WHERE t1.companyID='" . $companyID . "' AND t1.CommitteeID='" . $CommitteeID . "' AND  t1.SubAreaId = '" . $areaSubCmnt . "'")->result_array();

            if (empty($isAreaExist)) {

                $queryMc = $this->db->query("select IF ( isnull(MAX(subSortOrder)), 1, (MAX(subSortOrder) + 1) ) AS subSortOrder FROM `srp_erp_ngo_com_committeeareawise` WHERE companyID={$companyID} AND CommitteeID={$CommitteeID} ")->row_array();

                $this->db->trans_start();

                if (!empty($isRequired)) {
                    $thisRequired = $isRequired;
                } else {
                    $thisRequired = 0;
                }

                $data_setup = array(
                    'CommitteeID' => $CommitteeID,
                    'CommitteeAreawiseDes' => $subCmtDesc,
                    'SubAreaId' => $areaSubCmnt,
                    'CommitteeHeadID' => $subCmtHead,
                    'startDate' => input_format_date($subCmtAddedDate, $date_format_policy),
                    'endDate' => input_format_date($subCmtExrDate, $date_format_policy),
                    'isActive' => $thisRequired,
                    'awRemark' => $subCmtRemark,
                    'subSortOrder' => $queryMc['subSortOrder'],
                    'companyID' => current_companyID(),
                    'createdPCID' => current_pc(),
                    'CreatedUserName' => current_employee(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => current_date()
                );

                $this->db->insert('srp_erp_ngo_com_committeeareawise', $data_setup);
                $last_id = $this->db->insert_id();

                $datm['CommitteeID'] = $CommitteeID;
                $datm['CommitteeAreawiseID'] = $last_id;
                $datm['Com_MasterID'] = $subCmtHead;
                $datm['CommitteePositionID'] = $dataPos['CommitteePositionID'];
                $datm['joinedDate'] = input_format_date($subCmtAddedDate, $date_format_policy);
                $datm['expiryDate'] = input_format_date($subCmtExrDate, $date_format_policy);
                $datm['isMemActive'] = '1';
                $datm['companyID'] = $this->common_data['company_data']['company_id'];
                $datm['createdUserGroup'] = $this->common_data['user_group'];
                $datm['createdPCID'] = $this->common_data['current_pc'];
                $datm['createdUserID'] = $this->common_data['current_userID'];
                $datm['createdDateTime'] = date('Y-m-d H:i:s');
                $datm['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_ngo_com_committeemembers', $datm);

                $this->db->trans_complete();
                if ($this->db->trans_status() == true) {
                    $this->db->trans_commit();
                    return array('s', 'Added Successfully.');
                } else {
                    $this->db->trans_rollback();
                    return array('e', 'Error in process');
                }

            } else {
                $existItems = '';
                foreach ($isExist as $row) {
                    $existItems .= '</br>';
                }
                return array('e', 'Following area is already Exists in the relevant committee ' . $existItems);
            }
        } else {
            $existItems = '';
            foreach ($isExist as $row) {
                $existItems .= '</br>';
            }
            return array('e', 'Following committee is already Exists ' . $existItems);
        }

    }

    function save_SubCmntMem()
    {
        $this->db->trans_start();

        $companyID = current_companyID();
        $date_format_policy = date_format_policy();

        $CommitteeAreawiseID = $this->input->post('CommitteeAreawiseID');
        $Com_MasterID = $this->input->post('Com_MasterID');

        $selCmT = $this->db->query('SELECT endDate FROM srp_erp_ngo_com_committeeareawise WHERE CommitteeAreawiseID="' . $CommitteeAreawiseID . '"');
        $resCmT = $selCmT->row();

        $selMemExit = $this->db->query('SELECT Com_MasterID FROM srp_erp_ngo_com_committeemembers WHERE CommitteeAreawiseID="' . $CommitteeAreawiseID . '" AND Com_MasterID="' . $Com_MasterID . '"');
        $resMemExit = $selMemExit->row();

        if (empty($resMemExit)) {
            $data['CommitteeID'] = $this->input->post('CommitteeID');
            $data['CommitteeAreawiseID'] = $this->input->post('CommitteeAreawiseID');
            $data['Com_MasterID'] = $this->input->post('Com_MasterID');
            $data['CommitteePositionID'] = $this->input->post('CommitteePositionID');
            $data['joinedDate'] = input_format_date($this->input->post('joinedDate'), $date_format_policy);
            $data['expiryDate'] = $resCmT->endDate;
            $data['isMemActive'] = $this->input->post('isMemActive');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = date('Y-m-d H:i:s');
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_ngo_com_committeemembers', $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Committee member adding failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Committee member added successfully.');

            }
        } else {
            return array('e', 'This Committee Member is already Exists');

        }

    }

    function delete_SubCmntMem()
    {
        $this->db->delete('srp_erp_ngo_com_committeemembers', array('CommitteeMemID' => trim($this->input->post('CommitteeMemID') ?? '')));
        return true;
    }

    function get_editComMaserHd($editIdCmt, $areaEditCmt)
    {

        $companyID = current_companyID();
        $selCm = $this->db->query('SELECT SubAreaId,CommitteeHeadID FROM srp_erp_ngo_com_committeeareawise WHERE CommitteeAreawiseID="' . $editIdCmt . '"');
        $resCm = $selCm->row();

        if (empty($resCm->CommitteeHeadID) || $resCm->CommitteeHeadID == '0' || $resCm->CommitteeHeadID == NULL) {
            $query = $this->db->where('companyID', ($companyID))
                ->where('RegionID', ($areaEditCmt))
                ->select('Com_MasterID,CName_with_initials')
                ->from('srp_erp_ngo_com_communitymaster')
                ->get();
            $areaECom = $query->result();
            echo '<option></option>';
            if (!empty($areaECom)) {
                foreach ($areaECom as $rowECm) {
                    echo "<option value='" . $rowECm->Com_MasterID . "'>" . $rowECm->CName_with_initials . "</option>";
                }
            }
        } else {
            if ($areaEditCmt == $resCm->SubAreaId) {
                $query = $this->db->where('srp_erp_ngo_com_committeeareawise.companyID', ($companyID))
                    ->where('CommitteeAreawiseID', ($editIdCmt))
                    ->where('SubAreaId', ($resCm->SubAreaId))
                    ->select('Com_MasterID,CName_with_initials')
                    ->from('srp_erp_ngo_com_committeeareawise')
                    ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeeareawise.CommitteeHeadID', 'inner')
                    ->get();
                $areaECom = $query->result();
                echo '<option></option>';
                if (!empty($areaECom)) {
                    foreach ($areaECom as $rowECm) {
                        echo "<option value='" . $rowECm->Com_MasterID . "' selected>" . $rowECm->CName_with_initials . "</option>";
                    }
                }
            }
        }

    }

    function fetchEdit_subComt($cmId)
    {
        $cmmtdEL = $this->db->query('SELECT * FROM srp_erp_ngo_com_committeeareawise WHERE CommitteeAreawiseID ="' . $cmId . '"');
        $reseCmt = $cmmtdEL->result();

        foreach ($reseCmt as $rows) {
            echo json_encode(
                array(
                    "editsubCmtDesc" => $rows->CommitteeAreawiseDes,
                    "editareaSubCmnt" => $rows->SubAreaId,
                    "editsubCmtHead" => $rows->CommitteeHeadID,
                    "editCmtAddedDate" => $rows->startDate,
                    "editCmtExrDate" => $rows->endDate,
                    "editCmtRemark" => $rows->awRemark,
                    "vowel" => $rows->isActive,

                )
            );
        }
    }

    function edit_subComtSave()
    {

        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $editCommitteeID = $this->input->post('editCommitteeID');
        $descriptionID = $this->input->post('editsubCmtDesc');
        $editareaSubCmntID = $this->input->post('editareaSubCmnt');
        $editsubCmtHeadID = $this->input->post('editsubCmtHead');
        $editCmtAddedDate = $this->input->post('editCmtAddedDate');
        $editCmtExrDate = $this->input->post('editCmtExrDate');
        $editCmtRemark = $this->input->post('editCmtRemark');

        $isRequired = $this->input->post('vowel');

        $setupID = $this->input->post('hidden-id');


        $isExist = $this->db->query("SELECT CommitteeAreawiseID FROM srp_erp_ngo_com_committeeareawise WHERE CommitteeAreawiseDes='$descriptionID'
                                     AND CommitteeID = {$editCommitteeID} AND CommitteeAreawiseID!={$setupID} AND companyID=" . current_companyID())->row_array();

        if (empty($isExist)) {

            $selCmte = $this->db->query('SELECT CommitteeID,SubAreaId,CommitteeHeadID FROM srp_erp_ngo_com_committeeareawise WHERE CommitteeAreawiseID="' . $setupID . '"');
            $resCmte = $selCmte->row();

            $isAreaExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_committeeareawise  t1
                                     WHERE t1.companyID='" . $companyID . "' AND t1.CommitteeAreawiseID!={$setupID} AND t1.CommitteeID='" . $resCmte->CommitteeID . "' AND  t1.SubAreaId = '" . $editareaSubCmntID . "'")->result_array();

            if (empty($isAreaExist)) {

                $selCmem = $this->db->query('SELECT * FROM srp_erp_ngo_com_committeemembers WHERE companyID="' . $companyID . '" AND CommitteeID="' . $resCmte->CommitteeID . '" AND CommitteeAreawiseID="' . $setupID . '"');
                $resCmem = $selCmem->row();

                $dataPos = $this->db->query("SELECT CommitteePositionID FROM srp_erp_ngo_com_committeeposition WHERE companyID='{$companyID}' AND isDeleted='0'")->row_array();


                $this->db->trans_start();

                $data_setup = array(

                    'isActive' => $isRequired,
                    'CommitteeAreawiseDes' => $descriptionID,
                    'SubAreaId' => $editareaSubCmntID,
                    'CommitteeHeadID' => $editsubCmtHeadID,
                    'startDate' => input_format_date($editCmtAddedDate, $date_format_policy),
                    'endDate' => input_format_date($editCmtExrDate, $date_format_policy),
                    'awRemark' => $editCmtRemark,
                    'modifiedPCID' => $this->common_data['current_pc'],
                    'modifiedUserID' => $this->common_data['current_userID'],
                    'modifiedUserName' => $this->common_data['current_user'],
                    'modifiedDateTime' => $this->common_data['current_date']

                );

                $setupWhere = array(
                    'CommitteeAreawiseID' => $setupID

                );
                $this->db->where($setupWhere)->update('srp_erp_ngo_com_committeeareawise', $data_setup);

                if (empty($resCmem) || $resCmem == NULL) {
                    $datm['CommitteeID'] = $resCmte->CommitteeID;
                    $datm['CommitteeAreawiseID'] = $setupID;
                    $datm['Com_MasterID'] = $editsubCmtHeadID;
                    $datm['CommitteePositionID'] = $dataPos['CommitteePositionID'];
                    $datm['joinedDate'] = input_format_date($editCmtAddedDate, $date_format_policy);
                    $datm['expiryDate'] = input_format_date($editCmtExrDate, $date_format_policy);
                    $datm['isMemActive'] = '1';
                    $datm['companyID'] = $this->common_data['company_data']['company_id'];
                    $datm['createdUserGroup'] = $this->common_data['user_group'];
                    $datm['createdPCID'] = $this->common_data['current_pc'];
                    $datm['createdUserID'] = $this->common_data['current_userID'];
                    $datm['createdDateTime'] = date('Y-m-d H:i:s');
                    $datm['createdUserName'] = $this->common_data['current_user'];
                    $this->db->insert('srp_erp_ngo_com_committeemembers', $datm);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() == true) {
                    $this->db->trans_commit();
                    return array('s', 'Updated Successfully.');
                } else {
                    $this->db->trans_rollback();
                    return array('e', 'Error in update process');
                }
            } else {
                return array('e', 'Following area is already Exists in the relevant committee');
            }

        } else {
            return array('e', 'This Committee setup is already Exists');
        }
    }

    function save_cmteMembrEdit()
    {
        $this->db->trans_start();
        $CommitteeMemID = $this->input->post('editCommitteeMemID');
        $editjoinedDate = $this->input->post('editjoinedDate');
        $editExpDate = $this->input->post('editExpDate');

        $companyID = $this->common_data['company_data']['company_id'];

        $date_format_policy = date_format_policy();

        $data['CommitteePositionID'] = $this->input->post('editCommtPosID');
        $data['CommitteeMemID'] = $this->input->post('editCommitteeMemID');
        $data['joinedDate'] = input_format_date($editjoinedDate, $date_format_policy);
        $data['expiryDate'] = input_format_date($editExpDate, $date_format_policy);
        $data['isMemActive'] = $this->input->post('editMemActive');
        $data['committeeMemRemark'] = $this->input->post('memRemarks');

        if (!isset($CommitteeMemID)) {

        } else {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = date('Y-m-d H:i:s');
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->update('srp_erp_ngo_com_committeemembers', $data, array('CommitteeMemID' => $CommitteeMemID));

        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Committee member updated successfully.');

        }
    }

//committee member services
    function save_comiteMemService()
    {
        $this->db->trans_start();
        $CmtMemServiceID = $this->input->post('CmtMemServiceID');

        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];
        $data['CmtMemService'] = $this->input->post('CmtMemService');
        $data['ServiceDate'] = input_format_date($this->input->post('ServiceDate'), $date_format_policy);
        $data['CommitteeMemID'] = $this->input->post('masterID');
        if (!isset($CmtMemServiceID)) {
            $sortqry = $this->db->query("select sortOrder from srp_erp_ngo_com_committeememberservices WHERE CommitteeMemID={$data['CommitteeMemID']}  order by CmtMemServiceID desc limit 1")->row_array();
            $data['sortOrder'] = (!empty($sortqry) ? $sortqry['sortOrder'] + 1 : 1);
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = date('Y-m-d H:i:s');
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_ngo_com_committeememberServices', $data);
        } else {
            $sortOrder = $this->input->post('sortOrder');
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = date('Y-m-d H:i:s');
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->update('srp_erp_ngo_com_committeememberservices', $data, array('CmtMemServiceID' => $CmtMemServiceID));

            $current_sortOrder = $this->db->query("select sortOrder,CmtMemServiceID from srp_erp_ngo_com_committeememberservices WHERE CmtMemServiceID={$CmtMemServiceID} AND companyID={$companyID}")->row_array();

            if ($sortOrder != $current_sortOrder['sortOrder']) {
                $all_sortOrder = $this->db->query("select * from srp_erp_ngo_com_committeememberservices WHERE CommitteeMemID={$data['CommitteeMemID']} AND companyID={$companyID} order by sortOrder asc")->result_array();
                if (!empty($all_sortOrder)) {
                    $keys = array_keys(array_column($all_sortOrder, 'sortOrder'), $sortOrder);
                    $new_array = array_map(function ($k) use ($all_sortOrder) {
                        return $all_sortOrder[$k];
                    }, $keys);

                    $detail[0]['CmtMemServiceID'] = $current_sortOrder['CmtMemServiceID'];
                    $detail[0]['sortOrder'] = $sortOrder;

                    $detail[1]['CmtMemServiceID'] = $new_array[0]['CmtMemServiceID'];
                    $detail[1]['sortOrder'] = $current_sortOrder['sortOrder'];
                    $this->db->update_batch('srp_erp_ngo_com_committeememberservices', $detail, 'CmtMemServiceID');

                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Member service save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Member service saved Successfully.');

        }
    }

    function delete_comiteMemService()
    {
        $this->db->delete('`srp_erp_ngo_com_committeememberservices` ', array('CmtMemServiceID' => trim($this->input->post('CmtMemServiceID') ?? '')));
        return true;
    }

    /*End of Committee Master */
    /*family Report */
    function get_totalFamHousing($FamMasterID, $famAreaId, $houseOwnshp, $houseType)
    {
        $filter_req = array("AND (srp_erp_ngo_com_communitymaster.RegionID=" . $famAreaId . ")" => $famAreaId);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $companyID = $this->common_data['company_data']['company_id'];

        $FamMasID = "";
        if (!empty($FamMasterID)) {
            $FamMasID = "AND srp_erp_ngo_com_house_enrolling.FamMasterID IN(" . join(',', $FamMasterID) . ")";
        }

        $houseOwnshpS = "";
        if (!empty($houseOwnshp)) {
            $houseOwnshpS = "AND srp_erp_ngo_com_house_enrolling.ownershipAutoID = $houseOwnshp ";
        }

        $houseTypeS = "";
        if (!empty($houseType)) {
            $houseTypeS = "AND srp_erp_ngo_com_house_enrolling.hTypeAutoID = $houseType ";
        }

        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_house_enrolling.companyID = " . $companyID . $deleted;

        $houseTot = $this->db->query("SELECT COUNT(*) AS totHouseCount FROM srp_erp_ngo_com_house_enrolling INNER JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE (srp_erp_ngo_com_house_enrolling.FamHouseSt = '0' OR srp_erp_ngo_com_house_enrolling.FamHouseSt = NULL) AND $where " . $where_clauseq . " $FamMasID $houseOwnshpS $houseTypeS ")->row_array();


        echo json_encode(
            array(
                "noOfHseId" => $houseTot['totHouseCount'],

            )
        );

    }
    /*End of family Report */
    /*community Family login dropdown*/

    function fetch_LogFamilies_list($fam_key)
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $filter_family = '';
        if (isset($fam_key) && !empty($fam_key)) {
            $filter_family = " AND ((FamilySystemCode Like '%" . $fam_key . "%') OR (FamilyName Like '%" . $fam_key . "%') OR (LedgerNo Like '%" . $fam_key . "%'))";
        }

        $selFamily = $this->db->query('SELECT FamMasterID,FamilySerialNo,FamilySystemCode,LeaderID,FamilyName,CName_with_initials,CImage FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID WHERE srp_erp_ngo_com_familymaster.companyID="' . $companyID . '" AND srp_erp_ngo_com_familymaster.isDeleted="0" AND srp_erp_ngo_com_familymaster.confirmedYN ="1"' . $filter_family);
        $resLgFm = $selFamily->result();

        $html = '';
        if (!empty($resLgFm)) {
            $html .= '<div><ul id="list" class="nav nav-list left-sidenav">';
            foreach ($resLgFm as $rowLgFm) {

                $status = '<left>';
                if ($rowLgFm->CImage) {
                    $status .= '<img class="align-left"
                 src="' . base_url('uploads/NGO/communitymemberImage/' . $rowLgFm->CImage) . '"
                 width="25" height="25">';

                } else {
                    $status .= '<img class="align-left" src="' . base_url("images/crm/icon-list-contact.png") . '"
                                     alt="" width="25" height="25">';
                }
                $status .= '</left>';

                $html .= '<li class="' . $rowLgFm->FamMasterID . '">
                <a onclick="redirect_famLogConfPage(1,' . $rowLgFm->FamMasterID . ' ,' . $rowLgFm->LeaderID . ')">' . $status . ' &nbsp;' . $rowLgFm->FamilySystemCode . ' <i
                    class="fa fa-chevron-right pull-right"></i></a></li>';

            }
            $html .= '</ul>
          
            </div>';
        }

        return $html;

    }

    function save_famLogDel()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];

        $FamMasterID = $this->input->post('FamMasterID');
        $FamUsername = $this->input->post('FamUsername');
        $FamPassword = md5($this->input->post('FamPassword'));

        $isLogExist = $this->db->query("SELECT FamUsername FROM srp_erp_ngo_com_familymaster  t1
                                     WHERE t1.companyID='" . $companyID . "' AND t1.FamMasterID ={$FamMasterID}")->result_array();


        $isAllExist = $this->db->query('SELECT FamUsername,FamPassword,LeaderID FROM srp_erp_ngo_com_familymaster WHERE srp_erp_ngo_com_familymaster.companyID="' . $companyID . '" AND FamMasterID != "' . $FamMasterID . '" ');
        $resAllExist = $isAllExist->row();

        $isDelChk = $this->db->query('SELECT FamUsername,FamPassword,LeaderID,EmailID FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID WHERE srp_erp_ngo_com_familymaster.companyID="' . $companyID . '" AND FamMasterID = "' . $FamMasterID . '" ');
        $resDelChk = $isDelChk->row();

        $LeaderID = $resDelChk->LeaderID;
        if (empty($resDelChk->EmailID)) {
            $EmailID = '';
        } else {
            $EmailID = $resDelChk->EmailID;
        }

        if (empty($isLogExist->FamUsername)) {

            if (($resAllExist->FamUsername != $this->input->post('FamUsername')) && ($resAllExist->FamPassword != $this->input->post('FamPassword'))) {

                $data['FamUsername'] = $this->input->post('FamUsername');
                $data['FamPassword'] = md5($this->input->post('FamPassword'));
                $data['FamLogCreatedDate'] = input_format_date($this->input->post('FamLogCreatedDate'), $date_format_policy);
                $data['isLoginActive'] = $this->input->post('isLoginActive');
                $data['modifiedUserID'] = current_userID();
                $data['modifiedUserName'] = current_user();
                $data['modifiedDateTime'] = date('Y-m-d H:i:s');
                $data['modifiedPCID'] = current_pc();
                $this->db->where('FamMasterID', $this->input->post('FamMasterID'));
                $this->db->update('srp_erp_ngo_com_familymaster', $data);

                $db = $this->load->database('db2', true);

                $iscenExist = $db->query('SELECT FamMasterID FROM ngo_family  WHERE companyID="' . $companyID . '" AND FamMasterID = "' . $FamMasterID . '" ');
                $resCenExist = $iscenExist->row();

                if (empty($resCenExist->FamMasterID)) {
                    $updateDB2 = $db->query("INSERT INTO ngo_family (FamMasterID,FamUsername,FamPassword,FamEmail,LeaderID,companyID  ) VALUES ('" . $FamMasterID . "','" . $FamUsername . "','" . $FamPassword . "','" . $EmailID . "','" . $LeaderID . "','" . $companyID . "')");
                } else {
                    $updateDB2 = $db->query("UPDATE ngo_family SET FamUsername='{$FamUsername}', FamPassword='{$FamPassword}',  FamEmail='{$EmailID}' WHERE companyID={$companyID} AND `FamMasterID`='{$FamMasterID}'");
                }


                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Family login updating failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Family login updated successfully.');

                }
            } else {
                return array('e', 'The User Name or Password is already Exists');

            }
        } else {
            return array('e', 'This Family login setup is already Exists');

        }

    }

    function save_famLogEdit()
    {
        $this->db->trans_start();
        $FamMasterID = $this->input->post('editFamMasterID');
        $editCreatedDate = $this->input->post('editCreatedDate');

        $companyID = $this->common_data['company_data']['company_id'];

        $date_format_policy = date_format_policy();

        $data['FamUsername'] = $this->input->post('editFamUserName');
        $data['FamPassword'] = md5($this->input->post('editFamPassWord'));
        $data['FamLogCreatedDate'] = input_format_date($editCreatedDate, $date_format_policy);
        $data['isLoginActive'] = $this->input->post('editLogActive');


        //for db 2
        $isDelChk = $this->db->query('SELECT FamUsername,FamPassword,LeaderID,EmailID FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID WHERE srp_erp_ngo_com_familymaster.companyID="' . $companyID . '" AND FamMasterID = "' . $FamMasterID . '" ');
        $resDelChk = $isDelChk->row();

        if (empty($resDelChk->EmailID)) {
            $EmailID = '';
        } else {
            $EmailID = $resDelChk->EmailID;
        }
        $data2['FamUsername'] = $this->input->post('editFamUserName');
        $data2['FamPassword'] = md5($this->input->post('editFamPassWord'));
        $data2['FamEmail'] = $EmailID;


        if (!isset($FamMasterID)) {

        } else {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = date('Y-m-d H:i:s');
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->update('srp_erp_ngo_com_familymaster', $data, array('FamMasterID' => $FamMasterID));

            $db = $this->load->database('db2', true);
            $db->update('ngo_family', $data2, array('FamMasterID' => $FamMasterID));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Family login detail updated successfully.');

        }
    }

    function delete_famLogDel()
    {

        $FamMasterID = $this->input->post('FamMasterID');


        $data['FamUsername'] = NULL;
        $data['FamPassword'] = NULL;
        $data['FamLogCreatedDate'] = NULL;
        $data['isLoginActive'] = 0;

        if (!isset($FamMasterID)) {

        } else {

            $this->db->update('srp_erp_ngo_com_familymaster', $data, array('FamMasterID' => $FamMasterID));
            return true;
        }

    }

    /*end of community Family login dropdown*/

    /* rent update */
    function update_rentStockDel()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $querywHM = $this->db->query("SELECT DISTINCT warehouseItemsAutoID  FROM srp_erp_ngo_com_rentalitems WHERE companyID={$companyID} AND rentalItemType='1' AND isDeleted='0' ");
        $rowwHM = $querywHM->result();
        $whrHouseItem = array();
        foreach ($rowwHM as $reswHM) {

            $whrHouseItem[] = $reswHM->warehouseItemsAutoID;

        }

        $iTEMwhmRnt = "'" . implode("', '", $whrHouseItem) . "'";

        $querRnt = $this->db->query("SELECT srp_erp_warehouseitems.itemAutoID,revanueGLAutoID,revanueSystemGLCode,revanueGLCode,revanueDescription,revanueType,srp_erp_warehouseitems.unitOfMeasureID,srp_erp_warehouseitems.currentStock,srp_erp_warehouseitems.unitOfMeasure FROM srp_erp_warehouseitems  INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID=srp_erp_warehouseitems.itemAutoID WHERE srp_erp_warehouseitems.companyID='" . $companyID . "' AND srp_erp_warehouseitems.warehouseItemsAutoID IN ($iTEMwhmRnt) ");

        $resRnt = $querRnt->result();

        foreach ($resRnt as $rentRnt) {

            $qryRent = $this->db->query("SELECT itemAutoID,SUM(requestedQty) AS 'sumRequestedQty'  FROM srp_erp_ngo_com_itemissuedetails INNER JOIN srp_erp_ngo_com_itemissuemaster ON srp_erp_ngo_com_itemissuemaster.itemIssueAutoID=srp_erp_ngo_com_itemissuedetails.itemIssueAutoID WHERE srp_erp_ngo_com_itemissuedetails.companyID={$companyID} AND srp_erp_ngo_com_itemissuedetails.itemAutoID={$rentRnt->itemAutoID} AND isReturned='0'");
            $rowRent = $qryRent->row();

            $netRentStock = ($rentRnt->currentStock - $rowRent->sumRequestedQty);

            $this->db->query("UPDATE srp_erp_ngo_com_rentalitems SET revanueGLAutoID='{$rentRnt->revanueGLAutoID}', revanueSystemGLCode='{$rentRnt->revanueSystemGLCode}',revanueGLCode='{$rentRnt->revanueGLCode}',revanueDescription='{$rentRnt->revanueDescription}',revanueType='{$rentRnt->revanueType}',defaultUnitOfMeasureID='{$rentRnt->unitOfMeasureID}',defaultUnitOfMeasure='{$rentRnt->unitOfMeasure}',currentStock='{$netRentStock}' WHERE companyID={$companyID} AND rentalItemType='1' AND `itemAutoID`='{$rentRnt->itemAutoID}'");

        }
        return true;

    }
    /* end of rent update */

    /* Zakat project proposal */
    function send_project_proposal_email()
    {
        $proposalID = $this->input->post('proposalID');
        $Donors = $this->input->post('selectedDonorsEmailSync');
        $path = NGOImage . "ProjectProposal_" . $proposalID . ".pdf";
        if (!empty($Donors)) {
            foreach ($Donors as $data) {

                $donorData = $this->db->query("SELECT name,email FROM srp_erp_ngo_donors WHERE contactID = {$data}")->row_array();

                if (!empty($donorData['email'])) {
                    $param = array();
                    $param["empName"] = $donorData["name"];
                    $param["body"] = 'We are pleased to submit our proposal as follow. <br/>
                                          <table border="0px">
                                          </table>';
                    $mailData = [
                        'approvalEmpID' => '',
                        'documentCode' => '',
                        'toEmail' => $donorData["email"],
                        'subject' => 'Project Proposal',
                        'param' => $param
                    ];
                    send_approvalEmail($mailData, 1, $path);

                    $this->db->set('sendEmail', 1);
                    $this->db->where('proposalID', $proposalID);
                    $this->db->where('donorID', $data);
                    $this->db->update('srp_erp_ngo_projectproposaldonors');
                }

            }
        }
        return array('s', 'Email Send Successfully.');

    }

    function referback_project_proposal()
    {
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($proposalID, 'PRP');

        if ($status == 1) {

            $data = array('proposalStageID' => 1, 'status' => 1);
            $this->db->where('proposalID', trim($this->input->post('proposalID') ?? ''));
            $this->db->update('srp_erp_ngo_projectproposals', $data);
            $this->db->trans_complete();

            return array('s', ' Referred Back Successfully.', $status);
        } else {
            return array('e', ' Error in refer back.', $status);
        }

    }

    function delete_project_proposal_image()
    {
        $ngoProposalImageID = trim($this->input->post('ngoProposalImageID') ?? '');
        $myFileName = $this->input->post('myFileName');
        $this->db->delete('srp_erp_ngo_projectproposalimages', array('ngoProposalImageID' => trim($ngoProposalImageID)));
        return array('s', 'Document deleted successfully');

    }

    function save_project_proposal_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $company_code = $this->common_data['company_data']['company_code'];
        $documentDate = $this->input->post('documentDate');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $typeofpro = trim($this->input->post('typepro') ?? '');

        $format_documentDate = null;
        if (isset($documentDate) && !empty($documentDate)) {
            $format_documentDate = input_format_date($documentDate, $date_format_policy);
        }
        $format_startDate = null;
        if (isset($startDate) && !empty($startDate)) {
            $format_startDate = input_format_date($startDate, $date_format_policy);
        }
        $format_endDate = null;
        if (isset($endDate) && !empty($endDate)) {
            $format_endDate = input_format_date($endDate, $date_format_policy);
        }


        if ($proposalID) {
            $data['proposalName'] = trim($this->input->post('proposalName') ?? '');
            $data['proposalTitle'] = trim($this->input->post('proposalTitle') ?? '');
            $data['projectID'] = trim($this->input->post('projectID') ?? '');
            //$data['projectSubID'] = trim($this->input->post('subProjectID') ?? '');
           // $data['status'] = trim($this->input->post('status') ?? '');
            $data['zakatDefault'] = 1;
            $data['status'] = 12;
            $data['projectSummary'] = trim($this->input->post('projectSummary') ?? '');
            $data['detailDescription'] = $this->input->post('detailDescription');
            $data['processDescription'] = $this->input->post('processDescription');
            $data['bankGLAutoID'] = $this->input->post('bankGLAutoID');
            $data['DocumentDate'] = $format_documentDate;
            $data['startDate'] = $format_startDate;
            $data['endDate'] = $format_endDate;
            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['countryID'] = trim($this->input->post('countryID') ?? '');
            $data['provinceID'] = trim($this->input->post('province') ?? '');
            $data['areaID'] = trim($this->input->post('district') ?? '');
            $data['divisionID'] = trim($this->input->post('division') ?? '');
            $data['type'] = $typeofpro;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            /*$data['proposalStageID'] = 1;*/

            $this->db->where('proposalID', $proposalID);
            $this->db->update('srp_erp_ngo_projectproposals', $data);
            if ($typeofpro == 2) {
                $datass['description'] = trim($this->input->post('proposalName') ?? '');
                $datass['projectName'] = trim($this->input->post('proposalTitle') ?? '');
                $datass['startDate'] = $format_startDate;
                $datass['endDate'] = $format_endDate;
                $datass['modifiedPCID'] = $this->common_data['current_pc'];
                $datass['modifiedUserID'] = $this->common_data['current_userID'];
                $datass['modifiedUserName'] = $this->common_data['current_user'];
                $datass['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('proposalID', $proposalID);
                $this->db->update('srp_erp_ngo_projects', $datass);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Project Updated Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Project Updated Successfully.', $proposalID);

                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Proposal Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Proposal Updated Successfully.', $proposalID);

            }
        } else {
            $data['proposalName'] = trim($this->input->post('proposalName') ?? '');
            $data['proposalTitle'] = trim($this->input->post('proposalTitle') ?? '');
            $data['projectID'] = trim($this->input->post('projectID') ?? '');
            $data['zakatDefault'] = 1;
            //$data['projectSubID'] = trim($this->input->post('subProjectID') ?? '');
           // $data['status'] = trim($this->input->post('status') ?? '');
            $data['status'] = 12;
            $data['projectSummary'] = trim($this->input->post('projectSummary') ?? '');
            $data['detailDescription'] = $this->input->post('detailDescription');
            $data['processDescription'] = $this->input->post('processDescription');
            $data['bankGLAutoID'] = $this->input->post('bankGLAutoID');
            $data['DocumentDate'] = $format_documentDate;
            $data['startDate'] = $format_startDate;
            $data['endDate'] = $format_endDate;
            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['countryID'] = trim($this->input->post('countryID') ?? '');
            $data['provinceID'] = trim($this->input->post('province') ?? '');
            $data['areaID'] = trim($this->input->post('district') ?? '');
            $data['divisionID'] = trim($this->input->post('division') ?? '');
            $data['type'] = $typeofpro;
            $data['documentID'] = 'PRP';
            $data['documentSystemCode'] = $this->sequence->sequence_generator($data['documentID']);
            $data['companyID'] = $companyID;
            $data['companyCode'] = $company_code;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            /* $data['proposalStageID'] = 1;*/

            $this->db->insert('srp_erp_ngo_projectproposals', $data);
            $last_id = $this->db->insert_id();
            if ($typeofpro == 2) {
                $datas['description'] = trim($this->input->post('proposalName') ?? '');
                $datas['levelNo'] = 1;
                $datas['companyID'] = $companyID;

                $this->load->library('approvals');
                $this->db->select('*');
                $this->db->where('proposalID', $last_id);
                $this->db->from('srp_erp_ngo_projectproposals');
                $master = $this->db->get()->row_array();
                $approvals_status = $this->approvals->AutoApprovalProject('PRP', $master['proposalID'], $master['documentSystemCode'], ' Project Proposal', 'srp_erp_ngo_projectproposals', 'proposalID', 1);
                $datas['proposalID'] = $last_id;
                $datas['projectName'] = trim($this->input->post('proposalTitle') ?? '');
                $datas['startDate'] = $format_startDate;
                $datas['endDate'] = $format_endDate;
                $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_ngo_projects` WHERE companyID={$companyID}")->row_array();
                $datas['serialNo'] = $serial['serialNo'];
                $datas['masterID'] = trim($this->input->post('projectID') ?? '');
                $datas['documentCode'] = 'PROJ';
                $datas['documentSystemCode'] = ($company_code . '/' . 'PROJ' . str_pad($datas['serialNo'], 6, '0', STR_PAD_LEFT));
                $datas['createdUserGroup'] = $this->common_data['user_group'];
                $datas['createdPCID'] = $this->common_data['current_pc'];
                $datas['createdUserID'] = $this->common_data['current_userID'];
                $datas['createdDateTime'] = $this->common_data['current_date'];
                $datas['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_ngo_projects', $datas);
                $last_id_project = $this->db->insert_id();
                $data['projectSubID'] = $last_id_project;
                $this->db->where('proposalID', $last_id);
                $this->db->update('srp_erp_ngo_projectproposals', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Project Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Project Saved Successfully.', $last_id);

                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Proposal Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Proposal Saved Successfully.', $last_id);

            }


        }


    }

    function load_project_proposal_header()
    {
        $convertFormat = convert_date_format_sql();
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $data = $this->db->query("select *,DATE_FORMAT(DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(startDate,'{$convertFormat}') AS startDate,DATE_FORMAT(endDate,'{$convertFormat}') AS endDate FROM srp_erp_ngo_projectproposals WHERE proposalID = {$proposalID} ")->row_array();
        return $data;
    }

    function load_project_proposal_zaqath_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->db->select("zaq.proposalID,pp.isConvertedToProject as isConvertedToProject,zaq.proposalZaqathSetID,zaq.isActive AS isZakisActive,zaq.EconStateID,zaq.AgeGroupID,zaq.GrpPoints,zaq.ZakatAmount,zaq.TotalPerZakat,pp.approvedYN as approvedYN,pp.confirmedYN as confirmedYN,ecoMas.EconStateDes,zag.AgeGroup,zag.AgeLimit");
        $this->db->from('srp_erp_ngo_com_projectproposalzakatsetup zaq');
        $this->db->join('srp_erp_ngo_projectproposals pp', 'pp.proposalID = zaq.proposalID', 'left');
        $this->db->join('srp_erp_ngo_com_zakatagegroupmaster zag', 'zag.AgeGroupID = zaq.AgeGroupID', 'left');
        $this->db->join('srp_erp_ngo_com_familyeconomicstatemaster ecoMas', 'ecoMas.EconStateID = zaq.EconStateID', 'left');
        $this->db->where('zaq.companyID', $companyID);
        $this->db->where('zaq.proposalID', $proposalID);
        $this->db->order_by('proposalZaqathSetID', 'desc');
        return $this->db->get()->result_array();
    }

    function load_project_proposal_beneficiary_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->db->select("ppd.proposalID,pp.isConvertedToProject as isConvertedToProject,pp.documentSystemCode,pp.proposalTitle,ppd.proposalBeneficiaryID,ppd.EconStateID,ppd.totalEstimatedValue,ppd.beneficiaryID,bm.systemCode as benCode,bm.nameWithInitials as name,ecoMas.EconStateDes,bm.FamMasterID,bm.Com_MasterID,bm.totalCost AS totalCost,ppd.isQualified AS isQualified,pp.approvedYN as approvedYN,pp.confirmedYN as confirmedYN,ppd.companyID");
        $this->db->from('srp_erp_ngo_projectproposalbeneficiaries ppd');
        $this->db->join('srp_erp_ngo_beneficiarymaster bm', 'bm.benificiaryID = ppd.beneficiaryID', 'left');
        $this->db->join('srp_erp_ngo_projectproposals pp', 'pp.proposalID = ppd.proposalID', 'left');
        $this->db->join('srp_erp_ngo_com_familyeconomicstatemaster ecoMas', 'ecoMas.EconStateID = ppd.EconStateID', 'left');
        $this->db->where('ppd.companyID', $companyID);
        $this->db->where('ppd.proposalID', $proposalID);
        $this->db->order_by('proposalBeneficiaryID', 'desc');
        return $this->db->get()->result_array();
    }

    function load_project_proposal_donor_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertdateformat = convert_date_format_sql();

        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->db->select("pro.confirmedYN,pro.approvedYN,pro.isConvertedToProject as isConvertedToProject,ppd.proposalDonourID,ppd.isSubmitted,do.contactID,do.name as name,ppd.proposalID,ppd.donorID,DATE_FORMAT(ppd.submittedDate,'{$convertdateformat}') AS submittedDate,ppd.isApproved,DATE_FORMAT(ppd.approvedDate,'{$convertdateformat}') AS approvedDate,ppd.commitedAmount,pro.transactionCurrencyID,curm.CurrencyCode as CurrencyCode");
        $this->db->from('srp_erp_ngo_projectproposaldonors ppd');
        $this->db->join('srp_erp_ngo_donors do', 'ppd.donorID = do.contactID', 'left');
        $this->db->join('srp_erp_ngo_projectproposals pro', 'ppd.proposalID = pro.proposalID', 'left');
        $this->db->join('srp_erp_currencymaster curm', 'pro.transactionCurrencyID = curm.currencyID', 'left');
        $this->db->where('ppd.companyID', $companyID);
        $this->db->where('ppd.proposalID', $proposalID);
        $this->db->order_by('proposalDonourID', 'desc');
        return $this->db->get()->result_array();
    }

    function fetch_ngo_sub_projects()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('ngoProjectID,description');
        $this->db->from('srp_erp_ngo_projects');
        $this->db->where('masterID', $this->input->post("ngoProjectID"));
        $this->db->where('srp_erp_ngo_projects.companyID', $companyID);
        $master = $this->db->get()->result_array();
        return $master;

    }

    function assign_zaqath_for_project_proposal()
    {

        $this->db->trans_start();

        $companyID = current_companyID();
        $EconStateID = $this->input->post('EconStateID');
        $proposalID = $this->input->post('proposalID');
        $selectAgeGrp = $this->input->post('AgeGroupID');
        $GrpPoints = $this->input->post('GrpPoints');
        $ZakatAmount = $this->input->post('ZakatAmount');
        $TotalPerZakat = $this->input->post('TotalPerZakat');

        $agGrpExit = $this->db->query("select * FROM `srp_erp_ngo_com_projectproposalzakatsetup` WHERE companyID={$companyID} AND proposalID='" . $proposalID . "' AND EconStateID='" . $EconStateID . "'")->row_array();
        if (empty($agGrpExit)) {

            foreach ($selectAgeGrp as $key => $vals) {

                $data['proposalID'] = $proposalID;
                $data['EconStateID'] = $EconStateID;
                $data['AgeGroupID'] = $selectAgeGrp[$key];
                $data['GrpPoints'] = $GrpPoints[$key];
                $data['ZakatAmount'] = $ZakatAmount[$key];
                $data['TotalPerZakat'] = $TotalPerZakat[$key];
                $data['companyID'] = current_companyID();
                $data['createdUserGroup'] = current_user_group();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = current_date(true);
                $data['timestamp'] = current_date(true);

                $this->db->insert('srp_erp_ngo_com_projectproposalzakatsetup', $data);

            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();

                return array('e', 'save failed');
            } else {

                $this->db->trans_commit();

                return array('s', 'Zakat Contribution added successfully');
            }
        } else {

            return array('e', 'Already available');

        }


    }

    function assign_beneficiary_for_project_proposal()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $selectedItem = $this->input->post('selectedItemsSync[]');
        $EconStateID = $this->input->post('EconStateIDs[]');
        $proposalID = $this->input->post('proposalID');
        $data = [];

        foreach ($selectedItem as $key => $vals) {
            $data[$key]['beneficiaryID'] = $vals;
            $data[$key]['proposalID'] = $proposalID;
            $data[$key]['EconStateID'] = $EconStateID;

            $comPbene = $this->db->query("SELECT FamMasterID FROM srp_erp_ngo_beneficiarymaster  WHERE companyID='" . $companyID . "' AND benificiaryID='" . $vals . "'");
            $rowPbene = $comPbene->row();

            $queryag1 = $this->db->query("SELECT TotalPerZakat,pagz.isActive AS isActive FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $companyID . "' AND proposalID='" . $proposalID . "' AND EconStateID='" . $EconStateID . "' AND pagz.AgeGroupID='1'");
            $rowag1 = $queryag1->row();
            if (isset($rowag1) && $rowag1->isActive == '1') {
                $TotalPerZakat1 = $rowag1->TotalPerZakat;
            } else {
                $TotalPerZakat1 = 0;
            }
            $queryag2 = $this->db->query("SELECT TotalPerZakat,pagz.isActive AS isActive  FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $companyID . "' AND proposalID='" . $proposalID . "' AND EconStateID='" . $EconStateID . "' AND pagz.AgeGroupID='2'");
            $rowag2 = $queryag2->row();
            if (isset($rowag2) && $rowag2->isActive == '1') {
                $TotalPerZakat2 = $rowag2->TotalPerZakat;
            } else {
                $TotalPerZakat2 = 0;
            }
            $queryag3 = $this->db->query("SELECT TotalPerZakat,pagz.isActive AS isActive  FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $companyID . "' AND proposalID='" . $proposalID . "' AND EconStateID='" . $EconStateID . "' AND pagz.AgeGroupID='3'");
            $rowag3 = $queryag3->row();
            if (isset($rowag3) && $rowag3->isActive == '1') {
                $TotalPerZakat3 = $rowag3->TotalPerZakat;
            } else {
                $TotalPerZakat3 = 0;
            }

            $queryFD = $this->db->query("SELECT fd.Com_MasterID,fd.FamMasterID,FamDel_ID,FamilyName,CFullName,CName_with_initials,CurrentStatus,isMove,cm.isActive,cm.DeactivatedFor,COUNT(IF(0 <= TRIM(cm.Age) && TRIM(cm.Age) <= 5,1,NULL))  smal,COUNT(IF(6 <= TRIM(cm.Age) && TRIM(cm.Age) <= 15,1,NULL))  medi,COUNT(IF(16 <= TRIM(cm.Age),1,NULL)) larg FROM srp_erp_ngo_com_familydetails fd
  LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.FamMasterID=fd.FamMasterID LEFT JOIN srp_erp_ngo_com_communitymaster cm ON cm.Com_MasterID=fd.Com_MasterID WHERE fd.companyID='" . $companyID . "' AND fd.FamMasterID='" . $rowPbene->FamMasterID . "' AND cm.isActive='1' AND fd.isMove='0'");
            $rowag = $queryFD->row();

            $zakatAnt1 = $TotalPerZakat1 * $rowag->smal;

            $zakatAnt2 = $TotalPerZakat2 * $rowag->medi;

            $zakatAnt3 = $TotalPerZakat3 * $rowag->larg;

            $zakatAnt = ($zakatAnt1 + $zakatAnt2 + $zakatAnt3);

            $data[$key]['totalEstimatedValue'] = $zakatAnt;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserGroup'] = current_user_group();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdDateTime'] = current_date(true);
            $data[$key]['timestamp'] = current_date(true);

        }
        $result = $this->db->insert_batch('srp_erp_ngo_projectproposalbeneficiaries', $data);

        if ($result) {
            return array('s', 'Beneficiary Added successfully !');
        } else {
            return array('s', 'Beneficiary Insertion Failed');
        }
    }



    function delete_project_proposal_detail()
    {
        $proposalBeneficiaryID = trim($this->input->post('proposalBeneficiaryID') ?? '');
        $this->db->delete('srp_erp_ngo_projectproposalbeneficiaries', array('proposalBeneficiaryID' => $proposalBeneficiaryID));
        return true;
    }

    function delete_project_zakat_detail()
    {

        $proposalZaqathSetID = trim($this->input->post('proposalZaqathSetID') ?? '');
        //$this->db->delete('srp_erp_ngo_com_projectproposalzakatsetup', array('proposalZaqathSetID' => $proposalZaqathSetID));
        $data['isActive'] = '0';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = date('Y-m-d H:i:s');
        $data['modifiedUserName'] = $this->common_data['current_user'];

        $this->db->update('srp_erp_ngo_com_projectproposalzakatsetup', $data, array('proposalZaqathSetID' => $proposalZaqathSetID));
        return true;
    }

    function active_project_zakat_detail()
    {
        $proposalZaqathSetID = trim($this->input->post('proposalZaqathSetID') ?? '');
        //$this->db->delete('srp_erp_ngo_com_projectproposalzakatsetup', array('proposalZaqathSetID' => $proposalZaqathSetID));
        $data['isActive'] = '1';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = date('Y-m-d H:i:s');
        $data['modifiedUserName'] = $this->common_data['current_user'];

        $this->db->update('srp_erp_ngo_com_projectproposalzakatsetup', $data, array('proposalZaqathSetID' => $proposalZaqathSetID));
        return true;
    }

    function load_project_images()
    {
        $companyID = current_companyID();
        $ngoProjectID = trim($this->input->post('proposalID') ?? '');
        return $this->db->query("SELECT *,case master.imageType when 1 then 'Cover Imange' when 2 then 'Front Page Image' when 3 then 'House Plan' END as imageType FROM srp_erp_ngo_projectproposalimages master WHERE master.companyID={$companyID} AND master.ngoProposalID = {$ngoProjectID}")->result_array();
    }

    function project_proposal_confirmation()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->load->library('approvals');

        $this->db->select('*');
        $this->db->where('proposalID', $proposalID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_ngo_projectproposals');
        $row = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('proposalID', $proposalID);
        $this->db->from('srp_erp_ngo_projectproposals');
        $master = $this->db->get()->row_array();

        if (!empty($row)) {
            return array('w', 'Document already confirmed');
        } else {
            $approvals_status = $this->approvals->CreateApproval('PRP', $master['proposalID'], $master['documentSystemCode'], ' Project Proposal', 'srp_erp_ngo_projectproposals', 'proposalID');
            if ($approvals_status) {
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                    'proposalStageID' => 2,
                    'status' => 12
                );
                $this->db->where('proposalID', trim($this->input->post('proposalID') ?? ''));
                $this->db->update('srp_erp_ngo_projectproposals', $data);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Project Proposal Confirmed Failed ' . $this->db->_error_message());

                } else {

                    $this->db->trans_commit();
                    $data['master'] = $this->db->query("SELECT pro.projectImage,pp.proposalName as ppProposalName,pro.projectName as proProjectName,DATE_FORMAT(pp.DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(pp.startDate,'{$convertFormat}') AS ppStartDate,DATE_FORMAT(pp.endDate,'{$convertFormat}') AS ppEndDate,DATE_FORMAT(pp.DocumentDate, '%M %Y') as subprojectName,pp.detailDescription as ppDetailDescription,pp.projectSummary as ppProjectSummary,pp.totalNumberofHouses as ppTotalNumberofHouses,pp.floorArea as ppFloorArea,pp.costofhouse as ppCostofhouse,pp.additionalCost as ppAdditionalCost,pp.EstimatedDays as ppEstimatedDays,pp.proposalTitle as ppProposalTitle,pp.processDescription as ppProcessDescription,con.name as contractorName,ca.GLDescription as caBankAccName,ca.bankName as caBankName,ca.bankAccountNumber as caBankAccountNumber FROM srp_erp_ngo_projectproposals pp JOIN srp_erp_ngo_projects pro ON pp.projectID = pro.ngoProjectID LEFT JOIN srp_erp_ngo_contractors con ON pp.contractorID = con.contractorID LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = pp.bankGLAutoID WHERE pp.proposalID = $proposalID  ")->row_array();

                    $data['detail'] = $this->db->query("SELECT ppb.beneficiaryID as ppbBeneficiaryID,DATE_FORMAT(bm.registeredDate,'{$convertFormat}') AS bmRegisteredDate,DATE_FORMAT(bm.dateOfBirth,'{$convertFormat}') AS bmDateOfBirth,bm.nameWithInitials as bmNameWithInitials,bm.systemCode as bmSystemCode, CASE bm.ownLandAvailable WHEN 1 THEN 'Yes' WHEN 2 THEN 'No' END as bmOwnLandAvailable,bm.NIC as bmNIC,bm.familyMembersDetail as bmFamilyMembersDetail,bm.reasoninBrief as bmReasoninBrief,bm.totalSqFt as bmTotalSqFt,bm.totalCost as bmTotalCost,bm.helpAndNestImage as bmHelpAndNestImage,bm.helpAndNestImage1 as bmHelpAndNestImage1,bm.ownLandAvailableComments as bmOwnLandAvailableComments FROM srp_erp_ngo_projectproposalbeneficiaries ppb LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON ppb.beneficiaryID = bm.benificiaryID WHERE proposalID = $proposalID ")->result_array();

                    $data['images'] = $this->db->query("SELECT imageType,imageName FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = $proposalID ")->result_array();

                    $data['moto'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();

                    $data['proposalID'] = $proposalID;

                    $data['output'] = 'save';

                    $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print_all', $data, true);

                    return array('s', 'Project Proposal Confirmed Successfully');
                }
            }


        }

    }

    function update_beneficiary_edit()
    {

        $this->db->trans_start();

        $edit_BeneficiaryID = $this->input->post('edit_BeneficiaryID');
        $proBenificiaryID = $this->input->post('proBenificiaryID');

        $edit_beneEcState = $this->input->post('edit_beneEcState');
        $edit_beneTotZakAmnt = $this->input->post('edit_beneTotZakAmnt');

        $data['EconStateID'] = $edit_beneEcState;
        $data['totalEstimatedValue'] = $edit_beneTotZakAmnt;

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->update('srp_erp_ngo_projectproposalbeneficiaries', $data,
            array('proposalBeneficiaryID' => $edit_BeneficiaryID));


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Family Economic Status : update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();

            return array('status' => FALSE);
        } else {
            $this->session->set_flashdata('s', 'Family Economic Status : Records updated successfully.');
            $this->db->trans_commit();

            return array('s', 'Family Economic Status : Records updated successfully. ');
        }

    }

    function update_zakatSet_edit()
    {

        $this->db->trans_start();

        $edit_proposeId = $this->input->post('edit_proposeId');

        $edit_GrpPoints = $this->input->post('edit_GrpPoints');
        $edit_ZakAmount = $this->input->post('edit_ZakAmount');
        $edit_tZAmount = $this->input->post('edit_tZAmount');

        //$master = $this->db->query("select * from srp_erp_ngo_com_familymaster WHERE proposalZaqathSetID=$edit_proposeId")->row_array();

        $data['GrpPoints'] = $edit_GrpPoints;
        $data['ZakatAmount'] = $edit_ZakAmount;
        $data['TotalPerZakat'] = $edit_tZAmount;

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->update('srp_erp_ngo_com_projectproposalzakatsetup', $data,
            array('proposalZaqathSetID' => $edit_proposeId));
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Zakat Contribution : update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();

            return array('status' => FALSE);
        } else {
            $this->session->set_flashdata('s', 'Zakat Contribution : Records updated successfully.');
            $this->db->trans_commit();

            return array('s', 'Zakat Contribution : Records updated successfully. ');
        }

    }

    /* end of Zakat project proposal */

    public function saveUploadData_masCom()
    {

        $editComUploadID = $this->input->post('editComUploadID');
        $ComUploadType = $this->input->post('ComUploadType');
        $ComUploadSubject = $this->input->post('ComUploadSubject');
        $ComUpload_url = $this->input->post('ComUpload_url');
        $ComUploader = $this->input->post('ComUploader');
        $ComUploadDescription = $this->input->post('ComUploadDescription');
        $UploadPublishedDt = $this->input->post('UploadPublishedDate');
        $ComUploadExpireDt = $this->input->post('ComUploadExpireDate');
        $isRequired = $this->input->post('isRequired');
        $uploadGSDiviID= $this->input->post('uploadGSDiviID');

        $date_format_policy = date_format_policy();

//var_dump($uploadGSDiviID);

        if (!empty($isRequired)) {
            $isRequiredS = $isRequired;
        }
        else{
            $isRequiredS = 0;
        }

        if($UploadPublishedDt == ''){

            $UploadPublishedDate = null;

        }
        else{
            $UploadPublishedDate =input_format_date($this->input->post('UploadPublishedDate'), $date_format_policy);

        }

        if($ComUploadExpireDt == ''){

            $ComUploadExpireDate = null;

        }
        else{
            $ComUploadExpireDate =input_format_date($this->input->post('ComUploadExpireDate'), $date_format_policy);

        }

        $companyID = current_companyID();

        $isUpExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploads  t1  WHERE t1.companyID='" . $companyID . "' AND familyUplaod='0' AND t1.ComUploadID = '" . $editComUploadID . "'")->result_array();

        if (empty($isUpExist)) {

            $this->db->trans_start();

            $data_upload = array(
                'ComUploadType' => $ComUploadType,
                'ComUploadSubject' => $ComUploadSubject,
                'ComUpload_url' => $ComUpload_url,
                'ComUploader' => $ComUploader,
                'ComUploadDescription' => $ComUploadDescription,
                'UploadPublishedDate' => $UploadPublishedDate,
                'ComUploadExpireDate' => $ComUploadExpireDate,
                'ComUploadSubmited' => $isRequiredS,

                'companyID' => current_companyID(),
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_erp_ngo_com_uploads', $data_upload);

            $last_id = $this->db->insert_id();

            $p = 1;
            foreach ($uploadGSDiviID as $key => $gsuDivisions){

                $datauGS['ComUploadID'] = $last_id;
                $datauGS['uploadGSDiviID'] = $gsuDivisions;
                $datauGS['companyID'] = $this->common_data['company_data']['company_id'];
                $datauGS['createdUserGroup'] = $this->common_data['user_group'];
                $datauGS['createdPCID'] = $this->common_data['current_pc'];
                $datauGS['createdUserID'] = $this->common_data['current_userID'];
                $datauGS['createdUserName'] = $this->common_data['current_user'];
                $datauGS['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_ngo_com_uploadsdivision', $datauGS);
                $p++;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {

            $this->db->trans_start();

            $data_upUpdate = array(
                'ComUploadType' => $ComUploadType,
                'ComUploadSubject' => $ComUploadSubject,
                'ComUpload_url' => $ComUpload_url,
                'ComUploader' => $ComUploader,
                'ComUploadDescription' => $ComUploadDescription,
                'UploadPublishedDate' => $UploadPublishedDate,
                'ComUploadExpireDate' => $ComUploadExpireDate,
                'ComUploadSubmited' => $isRequiredS,

                'companyID' => current_companyID(),
                'ModifiedPC' => current_pc(),
                'ModifiedUserName' => current_employee(),
                'Timestamp' => current_date()
            );

            $this->db->update('srp_erp_ngo_com_uploads', $data_upUpdate, array('ComUploadID' => $editComUploadID));

            $isUpDiviAll = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploadsdivision  tu  WHERE tu.companyID='" . $companyID . "' AND tu.ComUploadID = '" . $editComUploadID . "'");
            $res_allUp = $isUpDiviAll->result();

            $ExistingUp = array();
            foreach ($res_allUp as $row_existedUp) {
                $ExistingUp[] = $row_existedUp->uploadGSDiviID;
            }

            $p5 = 1;
            foreach ($uploadGSDiviID as $key => $gsuDivisions) {

                if (in_array($gsuDivisions, $ExistingUp)) {
                    // $this->db->delete('srp_erp_ngo_com_uploadsdivision', array('uploadGSDiviID' => $gsuDivisions));
                    $output  = $this->db->where('ComUploadID', $editComUploadID,'uploadGSDiviID !=',$gsuDivisions)->delete('srp_erp_ngo_com_uploadsdivision');

                }
                else{

                }
                $p5++;
            }
            $p2 = 1;
            foreach ($uploadGSDiviID as $key => $gsuDivisions) {

                $isUpDiviExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploadsdivision  tu  WHERE tu.companyID='" . $companyID . "' AND tu.ComUploadID = '" . $editComUploadID . "' AND tu.uploadGSDiviID = '" . $gsuDivisions . "'")->result_array();

                if(!$isUpDiviExist){
                    $datauGS['ComUploadID'] = $editComUploadID;
                    $datauGS['uploadGSDiviID'] = $gsuDivisions;
                    $datauGS['companyID'] = $this->common_data['company_data']['company_id'];
                    $datauGS['createdUserGroup'] = $this->common_data['user_group'];
                    $datauGS['createdPCID'] = $this->common_data['current_pc'];
                    $datauGS['createdUserID'] = $this->common_data['current_userID'];
                    $datauGS['createdUserName'] = $this->common_data['current_user'];
                    $datauGS['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_ngo_com_uploadsdivision', $datauGS);
                }

                $p2++;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        }

    }

    function delete_comUpload()
    {

        $ComUploadID = trim($this->input->post('ComUploadID') ?? '');

        $isDivInChk = $this->db->query("SELECT ComUploadID FROM srp_erp_ngo_com_uploadsdivision WHERE ComUploadID = '$ComUploadID' ")->row('ComUploadID');

        if (empty($isDivInChk)) {

            $del = $this->db->where('ComUploadID', $ComUploadID)->delete('srp_erp_ngo_com_uploadsdivision');
        }

        $output  = $this->db->where('ComUploadID', $ComUploadID)->delete('srp_erp_ngo_com_uploads');
        if ($output) {
            return array('error' => 0, 'message' => 'Successfully uploaded data deleted');
        } else {
            return array('error' => 1, 'message' => 'Error while updating');
        }


    }

    public function edit_comUpload($ComUploadID)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploads WHERE ComUploadID = '" . $ComUploadID . "'");
        $res = $query->result();
        foreach ($res as $row) {
            echo json_encode(
                array(
                    "ComUploadID" => $row->ComUploadID,
                    "ComUploadType" => $row->ComUploadType,
                    "ComUploadSubject" => $row->ComUploadSubject,
                    "ComUpload_url" => $row->ComUpload_url,
                    "ComUploadDescription" => $row->ComUploadDescription,
                    "ComUploader" => $row->ComUploader,
                    "ComUploadSubmited" => $row->ComUploadSubmited,
                    "UploadPublishedDate" => $row->UploadPublishedDate,
                    "ComUploadExpireDate" => $row->ComUploadExpireDate,

                )
            );
        }
        return $res;
    }

    public function editGS_comUpload($ComUploadID)
    {

        $upGSDivision = load_divisionForUploads();

        $queryup = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploadsdivision WHERE ComUploadID = '" . $ComUploadID . "'");
        $res_existingUp = $queryup->result();

        $ExistingState = array();
        foreach ($res_existingUp as $row_existingUp) {
            $ExistingState[] = $row_existingUp->uploadGSDiviID;
        }
        echo'<select class="select2 uploadGSDiviID" name="uploadGSDiviID[]" id="uploadGSDiviID" style="width: 100%;" multiple>';

        foreach ($upGSDivision as $row3) {

            if (in_array($row3['stateID'], $ExistingState)) {

                echo '<option id="opt' . $row3['stateID'] . '" value="' . $row3['stateID']. '" selected="selected">' . $row3['Description'] . '</option>';
            } else {
                echo '<option id="opt' . $row3['stateID'] . '" value="' . $row3['stateID'] . '">' . $row3['Description'] . '</option>';
            }
        }
        echo '</select>';
    }

    /* user uploads management */
    public function save_user_UpldData_masCom()
    {

        $editComUploadID = $this->input->post('editComUploadID');
        $ComUploadType = $this->input->post('ComUploadType');
        $ComUploadSubject = $this->input->post('ComUploadSubject');
        $ComUpload_url = $this->input->post('ComUpload_url');
        $ComUploader = $this->input->post('ComUploader');
        $ComUploadDescription = $this->input->post('ComUploadDescription');
        $UploadPublishedDt = $this->input->post('UploadPublishedDate');
        $ComUploadExpireDt = $this->input->post('ComUploadExpireDate');
        $isRequired = $this->input->post('isRequired');
        $uploadGSDiviID= $this->input->post('uploadGSDiviID');

        $date_format_policy = date_format_policy();

//var_dump($uploadGSDiviID);

        if (!empty($isRequired)) {
            $isUserUpldActive = $isRequired;
        }
        else{
            $isUserUpldActive = 0;
        }

        if($UploadPublishedDt == ''){

            $UploadPublishedDate = null;

        }
        else{
            $UploadPublishedDate =input_format_date($this->input->post('UploadPublishedDate'), $date_format_policy);

        }

        if($ComUploadExpireDt == ''){

            $ComUploadExpireDate = null;

        }
        else{
            $ComUploadExpireDate =input_format_date($this->input->post('ComUploadExpireDate'), $date_format_policy);

        }

        $companyID = current_companyID();

        $isUpExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploads  t1  WHERE t1.companyID='" . $companyID . "' AND familyUplaod='1' AND t1.ComUploadID = '" . $editComUploadID . "'")->result_array();

        if (empty($isUpExist)) {

            $this->db->trans_start();

            $data_upload = array(
                'ComUploadType' => $ComUploadType,
                'ComUploadSubject' => $ComUploadSubject,
                'ComUpload_url' => $ComUpload_url,
                'ComUploader' => $ComUploader,
                'ComUploadDescription' => $ComUploadDescription,
                'UploadPublishedDate' => $UploadPublishedDate,
                'ComUploadExpireDate' => $ComUploadExpireDate,
                'familyUplaod' => '1',
                'familyUplaod_active' => $isUserUpldActive,

                'companyID' => current_companyID(),
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_erp_ngo_com_uploads', $data_upload);

            $last_id = $this->db->insert_id();

            $p = 1;
            foreach ($uploadGSDiviID as $key => $gsuDivisions){

                $datauGS['ComUploadID'] = $last_id;
                $datauGS['uploadGSDiviID'] = $gsuDivisions;
                $datauGS['companyID'] = $this->common_data['company_data']['company_id'];
                $datauGS['createdUserGroup'] = $this->common_data['user_group'];
                $datauGS['createdPCID'] = $this->common_data['current_pc'];
                $datauGS['createdUserID'] = $this->common_data['current_userID'];
                $datauGS['createdUserName'] = $this->common_data['current_user'];
                $datauGS['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_ngo_com_uploadsdivision', $datauGS);
                $p++;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {

            $this->db->trans_start();

            $data_upUpdate = array(
                'ComUploadType' => $ComUploadType,
                'ComUploadSubject' => $ComUploadSubject,
                'ComUpload_url' => $ComUpload_url,
                'ComUploader' => $ComUploader,
                'ComUploadDescription' => $ComUploadDescription,
                'UploadPublishedDate' => $UploadPublishedDate,
                'ComUploadExpireDate' => $ComUploadExpireDate,
                'familyUplaod' => '1',
                'familyUplaod_active' => $isUserUpldActive,

                'companyID' => current_companyID(),
                'ModifiedPC' => current_pc(),
                'ModifiedUserName' => current_employee(),
                'Timestamp' => current_date()
            );

            $this->db->update('srp_erp_ngo_com_uploads', $data_upUpdate, array('ComUploadID' => $editComUploadID));

            $isUpDiviAll = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploadsdivision  tu  WHERE tu.companyID='" . $companyID . "' AND tu.ComUploadID = '" . $editComUploadID . "'");
            $res_allUp = $isUpDiviAll->result();

            $ExistingUp = array();
            foreach ($res_allUp as $row_existedUp) {
                $ExistingUp[] = $row_existedUp->uploadGSDiviID;
            }

            $p5 = 1;
            foreach ($uploadGSDiviID as $key => $gsuDivisions) {

                if (in_array($gsuDivisions, $ExistingUp)) {
                    // $this->db->delete('srp_erp_ngo_com_uploadsdivision', array('uploadGSDiviID' => $gsuDivisions));
                    $output  = $this->db->where('ComUploadID', $editComUploadID,'uploadGSDiviID !=',$gsuDivisions)->delete('srp_erp_ngo_com_uploadsdivision');

                }
                else{

                }
                $p5++;
            }
            $p2 = 1;
            foreach ($uploadGSDiviID as $key => $gsuDivisions) {

                $isUpDiviExist = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploadsdivision  tu  WHERE tu.companyID='" . $companyID . "' AND tu.ComUploadID = '" . $editComUploadID . "' AND tu.uploadGSDiviID = '" . $gsuDivisions . "'")->result_array();

                if(!$isUpDiviExist){
                    $datauGS['ComUploadID'] = $editComUploadID;
                    $datauGS['uploadGSDiviID'] = $gsuDivisions;
                    $datauGS['companyID'] = $this->common_data['company_data']['company_id'];
                    $datauGS['createdUserGroup'] = $this->common_data['user_group'];
                    $datauGS['createdPCID'] = $this->common_data['current_pc'];
                    $datauGS['createdUserID'] = $this->common_data['current_userID'];
                    $datauGS['createdUserName'] = $this->common_data['current_user'];
                    $datauGS['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_ngo_com_uploadsdivision', $datauGS);
                }

                $p2++;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        }

    }

    function delete_user_comUpload()
    {

        $ComUploadID = trim($this->input->post('ComUploadID') ?? '');

        $isDivInChk = $this->db->query("SELECT ComUploadID FROM srp_erp_ngo_com_uploadsdivision WHERE ComUploadID = '$ComUploadID' ")->row('ComUploadID');

        if (empty($isDivInChk)) {

            $del = $this->db->where('ComUploadID', $ComUploadID)->delete('srp_erp_ngo_com_uploadsdivision');
        }

        $output  = $this->db->where('ComUploadID', $ComUploadID)->delete('srp_erp_ngo_com_uploads');
        if ($output) {
            return array('error' => 0, 'message' => 'Successfully uploaded data deleted');
        } else {
            return array('error' => 1, 'message' => 'Error while updating');
        }


    }

    public function edit_user_comUpload($ComUploadID)
    {
        $query = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploads WHERE ComUploadID = '" . $ComUploadID . "'");
        $res = $query->result();
        foreach ($res as $row) {
            echo json_encode(
                array(
                    "ComUploadID" => $row->ComUploadID,
                    "ComUploadType" => $row->ComUploadType,
                    "ComUploadSubject" => $row->ComUploadSubject,
                    "ComUpload_url" => $row->ComUpload_url,
                    "ComUploadDescription" => $row->ComUploadDescription,
                    "ComUploader" => $row->ComUploader,
                    "familyUplaod_active" => $row->familyUplaod_active,
                    "UploadPublishedDate" => $row->UploadPublishedDate,
                    "ComUploadExpireDate" => $row->ComUploadExpireDate,

                )
            );
        }
        return $res;
    }

    public function editGS_user_comUpload($ComUploadID)
    {

        $upGSDivision = load_divisionForUploads();

        $queryup = $this->db->query("SELECT * FROM srp_erp_ngo_com_uploadsdivision WHERE ComUploadID = '" . $ComUploadID . "'");
        $res_existingUp = $queryup->result();

        $ExistingState = array();
        foreach ($res_existingUp as $row_existingUp) {
            $ExistingState[] = $row_existingUp->uploadGSDiviID;
        }
        echo'<select class="select2 uploadGSDiviID" name="uploadGSDiviID[]" id="uploadGSDiviID" style="width: 100%;" multiple>';

        foreach ($upGSDivision as $row3) {

            if (in_array($row3['stateID'], $ExistingState)) {

                echo '<option id="opt' . $row3['stateID'] . '" value="' . $row3['stateID']. '" selected="selected">' . $row3['Description'] . '</option>';
            } else {
                echo '<option id="opt' . $row3['stateID'] . '" value="' . $row3['stateID'] . '">' . $row3['Description'] . '</option>';
            }
        }
        echo '</select>';
    }

    //community donors
    function save_com_donor_header()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $contactMasterID = trim($this->input->post('contactID') ?? '');
        $Com_MasterID = trim($this->input->post('requestedComMemID') ?? '');


        $this->db->select('phoneCountryCodePrimary,phonePrimary');
        $this->db->where('phoneCountryCodePrimary', trim($this->input->post('countryCodePrimary') ?? ''));
        $this->db->where('phonePrimary', trim($this->input->post('phonePrimary') ?? ''));
        $this->db->where('companyID', $companyID);
        if ($contactMasterID) {
            $this->db->where('contactID !=', $contactMasterID);
        }
        $this->db->from('srp_erp_ngo_donors');
        $recordExist = $this->db->get()->row_array();
        if (!empty($recordExist)) {
            return array('w', 'Primary Phone Number is already Exist.');
            exit();
        }

        $this->db->trans_start();
        $data['name'] = trim($this->input->post('name') ?? '');
        $data['Com_MasterID'] = trim($this->input->post('requestedComMemID') ?? '');
        $data['contactImage'] = trim($this->input->post('contactImage') ?? '');
        $data['email'] = trim($this->input->post('email') ?? '');
        $data['currencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['currencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['currencyID']);
        $data['phoneCountryCodePrimary'] = trim($this->input->post('countryCodePrimary') ?? '');
        $data['phoneAreaCodePrimary'] = trim($this->input->post('phoneAreaCodePrimary') ?? '');
        $data['phonePrimary'] = trim($this->input->post('phonePrimary') ?? '');
        $data['phoneCountryCodeSecondary'] = trim($this->input->post('countryCodeSecondary') ?? '');
        $data['phoneAreaCodeSecondary'] = trim($this->input->post('phoneAreaCodeSecondary') ?? '');
        $data['phoneSecondary'] = trim($this->input->post('phoneSecondary') ?? '');
        $data['fax'] = trim($this->input->post('fax') ?? '');
        $data['postalCode'] = trim($this->input->post('postalcode') ?? '');
        $data['city'] = trim($this->input->post('city') ?? '');
        $data['state'] = trim($this->input->post('state') ?? '');
        $data['website'] = trim($this->input->post('website') ?? '');
        $data['countryID'] = trim($this->input->post('countryID') ?? '');
        $data['address'] = trim($this->input->post('address') ?? '');

        $isComDonorExist = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_donors WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' ")->row('Com_MasterID');

        if ($contactMasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('contactID', trim($this->input->post('contactID') ?? ''));
            $update = $this->db->update('srp_erp_ngo_donors', $data);
            if ($update) {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return array('e', 'Donor Update Failed ' /*. $this->db->_error_message()*/
                    );

                } else {
                    $this->db->trans_commit();

                    return array('s', 'Donor Updated Successfully.', $contactMasterID);
                }
            }
        }
        else {
            if (!empty($isComDonorExist)) {
                return array('e', 'The Community Member is already Exists');

                exit();
            }
            else{
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_donors', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return array('e', 'Donor Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();

                return array('s', 'Donor Saved Successfully.');
            }
           }
        }
    }

    function delete_community_donor_mast()
    {
        $contacID = trim($this->input->post('contactID') ?? '');
        $this->db->where('documentID', 1);
        $this->db->where('documentAutoID', $contacID);
        $this->db->delete('srp_erp_ngo_notes');

        $this->db->where('documentID', 1);
        $this->db->where('documentAutoID', $contacID);
        $this->db->delete('srp_erp_ngo_attachments');

        $this->db->delete('srp_erp_ngo_donors', array('contactID' => trim($this->input->post('contactID') ?? '')));

        return TRUE;
    }

    function load_com_donor_header()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->where('contactID', $this->input->post('contactID'));
        $this->db->from('srp_erp_ngo_donors');
        $data = $this->db->get()->row_array();

        return $data;

    }

    function add_com_donor_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $contactID = trim($this->input->post('contactID') ?? '');

        $data['documentID'] = 1;
        $data['documentAutoID'] = $contactID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_ngo_notes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Donor Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();

            return array('s', 'Donor Note Added Successfully.');

        }
    }

    function com_donor_image_upload()
    {
        $this->db->trans_start();
      /*  $output_dir = "uploads/NGO/donorsImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/donorsImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Donor_' . trim($this->input->post('contactID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);*/

        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Donor_'.$this->common_data['company_data']['company_code'].'_'. trim($this->input->post('contactID') ?? '') . '.' . $info->getExtension();
        $currentDatetime = format_date_mysql_datetime();
        $file = $_FILES['files'];
        if($file['error'] == 1){
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if(!in_array($ext, $allowed_types)){
            return array('e', "The file type you are attempting to upload is not allowed. ( .{$ext} )");
        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);
        if($size > 5){
            return array('e', 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB)');
        }
        $path = "uploads/ngo/donorsImage/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', 'Error in document upload location configuration');
        }

        $data['contactImage'] = $fileName;

        $this->db->where('contactID', trim($this->input->post('contactID') ?? ''));
        $this->db->update('srp_erp_ngo_donors', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');
        }
    }

    //end of community donors

    /* end of moufi */


    /* start */

    function Save_new_notice(){
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $NoticeTypeID = $this->input->post('NoticeTypeID');
        $NoticeEdit = $this->input->post('noticeId');
        $gsDivision = $this->input->post('noticestateID');

        $date_format_policy = date_format_policy();
        $dateTimeFormate = 'Y-m-d H:i:s';
        $dateTimeFormat = 'Y-m-d H:i:s';

        $data['NoticeTypeID'] = $this->input->post('NoticeTypeID');
        $data['isSubmited'] = $this->input->post('active');
        $data['NoticePublishedDate'] = input_format_date($this->input->post('NoticePublishedDate'), $date_format_policy);

        $ExpDate = $this->input->post('NoticeExpireDate');
        if($ExpDate == ''){
            $data['NoticeExpireDate'] = null;
        } else{
            $data['NoticeExpireDate'] = input_format_date($this->input->post('NoticeExpireDate'), $date_format_policy);
        }


        if($NoticeTypeID == 1){
            $data['DeadPerson'] = $this->input->post('DeadPerson');
            $data['NoticeInformer'] = $this->input->post('deathInformer');
            $data['DeadPrsnFamilBCR'] = $this->input->post('DeadPrsnFamilBCR');
            $data['NoticeDescription'] = $this->input->post('DeathDescription');
            $data['VenuePlace'] = $this->input->post('burialPlace');
            $data['VenueDateTime'] =format_date_mysql_datetime($this->input->post('burialDate'),  $dateTimeFormate);

        }else if($NoticeTypeID == 2){
            $data['VenueDateTime'] =format_date_mysql_datetime($this->input->post('bayanVenueDate'), $dateTimeFormate);

            $data['VenuePlace'] = $this->input->post('VenuePlace');
            $data['Speaker'] = $this->input->post('Speaker');
            $data['NoticeInformer'] = $this->input->post('bayanOrganizer');
            $data['NoticeSubject'] = $this->input->post('BayanSubject');
            $data['NoticeDescription'] = $this->input->post('BayanDescription');

        } else if ($NoticeTypeID == 3){
            $data['NoticeSubject'] = $this->input->post('NoticeSubject');
            $data['NoticeDescription'] = $this->input->post('NoticeDescription');
        }

        if(!$NoticeEdit) {

            $data['companyID'] = $companyID;
            $data['CreatedPC'] = $this->common_data['current_pc'];
            $data['CreatedUserName'] = $this->common_data['current_user'];
            $data['CreatedDate'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_com_noticeboard', $data);
            $last_id = $this->db->insert_id();



            $aa = 1;
            foreach ($gsDivision as $key => $gsDivisions){

                $dataGS['NoticeID'] = $last_id;
                $dataGS['NoticeGSDiviID'] = $gsDivisions;
                $dataGS['companyID'] = $this->common_data['company_data']['company_id'];
                $dataGS['createdUserGroup'] = $this->common_data['user_group'];
                $dataGS['createdPCID'] = $this->common_data['current_pc'];
                $dataGS['createdUserID'] = $this->common_data['current_userID'];
                $dataGS['createdUserName'] = $this->common_data['current_user'];
                $dataGS['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_ngo_com_noticeboardgsdivision', $dataGS);
                $aa++;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Failed to create Announcement');
            } else {
                $this->db->trans_commit();
                return array('s', 'Announcement Created Successfully');
            }
        } else{

            $isGSDiviAll = $this->db->query("SELECT * FROM srp_erp_ngo_com_noticeboardgsdivision  gs  WHERE gs.companyID='" . $companyID . "' AND gs.NoticeID = '" . $NoticeEdit . "'");
            $res_allNoticeId = $isGSDiviAll->result();

            $Existingstate = array();
            foreach ($res_allNoticeId as $row_existedstate) {
                $Existingstate[] = $row_existedstate->NoticeGSDiviID;
            }

            $aaa = 1;
            foreach ($gsDivision as $key => $gsNDivisions) {

                if (in_array($gsNDivisions, $Existingstate)) {
                    $output  = $this->db->where('NoticeID', $NoticeEdit,'uploadGSDiviID !=',$gsNDivisions)->delete('srp_erp_ngo_com_noticeboardgsdivision');
                }
                else{

                }
                $aaa++;
            }

            $aa = 1;
            foreach ($gsDivision as $key => $gsDivisions){
                $data1 = $this->db->query("select NoticeGSAutoID from srp_erp_ngo_com_noticeboardgsdivision WHERE NoticeID = {$NoticeEdit} AND NoticeGSDiviID = {$gsDivisions}")->row_array();
                if(!$data1){
                    $dataGS['NoticeID'] = $NoticeEdit;
                    $dataGS['NoticeGSDiviID'] = $gsDivisions;
                    $dataGS['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataGS['createdUserGroup'] = $this->common_data['user_group'];
                    $dataGS['createdPCID'] = $this->common_data['current_pc'];
                    $dataGS['createdUserID'] = $this->common_data['current_userID'];
                    $dataGS['createdUserName'] = $this->common_data['current_user'];
                    $dataGS['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_ngo_com_noticeboardgsdivision', $dataGS);
                }
                $aa++;
            }

            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['ModifiedUserName'] = $this->common_data['current_user'];
            $data['Timestamp'] = $this->common_data['current_date'];
            $this->db->where('NoticeID', trim($this->input->post('noticeId') ?? ''));
            $update = $this->db->update('srp_erp_ngo_com_noticeboard', $data);
            if ($update) {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Failed to Update Announcement');
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Announcement Updated Successfully');
                }
            }
        }
    }

    function get_announcements()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertdateformat = convert_date_format_sql();

        $status = $this->input->post('status'); //expired or not
        $type = $this->input->post('noticeType');
        $dateTo = $this->input->post('dateTo');
        $dateFrom = $this->input->post('dateFrom');

        $current_date = current_format_date();
        $date_format_policy = date_format_policy();
        $datefrom = input_format_date('00-00-0000', $date_format_policy);
        $dateto = input_format_date($current_date, $date_format_policy);

        if($status == 0){
            $date = '';
        }else if($status == 1){
            $date = " AND ( NoticeExpireDate >= '". $dateto ."')";
        }else if($status == 2){
            $date = " AND ( NoticeExpireDate BETWEEN '". $datefrom ."' AND '" . $dateto . " ')";
        }

        if($type == '' || $type == 0){
            $typeStat = '';
        }else{
            $typeStat = " AND ( srp_erp_ngo_com_noticeboard.NoticeTypeID = '" . $type . " ')";
        }
        $output['noticeDate'] = $this->db->query("SELECT srp_erp_ngo_com_noticeboard.NoticeID,(DATE_FORMAT(NoticePublishedDate,'{$convertdateformat}'))as NoticePublishedDate, (DATE_FORMAT(NoticeExpireDate,'{$convertdateformat}'))as NoticeExpireDate FROM srp_erp_ngo_com_noticeboard  INNER JOIN srp_erp_ngo_com_noticeboardmaster ON srp_erp_ngo_com_noticeboardmaster.NoticeTypeID = srp_erp_ngo_com_noticeboard.NoticeTypeID LEFT JOIN srp_erp_ngo_com_noticeboardgsdivision ON srp_erp_ngo_com_noticeboardgsdivision.NoticeID=srp_erp_ngo_com_noticeboard.NoticeID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_noticeboardgsdivision.NoticeGSAutoID WHERE srp_erp_ngo_com_noticeboard.companyID = {$companyID} AND srp_erp_ngo_com_noticeboard.isSubmited='1' $date $typeStat  AND NoticePublishedDate >='".$dateFrom."' AND NoticePublishedDate <='".$dateTo."' GROUP BY srp_erp_ngo_com_noticeboard.NoticePublishedDate ORDER BY srp_erp_ngo_com_noticeboard.NoticePublishedDate DESC")->result_array();
        $output['validateDate'] = $date;
        $output['validateType'] = $typeStat;
        return $output;
    }

    function editNoticeAnnouncement(){
        $convertFormat = convert_date_format_sql();

        $id = $this->input->post('NoticeID');
        $data['master'] = $this->db->query("select * ,DATE_FORMAT(NoticePublishedDate,'{$convertFormat}') AS NoticePublishedDate,DATE_FORMAT(NoticeExpireDate,'{$convertFormat}') AS NoticeExpireDate, srp_erp_ngo_com_noticeboardgsdivision.NoticeGSDiviID FROM srp_erp_ngo_com_noticeboard LEFT JOIN srp_erp_ngo_com_noticeboardgsdivision ON srp_erp_ngo_com_noticeboardgsdivision.NoticeID = srp_erp_ngo_com_noticeboard.NoticeID WHERE srp_erp_ngo_com_noticeboard.NoticeID = {$id} ")->row_array();

        return $data;
    }

    function editNoticeAnnouncementGSDivision(){

        $id = $this->input->post('NoticeID');

        $GSDivision = load_divisionForUploads();

        $queryup = $this->db->query("SELECT * FROM srp_erp_ngo_com_noticeboardgsdivision WHERE NoticeID = '" . $id . "'");
        $res_existingGSdivi = $queryup->result();

        $ExistingNoticeState = array();
        foreach ($res_existingGSdivi as $row_existingGS) {
            $ExistingNoticeState[] = $row_existingGS->NoticeGSDiviID;
        }
        echo'<select class="select2 noticestateID" name="noticestateID[]" id="noticestateID" style="width: 100%;" multiple>';

        foreach ($GSDivision as $row3) {

            if (in_array($row3['stateID'], $ExistingNoticeState)) {

                echo '<option id="opt' . $row3['stateID'] . '" value="' . $row3['stateID']. '" selected="selected">' . $row3['Description'] . '</option>';
            } else {
                echo '<option id="opt' . $row3['stateID'] . '" value="' . $row3['stateID'] . '">' . $row3['Description'] . '</option>';
            }
        }
        echo '</select>';

    }

    function delete_notice(){
        $NoticeID = trim($this->input->post('NoticeID') ?? '');

        $this->db->trans_start();

        $data = $this->db->query("select NoticeGSAutoID from srp_erp_ngo_com_noticeboardgsdivision WHERE NoticeID = {$NoticeID} ")->result_array();

        $aa = 1;
        foreach ($data as $key => $gsDivisions){
            $this->db->where('NoticeGSAutoID', $gsDivisions['NoticeGSAutoID'])->delete('srp_erp_ngo_com_noticeboardgsdivision');

            $aa++;
        }

        $this->db->where('NoticeID', $NoticeID)->delete('srp_erp_ngo_com_noticeboard');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    //return $query1->result_array();



    /* end */
}
