function payInCashAutomated() {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: site_url + '/Pos/check_cash_gl_exist',
        cache: false,
        beforeSend: function () {
        },
        success: function (data) {
            stopLoad();
            //console.log(data[0]);
            if (data[0] != 'e') {
                var totalAmount = $("#netTot_after_g_disc").val();
                $("#paid").val(totalAmount);
                $("#total_payable_amt").val(totalAmount);
                if (totalAmount > 0) {
                    $("input[name='paymentTypes[1]']").val(totalAmount);
                    var postData = $('.form_pos_receipt').serializeArray();
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: site_url + '/Pos/submit_pos_payments',
                        data: postData,
                        cache: false,
                        beforeSend: function () {

                            startLoad();
                            calculateReturn();
                            $("#submit_btn").prop('disabled', true);
                        },
                        success: function (data) {
                            stopLoad();
                            $("#submit_btn").prop('disabled', false);
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                var zero = 0;
                                printcunt = printcunt + 1;
                                $('#isInvoiced').val('');
                                $('#totSpan').html(zero.toFixed(dPlaces));
                                $('#netTotSpan').html(zero.toFixed(dPlaces));
                                $("#pos_payments_modal").modal('hide');
                                newInvoice(1);
                                clearform_pos_receipt();
                                //searchByKeyword();
                                var doSysCode_refNo = $('#doSysCode_refNo').text();
                                invoicePrint(data[2], data[3], data[4]);
                                //searchByKeyword(1);
                                reset_generalDiscount();
                            }
                            if (data[0] == 'w') {
                                $('#errormsgInsuf').empty();
                                console.log(data[2]);
                                $.each(data[2], function (key, value) {
                                    $('#errormsgInsuf').append('<tr><td>' + value['itemCode'] + '</td><td>' + value['itemDesc'] + '</td><td>' + value['cruuentStock'] + '</td></tr>');
                                });
                                $('#insufficentmodel').modal('show');
                                $("#pos_payments_modal").modal('hide');
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            $("#submit_btn_pos_receipt").html('Submit');
                            $("#submit_btn").prop('disabled', false);
                            if (jqXHR.status == false) {
                                myAlert('w', 'No Internet, Please try again');
                            } else {
                                myAlert('e', 'Message: ' + errorThrown);
                            }
                        }
                    });
                } else {
                    myAlert('i', 'Please add the item and try again!');
                }
            } else {
                myAlert('e', 'Cash GL is not assigned to the outlet.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            if (jqXHR.status == false) {
                myAlert('w', 'No Internet, Please try again');
            } else {
                myAlert('e', 'Message: ' + errorThrown);
            }
        }
    });


}

function js_check_cash_gl_exist() {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: site_url + '/Pos/check_cash_gl_exist',
        cache: false,
        beforeSend: function () {
        },
        success: function (data) {
            stopLoad();
            //console.log(data[0]);
            if (data[0] == 'e') {
                return false;
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            stopLoad();
            if (jqXHR.status == false) {
                myAlert('w', 'No Internet, Please try again');
            } else {
                myAlert('e', 'Message: ' + errorThrown);
            }
        }
    });
}

function openPromotionModal() {
    $("#pos_payments_promotion_modal").modal('show');
}

function addPromotion(id) {
    console.log('addPromotion');
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
