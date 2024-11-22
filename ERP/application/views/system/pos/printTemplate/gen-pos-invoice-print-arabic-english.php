<html>
<head>
    <title>Invoice Print</title>
    <style type="text/css">
        #itemTable th {
            text-align: right !important;
            font-size: 12px;
            font-weight:400;
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

        .a-right {
            text-align: right !important;
        }

        .f11 {
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
$companylogo = get_s3_url($outletInfo['warehouseImage']);
$companyInfo = get_companyInfo();
$isGroupBasedPolicy =  $group_based_tax = getPolicyValues('GBT', 'All');
$colspan = '2';
$colspan_bill = '6';
$colspanForValue = '2';
$vatIN_view = '';
if($companyInfo['vatRegisterYN']==1){
    $vatIN = $this->db->query("SELECT IFNULL(companyVatNumber, '-') as companyVatNumber FROM srp_erp_company WHERE company_id = " . current_companyID())->row('companyVatNumber');
    $vatIN_view = '<td style=" font-size: 11px !important"><b>VAT IN </b></td>
                <td style=" font-size: 11px !important"><b>: </b></td>
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

if ($isHideZeroDiscountFields) {
    if ($discounts_total != 0) {
        
    }else{
        $colspanForValue = $colspanForValue-1;
        $colspan_bill = $colspan_bill-1;
    }
}

$styleChangePolicy = getPolicyValues('EXINV', 'All'); //
if($styleChangePolicy == 1){

//$companylogo = base_url() . 'uploads/warehouses/' . $outletInfo['warehouseImage'];
  echo '<table width="100%" style="">
            <tr>
                <td align="center" style="padding-bottom: 10px;"> <img alt="Logo" style="height: 60px" src="' . $companylogo . '"></td>
            </tr>
            <tr><td align="center" style="font-size: 14px !important; font-weight: 600;padding-bottom:3px;;">' . $outletInfo['wareHouseDescription'] . '</td></tr>
           </table>';
            /*<tr><td align="center" style="font-size: 14px !important; font-weight: 600;padding-bottom:3px;;">INVOICE &nbsp;&nbsp;  فاتورة</td></tr>*/
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
                    $ref = '<td style=" font-size: 11px !important"><b>Ref  المرجعي</b></td>
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
                                <td style=" font-size: 11px !important"><b>Customer &nbsp;   زبون </b></td>
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
                                <td style=" font-size: 11px !important"><b>Date &nbsp;   تاريخ</b></td>
                                <td style=" font-size: 11px !important"><b>:&nbsp;</b></td>
                                <td style=" font-size: 11px !important">' . date("d/m/Y h:i:sa", strtotime($invMaster['createdDateTime'])) . '</td>
                            </tr>
                            <tr>
                                <td style=" font-size: 11px !important"><b>Invoice No &nbsp;   رقم الفاتورة </b></td>
                                <td style=" font-size: 11px !important"><b>:&nbsp;</b></td>
                                <td style=" font-size: 11px !important">' . $invData[1]['invoiceCode'] . '</td>
                            </tr>
                        </table>
                    </td>
                </tr>           
           </table>';
  ?>


<table id="itemTable" width="100%" style="margin-top: 5px;">
    <tr>
        <th class="borderlefttopnot" style="text-align: left !important; border-left: none;">#</th>
        <th class="borderlefttopnot" style="width: 250px; text-align: center !important;">Description &nbsp; وصف</th>
        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Qty &nbsp; الكمية</th>
        <th class="borderlefttopnot" style="text-align: center !important;">Price &nbsp; السعر</th>
        <?php
        if ($isHideZeroDiscountFields) {
            if ($discounts_total != 0) {
                echo '<th class="borderlefttopnot" style="text-align: center !important;">Discount &nbsp;  خصم </th>';
            }
        } else {
            echo '<th class="borderlefttopnot" style="text-align: center !important;">Discount &nbsp;  خصم </th>';
        }
        ?>

        <?php if($isGroupBasedPolicy == 1){?>
            <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">VAT</th>
            <?php if($isOtherTaxExist > 0) {?>
                <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Other Tax &nbsp;   ضريبة أخرى</th>
            <?php }?>
        <?php }?>

        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Amount &nbsp; القيمة</th>
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
                    $per = number_format(($item['discountAmount']) * $item['qty'], $dPlace);
                }

                echo '
                <tr>
                    <td style="border-left: none;" class="borderlefttopbottomnot">' . ($key + 1) . '</td>
                    <td class="borderlefttopbottomnot">' . $item['seconeryItemCode'] . ' - ' . $item['itemDescription'] . '</td>
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
        $footerColSpan=$colspan_bill;
        if ($invMaster['generalDiscountAmount'] > 0 && $invMaster['promotiondiscountAmount'] > 0){
            //$rowspan=4;
            //$footerColSpan = 8;
        }else if($invMaster['promotiondiscountAmount'] > 0){
            //$rowspan=4;
            //$footerColSpan = 8;
        }else if($invMaster['generalDiscountAmount'] > 0){
        // $rowspan=5;
        // $footerColSpan = 8;
        }
        
        if($isGroupBasedPolicy  == 1){
            $rowspan = $rowspan+1;
            if($isOtherTaxExist > 0) {
                $rowspan = $rowspan+1;
                $footerColSpan = 8;
            }else {
                $footerColSpan = 7;
            }
        }else {
            $footerColSpan = 8;
        }
        ?>

        <tr class="border-top-1">
            <td class="borderleftnot" colspan="2" rowspan="<?php echo $rowspan; ?>" style="border-bottom: none;">Total Items &nbsp; الإجمالي
                - <?php echo $items ?> <br> Created by &nbsp; بواسطة &nbsp; : <?php echo $invMaster['repName']; ?></td>
            <td class="borderleftnot" colspan="<?php echo $colspan?>" style="padding-top: 3px;"><b>Sub Total &nbsp; الإجمالي </b></td>
            <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                style="text-align: right;padding-top: 3px;"><?php echo number_format($total_transactionAmountBeforeDiscount, $dPlace) ?></td>
        </tr>

        <?php if($isGroupBasedPolicy == 1){?>
            <tr>
                <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>VAT</b></td>
                <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                    style="text-align: right;"><?php echo number_format($VATTaxAmount, $dPlace) ?></td>
            </tr>
            <?php if($isOtherTaxExist > 0) { ?>
                <tr>
                    <td class="borderleftnot" colspan="<?php echo $colspan?>"><b>Other Tax &nbsp;   ضريبة أخرى</b></td>
                    <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                        style="text-align: right;"><?php echo number_format($totalTaxAmount, $dPlace) ?></td>
                </tr>
            <?php  }?>
        <?php }?>

        <?php if ($invMaster['generalDiscountAmount'] > 0) {
            ?>
            <tr>
                <td class="borderlefttopnot" colspan="<?php echo $colspan?>"><b>Discount &nbsp; خصم </b></td>
                <td class="borderlefttopnot" colspan="<?php echo $colspanForValue?>"
                    style="text-align: right;"><?php echo number_format($invMaster['generalDiscountAmount'], $dPlace); ?></td>
            </tr>
        <?php } ?>

        <tr>
            <td class="borderlefttopnot" colspan="<?php echo $colspan?>"><b>Grand Total &nbsp; الصافي </b></td>
            <td class="borderlefttopnot" colspan="<?php echo $colspanForValue?>"
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
            $Balancetxt = 'Change &nbsp; الباقي ';

            ?>
            <td colspan="<?php echo $colspan?>" style="padding-left: 2px;"><b><?php echo $Balancetxt ?></b></td>
            <?php
            $amn = abs($invMaster['netTotal'] - $invMaster['paidAmount']);
            ?>
            <td colspan="<?php echo $colspanForValue?>" style="text-align: right;"><?php echo number_format($amn, $dPlace) ?></td>

        </tr>
        <tr>
            <td class="borderlefttopnot" colspan="<?php echo $colspan_bill; ?>" style="height: 50px;"><?php echo $outletInfo['pos_footNote'] ?></td>
        </tr>
        <tr>
            <td class="borderlefttopnot" colspan="<?php echo $colspan_bill; ?>"
                align="center"><?php echo $outletInfo['warehouseAddress'] ?><br>
                Tel : <?php echo $outletInfo['warehouseTel'] ?>
                <br><b><?php echo $this->common_data['company_data']['companyPrintTagline'] ?></b></td>
        </tr>
    </tbody>
</table>    
<?php
}else{ 
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
            $cusTelNo = "";
            $cusEmail = "";
            $cusAddress = "";
            if ($invMaster['customerID'] > 0) {                
                $cusid = $invMaster['customerID'];
                $custdetails = $this->db->query("SELECT customerTelephone,customerAddress1,customerEmail FROM srp_erp_customermaster WHERE  customerAutoID=$cusid")->row_array();
                $cus = $invMaster['cusName'] . '<br>' . $custdetails['customerTelephone'];

                $cusTelNo = $custdetails['customerTelephone'];
                $cusEmail = $custdetails['customerEmail'];
                $cusAddress = $custdetails['customerAddress1'];
            } else {
                $cus = 'Cash';
            }
    ?>
    <table width="100%">
      <tbody>
        <tr>
          <td width="50%" align="left">
            <table width="100%">
              <tbody>
                <tr>
                  <td align="left" style="text-align: left;">
                    <img alt="Logo" style="height: 60px" src="<?php echo $companylogo; ?>">
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td width="50%" align="right">
            <table width="100%">
              <tbody>
                <tr>
                  <td align="right" style="text-align: right;">
                    <img alt="Logo" style="height: 60px" src="<?php echo $companylogo; ?>">
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
    <table width="100%">
      <tbody>
        <tr>
          <td width="35%">
            <table width="100%">
              <tbody>
                <tr>
                  <td style="font-size: 10px !important;text-align: left;"><?php echo $companyAddress; ?> </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td width="30%">
            <table width="100%">
              <tbody>
                <tr>
                  <td align="center" style="font-size: 12px !important;font-weight: 600;text-align: center;">فاتورة ضريبية</td>
                </tr>
                <tr>
                  <td align="center" style="font-size: 14px !important;font-weight: 600;text-align: center;">TAX INVOICE</td>
                </tr>
                <!--<tr>
                  <td align="center" style="font-size: 10px !important;font-weight: 600;text-align: center;">TAX INVOICE</td>
                </tr>-->
              </tbody>
            </table>
          </td>
          <td width="35%">
            <table width="100%">
              <tbody>
                <tr>
                  <td style="font-size: 10px !important;text-align: right;" dir="rtl"><?php echo $companyAddress; ?></td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
    <table width="100%">
      <tbody>
        <tr>
          <td style="height: 10px;font-size: 0 !important;">&nbsp;</td>
        </tr>
      </tbody>
    </table>
    <table width="100%">
      <tbody>
        <tr>
          <td align="right" style="font-size: 10px !important;font-weight: 600;text-align: right;">ACCOUNT COPY</td>
        </tr>
      </tbody>
    </table>
    <table width="100%">
      <tbody>
        <tr>
          <td width="40%" style="padding-bottom: 8px;">
            <table width="100%" style="border: 1px solid #000;border-radius: 5px;">
              <tbody>
                <tr style="border-bottom: 1px solid #000;">
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">Customer Name</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important;">اسم الزبون</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important"><?php echo $cus; ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #000;">
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">Contact No.</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important">رقم الاتصال.</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important"><?php echo $cusTelNo; ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #000;">
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">Email</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important">بريد إلكتروني</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important"><?php echo $cusEmail; ?></td>
                </tr>
                <tr>
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">Address</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important">عنوان</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important"><?php echo $cusAddress; ?></td>
                </tr>
              </tbody>
            </table>
          </td>
          <td width="20%" style="padding-bottom: 8px;">
            <table width="100%">
              <tbody>
                <tr>
                  <td align="center" style="font-size: 10px !important;font-weight: 600;padding-bottom:3px;">&nbsp;</td>
                </tr>
              </tbody>
            </table>
          </td>
          <td width="40%" style="padding-bottom: 8px;">
            <table width="100%" style="border: 1px solid #000;border-radius: 5px;">
              <tbody>
                <tr style="border-bottom: 1px solid #000;">
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">POS No</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important;">رقم نقطة البيع</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important"><?php echo $invData[1]['invoiceCode']; ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #000;">
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">Phone No</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important">رقم الهاتف</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important">0502563790</td>
                </tr>
                <tr style="border-bottom: 1px solid #000;">
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">Date &amp; Time</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important">التاريخ والوقت</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important"><?php echo date("d/m/Y h:i:sa", strtotime($invMaster['createdDateTime'])); ?></td>
                </tr>
                <tr>
                  <td style="font-size: 11px !important;padding: 0px 5px;">
                    <table width="100%">
                      <tbody>
                        <tr>
                          <td align="left" style="font-size: 10px !important">Salesman</td>
                        </tr>
                        <tr>
                          <td align="left" style="font-size: 8px !important">بائع</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="font-size: 11px !important">
                    <b>:&nbsp;</b>
                  </td>
                  <td style="font-size: 11px !important">-</td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
    <table id="itemTable" width="100%" style="margin-top: 5px;">
        <tr>
            <th style="text-align: center !important;border: 1px solid #000;border-left: 1px solid #000; background: #ddd;">
                <table width="100%">
                <tbody>
                    <tr>
                    <td align="center" style="font-size: 9px !important;">لا.</td>
                    </tr>
                    <tr>
                    <td align="center" style="font-size: 11px !important">No.</td>
                    </tr>
                </tbody>
                </table>
            </th>
            <th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                <table width="100%">
                <tbody>
                    <tr>
                    <td align="center" style="font-size: 9px !important;"> وصف</td>
                    </tr>
                    <tr>
                    <td align="center" style="font-size: 11px !important">Description</td>
                    </tr>
                </tbody>
                </table>
            </th>
            <th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                <table width="100%">
                <tbody>
                    <tr>
                    <td align="center" style="font-size: 9px !important;">الكمية</td>
                    </tr>
                    <tr>
                    <td align="center" style="font-size: 11px !important">Qty.</td>
                    </tr>
                </tbody>
                </table>
            </th>
            <th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                <table width="100%">
                <tbody>
                    <tr>
                    <td align="center" style="font-size: 9px !important;">السعر</td>
                    </tr>
                    <tr>
                    <td align="center" style="font-size: 11px !important">Price</td>
                    </tr>
                </tbody>
                </table>
            </th>
                        
            <?php
            if ($isHideZeroDiscountFields) {
                if ($discounts_total != 0) {
                    echo '<th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                        <table width="100%">
                            <tbody>
                                <tr>
                                <td align="center" style="font-size: 9px !important;">تخفيض</td>
                                </tr>
                                <tr>
                                <td align="center" style="font-size: 11px !important">Discount</td>
                                </tr>
                            </tbody>
                        </table>
                    </th>';
                }
            } else {
                echo '<th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                        <table width="100%">
                            <tbody>
                                <tr>
                                <td align="center" style="font-size: 9px !important;">تخفيض</td>
                                </tr>
                                <tr>
                                <td align="center" style="font-size: 11px !important">Discount</td>
                                </tr>
                            </tbody>
                        </table>
                    </th>';
            }
            ?>

            <?php if($isGroupBasedPolicy == 1){?>                
                <th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                    <table width="100%">
                    <tbody>
                        <tr>
                        <td align="center" style="font-size: 9px !important;">ضريبة</td>
                        </tr>
                        <tr>
                        <td align="center" style="font-size: 11px !important">VAT</td>
                        </tr>
                    </tbody>
                    </table>
                </th>
                <?php if($isOtherTaxExist > 0) {?>
                    <th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                        <table width="100%">
                        <tbody>
                            <tr>
                            <td align="center" style="font-size: 9px !important;">ضريبة أخرى</td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size: 11px !important">Other Tax</td>
                            </tr>
                        </tbody>
                        </table>
                    </th>                    
                <?php }?>
            <?php }?>

            <th style="text-align: center !important;border: 1px solid #000;background: #ddd;">
                <table width="100%">
                <tbody>
                    <tr>
                    <td align="center" style="font-size: 9px !important;">القيمة</td>
                    </tr>
                    <tr>
                    <td align="center" style="font-size: 11px !important">Amount</td>
                    </tr>
                </tbody>
                </table>
            </th>
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
                        $per = number_format(($item['discountAmount']) * $item['qty'], $dPlace);
                    }

                    echo '
                    <tr>
                        <td style="border-right: 1px solid #000;border-left: 1px solid #000;text-align: center;font-size: 11px;" class="borderlefttopbottomnot">' . ($key + 1) . '</td>
                        <td style="border-right: 1px solid #000;padding-left: 2px;font-size: 11px;" class="borderlefttopbottomnot">' . $item['seconeryItemCode'] . ' - ' . $item['itemDescription'] . '</td>
                        <td style="border-right: 1px solid #000;font-size: 11px;font-size: 11px;" class="borderlefttopbottomnot" align="center">' . $item['qty'] . '</td>
                        <td style="border-right: 1px solid #000;padding-right: 2px;font-size: 11px;" class="borderlefttopbottomnot" align="right">' . number_format($item['price'], $dPlace) . '</td>';

                    if ($isHideZeroDiscountFields) {
                        if ($discounts_total != 0) {
                            echo '<td style="border-right: 1px solid #000;padding-right: 2px;font-size: 11px;" class="borderlefttopbottomnot" align="right">('.number_format($item['discountPer'], 2).'%)&nbsp; ' . $per . '</td>';
                        }
                    } else {
                        echo '<td style="border-right: 1px solid #000;padding-right: 2px;font-size: 11px;" class="borderlefttopbottomnot" align="right">('.number_format($item['discountPer'], 2).'%)&nbsp; '. $per . '</td>';
                    }

                    if($isGroupBasedPolicy ==1){
                        echo '<td style="border-right: 1px solid #000;padding-right: 2px;font-size: 11px;" class="borderlefttopbottomnot" align="right">('.number_format($item['taxPercentage'],2).'%)&nbsp; '.number_format($item['amount'],$dPlace).'</td>';
                        if($isOtherTaxExist > 0) {
                            echo '<td style="border-right: 1px solid #000;padding-right: 2px;font-size: 11px;" class="borderlefttopbottomnot" align="right">' .number_format(($item['taxAmount']-$item['amount']), $dPlace) . '</td>';
                        }
                        echo '<td style="border-right: 1px solid #000;padding-right: 2px;font-size: 11px;" class="borderlefttopbottomnot" align="right">' . number_format($item['transactionAmountBeforeDiscount'] + $item['taxAmount'], $dPlace) . '</td>';
                    }else {
                        echo '<td style="border-right: 1px solid #000;padding-right: 2px;font-size: 11px;" class="borderlefttopbottomnot" align="right">' . number_format($item['transactionAmountBeforeDiscount'], $dPlace) . '</td>';
                    }
                    echo '</tr>';
                    $items = $items + 1;
                }
            }

            $rowspan=3;
            $footerColSpan=$colspan_bill;
            if ($invMaster['generalDiscountAmount'] > 0 && $invMaster['promotiondiscountAmount'] > 0){
                //$rowspan=4;
                //$footerColSpan = 8;
            }else if($invMaster['promotiondiscountAmount'] > 0){
                //$rowspan=4;
                //$footerColSpan = 8;
            }else if($invMaster['generalDiscountAmount'] > 0){
            // $rowspan=5;
            // $footerColSpan = 8;
            }
            
            if($isGroupBasedPolicy  == 1){
                $rowspan = $rowspan+1;
                if($isOtherTaxExist > 0) {
                    $rowspan = $rowspan+1;
                    $footerColSpan = 8;
                }else {
                    $footerColSpan = 7;
                }
            }else {
                $footerColSpan = 8;
            }
            ?>

            <tr class="border-top-1">
                <td class="borderleftnot" colspan="2" rowspan="<?php echo $rowspan; ?>" style="border-bottom: none;font-size: 11px;border-top: 1px solid #000;">Total Items &nbsp; الإجمالي
                    - <?php echo $items ?> <br> </td>
                <td class="borderleftnot" colspan="<?php echo $colspan?>" style="padding-top: 3px;font-size: 11px;border-top: 1px solid #000;"><b>Sub Total &nbsp; الإجمالي </b></td>
                <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                    style="text-align: right;padding-top: 3px;font-size: 11px;border-top: 1px solid #000;"><?php echo number_format($total_transactionAmountBeforeDiscount, $dPlace) ?></td>
            </tr>

            <?php if($isGroupBasedPolicy == 1){?>
                <tr>
                    <td class="borderleftnot" colspan="<?php echo $colspan?>" style="font-size: 11px"><b>VAT</b></td>
                    <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                        style="text-align: right;font-size:11px"><?php echo number_format($VATTaxAmount, $dPlace) ?></td>
                </tr>
                <?php if($isOtherTaxExist > 0) { ?>
                    <tr>
                        <td class="borderleftnot" colspan="<?php echo $colspan?>" style="font-size: 11px"><b>Other Tax &nbsp;   ضريبة أخرى</b></td>
                        <td class="borderleftnot" colspan="<?php echo $colspanForValue?>"
                            style="text-align: right;font-size: 11px"><?php echo number_format($totalTaxAmount, $dPlace) ?></td>
                    </tr>
                <?php  }?>
            <?php }?>

            <?php if ($invMaster['generalDiscountAmount'] > 0) {
                ?>
                <tr>
                    <td class="borderlefttopnot" colspan="<?php echo $colspan?>" style="font-size: 11px"><b>Discount &nbsp; خصم </b></td>
                    <td class="borderlefttopnot" colspan="<?php echo $colspanForValue?>"
                        style="text-align: right;font-size: 11px"><?php echo number_format($invMaster['generalDiscountAmount'], $dPlace); ?></td>
                </tr>
            <?php } ?>

            <tr>
                <td class="borderlefttopnot" colspan="<?php echo $colspan?>" style="font-size: 11px"><b>Grand Total &nbsp; الصافي </b></td>
                <td class="borderlefttopnot" colspan="<?php echo $colspanForValue?>"
                    style="text-align: right;font-size: 11px"><?php echo number_format($invMaster['netTotal'], $dPlace) ?></td>
            </tr>

            <?php
            $cn = 1;
            foreach ($gPosPayMethods as $gppm) {
                if ($invMaster['generalDiscountAmount'] > 0) {
                    echo '
                        <tr>
                            <td class="" colspan="2">&nbsp;</td>
                            <td class="borderlefttopnot" colspan="' . $colspan . '" style="font-size: 11px"><b>' . $gppm['description'] .'<span class="payref">'.$gppm['reference'].'</span></b></td>
                            <td class="borderlefttopnot" colspan="' . $colspanForValue . '" style="text-align: right;font-size: 11px">' . number_format($gppm['amount'], $dPlace) . '</td>
                        </tr>';
                } else {
                    if ($cn == 1) {
                        echo '
                    <tr>                
                        <td class="borderlefttopnot" colspan="' . $colspan . '" style="font-size: 11px"><b>' . $gppm['description'] .'<span class="payref">'.$gppm['reference'].'</span></b></td>
                        <td class="borderlefttopnot" colspan="' . $colspanForValue . '" style="text-align: right;font-size: 11px">' . number_format($gppm['amount'], $dPlace) . '</td>
                    </tr>';
                    } else {
                        echo '
                    <tr>
                        <td class="" colspan="2">&nbsp;</td>
                        <td class="borderlefttopnot" colspan="' . $colspan . '" style="font-size: 11px"><b>' . $gppm['description'] .'<span class="payref">'.$gppm['reference'].'</span></b></td>
                        <td class="borderlefttopnot" colspan="' . $colspanForValue . '" style="text-align: right;font-size: 11px">' . number_format($gppm['amount'], $dPlace) . '</td>
                    </tr>';
                    }

                }
                $cn++;
            }
            ?>

            <tr>
                <td class="borderlefttopnot" colspan="2">&nbsp;</td>
                <?php
                $Balancetxt = 'Change &nbsp; الباقي ';

                ?>
                <td colspan="<?php echo $colspan?>" style="padding-left: 2px;font-size: 11px"><b><?php echo $Balancetxt ?></b></td>
                <?php
                $amn = abs($invMaster['netTotal'] - $invMaster['paidAmount']);
                ?>
                <td colspan="<?php echo $colspanForValue?>" style="text-align: right;font-size: 11px"><?php echo number_format($amn, $dPlace) ?></td>

            </tr>
            <tr>
                <td class="borderlefttopnot" colspan="<?php echo $colspan_bill; ?>" style="height: 50px;font-size: 11px"><?php echo $outletInfo['pos_footNote'] ?></td>
            </tr>
            <!--<tr>
                <td class="borderlefttopnot" colspan="<?php //echo $colspan_bill; ?>"
                    align="center"><?php //echo $outletInfo['warehouseAddress'] ?><br>
                    Tel : <?php //echo $outletInfo['warehouseTel'] ?>
                    <br><b><?php //echo $this->common_data['company_data']['companyPrintTagline'] ?></b></td>
            </tr>-->
        </tbody>
    </table>
    
   
    <table width="100%">
      <tbody>
        <tr>
          <td style="height: 2px">&nbsp;</td>
        </tr>
      </tbody>
    </table>
    <table width="100%">
      <tbody>
        <tr>
          <td width="60%" style="padding-bottom: 8px;">
            <table>
              <tbody>
                <tr>
                  <td style="font-size: 11px !important;">
                    <b>Payment Method</b>
                  </td>
                </tr>
                <tr>
                  <td style="font-size: 8px !important;height: 10px;">&nbsp;</td>
                </tr>
                <tr>
                  <td style="font-size: 11px !important;height: 25px;">
                    <b>Balance Paid : <?php echo number_format($invMaster['netTotal'], $dPlace) ?></b>
                  </td>
                </tr>
                <tr>
                  <td style=" font-size: 11px !important"></td>
                  <td style=" font-size: 11px !important"></td>
                  <td style=" font-size: 11px !important"></td>
                </tr>
                <tr>
                  <td style=" font-size: 11px !important">
                    <b>Received : CASH : <?php echo number_format($gppm['amount'], $dPlace); ?></b>
                  </td>
                </tr>
                <!--<tr>
                  <td style="font-size: 11px !important;font-style: italic;">
                    <b>Five Thousand Four Hundred Fifty U.A.E DIRHAMS Only</b>
                  </td>
                </tr>-->
                
                <tr>
                  <td style=" font-size: 11px !important">Created by &nbsp; بواسطة &nbsp; : <?php echo $invMaster['repName']; ?></td>
                </tr>
              </tbody>
            </table>
          </td>
          <td width="40%" style="padding-bottom: 8px;">
            <table>
              <tbody>
                <tr>
                  <td style=" font-size: 11px !important">&nbsp;</td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
    <table width="100%">
      <tbody>
        <tr>
          <td  align="center" width="100%" style="padding-bottom: 8px;text-align: center;">
            <table style="text-align: center;width: 100%;">
              <tbody>
                <tr>
                  <td align="center" style="font-size: 11px !important;text-align:center;"><?php echo $outletInfo['warehouseAddress'] ?></td>
                </tr>
                <tr>
                  <td align="center" style="font-size: 11px !important;text-align:center;">TEL : <?php echo $outletInfo['warehouseTel'] ?></td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
<?php    
} ?>





</body>
</html>
