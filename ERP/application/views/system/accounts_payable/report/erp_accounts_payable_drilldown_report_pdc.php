<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($output) { ?>
    <div class="row" style="margin-top: 5px">
        <!--<div class="col-md-12">
            <?php
        /*            if ($type == 'html') {
                        echo export_buttons('salesOrderReport', 'Revenue Details', True, True);
                    } */ ?>
        </div>-->
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Post Dated Cheque Details</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Doc Date<!--Customer Name--></th>
                        <th>Document Code<!--Document Code--></th>
                        <th>Doc Type<!--Segment --></th>

                        <th> Narration</th>
                        <th> PDC Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($output) {
                        $total = 0;
                        $decimal = 2;
                        foreach ($output as $val) { ?>
                            <tr>
                                <td><?php echo $val["bookingDate"] ?></td>

                            <?php   if ($type == 'html') {
                                echo '<td class="text-left"><a href="#"  class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["InvoiceAutoID"] . ')">' . $val["bookingInvCode"] . '</a></td>';

                            }
                            else {
                                echo '<td class="text-right">' . $val["bookingInvCode"] . '</td>';

                                }?>
                                <td><?php echo $val["documentID"] ?></td>
                                <td><?php echo $val["comments"] ?></td>
                                <?php
                                if($fieldName == 'companyLocalAmount') {
                                    echo '<td class="text-right">' . number_format($val["companyLocalAmountcurrency"], $val["companyLocalAmountDecimalPlaces"]) . '</td>';
                                } else if($fieldName == 'companyReportingAmount')
                                {
                                    echo '<td class="text-right">' . number_format($val["companyReportingAmountcurrency"], $val["companyReportingAmountDecimalPlaces"]) . '</td>';
                                }else if ($fieldName == 'transactionAmount')
                                {
                                    echo '<td class="text-right">' . number_format($val["transactionAmountcurrency"], $val["transactionAmountDecimalPlaces"]) . '</td>';
                                }

                                ?>


                            </tr>

                            <?php
                            if($fieldName == 'companyLocalAmount') {
                                $total +=$val["companyLocalAmountcurrency"];
                                $decimal =$val["companyLocalAmountDecimalPlaces"];

                            } else if($fieldName == 'companyReportingAmount')
                            {
                                $total +=$val["companyReportingAmountcurrency"];
                                $decimal =$val["companyReportingAmountDecimalPlaces"];

                            }else if ($fieldName == 'transactionAmount')
                            {
                                $total +=$val["transactionAmountcurrency"];
                                $decimal =$val["transactionAmountDecimalPlaces"];
                            }


                        }?>

                        <tr>
                            <td colspan="4"><b>Total</b></td>

                            <td class="text-right reporttotal" colspan="5"><?php echo number_format($total,$decimal) ?></td>
                        </tr>
                        <?php
                    }
                    ?>

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