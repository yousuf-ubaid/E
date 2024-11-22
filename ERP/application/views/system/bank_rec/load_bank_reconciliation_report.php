<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$companyType = $this->session->userdata("companyType");
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('BankReconciliationReport', 'Sales Order', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="BankReconciliationReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('treasury_bank_reconciliation_report'); ?><!--Bank Reconciliation Report--></strong></div>
            <div style="">
                <table id="tbl_rpt_bankRec" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_document_code'); ?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('treasury_tr_lm_party_name'); ?><!--Party Name--></th>
                        <th><?php echo $this->lang->line('treasury_bank_currency'); ?><!--Bank Currency--></th>
                        <th><?php echo $this->lang->line('treasury_bank_amount'); ?><!--Bank Amount--></th>
                        <th><?php echo $this->lang->line('treasury_reconciliation_date'); ?><!--Reconciliation Date--></th>
                        <th><?php echo $this->lang->line('treasury_bank_cleared_by'); ?><!--Bank Cleared By--></th>
                        <th><?php echo $this->lang->line('treasury_bank_cleared_date'); ?><!--Bank Cleared Date--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $a = 1;
                        foreach ($details as $value) {
//                            , , partyCode clearedBy, clearedDate, clearedAmount
                                ?>
                                <tr>
                                    <td><?php echo $a ?></td>
                                    <td><?php echo $value["documentSystemCode"] ?></td>
                                    <td><?php echo $value["documentDate"] ?></td>
                                    <td><?php echo $value["memo"] ?></td>
                                    <td><?php echo $value["partyName"] ?></td>
                                    <td><?php echo $value["bankCurrency"] ?></td>
                                    <td style="text-align: right;"><?php echo number_format(($value["bankCurrencyAmount"]), $value["bankCurrencyDecimalPlaces"]) ?></td>

                                    <!--<td><a href="#" class="drill-down-cursor"
                                           onclick="documentPageView_modal('<?php /*echo $value["documentID"] */?>',<?php /*echo $value["contractAutoID"] */?>)"><?php /*echo $value["contractCode"] */?></a>
                                    </td>-->
                                    <td><?php echo $value["bankRecAsOf"] ?></td>
                                    <td><?php echo $value["clearedBy"] ?></td>
                                    <td><?php  echo $value["clearedDate"] ?></td>
                                </tr>
                                <?php
                            $a++;
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
    $('#tbl_rpt_bankRec tr').mouseover(function (e) {
        $('#tbl_rpt_bankRec tr').removeClass('highlighted');
        $(this).addClass('highlighted');
    });

    $('#tbl_rpt_bankRec').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>