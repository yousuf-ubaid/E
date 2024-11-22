<?php

class CommunityNgoDashboard extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('CommunityNgo_dash_model');
    }


    function communityDashSum_Count()
    {
        echo json_encode($this->CommunityNgo_dash_model->communityDashSum_Count());
    }

    function commPopulation_Count()
    {

        $companyID = $this->common_data['company_data']['company_id'];


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

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID ;

        $data['comMaster'] = $this->db->query("SELECT srp_erp_ngo_com_communitymaster.SerialNo AS SerialNos FROM srp_erp_ngo_com_communitymaster WHERE $where ")->row_array();


        $member_count = $this->db->query("SELECT COUNT(*) as membersCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0)  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();

        $male_count = $this->db->query("SELECT COUNT(*) as malesCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND GenderID='1'  $areaMemIdS  " . " $gsDivitnIdS")->row_array();

        $female_count = $this->db->query("SELECT COUNT(*) as femalesCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND GenderID='2'  $areaMemIdS  " . " $gsDivitnIdS")->row_array();


        $data['members'] = $member_count['membersCount'];
        $data['males'] = $male_count['malesCount'];
        $data['females'] = $female_count['femalesCount'];


        //get Occupation-Wise
        $data['OccupationBase'] = $this->db->query("SELECT srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_occupationtypes INNER JOIN srp_erp_ngo_com_memjobs ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."'  $areaMemIdS  " . " $gsDivitnIdS GROUP BY srp_erp_ngo_com_occupationtypes.OccTypeID ")->result_array();
        $occupation_count = $this->db->query("SELECT COUNT(*) as occupationCount FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."'")->row_array();
        $data['occupation_type1'] = $this->db->query("SELECT COUNT(*) as occType1,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='1'  $areaMemIdS  " . " $gsDivitnIdS")->row_array();
        $data['occupation_type2'] = $this->db->query("SELECT COUNT(*) as occType2,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='2'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type3'] = $this->db->query("SELECT COUNT(*) as occType3,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='3'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type4'] = $this->db->query("SELECT COUNT(*) as occType4,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='4' $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type5'] = $this->db->query("SELECT COUNT(*) as occType5,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='5' $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['occupation_type6'] = $this->db->query("SELECT COUNT(*) as occType6,srp_erp_ngo_com_occupationtypes.Description FROM  srp_erp_ngo_com_memjobs LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_memjobs.Com_MasterID =srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_memjobs.OccTypeID=srp_erp_ngo_com_occupationtypes.OccTypeID WHERE srp_erp_ngo_com_memjobs.companyID = '".$companyID."' AND srp_erp_ngo_com_memjobs.OccTypeID='6'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();


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

        $data['loadBloodCount'] = $this->db->query("SELECT COUNT(*) AS `NoOfGrpMem` FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_bloodgrouptype ON srp_erp_ngo_com_communitymaster.BloodGroupID=srp_erp_bloodgrouptype.BloodTypeID WHERE companyID = '".$companyID."' AND BloodGroupID IN ($BloodTypeID)  $areaMemIdS  " . " $gsDivitnIdS GROUP BY BloodGroupID")->result_array();

        //end of blood group counts

        //get Marital Status
        $data['maritalBase'] = $this->db->query("SELECT srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_maritalstatus INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."'  $areaMemIdS  " . " $gsDivitnIdS GROUP BY srp_erp_ngo_com_maritalstatus.maritalstatusID ORDER BY srp_erp_ngo_com_maritalstatus.maritalstatusID DESC")->result_array();
        $marital_count = $this->db->query("SELECT COUNT(*) as maritalStCount FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type1'] = $this->db->query("SELECT COUNT(*) as merrType1,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='1'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type2'] = $this->db->query("SELECT COUNT(*) as merrType2,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='2'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type3'] = $this->db->query("SELECT COUNT(*) as merrType3,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='3'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type4'] = $this->db->query("SELECT COUNT(*) as merrType4,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='4'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();
        $data['maritalSt_type5'] = $this->db->query("SELECT COUNT(*) as merrType5,srp_erp_ngo_com_maritalstatus.maritalstatus FROM  srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_communitymaster.CurrentStatus=srp_erp_ngo_com_maritalstatus.maritalstatusID WHERE srp_erp_ngo_com_communitymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_communitymaster.CurrentStatus='5'  $areaMemIdS  " . " $gsDivitnIdS ")->row_array();


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
        $data['loadPerFamilyAnces'] = $this->db->query("SELECT COUNT(*) AS `count` FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_ngo_com_ancestrycategory ON srp_erp_ngo_com_ancestrycategory.AncestryCatID=srp_erp_ngo_com_familymaster.AncestryCatID WHERE srp_erp_ngo_com_familymaster.companyID = '".$companyID."' AND srp_erp_ngo_com_familymaster.FamAncestory = '1' AND srp_erp_ngo_com_familymaster.AncestryCatID IN ($AncestryCatIDS) $areaMemIdS  " . " $gsDivitnIdS  GROUP BY srp_erp_ngo_com_familymaster.AncestryCatID")->result_array();
        //end of Family Ancestry

        $this->load->view('system/communityNgo/ajax/load_com_dash_mahalla', $data);

    }

    function get_family_details()
    {

        $FamMasterID = $this->input->post("FamMasterID");
       // $famAreaId = $this->input->post("areaMemId");

        $this->load->model('CommunityNgo_dash_model');
        $this->CommunityNgo_dash_model->get_family_details($FamMasterID);


    }

    function commPayments_summery()

    {

        $companyID = $this->common_data['company_data']['company_id'];

        $areaMemId = $this->input->post('areaMemId');
        $gsDivitnId = $this->input->post('gsDivitnId');

        if (isset($areaMemId) && !empty($areaMemId)) {
            $areaMemIdS = join(',', $areaMemId);
        }
        else{
            $areaMemIdS='';
        }
        // var_dump($areaMemIdS);

        if (isset($gsDivitnId) && !empty($gsDivitnId)) {
            $gsDivitnIdS = join(',', $gsDivitnId);
        }
        else{
            $gsDivitnIdS='';
        }

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID ;

        $data['master'] = $this->db->query("SELECT *,srp_erp_ngo_com_communitymaster.SerialNo AS SerialNos,srp_erp_ngo_com_familymaster.createdDateTime,LedgerNo,srp_erp_ngo_com_familymaster.createdUserName,srp_erp_ngo_com_familymaster.modifiedDateTime,areac.stateID,areac.Description AS arDescription,divisionc.stateID,divisionc.Description AS diviDescription,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_bloodgrouptype ON srp_erp_bloodgrouptype.BloodTypeID = srp_erp_ngo_com_communitymaster.BloodGroupID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus WHERE $where  ")->row_array();

        //get last seven details of fee
        $query_weekFeeData = $this->db->query("SELECT DISTINCT RVdate,receiptVoucherAutoId FROM srp_erp_customerreceiptmaster INNER JOIN srp_erp_customermaster ON srp_erp_customerreceiptmaster.customerID=srp_erp_customermaster.customerAutoID WHERE srp_erp_customerreceiptmaster.companyID = '".$companyID."' AND srp_erp_customermaster.communityMemberID IS NOT NULL  ORDER BY RVdate DESC LIMIT 7");
        $res_weekFeeData = $query_weekFeeData->result();
        $receiptVouchID = array();
        $RVdate = array();
        foreach($res_weekFeeData as $row_weekFeeData) {
            $receiptVouchID[] = $row_weekFeeData->receiptVoucherAutoId;
            $RVdate[] = $row_weekFeeData->RVdate;
        }

        $In_receiptVouchID = "'" . implode("', '", $receiptVouchID) . "'";
        $RVdateS = "'" . implode("', '", $RVdate) . "'";

        $data['loadWeekFee'] = $this->db->query("SELECT * FROM srp_erp_customerreceiptmaster WHERE companyID = '".$companyID."' AND RVdate IN ($RVdateS) GROUP BY RVdate")->result_array();
        $data['loadPerWeekFee'] = $this->db->query("SELECT SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS recAmountPaid FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId=srp_erp_customerreceiptmaster.receiptVoucherAutoId WHERE RVdate IN ($RVdateS) GROUP BY RVdate")->result_array();
        //end of last seven details of fee

        //get today fee collection
        $data['get_feeCatsList'] = $this->db->query("SELECT DISTINCT srp_erp_customerinvoicemaster.invoiceNarration FROM srp_erp_customerreceiptdetail 
LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerreceiptdetail.invoiceAutoID=srp_erp_customerinvoicemaster.invoiceAutoID 
LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId=srp_erp_customerreceiptmaster.receiptVoucherAutoId 
 LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
WHERE srp_erp_customerreceiptdetail.companyID = '".$companyID."' AND srp_erp_customermaster.communityMemberID IS NOT NULL AND srp_erp_customerreceiptmaster.RVdate= CURDATE() GROUP BY srp_erp_customerinvoicemaster.invoiceNarration ORDER BY srp_erp_customerinvoicemaster.invoiceNarration DESC")->result_array();

        $data['todayFeeDel'] = $this->db->query("SELECT SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS todayAmntPaid FROM srp_erp_customerreceiptdetail 
LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerreceiptdetail.invoiceAutoID=srp_erp_customerinvoicemaster.invoiceAutoID  
LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId=srp_erp_customerreceiptmaster.receiptVoucherAutoId 
 LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
WHERE srp_erp_customerreceiptdetail.companyID = '".$companyID."' AND srp_erp_customermaster.communityMemberID IS NOT NULL AND srp_erp_customerreceiptmaster.RVdate= CURDATE() GROUP BY srp_erp_customerinvoicemaster.invoiceNarration ORDER BY srp_erp_customerinvoicemaster.invoiceNarration DESC")->result_array();

        //end of today fee collection
        //get general ledger

        $data['glSumFeeDel'] = $this->db->query("SELECT DISTINCT srp_erp_customerinvoicemaster.invoiceNarration,srp_erp_customerinvoicemaster.companyID,srp_erp_customerinvoicemaster.invoiceAutoID,srp_erp_customermaster.communityMemberID FROM srp_erp_customerinvoicemaster 
 LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
WHERE srp_erp_customerinvoicemaster.companyID = '".$companyID."' AND srp_erp_customermaster.communityMemberID IS NOT NULL GROUP BY srp_erp_customerinvoicemaster.invoiceNarration ORDER BY srp_erp_customerinvoicemaster.invoiceNarration DESC")->result_array();

        //end of general ledger

        $this->load->view('system/communityNgo/ajax/load_com_dash_mahalla_fees', $data);

    }

    function get_mahalla_paymentsInfo(){

        $date_from = $_POST['date_from'];
        $date_To = $_POST['date_To'];

        $this->load->model('CommunityNgo_dash_model');
        $this->CommunityNgo_dash_model->get_mahallaPaymentsInfo($date_from,$date_To);
    }

    function commGeneral_getData()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $convertFormat = convert_date_format_sql();

        $areaMemId = $this->input->post('areaMemId');
        $gsDivitnId = $this->input->post('gsDivitnId');

        if (isset($areaMemId) && !empty($areaMemId)) {
            $areaMemIdS = join(',', $areaMemId);
        }
        else{
            $areaMemIdS='';
        }
        // var_dump($areaMemIdS);

        if (isset($gsDivitnId) && !empty($gsDivitnId)) {
            $gsDivitnIdS = join(',', $gsDivitnId);
        }
        else{
            $gsDivitnIdS='';
        }

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID ;

        $data['masterCom'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_communitymaster WHERE $where AND isDeleted='0' ")->row_array();

        //get rental
        $rentItems_count = $this->db->query("SELECT COUNT(*) as rentalitemsCount FROM srp_erp_ngo_com_rentalitems where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0)")->row_array();

        $prGoods_count = $this->db->query("SELECT COUNT(*) as prGoodsCount FROM srp_erp_ngo_com_rentalitems where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND rentalItemType='1'")->row_array();

        $asset_count = $this->db->query("SELECT COUNT(*) as assetsCount FROM srp_erp_ngo_com_rentalitems where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND rentalItemType='2'")->row_array();


        $data['rentTotals'] = $rentItems_count['rentalitemsCount'];
        $data['prGoods'] = $prGoods_count['prGoodsCount'];
        $data['assets'] = $asset_count['assetsCount'];

        //end of rental

        //get zakat progress
        $data['zakatBeneficiary'] = $this->db->query("SELECT * FROM srp_erp_ngo_projects INNER JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.projectID=srp_erp_ngo_projects.ngoProjectID WHERE srp_erp_ngo_projects.companyID = '".$companyID."' AND srp_erp_ngo_projects.templateID='beneficiary_feed_zakat_template' AND srp_erp_ngo_beneficiarymaster.Com_MasterID IS NOT NULL GROUP BY srp_erp_ngo_beneficiarymaster.projectID ORDER BY srp_erp_ngo_beneficiarymaster.projectID DESC")->result_array();

        $data['totFemAddedtoBen'] = $this->db->query("SELECT COUNT(*) as totFemAddedBen FROM srp_erp_ngo_beneficiarymaster INNER JOIN srp_erp_ngo_projects ON srp_erp_ngo_beneficiarymaster.projectID=srp_erp_ngo_projects.ngoProjectID WHERE srp_erp_ngo_beneficiarymaster.companyID = '".$companyID."' AND srp_erp_ngo_projects.templateID='beneficiary_feed_zakat_template' AND srp_erp_ngo_beneficiarymaster.Com_MasterID IS NOT NULL")->row_array();
        $data['totConfirmed'] = $this->db->query("SELECT COUNT(*) as totConfirmed,benificiaryID,projectID,srp_erp_ngo_projects.documentSystemCode,srp_erp_ngo_projects.projectName FROM srp_erp_ngo_beneficiarymaster INNER JOIN srp_erp_ngo_projects ON srp_erp_ngo_beneficiarymaster.projectID=srp_erp_ngo_projects.ngoProjectID WHERE srp_erp_ngo_beneficiarymaster.companyID = '".$companyID."' AND srp_erp_ngo_projects.templateID='beneficiary_feed_zakat_template' AND srp_erp_ngo_beneficiarymaster.confirmedYN='1' AND srp_erp_ngo_beneficiarymaster.Com_MasterID IS NOT NULL ")->row_array();
        $data['totNotConfirmed'] = $this->db->query("SELECT COUNT(*) as totNotConfirmed,benificiaryID,projectID,srp_erp_ngo_projects.documentSystemCode,srp_erp_ngo_projects.projectName FROM srp_erp_ngo_beneficiarymaster INNER JOIN srp_erp_ngo_projects ON srp_erp_ngo_beneficiarymaster.projectID=srp_erp_ngo_projects.ngoProjectID WHERE srp_erp_ngo_beneficiarymaster.companyID = '".$companyID."' AND srp_erp_ngo_projects.templateID='beneficiary_feed_zakat_template' AND srp_erp_ngo_beneficiarymaster.confirmedYN='0' AND srp_erp_ngo_beneficiarymaster.Com_MasterID IS NOT NULL")->row_array();


        $data['totZakatProposals'] = $this->db->query("SELECT COUNT(*) as totZakatProposal FROM srp_erp_ngo_projectproposals INNER JOIN srp_erp_ngo_projects ON srp_erp_ngo_projectproposals.projectID=srp_erp_ngo_projects.ngoProjectID WHERE srp_erp_ngo_projectproposals.companyID = '".$companyID."' AND srp_erp_ngo_projects.templateID='beneficiary_feed_zakat_template'")->row_array();

        $data['ZakatProposeList'] = $this->db->query("SELECT ppm.proposalID,ppm.projectID,ppm.proposalTitle,ppm.documentSystemCode,ppm.proposalName,DATE_FORMAT(ppm.DocumentDate,'{$convertFormat}') AS DocumentDate,st.description as statusName,ppm.confirmedYN,ppm.approvedYN as approvedYN,ppm.createdUserID FROM srp_erp_ngo_projectproposals ppm LEFT JOIN srp_erp_ngo_status st ON ppm.status = st.statusID LEFT JOIN srp_erp_ngo_projects ON ppm.projectID=srp_erp_ngo_projects.ngoProjectID WHERE ppm.companyID={$companyID} AND st.documentID = 6 AND srp_erp_ngo_projects.templateID='beneficiary_feed_zakat_template'  ORDER BY ppm.proposalID DESC")->result_array();

        //end of zakat progress

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

        //get renting progress
        $rentIssue_count = $this->db->query("SELECT COUNT(*) as rentalIssueCount FROM srp_erp_ngo_com_itemissuemaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0)")->row_array();

        $prReturn_count = $this->db->query("SELECT COUNT(*) as returnCount FROM srp_erp_ngo_com_itemissuemaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND isReturned='1'")->row_array();

        $ntReturn_count = $this->db->query("SELECT COUNT(*) as notReturnCount FROM srp_erp_ngo_com_itemissuemaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND isReturned='0'")->row_array();


        $data['rentingTot'] = $rentIssue_count['rentalIssueCount'];
        $data['ReturnRentTot'] = $prReturn_count['returnCount'];
        $data['notReturnRentTot'] = $ntReturn_count['notReturnCount'];

        //end of renting progress

        $this->load->view('system/communityNgo/ajax/load_com_dash_mahalla_other', $data);

    }

    function load_beneficiary_family_del()
    {

        $projectID = trim($this->input->post('projectID') ?? '');

        $confirmState = trim($this->input->post('confirmState') ?? '');
        if($confirmState ==2){
            $conform= 0;
        }
        else if($confirmState ==1){
            $conform=1;
        }
       else{
        $conform='';
        }

        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();

        $data['beneMem'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(bfm.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(bfm.modifiedDateTime,'" . $convertFormat . "') AS modifydate,DATE_FORMAT(bfm.registeredDate,'" . $convertFormat . "') AS registeredDate,DATE_FORMAT(bfm.dateOfBirth,'" . $convertFormat . "') AS dateOfBirth,bfm.createdUserName as contactCreadtedUser,bfm.email as contactEmail,bfm.phonePrimary as contactPhonePrimary,bfm.phoneSecondary as contactPhoneSecondary,project.projectName as projectName,benType.description as benTypeDescription,smpro.Description as provinceName,smdis.Description as districtName,smdiv.Description as divisionName,smsubdiv.Description as subDivisionName,bfm.projectID FROM srp_erp_ngo_beneficiarymaster bfm LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = bfm.countryID LEFT JOIN srp_erp_ngo_projects project ON project.ngoProjectID = bfm.projectID LEFT JOIN srp_erp_ngo_benificiarytypes benType ON benType.beneficiaryTypeID = bfm.benificiaryType LEFT JOIN srp_erp_statemaster smpro ON smpro.stateID = bfm.province LEFT JOIN srp_erp_statemaster smdis ON smdis.stateID = bfm.district LEFT JOIN srp_erp_statemaster smdiv ON smdiv.stateID = bfm.division LEFT JOIN srp_erp_statemaster smsubdiv ON smsubdiv.stateID = bfm.subDivision WHERE bfm.companyID = '" . $companyID . "' AND projectID = '" . $projectID . "' AND confirmedYN = '" . $conform . "'")->result_array();


        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_other_beneficiary.php', $data, true);

        // if ($this->input->post('html')) {
        echo $html;
        //  } else {
        //  $this->load->library('pdf');
        /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        //  }

    }

    function load_rental_family_del()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $rentalState = trim($this->input->post('rentalState') ?? '');
        $convertFormat = convert_date_format_sql();

        if (isset($rentalState) && !empty($rentalState)) {
            $itemTypIds = " AND srp_erp_ngo_com_rentalitems.rentalItemType = $rentalState ";
        } else {
            $itemTypIds = '';
        }
        $deleted = " AND srp_erp_ngo_com_rentalitems.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_rentalitems.companyID = " . $companyID . $deleted . $itemTypIds;

        $data['rentalMas'] = $this->db->query("SELECT srp_erp_ngo_com_rentalitems.companyID,srp_erp_ngo_com_rentalitems.createdUserID,rentalItemID,rentalItemType,srp_erp_ngo_com_rentalitems.SortOrder,rentalStatus,srp_erp_warehouseitems.itemAutoID,srp_erp_warehouseitems.itemSystemCode, srp_erp_warehouseitems.itemDescription,srp_erp_ngo_com_rentalitems.defaultUnitOfMeasure,srp_erp_ngo_com_rentalitems.currentStock,srp_erp_ngo_com_rentalitems.reorderPoint,srp_erp_ngo_com_rentalitems.maximunQty,srp_erp_ngo_com_rentalitems.minimumQty,srp_erp_ngo_com_rentalitems.RentalPrice,srp_erp_fa_asset_master.faID,srp_erp_fa_asset_master.faCode,srp_erp_fa_asset_master.assetDescription,srp_erp_fa_asset_master.depMonth,srp_erp_fa_asset_master.companyLocalAmount FROM srp_erp_ngo_com_rentalitems LEFT JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.warehouseItemsAutoID=srp_erp_ngo_com_rentalitems.warehouseItemsAutoID LEFT JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_master.faID=srp_erp_ngo_com_rentalitems.faID WHERE $where ORDER BY srp_erp_ngo_com_rentalitems.SortOrder DESC")->result_array();


        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_other_rentals.php', $data, true);

        // if ($this->input->post('html')) {
        echo $html;
        //  } else {
        //  $this->load->library('pdf');
        /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        //  }

    }

    function load_zakat_families_del()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $projectID = trim($this->input->post('projectID') ?? '');
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->db->select("ppd.proposalID,pp.isConvertedToProject as isConvertedToProject,pp.documentSystemCode,pp.proposalTitle,ppd.proposalBeneficiaryID,ppd.EconStateID,ppd.totalEstimatedValue,ppd.beneficiaryID,bm.systemCode as benCode,bm.nameWithInitials as name,ecoMas.EconStateDes,bm.FamMasterID,bm.Com_MasterID,bm.totalCost AS totalCost,ppd.isQualified AS isQualified,pp.approvedYN as approvedYN,pp.confirmedYN as confirmedYN,ppd.companyID");
        $this->db->from('srp_erp_ngo_projectproposalbeneficiaries ppd');
        $this->db->join('srp_erp_ngo_beneficiarymaster bm', 'bm.benificiaryID = ppd.beneficiaryID', 'left');
        $this->db->join('srp_erp_ngo_projectproposals pp', 'pp.proposalID = ppd.proposalID', 'left');
        $this->db->join('srp_erp_ngo_com_familyeconomicstatemaster ecoMas', 'ecoMas.EconStateID = ppd.EconStateID', 'left');
        $this->db->where('ppd.companyID', $companyID);
        $this->db->where('ppd.proposalID', $proposalID);
        $this->db->order_by('proposalBeneficiaryID', 'desc');
        $data['zakatMas'] = $this->db->get()->result_array();

        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_other_zakatDel.php', $data, true);

        // if ($this->input->post('html')) {
        echo $html;
        //  } else {
        //  $this->load->library('pdf');
        /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        //  }

    }

    function load_rentingPro_del(){

        $companyID = $this->common_data['company_data']['company_id'];

        $convertFormat = convert_date_format_sql();

        $this->db->select("srp_erp_ngo_com_itemissuemaster.itemIssueAutoID as itemIssueAutoID,itemIssueCode,narration,requestedMemberName,confirmedYN ,DATE_FORMAT(srp_erp_ngo_com_itemissuemaster.expectedReturnDate,'.$convertFormat.') AS expectedReturnDate,transactionCurrency ,srp_erp_ngo_com_itemissuemaster.transactionAmount,transactionCurrencyDecimalPlaces,srp_erp_ngo_com_itemissuemaster.transactionAmount as total_value,isDeleted,isReturned");
        $this->db->from('srp_erp_ngo_com_itemissuemaster');
        $this->db->where('srp_erp_ngo_com_itemissuemaster.companyID', $companyID);
        $this->db->where('srp_erp_ngo_com_itemissuemaster.isDeleted', 0);
        $this->db->order_by('srp_erp_ngo_com_itemissuemaster.itemIssueAutoID', 'desc');
        $data['rentingPro'] = $this->db->get()->result_array();

        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_other_rentingPro.php', $data, true);

        // if ($this->input->post('html')) {
        echo $html;
        //  } else {
        //  $this->load->library('pdf');
        /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        //  }
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
        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_other_housingDel.php', $data, true);

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
          $html = $this->load->view('system/communityNgo/ajax/load_com_dash_other_housingDel', $data, true);
          $this->load->library('pdf');
          $pdf = $this->pdf->printed($html, 'A4');
    }

    //area filtering

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


    function fetch_distric_diviBase_Area_Dropdown1()
    {
        $masterID = $this->input->post('masterID');

        $this->load->model('CommunityNgo_dash_model');
        $this->CommunityNgo_dash_model->fetch_distric_diviBase_AreaDsh($masterID);
    }


}

