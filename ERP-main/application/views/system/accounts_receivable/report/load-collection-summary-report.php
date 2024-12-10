<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('salesOrderReport', 'Collection Summary', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Collection Summary</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_customer_name')?><!--Customer Name--></th>
                        <th><?php echo $this->lang->line('common_previous_year')?><!--Previous Year--></th>
                        <?php
                            foreach($header as $headval){
                                ?>
                                <th><?php echo $headval; ?></th>
                            <?php
                            }
                        ?>
                        <th><?php echo $this->lang->line('common_total')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $coltot = [];
                    $netto = 0;
                    $exchangerate = 0;
                    $decimalPlace = 2;
                    $previoustot=0;
                    foreach($details as $dtl){
                        if($currency==1){
                            $decimalPlace = $dtl["transactionCurrencyDecimalPlaces"];
                            $exchangerate=$dtl["transactionExchangeRate"];
                        }elseif($currency==2){
                            $decimalPlace = $dtl["companyLocalCurrencyDecimalPlaces"];
                            $exchangerate=$dtl["companyLocalExchangeRate"];
                        }else{
                            $decimalPlace = $dtl["companyReportingCurrencyDecimalPlaces"];
                            $exchangerate=$dtl["companyReportingExchangeRate"];
                        }
                        ?>
                        <tr>
                            <td><?php echo $dtl['customermastername']; ?></td>
                            <?php
                            if($dtl['previoustransactionAmount']>0){
                            ?>
                            <td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="opencollectionsummaryPriviousDD('<?php echo $previousbeginingdate ?>','<?php echo $previousenddate ?>',<?php echo $currency ?>,<?php echo $dtl['segmentID'] ?>,<?php echo $dtl['customerID'] ?>)"><?php echo number_format($dtl['previoustransactionAmount'], $decimalPlace); ?></a></td>
                            <?php
                            }else{
                                ?>
                                <td style="text-align: right"><?php echo number_format($dtl['previoustransactionAmount'], $decimalPlace); ?></td>
                                <?php
                            }
                            ?>
                        <?php
                        $total=0;
                        foreach($header as $key => $headval){
                            $coltot[$key][] = $dtl[$headval];
                            //echo $exchangerate;
                            //echo '<pre>';print_r($exchangerate); echo '</pre>';
                            if($dtl[$headval]>0){
                                ?>
                                <td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="opencollectionsummaryDD('<?php echo $key ?>',<?php echo $currency ?>,<?php echo $dtl['segmentID'] ?>,<?php echo $dtl['customerID'] ?>)"><?php  echo number_format($dtl[$headval], $decimalPlace) ; ?></a></td>
                                <?php
                            }else{
                                ?>
                                <td style="text-align: right"><?php  echo number_format($dtl[$headval], $decimalPlace) ; ?></td>
                                <?php
                            }
                            ?>

                            <?php
                            $total += $dtl[$headval];
                        }
                        $netto+=$total;
                        ?>
                            <td style="text-align: right"><?php echo number_format($total, $decimalPlace) ; ?></td>
                        </tr>

                    <?php
                        $previoustot+=$dtl['previoustransactionAmount'];
                    }

                    //echo '<pre>';print_r($coltot); echo '</pre>'; die();
                    ?>

                    <tr>
                        <td><b><?php echo $this->lang->line('common_total')?></b></td>
                        <td class="text-right reporttotal "><?php echo number_format($previoustot, $decimalPlace) ; ?></td>
                        <?php
                        foreach($header as $key => $headval){
                            $tot=array_sum($coltot[$key]);
                            ?>
                            <td class="text-right reporttotal "><?php echo number_format($tot, $decimalPlace) ; ?></td>
                            <?php
                        }
                        ?>
                        <td class="text-right reporttotal "><?php echo number_format($netto, $decimalPlace) ; ?></td>
                    </tr>


                    </tbody>

                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>