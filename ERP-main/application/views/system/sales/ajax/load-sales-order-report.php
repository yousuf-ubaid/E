<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$companyType = $this->session->userdata("companyType");
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('salesOrderReport', 'Sales Order', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('sales_markating_sales_order_report'); ?></strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_customer_name'); ?><!--Customer Name--></th>
                        <th><?php echo $this->lang->line('common_document_code'); ?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_reference_number'); ?><!-- Reference Number --></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!-- Status --></th>
                        <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_expire_date'); ?><!--Document Date--></th>
                        <?php if ($companyType == 1) { ?>
                            <th><?php echo $this->lang->line('sales_markating_sales_person'); ?><!--Sales Person--></th>
                        <?php } ?>
                        <th><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                        <th>
                            <?php echo $this->lang->line('sales_markating_sales_order_amount'); ?><!--Sales order amount--></th>
                        <th><?php echo $this->lang->line('sales_markating_invoice_amount'); ?><!--Invoice Amount--></th>
                        <th><?php echo $this->lang->line('sales_markating_paid_amount'); ?><!--Paid Amount--> </th>
                        <th><?php echo $this->lang->line('sales_markating_unbilled_amount'); ?><!--Unbilled Amount--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $details = array_group_by($details, 'transactionCurrency');
                        foreach ($details as $value) {
                            $salesOrder = 0;
                            $invoice = 0;
                            $receipt = 0;
                            $unbilledAmttot = 0;
                            $decimalPlace = 2;
                            foreach ($value as $val) {
                                $decimalPlace = $val["transactionCurrencyDecimalPlaces"];
                                ?>
                                <tr>
                                    <td width="200px"><?php echo $val["customerName"] ?></td>
                                    <td><a href="#" class="drill-down-cursor"
                                           onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["contractAutoID"] ?>)"><?php echo $val["contractCode"] ?></a>
                                    </td>
                                    <td width="200px"><?php echo $val["referenceNo"] ?></td>

                                    <?php if($val['dostatus'] == 1)
                                    {
                                        echo '<td><center><span class="label label-success" style="font-size: 9px;" title="" rel="tooltip" data-original-title="Fully Received">Fully Received</span></center></td>';

                                    }else if ($val['dostatus'] == 2)
                                    {      echo '<td><center><span class="label label-danger" style="font-size: 9px;" title="" rel="tooltip" data-original-title="Not Received">Not Received</span></center></td>';

                                    }else
                                    {
                                        echo '<td><center><span class="label label-warning" style="font-size: 9px;" title="" rel="tooltip" data-original-title="Partially Received">Partially Received</span></center></td>';
                                    }

                                    ?>


                                    <td>

                                        <?php  echo $val["segmentCodemaster"] ?>

                                    </td>
                                    <td><?php echo $val["documentDate"] ?></td>
                                    <td><?php echo $val["contractExpDate"] ?></td>
                                    <?php if ($companyType == 1) { ?>
                                        <td><?php echo $val["SalesPersonName"] ?></td>
                                    <?php } ?>
                                    <td><?php echo $val["transactionCurrency"] ?></td>
                                    <td style="text-align: right"><?php echo number_format($val["transactionAmount"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
                                    <?php if ($type == "html") { ?>
                                        <td style="text-align: right"><a href="#"
                                                                         onclick="drilldownSalesOrder(<?php echo $val["contractAutoID"] ?>,'<?php echo $val["contractCode"] ?>',1,'Invoice')"> <?php echo number_format($val["invoiceAmount"], $val["transactionCurrencyDecimalPlaces"]) ?> </a>
                                        </td>
                                        <td style="text-align: right"><a href="#"
                                                                         onclick="drilldownSalesOrder(<?php echo $val["contractAutoID"] ?>,'<?php echo $val["contractCode"] ?>',2,'Receipt')"> <?php echo number_format($val["receiptAmount"], $val["transactionCurrencyDecimalPlaces"]) ?> </a>
                                        </td>
                                    <?php } else { ?>
                                        <td style="text-align: right"> <?php echo number_format($val["invoiceAmount"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
                                        <td style="text-align: right"><?php echo number_format($val["receiptAmount"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
                                    <?php } ?>
                                    <td style="text-align: right;"><?php echo number_format(($val["transactionAmount"]-$val["invoiceAmount"]), $val["transactionCurrencyDecimalPlaces"]) ?></td>
                                </tr>
                                <?php
                                $salesOrder += $val["transactionAmount"];
                                $invoice += $val["invoiceAmount"];
                                $receipt += $val["receiptAmount"];
                                $unbilledAmttot += ($val["transactionAmount"]-$val["invoiceAmount"]);
                            }
                            ?>
                            <tr>
                                <?php if ($companyType == 1) { ?>
                                    <td colspan="8"><b>Total</b></td>
                                <?php } else {?>
                                    <td colspan="7"><b>Total</b></td>
                                <?php } ?>

                                <td class="text-right reporttotal"><?php echo number_format($salesOrder,$decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($invoice,$decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($receipt,$decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($unbilledAmttot,$decimalPlace) ?></td>
                            </tr>
                            <?php
                        }
                    } ?>

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