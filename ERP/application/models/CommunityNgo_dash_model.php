<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class CommunityNgo_dash_model extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
    }

    function communityDashSum_Count()
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

        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_house_enrolling.companyID = " . $companyID . $deleted ;

                $member_count = $this->db->query("SELECT COUNT(*) as membersCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) $areaMemIdS  " . " $gsDivitnIdS")->row_array();

                $family_count = $this->db->query("SELECT COUNT(*) as familiesCount FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID where srp_erp_ngo_com_familymaster.companyID = '{$companyID}' AND (srp_erp_ngo_com_familymaster.isDeleted IS NULL OR srp_erp_ngo_com_familymaster.isDeleted = '' OR srp_erp_ngo_com_familymaster.isDeleted = 0) $areaMemIdS  " . " $gsDivitnIdS")->row_array();

                $committee_count = $this->db->query("SELECT COUNT(*) as committeesCount FROM srp_erp_ngo_com_committeesmaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND isActive='1'")->row_array();

               $houseCountTot = $this->db->query("SELECT COUNT(*) AS totHouseCount FROM srp_erp_ngo_com_house_enrolling INNER JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID INNER JOIN srp_erp_ngo_com_communitymaster ON Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE (srp_erp_ngo_com_house_enrolling.FamHouseSt = '0' OR srp_erp_ngo_com_house_enrolling.FamHouseSt = NULL) AND $where " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();

                $data['members'] = $member_count['membersCount'];
                $data['families'] = $family_count['familiesCount'];
                $data['committees'] = $committee_count['committeesCount'];
                $data['houseCount'] = $houseCountTot['totHouseCount'];


        return $data;
    }

    public function get_family_details($FamMasterID){


        echo '<table id="t01" class="display nowrap" cellspacing="0" width="100%" border="1">
            <thead>
            <tr style="background-color: #81dce4;">
            <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">#</td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">Ledger No</td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">Reference No</td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">Family Name</td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">Leader</td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">Added Date</td>
                <td class="" style="font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;" title="Total Members">Total Members</td>
            </tr>
            </thead>
            <tbody>';
        $FamMasRrtID = "";
        if (!empty($FamMasterID)) {
            $FamMasRrtID = "AND srp_erp_ngo_com_familymaster.FamMasterID IN(" . join(',', $FamMasterID) . ")";
        }


        $convertFormat = convert_date_format_sql();


        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID . $deleted;


        $queryFam = $this->db->query("SELECT *,CName_with_initials,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS diviDescription FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE $where " . " $FamMasRrtID  ORDER BY srp_erp_ngo_com_familymaster.FamMasterID DESC ");
        $familyDsh = $queryFam->result();
         if (($FamMasterID !=NULL || !empty($FamMasterID)) && !empty($familyDsh)) {

                $f = 1;

                foreach ($familyDsh as $valMas) {

                    $queryFM4 = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE companyID='" . $valMas->companyID . "' AND FamMasterID='" . $valMas->FamMasterID . "'");
                    $rowFM4 = $queryFM4->result();
                    $femMem2 = array();
                    $totalMm=1;
                    foreach ($rowFM4 as $resFM4) {
                        $femMem2[] = $resFM4->Com_MasterID;

                        $totFmMm = $totalMm++;

                    }
                    if(empty($rowFM4)){
                        $totFamMms = '0';
                    }
                    else{
                        $totFamMms = $totFmMm;
                    }

                    echo '<tr>
               <td>' . $f . '</td>
               <td>' . $valMas->FamilySystemCode . '</td>
               <td style="padding: 2px;">' . $valMas->LedgerNo . '</td>
               <td style="padding: 2px;">' . $valMas->FamilyName . '</td>
               <td style="padding: 2px;">' . $valMas->CName_with_initials . '</td>
               <td style="padding: 2px;">' . $valMas->FamilyAddedDate . '</td>
               <td style="padding: 2px;text-align:center;">' . $totFamMms . '</td>

                </tr>';

                    $f++;
                }

     } else {
     echo'<tr><td colspan="7">No Data Found</td></tr>';

     }
        echo'</tbody></table>';
 }

    public function get_mahallaPaymentsInfo($date_from,$date_To)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];


        $query = $this->db->query("SELECT RVcode,DATE_FORMAT(RVdate,'{$convertFormat}') AS RVdates,SUM(srp_erp_customerreceiptdetail.transactionAmount) AS totAmountPaid FROM srp_erp_customerreceiptdetail  LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId=srp_erp_customerreceiptdetail.receiptVoucherAutoId INNER JOIN srp_erp_customermaster ON srp_erp_customerreceiptmaster.customerID=srp_erp_customermaster.customerAutoID WHERE srp_erp_customerreceiptdetail.companyID = '".$companyID."' AND srp_erp_customermaster.communityMemberID IS NOT NULL AND RVdate >='".$date_from."' AND RVdate <='".$date_To."' GROUP BY srp_erp_customerreceiptdetail.receiptVoucherAutoId");
        $res = $query->result();
//var_dump($date_from);
        echo '<table id="t01" class="display nowrap" cellspacing="0" width="100%" border="1">
            <thead>
            <tr style="background-color: #81dce4;font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">
             <th>#</th>
             <th>RV Code</th>
             <th>Date Paid</th>
             <th>Amount</th>
            </tr>
            </thead>
            <tbody>';

        $a=1;
        $paidTot=0;
        foreach ($res as $row) {

            echo '<tr>
               <td>' . $a . '</td>
               <td>' . $row->RVcode . '</td>
               <td style="padding: 4px;">' . $row->RVdates . '</td>
               <td style="font-size:13px; padding: 5px; text-align:right;">' . format_number($row->totAmountPaid, $this->common_data['company_data']['company_default_decimal']) . '</td>

                </tr>';
            $paidTot +=$row->totAmountPaid;
            $a++;
        }

        echo '</tbody>
                          <tfoot>
                                        <tr>
                                            <td style="text-align:right;" colspan="3"><strong>Grand Total </strong></td>
                                            <td style="text-align:right;">'. format_number($paidTot, $this->common_data['company_data']['company_default_decimal']) . '</td>

                                        </tr>
                                    </tfoot>';

        echo' </table>';

    }

    function fetch_distric_diviBase_AreaDsh($masterID){

        $famArea = $this->db->query("SELECT stateID FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 4 AND divisionTypeCode = 'MH'");
        $res = $famArea->result();
        foreach ($res as $row) {
            echo json_encode(
                array(
                    "areaMemId" => $row->stateID,

                )
            );
        }
        return $res;
    }


}


