<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class CommunityJammiya_dash_modal extends ERP_Model
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
        $countyID = $this->input->post("countyID");
        $provinceID = $this->input->post("provinceID");
        $districtID = $this->input->post("districtID");
        $districtDivisionID = $this->input->post("districtDivisionID");

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

        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_house_enrolling.companyID = " . $companyID . $deleted ;

        $member_count = $this->db->query("SELECT COUNT(*) as membersCount FROM srp_erp_ngo_com_communitymaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS")->row_array();

        $family_count = $this->db->query("SELECT COUNT(*) as familiesCount FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID where srp_erp_ngo_com_familymaster.companyID = '{$companyID}' AND (srp_erp_ngo_com_familymaster.isDeleted IS NULL OR srp_erp_ngo_com_familymaster.isDeleted = '' OR srp_erp_ngo_com_familymaster.isDeleted = 0) $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS")->row_array();

        $committee_count = $this->db->query("SELECT COUNT(*) as committeesCount FROM srp_erp_ngo_com_committeesmaster where companyID = '{$companyID}' AND (isDeleted IS NULL OR isDeleted = '' OR isDeleted = 0) AND isActive='1'")->row_array();

        $houseCountTot = $this->db->query("SELECT COUNT(*) AS totHouseCount FROM srp_erp_ngo_com_house_enrolling INNER JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID INNER JOIN srp_erp_ngo_com_communitymaster ON Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE (srp_erp_ngo_com_house_enrolling.FamHouseSt = '0' OR srp_erp_ngo_com_house_enrolling.FamHouseSt = NULL) AND $where " . " $where_clsDsh " . " $areaMemIdS  " . " $gsDivitnIdS ")->row_array();

        $data['members'] = $member_count['membersCount'];
        $data['families'] = $family_count['familiesCount'];
        $data['committees'] = $committee_count['committeesCount'];
        $data['houseCount'] = $houseCountTot['totHouseCount'];


        return $data;
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
