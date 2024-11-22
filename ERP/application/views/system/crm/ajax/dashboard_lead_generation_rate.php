<?php

$companyID = $this->common_data['company_data']['company_id'];
$currentuserID = current_userID();
$issuperadmin = crm_isSuperAdmin();
$year = trim($this->input->post('year'));

$yearWiseMonth = array("'$year-01-01' AND '$year-01-31'", "'$year-02-01' AND '$year-02-31'", "'$year-03-01' AND '$year-03-31'", "'$year-04-01' AND '$year-04-31'", "'$year-05-01' AND '$year-05-31'", "'$year-06-01' AND '$year-06-31'", "'$year-07-01' AND '$year-07-31'", "'$year-08-01' AND '$year-08-31'", "'$year-09-01' AND '$year-09-31'", "'$year-10-01' AND '$year-10-31'", "'$year-11-01' AND '$year-11-31'", "'$year-12-01' AND '$year-12-31'");

$all_leads = array();
$all_opportunities = array();
$isGroupAdmin = crm_isGroupAdmin();
$masterEmployee = $this->input->post('employeeID');
$permission = $this->input->post('permission');
$where_lead1 = '';
$where_lead2 = '';
$where_lead3 = '';
$where_lead4 = '';
$where_opportunity1 = '';
$where_opportunity2 = '';
$where_opportunity3 = '';
$where_opportunity4 = '';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $employeeID = join(",", $masterEmployee);
}
$filteruserresponsibleID = '';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $filteruserresponsibleID = " AND srp_erp_crm_leadmaster.responsiblePersonEmpID IN ($employeeID)";
}
$filteropportuniID = '';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $filteropportuniID = " AND srp_erp_crm_opportunity.responsibleEmpID IN ($employeeID)";
}

if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {

    if (isset($masterEmployee) && empty($masterEmployee)) {
        //leads
        foreach ($yearWiseMonth as $key => $row) {
            $leads = $this->db->query("SELECT COUNT(leadID) as totLeads FROM srp_erp_crm_leadmaster WHERE companyID = '$companyID' AND DATE(createdDateTime) BETWEEN $row")->row_array();
            array_push($all_leads, $leads['totLeads']);
        }

        //Opportunities

        foreach ($yearWiseMonth as $key => $row) {
            $opportunities = $this->db->query("SELECT COUNT(opportunityID) as totOpportunities FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND DATE(createdDateTime) BETWEEN $row")->row_array();
            array_push($all_opportunities, $opportunities['totOpportunities']);
        }
    }else{
        foreach ($yearWiseMonth as $key => $row) {
            $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

            $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ('$employeeID') AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

            $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ('$employeeID') AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

            $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ('$employeeID') AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

            $leads = $this->db->query("SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY leadID")->result_array();

            array_push($all_leads, sizeof($leads));

        }

        foreach ($yearWiseMonth as $key => $row) {
            $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

            $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) $filteropportuniID AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

            $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) $filteropportuniID AND srp_erp_crm_opportunity.companyID = '{$companyID}'AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

            $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) $filteropportuniID AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

            $opportunities = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 GROUP BY opportunityID")->result_array();

            array_push($all_opportunities, sizeof($opportunities));
        }
    }

} else {


    //leads
    foreach ($yearWiseMonth as $key => $row) {
        if (isset($employeeID) && !empty($employeeID)) {

            $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}'  AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

            $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID)  AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

            $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID)  AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

            $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID)  AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

        } else if($permission == 1) {

            $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

            $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND (srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

            $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND (srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

            $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND (srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
        }else if($permission == 2)
        {
            $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";

            $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . "";

            $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . "";

            $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . "";
        }
        $leads = $this->db->query("SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY leadID")->result_array();

        array_push($all_leads, sizeof($leads));

    }

    //Opportunities
    foreach ($yearWiseMonth as $key => $row) {
        if (isset($employeeID) && !empty($employeeID)) {

            $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND srp_erp_crm_opportunity.createdDateTime BETWEEN $row";

            $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND srp_erp_crm_opportunity.createdDateTime BETWEEN $row";

            $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND srp_erp_crm_opportunity.createdDateTime BETWEEN $row";

            $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'   AND srp_erp_crm_opportunity.createdDateTime BETWEEN $row";

        } else if($permission == 1) {

            $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

            $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

            $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

            $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";
        }else if($permission == 2)
        {
            $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";

            $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";

            $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . " ";

            $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";
        }
        $opportunities = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 GROUP BY opportunityID")->result_array();

        array_push($all_opportunities, sizeof($opportunities));
    }

}
//echo $this->db->last_query();exit;
?>

<div style="height: 300px" id="leadGeneration"></div>

<script>

    $(document).ready(function () {

        Highcharts.chart('leadGeneration', {
            chart: {
                type: 'spline'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            yAxis: {
                title: {
                    text: 'Count'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                color: '#DADD39',
                name: 'Leads',
                data: [<?php
    foreach($all_leads as $val){
        echo  $val.',';
    }
                 ?>]
            }, {
                color: '#AF7AC5',
                name: 'Opportunities',
                data: [<?php
    foreach($all_opportunities as $val){
        echo  $val.',';
    }
                 ?>]
            }]

        });
    })
</script>

