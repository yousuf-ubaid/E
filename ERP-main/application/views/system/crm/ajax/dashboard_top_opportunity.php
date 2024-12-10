<?php

$companyId = $this->common_data['company_data']['company_id'];
$currentuserID = current_userID();
$issuperadmin = crm_isSuperAdmin();
$year = trim($this->input->post('year'));
$masterEmployee = $this->input->post('employeeID');
$permission = $this->input->post('permission');
$isGroupAdmin = crm_isGroupAdmin();
$employeeID = ' ';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $employeeID = join(",", $masterEmployee);
}
$filteropportuniID = '';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $filteropportuniID = " AND srp_erp_crm_opportunity.responsibleEmpID IN ($employeeID)";
}
if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
    if (isset($masterEmployee) && empty($masterEmployee)) {
        $data = $this->db->query("SELECT opportunityName, transactionAmount From srp_erp_crm_opportunity where companyID = '{$companyId}' AND YEAR(createdDateTime) = '{$year}' AND closeStatus != 2 ORDER BY companyReportingAmount DESC LIMIT 5")->result_array();
    } else {
        $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $data = $this->db->query("SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 UNION SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 UNION SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 UNION SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 order by transactionAmount DESC LIMIT 5")->result_array();
    }
} else {

    if (isset($employeeID) && !empty($employeeID)) {

        $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyId}' $filteropportuniID AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($currentuserID) AND srp_erp_crm_opportunity.companyID = '{$companyId}' $filteropportuniID AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($currentuserID) AND srp_erp_crm_opportunity.companyID = '{$companyId}' $filteropportuniID AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($currentuserID) AND srp_erp_crm_opportunity.companyID = '{$companyId}' $filteropportuniID AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";
    } else if($permission == 1) {

        $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";

        $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}'";
    }else if($permission == 2)
    {
        $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}' AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . " ";

        $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}' AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";

        $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}' AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";

        $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyId}' AND YEAR(srp_erp_crm_opportunity.createdDateTime) = '{$year}' AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";
    }
    $data = $this->db->query("SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 UNION SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 UNION SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 UNION SELECT opportunityName, transactionAmount FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 order by transactionAmount DESC LIMIT 5")->result_array();

}

?>

<div style="height: 300px" id="topOpportunity"></div>

<script>

    $(document).ready(function () {

        Highcharts.chart('topOpportunity', {
            chart: {
                type: 'column',
                inverted: true
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Total Value'
                }

            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:,.1f}'
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:,.1f}</b> of total<br/>'
            },
            series: [{
                color: '#D4E6F1',
                name: 'Leads',
                colorByPoint: false,
                data: [
                    <?php
foreach($data as $value)
{
$transaction = 0;
if(!empty($value['transactionAmount'])){
$transaction = $value['transactionAmount'];
}
echo "{";
echo "name:'".$value['opportunityName']."',";
echo "y:".$transaction.",";
echo "drilldown:'".$value['opportunityName']."'";
echo "}";
echo ",";
}
?>]
            }]
        });

    })
</script>