<?php
$companycountry = fetch_company_country();
?>
<style>
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

<!-- Payment Modal -->
<div aria-hidden="true" role="dialog" id="pos_payments_modal" class="modal" data-keyboard="true" data-backdrop="static"
     style="z-index: 5000;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader" style="background-color: #0581B8;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h3 class="modal-title" style="color:white;">
                    <?php echo $this->lang->line('common_payment'); ?><!--Payment--></h3>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form method="post" id="frm_pos_invoice_submit" class="form_pos_receipt">
                    <input type="hidden" name="isCreditSale" id="isCreditSale" value="0">
                    <input type="hidden" name="customerID" id="customerID" value="0">
                    <input type="hidden" name="CreditSalesAmnt" id="CreditSalesAmnt" value="0">
                    <input type="hidden" name="customerTelephone" id="customerTelephone">
                    <input type="hidden" name="customerName" id="customerName">
                    <input type="hidden" name="cardTotalAmount" id="cardTotalAmount" value="0"/>
                    <input type="hidden" name="netTotalAmount" id="netTotalAmount" value="0"/>
                    <input type="hidden" name="isDelivery" id="isDelivery" value="0"/>
                    <input type="hidden" name="isOnTimePayment" id="frm_isOnTimePayment" value=""/>
                    <input type="hidden" name="total_payable_amt" id="total_payable_amt" value="0">
                    <input type="hidden" name="delivery_advancePaymentAmount"
                           id="delivery_advancePaymentAmount" value="0">
                    <input type="hidden" name="memberidhn" id="memberidhn">
                    <input type="hidden" name="membernamehn" id="membernamehn">
                    <input type="hidden" name="contactnumberhn" id="contactnumberhn">
                    <input type="hidden" name="mailaddresshn" id="mailaddresshn">
                    <input type="hidden" name="customerAddress" id="customerAddress">
                    <input type="hidden" name="customerEmail" id="customerEmail">

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

                            <!--promotion Row-->


                            <!-- Net Total -->
                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-fs"> Total</div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="Grosstotal" class="ar text-red payment-fs" style="padding: 5px 0px">0.00
                                    </div>
                                </div>
                            </div>


                            <div class="row formRowPad" style="padding: 1px;display:none;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-fs"> Discount</div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="discounttxt" class="ar text-red  payment-fs" style="padding: 5px 0px;">
                                        0.00
                                    </div>
                                </div>
                            </div>

                            <!--promotion Row-->
                            <div class="row formRowPad" id="deliveryPersonContainer" style="display: none;"> <!--promotionRow-->
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
                                        $deliveryPersonArray = get_specialCustomers(array(2),2);

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
                                               value="<?php echo number_format(0, 2);//this should change to decimal places config in company. ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6  payment-fs">
                                    <?php echo $this->lang->line('posr_net_total'); ?><!--Net Total --> </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payableNet_amt" class="ar text-red  payment-fs"
                                         style="padding: 5px 0px;">0.00
                                    </div>
                                </div>
                            </div>



                            <table class="<?php echo table_class_pos() ?>" id="posg_payment_modal_table">
                                <?php
                                $payments = get_paymentMethods_GLConfig();
                                foreach ($payments as $payment) {
                                    if ($payment['autoID'] == 25 || $payment['autoID'] == 5) { // java app & Gift card skipped
                                        continue;
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">

                                                    <div style="padding:  4px;"
                                                         class="tbl-payment-font">
                                                        <input type="hidden"
                                                               name="customerAutoID_master[<?php echo $payment['autoID'] ?>]"
                                                               id="customerAutoID_master<?php echo $payment['autoID'] ?>">
                                                        <input type="hidden"
                                                               name="customerAutoID[<?php echo $payment['ID'] ?>]"
                                                               id="customerAutoID<?php echo $payment['ID'] ?>">
                                                        <?php echo $payment['description']; ?>

                                                    </div>
                                                </div>
                                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                    <?php
                                                    /** CREDIT SALES */
                                                    if ($payment['autoID'] == 7) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar CreditSalesRefNo touchEngKeyboard"
                                                               id="reference_<?php echo $payment['autoID']; ?>"
                                                               name="reference[<?php echo $payment['autoID'] ?>]"
                                                               placeholder="Reference"/>
                                                        <?php

                                                    } else {
                                                        if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                            ?>
                                                            <input type="text" value=""
                                                                   class="form-control cardRef ar"
                                                                   id="reference_<?php echo $payment['autoID']; ?>"
                                                                   name="reference[<?php echo $payment['autoID'] ?>]"
                                                                   placeholder="Ref#"/>
                                                            <?php
                                                        }
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $onclick = "";
                                            if ($payment['autoID'] == 2) {
                                                /** Credit Sales */
                                                //$onclick = ' onclick="openCreditSalesModal(' . $payment['ID'] . ')" ';
                                                $onclick = 'onclick="openCreditSalesModal()"';
                                            } else if ($payment['autoID'] == 1 || $payment['glAccountType'] == 1) {
                                                /** Cash */
                                                if ($payment['autoID'] == 1) {
                                                    $onclick = ' onclick="updateExactCard(' . $payment['ID'] . ')" ';
                                                } elseif ($payment['autoID'] == 26) {
                                                    $onclick = ' onclick="openRCGCmodel(' . $payment['autoID'] . ')" id="rcgcbtn"';
                                                }

                                            } else if ($payment['autoID'] == 7) {
                                                /** Credit Sales */
                                                $onclick = ' onclick="checkPosAuthentication(11,' . $payment['ID'] . ')"';
                                            }else if($payment['autoID'] == 42){//Loyalty
                                                $onclick = ' onclick="redeem_loyalty(' . $payment['ID'] . ')"';
                                            } else if ($payment['autoID'] == 4 || $payment['autoID'] == 6 || $payment['autoID'] == 1) {
                                                /**    3 Master Card | 4 Visa Card | 6 AMEX */
                                                $onclick = ' onclick="updateExactCard(' . $payment['ID'] . ')" ';
                                            } else {
                                                $onclick = ' onclick="updateExactCard(' . $payment['ID'] . ')" ';
                                            }
                                            ?>
                                            <button class="btn btn-default btn-block" <?php echo $onclick ?>
                                                    type="button"
                                                    style="padding: 0px;">
                                                <img src="<?php echo base_url($payment['image']); ?>"
                                                     style="max-height: 27px;">
                                            </button>

                                        </td>
                                        <td class="payment-tblSize">
                                            <?php
                                            if ($payment['autoID'] == 7) {
                                                /** CREDIT SALE  */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['autoID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       class="form-control al payment-inputTextMedium creditSalesPayment paymentInput allownumericwithdecimal <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php
                                            } else {
                                                if ($payment['glAccountType'] == 1) {
                                                    $tmpID = $payment['autoID'];
                                                } else {
                                                    $tmpID = $payment['ID'];
                                                }
                                                $tmpID = $payment['ID'];
                                                if ($payment["isAuthRequired"]) {
                                                    ?>
                                                    <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                           name="paymentTypes[<?php echo $payment['autoID'] ?>]"
                                                           class="form-control al payment-inputTextMedium paymentInput  allownumericwithdecimal <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="0.00">
                                                <?php } else {
                                                    if ($payment['autoID'] == 0) {

                                                        $class = '';
                                                        $readonly = '';
                                                    } else if ($payment['autoID'] == 1 || $payment['glAccountType'] == 1) {
                                                        /** Cash */
                                                        $class = '';
                                                        if ($payment['autoID'] == 26) {
                                                            $readonly = 'readonly';
                                                        } else {
                                                            $readonly = '';
                                                        }

                                                    } else if ($payment['autoID'] == 7 || $payment['autoID'] == 3 || $payment['autoID'] == 4 || $payment['autoID'] == 6 || $payment['autoID'] == 1) {
                                                        /** 3 Master Card | 4 Visa Card | 6 AMEX */
                                                        $class = '';
                                                        $readonly = '';
                                                    } else if ($payment['autoID'] == 25) {
                                                        /** java App */
                                                        $class = '';
                                                        $readonly = '';
                                                    } else if ($payment['autoID'] == 2) {
                                                        $class = 'CreditNoteAmnt';
                                                        $readonly = 'readonly';
                                                    } else if ($payment['autoID'] == 38 || $payment['autoID'] == 39) {
                                                        $class = '';
                                                        $readonly = '';
                                                    } else {
                                                        $class = '';
                                                        $readonly = 'readonly';
                                                    }
                                                    //echo $payment['autoID'];
                                                    ?>

                                                    <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                           name="paymentTypes[<?php echo $payment['autoID'] ?>]"
                                                           onchange="calculatePaidAmount(this)"
                                                           class="form-control <?php echo $class ?> al payment-inputTextMedium paymentInput allownumericwithdecimal <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="0.00" <?php echo $readonly ?>>

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
                                <div class="col-md-4 lbl-delivery">
                                    <!--Paid By-->
                                </div>


                                <div class="col-md-8">
                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        foreach ($paymentType as $key => $payType) {

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
                                        onclick="openCustomerModal_general()"><i
                                        class="fa fa-users"></i> Customer
                                </button>
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
                                                <option value=""></option>
                                                <option value="-1" data-cp="0" data-otp="1" selected>Normal Delivery -
                                                    0%
                                                </option>

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




                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 loylitycusdetail hide">
                            <div
                                style="padding: 10px 24px 10px 10px; border: 1px dashed grey;  border-radius: 14px;"
                                class="hidden-xs">


                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Loyality card No :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="loyalitycardno"> - </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Available Points :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="availablepoints_loyality">-</label>
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
                                      <input type="hidden" id="earnedpoints_val" value="0">
                                      <input type="hidden" id="loyaltyPoints" value="0">
                                            <label id="earnedpoints">-</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Earned Amount :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="earnedpoints_amount">-</label>
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
                                                <option value=""></option>
                                                <option value="-1" data-cp="0" data-otp="1" selected>Normal Delivery -
                                                    0%
                                                </option>

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
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>


                <button id="submit_btn" type="submit" onclick="submit_pos_payments()" class="btn btn-lg btn-primary"
                        style="background-color: #1b9af7; color: #FFF; border: 0px; float: right; display: none;">
                    <span
                            id="submit_btn_pos_receipt"><?php echo $this->lang->line('common_submit'); ?><!--Submit--> &nbsp; [F1]</span>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Payment Modal -->
<div aria-hidden="true" tabindex="1" role="dialog" id="pos_return_payments_modal" class="modal" data-keyboard="true" data-backdrop="static"
     style="z-index: 5000;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title" style="color:white;">
                    <?php echo $this->lang->line('posr_refund_payment'); ?><!--Payment--></h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form method="post" id="refund_frm_pos_invoice_submit" class="refund_frm_pos_invoice_submit">
                    
                    <input type="hidden" name="RefundGrossTotal_in" id="RefundGrossTotal_in" value="0"/>
                    <input type="hidden" name="RefundNetTotalAmount_in" id="RefundNetTotalAmount_in" value="0"/>
                    <input type="hidden" name="RefundPaymentType" id="RefundPaymentType" value="">

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

                            <!--promotion Row-->


                            <!-- Net Total -->
                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-fs"> Total</div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="RefundGrosstotal" class="ar text-red payment-fs" style="padding: 5px 0px">0.00
                                    </div>
                                </div>
                            </div>


                            <!--Net Total Row-->

                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6  payment-fs">
                                    <?php echo $this->lang->line('posr_net_total'); ?><!--Net Total --> </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="RefundNetTotalAmount" class="ar text-red  payment-fs"
                                         style="padding: 5px 0px;">0.00
                                    </div>
                                </div>
                            </div>



                            <table class="<?php echo table_class_pos() ?>" id="posg_payment_modal_table">
                                <?php
                                $payments = get_paymentMethods_GLConfig();
                                foreach ($payments as $payment) {
                                    if ($payment['autoID'] == 25 || $payment['autoID'] == 5) { // java app & Gift card skipped
                                        continue;
                                    }
                                    ?>

                                    <?php if(in_array($payment['autoID'],[1,3,4,6])) { ?>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">

                                                    <div style="padding:  4px;"
                                                         class="tbl-payment-font">
                                                        <input type="hidden"
                                                               name="customerAutoID_master[<?php echo $payment['autoID'] ?>]"
                                                               id="customerAutoID_master<?php echo $payment['autoID'] ?>">
                                                        <input type="hidden"
                                                               name="customerAutoID[<?php echo $payment['ID'] ?>]"
                                                               id="customerAutoID<?php echo $payment['ID'] ?>">
                                                        <?php echo $payment['description']; ?>

                                                    </div>
                                                </div>
                                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                    <?php
                                                    /** CREDIT SALES */
                                                    if ($payment['autoID'] == 7) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control hide cardRef ar CreditSalesRefNo touchEngKeyboard"
                                                               id="reference_<?php echo $payment['autoID']; ?>"
                                                               name="reference[<?php echo $payment['autoID'] ?>]"
                                                               placeholder="Reference"/>
                                                        <?php

                                                    } else {
                                                        if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                            ?>
                                                            <input type="text" value=""
                                                                   class="form-control cardRef ar"
                                                                   id="reference_<?php echo $payment['autoID']; ?>"
                                                                   name="reference[<?php echo $payment['autoID'] ?>]"
                                                                   placeholder="Ref#"/>
                                                            <?php
                                                        }
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $onclick = "";
                                            if ($payment['autoID'] == 1 || $payment['glAccountType'] == 3) {
                                                /** Cash */
                                                if ($payment['autoID'] == 1) {
                                                    $onclick = ' onclick="updateRefundExactCard(' . $payment['ID'] . ')" ';
                                                } 

                                            }  else if ($payment['autoID'] == 4 || $payment['autoID'] == 6 ) {
                                                /**    3 Master Card | 4 Visa Card | 6 AMEX */
                                                $onclick = ' onclick="updateRefundExactCard(' . $payment['ID'] . ')" ';
                                            } else {
                                                $onclick = ' onclick="updateRefundExactCard(' . $payment['ID'] . ')" ';
                                            }
                                            ?>
                                            <?php if($onclick != '') { ?>
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
                                            if ($payment['autoID'] == 7) {
                                                /** CREDIT SALE  */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['autoID'] ?>]"
                                                       onchange="calculateRefundPaidAmount(this)"
                                                       class="form-control al payment-inputTextMedium creditSalesPayment paymentInput allownumericwithdecimal <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php
                                            } else {
                                                if ($payment['glAccountType'] == 1) {
                                                    $tmpID = $payment['autoID'];
                                                } else {
                                                    $tmpID = $payment['ID'];
                                                }
                                                $tmpID = $payment['ID'];
                                                if ($payment["isAuthRequired"]) {
                                                    ?>
                                                    <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                           name="paymentTypes[<?php echo $payment['autoID'] ?>]"
                                                           class="form-control al payment-inputTextMedium paymentInput  allownumericwithdecimal <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="0.00">
                                                <?php } else {
                                                    if ($payment['autoID'] == 0) {

                                                        $class = '';
                                                        $readonly = '';
                                                    } else if ($payment['autoID'] == 1 || $payment['glAccountType'] == 1) {
                                                        /** Cash */
                                                        $class = '';
                                                        if ($payment['autoID'] == 26) {
                                                            $readonly = 'readonly';
                                                        } else {
                                                            $readonly = '';
                                                        }

                                                    } else if ($payment['autoID'] == 7 || $payment['autoID'] == 3 || $payment['autoID'] == 4 || $payment['autoID'] == 6 || $payment['autoID'] == 1) {
                                                        /** 3 Master Card | 4 Visa Card | 6 AMEX */
                                                        $class = '';
                                                        $readonly = '';
                                                    } else if ($payment['autoID'] == 25) {
                                                        /** java App */
                                                        $class = '';
                                                        $readonly = '';
                                                    } else if ($payment['autoID'] == 2) {
                                                        $class = 'CreditNoteAmnt';
                                                        $readonly = 'readonly';
                                                    } else if ($payment['autoID'] == 38 || $payment['autoID'] == 39) {
                                                        $class = '';
                                                        $readonly = '';
                                                    } else {
                                                        $class = '';
                                                        $readonly = 'readonly';
                                                    }
                                                    //echo $payment['autoID'];
                                                    ?>

                                                    <input type="text" id="refund_paymentType_<?php echo $tmpID; ?>"
                                                           name="refund_paymentTypes[<?php echo $payment['autoID'] ?>]"
                                                           onchange="calculateRefundPaidAmount(this)"
                                                           class="form-control <?php echo $class ?> al payment-inputTextMedium paymentInput allownumericwithdecimal <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="0.00" <?php echo $readonly ?>>

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <?php } ?>


                                    <?php
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div style="padding:  8px; "
                                             class="tbl-payment-font">
                                            <?php echo $this->lang->line('posr_refund_paid_amount'); ?><!--Paid Amount--></div>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td class="payment-tblSize">
                                        <input readonly type="number"
                                               name="refund_paid"
                                               id="refund_paid"
                                               class="form-control payment-inputTextLg paymentTypeTextRed al"
                                               placeholder="0.00"
                                               autocomplete="off">
                                        <span id="refund_paid_temp" class="hide"></span></td>
                                </tr>
                            </table>


                            <div class="row formRowPad hide">
                                <div class="col-md-4 lbl-delivery">
                                    <!--Paid By-->
                                </div>


                                <div class="col-md-8">
                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        foreach ($paymentType as $key => $payType) {

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
                                <div class="row hide">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="btn-toolbar" role="toolbar">

                                            <input type="hidden" id="tmpQtyValue" value="0">
                                            <div class="row">
                                                <?php
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
                            <div class="container-fluid hide" style="padding: 10px;">
                                <button class="btn btn-lg btn-default btn-strong btn-xl" type="button"
                                        onclick="openCustomerModal_general()"><i
                                        class="fa fa-users"></i> Customer
                                </button>
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
                                                <option value=""></option>
                                                <option value="-1" data-cp="0" data-otp="1" selected>Normal Delivery -
                                                    0%
                                                </option>

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




                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 loylitycusdetail hide">
                            <div
                                style="padding: 10px 24px 10px 10px; border: 1px dashed grey;  border-radius: 14px;"
                                class="hidden-xs">


                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Loyality card No :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="loyalitycardno"> - </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Available Points :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="availablepoints_loyality">-</label>
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
                                      <input type="hidden" id="earnedpoints_val" value="0">
                                      <input type="hidden" id="loyaltyPoints" value="0">
                                            <label id="earnedpoints">-</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label>Earned Amount :</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label id="earnedpoints_amount">-</label>
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
                                                <option value=""></option>
                                                <option value="-1" data-cp="0" data-otp="1" selected>Normal Delivery -
                                                    0%
                                                </option>

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
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal" onclick="close_payment_refund()">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>


                <button id="submit_btn" type="submit" onclick="submit_pos_refund_payments()" class="btn btn-lg btn-primary"
                        style="border: 0px; float: right;">
                    <span
                            id=""><?php echo $this->lang->line('common_submit'); ?><!--Submit--> &nbsp; [F1]</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="pos_payments_customer_modal" class="modal" data-keyboard="true"
     data-backdrop="static" style="z-index: 5000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" id="frm_pos_customer">
                <div class="modal-header posModalHeader" style="background-color: #0581B8;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-close text-red"></i></button>
                    <h4 style="color: white"><i class="fa fa-users"></i> Customer </h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="customerTelephoneTmp">
                            <?php //echo $this->lang->line('posr_customer_telephone'); ?>Telephone</label>
                        <div class="col-md-2">
                            <input type="text" name="customerCountryCode" id="customerCountryCode"
                                   class="form-control text-right" value="<?php echo $companycountry['countryCode']?>" readonly>

                        </div>
                        <div class="col-md-4">
                            <input type="number" onkeydown="remove_item_all_description_edit_gpos(event)"
                                   name="customerTelephoneTmp" id="customerTelephoneTmp"
                                   class="form-control">
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
                                   class="form-control input-md" autocomplete="off">

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
                            <input type="text" name="loyalitycardno_gpos" id="loyalitycardno_gpos"
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
                    <button class="btn btn-lg btn-default" onclick="clearCustomerVal_gpos()" type="reset"><i
                            class="fa fa-eraser text-purple"></i> Clear
                    </button>
                    <button class="btn btn-lg btn-primary" type="button" onclick="setCustomerInfo_gpos()"><i
                            class="fa fa-plus"></i> Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PROMOTION -->
<div aria-hidden="true" role="dialog" id="pos_payments_promotion_modal" class="modal" data-keyboard="true"
     data-backdrop="static" style="    z-index: 5001;">
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

                    $promotion = get_specialCustomers(array('2'),2);
                    if (!empty($promotion)) {
                        foreach ($promotion as $val) {
                            $val['customerID'];
                            ?>
                            <button
                                    class="btn btn-lg <?php echo $val['customerTypeMasterID'] == 3 ? 'btn-default' : 'btn-default'; ?> btn-block"
                                    onclick="checkPosAuthentication(21,<?php echo $val['customerID'] ?>)" type="button">
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