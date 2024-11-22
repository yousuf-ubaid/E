<?php
/**
 * Modified on 2023-01-20
 *      Comment  : item batch implimentation to code / code reviewed
 *
 **/

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/numPadmaster/jquery.numpad.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos-general.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/buttons/button.css'); ?>">
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$bank_card = posPaymentConfig_data('Y'); //load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];
$this->load->view('include/header', $title);
$this->load->view('include/top-gpos');

$currncy_arr = all_currency_new_drop();
$country = load_country_drop();
$customerCategory = party_category(1);
$gl_code_arr = supplier_gl_drop();
$country_arr = array('' => 'Select Country');
$taxGroup_arr = customer_tax_groupMaster();
$dPlace = get_company_currency_decimal();
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['countryID'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$isGroupBasedTaxPolicy =  getPolicyValues('GBT', 'All');
$hideExchange =  getPolicyValues('PEXHI', 'All');

$isExchangeThroughInvoice = getPolicyValues('EXINV', 'All');
?>


<div class="row"></div>

<div id="posHeader_1" style="position: fixed; width: 100%; z-index: 10">
    <div class="row" id="displayDet" style="background-color: #363636; margin-top: 53px">
        <div class="col-12" style="margin-top: 7px;color: #eff7ff !important; padding-left: 1%">

            <div class="col-md-2 col-sm-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2"><img src="<?php echo base_url('images/ico-3.png'); ?>" style="width: 22px;">
                         Cashier :
                    </label>
                    <span class=""><?php echo ucwords($this->session->EmpShortCode ?? ''); ?></span>
                </div>
            </div>

            <div class="col-md-3 col-sm-3">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2"><img src="<?php echo base_url('images/ico-2.png'); ?>" style="width: 22px;">
                         Customer : </label>
                    <span class="customerSpan">Cash</span>
                </div>
            </div>

            <div class="col-md-2 col-sm-2">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2 "><img src="<?php echo base_url('images/ico-4.png'); ?>" style="width: 22px;">
                         No of Items
                        : </label>
                    <span class="itemCount">0</span>
                </div>
            </div>

            <div class="col-md-3 col-sm-3 pull-right" id="refNo_masterDiv">
                <div class="form-group pull-right" id="refNo_Div" style="margin-bottom: 2px; padding-right: 10%">
                    <label for="" class="cols-sm-3"><img src="<?php echo base_url('images/ico-5.png'); ?>" style="width: 22px;">
                         Invoice No :
                    </label>
                    <span class="" id="doSysCode_refNo" style=""><?php echo $refNo; ?></span>
                </div>
            </div>

            <div class="col-md-2 col-sm-2 pull-right" id="currency_masterDiv">
                <div class="form-group pull-right" id="currency_Div" style="margin-bottom: 2px; padding-right: 10%">
                    <label for="" class="cols-sm-2 "><img src="<?php echo base_url('images/ico-1.png'); ?>" style="width: 22px;">
                         Currency :
                    </label>
                    <span class="trCurrencySpan"><?php echo $tr_currency; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="posHeader_2" style="display: none;">
    <table id="posHeader_2_TB">
        <tr>
            <td width="90px">Cashier</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><?php echo ucwords($this->session->loginusername); ?></td>
            <td width="90px">Customer</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><span class="customerSpan">Cash</span></td>
        </tr>
        <tr>
            <td>No of Items</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td class="itemCount" style="padding-left: 0px !important;">0</td>
            <td>Sales Mode</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;">Retail</td>
        </tr>
        <tr>
            <td>Ref No</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><?php echo $refNo; ?></td>
            <td>Currency</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><span
                        class="trCurrencySpan"><?php echo $tr_currency; ?></span></td>
        </tr>
    </table>
</div>
<div>
    <input type="text" id="tempQty" onchange="tempQtychange()" style="display: none;">
    <script>
        /*onchange="tempQtychange()" */
        /*function tempQtychange() {
            var tempQty_val = $('#tempQty').val();
            $('#qtynumbrpd').val(tempQty_val);
            $('.itemQtySpan hidden').html(tempQty_val);
        }*/
        function tempQtychange() {
            var tempQty_val = $('#tempQty').val();
            if(tempQty_val !== ""){
                $('#qtynumbrpd').val(tempQty_val).change();
            }
        }
    </script>
</div>
<div id="form_div" class="bg-purple-900">
    <div class="hide" style="margin-bottom: -10px">
        <label class="checkbox-inline no_indent">
            <input type="checkbox" id="enable_BC" value="option1" checked="checked"> <strong>Enable BC</strong>
        </label>
    </div>
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <div class="cols-12">
                <div class="m-b-lg ls-bg-row">
                    <form class="form-horizontal pos-style-one" role="form" id="pos_form" autocomplete="off">
                        <div class="row" style="margin-top: 1%">
                            <div class="col-md-2 col-sm-2">
                                <div class="form-group cols-sm-3 item-search-container">
                                    <label for="itemSearch" class="cols-sm-4"> Item </label>

                                    <div class="input-group">
                                        <input type="text" name="itemSearch" id="itemSearch" placeholder="shortcut [F9]"
                                               class="form-control br-r-0">
                                        <span class="input-group-addon" onclick="searchItem_modal();"
                                              style="cursor: pointer;"><i class="fa fa-search"></i> [F4]</span>
                                    </div>

                                    <input type="hidden" id="itemAutoID" name="itemAutoID">
                                    <input type="hidden" id="itemDescription" name="itemDescription">
                                    <input type="hidden" id="barcode" name="barcode">
                                    <input type="hidden" id="currentStock" name="currentStock">
                                    <input type="hidden" id="taxCalTotal" name="taxCalTotal">
                                    <input type="hidden" id="isNonDefaultDiscount" name="isNonDefaultDiscount">
                                </div>
                            </div>


                            <div class="col-md-3 col-sm-2">
                                <div class="form-group">
                                    <label for="itemDescription2" class="cols-sm-2">Description</label>
                                    <input type="text" name="" id="itemDescription2" disabled="disabled"
                                           class="form-control  formInput br-l-0" style="width: 85%;">
                                </div>
                            </div>

                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label for="currentStockDsp" class="cols-sm-2">Stock</label>
                                    <input type="text" name="" id="currentStockDsp" disabled="disabled"
                                           class="form-control number formInput" style="width: 85%;">
                                </div>
                            </div>

                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label for="itemUOM" class="cols-sm-1"> UOM </label>
                                    <select name="itemUOM" disabled="disabled" id="itemUOM"
                                            class="form-control  formInput"
                                            style="width: 85%;">
                                        <option></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-1 col-sm-1">
                                <div class="form-group cols-sm-3">
                                    <label for="itemQty" class="cols-sm-4"> Qty </label>
                                    <input type="text" name="itemQty" id="itemQty"
                                           class="form-control  formInput number"
                                           style="width: 85%;" onclick="selectvalue(this)">
                                </div>
                            </div>

                            <div class="col-md-1 col-sm-2">
                                <div class="form-group cols-sm-3">
                                    <label for="salesPrice" class="cols-sm-4"> Sales Price </label>
                                    <input type="text" name="salesPrice" id="salesPrice" placeholder="Ctrl+E"
                                           class="form-control  formInput number"
                                           style="width: 85%" onclick="selectvalue(this)">
                                    <input type="hidden" id="salesPricehn" name="salesPricehn">
                                </div>
                            </div>


                            <div class="col-md-1 col-sm-1">
                                <div class="form-group cols-sm-3">
                                    <label for="disPer" class="cols-sm-4"> Disc% </label>
                                    <input type="text" name="disPer" id="disPer" placeholder="Ctrl+Q"
                                           class="form-control  formInput number"
                                           style="width: 85%;" onclick="selectvalue(this)">
                                </div>
                                <input type="hidden" id="isPromo" value="0"/>
                            </div>

                            <div class="col-md-1 col-sm-1">
                                <div class="form-group cols-sm-3">
                                    <label for="disAmount" class="cols-sm-4"> Discount </label>
                                    <input type="text" name="disAmount" placeholder="Ctrl+D" id="disAmount"
                                           class="form-control  formInput number"
                                           style="width: 85%;" onclick="selectvalue(this)">
                                </div>
                            </div>


                            <div class="col-md-1 col-sm-1" style="margin-top:25px">
                                <div class="form-group cols-sm-2">
                                    <label class="cols-sm-4"> &nbsp;</label>
                                    <button type="submit"
                                            class="btn btn-primary"
                                            id="pos-add-btn"
                                            style="height:28px; " disabled="disabled">Update</button>
                                    <input type="hidden" id="item-image-hidden"/>
                                    <input type="hidden" id="is-edit" value=""/>
                                </div>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
            <form id="my_form" class="form_pos_receipt" onsubmit="return false">
                <div class="cols-12">
                    <div class="m-b-lg fixHeader_Div" id="itemDisplayTB_div" style="height:422px;auto;width: 100%;background:#f1e7fe;">
                        <table class="table table-bordered table-condensed table-row-select table-striped" id="itemDisplayTB"
                               style="">
                            <thead>
                            <tr class="header_tr" style="background-color: #75BDD8 !important;;">
                                <th></th>
                                <th style="width: 10%;">Secondary Code</th>
                                <th>Barcode</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Disc%</th>
                                <th>Discount</th>
                                <?php if($isGroupBasedTaxPolicy==1){?>
                                <th>Tax</th>
                                <th>Tax Total</th>
                                <?php }?>
                                <th>Total</th>

                                <th></th>
                            </tr>
                            </thead>

                            <tbody id="tbody_itemList">
                            </tbody>
                        </table>
                        <input type="hidden" name="_cashAmount" id="h_cashAmount" value="0">
                        <input type="hidden" name="_chequeAmount" id="h_chequeAmount" value="0">
                        <input type="hidden" name="_cardAmount" id="h_cardAmount" value="0">
                        <input type="hidden" name="_giftCardAmount" id="h_giftCardAmount" value="0">
                        <input type="hidden" name="_creditNoteAmount" id="h_creditNoteAmount" value="0">
                        <input type="hidden" name="_creditSalesAmount" id="h_creditSalesAmount" value="0">
                        <input type="hidden" name="creditNote-invID" id="creditNote-invID">
                        <input type="hidden" name="customerCode" id="customerCode" value="CASH">
                        <input type="hidden" name="_trCurrency" id="_trCurrency" value="<?php echo $tr_currency; ?>">
                        <input type="hidden" name="_referenceNO" id="_referenceNO" value="">
                        <input type="hidden" name="_cardNumber" id="_cardNumber" value="">
                        <input type="hidden" name="_bank" id="_bank" value="">
                        <input type="hidden" name="_chequeNO" id="_chequeNO" value="">
                        <input type="hidden" name="_chequeCashDate" id="_chequeCashDate" value="">
                        <input type="hidden" id="isInvoiced" name="isInvoiced">
                    </div>
                </div>

                <div class="col-12" style="margin-top: 1%; padding-bottom: 5%; text-align: center !important;">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="padding-top: 1%;">
                        <div class="row row-centered" style="" id="actionBtn-div">
                            <div class="col-12 ac">


                                <span class="button-wrap">
                                        <button type="button"
                                                class="button f-13  button-rounded   button-primary button-pill"
                                                onclick="newInvoice()"> New [F5]
                                        </button>
                                 </span>

                                <span class="button-wrap">
                                    <button type="button"
                                            class="button f-13   button-rounded button-primary button-pill"
                                            onclick="hold_invoice()">
                                        Hold [F2]
                                    </button>
                                </span>

                                <span class="button-wrap">
                                    <button type="button" class="button f-13 button-rounded button-primary button-pill"
                                            title="shortcut - F6"
                                            onclick="open_customer_modal()">
                                        Customer [F6]
                                    </button>
                                </span>


                                <span class="button-wrap">
                                   <button type="button"
                                           class="button f-13 button-rounded button-border button-pill btn-primary-pos"
                                           onclick="adjust_qty_new()"
                                           title="shortcut - F8">
                                       Edit Qty [F8]
                                   </button>
                                </span>

                                <br class="br_tmp"/>
                                <span class="button-wrap">
                                   <button type="button"
                                           class="f-13 button button-rounded button-border button-pill btn-primary-pos"
                                           title="Closed Bills"
                                           onclick="open_void_Modal_g_pos()">
                                       Closed Bills <i class="fa fa-ban text-red" aria-hidden="true"></i>
                                   </button>
                                </span>
                                <span class="button-wrap">
                                   <button type="button"
                                           class="button f-13 button-rounded button-border button-pill btn-primary-pos"
                                           title="shortcut - F7"
                                           onclick="checkifItemExsist()">
                                       Recall [F7]
                                   </button>
                                </span>


                                <span class="button-wrap">
                                   <button type="button"
                                           class="button f-13 button-rounded button-border button-pill btn-primary-pos"
                                           onclick="checkPosAuthentication(14)"
                                           title="shortcut - F3">
                                       Return [F3]
                                   </button>
                                </span>


                                <span class="button-wrap">
                                 <button type="button"
                                         class="button f-13 button-caution button-box button-raised button-pill"
                                         onclick="deleteItem()">
                                     <i class="fa fa-trash" style="font-size:20px"></i>
                                 </button>
                                </span>


                                <span class="button-wrap">
                                 <button type="button"
                                         class="button f-13 button-caution button-box button-raised button-longshadow button-pill"
                                         onclick="checkifItemExsistpower()">
                                     <i class="fa fa-power-off " style="font-size:20px"></i>
                                 </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pad0">
                        <div class="well well-sm">
                            <table class="table table-striped table-condensed">
                                <tr>
                                    <td><span class="f-font">Total :</span></td> <!--class="f-1-2em"-->
                                    <td>
                                        <div class="f-font ar"
                                             id="netTotSpan"><?php echo $dPlace == 3 ? '0.000' : '0.00'; ?>
                                        </div>
                                        <input type="hidden" name="netTotVal" id="netTotVal" value="0"/>

                                        <div class="hide"> <!--before Discount-->
                                            <span class="f-font"
                                                  id="totSpan"><?php echo $dPlace == 3 ? '0.000' : '0.00'; ?></span>
                                            <input type="hidden" name="totVal" id="totVal" value="0"/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="f-font"> Discount [F11] :</span></td> <!--class="f-1-2em"-->
                                    <td>
                                        <input type="text"
                                               class="form-control allownumericwithdecimal w60 f-l f-16 fw600 ar bg-gray-700 br-5"
                                               name="gen_disc_percentage"
                                               id="gen_disc_percentage" value="0" onchange="checkPosAuthentication(18,'gen_disc_percentage')" onclick="selectvalue(this)" placeholder="%"/>
                                        <input type="text"
                                               class="allownumericwithdecimal form-control w100 f-r f-16 fw600 ar bg-gray-700 br-5"
                                               placeholder="<?php echo $dPlace == 3 ? '0.000' : '0.00'; ?>"
                                               name="gen_disc_amount" onchange="checkPosAuthentication(18,'gen_disc_amount')" onclick="selectvalue(this)" id="gen_disc_amount"/>
                                        <input type="hidden" name="gen_disc_amount_hide" id="gen_disc_amount_hide"
                                               value="0"/ >
                                        <div class="hide"> <!--Item Discount Amount -->
                                            <span class="f-font" id="discSpan">
                                                <?php echo $dPlace == 3 ? '0.000' : '0.00'; ?>
                                            </span>
                                            <input type="hidden" name="discVal" id="discVal" value="0"/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="f-font">Net Total </span></td>
                                    <td class="ar">
                                        <span class="f-font ar"
                                              id="netTot_after_g_disc_div"><?php echo $dPlace == 3 ? '0.000' : '0.00'; ?></span>
                                        <input type="hidden" name="netTot_after_g_disc" id="netTot_after_g_disc"
                                               value="0"/>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </form>

        </div> <!-- span 7 -->
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 rs-bg">
            <?php $this->load->view('system/pos-general/includes/help-modal'); ?>
            <?php $this->load->view('system/pos-general/includes/number-plate'); ?>
            <hr>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                 <span class="button-wrap">
                     <button type="button" class="button f-13 button-rounded button-primary button-pill "
                             onclick="open_pos_payments_modal()"> Pay  [F1]
                     </button>
                 </span>

                    <span class="button-wrap">
                     <button type="button"
                             class="button f-13 button-rounded button-border button-pill btn-primary-pos"
                             title="Shortcut - Ctrl + P" onclick="payInCashAutomated()">
                         Pay Cash

                         [F12]
                     </button>
                 </span>

                </div>
            </div>
        </div>
    </div>
</div>


<?php
$this->load->view('include/footer-pos-general.php');
$this->load->view('system/pos/modals/gpos-modal-auth-process');
?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="tender_modal" class="modal" style="display: none;">
    <div class="modal-dialog" style="width: 327px">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Payment Tender</h5>
            </div>
            <div class="modal-header row" style="padding: 0px 15px 0px 15px; border-bottom: none">
                <div class="col-md-12" style="background: #3c3939 !important; padding: 4px">
                    <table style="color: #ffffff">
                        <tr>
                            <td style="width: 65px"> Cashier</td>
                            <td>:</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td style="height: 20px" valign="bottom">Customer</td>
                            <td valign="bottom">:</td>
                            <td valign="bottom"><span class="customerSpan">Cash</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12" style="background: #3c3939 !important; height: 4px">&nbsp;</div>
            </div>
            <form role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body" style="padding: 0px; height: 45%">
                    <table class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Payment Type</th>
                            <th>Tender Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>Cash Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="" id="cashAmount"
                                       class="tenderTBTxt tenderPay number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Cheque Amount</td>
                            <td style="padding: 0px">
                                <div class="input-group">
                                      <span class="input-group-btn">
                                        <button class="btn btn-secondary" id="clear-cheque-amount" type="button"
                                                style="padding: 1px 6px; border-radius: 0px;">X
                                        </button>
                                      </span>
                                    <input type="text" id="chequeAmount"
                                           class="tenderTBTxt tenderPay searchData number inputCustom1" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Card Amount</td>
                            <td style="padding: 0px">
                                <div class="input-group">
                                      <span class="input-group-btn">
                                        <button class="btn btn-secondary" id="clear-card-amount" type="button"
                                                style="padding: 1px 6px; border-radius: 0px;">X
                                        </button>
                                      </span>
                                    <input type="text" id="cardAmount"
                                           class="tenderTBTxt tenderPay searchData number inputCustom1" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Gift Card Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="" id="giftCard"
                                       class="tenderTBTxt tenderPay searchData inputCustom1"
                                       readonly>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Credit Note Amount</td>
                            <td style="padding: 0px">

                                <div class="input-group">
                                      <span class="input-group-btn">
                                        <button class="btn btn-secondary" id="clear-credit-amount" type="button"
                                                style="padding: 1px 6px; border-radius: 0px;">X
                                        </button>
                                      </span>
                                    <input type="text" id="creditNote"
                                           class="tenderTBTxt tenderPay searchData number inputCustom1" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr id="creditsalesfield" class="hidden">
                            <td></td>
                            <td>Credit Sales Amount</td>
                            <td style="padding: 0px">
                                <input type="text" onchange="validateCreditSales()"
                                       name="creditSalesAmount" id="creditSalesAmount"
                                       class="tenderTBTxt tenderPay number inputCustom1">
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="col-md-12">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <table class="table table-condensed" id="paymentTB">
                                <tbody>
                                <tr>
                                    <td>
                                        <div style="width:80px">Net Total</div>
                                    </td>
                                    <td>
                                        <input type="text" name="" id="tenderNetTotal" class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:150px">Tender Amount</td>
                                    <td>
                                        <input type="text" name="" id="tenderAmountTotal"
                                               class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:150px">Amount Due</td>
                                    <td>
                                        <input type="text" name="" id="tenderDueAmount" class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:150px">Change Due</td>
                                    <td>
                                        <input type="text" name="" id="tenderChangeAmount"
                                               class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 10px">
                    <button class="btn btn-primary btn-xs" type="button" id="tenderBtn">Tender</button>
                    <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="cardDet_modal" data-keyboard="false" class="modal"
     style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 25%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">Card Details</h5>
            </div>
            <div class="modal-header row" style="padding: 0px 15px 0px 15px; border-bottom: none">
                <div class="col-md-12" style="background: #000 !important; padding: 4px">
                    <table style="color: #ffffff">
                        <tr>
                            <td style="width: 65px">Cashier</td>
                            <td>:</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td style="height: 20px" valign="bottom">Customer</td>
                            <td valign="bottom">:</td>
                            <td valign="bottom"><span class="customerSpan">Cash</span></td>
                        </tr>
                        <!--<tr>
                        <td style="height: 20px" valign="bottom">Invoice No</td>
                        <td valign="bottom">:</td>
                        <td valign="bottom"><span id="invoiceNo"></span> </td>
                    </tr>-->
                    </table>
                </div>
                <div class="col-md-12" style="background: #000 !important; height: 4px">&nbsp;</div>
            </div>
            <form role="form" id="cardPayment_form" class="form-horizontal">
                <div class="modal-body" style="padding: 0px;">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td></td>
                            <td>Card Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="cardAmount_cardDet" id="cardAmount_cardDet"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Card Number</td>
                            <td style="padding: 0px">
                                <input type="text" name="cardNumber" id="cardNumber"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Reference No</td>
                            <td style="padding: 0px">
                                <input type="text" name="referenceNO" id="referenceNO"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Card <!--Bank--></td>
                            <td style="padding: 0px">
                                <select name="bank" id="bank" class="tenderTBTxt" style="height: 24px">
                                    <?php
                                    foreach ($bank_card as $card) {
                                        echo '<option value="' . $card['GLCode'] . '"> ' . $card['description'] . ' </option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer" style="padding: 10px">
                    <input type="hidden" name="invID" id="invID" value="">
                    <button class="btn btn-primary btn-xs" type="button" onclick="save_moreCardDetails()">Save
                    </button>
                    <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="cheque_modal" data-keyboard="false" class="modal"
     style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 25%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">Cheque Details</h5>
            </div>
            <div class="modal-header row" style="padding: 0px 15px 0px 15px; border-bottom: none">
                <div class="col-md-12" style="background: #000 !important; padding: 4px">
                    <table style="color: #ffffff">
                        <tr>
                            <td style="width: 65px">Cashier</td>
                            <td>:</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td style="height: 20px" valign="bottom">Customer</td>
                            <td valign="bottom">:</td>
                            <td valign="bottom"><span class="customerSpan">Cash</span></td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-12" style="background: #000 !important; height: 4px">&nbsp;</div>
            </div>
            <form role="form" id="cardPayment_form2" class="form-horizontal">
                <div class="modal-body" style="padding: 0px;">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td></td>
                            <td>Cheque Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="chequeAmount_cheqDet" id="chequeAmount_cheqDet"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Cheque Number</td>
                            <td style="padding: 0px">
                                <input type="text" name="chequeNumber" id="chequeNumber"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Cheque Date</td>
                            <td style="padding: 0px">

                                <input type="text" value="<?php echo date('Y-m-d'); ?>" id="cashDate"
                                       class="tenderTBTxt dateFields inputCustom1" style="padding-left: 10px">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer" style="padding: 10px">
                    <button class="btn btn-primary btn-xs" type="button" onclick="save_moreChequeDetails()">Save
                    </button>
                    <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="posg_help_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-question-circle" aria-hidden="true"></i> Help - Shortcut </h4>
            </div>
            <div class="modal-body">

            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="customer_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 65%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Customer </h4>
            </div>
            <div class="modal-body">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <label for="customerSearch" style="font-weight: 600">Search </label>
                        <input type="text" placeholder="search [F10]" name="customerSearch" class="form-control"
                               id="customerSearch"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                    <div class="form-group" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <button type="button" onclick="add_new_customer_modal()"
                                class="btn btn-primary btn-xs pull-right">
                            <i class="fa fa-plus"></i>Add
                        </button>
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 300px; overflow: auto;">
                    <table class="<?php echo table_class(); ?> arrow-nav" id="customerSearchTB">
                        <thead>
                        <tr class="headerTR">
                            <th></th>
                            <th>Code</th>
                            <th>Secondary Code</th>
                            <th>Name</th>
                            <th>Telephone No</th>
                            <th>Loyalty Card Number</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-md" type="button"
                        onclick="selectEmployee()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
                <button data-dismiss="modal" class="btn btn-default btn-md" onclick="defaultCustomer()" type="button">
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="item_batch_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 65%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Select Batch </h4>
            </div>
            <div class="modal-body">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <label for="customerSearch" style="font-weight: 600; visibility:hidden">Search </label>
                        <input type="text" placeholder="search [F10]" name="customerSearch" class="form-control"
                               id="customerSearch"
                               style="height: 20px; font-size: 10px; padding: 7px 5px; visibility:hidden" autocomplete="off">
                    </div>
                    <div class="form-group" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                            <span class="text-bold">Requested Item Quantity : </span> <span id="batch_modal_qty"></span>
                    </div>
                    <div class="form-group pull-right" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <div class="form-group">
                            <label for=""><img src="<?php echo base_url('images/ico-5.png'); ?>" style="width: 22px;">
                                Invoice No :
                            </label>
                            <span style=""><?php echo $refNo; ?></span>
                        </div>
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 300px; overflow: auto;">
                    <input type="hidden" name="item_bar_code" id="item_bar_code" value="" />
                    <table class="<?php echo table_class(); ?> arrow-nav" id="itemBatchSearch">
                        <thead>
                        <tr class="headerTR">
                            <th></th>
                            <th>Batch Number</th>
                            <th>Batch Code</th>
                            <th>Default UOM</th>
                            <th>Expire Date</th>
                            <th>Remaining Quantity</th>
                            <th>Reserved Quantity</th>
                            <th>Selected</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-md" type="button" onclick="selectBatch()">
                    <!--  onclick="selectEmployee()" -->
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
               
            </div>
        </div>
    </div>
</div>

<div class="modal" id="item_warehouse_modal" role="dialog" data-keyboard="false" style="z-index:9999999;">
    <div class="modal-dialog" style="width: 65%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Stock Details </h4>
            </div>
            <div class="modal-body">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <label for="customerSearch" style="font-weight: 600; visibility:hidden">Search </label>
                        <input type="text" placeholder="search [F10]" name="customerSearch" class="form-control"
                               id="customerSearch"
                               style="height: 20px; font-size: 10px; padding: 7px 5px; visibility:hidden" autocomplete="off">
                    </div>
                    <div class="form-group pull-right" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <div class="form-group">
                            <label for=""><img src="<?php echo base_url('images/ico-5.png'); ?>" style="width: 22px;">
                                Invoice No :
                            </label>
                            <span style=""><?php echo $refNo; ?></span>
                        </div>
                    </div>
                  
                </div>
                <div class="fixHeader_Div" style="height: 300px; overflow: auto;">
                    <input type="hidden" name="item_bar_code_warehouse" id="item_bar_code_warehouse" value="" />
                    <table class="<?php echo table_class(); ?> arrow-nav" id="itemWareHouseQty">
                        <thead>
                        <tr class="headerTR">
                            <th></th>
                            <th>Store</th>
                            <th>Location</th>
                            <th>UOM</th>
                            <th>In Stock</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
               
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
               
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="recall_modal" class="modal" style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 50%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invoice Recall</h4>
            </div>
            <div class="modal-body" style="padding: 0px; height: 36%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="recall_search" style="color: #eff7ff; font-weight: 600">Search </label>
                        <input type="text" onchange="open_recallHold_modal_search()" name="recall_search"
                               class="form-control" id="recall_search"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 150px">
                    <table class="<?php echo table_class(); ?>" id="invoiceSearchTB">
                        <thead>
                        <tr class="headerTR">
                            <th></th>
                            <th>Code</th>
                            <th>CustomerID</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button class="btn btn-primary btn-md" type="button" onclick="selectInvoice()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="error_modal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Item Qty Insufficient Details</h4>
            </div>
            <div class="modal-body">
                <table class="<?php echo table_class(); ?>" id="qtyDemandTB">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item</th>
                        <th>UOM</th>
                        <th>Request QTY</th>
                        <th>Available QTY</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="creditNote_modal" class="modal" style="z-index: 1000000;">
    <div class="modal-dialog modal-md" style="width: 711px;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="" style="background-color: #0581B8;height: 45px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;"><span
                            aria-hidden="true" style="color:white;">&times;</span></button>
                <h4 class="modal-title" style="color:white;">Credit Note Search</h4>
            </div>
            <div class="modal-body" style="padding: 19px; height: 50%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="creditNote_search" style=" font-weight: 600">Search </label>
                        <input type="text" name="creditNote_search" class="form-control" id="creditNote_search"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 280px">
                    <table class="table table-striped table-condensed" id="creditNoteTB">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Note</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-sm" type="button"
                        onclick="selectCreditNote()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="giftCard_modal" class="modal" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Gift Card Search</h5>
            </div>
            <div class="modal-body" style="padding: 0px; height: 25%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="giftCard_search" style="color: #eff7ff; font-weight: 600">Search </label>
                        <input type="text" name="giftCard_search" class="form-control" id="giftCard_search"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>

                <table class="table table-striped table-condensed">
                    <tr>
                        <td>
                            <label>Issued Date</label>
                        </td>
                        <td>:</td>
                        <td>2016-09-15</td>
                    </tr>
                    <tr>
                        <td>
                            <label>Amount</label>
                        </td>
                        <td>:</td>
                        <td>3000.000</td>
                    </tr>
                    <tr>
                        <td>
                            <label>Expired Date</label>
                        </td>
                        <td>:</td>
                        <td>2016-12-15</td>
                    </tr>
                </table>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-md" type="button"
                        onclick="selectEmployee()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="till_modal" class="modal" data-keyboard="false"
     style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <!--<button type="button" class="close tillModal_close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>-->
                <h5 class="modal-title" id="tillModal_title">Start Day</h5>
            </div>
            <div class="modal-body" style="padding: 10px; height: auto">
                <div class="smallScroll" id="currencyDenomination_data" align="center"
                     style="height: auto; overflow-y: scroll"></div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <input type="hidden" id="isStart"/>

                <button class="btn btn-primary btn-sm" type="button" id="tillSave_Btn">
                    Save
                </button>
                <button data-dismiss="modal" onclick="window.location = '<?php echo site_url('dashboard'); ?>'"
                        class="btn btn-default btn-sm tillModal_close" type="button">Close
                </button>

            </div>
        </div>
    </div>
</div>

<?php if($isExchangeThroughInvoice != 1){ ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="return_modal" class="modal" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <!--<div class="modal-header " id="">-->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invoice Return</h4>
            </div>

            <div class="modal-body" style="padding: 0px;">
                <?php echo form_open('', 'id="return_form" autocomplete="off"') ?>
                <table id="inv-return-tb">
                    <tbody>
                    <tr>
                        <td style="width: 130px">Customer Code</td>
                        <td>
                            <input type="text" class="form-control returnTxt" name="return-cusCode"
                                   id="return-cusCode" readonly>
                            <input type="hidden" class="returnTxt" name="return-customerID" id="return-customerID"
                                   value="">
                        </td>
                        <td style="width: auto">&nbsp;</td>
                        <td width="100px">Credit Note No</td>
                        <td width="">
                            <input type="text" class="form-control returnTxt" aria-invalid="credit-note-no"
                                   id="returnCreditNo" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">Customer Name</td>
                        <td>
                            <input type="text" class="form-control returnTxt" name="return-cusName"
                                   id="return-cusName" readonly>
                        </td>
                        <td></td>
                        <td>Return Date</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"
                                                                  style="font-size: 10px;"></i></div>
                                <input type="text" name="return-date" value="<?php echo date('Y-m-d'); ?>"
                                       id="return-date"
                                       class="form-control returnTxt pastDateFields " style="width: 165px">
                            </div>
                        </td>
                    </tr>
                    <!--<tr>
                        <td style="width: 130px">Customer Balance</td>
                        <td>
                            <input type="text" class="form-control number returnTxt" id="return-cusBalance"
                                   readonly>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>-->
                    </tbody>
                </table>

                <table id="table-invoice-return" style="width: 750px !important;">
                    <tr>
                        <td style="width: 100px">Invoice No</td>
                        <td style="width: 200px">
                            <input type="text" class="form-control returnTxt" id="invoiceCode" value="COM/REF0000">
                            <input type="hidden" class="returnTxt" name="return-invoiceID" id="return-invoiceID"
                                   value="">
                        </td>
                        <td style="width: 50px; padding: 0px">

                            <button type="button" class="btn btn-primary" onclick="invoice_search()">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>

                        </td>
                        <td style="width: 100px">&nbsp;</td>
                        <td style="width: 100px">Invoice Date</td>
                        <td style="width: 130px">
                            <input type="text" class="form-control returnTxt" id="return-inv-date" readonly>
                        </td>
                        <td style="width: 50px; padding: 0px">

                            <button type="button" class="btn btn-primary" onclick="advance_invoice_search_modal()">
                                <i class="fa fa-search" aria-hidden="true"></i> Advance Search
                            </button>
                        </td>
                    </tr>
                </table>

                <div style="padding: 10px;">
                    <div class="fixHeader_Div" style="height: 150px; border: 1px solid #CCCCCC">
                        <table class="<?php echo table_class(); ?>" id="returnInvoiceTB">
                            <thead>
                            <tr class="header_tr">
                                <th></th>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>Bal.Qty</th>
                                <th width="60px">R.Qty</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Disc%</th>
                                <th>Discount</th>
                                <th class="tax_columns_return">Tax</th>
                                <th class="tax_columns_return">Tax Total</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <table id="return-calculation-tb" style="margin-top: 15px" border="0">
                    <tr>
                        <td rowspan="5" style="width: 150px" class="hidden" valign="middle">
                            <img class="img-thumbnail" src="<?php echo base_url('images/item/no-image.png'); ?>"
                                 id="return-item-image" style="height: 100px; width: 150px"/>
                        </td>
                        <td style="width: 70px" rowspan="5" valign="top">Remarks</td>
                        <td rowspan="5" valign="top">
                                <textarea name="remarks" cols="3" id="remarks"
                                          style="width: 90%; height: 50px; padding: 2px 5px"></textarea>
                        </td>

                        <td style="width: 70px" valign="top">&nbsp;</td>
                        <td style="width: 120px">Invoice Total</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" id="return-calculate-invTot"
                                   readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px" valign="top">&nbsp;</td>
                        <td style="width: 120px">Discount <span id="gendiscref"></span></td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" id="return-calculate-invDisc" readonly>
                            <input type="hidden" class="form-control number" name="generaldiscreturn" id="return-calculate-invDiscperc_hn" readonly>
                        </td>
                    </tr>
                    <!--<tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td>Invoice Balance</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt"
                                   id="return-calculate-invBalance" readonly>
                        </td>
                    </tr>-->
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Promo. Disc. <span id="promodiscref"></span></td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="promoDiscountAmount"
                                   id="promoDiscountAmount" readonly>
                            <input type="hidden" class="returnTxt" name="promo-discount"
                                   id="promo-discount">
                            <input type="hidden" class="returnTxt" name="promotionDiscountID"
                                   id="promotionDiscountID">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Credit Total</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="return-credit-total"
                                   id="return-credit-total" readonly>
                            <input type="hidden" class="returnTxt" name="return-subTotalAmount"
                                   id="return-subTotalAmount">
                            <input type="hidden" class="returnTxt" name="return-discTotal" id="return-discTotal">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Refundable</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="return-refund"
                                   id="return-refund" readonly>
                            <input type="hidden" name="return-refundable-hidden" id="return-refundable-hidden">
                        </td>
                    </tr>
                </table>
                <?php echo form_close(); ?>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button class="btn btn-primary btn-md" type="button" onclick="checkifItemExsistReturn()">New</button>

                <?php if($hideExchange != 1){ ?>
                    <button class="btn btn-primary btn-md" type="button" onclick="itemReturn('exchange')">Exchange
                    </button>
                <?php } ?>

                <button class="btn btn-primary btn-md" type="button" onclick="itemReturn('Refund')">Refund</button>

                <button data-dismiss="modal" class="btn btn-default btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>
<?php  } else { ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="return_modal" class="modal" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <!--<div class="modal-header " id="">-->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invoice Return</h4>
            </div>

            <div class="modal-body" style="padding: 0px;">
                <?php echo form_open('', 'id="return_form" autocomplete="off"') ?>
                <table id="inv-return-tb">
                    <tbody>
                    <tr>
                        <td style="width: 130px">Customer Code</td>
                        <td>
                            <input type="text" class="form-control returnTxt" name="return-cusCode"
                                   id="return-cusCode" readonly>
                            <input type="hidden" class="returnTxt" name="return-customerID" id="return-customerID"
                                   value="">
                        </td>
                        <td style="width: auto">&nbsp;</td>
                        <td width="100px">Credit Note No</td>
                        <td width="">
                            <input type="text" class="form-control returnTxt" aria-invalid="credit-note-no"
                                   id="returnCreditNo" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">Customer Name</td>
                        <td>
                            <input type="text" class="form-control returnTxt" name="return-cusName"
                                   id="return-cusName" readonly>
                        </td>
                        <td></td>
                        <td>Return Date</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"
                                                                  style="font-size: 10px;"></i></div>
                                <input type="text" name="return-date" value="<?php echo date('Y-m-d'); ?>"
                                       id="return-date"
                                       class="form-control returnTxt pastDateFields " style="width: 165px">
                            </div>
                        </td>
                    </tr>
                    <!--<tr>
                        <td style="width: 130px">Customer Balance</td>
                        <td>
                            <input type="text" class="form-control number returnTxt" id="return-cusBalance"
                                   readonly>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>-->
                    </tbody>
                </table>

                <table id="table-invoice-return" style="width: 750px !important;">
                    <tr>
                        <td style="width: 100px">Invoice No</td>
                        <td style="width: 200px">
                            <input type="text" class="form-control returnTxt" id="invoiceCode" value="COM/REF0000">
                            <input type="hidden" class="returnTxt" name="return-invoiceID" id="return-invoiceID"
                                   value="">
                        </td>
                        <td style="width: 50px; padding: 0px">

                            <button type="button" class="btn btn-primary" onclick="invoice_search()">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>

                        </td>
                        <td style="width: 100px">&nbsp;</td>
                        <td style="width: 100px">Invoice Date</td>
                        <td style="width: 130px">
                            <input type="text" class="form-control returnTxt" id="return-inv-date" readonly>
                        </td>
                        <td style="width: 50px; padding: 0px">

                            <button type="button" class="btn btn-primary" onclick="advance_invoice_search_modal()">
                                <i class="fa fa-search" aria-hidden="true"></i> Advance Search
                            </button>
                        </td>
                    </tr>
                </table>

                <div style="padding: 10px;">
                    <div class="fixHeader_Div" style="height: 150px; border: 1px solid #CCCCCC">
                        <table class="<?php echo table_class(); ?>" id="returnInvoiceTB">
                            <thead>
                            <tr class="header_tr">
                                <th></th>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>Bal.Qty</th>
                                <th width="60px">R.Qty</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Disc%</th>
                                <th>Discount</th>
                                <th class="tax_columns_return">Tax</th>
                                <th class="tax_columns_return">Tax Total</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <table id="return-calculation-tb" style="margin-top: 15px" border="0">
                    <tr>
                        <td rowspan="5" style="width: 150px" class="hidden" valign="middle">
                            <img class="img-thumbnail" src="<?php echo base_url('images/item/no-image.png'); ?>"
                                 id="return-item-image" style="height: 100px; width: 150px"/>
                        </td>
                        <td style="width: 70px" rowspan="5" valign="top">Remarks</td>
                        <td rowspan="5" valign="top">
                                <textarea name="remarks" cols="3" id="remarks"
                                          style="width: 90%; height: 50px; padding: 2px 5px"></textarea>
                        </td>

                        <td style="width: 70px" valign="top">&nbsp;</td>
                        <td style="width: 120px">Invoice Total</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" id="return-calculate-invTot"
                                   readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px" valign="top">&nbsp;</td>
                        <td style="width: 120px">Discount <span id="gendiscref"></span></td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" id="return-calculate-invDisc" readonly>
                            <input type="hidden" class="form-control number" name="generaldiscreturn" id="return-calculate-invDiscperc_hn" readonly>
                        </td>
                    </tr>
                    <!--<tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td>Invoice Balance</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt"
                                   id="return-calculate-invBalance" readonly>
                        </td>
                    </tr>-->
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Promo. Disc. <span id="promodiscref"></span></td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="promoDiscountAmount"
                                   id="promoDiscountAmount" readonly>
                            <input type="hidden" class="returnTxt" name="promo-discount"
                                   id="promo-discount">
                            <input type="hidden" class="returnTxt" name="promotionDiscountID"
                                   id="promotionDiscountID">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Credit Total</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="return-credit-total"
                                   id="return-credit-total" readonly>
                            <input type="hidden" class="returnTxt" name="return-subTotalAmount"
                                   id="return-subTotalAmount">
                            <input type="hidden" class="returnTxt" name="return-discTotal" id="return-discTotal">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Refundable</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="return-refund"
                                   id="return-refund" readonly>
                            <input type="hidden" name="return-refundable-hidden" id="return-refundable-hidden">
                        </td>
                    </tr>
                </table>
                <?php echo form_close(); ?>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button class="btn btn-primary btn-md" type="button" onclick="checkifItemExsistReturn()">New</button>

                <?php if($hideExchange != 1){ ?>
                    <button class="btn btn-primary btn-md" type="button" id="btn-exchangeToInvoice" onclick="exchangeAddInvoice('exchange')">Exchange
                    </button>
                <?php } ?>

                <button class="btn btn-primary btn-md" type="button" onclick="itemReturn('Refund')">Refund</button>

                <button data-dismiss="modal" class="btn btn-default btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<?php } ?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="testModal" class="modal" data-keyboard="false"
     style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">

            <div class="modal-body" style="padding: 0px; height: 0px">
                <div class="alert alert-success fade in" style="margin-top:18px;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"
                       style="text-decoration: none">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size:15px"></i>
                    </a>
                    <strong>Session Successfully closed.</strong> Redirect in <span id="countDown"> 5 </span>
                    Seconds.
                </div>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="print_template" data-keyboard="false" class="modal"
     style="display: none;">
    <div class="modal-dialog" style="width: 420px">
        <div class="modal-content">
            <div class="modal-header posModalHeader">
                <button type="button" class="close close-btn-pos" data-dismiss="modal" aria-hidden="true" onclick="newInvoice(1)" id="btn_print_template">
                    <i class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Print </h4>
            </div>
            <div class="modal-body" id="pos_modalBody_posPrint_template" style="height: 400px;overflow-y: auto;">
                <div id="wrapper">
                    <div id="print_content"></div>

                    <div id="bkpos_wrp" style="margin-top: 10px;">


                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-block btn-primary btn-flat" onclick="print_pos_report()"
                        style="">
                    <i class="fa fa-print"></i> Print
                </button>
                <button type="button" id="gposvoidbillbtn" class="hidden btn btn-block btn-default btn-flat" onclick="checkPosAuthentication(3)" style=" cursor:pointer; background-color:#ff7b6c; color:white;">
                    <i class="fa fa-close"></i> Void Bill
                </button>
                <button type="button" class="btn btn-block btn-default btn-flat" onclick="close_posPrint();">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i> Back to POS &amp; Create New
                </button>
                <input type="hidden" id="voidhnid">
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="gpos_open_void_receipt" class="modal fade" data-keyboard="true"
     data-backdrop="static" >
    <div class="modal-dialog modal-lg" >
        <div class="modal-content" style="min-height: 600px;">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_closed_bills');?><!--Closed Bills--> </h4>
            </div>
            <div id="voidReceiptgpos" class="modal-body" style="overflow: visible; background-color: #FFF; min-height: 100px;" onclick="">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" id="insufficentmodel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Following items are insufficient</h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th >Item Code</th>
                            <th>Description</th>
                            <th>Qty</th>
                        </tr>
                        </thead>
                        <tbody id="errormsgInsuf">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<div class="modal" id="loyalty_redeem_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="z-index: 10000;">
    <div class="modal-dialog" style="width: 30%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-question-circle" aria-hidden="true"></i> Redeem Loyalty </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="rdmpaymentTypeID" id="rdmpaymentTypeID">
                    <input type="hidden" name="rdmcustomerID" id="rdmcustomerID">
                    <input type="hidden" name="pointsToPriceRedeemed" id="pointsToPriceRedeemed">
                    <input type="hidden" name="minimumPointstoRedeem" id="minimumPointstoRedeem">
                    <input type="hidden" name="exchangeRate" id="exchangeRate">
                    <input type="hidden" name="poinforPuchaseAmount" id="poinforPuchaseAmount">
                    <input type="hidden" name="purchaseRewardPoint" id="purchaseRewardPoint">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label>Barcode</label>
                            <input id="rdmbarcode" name="barcode" type="text"  class="form-control">
                            <div id="myInputautocompleteBarcode-list" class="autocomplete-items"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label>Telephone</label>
                            <input id="rdmcustomerTelephone" name="customerTelephone" type="text" placeholder="Telephone" autocomplete="off" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label>Name</label>
                            <input id="rdmCustomerName" name="CustomerName" type="text" placeholder="Name" readonly class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-6">
                        <label>Loyalty Balance Points</label>
                            <input id="availablepoints" name="availablepoints" type="text" readonly class="form-control" value="0">
                        </div>
                        <div class="col-sm-6">
                            <label>Loyalty Balance(<?php echo $this->common_data['company_data']['company_default_currency']?>)</label>
                            <input id="loyalty_balance_amount" name="loyalty_balance_amount" type="text" placeholder=""
                                   readonly class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label>Bill Amount(<?php echo $this->common_data['company_data']['company_default_currency'] ?>)</label>
                            <input id="rdmbillamnt" name="rdmbillamnt" type="text" readonly class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label>Redeem(<?php echo $this->common_data['company_data']['company_default_currency'] ?>)</label>
                            <input id="redeempts" name="redeempts" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-12 control-label" style="color: red;font-size: 80%"> <i class="fa fa-check" style="color: black" aria-hidden="true"></i><label id="msgpoints"> </label></label>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group">
                       <label class="col-sm-9 control-label" style="color: red;font-size: 80%"> <i class="fa fa-check" style="color: black" aria-hidden="true"></i>&nbsp;Minimum points allowed for redemption : <label id="minimupoints" style="color: red;">0</label> </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 10px">
                <button class="btn btn-primary btn-lg" onclick="check_loyalty_points_redeem()" type="button">Redeem</button>
                <button data-dismiss="modal" class="btn btn-danger btn-lg" type="button">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="advance_return_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="z-index: 10000;">
    <div class="modal-dialog">
        <div class="modal-content modal-lg">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-question-circle" aria-hidden="true"></i> Advance Return Search </h4>
            </div>
            <div class="modal-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_public_1"  data-toggle="tab" aria-expanded="false">Date Search</a></li>
                        <li class=""><a href="#tab_public_2"  data-toggle="tab" aria-expanded="true" >Item</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_public_1">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>From</label>
                                    <input type="text"  class="form-control"
                                           name="filterFrom2" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
                                           style="width: 100px;">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>to</label>
                                    <input type="text"  class="form-control"
                                           value="<?php echo date('d/m/Y') ?>"
                                           style="width: 100px;" placeholder="To" name="filterTo" id="filterTo2">
                                </div>

                                <div class="form-group col-md-3 hidden">
                                    <label>Invoice Code</label>
                                    <input type="text" class="form-control returnTxt" id="invoiceCodeDate" name="invoiceCodeDate" value="COM/REF0000" autocomplete="off">
                                </div>

                                <div class="form-group col-md-3">
                                   <br>
                                    <button type="button" class="btn btn-lg btn-primary" onclick="load_date_wise_return()">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <table  class="table table-striped table-condensed" id="dateWiseinvtable">
                                    <thead>
                                    <tr>
                                        <th >#</th>
                                        <th >Invoice Code</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="Returninvload">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane " id="tab_public_2">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Item</label>
                                    <input type="text" class="form-control returnTxt" id="invoiceItemCode" name="invoiceItemCode" value="COM/REF0000" autocomplete="off">
                                    <input type="hidden" class="form-control" id="itemIdhn" name="itemAutoID">
                                </div>

                                <div class="form-group col-md-3">
                                    <br>
                                    <button type="button" class="btn btn-lg btn-primary" onclick="load_item_wise_return()">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>


                            <div class="row">
                                <table  class="table table-striped table-condensed">
                                    <thead>
                                    <tr>
                                        <th >#</th>
                                        <th >Invoice Code</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="ReturninvItemload">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
            </div>
            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
$data['customerCategory'] = $customerCategory;
$data['gl_code_arr'] = $gl_code_arr;
$data['country_arr'] = $country_arr;
$data['currncy_arr'] = $currncy_arr;
$data['taxGroup_arr'] = $taxGroup_arr;
$this->load->view('system/pos-general/includes/payment-modal', $data);
$this->load->view('system/pos-general/includes/rcgc-modal', $data);
$this->load->view('system/pos-general/includes/customer-modal', $data);
$this->load->view('system/pos-general/modals/gpos-modal-search-item', $data);
$this->load->view('system/pos-general/js/gpos-js', $data);


?>


<script>
    var site_url = '<?php echo site_url() ?>';
    $(document).ready(function (e) {
        $("#filterFrom2,#filterTo2").datepicker({
            format: 'dd/mm/yyyy'
        });
    });

    var currentRequest = null;
    $("#rdmbarcode").keyup(function () {
        var skey = $("#rdmbarcode").val();
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
                    let customer = '<div onclick="set_customer_inloyalty(' + item.posCustomerAutoID + ')" data-cus_id="' + item.posCustomerAutoID + '" data-cus_name="' + item.CustomerName + '" data-cus_phone="' + item.customerTelephone + '">' + item.barcode + ' - ' + item.CustomerName + '</div>';
                    list += customer;
                });
                $("#myInputautocompleteBarcode-list").html(list);
            },
            error: function (e) {
                // Error
            }
        });
    });
</script>
<script type="text/javascript" src="<?php echo base_url('plugins/pos/pos-general.js'); ?>"></script>
</body></html>