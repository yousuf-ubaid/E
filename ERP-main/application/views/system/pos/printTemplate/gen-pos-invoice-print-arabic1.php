<html>
<head>
    <title>Invoice Print</title>
    <style type="text/css">
        #itemTable th {
            text-align: right !important;
            font-size: 12px;
            font-weight: 400;
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
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
            border: 1px solid #fff;
            border-left: none;
            border-top: none;
            border-bottom: none;
            padding-left: 2px;
        }

        .borderlefttopnot {
            /*border: 1px solid black;*/
            border-left: none;
            border-top: none;
            padding-left: 2px;
        }

        .borderleftnot {
            /*border: 1px solid black;*/
            border-left: none;
            padding-left: 2px;
        }
        .a-right{
            text-align: right !important;
        }
        .f11{
            font-size: 11px !important;
        }
        .payref{
            float: right;
        }
        .border-top-1{
            border-top: 1px solid #000;
        }
    </style>
</head>

<body onload="/*window.print()*/">

<?php
$outletInfo = get_outletInfo();
$invMaster = $invData[1];
$invItems =  $invData[2];
$dPlace = $invMaster['transactionCurrencyDecimalPlaces'];
//$companylogo = base_url() . 'images/logo/' . $this->common_data['company_data']['company_logo'];
//$companylogo = base_url() . 'uploads/warehouses/' . $outletInfo['warehouseImage'];
$companylogo = get_s3_url($outletInfo['warehouseImage']);
$isGroupBasedPolicy =  $group_based_tax = getPolicyValues('GBT', 'All');
$colspan = '2';
$colspan_bill = '6';
$colspanForValue = '2';
$vatIN_view = '';
if($isGroupBasedPolicy ==1){
    $vatIN = $this->db->query("SELECT IFNULL(companyVatNumber, '-') as companyVatNumber FROM srp_erp_company WHERE company_id = " . current_companyID())->row('companyVatNumber');
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
echo '<table width="100%" style="">
            <tr>
                <td align="center" style="padding-bottom: 10px;"> <img alt="Logo" style="height: 60px" src="' . $companylogo . '"></td>
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

if($invMaster['referenceNo']!=''){
    $refNoArray = explode("-",$invMaster['referenceNo']);
    if(isset($refNoArray[1])){
        $refNo=trim($refNoArray[1]," ");
        $ref = '<td style=" font-size: 11px !important"><b>المرجعي </b></td>
                <td style=" font-size: 11px !important"><b>:</b></td>
                <td style=" font-size: 11px !important">'.$refNo.'</td>';
    }else{
        $ref = '<td style=" font-size: 11px !important"> </td>
                <td style=" font-size: 11px !important"> </td>
                <td style=" font-size: 11px !important"> </td>';
    }
}else{
    $ref = '<td style=" font-size: 11px !important"> </td>
                <td style=" font-size: 11px !important"> </td>
                <td style=" font-size: 11px !important"> </td>';
}

echo '<table width="100%" style="">
                <tr>
                    <td width="50%" style="padding-bottom: 8px;">
                        <table width="100%">
                            <tr>
                                <td style=" font-size: 11px !important"><b>  زبون </b></td>
                                <td style=" font-size: 11px !important"><b>:&nbsp;</b></td>
                                <td style=" font-size: 11px !important">' . $cus . '</td>
                            </tr>    
                            <tr>
                            ' . $ref . '
                                           
                            </tr>'. $vatIN_view.'
                        </table>
                    </td>
                    <td width="50%" style="padding-bottom: 8px;">
                        <table width="100%">
                            <tr>
                                <td style=" font-size: 11px !important"><b>  تاريخ</b></td>
                                <td style=" font-size: 11px !important"><b>:&nbsp;</b></td>
                                <td style=" font-size: 11px !important">' . date("d/m/Y h:i:sa", strtotime($invMaster['createdDateTime'])) . '</td>
                            </tr>
                            <tr>
                                <td style=" font-size: 11px !important"><b>  رقم الفاتورة </b></td>
                                <td style=" font-size: 11px !important"><b>:&nbsp;</b></td>
                                <td style=" font-size: 11px !important">' . $doSysCode_refNo . '</td>
                            </tr>
                        </table>
                    </td>
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
<table id="itemTable" width="100%" style="margin-top: 5px;">
    <tr>
        <th class="borderlefttopnot" style="text-align: left !important; border-left: none;">#</th>
        <th class="borderlefttopnot" style="width: 250px; text-align: center !important;">    وصف</th>
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
                <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Oth Tax</th>
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

            $total_transactionAmountBeforeDiscount += $item['transactionAmountBeforeDiscount'];
            $totalTaxAmount += ($item['taxAmount'] - $item['amount']);
            $VATTaxAmount += ($item['amount']);

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
                    echo '<td class="borderlefttopbottomnot" align="right">('.number_format($item['discountPer'], 2).'%)&nbsp; ' . $per . '</td>';
                }
            } else {
                echo '<td class="borderlefttopbottomnot" align="right">('.number_format($item['discountPer'], 2).'%)&nbsp; '. $per . '</td>';
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
        $footerColSpan = 8;
    }else if($invMaster['promotiondiscountAmount'] > 0){
        $rowspan=4;
        $footerColSpan = 8;
    }else if($invMaster['generalDiscountAmount'] > 0){
        $rowspan=5;
        $footerColSpan = 8;
    }else if($isGroupBasedPolicy  == 1){
        $rowspan = 4;
        if($isOtherTaxExist > 0) {
            $rowspan = 5;
            $footerColSpan = 8;
        }else {
            $footerColSpan = 7;
        }
    }else {
        $footerColSpan = 8;
    }

    ?>


    <tr class="border-top-1">
        <td class="borderleftnot" colspan="2" rowspan="<?php echo $rowspan; ?>" style="border-bottom: none;">&nbsp; الإجمالي
            - <?php echo $items ?> <br> &nbsp;  بواسطة &nbsp;   : <?php echo $invMaster['repName']; ?></td>
        <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>&nbsp; الإجمالي </b></td>
        <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
            style="text-align: right;"><?php echo number_format($total_transactionAmountBeforeDiscount, $dPlace) ?></td>
    </tr>

    <?php if($isGroupBasedPolicy == 1){?>
        <tr>
            <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>VAT</b></td>
            <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                style="text-align: right;"><?php echo number_format($VATTaxAmount, $dPlace) ?></td>
        </tr>
        <?php if($isOtherTaxExist > 0) { ?>
            <tr>
                <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>Oth Tax &nbsp;   ضريبة أخرى</b></td>
                <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                    style="text-align: right;"><?php echo number_format($totalTaxAmount, $dPlace) ?></td>
            </tr>
        <?php  }?>
    <?php }?>

    <?php if ($invMaster['generalDiscountAmount'] > 0){?>
    <tr>
        <td class="borderlefttopnot" colspan="<?php echo $colspan; ?>"><b> &nbsp;  خصم </b></td>
        <td class="borderlefttopnot" colspan="<?php echo $colspanForValue; ?>"
            style="text-align: right;"><?php echo number_format($invMaster['generalDiscountAmount'], $dPlace); ?></td>
    </tr>
    <?php }?>
    <tr>
        <td class="borderlefttopnot" colspan="<?php echo $colspan; ?>"><b>&nbsp;  الصافي </b></td>
        <td class="borderlefttopnot" colspan="<?php echo $colspanForValue; ?>"
            style="text-align: right;"><?php echo number_format($invMaster['netTotal'], $dPlace) ?></td>
    </tr>

    <?php
    $cn = 1;
    foreach ($gPosPayMethods as $gppm) {
        if ($invMaster['generalDiscountAmount'] > 0) {
            echo '
                <tr>
                    <td class="" colspan="2">&nbsp;</td>
                    <td class="borderlefttopnot" colspan="' . $colspan . '" style=""><b>' . $gppm['description'] .'<span class="payref">'.$gppm['reference'].'</span></b></td>
                    <td class="borderlefttopnot" colspan="' . $colspanForValue . '" style="text-align: right;">' . number_format($gppm['amount'], $dPlace) . '</td>
                </tr>';
        } else {
            if ($cn == 1) {
                echo '
            <tr>                
                <td class="borderlefttopnot" colspan="' . $colspan . '" style=""><b>' . $gppm['description'] .'<span class="payref">'.$gppm['reference'].'</span></b></td>
                <td class="borderlefttopnot" colspan="' . $colspanForValue . '" style="text-align: right;">' . number_format($gppm['amount'], $dPlace) . '</td>
            </tr>';
            } else {
                echo '
            <tr>
                <td class="" colspan="2">&nbsp;</td>
                <td class="borderlefttopnot" colspan="' . $colspan . '" style=""><b>' . $gppm['description'] .'<span class="payref">'.$gppm['reference'].'</span></b></td>
                <td class="borderlefttopnot" colspan="' . $colspanForValue . '" style="text-align: right;">' . number_format($gppm['amount'], $dPlace) . '</td>
            </tr>';
            }

        }
        $cn++;
    }
    ?>

    <tr>
        <td class="borderlefttopnot" colspan="2">&nbsp;</td>
        <?php
        $Balancetxt = '&nbsp; الباقي ';

        ?>
        <td colspan="<?php echo $colspan; ?>" style="padding-left: 2px;"><b><?php echo $Balancetxt ?></b></td>
        <?php
        $amn = abs($invMaster['netTotal'] - $invMaster['paidAmount']);
        ?>
        <td colspan="<?php echo $colspan; ?>" style="text-align: right;"><?php echo number_format($amn, $dPlace) ?></td>

    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="<?php echo $footerColSpan; ?>" style="height: 50px;"><?php echo $outletInfo['pos_footNote'] ?></td>
    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="<?php echo $footerColSpan; ?>"
            align="center"><?php echo $outletInfo['warehouseAddress'] ?><br>
            Tel : <?php echo $outletInfo['warehouseTel'] ?> <br>
            <b><?php echo $this->common_data['company_data']['companyPrintTagline'] ?></b></td>
    </tr>
    </tbody>

</body>
</html>