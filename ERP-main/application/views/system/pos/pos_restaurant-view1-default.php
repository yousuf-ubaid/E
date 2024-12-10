<?php
$bank_card = load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$this->load->view('include/header', $title);
$this->load->view('include/top-posr');

$d = get_company_currency_decimal();

$companyInfo = get_companyInfo();
$templateInfo = get_pos_templateInfo();
$templateID = get_pos_templateID();
$discountPolicy = show_item_level_discount();
$roundoffPolicy = round_off_option();

$get_outletID = get_outletID();
$current_companyID = current_companyID();
$isOutletTaxEnabled = json_encode(isOutletTaxEnabled($get_outletID, $current_companyID));


$smsAllowed = getPolicyValues('SMS', 'All');
if ($smsAllowed == ' ' || empty($smsAllowed) || $smsAllowed == null) {
    $smsAllowed = 0;
}
?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos-style-all-device.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/numPadmaster/jquery.numpad.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/virtual-keyboard-mlkeyboard/jquery.ml-keyboard.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/buttons/button.css') ?>">

    <style>
        .receiptPadding {
            width: <?php echo $discountPolicy ? '16.5%' : '24.5%';  ?>;
            float: left;
            text-align: right;
            padding-right: 3px;
        }

        .receiptPaddingHead {
            width: <?php echo $discountPolicy ? '15%' : '20%';  ?>;
            float: left;
            text-align: right;
            padding-right: 3px;
        }

        .fade {
            opacity: 0;
            -webkit-transition: opacity 0.1s linear;
            -moz-transition: opacity 0.1s linear;
            -ms-transition: opacity 0.1s linear;
            -o-transition: opacity 0.1s linear;
            transition: opacity 0.1s linear;
        }

        .overlay {
            background-color: white;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0px;
            left: 0px;
            z-index: 1000;
        }

        .num-button {
            display: inline-block;
            height: 84px;
            list-style-type: none;
            padding: 6px;
            position: relative;
            transition: transform .2s cubic-bezier(.333, 0, 0, 1);
            vertical-align: top;
            width: 84px;
            margin: 15px;
        }

        .num-button button {
            height: 92px;
            width: 109px;
            font-size: 37px;
        }
    </style>


    <div id="posHeader_2" class="hide" style="display: none;">
    </div>
    <div id="form_div" style="padding: 1%; margin-top: 40px">

        <div class="row" style="margin-top: 0px;">
            <div class="col-md-6">

                <div class="panel panel-default" style="border: 1px solid #ddd;">
                    <div class="panel-body tabs" style="padding:3px;">
                        <div style="padding: 0px 0px 0px 15px;">
                            <button id="backToCategoryBtn" style="padding: 11px 11px 9px 7px;" data-toggle="tab"
                                    class="btn btn-lg btn-default btnCategoryTab"
                                    tabindex="-1"
                                    href="#pilltabCategory">
                                <i class="fa-21 fa fa-backward fa-2x"></i>
                            </button>

                            <?php
                            for ($i = 0; $i < 10; $i++) {
                                ?>
                                <button style="padding: 10px 14px;" onclick="updateQty_invoice(this)"
                                        class="btn btn-lg btn-primary fSizeBtn">
                                    <?php echo $i; ?>
                                </button>
                                <?php
                            }
                            ?>

                            <button style="padding: 10px 14px;" onclick="updateQty_invoice(this)"
                                    class="btn btn-lg btn-primary fSizeBtn">
                                .
                            </button>
                            <button rel="tooltip" style="    padding: 11px 12px 9px 13px; font-weight: 600;"
                                    title="clear"
                                    onclick="updateQty_invoice(this)"
                                    class="btn btn-lg btn-default fSizeBtn">C
                            </button>
                            <button id="backToCategoryBtn" onclick="go_one_step_back_category()"
                                    style="padding: 11px 12px 9px 7px;" data-toggle="tab"
                                    class="btn btn-lg btn-default btnCategoryTab">
                                <i class="fa-21 fa fa-chevron-left fa-2x"></i>
                            </button>


                        </div>

                        <div class="row" style="margin-left: 0px; margin-right: 0px;">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"
                                 style="padding-left: 15px; padding-top: 10px;">
                                <input type="text" class="form-control" placeholder="Press 'F2' or 'Ctrl+F' to Search"
                                       id="searchProd">
                            </div>

                            <!-- BARCODE READER  -->
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="padding-top: 10px;">
                                <input type="text" class="form-control" placeholder="Barcode (shortcut F3)"
                                       id="barcodeInput">
                            </div>
                        </div>

                        <input type="hidden" id="categoryParentID" value="0">
                        <input type="hidden" id="categoryCurrentID" value="0">

                        <div style="margin: 0px 0px 0px 15px;">
                            <div class="tab-content dynamicSizeCategory" style="overflow: scroll; height: 350px;"
                                 id="allProd">
                                <div class="tab-pane fade in" id="pilltabAll_full">
                                    <?php
                                    if (!empty($menuCategory)) {
                                        foreach ($menuCategory as $Category) {
                                            $autoID = $Category['autoID'];
                                            $menuList = get_wareHouseMenuByCategory($autoID);
                                            if (!empty($menuList)) {
                                                foreach ($menuList as $menu) {
                                                    echo generate_menu($menu);
                                                }
                                            }
                                        }
                                    }

                                    /*if (!empty($menuSubCategory)) {
                                        foreach ($menuSubCategory as $Category) {
                                            $autoID = $Category['autoID'];
                                            $menuCategoryID = $Category['menuCategoryID'];
                                            $subCategoryList = get_subCategory($menuCategoryID, $warehouseID);

                                            if (!empty($subCategoryList)) {
                                                foreach ($subCategoryList as $catList) {
                                                    echo generate_menuCategory($catList, $autoID);

                                                }
                                            }
                                        }
                                    }*/
                                    ?>
                                </div>

                                <div class="tab-pane fade in active" id="pilltabCategory">
                                    <?php
                                    /** ------ Shortcuts ------  */
                                    $shortcuts = get_warehouseMenuShortcuts();
                                    if ((!empty($shortcuts))) {
                                        ?>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <?php
                                                foreach ($shortcuts as $menu) {
                                                    echo generate_menu($menu);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <hr class="posSeparator">

                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <?php
                                            if (!empty($menuCategory)) {

                                                foreach ($menuCategory as $Category) {
                                                    if ($Category['levelNo'] == 0) {
                                                        echo generate_menuCategory($Category, 0);
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                /********************** Sub Category *********************** */
                                if (!empty($menuSubCategory)) {
                                    foreach ($menuSubCategory as $Category) {

                                        $autoID = $Category['autoID'];
                                        $menuCategoryID = $Category['menuCategoryID']; /* master ID */
                                        $subCategoryList = get_subCategory($menuCategoryID, $warehouseID);
                                        ?>
                                        <div class="tab-pane fade" id="pilltab<?php echo $autoID ?>">
                                            <?php
                                            if (!empty($subCategoryList)) {
                                                foreach ($subCategoryList as $catList) {
                                                    echo generate_menuCategory($catList, $autoID);
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                                <!--Global Search -->
                                <div class="tab-pane fade in" id="pilltabAll">
                                    <?php
                                    if (!empty($menuCategory)) {
                                        foreach ($menuCategory as $Category) {
                                            $autoID = $Category['autoID'];
                                            $menuList = get_wareHouseMenuByCategory($autoID);
                                            if (!empty($menuList)) {
                                                foreach ($menuList as $menu) {
                                                    echo generate_menu($menu);
                                                }
                                            }
                                        }
                                    }

                                    if (!empty($menuSubCategory)) {
                                        foreach ($menuSubCategory as $Category) {
                                            $autoID = $Category['autoID'];
                                            $menuCategoryID = $Category['menuCategoryID']; /* master ID */
                                            $subCategoryList = get_subCategory($menuCategoryID, $warehouseID);

                                            if (!empty($subCategoryList)) {
                                                foreach ($subCategoryList as $catList) {
                                                    echo generate_menuCategory($catList, $autoID);

                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                                <?php
                                if (!empty($menuCategory)) {
                                    foreach ($menuCategory as $Category) {
                                        $autoID = $Category['autoID'];
                                        ?>
                                        <div class="tab-pane fade in" id="pilltab<?php echo $autoID ?>">
                                            <?php
                                            $menuList = get_wareHouseMenuByCategory($autoID);

                                            if (!empty($menuList)) {
                                                foreach ($menuList as $menu) {
                                                    echo generate_menu($menu);
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <?php
                    $cctv = is_cctv_feed_active();
                    if ($cctv) {
                        ?>
                        <button type="button" onclick="open_cctv_modal();"
                                class="btn btn-default btn-lg buttonDefaultSize">
                            <i class="fa fa-video-camera" aria-hidden="true"></i> CCTV
                        </button> <?php } ?>

                    <button type="button" onclick="POS_SizeMax()" style="font-weight: bold;"
                            class="btn btn-default btn-lg buttonDefaultSize">&nbsp;+&nbsp;
                    </button>
                    <input type="hidden" id="currentSize" value="">
                    <button type="button" onclick="POS_SizeMin()" style="font-weight: bold;"
                            class="btn btn-default btn-lg buttonDefaultSize">&nbsp;-&nbsp;
                    </button>
                    <button type="button" onclick="POS_SizeDefault()" class="btn btn-default btn-lg buttonDefaultSize">
                        Default Size
                    </button>

                </div>

                <?php
                $kotBtn = is_show_KOT_button();
                if ($kotBtn) {
                    $BF_KOT_REF_ON = is_show_hold_reference_before_KOT();
                    if ($BF_KOT_REF_ON) {
                        $kot_js = 'onclick="before_kot_hold_reference()"';
                    } else {
                        $kot_js = 'onclick="POS_SendToKitchen()"';
                    }
                    ?>
                    <button type="button" <?php echo $kot_js ?> class="btn btn-lg btn-danger buttonDefaultSize"
                            id="btn_pos_sendtokitchen"><i class="fa fa-cutlery" aria-hidden="true"></i> Send
                        KOT
                    </button>
                <?php } ?>

                <?php

                if ($showStaffButton) {
                    echo ' <button type="button" class="btn btn-warning btn-lg buttonDefaultSize" style="width: 105px;"
                            id="popupWaitersList" onclick="popupWaitersList()">' . $this->lang->line('posr_waiter') . '
                    </button>';
                }

                ?>

                <?php
                if ($pinBasedAccess) {
                    if ($isScreenLockButtonEnabled) {
                        echo ' <button type="button" class="btn btn-primary btn-lg buttonDefaultSize" style="width: 105px;"
                            id="screenLockButton" onclick="lockScreen();"><i class="fa fa-lock"></i></button>';
                    }
                }
                ?>

                <?php
                $isLocalPOSEnabled = open_delevery_order();
                if ($isLocalPOSEnabled && false) {
                    ?>
                    <button type="button" onclick="updateLiveTables()" class="btn btn-lg btn-warning buttonDefaultSize">
                        <i class="fa fa-cloud-download" aria-hidden="true"></i>
                        Pull Data
                    </button>
                <?php } ?>


                <script>
                    function POS_SizeDefault() {
                        $(".itemButton").css('min-height', 112, 'important');
                        $(".itemButton").css('width', 112, 'important');
                        setCookie('btnSize', 112);
                    }

                    function POS_SizeMax() {
                        var containerSize = $(".btnStyleCustom:visible").height();
                        $("#currentSize").val(containerSize);
                        var tmpHeight = parseInt($("#currentSize").val()) + 5;
                        $(".itemButton").css('min-height', tmpHeight - 10, 'important');
                        $(".itemButton").css('width', tmpHeight - 10, 'important');
                        setCookie('btnSize', tmpHeight - 10);
                    }

                    function POS_SizeMin() {
                        var containerSize = $(".btnStyleCustom:visible").height();
                        $("#currentSize").val(containerSize);

                        var tmpHeight = parseInt($("#currentSize").val()) - 5;
                        /*$(".btnStyleCustom").css('height', tmpHeight);
                         $(".btnStyleCustom").css('width', tmpHeight, 'important');*/
                        $(".itemButton").css('min-height', tmpHeight - 10, 'important');
                        $(".itemButton").css('width', tmpHeight - 10, 'important');
                        setCookie('btnSize', tmpHeight - 10);


                    }

                    function setBtnSizeCookie() {
                        var btnSize = getCookie('btnSize');
                        if (btnSize > 0) {
                            $(".itemButton").css('min-height', btnSize, 'important');
                            $(".itemButton").css('width', btnSize, 'important');
                        }
                    }

                    function setCookie(cname, cvalue, exdays) {
                        var d = new Date();
                        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                        var expires = "expires=" + d.toUTCString();
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
                    }

                    function getCookie(cname) {
                        var name = cname + "=";
                        var decodedCookie = decodeURIComponent(document.cookie);
                        var ca = decodedCookie.split(';');
                        for (var i = 0; i < ca.length; i++) {
                            var c = ca[i];
                            while (c.charAt(0) == ' ') {
                                c = c.substring(1);
                            }
                            if (c.indexOf(name) == 0) {
                                return c.substring(name.length, c.length);
                            }
                        }
                        return "";
                    }

                    /*update the status to sent to kitchent of the order*/
                    function POS_SendToKitchen() {
                        var date = new Date();
                        var hours = date.getHours();
                        var dts = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
                        var minutes = date.getMinutes();
                        var strTime = dts + " " + hours + ':' + minutes + ':' + date.getSeconds();
                        if ($("#holdInvoiceID").val()) {
                            $.ajax({
                                type: 'POST',
                                dataType: 'JSON',
                                url: "<?php echo site_url('Pos_kitchen/updateSendToKitchen'); ?>",
                                data: {menuSalesID: $("#holdInvoiceID").val(), KOTStartDateTime: strTime},
                                cache: false,
                                beforeSend: function () {
                                    startLoad();
                                },
                                success: function (data) {
                                    stopLoad();
                                    if (data['error'] == 0) {
                                        $('#btn_pos_sendtokitchen').removeClass('btn-danger');
                                        $('#btn_pos_sendtokitchen').addClass('btn-success');
                                        //confirm_createNewBill();
                                        load_KOT_print_view(data['code'], data['auth']);
                                        $(".isSamplePrintedFlag").val(1);
                                    } else {
                                        load_KOT_print_view($("#holdInvoiceID").val());
                                        myAlert('e', data['message'])
                                    }

                                }, error: function (jqXHR, textStatus, errorThrown) {
                                    stopLoad();
                                    if (jqXHR.status == false) {
                                        myAlert('w', 'No Internet, Please try again');
                                    } else {
                                        myAlert('e', '<br>Message: ' + errorThrown);
                                    }
                                }
                            });

                        } else {
                            myAlert('e', 'Please place an order and click.')
                        }
                    }

                    function resetKotButton() { // reset the kot button as not send to kitchen color red
                        $('#btn_pos_sendtokitchen').removeClass('btn-success');
                        $('#btn_pos_sendtokitchen').addClass('btn-danger');
                    }
                </script>

            </div> <!-- / col-md-6 -->

            <div class="col-md-5" style="padding-right: 0px;">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5">
                        <input type="hidden" id="pos_orderNo" value="<?php
                        $new = $this->lang->line('common_new');
                        $invoiceID = isPos_invoiceSessionExist();
                        if (!empty($invoiceID)) {
                            $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                            echo get_pos_invoice_code($id);
                        } else {
                            echo 'New';
                        }
                        ?>">
                        <button class="btn btn-sm btn-default btn-block" style="border-radius: 0px;">
                            Order : <strong id="pos_salesInvoiceID_btn">
                                <span>
                                <?php
                                $new = $this->lang->line('common_new');
                                $invoiceID = isPos_invoiceSessionExist();
                                if (!empty($invoiceID)) {
                                    $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                                    echo get_pos_invoice_code($id);
                                } else {
                                    echo '<span class="label label-danger">' . $new . '<!--New--></span>';
                                }
                                ?>
                                    </span>
                            </strong>
                        </button>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-7 col-lg-7">
                        <button class="btn btn-sm btn-primary btn-block" style="border-radius: 0px; display: none; "
                                id="deliveryDateDiv">
                            Delivery Date : <strong id="pos_delivery_date">
                                <?php echo date('d-m-Y'); ?>
                            </strong>
                        </button>
                    </div>
                </div>

                <?php current_pc() ?>
                <div class="productCls">
                    <div class="row">
                        <div class="col-md-3 col-sm-2">
                            <!-- TABLE -->

                            <button class="btn btn-default btn-lg" type="button" id="table_order_btn"><i
                                        class="fa fa-life-ring"></i> <span
                                        id="current_table_description">Table</span>
                            </button>

                        </div>
                        <div class="col-md-9">
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></div>
                            <div class="receiptPaddingHead">@
                                <?php echo $this->lang->line('posr_rate'); ?><!--rate--></div>
                            <div class="receiptPaddingHead">
                                <?php echo $this->lang->line('common_total'); ?><!--Total--></div>
                            <div
                                    class="receiptPaddingHead <?php echo $discountPolicy ? '' : 'hide' ?>"><?php echo $this->lang->line('posr_dist'); ?>
                                .<br/>
                                <!--Dist-->%
                            </div>
                            <div class="receiptPaddingHead <?php echo $discountPolicy ? '' : 'hide' ?>">
                                <?php echo $this->lang->line('posr_dist'); ?><!--Dis-->
                                .<br/><?php echo $this->lang->line('common_amount'); ?><!--Amt-->.
                            </div>
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_net'); ?><!--Net--><br/>
                                <?php echo $this->lang->line('common_total'); ?><!--Total--></div>
                        </div>

                    </div>
                </div>

                <div style="overflow: scroll; height: 240px; width: 100%;" class="dynamicSizeItemList">
                    <form id="posInvoiceForm" class="form_pos_receipt" method="post">
                        <div id="log">
                        </div>
                    </form>
                </div>

                <form class="form_pos_receipt" method="post">
                    <div class="itemListContainer posFooterBgColor">

                        <div class="row itemListFoot">
                            <div class="col-md-4"><span
                                        class="posFooterTxtLg"><?php echo $this->lang->line('posr_total_item'); ?><!--Total Items--> : <span
                                            id="total_item_qty" class="posFooterTxtLg">0</span></span>
                            </div>

                            <div class="col-md-5 ar">
                                <input type="hidden" id="total_item_qty_input" name="total_item_qty_input"/>
                                <?php echo $this->lang->line('posr_total_amount'); ?><!--Gross Total-->
                                :
                            </div>
                            <div class="col-md-3 ar">
                                <div id="gross_total">0</div>
                                <input type="hidden" id="gross_total_amount_input" name="gross_total_amount_input"
                                       value="0"/>
                                <input type="hidden" id="gross_total_input" name="gross_total_input"/>
                            </div>
                        </div>


                        <div class="row itemListFoot hide">
                            <div class="col-md-4">&nbsp;</div>

                            <div class="col-md-5 ar"> <?php echo $this->lang->line('common_total'); ?><!--Total-->
                            </div>
                            <div class="col-md-3 ar">
                                <div id="totalWithoutTax">0</div>
                            </div>
                        </div>

                        <div class="row itemListFoot <?php if ($templateID == 2 || $templateID == 3) {
                        } else {
                            echo 'hide';
                        }
                        ?>">
                            <div class="col-md-4">&nbsp;</div>
                            <div class="col-md-2">
                                <div>&nbsp;</div>
                            </div>
                            <div class="col-md-3 ar">
                                <?php echo $this->lang->line('posr_total_tax'); ?><!--Total Tax -->
                            </div>
                            <div class="col-md-3 ar" style="padding-bottom: 5px;">
                                <div id="display_totalTaxAmount">0.00</div>
                            </div>
                        </div>

                        <div class="row itemListFoot <?php if ($templateID == 2 || $templateID == 4) {
                        } else {
                            echo 'hide';
                        }
                        ?>">
                            <div class="col-md-4">&nbsp;</div>

                            <div class="col-md-5 ar">
                                <?php echo $this->lang->line('posr_service_charge'); ?> <!--Service Charge--> :
                            </div>
                            <div class="col-md-3 ar" style="padding-bottom: 5px;">
                                <div id="display_totalServiceCharge">0.00</div>
                            </div>
                        </div>

                        <div class="row itemListFoot">
                            <div class="col-md-4"></div>


                            <div class="col-md-5 ar">
                                <?php echo $this->lang->line('posr_discount'); ?><!--Discount--> % :
                                <input maxlength="6" class="numpad" onchange="updateDiscountPers()"
                                       type="text"
                                       style="color: black; width: 45px; font-weight: 800; text-align: right;"
                                       name="discount_percentage" id="dis_amt" value="0" readonly>
                            </div>
                            <div class="col-md-3 ar" style="border-bottom: 0px solid #ffffff; padding-bottom: 5px;">
                                <div class="hide" id="total_discount">0.00</div>
                                <input type="text" class="numpad" id="discountAmountFooter"
                                       onchange="backCalculateDiscount()"
                                       style="width: 100%; text-align: right; font-weight: 700; color:#2E2E2E;" value=""
                                       placeholder="0.00">
                                <input type="hidden" id="total_discount_amount" name="total_discount_amount"/>
                            </div>
                        </div>

                        <?php if ($isOutletTaxEnabled == "true") { ?>
                            <div class="row itemListFoot">
                                <div class="col-md-4">&nbsp;</div>

                                <div class="col-md-5 ar">
                                    Outlet Tax :
                                </div>
                                <div class="col-md-3 ar" style="padding-bottom: 5px;">
                                    <div id="display_outlet_tax">0.00</div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row itemListFoot">
                            <div class="col-md-4 posFooterBorderBottom">&nbsp;</div>
                            <div class="col-md-2 posFooterBorderBottom">
                                <div>&nbsp;</div>
                            </div>
                            <div class="col-md-3 ar posFooterBorderBottom">
                            <span class="posFooterTxtLg">
                            <?php echo $this->lang->line('posr_net_total'); ?> <!--Net Total--> :
                                </span>
                            </div>
                            <div class="col-md-3 ar posFooterBorderBottom">
                                <div id="total_netAmount" class="posFooterTxtLg">0</div>
                            </div>
                        </div>

                    </div>
                </form>

                <input type="hidden" id="customerTypeBtnString" value="">
                <!-- 2017-03-09 -->
                <div id="pos_btnSet_dtd">
                    <?php
                    $customerType = getCustomerType();

                    if (!empty($customerType)) {
                        ?>
                        <input type="hidden" id="customerType" name="customerType" value="" data-text="">
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
                                            onclick="updateCustomerTypeBtn(<?php echo $val['customerTypeID']; ?>,<?php echo $val['isThirdPartyDelivery'] ?>,<?php echo $val['isDineIn'] ?>)"
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
                                    updateCustomerTypeBtn(<?php echo $defaultID ?>, <?php echo $isDelivery ?>,<?php echo $isDineIn ?>);
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

                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
                <div class="row">

                    <?php
                    $confomion = $this->lang->line('common_confirmation');
                    $message = $this->lang->line('common_are_you_sure_you_want_to_close_the_counter');
                    $cancel = $this->lang->line('common_cancel');
                    $ok = $this->lang->line('common_ok');
                    ?>

                    <?php if ($isPowerButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom"
                                    onclick="clickPowerOff()">
                                <i class="fa fa-power-off text-red" aria-hidden="true"></i>
                                <br/><?php echo $this->lang->line('posr_power'); ?> <!--Power-->
                            </button>
                        </div>
                    <?php } ?>




                    <?php if ($isOpenButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom" rel="tooltip"
                                    title="short cut  Ctrl+O "
                                    onclick="open_holdReceipt()">
                                <span id="wowfood_count" class="badge"
                                      style="background-color: red;color: white;">-</span>
                                <i class="fa fa-external-link-square text-purple" aria-hidden="true"></i> <br/>
                                <?php echo $this->lang->line('posr_open'); ?><!--Open-->
                            </button>
                        </div>
                    <?php } ?>


                    <?php if ($isPayButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <button class="btn btn-block btn-lg btn-success btn-myCustom btn-disable-when-load"
                                    onclick="open_pos_payments_modal()">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i><br/> &nbsp;
                                <?php echo $this->lang->line('common_pay'); ?><!--Pay--> (F1)
                            </button>
                        </div>
                    <?php } ?>

                    <?php
                    if (isset($sampleBillPolicy) && $sampleBillPolicy) {
                        ?>
                        <?php if ($isPrintSampleButtonEnabled) { ?>
                            <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                                <button type="button" onclick="print_sample_bill()"
                                        class="btn btn-block btn-lg btn-default btn-myCustom" style="padding: 10px 0">
                                    <i class="fa fa-print"></i><br/> &nbsp;
                                    <span style="font-size: 12px;">Print Sample</span>
                                </button>
                            </div>
                        <?php } ?>
                        <?php
                    }
                    ?>

                    <?php
                    if (isset($isSplitBillEnabled) && $isSplitBillEnabled) {
                        ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <button type="button" onclick="split_bill_dialog()"
                                    class="btn btn-block btn-lg btn-default btn-myCustom" style="padding: 10px 0">
                                <i class="fa fa-print"></i><br/> &nbsp;
                                <span style="font-size: 12px;">Split Bill</span>
                            </button>
                        </div>
                        <?php
                    }
                    ?>

                    <?php if ($isHoldButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <a href="#holdmodel" data-toggle="modal" style="text-decoration: none;">

                                <button class="btn btn-block btn-lg btn-danger dangerCustom2 btn-myCustom" rel="tooltip"
                                        title="short cut  Ctrl+S "
                                        onclick="holdReceipt();">
                                    <i class="fa fa-pause" aria-hidden="true"></i> &nbsp;<br>
                                    <?php echo $this->lang->line('posr_hold'); ?><!--Hold-->
                                </button>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if ($isCancelButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <button class="btn btn-lg btn-danger btn-block dangerCustom"
                                    onclick="checkPosAuthentication(9)">
                                <i class="fa fa-times" aria-hidden="true"></i> &nbsp;<br/>
                                <?php echo $this->lang->line('common_cancel'); ?><!--Cancel-->
                            </button>
                        </div>
                    <?php } ?>

                    <?php if ($isKitchenButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList" id="btn_kitchenModal">
                            <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom" rel="tooltip"
                                    title=""
                                    onclick="open_kitchen_ready()">
                                <i class="fa fa-cutlery text-purple" aria-hidden="true"></i> <br/>
                                <?php echo $this->lang->line('posr_kitchen'); ?><!--Kitchen-->
                            </button>
                        </div>
                    <?php } ?>

                    <?php if ($isGiftCardButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList" id="btn_kitchenModal">
                            <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom" rel="tooltip"
                                    title=""
                                    onclick="open_giftCardModal()">
                                <i class="fa fa-credit-card text-green"></i> <br/>
                                Gift Card
                            </button>
                        </div>
                    <?php } ?>

                    <?php if ($isClosedBillsButtonEnabled) { ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom"
                                    onclick="open_void_Modal()">
                                <i class="fa fa-ban text-red" aria-hidden="true"></i> <br/>
                                <?php echo $this->lang->line('common_closed'); ?><!--Closed--><br/>
                                <?php echo $this->lang->line('posr_bills'); ?><!--Bills-->
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div> <!--/ row -->
    </div>

    <div id="posHeader_1" class="hide">
    </div>

    <div class="modal fade" id="insufficientItemPOSRSingle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Following Items are insufficient</h4>
                </div>

                <form class="form-horizontal" id="">
                    <div class="modal-body">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Ledger Quantity</th>
                                <th>Quantity used in unclosed sessions</th>
                                <th>Quantity used in this bill</th>
                            </tr>
                            </thead>
                            <tbody id="insufficientItemPOSRSingle_body">

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="waitersListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Please select a person</h4>
                </div>

                <form class="form-horizontal" id="">
                    <div class="modal-body">
                        <div class="row">

                            <?php

                            if (isset($waiters)) {
                                $waiterIndex = 1;
                                foreach ($waiters as $waiter) {


                                    ?>

                                    <div class="col-md-4">
                                        <input type="button" class="btn btn-default waiterBtn"
                                               style="width: 100%;margin: 5px 0;height: 64px;"
                                               data-emp_id="<?php echo $waiter['crewMemberID']; ?>"
                                               value="<?php echo $waiter['crewFirstName']; ?>"
                                               onclick="markThisWaiterAsSelectedFromTerminal.call(this)"/>
                                    </div>
                                    <?php


                                }
                            }

                            ?>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="split_bill_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document" style="width: 19%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Split Bill</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Split into</label>
                                <input id="split_into" type="number"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                            data-dismiss="modal"
                            onclick="show_splitted_bill()"><?php echo $this->lang->line('common_ok'); ?></button>
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay" id="screenLockOverlay" style="display: none;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div style="margin-top: 10%;">
                        <div style="text-align: center;font-size: 25px;font-weight: bold;width: 100%;margin-bottom: 14px;">
                            <?php echo $this->lang->line('posr_pin_label'); ?>
                        </div>
                        <div>
                            <form autocomplete="off" onsubmit="waiterPinInputOnKeyUp();return false;">
                            <input type="password" style="width: 100%;height: 64px;" id="waiterPinInput" autocomplete="off"/>
                            </form>
                        </div>
                        <div>
                            <div style="    margin-left: 10%;">
                            <ul style="    width: 352px;padding: 0px;">
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(1)">1</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(2)">2</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(3)">3</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(4)">4</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(5)">5</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(6)">6</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(7)">7</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(8)">8</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(9)">9</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClear()">Clear</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-default" onclick="customNumpadClick(0)">0</button>
                                </li>
                                <li class="num-button">
                                    <button class="btn btn-success" onclick="unlockButtonClick()">Enter</button>
                                </li>
                            </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>

<?php
$data['notFixed'] = true;
$data['control_sidebar'] = false;
$data['tablet'] = false;
$data['d'] = $d;
$this->load->view('system/pos/modals/rpos-barcode', $data);
$this->load->view('system/pos/modals/pos-modal-payments', $data);
$this->load->view('system/pos/modals/rpos-modal-hold-receipt', $data);
$this->load->view('system/pos/modals/rpos-modal-kitchen-status', $data);
$this->load->view('system/pos/modals/rpos-modal-print-template', $data);
$this->load->view('system/pos/modals/rpos-modal-pack-invoice', $data);
$this->load->view('system/pos/modals/rpos-modal-void-invoice', $data);
$this->load->view('system/pos/modals/rpos-modal-till', $data);
$this->load->view('system/pos/modals/rpos-modal-kitchen-note', $data);
$this->load->view('system/pos/modals/pos-modal-gift-card');
$this->load->view('system/pos/modals/pos-modal-credit-sales');
$this->load->view('system/pos/modals/pos-modal-java-app');
$this->load->view('system/pos/modals/pos-modal-delivery', $data);
$this->load->view('system/pos/modals/pos-modal-table-order', $data);
$this->load->view('system/pos/modals/rpos-modal-auth-process', $data);
$this->load->view('system/pos/modals/rpos-modal-cctv', $data);
$this->load->view('system/pos/js/pos-restaurant-common-js', $data);

?>
    <script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/pos/r-pos.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/pos/r-pos-shortcuts.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/slick/slick/slick.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('plugins/numPadmaster/jquery.numpad.js') ?>" type="text/javascript"></script>
    <script type="text/javascript"
            src="<?php echo base_url('plugins/virtual-keyboard-mlkeyboard/jquery.ml-keyboard.js') ?>"></script>
    <script type="text/javascript">
        var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>; // Don't delete
        var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>; // Don't delete
        var till_modal = $('#till_modal');

        till_modal.on('shown.bs.modal', function (e) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/load_currencyDenominationPage'); ?>",
                beforeSend: function () {
                    $('#currencyDenomination_data').html('');
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#currencyDenomination_data').html(data);
                    if ($('#isStart').val() == 1) {
                        $('#counterID').prop('disabled', false);
                    } else {
                        $('#counterID').prop('disabled', true);
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Error in loading currency denominations.')
                    }

                }
            });
        });

        $(document).on('keypress', '.number', function (event) {
            var amount = $(this).val();
            if (amount.indexOf('.') > -1) {
                if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                    event.preventDefault();
                }
            } else {
                if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                    event.preventDefault();
                }
            }

        });
    </script>
    <script type="text/javascript">
        terminalGlobalVariables = {};
        terminalGlobalVariables.selectedWaiter = null;
        terminalGlobalVariables.holdBillWindowDefaultOrderType = 'all';
        terminalGlobalVariables.holdBillWindowDefaultWaiter = 'clear';
        terminalGlobalVariables.holdBillWindowWaiterName = 'Clear';
        terminalGlobalVariables.waiterName = '';
        var cusTypeArray = <?php echo json_encode(getCustomerType()) ?>;

        terminalGlobalVariables.cusTypeArray = [];
        cusTypeArray.forEach(function (item) {
            terminalGlobalVariables.cusTypeArray[item.customerTypeID] = item.customerDescription;
        });

        terminalGlobalVariables.dineInId = <?php echo $dineInId; ?>;

        var numberOfRequest = 0;

        function initNumPad() {
            $('.numpad').unbind();
            $('.numpad').numpad();
        }

        $(document).on('ready', function () {
            var screenLock = localStorage.getItem('screenLock');
            if (screenLock == 'on') {
                lockScreen();
            }
            load_woowfood_count();
            /** Virtual Keyboard */
            $.fn.numpad.defaults.gridTpl = '<table class="modal-content table" style="width:200px" ></table>';
            $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in" style="z-index: 5000;"></div>';
            $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:16px; font-weight: 600;" />';
            $.fn.numpad.defaults.buttonNumberTpl = '<button type="button" class="btn btn-xl-numpad btn-numpad-default"></button>';
            $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-xl-numpad" style="width: 100%;"></button>';
            $.fn.numpad.defaults.onKeypadCreate = function () {
                $(this).find('.done').addClass('btn-primary');
                $(this).find('.done').attr('onclick', 'num_padReturnCal()');
            };

            $('html').click(function (e) {
                if (!$(e.target).hasClass('touchEngKeyboard')) {
                    $("#mlkeyboard").hide();
                }
            });
            /** Dynamic Size Setup */
            setTimeout(function () {
                <?php
                switch ($templateID) {
                    case 2:
                        /** Tax & Service Charge Separated*/
                        $sizeOfDynamicHeight = '380';
                        break;
                    case 3:
                    case 4:
                        /** Tax Separated */
                        /** Service Charge Separated */
                        $sizeOfDynamicHeight = '355';
                        break;

                    default:
                        /** All Inclusive*/
                        $sizeOfDynamicHeight = '325';
                }
                ?>
                var template = '<?php echo $sizeOfDynamicHeight ?>';
                $(".dynamicSizeCategory").css("height", $(window).height() - 245 + 'px');
                $(".dynamicSizeItemList").css("height", $(window).height() - template + 'px');
            }, 100);

            initNumPad();

            setBtnSizeCookie();
            $(".btnCategoryTab").click(function (e) {
                $("#searchProd").val('');
                //$("#searchProd").trigger('keyup');
            });
            $("[rel='tooltip']").tooltip();

            $("#backToCategoryBtn").click(function (e) {
                $("#pilltabCategory").attr('data-visible', true);
                $("#pilltabCategory").show();
                $("#pilltabCategory").addClass('in');

                $(".itemButton").show();

                $("#pilltabAll_full").attr('data-visible', false);
                $("#pilltabAll_full").hide();
            });

            $("#searchProd").keyup(function (e) {
                // Retrieve the input field text
                var filter = $(this).val();

                if (filter === "") {
                    $("#pilltabCategory").attr('data-visible', true);
                    $("#pilltabCategory").show();
                    $("#pilltabAll_full").attr('data-visible', false);
                    $("#pilltabAll_full").hide();
                    $("#pilltabCategory").addClass('in');

                    var activeids = $("#allProd .active").attr('id');
                    if (activeids !== "") {
                        $("activeids").attr('data-visible', false);
                        $("activeids").hide();
                    }

                } else {
                    $("#pilltabAll_full").attr('data-visible', true);
                    $("#pilltabAll_full").show();

                    var activeids = "#" + $("#allProd .active").attr('id');
                    if (activeids !== "") {
                        $(activeids).removeClass("active");
                    }

                    $("#pilltabCategory").attr('data-visible', false);
                    $("#pilltabCategory").hide();
                }

                $(".proname").each(function () {
                    if ($(this).text().search(new RegExp(filter, "gi")) < 0) {
                        $(this).parent().hide();
                        $(this).parent().attr('data-visible', false);
                    } else {
                        $(this).parent().show();
                        $(this).parent().attr('data-visible', true);
                    }
                });

                if (e.keyCode == 13) {
                    var tmpVar = $("#pilltabAll button:visible")[0];
                    var code = $(tmpVar).data('code');
                    var pack = $(tmpVar).data('pack');
                    if (code > 0) {
                        if (pack > 0) {
                            LoadToInvoicePack(code);
                        } else {
                            LoadToInvoice(code);
                        }
                    }

                }
            });


            $('.mainCategories').slick({
                dots: false,
                infinite: false,
                speed: 300,
                slidesToShow: 4,
                adaptiveHeight: false
            });

            $("#paid_by").select2({
                templateResult: formatState,
                minimumResultsForSearch: -1
            });

            $('#rpos_print_template').on('hidden.bs.modal', function () {
                $("#dis_amt").val(0);
                $("#cardTotalAmount").val(0);
                $("#netTotalAmount").val(0);
                $("#serviceCharge").val('<?php echo get_defaultServiceCharge() ?>');
                $("#pos_payments_modal").modal('hide');
                clearSalesInvoice();

            });

            $('.numberFloat').keypress(function (event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
            updateCurrentMenuWAC();
            <?php if ($isHadSession == 0) { ?>
            $("#isStart").val(1);
            //$(".tillModal_close").hide();
            $("#tillModal_title").text("Day Start");
            $("#tillSave_Btn").attr("onclick", "shift_create()");
            till_modal.modal({backdrop: "static"});

            <?php }else { ?>
            checkPosSession();
            showNotificationUnclosedShift();
            <?php } ?>
            loadCountryDetail();
        });

        function clickPowerOff() {
            if ($("#holdInvoiceID").val() == 0) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Pos_restaurant/clickPowerOff'); ?>",
                    data: {id: null},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            $("#isStart").val(0);
                            $(".tillModal_close").show();
                            $("#tillModal_title").text("Day End");
                            $("#tillSave_Btn").attr("onclick", "shift_close()");
                            till_modal.modal({backdrop: "static"});
                        } else {
                            bootbox.alert('<div class="alert alert-danger">' + data['message'] + '</div>');
                        }
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
                return false;
            } else {
                bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Warning! </strong><br/><br/>Please close the current bill.</div>');
            }

        }

        function updateCurrentMenuWAC() {
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/updateCurrentMenuWAC'); ?>",
                data: {id: null},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
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
            return false;
        }

        function updateCustomerType(tmp) {
            var customerType = tmp.value;
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
                    stopLoad();
                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
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
            return false;
        }


        function checkisKOT(id, isPack, kotID, description) {
            localStorage.setItem('kot', kotID);
            $("#kot_kotID").val(kotID);

            if (kotID > 0) {

                if (isPack == 1) {

                    isPackCMbo = 1;
                    LoadToInvoicePack(id);
                } else {

                }

                open_kitchen_note(id, kotID);
                $("#kitchenNoteDescription").html(description);
            } else {

                if (isPack == 1) {
                    LoadToInvoicePack(id);
                } else {
                    LoadToInvoice(id);
                }
            }
        }

        function open_kitchen_note(id, kotID) {


            $("#tmpWarehouseMenuID").val(id);
            if (kotID > 0) {
                $("#pos_kitchen_note").modal('show');

            }
        }

        function LoadToInvoicePack(id) {
            load_packItemList(id);
        }

        var parentID_addOn = 0;

        function LoadToInvoice(id, parentID = 0, source = 0) {

            //setting kot.
            kotID = localStorage.getItem('kot');
            $("#kot_kotID").val(kotID);

            var discountPolicy = '<?php echo $discountPolicy ? 0 : 1;  ?>';
            var classDiscountHide = '<?php echo $discountPolicy ? '' : 'hide';  ?>';
            var dynamicWidth = '<?php echo $discountPolicy ? '16.5%' : '24.5%';  ?>';
            var date = new Date,
                hour = date.getHours(),
                minute = date.getMinutes(),
                seconds = date.getSeconds(),
                minute = minute > 9 ? minute : "0" + minute;
            seconds = seconds > 9 ? seconds : "0" + seconds;
            hour = hour > 9 ? hour : "0" + hour;
            date = hour + ":" + minute + ":" + seconds;

            var customerType = $("#customerType").val();
            var kotID = $("#kot_kotID").val();

            var kitchenNote = $("#kitchenNote").val();
            var addOnID = kotAddOnList[0];
            var Qty = 0;

            var findSelector = "[data-wh_menuid='"+id+"']";
            $("div").find(findSelector).each(function( index ) {
                Qty += parseFloat($(this).val());
            });
                
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/LoadToInvoice'); ?>",
                data: {
                    id: id,
                    customerType: customerType,
                    kotID: kotID,
                    kitchenNote: kitchenNote,
                    pos_templateID: '<?php echo get_pos_templateID(); ?>',
                    currentTime: date,
                    parentMenuSalesItemID: parentID,
                    isFromTablet: 0,
                    selectedWaiter: terminalGlobalVariables.selectedWaiter,
                    itemQty: Qty
                },
                cache: false,
                beforeSend: function () {
                    numberOfRequest++;
                    disable_POS_btnSet()
                    startLoadPos();
                },
                success: function (data) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    stopLoad();
                    if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    } else if (data['error'] == 0) {
                        var divTmp = '';
                        /*<img src="' + data['menuImage'] + '" style="max-height: 40px;" alt=""> */
                        divTmp = '<div onclick="selectMenuItem(this)" class="row itemList" id="item_row_' + data['code'] + '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;">';
                        divTmp += '<div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1 hide"></div>';
                        divTmp += '<div class="col-md-3 menuItem_pos_col_5">' + data['menuMasterDescription'] + ' </div>';
                        divTmp += '<div class="col-md-9">';
                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<input type="text" data-wh_menuID="'+data['warehouseMenuID']+'" onfocus="keepTheExistingQuantity.call(this)" onkeyup="updateQtyWithAuth(' + data['code'] + ',\'onkeyup\','+data['warehouseMenuID']+')" onchange="updateQtyWithAuth(' + data['code'] + ',\'onchange\','+data['warehouseMenuID']+')" value="1" class="display_qty menuItem_input numberFloat" id="qty_' + data['code'] + '" name="qty[' + data['code'] + ']"  />';
                        divTmp += '</div>';

                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<span class="menu_itemCost menuItemTxt"> ' + data['sellingPrice'] + '</span> <!-- @rate -->';
                        divTmp += '<input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' + data['code'] + ']" value="' + data['sellingPrice'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_inputDiscount numberFloat pricewithoutTaxDiscount" name="pricewithoutTaxDiscount[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_inputDiscount totalMenuTaxAmountDiscount numberFloat" name="totalMenuTaxAmountDiscount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/>';
                        divTmp += '<input type="hidden" class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_inputDiscount numberFloat totalMenuServiceChargeDiscount" name="totalMenuServiceChargeDiscount[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/>';
                        divTmp += '<input type="hidden"  name="frm_isTaxEnabled[' + data['code'] + ']" value="' + data['isTaxEnabled'] + '"/>';
                        divTmp += '<input type="hidden" class="isSamplePrintedFlag"  id="isSamplePrinted_' + data['code'] + '" value="0"/>';
                        divTmp += '</div>';

                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<span class="menu_total menuItemTxt">0</span>  <!-- total -->';
                        divTmp += '</div>';
                        divTmp += '<div class="receiptPadding ' + classDiscountHide + '" style="width:' + dynamicWidth + '"> <input style="width:60%;" onfocus="keepTheExistingDiscountPercentage.call(this)" id="discountPercentage_' + data['code'] + '" onchange="item_wise_discount(this,\'P\',' + data['code'] + ')" name="discountPercentage[' + data['code'] + ']"  maxlength="3" type="text" value="0" class="menu_discount_percentage menu_qty menuItem_input numberFloat numpad"> <!-- disc. % -->';
                        divTmp += '</div>';
                        divTmp += '<div class="receiptPadding ' + classDiscountHide + '" >';
                        divTmp += '<input style="width:90%;" id="discountAmount_' + data['code'] + '" onfocus="keepTheExistingDiscountAmount.call(this)" onchange="item_wise_discount(this,\'A\',' + data['code'] + ')" name="discountAmount[' + data['code'] + ']" type="text" value="0" class="menu_discount_amount menu_qty menuItem_input numberFloat numpad"><!-- disc. amount -->';
                        divTmp += '</div>';
                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt set-delete"> [' + data['sellingPrice'] + '</div> <!-- net total -->';
                        divTmp += '<div onclick="deleteLineItem(25,\'' + data['code'] + '\')" data-placement="bottom" rel="tooltip" title="Delete" style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 2px;" class="pull-right">';
                        divTmp += '<button type="button" class="btn btn-default btn-sm  itemList-delBtn c-b-20"><i class="fa fa-close closeColor"></i></button>';
                        divTmp += '</div>';
                        divTmp += '</div>';
                        divTmp += '</div>';
                        divTmp += '</div>';

                        $("#log").append(divTmp);

                        $("[rel='tooltip']").tooltip();
                        $("#pos_salesInvoiceID_btn").html(data['tmpInvoiceID_code']);
                        $("#pos_orderNo").val(data['tmpInvoiceID_code']);
                        $("#holdInvoiceID_input").val(data['tmpInvoiceID']);
                        $("#holdInvoiceID").val(data['tmpInvoiceID']);
                        $("#holdInvoiceID_codeTmp").val(data['tmpInvoiceID_code']);

                        if (data['isPack'] == 1) {
                            savePackDetailItemList(data['code']);
                        }
                        calculateFooter();
                        selectMenuItemSpefici('item_row_' + data['code']);
                        if (kotAddOnList.length > 0) {
                            if (kotAddOnList[0] > 0) {
                                if (source == 0) {
                                    parentID_addOn = data['code'];
                                }
                                LoadToInvoice(kotAddOnList[0], parentID_addOn, 1);
                                kotAddOnList = jQuery.grep(kotAddOnList, function (value) {
                                    return value != kotAddOnList[0];
                                });
                            }
                        }
                        initNumPad();
                    } else if (data['error'] == 2) {
                        $('#insufficientItemPOSRSingle_body').html('');
                        if (!jQuery.isEmptyObject(data['insuf'])) {
                            $.each(data['insuf'], function (item, value) {
                                var msgData = '<tr>' 
                                +'<td>' + value['itemSystemCode'] + '</td>'
                                +'<td>' + value['itemDescription'] + '</td>'
                                +'<td>' + value['currentStock'] + '</td>'
                                +'<td>' + value['currentUsage'] + '</td>'
                                +'<td>' + value['quantityUsedInThisBill'] + '</td>'
                                +'</tr>';
                                $('#insufficientItemPOSRSingle_body').append(msgData);
                            });
                            $("#insufficientItemPOSRSingle").modal({backdrop: "static"});
                        }
                    }
                    $("#kitchenNote").val('');
                    $("#kot_kotID").val(0);
                    $(".kot-add-on-input-check").iCheck('uncheck');
                    isPackCMbo = 0;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
            return false;
        }

        function calculateTotalAmount(priceWithoutTax, taxAmount, ServiceCharge, qty, discount) {
            var discount = parseFloat(discount);
            var discountAmount = 0;
            var templateID = '<?php echo $templateID ?>';
            var totalAmount = 0;
            switch (parseInt(templateID)) {
                case 1:
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount) + parseFloat(ServiceCharge)) * parseFloat(qty);
                    /** All Inclusive */
                    break;
                case 2:
                    /** Tax & Service Charge Separated */
                    totalAmount = parseFloat(priceWithoutTax) * parseFloat(qty);
                    break;
                case 3:
                    /** Tax Separated */
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(ServiceCharge)) * parseFloat(qty);
                    break;
                case 4:
                    /** Service Charge Separated */
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount)) * parseFloat(qty);
                    break;

                default:
                    /** All Inclusive */
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount) + parseFloat(ServiceCharge)) * parseFloat(qty);
            }
            if (discount > 0) {
                discountAmount = (totalAmount * discount) / 100;
            }
            totalAmount = totalAmount - discountAmount;
            return totalAmount;
        }

        function calculateFooter(discountFrom) {
            $('.numberFloat').keypress(function (event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

            var totalAmount = 0;
            var grossTotal = 0;
            var totalQty = 0;
            var totalDiscount = 0;
            var netAmountTotal = 0;
            var totalTax = 0;
            var totalTaxDiscount = 0;
            var serviceCharge = 0;
            var serviceChargeDiscount = 0;
            var totalPriceWithoutTax = 0;
            var totalPriceWithoutTaxDiscount = 0;

            $("div .itemList").each(function (e) {

                var qty = $(this).find(".display_qty").val();
                if (qty < 0) {
                    $(this).find(".display_qty").val(1);
                    qty = $(this).find(".display_qty").val();
                }

                var tmpSC = $(this).find(".totalMenuServiceCharge").val();
                serviceCharge = parseFloat(serviceCharge) + (parseFloat(tmpSC) * qty);

                var tmpTax = $(this).find(".totalMenuTaxAmount").val();
                totalTax = parseFloat(totalTax) + (parseFloat(tmpTax) * qty);

                var pricewithoutTax = $(this).find(".pricewithoutTax").val();
                totalPriceWithoutTax = parseFloat(totalPriceWithoutTax) + (parseFloat(pricewithoutTax) * qty);

                var perItemCost = parseFloat($(this).find(".menu_itemCost").text());
                var discountAmount = $(this).find(".menu_discount").val();
                var total = qty * perItemCost;

                if (discountFrom == 'P') {

                    var percentage = $(this).find(".menu_discount_percentage").val();
                    if (percentage > 100 || percentage < 0) {
                        $(this).find(".menu_discount_percentage").val(0);
                        $(this).find(".menu_discount_amount").val(0);
                    } else {
                        var discountedAmount = (percentage / 100) * total
                        $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(<?php echo $d ?>));

                        /** Tax Handling */
                        var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                        var discountedTax = (percentage / 100) * taxAmount;
                        $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                        totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);
                        /** Service Charge */
                        var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                        var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                        $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                        serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

                        /** PriceWithoutTax */
                        var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                        var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                        $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                        totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
                    }


                } else if (discountFrom == 'A') {

                    var discountedAmount = $(this).find(".menu_discount_amount").val();
                    var percentage = (discountedAmount * 100) / total;
                    if (percentage == 0) {
                        $(this).find(".menu_discount_percentage").val(percentage);
                    } else if (percentage > 100 || percentage < 0) {
                        //alert('Invalid discount amount ' + percentage);
                        $(this).find(".menu_discount_percentage").val(0);
                        $(this).find(".menu_discount_amount").val(0);
                        var discountedAmount = $(this).find(".menu_discount_amount").val();
                    } else {
                        $(this).find(".menu_discount_percentage").val(percentage.toFixed(2));
                        /** Tax Handling */
                        var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                        var discountedTax = (percentage / 100) * taxAmount;
                        $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                        totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);
                        /** Service Charge */
                        var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                        var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                        $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                        serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);
                        /** PriceWithoutTax */
                        var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                        var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                        $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                        totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
                    }

                } else {

                    var discountedAmount = $(this).find(".menu_discount_amount").val();
                    var percentage = $(this).find(".menu_discount_percentage").val();
                    var discountedAmount = (percentage / 100) * total
                    $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(<?php echo $d ?>));

                    /** Tax Handling */
                    var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                    var discountedTax = (percentage / 100) * taxAmount;
                    $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                    totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);

                    /** Service Charge */
                    var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                    var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                    $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                    serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

                    /** PriceWithoutTax */
                    var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                    var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                    $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                    totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
                }

                if (discountedAmount == undefined) {
                    discountedAmount = 0;
                }

                var netTotal = total - discountedAmount;
                $(this).find(".itemCostNet").text(netTotal.toFixed(<?php echo $d ?>));
                var sellingPrice = $(this).find(".itemCostNet").text();
                //netAmountTotal = parseFloat(netAmountTotal) + parseFloat(netAmount); // commented

                /** Policy based Amount */
                var policyBasedAmount = calculateTotalAmount(pricewithoutTax, tmpTax, tmpSC, qty, percentage);
                // var policyBasedAmount = calculateTotalAmount(totalPriceWithoutTaxDiscount, discountedTax, discountedServiceCharge, qty);
                netAmountTotal = parseFloat(netAmountTotal) + parseFloat(policyBasedAmount);

                var totalWithoutDiscount = qty * perItemCost;
                $(this).find(".menu_total").text(total.toFixed(<?php echo $d ?>));
                totalAmount = parseFloat(totalAmount) + parseFloat(total);
                totalQty = parseFloat(totalQty) + parseFloat(qty);
                grossTotal = parseFloat(grossTotal) + parseFloat(totalWithoutDiscount);
                totalDiscount = parseFloat(totalDiscount) + parseFloat(discountAmount);

            });

            var taxDiscountount = totalTax - totalTaxDiscount;
            var serviceChargeDiscountount = serviceCharge - serviceChargeDiscount;
            var priceWithoutTaxDiscountount = netAmountTotal - totalPriceWithoutTaxDiscount;

            //debugger;

            /** Total Tax */
            $("#display_totalTaxAmount").html(taxDiscountount.toFixed(<?php echo $d ?>));


            /**
             *  Service Charge only for Dine-in Customers
             *  only applied in Tax and SC separated tmpleate & SC separated template
             *
             *  Template
             *  2 - Tax & Service Charge Separated
             *  4 - Service Charge Separated
             *
             *  */

            var template = '<?php echo $templateID ?>';
            if (template == 2 || template == 4) {
                var dineIn = $("#is_dine_in").val();
                if (dineIn != 1) {
                    serviceChargeDiscountount = 0;
                    serviceCharge = 0;
                }
            }
            $("#display_totalServiceCharge").html(serviceChargeDiscountount.toFixed(<?php echo $d ?>));
            var netTotal = totalTax + serviceCharge + totalPriceWithoutTax;
            $("#total_item_qty").html(totalQty);
            $("#total_item_qty_input").val(totalQty);
            $("#final_purchased_item").html(totalQty);
            $("#gross_total").html(netAmountTotal.toFixed(<?php echo $d ?>));
            var tmpPriceWithoutTax = parseFloat($("#gross_total").text());
            var tmpTax = parseFloat($("#display_totalTaxAmount").text());
            var tmpSC = parseFloat($("#display_totalServiceCharge").text());
            var tmpGrossTotal = get_gross_amount(tmpPriceWithoutTax, tmpTax, tmpSC);
            $("#gross_total_amount_input").val(tmpGrossTotal);
            calculateFinalDiscount();
            calculateReturn();


            //calculating outlet tax
            var isOutletTaxEnabled =<?php echo $isOutletTaxEnabled; ?>;
            if (isOutletTaxEnabled) {
                var total_netAmount = parseFloat($("#total_netAmount").text());
                var outlet_tax = apply_outlet_tax_to_net_value(total_netAmount);
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                var outletTaxAmount = outlet_tax.calculated_tax_amount;
                $("#total_netAmount").text(netValueWithOutletTax.toFixed(<?php echo $d ?>));
                $("#final_payableNet_amt").html(netValueWithOutletTax.toFixed(<?php echo $d ?>));
                $("#gross_total_input").val(netValueWithOutletTax);
                $("#display_outlet_tax").text(outletTaxAmount.toFixed(<?php echo $d ?>));
                $("#netTotalAmount").val(netValueWithOutletTax.toFixed(<?php echo $d ?>));
            }

            var isOwnDelivery = $("#isOwnDelivery").val();
            if (isOwnDelivery == '1') {
                let total_netAmount = $("#total_netAmount").text();
                let ownDeliveryAmount = localStorage.getItem('ownDeliveryAmount');
                let total_amount_with_own_delivery = parseFloat(total_netAmount) + parseFloat(ownDeliveryAmount);

                $("#total_netAmount").text(total_amount_with_own_delivery.toFixed(<?php echo $d ?>));
                $("#netTotalAmount").val(total_amount_with_own_delivery.toFixed(<?php echo $d ?>));
            }


        }

        function apply_outlet_tax_to_net_value(total_netAmount) {

            var outlet_taxes = outlet_tax_list();
            var total_of_calculated_amounts = 0;
            outlet_taxes.forEach(function (item, index) {
                var calculated_amount = ((total_netAmount / 100) * item.taxPercentage).toFixed(<?php echo $d ?>);
                total_of_calculated_amounts += parseFloat(calculated_amount);
            });
            total_netAmount = total_netAmount + total_of_calculated_amounts;

            return {updated_net_value: total_netAmount, calculated_tax_amount: total_of_calculated_amounts};
        }

        function outlet_tax_list() {
            var taxes = null;
            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_config/outlet_tax_list'); ?>",
                data: {},
                cache: false,
                success: function (data) {
                    taxes = data.taxes;
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
            return taxes;
        }

        function get_gross_amount(priceWithoutTax, totalTax, serviceCharge) {
            var templateID = ('<?php echo $templateID ?>');
            templateID = parseInt(templateID);
            var grossAmount = 0;
            switch (templateID) {
                case 1:
                    /*All Inclusive*/
                    grossAmount = priceWithoutTax;

                    break;
                case 2:
                    /*Tax & Service Charge Separated*/
                    grossAmount = priceWithoutTax + totalTax + serviceCharge;

                    break;
                case 3:
                    /*Tax Separated*/
                    grossAmount = priceWithoutTax + totalTax;
                    break;
                case 4:
                    /*Service Charge Separated*/
                    grossAmount = priceWithoutTax + serviceCharge;
                    break;
            }
            return grossAmount;
        }

        function checkPosSessionSubmitted(id) {
            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/checkPosSession'); ?>",
                data: {id: id},
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 1) {
                    }
                    if (data['error'] == 0) {


                        var menusalesmaster = load_menusalesmaster_data(id);
                        $("#customerType").val(menusalesmaster.customerTypeID);
                        $("#netTotalAmountUpdate").val(menusalesmaster.netTotal);
                        $("#isDeliveryUpdate").val(menusalesmaster.isDelivery);

                        $("#final_payable_amtUpdate").text(menusalesmaster.grossAmount - menusalesmaster.discountAmount);
                        $("#total_payable_amtUpdate").val(menusalesmaster.grossAmount);

                        $("#promotional_discountUpdate").val(menusalesmaster.promotionDiscountAmount);

                        $("#final_payableNet_amtUpdate").text(menusalesmaster.grossTotal);
                        addPromotion_update(menusalesmaster.promotionID);
                        load_payments_list(id);

                        $("#paidUpdate").val(menusalesmaster.cashReceivedAmount);
                        $("#return_changeUpdate").text(menusalesmaster.balanceAmount);


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
            return false;
        }

        function load_payments_list(menusalesID) {
            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/load_payments_list'); ?>",
                data: {menusalesID: menusalesID},
                cache: false,
                success: function (data) {
                    data.forEach(function (currentValue, index, arr) {
                        var paymentConfigDetailID = currentValue.paymentConfigDetailID;
                        var amount = currentValue.amount;
                        var input_field_name = "paymentTypesUpdate[" + paymentConfigDetailID + "]";
                        var input_selector = "input[name='" + input_field_name + "']";
                        $(input_selector).val(amount);
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
        }

        function load_menusalesmaster_data(id) {
            app.data = null;
            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/load_menusalesmaster_data'); ?>",
                data: {id: id},
                cache: false,
                success: function (data) {
                    app.data = data;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
            return app.data;
        }

        function checkPosSession(id) {
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/checkPosSession'); ?>",
                data: {id: id},
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 1) {
                    }
                    if (data['error'] == 0) {

                        if (data['advancePayment'] > 0) {
                            $("#delivery_advancePaymentAmount").val(data['advancePayment']);
                            var advancePayment = parseFloat(data['advancePayment']);
                            $("#delivery_advancePaymentAmountShow").html(advancePayment.toFixed(<?php echo $d ?>));
                        }

                        if (data['deliveryRevenueGLID'] != null) {//deliveryRevenueGLID is cheking to identify whether it is own delivery or not.
                            $("#isOwnDelivery").val(1);
                            localStorage.setItem('ownDeliveryAmount', data['ownDeliveryAmount']);//store to access in next function.
                        } else {
                            $("#isOwnDelivery").val(0);
                        }
                        $("#is_dine_in").val(data['dine_in']);
                        $("#dis_amt").val(data['discountPer']);
                        $("#serviceCharge").val(data['serviceCharge']);
                        Load_pos_holdInvoiceData(data['code'], data['master']['wareHouseAutoID']);
                        $("#holdInvoiceID_input").val(data['code']);
                        $("#holdOutletID_input").val(data['master']['wareHouseAutoID']);
                        $("#holdInvoiceID").val(data['code']);
                        $("#holdInvoiceID_codeTmp").val(data['master']['invoiceCode']);
                        $("#customerType").val(data['customerTypeID']);
                        $(".customerType").removeClass('btn-primary');
                        $(".customerType").addClass('btn-default');
                        $("#customerTypeID_" + data['customerTypeID']).removeClass('btn-default');
                        $("#customerTypeID_" + data['customerTypeID']).addClass('btn-primary');

                        var deliveryType = $("#customerTypeID_" + data['customerTypeID']).data('val');
                        if (deliveryType !== undefined) {
                            if (deliveryType.trim() == "Delivery Orders") {
                                $(".deliveryRow").show();
                                $(".deliveryPromotionRow").show();

                                if ($("#owdAllowed").val() == 1) {//Own delivery section show/hide for delivery order.
                                    $("#own_delivery_div").show();
                                } else {
                                    $("#own_delivery_div").hide();//Own delivery section hide initially.
                                }
                            }else{
                                $("#own_delivery_div").hide();//Own delivery section hide initially.
                            }
                        }

                        var customerDescription = data['customerDescription'];
                        if (customerDescription.trim() == "Delivery Orders") {
                            $("#isDelivery").val(1);
                        }

                        $("#current_table_description").text('Table');

                        terminalGlobalVariables.selectedWaiter = data['waiterID'];

                        //$("#customerTypeID_" + data['customerTypeID']).click()
                        selectCustomerButton(data['customerTypeID']);
                        if (parseInt(data['master']['isOrderPending'])) { /*check order is pending and change the send to kitchen button color*/
                            $('#btn_pos_sendtokitchen').removeClass('btn-danger');
                            $('#btn_pos_sendtokitchen').addClass('btn-success');
                            //load_KOT_print_view(data['menuSalesID']);
                        }
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
            return false;
        }

        function Load_pos_holdInvoiceData(invoiceID, outletID) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/Load_pos_holdInvoiceData_withDiscount'); ?>",
                data: {invoiceID: invoiceID, template: 2, outletID: outletID},
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    $("#log").html(data);
                    calculateFooter();
                    initNumPad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
            return false;
        }

        function clearInvoice() {
            $("#log").html('');
        }

        function beforeDeleteItem(id) {
            var tmpIsSamplePrinted = $("#isSamplePrinted_" + id).val();
            if (tmpIsSamplePrinted == 0) {
                deleteDiv(id)
            } else {
                checkPosAuthentication(13, id)
            }
        }

        function deleteDiv(id) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/delete_menuSalesItem'); ?>",
                data: {id: id, outletID: $("#holdOutletID_input").val()},
                cache: false,
                beforeSend: function () {
                    numberOfRequest++;
                    disable_POS_btnSet();
                    startLoadPos();
                },
                success: function (data) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    stopLoad();
                    if (data['error'] == 0) {
                        if (data.add_on.length > 0) {
                            $.each(data.add_on, function (key, value) {
                                $("#item_row_" + value.menuSalesItemID).remove();
                            });
                        }
                        $("#item_row_" + id).remove();
                        calculateFooter();
                    } else if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                    focus_barcode();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
            return false;
        }

        function clearPosInvoiceSession() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/clearPosInvoiceSession'); ?>",
                data: {id: null},
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 0) {
                        resetKotButton();
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
            return false;
        }

        function NewBill() {
            bootbox.confirm('<?php echo createNewInvoiceConfirmation() ?>', function (result) {
                if (result) {
                    clearInvoice();
                    clearPosInvoiceSession();
                    $("#pos_salesInvoiceID_btn").html('<span class="label label-danger"><?php echo $this->lang->line('common_new');?></span>');
                    $("#pos_orderNo").val('New');
                    <!--New-->
                    $("#holdInvoiceID_input").val('0');
                    $("#holdInvoiceID").val('0');
                    calculateFooter();
                    $("#paid").val(0);
                }
            });

        }

        function clearSalesInvoiceUpdate() {
            setTimeout(function () {
                clearInvoice();
                clearPosInvoiceSession();
                $("#pos_salesInvoiceID_btnUpdate").html('<span class="label label-danger"><?php echo $this->lang->line('common_new');?></span>');
                $("#pos_orderNoUpdate").val('New');
                <!--New-->
                $("#holdInvoiceID_inputUpdate").val('0');
                $("#holdInvoiceIDUpdate").val('0');
                calculateFooter();
                $("#paidUpdate").val(0);
                $("#promotionIDUpdate").val('');
                $("#dis_amtUpdate").val(0);
                $(".paymentInput ").val('');
                resetPaymentForm();
                resetPayTypeBtn();
            }, 500);
        }

        function clearSalesInvoice() {
            setTimeout(function () {
                clearInvoice();
                clearPosInvoiceSession();
                $("#pos_salesInvoiceID_btn").html('<span class="label label-danger"><?php echo $this->lang->line('common_new');?></span>');
                $("#pos_orderNo").val('New');
                <!--New-->
                $("#holdInvoiceID_input").val('0');
                $("#holdInvoiceID").val('0');
                calculateFooter();
                $("#paid").val(0);
                $("#promotionID").val('');
                $("#dis_amt").val(0);
                $(".paymentInput ").val('');
                resetPaymentForm();
                resetPayTypeBtn();
                var pba = <?php echo $pinBasedAccess ? $pinBasedAccess : 0; ?>;
                if (pba == 0) {
                    terminalGlobalVariables.selectedWaiter = null;
                    $(".waiterBtn").removeClass('btn-primary');//change waiter buttons to default color.
                }
            }, 500);
        }

        function cancelCurrentOrder() {
            bootbox.confirm('<?php echo cancelOrderConfirmation() ?>', function (result) {
                if (result) {

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('Pos_restaurant/cancelCurrentOrder'); ?>",
                        data: {id: null},
                        cache: false,
                        beforeSend: function () {
                            startLoadPos();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 0) {
                                myAlert('s', data['message']);
                                clearSalesInvoice();
                                resetKotButton();

                                setTimeout(function () {
                                    reset_delivery_order();
                                }, 500);

                            } else {
                                myAlert('d', data['message']);
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
                }
            });
        }

        function calculateFinalDiscount() {
            var disPercentage = $("#dis_amt").val();
            if (disPercentage <= 100 && disPercentage >= 0) {
                /** new Logic */
                var templateID = '<?php echo $templateID ?>';
                var grossTotal = 0;
                var totalAmount = parseFloat($("#gross_total").text());
                var totalTax = parseFloat($("#display_totalTaxAmount").text());
                var totalServiceCharge = parseFloat($("#display_totalServiceCharge").text());
                switch (parseInt(templateID)) {
                    case 1:
                        grossTotal = totalAmount;
                        break;
                    case 2:
                        grossTotal = totalAmount + totalTax + totalServiceCharge;
                        break;
                    case 3:
                        grossTotal = totalAmount + totalTax;
                        break;
                    case 4:
                        grossTotal = totalAmount + totalServiceCharge;
                        break;
                    default:
                        grossTotal = totalAmount;
                }
                var discountPercentage = $("#dis_amt").val();
                var discountAmount = (discountPercentage / 100) * grossTotal;
                $("#total_discount_amount").val(discountAmount);
                $("#total_discount").html('(' + discountAmount.toFixed(<?php echo $d ?>) + ')');
                $("#discountAmountFooter").val(discountAmount.toFixed(<?php echo $d ?>));
                var netTotal = grossTotal - discountAmount;

                $("#total_netAmount").html(netTotal.toFixed(<?php echo $d ?>)); //output Net Amount
                $("#final_payable_amt").html(netTotal.toFixed(<?php echo $d ?>)); //output Net Amount
                $("#gross_total_input").val(netTotal);

            } else {
                $("#dis_amt").val(0);
                $("#total_discount_amount").val(0);
                $("#discountAmountFooter").val('');
            }
        }

        app = {};
        app.submit_mode = null;

        function update_pos_submitted_payments() {
            //debugger;
            validateBalanceAmountUpdate();
        }

        function submit_pos_payments() {
            <?php
            if (isset($isHidePrintPreview) && $isHidePrintPreview) {
                echo "app.submit_mode = 'submit_and_send_to_printer';";
            } else {
                echo "app.submit_mode = 'submit_and_print';";
            }
            ?>
            $("#customerNameTmp").val('');
            $("#customerTelephoneTmp").val('');
            $("#customerAddressTmp").val('');
            $("#customerIDTmp").val('');
            $("#customerEmailTmp").val('');
            setDeliveryInfo();
            var isDelivery = $("#isDelivery").val();
            var deliveryPersonID = $("#deliveryPersonID").val();
            if (isDelivery == 1) {
                if (deliveryPersonID > 0 || deliveryPersonID == -1) {
                    validateBalanceAmount();
                } else {
                    /*myAlert('e', 'Please select Delivery person before submit person.')
                    return false;*/
                    validateBalanceAmount();
                }
            } else {
                validateBalanceAmount();
            }
        }

        function submit_and_close_pos_payments() {
            app.submit_mode = 'submit_and_close';
            $("#customerNameTmp").val('');
            $("#customerTelephoneTmp").val('');
            $("#customerAddressTmp").val('');
            $("#customerIDTmp").val('');
            $("#customerEmailTmp").val('');
            setDeliveryInfo();
            var isDelivery = $("#isDelivery").val();
            var deliveryPersonID = $("#deliveryPersonID").val();
            if (isDelivery == 1) {
                if (deliveryPersonID > 0 || deliveryPersonID == -1) {
                    validateBalanceAmount();
                } else {
                    /*myAlert('e', 'Please select Delivery person before submit person.')
                    return false;*/
                    validateBalanceAmount();
                }
            } else {
                validateBalanceAmount();
            }
        }

        function validateBalanceAmountUpdate() {
            var isDelivery = $("#isDeliveryUpdate").val();
            var isOnTimePayment = $("#deliveryPersonIDUpdate option:selected").data('otp');
            var deliveryOrder = $("#deliveryOrderIDUpdate").val();
            var deliveryInfo = $('#is_delivery_info_exist').val();
            if (deliveryOrder > 0) {
                var returnChange = parseFloat($("#return_changeUpdate").text());
            } else if (isDelivery == 1 && isOnTimePayment == 1) {
                /** Delivery and on time payment */
                var returnChange = $("#returned_change_toDeliveryUpdate").val();
            } else {
                var returnChange = parseFloat($("#return_changeUpdate").text());
            }

            if (returnChange < 0) {
                var pydamnt = $("#paidUpdate").val();
                var crdttamnt = $("#cardTotalAmountUpdate").val();
                if (<?php echo $roundoffPolicy ?>==1
            )
                {
                    if ($(".paymentInputupdate").hasClass("rundoff")) {
                        bootbox.confirm({
                            message: '<div class="alert alert-warning" style="color: #293225 !important; background-color: #ffe8c3 !important;;"><strong>Warning</strong><br/><br/><span style="font-size:18px;"> Under payment of <span style="color:red;font-weight:700">' + returnChange + ' <?php echo $companyInfo['company_default_currency'] ?></span></span><br/><br/>Do you want to round off the balance amount.</div>',
                            closeButton: false,
                            buttons: {
                                confirm: {
                                    label: 'Cancel',
                                    className: 'btn-danger btn-lg'
                                },
                                cancel: {
                                    label: 'Pay With Round Off',
                                    className: 'btn-success btn-lg'
                                }
                            },
                            callback: function (result) {
                                if (result == false) {
                                    $(".rundoff").val(parseFloat(Math.abs(returnChange)));
                                    paydamnt = parseFloat(Math.abs(returnChange)) + parseFloat(pydamnt);
                                    crdttamnt = parseFloat(Math.abs(returnChange)) + parseFloat(crdttamnt);
                                    $("#paidUpdate").val(paydamnt);
                                    $("#paid_tempUpdate").html(paydamnt);
                                    $("#cardTotalAmountUpdate").val(crdttamnt);
                                    $("#return_changeUpdate").html('0.00');
                                    updateBill();
                                } else {

                                    if (isDelivery == 1) {
                                        if (deliveryInfo != "0") {
                                            updateBill();
                                        } else {
                                            bootbox.alert('Payment amount should be paid to submit the payment');
                                        }
                                    }
                                }
                            }
                        });

                    } else {
                        if (isDelivery == 1) {
                            if (deliveryInfo != "0") {
                                updateBill();
                            } else {
                                bootbox.alert('Payment amount should be paid to submit the payment');
                            }
                        } else {
                            bootbox.alert('Round Off Payment Type Does not exsist');
                        }

                    }
                }
            else
                {
                    if (isDelivery == 1) {
                        if (deliveryInfo != "0") {
                            updateBill();
                        } else {
                            bootbox.alert('Payment amount should be paid to submit the payment');
                        }
                    } else {
                        bootbox.alert('<div class="alert alert-warning" style="color: #293225 !important; background-color: #ffe8c3 !important;;"><strong>Warning</strong><br/><br/><span style="font-size:18px;"> Under payment of <span style="color:red;font-weight:700">' + returnChange + ' <?php echo $companyInfo['company_default_currency'] ?></span></span><br/><br/>Please enter the exact bill amount and submit again.</div>');
                    }

                }
            } else {
                updateBill();
            }
            modalFix();
        }

        function validateBalanceAmount() {
            var isDelivery = $("#isDelivery").val();
            var isOnTimePayment = $("#deliveryPersonID option:selected").data('otp');
            var deliveryOrder = $("#deliveryOrderID").val();
            var deliveryInfo = $('#is_delivery_info_exist').val();
            let isOwnDelivery = $("#isOwnDelivery").val();
            let own_delivery_person = $("#own_delivery_person").val();

            // if (isOwnDelivery == 1) {
            //     if (own_delivery_person == "") {
            //         myAlert('e', 'Delivery person is required.');
            //         return;
            //     }
            // }

            if (deliveryOrder > 0) {
                var returnChange = parseFloat($("#return_change").text());
            } else if (isDelivery == 1 && isOnTimePayment == 1) {
                /** Delivery and on time payment */
                var returnChange = $("#returned_change_toDelivery").val();
            } else {
                var returnChange = parseFloat($("#return_change").text());
            }

            if (isOwnDelivery == 1) {
                var returnChange = parseFloat($("#return_change").text()) - parseFloat($("#own_delivery_amount").val());
            }


            if (returnChange < 0) {

                if (<?php echo json_encode($isPartialPaymentEnabled); ?>) {
                    saveBill();
                } else {
                    var pydamnt = $("#paid").val();
                    var crdttamnt = $("#cardTotalAmount").val();
                    if (<?php echo $roundoffPolicy ?>==1
                )
                    {
                        if ($(".paymentInput").hasClass("rundoff")) {
                            bootbox.confirm({
                                message: '<div class="alert alert-warning" style="color: #293225 !important; background-color: #ffe8c3 !important;;"><strong>Warning</strong><br/><br/><span style="font-size:18px;"> Under payment of <span style="color:red;font-weight:700">' + returnChange + ' <?php echo $companyInfo['company_default_currency'] ?></span></span><br/><br/>Do you want to round off the balance amount.</div>',
                                closeButton: false,
                                buttons: {
                                    confirm: {
                                        label: 'Cancel',
                                        className: 'btn-danger btn-lg'
                                    },
                                    cancel: {
                                        label: 'Pay With Round Off',
                                        className: 'btn-success btn-lg'
                                    }
                                },
                                callback: function (result) {
                                    if (result == false) {
                                        $(".rundoff").val(parseFloat(Math.abs(returnChange)));
                                        paydamnt = parseFloat(Math.abs(returnChange)) + parseFloat(pydamnt);
                                        crdttamnt = parseFloat(Math.abs(returnChange)) + parseFloat(crdttamnt);
                                        $("#paid").val(paydamnt);
                                        $("#paid_temp").html(paydamnt);
                                        $("#cardTotalAmount").val(crdttamnt);
                                        $("#return_change").html('0.00');
                                        saveBill()
                                    } else {

                                        if (isDelivery == 1) {
                                            if (deliveryInfo != "0") {
                                                saveBill();
                                            } else {
                                                bootbox.alert('Payment amount should be paid to submit the payment');
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            if (isDelivery == 1) {
                                if (deliveryInfo != "0") {
                                    saveBill();
                                } else {
                                    bootbox.alert('Payment amount should be paid to submit the payment');
                                }
                            } else {
                                bootbox.alert('Round Off Payment Type Does not exsist');
                            }
                        }
                    }
                else
                    {
                        if (isDelivery == 1) {
                            if (deliveryInfo != "0") {
                                saveBill();
                            } else {
                                bootbox.alert('Payment amount should be paid to submit the payment');
                            }
                        } else {
                            bootbox.alert('<div class="alert alert-warning" style="color: #293225 !important; background-color: #ffe8c3 !important;;"><strong>Warning</strong><br/><br/><span style="font-size:18px;"> Under payment of <span style="color:red;font-weight:700">' + returnChange + ' <?php echo $companyInfo['company_default_currency'] ?></span></span><br/><br/>Please enter the exact bill amount and submit again.</div>');
                        }

                    }
                }

            } else {
                saveBill();
            }
            modalFix();
        }

        function updateBill() {
            var formData = $(".form_pos_receipt_update").serializeArray();
            var date = new Date,
                hour = date.getHours(),
                minute = date.getMinutes(),
                seconds = date.getSeconds(),
                minute = minute > 9 ? minute : "0" + minute;
            seconds = seconds > 9 ? seconds : "0" + seconds;
            hour = hour > 9 ? hour : "0" + hour;

            date = hour + ":" + minute + ":" + seconds;

            var dates = new Date();
            var hours = dates.getHours();
            var dts = dates.getFullYear() + '-' + dates.getMonth() + '-' + dates.getDate();
            var minutes = dates.getMinutes();
            var strTime = dts + " " + hours + ':' + minutes + ':' + dates.getSeconds();

            formData.push({'name': 'currentTime', 'value': date});
            formData.push({'name': 'KOTStartDateTime', 'value': strTime});

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/update_pos_submitted_payments'); ?>",
                data: formData,
                cache: false,
                beforeSend: function () {
                    startLoad();
                    // $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> <?php echo $this->lang->line('common_submit');?>');

                    // $("#submit_btn").prop('disabled', true);
                },
                success: function (data) {
                    stopLoad();
                    clearSalesInvoiceUpdate();
                    $("#pos_submitted_payments_modal").modal('hide');
                    restaurant_doubleEntry_for_billUpdate(data['invoiceID']);
                    $(".paymentInputupdate").val('');
                    if (data['error'] == 0) {
                        myAlert('s', data['message'])
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false);
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
        }

        function saveBill() {

            var formData = $(".form_pos_receipt").serializeArray();
            var date = new Date,
                hour = date.getHours(),
                minute = date.getMinutes(),
                seconds = date.getSeconds(),
                minute = minute > 9 ? minute : "0" + minute;
            seconds = seconds > 9 ? seconds : "0" + seconds;
            hour = hour > 9 ? hour : "0" + hour;

            date = hour + ":" + minute + ":" + seconds;

            var dates = new Date();
            var hours = dates.getHours();
            var dts = dates.getFullYear() + '-' + dates.getMonth() + '-' + dates.getDate();
            var minutes = dates.getMinutes();
            var strTime = dts + " " + hours + ':' + minutes + ':' + dates.getSeconds();

            formData.push({'name': 'currentTime', 'value': date});
            formData.push({'name': 'KOTStartDateTime', 'value': strTime});
            formData.push({'name': 'selectedWaiter', 'value': terminalGlobalVariables.selectedWaiter});

            //own delivery details
            let own_delivery_setup = $("#own_delivery_setup").val();
            let own_delivery_person = $("#own_delivery_person").val();
            let own_delivery_percentage = $("#own_delivery_percentage").val();
            let own_delivery_amount = $("#own_delivery_amount").val();
            let isOwnDelivery = $("#isOwnDelivery").val();
            let revGLID = $("#revGLID").val();
            formData.push({'name': 'own_delivery_setup', 'value': own_delivery_setup});
            formData.push({'name': 'own_delivery_person', 'value': own_delivery_person});
            formData.push({'name': 'own_delivery_percentage', 'value': own_delivery_percentage});
            formData.push({'name': 'own_delivery_amount', 'value': own_delivery_amount});
            formData.push({'name': 'isOwnDelivery', 'value': isOwnDelivery});
            formData.push({'name': 'revGLID', 'value': revGLID});

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/submit_pos_payments'); ?>",
                data: formData,
                cache: false,
                beforeSend: function () {
                    startLoad();
                    $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> <?php echo $this->lang->line('common_submit');?>');
                    <!--Submit-->
                    $("#submit_btn").prop('disabled', true);
                },
                success: function (data) {
                    stopLoad();
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false); // Please comment it later
                    $("#backToCategoryBtn").click();
                    if (data['error'] == 0) {
                        //myAlert('s', data['message']);
                        if (app.submit_mode == 'submit_and_print') {
                            loadPrintTemplate(data['invoiceID'], data['outletID']);
                        }
                        if (app.submit_mode == 'submit_and_send_to_printer') {
                            loadPrintTemplate(data['invoiceID']);
                            print_paymentReceipt();
                            $("#pos_sampleBill").hide();
                            $("#rpos_print_template").hide();
                        }
                        $("#email_invoiceID").val(data['invoiceID']);
                        resetKotButton();
                        clearCreditSales();
                        resetGiftCardForm();
                        clearPromotion();
                        $("#deliveryDateDiv").hide();
                        resetPaymentForm();
                        reset_delivery_order();
                        clear_own_delivery_after_submit();
                        $(".waiterBtn").removeClass('btn-primary');
                        restaurant_doubleEntry_for_bill(data['invoiceID']);
                        reset_loyalty_modal();
                        var pba = <?php echo $pinBasedAccess ? $pinBasedAccess : 0; ?>;
                        if (pba == 0) {
                            terminalGlobalVariables.selectedWaiter = null;
                        }
                        if (app.submit_mode == 'submit_and_close') {
                            clearSalesInvoice();
                            $("#pos_payments_modal").modal('hide');
                            if (<?php echo $smsAllowed ?> == 1
                        )
                            {
                                send_rpos_submit_sms_chk_cus(data['invoiceID'], data['outletID']);
                            }
                        }

                    } else if (data['error'] == 2) {
                        bootbox.alert('<div class="alert alert-success"> This bill has already saved, you can open this under closed bills. <br/><br/> <strong>Page is refreshing <i class="fa fa-refresh fa-spin" ></i></strong></div>');
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    } else {
                        myAlert('d', data['message']);
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false);
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
        }

        function reset_loyalty_modal() {
            $("#barcode").val("");
            $("#loyaltyCustomerTelephone").val("");
            $("#gc_CustomerName").val("");
            $("#loyalty_balance").val("");
            $("#bill_amount").val("");
            $("#redeem").val("");
            $("#customer_loyalty_card_number").val("");
            $("#customer_loyalty_balance").val("");
            $("#customer_loyalty_earned").val("");
            $("#customer_loyalty_details_div").hide();
        }

        function restaurant_doubleEntry_for_billUpdate(invoiceID) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/restaurant_doubleEntry_for_billUpdate'); ?>",
                data: {invoiceID: invoiceID},
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            })
        }

        function restaurant_doubleEntry_for_bill(invoiceID) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/restaurant_doubleEntry_for_bill'); ?>",
                data: {invoiceID: invoiceID},
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            })
        }

        function print_pos_report() {
            $.print("#print_content");
            return false;
        }

        function prepareforDoubleEntry(warehouseID, counterID) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/prepareforDoubleEntry'); ?>",
                data: $(".form_pos_receipt").serialize(),
                cache: false,
                beforeSend: function () {
                    $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> <?php echo $this->lang->line('common_submit');?>');
                    <!--Submit-->
                    $("#submit_btn").prop('disabled', true);

                },
                success: function (data) {
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false); // Please comment it later

                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
                        loadPrintTemplate();
                    } else {
                        myAlert('d', data['message']);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false);
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            })

        }

        function holdAndCreateNewBill() {
            /** when submit */
            $("#submit_btn_pos_receipt").html('Submit and Print');
            $("#submit_btn").prop('disabled', false); // Please comment it later
            $("#backToCategoryBtn").click();
            resetKotButton();
            clearCreditSales();
            resetGiftCardForm();
            resetPaymentForm();
            /** when hidden*/
            $("#dis_amt").val(0);
            $("#cardTotalAmount").val(0);
            $("#netTotalAmount").val(0);
            $("#serviceCharge").val('<?php echo get_defaultServiceCharge() ?>');
            $("#pos_payments_modal").modal('hide');
            clearSalesInvoice();
            resetPayTypeBtn();
        }

        function confirm_createNewBill() {
            bootbox.confirm('<div class="alert alert-info"><strong> <i class="fa fa-check-circle fa-2x"></i> Sent to Kitchen successfully</string><br/><br/><br/> Do you want to create a new order?</div>', function (result) {
                if (result) {
                    holdAndCreateNewBill()
                }
            });
        }

        function get_currentTime() {
            var date = new Date();
            var nHour = date.getHours(), nMin = date.getMinutes(), nSec = date.getSeconds(), ap;

            if (nHour == 0) {
                ap = " AM";
                nHour = 12;
            } else if (nHour < 12) {
                ap = " AM";
            } else if (nHour == 12) {
                ap = "PM";
            } else if (nHour > 12) {
                ap = "PM";
                nHour -= 12;
            }

            if (nMin <= 9) {
                nMin = "0" + nMin;
            }
            if (nSec <= 9) {
                nSec = "0" + nSec;
            }

            var output = nHour + ":" + nMin + " " + ap;
            return output;
        }

        function updaterestaurantTable(tmp) {
            var tableType = tmp.value;

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/updaterestaurantTable'); ?>",
                data: {tableType: tableType},
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
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

            return false;
        }

        function num_padReturnCal() {
            setTimeout(function () {
                calculateReturn();
            }, 200);
        }

        function loadCountryDetail() {
            $("#country-select").empty();

            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Pos_customerMaster/loadCountryDetail"); ?>',
                dataType: 'json',
                data: '',
                async: false,
                success: function (data) {
                    $("#country-select").append(data);
                    $("#customerCountry").select2();
                }
            });
        }

        function setDeliveryInfo() {

            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Pos_restaurant/get_delivery_order_by_invoice_id"); ?>',
                dataType: 'json',
                data: '',
                async: false,
                success: function (data) {
                    if (data.isExistDeliveryInfo == 1) {
                        $('#is_delivery_info_exist').val(1);
                    } else {
                        $('#is_delivery_info_exist').val(0);
                    }
                }
            });
        }

        function load_woowfood_count() {
            //wowfood_orders_count
            $("#wowfood_count").hide();
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/wowfood_orders_count'); ?>",
                data: {},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data.is_wowfood_enabled == true) {
                        $("#wowfood_count").show();
                        $("#wowfood_count").html(data.count);
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
        }

        function popupWaitersList() {
            if (terminalGlobalVariables.selectedWaiter != null) {
                $("div").find("[data-emp_id='" + terminalGlobalVariables.selectedWaiter + "']").addClass('btn-primary');//marking selected waiter.
            }
            $("#waitersListModal").modal('show');
        }

        function markThisWaiterAsSelected(crewMemberID, crewFirstName) {

            var menuSalesId = $("#holdInvoiceID").val();
            terminalGlobalVariables.selectedWaiter = crewMemberID;
            terminalGlobalVariables.waiterName = crewFirstName;
            if (menuSalesId != "") {
                var ajaxStatus = null;
                $.ajax({
                    async: false,
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Pos_restaurant/save_waiter_id'); ?>",
                    data: {menuSalesId: menuSalesId, waiterId: terminalGlobalVariables.selectedWaiter},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data.status == 'updated') {
                            ajaxStatus = 1;
                            $(".nmpd-display").attr('type', 'text');
                            $("#screenLockOverlay").css('display', 'none');
                            $(".main-header").show();
                            myAlert('s', 'Waiter Assigned.');
                        } else {
                            ajaxStatus = 0;
                            myAlert('e', 'Error.');
                        }
                    }, error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        ajaxStatus = 0;
                        if (jqXHR.status == false) {
                            myAlert('w', 'No Internet, Please try again');
                        } else {
                            myAlert('e', '<br>Message: ' + errorThrown);
                        }
                    }
                });
                if (ajaxStatus == 1) {
                    $(".waiterBtn").removeClass('btn-primary');
                    $("div").find("[data-emp_id='" + terminalGlobalVariables.selectedWaiter + "']").addClass('btn-primary');//marking similar button in other dialogs.
                    $("#waitersListModal").modal('hide');
                } else {
                    $("#waitersListModal").modal('hide');
                }
            } else {
                $(".nmpd-display").attr('type', 'text');
                $("#screenLockOverlay").css('display', 'none');
                $(".main-header").show();
                myAlert('s', 'Waiter Assigned.');

                $(".waiterBtn").removeClass('btn-primary');
                $("div").find("[data-emp_id='" + terminalGlobalVariables.selectedWaiter + "']").addClass('btn-primary');//marking similar button in other dialogs.
                $("#waitersListModal").modal('hide');
            }

        }

        function markThisWaiterAsSelectedFromTerminal() {

            var pba = <?php echo $pinBasedAccess ? $pinBasedAccess : 0; ?>;
            if (pba == 1) {
                myAlert('w', 'This feature is disabled due to PIN based access enabled.');
            } else {
                var gross_total = parseFloat($("#gross_total").html());
                if (gross_total > 0) {
                    var menuSalesId = $("#holdInvoiceID").val();
                    terminalGlobalVariables.selectedWaiter = $(this).data('emp_id');
                    terminalGlobalVariables.waiterName = $(this).val();
                    var ajaxStatus = null;
                    $.ajax({
                        async: false,
                        type: 'POST',
                        dataType: 'JSON',
                        url: "<?php echo site_url('Pos_restaurant/save_waiter_id'); ?>",
                        data: {menuSalesId: menuSalesId, waiterId: terminalGlobalVariables.selectedWaiter},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data.status == 'updated') {
                                ajaxStatus = 1;
                                myAlert('s', 'Waiter Assigned.');
                            } else {
                                ajaxStatus = 0;
                                myAlert('e', 'Error.');
                            }
                        }, error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            ajaxStatus = 0;
                            if (jqXHR.status == false) {
                                myAlert('w', 'No Internet, Please try again');
                            } else {
                                myAlert('e', '<br>Message: ' + errorThrown);
                            }
                        }
                    });

                    if (ajaxStatus == 1) {
                        //$('#holdReference').val(terminalGlobalVariables.waiterName);
                        $(".waiterBtn").removeClass('btn-primary');
                        $(this).addClass('btn-primary');

                        $("div").find("[data-emp_id='" + terminalGlobalVariables.selectedWaiter + "']").addClass('btn-primary');//marking similar button in other dialogs.

                        $("#waitersListModal").modal('hide');
                    } else {
                        $("#waitersListModal").modal('hide');
                    }

                } else {
                    bootbox.alert('<div class="alert alert-info"><strong><?php echo $this->lang->line('posr_no_menus_added_to_invoice_please_add_at_least_one_item');?>.</strong></div>');
                }
            }

        }

        function markThisWaiterAsSelectedOrderModel() {
            var pba = <?php echo $pinBasedAccess ? $pinBasedAccess : 0; ?>;
            if (pba == 1) {
                myAlert('w', 'This feature is disabled due to PIN based access enabled.');
                var customerType = $("#customerType").val();
                if (customerType == "") {

                } else {
                    $('#order_mode_modal').modal('hide');
                    open_pos_payments_modal();
                }
            } else {
                var gross_total = parseFloat($("#gross_total").html());
                if (gross_total > 0) {
                    var menuSalesId = $("#holdInvoiceID").val();
                    terminalGlobalVariables.selectedWaiter = $(this).data('emp_id');
                    terminalGlobalVariables.waiterName = $(this).val();
                    var ajaxStatus = null;
                    $.ajax({
                        async: false,
                        type: 'POST',
                        dataType: 'JSON',
                        url: "<?php echo site_url('Pos_restaurant/save_waiter_id'); ?>",
                        data: {menuSalesId: menuSalesId, waiterId: terminalGlobalVariables.selectedWaiter},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data.status == 'updated') {
                                ajaxStatus = 1;
                                myAlert('s', 'Waiter Assigned.');
                                var customerType = $("#customerType").val();
                                if (customerType == "") {

                                } else {
                                    $('#order_mode_modal').modal('hide');
                                    open_pos_payments_modal();
                                }
                            } else {
                                ajaxStatus = 0;
                                myAlert('e', 'Error.');
                            }
                        }, error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            ajaxStatus = 0;
                            if (jqXHR.status == false) {
                                myAlert('w', 'No Internet, Please try again');
                            } else {
                                myAlert('e', '<br>Message: ' + errorThrown);
                            }
                        }
                    });

                    if (ajaxStatus == 1) {
                        //$('#holdReference').val(terminalGlobalVariables.waiterName);
                        $(".waiterBtn").removeClass('btn-primary');
                        $(this).addClass('btn-primary');

                        $("div").find("[data-emp_id='" + terminalGlobalVariables.selectedWaiter + "']").addClass('btn-primary');//marking similar button in other dialogs.

                        $("#waitersListModal").modal('hide');
                    } else {
                        $("#waitersListModal").modal('hide');
                    }

                } else {
                    bootbox.alert('<div class="alert alert-info"><strong><?php echo $this->lang->line('posr_no_menus_added_to_invoice_please_add_at_least_one_item');?>.</strong></div>');
                }
            }

        }

        function lockScreen() {
            terminalGlobalVariables.selectedWaiter = null;
            localStorage.setItem('screenLock', 'on');
            $(".nmpd-display").attr('type', 'password');
            $("#screenLockOverlay").css('display', 'block');
            $(".main-header").hide();
        }

        function unlockButtonClick() {
            var waiterPinInput = $("#waiterPinInput").val();
            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/check_waiter_pin'); ?>",
                data: {waiterPinInput: waiterPinInput},
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.status == 'success') {
                        localStorage.setItem('screenLock', 'off');
                        $("#waiterPinInput").val('');
                        markThisWaiterAsSelected(data.crewMemberID, data.crewFirstName);
                    } else {
                        myAlert('e', 'Login failed.');
                        $("#waiterPinInput").val('');
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    ajaxStatus = 0;
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });

        }

        function customNumpadClick(number) {
           var newValue = $("#waiterPinInput").val()+number;
            $("#waiterPinInput").val(newValue);
        }

        function customNumpadClear() {
            $("#waiterPinInput").val('');
        }

        function waiterPinInputOnKeyUp() {

            unlockButtonClick();
        }

        function keepTheExistingDiscountPercentage(){
            localStorage.setItem('oldDiscountPercentage',this.value);
        }

        function keepTheExistingDiscountAmount() {
            localStorage.setItem('oldDiscountAmount',this.value);
        }

        function keepTheExistingQuantity(){
            localStorage.setItem('oldQty',this.value);
        }

        function updateQtyWithAuth(id,event,wh_menuid) {

            
            var oldValue = localStorage.getItem('oldQty');
            var newValue = $('#qty_' + id).val();
            var qtyInputId = '#qty_' + id;
            localStorage.setItem('qtyInputId',qtyInputId);
            var QtyAddedInThisAttemps = newValue - oldValue;
           if(checkStockWhenQtyChange(wh_menuid,QtyAddedInThisAttemps)){           
           
            var isHold = checkBillIsHoldByUser();
            if(event=='onkeyup'){
                //
            }else if(event=='onchange'){
                if(newValue<oldValue){
                    if(isHold){
                        checkPosAuthentication(22, id);//authentication check only after hold the bill.
                    }else{
                        calculateFooter();
                        updateQty(id);
                    }
                }else{
                    calculateFooter();
                    updateQty(id);
                }
            }
           }else{            
                $(qtyInputId).val(oldValue);
                
           }        
        }

        function checkStockWhenQtyChange(whMenuID,QtyAddedInThisAttemps){
            var itemQty = 0;            
            var findSelector = "[data-wh_menuid='"+whMenuID+"']";
            $("div").find(findSelector).each(function( index ) {
                itemQty += parseFloat($(this).val());
            });
            itemQty -= QtyAddedInThisAttemps;
            var ret=true;
            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/check_stock_on_qty_change'); ?>",
                data: {itemQty: itemQty,id:whMenuID,requestedQty:QtyAddedInThisAttemps},
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {
                    if(data.error==2){
                        ret = false;
                        $('#insufficientItemPOSRSingle_body').html('');
                        if (!jQuery.isEmptyObject(data['insuf'])) {
                        $.each(data.insuf, function (item, value) {
                                var msgData = '<tr>' 
                                +'<td>' + value['itemSystemCode'] + '</td>'
                                +'<td>' + value['itemDescription'] + '</td>'
                                +'<td>' + value['currentStock'] + '</td>'
                                +'<td>' + value['currentUsage'] + '</td>'
                                +'<td>' + value['quantityUsedInThisBill'] + '</td>'
                                +'</tr>';
                                $('#insufficientItemPOSRSingle_body').append(msgData);
                            });
                            $("#insufficientItemPOSRSingle").modal({backdrop: "static"});
                        }
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    ajaxStatus = 0;
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        }
                }
            });
            return ret;
        }

        function checkBillIsHoldByUser(){
            var menuSalesID = $("#holdInvoiceID").val();
            var res = '';
            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/check_bill_is_hold_by_user'); ?>",
                data: {menuSalesID: menuSalesID},
                cache: false,
                beforeSend: function () {

                },
                success: function (data) {
                    res = data.status;
                }, error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    ajaxStatus = 0;
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                }
            });
            return res;
        }

       function deleteLineItem(authID,itemID){
           var isHold = checkBillIsHoldByUser();
           if(isHold){
               checkPosAuthentication(authID,itemID);
           }else{
               beforeDeleteItem(itemID);
           }
       }

    </script>
<?php
$this->load->view('include/footer-pos', $data);
