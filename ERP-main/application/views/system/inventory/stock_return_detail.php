<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

$umo_arr = array('' => 'Select UOM');
$segment_arr = fetch_segment();

?>
<div class="row">
    <button style="margin-right: 15px" type="button" class="btn btn-primary pull-right "
            onclick="openInvBasedModal()"><i class="fa fa-plus"></i><?php echo $this->lang->line('common_add_item'); ?>
    </button>
</div>
<hr style="margin-top: 10px">


<table class="table table-bordered table-striped table-condesed">
<thead>
    <tr>
        <th style="min-width: 5%">#</th>
        <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
        <th style="min-width: 30%" class="text-left"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
        <th style="min-width: 10%"><?php echo $this->lang->line('common_uom');?> </th><!--UOM-->
        <th style="min-width: 10%"><?php echo $this->lang->line('common_cost');?> </th><!--Cost-->
        <th style="min-width: 10%">Qty </th><!--Return-->
        <th style="min-width: 10%" class="taxGrpPolicy hide">Total</th><!--Return-->
        <th style="min-width: 10%" class="taxGrpPolicy hide">Tax</th>
        <th style="min-width: 10%" class="taxGrpPolicy hide">Tax Amount</th>
        <th style="min-width: 15%"><?php echo $this->lang->line('common_value');?> </th><!--Value-->
        <th style="min-width: 5%">&nbsp;</th>
    </tr>
</thead>
    <tbody id="invoice_table_body">
    <tr class="danger">
        <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
    </tr>
    </tbody>
    <tfoot id="invoice_table_tfoot">

    </tfoot>
</table>

<div class="nav-tabs-custom hide">
    <ul class="nav nav-tabs pull-right">
        <li class="active"><a data-toggle="tab" href="#tab_1" aria-expanded="false"><?php echo $this->lang->line('transaction_common_grv');?><!--GRV--></a></li>
        <!-- <li class=""><a data-toggle="tab" href="#tab_2" aria-expanded="false">Item</a></li> -->
        <!--<li class="pull-left header"><i class="fa fa-hand-o-right"></i>Payment for : - <?php /*echo $master['supplierName']; */ ?></li>-->
    </ul>
    <div class="tab-content">
        <div id="tab_1" class="tab-pane active">
        </div><!-- /.tab-pane -->
        <div id="tab_2" class="tab-pane">
            <table class="table table-bordered table-striped table-condesed">
                <thead>
                <tr>
                    <th colspan="5"><?php echo $this->lang->line('transaction_common_item_details');?> </th><!--Item Details-->
                    <th colspan="2">
                        <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right btn-xs"><i
                                class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item');?>
                        </button><!--Add Item-->
                    </th>
                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%"> <?php echo $this->lang->line('common_code');?> </th><!--Code-->
                    <th style="min-width: 40%" class="text-left"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_uom');?> </th><!--UOM-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_qty');?> </th><!--Qty-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_value');?> </th><!--Value-->
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
    <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous');?> </button><!--Previous-->
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_common_add_item_detail');?> </h5><!--Add Item Detail-->
            </div>
            <form role="form" id="item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_common_item_code');?>  <?php required_mark(); ?></label><!--Item Code-->
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="search" id="search"
                                   placeholder="Item ID, Item Description...">
                            <input type="hidden" class="form-control" id="itemSystemCode" name="itemSystemCode">
                            <input type="hidden" class="form-control" id="itemAutoID" name="itemAutoID">
                            <input type="hidden" class="form-control" id="itemDescription" name="itemDescription">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_uom');?> <?php required_mark(); ?></label><!--UOM-->
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
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_return_qty');?> <?php required_mark(); ?></label><!--Return Qty-->
                        <div class="col-sm-4">
                            <input type="text" name="return_Qty" id="return_Qty" placeholder="0.00"
                                   class="form-control number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_comment');?> </label><!--Comment-->
                        <div class="col-sm-6">
                            <textarea class="form-control" rows="3" name="comment" placeholder="<?php echo $this->lang->line('transaction_common_item_comment');?>..."
                                      id="comment"></textarea><!--Item Comment-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                    <button class="btn btn-primary" type=""><?php echo $this->lang->line('common_save_change');?> </button><!--Save changes-->
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
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_add_item');?> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h4><?php echo $this->lang->line('transaction_common_search_item');?> </h4><!--Search Item-->
                            </div>
                            <div class="box-footer no-padding">
                                <!-- <ul class="nav nav-stacked" id="grv_list">
                                    <li><a>No Records found</a></li>
                                </ul> -->
                                <input type="text" class="form-control" name="grv_item" id="grv_item"
                                       placeholder="<?php echo $this->lang->line('common_item_id');?>,<?php echo $this->lang->line('common_item_description');?> ..."><!--Item ID--><!--Item Description-->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <!-- <tr>
                                <th colspan="3">Item Details</th>
                                <th colspan="3">Qty </th>
                            </tr> -->
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 25%"><?php echo $this->lang->line('common_document');?> <!--Document--> </th>
                                <th style="width: 30%"><?php echo $this->lang->line('transaction_common_item_description');?> </th><!--Item Description-->
                                <th style="width: 11%"><?php echo $this->lang->line('transaction_item_cost');?> </th><!--Item Cost-->
                                <th style="width: 5%"><?php echo $this->lang->line('transaction_balance');?> </th><!--Balance-->
                                <th style="width: 10%"><?php echo $this->lang->line('transaction_common_return');?> </th><!--Return-->
                            </tr>
                            </thead>
                            <tbody id="grv_table_body">
                            <tr class="danger">
                                <td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                <button type="button" class="btn btn-primary" onclick="save_grv_base_items()"><?php echo $this->lang->line('common_save_change');?> </button><!--Save changes-->
            </div>
        </div>
    </div>
</div>
<?php
$data['documentID'] = 'PR';
$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);

?>

<script type="text/javascript">
    var stockReturnAutoID;
    var stockReturnDetailsID;
    var supplierID;
    var wareHouseLocation;
    var isGroupBasedTax = 0;
    $(document).ready(function () {
        stockReturnAutoID = <?php echo json_encode(trim($master['stockReturnAutoID'] ?? '')); ?>;
        stockReturnDetailsID = null;
        supplierID = <?php echo json_encode(trim($master['supplierID'] ?? '')); ?>;
        wareHouseLocation = <?php echo json_encode(trim($master['wareHouseLocation'] ?? '')); ?>;
        initializeitemTypeahead();
        fetch_return_direct_details();
        number_validation();
        $('#item_detail_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                search: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_supplier_item_is_required');?>.'}}},/*Item is required*/
                itemSystemCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_supplier_item_is_required');?>.'}}},/*Item is required*/
                itemAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_supplier_item_is_required');?>.'}}},/*Item is required*/
                itemDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_supplier_item_is_required');?>.'}}},/*Item is required*/
                UnitOfMeasure: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_unit_of_measure_is_required');?>.'}}},/*Unit Of Measure is required*/
                return_Qty: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_qty_is_required');?>.'}}},/*Quantity Return is required*/
                estimatedAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_estimated_amount_is_required');?>.'}}}/*Estimated Amount is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            if (stockReturnAutoID) {
                data.push({'name': 'stockReturnAutoID', 'value': stockReturnAutoID});
                data.push({'name': 'stockReturnDetailsID', 'value': stockReturnDetailsID});
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
                            stockReturnDetailsID = null;
                            $('#item_detail_modal').modal('hide');
                            stopLoad();
                            refreshNotifications(true);
                            if (data['status']) {
                                fetch_return_direct_details();
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
            remote: '<?php echo site_url();?>Procurement/fetch_itemrecode/?column=allowedtoBuyYN&q=%QUERY',
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
            fetch_item_for_grv(itemSelected);
            // $('#itemSystemCode').val(datum.itemSystemCode);
            // $('#itemAutoID').val(datum.itemAutoID);
            // $('#itemDescription').val(datum.itemDescription);
            // $('#defaultUOM').val(datum.defaultUnitOfMeasure);
            // fetch_related_uom(datum.defaultUnitOfMeasure,datum.defaultUnitOfMeasure);
            // $('#item_detail_form').bootstrapValidator('revalidateField', 'itemSystemCode');
            // $('#item_detail_form').bootstrapValidator('revalidateField', 'itemAutoID');
            // $('#item_detail_form').bootstrapValidator('revalidateField', 'itemDescription');
            // $('#item_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasure');
        });

        $("#grv_item").change(function () { // make table empty after deleting the item description - added by mubashir
            if (!this.value) {
                itemSelected = "";
                $('#grv_table_body').html('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
            }
        });

        /*$("#inv_base_modal").on('shown.bs.modal', function () { // load item when modal loads - added by mubashir
            fetch_item_for_grv(itemSelected);
        });*/
    }

    function fetch_item_for_grv(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'stockReturnAutoID': stockReturnAutoID,
                'itemAutoID': itemAutoID,
                'supplierID': supplierID,
                'wareHouseLocation': wareHouseLocation
            },
            url: "<?php echo site_url('Inventory/fetch_item_for_grv'); ?>",
            success: function (data) {
                $('#grv_table_body').empty();
                if (!jQuery.isEmptyObject(data)) {
                    var x = 1;
                    $.each(data, function (val, text) {
                        $('#grv_table_body').append('<tr><td>' + x + '</td><td>' + text['grvPrimaryCode'] + ' - ' + text['transactionCurrency'] + ' - ' + text['grvDate'] + '</td><td>' + text['itemSystemCode'] + ' - ' + text['itemDescription'] + '</td><td class="text-right">' + text['unitOfMeasure'] + ' ' + parseFloat(text['receivedAmount']).formatMoney(text['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right">' + text['receivedQty'] + ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + text['grvDetailsID'] + ',' + text['receivedQty'] + ')" aria-hidden="true"></i></td><td class="text-center"> <input type="text" class="number" size="15" id="qty_' + text['grvDetailsID'] + '" onkeyup="select_check_box(this,' + text['grvDetailsID'] + ',' + text['receivedQty'] + ')"></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + text['grvDetailsID'] + '" type="checkbox" value="' + text['grvDetailsID'] + '"><input type="hidden" id="type_' + text['grvDetailsID'] + '"  value="' + text['type'] + '"></td></tr>');
                        x++;
                    });
                } else {
                    $('#grv_table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                }
                number_validation();
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function select_check_box(data, id, receivedQty) {
        $("#check_" + id).prop("checked", false);
        if (data.value > receivedQty) {
            $("#qty_" + id).val('');
            myAlert('w', "<?php echo $this->lang->line('transaction_you_canot_return_more_than_balance_qty');?>", 1000);/*You cannot return more than balance Qty*/
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

    function fetch_return_direct_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'stockReturnAutoID': stockReturnAutoID},
            url: "<?php echo site_url('Inventory/fetch_return_direct_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                $('#item_table_body,#invoice_table_body').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $("#supplierID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $('#invoice_table_tfoot').empty();
                    $('#item_table_body,#invoice_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                } else {
                    $("#supplierID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    currency_decimal = 2;
                    var x = 1;
                    var y = 1;
                    var tax = '';
                    var taxAmount = '';
                    var wacAmount = '';
                    var totalwithouttax = '';
                    var taxVatAmount = 0;
                    $.each(data['detail'], function (key, value) {
                        taxVatAmount += value['taxAmount'];
                        wacAmount = '<td class="text-center">' + value['currentlWacAmount'] + '</td>';

                        if (value['type'] == 'rrr') {
                            $('#item_table_tfoot').empty();
                            $('#item_table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['return_Qty'] + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(tran, '.', ',') + '</td><td class="text-right"><a onclick="delete_item(' + value['stockReturnDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;

                            total += parseFloat(value['totalValue']);
                        } else if (value['type'] == 'GRV' || value['type'] == 'BSI') {


                            if(taxVatAmount != 0){
                                isGroupBasedTax = 1
                            }else {
                                isGroupBasedTax = 0;
                            }
                            if(isGroupBasedTax == 1){
                                tax ='<td>'+value['Description']+' </td>';


                                taxAmount = '<td class="text-right"><a onclick="open_tax_dd(\'\',' + stockReturnAutoID + ',\'PR\',' + value['transactionCurrencyDecimalPlaces'] +', '+ value['stockReturnDetailsID'] +', \'srp_erp_stockreturndetails\', \'stockReturnDetailsID\',0,1)">'+ parseFloat(value['taxAmount']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</a></td>';

                                wacAmount = '<td class="text-center">' + value['currentlWacAmountTaxGroupEnable'] + '</td>';
                                totalwithouttax = '<td class="text-right">' + parseFloat(value['totalValueTaxGroupEnable']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td>';
                                $('.taxGrpPolicy').removeClass('hide');
                            } else {
                                $('.taxGrpPolicy').addClass('hide');
                            }

                            if (value['isSubitemExist'] == 1) {
                                var colour = 'color: #dad835 !important';
                                colour = '';


                                string = '<tr><td>' + y + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td>'+wacAmount+'<td class="text-center">' + value['return_Qty'] + '</td>'+totalwithouttax+' '+tax+''+taxAmount+'<td class="text-right">' + parseFloat(value['totalValue']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['stockReturnDetailsID'] + ',\'SR\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;  <a onclick="delete_item(' + value['stockReturnDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            } else {
                                string = '<tr><td>' + y + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td> '+wacAmount+' <td class="text-center">' + value['return_Qty'] + '</td>'+totalwithouttax+''+tax+''+taxAmount+'<td class="text-right">' + parseFloat(value['totalValue']).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a onclick="delete_item(' + value['stockReturnDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';

                            }

                            $('#invoice_table_tfoot').empty();
                            $('#invoice_table_body').append(string);
                            y++;

                            total += parseFloat(value['totalValue']);
                        }
                    });

                    if(isGroupBasedTax == 1){
                        $('#invoice_table_tfoot').html('<tr> <td class="text-right" colspan="9"><?php echo $this->lang->line('common_total');?> </td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>')
                    }else {
                        $('#invoice_table_tfoot').html('<tr> <td class="text-right" colspan="6"><?php echo $this->lang->line('common_total');?> </td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>')
                    }




                    $('#item_table_tfoot').html('<tr> <td class="text-right" colspan="6"><?php echo $this->lang->line('common_total');?> </td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>')
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
        if (stockReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'stockReturnDetailsID': id},
                        url: "<?php echo site_url('Inventory/delete_return_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            fetch_return_direct_details();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function item_detail_modal() {
        if (stockReturnAutoID) {
            $('#item_detail_form')[0].reset();
            $('#item_detail_form').bootstrapValidator('resetForm', true);
            $("#item_detail_modal").modal({backdrop: "static"});
        }
    }

    function save_grv_base_items() {
        var selected = [];
        var qty = [];
        var types = [];
        $('#grv_table_body input:checked').each(function () {
            selected.push($(this).val());
            qty.push($('#qty_' + $(this).val()).val());
            types.push($('#type_' + $(this).val()).val());
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'grvDetailsID': selected, 'stockReturnAutoID': stockReturnAutoID, 'qty': qty, 'types': types},
                url: "<?php echo site_url('Inventory/save_grv_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#inv_base_modal').modal('hide');
                    refreshNotifications(true);
                    fetch_return_direct_details();
                }, error: function () {
                    $('#inv_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function openInvBasedModal(){
        $('#inv_base_modal').modal('show');
        $('#grv_table_body').empty();
        $('#grv_item').val('');
    }

    function setQty(detailID,Qty){
        var ordQtyId = "#qty_"+detailID;
        $(ordQtyId).val(Qty);
        var data = {value:Qty};
        select_check_box(data,detailID,Qty);
    }
</script>  