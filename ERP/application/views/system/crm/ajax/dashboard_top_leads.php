<?php

$companyId = $this->common_data['company_data']['company_id'];
$companyID = $this->common_data['company_data']['company_id'];
$currentuserID = current_userID();
$issuperadmin = crm_isSuperAdmin();
$year = trim($this->input->post('year'));
$masterEmployee = $this->input->post('employeeID');
$permissiontype = $this->input->post('permissiontype');
$isGroupAdmin = crm_isGroupAdmin();
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $employeeID = join(",", $masterEmployee);
}

$filteruserresponsibleID = '';
if (isset($masterEmployee) && !empty($masterEmployee)) {
    $filteruserresponsibleID = " AND srp_erp_crm_leadmaster.responsiblePersonEmpID IN ($employeeID)";
}


if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
    if (isset($masterEmployee) && empty($masterEmployee)) {
        $sql = "SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price/companyLocalCurrencyExchangeRate) as totalvalue From srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' where srp_erp_crm_leadmaster.companyID = '$companyId' GROUP BY srp_erp_crm_leadmaster.leadID ORDER BY totalvalue desc Limit 10";
        $data = $this->db->query($sql)->result_array();
    } else {
        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyId}' $filteruserresponsibleID";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ('$employeeID') AND srp_erp_crm_leadmaster.companyID = '{$companyId}' $filteruserresponsibleID";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ('$employeeID') AND srp_erp_crm_leadmaster.companyID = '{$companyId}' $filteruserresponsibleID";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ('$employeeID') AND srp_erp_crm_leadmaster.companyID = '{$companyId}' $filteruserresponsibleID";

        $data = $this->db->query("SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price/companyLocalCurrencyExchangeRate) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY srp_erp_crm_leadmaster.leadID  UNION SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadproducts.leadID $where_lead2 GROUP BY srp_erp_crm_leadmaster.leadID  UNION SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY srp_erp_crm_leadmaster.leadID  UNION SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY srp_erp_crm_leadmaster.leadID ORDER BY totalvalue desc Limit 10 ")->result_array();
    }
} else {


    if (isset($employeeID) && !empty($employeeID)) {

        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyId}' ";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyId}'";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyId}'";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyId}'";

    } else if ($permissiontype == 1) {
        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND (srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND (srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND (srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";
    }else if ($permissiontype == 2)
    {
        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";

        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";

        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";

        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";
    }
    $data = $this->db->query("SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price/companyLocalCurrencyExchangeRate) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY srp_erp_crm_leadmaster.leadID  UNION SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadproducts.leadID $where_lead2 GROUP BY srp_erp_crm_leadmaster.leadID  UNION SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY srp_erp_crm_leadmaster.leadID  UNION SELECT CONCAT(firstName,' ',lastName) as fullname, SUM(price) as totalvalue FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_leadproducts ON srp_erp_crm_leadproducts.leadID = srp_erp_crm_leadmaster.leadID AND YEAR(srp_erp_crm_leadproducts.createdDateTime) = '{$year}' LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY srp_erp_crm_leadmaster.leadID ORDER BY totalvalue desc Limit 10 ")->result_array();
}
//var_dump($data);
?>

<div style="height: 300px" id="topLeads"></div>

<script>

    $(document).ready(function () {

        Highcharts.chart('topLeads', {
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
$total = 0;
if($value['totalvalue'] != ''){
$total = $value['totalvalue'];
}
echo "{";
echo "name:'".$value['fullname']."',";
echo "y:".$total.",";
echo "drilldown:'".$value['fullname']."'";
echo "}";
echo ",";
}
?>]
            }]
        });

    })
</script>