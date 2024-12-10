<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            // if ($type == 'html') {
            //     echo export_buttons('salesOrderReport', 'Sales Person Summary', false, false);
            // } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="itemwiseSalesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Item wise Sales Person Performance</strong></div>
            <br>
            <div style="">
                <table id="tbl_rpt_salesreturn" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th rowspan="2"><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
                        <th rowspan="2"><?php echo $this->lang->line('erp_item_master_secondary_code'); ?></th>
                        <th colspan="3"><?php echo $this->lang->line('sales_markating_contract_salesorder'); ?><!--Contract/Sales Order--></th>
                        <th rowspan="2"><?php echo $this->lang->line('common_balance'); ?><!--Balance--></th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('common_documents_no'); ?><!--Doc Num--></th>
                        <th style="width: 9%"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                        <th><?php echo $this->lang->line('common_value'); ?><!--Value--></th>
                    </tr>

                    
                    </thead>
                    <tbody>
                    <?php
                    if ($details) { 
                        $blance_value_total = 0;
                        $currencyDecimalPlaces  = 2;
                        foreach ($details as $val) { 
                            $currencyDecimalPlaces = $val['currencyDecimalPlaces'];
                            ?>
                            <tr>
                                <td width="200px"><?php echo $val["itemSystemCode"] ?></td>
                                <td width="200px"><?php echo  $val["seconeryItemCode"] ?> </td>
                                <td><a href="#" class="drill-down-cursor"
                                               onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["masterID"] ?>)"><?php echo $val["docsyscode"] ?></a> </td>
                                <td width="200px"><?php echo $val["docDate"] ?></td>
                                <td class="text-right"><?php echo number_format($val['amount'], $val['currencyDecimalPlaces']); ?></td>
                                <td width="200px"></td>
                            </tr>
                           
                        <?php 
                             $blance_value_total += $val['amount'];
                        }

                    } ?>
                        <tr>
                            <td colspan="5"><b><?php echo $this->lang->line('common_total'); ?><!--Total--></b></td>
                            <td class="text-right reporttotal"><?php echo number_format($blance_value_total, $currencyDecimalPlaces) ?></td>
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
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_salesreturn tr').mouseover(function (e) {
        $('#tbl_rpt_salesreturn tr').removeClass('highlighted');
        $(this).addClass('highlighted');
    });

    $('#tbl_rpt_salesreturn').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>