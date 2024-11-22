<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


$umo_arr = array('' => 'Select UOM');
$segment_arr = fetch_segment();

?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-right">
        <li class="active hide"><a data-toggle="tab" href="#tab_1" aria-expanded="false"><?php echo $this->lang->line('common_item');?></a></li><!--Item-->

    </ul>
    <div class="tab-content">
        <div id="tab_1" class="tab-pane active">
            <table class="table table-bordered table-striped  table-condensed">
                <thead>
                <tr style="background-color: #ffffff;">
                    <th colspan="7"> &nbsp</th>
                    <th colspan="2">
                        <button type="button" data-toggle="modal" data-target="#inv_base_modal"
                                class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i><?php echo $this->lang->line('sales_markating_transaction_document_add_item');?>
                        </button><!--Add Item-->
                    </th>
                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_document_code');?> </th><!--Item Code-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_item_code');?> </th><!--Item Code-->
                    <th style="min-width: 30%" class="text-left"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?>  </th><!--UOM-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_sales_price');?></th><!--Sales Price-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_return_qty');?> </th><!--Return Qty-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_tax_total');?></th><!--Total Tax-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_value');?> </th><!--Value-->
                    <th style="min-width: 5%">&nbsp;</th>
                </tr>
                </thead>
                <tbody id="invoice_table_body">
                <tr class="danger">
                    <td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td><!--No Records Found-->
                </tr>
                </tbody>
                <tfoot id="invoice_table_tfoot">

                </tfoot>
            </table>
        </div><!-- /.tab-pane -->
        <div id="tab_2" class="tab-pane">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                <tr>
                    <th colspan="5"><?php echo $this->lang->line('sales_markating_transaction_document_item_details');?> </th><!--Item Details-->
                    <th colspan="2">
                        <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right btn-xs"><i
                                class="fa fa-plus"></i><?php echo $this->lang->line('sales_markating_transaction_document_add_item');?>
                        </button><!--Add Item-->
                    </th>
                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?> </th><!--Code-->
                    <th style="min-width: 40%" class="text-left"><?php echo $this->lang->line('common_description');?>  </th><!--Description-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_uom');?> </th><!--UOM-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
                    <th style="min-width: 5%">&nbsp;</th>
                </tr>
                </thead>
                <tbody id="item_table_body">
                <tr class="danger">
                    <td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                </tr>
                </tbody>
                <tfoot id="item_table_tfoot">

                </tfoot>
            </table>
        </div><!-- /.tab-pane -->
    </div><!-- /.tab-content -->
</div>
<hr>
<div class="text-right m-t-xs">
    <!--<button class="btn btn-default prev" onclick="">Previous XX</button>-->
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_sales_add_item_detail');?></h5><!--Add Item Detail-->
            </div>
            <form role="form" id="item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('sales_markating_transaction_item_code');?>  <?php required_mark(); ?></label><!--Item Code-->
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="search" id="search"
                                   placeholder="Item ID, Item Description...">
                            <input type="hidden" class="form-control" id="itemSystemCode" name="itemSystemCode">
                            <input type="hidden" class="form-control" id="itemAutoID" name="itemAutoID">
                            <input type="hidden" class="form-control" id="itemDescription" name="itemDescription">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?> <?php required_mark(); ?></label><!--UOM-->
                        <div class="col-sm-4">
                            <input type="hidden" class="form-control" id="defaultUOM" name="defaultUOM">
                            <?php echo form_dropdown('UnitOfMeasure', $umo_arr, 'Each', 'class="form-control" id="UnitOfMeasure" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></label><!--Segment-->
                        <div class="col-sm-4">
                            <?php echo form_dropdown('a_segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control" id="a_segment" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('sales_markating_transaction_return_qty');?> <?php required_mark(); ?></label><!--Return Qty-->
                        <div class="col-sm-4">
                            <input type="text" name="return_Qty" id="return_Qty" placeholder="0.00"
                                   class="form-control number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_comment');?> </label><!--Comment-->
                        <div class="col-sm-6">
                            <textarea class="form-control" rows="3" name="comment" placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_comment');?>..."
                                      id="comment"></textarea><!--Item Comment-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary" type=""><?php echo $this->lang->line('common_save_change');?></button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="inv_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_transaction_items');?></h4><!--Items-->
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 pt-3">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h4><?php echo $this->lang->line('sales_markating_transaction_search_item');?>  </h4><!--Search Item-->
                            </div>
                            <!-- <div class="box-footer no-padding">
                                <label class="control-label"><?php echo $this->lang->line('sales_markating_transaction_item_search');?> </label>
                                <input type="text" class="form-control" name="grv_item" id="grv_item"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description');?> ...">
                            </div>
                            <div class="box-footer no-padding">
                                <label class="control-label"><?php echo $this->lang->line('sales_markating_transaction_invoice_search');?> </label>
                                <input type="text" class="form-control" name="invoice_code" id="invoice_code"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_invoice_id');?> ...">
                            </div>  -->

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="searchByItemInvice" id="searchItem" value="1" checked>
                                <label class="control-label" for="searchItem">
                                    <?php echo $this->lang->line('sales_markating_transaction_search_item');?> 
                                </label>
                                <input type="text" class="form-control" name="grv_item" id="grv_item"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description');?> ...">
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="searchByItemInvice" id="searchInvoice" value="2">
                                <label class="control-label" for="searchInvoice">
                                    <?php echo $this->lang->line('sales_markating_transaction_invoice_search');?>
                                </label>
                                <div style="display:flex" class="hide" id="invoice_code">
                                    <input type="text" class="form-control" name="invoice_code" id="invoice_code_text"
                                        placeholder="<?php echo $this->lang->line('sales_markating_transaction_invoice_id');?> ...">
                                    <button class="btn btn-success btn-theme" onclick="getInvoiceItem()"><i class="fa fa-arrow-right"></i></button>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>

                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 40%"><?php echo $this->lang->line('sales_markating_transaction_header_info');?> </th><!--Header Info-->
                                <th style="width: 30%"><?php echo $this->lang->line('sales_markating_transaction_secondary_item_description');?></th><!--Item Description-->
                                <th style="width: 15%"><?php echo $this->lang->line('sales_markating_transaction_sales_price');?> </th><!--Sales Price-->
                                <th style="width: 15%">Unit of Measure</th>
                                <th style="width: 5%"><?php echo $this->lang->line('sales_markating_transaction_balance');?></th><!--Balance-->
                                <th style="width: 5%"><?php echo $this->lang->line('sales_markating_transaction_return');?> </th><!--Return-->
                            </tr>
                            </thead>
                            <tbody id="item_cinv_table_body">
                            <tr class="danger">
                                <td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                <button type="button" class="btn btn-primary" onclick="save_sales_return_detail()"><?php echo $this->lang->line('common_save_change');?> </button><!--Save changes-->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var salesReturnAutoID;
    var salesReturnDetailsID;
    var customerID;
    var wareHouseLocation;
    $(document).ready(function () {
        salesReturnAutoID = <?php echo json_encode(trim($master['salesReturnAutoID'] ?? '')); ?>;
        salesReturnDetailsID = null;
        customerID = <?php echo json_encode(trim($master['customerID'] ?? '')); ?>;
        wareHouseLocation = <?php echo json_encode(trim($master['wareHouseLocation'] ?? '')); ?>;
        initializeitemTypeahead();
        fetch_sales_return_details();
        $('#item_detail_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                search: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}},/*Item is required*/
                itemSystemCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}},/*Item is required.*/
                itemAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}},/*Item is required*/
                itemDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}},/*Item is required*/
                UnitOfMeasure: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_uom_is_required');?>.'}}},/*Unit Of Measure is required*/
                return_Qty: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_qty_retutn_is_required');?>.'}}},/*Quantity Return is required*/
                estimatedAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_estimated_amount_is_required');?>.'}}}/*Estimated Amount is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            if (salesReturnAutoID) {
                data.push({'name': 'salesReturnAutoID', 'value': salesReturnAutoID});
                data.push({'name': 'salesReturnDetailsID', 'value': salesReturnDetailsID});
                $.ajax(
                    {
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Inventory/save_return_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            $form.bootstrapValidator('resetForm', true);
                            salesReturnDetailsID = null;
                            $('#item_detail_modal').modal('hide');
                            stopLoad();
                            refreshNotifications(true);
                            if (data['status']) {
                                fetch_sales_return_details();
                            }
                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                    });
            } else {
                swal({
                    title: "Good job!",
                    text: "You clicked the button!",
                    type: "success"
                });
            }
        });
    });

    function initializeitemTypeahead() {
        var itemSelected; // store selected item in typehead - added by mubashir
        var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?column=allowedtoSellYN&q=%QUERY"
        });

        item.initialize();
        $('#search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#itemSystemCode').val(datum.itemSystemCode);
            $('#itemAutoID').val(datum.itemAutoID);
            $('#itemDescription').val(datum.itemDescription);
            $('#defaultUOM').val(datum.defaultUnitOfMeasure);
            //$('#currentWac').text(datum.companyLocalWacAmount);
            fetch_related_uom(datum.defaultUnitOfMeasure, datum.defaultUnitOfMeasure);
            $('#item_detail_form').bootstrapValidator('revalidateField', 'itemSystemCode');
            $('#item_detail_form').bootstrapValidator('revalidateField', 'itemAutoID');
            $('#item_detail_form').bootstrapValidator('revalidateField', 'itemDescription');
            $('#item_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasure');
        });

        var myTypeAhead = $('#grv_item').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            itemSelected = datum.itemAutoID;
            fetch_item_for_sales_return(itemSelected);

        });

        $("#grv_item").change(function () { // make table empty after deleting the item description - added by mubashir
            if (!this.value) {
                itemSelected = "";
                $('#item_cinv_table_body').html('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
            }
        });<!--No Records Found-->

        $("#inv_base_modal").on('shown.bs.modal', function () { // load item when modal loads - added by mubashir
            fetch_item_for_sales_return(itemSelected);
        });
    }

    function fetch_item_for_sales_return(itemAutoID) {

        var searchByItemInvice = $('input[name="searchByItemInvice"]:checked').val();
        var invoice_code = $('#invoice_code_text').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'salesReturnAutoID': salesReturnAutoID,
                'itemAutoID': itemAutoID,
                'customerID': customerID,
                'wareHouseLocation': wareHouseLocation,
                'searchByItemInvice': searchByItemInvice,
                'invoice_code': invoice_code
            },
            url: "<?php echo site_url('Inventory/fetch_item_for_sales_return'); ?>",
            beforeSend: function () {
                $('#item_cinv_table_body').hide();
            },
            success: function (data) {
                $('#item_cinv_table_body').empty();
                if (!jQuery.isEmptyObject(data)) {
                    var x = 1;
                    $.each(data, function (val, text) {
                        var detID = text['invoiceDetailsAutoID'];
                        var str = '<tr><td>' + x + '</td><td>' + text['invoiceCode'] + ' - ' + text['transactionCurrency'] + ' - ' + text['invoiceDate'] + '</td>';
                        str += '<td>' + text['itemSystemCode'] + ' - ' + text['itemDescription'] + '</td><td class="text-right">' + parseFloat(text['transactionAmount']).formatMoney(text['transactionCurrencyDecimalPlaces'], '.', ',') +' <td>' + text['unitOfMeasure'] + '</td>';
                        str +='<td id="bal_'+detID+'" class="text-right">' + text['requestedQty'] + ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + detID + ',' + text['requestedQty'] + ')" aria-hidden="true"></i></td>';
                        str += '<td class="text-center"><input type="text" class="number" size="15" id="qty_' + detID + '" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="select_check_box(this,' + detID + ',' + text['requestedQty'] + ')"></td>';
                        str += '<td class="text-right" style="display: none;"><input class="checkbox" id="check_' + detID + '" type="checkbox" value="' + detID + '" data-type="' + text['documentID'] + '"></td></tr>';

                        $('#item_cinv_table_body').append(str);
                        x++;
                    });
                } else {
                    $('#item_cinv_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                }

                $('#item_cinv_table_body').fadeIn();
                /*No Records Found*/
            }, error: function () {
                $('#item_cinv_table_body').fadeIn();
                $('#item_cinv_table_body').empty().append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                /*swal("Cancelled", "Your " + value + " file is safe :)", "error");*/
            }
        });
    }

    function select_check_box(data, id, receivedQty) {
        $("#check_" + id).prop("checked", false);
        if (data.value > receivedQty) {
            $("#qty_" + id).val('');
            myAlert('w', "You cannot return more than balance Qty", 1000);
            //alert('Received Qty Smaller than you return');
        } else {

            if (data.value > 0) {
                $("#check_" + id).prop("checked", true);
            }
        }
    }

    function fetch_related_uom(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                $('#UnitOfMeasure').empty();
                var mySelect = $('#UnitOfMeasure');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitShortCode']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#UnitOfMeasure").val(select_value);
                        $('#item_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasure');
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_sales_return_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'salesReturnAutoID': salesReturnAutoID},
            url: "<?php echo site_url('Inventory/fetch_sales_return_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                $('#item_table_body,#invoice_table_body,#invoice_table_tfoot').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $("#customerID").prop("disabled", false);
                    $("#returnDate").prop("readonly", false);
                    $("#location").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                    $('#item_table_body,#invoice_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                } else {
                    $("#returnDate").prop("readonly", true);
                    $("#customerID").prop("disabled", true);
                    $("#location").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                    currency_decimal = 2;
                    var x = 1;
                    var y = 1;


                    $.each(data['detail'], function (key, value) {

                        $('#item_table_tfoot').empty();
                        var row = '<tr><td>' + x + '</td><td>' + value['mas_code'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-right">' + parseFloat(value['salesPrice']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-center">' + value['return_Qty'] + '</td><td class="text-right">' + parseFloat(value['taxAmount']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalFinal']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a onclick="delete_item(' + value['salesReturnDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        $('#invoice_table_body').append(row);

                        x++;

                        total += parseFloat(value['totalFinal']);

                        /*if (value['type'] == 'Item') {

                         } else if (value['type'] == 'GRV') {
                         if (value['isSubitemExist'] == 1) {
                         var colour = 'color: #dad835 !important';
                         colour = '';


                         string = '<tr><td>' + y + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['currentlWacAmount'] + '</td><td class="text-center">' + value['return_Qty'] + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['salesReturnDetailsID'] + ',\'SLR\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;  <a onclick="delete_item(' + value['salesReturnDetailsID'] + ',\'' + value['itemDescription'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                         } else {
                         string = '<tr><td>' + y + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['currentlWacAmount'] + '</td><td class="text-center">' + value['return_Qty'] + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a onclick="delete_item(' + value['salesReturnDetailsID'] + ',\'' + value['itemDescription'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';

                         }

                         $('#invoice_table_tfoot').empty();
                         $('#invoice_table_body').append(string);
                         y++;

                         total += parseFloat(value['totalValue']);
                         }*/
                    });
                    $('#invoice_table_tfoot').html('<tr> <td class="text-right" colspan="8"> Total</td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>')
                    $('#item_table_tfoot').html('<tr> <td class="text-right" colspan="6"> Total</td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>')
                }

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_item(id) {
        if (salesReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?> " ,/*You want to delete*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>"/*Delete*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'salesReturnDetailsID': id},
                        url: "<?php echo site_url('Inventory/delete_sales_return_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            fetch_sales_return_details();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function item_detail_modal() {
        if (salesReturnAutoID) {
            $('#item_detail_form')[0].reset();
            $('#item_detail_form').bootstrapValidator('resetForm', true);
            $("#item_detail_modal").modal({backdrop: "static"});
        }
    }

    function save_sales_return_detail() {
        var selected = [];
        var qty = {};
        var doc_id = [];

        $('#item_cinv_table_body input:checked').each(function () {
            var sl_id = $(this).val();
            var thisType = $(this).attr('data-type');
            selected.push( sl_id );
            doc_id.push( thisType );
            qty[thisType+'_'+sl_id] = $('#qty_' + sl_id).val();
        });

        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'invoiceDetailsAutoID': selected, 'salesReturnAutoID': salesReturnAutoID, 'qty': qty, 'doc_id':doc_id},
                url: "<?php echo site_url('Inventory/save_sales_return_detail_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#inv_base_modal').modal('hide');
                        fetch_sales_return_details();
                    }

                }, error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('sales_markating_transaction_an_error_has_occurred_please_contact_your_system_support_team');?>.')/*An error has occurred!, Please contact your system support team*/
                }
            });
        } else {
            myAlert('w', '<?php echo $this->lang->line('sales_markating_transaction_an_error_has_occurred_please_type_return_amount');?>', 100);/*Please type return amount*/
        }
    }

    function setQty(detailID,Qty){
        var ordQtyId = "#qty_"+detailID;
        $(ordQtyId).val(Qty);
        var data = {value:Qty};
        select_check_box(data,detailID,Qty);
    }

    //////////////////////////////////////////

    $('input[type=radio][name=searchByItemInvice]').change(function() {
        if (this.value == '1') {
           $('#grv_item').removeClass('hide');
           $('#invoice_code').addClass('hide');
        }
        else if (this.value == '2') {
            $('#grv_item').addClass('hide');
            $('#invoice_code').removeClass('hide');
        }
    });

    ////////////////////////////////////////////

    function getInvoiceItem(){

        fetch_item_for_sales_return('')

    }
</script>  