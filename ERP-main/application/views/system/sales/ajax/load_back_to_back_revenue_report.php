<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
//if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('backTobackRevenueDetailsReport', 'Back To Back Revenue Details', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="backTobackRevenueDetailsReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Back to Back Revenue Details</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>invoice Code</th>
                        <th>reference No</th>
                        <th>invoice Date</th>
                        <th>revenue GL Code</th>
                        <th>revenue System GL Code</th>
                        <th>revenue GL Description</th>
                        <th>supplier System Code</th>
                        <th>secondary Code</th>
                        <th>supplier Name</th>
                        <th>customer System Code</th>
                        <th>customer secondary Code</th>
                        <th>customer Name</th>
                        <th>transaction Currency</th>
                        <th>company Local Currency</th>
                        <th>transaction Amount</th>
                        <th>company Finance Year</th>
                        <th>company Local Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $total = 0;
                        $companytransactionAmount_total = 0;
                        $companyLocalAmount_total = 0;

                        foreach ($details as $val) {
                            // foreach ($value as $val) {      ?>
                                <tr>
                                    <td><?php echo $val['invoiceCode'] ?></td>
                                    <td><?php echo $val['referenceNo'] ?></td>
                                    <td><?php echo $val['invoiceDate'] ?></td>
                                    <td><?php echo $val['revenueGLCode'] ?></td>
                                    <td><?php echo $val['revenueSystemGLCode'] ?></td>
                                    <td><?php echo $val['revenueGLDescription'] ?></td>
                                    <td><?php echo $val['supplierSystemCode'] ?></td>
                                    <td><?php echo $val['secondaryCode'] ?></td>
                                    <td><?php echo $val['supplierName'] ?></td>
                                    <td><?php echo $val['customerSystemCode'] ?></td>
                                    <td><?php echo $val['customersecondaryCode'] ?></td>
                                    <td><?php echo $val['customerName'] ?></td>
                                    <td><?php echo $val['transactionCurrency'] ?></td>
                                    <td><?php echo $val['companyLocalCurrency'] ?></td>
                                    <td><?php echo $val['transactionAmount'] ?></td>
                                    <td><?php echo $val['companyFinanceYear'] ?></td>
                                    <td><?php echo $val['companyLocalAmount'] ?></td>
                                </tr>

                                <?php
                                $companytransactionAmount_total += $val['transactionAmount'];
                                $companyLocalAmount_total += $val['companyLocalAmount'];
                            ?>

                            <tr>
                                <td>Total</td>
                                <td colspan="14"><?php echo $companytransactionAmount_total ?></td>
                                <td colspan="2"><?php echo $companyLocalAmount_total ?></td>
                            </tr>
                           
                            <?php  
                        } ?>
                           
                        <?php
                    }else{ ?>
                            <tr><td colspan="17">&nbsp;</td></tr>
                            <tr>
                                <td colspan="17" style="text-align:center;font-size:medium;background-color:#f7c16c;"><?php echo $this->lang->line('common_no_records_found'); ?></td>
                            </tr>
                   <?php  }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>