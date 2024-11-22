<?php
/**
 * --- Created on 16-NOV-2016 by Mohames Shafry
 * --- POS Open Hold Receipt Modal Window
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$d = get_company_currency_decimal();
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_open_hold_receipt" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog pos_open_hold_receipt modal-lg">
        <div class="modal-content">


            <div class="modal-header posModalHeader">
                <button type="button" class="close closeTouchPad" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_open_bills'); ?><!--Open Bills--> </h4>
            </div>
            <div id="modal_body_pos_openHoldReceipt" class="modal-body"
                 style="overflow: visible; background-color: #FFF; min-height: 600px;" onclick="">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default closeTouchPad" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * --- POS Hold Receipt Modal Window
 */
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_hold_receipt_modal" class="modal main-model-receipt"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red closeTouchPad"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_hold_bills'); ?><!--Hold Receipt--> </h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF; min-height: 100px;"
                 id="modal_body_hold">
                <form class="form-horizontal" id="frm_POS_holdReceipt">
                    <input type="hidden" name="holdInvoiceID_input" id="holdInvoiceID_input" value="0">
                    <input type="hidden" name="holdOutletID_input" id="holdOutletID_input" value="0">

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="holdInvoiceID_codeTmp">
                            <?php echo $this->lang->line('posr_invoice_id'); ?><!--Invoice ID--></label>
                        <div class="col-md-4">
                            <input id="holdInvoiceID_codeTmp" readonly type="text"
                                   placeholder="<?php echo $this->lang->line('posr_invoice_id'); ?>"
                                   class="form-control input-md"><!--Invoice ID-->
                        </div>
                    </div>

                    <div class="form-group hide">
                        <label class="col-md-3 control-label" for="holdInvoiceID">
                            <?php echo $this->lang->line('posr_invoice_id'); ?><!--Invoice ID--></label>
                        <div class="col-md-4">
                            <input id="holdInvoiceID" name="invoiceID" readonly type="text"
                                   placeholder="<?php echo $this->lang->line('posr_invoice_id'); ?>"
                                   class="form-control input-md"><!--Invoice ID-->
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="holdReference">
                            <?php echo $this->lang->line('posr_hold_reference'); ?><!--Hold Reference--> </label>
                        <div class="col-md-8">
                            <input type="text" id="holdReference" name="holdReference"
                                   placeholder="<?php echo $this->lang->line('posr_hold_reference'); ?>"
                                   class="form-control input-md custom_touch_keyboad"><!--Type Hold Reference-->
                        </div>
                    </div>

                    <div class="form-group">
                        <?php

                            if (isset($waiters)) {
                                $waiterIndex = 1;
                                foreach ($waiters as $waiter) {


                                    ?>

                                    <div class="col-md-4">
                                        <input type="button" class="btn btn-default waiterBtn"
                                               style="width: 100%;margin: 5px 0;height: 64px;"
                                               data-emp_id="<?php echo $waiter['crewMemberID']; ?>"
                                               onclick="markThisWaiterAsSelectedFromTerminal.call(this)"
                                               value="<?php echo $waiter['crewFirstName']; ?>"/>
                                    </div>
                                    <?php


                                }
                            }

                        ?>
                    </div>


                </form>
                <div class="modal-footer" style="margin-top: 10px;">
                    <button type="button" style="padding: 15px 25px;" class="btn btn-lg btn-default closeTouchPad"
                            data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    &nbsp; <input type="button" name="hold_bill_submit" id="hold_bill_submit"
                                  value="<?php echo $this->lang->line('common_submit'); ?>"
                                  onclick="submitHoldReceipt()"
                                  class="btn btn-primary closeTouchPad"
                                  style="background-color: #3fb618; color: #FFF; border: 0px; padding: 15px 25px; float: right;">
                    <!--Submit-->
                </div>
            </div>

        </div>
    </div>
</div>

<script>

    function holdReceipt() {
        if (terminalGlobalVariables.selectedWaiter != null) {
            $("div").find("[data-emp_id='" + terminalGlobalVariables.selectedWaiter + "']").addClass('btn-primary');//marking selected waiter.
        }
        // $("#pos_hold_receipt_modal").modal('show');
        // setTimeout(function () {
        //
        //     $("#holdReference").val('');
        //     load_hold_refno($("#holdInvoiceID_input").val());
        // }, 800);
        $("#holdReference").val('');
        load_hold_refno($("#holdInvoiceID_input").val());
        $("#pos_hold_receipt_modal").keyup(function (e) {
            if (e.keyCode == 13) {
                submitHoldReceipt();
            }
        })
    }

    function submitHoldReceipt() {
        var formData = $(".form_pos_receipt,#frm_POS_holdReceipt").serializeArray();
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;

        date = hour + ":" + minute + ":" + seconds;
        formData.push({'name': 'currentTime', 'value': date});
        formData.push({'name': 'selectedWaiter', 'value': terminalGlobalVariables.selectedWaiter});

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/submitHoldReceipt'); ?>",
            data: formData,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#holdReference").val('');
                    $("#pos_hold_receipt_modal").modal('hide');
                    clearSalesInvoice();
                    reset_delivery_order();
                } else {
                    myAlert('d', data['message']);
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
    }

    function open_holdReceipt() {
        load_pos_hold_receipt()
    }


    function load_pos_hold_receipt() {
        <?php
        if (isset($tablet) && $tablet) {
            $method = 'load_pos_hold_receipt_tablet';
        } else {
            $method = 'load_pos_hold_receipt';
        }
        ?>
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/' . $method); ?>",
            data: $("#frm_POS_holdReceipt").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_open_hold_receipt").modal("show");
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#modal_body_pos_openHoldReceipt").html(data);
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

    function open_wowfood_sales(id, outletID) {
        bootbox.confirm({
            message: "Are sure you want to accept this order?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (user_confirmation) {
                if (user_confirmation) {
                    update_wowfood_status(id);
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('Pos_restaurant/openHold_sales'); ?>",
                        data: {id: id, outletID: outletID},
                        cache: false,
                        beforeSend: function () {
                            $("#pos_open_hold_receipt").modal("hide");
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 0) {
                                $("#pos_salesInvoiceID_btn").html(data['code']);
                                $("#delivery_invoiceCode").html(data['code']);
                                if (data['advancePayment'] > 0) {
                                    $("#delivery_advancePaymentAmount").val(data['advancePayment']);
                                    var advancePayment = parseFloat(data['advancePayment']);
                                    $("#delivery_advancePaymentAmountShow").html(advancePayment.toFixed(<?php echo $d ?>));
                                }
                                if (data['isDeliveryOrder'] == 1) {
                                    $("#deliveryPersonID").val('-1').change();
                                    $("#isDelivery").val(1);
                                    $("#deliveryOrderID").val(data['deliveryOrderID'])
                                }
                                checkPosSession();
                            } else {
                                myAlert('e', data['message']);
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
                }
            }
        });


    }

    function update_wowfood_status(menuSalesID) {
        $.ajax({
            async: false,
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/update_wowfood_status'); ?>",
            data: {menuSalesID: menuSalesID, status: 1},
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
    }

    function open_submitted_invoice(id, outletID) {
        $('.paymentInput').val('');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/openHold_sales'); ?>",
            data: {id: id, outletID: outletID},
            cache: false,
            beforeSend: function () {
                $("#pos_open_hold_receipt").modal("hide");
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data['error'] == 0) {

                    $("#pos_salesInvoiceID_btn").html(data['code']);
                    $("#delivery_invoiceCode").html(data['code']);
                    if (data['advancePayment'] > 0) {
                        $("#delivery_advancePaymentAmount").val(data['advancePayment']);
                        var advancePayment = parseFloat(data['advancePayment']);
                        $("#delivery_advancePaymentAmountShow").html(advancePayment.toFixed(<?php echo $d ?>));
                    }
                    if (data['isDeliveryOrder'] == 1) {
                        $("#deliveryPersonID").val('-1').change();
                        $("#isDelivery").val(1);
                        $("#deliveryOrderID").val(data['deliveryOrderID'])
                    }

                    $(".paymentInputupdate").val('');

                    checkPosSessionSubmitted(id);
                    $("#pos_open_void_receipt").modal("hide");
                    $is_credit_sale = is_credit_sale(id);
                    if ($is_credit_sale == false) {
                        //open_pos_submitted_payments_modal();
                        open_pos_submitted_payments_modal_update();
                    } else {
                        myAlert('w', 'You cannot edit payment type for a credit sale.');
                    }

                } else {
                    myAlert('e', data['message']);
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
    }


    function is_credit_sale(menuSalesID) {
        var is_credit_sale = null;
        $.ajax({
            async: false,
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/is_credit_sale'); ?>",
            data: {menusalesID: menuSalesID},
            cache: false,
            success: function (data) {
                is_credit_sale = data.status;
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
        return is_credit_sale;
    }

    function openHold_sales(id, outletID) {
        localStorage.setItem('isHomeRedirect', '0');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/openHold_sales'); ?>",
            data: {id: id, outletID: outletID},
            cache: false,
            beforeSend: function () {
                $("#pos_open_hold_receipt").modal("hide");
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    $("#pos_salesInvoiceID_btn").html(data['code']);
                    $("#delivery_invoiceCode").html(data['code']);
                    if (data['advancePayment'] > 0) {
                        $("#delivery_advancePaymentAmount").val(data['advancePayment']);
                        var advancePayment = parseFloat(data['advancePayment']);
                        $("#delivery_advancePaymentAmountShow").html(advancePayment.toFixed(<?php echo $d ?>));
                    }
                    if (data['isDeliveryOrder'] == 1) {
                        $("#deliveryPersonID").val('-1').change();
                        $("#isDelivery").val(1);
                        $("#deliveryOrderID").val(data['deliveryOrderID'])
                    }
                    checkPosSession();
                } else {
                    myAlert('e', data['message']);
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
    }

    function load_hold_refno(menuSalesID) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/load_hold_refno'); ?>",
            data: {menuSalesID: menuSalesID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                // if (!jQuery.isEmptyObject(data)) {
                //     if (data['holdRemarks'] == null) {
                //         data['holdRemarks'] = '';
                //     }
                //     if (terminalGlobalVariables.waiterName == "" && data['holdRemarks'] == "") {
                //         $('#holdReference').val('');
                //     } else if (data['holdRemarks'] == "") {
                //         $('#holdReference').val(terminalGlobalVariables.waiterName);
                //     } else if (terminalGlobalVariables.waiterName == "") {
                //         $('#holdReference').val(data['holdRemarks']);
                //     } else {
                //         $('#holdReference').val(data['holdRemarks']);
                //     }
                // } else {
                //     if (terminalGlobalVariables.waiterName == "") {
                //         $('#holdReference').val('');
                //     } else {
                //         $('#holdReference').val(terminalGlobalVariables.waiterName);
                //     }
                //
                // }
                if (!jQuery.isEmptyObject(data)) {
                    $('#holdReference').val(data['holdRemarks']);
                }
                $("#pos_hold_receipt_modal").modal('show');
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

    function closeTouchPad(e) {
        if (!$(e.target).hasClass('touchEngKeyboard')) {
            $("div.touchEngKeyboard").hide();
        }
    }

    $(document).on('click', '.closeTouchPad', function (e) {
        closeTouchPad(e);
    });

</script>
