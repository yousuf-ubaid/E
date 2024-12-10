    <style>
        .radio_button_area{
            display: flex;
            justify-content: space-evenly;
        }
    </style>
    
    <div class="form-group">

        <label class="col-sm-2 control-label">Service Type</label><!--Comments-->
        <div class="col-sm-4">
        <span class="form-control"><?php echo $clent_order_detail['service_type'] ?> </span>
        </div>

            <label class="col-sm-2 control-label">Status</label><!--Comments-->
        <div class="col-sm-4">
            <span class="form-control"><?php echo $clent_order_detail['status'] ?> </span>
        </div>

    </div>

    <div class="form-group">

        <label class="col-sm-2 control-label">Date</label><!--Comments-->
        <div class="col-sm-4">
            <span class="form-control"><?php echo $clent_order_detail['date_time'] ?> </span>
        </div>

        <label class="col-sm-2 control-label">Order</label><!--Comments-->
        <div class="col-sm-4">
            <span class="form-control"><?php echo $clent_order_detail['date_time'] ?> </span>
        </div>

    </div>

    <div class="form-group">

        <label class="col-sm-2 control-label">Customer </label><!--Comments-->
        <div class="col-sm-4">
            <span class="form-control"><?php echo $clent_order_detail['customer'] ?> </span>
        </div>

        <label class="col-sm-2 control-label">Store Name</label><!--Comments-->
        <div class="col-sm-4">
            <span class="form-control"><?php echo $clent_order_detail['store'] ?> </span>
        </div>

    </div>

<hr>
<div id="filter-panel" class="filter-panel">

    <form id="dn_form">

    <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id ?>" />

    <div class="table-responsive">
        <table id="clent_double_entry" class="<?php echo table_class() ?>">
            <thead>
                <tr>
                    <th style="min-width: 10%">Edit Field</th><!--Code-->
                    <th style="min-width: 10%">Value</th><!--Code-->
                    <th style="min-width: 10%">Apply for all the records of this Store</th><!--Code-->
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td>
                        <span class="text-bold">Delivery fee</span>
                        <input type="hidden" name="fields[]" value="delivery_fee" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['delivery_fee'] ?>" name="edit_fields[]" id="delivery_fee" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_fee_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_fee_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><span class="text-bold">Credits</span>
                        <input type="hidden" name="fields[]" value="credit" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['credit'] ?>" name="edit_fields[]" id="credit" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="credit_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="credit_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Discounts</span>
                        <input type="hidden" name="fields[]" value="discount" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['discount'] ?>" name="edit_fields[]" id="discount" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="discount_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="discount_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">3rd Party Commission</span>
                        <input type="hidden" name="fields[]" value="tmdone_commission" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['tmdone_commission'] ?>" name="edit_fields[]" id="tmdone_commission" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tmdone_commission_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tmdone_commission_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                
                <tr>
                    <td>
                        <span class="text-bold">Bank Charges</span>
                        <input type="hidden" name="fields[]" value="bank_charges" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['bank_charges'] ?>" name="edit_fields[]" id="bank_charges" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="bank_charges_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="bank_charges_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Bank Charges VAT</span>
                        <input type="hidden" name="fields[]" value="bank_charges_vat" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['bank_charges_vat'] ?>" name="edit_fields[]" id="bank_charges_vat" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="bank_charges_vat_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="bank_charges_vat_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Total Adjustment</span>
                        <input type="hidden" name="fields[]" value="total_adjustment" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['total_adjustment'] ?>" name="edit_fields[]" id="total_adjustment" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="total_adjustment_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="total_adjustment_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">TmDone Adjustment</span>
                        <input type="hidden" name="fields[]" value="tmdone_adjustment" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['tmdone_adjustment'] ?>" name="edit_fields[]" id="tmdone_adjustment" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tmdone_adjustment_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tmdone_adjustment_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Driver Adjustment</span>
                        <input type="hidden" name="fields[]" value="driver_adjustment" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['driver_adjustment'] ?>" name="edit_fields[]" id="driver_adjustment" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="driver_adjustment_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="driver_adjustment_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Vendor Adjustment.</span>
                        <input type="hidden" name="fields[]" value="vendor_adjustment" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['vendor_adjustment'] ?>" name="edit_fields[]" id="vendor_adjustment" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vendor_adjustment_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vendor_adjustment_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Vendor Free Delivery.</span>
                        <input type="hidden" name="fields[]" value="vendor_free_delivery" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['vendor_free_delivery'] ?>" name="edit_fields[]" id="vendor_free_delivery" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vendor_free_delivery_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vendor_free_delivery_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Vendor Barring Fee.</span>
                        <input type="hidden" name="fields[]" value="vendor_barring_fee" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['vendor_barring_fee'] ?>" name="edit_fields[]" id="vendor_barring_fee" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vendor_barring_fee_apply_for_all[]" id="inlineRadio1" value="1">
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="vendor_barring_fee_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <span class="text-bold">Adjusted Vendor Settlement.</span>
                        <input type="hidden" name="fields[]" value="adjusted_settlement" />
                    </td>
                    <td>
                        <input type="text" class="form-control text-right" value="<?php echo $clent_order_detail['adjusted_settlement'] ?>" name="edit_fields[]" id="adjusted_settlement" />
                    </td>
                    <td>
                        <div class="col-sm-12 radio_button_area">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="adjusted_settlement_apply_for_all[]" id="inlineRadio1" value="2" checked>
                                <label class="form-check-label pl-2" for="inlineRadio1">&nbsp No</label>
                            </div>
                        </div>
                    </td>
                </tr>
                
                
            </tbody>
            </table>
            <hr>
            <button class="btn btn-success" type="submit" id="btnSubmit">
                <i class="fa fa-check"></i> Update
            </button>

        </form>
    </div>

</div>


<script>

        $('#dn_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
               
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $('#btnSubmit').prop('disabled',false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            // data.push({'name': 'debitNoteMasterAutoID', 'value': debitNoteMasterAutoID});
            // data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            // data.push({'name': 'SupplierDetails', 'value': $('#supplier option:selected').text()});
            // data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: data,
                url: "<?php echo site_url('DataSync/edit_order_manage'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    $('#order_edit_modal').modal('toggle');
                    sales_client_mapping_table();
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
</script>