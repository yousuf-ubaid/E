<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('tax', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

if($reportType == 'Output') {
    $id = 'vat_outputReport';
    $tableID = 'tbl_rpt_vat_return_filing';
    // $tableID = 'tbl_outputVat_report';
}else{
    $id = 'vat_inputReport'; 
    $tableID = 'tbl_rpt_vat_return_filing_input';
    // $tableID = 'tbl_inputVat_report';
}
$comp_vatRegistered = 'NO';
if($this->common_data['company_data']['vatRegisterYN'] == 1) {
    $comp_vatRegistered = 'YES';
}

if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                if($reportType=='Output'){
                    echo export_buttons($id, 'VAT Report', true, false);
                }else{
                    echo export_buttons($id, 'VAT Report', true, false);
                }
            } ?>
        </div>
    </div>
    
<div class="row" style="margin-top: 5px;">
    <div class="col-md-12 " id="<?php echo $id; ?>">
        <div class="reportHeaderColor" style="text-align: center">
            <strong><?php echo current_companyName(); ?></strong><br>
            <strong><?php echo 'Company VAT No : ' . $this->common_data['company_data']['vatIdNo']; ?></strong>
        </div>
        <div class="reportHeader reportHeaderColor" style="text-align: center">
            <strong>
                <?php
                    if($reportType == 'Output') {
                        echo $this->lang->line('tax_output_vat_summary_report'); 
                    } else {
                        echo $this->lang->line('tax_input_vat_summary_report'); 
                    }
                ?>
            </strong>
        </div>
        <div style="height: 500px;">
            <table id="<?php echo $tableID; ?>" class="borderSpace report-table-condensed" style="width: 100%">
                <thead class="report-header" style="position: sticky;">
                    <tr>
                        <th>Accounting Document Number</th>
                        <th>Accounting Document Date</th>
                        <?php if($reportType == 'Output') {
                            echo '  <th>Revenue GL Code</th>
                                    <th>Revenue GL Description</th>';
                        } else {
                            echo '  <th>Expense GL Code</th>
                                    <th>Expense GL Description</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Country</th>
                                    <th>Supplier Type</th>
                                    <th>Supplier VAT Registration Number</th>';
                        } ?>
                        <th>Document Currency</th>
                        <th>Document Type</th>
                        <th>Original Document No</th>
                        <th>Original Document Date</th>
                        <?php if($reportType == 'Input') {
                            echo '<th>Payment Due Date</th>';
                        } else {
                            echo '<th>Date of Supply</th>';
                        } ?>
                        <th>Reference Invoice No</th>
                        <th>Reference Invoice Date</th>
                        <?php if($reportType == 'Output') {
                            echo '<th>Bill To Country</th>';
                            echo '<th>Bill To Customer Name</th>';
                            echo '<th>Customer Type</th>';
                        } ?>
                        <th>Invoice Line Item No</th>
                        <th>Line Item Description</th>
                        <?php if($reportType == 'Output') {
                            echo '<th>Place of Supply</th>';
                        } ?>
                        <th>Tax Code Type</th>
                        <th>Tax Code Description</th>
                        <th>VAT Rate</th>
                        <th>Base Value In Document Currency</th>
                        <th>Tax Value In Document Currency</th>
                        <th>Document Currency to <?php echo $this->common_data['company_data']['company_default_currency']; ?> FX Rate</th>
                        <th>Base Value in <?php echo $this->common_data['company_data']['company_default_currency']; ?></th>
                        <th>VAT in <?php echo $this->common_data['company_data']['company_default_currency']; ?></th>
                        <th>VAT GL Code</th>
                        <th>VAT GL Code Description</th>
                        <?php if($reportType == 'Input') {
                            echo '  <th>Input Tax Recoverability</th>
                                    <th>Input Tax Recoverability %</th>
                                    <th>Input Tax Recoverability Amount</th>';
                        } ?>
                    </tr>
                </thead>
                <tbody style="height: 500px">
                    <?php
                    $documentTotal = 0;
                    $vatTotal = 0;
                    $documentLocalTotal = 0;
                    $vatLocalTotal = 0;
                    foreach ($details as $val) {
                        ?>
                        <tr>
                            <td><a href="#" class="drill-down-cursor"
                                onclick="documentPageView_modal('<?php echo $val['documentID'] ?>',<?php echo $val['documentMasterAutoID'] ?>)"><?php echo $val['documentCode'] ?></a>
                            </td>
                            <td><?php echo $val['documentDate'] ?></td>
                            <td><?php echo $val['glCode'] ?></td>
                            <td><?php echo $val['glDescription'] ?></td>
                            <?php if($reportType == 'Input') { ?>
                                <td><?php echo $val['partyName'] ?></td>
                                <td><?php echo $val['partyCountry'] ?></td>
                                <td><?php echo $val['vatregistered'] ?></td>
                                <td><?php echo $val['vatIdNo'] ?></td>
                            <?php } ?>
                            <td><?php echo $val['transactionCurrency'] ?></td>
                            <td><?php echo $val['documentType'] ?></td>
                            <td><?php echo $val['referenceNumber'] ?></td>
                            <td><?php echo $val['invoiceDate'] ?></td>

                            <!-- date of supply / payment due date -->
                            <td><?php echo $val['invoiceDueDate'] ?></td>


                            <td><?php echo $val['referenceDocNo'] ?></td>
                            <td><?php echo $val['referenceDocDate'] ?></td>
                            <?php if($reportType == 'Output') { 
                                if($this->common_data['company_data']['company_country'] == $val['partyCountry']) {
                                    echo '<td>'.$this->common_data['company_data']['company_country'].'</td>';
                                } else {
                                    echo '<td>Out Of '.$this->common_data['company_data']['company_country'].'</td>';
                                }?>
                                <td><?php echo $val['partyName'] ?></td>
                                <td><?php echo $val['vatregistered'] ?></td>
                            <?php } ?>

                            <td><?php echo ' ' ?></td>
                            <td><?php echo ' ' ?></td>

                            <?php if($reportType == 'Output') {
                               if($this->common_data['company_data']['company_country'] == $val['partyCountry']) {
                                    echo '<td>'.$this->common_data['company_data']['company_country'].'</td>';
                                } else {
                                    echo '<td>Out Of '.$this->common_data['company_data']['company_country'].'</td>';
                                }
                            } ?>
                            
                            <td><?php echo $val['vatType'] ?></td>
                            <td><?php echo $val['VATtypeDesription'] ?></td>
                            <td><?php echo $val['taxPercentage'] ?>%</td>
                            <td style="text-align: right"><?php echo number_format($val['documentAmount'], $val['decimalPlace']) ?></td>
                            <td style="text-align: right"><?php echo number_format($val['vatAmount'], $val['decimalPlace']) ?></td>
                            <td><?php echo $val['companyLocalExchangeRate'] ?></td>
                            <td style="text-align: right"><?php echo number_format($val['documentAmount']/$val['companyLocalExchangeRate'], $val['decimalPlace']) ?></td>
                            <td style="text-align: right"><?php echo number_format($val['vatAmount']/$val['companyLocalExchangeRate'], $val['decimalPlace']) ?></td>
                            <td><?php echo $val['vatGLCode'] ?></td>
                            <td><?php echo $val['vatGLDescription'] ?></td>
                            <?php if($reportType == 'Input') { ?>
                                <td><?php echo $comp_vatRegistered ?></td>
                                <td><?php echo '100%' ?></td>
                                <td style="text-align: right"><?php echo number_format($val['vatAmount'], $val['decimalPlace']) ?></td>
                            <?php } ?>
                        </tr>
                    <?php
                        $documentTotal += $val['documentAmount'];
                        $vatTotal += $val['vatAmount'];
                        $documentLocalTotal += $val['documentAmount']/$val['companyLocalExchangeRate'];
                        $vatLocalTotal += $val['vatAmount']/$val['companyLocalExchangeRate'];
                        $decimalPlace = $val['decimalPlace'];
                    }
                    ?>
                <tr>
                    <td colspan="20"><b><?php echo $this->lang->line('tax_net_total'); ?></b></td>
                    <td class="text-right reporttotal"><?php echo number_format($documentTotal,$decimalPlace) ?></td>
                    <td class="text-right reporttotal"><?php echo number_format($vatTotal,$decimalPlace) ?></td>
                    <td>&nbsp;</td>
                    <td class="text-right reporttotal"><?php echo number_format($documentLocalTotal,$decimalPlace) ?></td>
                    <td class="text-right reporttotal"><?php echo number_format($vatLocalTotal,$decimalPlace) ?></td>
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
    $('#tbl_rpt_vat_return_filing').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

    $('#tbl_rpt_vat_return_filing_input').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
</script>