<?php

$companyID = $this->common_data['company_data']['company_id'];
$currentuserID = current_userID();
$issuperadmin = crm_isSuperAdmin();
$isGroupAdmin = crm_isGroupAdmin();
$year = trim($this->input->post('year'));
$masterEmployee = $this->input->post('employeeID');
$permission = $this->input->post('permission');
$where_lead1 = '';
$where_lead2 = '';
$where_lead3 = '';
$where_lead4 = '';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $employeeID = join(",", $masterEmployee);
}
$filteruserresponsibleID = '';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $filteruserresponsibleID = " AND srp_erp_crm_leadmaster.responsiblePersonEmpID IN ($employeeID)";
}
if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
    if (isset($masterEmployee) && empty($masterEmployee)) {
        $data = $this->db->query("SELECT srp_erp_crm_source.description,COUNT(srp_erp_crm_leadmaster.sourceID) as tot From srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_source ON srp_erp_crm_leadmaster.sourceID = srp_erp_crm_source.sourceID where srp_erp_crm_leadmaster.companyID = '$companyID' AND srp_erp_crm_source.documentID = 5 AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}' GROUP BY srp_erp_crm_leadmaster.sourceID")->result_array();
    } else {
        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ('$employeeID') AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ('$employeeID') AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ('$employeeID') AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $data = $this->db->query("SELECT srp_erp_crm_source.description,COUNT(srp_erp_crm_leadmaster.sourceID) as tot FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_source ON srp_erp_crm_leadmaster.sourceID = srp_erp_crm_source.sourceID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY srp_erp_crm_leadmaster.sourceID UNION SELECT srp_erp_crm_source.description,COUNT(srp_erp_crm_leadmaster.sourceID) as tot FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_source ON srp_erp_crm_leadmaster.sourceID = srp_erp_crm_source.sourceID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY srp_erp_crm_leadmaster.sourceID UNION SELECT srp_erp_crm_source.description,COUNT(srp_erp_crm_leadmaster.sourceID) as tot FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_source ON srp_erp_crm_leadmaster.sourceID = srp_erp_crm_source.sourceID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY srp_erp_crm_leadmaster.sourceID")->result_array();
    }

} else {


    if (isset($employeeID) && !empty($employeeID)) {

        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

    } else if($permission == 1) {

        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}'";
    }else if ($permission == 2)
    {
        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . "";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . "";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . "";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_source.documentID = 5 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND YEAR(srp_erp_crm_leadmaster.createdDateTime) = '{$year}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";
    }
    $data = $this->db->query("SELECT srp_erp_crm_source.description,COUNT(srp_erp_crm_leadmaster.sourceID) as tot FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_source ON srp_erp_crm_leadmaster.sourceID = srp_erp_crm_source.sourceID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY srp_erp_crm_leadmaster.sourceID UNION SELECT srp_erp_crm_source.description,COUNT(srp_erp_crm_leadmaster.sourceID) as tot FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_source ON srp_erp_crm_leadmaster.sourceID = srp_erp_crm_source.sourceID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY srp_erp_crm_leadmaster.sourceID UNION SELECT srp_erp_crm_source.description,COUNT(srp_erp_crm_leadmaster.sourceID) as tot FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_source ON srp_erp_crm_leadmaster.sourceID = srp_erp_crm_source.sourceID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY srp_erp_crm_leadmaster.sourceID")->result_array();

// echo $this->db->last_query();
}

?>

<div style="height: 300px" id="leadsbysource"></div>

<script>

    $(document).ready(function () {

        Highcharts.chart('leadsbysource', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            tooltip: {
                pointFormat: '<b>{point.y:,1f}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: '',
                colorByPoint: true,
                data: [
                    <?php
    foreach($data as $value)
    {
    echo "{";
    echo "name:'".$value['description']."',";
    echo "y:".$value['tot'].",";
    echo "}";
    echo ",";
    }
    ?>
                ]
            }]
        });

    })
</script>