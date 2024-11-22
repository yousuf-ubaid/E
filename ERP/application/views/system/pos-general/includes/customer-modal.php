<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="customer_master_add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Customer</h4>
            </div>
            <?php echo form_open('', 'role="form" id="customer_master_form"'); ?>
            <input type="hidden" class="form-control input-md" id="customerID" name="customerID">
            <div class="modal-body">
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Customer Name <?php required_mark() ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-md" id="cus_customerName" name="customerName" autocomplete="off"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Telephone</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control input-md" id="cus_customerTelephone"
                                       name="customerTelephone" autocomplete="off" onkeypress="return validateFloatKeyPress(this,event);">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Email</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                <input type="text" class="form-control input-md" id="customerEmail"
                                       name="customerEmail" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Secondary Code</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="cus_customercode" name="customercode">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Address</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-id-card" aria-hidden="true"></i></div>
                                <textarea class="form-control" rows="3" id="customerAddress1"
                                  name="customerAddress1"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Category</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-archive" aria-hidden="true"></i></div>
                                <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group loyalitycardgen hide">
                        <label class="col-md-4 control-label">Loyality Card No</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control input-md" id="loyalitycardno_gen_customer"
                                       name="loyalitycardno_gen_customer">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <button class="btn btn btn-primary" type="button" onclick="save_loyalty_card_gpos_customer()"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="row hide">
                    <!-- <div class="form-group col-sm-4 hide">
                        <label for="">Customer Secondary Code <?php //required_mark() ?></label>
                        <input type="text" class="form-control" id="cus_customercode" name="customercode">
                    </div>
                    <div class="form-group col-sm-4 hide"><label for="financeyear">Category</label>
                        <?php //echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
                    </div> -->
                </div>
                <div class="row">
                    <div class="form-group col-sm-4 hide">
                        <label for="">Receivable Account <?php required_mark() ?></label>
                        <?php echo form_dropdown('receivableAccount', $gl_code_arr, $this->common_data['controlaccounts']['ARA'], 'class="form-control select2" id="receivableAccount" required'); ?>
                    </div>
                    <div class="form-group col-sm-4 hide">
                        <label for="financeyear">Customer Currency <?php required_mark() ?></label>
                        <?php echo form_dropdown('customerCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="customerCurrency" required'); ?>
                    </div>
                    <div class="form-group col-sm-4 hide"><label for="financeyear">Customer
                            Country <?php required_mark() ?></label>

                        <?php echo form_dropdown('customercountry', $country_arr, $this->common_data['company_data']['countryID'], 'class="form-control select2"  id="customercountry" required'); ?>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4 hide">
                        <label for="">Tax Group</label>
                        <?php echo form_dropdown('customertaxgroup', $taxGroup_arr, '', 'class="form-control"  id="customertaxgroup"'); ?>
                    </div>

                </div>

                <div class="row hide">
                    <div class="form-group col-sm-4 hide">
                        <label for="">Fax</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="customerFax" name="customerFax">
                        </div>
                    </div>
                    <div class="form-group col-sm-4 hide">
                        <label for="financeyear">Credit Period</label>
                        <div class="input-group">
                            <div class="input-group-addon">Month</div>
                            <input type="text" class="form-control number" id="customerCreditPeriod"
                                   name="customerCreditPeriod">
                        </div>
                    </div>
                    <div class="form-group col-sm-4 hide"><label for="financeyear">Credit Limit</label>
                        <div class="input-group">
                            <div class="input-group-addon">LKR</div>
                            <input type="text" class="form-control number" id="customerCreditLimit"
                                   name="customerCreditLimit">
                        </div>
                    </div>
                </div>
                <div class="row hide">
                    <div class="form-group col-sm-4 hide">
                        <label for="">URL</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="customerUrl" name="customerUrl">
                        </div>
                    </div>
                    <!-- <div class="form-group col-sm-4">
                        <label for="customerAddress1">Primary Address</label>
                        <textarea class="form-control" rows="2" id="customerAddress1"
                                  name="customerAddress1"></textarea>
                    </div>
                    <div class="form-group col-sm-4"><label for="customerAddress2">Secondary Address</label>
                        <textarea class="form-control" rows="2" id="customerAddress2"
                                  name="customerAddress2"></textarea>
                    </div> -->
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary size-sm" id="customer_add_submit_btn"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('#customer_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                /*customercode: {validators: {notEmpty: {message: 'customer Code is required.'}}},*/
                customerName: {validators: {notEmpty: {message: 'customer Name is required.'}}},
                customerTelephone: {validators: {notEmpty: {message: 'customer Telephone Number is required.'}}}
                /*customercountry: {validators: {notEmpty: {message: 'customer Country is required.'}}},*/
                /*receivableAccount: {validators: {notEmpty: {message: 'Receivabl Account is required.'}}},
                 customerCurrency: {validators: {notEmpty: {message: 'customer Currency  is required.'}}},
                 customerName: {validators: {notEmpty: {message: 'customer Name is required.'}}}*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'currency_code', 'value': $('#customerCurrency option:selected').text()});
            data.push({'name': 'country', 'value': $('#customercountry option:selected').text()});
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Pos/savecustomer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);
                        if(data['status'] == true)
                        {
                          //  $('#customer_master_add').modal('hide');
                            $('#customerID').val(data['last_id']);
                            $('.customerSpan').text(data['customerName']);
                            load_barcode_loyalty_customer_gpos();
                            LoadCustomers();
                            $('#customer_add_submit_btn').prop('disabled',true);
                        }



                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });
    })


    function load_barcode_loyalty_customer_gpos() {
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
                $('#loyalitycardno_gen_customer').val(data);
                $('.loyalitycardgen').removeClass('hide');
            }, error: function () {
                stopLoad();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }
    function save_loyalty_card_gpos_customer() {
        var barcode = $("#loyalitycardno_gen_customer").val();
        var gc_customerTelephone = $("#cus_customerTelephone").val();
        var gc_CustomerName = $("#cus_customerName").val();
        var customerID = $("#customerID").val();
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
            url: "<?php echo site_url('pos/save_loyalty_card'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $("#customer_master_add").modal('hide');
                    $(".loyalitycardgen").addClass('hide');
                    LoadCustomers();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }

        return true;
    }


</script>