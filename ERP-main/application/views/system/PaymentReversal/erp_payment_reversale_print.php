<?php echo fetch_account_review(false,true,$approval); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4>Payment Reversal </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Payment Reversal Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentSystemCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Reversal Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Reference Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referenceNo']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:15%;vertical-align: top;"><strong>Supplier</strong></td>
            <td style="width:2%;vertical-align: top;"><strong>:</strong></td>
            <td style="width:33%;vertical-align: top;"><?php echo $extra['master']['partyName'] ; ?></td>

            <td style="width:15%;"><strong>Currency </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
        </tr>

        <tr>
            <td style="width:15%;"><strong>Narration </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:33%;"><?php echo $extra['master']['narration']; ?></td>
        </tr>

        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">PV Code</th>
            <th style="min-width: 25%" class="text-left">PV Date</th>
            <th style="min-width: 5%">Cheque No</th>
            <th style="min-width: 5%">Cheque Date</th>
            <th style="min-width: 10%">Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $num = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-center"><?php echo $val['PVcode']; ?></td>
                    <td><?php echo $val['pvDate']; ?></td>
                    <td class="text-center"><?php echo $val['PVchequeNo']; ?></td>
                    <td class="text-right"><?php echo $val['PVchequeDate']; ?></td>
                    <td class="text-right"><?php echo number_format($val['pvAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?> </td>
                </tr>
                <?php
                $num++;
                $total += $val['pvAmount'];
            }
        } else {
            echo '<tr class="danger"><td colspan="6" class="text-center">No Records Found</td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="min-width: 85%  !important" class="text-right sub_total" colspan="5">
                Total <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <td style="min-width: 15% !important"
                class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div><br>

<?php if ($extra['master']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:28%;"><strong>Electronically Approved By </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td><strong>Electronically Approved Date </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('PaymentReversal/load_payment_reversal_conformation'); ?>/<?php echo $extra['master']['paymentReversalAutoID'] ?>";
    $("#a_link").attr("href",a_link);
</script>



