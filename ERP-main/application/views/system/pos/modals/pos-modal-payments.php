<?php
$segmentconfig_posTemplateID = get_segmentconfig_posTemplateID();
$companycountry = fetch_company_country();
$odwAllowed = $this->pos_policy->isODWEnabled();
$d = get_company_currency_decimal();
if (!isset($odwAllowed) || $odwAllowed == false) {
    $odwAllowed = 0;
}
?>
<style>
    #loyalty_card_modal input {
        font-size: 17px !important;
        font-weight: 700 !important;
        color: #424242 !important;
    }

    #customer_loyalty_details_div input {
        font-size: 14px !important;
        color: #424242 !important;
    }

    .autocomplete {
        position: relative;
        display: inline-block;
    }


    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }

    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: DodgerBlue !important;
        color: #ffffff;
    }
</style>

<style>
    .touchSizeButton {
        width: 50px;
        height: 33px;
        font-weight: 700;
    }

    .customBtnNumb {
        padding: 17px;
        margin: 3px 4px;
        font-size: 18px;
        /* background-color: rgba(255, 255, 57, 0.14); */
        height: 62px;
        width: 28%;
        font-size: 21px;
    }

    .currencyNoteBtn {
        padding: 0px;
        margin: 4px 11px;
        font-size: 18px;
        height: 38px;
        width: 100%;
    }

    .formRowPad {
        padding-top: 5px;
        padding-bottom: 8px;
    }

    .al {
        text-align: right !important;
    }

    .lbl-delivery {
        background-color: #b96868;
        padding: 4px 15px;
        color: #ffffff;
        font-weight: 600;
    }

    .payment-font {
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    .tbl-payment-font {
        font-size: 15px !important;
        font-weight: 600 !important;
        font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
    }

    .btn-strong {
        font-weight: 800;
    }

    .btn-xl {
        padding: 15px 16px !important;
    }

    .payment-tblSize {
        width: 120px !important;;

    }

</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$get_outletID = get_outletID();
$current_companyID = current_companyID();
$isOutletTaxEnabled = json_encode(isOutletTaxEnabled($get_outletID, $current_companyID));
//var_dump($isOutletTaxEnabled);exit;
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_submitted_payments_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg"
         style="width: <?php //echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '70%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" onclick="close_update_pos_submitted()" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_payment'); ?><!--Payment--></h3>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form method="post" id="frm_pos_invoice_submitUpdate" class="form_pos_receipt_update">
                    <input type="hidden" name="isCreditSaleUpdate" id="isCreditSaleUpdate" value="0">
                    <input type="hidden" name="customerTelephoneUpdate" id="customerTelephoneUpdate">
                    <input type="hidden" name="customerNameUpdate" id="customerNameUpdate">
                    <input type="hidden" name="customerAddressUpdate" id="customerAddressUpdate">
                    <input type="hidden" name="customerCountry_oUpdate" id="customerCountry_oUpdate">
                    <input type="hidden" name="customerCountryCode_oUpdate" id="customerCountryCode_oUpdate">
                    <input type="hidden" name="customerCountryId_oUpdate" id="customerCountryId_oUpdate">
                    <input type="hidden" name="customerEmailUpdate" id="customerEmailUpdate">
                    <input type="hidden" name="customerIDUpdate" id="customerIDUpdate">
                    <input type="hidden" name="cardTotalAmountUpdate" id="cardTotalAmountUpdate" value="0"/>
                    <input type="hidden" name="netTotalAmountUpdate" id="netTotalAmountUpdate" value="0"/>
                    <input type="hidden" name="isDeliveryUpdate" id="isDeliveryUpdate" value="0"/>
                    <input type="hidden" name="isOnTimePaymentUpdate" id="frm_isOnTimePaymentUpdate" value=""/>
                    <input type="hidden" name="is_delivery_info_existUpdate" id="is_delivery_info_existUpdate"
                           value=""/>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_gross_total'); ?><!--Gross Total -->

                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payable_amtUpdate" class="ar payment-textLg"
                                         style="padding: 5px 0px;">0.00
                                    </div>
                                    <input type="hidden" name="total_payable_amtUpdate" id="total_payable_amtUpdate"
                                           value="0">
                                </div>
                            </div>


                            <!--promotion Row-->
                            <div class="row formRowPad" id="deliveryPersonContainer"> <!--promotionRow-->
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 payment-font"
                                     style="">
                                    <button class="pos2-btn-default p-disc-btn" disabled type="button"
                                            onclick="openPromotionModal()">
                                        <?php echo $this->lang->line('posr_promotion'); ?> </button>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                                    <div>
                                        <input type="text" id="tmp_promotionUpdate" readonly value=""
                                               class="form-control">
                                        <?php
                                        $deliveryPersonArray = get_specialCustomers(array(2, 3));

                                        ?>
                                        <select name="promotionIDUpdate" style="display: none;" id="promotionIDUpdate"
                                                class="form-control"
                                                onchange="calculateReturn(this)">
                                            <option value="">
                                                <?php echo $this->lang->line('common_non'); ?><!--None--></option>
                                            <?php if (!empty($deliveryPersonArray)) {
                                                foreach ($deliveryPersonArray as $val) {
                                                    ?>
                                                    <option value="<?php echo $val['customerID'] ?>"
                                                            data-cp="<?php echo $val['commissionPercentage'] ?>">
                                                        <?php echo $val['customerName'] . ' - ' . $val['commissionPercentage'] . ' %'; ?>

                                                    </option>
                                                    <?php
                                                }
                                            } ?>

                                        </select>

                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 payment-font">
                                    <div class="p-disc-container mrg-top5">
                                        <?php echo $this->lang->line('posr_discount'); ?><!--Promotional Discount-->
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                                    <div class="mrg-top5">
                                        <input type="text" readonly name="promotional_discountUpdate"
                                               id="promotional_discountUpdate"
                                               class="form-control input-sm ar payment-inputTextMedium" value="0">
                                    </div>
                                </div>
                            </div>

                            <?php if ($isOutletTaxEnabled == "true") { ?>
                                <div class="row formRowPad" style="padding: 1px;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                        Outlet Tax
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <div id="outlet_tax_in_invoiceUpdate" class="ar payment-textLg"
                                             style="padding: 5px 0px;">
                                            <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row formRowPad" style="padding: 1px;" id="advancePaidDiv">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    Advance Paid
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="delivery_advancePaymentAmountShowUpdate" class="ar payment-textLg"
                                         style="padding: 5px 0px;">
                                        <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                    </div>
                                    <input type="hidden" name="delivery_advancePaymentAmountUpdate"
                                           id="delivery_advancePaymentAmountUpdate" value="0">
                                </div>
                            </div>


                            <!-- Net Total -->
                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-6 col-sm-8 col-md-6 col-lg-6"
                                     style="font-weight: 800; font-size: 20px;"><b>
                                        <?php echo $this->lang->line('posr_net_total'); ?><!--Net Total --></b>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payableNet_amtUpdate" class="ar text-red"
                                         style="padding: 5px 0px; font-weight: 800; font-size: 20px;">0.00
                                    </div>
                                </div>
                            </div>

                            <table class="<?php echo table_class_pos() ?>">
                                <?php
                                $payments = get_paymentMethods_GLConfig();
                                foreach ($payments as $payment) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <!---->
                                                    <div style="padding:  4px;"
                                                         class="tbl-payment-font">
                                                        <input type="hidden"
                                                               name="customerAutoIDUpdate[<?php echo $payment['ID'] ?>]"
                                                               id="customerAutoIDUpdate<?php echo $payment['ID'] ?>">
                                                        <?php echo $payment['description']; ?>
                                                        <?php //echo $payment['autoID']; ?>
                                                    </div>
                                                </div>
                                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                    <?php //print_r($payment)
                                                    /** GIFT CARD */
                                                    if ($payment['autoID'] == 5) {
                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar gitCardRefNo"
                                                               id="reference_Update<?php echo $payment['ID']; ?>"
                                                               name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                               readonly
                                                               placeholder="Gift Card"/>
                                                        <?php
                                                        /** CREDIT SALES */
                                                    } else if ($payment['autoID'] == 7) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar CreditSalesRefNo"
                                                               id="reference_Update<?php echo $payment['ID']; ?>"
                                                               name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                               placeholder="Reference"/>
                                                        <?php
                                                        /** JAVA APP */
                                                    } else if ($payment['autoID'] == 25) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar javaAppRefNo"
                                                               id="reference_Update<?php echo $payment['ID']; ?>"
                                                               name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                               readonly
                                                               placeholder="App PIN"/>
                                                        <?php
                                                        /** OTHER */
                                                    } else {
                                                        if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                            if ($payment['autoID'] != 32) {
                                                                ?>
                                                                <input type="text" value=""
                                                                       class="form-control cardRef ar numpad"
                                                                       id="reference_Update<?php echo $payment['ID']; ?>"
                                                                       name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                                       placeholder="Ref#"/>
                                                                <?php
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td> <!--style="width:50px;"-->
                                            <?php

                                            if ($payment['autoID'] == 5) {
                                                /** Gift Card  */
                                                $onclick = ' onclick="openGiftCardRedeemModal()" ';
                                            } else if ($payment['autoID'] == 7) {
                                                /** Credit Sales */
                                                //$onclick = ' onclick="openCreditSalesModal(' . $payment['ID'] . ')" ';
                                                $onclick = ' onclick="checkPosAuthentication(11,' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 1) {
                                                /** Cash */
                                                $onclick = ' onclick="updateExactCard_update(1)" ';
                                            } else if ($payment['autoID'] == 46 || $payment['autoID'] == 53 || $payment['autoID'] == 52 || $payment['autoID'] == 51 || $payment['autoID'] == 34 || $payment['autoID'] == 40 || $payment['autoID'] == 41 || $payment['autoID'] == 33 || $payment['autoID'] == 44 || $payment['autoID'] == 43 || $payment['autoID'] == 37 || $payment['autoID'] == 2 || $payment['autoID'] == 38 || $payment['autoID'] == 39 || $payment['autoID'] == 35 || $payment['autoID'] == 26 || $payment['autoID'] == 36 || $payment['autoID'] == 3 || $payment['autoID'] == 4 || $payment['autoID'] == 6 || $payment['autoID'] == 1 || $payment['autoID'] == 27 || $payment['autoID'] == 28 || $payment['autoID'] == 29 || $payment['autoID'] == 30 || $payment['autoID'] == 31 || $payment['autoID'] == 54) {
                                                /** 3 Master Card | 4 Visa Card | 6 AMEX | 27 FriMi  | 28 Ali Pay | 36 Akeed*/
                                                $onclick = ' onclick="updateExactCard_update(' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 25) {
                                                /** java App */
                                                $onclick = ' onclick="openJavaAppModal(' . $payment['ID'] . ')" ';
                                            } else {
                                                $onclick = '';
                                            }
                                            ?>
                                            <?php
                                            if ($payment['autoID'] != 32) { ?>
                                                <button class="btn btn-default btn-block" <?php echo $onclick ?>
                                                        type="button"
                                                        style="padding: 0px;">
                                                    <img src="<?php echo base_url($payment['image']); ?>"
                                                         style="max-height: 27px;">
                                                </button>
                                            <?php } ?>

                                        </td>
                                        <td class="payment-tblSize">
                                            <?php
                                            if ($payment['autoID'] == 5) {
                                                /** GIFT CARD */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       onclick="openGiftCardRedeemModal()"
                                                       class="form-control al payment-inputTextMedium giftCardPayment paymentInputupdate <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php

                                            } else if ($payment['autoID'] == 7) {
                                                /** CREDIT SALE  */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       onclick="checkPosAuthentication(11,<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium creditSalesPayment paymentInputupdate <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php

                                            } else if ($payment['autoID'] == 25) {
                                                /** JAVA APP */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       onclick="openJavaAppModal(<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium javaAppPayment paymentInputupdate <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php

                                            } else if ($payment['autoID'] == 32) {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                /** Round OFF */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $tmpID; ?>"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       class="form-control al payment-inputTextMedium paymentInputupdate rundoff <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?> "
                                                       placeholder="0.00" readonly>
                                                <?php

                                            } else {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                if ($payment["isAuthRequired"]) {
                                                    ?>
                                                    <input type="text" id="paymentType_Update<?php echo $tmpID; ?>"
                                                           name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                           onchange="checkPosAuthentication(10,this)"
                                                           class="form-control al payment-inputTextMedium paymentInputupdate numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOtherUpdate';
                                                           }
                                                           ?>"
                                                           placeholder="0.00">
                                                <?php } else {
                                                    ?>
                                                    <input type="text" id="paymentType_Update<?php echo $tmpID; ?>"
                                                           name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                           onchange="calculatePaidAmountUpdate(this)"
                                                           class="form-control al payment-inputTextMedium paymentInputupdate numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOtherUpdate';
                                                           }
                                                           ?>"
                                                           placeholder="0.00">

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>


                                    <?php
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div style="padding:  8px; "
                                             class="tbl-payment-font">
                                            <?php echo $this->lang->line('posr_paid_amount'); ?><!--Paid Amount--></div>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td class="payment-tblSize">
                                        <input readonly type="number"
                                               onkeyup="calculateReturnUpdate()" name="paidUpdate"
                                               id="paidUpdate"
                                               class="form-control payment-inputTextLg paymentTypeTextRed al"
                                               placeholder="0.00"
                                               autocomplete="off">
                                        <span id="paid_tempUpdate" class="hide"></span></td>
                                </tr>
                            </table>


                            <div class="row formRowPad hide">
                                <div class="col-md-4 lbl-delivery"></div>


                                <div class="col-md-8">
                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        foreach ($paymentType as $key => $payType) {
                                            //id="paid_by"
                                            ?>
                                            <label class="radio-inline" for="<?php echo $key ?>">
                                                <input onclick="checkChequePayment(this.value)" <?php if ($key == 1) {
                                                    echo 'checked';
                                                } ?> type="radio" name="payment_methodUpdate"
                                                       id="Update_<?php echo $key ?>"
                                                       value="<?php echo $key ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '.png' ?>"/> 
                                            </label>

                                            <?php
                                        }
                                    }
                                    ?>


                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        echo '<div class="btn-group pull-right">';
                                        foreach ($paymentType as $key => $payType) {
                                            ?>
                                            <button id="payTypeBtnIDUpdate<?php echo $key ?>"
                                                    onclick="checkChequePayment(<?php echo $key ?>)" type="button"
                                                    class="btn payType <?php if ($key == 1) {
                                                        echo 'paymentTypeCustom';
                                                    } else {
                                                        echo 'btn-default';
                                                    } ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '-32.png' ?>"/>
                                                <br/>
                                                <?php echo $payType ?>
                                            </button>

                                            <?php
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                    <input type="hidden" name="payment_methodUpdate" id="payment_methodUpdate"
                                           value="1">


                                </div>
                            </div>

                            <div class="row formRowPad" id="cheque_wrpUpdate"
                                 style="display: none;">
                                <div class="col-md-6">
                                    <b>
                                        <?php echo $this->lang->line('posr_cheque_number'); ?><!--Cheque Number-->
                                    </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="chequeUpdate" class="form-control" id="chequeUpdate"
                                           placeholder="<?php echo $this->lang->line('posr_cheque_number'); ?>"
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Cheque Number-->
                                </div>
                            </div>

                            <div class="row formRowPad" id="cardRefNo_wrpUpdate"
                                 style=" display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_card_ref_no'); ?><!--Card Ref. No-->. </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="cardRefNoUpdate" class="form-control" id="cardRefNoUpdate"
                                           placeholder="<?php echo $this->lang->line('posr_card_ref_no'); ?>."
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Card Ref. No-->
                                </div>
                            </div>


                            <div class="row formRowPad" id="card_wrpUpdate"
                                 style="display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_gift_card_number'); ?><!--Gift Card Number--> </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="gift_card_numbUpdate" class="form-control"
                                           id="card_numbUpdate"
                                           placeholder="<?php echo $this->lang->line('posr_gift_card_number'); ?>"

                                           style="border: 1px solid #3a3a3a; color: #010101;">
                                </div>
                            </div>


                            <div class="row formRowPad">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Return Change-->
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="return_changeUpdate" class="ar"
                                         style=" padding:0px; font-size: 20px; font-weight: 700"></div>
                                    <input type="hidden" id="returned_changeUpdate" name="returned_changeUpdate"
                                           value="0">
                                </div>
                            </div>


                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 ">
                            <div
                                    style="padding: 10px 24px 10px 10px; border: 1px dashed grey;  border-radius: 14px;"
                                    class="hidden-xs">
                                <?php
                                $result = get_companyInfo();
                                $currencyID = $result['company_default_currencyID'];
                                $currencyCode = $result['company_default_currency'];

                                $currencies = getCurrencyNotes($currencyID);
                                ?>
                                <?php echo $this->lang->line('posr_currency_code'); ?><!--Currency Code-->:
                                <strong><?php echo $currencyCode ?></strong>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="btn-toolbar" role="toolbar">

                                            <input type="hidden" id="tmpQtyValueUpdate" value="0">
                                            <div class="row">


                                                <?php
                                                /*echo '<pre>';
                                                print_r($currencies);
                                                echo '</pre>';*/

                                                if (!empty($currencies)) {
                                                    foreach ($currencies as $currency) {

                                                        if ($currency['currencyCode'] == 'LKR') {
                                                            if ($currency['value'] > 90) {
                                                                echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                                echo '<button type="button" onclick="updateNoteValueUpdate(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                            echo '<button type="button" onclick="updateNoteValueUpdate(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                            echo '</div>';
                                                        }

                                                    }
                                                }
                                                ?>
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <button class="currencyNoteBtn pos2-btn-default"
                                                            type="button" onclick="updateExactCashUpdate();">
                                                        <?php echo $this->lang->line('posr_exact_cash'); ?><!--Exact Cash-->
                                                    </button>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                    <button type="button" onclick="updatePaidAmountUpdate(this);"
                                                            class="currencyNoteBtn pos2-btn-default">C
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid" style="padding: 10px;">
                                <button class="btn btn-lg btn-default btn-strong btn-xl" type="button"
                                        onclick="openCustomerModal()"><i
                                            class="fa fa-users"></i> Customer
                                </button>
                            </div>
                            <div class="container-fluid" style="padding: 10px;" id="deliveryCommissionDivUpdate">
                                <div class="row formRowPad deliveryRow" style="display: none;"
                                     id="deliveryPersonContainerUpdate">
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_delivery_person'); ?><!--Delivery Person-->
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6">
                                        <div>
                                            <?php
                                            $deliveryPersonArray = get_specialCustomers();
                                            ?>
                                            <select name="deliveryPersonIDUpdate" id="deliveryPersonIDUpdate"
                                                    class="form-control"
                                                    onchange="calculateReturn(this)">
                                                <option value="" selected>Select Delivery Person</option>
                                                <!--<option value="-1" data-cp="0" data-otp="1">Normal Delivery -
                                                    0%
                                                </option>-->

                                                <?php echo $this->lang->line('common_please_select'); ?><!--Please select--></option>
                                                <?php if (!empty($deliveryPersonArray)) {
                                                    foreach ($deliveryPersonArray as $val) {
                                                        ?>
                                                        <option value="<?php echo $val['customerID'] ?>"
                                                                data-cp="<?php echo $val['commissionPercentage'] ?>"
                                                                data-otp="<?php echo $val['isOnTimePayment'] ?>">
                                                            <?php echo $val['customerName'] ?>
                                                            - <?php echo $val['commissionPercentage'] ?>%
                                                        </option>
                                                        <?php
                                                    }
                                                } ?>

                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_amount'); ?><!--Net Total Payable Amount-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="totalPayableAmountDelivery_idUpdate"
                                               name="totalPayableAmountDelivery_idUpdate"
                                               value="0">
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Net Return Change-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="returned_change_toDeliveryUpdate"
                                               name="returned_change_toDeliveryUpdate"
                                               value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" onclick="close_update_pos_submitted()"
                        style="height: 57px;">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                <button id="" type="button" onclick="update_pos_submitted_payments()" class="btn btn-lg btn-primary"
                        style="height: 57px;">
                    <span id="">Update</span>
                </button>

            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_payments_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg"
         style="width: <?php //echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '70%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_payment'); ?><!--Payment--></h3>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form method="post" id="frm_pos_invoice_submit" class="form_pos_receipt">
                    <input type="hidden" name="isCreditSale" id="isCreditSale" value="0">
                    <input type="hidden" name="customerTelephone" id="customerTelephone">
                    <input type="hidden" name="customerName" id="customerName">
                    <input type="hidden" name="customerAddress" id="customerAddress">
                    <input type="hidden" name="customerCountry_o" id="customerCountry_o">
                    <input type="hidden" name="customerCountryCode_o" id="customerCountryCode_o">
                    <input type="hidden" name="customerCountryId_o" id="customerCountryId_o">
                    <input type="hidden" name="customerEmail" id="customerEmail">
                    <input type="hidden" name="customerID" id="customerID">
                    <input type="hidden" name="cardTotalAmount" id="cardTotalAmount" value="0"/>
                    <input type="hidden" name="netTotalAmount" id="netTotalAmount" value="0"/>
                    <input type="hidden" name="isDelivery" id="isDelivery" value="0"/>
                    <input type="hidden" name="isOnTimePayment" id="frm_isOnTimePayment" value=""/>
                    <input type="hidden" name="is_delivery_info_exist" id="is_delivery_info_exist" value=""/>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_gross_total'); ?><!--Gross Total -->

                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payable_amt" class="ar payment-textLg"
                                         style="padding: 5px 0px;">0.00
                                    </div>
                                    <input type="hidden" name="total_payable_amt" id="total_payable_amt" value="0">
                                </div>
                            </div>


                            <!--promotion Row-->
                            <div class="row formRowPad" id="deliveryPersonContainer"> <!--promotionRow-->
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 payment-font"
                                     style="">
                                    <button class="pos2-btn-default p-disc-btn" type="button"
                                            onclick="openPromotionModal()">
                                        <?php echo $this->lang->line('posr_promotion'); ?> </button>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                                    <div>
                                        <input type="text" id="tmp_promotion" readonly value="" class="form-control">
                                        <?php
                                        $deliveryPersonArray = get_specialCustomers(array(2, 3));

                                        ?>
                                        <select name="promotionID" style="display: none;" id="promotionID"
                                                class="form-control"
                                                onchange="calculateReturn(this)">
                                            <option value="">
                                                <?php echo $this->lang->line('common_non'); ?><!--None--></option>
                                            <?php if (!empty($deliveryPersonArray)) {
                                                foreach ($deliveryPersonArray as $val) {
                                                    ?>
                                                    <option value="<?php echo $val['customerID'] ?>"
                                                            data-cp="<?php echo $val['commissionPercentage'] ?>">
                                                        <?php echo $val['customerName'] . ' - ' . $val['commissionPercentage'] . ' %'; ?>

                                                    </option>
                                                    <?php
                                                }
                                            } ?>

                                        </select>

                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 payment-font">
                                    <div class="p-disc-container mrg-top5">
                                        <?php echo $this->lang->line('posr_discount'); ?><!--Promotional Discount-->
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                                    <div class="mrg-top5">
                                        <input type="text" readonly name="promotional_discount"
                                               id="promotional_discount"
                                               class="form-control input-sm ar payment-inputTextMedium"
                                               value="<?php echo number_format(0, $d) ?>">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="owdAllowed" value="<?php echo $odwAllowed; ?>">

                            <?php if ($isOutletTaxEnabled == "true") { ?>
                                <div class="row formRowPad" style="padding: 1px;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                        Outlet Tax
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <div id="outlet_tax_in_invoice" class="ar payment-textLg"
                                             style="padding: 5px 0px;">
                                            <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row formRowPad" style="padding: 1px;" id="own_delivery_div">
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 payment-font">
                                    <button class="pos2-btn-default p-disc-btn" type="button"
                                            onclick="show_own_delivery_modal()">
                                        Delivery
                                    </button>
                                </div>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 payment-font">
                                    <input type="text" id="own_delivery_type" style="width:100%" class="form-control"
                                           disabled/>
                                </div>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-3 payment-font">
                                    <input type="hidden" id="isOwnDelivery" value="0"/>
                                    <?php
                                    $delivery_persons = get_delivery_persons();
                                    ?>
                                    <select id="own_delivery_person" class="form-control" style="width:100%">
                                        <option value="" selected>Select Delivery Person</option>
                                        <?php if (!empty($delivery_persons)) {
                                            foreach ($delivery_persons as $val) {
                                                ?>
                                                <option value="<?php echo $val['crewMemberID'] ?>">
                                                    <?php echo $val['crewFirstName'] ?>
                                                </option>
                                                <?php
                                            }
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 payment-font">
                                    <input type="text" id="own_delivery_percentage"
                                           onchange="own_delivery_percentage_change()" class="form-control"
                                           style="width:74%;display: inline-block;"/>&nbsp;<span
                                            style="display: inline-block">%</span>
                                </div>
                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 payment-font">
                                    <input type="text" id="own_delivery_amount" onchange="own_delivery_amount_change()"
                                           class="form-control"
                                           style="width:100%"/>
                                </div>
                            </div>

                            <div class="row formRowPad" style="padding: 1px;" id="advancePaidDiv">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    Advance Paid
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="delivery_advancePaymentAmountShow" class="ar payment-textLg"
                                         style="padding: 5px 0px;">
                                        <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                    </div>
                                    <input type="hidden" name="delivery_advancePaymentAmount"
                                           id="delivery_advancePaymentAmount" value="0">
                                </div>
                            </div>

                            <!-- Net Total -->
                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-6 col-sm-8 col-md-6 col-lg-6"
                                     style="font-weight: 800; font-size: 20px;"><b>
                                        <?php echo $this->lang->line('posr_net_total'); ?><!--Net Total --></b>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payableNet_amt" class="ar text-red"
                                         style="padding: 5px 0px; font-weight: 800; font-size: 20px;">0.00
                                    </div>
                                </div>
                            </div>

                            <table class="<?php echo table_class_pos() ?>">
                                <?php
                                $payments = get_paymentMethods_GLConfig();
                                foreach ($payments as $payment) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <!---->
                                                    <div style="padding:  4px;"
                                                         class="tbl-payment-font">
                                                        <input type="hidden"
                                                               name="customerAutoID[<?php echo $payment['ID'] ?>]"
                                                               id="customerAutoID<?php echo $payment['ID'] ?>">
                                                        <?php echo $payment['description']; ?>
                                                        <?php //echo $payment['autoID']; ?>
                                                    </div>
                                                </div>
                                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                    <?php //print_r($payment)
                                                    /** GIFT CARD */
                                                    if ($payment['autoID'] == 5) {
                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar gitCardRefNo"
                                                               id="reference_<?php echo $payment['ID']; ?>"
                                                               name="reference[<?php echo $payment['ID'] ?>]" readonly
                                                               placeholder="Gift Card"/>
                                                        <?php
                                                        /** CREDIT SALES */
                                                    } else if ($payment['autoID'] == 7) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar CreditSalesRefNo"
                                                               id="reference_<?php echo $payment['ID']; ?>"
                                                               name="reference[<?php echo $payment['ID'] ?>]"
                                                               placeholder="Reference"/>
                                                        <?php
                                                        /** JAVA APP */
                                                    } else if ($payment['autoID'] == 25) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar javaAppRefNo"
                                                               id="reference_<?php echo $payment['ID']; ?>"
                                                               name="reference[<?php echo $payment['ID'] ?>]" readonly
                                                               placeholder="App PIN"/>
                                                        <?php

                                                    } else {
                                                        if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                            if ($payment['autoID'] != 32) {
                                                                ?>
                                                                <input type="text" value=""
                                                                       class="form-control cardRef ar numpad"
                                                                       id="reference_<?php echo $payment['ID']; ?>"
                                                                       name="reference[<?php echo $payment['ID'] ?>]"
                                                                       placeholder="Ref#"/>
                                                                <?php
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td> <!--style="width:50px;"-->
                                            <?php

                                            if ($payment['autoID'] == 5) {
                                                /** Gift Card  */
                                                $onclick = ' onclick="openGiftCardRedeemModal()" ';
                                            } else if ($payment['autoID'] == 7) {
                                                /** Credit Sales */
                                                //$onclick = ' onclick="openCreditSalesModal(' . $payment['ID'] . ')" ';
                                                $onclick = ' onclick="checkPosAuthentication(11,' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 1) {
                                                /** Cash */
                                                $onclick = ' onclick="updateExactCard(1)" ';
                                            } else if ($payment['autoID'] == 46 || $payment['autoID'] == 53 || $payment['autoID'] == 52 || $payment['autoID'] == 51 || $payment['autoID'] == 34 || $payment['autoID'] == 40 || $payment['autoID'] == 41 || $payment['autoID'] == 33 || $payment['autoID'] == 44 || $payment['autoID'] == 43 || $payment['autoID'] == 37 || $payment['autoID'] == 2 || $payment['autoID'] == 38 || $payment['autoID'] == 39 || $payment['autoID'] == 35 || $payment['autoID'] == 26 || $payment['autoID'] == 36 || $payment['autoID'] == 3 || $payment['autoID'] == 4 || $payment['autoID'] == 6 || $payment['autoID'] == 1 || $payment['autoID'] == 27 || $payment['autoID'] == 28 || $payment['autoID'] == 29 || $payment['autoID'] == 30 || $payment['autoID'] == 31 || $payment['autoID'] == 54) {
                                                /** 3 Master Card | 4 Visa Card | 6 AMEX | 27 FriMi  | 28 Ali Pay | 36 Akeed*/
                                                $onclick = ' onclick="updateExactCard(' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 25) {
                                                /** java App */
                                                $onclick = ' onclick="openJavaAppModal(' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 42) {
                                                /** java App */
                                                $onclick = ' onclick="loadLoyaltyModal(' . $payment['ID'] . ')" ';
                                            } else {
                                                $onclick = 'onclick="updateExactCard(' . $payment['ID'] . ')" ';
                                            }
                                            ?>
                                            <?php
                                            if ($payment['autoID'] != 32) { ?>
                                                <button class="btn btn-default btn-block" <?php echo $onclick ?>
                                                        type="button"
                                                        style="padding: 0px;">
                                                    <img src="<?php echo base_url($payment['image']); ?>"
                                                         style="max-height: 27px;">
                                                </button>
                                            <?php } ?>

                                        </td>
                                        <td class="payment-tblSize">
                                            <?php
                                            if ($payment['autoID'] == 5) {
                                                /** GIFT CARD */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       onclick="openGiftCardRedeemModal()"
                                                       class="form-control al payment-inputTextMedium giftCardPayment paymentInput <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php

                                            } else if ($payment['autoID'] == 7) {
                                                /** CREDIT SALE  */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       onclick="checkPosAuthentication(11,<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium creditSalesPayment paymentInput <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php

                                            } else if ($payment['autoID'] == 25) {
                                                /** JAVA APP */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       onclick="openJavaAppModal(<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium javaAppPayment paymentInput <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php

                                            } else if ($payment['autoID'] == 32) {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                /** Round OFF */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       class="form-control al payment-inputTextMedium paymentInput rundoff <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?> "
                                                       placeholder="<?php echo number_format(0, $d) ?>" readonly>
                                                <?php

                                            } else if ($payment['autoID'] == 42) {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                /** Round OFF */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       style="background-color: #eee;cursor: pointer;" readonly
                                                       class="form-control al payment-inputTextMedium paymentInput loyaltyRedeemAmount <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>"
                                                       placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php

                                            } else {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                if ($payment["isAuthRequired"]) {
                                                    ?>
                                                    <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                           name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                           onchange="checkPosAuthentication(10,this)"
                                                           class="form-control al payment-inputTextMedium paymentInput numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php } else {
                                                    ?>
                                                    <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                           name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                           onchange="calculatePaidAmount(this)"
                                                           class="form-control al payment-inputTextMedium paymentInput numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="<?php echo number_format(0, $d) ?>">

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>


                                    <?php
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div style="padding:  8px; "
                                             class="tbl-payment-font">
                                            <?php echo $this->lang->line('posr_paid_amount'); ?><!--Paid Amount--></div>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td class="payment-tblSize">
                                        <input readonly type="number"
                                               onkeyup="calculateReturn()" name="paid"
                                               id="paid"
                                               class="form-control payment-inputTextLg paymentTypeTextRed al"
                                               placeholder="0.00"
                                               autocomplete="off">
                                        <span id="paid_temp" class="hide"></span></td>
                                </tr>
                            </table>


                            <div class="row formRowPad hide">
                                <div class="col-md-4 lbl-delivery"></div>


                                <div class="col-md-8">
                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        foreach ($paymentType as $key => $payType) {
                                            //id="paid_by"
                                            ?>
                                            <label class="radio-inline" for="<?php echo $key ?>">
                                                <input onclick="checkChequePayment(this.value)" <?php if ($key == 1) {
                                                    echo 'checked';
                                                } ?> type="radio" name="payment_method" id="<?php echo $key ?>"
                                                       value="<?php echo $key ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '.png' ?>"/>
                                            </label>

                                            <?php
                                        }
                                    }
                                    ?>


                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        echo '<div class="btn-group pull-right">';
                                        foreach ($paymentType as $key => $payType) {
                                            ?>
                                            <button id="payTypeBtnID<?php echo $key ?>"
                                                    onclick="checkChequePayment(<?php echo $key ?>)" type="button"
                                                    class="btn payType <?php if ($key == 1) {
                                                        echo 'paymentTypeCustom';
                                                    } else {
                                                        echo 'btn-default';
                                                    } ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '-32.png' ?>"/>
                                                <br/>
                                                <?php echo $payType ?>
                                            </button>

                                            <?php
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                    <input type="hidden" name="payment_method" id="payment_method" value="1">


                                </div>
                            </div>

                            <div class="row formRowPad" id="cheque_wrp"
                                 style="display: none;">
                                <div class="col-md-6">
                                    <b>
                                        <?php echo $this->lang->line('posr_cheque_number'); ?><!--Cheque Number-->
                                    </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="cheque" class="form-control" id="cheque"
                                           placeholder="<?php echo $this->lang->line('posr_cheque_number'); ?>"
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Cheque Number-->
                                </div>
                            </div>

                            <div class="row formRowPad" id="cardRefNo_wrp"
                                 style=" display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_card_ref_no'); ?><!--Card Ref. No-->. </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="cardRefNo" class="form-control" id="cardRefNo"
                                           placeholder="<?php echo $this->lang->line('posr_card_ref_no'); ?>."
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Card Ref. No-->
                                </div>
                            </div>


                            <div class="row formRowPad" id="card_wrp"
                                 style="display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_gift_card_number'); ?><!--Gift Card Number--> </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="gift_card_numb" class="form-control" id="card_numb"
                                           placeholder="<?php echo $this->lang->line('posr_gift_card_number'); ?>"
                                    <!--Gift Card Number-->
                                    style="border: 1px solid #3a3a3a; color: #010101;">
                                </div>
                            </div>


                            <div class="row formRowPad">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Return Change-->
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="return_change" class="ar"
                                         style=" padding:0px; font-size: 20px; font-weight: 700"></div>
                                    <input type="hidden" id="returned_change" name="returned_change" value="0">
                                </div>
                            </div>


                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 ">
                            <div
                                    style="padding: 10px 24px 10px 10px; border: 1px dashed grey;  border-radius: 14px;"
                                    class="hidden-xs">
                                <?php
                                $result = get_companyInfo();
                                $currencyID = $result['company_default_currencyID'];
                                $currencyCode = $result['company_default_currency'];

                                $currencies = getCurrencyNotes($currencyID);
                                ?>
                                <?php echo $this->lang->line('posr_currency_code'); ?><!--Currency Code-->:
                                <strong><?php echo $currencyCode ?></strong>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="btn-toolbar" role="toolbar">

                                            <input type="hidden" id="tmpQtyValue" value="0">
                                            <div class="row">


                                                <?php
                                                /*echo '<pre>';
                                                print_r($currencies);
                                                echo '</pre>';*/

                                                if (!empty($currencies)) {
                                                    foreach ($currencies as $currency) {

                                                        if ($currency['currencyCode'] == 'LKR') {
                                                            if ($currency['value'] > 90) {
                                                                echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                                echo '<button type="button" onclick="updateNoteValue(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                            echo '<button type="button" onclick="updateNoteValue(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                            echo '</div>';
                                                        }

                                                    }
                                                }
                                                ?>
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <button class="currencyNoteBtn pos2-btn-default"
                                                            type="button" onclick="updateExactCash();">
                                                        <?php echo $this->lang->line('posr_exact_cash'); ?><!--Exact Cash-->
                                                    </button>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                    <button type="button" onclick="updatePaidAmount(this);"
                                                            class="currencyNoteBtn pos2-btn-default">C
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid" style="padding: 10px;">
                                <button class="btn btn-lg btn-default btn-strong btn-xl" type="button"
                                        onclick="openCustomerModal()"><i
                                            class="fa fa-users"></i> Customer
                                </button>
                            </div>

                            <div style="padding: 10px 24px 10px 10px; border: 1px dashed grey;  border-radius: 14px;"
                                 class="hidden-xs" id="customer_loyalty_details_div">


                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Loyality card No :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="customer_loyalty_card_number"> - </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Available Points :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="customer_loyalty_balance">-</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Available Amount :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="customer_loyalty_amount">-</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Earned Points :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <!-- <input type="hidden" id="earnedpoints_val" value="0">-->
                                            <label id="customer_loyalty_earned">-</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Earned Amount :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <!-- <input type="hidden" id="earnedpoints_val" value="0">-->
                                            <label id="customer_loyalty_earned_amount">-</label>
                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="container-fluid" style="padding: 10px;" id="deliveryCommissionDiv">
                                <div class="row formRowPad deliveryRow" style="display: none;"
                                     id="deliveryPersonContainer">
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_delivery_person'); ?><!--Delivery Person-->
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6">
                                        <div>
                                            <?php
                                            $deliveryPersonArray = get_specialCustomers();
                                            ?>
                                            <select name="deliveryPersonID" id="deliveryPersonID" class="form-control"
                                                    onchange="calculateReturn(this)">
                                                <option value="" selected>Select Delivery Person</option>
                                                <!--<option value="-1" data-cp="0" data-otp="1">Normal Delivery -
                                                    0%
                                                </option>-->

                                                <?php echo $this->lang->line('common_please_select'); ?><!--Please select--></option>
                                                <?php if (!empty($deliveryPersonArray)) {
                                                    foreach ($deliveryPersonArray as $val) {
                                                        ?>
                                                        <option value="<?php echo $val['customerID'] ?>"
                                                                data-cp="<?php echo $val['commissionPercentage'] ?>"
                                                                data-otp="<?php echo $val['isOnTimePayment'] ?>">
                                                            <?php echo $val['customerName'] ?>
                                                            - <?php echo $val['commissionPercentage'] ?>%
                                                        </option>
                                                        <?php
                                                    }
                                                } ?>

                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_amount'); ?><!--Net Total Payable Amount-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="totalPayableAmountDelivery_id" name="totalPayableAmountDelivery_id"
                                               value="0">
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Net Return Change-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="returned_change_toDelivery" name="returned_change_toDelivery"
                                               value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal" style="height: 57px;">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                <?php
                if (isset($sampleBillPolicy) && $sampleBillPolicy) {
                    ?>
                    <button type="button" onclick="print_sample_bill()" class="btn btn-lg btn-default"
                            style="height: 57px;">
                        <i class="fa fa-print"></i>Print Sample
                    </button>
                    <?php
                }
                ?>

                <button id="submit_and_close_btn" type="button" onclick="submit_and_close_pos_payments()"
                        class="btn btn-lg btn-default" style="height: 57px;">
                    <span><?php echo $this->lang->line('common_submit_and_close'); ?><!--Submit--></span>
                </button>

                <button id="submit_btn" type="submit" onclick="submit_pos_payments()" class="btn btn-lg btn-primary"
                        style="background-color: #3fb618; color: #FFF; border: 0px; float: right; display: none;height: 57px;">
                    <span
                            id="submit_btn_pos_receipt"><?php echo $this->lang->line('common_submit_and_print'); ?><!--Submit--></span>
                </button>


            </div>
        </div>
    </div>
</div>

<!--EMAIL MODAL -->
<div aria-hidden="true" role="dialog" tabindex="1" style="z-index: 9999;" id="email_modal" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_enter_email'); ?><!--Enter Email--> </h4>
            </div>
            <div class="modal-body" id="" style="">
                <form method="post" id="frm_print_email_address">
                    <input type="hidden" name="invoiceID" id="email_invoiceID" value="0">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="email" class="form-control" id="emailAddress" name="emailAddress">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="margin-top: 0px; padding: 7px;">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                <button type="button" onclick="send_pos_email()" class="btn btn-sm btn-primary"
                        style="background-color: #3fb618; color: #FFF; border: 0px; float: right;">
                    <span id=""><?php echo $this->lang->line('common_submit'); ?><!--Submit--></span>
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="1" style="z-index: 9999;" id="order_mode_modal" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Please select order mode</h4>
            </div>
            <div class="modal-body" id="" style="">
                <div style="text-align: center;margin: 35px;">
                    <?php
                    $customerType = getCustomerType();

                    if (!empty($customerType)) {
                        ?>
                        <input type="hidden" id="customerType" name="customerType" value="">
                        <input type="hidden" id="is_dine_in" name="is_dine_in" value="0">
                        <div class="order-type-btn-group">
                            <div class="btn-group btn-group-lg">
                                <?php
                                $defaultID = 0;
                                $isDelivery = 0;
                                $isDineIn = 0;
                                foreach ($customerType as $val) {
                                    ?>
                                    <button type="button" data-val="<?php echo $val['customerDescription'] ?>"
                                            onclick="updateCustomerTypeBtn(<?php echo $val['customerTypeID']; ?>,<?php echo $val['isThirdPartyDelivery'] ?>,<?php echo $val['isDineIn'] ?>,1)"
                                            class="btn buttonCustomerType buttonDefaultSize <?php if ($val['isDefault'] == 1) {
                                                $defaultID = $val['customerTypeID'];
                                                $isDelivery = $val['isThirdPartyDelivery'];
                                                $isDineIn = $val['isDineIn'];
                                                //echo 'btn-primary';
                                                echo 'btn-default';
                                            } else {
                                                echo 'btn-default';
                                            }
                                            ?>  customerType"
                                            id="customerTypeID_<?php echo $val['customerTypeID']; ?>">
                                        <?php echo $val['displayDescription']; ?>
                                    </button>
                                <?php }

                                ?>
                            </div>
                            <script>
                                function defaultDelivaryButton() {
                                    <?php
                                    if($defaultID){
                                    ?>
                                    updateCustomerTypeBtn(<?php echo $defaultID ?>, <?php echo $isDelivery ?>,<?php echo $isDineIn ?>, 1);
                                    <?php
                                    }
                                    ?>
                                }

                                $(document).ready(function (e) {
                                    defaultDineinButtonID = '<?php echo $defaultID; ?>';
                                });
                            </script>
                        </div>
                    <?php } ?>
                    <hr>
                    <div class="btn-group btn-group-lg">
                        <?php

                            if (isset($waiters)) {
                                $waiterIndex = 1;
                                foreach ($waiters as $waiter) {


                                    ?>

                                    <div class="col-md-4">
                                        <input type="button" class="btn btn-default waiterBtn"
                                               style="width: 100%;margin: 5px 0;height: 64px;min-width: 155px;"
                                               data-emp_id="<?php echo $waiter['crewMemberID']; ?>"
                                               onclick="markThisWaiterAsSelectedOrderModel.call(this)"
                                               value="<?php echo $waiter['crewFirstName']; ?>"/>

                                    </div>
                                    <?php


                                }
                            }


                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="shiftCloseReportModal" class="modal fade" data-keyboard="false"
     style="display: none;">
    <div class="modal-dialog" style="width: 71%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id=""> <!--style="background-color: #373942; color:#ffffff;"-->
                <button type="button" class="close tillModal_close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="">Report</h5>
            </div>
            <div class="modal-body" style="padding: 10px; height: auto">
                <div id="shiftCloseReport"></div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <a href="<?php echo site_url('dashboard') ?>" class="btn btn-default btn-xs">Close</a>
            </div>
        </div>
    </div>
</div>


<script>

    var globalVar = '';
    var defaultDineinButtonID = 0;
    var app = {}
    app.segmentconfig_posTemplateID = <?php echo $segmentconfig_posTemplateID; ?>;

    function updateNoteValueUpdate(tmpValue) {
        var noteValue = $(tmpValue).text();
        $("#paid_tempUpdate").html(noteValue);
        $("#paidUpdate").val(parseFloat(noteValue));
        $("#paymentType_Update1").val(parseFloat(noteValue));
        calculateReturnUpdate();
        calculatePaidAmountUpdate();
    }

    function updateNoteValue(tmpValue) {

        var noteValue = $(tmpValue).text();

        $("#paid_temp").html(noteValue);
        $("#paid").val(parseFloat(noteValue));
        $("#paymentType_1").val(parseFloat(noteValue));
        /*$("#paid").focus();*/
        calculateReturn();
        calculatePaidAmount();
        clearCreditSales();
    }


    function updatePaidAmountUpdate(tmpValue) {
        var cPaidAmount = $("#paidUpdate").val();
        var tmpAmount = $(tmpValue).text();
        var tmpAmount_txt = $("#paid_tempUpdate").text();
        if (parseFloat(tmpAmount) >= 0 || $.trim(tmpAmount) == '.') {
            var updateVal = cPaidAmount + tmpAmount;
            var tmpAmount_output = $.trim(tmpAmount_txt) + $.trim(tmpAmount);
            if ($.trim(tmpAmount) == '.') {
            }

            $("#paid_tempUpdate").html(tmpAmount_output);
            $("#paidUpdate").val(parseFloat(tmpAmount_output));

        } else if ($.trim(tmpAmount) == 'C') {
            $("#isCreditSaleUpdate").val(0);
            $("#paidUpdate").val(0);
            $("#paid_tempUpdate").html(0);
            $(".paymentInputupdate").val(0);
            $('.cardRef').val('');
        }
        calculateReturnUpdate();
    }

    function updatePaidAmount(tmpValue) {
        var cPaidAmount = $("#paid").val();
        var tmpAmount = $(tmpValue).text();
        var tmpAmount_txt = $("#paid_temp").text();
        if (parseFloat(tmpAmount) >= 0 || $.trim(tmpAmount) == '.') {
            var updateVal = cPaidAmount + tmpAmount;
            var tmpAmount_output = $.trim(tmpAmount_txt) + $.trim(tmpAmount);
            if ($.trim(tmpAmount) == '.') {
            }

            $("#paid_temp").html(tmpAmount_output);
            $("#paid").val(parseFloat(tmpAmount_output));

        } else if ($.trim(tmpAmount) == 'C') {
            $("#isCreditSale").val(0);
            $("#paid").val(0);
            $("#paid_temp").html(0);
            $(".paymentInput").val(0);
            $('.cardRef').val('');
        }
        calculateReturn();
    }

    function updateExactCashUpdate() {
        $(".paymentInputupdate").val('');
        var totalAmount = $("#final_payableNet_amtUpdate").text();
        $("#paidUpdate").val(parseFloat(totalAmount));
        $("#paymentType_Update1").val(parseFloat(totalAmount).toFixed(<?php echo $d ?>));
        calculateReturnUpdate();
    }

    function updateExactCash() {
        $(".paymentInput").val('');
        var totalAmount = $("#final_payableNet_amt").text();
        $("#paid").val(parseFloat(totalAmount));
        $("#paymentType_1").val(parseFloat(totalAmount).toFixed(<?php echo $d ?>));
        calculateReturn();
        clearCreditSales();

    }

    function resetPaymentForm() {
        $("#cardRefNo").val("");
        $("#paid").val("");
        $("#customerName").val("");
        $("#deliveryPersonID").val("");
        $("#customerTelephone").val("");
        $("#customerAddress").val("");
        $("#customerCountryCode").val("");
        $("#customerCountryId").val("");
        $("#customerCountry").val("");
        $('#customerCountry').select2().trigger('change');
        $("#customerID").val("");
        $("#paid_temp").html('');
        $("#isDelivery").html(0);
        $("#frm_isOnTimePayment").html('');
        $("#netTotalAmount").html(0);
        $("#cardTotalAmount").html(0);
        $(".cardRef").val('');
        $("#loyalitycardno").val('');
        $("#delivery_advancePaymentAmount").val(0);
        $("#delivery_advancePaymentAmountShow").html('0.00');
        $("#current_table_description").text('Table');
        resetKotButton();
        reset_paymentMode();
    }

    function open_pos_submitted_payments_modal() {
        <?php
        if (isset($template) && $template == 'general') { ?>
        $("#customerType").val(terminalGlobalVariables.dineInId);
        <?php }
        ?>
        /*handling exception*/
        // handleItemMisMatchException();

        var gross_total = parseFloat($("#gross_total").html());
        var customerType = $("#customerType").val();
        if (customerType > 0) {
            $("#pos_submitted_payments_modal").modal('show');
            //$("#paid_by").select2("val", "");

            setTimeout(function () {
                calculateReturn();
                $("#paid").focus();
            }, 500);
        } else {
            //bootbox.alert('<div class="alert alert-info"><strong>Please select order mode.</strong></div>');
            $("#order_mode_modal").modal("show");
        }

    }

    function open_pos_payments_modal() {
        var customerID = $('#customerID').val();
        if (customerID > 0) {
            $("#customer_loyalty_details_div").show();
        } else {
            $("#customer_loyalty_details_div").hide();
        }

        <?php
        if (isset($template) && $template == 'general') { ?>
        $("#customerType").val(terminalGlobalVariables.dineInId);
        <?php }
        ?>
        /*handling exception*/
        handleItemMisMatchException();
        loyalty_points_details();

        var gross_total = parseFloat($("#gross_total").html());
        var customerType = $("#customerType").val();

        if (gross_total > 0) {
            var isWaiterMandatory = <?php echo $this->pos_policy->waiterSelectionMandatory() ? $this->pos_policy->waiterSelectionMandatory() : 0; ?>;
            var customerType = $("#customerType").val();
            var customerTypeString = terminalGlobalVariables.cusTypeArray[customerType];
            var pba = <?php echo $pinBasedAccess?$pinBasedAccess:0; ?>;
            if(pba==0 && (terminalGlobalVariables.selectedWaiter == null || terminalGlobalVariables.selectedWaiter==0) && isWaiterMandatory){
                $("#order_mode_modal").modal("show");
            } else if (customerType > 0 || app.segmentconfig_posTemplateID == 2) {
                $("#pos_payments_modal").modal('show');
                //$("#paid_by").select2("val", "");

                setTimeout(function () {
                    calculateReturn();
                    $("#paid").focus();
                }, 500);
            } else {
                //bootbox.alert('<div class="alert alert-info"><strong>Please select order mode.</strong></div>');
                $("#order_mode_modal").modal("show");
            }

        } else {
            bootbox.alert('<div class="alert alert-info"><strong><?php echo $this->lang->line('posr_no_menus_added_to_invoice_please_add_at_least_one_item');?>.</strong></div>');
            <!--No menus added to Invoice, please add at least one item-->
        }
    }

    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span><img src="<?php echo base_url()?>images/payment_type/' + state.element.value.toLowerCase() + '.png" class="img-flag" />  ' + state.text + '</span>'
        );
        return $state;
    }

    function deliveryValidation() {
        var isDelivery = $("#isDelivery").val();
        var deliveryPersonID = $("#deliveryPersonID").val();
        if ((isDelivery == 1 && (deliveryPersonID == 0 || deliveryPersonID == '' || deliveryPersonID == null))) {
            myAlert('e', 'Please select delivery person before add payments!');
            return false;
        } else {
            return true;
        }
    }

    $(document).ready(function (e) {
        $(".paymentInput").change(function (e) {
            var validation = deliveryValidation();
            if (validation) {
                setTimeout(function () {
                    calculateDelivery();
                }, 100);
            }
        });

        $('#pos_payments_modal').on('hidden.bs.modal', function (e) {
            if ($("#holdInvoiceID_input").val() == 0) {
                resetPaymentForm()
            }
        });
        $('#pos_payments_modal').on('shown.bs.modal', function (e) {
            setTimeout(function () {
            }, 100);
        });


        $("#paid").keyup(function (e) {
            if (e.keyCode == 13) {
                var tmpVisible = $("#submit_btn").is(":visible");
                if (tmpVisible) {
                    submit_pos_payments();
                }
            }
        });


            $("#own_delivery_person").prop("disabled", true);
            $("#own_delivery_percentage").prop("disabled", true);
            $("#own_delivery_amount").prop("disabled", true);

    });

    function clearCustomerTypeButtons() {
        $(".customerType").removeClass('btn-primary');
        $(".customerType").addClass('btn-default');
    }

    function selectCustomerButton(id) {
        $(".customerType").removeClass('btn-primary');
        $(".customerType").removeClass('btn-custype-selected');//this is only affeted in new theme.
        $(".customerType").addClass('btn-default');
        $("#customerTypeID_" + id).removeClass('btn-default');
        $("#customerTypeID_" + id).addClass('btn-primary');
        $("#customerTypeID_" + id).addClass('btn-custype-selected');//this is only affeted in new theme.
        var lifeThemeParentClass = $("#customerTypeID_" + id).parent().prop('className');
        if(lifeThemeParentClass=='info-item-body'){
            $(".info-item-body").removeClass('btn-custype-selected');
            $("#customerTypeID_" + id).removeClass('btn-primary');
            $("#customerTypeID_" + id).removeClass('btn-custype-selected');
            $("#customerTypeID_" + id).parent().addClass('btn-custype-selected');
        }
    }


    function updateCustomerTypeBtn(id, isDelivery, isDineIn, ordermd = 0) {

        $("#is_dine_in").val(isDineIn);
        $("#customerType").val(id);
        var customerType = id;
        var deliveryType = $("#customerTypeID_" + id).html();

        var tmpDeliveryTxt = $('#customerTypeBtnString').val(deliveryType.trim())


        if (deliveryType.trim() == "Delivery Orders") {
            openDeliveryModal();
            $(".deliveryRow").show();
            if ($("#owdAllowed").val() == 1) {
                $("#own_delivery_div").show();
            } else {
                $("#own_delivery_div").hide();
            }
            $(".deliveryPromotionRow").show();
            $(".promotionRow").hide();
            $('select[name="deliveryPersonID"]').attr('id', 'deliveryPersonID')
            $('select[name="promotionID"]').attr('id', 'promotionID')
            $("#isDelivery").val(1);
            $("#deliveryDateDiv").show();
            $("#delivery_customerTypeID").val(id);
            load_delivery_info();
            if ($("#deliveryOrderID").val() > 0) {
                $("#advancePaidDiv").show();
            } else {
                $("#advancePaidDiv").hide();
            }
            $("#deliveryPersonID").val('').change();
            $('#delivery_update_btn_div').hide();
        } else if (deliveryType.trim() == "Promotion") {
            $(".promotionRow").show()
            $(".deliveryPromotionRow").show()
            $(".deliveryRow").hide()
            $("#own_delivery_div").hide();
            $('select[name="deliveryPersonID"]').attr('id', 'deliveryPersonID')
            $('select[name="promotionID"]').attr('id', 'promotionID')
            $("#isDelivery").val(0);
            $("#isOwnDelivery").val(0);
            $("#deliveryDateDiv").hide();
            $("#advancePaidDiv").hide();
        } else {
            $(".promotionRow,.deliveryRow,.deliveryPromotionRow").hide();
            $("#own_delivery_div").hide();
            $("#isDelivery").val(0);
            $("#isOwnDelivery").val(0);
            $("#deliveryDateDiv").hide();
            $("#advancePaidDiv").hide();
        }

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/updateCustomerType'); ?>",
            data: {customerType: customerType},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                selectCustomerButton(id);
                stopLoad();

                if (data['error'] == 0) {
//                    myAlert('s', data['message']);
                    calculateFooter('A');

                    if (ordermd == 1) {

                        var isWaiterMandatory = <?php echo $this->pos_policy->waiterSelectionMandatory() ? $this->pos_policy->waiterSelectionMandatory() : 0; ?>;
                        var pba = <?php echo $pinBasedAccess?$pinBasedAccess:0; ?>;
                        if(pba){
                            $("#order_mode_modal").modal("hide");
                            open_pos_payments_modal();
                        }else{

                            if (isWaiterMandatory == 1 && (terminalGlobalVariables.selectedWaiter == null || terminalGlobalVariables.selectedWaiter==0)) {

                            } else {

                                $("#order_mode_modal").modal("hide");
                                open_pos_payments_modal();
                            }

                        }

                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad()
                if (jqXHR.status == false) {
                    myAlert('w', 'No Interent: Please try again');
                }
            }
        });
        return false;
    }

    function openemailPrintmodule() {
        $("#email_modal").modal('show');
    }

    function send_pos_email() {

        var email = $('#emailAddress').val();
        if (validateEmail(email)) {

            var data = $('#frm_print_email_address').serializeArray();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/save_send_pos_email'); ?>",
                data: data,
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 0) {
                        $("#email_modal").modal('hide');
                        myAlert('s', 'Message: ' + "Email Sent Successfully");
                    } else {
                        if (data['message'] == 'Mailer Error: You must provide at least one recipient email address.') {
                            myAlert('w', 'Please enter a valid email');
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();

                }
            });

        } else {
            myAlert('w', 'Please enter a valid email');
        }


    }

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

</script>


<!-- CUSTOMER MODAL  -->
<div aria-hidden="true" role="dialog" id="pos_payments_customer_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" id="frm_pos_customer">
                <div class="modal-header posModalHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-close text-red"></i></button>
                    <h4><i class="fa fa-users"></i> Customer </h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="customerTelephoneTmp">
                            <?php //echo $this->lang->line('posr_customer_telephone'); ?>Telephone</label>
                        <div class="col-md-2">
                            <input type="text" name="customerCountryCode" id="customerCountryCode"
                                   class="form-control text-right" value="<?php echo $companycountry['countryCode'] ?>"
                                   readonly>

                        </div>
                        <div class="col-md-4">
                            <input type="number" onkeydown="remove_item_all_description_edit(event)"
                                   name="customerTelephoneTmp" id="customerTelephoneTmp"
                                   class="form-control" autocomplete="off">
                            <input type="hidden" class="form-control" id="customerIDTmp" name="customerID">

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="customerNameTmp">
                            Country</label>
                        <div class="col-md-6" id="country-select">

                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="customerNameTmp">
                            <?php //echo $this->lang->line('posr_customer_name'); ?>Name</label>
                        <div class="col-md-6">
                            <input type="text" name="customerNameTmp" id="customerNameTmp"
                                   class="form-control input-md" autocomplete="off" autocomplete="off">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="customerNameTmp">
                            Address</label>
                        <div class="col-md-6">
                            <input type="text" name="customerAddressTmp" id="customerAddressTmp"
                                   class="form-control input-md" autocomplete="off">

                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-4 control-label"> Email </label>
                        <div class="col-md-6">
                            <input type="text" name="customerEmailTmp" id="customerEmailTmp"
                                   class="form-control input-md" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label"> Loyality Card No </label>
                        <div class="col-md-4">
                            <input type="text" name="loyalitycardno" id="loyalitycardno"
                                   class="form-control input-md" readonly>
                        </div>
                        <div class="col-md-4 loyalitycardgen hide">
                            <button class="btn btn btn-primary" type="button" onclick="save_loyalty_card()"><i
                                        class="fa fa-plus"></i> Generate
                            </button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="margin-top: 0px;">
                    <button class="btn btn-lg btn-default" data-dismiss="modal"><i class="fa fa-times text-red"></i>
                        Cancel
                    </button>
                    <button class="btn btn-lg btn-default" onclick="clearCustomerVal()" type="reset"><i
                                class="fa fa-eraser text-purple"></i> Clear
                    </button>
                    <button class="btn btn-lg btn-primary" type="button" onclick="setCustomerInfo()"><i
                                class="fa fa-plus"></i> Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCustomerModal() {
        /*$("#customerNameTmp").val('');
        $("#customerTelephoneTmp").val('');
        $("#customerAddressTmp").val('');
        $("#customerIDTmp").val('');*/
        $("#pos_payments_customer_modal").modal('show');
        initializeitemTypeahead()
        loadCountryDetail_customer();
        //$("#customerCountry").select2();
    }

    function setCustomerInfo() {

        var customerEmail = $("#customerEmailTmp").val();
        if (customerEmail != null && customerEmail != '') {
            if (!validateEmail(customerEmail)) {
                myAlert('e', 'Please enter valid email');
                return false;
            }
        }
        var customerName = $("#customerNameTmp").val();
        var customerTel = $("#customerTelephoneTmp").val();
        var customerCountry = $("#customerCountry").val();
        var customerCountryCode = $("#customerCountryCode").val();
        var customerCountryId = $("#customerCountryId").val();
        var customerAddress = $("#customerAddressTmp").val();
        var customerID = $("#customerIDTmp").val();

        if ($("#customerTelephoneTmp").val() == '') {
            myAlert('e', 'Telephone Number is not Valid');
            return false;
        }

        if ($("#customerNameTmp").val() == '') {
            myAlert('e', 'Customer Name is Required');
            return false;
        }
        $("#customerName").val(customerName);
        $("#customerTelephone").val(customerTel);
        $("#customerAddress").val(customerAddress);
        $("#customerID").val(customerID);
        $("#customerEmail").val(customerEmail);
        $("#customerCountry_o").val(customerCountry);
        $("#customerCountryCode_o").val(customerCountryCode);
        $("#customerCountryId_o").val(customerCountryId);

        if (customerID > 0) {
            customer_loyalty_card_details(customerID, customerName, customerTel);
            $("#pos_payments_customer_modal").modal('hide');
        } else {
            save_customer_detail();

        }

    }

    function clearCustomerVal() {
        $("#customerName").val('');
        $("#customerTelephone").val('');
        $("#customerAddressTmp").val('');
        $("#customerIDTmp").val('');
        $("#customerEmail").val('');
        $("#customerEmailTmp").val('');
        $("#customer_loyalty_card_number").html('');
        $("#customer_loyalty_balance").html('');
        $("#customer_loyalty_amount").html('');
        $("#customer_loyalty_earned").html('');

        $("#customerID").val('');
        $("#redeem").val('');
        $('#loyalitycardno').html('');
        $('#availablepoints_loyality').html('');
        $('#earnedpoints').html('');
        $('#earnedpoints_val').val(' ');
        $('#loyalty_balance').val(' ');
        $("#gc_CustomerName").val('');
        $("#rdmcustomerID").val(0);
        $("#loyaltyCustomerTelephone").val('');
        $("#barcode").val('');
        $("#bill_amount").val('');
        $("#availablepoints").val('');
        $("#minimumPointstoRedeem").val('');
        $("#priceToPointsEarned").val('');
        $("#pointstoprice").html(0);
        $("#minimupoints").html(0);
        $("#gc_CustomerName").prop('disabled', true);
        $('.loylitycusdetail').addClass('hide');
        $("#customerCountry").val('<?php echo $companycountry['CountryDes']?>');
        $('#customerCountryCode').val('<?php echo $companycountry['countryCode']?>');
        $('#customerCountryId').val('<?php echo $companycountry['countryID']?>');
        $('#customer_loyalty_details_div').hide();
        $('#customerCountry').select2().trigger('change');
    }

    function loadCountryDetail_customer() {
        $("#country-select").empty();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_customerMaster/loadCountryDetail"); ?>',
            dataType: 'json',
            data: '',
            async: false,
            success: function (data) {
                $("#country-select").append(data);
                $("#customerCountry").val('<?php echo $companycountry['CountryDes']?>');
                $('#customerCountryCode').val('<?php echo $companycountry['countryCode']?>');
                $('#customerCountryId_o').val('<?php echo $companycountry['countryID']?>');
                $("#customerCountry").select2();
            }
        });
    }
</script>

<!-- Delivery -->
<div aria-hidden="true" role="dialog" id="own_delivery_setup_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header posModalHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-close text-red"></i></button>
                    <h4> Own Delivery </h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="revGLID" value="0"/>
                    <button class="btn btn-lg btn-default btn-block" onclick="clear_own_delivery_setup()" type="button">
                        <i
                                class="fa fa-eraser text-red"></i> Clear
                    </button>
                    <?php
                    $delivery_setup = get_specialCustomers(array('5'));
                    ?>
                    <?php if (!empty($delivery_setup)) {
                        foreach ($delivery_setup as $val) {
                            ?>
                            <button class="btn btn-lg btn-block ownDeliveryOptionsList" onclick="own_delivery_setup_change.call(this)"
                                    type="button" data-cus_name="<?php echo $val['customerName'] ?>"
                                    data-cp="<?php echo $val['commissionPercentage'] ?>"
                                    data-ca="<?php echo $val['commissionAmount'] ?>"
                                    data-cbased_on="<?php echo $val['ownDeliveryBasedOn'] ?>"
                                    data-revgl="<?php echo $val['revenueGLAutoID'] ?>">
                                    <?php
                                        if($val['ownDeliveryBasedOn']=='1'){
                                            echo $val['customerName'].' '.round($val['commissionAmount'],$d);
                                        }else if($val['ownDeliveryBasedOn']=='2'){
                                            echo $val['customerName'].' '.round($val['commissionPercentage'],$d).'%';
                                        }
                                    ?>
                            </button>
                            <?php
                        }
                    } ?>

                </div>
                <div class="modal-footer" style="margin-top: 0px;">
                    <button class="btn btn-lg btn-default" data-dismiss="modal"><i class="fa fa-times text-red"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PROMOTION -->
<div aria-hidden="true" role="dialog" id="pos_payments_promotion_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header posModalHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-close text-red"></i></button>
                    <h4> Promotion </h4>
                </div>
                <div class="modal-body">

                    <button class="btn btn-lg btn-default btn-block" onclick="clearPromotion()" type="button"><i
                                class="fa fa-eraser text-red"></i> Clear
                    </button>

                    <?php

                    $promotion = get_specialCustomers(array(2, 3));
                    if (!empty($promotion)) {
                        foreach ($promotion as $val) {
                            $val['customerID'];
                            ?>
                            <button
                                    class="btn btn-lg <?php echo $val['customerTypeMasterID'] == 3 ? 'btn-default' : 'btn-default'; ?> btn-block"
                                    onclick="checkPosAuthentication(1,<?php echo $val['customerID'] ?>)" type="button">
                                <!--addPromotion(--><?php /*echo $val['customerID'] */ ?><!--)-->
                                <?php echo $val['customerTypeMasterID'] == 3 ? '<i class="fa fa-bullhorn text-red"></i>' : '<i class="fa fa-bullhorn text-purple"></i>'; ?> <?php echo $val['customerName'] ?> <?php echo $val['commissionPercentage']; ?>
                                %
                            </button>
                            <?php
                        }
                    } ?>

                </div>
                <div class="modal-footer" style="margin-top: 0px;">
                    <button class="btn btn-lg btn-default" data-dismiss="modal"><i class="fa fa-times text-red"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="split_bill" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body modal-responsive-bill" id="split_bill_body">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#FFF; background-color:#005b8a; border:0px solid #007FFF; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                    Back
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="pos_sampleBill" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body modal-responsive-bill" id="pos_modalBody_sampleBill">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#FFF; background-color:#005b8a; border:0px solid #007FFF; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                    Back
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="loyalty_card_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 30%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <legend style="margin-bottom: 0px;">Redeem Loyalty</legend>
                </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="loyalty_card_form" method="post" class="form-group">
                    <input type="hidden" name="cardMasterID" id="cardMasterID">
                    <input type="hidden" name="loyalitypaymentTypeID" id="loyalitypaymentTypeID">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label>Barcode</label>
                                <input id="barcode" name="barcode" type="text" class="form-control" autocomplete="off">
                                <div id="myInputautocompleteBarcode-list" class="autocomplete-items"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">

                            <div class="col-sm-12">
                                <label style="display: block">Telephone</label>
                                <div class="autocomplete" style="width:100%;">
                                    <input id="loyaltyCustomerTelephone" name="customerTelephone" type="text"
                                           placeholder="" class="form-control" autocomplete="off">
                                    <div id="myInputautocomplete-list" class="autocomplete-items">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="hidden" name="customerID" id="customerID" value="0">
                                <label>Name</label>
                                <input id="gc_CustomerName" name="CustomerName" type="text" placeholder=""
                                       readonly class="form-control">
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <label>Loyalty Balance Points</label>
                                <input id="loyalty_balance" name="loyalty_balance" type="text" placeholder=""
                                       readonly class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label>Loyalty
                                    Balance(<?php echo $this->common_data['company_data']['company_default_currency'] ?>
                                    )</label>
                                <input id="loyalty_balance_amount" name="loyalty_balance_amount" type="text"
                                       placeholder=""
                                       readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="hidden" name="customerID" id="customerID" value="0">
                                <label>Bill
                                    Amount(<?php echo $this->common_data['company_data']['company_default_currency'] ?>
                                    )</label>
                                <input id="bill_amount" name="bill_amount" type="text" placeholder=""
                                       readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="hidden" name="customerID" id="customerID" value="0">
                                <label>Redeem
                                    Amount(<?php echo $this->common_data['company_data']['company_default_currency'] ?>
                                    )</label>
                                <input id="redeem" name="redeem" type="text" placeholder=""
                                       class="form-control numpad">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-12 control-label" style="color: red;font-size: 80%"> <i
                                        class="fa fa-check" style="color: black" aria-hidden="true"></i><label
                                        id="msgpoints">Minimum 0 pts should be available to perform redemption </label></label>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-9 control-label" style="color: red;font-size: 80%"> <i
                                        class="fa fa-check" style="color: black" aria-hidden="true"></i>&nbsp;Minimum
                                points allowed for redemption : <label id="minimupoints" style="color: red;">0</label>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-lg btn-primary" style="width: 111px;"
                        onclick="set_loyalty_redeem_details.call(this)" type="button">Add
                </button>
                <button data-dismiss="modal" class="btn btn-lg btn-default" style="width: 111px;"
                        type="button">
                    <!--Close--><?php echo $this->lang->line('common_Close'); ?></button>
            </div>
        </div>
    </div>
</div>


<script>

    var isOutletTaxEnabled = "<?php echo $isOutletTaxEnabled; ?>";

    function openPromotionModal() {
        $("#pos_payments_promotion_modal").modal('show');
    }

    function addPromotion(id) {
        $("#promotionID").val(id).change();
        setTimeout(function () {
            $("#deliveryPersonID").val('').change();
        }, 50);
        $("#pos_payments_promotion_modal").modal('hide');
        $("#tmp_promotion").val($("#promotionID option:selected").text().trim());
        setTimeout(function () {
            var netTotalTmp = $("#final_payableNet_amt").text();
            var netTotal = parseFloat(netTotalTmp);
            $("#gross_total_input").val(netTotal);
        }, 50);
    }


    function addPromotion_update(id) {
        $("#promotionIDUpdate").val(id).change();
        setTimeout(function () {
            $("#deliveryPersonID").val('').change();
        }, 50);
        //$("#pos_payments_promotion_modal").modal('hide');
        $("#tmp_promotionUpdate").val($("#promotionIDUpdate option:selected").text().trim());
        setTimeout(function () {
            var netTotalTmp = $("#final_payableNet_amtUpdate").text();
            var netTotal = parseFloat(netTotalTmp);
            $("#gross_total_input").val(netTotal);
        }, 50);
    }

    function clearPromotion() {
        $("#promotionID").val('').change();
        $("#deliveryPersonID").val('').change();
        $("#pos_payments_promotion_modal").modal('hide');
        $("#tmp_promotion").val('');
        setTimeout(function () {
            var netTotalTmp = $("#final_payableNet_amt").text();
            var netTotal = parseFloat(netTotalTmp);
            $("#gross_total_input").val(netTotal);
        }, 50);
    }

    function calculatePaidAmountUpdate(tmpThis) {

        if ($("#isDeliveryUpdate").val() == 1) {
            if ($("#deliveryPersonIDUpdate").val() == "") {
                //$(".paymentOther").val(0);
            } else {

                if ($("#deliveryPersonIDUpdate").val() > 0) {

                    if ($("#deliveryPersonIDUpdate option:selected").data('otp') == 1) { // on time payment

                        var cardTotal = 0;
                        $(".paymentOtherUpdate").each(function (e) {
                            var valueThis = $.trim($(this).val());
                            cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                        });
                        var deliveryAmount = $("#totalPayableAmountDelivery_idUpdate").val();

                        if (cardTotal > deliveryAmount) {
                            $(".paymentOtherUpdate").val(0);
                            myAlert('e', 'You can not enter card amount more than delivery amount!')
                            return false;

                        }

                    }
                }
            }
        }


        var total = 0
        $(".paymentInputupdate").each(function (e) {
            var valueThis = $.trim($(this).val());
            total += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;

        });
        $("#paidUpdate").val(total);
        var payable = $("#total_payable_amtUpdate").val();
        var returnAmount = total - payable;
        if (returnAmount > 0) {
            $("#returned_changeUpdate").val(returnAmount);
            $("#return_changeUpdate").html(returnAmount.toFixed(<?php echo $d ?>))
        }

        setTimeout(function () {
            var discount = parseFloat($("#promotional_discountUpdate").val());
            var subTotal = $("#final_payable_amtUpdate").text();
            var netTotal = subTotal - discount;
            var paidAmountTmp = parseFloat($("#paidUpdate").val());

            var advancePaymets = $("#delivery_advancePaymentAmountUpdate").val();

            //netTotal = netTotal - advancePaymets;
            //$("#final_payableNet_amtUpdate").html(netTotal.toFixed(<?php //echo $d ?>//));

            var returnChange;
            //update amount with taxes. 1
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                var outletTaxAmount = outlet_tax.calculated_tax_amount;
                $("#outlet_tax_in_invoiceUpdate").html(outletTaxAmount.toFixed(<?php echo $d ?>));
                netTotal = netValueWithOutletTax - advancePaymets;
                $("#final_payableNet_amtUpdate").html(netTotal.toFixed(<?php echo $d ?>));
                returnChange = paidAmountTmp - netTotal;
            } else {
                netTotal = netTotal - advancePaymets;
                $("#final_payableNet_amtUpdate").html(netTotal.toFixed(<?php echo $d ?>));
                netTotal = netTotal.toFixed(<?php echo $d ?>);
                returnChange = paidAmountTmp - netTotal;
            }

            if (returnChange > 0 || true) {
                $("#returned_changeUpdate").val(returnChange.toFixed(<?php echo $d ?>));
                $("#return_changeUpdate").html(returnChange.toFixed(<?php echo $d ?>))
            }


            /** Total card amount should not be more than the NET Total */

            if (typeof tmpThis !== "undefined") {
                var cardTotal = 0;
                $(".paymentOtherUpdate").each(function (e) {
                    var valueThis = $.trim($(this).val());
                    cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                });

                //netTotal = netTotal.toFixed(<?php echo $d ?>);
                //update amount with taxes. 2
                if (isOutletTaxEnabled == "true") {
                    var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                    var netValueWithOutletTax = outlet_tax.updated_net_value;
                    netTotal = netValueWithOutletTax;
                } else {
                    netTotal = netTotal.toFixed(<?php echo $d ?>);
                }


                if (cardTotal > netTotal) {

                    $(".paymentOtherUpdate").val(0);
                    calculateReturnUpdate();
                    $("#cardTotalAmountUpdate").val(0);
                    myAlert('e', 'You can not pay more than the net total using cards!');


                } else {
                    $("#cardTotalAmountUpdate").val(cardTotal);

                }

            }
            $("#netTotalAmountUpdate").val(netTotal);


        }, 50);
    }

    function calculatePaidAmount(tmpThis) {
        if ($("#isDelivery").val() == 1) {
            if ($("#deliveryPersonID").val() == "") {
                //$(".paymentOther").val(0);
            } else {

                if ($("#deliveryPersonID").val() > 0) {

                    if ($("#deliveryPersonID option:selected").data('otp') == 1) { // on time payment

                        var cardTotal = 0;
                        $(".paymentOther").each(function (e) {
                            var valueThis = $.trim($(this).val());
                            cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                        });
                        var deliveryAmount = $("#totalPayableAmountDelivery_id").val();

                        if (cardTotal > deliveryAmount) {
                            $(".paymentOther").val(0);
                            myAlert('e', 'You can not enter card amount more than delivery amount!')
                            return false;

                        }

                    }
                }
            }
        }


        var total = 0
        $(".paymentInput").each(function (e) {

            var valueThis = $.trim($(this).val());
            total += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;

        });
        $("#paid").val(total.toFixed(<?php echo $d ?>));
        var payable = $("#total_payable_amt").val();
        var returnAmount = total - payable;
        if (returnAmount > 0) {
            $("#returned_change").val(returnAmount);
            $("#return_change").html(returnAmount.toFixed(<?php echo $d ?>))
        }

        setTimeout(function () {
            var discount = parseFloat($("#promotional_discount").val());
            var subTotal = $("#total_payable_amt").val();
            var netTotal = subTotal - discount;
            var paidAmountTmp = parseFloat($("#paid").val());

            var advancePaymets = $("#delivery_advancePaymentAmount").val();


            //update amount with taxes. 3
            var returnChange;
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                var outletTaxAmount = outlet_tax.calculated_tax_amount;
                $("#outlet_tax_in_invoice").html(outletTaxAmount.toFixed(<?php echo $d ?>));
                netTotal = netValueWithOutletTax - advancePaymets;
                $("#final_payableNet_amt").html(netTotal.toFixed(<?php echo $d ?>));

                returnChange = paidAmountTmp - netTotal;
            } else {
                netTotal = netTotal - advancePaymets;
                $("#final_payableNet_amt").html(netTotal.toFixed(<?php echo $d ?>));
                netTotal = netTotal.toFixed(<?php echo $d ?>);
                returnChange = paidAmountTmp - netTotal;
            }

            var isOwnDelivery = $("#isOwnDelivery").val();
            if (isOwnDelivery == '1') {
                let ownDeliveryAmount = $("#own_delivery_amount").val();
                returnChange = parseFloat(returnChange) - parseFloat(ownDeliveryAmount);
                let owndellog = 'own del- ' + returnChange + '***' + ownDeliveryAmount;

            }

            if (returnChange > 0 || true) {
                $("#returned_change").val(returnChange.toFixed(<?php echo $d ?>));
                $("#return_change").html(returnChange.toFixed(<?php echo $d ?>));
            }


            /** Total card amount should not be more than the NET Total */
            if (typeof tmpThis !== "undefined") {
                var cardTotal = 0;
                $(".paymentOther").each(function (e) {
                    var valueThis = $.trim($(this).val());
                    cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                });

                //netTotal = netTotal.toFixed(<?php echo $d ?>);
                //update amount with taxes. 4
                if (isOutletTaxEnabled == "true") {
                    var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                    var netValueWithOutletTax = outlet_tax.updated_net_value;
                    netTotal = netValueWithOutletTax;
                }

                if (cardTotal > netTotal) {

                    $(".paymentOther ").val(0);
                    calculateReturn();
                    $("#cardTotalAmount").val(0);
                    myAlert('e', 'You can not pay more than the net total using cards!');


                } else {
                    $("#cardTotalAmount").val(cardTotal);
                }
                $("#netTotalAmount").val(netTotal);
            }
            calculate_own_delivery();

        }, 50);

    }

    function calculate_net_card_total() {
        setTimeout(function () {
            var discount = parseFloat($("#promotional_discount").val());
            var subTotal = $("#total_payable_amt").val();
            var netTotal = subTotal - discount;
            var cardTotal = 0;
            var isGiftCardModal = true;
            $(".paymentOther").each(function (e) {
                var valueThis = $.trim($(this).val());
                cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                if ($(this).attr('p-type') == 'gift_card') {
                    isGiftCardModal = false;
                }
            });

            //netTotal = netTotal.toFixed(<?php echo $d ?>);
            //update amount with taxes. 5
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                netTotal = netValueWithOutletTax;
            } else {
                netTotal = netTotal.toFixed(<?php echo $d ?>);
            }


            if (cardTotal > netTotal && isGiftCardModal) {
                $(".paymentOther ").val(0);
                calculateReturn();
                $("#cardTotalAmount").val(0);
                myAlert('e', 'You can not pay more than the net total using cards!');
            } else {
                $("#cardTotalAmount").val(cardTotal);
            }
            $("#netTotalAmount").val(netTotal);
        }, 60);
    }

    function calculate_net_card_totalUpdate() {
        setTimeout(function () {
            var discount = parseFloat($("#promotional_discountUpdate").val());
            var subTotal = $("#total_payable_amtUpdate").val();
            var netTotal = subTotal - discount;
            var cardTotal = 0;
            var isGiftCardModal = true;
            $(".paymentOtherUpdate").each(function (e) {
                var valueThis = $.trim($(this).val());
                cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                if ($(this).attr('p-type') == 'gift_card') {
                    isGiftCardModal = false;
                }
            });

            //netTotal = netTotal.toFixed(<?php echo $d ?>);
            //update amount with taxes. 6
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                netTotal = netValueWithOutletTax;
            } else {
                netTotal = netTotal.toFixed(<?php echo $d ?>);
            }

            if (cardTotal > netTotal && isGiftCardModal) {
                $(".paymentOtherUpdate").val(0);
                calculateReturnUpdate();
                $("#cardTotalAmountUpdate").val(0);
                myAlert('e', 'You can not pay more than the net total using cards!');
            } else {
                $("#cardTotalAmountUpdate").val(cardTotal);
            }
            $("#netTotalAmountUpdate").val(netTotal);
        }, 60);
    }

    function reset_paymentMode() {
        $("#customerType").val('');
        $(".customerType").removeClass('btn-primary');
        $(".customerType").removeClass('btn-default');
        $(".customerType").addClass('btn-default');
    }

    function updateExactCard(paymentTypeID) {
        $("#paid").val(0);
        $("#paid_temp").html(0);
        $(".paymentInput").val('');
        $('.cardRef').val('');
        var totalAmount = $("#final_payableNet_amt").text();
        $("#paymentType_" + paymentTypeID).val(parseFloat(totalAmount).toFixed(<?php echo $d?>));
        calculateReturn();
        clearCreditSales();

    }

    function updateExactCard_update(paymentTypeID) {
        $("#paidUpdate").val(0);
        $("#paid_tempUpdate").html(0);
        $(".paymentInputupdate").val('');
        $('.cardRef').val('');
        var totalAmount = $("#final_payableNet_amtUpdate").text();
        $("#paymentType_Update" + paymentTypeID).val(parseFloat(totalAmount).toFixed(<?php echo $d?>));
        calculateReturnUpdate();

    }


    function print_sample_bill() {
        <?php
        if (isset($isHidePrintPreview) && $isHidePrintPreview) {
            echo "app.submit_mode = 'submit_and_send_to_printer';";
        } else {
            echo "app.submit_mode = 'submit_and_print';";
        }
        ?>
        var invoiceID = $("#holdInvoiceID").val();
        var outletID = $("#holdOutletID_input").val();
        // var tmp_promotion = $("#tmp_promotion").val();
        var promotional_discount = $("#promotional_discount").val();
        var promotionID = $("#promotionID").val();
        var promotionIDdatacp = $("#promotionID").find(':selected').attr('data-cp');


        var formData = $(".form_pos_receipt").serializeArray();
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;

        date = hour + ":" + minute + ":" + seconds;
        formData.push({'name': 'currentTime', 'value': date});
        formData.push({'name': 'invoiceID', 'value': invoiceID});
        formData.push({'name': 'promotional_discount', 'value': promotional_discount});
        formData.push({'name': 'promotionID', 'value': promotionID});
        formData.push({'name': 'promotionIDdatacp', 'value': promotionIDdatacp});
        formData.push({'name': 'outletID', 'value': outletID});

        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplateSampleBill'); ?>",
                data: formData,
                cache: false,
                beforeSend: function () {

                    $("#pos_sampleBill").modal('show');
                    startLoadPos();
                    $("#pos_modalBody_sampleBill").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                    <!--Loading Print view-->
                    $(".isSamplePrintedFlag").val(1);
                },
                success: function (data) {
                    stopLoad();
                    $("#pos_modalBody_sampleBill").html(data);
                    if (app.submit_mode == 'submit_and_send_to_printer') {
                        print_paymentReceipt()
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }

                }
            });
        } else {
            myAlert('e', 'Please select an invoice to print!');
        }
    }

    function initializeitemTypeahead() {

        $('#customerTelephoneTmp').autocomplete({
            serviceUrl: '<?php echo site_url();?>Pos_restaurant/fetch_pos_customer_details/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#customerIDTmp').val(suggestion.posCustomerAutoID);
                    $('#customerNameTmp').val(suggestion.CustomerName);
                    $('#customerAddressTmp').val(suggestion.CustomerAddress1);
                    $('#customerTelephoneTmp').val(suggestion.customerTelephone);
                    $('#customerEmailTmp').val(suggestion.customerEmail);
                    if ((suggestion.loyalityno != 0)) {
                        $('#loyalitycardno').val(suggestion.loyalityno);
                        $('#loyalitycardno').prop('readonly', true);
                        $('.loyalitycardgen').addClass('hide');
                    } else {
                        load_barcode_loyalty();
                        $('#loyalitycardno').prop('readonly', false);
                    }


                    if (suggestion.customerCountry) {
                        $("#customerCountry").val(suggestion.customerCountry);
                        $('#customerCountry').select2().trigger('change');
                    }
                }, 150);

            }
        });
        $('#customerTelephoneTmp').off('focus.autocomplete');
    }

    function remove_item_all_description_edit(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $('#customerIDTmp').val('');
            $('#customerNameTmp').val('');
            $('#customerAddressTmp').val('');
            $('#customerEmailTmp').val('');
            $('#customerCountryId').val(<?php echo $companycountry['countryID']?>);
            $('#customerCountry').val('<?php echo $companycountry['CountryDes']?>');
            $('#customerCountry').select2().trigger('change');
            $('#customerCountryCode').val('<?php echo $companycountry['countryCode']?>');
            $('#customerCountryId_o').val('<?php echo $companycountry['countryID']?>');
            $('#loyalitycardno').val('');
            $('#loyalitycardno').prop('readonly', true);
            $('.loyalitycardgen').addClass('hide');
        }
    }

    $('#country-select').on('change', '#customerCountry', function (e) {
        $('#customerCountryCode').val('');
        $('#customerCountryId_o').val('');
        var code = $('option:selected', this).attr('code');
        var countryId = $('option:selected', this).attr('country-id');
        if (code && countryId) {
            $('#customerCountryCode').val('+' + code);
            $('#customerCountryId_o').val(countryId);
        }
    });

    function handleItemMisMatchException() {

        var idArray = [];
        $('#posInvoiceForm #log .itemList').each(function () {
            var tempid = this.id.match(/\d+/);  // get only integer
            idArray.push(tempid[0]);
        });

        if (idArray.length > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {id_array: idArray},
                url: "<?php echo site_url('Pos_restaurant/handleItemListCountForCurrentInvoice'); ?>",
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    if (data.is_handled) {
                        calculateFooter();
                    }
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }

                }
            });
        }

    }

    $('#pos_sampleBill').on('hidden.bs.modal', function (e) {
        //confirmation_to_hold_bill();
        holdReceipt();
    });

    function confirmation_to_hold_bill() {
        bootbox.confirm({
            message: "Do you want to hold this bill?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-lg btn-success touchSizeButton'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-lg btn-danger touchSizeButton'
                }
            },
            callback: function (result) {
                if (result) {
                    holdReceipt();
                }
            }
        });
    }


    function open_pos_submitted_payments_modal_update() {
        <?php
        if (isset($template) && $template == 'general') { ?>
        $("#customerType").val(terminalGlobalVariables.dineInId);
        <?php
        }
        ?>
        /*handling exception*/
        // handleItemMisMatchException();

        var gross_total = parseFloat($("#gross_total").html());
        var customerType = $("#customerType").val();
        if (customerType > 0) {
            $("#pos_submitted_payments_modal").modal('show');
            //$("#paid_by").select2("val", "");
            $("#paymentType_Update34").parent().parent().hide();
            setTimeout(function () {
                //calculateReturnUpdate();
                calculatePaidAmountUpdate();
                $("#paidUpdate").focus();
            }, 500);
        } else {
            //bootbox.alert('<div class="alert alert-info"><strong>Please select order mode.</strong></div>');
            //$("#order_mode_modal").modal("show");
        }

    }

    function close_update_pos_submitted() {
        $("#pos_submitted_payments_modal").modal('hide');
        clearPosInvoiceSession();
    }

    var currentRequest = null;
    $("#loyaltyCustomerTelephone").keyup(function () {
        var skey = $("#loyaltyCustomerTelephone").val();
        currentRequest = $.ajax({
            dataType: 'json',
            type: 'POST',
            data: {skey: skey},
            url: '<?php echo site_url('Pos_restaurant/get_loyalty_customers_by_phone'); ?>',
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                // Success

                var list = "";
                data.forEach(function (item, index) {
                    let customer = '<div onclick="set_customer_input.call(this)" data-cus_id="' + item.posCustomerAutoID + '" data-cus_name="' + item.CustomerName + '" data-cus_phone="' + item.customerTelephone + '">' + item.customerTelephone + ' - ' + item.CustomerName + '</div>';
                    list += customer;
                });
                $("#myInputautocomplete-list").html(list);
            },
            error: function (e) {
                // Error
            }
        });
    });

    $("#barcode").keyup(function () {
        var skey = $("#barcode").val();
        currentRequest = $.ajax({
            dataType: 'json',
            type: 'POST',
            data: {skey: skey},
            url: '<?php echo site_url('Pos_restaurant/get_loyalty_customers_by_barcode'); ?>',
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                // Success

                var list = "";
                data.forEach(function (item, index) {
                    let customer = '<div onclick="set_customer_input.call(this)" data-cus_id="' + item.posCustomerAutoID + '" data-cus_name="' + item.CustomerName + '" data-cus_phone="' + item.customerTelephone + '">' + item.barcode + ' - ' + item.CustomerName + '</div>';
                    list += customer;
                });
                $("#myInputautocompleteBarcode-list").html(list);
            },
            error: function (e) {
                // Error
            }
        });
    });


    function loadLoyaltyModal(paymenttypeID) {
        if (app.priceToPointsEarned !== null) {
            var bill_amount = $("#final_payableNet_amt").text();
            $("#bill_amount").val(bill_amount);
            $("#loyalitypaymentTypeID").val(paymenttypeID);
            $("#minimupoints").html(app.minimumPointstoRedeem);
            $("#msgpoints").html('Minimum ' + app.pointsToPriceRedeemed + ' pts should be available to perform redemption');
            $("#loyalty_card_modal").modal('show');
        } else {
            myAlert('e', 'Loyalty setup not loaded');
        }
    }

    function set_customer_input() {
        var cus_phone = $(this).data('cus_phone');
        var cus_name = $(this).data('cus_name');
        var cus_id = $(this).data('cus_id');
        $("#loyaltyCustomerTelephone").val(cus_phone);
        $("#gc_CustomerName").val(cus_name);
        $("#customerID").val(cus_id);
        $("#customerTelephone").val(cus_phone);
        $("#customerName").val(cus_name);
        $("#myInputautocomplete-list").html("");
        $("#myInputautocompleteBarcode-list").html("");
        customer_loyalty_card_details(cus_id, cus_name, cus_phone);

        get_loyalty_details(cus_id);
    }

    function get_loyalty_details(customerID) {
        $.ajax({
            async: false,
            type: 'POST',
            dataType: 'json',
            data: {customerID: customerID},
            url: "<?php echo site_url('Pos_restaurant/get_loyalty_details'); ?>",
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                if (data.status == 'success') {
                    $("#loyalty_balance").val(parseFloat(data.available_points).toFixed(<?php echo $d?>));
                    var loyalty_balance_amount = parseFloat(data.exchange_rate) * parseFloat(data.available_points);
                    $("#loyalty_balance_amount").val(loyalty_balance_amount.toFixed(<?php echo $d?>));
                    $("#barcode").val(data.barcode);

                } else {
                    myAlert('e', '<br>Message: ' + data.message);
                }

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }

            }
        });
    }

    function set_loyalty_redeem_details() {

        var redeem = ($("#redeem").val()) ? $("#redeem").val() : 0;
        var redeem_points = parseFloat(redeem) / parseFloat(app.amount);
        var loyalty_balance = $("#loyalty_balance").val();
        var loyalty_balance_amount = $("#loyalty_balance_amount").val();
        var pointsToPriceRedeemed = app.pointsToPriceRedeemed;
        var bill_amount = $("#final_payableNet_amt").text();
        var paymentTypeID = $("#loyalitypaymentTypeID").val();
        if ((redeem != 0)) {
            if (parseFloat(loyalty_balance) >= parseFloat(pointsToPriceRedeemed)) {
                if ((parseFloat(app.minimumPointstoRedeem) <= parseFloat(redeem_points))) {
                    if (parseFloat(redeem) <= parseFloat(loyalty_balance_amount)) {
                        if (parseFloat(redeem) <= parseFloat(bill_amount)) {
                            $(".loyaltyRedeemAmount").val(redeem);
                            var topupAmount = parseFloat(bill_amount) - parseFloat(redeem);
                            if (app.priceToPointsEarned <= bill_amount) {
                                var earned = topupAmount * (parseFloat(app.purchaseRewardPoint) / parseFloat(app.poinforPuchaseAmount));
                            } else {
                                var earned = 0;
                            }
                            $("#customer_loyalty_earned").html(earned.toFixed(<?php echo $d?>));
                            let earnedAmount = app.exRate * earned;
                            $("#customer_loyalty_earned_amount").html(earnedAmount.toFixed(<?php echo $d?>));
                            $("#customer_loyalty_card_number").html($("#barcode").val());
                            $("#customer_loyalty_balance").html($("#loyalty_balance").val());
                            $("#customer_loyalty_amount").html($("#loyalty_balance_amount").val());
                            $("#customer_loyalty_details_div").show();
                            $("#loyalty_card_modal").modal('hide');
                            $("#paymentType_" + paymentTypeID).val(parseFloat(redeem));
                            $(".loyaltyRedeemAmount").val(parseFloat(redeem));
                        } else {
                            myAlert('e', 'Redeem value is greater than bill value.');

                        }
                    } else {
                        myAlert('e', 'Loyalty point is not sufficient');

                    }
                } else {
                    myAlert('e', 'Minimum points allowed for redemption is ' + parseFloat(app.minimumPointstoRedeem).toFixed(<?php echo $d?>));

                }
            } else {
                myAlert('e', 'Minimum loyalty balance should be ' + pointsToPriceRedeemed);
            }
        } else {
            if (app.priceToPointsEarned <= bill_amount) {
                var earned = parseFloat(bill_amount) * (parseFloat(app.purchaseRewardPoint) / parseFloat(app.poinforPuchaseAmount));
            } else {
                var earned = 0;
            }
            $("#customer_loyalty_earned").html(earned.toFixed(<?php echo $d?>));
            let earnedAmount = app.exRate * earned;
            $("#customer_loyalty_earned_amount").html(earnedAmount.toFixed(<?php echo $d?>));
            $("#customer_loyalty_card_number").html($("#barcode").val());
            $("#customer_loyalty_balance").html($("#loyalty_balance").val());
            $("#customer_loyalty_amount").html($("#loyalty_balance_amount").val());
            $("#customer_loyalty_details_div").show();
            $("#loyalty_card_modal").modal('hide');
            $("#paymentType_" + paymentTypeID).val(parseFloat(redeem));
            $(".loyaltyRedeemAmount").val(parseFloat(redeem));

        }

        calculatePaidAmount();
    }

    $(".loyaltyRedeemAmount").on('click', function () {
        loadLoyaltyModal();
    });

    function loyalty_points_details() {
        var bill_amount = $("#final_payableNet_amt").text();
        var customerID = $("#customerIDTmp").val();
        $.ajax({
            async: false,
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/loyalty_points_details'); ?>",
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                if (data.status == 'success') {
                    app.priceToPointsEarned = data.loyalty_setup.priceToPointsEarned;
                    app.pointsToPriceRedeemed = data.loyalty_setup.pointsToPriceRedeemed;
                    app.minimumPointstoRedeem = data.loyalty_setup.minimumPointstoRedeem;
                    app.loyaltyPoints = data.loyalty_setup.loyaltyPoints;
                    app.poinforPuchaseAmount = data.loyalty_setup.poinforPuchaseAmount;
                    app.purchaseRewardPoint = data.loyalty_setup.purchaseRewardPoint;
                    app.amount = data.loyalty_setup.amount;
                    app.exRate = data.loyalty_setup.amount;
                    if (app.priceToPointsEarned <= bill_amount) {
                        var earned = parseFloat(bill_amount) * (parseFloat(app.purchaseRewardPoint) / parseFloat(app.poinforPuchaseAmount));
                    } else {
                        var earned = 0;
                    }
                    $("#customer_loyalty_earned").html(earned.toFixed(<?php echo $d?>));
                    let earnedAmount = app.exRate * earned;
                    $("#customer_loyalty_earned_amount").html(earnedAmount.toFixed(<?php echo $d?>));

                } else {
                    app.priceToPointsEarned = null;
                    app.pointsToPriceRedeemed = null;
                    app.minimumPointstoRedeem = null;
                    app.loyaltyPoints = null;
                    app.poinforPuchaseAmount = null;
                    app.purchaseRewardPoint = null;
                    app.amount = null;
                    app.exRate = null;
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    function customer_loyalty_card_details(customerID, name, phone) {
        var bill_amount = $("#final_payableNet_amt").text();
        if (app.priceToPointsEarned <= bill_amount) {
            var earned = bill_amount * (parseFloat(app.purchaseRewardPoint) / parseFloat(app.poinforPuchaseAmount));
        } else {
            var earned = 0;
        }

        $.ajax({
            async: false,
            type: 'POST',
            dataType: 'json',
            data: {customerID: customerID},
            url: "<?php echo site_url('Pos_restaurant/get_loyalty_details'); ?>",
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                if (data.status == 'success') {
                    //fill data to details box
                    $("#customer_loyalty_details_div").show();
                    $("#customer_loyalty_card_number").html(data.barcode);
                    $("#customer_loyalty_balance").html(parseFloat(data.available_points).toFixed(<?php echo $d?>));
                    var loyalty_balance_amount = parseFloat(data.exchange_rate) * parseFloat(data.available_points);
                    $("#customer_loyalty_amount").html(loyalty_balance_amount.toFixed(<?php echo $d?>));

                    //fill data to redeem modal
                    $("#loyalty_balance_amount").val(loyalty_balance_amount.toFixed(<?php echo $d?>));
                    $("#customer_loyalty_earned").html(earned.toFixed(<?php echo $d?>));
                    let earnedAmount = data.exchange_rate * earned;
                    $("#customer_loyalty_earned_amount").html(earnedAmount.toFixed(<?php echo $d?>));
                    $("#barcode").val(data.barcode);
                    $("#loyalty_balance").val(parseFloat(data.available_points).toFixed(<?php echo $d?>));
                    $("#loyaltyCustomerTelephone").val(phone);
                    $("#gc_CustomerName").val(name);
                } else {
                    $("#customer_loyalty_details_div").hide();
                    $("#customer_loyalty_card_number").html("");
                    $("#customer_loyalty_balance").html("");

                    $("#barcode").val("");
                    $("#loyalty_balance").val("");
                    $("#loyaltyCustomerTelephone").val("");
                    $("#gc_CustomerName").val("");
                    $("#customer_loyalty_earned").html("");
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    function save_customer_detail() {
        var data = $('#frm_pos_customer').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos_restaurant/save_customer_posres'); ?>",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    $("#customerIDTmp").val(data[2]);
                    $("#customerID").val(data[2]);
                    load_barcode_loyalty();
                    $('#loyalitycardno').prop('readonly', false);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }


    function load_barcode_loyalty() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {telephone: 0},
            url: "<?php echo site_url('Pos/load_barcode_loyalty'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#loyalitycardno').val(data)
                $('.loyalitycardgen').removeClass('hide');
            }, error: function () {
                stopLoad();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function save_loyalty_card() {
        var barcode = $("#loyalitycardno").val();
        var gc_customerTelephone = $("#customerTelephoneTmp").val();
        var gc_CustomerName = $("#customerNameTmp").val();
        var customerID = $("#customerIDTmp").val();
        var cardMasterID = ' ';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                barcode: barcode,
                customerTelephone: gc_customerTelephone,
                gc_CustomerName: gc_CustomerName,
                customerID: customerID,
                cardMasterID: cardMasterID
            },
            url: "<?php echo site_url('Pos_restaurant/save_loyalty_card'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $("#pos_payments_customer_modal").modal('hide');
                    $(".loyalitycardgen").addClass('hide');
                    customer_loyalty_card_details(customerID, gc_CustomerName, gc_customerTelephone);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function split_bill_dialog() {
        $("#split_into").val("");
        $("#split_bill_modal").modal('show');
    }

    function show_splitted_bill() {
        var split_into = $("#split_into").val();
        if (split_into == "") {
            myAlert('e', 'Split into field is required.');
            return;
        }
        if (split_into == 0) {
            myAlert('e', 'Split into field cannot be zero.');
            return;
        }
        var invoiceID = $("#holdInvoiceID").val();
        var outletID = $("#holdOutletID_input").val();

        // var tmp_promotion = $("#tmp_promotion").val();
        var promotional_discount = $("#promotional_discount").val();
        var promotionID = $("#promotionID").val();
        var promotionIDdatacp = $("#promotionID").find(':selected').attr('data-cp');


        var formData = $(".form_pos_receipt").serializeArray();
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;

        date = hour + ":" + minute + ":" + seconds;
        formData.push({'name': 'currentTime', 'value': date});
        formData.push({'name': 'invoiceID', 'value': invoiceID});
        formData.push({'name': 'promotional_discount', 'value': promotional_discount});
        formData.push({'name': 'promotionID', 'value': promotionID});
        formData.push({'name': 'promotionIDdatacp', 'value': promotionIDdatacp});
        formData.push({'name': 'outletID', 'value': outletID});
        formData.push({'name': 'split_into', 'value': split_into});

        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplateSplitBill'); ?>",
                data: formData,
                cache: false,
                beforeSend: function () {
                },
                success: function (data) {
                    stopLoad();
                    $("#split_bill_modal").modal('hide');
                    $("#split_bill").modal('show');
                    $("#split_bill_body").html(data);
                    //print_paymentReceipt();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }

                }
            });
        } else {
            myAlert('e', 'Please select an invoice to print!');
        }
    }

    function show_own_delivery_modal() {
        $("#own_delivery_setup_modal").modal('show');
    }

    function clear_own_delivery_setup() {
        $("#deliveryCommissionDiv").show();
        if ($("#owdAllowed").val() == 1) {
            $("#own_delivery_div").show();
        } else {
            $("#own_delivery_div").hide();
        }

        $("#isOwnDelivery").val(0);
        $("#own_delivery_percentage").val("");
        $("#own_delivery_amount").val("");
        $("#own_delivery_type").val("");
        $("#own_delivery_setup_modal").modal('hide');

        let gross_total = $("#total_payable_amt").val();
        var tax = $("#outlet_tax_in_invoice").text();
        if (tax == "") {
            tax = 0;
        }
        let netAmount = parseFloat(gross_total) + parseFloat(tax);
        $("#final_payableNet_amt").text(netAmount.toFixed(<?php echo $d?>));
        $("#netTotalAmount").val(netAmount.toFixed(<?php echo $d?>));

        $("#own_delivery_person").prop("disabled", true);
        $("#own_delivery_percentage").prop("disabled", true);
        $("#own_delivery_amount").prop("disabled", true);
    }

    function clear_own_delivery_after_submit() {
        $("#own_delivery_amount").val("");
        $("#isOwnDelivery").val(0);
        $("#own_delivery_percentage").val("");
        $("#own_delivery_person").val("");
        clear_own_delivery_setup();
    }

    function own_delivery_setup_change() {
        $("#isOwnDelivery").val(1);
        $("#deliveryCommissionDiv").hide();

        let commissionBasedOn = $(this).data('cbased_on');

        if(commissionBasedOn=='2'){//based on percentage.
            let commission_percentage = parseFloat($(this).data('cp'));
            let revGLID = $(this).data('revgl');
            app.commission_percentage = commission_percentage;
            app.revGLID = revGLID;
            let cus_name = $(this).data('cus_name');
            let type = cus_name + '-' + commission_percentage + '%';
            $("#own_delivery_type").val(type);
            $("#revGLID").val(revGLID);
            $("#own_delivery_percentage").val(commission_percentage.toFixed(<?php echo $d?>));
            //let netAmount = parseFloat($("#final_payableNet_amt").text());
            let netAmount = parseFloat($("#total_netAmount").text());

            let commission_amount = netAmount * (commission_percentage / 100);
            $("#own_delivery_amount").val(commission_amount.toFixed(<?php echo $d?>));
            let updated_netAmount = netAmount + commission_amount;
            $("#final_payableNet_amt").text(updated_netAmount.toFixed(<?php echo $d?>));
            $("#netTotalAmount").val(updated_netAmount);
        }else if(commissionBasedOn=='1'){//based on amount.
            let commission_amount = parseFloat($(this).data('ca'));
            let revGLID = $(this).data('revgl');

            app.revGLID = revGLID;
            let cus_name = $(this).data('cus_name');
            let type = cus_name + '-' + commission_amount + '%';
            $("#own_delivery_type").val(type);
            $("#revGLID").val(revGLID);

            let netAmount = parseFloat($("#total_netAmount").text());
            $("#own_delivery_amount").val(commission_amount.toFixed(<?php echo $d?>));
            let updated_netAmount = netAmount + commission_amount;
            $("#final_payableNet_amt").text(updated_netAmount.toFixed(<?php echo $d?>));
            $("#netTotalAmount").val(updated_netAmount);

            let commission_percentage = (commission_amount/netAmount)*100;
            app.commission_percentage = commission_percentage;
            $("#own_delivery_percentage").val(commission_percentage.toFixed(<?php echo $d?>));
        }

        $("#own_delivery_person").prop("disabled", false);
        $("#own_delivery_percentage").prop("disabled", false);
        $("#own_delivery_amount").prop("disabled", false);

        $("#own_delivery_setup_modal").modal('hide');
        calculateReturn();
    }

    function own_delivery_percentage_change() {
        $("#isOwnDelivery").val(1);
        if ($("#own_delivery_percentage").val() != "") {
            let commission_percentage = parseFloat($("#own_delivery_percentage").val());
            app.commission_percentage = commission_percentage;
            let netAmount = parseFloat($("#total_netAmount").text());
            let commission_amount = netAmount * (commission_percentage / 100);
            $("#own_delivery_amount").val(commission_amount.toFixed(<?php echo $d?>));
            let updated_netAmount = netAmount + commission_amount;
            $("#final_payableNet_amt").text(updated_netAmount.toFixed(<?php echo $d?>));
            $("#netTotalAmount").val(updated_netAmount);
            calculateReturn();
        } else {
            clear_own_delivery_setup();
        }
    }

    function own_delivery_amount_change() {
        $("#isOwnDelivery").val(1);
        if ($("#own_delivery_amount").val() != "") {
            let netAmount = parseFloat($("#total_netAmount").text());
            let commission_amount = parseFloat($("#own_delivery_amount").val());
            let updated_netAmount = netAmount + commission_amount;
            $("#final_payableNet_amt").text(updated_netAmount.toFixed(<?php echo $d?>));
            $("#netTotalAmount").val(updated_netAmount);
            let delivery_percentage = (100 / netAmount) * commission_amount;
            app.commission_percentage = delivery_percentage;
            $("#own_delivery_percentage").val(delivery_percentage.toFixed(<?php echo $d?>));
            calculateReturn();
        } else {
            clear_own_delivery_setup();
        }
    }

    function calculate_own_delivery() {
        let isOwnDelivery = $("#isOwnDelivery").val();
        if (isOwnDelivery == '1') {

            let commission_percentage = parseFloat(app.commission_percentage);
            let revGLID = app.revGLID;
            $("#revGLID").val(revGLID);
            $("#own_delivery_percentage").val(commission_percentage.toFixed(<?php echo $d?>));
            let netAmount = parseFloat($("#total_netAmount").text());
            let commission_amount = netAmount * (commission_percentage / 100);
            $("#own_delivery_amount").val(commission_amount.toFixed(<?php echo $d?>));
            let updated_netAmount = netAmount + commission_amount;
            $("#final_payableNet_amt").text(updated_netAmount.toFixed(<?php echo $d?>));
            $("#netTotalAmount").val(updated_netAmount.toFixed(<?php echo $d?>));
        }
    }

    $('#final_payableNet_amt').bind('DOMSubtreeModified', function () {
        let log = 'Net Total changed : ' + $('#final_payableNet_amt').text();

    });
</script>
