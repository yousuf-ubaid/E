<style>
    .tblclm {
        background-color: #80D287;
        border-bottom: #80D287;
    }

    .ex3 {
        background-color: lightblue;

        height: 750px;
        overflow: auto;

    }
</style>

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($drilldowndata) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">

        </div>
    </div>
    <br>
    <br>
    <br>
    <br>


    <div class="box box-warning ex3" style="width: 90%;margin: 0 auto;background: #f3f4f5;">
        <div class="box-header with-border">
            <div class="row" style="margin-top: 5px">
                <div class="col-md-10">
                    <h4 class="box-title">MPR - Monthly Performance Report Drill Down
                        - <?php echo $glDescMaster['GLDescription'] ?></h4>
                </div>
                <div class="col-md-2">
                    <h4 class="box-title" style="font-size: 14px;"><strong>Curreny
                            : <?php echo $this->common_data['company_data']['company_reporting_currency']; ?></strong>
                    </h4>
                </div>
            </div>


        </div>
        <div class="row" style="width: 95%; !important;margin: 0 auto; border: 0px solid">
            <div class="col-md-12 " id="salesOrderReport">
                <div style="">
                    <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th class="">Doc Code</th>
                            <th>Doc ID</th>
                            <th>Segment</th>
                            <th>Date</th>
                            <th>Narration</th>
                            <th>Amount</th>
                            <th>Confirmed By</th>
                            <th>Final Approved By</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $total = 0;
                        $rpt_curr = $this->common_data['company_data']['company_reporting_currencyID'];
                        $dPlace = fetch_currency_desimal_by_id($rpt_curr);

                        foreach ($drilldowndata as $dtl) {
                            ?>
                            <tr>
                                <td>
                                    <a href="#" class="drill-down-cursor"
                                       onclick="documentPageView_modal('<?php echo $dtl['documentCode'] ?>','<?php echo $dtl['documentMasterAutoID'] ?>')"> <?php echo $dtl['doccode']; ?></a>
                                </td>
                                <td><?php echo $dtl['documentCode']; ?></td>
                                <td><?php echo $dtl['department']; ?></td>
                                <td>
                                    <div style="width: 60px;">
                                        <?php echo $dtl['documentDate']; ?>
                                    </div>
                                </td>
                                <td style="width: 44%;"><?php echo $dtl['documentNarration']; ?></td>
                                <td style="text-align: right;"><?php
                                    if ($dtl['companyReportingAmount'] >= 0) {
                                        echo number_format($dtl['companyReportingAmount'], $dtl['companyReportingAmountDecimalPlaces']);
                                    } else {
                                        echo '(' . number_format(abs($dtl['companyReportingAmount']), $dtl['companyReportingAmountDecimalPlaces']) . ')';
                                    }


                                    ?></td>
                                <td><?php echo $dtl['confirmedByName'] ?></td>
                                <td><?php echo $dtl['approvedbyEmp'] ?></td>
                            </tr>

                            <?php
                            $total += $dtl['companyReportingAmount'];
                        }

                        //echo '<pre>';print_r($coltot); echo '</pre>'; die();
                        ?>

                        <tr>
                            <td><b>Total</b></td>
                            <td colspan="4">&nbsp;</td>
                            <td class="text-right reporttotal"><b><?php
                                    if ($total >= 0) {
                                        echo number_format($total, $dPlace);
                                    } else {
                                        echo '(' . number_format(abs($total), $dPlace) . ')';
                                    }


                                    ?></b></td>
                        </tr>


                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <br>
    <br>
    <div class="row" style="margin: 0 auto; border: 0px solid">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    /*    $('#tbl_rpt_salesorder').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 10
        });*/

</script>