<?php
$companyID = current_companyID();


$lastYear = $financeYear -1;

$lastYearCost =  $this->db->query("SELECT round(SUM(ABS(companyReportingAmount)), companyReportingCurrencyDecimalPlaces)  AS trAmount
                                 FROM srp_erp_generalledger AS genLedger
                                 JOIN srp_erp_payrollmaster AS payMaster ON payMaster.payrollMasterID = genLedger.documentMasterAutoID  AND payMaster.companyID={$companyID}
                                 WHERE genLedger.documentCode='SP' AND amount_type='cr' AND financialYearID={$lastYear} AND genLedger.companyID={$companyID}
                                 GROUP BY genLedger.documentCode")->row('trAmount');
//echo $this->db->last_query();

$lastYearCost = (empty($lastYearCost))? '0' : $lastYearCost;
$cost_arr = array();
//array_push($cost_arr, $lastYearCost);


$cost_result = $this->db->query("SELECT  companyFinancePeriodID, dateFrom, IFNULL( (ROUND(SUM(ABS(trAmount)), dPlace)) , 0) AS trAmount
                                 FROM srp_erp_companyfinanceperiod AS financePeriod
                                 JOIN srp_erp_companyfinanceyear AS financeYear ON financePeriod.companyFinanceYearID=financeYear.companyFinanceYearID
                                 AND financePeriod.companyID={$companyID}
                                 LEFT JOIN (
                                     SELECT payrollMasterID, financialPeriodID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND financialYearID={$financeYear}
                                 ) AS payrollTB ON payrollTB.financialPeriodID = financePeriod.companyFinancePeriodID
                                 LEFT JOIN (
                                     SELECT documentMasterAutoID, companyReportingAmount AS trAmount, companyReportingCurrencyDecimalPlaces AS dPlace
                                     FROM srp_erp_generalledger WHERE companyID={$companyID} AND documentCode='SP' AND amount_type='cr'
                                 ) AS genLedger ON payrollTB.payrollMasterID = genLedger.documentMasterAutoID
                                 WHERE financePeriod.companyID={$companyID} AND financePeriod.companyFinanceYearID={$financeYear}
                                 GROUP BY financePeriod.companyFinancePeriodID ORDER BY financePeriod.companyFinancePeriodID")->result_array();



if(!empty($cost_result)){
    foreach($cost_result as $cost_row){
        array_push($cost_arr, $cost_row['trAmount']);
    }
}
//echo '<pre>'; print_r($cost_arr); echo '</pre>';
//die();

?>


<div id="payrollCostView_<?php echo $userDashboardID ?>"></div>

<script>

    Highcharts.chart('payrollCostView_<?php echo $userDashboardID ?>', {
        chart: {
            height: '350'
        },
        title: false,
        subtitle: false,
        xAxis: {
            categories: [<?php echo "'" . join("','", $months) . "'" ?>]
        },
        yAxis: {
            title: {
                text:false
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        legend: {
            itemDistance: 50
        },
        series: [{
            name: 'Payroll Cost',
            data: [<?php echo join(',', $cost_arr); ?>]
        }]
    });

</script>
