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
                <strong><?php echo $this->lang->line('sales_markating_sales_analysis_details');?><!--Sales Analysis Details--></strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_document_code');?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                        <th><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_item');?><!--Item--> </th>
                        <th><?php echo $this->lang->line('sales_markating_income');?><!--Income--></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $total = 0;
                        foreach ($details as $val) {
                            /*if ($val['transactionQTY']< 0)
                            {
                                $val['transactionQTY'] = $val['transactionQTY'] * (-1);
                            }*/

                            ?>
                            <tr>
                                <td><a href="#" class="drill-down-cursor"
                                       onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["documentAutoID"] ?>)"><?php echo $val["documentSystemCode"] ?></a>
                                </td>
                                <td><?php echo $val["segmentCode"] ?></td>
                                <td><?php echo $val["documentDate"] ?></td>
                                <td><?php echo $val["item"]?></td>
                                <td class="text-right"><?php echo number_format($val['total_value'],$val['transactionCurrencyDecimalPlaces']); ?></td>

                            </tr>
                            <?php
                            $total += $val['total_value'];

                        }
                        ?>
                        <tr>
                            <td colspan="4"><b><?php echo $this->lang->line('common_total');?> <!--Total--></b></td>
                            <td class="text-right reporttotal"><?php echo number_format($total,$val['transactionCurrencyDecimalPlaces']); ?></td>
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