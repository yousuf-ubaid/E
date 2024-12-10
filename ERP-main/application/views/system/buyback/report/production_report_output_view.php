<?php
$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12">
        <?php
        if ($type == 'html') {
            echo export_buttons('productionReport', 'Production Report', True, True);
        } ?>
    </div>
</div>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12 " id="productionReport">
        <div class="reportHeaderColor" style="text-align: center">
            <strong><?php echo current_companyName(); ?></strong></div>
        <div class="reportHeader reportHeaderColor" style="text-align: center">
            <strong>Production Report</strong>  (<?php echo $year; ?>)</div>
        <div style="">
            <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                <thead class="report-header">
                <tr>
                    <th>#</th>
                    <th style="width: 20%">Product</th>
                    <th style="width: 8%">UOM</th>
                    <th id="Jan">Jan</th>
                    <th id="Feb">Feb</th>
                    <th id="Mar">Mar</th>
                    <th>Apr</th>
                    <th>May</th>
                    <th>June</th>
                    <th>July</th>
                    <th>Aug</th>
                    <th>Sep</th>
                    <th>Oct</th>
                    <th>Nov</th>
                    <th>Dec</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($details) {
                    $totalAmount = 0;
                    $totalJan = 0;
                    $totalFeb = 0;
                    $totalMar = 0;
                    $totalApr = 0;
                    $totalMay = 0;
                    $totalJune = 0;
                    $totalJuly = 0;
                    $totalAug = 0;
                    $totalSep = 0;
                    $totalOct = 0;
                    $totalNov = 0;
                    $totalDec = 0;
                $a =1;
                    foreach ($details as  $var) {
?>
                        <tr class="hoverTr">
                            <td><?php echo $a ?></td>
                            <td><?php echo $var['itemSystemCode'] ?> - <?php echo $var['itemName'] ?></td>
                            <td><?php echo $var['unitOfMeasure'] ?></td>
                            <td class="text-right"><?php if ($var['jan']){ echo $var['jan']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['feb']){ echo $var['feb']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['mar']){ echo $var['mar']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['apr']){ echo $var['apr']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['may']){ echo $var['may']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['june']){ echo $var['june']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['july']){ echo $var['july']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['aug']){ echo $var['aug']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['sep']){ echo $var['sep']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['oct']){ echo $var['oct']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['nov']){ echo $var['nov']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['dec']){ echo $var['dec']; } else{ ?> - <?php } ?></td>
                            <td class="text-right"><?php if ($var['tot']){ echo $var['tot']; } else{ ?> - <?php } ?></td>
                        </tr>
                        <?php
                        $totalAmount += $var['tot'];
                        $totalJan += $var['jan'];
                        $totalFeb += $var['feb'];
                        $totalMar += $var['mar'];
                        $totalApr += $var['apr'];
                        $totalMay += $var['may'];
                        $totalJune += $var['june'];
                        $totalJuly += $var['july'];
                        $totalAug += $var['aug'];
                        $totalSep += $var['sep'];
                        $totalOct += $var['oct'];
                        $totalNov += $var['nov'];
                        $totalDec += $var['dec'];
                    $a++;
                    } ?>
                <tr>
                        <td colspan="3"><b>Total Amount</b></td>
                <td class="reporttotal text-right"><?php echo $totalJan ?></td>
                <td class="reporttotal text-right"><?php echo $totalFeb ?></td>
                <td class="reporttotal text-right"><?php echo $totalMar ?></td>
                <td class="reporttotal text-right"><?php echo $totalApr ?></td>
                <td class="reporttotal text-right"><?php echo $totalMay ?></td>
                <td class="reporttotal text-right"><?php echo $totalJune ?></td>
                <td class="reporttotal text-right"><?php echo $totalJuly ?></td>
                <td class="reporttotal text-right"><?php echo $totalAug ?></td>
                <td class="reporttotal text-right"><?php echo $totalSep ?></td>
                <td class="reporttotal text-right"><?php echo $totalOct ?></td>
                <td class="reporttotal text-right"><?php echo $totalNov ?></td>
                <td class="reporttotal text-right"><?php echo $totalDec ?></td>
                <td class="reporttotal text-right"><?php echo $totalAmount ?></td>
           <?php
                }
                ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } else {
    ?>
    <br>
    <div class="alert" role="alert" style="background: #9ab9f1">
        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
    </div>

    <?php
} ?>
<script>
    $('#div_fuelusage_history').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>


