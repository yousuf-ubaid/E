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
                echo export_buttons('salesOrderReport', 'Sales Order', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Customer Sales Summary Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th> </th>
                        <th><?php echo $this->lang->line('sales_maraketing_masters_customer_code'); ?><!--Customer Code--></th>
                        <th>Customer Name</th>
                        <th>Rebate</th>
                        <th>Previous 12 <br> Months Sales</th>
                        <th>Credit Months</th>
                        <th>Credit Amount</th>
                        <th>Current Outstanding</th>
                        <th>Outstanding <br> More Than <br> Credit Months</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dPlace = get_company_currency_decimal();
                        $sequence = 1;
                            foreach ($details as $item){
                                echo '<tr>';
                                echo '<td>'.$sequence.'</td>';
                                echo '<td>'.$item['customerSystemCode'].'</td>';
                                echo '<td>'.$item['customerName'].'</td>';
                                if($item['rebatePercentage']!=''){
                                    echo '<td>'.$item['rebatePercentage'].'%</td>';
                                }else{
                                    echo '<td>-</td>';
                                }
                                echo '<td class="text-right">'.format_number($item['previous12monthsales']).'</td>';
                                echo '<td class="text-right">'.$item['credintMonths'].'</td>';
                                echo '<td class="text-right">'.format_number($item['CreditAmount'], $dPlace).'</td>';
                                echo '<td class="text-right">'.format_number($item['outstanding'], $dPlace).'</td>';
                                echo '<td class="text-right">'.format_number($item['outStandingmorethanCreditMonth'], $dPlace).'</td>';
                                echo '<td class="text-right"></td>';
                                $sequence++;
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