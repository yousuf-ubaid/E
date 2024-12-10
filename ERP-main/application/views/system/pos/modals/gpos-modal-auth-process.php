<?php
/**
 * --- POS Open void Receipt Modal Window
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .loginmodal-container {
        padding: 15px;
        max-width: 350px;
        width: 100% !important;
        background-color: #F7F7F7;
        margin: 0 auto;
        border-radius: 2px;
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    .loginmodal-container h1 {
        text-align: center;
        font-size: 1.8em;
    }

    .loginmodal-container input[type=button] {
        width: 100%;
        display: block;
        margin-bottom: 10px;
        position: relative;
    }

    .loginmodal-container input[type=text], input[type=password] {
        height: 44px;
        font-size: 16px;
        width: 100%;
        margin-bottom: 10px;
        -webkit-appearance: none;
        background: #fff;
        border: 1px solid #d9d9d9;
        border-top: 1px solid #c0c0c0;
        /* border-radius: 2px; */
        padding: 0 8px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .loginmodal-container input[type=text]:hover, input[type=password]:hover {
        border: 1px solid #b9b9b9;
        border-top: 1px solid #a0a0a0;
        -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .loginmodal-container a {
        text-decoration: none;
        color: #666;
        font-weight: 400;
        text-align: center;
        display: inline-block;
        opacity: 0.6;
        transition: opacity ease 0.5s;
    }

    .loginmodal-container .nav-tabs {
        border-bottom: none !important;
    }

    .loginmodal-container .nav-tabs > li {
        color: #222 !important;
    }

    .loginmodal-container .nav-tabs > li.active > a, .loginmodal-container .nav-tabs > li.active > a:hover, .loginmodal-container .nav-tabs > li.active > a:focus {
        color: #fff;
        background-color: #d14d42;
        border: none !important;
        border-bottom-color: transparent;
        border-radius: 0 !important;
    }

    .loginmodal-container .nav-tabs > li > a {
        margin-right: 2px;
        line-height: 1.428571429;
        border: none !important;
        border-radius: 0 !important;
        text-transform: uppercase;
        font-size: 16px;
    }
</style>
<div class="modal" id="pos_auth_modal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none; z-index: 5050;">
    <div class="modal-dialog">
        <div class="loginmodal-container">
            <h1><?php echo $this->lang->line('posr_authentication'); ?></h1><br>
            <ul class="nav nav-tabs final-login">
                <li class="active"><a data-toggle="tab" href="#pos_auth_barcode"
                                      onclick="pos_auth_mode(2)"><?php echo $this->lang->line('posr_access_card'); ?></a>
                </li>
                <li><a data-toggle="tab" href="#pos_auth_login"
                       onclick="pos_auth_mode(1)"><?php echo $this->lang->line('posr_manual'); ?></a></li>
            </ul>
            <form id="frm_pos_auth_process" method="post">
                <input type="hidden" name="processMasterID" id="pos_auth_processMasterID">
                <input type="hidden" name="type" id="pos_auth_type" value="2">
                <div class="tab-content">
                    <div id="pos_auth_barcode" class="tab-pane in active">
                        <div class="innter-form" style="margin-top: 10px">
                            <input type="password" name="pos_barCode" id="pos_auth_barCode"
                                   placeholder="<?php echo $this->lang->line('posr_access_card'); ?>" autofocus>
                            <button class="btn btn-lg btn-default pos_auth_btn_submit"
                                    type="submit"><?php echo $this->lang->line('common_ok'); ?></button>
                            <button type="button" class="btn btn-lg btn-default" onclick="po_auth_modal_close()">
                                <?php echo $this->lang->line('common_cancel'); ?> </button>
                        </div>
                    </div>
                    <div id="pos_auth_login" class="tab-pane">
                        <div class="innter-form" style="margin-top: 10px">
                            <input type="text" name="username" id="pos_auth_username" placeholder="Username" autofocus>
                            <input type="password" name="password" placeholder="Password">
                            <button class="btn btn-lg btn-default pos_auth_btn_submit"
                                    type="submit"><?php echo $this->lang->line('common_ok'); ?></button>
                            <button type="button" class="btn btn-lg btn-default" onclick="po_auth_modal_close()">
                                <?php echo $this->lang->line('common_cancel'); ?> </button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var extraParameter;
    var extraParameter2;

    /*check company has authentication check and load the authentication modal*/
    function checkPosAuthentication(processMasterID, parameter = null, parameter2 = null) {
        extraParameter = parameter;
        extraParameter2 = parameter2;
        var hasAccess = checkHasCompanyPosAuthProcess(processMasterID); // store returned value
        if (hasAccess) {

            if (processMasterID == 18 || processMasterID == 19) {
                check_discount_capamnt(processMasterID, parameter, parameter2);
            } else {
                openUserPosAuthProcessModal(processMasterID); // call authentication modal
            }
        } else {
            loadPosProcess(processMasterID, parameter, parameter2);
            $("#pos_form").submit();
        }
    }

    function check_discount_capamnt(processMasterID, parameter, parameter2) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_auth_process/check_discount_capamnt'); ?>",
            data: {processMasterID: processMasterID},
            cache: false,
            async: false,
            beforeSend: function () {
            },
            success: function (data) {
                gpos_discount_cal(parameter, data, processMasterID);
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

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) { // focus text box
        $("#pos_auth_username").focus();
        $("#pos_auth_barCode").focus();
    });

    function pos_auth_mode(type) { // set loging mode type
        $('#pos_auth_type').val(type);
    }

    function hideAuthModal() {
        $('#pos_auth_modal').modal('hide');
    }

    function po_auth_modal_close() {


        if ($('#pos_auth_processMasterID').val() == 10) {
            $(extraParameter).val('');
            updatePaidAmount(extraParameter);
            calculateReturn();
            hideAuthModal()

        } else if ($('#pos_auth_processMasterID').val() == 2) {
            var zero = 0;
            var discat = $("#gen_disc_amount").val();
            var nettotaftrdictt = $("#netTot_after_g_disc").val();
            $("#gen_disc_percentage").val(zero.toFixed(d));
            $("#gen_disc_amount").val(zero.toFixed(d));
            $("#netTot_after_g_disc_div").html((parseFloat(nettotaftrdictt) + parseFloat(discat)).toFixed(d));
            $("#netTot_after_g_disc").val((parseFloat(nettotaftrdictt) + parseFloat(discat)).toFixed(d));
            $("#gen_disc_amount_hide").val(zero.toFixed(d));
            hideAuthModal()


        } else if ($('#pos_auth_processMasterID').val() == 12) {
            $('#disAmount').val('');
            $('#disPer').val('');
            disAmount.val('');
            disPer.val('');
            $('#pos_auth_modal').modal('hide');
            hideAuthModal()
        } else if ($('#pos_auth_processMasterID').val() == 15) {
            $('#salesPrice').val($('#salesPricehn').val());
            $('#pos_auth_modal').modal('hide');
            hideAuthModal()
        } else if ($('#pos_auth_processMasterID').val() == 19) {
            disPer.val('');
            disAmount.val('');
            hideAuthModal()
        } else if ($('#pos_auth_processMasterID').val() == 18) {
            neutral_generalDiscount();
            hideAuthModal()
        } else {
            hideAuthModal()
        }
    }

    function openUserPosAuthProcessModal(processMasterID) {  // open authenticaiton modal
        $('#pos_auth_processMasterID').val(processMasterID);
        $('#frm_pos_auth_process')[0].reset();
        $('#pos_auth_modal').modal();
    }

    $("#frm_pos_auth_process").submit(function (e) { //submin authentication form
        //prevent Default functionality
        e.preventDefault();
        submitUserPosAuthProcess()
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        $(this).find('[autofocus]').focus();
    });

    /* check user authentication on pos process*/
    function submitUserPosAuthProcess() {
        var status = false;
        var data = $("#frm_pos_auth_process").serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_auth_process/check_gpos_auth_process'); ?>",
            data: data,
            cache: false,
            async: false,
            beforeSend: function () {
                $(".pos_auth_btn_submit").prop('disabled', true);
            },
            success: function (data) {
                $(".pos_auth_btn_submit").prop('disabled', false); // Please comment it later
                myAlert(data[0], data[1]);
                var processMasterID = $('#pos_auth_processMasterID').val();
                if (data[0] == 's') {
                    $('#pos_auth_modal').modal('hide');
                    loadPosProcess(processMasterID, extraParameter, extraParameter2);
                    $("#pos_form").submit();
                    extraParameter = null;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".pos_auth_btn_submit").prop('disabled', false);
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    /*check company has access to check authentication*/
    function checkHasCompanyPosAuthProcess(processMasterID) {
        var status = false;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_auth_process/check_has_gpos_auth_process'); ?>",
            data: {processMasterID: processMasterID},
            cache: false,
            async: false,
            beforeSend: function () {
            },
            success: function (data) {

                if (data) {
                    status = true;
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
        return status;
    }

    function loadPosProcess(processMasterID, parameter = null, parameter2 = null) {

        switch (parseInt(processMasterID)) {
            case 1:
                //addPromotion(parameter); //trigger promotion button function in payment
                break;
            case 3:
                void_gpos(); //trigger voidBill button function
                break;
            case 4:
                //$.print('#print_content' + parameter); //trigger print button function
                //print_paymentReceipt(parameter); //added by Madura
                break;
            case 6:
                //topUpGiftCard(); //trigger gift card topup button
                break;
            case 7:
                //update_redeemAmount(); //trigger gift card redeem button
                break;
            case 8:
                //javaApp_submit(); //trigger java app redeem button
                break;
            case 9:
                //cancelCurrentOrder(); //trigger cancel button function
                break;
            case 10:
                //calculatePaidAmount(parameter); //trigger calculate paid amount function in payment
                break;
            case 11:
                updateCreditSale(parameter);
                break;
            case 12:
                //calculateFooter(parameter.typeOfSource);
                break;
            case 13:
                if (parameter2 != null) {
                    //updateQty_afterAuth(parameter, parameter2);
                } else {
                    //deleteDiv(parameter);
                }
                break;
            case 14:
                checkifItemExsistReturn();
                break;
            case 18:
                gpos_discount_cal(parameter);

                break;
            case 19:
                gpos_discount_cal(parameter);
                //check_discount_capamnt(processMasterID, parameter, parameter2);
                break;
            case 21:
                addPromotion(parameter); //trigger promotion button function in payment
                break;
        }
    }

    function gpos_discount_cal(field, capdetails = null, processMasterID = null) {

        if (field == 'disPer') {
            var disc = $.trim(disPer.val());
            if (disc != '') {
                if (disc > 100) {
                    myAlert('e', 'Discount Percentage must be lesser than 100');
                    disPer.val('');
                    disAmount.val('');
                } else if (disc > 0) {
                    var price = $.trim(salesPrice.val());
                    if (price != '') {
                        var m = price * disc * 0.01;
                        disAmount.val(commaSeparateNumber(m, dPlaces));
                    }
                } else if (disc == 0) {
                    disAmount.val(commaSeparateNumber(0, dPlaces));
                }

                var salesPrice_val = getNumberAndValidate(salesPrice.val());
                if (capdetails != null) {

                    if (parseFloat(capdetails['capPercentage']) > 0) {
                        if (disc > parseFloat(capdetails['capPercentage'])) {
                            openUserPosAuthProcessModal(processMasterID);
                        }else{
                            $("#pos_form").submit();
                        }
                    } else if (parseFloat(capdetails['capPercentage']) == 0) {
                        openUserPosAuthProcessModal(processMasterID);
                    }else{

                        $("#pos_form").submit();
                    }
                }
            }
        } else if (field == 'disAmount') {
            var disAmount_val = disAmount.val();
            if (disAmount_val != '') {
                disPer.val('');
                var salesPrice_val = getNumberAndValidate(salesPrice.val());
                if (salesPrice_val < disAmount_val) {
                    myAlert('e', 'Discount amount could not be greater than sales price');
                    $(this).val('');
                    disPer.val('');
                } else if (disAmount_val > 0) {
                    var discountInPercentage = parseFloat((disAmount_val * 100) / salesPrice_val);
                    disPer.val(discountInPercentage);
                }

                if (capdetails != null) {
                    if (parseFloat(capdetails['capPercentage']) > 0) {
                        if (discountInPercentage > parseFloat(capdetails['capPercentage'])) {
                            openUserPosAuthProcessModal(processMasterID);
                        }else{
                            $("#pos_form").submit();
                        }
                    } else if (parseFloat(capdetails['capPercentage']) == 0) {
                        openUserPosAuthProcessModal(processMasterID);
                    }else{

                        $("#pos_form").submit();
                    }
                }

            } else {
                disPer.val('');
            }
        } else if (field == 'gen_disc_percentage') {
            if (capdetails != null) {
                calculate_general_discount($("#gen_disc_percentage"), 1, capdetails, processMasterID);
            } else {
                calculate_general_discount($("#gen_disc_percentage"));
            }

        } else if (field == 'gen_disc_amount') {
            if (capdetails != null) {
                calculate_general_discount($("#gen_disc_amount"), 1, capdetails, processMasterID);
            } else {
                calculate_general_discount($("#gen_disc_amount"));
            }

        }
    }

</script>