<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('allJobReport', 'All Jobs Report', True, false);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="allJobReport">
            <div class="reportHeaderColor" style="text-align: center;">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center;">
                <strong><?php echo $this->lang->line('manufacturing_jobs'); ?><!-- Jobs --></strong></div>
            <div style="height: 500px;">
                <table id="tbl_rpt_job" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header" style="position: sticky;">
                    <tr>
                        <th>INQUIRY CODE</th>
                        <th>INQUIRY DATE</th>
                        <th>CLIENT</th>
                        <th>SEGMENT</th>
                        <th>INQUIRY STATUS</th>
                        <th>ACTUAL SUBMISSION DATE</th>
                        <th>PLANNED DELIVERY DATE</th>
                        <th>AWARDED DATE</th>
                        <th>PO NUMBER</th>
                        <th>ESTIMATE CODE</th>
                        <th>QUOTE STATUS</th>
                        <th>JOB TYPE</th>
                        <th>JOB NUMBER</th>
                        <th>MAIN JOB STATUS</th>
                        <th>SUB JOB NO</th>
                        <th>SUB JOB STATUS</th>
                        <th>BOM COST</th>
                        <th>JOB COST</th>
                        <th>ESTIMATED REVENUE</th>
                        <th>DELIVERY NOTE NO</th>
                        <th>DELVIERY DATE</th>
                        <th>MAN INVOICE NO</th>
                        <th>CUSTOMER INVOICE NO</th>
                        <th>INVOICED REVENUE</th>
                        <th>REALIZED COST</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($details) {
                            $BOMCost = 0;
                            $amount = 0;
                            $estimateValue = 0;
                            $invoiceRevenue = 0;
                            $realizedCost = 0;
                            foreach ($details as $val) { ?>
                                <tr>
                                    <td><?php echo $val["ciCode"] ?></td>
                                    <td><?php echo $val["documentDate"] ?></td>
                                    <td style="width: 45% !important;"><?php echo $val["CustomerName"] ?></td>
                                    <td><?php echo $val["segmentCode"] ?></td>
                                    <td>
                                        <?php 
                                            if ($val["inquiryStatus"] == 1) {
                                                echo '<a href="#" class="label label-danger"> Open </a>';
                                            } else if ($val["inquiryStatus"] == 2) {
                                                echo '<a href="#" class="label label-success"> Awarded </a>';
                                            } else if ($val["inquiryStatus"] == 3) {
                                                echo '<a href="#" class="label label-warning"> Lost </a>';
                                            }
                                        ?>
                                    </td>                                
                                    <td><?php echo $val["actualSubmissionDate"] ?></td>
                                    <td><?php echo $val["plannedDeliveryDate"] ?></td>
                                    <td><?php echo $val["awardedDate"] ?></td>
                                    <td><?php echo $val["poNumber"] ?></td>
                                    <td><?php echo $val["estimateCode"] ?></td>
                                    <td>
                                        <?php 
                                            if ($val["quoteStatus"] == 0) {
                                                echo '<a onclick="" class="label label-danger"> Open</a>';
                                            } else if ($val["quoteStatus"] == 1) {
                                                echo '<a onclick="" class="label label-success">Submitted</a>';
                                            } else {
                                                echo '<a class="label label-info">Declined</a>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $val["mainCategory"] ?></td>
                                    <td><?php echo $val["mainJobCode"] ?></td>
                                    <td>
                                        <?php 
                                            if($val["mainjobstatus"] == 3) {
                                                echo '<a class="label label-success">Invoiced</a>';
                                            } else if($val["mainjobstatus"] == 2) {
                                                echo '<a class="label label-info">Delivered</a>';
                                            } else if($val["mainjobstatus"] == 1) {
                                                echo '<a class="label label-danger">Pending</a>';
                                            } else {
                                                echo '';
                                            } 
                                        ?>
                                    </td>
                                    <td><?php echo $val["subJobCode"] ?></td>
                                    <td>
                                        <?php
                                            if ($val["jobStatus"] == 'Open') {
                                                echo '<a class="label label-warning">Open</a>';
                                            } else if ($val["jobStatus"] == 'Invoiced') {
                                                echo '<a class="label label-success">Invoiced</a>';
                                            } else if ($val["jobStatus"] == 'Delivered') {
                                                echo '<a class="label label-info">Delivered</a>';
                                            } else if ($val["jobStatus"] == 'Overdue') {
                                                echo '<a class="label label-danger">Overdue</a>';
                                            } else {
                                                echo '<a class="label label-danger">Closed</a>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo number_format($val["BOMCost"], $this->common_data["company_data"]["company_default_decimal"]) ?></td>
                                    <td><?php echo number_format($val["amount"], $this->common_data["company_data"]["company_default_decimal"]) ?></td>
                                    <td><?php echo number_format($val["estimateValue"], $this->common_data["company_data"]["company_default_decimal"]) ?></td>
                                    <td><?php echo $val["deliveryNoteCode"] ?></td>
                                    <td><?php echo $val["deliveryDate"] ?></td>
                                    <td><?php echo $val["mfqInvoiceNo"] ?></td>
                                    <td><?php echo $val["customerinvoiceCode"] ?></td>
                                    <td style="text-align: right"><?php echo number_format($val['invoiceRevenue'], $this->common_data["company_data"]["company_default_decimal"]) ?></td>
                                    <td style="text-align: right"><?php echo number_format($val['realizedCost'], $this->common_data["company_data"]["company_default_decimal"]) ?></td>
                                </tr>
                                <?php
                                $BOMCost += $val['BOMCost'];
                                $amount += $val['amount'];
                                $estimateValue += $val['estimateValue'];
                                $invoiceRevenue += $val['invoiceRevenue'];
                                $realizedCost += $val['realizedCost'];
                            }
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="16"><b>Total</b></td>
                            <td class="text-right reporttotal"><?php echo number_format($BOMCost,$this->common_data["company_data"]["company_default_decimal"]) ?></td>
                            <td class="text-right reporttotal"><?php echo number_format($amount,$this->common_data["company_data"]["company_default_decimal"]) ?></td>
                            <td class="text-right reporttotal"><?php echo number_format($estimateValue,$this->common_data["company_data"]["company_default_decimal"]) ?></td>
                            <td colspan="4"><b>&nbsp;</b></td>
                            <td class="text-right reporttotal"><?php echo number_format($invoiceRevenue,$this->common_data["company_data"]["company_default_decimal"]) ?></td>
                            <td class="text-right reporttotal"><?php echo number_format($realizedCost,$this->common_data["company_data"]["company_default_decimal"]) ?></td>
                        </tr>
                    </tfoot>
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
    $('#tbl_rpt_job').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>