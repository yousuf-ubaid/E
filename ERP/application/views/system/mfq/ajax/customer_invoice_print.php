<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false, false, $approval);
$JObView = '';
if($header['po_numberEST']) {
    $POView = implode(',&nbsp;&nbsp;&nbsp;', (array_column($header['po_numberEST'], 'poNumber')));
    $JObView = implode(',&nbsp;&nbsp;&nbsp;', (array_column($header['po_numberEST'], 'documentCode')));
} else {
    $POView = '     ';
}
if($header['isGroupBasedTax'] == 1) {
    $colspan_left = '3';
    $colspan_right = '4';
} else {
    $colspan_left = '2';
    $colspan_right = '3';
}
?>
<div id="div_print" style="padding:5px;">
    <table width="100%">
        <tbody>
        <tr>
            <td width="200px"><img alt="Logo" style="height: 130px"
                                   src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>"></td>
            <td>
                <div style="text-align: center; font-size: 17px; line-height: 26px; margin-top: 10px;">
                    <strong> <?php echo $this->common_data['company_data']['company_name'] ?></strong><br>
                    <center><?php echo $this->lang->line('manufacturing_customer_invoice') ?><!--Customer Invoice--></center>
                </div>
            </td>
            <td style="text-align:right;">
                <div style="text-align:right; font-size: 17px; vertical-align: top;">

                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table width="100%" cellspacing="0" cellpadding="4" border="1">
        <tbody>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('common_invoice_number') ?><!--Invoice Number--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="79"><?php echo $header["invoiceCode"] ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('common_invoice_date') ?><!--Invoice Date--></b></td>
            <td colspan="<?php echo $colspan_right ?>"><?php echo $header["invoiceDate"]; ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('manufacturing_serial_no') ?><!--SE No--></b></td>
            <td colspan="<?php echo $colspan_right ?>"><?php //echo $header[""]; ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('manufacturing_due_date') ?><!--Due Date--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214"><?php echo $header["invoiceDueDate"]; ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('manufacturing_contract') ?><!--Contract--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214"><?php //echo $header[""]; ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('manufacturing_delivery_note') ?><!--Delivery Note--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214"><?php echo $header["deliveryNoteCode"]; ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('manufacturing_job_no') ?><!--Job No--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214">
                <?php if($header['confirmedYN']!=1){?>
                    <textarea class="form-control" rows="4" onchange="update_customerInovoiceheader(<?php echo $header['invoiceAutoID']?>,this.value);"><?php echo str_replace('|', PHP_EOL, $header['jobreferenceNo']);?></textarea>
                <?php }else {?>
                    <?php echo str_replace(PHP_EOL, '<br /> ', $header['jobreferenceNo']);?>
                <?php }?>


            </td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b>Sub <?php echo $this->lang->line('manufacturing_job_no') ?><!--Job No--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214">
                <?php echo $header['linkedSubJobs'];?>
            </td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('manufacturing_purchase_order_number') ?><!--PO Number--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214"><?php echo $POView ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b>Invoice Number</b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214"><?php echo $header['erpInvoiceCode'] ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $colspan_left ?>"><b><?php echo $this->lang->line('common_comments') ?><!--Comments--></b></td>
            <td colspan="<?php echo $colspan_right ?>" width="214"><?php echo $header["invoiceNarration"]; ?></td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <?php if($header['isGroupBasedTax'] == 1) { ?>
                <td colspan="7" style="text-align:center;"><?php echo $this->lang->line('manufacturing_item_detail') ?><!--Item Detail--></td>
            <?php } else { ?>
                <td colspan="5" style="text-align:center;"><?php echo $this->lang->line('manufacturing_item_detail') ?><!--Item Detail--></td>
            <?php } ?>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td><?php echo $this->lang->line('common_item_description') ?><!--Item Description--></td>
            <td ><?php echo $this->lang->line('common_uom') ?><!--UoM--></td>
            <td><?php echo $this->lang->line('common_qty') ?><!--Qty--></td>
            <td><?php echo $this->lang->line('manufacturing_unit_rate') ?><!--Unit Rate--></td>
            <td><?php echo $this->lang->line('common_amount') ?><!--Amount--></td>
            <?php if($header['isGroupBasedTax'] == 1) { ?>
                <th><?php echo $this->lang->line('common_tax') ?><!--tax--></th>
                <th><?php echo $this->lang->line('common_net_amount') ?><!--Net Amount--></th>
            <?php } ?>
        </tr>
        <?php
        $totalAmount = 0;
        if (!empty($itemDetail)) {
            foreach ($itemDetail as $val) {
                if ($val['type'] == 2) {
                    $totalAmount += $val['transactionAmount'];
                    ?>
                    <tr>
                        <td width="25%"><?php echo $val['itemDescription']; ?></td>
                        <td width="25%"><?php echo $val['defaultUnitOfMeasure']; ?></td>
                        <td style="text-align: right"><?php echo $val['requestedQty']; ?></td>
                        <td style="text-align: right"><?php echo number_format($val['unitRate'], $header["transactionCurrencyDecimalPlaces"]); ?></td>
                        <td style="text-align: right"><?php echo number_format(($val['transactionAmount'] - $val['taxAmount']), $header["transactionCurrencyDecimalPlaces"]); ?></td>
                        <?php if($header['isGroupBasedTax'] == 1) { ?>
                            <td style="text-align: right"><?php echo number_format($val['taxAmount'], $header["transactionCurrencyDecimalPlaces"]); ?></td>
                            <td style="text-align: right"><?php echo number_format($val['transactionAmount'], $header["transactionCurrencyDecimalPlaces"]); ?></td>
                        <?php } ?>
                    </tr>
                <?php }
            }
        }else{
            if($header['isGroupBasedTax'] == 1) { ?>
                <tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found') ?><!--No Records Found--></b></td></tr>
            <?php } else { ?>
                <tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found') ?><!--No Records Found--></b></td></tr>
            <?php }
        }
        ?>
        <?php if($header['isGroupBasedTax'] == 1) { ?>
            <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
                <td colspan="7" style="text-align:center;"><?php echo $this->lang->line('common_gl_details') ?><!--GL Detail--></td>
            </tr>
        <?php } else { ?>
            <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
                <td colspan="5" style="text-align:center;"><?php echo $this->lang->line('common_gl_details') ?><!--GL Detail--></td>
            </tr>
        <?php } ?>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td ><?php echo $this->lang->line('common_gl_code') ?><!--GL Code--></td>
            <td ><?php echo $this->lang->line('common_gl_code_description') ?><!--GL Code Description--></td>
            <td><?php echo $this->lang->line('common_qty') ?><!--Qty--></td>
            <td><?php echo $this->lang->line('manufacturing_unit_rate') ?><!--Unit Rate--></td>
            <td><?php echo $this->lang->line('common_amount') ?><!--Amount--></td>
            <?php if($header['isGroupBasedTax'] == 1) { ?>
                <th><?php echo $this->lang->line('common_tax') ?><!--tax--></th>
                <th><?php echo $this->lang->line('common_net_amount') ?><!--Net Amount--></th>
            <?php } ?>
        </tr>
        <?php
        if (!empty($itemDetail)) {
            foreach ($itemDetail as $val) {
                if ($val['type'] == 1) {
                    $totalAmount += $val['transactionAmount'];
                    ?>
                    <tr>
                        <td width="25%"><?php echo $val['revenueGLAutoID']; ?></td>
                        <td width="25%"><?php echo $val['GLDescription']; ?></td>
                        <td style="text-align: right"><?php echo $val['requestedQty']; ?></td>
                        <td style="text-align: right"><?php echo number_format($val['unitRate'], $header["transactionCurrencyDecimalPlaces"]); ?></td>
                        <td style="text-align: right"><?php echo number_format(($val['transactionAmount'] - $val['taxAmount']), $header["transactionCurrencyDecimalPlaces"]); ?></td>
                        <?php if($header['isGroupBasedTax'] == 1) { ?>
                            <td style="text-align: right"><?php echo number_format($val['taxAmount'], $header["transactionCurrencyDecimalPlaces"]); ?></td>
                            <td style="text-align: right"><?php echo number_format($val['transactionAmount'], $header["transactionCurrencyDecimalPlaces"]); ?></td>
                        <?php } ?>
                    </tr>
                <?php }
            }
            ?>
            <tr>
                <?php  if($header['isGroupBasedTax'] == 1) { ?>
                    <td style="text-align: right" colspan="6"><b><?php echo $this->lang->line('common_total') ?><!--Total--></b></td>
                <?php } else { ?>
                    <td style="text-align: right" colspan="4"><b><?php echo $this->lang->line('common_total') ?><!--Total--></b></td>
                <?php } ?>
                <td style="text-align: right"><b><?php echo number_format($totalAmount,$header["transactionCurrencyDecimalPlaces"]) ?></b></td>
            </tr>
            <?php
        }else{
            if($header['isGroupBasedTax'] == 1) { ?>
                <tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>
            <?php } else { ?>
                <tr class="danger"><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>
            <?php }
        }
        ?>
        </tbody>
    </table>
</div>
<script>
    $('.review').removeClass('hide');
    de_link = "<?php echo site_url('MFQ_CustomerInvoice/fetch_double_entry_mfq_customerInvoice'); ?>/" + <?php echo $header['invoiceAutoID'] ?> +'/MCINV';
    //$("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);

    function update_customerInovoiceheader(invoiceAutoID,JobValue)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'customerInvoiceID':invoiceAutoID,'value':JobValue},
            url: "<?php echo site_url('MFQ_CustomerInvoice/save_customer_invoice_jobref'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                } else {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>