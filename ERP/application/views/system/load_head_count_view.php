<?php
$comp_id = current_companyID();

$companyFinanceYearID = $financeyearid;

$period = company_finance_year($companyFinanceYearID);
$curr_period = getPeriods($period['startdate'], $period['endingdate']);

$yearStart = $period['startdate'];
$cf = $this->db->query("SELECT COUNT(EDOJ) AS tot FROM srp_employeesdetails WHERE Erp_companyID={$comp_id} AND 
                    EDOJ < '{$yearStart}' AND (dischargedDate IS NULL OR dischargedDate > '{$yearStart}') AND isSystemAdmin=0")->row('tot');

foreach ($curr_period as $values) {

    $dateFrom = $values['dateFrom'];
    $dateTo = $values['dateTo'];
    $joined[] = $this->db->query("SELECT COUNT(EDOJ) AS joined FROM srp_employeesdetails WHERE Erp_companyID={$comp_id} 
                                  AND EDOJ BETWEEN '{$dateFrom}' AND '{$dateTo}' and isSystemAdmin=0")->row('joined');

    $discharged[] = $this->db->query("SELECT COUNT(dischargedDate) AS disch FROM srp_employeesdetails 
                                      WHERE Erp_companyID={$comp_id} AND dischargedDate BETWEEN '{$dateFrom}' AND '{$dateTo}' 
                                      AND isDischarged=1 and isSystemAdmin=0")->row('disch');

    $total[] = $this->db->query("SELECT COUNT(EDOJ) AS tot FROM srp_employeesdetails WHERE Erp_companyID = {$comp_id} AND EDOJ <= '{$dateTo}' 
                                 AND (dischargedDate IS NULL OR dischargedDate > '{$dateTo}') 
                                 AND isSystemAdmin=0")->row('tot');

}

?>

<div id="headcountview_<?php echo $userDashboardID ?>"></div>

<script>
    Highcharts.chart('headcountview_<?php echo $userDashboardID ?>', {
        chart: {
            zoomType: 'xy',
            height: '350'
        },
        title: false,
        subtitle: false,
        xAxis: [{
            categories: ['CF','Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: false,
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: false,
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: false,
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: false,
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            itemDistance: 50
        },
        series: [{
            name: 'Joined',
            type: 'column',
            data: [0,<?php echo join(',', $joined) ?>]
        }, {
            name: 'Discharges',
            type: 'column',
            data: [0,<?php echo join(',', $discharged) ?>]
        }, {
            name: 'Total',
            type: 'spline',
            yAxis: 1,
            data: [<?php echo $cf ?>,<?php echo join(',', $total) ?>]
        }]
    });
</script>
