<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($toptensupplierdd) { ?>
    <div class="row" style="margin-top: 5px">
      <!--  <div class="col-md-12">
            <?php
/*            if ($type == 'html') {
                //echo export_buttons('salesOrderDrilldownReport', 'Sales Order Drilldown', True, True);
            } */?>
        </div>-->
    </div>
    <div class="row" style="margin-top: 5px;">
        <div class="col-md-12 " id="toptencustomersdrilldown">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Top Ten Supplier Drill Down - <?php echo $suppliername;?></strong></div>
            <div style="height: 600px">
                <table id="tbl_rpt_toptencustomers" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Document Code</th>
                        <th>Document Date</th>
                        <th>Document ID</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = 0;
                    $decimalPlaces = 0;
                    if ($toptensupplierdd) {
                        if($currenyID == 1)
                        {
                            $decimalPlaces =  $this->common_data['company_data']['company_default_decimal'];
                        }else
                        {
                            $decimalPlaces =  $this->common_data['company_data']['company_reporting_decimal'];
                        }
                        foreach ($toptensupplierdd as $val) {
                            $total += $val["transactionAmount"];

                            ?>
                            <tr>
                                <td width="200px"><a href="#" class="drill-down-cursor"
                                                     onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["documentmasterID"] ?>)"><?php echo $val["documentcode"] ?></a></td>
                                <td><?php echo $val["DocumentDate"] ?></td>
                                <td><?php echo $val["documentID"] ?></td>
                                <td style="text-align: right"><?php echo number_format($val["transactionAmount"],$decimalPlaces) ?></td>
                            </tr>
                            <?php
                        }
                    } ?>
                    <tr>
                        <td colspan="3"><b><?php echo $this->lang->line('common_total'); ?></b></td>
                        <td class="text-right reporttotal"><?php echo number_format($total,$decimalPlaces); ?></td>
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
    $('#tbl_rpt_toptencustomers').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>