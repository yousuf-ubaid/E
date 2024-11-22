<?php
$salaryProportionFormulaDays = getPolicyValues('SPF', 'All');
$salaryProportionDays = (empty($salaryProportionFormulaDays))? 365 : $salaryProportionFormulaDays;
$totalWorkedDays = getPolicyValues('SCD', 'All');

$lastWorkingDate_first = date('Y-m-01', strtotime($lastWorkingDate));
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .total-sd {
        border-top: 1px double #151313 !important;
        border-bottom: 3px double #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }
</style>
<div style="height: 250px" id="salary-drill-down-container">
<table class="<?php echo table_class() ?>" id="salary-drill-down-tb">
    <thead>
    <tr>
        <th>Period</th>
        <th>Type</th>
        <th>Description</th>
        <th>Declaration Amount</th>
        <th>No of Days</th>
        <th>Amount</th>
        <th>
            <input type="checkbox" id="chk-all" value="" checked onclick="check_all_button_status_change()" />
        </th>
    </tr>
    </thead>

    <tbody>
    <?php
    $totAmount = 0; $row_count = 0;
    if(!empty($unProcessPayrollMonths)) {
        foreach ($unProcessPayrollMonths as $period => $items) {
            $numberOfDays = ($lastWorkingDate_first == $period) ? date('d', strtotime($lastWorkingDate)) : date('t', strtotime($period));
            $period_tot = 0;
            $row_count++;

            foreach ($items as $key => $row) {
                $firstColumn = ($key == 0) ? date('Y F', strtotime($period)) : '';
                $type = ''; $nameFiled = ''; $numberOfDays_str = ''; $extra = ''; $decAmount = 0;
                $fromType = $row['fromType']; $detID = $row['detID']; $amount = round($row['amount'], $dPlace);

                switch ($fromType){
                    case 'FA'; $type = 'Fixed Payment'; $nameFiled = 'salary[]';  $numberOfDays_str = $numberOfDays; $decAmount = number_format($row['fullAmount'], $dPlace); break;
                    case 'MA'; $type = 'Monthly Addition'; $nameFiled = 'MA[]'; $decAmount = ''; break;
                    case 'MD'; $type = 'Monthly Deduction'; $nameFiled = 'MD[]'; $decAmount = ''; break;
                    case 'NP'; $type = 'No Pay'; $nameFiled = 'NP[]'; $extra = '( '.$row['otCode'].' )'; $decAmount = ''; break;
                    case 'OT'; $type = 'Over Time'; $nameFiled = 'OT[]'; $extra = '( '.$row['otCode'].' )'; $decAmount = ''; break;
                }

                echo '<tr>
                     <td>' . $firstColumn . '</td>
                     <td>'.$type.'</td>
                     <td>' . $row['itemDes'] . ' '.$extra.'</td> 
                     <td align="right">' . $decAmount . '</td>
                     <td align="center">' . $numberOfDays_str . '</td>
                     <td align="right">' . number_format($amount, $dPlace) . '</td>
                     <td align="center">
                        <input type="checkbox" name="'.$nameFiled.'" class="chk-ind" checked onchange="individual_status_change()" 
                            value="' . $period . '|' . $detID . '|'.$fromType.'" data-val="' . $amount . '" />
                     </td>
                  </tr>';

                $period_tot += $amount;
                $row_count++;
            }

            echo '<tr>
                 <td colspan="5" class="total-sd" align="right">Total</td>                 
                 <td class="total-sd" align="right">' . number_format($period_tot, $dPlace) . '</td>
                 <td></td>
              </tr>';

            $totAmount += $period_tot;
        }
    }
    else{
        echo '<tr><td colspan="7" align="center">' . $this->lang->line('common_no_records_found') . '</td></tr>';
    }
    ?>
    </tbody>
</table>
</div>
<div style="margin-top: 10px;">
    <b>Total Hold Amount <span class="pull-right"><?php echo number_format($totAmount, $dPlace); ?></span></b><br>
    <b>Selected Hold Amount <span id="selected-salary-tot" class="pull-right"><?php echo number_format($totAmount, $dPlace); ?></span></b>
</div>

<script>
    var row_count =  '<?php echo $row_count; ?>';

    if(row_count < 7){
        $('#salary-drill-down-container').css({'height' : 'auto'});
    }

    if(row_count == 0){
        $('#chk-all').hide();
    }
</script>
<?php
