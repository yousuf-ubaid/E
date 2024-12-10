
<?php if($typehtml==true){?>
    <style>
        .bgcolour {
            background-color: #00a65a;
            margin-top: 3%;
        }
        .bgcolourconfirm {
            background-color: #f9ac38;
            margin-top: 3%;
        }
        .item-labellabelbuyback {
            color: #fff;
            height: 21px;
            width: 90px;
            position: absolute;
            font-weight: bold;
            padding-left: 10px;
            padding-top: 0px;
            top: 10px;
            right: -59px;
            margin-right: 0;
            border-radius: 3px 3px 0 3px;
            box-shadow: 0 3px 3px -2px #ccc;
            text-transform: capitalize;
        }
        .item-labellabelbuyback:after {
            top: 20px;
            right: 0;
            border-top: 4px solid #1f1d1d;
            border-right: 4px solid rgba(0, 0, 0, 0);
            content: "";
            position: absolute;
        }
        .item-labelapproval {
            color: #fff;
            height: 21px;
            width: 90px;
            position: absolute;
            font-weight: bold;
            padding-left: 10px;
            padding-top: 0px;
            top: 10px;
            right: -20px;
            margin-right: 0;
            border-radius: 3px 3px 0 3px;
            box-shadow: 0 3px 3px -2px #ccc;
            text-transform: capitalize;
        }
        .item-labelapproval:after {
            top: 20px;
            right: 0;
            border-top: 4px solid #1f1d1d;
            border-right: 4px solid rgba(0, 0, 0, 0);
            content: "";
            position: absolute;
        }
    </style>
<?php }?>
<?php echo fetch_account_review(true, true, $approval);
if ($extra['master']['PVtype'] == 1) {
    $type = "Payment Voucher";
} else if ($extra['master']['PVtype'] == 2) {
    $type = "Receipt Voucher";
} else if ($extra['master']['PVtype'] == 3) {
    $type = "Settlement";
}else if ($extra['master']['PVtype'] == 4) {
    $type = "Journal Entry";
}
?>
<div class="table-responsive"  style="margin-bottom: -10px">
    <table style="width: 100%;">
        <tr>
            <td>
                <table style="font-family:'Arial, Sans-Serif, Times, Serif';">
                    <tr>
                        <td style="text-align: center;">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h4>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <p><?php echo 'Phone: ' . $this->common_data['company_data']['company_phone']?></p>
                            <h4><?php echo $type; ?></h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
<hr>
<?php if($typehtml==true){
    $class = 'theadtr'
    ?>
    <?php if($extra['master']['approvedYN']== 1 && $extra['master']['confirmedYN']== 1) {
        echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="item-labellabelbuyback file bgcolour">Approved</div>
    </article>';
    }?>
    <?php if($extra['master']['confirmedYN']==1 && $extra['master']['approvedYN']!= 1 && $size !=1) {
        echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="item-labellabelbuyback file bgcolourconfirm">Confirmed</div>
    </article>';
    }?>
<?php } else {
    $class = '';
}?>

<?php if($extra['master']['confirmedYN']== 1 && $size == 1) {
    echo '<div class="post-area" >
    <article class="post" style="padding-bottom: 2%">
        <div class="item-labelapproval file bgcolourconfirm">Confirmed</div>
    </article>';
}?>
<br>
<div class="table-responsive">
    <table style="font-family:'Arial, Sans-Serif, Times, Serif'; width: 100%">
        <tbody>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:20%;"><strong><?php echo $type; ?></strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:2%;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px; width:25%;"><?php echo $extra['master']['documentSystemCode']; ?></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong><?php echo $type; ?> Date</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['documentDate']; ?></td>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Reference Number</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['referenceNo']; ?></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Farmer Name</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo (empty($extra['master']['farmName'])) ? $extra['master']['farmName'] : $extra['master']['farmName'] . ' ( ' . $extra['master']['farmerCode'] . ' )'; ?></td>
        </tr>
        <tr>
            <?php if ($extra['master']['PVtype'] != 4) {?>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Bank</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['PVbank'] . ' / ' . $extra['master']['PVbankBranch']; ?></td>
            <?php } ?>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Address</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php if (!empty($extra['master']['farmAddress'])) echo $extra['master']['farmAddress']; ?></td>
            <?php if ($extra['master']['PVtype'] != 4) {?>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong> Bank Account</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['PVbankAccount'] ?></td>
            <?php } ?>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Telephone / Fax</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php if (!empty($extra['master']['farmTelephone'])) echo $extra['master']['farmTelephone']; ?></td>
        </tr>
        <tr>
            <?php if ($extra['master']['PVtype'] != 4) {?>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong> Bank Swift Code</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['PVbankSwiftCode']; ?></td>
            <?php } ?>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Currency</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['CurrencyName'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <?php if ($extra['master']['PVtype'] != 4) {?>
        </tr>
        <tr>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Cheque Number</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['PVchequeNo']; ?></td>
            <?php } ?>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Narration</td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
            <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['PVNarration']; ?></td>
        </tr>
        <tr>
            <?php if ($extra['master']['PVtype'] != 4) {?>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>Cheque Date</td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><strong>:</strong></td>
                <td style="font-size: 12px;  height: 8px; padding: 1px;"><?php echo $extra['master']['PVchequeDate']; ?></td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
</div> <br>
<?php $grand_total = 0;
$companyID = $this->common_data['company_data']['company_id'];
$tax_transaction_total = 0;
if ($extra['master']['PVtype'] == 1) {
    if (!empty($extra['expense'])) { ?>
        <h5 style="margin-left: 1%; font-family:'Arial, Sans-Serif, Times, Serif';">Expense Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 15%">Batch</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%">GL Code</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">GL Code Description</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">Segment</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Transaction
                        (<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                $grand_total = 0;
                if (!empty($extra['expense'])) {
                    foreach ($extra['expense'] as $val) { ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; text-align:center;"><?php echo $val['batchCode']; ?></td>
                            <td style="font-size: 14px; text-align:center;"><?php echo $val['GLCode'] ?></td>
                            <td style="font-size: 14px; text-align:center;"><?php echo $val['GLDescription']; ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo $val['segmentCode']; ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $grand_total += $val['transactionAmount'];
                    }
                } else {
                    echo '<tr style="font-size: 11px; text-align:center;"><td colspan="7" class="text-center" style="font-size: 14px; text-align:right;">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="5">Total</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <br>
    <?php } ?>
    <?php if (!empty($extra['income'])) { ?>
        <h5 style="margin-left: 1%; font-family:'Arial, Sans-Serif, Times, Serif';">Deposit Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 10%">#</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 70%"> Description</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 20%">Transaction (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                $grand_total = 0;
                if (!empty($extra['income'])) {
                    foreach ($extra['income'] as $val) { ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; ">Deposit : <?php echo $val['comment']; ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $grand_total += $val['transactionAmount'];
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="2">Total</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <br>
    <?php }?>

    <?php if (!empty($extra['advance'])) {
        $advance_total = 0;
        ?>
        <h5 style="margin-left: 1%;font-family:'Arial, Sans-Serif, Times, Serif';">Advance Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Description</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Transaction (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                if (!empty($extra['advance'])) {
                    foreach ($extra['advance'] as $val) { ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; text-align:center;">Advance :<?php echo $val['comment'];?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $advance_total += $val['transactionAmount'];
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="6" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="2"> Total</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($advance_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <br>
    <?php } ?>
    <?php if (!empty($extra['paymentbatch'])) {
        $batch_total = 0;
        ?>
        <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Batch Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Batch</th>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Description</th>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Transaction
                        (<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                if (!empty($extra['paymentbatch'])) {
                    foreach ($extra['paymentbatch'] as $val) { ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; text-align:center;"><?php echo $val['batchCode']; ?></td>
                            <td style="font-size: 14px; text-align:center;">Batch : <?php echo $val['comment']; ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $batch_total += $val['transactionAmount'];
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="3"> Total</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($batch_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php } ?>
    <?php if (!empty($extra['loan'])) {
        $loan_total = 0;
        ?>
        <br>
        <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Loan Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Description</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">
                        Transaction(<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                if (!empty($extra['loan'])) {
                    foreach ($extra['loan'] as $val) { ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; text-align:center;">Loan :<?php echo $val['comment']; ?></td>
                            <td style="font-size: 14px; text-align:right;">
                                <?php
                                if ($val['isMatching'] == 0) {
                                    echo number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                    $loan_total += $val['transactionAmount'];
                                } else {
                                    echo '(' . number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . ')';
                                    $loan_total -= $val['transactionAmount'];
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $num++;
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="6" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="2"> Total</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($loan_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php }
}
if ($extra['master']['PVtype'] == 2) { ?>
    <?php if (!empty($extra['income'])) { ?>
        <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Deposit Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 10%">#</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 70%"> Description</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; width: 20%">Transaction
                        (<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                $grand_total = 0;
                if (!empty($extra['income'])) {
                    foreach ($extra['income'] as $val) { ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; ">Deposit : <?php echo $val['comment']; ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $grand_total += $val['transactionAmount'];
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="2">Total</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($grand_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php }?>
    <br>
    <?php if (!empty($extra['advance'])) {
        $advance_total = 0;
        $balance_total = 0;
        $paid_total = 0;
        ?>
        <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Advance Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Description</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Due Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Paid Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Balance Amount
                        (<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                if (!empty($extra['advance'])) {
                    foreach ($extra['advance'] as $val) { ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; text-align:center;">Advance :<?php echo $val['comment']; ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $paid_total += $val['transactionAmount'];
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="3"> Total (<?php echo $extra['master']['transactionCurrency']; ?>)</td>
                        <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($paid_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php } ?>
    <?php if (!empty($extra['loan'])) {
        $loan_total = '';
        $loanPaid_total = 0;
        $balance_total = 0;
        ?>
        <br>
        <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Loan Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Description</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Due Amount(<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Paid Amount(<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                    <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Balance Amount(<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                if (!empty($extra['loan'])) {
                    foreach ($extra['loan'] as $val) {
                        ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; text-align:center;">Loan :<?php echo $val['comment']; ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo number_format($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $loanPaid_total += $val['transactionAmount'];
                        $num++;
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="3"> Total (<?php echo $extra['master']['transactionCurrency']; ?>)</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($loanPaid_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php }  ?>
    <?php if (!empty($extra['batch'])) {
        $batch_total = '';
        $batchPaid_total = 0;
        $balance_total = 0;
        ?>
        <br>
        <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Batch Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
        <div class="table-responsive">
            <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
                <thead>
                <tr>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 3%">#</th>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Batch</th>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 40%">Description</th>
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Due Amount(<?php echo $extra['master']['transactionCurrency']; ?>)
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Paid Amount(<?php echo $extra['master']['transactionCurrency']; ?>)
                    <th <?php echo $class?>  style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 12%">Balance Amount(<?php echo $extra['master']['transactionCurrency']; ?>)
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num = 1;
                if (!empty($extra['batch'])) {
                    foreach ($extra['batch'] as $val) {
                        ?>
                        <tr>
                            <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                            <td style="font-size: 14px; text-align:center;"><?php echo $val['batchCode']; ?></td>
                            <td style="font-size: 14px; text-align:center;">Batch :<?php echo $val['comment'];?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="font-size: 14px; text-align:right;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        </tr>
                        <?php
                        $num++;
                        $batchPaid_total += $val['transactionAmount'];
                    }
                } else {
                    echo '<tr style="font-size: 14px; text-align:center;"><td colspan="7" class="text-center">No Records Found</td></tr>';
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="4"> Total (<?php echo $extra['master']['transactionCurrency']; ?>)</td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($batchPaid_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="font-size: 14px; font-weight: bold;" class="text-right"></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php }
}
if ($extra['master']['PVtype'] == 3) { ?>
    <br>
    <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Batch Settlement Details (<?php echo $extra['detail']['documentSystemCode'] ?>)</h5>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">
            <thead>
            <tr>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%">Description</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">Batch Code</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Due Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Paid Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Balance Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $grand_settlementTotal = 0;
            if (!empty($extra['settlement'])) {
                foreach ($extra['settlement'] as $set) {  ?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $set['type'] ?></td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $set['batchCode']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($set['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($set['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($set['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $grand_settlementTotal += $set['transactionAmount'];
                }
            } else {
                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="6" class="text-center">No Records Found</td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="4">Total</td>
                <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($grand_settlementTotal, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php }
if ($extra['master']['PVtype'] == 4) { ?>
    <br>
    <h5 style="font-family:'Arial, Sans-Serif, Times, Serif'; margin-left: 1%">Journal Entry</h5>
    <div class="table-responsive">
        <table class="table table-striped" style="font-family:'Arial, Sans-Serif, Times, Serif';">

            <thead>
            <tr>
                <th <?php echo $class?> id="journalEntry_View" colspan="6" style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; ">GL details</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; "> <span class="jvDetailscurrency">&nbsp;</span></th>
            </tr>
            <tr>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">#</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 45%">System Code</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 5%">GL Code</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">GL Code Description</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Journal Type</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Narration</th>
                <th <?php echo $class?> style="font-size: 12px;font-weight:normal; border-bottom: 1px solid black; min-width: 10%">Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $grand_TotalJournal = 0;
            if (!empty($extra['journalEntry'])) {
                foreach ($extra['journalEntry'] as $journal) {
                    if($journal['type'] == 'Profit' OR $journal['type'] == 'Deposit'){
                        $JournalAmount = $journal['debitAmount'];
                    } else {
                        $JournalAmount = $journal['creditAmount'];
                    }?>
                    <tr>
                        <td style="font-size: 14px; text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $journal['systemGLCode'] ?></td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $journal['GLCode']; ?></td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $journal['GLDescription']; ?></td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $journal['type']; ?></td>
                        <td style="font-size: 14px; text-align:center;"><?php echo $journal['comment']; ?></td>
                        <td style="font-size: 14px; text-align:right;"><?php echo format_number($JournalAmount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $grand_TotalJournal += $JournalAmount;
                }
            } else {
                echo '<tr style="font-size: 14px; text-align:center;"><td colspan="8" class="text-center">No Records Found</td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total" colspan="6">Total</td>
                <td style="font-size: 14px; font-weight: bold;" class="text-right sub_total"><?php echo format_number($grand_TotalJournal, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<br>
<div class="table-responsive">
    <table style="width: 100%;font-family:'Arial, Sans-Serif, Times, Serif';">
        <tr>
            <td style="width:50%;">
                <?php if ($extra['master']['confirmedYN'] && $extra['master']['approvedYN']!=1) { ?>
                    <table style="width: 100%; font-family:'Times New Roman';">
                        <tbody>
                        <tr>
                            <td style="font-size: 11px;"><strong>Confirmed By </strong></td>
                            <td style="font-size: 11px;"><strong>:</strong></td>
                            <td style="font-size: 11px;"><?php echo $extra['master']['confirmedByName']; ?> / <?php echo $extra['master']['confirmedDate']; ?> </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </td>
            <td style="width:70%;">
                &nbsp;
            </td>
        </tr>
    </table>
</div>
<?php if ($extra['master']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%; font-family:'Arial, Sans-Serif, Times, Serif';">
            <tbody>
            <tr>
                <td style="font-size: 11px;width:30%;"><b> Approved By </b></td>
                <td><strong>:</strong></td>
                <td style="font-size: 11px;width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?> / <?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            <tr>
                <td style="font-size: 11px;" ><strong>Confirmed By </strong></td>
                <td ><strong>:</strong></td>
                <td style="font-size: 11px;"><?php echo $extra['master']['confirmedByName']; ?> / <?php echo $extra['master']['confirmedDate']; ?> </td>
            </tr>
            <tr>
                <td style="font-size: 11px;width:30%;">&nbsp;</td>
                <td><strong>&nbsp;</strong></td>
                <td style="font-size: 11px;width:70%;">&nbsp;</td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Buyback/load_paymentVoucher_confirmation'); ?>/<?php echo $extra['master']['pvMasterAutoID'] ?>";
    de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_paymentVoucher'); ?>/" + <?php echo $extra['master']['pvMasterAutoID'] ?> +'/BBPV';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>
