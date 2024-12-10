<?php
$companyId = current_companyID();
$tablevalue = '';

$months = $this->db->query("SELECT
  companyFinanceYearID, dateFrom, dateTo, companyID
FROM
    srp_erp_companyfinanceperiod
    where companyID = " . $companyId . " AND  companyFinanceYearID = " . $financeyear . "")->result_array();

if (!empty($months)) {
    $monthdes = '';
    foreach ($months as $ch) {
        $dateFrom = explode("-", $ch['dateFrom']);
        $newmonth = $dateFrom[1] . '/' . $dateFrom[0];
        if ($dateFrom[1] == '01') {
            $monthdes = 'January';
        } else if ($dateFrom[1] == '02') {
            $monthdes = 'February';
        } else if ($dateFrom[1] == '03') {
            $monthdes = 'March';
        } else if ($dateFrom[1] == '04') {
            $monthdes = 'April';
        } else if ($dateFrom[1] == '05') {
            $monthdes = 'May';
        } else if ($dateFrom[1] == '06') {
            $monthdes = 'June';
        } else if ($dateFrom[1] == '07') {
            $monthdes = 'July';
        } else if ($dateFrom[1] == '08') {
            $monthdes = 'August';
        } else if ($dateFrom[1] == '09') {
            $monthdes = 'September';
        } else if ($dateFrom[1] == '10') {
            $monthdes = 'October';
        } else if ($dateFrom[1] == '11') {
            $monthdes = 'November';
        } else if ($dateFrom[1] == '12') {
            $monthdes = 'December';
        }

        $tablevalue .= "SUM(if(srp_erp_fa_assetdepreciationperiods.depMonthYear = '" . $newmonth . "',srp_erp_fa_assetdepreciationperiods.transactionAmount,0)) as `" . $newmonth . "`,";
    }
}
$monthdepreciation = $this->db->query("SELECT
    srp_erp_fa_depmaster.companyID,
    srp_erp_fa_depmaster.companyFinanceYearID,
    srp_erp_fa_assetdepreciationperiods.faID,
    srp_erp_fa_assetdepreciationperiods.depMonthYear,
    srp_erp_fa_assetdepreciationperiods.assetDescription,
    srp_erp_fa_assetdepreciationperiods.transactionCurrency,
    srp_erp_fa_assetdepreciationperiods.transactionAmount,
    " . $tablevalue . "
    srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces
FROM
    srp_erp_fa_depmaster
        LEFT JOIN
    srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
    where srp_erp_fa_depmaster.companyID = " . $companyId . " AND srp_erp_fa_depmaster.approvedYN = 1 AND  srp_erp_fa_depmaster.companyFinanceYearID = " . $financeyear . " group by faID")->result_array();
?>
<div class="row">
    <div class="col-md-12">
        <div class="text-center reportHeaderColor">
            <strong>Asset Monthly Depreciation Report</strong>
        </div>
        <div class="text-center reportHeaderColor">
            <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
        </div>
        <!--<div class="text-center reportHeaderColor"><strong>As of: </strong><?php /*echo $financeyear; */?></div>-->
    </div>
</div>
<table class="<?php echo table_class() ?> assetRegisterTable" id="assetRegisterTable">
    <thead>
    <tr>
        <th style="">Asset</th>
        <?php
        foreach ($months as $mon) {
            $dateFrom = explode("-", $mon['dateFrom']);
            $dateTo = explode("-", $mon['dateTo']);
            ?>
            <th>
                <?php
                if ($dateFrom[1] == '01' && $dateTo[1] == '01') {
                    echo 'January';
                } else if ($dateFrom[1] == '02' && $dateTo[1] == '02') {
                    echo 'February';
                } else if ($dateFrom[1] == '03' && $dateTo[1] == '03') {
                    echo 'March';
                } else if ($dateFrom[1] == '04' && $dateTo[1] == '04') {
                    echo 'April';
                } else if ($dateFrom[1] == '05' && $dateTo[1] == '05') {
                    echo 'May';
                } else if ($dateFrom[1] == '06' && $dateTo[1] == '06') {
                    echo 'June';
                } else if ($dateFrom[1] == '07' && $dateTo[1] == '07') {
                    echo 'July';
                } else if ($dateFrom[1] == '08' && $dateTo[1] == '08') {
                    echo 'August';
                } else if ($dateFrom[1] == '09' && $dateTo[1] == '09') {
                    echo 'September';
                } else if ($dateFrom[1] == '10' && $dateTo[1] == '10') {
                    echo 'October';
                } else if ($dateFrom[1] == '11' && $dateTo[1] == '11') {
                    echo 'November';
                } else if ($dateFrom[1] == '12' && $dateTo[1] == '12') {
                    echo 'December';
                }
                ?>
            </th>
            <?php
        }
        ?>
        <th style="background-color: rgba(119, 119, 119, 0.33)">Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sum = 0;
    $sum_jan = 0;
    $sum_feb = 0;
    $sum_mar = 0;
    $sum_apr = 0;
    $sum_may = 0;
    $sum_jun = 0;
    $sum_jul = 0;
    $sum_aug = 0;
    $sum_sep = 0;
    $sum_oct = 0;
    $sum_nov = 0;
    $sum_dec = 0;
    $maintotal = array();
		$grandTotal = array();
        if (!empty($monthdepreciation)) {
            foreach ($monthdepreciation as $val) {
                $depMonthYear = explode("/", $val['depMonthYear']);
				$lineTot = 0;
			
                ?>
                <tr>
                    <td><?php echo $val['assetDescription']; ?></td>
					<?php foreach ($months as $mon) {
					$dateFrom = explode("-", $mon['dateFrom']);
					$newmonth = $dateFrom[1] . '/' . $dateFrom[0];
					$lineTot += $val[$newmonth];
					$maintotal[$newmonth][] = (float)$val[$newmonth];
					
					?>
                    <td class="text-right">
                        <?php
                        echo number_format($val[$newmonth], $val['transactionCurrencyDecimalPlaces'])
                        ?>
                    </td>
                  
					<?php } 
					$grandTotal['grandTotal'][] = (float)$lineTot;?>
					<td class="text-right"><?php
                        echo number_format($lineTot, $val['transactionCurrencyDecimalPlaces']);
                        ?></td>
                   
                </tr>
                <?php

            
			}
        } else { ?>
            <tr>
                <td colspan="14" style="text-align: center"> No Records Found</td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
		<tr>
		            <td>Total</td>
        <?php foreach ($months as $mon) {
			$dateFrom = explode("-", $mon['dateFrom']);
					$newmonth = $dateFrom[1] . '/' . $dateFrom[0];
			 $sum = array_sum($maintotal[$newmonth]);
		?>
	
           <td class="text-right total"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php echo number_format($sum, 2) ?></strong></td>
				
		<?php
		}?>
		 <td class="text-right total"
                style="background-color: rgba(119, 119, 119, 0.33)">
                <strong><?php 
				$sum = array_sum($grandTotal['grandTotal']);
				echo number_format($sum, 2) ?></strong></td>
		</tr>
        </tfoot>
    </tfoot>
</table>