<html>
<head>
    <title>Invoice Print</title>
    <style type="text/css">
        #itemTable th {
            text-align: right !important;
            font-size: 13px;
        }

        #itemTable td {
            font-size: 18px;
        }

        #itemBreak {
            border-top: 1px dashed #000;
        }

        #headerTB td {
            font-size: 18px
        }


        #thanking-div {
            border: 1px dashed #000;
            border-left: none;
            border-right: none;
            padding: 10px;
            font-size: 12px;
            font-weight: bolder;
        }

        .borderlefttopbottomnot {
            border: 1px solid black;
            border-left: none;
            border-top: none;
            border-bottom: none;
            padding-left: 2px;
        }

        .borderlefttopnot {
            border: 1px solid black;
            border-left: none;
            border-top: none;
            padding-left: 2px;
        }

        .borderleftnot {
            border: 1px solid black;
            border-left: none;
            padding-left: 2px;
        }
        .a-right{
            text-align: right !important;
        }
        .f11{
            font-size: 11px !important;
        }

    </style>
</head>

<body onload="/*window.print()*/">

<?php
$outletInfo = get_outletInfo();
$isGroupBasedPolicy =  $group_based_tax = getPolicyValues('GBT', 'All');
$colspan = '2';
$colspan_bill = '6';
$colspanForValue = '2';
if($isGroupBasedPolicy ==1){
    $vatIN = $this->db->query("SELECT IFNULL(vatIdNo, '-') as vatIdNo FROM srp_erp_company WHERE company_id = ".current_companyID())->row('vatIdNo');
    $vatIN_view = '<td style=" font-size: 11px !important"><b>VAT IN </b></td>
                <td style=" font-size: 11px !important"><b>:</b></td>
                <td style=" font-size: 11px !important">'.$vatIN.'</td>';
    if($isOtherTaxExist > 0) {
        $colspan = '4';
        $colspan_bill = '8';
        $colspanForValue = '2';
    }else {
        $colspan = '3';
        $colspan_bill = '7';
    }

}
$invMaster = $invData[1];
$invItems = $invData[2];
$dPlace = $invMaster['transactionCurrencyDecimalPlaces'];
//$companylogo = base_url() . 'images/logo/' . $this->common_data['company_data']['company_logo'];
//$companylogo = base_url() . 'uploads/warehouses/' . $outletInfo['warehouseImage'];
$companylogo = get_s3_url($outletInfo['warehouseImage']);
echo '<table width="100%" style="">
            <tr>
                <td align="center"> <img alt="Logo" style="height: 70px" src="' . $companylogo . '"></td>
            </tr>
            <tr><td align="center" style="font-size: 14px !important; font-weight: 600;padding-bottom:3px;;">' . $outletInfo['wareHouseDescription'] . '</td></tr>
           </table>';
/*<tr><td align="center" style="font-size: 14px !important; font-weight: 600;padding-bottom:3px;;">  فاتورة</td></tr>*/
$trype = 'CASH';
if ($invMaster['cashAmount'] != 0) {
    $trype = 'Cash';
}
if ($invMaster['chequeAmount'] != 0) {
    $trype = 'Cheque';
}
if ($invMaster['cardAmount'] != 0) {
    $trype = 'Card';
}
if ($invMaster['creditNoteAmount'] != 0) {
    $trype = 'Credit Note';
}
$cus = '';
if ($invMaster['customerID'] > 0) {
    $cusid = $invMaster['customerID'];
    $custdetails = $this->db->query("SELECT customerTelephone FROM srp_erp_customermaster WHERE  customerAutoID=$cusid")->row_array();
    $cus = $invMaster['cusName'] . '<br>' . $custdetails['customerTelephone'];

} else {
    $cus = 'Cash';
}
echo '<table width="100%" style="">
            <tr>
                <td class="f11"><b>   زبون </b></td>
                <td class="f11"><b>:</b></td>
                <td class="f11">' . $cus . '</td>

                <td class="f11 a-right"><b>   تاريخ </b></td>
                <td class="f11"><b>:</b></td>
                <td class="f11 a-right">' . date("Y-m-d h:i:A", strtotime($invMaster['createdDateTime'])) . '</td>
            </tr>

            <tr>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>

                <td class="f11 a-right"><b>&nbsp;   رقم الفاتورة </b></td>
                <td class="f11"><b>:</b></td>
                <td class="f11 a-right">' . $doSysCode_refNo . '</td>
            </tr>
            '.$vatIN_view.'

            <tr>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>
            </tr>
           </table>';
?>
<?php
$isHideZeroDiscountFields = $this->pos_policy->isHideZeroDiscountFields();
$discounts_total = 0;
if (!empty($invItems)) {
    foreach ($invItems as $key => $item) {
        $per = number_format(0, $dPlace);
        if ($item['discountPer'] > 0) {
            $per = number_format(($item['discountAmount']) * $item['qty'], $dPlace);
        }
        $discounts_total += $per;
    }
}
?>
<table id="itemTable" width="100%" style="border: 1px solid black; margin-top: 5px;">
    <tr>
        <th class="borderlefttopnot" style="text-align: left !important; border-left: none;">#</th>
        <th class="borderlefttopnot" style="width: 160px; text-align: center !important;">    وصف</th>
        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">   الكمية </th>
        <th class="borderlefttopnot" style="text-align: center !important;">  السعر </th>
        <?php
        if ($isHideZeroDiscountFields) {
            if ($discounts_total != 0) {
                echo '<th class="borderlefttopnot" style="text-align: center !important;">  خصم </th>';
            }
        } else {
            echo '<th class="borderlefttopnot" style="text-align: center !important;">  خصم </th>';
        }
        ?>

        <?php if($isGroupBasedPolicy == 1){?>
            <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">VAT</th>
            <?php if($isOtherTaxExist > 0) {?>
                <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">ضريبة أخرى</th>
            <?php }?>
        <?php }?>

        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">   القيمة </th>
    </tr>
    <tbody>
    <?php
    $items = 0;
    $total_transactionAmountBeforeDiscount = 0;
    $totalTaxAmount = 0;
    $VATTaxAmount = 0;
    if (!empty($invItems)) {
        foreach ($invItems as $key => $item) {
            $totalTaxAmount += ($item['taxAmount'] - $item['amount']);
            $VATTaxAmount += ($item['amount']);
            $total_transactionAmountBeforeDiscount += $item['transactionAmountBeforeDiscount'];

            $umo = $item['unitOfMeasure'];
            $per = number_format(0, $dPlace);
            if ($item['discountPer'] > 0) {
                $per = number_format(($item['discountAmount'] ) * $item['qty'], $dPlace);
            }

            echo '
      <tr>
        <td style="border-left: none;" class="borderlefttopbottomnot">' . ($key + 1) . '</td>
        <td class="borderlefttopbottomnot">' . $item['itemDescription'] . '</td>
        <td class="borderlefttopbottomnot" align="right">' . $item['qty'] . '</td>
        <td class="borderlefttopbottomnot" align="right">' . number_format($item['price'], $dPlace) . '</td>';
            if ($isHideZeroDiscountFields) {
                if ($discounts_total != 0) {
                    echo '<td class="borderlefttopbottomnot" align="right">' . $per . '</td>';
                }
            }else{
                echo '<td class="borderlefttopbottomnot" align="right">' . $per . '</td>';
            }
            if($isGroupBasedPolicy ==1){
                echo '<td style="border-left: none;" class="borderlefttopbottomnot" align="right">('.number_format($item['taxPercentage'],2).'%)&nbsp; '.number_format($item['amount'],$dPlace).'</td>';
                if($isOtherTaxExist > 0) {
                    echo '<td style="border-left: none;" class="borderlefttopbottomnot" align="right">' .number_format(($item['taxAmount']-$item['amount']), $dPlace) . '</td>';
                }
                echo '<td style="border-left: none;" class="borderlefttopbottomnot" align="right">' . number_format($item['transactionAmountBeforeDiscount'] + $item['taxAmount'], $dPlace) . '</td>';
            }else {
                echo '<td style="border-left: none;" class="borderlefttopbottomnot" align="right">' . number_format($item['transactionAmountBeforeDiscount'], $dPlace) . '</td>';
            }
        echo '</tr>';
            $items = $items + 1;
        }
    }

    $rowspan=3;
    if ($invMaster['generalDiscountAmount'] > 0 && $invMaster['promotiondiscountAmount'] > 0){
        $rowspan=4;
    }else if($invMaster['promotiondiscountAmount'] > 0){
        $rowspan=4;
    }else if($invMaster['generalDiscountAmount'] > 0){
        $rowspan=5;
    }else if($isGroupBasedPolicy  == 1){
        $rowspan = 4;
        if($isOtherTaxExist > 0) {
            $rowspan = 5;
        }
    }
    ?>


    <tr>
        <td class="borderleftnot" colspan="2" rowspan="<?php echo $rowspan; ?>" style="border-bottom: none;">&nbsp; الإجمالي
            - <?php echo $items ?> <br> &nbsp;  بواسطة &nbsp;   : <?php echo $invMaster['repName']; ?></td>
        <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>&nbsp; الإجمالي </b></td>
        <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
            style="text-align: right;"><?php echo number_format($total_transactionAmountBeforeDiscount, $dPlace) ?></td>
    </tr>
    <?php if ($invMaster['generalDiscountAmount'] > 0){?>
    <tr>
        <td class="borderlefttopnot" colspan="<?php echo $colspan?>"><b> &nbsp;  خصم </b></td>
        <td class="borderlefttopnot" colspan="<?php echo $colspanForValue?>"
            style="text-align: right;"><?php echo number_format($invMaster['generalDiscountAmount'], $dPlace); ?></td>
    </tr>
    <?php }?>

    <?php if($isGroupBasedPolicy == 1){?>
        <tr>
            <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>VAT</b></td>
            <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                style="text-align: right;"><?php echo number_format($VATTaxAmount, $dPlace) ?></td>
        </tr>
        <?php if($isOtherTaxExist > 0) { ?>
            <tr>
                <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>ضريبة أخرى</b></td>
                <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                    style="text-align: right;"><?php echo number_format($totalTaxAmount, $dPlace) ?></td>
            </tr>
        <?php  }?>
    <?php }?>

    <tr>
        <td class="borderlefttopnot" colspan="<?php echo $colspan?>"><b>&nbsp;  الصافي </b></td>
        <td class="borderlefttopnot" colspan="<?php echo $colspanForValue?>"
            style="text-align: right;"><?php echo number_format($invMaster['netTotal'], $dPlace) ?></td>
    </tr>
    <?php
    $cn=1;
    foreach ($gPosPayMethods as $gppm){
        if ($invMaster['generalDiscountAmount'] > 0){
            echo '
                <tr>
                    <td class="" colspan="2">&nbsp;</td>
                    <td class="borderlefttopnot" colspan="'.$colspan.'" style="border-left: 1px solid black;"><b>'. $gppm['description'] .' &nbsp;</b></td>
                    <td class="borderlefttopnot" colspan="'.$colspanForValue.'" style="text-align: right;">' . number_format($gppm['amount'], $dPlace) . '</td>
                </tr>';
        }else{
            if($cn==1){
                echo '
            <tr>
                
                <td class="borderlefttopnot" colspan="'.$colspan.'" style="border-left: 1px solid black;"><b>'. $gppm['description'] .' &nbsp;</b></td>
                <td class="borderlefttopnot" colspan="'.$colspanForValue.'" style="text-align: right;">' . number_format($gppm['amount'], $dPlace) . '</td>
            </tr>';
            }else{
                echo '
            <tr>
                <td class="" colspan="'.$colspan.'">&nbsp;</td>
                <td class="borderlefttopnot" colspan="'.$colspan.'" style="border-left: 1px solid black;"><b>'. $gppm['description'] .' &nbsp;</b></td>
                <td class="borderlefttopnot" colspan="'.$colspanForValue.'" style="text-align: right;">' . number_format($gppm['amount'], $dPlace) . '</td>
            </tr>';
            }

        }
        $cn++;
    }

//    if ($invMaster['cashAmount'] != 0) {
//        echo '
//    <tr>
//        <td class="" colspan="2">&nbsp;</td>
//        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b> &nbsp;  كاش </b></td>
//        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['cashAmount'], $dPlace) . '</td>
//    </tr>';
//    }
//    if ($invMaster['chequeAmount'] != 0) {
//        echo '
//    <tr>
//    <td class="" colspan="2">&nbsp;</td>
//        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>&nbsp; التحقق من </b></td>
//        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['chequeAmount'], $dPlace) . '</td>
//    </tr>';
//    }
//    if ($invMaster['cardAmount'] != 0) {
//        echo '
//    <tr>
//    <td class="" colspan="2">&nbsp;</td>
//        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Card &nbsp;  بطاقة </b></td>
//        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['cardAmount'], $dPlace) . '</td>
//    </tr>';
//    }
//    if ($invMaster['creditNoteAmount'] != 0) {
//        echo '
//    <tr>
//    <td class="" colspan="2">&nbsp;</td>
//        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Credit Note :   إشعار خصم ' . $returnDet . '</b></td>
//        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['creditNoteAmount'], $dPlace) . '</td>
//    </tr>';
//    }
//    if ($invMaster['creditSalesAmount'] != 0) {
//        echo '
//    <tr>
//    <td class="" colspan="2">&nbsp;</td>
//        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Credit Sales &nbsp; مبيعات الائتمان </b></td>
//        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['creditSalesAmount'], $dPlace) . '</td>
//    </tr>';
//    }
    ?>

    <tr>
        <td class="borderlefttopnot" colspan="2">&nbsp;</td>
        <?php
        $Balancetxt = '&nbsp; الباقي ';

        ?>
        <td colspan="<?php echo $colspan?>" style="padding-left: 2px;"><b><?php echo $Balancetxt ?></b></td>
        <?php
        $amn = abs($invMaster['netTotal'] - $invMaster['paidAmount']);
        ?>
        <td colspan="<?php echo $colspan?>" style="text-align: right;"><?php echo number_format($amn, $dPlace) ?></td>

    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="6" style="height: 50px;"></td>
    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="6"
            align="center"><?php echo $outletInfo['warehouseAddress'] ?><br>
            Tel : <?php echo $outletInfo['warehouseTel'] ?> <br>
            <b><?php echo $this->common_data['company_data']['companyPrintTagline'] ?></b></td>
    </tr>
    </tbody>
    <table width="100%" style="">
        <br>
        <tr><td align="center" style="font-size: 14px !important; font-weight: 600;">  <?php echo $outletInfo['pos_footNote'] ?></td></tr>
    </table>

</body>
</html>