<style>
    .dataText {
        font-size: 12px;
        font-weight: 400;
    }

    .dataContact {
        text-align: center;
        color: #00C0EF;
    }

    .dataOrganizations {
        text-align: center;
        color: #00C0EF;
    }

    .dataLeads {
        text-align: center;
        color: #00C0EF;
    }

    .dataOpportunity {
        text-align: center;
        color: #00C0EF;
    }

    .dataProject {
        text-align: center;
        color: #00C0EF;
    }

    .dataMonth {
        color: teal;
    }

    .dataHead {
        color: teal;
    }

</style>
<?php
$companyID = $this->common_data['company_data']['company_id'];
$currentuserID = current_userID();
$issuperadmin = crm_isSuperAdmin();
$masterEmployee = $this->input->post('employeeID');

$incidateYear = $this->db->query("SELECT DISTINCT YEAR(createdDateTime) AS year FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL ORDER BY YEAR(createdDateTime) DESC")->result_array();

$lineTotal = 0;
$JanTotalReport = 0;
$FebTotalReport = 0;
$MarTotalReport = 0;
$AprTotalReport = 0;
$MayTotalReport = 0;
$JunTotalReport = 0;
$JulTotalReport = 0;
$AugTotalReport = 0;
$SeptTotalReport = 0;
$OctTotalReport = 0;
$NovTotalReport = 0;
$DecTotalReport = 0;
$GrandTotalReport = 0;
?>
<table style="width: 100%" class="table table-responsive">
    <tr>
        <th class="dataHead">Year</th>
        <th class="dataHead">Jan</th>
        <th class="dataHead">Feb</th>
        <th class="dataHead">Mar</th>
        <th class="dataHead">Apr</th>
        <th class="dataHead">May</th>
        <th class="dataHead">Jun</th>
        <th class="dataHead">Jul</th>
        <th class="dataHead">Aug</th>
        <th class="dataHead">Sept</th>
        <th class="dataHead">Oct</th>
        <th class="dataHead">Nov</th>
        <th class="dataHead">Dec</th>
        <th class="dataHead">Total</th>
    </tr>
    <tbody>
    <tr>
        <?php
        if ($issuperadmin['isSuperAdmin'] == 1) {

            foreach ($incidateYear as $row) {

                $year = $row['year'];
                echo " <tr>";
                echo " <td><div class='dataMonth'>" . $row['year'] . "</div></td>";

                $janReport = $this->db->query("SELECT COUNT(opportunityID) as totJan FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-01-01' AND '$year-01-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $janReport['totJan'] . "</div></td>";

                $JanTotalReport += $janReport['totJan'];

                $febReport = $this->db->query("SELECT COUNT(opportunityID) as totFeb FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-02-01' AND '$year-02-29'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $febReport['totFeb'] . "</div></td>";

                $FebTotalReport += $febReport['totFeb'];

                $marReport = $this->db->query("SELECT COUNT(opportunityID) as totMar FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-03-01' AND '$year-03-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $marReport['totMar'] . "</div></td>";

                $MarTotalReport += $marReport['totMar'];

                $aprReport = $this->db->query("SELECT COUNT(opportunityID) as totApr FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-04-01' AND '$year-04-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $aprReport['totApr'] . "</div></td>";

                $AprTotalReport += $aprReport['totApr'];

                $mayReport = $this->db->query("SELECT COUNT(opportunityID) as totMay FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-05-01' AND '$year-05-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $mayReport['totMay'] . "</div></td>";

                $MayTotalReport += $mayReport['totMay'];

                $junReport = $this->db->query("SELECT COUNT(opportunityID) as totJun FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-06-01' AND '$year-06-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $junReport['totJun'] . "</div></td>";

                $JunTotalReport += $junReport['totJun'];

                $julReport = $this->db->query("SELECT COUNT(opportunityID) as totJul FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-07-01' AND '$year-07-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $julReport['totJul'] . "</div></td>";

                $JulTotalReport += $julReport['totJul'];

                $augReport = $this->db->query("SELECT COUNT(opportunityID) as totAug FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-08-01' AND '$year-08-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $augReport['totAug'] . "</div></td>";

                $AugTotalReport += $augReport['totAug'];

                $sepReport = $this->db->query("SELECT COUNT(opportunityID) as totSep FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-09-01' AND '$year-09-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $sepReport['totSep'] . "</div></td>";

                $SeptTotalReport += $sepReport['totSep'];

                $octReport = $this->db->query("SELECT COUNT(opportunityID) as totOct FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-10-01' AND '$year-10-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $octReport['totOct'] . "</div></td>";

                $OctTotalReport += $octReport['totOct'];

                $novReport = $this->db->query("SELECT COUNT(opportunityID) as totNov FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-11-01' AND '$year-11-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $novReport['totNov'] . "</div></td>";

                $NovTotalReport += $novReport['totNov'];

                $decReport = $this->db->query("SELECT COUNT(opportunityID) as totDec FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND leadID IS NOT NULL AND DATE(createdDateTime) BETWEEN '$year-12-01' AND '$year-12-31'")->row_array();

                echo " <td class='dataContact'><div class='dataText'>" . $decReport['totDec'] . "</div></td>";

                $DecTotalReport += $decReport['totDec'];

                $lineTotal = $janReport['totJan'] + $febReport['totFeb'] + $marReport['totMar'] + $aprReport['totApr'] + $mayReport['totMay'] + $junReport['totJun'] + $julReport['totJul'] + $augReport['totAug'] + $sepReport['totSep'] + $octReport['totOct'] + $novReport['totNov'] + $decReport['totDec'];

                echo "<td class='dataProject'><div class='dataText'>" . $lineTotal . "</div></td>";
                echo " </tr>";
                //$project_total += $project['totProjects'];
            }
        } else {

            foreach ($incidateYear as $row) {
                $year = $row['year'];

                $where_jan1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-01-01' AND '$year-01-31'";

                $where_jan2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-01-01' AND '$year-01-31'";

                $where_jan3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-01-01' AND '$year-01-31'";

                $where_jan4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-01-01' AND '$year-01-31'";

                $opportunity_jan = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_jan1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_jan2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_jan3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_jan4 GROUP BY opportunityID ")->result_array();


                $where_feb1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-02-01' AND '$year-02-29'";

                $where_feb2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-02-01' AND '$year-02-29'";

                $where_feb3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-02-01' AND '$year-02-29'";

                $where_feb4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-02-01' AND '$year-02-29'";

                $opportunity_feb = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_feb1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_feb2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_feb3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_feb4 GROUP BY opportunityID ")->result_array();

                $where_mar1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-03-01' AND '$year-03-31'";

                $where_mar2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-03-01' AND '$year-03-31'";

                $where_mar3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-03-01' AND '$year-03-31'";

                $where_mar4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-03-01' AND '$year-03-31'";

                $opportunity_mar = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_mar1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_mar2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_mar3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_mar4 GROUP BY opportunityID ")->result_array();


                $where_apr1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-04-01' AND '$year-04-31'";

                $where_apr2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-04-01' AND '$year-04-31'";

                $where_apr3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-04-01' AND '$year-04-31'";

                $where_apr4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-04-01' AND '$year-04-31'";

                $opportunity_apr = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_apr1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_apr2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_apr3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_apr4 GROUP BY opportunityID ")->result_array();


                $where_may1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-05-01' AND '$year-05-31'";

                $where_may2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-05-01' AND '$year-05-31'";

                $where_may3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-05-01' AND '$year-05-31'";

                $where_may4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-05-01' AND '$year-05-31'";

                $opportunity_may = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_may1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_may2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_may3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_may4 GROUP BY opportunityID ")->result_array();

                $where_jun1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-06-01' AND '$year-06-31'";

                $where_jun2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-06-01' AND '$year-06-31'";

                $where_jun3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-06-01' AND '$year-06-31'";

                $where_jun4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-06-01' AND '$year-06-31'";

                $opportunity_jun = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_jun1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_jun2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_jun3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_jun4 GROUP BY opportunityID ")->result_array();

                $where_jul1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-07-01' AND '$year-07-31'";

                $where_jul2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-07-01' AND '$year-07-31'";

                $where_jul3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-07-01' AND '$year-07-31'";

                $where_jul4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-07-01' AND '$year-07-31'";

                $opportunity_jul = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_jul1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_jul2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_jul3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_jul4 GROUP BY opportunityID ")->result_array();

                $where_aug1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-08-01' AND '$year-08-31'";

                $where_aug2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-08-01' AND '$year-08-31'";

                $where_aug3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-08-01' AND '$year-08-31'";

                $where_aug4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-08-01' AND '$year-08-31'";

                $opportunity_aug = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_aug1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_aug2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_aug3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_aug4 GROUP BY opportunityID ")->result_array();

                $where_sep1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-09-01' AND '$year-09-31'";

                $where_sep2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-09-01' AND '$year-09-31'";

                $where_sep3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-09-01' AND '$year-09-31'";

                $where_sep4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-09-01' AND '$year-09-31'";

                $opportunity_sep = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_sep1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_sep2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_sep3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_sep4 GROUP BY opportunityID ")->result_array();

                $where_oct1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-10-01' AND '$year-10-31'";

                $where_oct2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-10-01' AND '$year-10-31'";

                $where_oct3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-10-01' AND '$year-10-31'";

                $where_oct4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-10-01' AND '$year-10-31'";

                $opportunity_oct = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_oct1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_oct2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_oct3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_oct4 GROUP BY opportunityID ")->result_array();


                $where_nov1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-11-01' AND '$year-11-31'";

                $where_nov2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-11-01' AND '$year-11-31'";

                $where_nov3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-11-01' AND '$year-11-31'";

                $where_nov4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-11-01' AND '$year-11-31'";

                $opportunity_nov = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_nov1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_nov2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_nov3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_nov4 GROUP BY opportunityID ")->result_array();


                $where_dec1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-12-01' AND '$year-12-31'";

                $where_dec2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-12-01' AND '$year-12-31'";

                $where_dec3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-12-01' AND '$year-12-31'";

                $where_dec4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND leadID IS NOT NULL AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN '$year-12-01' AND '$year-12-31'";

                $opportunity_dec = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_dec1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_dec2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_dec3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_dec4 GROUP BY opportunityID ")->result_array();


                echo " <tr>";
                echo " <td><div class='dataMonth'>" . $row['year'] . "</div></td>";

                $janCount = sizeof($opportunity_jan);
                $JanTotalReport += $janCount;

                echo " <td class='dataContact'><div class='dataText'>" . $janCount . "</div></td>";

                $febCount = sizeof($opportunity_feb);
                $FebTotalReport += $febCount;

                echo " <td class='dataContact'><div class='dataText'>" . $febCount . "</div></td>";

                $marCount = sizeof($opportunity_mar);
                $MarTotalReport += $marCount;

                echo " <td class='dataContact'><div class='dataText'>" . $marCount . "</div></td>";

                $aprCount = sizeof($opportunity_apr);
                $AprTotalReport += $aprCount;

                echo " <td class='dataContact'><div class='dataText'>" . $aprCount . "</div></td>";

                $mayCount = sizeof($opportunity_may);
                $MayTotalReport += $mayCount;

                echo " <td class='dataContact'><div class='dataText'>" . $mayCount . "</div></td>";

                $junCount = sizeof($opportunity_jun);
                $JunTotalReport += $junCount;

                echo " <td class='dataContact'><div class='dataText'>" . $junCount . "</div></td>";

                $julCount = sizeof($opportunity_jul);
                $JulTotalReport += $julCount;

                echo " <td class='dataContact'><div class='dataText'>" . $julCount . "</div></td>";

                $augCount = sizeof($opportunity_aug);
                $AugTotalReport += $augCount;

                echo " <td class='dataContact'><div class='dataText'>" . $augCount . "</div></td>";

                $sepCount = sizeof($opportunity_sep);
                $SeptTotalReport += $sepCount;

                echo " <td class='dataContact'><div class='dataText'>" . $sepCount . "</div></td>";

                $octCount = sizeof($opportunity_oct);
                $OctTotalReport += $octCount;

                echo " <td class='dataContact'><div class='dataText'>" . $octCount . "</div></td>";

                $novCount = sizeof($opportunity_nov);
                $NovTotalReport += $novCount;

                echo " <td class='dataContact'><div class='dataText'>" . $novCount . "</div></td>";

                $decCount = sizeof($opportunity_dec);
                $DecTotalReport += $decCount;

                echo " <td class='dataContact'><div class='dataText'>" . $decCount . "</div></td>";

                $lineTotal = $janCount + $febCount + $marCount + $aprCount + $mayCount + $junCount + $julCount + $augCount + $sepCount + $octCount + $novCount + $decCount;

                echo "<td class='dataProject'><div class='dataText'>" . $lineTotal . "</div></td>";
                echo " </tr>";

            }
        }
        ?>

    </tbody>
    <tfoot style="border-top: 1px double #0044cc;border-bottom: 1px double #0044cc;">
    <tr>
        <td>
            Total
        </td>
        <td style="text-align: center">
            <?php echo $JanTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $FebTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $MarTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $AprTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $MayTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $JunTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $JulTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $AugTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $SeptTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $OctTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $NovTotalReport; ?>
        </td>
        <td style="text-align: center">
            <?php echo $DecTotalReport; ?>
        </td>
        <?php
        $GrandTotalReport = $JanTotalReport + $FebTotalReport + $MarTotalReport + $AprTotalReport + $MayTotalReport + $JunTotalReport + $JulTotalReport + $AugTotalReport + $SeptTotalReport + $OctTotalReport + $NovTotalReport + $DecTotalReport;
        ?>
        <td style="text-align: center">
            <?php echo $GrandTotalReport; ?>
        </td>
    </tr>
    </tfoot>
</table>