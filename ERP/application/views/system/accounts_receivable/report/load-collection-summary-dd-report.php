<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);

if (!empty($details)) {
    ?>

    <!--<div class="row" style="margin-top: 5px">

        <div class="col-md-6">
            <?php /*if (!empty($customers)) { */?>
                <div style="font-size: 12px;"><strong>Customer Name</strong> : <?php /*echo $customers['customerName'] */?> </div>
            <?php /*}*/?>
        </div>
    </div>-->
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">

            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="">
                <span style="font-size: 12px; color: black; font-weight: normal;" class="pull-left"><strong><?php echo $this->lang->line('common_customer_name'); ?></strong> : <?php echo $customers['customerName'] ?></span> <span style="    padding-left: 22%;"><strong><?php echo $this->lang->line('accounts_receivable_rs_cad_revenue_collection_summary_drill_down') ?></strong></span></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>RV Code</th>
                        <th><?php echo $this->lang->line('common_document_date'); ?></th>
                        <th><?php echo $this->lang->line('common_narration'); ?></th>
                        <th><?php echo $this->lang->line('common_segment'); ?></th>
                        <th><?php echo $this->lang->line('common_bank'); ?></th>
                        <th><?php echo $this->lang->line('common_account'); ?></th>
                        <th><?php echo $this->lang->line('common_currency'); ?></th>
                        <th><?php echo $this->lang->line('common_amount'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $total=0;
                        $decimalPlace=2;
                        $currencyname='';
                        foreach($details as $val){
                            if($currency==1){
                                $decimalPlace = $val["transactionCurrencyDecimalPlaces"];
                                $currencyname = $val["transactionCurrency"];
                            }else if($currency==2){
                                $decimalPlace = $val["companyLocalCurrencyDecimalPlaces"];
                                $currencyname = $val["companyLocalCurrency"];
                            }else{
                                $decimalPlace = $val["companyReportingCurrencyDecimalPlaces"];
                                $currencyname = $val["companyReportingCurrency"];
                            }
                        ?>
                        <tr>
                            <?php
                            if ($type == 'html') {
                                ?>
                                <td><a href="#" class="drill-down-cursor"
                                       onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["receiptVoucherAutoId"] ?>)"><?php echo $val["RVcode"] ?></a>
                                </td>
                                <?php
                            } else{
                                ?>
                                <td><?php echo $val["RVcode"] ?></td>
                                <?php
                            }
                            ?>
                            <td><?php echo $val['RVdate'] ?></td>
                            <td><?php echo $val['RVNarration'] ?></td>
                            <td><?php echo $val['segmentCode'] ?></td>
                            <td><?php echo $val['bankName'] ?></td>
                            <td><?php echo $val['bankAccountNumber'] ?></td>
                            <td><?php echo $currencyname ?></td>
                            <td style="text-align: right"><?php echo number_format($val['transactionAmount'], $decimalPlace); ?></td>
                        </tr>
                    <?php
                            $total += $val['transactionAmount'];
                        }
                    } ?>
                    <tr>
                        <td colspan="7" style="text-align: right"><b><?php echo $this->lang->line('common_total'); ?></b></td>
                        <td class="text-right reporttotal "><?php echo number_format($total, $decimalPlace) ; ?></td>
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