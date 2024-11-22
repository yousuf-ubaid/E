<style type="text/css">
    .headerTxt {
        font-size: 10px !important;
        margin: 0;
        text-align: center
    }

    .fWidth {
        width: 100% !important
    }

    .fSize {
        font-size: 12px !important
    }

    .f {
        font-family: Raleway, Arial, sans-serif !important
    }

    .pad-top {
        padding-top: 1px;
        font-size: 12px;
    }

    .ac {
        text-align: center !important
    }

    .ar {
        text-align: right !important
    }

    .al {
        text-align: left !important
    }

    #tblListItems tr td {
        padding: 0 1px !important
    }

    .vLine {
        border-top: 1px dashed #000;
        margin: 4px 0;
        height: 2px
    }

    .printAdvance {
        margin-bottom: 7px !important;
        height: 38px !important;
        border-radius: 0 !important
    }

    tbody td {
        font-size: 8px !important;
    }
</style>
<div id="wrapper">
    <?php
    $d = get_company_currency_decimal();
    $paymentTypes = get_bill_payment_types($masters['menuSalesID']);
    $tmpPayTypes = '';
    if (!empty($paymentTypes)) {

        foreach ($paymentTypes as $paymentType) {
            $tmpPayTypes .= $paymentType['description'] . ', ';
        }
        $tmpPayTypes = rtrim($tmpPayTypes, ', ');
    }

    $data['paymentTypes'] = '';

    $companyInfo = get_companyInfo();
    //$outletInfo = get_outletInfo();
    $outletInfo = get_warehouseInfo($masters['menuSalesID'], $masters['wareHouseAutoID']);

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_restaurent', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    $uniqueID = time();
    $isConfirmedDelivery = isDeliveryConfirmedOrder($masters['menuSalesID']);
    $deliveryInfo = get_deliveryConfirmedOrder($masters['menuSalesID']);
    ?>

    <style>

        @media print {
            body * {
                visibility: hidden;
            }

            .myCustomPrint * {
                visibility: visible;
            }

            .myCustomPrint {
                /*position: absolute;*/
                /*left: 0;*/
                /*top: 0;*/
            }

            .pagebreak {
                page-break-before: always;
            }

        }

        @page {
            size: auto;   /* auto is the initial value */
            margin-left: 10mm;  /* this affects the margin in the printer settings */
            margin-top: 0mm;  /* this affects the margin in the printer settings */
            margin-bottom: 0mm;  /* this affects the margin in the printer settings */
        }

    </style>
    <script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
    <script>
        function printElement(elem, append, delimiter) {
            var domClone = elem.cloneNode(true);

            var $printSection = document.getElementById("printSection");

            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            if (append !== true) {
                $printSection.innerHTML = "";
            } else if (append === true) {
                if (typeof (delimiter) === "string") {
                    $printSection.innerHTML += delimiter;
                } else if (typeof (delimiter) === "object") {
                    $printSection.appendChlid(delimiter);
                }
            }

            $printSection.appendChild(domClone);
        }

        function print_paymentReceipt(parameter = null) {
            var screenWidth = $(window).width();

            <?php if (isset($splitBill) && $splitBill) { ?>
            var split_qty = '<?php echo $split_qty; ?>';
            <?php }else{ ?>
            var split_qty = null;
            <?php  } ?>

            if (screenWidth < 768) {
                if (parameter !== null) {
                    window.print();
                } else {
                    window.print();

                }
            } else {
                if (parameter == null) {
                    parameter = <?php echo $uniqueID ?>;
                }
                var print_content = $(".myCustomPrint").html();
                if (split_qty != null) {
                    let i = 0;
                    let print_copy = "";
                    while (i < split_qty) {
                        print_copy += print_content + '<div class="pagebreak"> </div>';
                        i++;
                    }
                    $(".myCustomPrint").html(print_copy);
                }
                $.print("#print_content" + parameter);
                $(".myCustomPrint").html(print_content);
            }
            $("#pos_sampleBill").modal('hide');
            setTimeout(function () {
                $("#rpos_print_template").modal('hide');
            }, 5000);
        }
    </script>

    <style>
        tr td {
            font-size: 8px !important;
        }
    </style>

    <?php if (isset($from_up_coming)) {
        echo '<div style="width: 570px; margin-left: 20%;">';
    } ?>

    <div id="print_content<?php echo $uniqueID; ?>">
        <div class="myCustomPrint" style="margin:0 92px 0 -19px;width:123px;">
            <table border="0" style="width:100%" class="f fSize fWidth">
                <tbody>

                <tr>
                    <td>
                        <div class="headerTxt" style="font-size:17px !important; text-align: center;">
                            <?php echo $outletInfo['wareHouseDescription']; ?>
                        </div>
                    </td>
                </tr>
                <?php
                if (!isset($from_up_coming)) {
                    if (!empty($outletInfo['warehouseImage']) && (!isset($from_up_coming))) {
                        $LogImage = 'uploads/warehouses/' . $outletInfo['warehouseImage'];
                        ?>
                        <tr>
                            <td>
                                <div class="ac">
                                    <img src="<?php echo get_s3_url($outletInfo['warehouseImage']); ?>"
                                         alt="Restaurant Logo" style="max-height: 80px;">
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>


                <tr>
                    <td>
                        <div class="headerTxt" style="margin-top:5px;">
                            <table style="width: 100%" class="f">
                                <tr>
                                    <td style="width:20%;font-size: 8px !important;" class="al">Name</td>
                                    <?php
                                    $menusalescust = '';
                                    if ($masters['isCreditSales'] == 1) {
                                        $menusalescust = get_credit_salesCustomers($masters['menuSalesID']);
                                    }

                                    if (!empty($deliveryInfo)) {
                                        ?>
                                        <td style="width:30%;font-size: 8px !important;"
                                            class="al"><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerName'] : '-'; ?></td>
                                        <?php
                                    } elseif (!empty($masters['cusname'])) {
                                        ?>
                                        <td style="width:30%;font-size: 8px !important;"
                                            class="al"><?php echo $masters['cusname'] ?></td>
                                        <?php
                                    } elseif (!empty($menusalescust)) {
                                        ?>
                                        <td style="width:30%;font-size: 8px !important;"
                                            class="al"><?php echo $menusalescust['CustomerName'] ?></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td style="width:30%;font-size: 8px !important;" class="al">Cash</td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td style="width:20%;font-size: 8px !important;" class="al">Date</td>
                                    <td style="width:30%;font-size: 8px !important;"
                                        class="text-right"><?php echo date('d/m/Y&\nb\sp;&\nb\sp;&\nb\sp;&\nb\sp;&\nb\sp;&\nb\sp;&\nb\sp;&\nb\sp;g:i A', strtotime($masters['createdDateTime'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="al" style="font-size: 8px !important;">Address</td>
                                    <?php
                                    if (!empty($deliveryInfo)) {
                                        ?>
                                        <td class="al"
                                            style="font-size: 8px !important;"><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerAddress1'] : '-'; ?></td>
                                        <?php
                                    } elseif (!empty($masters['cusaddress'])) {
                                        ?>
                                        <td class="al"
                                            style="font-size: 8px !important;"><?php echo $masters['cusaddress']; ?></td>
                                        <?php
                                    } elseif (!empty($menusalescust)) {
                                        ?>
                                        <td style="width:30%;font-size: 8px !important;"
                                            class="al"><?php echo $menusalescust['CustomerAddress1'] ?></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td class="al" style="font-size: 8px !important;">-</td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="al" style="font-size: 8px !important;">Invoice No</td>
                                    <td class="ar"
                                        style="font-size: 8px !important;"><?php echo get_pos_invoice_code($masters['menuSalesID']); ?></td>
                                </tr>
                                <tr>
                                    <td class="al" style="font-size: 8px !important;">Mobile</td>
                                    <?php
                                    if (!empty($deliveryInfo)) {
                                        ?>
                                        <td class="al"
                                            style="font-size: 8px !important;"><?php echo !empty($deliveryInfo) ? $deliveryInfo['phoneNo'] : '-'; ?></td>
                                        <?php
                                    } elseif (!empty($masters['custel'])) {
                                        ?>
                                        <td class="al"
                                            style="font-size: 8px !important;"><?php echo $masters['custel']; ?></td>
                                        <?php
                                    } elseif (!empty($menusalescust)) {
                                        ?>
                                        <td style="width:30%;font-size: 8px !important;"
                                            class="al"><?php echo $menusalescust['customerTelephone'] ?></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td class="al" style="font-size: 8px !important;">-</td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td class="al" style="font-size: 8px !important;">Payment Type</td>
                                    <td class="ar" style="font-size: 8px !important;"><?php echo $tmpPayTypes ?></td>
                                </tr>
                            </table>
                            <!--<table style="width: 100%" class="f">
                            <tr>
                                <td style="width:25%; text-align: left;"> <?php /*echo $this->lang->line('posr_ord_type') . ':'; */ ?> </td>
                                <td style="width:30%"> <?php /*echo $masters['customerDescription'] */ ?>   </td>
                                <td style="width:20%; "><?php /*echo $this->lang->line('posr_inv_no') . ':'; */ ?> </td>
                                <td style="width:25%;"
                                    class="ar"><?php /*echo get_pos_invoice_code($masters['menuSalesID']) */ ?> </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;"><?php /*echo $this->lang->line('common_date') . ':'; */ ?>  </td>
                                <td> <?php /*echo date('d/m/Y', strtotime($masters['createdDateTime'])) */ ?></td>
                                <td><?php /*echo $this->lang->line('common_time').':'; */ ?></td>
                                <td class="ar"><?php /*echo date('g:i A', strtotime($masters['createdDateTime'])) */ ?></td>
                            </tr>
                        </table>-->
                        </div>
                    </td>
                </tr>

                </tbody>
            </table>
            <div class="vLine">&nbsp;</div>

            <div style="clear:both;" class="f"></div>
            <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
                <tr>
                    <td style="width:40%; text-align: left;"> Item | وصف</td>
                    <td style="width:20%; text-align: left;"> @rate | سعر الوحدة</td>
                    <td style="width:10%; text-align: left;">Qty | الكمية</td>
                    <td style="width:30%; text-align: right;"> Price | السعر</td>
                </tr>
            </table>
            <div class="vLine">&nbsp;</div>
            <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
                <tbody>

                <?php
                $templateID = get_pos_templateID();


                $qty = 0;
                $total = 0;
                $totalTax = 0;
                $totalServiceCharge = 0;
                if (!empty($invoiceList)) {
                    $i = 1;
                    foreach ($invoiceList as $item) {
                        //print_r($item);
//                        $totalTax += ($item['totalTaxAmount'] * $item['qty']) - (($item['totalTaxAmount'] * $item['qty']) * $item['discountPer'] / 100);
//                        $totalServiceCharge += ($item['totalServiceCharge'] * $item['qty']) - (($item['totalServiceCharge'] * $item['qty']) * $item['discountPer'] / 100);
//                        $item['pricewithoutTax'] = $item['pricewithoutTax'] - ($item['discountAmount'] / $item['qty']);
//                        //$item['pricewithoutTax'] = $item['pricewithoutTax'] - ($item['pricewithoutTax'] * $item['discountPer'] / 100);
//                        $item['totalTaxAmount'] = $item['totalTaxAmount'] - ($item['totalTaxAmount'] * $item['discountPer'] / 100);
//                        $item['totalServiceCharge'] = $item['totalServiceCharge'] - ($item['totalServiceCharge'] * $item['discountPer'] / 100);
//                        $sellingPrice = getSellingPricePolicy($templateID, $item['pricewithoutTax'], $item['totalTaxAmount'], $item['totalServiceCharge'], $item['qty']);
//                        $comboSub = get_pos_combos($item['menuSalesID'], $item['menuSalesItemID'], $item['warehouseMenuID']);
                        $totalTax += ($item['totalTaxAmount'] * $item['qty']) - (($item['totalTaxAmount'] * $item['qty']) * $item['discountPer'] / 100);
                        $totalServiceCharge += ($item['totalServiceCharge'] * $item['qty']) - (($item['totalServiceCharge'] * $item['qty']) * $item['discountPer'] / 100);
                        $item['pricewithoutTax'] = $item['pricewithoutTax'] - ($item['discountAmount'] / $item['qty']);
                        $sellingPrice = getSellingPricePolicy($templateID, $item['pricewithoutTax'], $item['totalTaxAmount'], $item['totalServiceCharge'], $item['qty']);
                        $comboSub = get_pos_combos($item['menuSalesID'], $item['menuSalesItemID'], $item['warehouseMenuID']);
                        ?>
                        <tr>
                            <!-- <td width="5%">
                            <?php /*echo $i;
                            $i++ */ ?>
                        </td> -->
                            <td width="40%" align="left">
                                <?php echo $item['menuMasterDescription'] ?>
                                <?php echo isset($item['discountPer']) && $item['discountPer'] > 0 ? '(' . $item['discountPer'] . '% Dis.)' : ''; ?>
                            </td>
                            <td width="20%">
                                <?php echo $item['sellingPrice'] ?>
                            </td>
                            <td width="10%" class="text-center">
                                <?php
                                echo $item['qty'];
                                $qty = $qty + $item['qty'];
                                ?>
                            </td>
                            <td width="30%"
                                align="right">
                                <?php
                                //$total = $total + ($item['sellingPrice'] * $item['qty']);
                                //echo number_format(($item['sellingPrice'] * $item['qty']), $d)
                                $total = $total + $sellingPrice;
                                echo number_format($sellingPrice, $d)
                                ?>
                            </td>
                        </tr>
                        <?php
                        if (!empty($comboSub)) {
                            foreach ($comboSub as $cmbo) {
                                ?>
                                <tr style="margin-left: 2px;">
                                    <td width="40%" align="left" style="padding-left: 10px !important;">
                                        * <?php echo $cmbo['menuMasterDescription'] ?></td>
                                    <td width="20%">&nbsp;</td>
                                    <td width="10%" class="text-center"> <?php echo $cmbo['qty'] ?></td>
                                    <td width="30%" align="right">&nbsp;</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <?php
                    }
                }
                ?>


                </tbody>
            </table>
            <div class="vLine">&nbsp;</div>


            <table class="totals f" style="width:100%" cellspacing="0" border="0">
                <tr>
                    <td style="width: 40%; vertical-align: top;">
                        <div><strong>Delivery Details</strong></div>
                        <div><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerName'] : '&nbsp;'; ?></div>
                        <div>
                            <?php echo !empty($deliveryInfo['deliveryDate']) ? date('d/m/Y', strtotime($deliveryInfo['deliveryDate'])) : '&nbsp;'; ?>
                            <?php echo !empty($deliveryInfo['deliveryTime']) ? ', ' . date('g:H A', strtotime($deliveryInfo['deliveryTime'])) : '&nbsp;'; ?>
                        </div>
                        <div><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerAddress1'] : '&nbsp;'; ?></div>
                        <div><?php echo !empty($deliveryInfo) ? $deliveryInfo['phoneNo'] : '&nbsp;'; ?></div>

                    </td>

                    <td style="width: 60%; vertical-align: top;">
                        <table class="totals f" style="width:100%" cellspacing="0" border="0">
                            <tbody>
                            <?php
                            $totalDiscount = 0;
                            $delivery = $masters['isDelivery'] == 1 ? true : false;
                            $promotion = $masters['isPromotion'] == 1 ? true : false;

                            ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight:bold;font-size: 8px !important;">
                                    Total | مجموع
                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                    <?php echo number_format($total, $d) ?>
                                </td>
                            </tr>

                            <?php if ($masters['isDelivery'] == 1 && $masters['ownDeliveryAmount'] > 0) { ?>
                                <tr>
                                    <td colspan="2"
                                        style="text-align:left; font-weight:bold;font-size: 8px !important;">
                                        <?php echo $this->lang->line('posr_owndelivery_bill'); ?>
                                    </td>
                                    <td colspan="2"
                                        style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                        <?php echo number_format($masters['ownDeliveryAmount'], $d) ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php

                            switch ($templateID) {
                                case 2 :

                                    /**
                                     *  Service Charge only for Dine-in Customers
                                     *  only applied in Tax and SC separated tmpleate & SC separated template
                                     *
                                     *  Template
                                     *  2 - Tax & Service Charge Separated
                                     *  4 - Service Charge Separated
                                     *
                                     *  */

                                    $is_dineIn_order = is_dineIn_order($masters['menuSalesID']);
                                    if ($is_dineIn_order) {
                                        $hide = ' ';
                                        $total += $totalTax + $totalServiceCharge;
                                    } else {
                                        $hide = ' hide ';
                                        $total += $totalTax;
                                        $totalServiceCharge = 0;
                                    }

                                    ?>
                                    <tr>
                                        <td colspan="2"
                                            style="text-align:left; font-weight:bold;font-size: 8px !important;">
                                            Total Tax | مجموع الضريبة
                                        </td>
                                        <td colspan="2"
                                            style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                            <?php echo number_format($totalTax, $d) ?>
                                        </td>
                                    </tr>
                                    <tr class="<?php echo $hide ?>">
                                        <td colspan="2"
                                            style="text-align:left; font-weight:bold;font-size: 8px !important;">
                                            Total Service Charge | إجمالي رسوم الخدمة
                                        </td>
                                        <td colspan="2"
                                            style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                            <?php echo number_format($totalServiceCharge, $d) ?>
                                        </td>
                                    </tr>
                                    <?php
                                    break;


                                case 3 :
                                    $total += $totalTax;
                                    ?>
                                    <tr>
                                        <td colspan="2"
                                            style="text-align:left; font-weight:bold;font-size: 8px !important;">
                                            Total Tax | مجموع الضريبة
                                        </td>
                                        <td colspan="2"
                                            style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                            <?php echo number_format($totalTax, $d) ?>
                                        </td>
                                    </tr>
                                    <?php
                                    break;
                                case 4 :
                                    /**
                                     *  Service Charge only for Dine-in Customers
                                     *  only applied in Tax and SC separated tmpleate & SC separated template
                                     *
                                     *  Template
                                     *  2 - Tax & Service Charge Separated
                                     *  4 - Service Charge Separated
                                     *
                                     *  */

                                    $is_dineIn_order = is_dineIn_order($masters['menuSalesID']);
                                    if ($is_dineIn_order) {
                                        $hide = '';
                                        $total += $totalServiceCharge;

                                    } else {
                                        $total += 0;
                                        $totalServiceCharge = 0;
                                        $hide = ' hide ';
                                    }
                                    ?>
                                    <tr class="<?php echo $hide ?>">
                                        <td colspan="2"
                                            style="text-align:left; font-weight:bold;font-size: 8px !important;">
                                            Total Service Charge | إجمالي رسوم الخدمة
                                        </td>
                                        <td colspan="2"
                                            style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                            <?php echo number_format($totalServiceCharge, $d) ?>
                                        </td>
                                    </tr>
                                    <?php
                                    break;

                            }
                            ?>

                            <!-- Outlet taxes print here if exist. -->
                            <?php
                            if (isset($isSample) && $isSample == true) {
                                $genDiscountAmount = 0;
                                $discount = $total * ($masters['discountPer'] / 100);
                                $genDiscountAmount = $discount;
                                $totalDiscount += $discount;
                                $netTotal = $total - $genDiscountAmount;

                                //applying promtion discount if available
                                if ($masters['promotionDiscount'] > 0) {
                                    $discounttt = $netTotal * ($masters['promotionDiscount'] / 100);
                                    $promoDiscountAmounttt = $discounttt;
                                    $netTotal = $netTotal - $promoDiscountAmounttt;
                                }

                                $outletTaxes = array();
                                $sOutletTaxEnabled = $this->pos_policy->isOutletTaxEnabled();
                                if ($sOutletTaxEnabled) {
                                    foreach ($outletTaxMaster as $item) {
                                        $taxAmount = ($netTotal / 100) * ($item->taxPercentage);
                                        echo '<tr>
                <td colspan="2" style="text-align:left; font-weight:bold;font-size: 8px !important;">
                  ' . $item->taxDescription . '
                </td>
                <td colspan="2" style="text-align:right; font-weight:bold;font-size: 8px !important;">
                  ' . number_format($taxAmount, $d) . '
                </td>
            </tr>';
                                        $outletTaxes[] = array('taxAmount' => $taxAmount);
                                    }
                                }

                            } else {
                                $sOutletTaxEnabled = $this->pos_policy->isOutletTaxEnabled();
                                if ($sOutletTaxEnabled) {
                                    $genDiscountAmounttt = 0;

                                    $discounttt = $total * ($masters['discountPer'] / 100);
                                    $genDiscountAmounttt = $discounttt;
                                    $netTotaltt = $total - $genDiscountAmounttt;

                                    //applying promtion discount if available
                                    if ($masters['promotionDiscount'] > 0) {
                                        $discounttt = $netTotaltt * ($masters['promotionDiscount'] / 100);
                                        $promoDiscountAmounttt = $discounttt;
                                        $netTotaltt = $netTotaltt - $promoDiscountAmounttt;
                                    }

                                    foreach ($outletTaxMaster as $taxItem) {
                                        $taxAmounttt = ($netTotaltt / 100) * ($taxItem->taxPercentage);
                                        echo '<tr>
                <td colspan="2" style="text-align:left; font-weight:bold;font-size: 8px !important;">
                   ' . $taxItem->taxDescription . '
                </td>
                <td colspan="2" style="text-align:right; font-weight:bold;font-size: 8px !important;">
                   ' . number_format($taxAmounttt, $d) . '
                </td>
            </tr>';
                                    }
                                }
                            }
                            ?>

                            <!--Discount if Exist -->
                            <?php
                            //print_r($masters);
                            if (!empty($masters['discountPer']) && $masters['discountPer'] > 0 || true) {
                                $discount = $total * ($masters['discountPer'] / 100);
                                if ($discount > 0) {
                                    ?>
                                    <tr>
                                        <td colspan="2"
                                            style="text-align:left; font-weight:bold; padding-top:1px;font-size:8px !important;">
                                            Discount / Round off | (خصم)
                                            <?php //echo number_format($masters['discountPer'], $d).'%'
                                            ?>

                                        </td>
                                        <td colspan="2"
                                            style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                            <?php
                                            $discount = $total * ($masters['discountPer'] / 100);
                                            echo '(' . number_format($discount, $d) . ')';
                                            //$totalDiscount += $discount;// this is already updated in a previous line.
                                            $total -= $discount;
                                            ?>
                                        </td>
                                    </tr>


                                    <?php
                                }
                            }
                            ?>

                            <?php
                            $promoDiscountAmount = 0;
                            if ($promotion) {
                                ?>
                                <tr>
                                    <td colspan="2"
                                        style="text-align:left; font-weight:bold; padding-top:1px;font-size:8px !important;">
                                        prmt.Discount | (خصم ترويجي)(<?php echo $masters['promotn'] . '%' ?>)
                                        <?php //echo $masters['promotionDiscount'].'%' ?>
                                    </td>
                                    <td colspan="2"
                                        style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                        <?php
                                        $discount = $total * ($masters['promotionDiscount'] / 100);
                                        echo '(' . number_format($discount, $d) . ')';
                                        $promoDiscountAmount = $discount;
                                        $totalDiscount += $discount;
                                        ?>
                                    </td>
                                </tr>

                                <?php
                            }
                            ?>


                            <tr>
                                <td colspan="2"
                                    style="text-align:left; font-weight:bold; padding-top:1px;font-size:8px !important;">
                                    Net Total | الصافي الاجمالي
                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold; border-bottom: 1px solid;">
                                    <?php
                                    $netTotal = $total - $promoDiscountAmount;
                                    $taxTotal = 0;

                                    //                                    foreach ($outletTaxes as $taxItem) {
                                    //                                        $taxTotal = $taxTotal + $taxItem['taxAmount'];
                                    //                                    }

                                    $sOutletTaxEnabled = $this->pos_policy->isOutletTaxEnabled();
                                    if ($sOutletTaxEnabled) {
                                        foreach ($outletTaxMaster as $taxItem) {
                                            $promoDiscountAmounttt = 0;
                                            $discounttt = $total * ($masters['promotionDiscount'] / 100);
                                            $promoDiscountAmounttt = $discounttt;

                                            $netTotaltt = $total - $promoDiscountAmounttt;
                                            $taxAmounttt = ($netTotaltt / 100) * ($taxItem->taxPercentage);
                                            $taxTotal = $taxTotal + $taxAmounttt;
                                        }
                                    }

                                    $netTotal = $netTotal + $taxTotal;
                                    if ($masters['isHold'] == 1) {
                                        if ($masters['ownDeliveryAmount'] > 0) {
                                            $netTotal += $masters['ownDeliveryAmount'];
                                        }
                                        echo number_format($netTotal, $d);

                                    } else {
                                        if ($masters['isDelivery'] == 1 && $masters['ownDeliveryAmount'] > 0) {
                                            echo number_format($masters['subTotal'], $d);
                                        } else {

                                            echo number_format($netTotal, $d);
                                        }
                                    }

                                    ?>
                                </td>
                            </tr>

                            <?php if (isset($splitBill) && $splitBill) { ?>
                                <tr>
                                    <td colspan="2"
                                        style="text-align:left; font-weight:bold; padding-top:1px;font-size:8px !important;">
                                        <?php echo $this->lang->line('posr_split_amount'); ?><!-- Split Amount -->
                                    </td>
                                    <td colspan="2"
                                        style="text-align:right; font-weight:bold;font-size: 8px !important;">
                                        <?php
                                        $netTotal = $total - $promoDiscountAmount;
                                        $split_amount = $netTotal / $split_qty;
                                        echo number_format($split_amount, $d);
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>




                            <?php
                            $payments = get_pos_payments_by_menuSalesID($masters['menuSalesID'], $masters['wareHouseAutoID']);
                            //var_dump($payments);
                            if (!empty($payments)) {
                                foreach ($payments as $payment) {
                                    ?>
                                    <tr>
                                        <td colspan="2">
                                            <strong style="font-size: 8px;">
                                                <?php
                                                if ($payment['paymentConfigMasterID'] == 7) {
                                                    echo !empty($payment['CustomerName']) ? $payment['CustomerName'] : $payment['description'];
                                                } else if ($payment['paymentConfigMasterID'] == 25) {
                                                    echo $payment['description'] . ' (' . $payment['reference'] . ')';
                                                } else {
                                                    echo $payment['description'];
                                                }
                                                ?>
                                            </strong>
                                        </td>
                                        <td style="text-align:right; ">
                                            <strong style="font-size: 8px;">
                                                <?php
                                                if ($payment['autoID'] == 1) {
                                                    /*actual cash amount paid by customer */
                                                    //$payment['amount'] = $payment['amount'];
                                                    $masters['cashAmount'];
                                                    $payment['amount'] = $masters['cashAmount'];
                                                }
                                                //                                    if($isHold=="1"){
                                                //                                        $payment['amount'] =$partial_payment_amount;
                                                //                                    }
                                                echo number_format($payment['amount'], $d)
                                                ?>
                                            </strong>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>


                            <tr>
                                <td colspan="2"
                                    style="text-align:left; font-weight:bold; padding-top:1px;font-size:8px !important;">
                                    <!--Paid By-->
                                    <?php
                                    if ($delivery) {
                                        $paidByAmount = number_format($total - $totalDiscount, $d);
                                        $paidAmount = $total - $totalDiscount;
                                        $balance = $paidAmount - $netTotal;

                                    } else {
                                        $paidByAmount = number_format($masters['cashReceivedAmount'], $d);
                                        $paidAmount = $masters['cashReceivedAmount'];
                                        $balance = $paidAmount - $netTotal;
                                    }

                                    $totalPaidAmount = get_paidAmount($masters['menuSalesID']);
                                    $advancePayment = $totalPaidAmount - $paidAmount;

                                    if ($isConfirmedDelivery) {
                                        echo 'Total Paid  ( إجمالي المبلغ المدفوع )';
                                    } else {
                                        echo 'Paid Amount ( المبلغ المدفوع )';
                                    }
                                    if ($paidAmount != 0) {
                                        if (!$isConfirmedDelivery) {
                                            //echo $tmpPayTypes;
                                        }
                                    }
                                    echo ':';

                                    $cash = '';//$this->lang->line('common_cash');
                                    $visa = '';// $this->lang->line('common_visa');
                                    $master_card = ''; // $this->lang->line('common_master_card');
                                    $cheque = ''; // $this->lang->line('common_cheque');
                                    $payment = $masters['paymentMethod'];
                                    $img = '<img src="' . base_url() . 'images/payment_type/' . $payment . '.png"> ';
                                    //echo $img;

                                    switch ($payment) {
                                        case 1:
                                            echo '' . $cash . '';
                                            break;
                                        case 2:
                                            echo '' . $visa . '';
                                            break;
                                        case 3:
                                            echo '' . $master_card . '';
                                            break;
                                        case 4:
                                            echo '' . $cheque . '';
                                            break;
                                        default:
                                            echo '' . $cash . '';
                                    }
                                    ?>
                                </td>
                                <td colspan="2"
                                    style="padding-top:1px; text-align:right; font-weight:bold;">
                                    <?php
                                    //                                    if ($isConfirmedDelivery) {
                                    //                                        $totalPaid = get_paidAmount($masters['menuSalesID']);
                                    //                                        echo number_format(abs($totalPaid), $d);
                                    //                                        $paidByAmount = $totalPaid;
                                    //                                    } else {
                                    //                                        echo $paidByAmount;
                                    //                                    }
                                    $paidAmount = $masters['cashAmount'];
                                    $payments = get_pos_payments_by_menuSalesID($masters['menuSalesID'], $masters['wareHouseAutoID']);
                                    foreach ($payments as $payment) {
                                        if ($payment['autoID'] != 1) {
                                            $paidAmount += $payment['amount'];
                                        }
                                    }

                                    $totalPaid = get_paidAmount($masters['menuSalesID']);
                                    //echo number_format(abs($totalPaid), $d);
                                    echo number_format(abs($paidAmount), $d);
                                    $paidByAmount = $totalPaid;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"
                                    style="text-align:left; font-weight:bold; padding-top:1px;font-size:8px !important;">
                                    <?php

                                    if ($isConfirmedDelivery) {

                                        $balanceAmount = $netTotal - $totalPaid;
                                        //echo 'Balance Payable:';
                                        if ($balanceAmount > 0) {
                                            echo 'Balance Payable | الرصيد المستحق الدفع';
                                        } else {
                                            echo 'Balance | المبلغ المتبقي';
                                        }
                                    } else {
                                        echo 'Balance | المبلغ المتبقي';
                                    }

                                    ?>
                                </td>
                                <td colspan="2"
                                    style="padding-top:1px; text-align:right; font-weight:bold;">
                                    <?php
                                    $sOutletTaxEnabled = $this->pos_policy->isOutletTaxEnabled();
                                    if ($isConfirmedDelivery) {

                                        if ($masters['balanceAmount'] != 0) {
                                            $balanceAmount = $masters['balanceAmount'];
                                        } else {
                                            $balanceAmount = $netTotal - $totalPaid;
                                        }

                                        if ($masters['isHold'] == 1) {
                                            if ($masters['isDelivery'] == 1 && $masters['ownDeliveryAmount'] > 0) {
                                                echo number_format($masters['balanceAmount'], $d);
                                            } else {
                                                echo number_format($balanceAmount, $d);
                                            }
                                        } else {
                                            if ($masters['isDelivery'] == 1 && $masters['ownDeliveryAmount'] > 0) {
                                                echo number_format($masters['balanceAmount'], $d);
                                            } else if ($sOutletTaxEnabled) {
                                                echo number_format($masters['balanceAmount'], $d);
                                            } else {
                                                echo number_format($balanceAmount, $d);
                                            }
                                        }


                                    } else {
                                        //$balance = $totalPaidAmount-$total;
                                        $balance = $paidAmount - $netTotal;
                                        if ($masters['isHold'] == 1) {
                                            if ($masters['isDelivery'] == 1 && $masters['ownDeliveryAmount'] > 0) {
                                                echo number_format($masters['balanceAmount'], $d);
                                            } else {
                                                echo number_format($balance, $d);
                                            }
                                        } else {
                                            if ($masters['isDelivery'] == 1 && $masters['ownDeliveryAmount'] > 0) {
                                                echo number_format($masters['balanceAmount'], $d);
                                            } else if ($sOutletTaxEnabled) {
                                                echo number_format($masters['balanceAmount'], $d);
                                            } else {
                                                echo number_format($balance, $d);
                                            }
                                        }


                                    }

                                    ?>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </td>

                </tr>
            </table>

            <?php if (!isset($from_up_coming)) { ?>
                <div class="vLine">&nbsp;</div>
                <table border="0" style="width:100%" class="f fSize fWidth">
                    <tbody>
                    <tr>
                        <td width="100%" class="ac">
                            <div class="headerTxt" style="text-align: center;">
                                <?php echo $outletInfo['warehouseAddress']; ?>
                            </div>
                            <div class="headerTxt" style="text-align: center;">
                                <?php echo 'TEL : ' . $outletInfo['warehouseTel']; ?>
                            </div>
                            <div class="headerTxt" style="text-align: center;">
                                <?php echo $companyInfo['companyPrintOther'] ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>


                <div class="f pad-top ac">
                    <?php //echo $this->lang->line('posr_fresh_natural_care_puff');?><!--fresh & natural care puff--> <?php echo $outletInfo['pos_footNote'] ?>
                </div>
            <?php } ?>

            <?php if ($masters['isDelivery'] == 1 && $masters['ownDeliveryAmount'] > 0) {
                $delivery_person = get_delivery_person_name($masters['deliveryPersonID']);
                ?>
                <div class="pad-top">
                    Delivery Person : <?php echo $delivery_person->crewFirstName; ?>
                </div>
            <?php } ?>

            <div class="pad-top" style="font-size:11px;">
                Cashier | أمين الصندوق <!--Cashier--> : <?php echo get_employeeShortName($masters['createdUserID']) ?>
            </div>
            <?php
            if (isset($wifi) && $wifi) {
                $wifi_pw = is_wifi_password_in_bill();
                if ($wifi_pw) {
                    ?>
                    <div class="pad-top" style="font-size:11px;">
                        WiFi Password : <strong><?php
                            $wifi = get_random_wifi_password();
                            echo $wifi['wifiPassword'];
                            ?></strong>
                    </div>
                    <?php
                    /** used password  */
                    update_wifi_password($wifi['id'], $masters['menuSalesID']);
                }
            }

            ?>

            <?php
            if (isset($void) && $void) {
                ?>
                <!--Only for void bills-->

                <div class="f pad-top ac">
                    ***** <?php echo $this->lang->line('posr_voided_bill'); ?><!--Voided Bill--> *****
                </div>
                <div class="f pad-top">
                    <?php echo $this->lang->line('posr_remarks'); ?><!--Remarks-->:
                    <hr>
                </div>
                <div class="f pad-top ac" style="min-height: 40px;">
                </div>
            <?php } ?>
        </div>

    </div>

    <?php
    if (isset($email) && $email) {
        $reprint = reprint_salesdetail_print($masters['wareHouseAutoID']);
        if ($reprint == 1) { ?>
            <button type="button" onclick="print_paymentReceipt()"
                    style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
            </button>
        <?php }
    } else {
        ?>
        <div class="vLine">&nbsp;</div>

        <div id="bkpos_wrp">
            <?php if ($auth) { ?>
                <button type="button" onclick="checkPosAuthentication(4,<?php echo $uniqueID ?>)"
                        style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                    <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
                </button>
            <?php } else {

                $invoiceID = $masters['menuSalesID'];
                if ($invoiceID > 0) {
                    $isDelivery = isDeliveryConfirmedOrder($invoiceID);
                    if ($isDelivery) {
                        ?>
                        <button type="button" class="btn btn-default btn-block printAdvance"
                                onclick="print_delivery_order_payments()">
                            <i class="fa fa-print"></i> Print Advance Payment
                        </button>
                        <?php
                    }
                }
                //echo $invoiceID;
                ?>


                <?php $result = isPos_invoiceSessionExist(); ?>

                <button type="button" onclick="print_paymentReceipt();"
                        style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                    <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
                </button>
                <?php
            } ?>
        </div>

        <div id="bkpos_wrp" style="margin-top: 8px;">
        <span class="left">
            <button type="button" onclick="checkPosAuthentication(20)"
                    style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#000; background-color:#4FA950; border:2px solid #4FA950; padding: 10px 0px; font-weight:bold;"
                    id="email"><i class="fa fa-envelope-o" aria-hidden="true"></i>
                <?php echo $this->lang->line('common_email'); ?><!--Email--></button></span>
        </div>
    <?php } ?>

    <?php
    if (isset   ($voidBtn) && $voidBtn) {
        ?>
        <!--Only for void bills-->
        <div class="vLine">&nbsp;</div>

        <div id="bkpos_wrp">
            <button type="button" onclick="checkPosAuthentication(3,<?php echo $masters['menuSalesID'] ?>)"
                    style="width:101%; cursor:pointer; font-size:12px; background-color:#ff7b6c; color:#000; text-align: center; border:1px solid #db6e61; padding: 10px 0px; font-weight:bold;">
                <i class="fa fa-close"></i> <?php echo $this->lang->line('posr_void_bill'); ?><!--Void Bill-->
            </button>
        </div>
    <?php } ?>


    <!--<div id="bkpos_wrp">
    	<span class="left">
    		<a href="http://pos.prosoft-apps.com/pos/view_invoice_a4?id=216"
               style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#000; background-color:#4FA950; border:2px solid #4FA950; padding: 10px 0px; font-weight:bold; margin-top: 6px;">
	    		Print A4
	    	</a>
	    </span>
    </div>-->

    <input type="hidden" id="id" value="216">

</div>
