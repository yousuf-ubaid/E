<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('tax', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

if($reportType == 'Output') {
    $id = 'vat_outputReport';
    $tableID = 'tbl_outputVat_report';
}else{
    $id = 'vat_inputReport'; 
    $tableID = 'tbl_inputVat_report';
}

if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                if($reportType=='Output'){ ?>
                    <div class="col-md-12">
                        <div class="pull-right">
                            <a href="#" class="btn btn-success-new size-sm" id="btn-excel" onclick="generateReportVAToutputSummaryExcel()">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a>
                        </div>
                    </div>      
            <?php
                }else{ ?>
                    <div class="col-md-12">
                        <div class="pull-right">
                            <a href="#" class="btn btn-success-new size-sm" id="btn-excel" onclick="generateReportVATinputSummaryExcel()">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a>
                        </div>
                    </div>    
                     <!-- echo export_buttons($id, 'VAT Report', true, false, 'btn-xs', 'generateReportPdfInputVat()'); -->
            <?php
                }
            } ?>
        </div>
    </div>
    
<div class="row" style="margin-top: 5px">
    <div class="col-md-12 " id="<?php echo $id; ?>">
        <div class="reportHeaderColor" style="text-align: center">
            <strong><?php echo current_companyName(); ?></strong></div>
        <div class="reportHeader reportHeaderColor" style="text-align: center">
            <strong>
                <?php
                    if($reportType == 'Output') {
                        echo $this->lang->line('tax_output_vat_summary_report'); 
                    } else {
                        echo $this->lang->line('tax_input_vat_summary_report'); 
                    }
                ?>
            </strong></div>
        <div>
            <table id="<?php echo $tableID; ?>" class="borderSpace report-table-condensed" style="width: 100%">
                <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_document_code'); ?></th>
                        <th><?php echo $this->lang->line('common_document_types'); ?></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?></th>
                        <th>Invoice Code</th>
                        <th>Invoice Date</th>
                        <th>VAT No</th>
                        <th>
                            <?php
                                if($reportType == 'Output') {
                                    echo $this->lang->line('common_customer'); 
                                } else {
                                    echo $this->lang->line('common_supplier'); 
                                }
                            ?>
                        </th>    
                        <th>Description</th>                    
                        <th><?php echo $this->lang->line('tax_vat_type'); ?></th>
                        <th><?php echo $this->lang->line('tax_vat_claimed'); ?></th>
                        <th><?php echo $this->lang->line('common_approved_by'); ?></th>
                        <th><?php echo $this->lang->line('common_total_amount'); ?></th>
                        <th><?php echo $this->lang->line('tax_vat_amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $a = 1;
                    $documentTotal = 0;
                    $vatTotal = 0;
                    foreach ($details as $val) {
                        ?>
                        <tr>
                            <td><?php echo $a; ?></td>
                            <td>
                                <a href="#" class="drill-down-cursor" onclick="documentPageView_modal('<?php echo $val['documentID'] ?>',<?php echo $val['documentMasterAutoID'] ?>)"><?php echo $val['documentCode'] ?></a>
                            </td>
                            <td><?php echo $val['documentType'] ?></td>
                            <td>
                                <?php echo $val['documentDate'] ?>
                            </td>
                            <td>
                                <?php
                                    if($reportType == 'Output') {
                                        echo $val['invoiceSystemCode']; 
                                    } else {
                                        echo $val['bookingInvCode']; 
                                    }
                                ?>
                            <td><?php echo $val['invoiceDate'] ?></td>
                            <td><?php echo $val['vatIdNo'] ?></td>
                            <td><?php echo $val['partyName'] ?></td>   
                            <td>
                                <?php
                                    if($reportType == 'Output') {
                                        echo $val['invoiceNarration'] ?? '';
                                    } else {
                                        echo $val['comments']; 
                                    }
                                ?>
                                <?php echo $val['invoiceNarration'] ?? '' ?></td>
                            <td><?php echo $val['vatType'] ?></td>
                            <td style="text-align: center"><?php if($val['approvedEmp'] == 1) {
                                    echo '<span class="label label-success">Yes</span>';
                                } else {
                                    echo '<span class="label label-danger">NO</span>';
                                }?>
                            </td>
                            <td><?php echo $val['approvedEmp'] ?></td>
                            <td style="text-align: right"><?php echo number_format($val['documentAmount'], $val['decimalPlace']) ?></td>
                            <td style="text-align: right"><?php echo number_format($val['vatAmount'], $val['decimalPlace']) ?></td>
                        </tr>
                    <?php
                        $documentTotal += $val['documentAmount'];
                        $vatTotal += $val['vatAmount'];
                        $decimalPlace = $val['decimalPlace'];
                        $a++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="12"><b><?php echo $this->lang->line('tax_net_total'); ?></b></td>
                        <td class="text-right reporttotal"><?php echo number_format($documentTotal,$decimalPlace) ?></td>
                        <td class="text-right reporttotal"><?php echo number_format($vatTotal,$decimalPlace) ?></td>
                    </tr>
                </tfoot>                    
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
    var table = $('#<?php echo $tableID; ?>').DataTable();
    table.destroy();
    $('#<?php echo $tableID; ?>').DataTable();
</script>
