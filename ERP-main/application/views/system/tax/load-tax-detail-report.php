<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('tax', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('taxDetailsReport', 'Tax Details', True, True);
            } ?>
        </div>
    </div>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12 " id="taxDetailsReport">
        <div class="reportHeaderColor" style="text-align: center">
            <strong><?php echo current_companyName(); ?></strong></div>
        <div class="reportHeader reportHeaderColor" style="text-align: center">
            <strong><?php echo $this->lang->line('tax_statement'); ?> </strong></div>
        <div style="">
            <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                <thead class="report-header">
                <tr>
                    <th rowspan="2"><?php echo $this->lang->line('common_document_code'); ?></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_document_date'); ?></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_narration'); ?></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_supplier') . ' / ' . $this->lang->line('common_customer'); ?></th>
                    <th rowspan="2"><?php echo $this->lang->line('tax_tax_id_no'); ?><!--Tax ID No--></th>
                    <th rowspan="2"><?php echo $this->lang->line('sales_markating_invoice_amount'); ?> <br> <?php echo $this->lang->line('tax_gross_of_tax'); ?></th>
                    <?php
                    $taxtype1 = array_column($taxtype, "taxType");
                    $counts = array_count_values($taxtype1);
                    $salestaxcol=0;
                    $purchasetaxcol=0;
                    foreach ($counts as $key => $type) {
                        if ($key == 1) {
                            $salestaxcol=$type;
                            echo '<th colspan="'. $type .'">'.$this->lang->line('tax_sales_tax') . '</th>';
                        } else if ($key == 2) {
                            $purchasetaxcol=$type;
                            echo '<th colspan="'. $type .'">'.$this->lang->line('tax_purchase_tax') . '</th>';
                        } ?>
                        <?php
                    }
                    if (in_array(0, $taxtype1)) {
                        echo "<th>". $this->lang->line('tax_sales_tax') . "</th><th>" .  $this->lang->line('tax_purchase_tax') . "</th>";
                    }
                    ?>
                    <th rowspan="2"><?php echo $this->lang->line('tax_net_tax'); ?></th>
                    <th rowspan="2"><?php echo $this->lang->line('tax_invoice_value'); ?> <br> <?php echo $this->lang->line('tax_net_of_tax'); ?></th>
                </tr>
                <tr>
                    <?php
                    $vatDescription = '';
                    foreach ($taxtype as $type) {
                        if($type['taxType'] == 1 || $type['taxType'] == 2) {
                        ?>
                            <th><?php echo $type['taxShortCode'] ?></th>
                        <?php
                        } else {
                            $vatDescription = $type['taxShortCode'];
                        }
                    }
                    if ($vatDescription != '') {
                        echo "<th>". $vatDescription . "</th><th>" .  $vatDescription . "</th>";
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                $details = array_group_by($details, 'taxType');
                $nettaxtotal=0;
                $taxTotal=[];
                $vat_purchase_nettot = 0;
                $vat_sales_nettot = 0;
                $colspantot=0;
                foreach ($taxtype as $taxtot) {
                    $taxTotal[$taxtot['taxMasterAutoID']]=0;
                }
                foreach ($details as $value) {
                    $grossoftaxtot=0;
                    $nettaxtot=0;
                    $netoftaxtot=0;
                    $tax=[];
                    foreach ($taxtype as $type) {
                        $tax[$type['taxMasterAutoID']]=0;
                    }
                    foreach ($value as $val) {
                        $totalgrossoftax = '';
                        $currvariable = '';
                        $decimalPlace = 2;
                        if ($currency == 1) {
                            $totalgrossoftax = $val['totalgrossofTaxLocal'];
                            $currvariable = "L_";
                            $decimalPlace = $val['companyLocalCurrencyDecimalPlaces'];
                        } else {
                            $totalgrossoftax = $val['totalGrossofTaxReporting'];
                            $currvariable = "R_";
                            $decimalPlace = $val['companyReportingCurrencyDecimalPlaces'];
                        }
                        ?>
                        <tr>
                            <td><a href="#" class="drill-down-cursor"
                                   onclick="documentPageView_modal('<?php echo $val["documentCode"] ?>',<?php echo $val["documentMasterAutoID"] ?>)"><?php echo $val['documentSystemCode'] ?></a>
                            </td>
                            <td><?php echo $val['documentDate'] ?></td>
                            <td><?php echo $val['narration'] ?></td>
                            <td><?php echo $val['SupplierName'] ?></td>
                            <td><?php echo $val['partyVatIdNo'] ?></td>
                            <td style="text-align: right"><?php echo number_format($totalgrossoftax, $decimalPlace) ?></td>
                            <?php
                            $nettax = 0;
                            $vat_purchase_tot = 0;
                            $vat_sales_tot = 0;
                            $vat_purchase = 0;
                            $vat_sales = 0;
                            foreach ($taxtype as $type) {
                                $absamount = abs($val[$currvariable.$type['taxMasterAutoID']]);
                               if($type['taxType'] == 1 || $type['taxType'] == 2) {
                                    ?>
                                    <td style="text-align: right"><?php echo number_format($absamount, $decimalPlace); ?></td>
                                    <?php

                                    $tax[$type['taxMasterAutoID']]+=$absamount;
                               } else {
                                   if($val['documentCode'] == 'CINV' || $val['documentCode'] == 'RV' || $val['documentCode'] == 'SLR' ||$val['documentCode'] == 'DO') {
                                       $vat_purchase = 0;
                                       $vat_sales = number_format($absamount, $decimalPlace);
                                       $vat_sales_tot += $absamount;
                                       $vat_sales_nettot += $absamount;
                                   } else {
                                       $vat_sales = 0;
                                       $vat_purchase = number_format($absamount, $decimalPlace);
                                       $vat_purchase_tot += $absamount;
                                       $vat_purchase_nettot += $absamount;
                                   }
                               }
                                $nettax += abs($val[$currvariable.$type['taxMasterAutoID']]);
                            }
                             if ($vatDescription != '') { ?>
                                <td style="text-align: right"><?php echo number_format($vat_sales, $decimalPlace); ?></td>
                                <td style="text-align: right"><?php echo number_format($vat_purchase, $decimalPlace); ?></td>
                            <?php }
                            ?>
                            <?php
                            $netoftax = $totalgrossoftax + $nettax;
                            $nettaxtot+=$nettax;
                            $netoftaxtot+=$netoftax;
                            ?>
                            <td style="text-align: right"><?php echo number_format($nettax, $decimalPlace) ?></td>
                            <td style="text-align: right"><?php echo number_format($netoftax, $decimalPlace) ?></td>
                        </tr>
                        <?php
                        $grossoftaxtot+=$totalgrossoftax
                        ?>
                        <?php
                    }
                        ?>
                        <tr>
                            <td colspan="5"><b><?php echo $this->lang->line('common_total'); ?></b></td>
                            <td class="text-right reporttotal"><?php echo number_format($grossoftaxtot,$decimalPlace) ?></td>
                            <?php
                            foreach ($taxtype as $type) {
                                if($type['taxType'] == 1 || $type['taxType'] == 2) {
                                ?>
                                    <td class="text-right reporttotal"><?php echo number_format($tax[$type['taxMasterAutoID']],$decimalPlace) ?></td>
                                <?php
                                }
                            }
                            if ($vatDescription != '') {
                            ?>
                                <td class="text-right reporttotal"><?php echo number_format($vat_sales_tot ,$decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($vat_purchase_tot ,$decimalPlace) ?></td>
                            <?php
                            }
                            ?>
                            <td class="text-right reporttotal"><?php echo number_format($nettaxtot,$decimalPlace) ?></td>
                            <td class="text-right reporttotal"><?php echo number_format($netoftaxtot,$decimalPlace) ?></td>

                        </tr>
                    <?php
                    if($nettaxtotal>0){
                        $nettaxtotal=$nettaxtotal-$nettaxtot;
                    }else{
                        $nettaxtotal=$nettaxtot-$nettaxtotal;
                    }
                    foreach ($taxtype as $taxtot) {
                        if($taxTotal[$taxtot['taxMasterAutoID']]>0){
                            $taxTotal[$taxtot['taxMasterAutoID']]=$taxTotal[$taxtot['taxMasterAutoID']]-$tax[$taxtot['taxMasterAutoID']];
                        }else{
                            $taxTotal[$taxtot['taxMasterAutoID']]=$tax[$taxtot['taxMasterAutoID']]-$taxTotal[$taxtot['taxMasterAutoID']];
                        }
                    }

                    $colspantot=$salestaxcol+$purchasetaxcol+6;
                }
                    ?>
                <tr>
                    <td colspan="6"><b><?php echo $this->lang->line('tax_net_total'); ?></b></td>
                    <?php
                    foreach ($taxtype as $taxtot) {
                        if($taxtot['taxType'] == 1 || $taxtot['taxType'] == 2) {
                        ?>
                        <td class="text-right reporttotal"><?php echo number_format($taxTotal[$taxtot['taxMasterAutoID']],$decimalPlace) ?></td>
                        <?php
                        }
                    }
                    if ($vatDescription != '') {
                     ?>
                        <td class="text-right reporttotal"><?php echo number_format($vat_sales_nettot,$decimalPlace) ?></td>
                        <td class="text-right reporttotal"><?php echo number_format($vat_purchase_nettot,$decimalPlace) ?></td>
                    <?php
                    }
                    ?>
                    <td class="text-right reporttotal"><?php echo number_format($nettaxtotal,$decimalPlace) ?></td>
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