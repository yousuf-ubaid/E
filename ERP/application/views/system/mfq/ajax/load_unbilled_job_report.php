<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('unbilledJobReport', 'Unbilled Jobs Report', True, false);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="unbilledJobReport">
            <div class="reportHeaderColor" style="text-align: center;">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center;">
                <strong><?php echo $this->lang->line('manufacturing_jobs'); ?><!-- Jobs --></strong></div>
            <div class="table-responsive" style="height: 500px;">
                <table id="tbl_rpt_job" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header" style="position: sticky;">
                    <tr>
                        <th style="width: 15%;"><?php echo $this->lang->line('manufacturing_client'); ?><!-- Customer Name --></th>
                        <th style="width: 15%;"><?php echo $this->lang->line('manufacturing_job_number'); ?><!-- Job No --></th>
                        <th style="width: 50%;"><?php echo $this->lang->line('manufacturing_job_description'); ?><!-- Job Description --></th>
                        <th style="width: 10%;"><?php echo $this->lang->line('manufacturing_job_date'); ?><!-- Job Date --></th>
                        <th style="width: 10%;">Created Date</th>
                        <th style="width: 8%;"><?php echo $this->lang->line('manufacturing_job_status'); ?><!-- Job Status --></th>
                        <th style="width: 8%;"><?php echo $this->lang->line('manufacturing_department'); ?><!-- Segment --></th>
                        <th style="width: 8%;"><?php echo $this->lang->line('manufacturing_main_category'); ?><!-- Main Category --></th>
                        <!-- <th style="width: 15%;"><?php echo $this->lang->line('manufacturing_item'); ?></th> -->
                        <th style="width: 15%;"><?php echo $this->lang->line('manufacturing_delivery_note'); ?></th>
                        <th style="width: 8%;"><?php echo $this->lang->line('manufacturing_delivery_date'); ?></th>
                        <!-- <th style="width: 8%;"><?php echo $this->lang->line('manufacturing_quantity'); ?></th> -->
                        <th style="width: 8%;">WIP Cost</th>
                        <th style="width: 8%;">Estimated Revenue</th>
                        <th style="width: 15%;">Man. Invoice No </th>
                        <th style="width: 8%;">Man. Inv Date</th>
                        <th style="width: 15%;">Customer Inv. No</th>
                        <th style="width: 8%;">Customer Inv. Date</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($details) {
                            $amount = 0;
                            $estimateValue = 0;
                            foreach ($details as $val) { ?>
                                <tr>
                                    <td><?php echo $val["CustomerName"] ?></td>
                                    <td><a href="#"><?php echo $val["documentCode"] ?></a></td>
                                    <?php
                                    if ($type == 'html') { ?>
                                        <td><a href="#"><?php echo $val["description"]; ?></a></td>
                                    <?php 
                                    } else { ?>
                                        <td><?php echo $val["description"]; ?></td>
                                    <?php
                                    } ?>
                                    <td><?php echo $val["documentDate"] ?></td>
                                    <td><?php echo $val["createdDate"] ?></td>
                                    <td>
                                        <?php
                                            if ($val["jobStatus"] == 'Open') {
                                                echo '<a class="label label-warning">Open</a>';
                                            } else if ($val["jobStatus"] == 'Closed') {
                                                echo '<a class="label label-danger">Closed</a>';
                                            } else if ($val["jobStatus"] == 'Overdue') {
                                                echo '<a class="label label-danger">Overdue</a>';
                                            } else {
                                                echo '<a class="label label-info">Delivered</a>';
                                            }
                                        ?>    
                                    </td>
                                    <td><?php echo $val["segmentCode"]; ?></td>
                                    <td><?php echo $val["mainCategory"]; ?></td>
                                    <!-- <?php
                                    if ($type == 'html') { ?>
                                        <td><?php echo trim_value($val["itemDescription"], 20)?></td>
                                    <?php 
                                    } else { ?>
                                    <td><?php echo $val["itemDescription"]; ?></td>
                                    <?php
                                    } ?> -->
                                    <td><?php echo $val["deliveryNoteCode"]; ?></td>
                                    <td><?php echo $val["deliveryDate"]; ?></td>
                                    <!-- <td><?php echo $val['qty'] ?></td> -->
                                    <td style="text-align: right"><?php echo number_format($val['amount'], $this->common_data["company_data"]["company_default_decimal"]) ?></td>
                                    <td style="text-align: right"><?php echo number_format($val['estimateValue'], $this->common_data["company_data"]["company_default_decimal"]) ?></td>
                                    <td><?php echo $val["mfqInvoiceCode"]; ?></td>
                                    <td><?php echo $val["mfqInvoiceDate"]; ?></td>
                                    <td><?php echo $val["cusInvoiceCode"]; ?></td>
                                    <td><?php echo $val["cusInvoiceDate"]; ?></td>
                                </tr>
                                <?php
                                $amount += $val['amount'];
                                $estimateValue += $val['estimateValue'];
                            }
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10"><b>Total</b></td>
                            <td class="text-right reporttotal"><?php echo number_format($amount,$this->common_data["company_data"]["company_default_decimal"]) ?></td>
                            <td class="text-right reporttotal"><?php echo number_format($estimateValue,$this->common_data["company_data"]["company_default_decimal"]) ?></td>
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