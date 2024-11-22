<!---- =============================================
-- File Name : load_sales_order_details_report.php
-- Project Name : SME ERP
-- Module Name : Report - Sales & Marketing
-- Create date : 21 - February 2020
-- Description : This file contains Sales Order Details Report.

-- REVISION HISTORY
-- =============================================-->
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
                echo export_buttons('salesOrderDetailsReport', 'Sales Order Details Report', True, true);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderDetailsReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('sales_markating_sales_order_details'); ?></strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_document_code'); ?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_reference_no'); ?><!--Reference No--></th>
                        <th><?php echo $this->lang->line('common_item') . " " . $this->lang->line('common_code'); ?><!--Item Code--></th>
                        <th><?php echo $this->lang->line('common_item_description'); ?><!--Item Description--></th>
                        <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                        <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                        <th><?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--></th>
                        <th><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                        <th><?php echo $this->lang->line('sales_markating_sales_delivered_qty'); ?><!--Delivered Qty--></th>
                        <th><?php echo $this->lang->line('common_balance_qty'); ?><!--Balance Qty--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $grandTotal = 0;
                        foreach ($details as $value) {
                            $category[$value["customerSystemCode"] . " - " . $value["customerName"]][] = $value;
                        }
                        foreach ($category as $key => $customer) {
                            echo '<tr><td colspan="11"><div class="mainCategoryHead">' . $key . '</div></td></tr>';
                            foreach ($customer as $key2=>$val) {
                            ?>
                                
                                <tr><td><a href="#" class="drill-down-cursor"
                                           onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["contractAutoID"] ?>)"><?php echo $val["contractCode"] ?></a>
                                    </td>
                                    <!-- <td><?php // echo $val['contractCode']; ?></td> -->
                                    <td><?php echo $val['contractDate']; ?></td>
                                    <td><?php echo $val['referenceNo']; ?></td>
                                    <td><?php echo $val['seconeryItemCode']; ?></td>
                                    <td><?php echo $val['itemDescription']; ?></td>
                                    <td><?php echo $val['unitOfMeasure']; ?></td>
                                    <td class="text-right"><?php echo $val['Qty']; ?></td>
                                    <td class="text-right"><?php echo number_format($val['unittransactionAmount'], $val['transactionCurrencyDecimalPlaces']); ?></td>
                                    <td class="text-right"><?php echo number_format($val['transactionAmount'], $val['transactionCurrencyDecimalPlaces']); ?></td>
                                    <td class="text-right"><?php echo number_format($val['receivedQty'],3); ?></td>
                                    <td class="text-right"><?php echo number_format($val['balanceQty'],3); ?></td>
                                    <?php if($val['status'] == 1)
                                    {
                                        echo '<td><center><span class="label label-success" style="font-size: 9px;" title="" rel="tooltip" data-original-title="Fully Received">Fully Received</span></center></td>';

                                    }else if ($val['status'] == 2)
                                    {      echo '<td><center><span class="label label-danger" style="font-size: 9px;" title="" rel="tooltip" data-original-title="Not Received">Not Received</span></center></td>';

                                    }else
                                    {
                                        echo '<td><center><span class="label label-warning" style="font-size: 9px;" title="" rel="tooltip" data-original-title="Partially Received">Partially Received</span></center></td>';
                                    }

                                    ?>
                                </tr>
                                <?php
                            }
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