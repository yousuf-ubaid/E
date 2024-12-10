<script src="<?php echo $this->config->item('merchant_base_url');?>/checkout/version/<?php echo $this->config->item('mastercard_version');?>/checkout.js" 
            data-error="errorCallback" 
            data-cancel="cancelCallback" 
            data-complete="completeCallback"></script>
<script>
    let selectedRowID = null;
    let sub_tbl = null;
    $(document).ready(function(){
        fetch_subscription_invoices();
        credit_cardpaymentstatus();
    });

    function fetch_subscription_invoices(selectedRowID){
        sub_tbl = $('#subscription_master_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Company/subscription_invoices'); ?>",
            "aaSorting": [[0, 'desc']],
            "dom": '<"row custom_data_table_container"<"custom_data_table_header" <"col-sm-6"i><"col-sm-6"f>>>rt<"row" <"col-sm-3"l><"col-sm-9 custom_data_table_paginate"p>>',
            "fnInitComplete": function () {
                var str = '<div class="input-group"><span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>';
                str += '<input type="search" class="form-control input-sm custom_data_table_search_box" placeholder="" ';
                str += 'onkeyup="search_sub_tbl(this)"  aria-controls="subscription_master_table"></div>';

                $('#subscription_master_table_filter').html(str);
            },
            "fnDrawCallback": function (oSettings) {

                $("[rel=tooltip]").tooltip();
                let tmp_i   = oSettings._iDisplayStart;
                let iLen    = oSettings.aiDisplay.length;

                let x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    //$('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['invID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "invNo"},
                {"mData": "invDate"},
                {"mData": "dueDate"},
                {"mData": "invTotal"},
                {"mData": "payStatus"},
                {"mData": "action"}
            ],
            "columnDefs": [
                { "targets": [4,5],  "orderable": false }
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function search_sub_tbl(obj){
        let search_val = $(obj).val();
        sub_tbl.search(search_val).draw();
    }

    function load_invoice(inv_id){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Company/load_subscription_invoice_view'); ?>",
            data: {'inv_id': inv_id},
            cache: false,
            beforeSend: function () {
                $('.pay-input').hide();
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    if(data['isAmountPaid'] == 0){
                        $('.pay-input').show();
                        $('#inv-pay-btn').show().attr('onclick', 'receive_payment('+inv_id+')');

                        <?php if(PAY_PAL_ENABLED){ ?>
                        setTimeout(function(){
                            stopLoad();
                            let pay_amount = $('#inv_amount').val();
                            paypal.Buttons({
                                createOrder: function(data, actions) {
                                    //startLoad();
                                    return actions.order.create({
                                        purchase_units: [{
                                            amount: { value: pay_amount }
                                        }],
                                        experience: {
                                            input_fields: {
                                                no_shipping: 1
                                            }
                                        }
                                    });
                                },
                                onApprove: function(data, actions) {
                                    return actions.order.capture().then(function(details) {
                                        startLoad();
                                        verify_payment(inv_id, details.id);
                                    });
                                },
                                onCancel: function () {
                                    stopLoad();
                                }
                            }).render('#pay-pal-btn-container');
                        }, 300);
                        <?php } ?>
                    }

                    $('#invoice_body').html(data['view']);
                    $('#invoice_modal').modal('show');
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function verify_payment(inv_id, orderID){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Company/pay_pal_payment_verify'); ?>",
            data: {'inv_id': inv_id, 'orderID': orderID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0] == 's'){
                    sub_tbl.ajax.reload();
                    $('#payment-det').html(data['payment_det']);
                    $('#pay-pal-btn-container').hide();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function receive_payment(inv_id){
        $('#att_inv_id').val(inv_id);
        $('#pay_modal').modal('show');
    }

    function subscription_attachment_upload() {
        let formData = new FormData($("#pay_attachment_frm")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Company/subscription_attachment_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#remove_id').click();
                    $('#att_description').val('');
                    append_attachment( data['att_data'] );
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'Error in payment process');
            }
        });
        return false;
    }

    function sub_attachment_delete(id, fileName){
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?> ",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Company/subscription_attachment_delete'); ?>",
                    data: {'attachmentID': id, 'fileName': fileName},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            append_attachment(data['att_data']);
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'Error in attachment delete process');
                    }
                });
            }
        );
    }

    function sub_payment_type_confirm(id) {
        let pay_type = $('#payment_type').val();
        $('#pay-pal-btn-container').hide();

        if (pay_type == 2) {
            $('#pay-pal-btn-container').show();
            return false;
        }
        if (pay_type == 4) {
            window.location = "<?=site_url('credit_card_payment'); ?>/inv-"+id+"/";
            return false;
            proceed_payment(id);
        }
        if (pay_type == 5)
        {
            proceed_payment_debit(id);
            return false;
        }

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_proceed');?> ",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Company/subscription_payment_confirmation'); ?>",
                    data: {'att_inv_id': id, 'pay_type': pay_type},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            sub_tbl.ajax.reload();
                            $('#payment-det').html(data['payment_det']);
                            $('.delete-rel-items').hide();
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'Error in payment process');
                    }
                });
            }
        );
    }

    function append_attachment(attachment){
        if($.trim(attachment) == ''){
            attachment = '<tr class="danger"><td colspan="5" class="text-center"><?=$this->lang->line('common_no_attachment_found')?></td></tr>';
        }

        $('#subscription_attachment_padding').empty().append(attachment);
    }

    function on_payment_type_change() {
        let pay_btn_container = $('#pay-pal-btn-container');
        let pay_btn = $('#sub-pay-btn');

        if( $('#payment_type').val() == 1 ){
            pay_btn_container.hide();
            pay_btn.prop('disabled', false);
        }
        else if ( $('#payment_type').val() == 2){
            pay_btn_container.show();
            pay_btn.prop('disabled', true);
        }else
        {
            pay_btn_container.hide();
            pay_btn.prop('disabled', false);
        }
    }

    function errorCallback(error) {
    }

    function cancelCallback() {
        var invoiceID = window.localStorage.getItem('invoiceID_creditcard');
    }
     

    function proceed_payment(id){ 
        window.localStorage.setItem('invoiceID_creditcard', id);
        $.ajax({
            async: true,
            type: 'post',
            data: {'invoiceID': id},
            dataType: 'json',
            url: "<?=site_url('Company/get_mastercard_sessionID'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['session_id'] == '0') {
                    swal("Error", data['error_msg'], "error");
                    return false;
                }
                
                Checkout.configure({
                    merchant: data['merchant'],
                    order: {
                        amount: function() {                            
                            return data['invoice_amount']
                        },
                        currency: data['mastercard_currency'],
                        description: 'Subscription Payments',
                        id: data['invID'],
                        reference : 'ORDREF'+data['invID']
                    },
                    session : {
                        id : data['session_id']
                    },
                    transaction :{
                            reference : 'TRANSREF'+data['invID']
                    },
                    interaction: {
                        operation : 'PURCHASE',
                        displayControl: {
                            billingAddress : 'HIDE',
                            customerEmail  : 'HIDE',
                            orderSummary   : 'SHOW',
                            shipping       : 'HIDE'
                        },
                        merchant: {
                            name: data['invoice_name'],
                            address: {
                                line1: data['companyPrintAddress'],
                            }
                        },                        
                    }
                });

                Checkout.showPaymentPage();
            }, error: function () {
                    myAlert('e', 'Some thing went wrong,Please contact system support.')
            }
        });
    }

    function proceed_payment_debit(id){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'invoiceID': id},
            url: "<?php echo site_url('Company/pay_debicardpayment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#sub-pay-btn').prop("disabled", true);
                window.location=data;
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function fetch_credit_receipt_view(invoiceID,results)
    { 
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
           data: {'invoiceID': invoiceID,'results':results},
            url: "<?php echo site_url('Company/credit_card_receipt_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                
                $('#credit_card_receiptview').html(data);
                $('#view_creditcard_receipt').modal('show');
                window.localStorage.removeItem("invoiceID_creditcard");
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    
    function credit_cardpaymentstatus(){ 
        var invoiceID = window.localStorage.getItem('invoiceID_creditcard');
        if(invoiceID!=null && invoiceID!='') { 
            $.ajax({
                async: true,
                type: 'post',
                data: {'invoiceID':invoiceID},
                dataType: 'json',
                url: "<?php echo site_url('Company/save_mastercard_details'); ?>",
                beforeSend: function () {
                    startLoad();


                },
                success: function (data) {
                    stopLoad();
                    if(data[0]=='s')
                    {
                        
                        sub_tbl.ajax.reload();
                        $('#payment-det').html(data['payment_det']);
                        fetch_credit_receipt_view(invoiceID,'Success');
                    // $('#invoice_modal2').modal("show");
                    }else { 
                        fetch_credit_receipt_view(invoiceID,'Payment cancelled');
                    }
                }, error: function () {
                    myAlert('e', 'Some thing went wrong,Please contact system support.')
                }
            });  

            window.localStorage.removeItem("invoiceID_creditcard");
        }       
    }
</script>

<?php
