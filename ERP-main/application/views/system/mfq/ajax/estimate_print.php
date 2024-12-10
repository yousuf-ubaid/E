<?php
//$confirmedUser = fetch_employeeNo($header["createdUserID"]);
//$approvedUser = fetch_employeeNo($header["approvedbyEmpID"]);
//$currencyCode = fetch_currency_dec($this->common_data["company_data"]["company_default_currency"]);

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$confirmedUser = fetch_employeeNo($header["createdUserID"]);
$approvedUser = fetch_employeeNo($header["approvedbyEmpID"]);
$reviewedUser = fetch_employeeNo($header["reviewedBy"]);
$currencyCode = fetch_currency_dec($header['CurrencyCode']);
//$this->load->library('NumberToWords');

$manufacturing_Flow = getPolicyValues('MANFL', 'All');

$colspan = 4;
if ($viewMargin == 1) {
    $colspan = 9;

    if($manufacturing_Flow == 'Micoda' || $manufacturing_Flow == 'GCC'){
        $colspan = 10;
    }
}

?>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:40%;">
                <img alt="Logo" style="height: 80px;font-size: 12px"
                     src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>"></td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%;margin-top: 10px">
        <tr>
            <td><span style="font-weight: bold;font-size: 12px">
                    Quotation: </span> <?php echo $header["estimateCode"]; ?>
        </tr>
        <tr>
            <td style="height:40px;"><span style="font-weight: bold;font-size: 12px">
                    Date: </span> <?php echo $header["documentDate"]; ?>
            </td>
        </tr>
        <tr>
            <td style="height:17px;font-size: 12px"><b>
                    To: M/s._<u><?php echo $header["CustomerName"]; ?><u>_
            </td>
        </tr>
        <tr>
     <?php 
       $countrydes = $customercountry['customerCountry'];
        if($customercountry['customerCountry'] == 'Oman') { ?>
      
             <?php  
            $countrydes ='Sultanate of Oman';
            }?>
  
        <td style="font-size: 12px"><b><?php echo $countrydes?></b></td>
       
        </tr>
        <tr>
            <td style="height:40px;font-size: 12px"><b><i> Subject: </i></b> : <?php echo $header["description"]; ?>
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px">
                Dear Sir,
            </td>
        </tr>
        <tr>
            <td style="font-size: 12px">
                <p> Thank you for forwarding us your valued inquiry. Based on information
                    furnished, we are pleased to submit our quotation as follows:</p></strong>
            </td>
        </tr>
    </table>

    <table style="width: 100%;">
        <tr>
            <td width="5%" align="right"><span style="font-weight: bold;font-size: 12px">I.</span></td>
            <td><span style="font-weight: bold;font-size: 12px">Schedule of Price </span></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px"></td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="4" border="1" style="font-size: 12px">
        <thead>
        <tr>

            <th style="width: 5%">Item</th>
            <th style="width: 15%">Description</th>
            <th style="width: 5%">Qty</th>
            <?php if($viewMargin == 1) {
                echo ' <th style="width: 10%">Unit Cost</th>';
                echo ' <th style="width: 10%">Total Cost</th>';
                if($header['isFormulaChanged'] == 1) {
                    echo ' <th style="width: 10%">Margin %</th>';
                }else{
                    echo ' <th style="width: 10%">Markup %</th>';
                }
                echo ' <th style="width: 10%">Sales Price</th>';
                echo ' <th style="width: 10%">Discount %</th>';
                echo ' <th style="width: 10%">Actual Margin %</th>';
                if($manufacturing_Flow == 'GCC') {
                    echo ' <th style="width: 10%">Unit Selling Price( '. $header['CurrencyCode'] .')</th>';
                }
                if($manufacturing_Flow == 'Micoda'){
                    echo ' <th style="width: 10%">Alloted Manhours</th>';
                }
            } else {
               echo '<th style="width: 10%">Unit Price</th>';
            }?>
            <th style="width:10%">Total Price</th>
        </tr>
        </thead>

  <?php
           // $totCost = 0;
            $total = 0;
           // $discount = 0;
            if (!empty($itemDetail)) {
                if($header['totDiscount'] > 0 && $header['showDiscountYN'] == 1) {
                    foreach ($itemDetail as $val) {
                        $expectedQty = 1;
                        if($val['expectedQty']){
                            $expectedQty = $val['expectedQty'];
                        }

                        if($viewMargin == 1) {
                            $totalAmount = $val['discountedPrice'];
                        } else {
                            if($header['isFormulaChanged'] == 1) {
                                $totalAmount = ($val['discountedPrice'] / (1-($header['totMargin'] / 100)));
                            } else {
                                $totalAmount = ($val['discountedPrice'] * ((100 + $header['totMargin'])/100));
                            }                           
                        }
                        //$totalAmount = ($val['discountedPrice'] * ((100 + $header['totMargin'])/100))/* * ((100 - $header['totDiscount'])/100)*/;

                        // $totCost += ($val['sellingPrice']/$expectedQty);
                        $total += $totalAmount;
                        // $discount += $val['discountedPrice'];
                        ?>

                        <tr>
                            <td style="font-size: 12px">
                                <a href="#" class="drill-down-cursor" onclick="viewItemBOM(<?php echo $val['bomMasterID']?>)"><?php echo $val['itemSystemCode'] ?></a>
                            </td>
                            <td style="font-size: 12px"><?php echo $val['itemDescription'] ?></td>
                            <td align="right" style="font-size: 12px"><?php echo $val['expectedQty'] ?></td>

                            <?php if ($viewMargin == 1) { ?>
                                <td align="right" style="font-size: 12px"><?php echo number_format($val['estimatedCost'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <td align="right" style="font-size: 12px"><?php echo number_format($val['totalCost'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <td align="right" style="font-size: 12px"><?php echo $val['margin'] . '%' ?></td>
                                <td align="right" style="font-size: 12px"><?php echo number_format(($val['sellingPrice']), $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <td align="right" style="font-size: 12px"><?php echo $val['discount'] . '%' ?></td>

                            <?php } else { ?>
                                <td align="right" style="font-size: 12px">
                                    <?php  echo number_format(($totalAmount/$expectedQty), $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <!--<td align="right" style="font-size: 12px">
                        <?php /* echo number_format(($val['sellingPrice']/$expectedQty), $val['transactionCurrencyDecimalPlaces']) */?></td>-->
                           <?php } ?>
                            <td align="right" style="font-size: 12px">
                                <?php echo number_format($totalAmount, $val['transactionCurrencyDecimalPlaces']) ?></td>
                            <!-- <td align="right" style="font-size: 12px">
                        <?php /*echo number_format($val['discountedPrice'], $val['transactionCurrencyDecimalPlaces']) */?></td>-->
                        </tr>
                    <?php } ?>
                    <tr>
                        <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Sub Total (<?php echo $header['CurrencyCode']?>)</span></td>
                        <td align="right" style="font-size: 12px">
                            <?php
                            echo number_format($total, $header['decimalPlace'])
                            ?>
                        </td>
                    </tr>
                    <?php if($viewMargin == 1) { ?>
                        <tr>

                            <?php if($header['isFormulaChanged'] == 1){ ?>
                                <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Margin (<?php echo $header['totMargin'] . '%'?>)</span></td>
                            <?php }else{ ?>
                                <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Markup (<?php echo $header['totMargin'] . '%'?>)</span></td>
                            <?php } ?>
                            
                            <td align="right" style="font-size: 12px">
                                <?php
                                if($header['isFormulaChanged'] == 1) {
                                    $marginAmount = ($total/ (1- ($header['totMargin'] / 100))) - $total;
                                } else {
                                    $marginAmount = ($total / 100) * $header['totMargin'];
                                }
                                echo number_format($marginAmount, $header['decimalPlace'])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Sales Total (<?php echo $header['CurrencyCode']?>)</span></td>
                            <td align="right" style="font-size: 12px">
                                <?php
                                $total = $total + $marginAmount;
                                echo number_format($total, $header['decimalPlace'])
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Discount (<?php echo $header['totDiscount'] . '%'?>)</span></td>
                        <td align="right" style="font-size: 12px">
                            <?php
                            $discountAmount = ($total / 100) * $header['totDiscount'];
                            echo number_format($discountAmount, $header['decimalPlace'])
                            ?>
                        </td>
                    </tr>
                    <?php
                    $total = $total - $discountAmount;
                } else {
                    foreach ($itemDetail as $val) {
                        $expectedQty = 1;
                        if($val['expectedQty']){
                            $expectedQty = $val['expectedQty'];
                        }

                        if($viewMargin == 1) {
                            $totalAmount = $val['discountedPrice'];
                        } else {
                            if($header['isFormulaChanged'] == 1) {
                                $totalAmount = ($val['discountedPrice'] / (1-($header['totMargin'] / 100))) * ((100 - $header['totDiscount'])/100);
                            } else {
                                $totalAmount = ($val['discountedPrice'] * ((100 + $header['totMargin'])/100)) * ((100 - $header['totDiscount'])/100);
                            }
                        }

                        // $totCost += ($val['sellingPrice']/$expectedQty);
                        $total += $totalAmount;
                        // $discount += $val['discountedPrice'];
                        ?>

                        <tr>
                            <td style="font-size: 12px">
                                <a href="#" class="drill-down-cursor" onclick="viewItemBOM(<?php echo $val['bomMasterID']?>)"><?php echo $val['itemSystemCode'] ?></a>
                            </td>
                            <td style="font-size: 12px"><?php echo $val['itemDescription'] ?></td>
                            <td align="right" style="font-size: 12px"><?php echo $val['expectedQty'] ?></td>

                            <?php if ($viewMargin == 1) { ?>
                                <td align="right" style="font-size: 12px"><?php echo number_format($val['estimatedCost'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <td align="right" style="font-size: 12px"><?php echo number_format($val['totalCost'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <td align="right" style="font-size: 12px"><?php echo $val['margin'] . '%' ?></td>
                                <td align="right" style="font-size: 12px"><?php echo number_format(($val['sellingPrice']), $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <td align="right" style="font-size: 12px"><?php echo $val['discount'] . '%' ?></td>
                                

                                <td align="right" style="font-size: 12px"><?php echo $val['actualMargin'] ?></td>
                                <?php if($manufacturing_Flow == 'Micoda'){?>
                                    <td align="right" style="font-size: 12px"><?php echo $val['allotedManHrs'] ?></td>
                                <?php } ?>
                                <?php if($manufacturing_Flow == 'GCC'){?>
                                    <td align="right" style="font-size: 12px"><?php echo $val['unitSellingPrice'] ?></td>
                                <?php } ?>

                            <?php } else { ?>
                                <td align="right" style="font-size: 12px">
                                    <?php  echo number_format(($totalAmount/$expectedQty), $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <!--<td align="right" style="font-size: 12px">
                        <?php /* echo number_format(($val['sellingPrice']/$expectedQty), $val['transactionCurrencyDecimalPlaces']) */?></td>-->
                            <?php } ?>
                            
                            <td align="right" style="font-size: 12px">
                                <?php echo number_format($totalAmount, $val['transactionCurrencyDecimalPlaces']) ?></td>
                            <!-- <td align="right" style="font-size: 12px">
                        <?php /*echo number_format($val['discountedPrice'], $val['transactionCurrencyDecimalPlaces']) */?></td>-->
                        </tr>
                    <?php }
                    
                    if($viewMargin) { ?>
                        <tr>
                            <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Sub Total (<?php echo $header['CurrencyCode']?>)</span></td>
                            <td align="right" style="font-size: 12px">
                                <?php
                                echo number_format($total, $header['decimalPlace'])
                                ?>
                            </td>
                        </tr>
                        <tr>

                            <?php if($header['isFormulaChanged'] == 1){ ?>
                                <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Margin (<?php echo $header['totMargin'] . '%'?>)</span></td>
                            <?php }else{ ?>
                                <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Markup (<?php echo $header['totMargin'] . '%'?>)</span></td>
                            <?php } ?>
                           
                            <td align="right" style="font-size: 12px">
                                <?php
                                if($header['isFormulaChanged'] == 1) {
                                    $marginAmount = ($total/ (1- ($header['totMargin'] / 100))) - $total;
                                } else {
                                    $marginAmount = ($total / 100) * $header['totMargin'];
                                }
                                echo number_format($marginAmount, $header['decimalPlace'])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Sales Total (<?php echo $header['CurrencyCode']?>)</span></td>
                            <td align="right" style="font-size: 12px">
                                <?php
                                $total = $total + $marginAmount;
                                echo number_format($total, $header['decimalPlace'])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Discount (<?php echo $header['totDiscount'] . '%'?>)</span></td>
                            <td align="right" style="font-size: 12px">
                                <?php
                                $discountAmount = ($total / 100) * $header['totDiscount'];
                                echo number_format($discountAmount, $header['decimalPlace'])
                                ?>
                            </td>
                        </tr>
                    <?php
                        $total = $total - $discountAmount;
                    }
                }
            
            } 
            else {
                ?>
                <tr class="danger">
                    <td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found') ?><!--No Records Found--></b></td>
                </tr>
            <?php
            }
           // echo $total;
           // $discountAmount='';
                    $total = number_format($total, $header['decimalPlace'], '.', '');
                   $dicountExplode = explode('.',$total);
                   //echo $dicountExplode[0];
                   $numberinword= $this->numbertowords->convert_number($dicountExplode[0]);
                    $discountAmount=$numberinword;
                    if($dicountExplode) {
                        if (isset($dicountExplode[1])) {
                            if ($header['currencyID'] == 1) {
                                $discountAmount = $numberinword . ' and ' . $dicountExplode[1] . ' / 1000';
                            } else {
                                $discountAmount = $numberinword . ' and ' . $dicountExplode[1] . ' / 100';
                            }
                        }
                    }

            ?>

        <tr>
            <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Total Amount(<?php echo $header['CurrencyCode']?>)</span></td>
            <td align="right" style="font-size: 12px">
                <?php echo number_format($total, $header['decimalPlace']) ?></td>
        </tr>

        <!--warranty cost / commision-->
        <?php if($viewMargin) { ?>
            <?php if($manufacturing_Flow == 'SOP'){ ?>
            <tr>
                <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Warranty Cost(<?php echo $header['CurrencyCode']?>)</span></td>
                <td align="right" style="font-size: 12px">
                    <?php echo number_format($val['warrantyCost'], $header['decimalPlace']) ?></td>
            </tr>
            <tr>
                <td align="right" colspan="<?php echo $colspan?>"><span style="font-weight: bold;font-size: 12px">Commision(<?php echo $header['CurrencyCode']?>)</span></td>
                <td align="right" style="font-size: 12px">
                    <?php echo number_format($val['commision'], $header['decimalPlace']) ?></td>
            </tr>
            <?php } ?>
        <?php } ?>
        <tr>
            <td colspan="<?php echo $colspan + 1?>" style="font-size: 12px">In words: <b><?php echo $currencyCode.' – '.$discountAmount ?></b></td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;;font-size: 12px">
        <tr>
            <td  align="right"><span style="font-weight: bold;font-size: 12px">II.</span></td>
            <td><span style="font-weight: bold;font-size: 12px">Scope Of Work </span></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px"><?php echo $header["scopeOfWork"]; ?></td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">III.</span></td>
            <td><span style="font-weight: bold;font-size: 12px">Exclusion </span></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px"><?php echo $header["exclusions"]; ?></td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td width="5%" align="right"><span style="font-weight: bold;font-size: 12px">IV.</span></td>
            <td>
                <span style="font-weight: bold;font-size: 12px">Terms of Payment </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px"><?php echo $header["paymentTerms"]; ?></td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">V.</span></td>
            <td>
               <span style="font-weight: bold;font-size: 12px">Delivery </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px"><?php echo $header["deliveryTerms"]; ?></td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">VI.</span></td>
            <td>
                <span style="font-weight: bold;font-size: 12px">Warranty</span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px"><?php echo $header["warranty"]; ?> Months</td>
        </tr>
        <tr>
            <td style="height:10px;"></td>
        </tr>
        <tr>
            <td align="right"><span style="font-weight: bold;font-size: 12px">VII.</span></td>
            <td>
                <span style="font-weight: bold;font-size: 12px">Validity</span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 12px"><?php echo $header["validity"]; ?></td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="height:20px;"></td>
        </tr>
        <tr>
            <td style="height:20px;font-size: 12px">
                Please feel free to contact us for any further assistance.
            </td>
        </tr>
        <tr>
            <td style="height:20px;font-size: 12px">
                Yours truly,
            </td>
        </tr>
        <tr>
            <td style="height:20px;"></td>
        </tr>
        <tr>
            <td style="height:20px;font-size: 12px">
                For and on behalf of,
            </td>
        </tr>
        <tr>
            <td><span style="font-weight: bold;font-size: 12px"><?php echo current_companyName() ?></span></td>
        </tr>
    </table>
    <br>
    <br>
    <table style="width: 100%;font-size: 12px">
        <tr>
            <td style="width:30%;"><b>Prepared by:</b></td>
            <td style="width:30%;"><b>Reviewed by:</b></td>
            <td style="width:30%;"><b>Approved by:</b></td>
        </tr>
        <tr>
            <td style="width:30%;"><?php echo $confirmedUser["Ename2"] ?? null ?><br> Tel No: <?php echo $confirmedUser["EcMobile"] ?? null ?>  <br> email: <?php echo $confirmedUser["EEmail"] ?? null ?> </td>
            <td style="width:30%;"><?php echo $reviewedUser["Ename2"] ?? null ?><br> Tel No: <?php echo $reviewedUser["EcMobile"] ?? null ?> <br> email: <?php echo $reviewedUser["EEmail"] ?? null ?> </td>
            <td style="width:30%;"><?php echo $approvedUser["Ename2"] ?? null ?><br> Tel No: <?php echo $approvedUser["EcMobile"] ?? null ?> <br> email: <?php echo $approvedUser["EEmail"] ?? null ?> </td>
        </tr>
    </table>
</div>

<br>
<div class="table-responsive"> 
<table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:100%;"><span style="font-weight: bold;font-size: 12px">Terms and Conditions</span></td>
        </tr>
        <tr>
            <td style="width:100%;"><?php echo $header["termsAndCondition"]; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<script>

</script>


