<?php

class CommunityJammiyaDashboard extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('CommunityJammiya_dash_modal');
    }


    function communityDashSum_Count()
    {
        echo json_encode($this->CommunityJammiya_dash_modal->communityDashSum_Count());
    }

    function commPopulation_Count()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $countyID = $this->input->post("countyID");
        $provinceID = $this->input->post("provinceID");
        $districtID = $this->input->post("districtID");
        $districtDivisionID = $this->input->post("districtDivisionID");

        $areaMemId = $this->input->post('areaMemId');
        $gsDivitnId = $this->input->post('gsDivitnId');

        $areaMemIdS = "";
        if (isset($areaMemId) && !empty($areaMemId)) {
            $areaMemIdS = "AND srp_erp_ngo_com_communitymaster.RegionID IN(" . join(',', $areaMemId) . ")";
        }

        $gsDivitnIdS = "";
        if (isset($gsDivitnId) && !empty($gsDivitnId)) {
            $gsDivitnIdS = "AND srp_erp_ngo_com_communitymaster.GS_Division IN(" . join(',', $gsDivitnId) . ")";
        }

        $filter_req = array("AND (srp_erp_ngo_com_communitymaster.countyID=" . $countyID . ")" => $countyID,"AND (srp_erp_ngo_com_communitymaster.provinceID=" . $provinceID . ")" => $provinceID,"AND (srp_erp_ngo_com_communitymaster.districtID=" . $districtID . ")" => $districtID,"AND (srp_erp_ngo_com_communitymaster.districtDivisionID=" . $districtDivisionID . ")" => $districtDivisionID);
        $set_filter_req = array_filter($filter_req);
        $where_clsDsh = join(" ", array_keys($set_filter_req));

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID ;

        $data['comMaster'] = $this->db->query("SELECT srp_erp_ngo_com_communitymaster.SerialNo AS SerialNos FROM srp_erp_ngo_com_communitymaster WHERE $where ")->row_array();


        $member_count = $this->db->query("SELECT COUNT(*) as membersCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();

        $male_count = $this->db->query("SELECT COUNT(*) as malesCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND GenderID='1' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS")->row_array();

        $female_count = $this->db->query("SELECT COUNT(*) as femalesCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND GenderID='2' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS")->row_array();


        $data['members'] = $member_count['membersCount'];
        $data['males'] = $male_count['malesCount'];
        $data['females'] = $female_count['femalesCount'];


        //get Occupation-Wise
        $data['OccupationBase'] = $this->db->query("SELECT srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_occupationtypes INNER JOIN srp_erp_ngo_com_memjobs ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS GROUP BY srp_erp_ngo_com_occupationtypes.OccTypeID")->result_array();
        $occupation_count = $this->db->query("SELECT COUNT(*) as occupationCount FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."'")->row_array();
        $data['occupation_type1'] = $this->db->query("SELECT COUNT(*) as occType1,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='1' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS")->row_array();
        $data['occupation_type2'] = $this->db->query("SELECT COUNT(*) as occType2,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='2' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type3'] = $this->db->query("SELECT COUNT(*) as occType3,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='3' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type4'] = $this->db->query("SELECT COUNT(*) as occType4,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='4' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type5'] = $this->db->query("SELECT COUNT(*) as occType5,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='5' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type6'] = $this->db->query("SELECT COUNT(*) as occType6,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='6' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();


        $data['occupationTot'] = $occupation_count['occupationCount'];
        //end of  Occupation-Wise

        //get blood group counts

        $delBlood = $this->db->query("SELECT BloodTypeID,BloodDescription FROM srp_erp_bloodgrouptype ORDER BY BloodTypeID DESC ");
        $res_bloodGrp = $delBlood->result();
        $BloodTypeIDs = array();
        foreach($res_bloodGrp as $row_bloodGrps) {
            $BloodTypeIDs[] = $row_bloodGrps->BloodTypeID;
        }

        $BloodTypeID = "'" . implode("', '", $BloodTypeIDs) . "'";

        $data['loadBloodDes'] = $this->db->query("SELECT BloodTypeID,BloodDescription FROM srp_erp_bloodgrouptype WHERE BloodTypeID IN ($BloodTypeID) ORDER BY BloodTypeID DESC ")->result_array();

        $data['loadBloodCount'] = $this->db->query("SELECT COUNT(*) AS `NoOfGrpMem` FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_bloodgrouptype ON srp_erp_ngo_com_communitymaster.BloodGroupID=srp_erp_bloodgrouptype.BloodTypeID WHERE companyID = '".$companyID."' AND BloodGroupID IN ($BloodTypeID) $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS GROUP BY BloodGroupID")->result_array();

        //end of blood group counts

        //get Marital Status
        $data['maritalBase'] = $this->db->query("SELECT srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_maritalstatus INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS GROUP BY srp_erp_ngo_com_maritalstatus.maritalstatusID ORDER BY srp_erp_ngo_com_maritalstatus.maritalstatusID DESC")->result_array();
        $marital_count = $this->db->query("SELECT COUNT(*) as maritalStCount FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type1'] = $this->db->query("SELECT COUNT(*) as merrType1,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='1' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type2'] = $this->db->query("SELECT COUNT(*) as merrType2,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='2' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type3'] = $this->db->query("SELECT COUNT(*) as merrType3,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='3' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type4'] = $this->db->query("SELECT COUNT(*) as merrType4,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='4' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type5'] = $this->db->query("SELECT COUNT(*) as merrType5,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='5' $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();


        $data['maritalStCount'] = $marital_count['maritalStCount'];
        //end of Marital Status
        //get Family Ancestry
        $query_ancesData = $this->db->query("SELECT DISTINCT AncestryCatID,AncestryDes FROM srp_erp_ngo_com_ancestrycategory");
        $res_ancesData = $query_ancesData->result();
        $AncestryCatID = array();
        foreach($res_ancesData as $row_ancesData) {
            $AncestryCatID[] = $row_ancesData->AncestryCatID;
        }

        $AncestryCatIDS = "'" . implode("', '", $AncestryCatID) . "'";

        $data['loadFamAnces'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_ancestrycategory WHERE AncestryCatID IN ($AncestryCatIDS)")->result_array();
        $data['loadPerFamilyAnces'] = $this->db->query("SELECT COUNT(*) AS `count` FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_ngo_com_ancestrycategory ON srp_erp_ngo_com_ancestrycategory.AncestryCatID=srp_erp_ngo_com_familymaster.AncestryCatID WHERE srp_erp_ngo_com_familymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_familymaster.FamAncestory = '1' AND srp_erp_ngo_com_familymaster.AncestryCatID IN ($AncestryCatIDS) $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS  GROUP BY srp_erp_ngo_com_familymaster.AncestryCatID")->result_array();
        //end of Family Ancestry

        //get Econ State
        $query_econData = $this->db->query("SELECT EconStateID,EconStateDes,EconStateVal FROM srp_erp_ngo_com_familyeconomicstatemaster ORDER BY EconStateID DESC");
        $res_econData = $query_econData->result();
        $EconStateID = array();
        foreach($res_econData as $row_econData) {
            $EconStateID[] = $row_econData->EconStateID;
        }

        $EconStateIDS = "'" . implode("', '", $EconStateID) . "'";

        $data['loadEconState'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_familyeconomicstatemaster WHERE EconStateID IN ($EconStateIDS) ORDER BY EconStateID DESC")->result_array();
        $data['loadPerEconState'] = $this->db->query("SELECT COUNT(*) AS `count` FROM srp_erp_ngo_com_familymaster LEFT JOIN srp_erp_ngo_com_familyeconomicstatemaster ON srp_erp_ngo_com_familymaster.ComEconSteID=srp_erp_ngo_com_familyeconomicstatemaster.EconStateID WHERE srp_erp_ngo_com_familymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_familymaster.isDeleted='0' AND srp_erp_ngo_com_familymaster.ComEconSteID IN ($EconStateIDS) GROUP BY srp_erp_ngo_com_familymaster.ComEconSteID ORDER BY srp_erp_ngo_com_familymaster.ComEconSteID DESC")->result_array();
        //end of  Econ State

        // column Chart //

        $data['loadEconSte'] = $this->db->query("SELECT COUNT(*) AS `countEconSte` FROM srp_erp_ngo_com_familymaster LEFT JOIN srp_erp_ngo_com_familyeconomicstatemaster ON srp_erp_ngo_com_familymaster.ComEconSteID=srp_erp_ngo_com_familyeconomicstatemaster.EconStateID WHERE srp_erp_ngo_com_familymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_familymaster.isDeleted='0' AND srp_erp_ngo_com_familymaster.ComEconSteID IN ($EconStateIDS) GROUP BY srp_erp_ngo_com_familymaster.ComEconSteID ORDER BY srp_erp_ngo_com_familymaster.ComEconSteID ASC")->result_array();



        // End of column Chart //

        $this->load->view('system/communityNgo/ajax/load_com_dash_jammiya', $data);

    }

    function load_houseEnrolling_del(){

        $companyID = $this->common_data['company_data']['company_id'];

        $areaMemId = $this->input->post('areaMemId');
        $gsDivitnId = $this->input->post('gsDivitnId');

        $areaMemIdS = "";
        if (isset($areaMemId) && !empty($areaMemId)) {
            $areaMemIdS = "AND cm.RegionID IN(" . join(',', $areaMemId) . ")";
        }

        $gsDivitnIdS = "";
        if (isset($gsDivitnId) && !empty($gsDivitnId)) {
            $gsDivitnIdS = "AND cm.GS_Division IN(" . join(',', $gsDivitnId) . ")";
        }

        $data['housingEnrl'] = $this->db->query("SELECT hEnr.hEnrollingID,hEnr.companyID AS companyID,fm.FamilySystemCode,fm.FamilyName,hEnr.FamHouseSt,hEnr.Link_hEnrollingID,cm.CName_with_initials,cm.C_Address,onrSp.ownershipDescription,tpMas.hTypeDescription,hEnr.hESizeInPerches,hEnr.isHmElectric,hEnr.isHmWaterSup,hEnr.isHmToilet,hEnr.isHmBathroom,hEnr.isHmTelephone,hEnr.isHmKitchen FROM srp_erp_ngo_com_house_enrolling hEnr 
LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.FamMasterID = hEnr.FamMasterID 
LEFT JOIN srp_erp_ngo_com_house_ownership_master onrSp ON onrSp.ownershipAutoID = hEnr.ownershipAutoID
LEFT JOIN srp_erp_ngo_com_house_type_master tpMas ON tpMas.hTypeAutoID = hEnr.hTypeAutoID
LEFT JOIN srp_erp_ngo_com_communitymaster cm ON cm.Com_MasterID = fm.LeaderID
WHERE hEnr.companyID = '".$companyID."' AND (hEnr.FamHouseSt = '0' OR hEnr.FamHouseSt = NULL) $areaMemIdS  " . " $gsDivitnIdS  ORDER BY hEnr.hEnrollingID")->result_array();

        $data["type"] = "html";
        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_jammiya_housingDel.php', $data, true);

        // if ($this->input->post('html')) {
        echo $html;
        //  } else {
        //  $this->load->library('pdf');
        /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        //  }
    }

    function load_houseEnrolling_del_pdf(){

        $companyID = $this->common_data['company_data']['company_id'];

        $areaMemId = $this->input->post('areaMemId');
        $gsDivitnId = $this->input->post('gsDivitnId');

        $areaMemIdS = "";
        if (isset($areaMemId) && !empty($areaMemId)) {
            $areaMemIdS = "AND cm.RegionID IN(" . join(',', $areaMemId) . ")";
        }

        $gsDivitnIdS = "";
        if (isset($gsDivitnId) && !empty($gsDivitnId)) {
            $gsDivitnIdS = "AND cm.GS_Division IN(" . join(',', $gsDivitnId) . ")";
        }

        $data['housingEnrl'] = $this->db->query("SELECT hEnr.hEnrollingID,hEnr.companyID AS companyID,fm.FamilySystemCode,fm.FamilyName,hEnr.FamHouseSt,hEnr.Link_hEnrollingID,cm.CName_with_initials,cm.C_Address,onrSp.ownershipDescription,tpMas.hTypeDescription,hEnr.hESizeInPerches,hEnr.isHmElectric,hEnr.isHmWaterSup,hEnr.isHmToilet,hEnr.isHmBathroom,hEnr.isHmTelephone,hEnr.isHmKitchen FROM srp_erp_ngo_com_house_enrolling hEnr 
LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.FamMasterID = hEnr.FamMasterID 
LEFT JOIN srp_erp_ngo_com_house_ownership_master onrSp ON onrSp.ownershipAutoID = hEnr.ownershipAutoID
LEFT JOIN srp_erp_ngo_com_house_type_master tpMas ON tpMas.hTypeAutoID = hEnr.hTypeAutoID
LEFT JOIN srp_erp_ngo_com_communitymaster cm ON cm.Com_MasterID = fm.LeaderID
WHERE hEnr.companyID = '".$companyID."' AND (hEnr.FamHouseSt = '0' OR hEnr.FamHouseSt = NULL) $areaMemIdS  " . " $gsDivitnIdS  ORDER BY hEnr.hEnrollingID")->result_array();

        $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_jammiya_housingDel', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    //area filtering
    function fetch_provinceBased_countryDropdown()
    {
        $countyID = $this->input->post('countyID');

        if ($countyID) {
            $province = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE countyID = {$countyID} AND type = 1")->result_array();

            echo '<option value="">Select a Province</option>';
            foreach ($province as $row) {

                echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
            }
        }
    }

    function fetch_provinceBased_districtDropdown()
    {
        $masterID = $this->input->post('masterID');

        $companyID = $this->common_data['company_data']['company_id'];

        $dataStGet = $this->db->query("SELECT srp_erp_statemaster.countyID,srp_erp_ngo_com_regionmaster.stateID,srp_erp_statemaster.type,srp_erp_statemaster.divisionTypeCode FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_regionmaster.stateID=srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_regionmaster.companyID = {$companyID}");
        $stateGet = $dataStGet->row();

        if (!empty($masterID)) {
            $district = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 2")->result_array();
        }

        echo '<option  value="">Select a District</option>';
        if (!empty($district)) {
            foreach ($district as $row) {

                if((!empty($stateGet) && $stateGet->type == 2) && ($stateGet->stateID == $row['stateID'])){

                    echo '<option value="' . trim($row['stateID'] ?? '') . '" selected="selected">' . trim($row['Description'] ?? '') . '</option>';
                }
                else {
                    echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
                }
            }
        }
    }

    function fetch_district_based_jammiyaDropdown()
    {
        $masterID = $this->input->post('masterID');

        $companyID = $this->common_data['company_data']['company_id'];

        $dataStGet = $this->db->query("SELECT srp_erp_statemaster.countyID,srp_erp_ngo_com_regionmaster.stateID,srp_erp_statemaster.type,srp_erp_statemaster.divisionTypeCode FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_regionmaster.stateID=srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_regionmaster.companyID = {$companyID}");
        $stateGet = $dataStGet->row();

        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 3 AND divisionTypeCode = 'JD'")->result_array();
        }

        echo '<option value="">Select a Jammiyah Division</option>';
        if (!empty($division)) {
            foreach ($division as $row) {
                if((!empty($stateGet) && $stateGet->type == 3 && $stateGet->divisionTypeCode =='JD') && ($stateGet->stateID == $row['stateID'])){
                    echo '<option value="' . trim($row['stateID'] ?? '') . '" selected="selected">' . trim($row['Description'] ?? '') . '</option>';
                }
                else {
                    echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
                }
            }
        }
    }

    function fetch_district_divisionDropdown()
    {
        $masterID = $this->input->post('masterID');

        $companyID = $this->common_data['company_data']['company_id'];

        $dataStGet = $this->db->query("SELECT srp_erp_statemaster.countyID,srp_erp_ngo_com_regionmaster.stateID,srp_erp_statemaster.type,srp_erp_statemaster.divisionTypeCode FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_regionmaster.stateID=srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_regionmaster.companyID = {$companyID}");
        $stateGet = $dataStGet->row();

        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 3 AND divisionTypeCode = 'DD'")->result_array();
        }

        echo '<option value="">Select a District Division</option>';
        if (!empty($division)) {
            foreach ($division as $row) {
                if((!empty($stateGet) && $stateGet->type == 3 && $stateGet->divisionTypeCode =='DD') && ($stateGet->stateID == $row['stateID'])){
                    echo '<option value="' . trim($row['stateID'] ?? '') . '" selected="selected">' . trim($row['Description'] ?? '') . '</option>';
                }
                else {
                    echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
                }
            }
        }

    }

    function fetch_division_based_GSDropdown()
    {
        $masterID = $this->input->post('masterID');
        if(!empty($masterID)){
            $GSDrop = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 4 AND divisionTypeCode = 'GN'")->result_array();

            $data['gsDiviDrop'] = $GSDrop;
        }
        else{
            $data['gsDiviDrop'] = '';
        }

        echo $this->load->view('system/communityNgo/ajax/com_gsDivision_dropDown', $data, true);
    }

    function fetch_distric_diviBase_Area_Dropdown1()
    {
        $masterID = $this->input->post('masterID');

        $this->load->model('CommunityJammiya_dash_modal');
        $this->CommunityJammiya_dash_modal->fetch_distric_diviBase_AreaDsh($masterID);
    }

    function fetch_distric_diviBase_Area_Dropdown(){

        $masterID = $this->input->post('masterID');

        if(!empty($masterID)){
            $result = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 4 AND divisionTypeCode = 'MH'")->result_array();

            $data['areaDrop'] = $result;
        }
        else{
            $data['areaDrop'] = '';
        }

        echo $this->load->view('system/communityNgo/ajax/communiy_area_dropDown', $data, true);
    }

}

