<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin:1px;margin-top: 5px">
        <div class="col-md-12 " id="tbl_rpt_invoice_overdue">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Invoice Overdue Drill-down Report</strong></div>
            <div style="">
                <div>
                    <table id="tbl_rpt_invoice_overdue" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th style="width: 2%;">#</th>
                            <th style="width: 10%;">Document Code</th>
                            <th style="width: 5%;">Document Date</th>
                            <th style="width: 5%;">Currency</th>
                            <th style="width: 6%;"><?php echo $this->lang->line('common_amount'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $a = 1;
                        if($details){
                            $totalAmount = 0;
                            $decimal = 2;
                            foreach ($details as $val){?>
                                <tr>
                                    <td style=""><?php echo $a ?></td>
                                    <td><a href="#" class="drill-down-cursor"
                                           onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["documentAutoID"] ?>)"><?php echo $val['documentCode'] ?></a>
                                    </td>
                                    <td><?php echo $val['documentDate'] ?></td>
                                    <td><?php echo $val[$currency . 'currency'] ?></td>
                                    <td class="text-right"><?php echo number_format($val[$currency], $val[$currency . 'decimal']) ?></td>
                                </tr>
                                <?php
                                $totalAmount += $val[$currency];
                                $decimal = $val[$currency . 'decimal'];
                            }
                            $a++;
                        } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td colspan="3">Total</td>
                                <td class="text-right reporttotal"><?php echo number_format($totalAmount, $decimal) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <br>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row" style="margin: 5px">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_invoice_overdue').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>