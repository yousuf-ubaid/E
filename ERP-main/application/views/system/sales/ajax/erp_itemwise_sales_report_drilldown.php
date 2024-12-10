<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('sales_markating_item_sales_details');?><!--Item Sales Details--></strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_document_code');?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_item');?><!--Item --></th>
                        <th><?php echo $this->lang->line('common_warehouse');?><!--Warehouse--></th>
                        <th><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
                        <th><?php echo $this->lang->line('sales_markating_transaction_qty');?><!--Transaction Qty--></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $total = 0;
                        foreach ($details as $val) {
                            if ($val['transactionQTY']< 0)
                            {
                                $val['transactionQTY'] = $val['transactionQTY'] * (-1);
                            }

                                ?>
                                <tr>
                                    <?php
                                    if ($type == 'html') {
                                        ?>
                                        <td><a href="#" class="drill-down-cursor"
                                               onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["documentAutoID"] ?>)"><?php echo $val["documentSystemCode"] ?></a>
                                        </td>
                                        <?php
                                    } else{
                                        ?>
                                        <td><?php echo $val["documentSystemCode"] ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td><?php echo $val["documentDate"] ?></td>
                                    <td width=""><?php echo $val["itemSystemCode"] . ' | ' . $val['itemDescription']?></td>
                                    <td width=""><?php echo  $val['wareHouseLocation'] . ' | ' . $val["wareHouseDescription"] ?></td>
                                    <td><?php echo $val["defaultUOM"] ?></td>
                                    <td class="text-right"><?php echo $val['transactionQTY'] ?></td>

                                </tr>
                                <?php
                            $total += $val['transactionQTY'];

                            }
                            ?>
                            <tr>
                                <td colspan="5"><b><?php echo $this->lang->line('common_total');?><!--Total--></b></td>
                                <td class="text-right reporttotal"><?php echo $total ?></td>
                            </tr>
                            <?php

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


<?php
