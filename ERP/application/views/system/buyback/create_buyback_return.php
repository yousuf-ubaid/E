<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('buyback_helper');
$title = $this->lang->line('sales_markating_transaction_create_sales_return');
echo head_page('Create Return', false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$customer_arr = all_customer_drop();
$farms_arr = load_all_farms();
$batch_arr = array(''=> 'Select Batch');
$segment_arr = fetch_segment();
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>


<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1"
       data-toggle="tab"> Step 1 - Return Header </a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail()"
       data-toggle="tab">Step 2 - Return Detail </a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();"
       data-toggle="tab">Step 3 - Return Confirmation</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="sales_return_form"'); ?>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>RETURN HEADER</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Farmer</label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                             <?php echo form_dropdown('farmID', $farms_arr, '', 'class="form-control select2" id="farmID" onchange="fetch_farmer_currencyID(this.value),fetch_farmBatch(this.value)" required'); ?>
                             <span class="input-req-inner"></span>
                         </span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Batch</label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                             <div id="batchload">
                                 <?php echo form_dropdown('batchMasterID', $batch_arr, 'Each', 'class="form-control select2" id="batchMasterID" '); ?>
                             </div>
                             <span class="input-req-inner"></span>
                         </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Return Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                             <div class="input-group datepic">
                                     <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                     <input type="text" name="returnDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                            value="<?php echo $current_date; ?>" id="returnDate"
                                            class="form-control" required>
                             </div>
                             <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Document Date</label>
                    </div>

                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                             <div class="input-group datepic">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" name="documentdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="documentdate"
                                   class="form-control" required>
                             </div>
                             <span class="input-req-inner" style="z-index: 100"></span>
                         </span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Reference No</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="referenceNo" name="referenceNo">
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Warehouse Location</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php echo form_dropdown('location', $location_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="location" required'); ?>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Segment</label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment"'); ?>
                     <span class="input-req-inner"></span>
                            </span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Narration</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Financial Year</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Financial Period</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('financeyear_period', array('' => 'Finance Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Currency</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr,'', 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation_modal(this.value,\'BBDR\',\'\',\'\')" required'); ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button class="btn btn-primary pull-right" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;
                    <li class="pull-left header"><i class="fa fa-hand-o-right"></i>Return For : <span id="farmerNameReturn"></span></li>
                </h4></div>
            <div class="col-md-4">
                <button type="button" onclick="add_dispatch_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Dispatch
                </button>
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th colspan="4">Item Details</th>

                <th colspan="1">Qty</th>
                <th colspan="2">Amount</th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%">Item Code </th>
                <th style="min-width: 30%">Item Description </th>
                <th style="min-width: 10%">UOM</th>
                <th style="min-width: 15%">Return</th>
                <th style="min-width: 15%">Unit</th>
                <th style="min-width: 15%">Total</th>
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="7" class="text-center"><b>No Records Found</b>
                </td>
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>

        </div>
    </div>
    <div id="step3" class="tab-pane">
        <!--    <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="<?php /*echo site_url('Double_entry/fetch_double_entry_sales_return/'); */ ?>"><span class="glyphicon glyphicon-random" aria-hidden="true"></span>  &nbsp;&nbsp;&nbsp;Account Review entries
                </a>
                <a class="btn btn-default btn-sm" id="a_link" target="_blank" href="<?php /*echo site_url('Inventory/load_sales_return_conformation/'); */ ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div><hr>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title"
                id="buybackReturn_attachment_label"> Modal title </h4>

            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="buybackReturn_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5"
                            class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?> </td>
                        <!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <!--<button class="btn btn-default prev">Previous XX</button>-->
            <button class="btn btn-primary "
                    onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?></button>
            <!--Save as Draft-->
            <button class="btn btn-success submitWizard"
                    onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?></button><!--Confirm-->
        </div>
    </div>
</div>

<div class="modal fade" id="dispatch_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <b>Dispatch</b> &nbsp;&nbsp; <input id="farmerBatchName" name="farmerBatchName" size="100" style="border: none" readonly>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h5>Dispatch</h5>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked" id="dispatchcode">


                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan='4'>Item</th>
                                <th colspan='2'>Dispatch Item <span
                                            class="currency"> </span></th>
                                <th colspan='2'>Return Item <span
                                            class="currency"> </span></th>
                            <tr>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th class="text-left">Description</th>
                                <th>UOM</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>Return Qty</th>
                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_pr_detail">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="save_return_items()">Save changes</button>
            </div>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var returnAutoID;
    var batchid;
    var salesreturnDetailsID;
    var documentCurrency;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/buyback/buyback_return', '', 'Return ')
        });
        $('.select2').select2();
        number_validation();
        returnAutoID = null;
        batchid = null;
        salesreturnDetailsID = null;
        documentCurrency = null;
        //initializeitemTypeahead();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#sales_return_form').bootstrapValidator('revalidateField', 'returnDate');
        });
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            returnAutoID = p_id;
            load_return_header();
             $("#a_link").attr("href","<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + returnAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatch_return'); ?>/" + returnAutoID + '/BBDRN');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            var CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID, 'BBDRN', '', '');
        }

        var FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        var DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        var periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);
        $('#sales_return_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                financeyear: {validators: {notEmpty: {message: 'Financial Year is required.'}}},
                financeyear_period: {validators: {notEmpty: {message: 'Financial Period is required.'}}},
                returnDate: {validators: {notEmpty: {message: 'Return Date is required.'}}},
                location: {validators: {notEmpty: {message: 'Location is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Customer Currency is required.'}}},
                narration: {validators: {notEmpty: {message: 'Narration is required.'}}},
                documentdate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
                segment: {validators: {notEmpty: {message: 'Segment is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#batchMasterID").prop("disabled", false);
            $("#returnDate").prop("disabled", false);
            $("#documentdate").prop("disabled", false);
            $("#farmID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#financeyear").prop("disabled", false);
            $("#financeyear_period").prop("disabled", false);
            $("#segment").prop("disabled", false);
            $("#location").prop("disabled", false);

            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'Returnautoid', 'value': returnAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/save_return_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data['s']) {
                        $("#batchMasterID").prop("disabled", true);
                        $("#returnDate").prop("disabled", true);
                        $("#documentdate").prop("disabled", true);
                        $("#farmID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#financeyear").prop("disabled", true);
                        $("#financeyear_period").prop("disabled", true);
                        $("#segment").prop("disabled", true);
                        $("#location").prop("disabled", true);
                    }
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        returnAutoID = data['last_id'];
                        batchid = data['batchid'];
                        $('#farmerNameReturn').text(data['farmerName']);
                        $("#a_link").attr("href", "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + returnAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatch_return'); ?>/" + returnAutoID + '/BBDRN');
                        $("#transactionCurrencyID").prop("disabled", true);
                        fetch_detail();
                        $('[href=#step2]').tab('show');
                    };
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });
    });

    function load_return_header() {
        if (returnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'returnAutoID': returnAutoID},
                url: "<?php echo site_url('Buyback/load_bubyack_return_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        documentCurrency = data['transactionCurrencyID'];
                        returnAutoID = data['returnAutoID'];
                        $('#returnDate').val(data['returnedDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#location").val(data['wareHouseAutoID']).change();
                        $('#farmID').val(data['farmID']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#narration').val(data['Narration']);
                        $('#referenceNo').val(data['referenceNo']);
                        $('#farmerNameReturn').text(data['farmName']);
                        batchid = data['batchMasterID'];
                        setTimeout(function () {
                            $('#batchMasterID').val(data['batchMasterID']).change();
                        }, 500);
                        fetch_detail();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }


    function fetch_detail() {
        if (returnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'returnAutoID': returnAutoID},
                url: "<?php echo site_url('Buyback/fetch_return_table_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#item_table_body').empty();
                    $('#table_tfoot').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $("#batchMasterID").prop("disabled", false);
                        $("#returnDate").prop("disabled", false);
                        $("#documentdate").prop("disabled", false);
                        $("#farmID").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $("#financeyear").prop("disabled", false);
                        $("#financeyear_period").prop("disabled", false);
                        $("#segment").prop("disabled", false);
                        $("#location").prop("disabled", false);
                        $('#item_table_body').append('<tr class="danger"><td colspan="10" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $("#batchMasterID").prop("disabled", true);
                        $("#returnDate").prop("disabled", true);
                        $("#documentdate").prop("disabled", true);
                        $("#farmID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#financeyear").prop("disabled", true);
                        $("#financeyear_period").prop("disabled", true);
                        $("#segment").prop("disabled", true);
                        $("#location").prop("disabled", true);
                        tot_amount = 0;
                        $.each(data['detail'], function (key, value) {
                            $('#item_table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['description'] +'</td><td>' + value['unitOfMeasure'] +'</td><td class="text-right">' + value['qty'] +'</td><td class="text-right"> ' + parseFloat(value['unitTransferCost']).formatMoney(2, '.', ',') + '</td> <td class="text-right"> ' + parseFloat(value['totalTransferCost']).formatMoney(2, '.', ',') + '</td>  <td class="text-right"><a onclick="delete_buyback_return(' + value['returnDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                            x++;
                            tot_amount += parseFloat(value['totalTransferCost']);
                        });
                        $('#table_tfoot').append('<tr><td colspan="6" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                    }

                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function load_conformation() {
        if (returnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'returnAutoID': returnAutoID, 'html': true},
                url: "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href","<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + returnAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Buyback/fetch_double_entry_buyback_dispatch_return'); ?>/" + returnAutoID + '/BBDRN');
                    attachment_modal_return(returnAutoID, "Return", "BBDR");
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }
    }


    function confirmation() {
        if (returnAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to confirm this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'returnAutoID': returnAutoID},
                        url: "<?php echo site_url('Buyback/buyback_return_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();

                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 2) {
                                myAlert('w', data['message']);
                            }
                            else {
                                refreshNotifications(true);
                                myAlert('s', data['message'])
                                fetchPage('system/buyback/buyback_return', '', 'Return ')
                            }

                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function save_draft() {
        if (returnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/buyback/buyback_return', '', 'Return ')
                });
        }
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Finance Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function attachment_modal_return(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#buybackReturn_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " Attachments ");

                    $('#buybackReturn_attachment').empty();
                    $('#buybackReturn_attachment').append('' +data+ '');


                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function fetch_farmer_currencyID(farmID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'farmID': farmID},
            url: "<?php echo site_url('Buyback/fetch_farmer_currencyID'); ?>",
            success: function (data) {
                /*   if (documentCurrency) {
                       $("#transactionCurrencyID").val(documentCurrency).change()
                   } else {*/
                if (data.farmerCurrencyID) {
                    $("#transactionCurrencyID").val(data.farmerCurrencyID).change();

                }

            }
        });
    }
    function fetch_farmBatch(farmID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/fetch_farm_BatchesDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#batchload').html(data);
              //  $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function add_dispatch_modal() {
        if (returnAutoID) {
            load_dispatch_codes();
            farmBatchName();
            $("#dispatch_base_modal").modal({backdrop: "static"});
        }
    }
    function load_dispatch_codes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'batchid': batchid},
            url: "<?php echo site_url('Buyback/fetch_dispatch_codes'); ?>",
            success: function (data) {
                $('#dispatchcode').empty();
                $('#table_body_pr_detail').empty();
                var mySelect = $('#dispatchcode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        var bal=value['transfercos']-value['requestedm'];
                        if(bal>0){
                            mySelect.append('<li title="Dispatch Date :- '+ value['documentDate'] +' "  rel="tooltip"><a onclick="fetch_dispatchreturn_detail_table(' + value['dispatchAutoID'] + ')">' + value['documentSystemCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
                            $("[rel=tooltip]").tooltip();
                        }

                    });
                } else {
                    mySelect.append('<li><a>No Records found</a></li>');
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }

        });

    }
    function farmBatchName()
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'batchid': batchid},
            url: "<?php echo site_url('Buyback/fetchFarmBatch_grn'); ?>",
            success: function (data)
            {
                if(data){
                    $('#farmerBatchName').val(data);
                }
            }
        });
    }
    function fetch_dispatchreturn_detail_table(dispatchAutoID) {
        if (dispatchAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'dispatchAutoID': dispatchAutoID},
                url: "<?php echo site_url('Buyback/fetch_dispatchdetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body_pr_detail').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#table_body_pr_detail').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        tot_amount = 0;
                        receivedQty = 0;
                        $.each(data , function (key, value) {

                            $('#table_body_pr_detail').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + (value['qtygrn'] - value['returnqty'] ) + '</td><td class="text-right">' +parseFloat(value['unitTransferCostCost']).toFixed(2) + '</td><td class="text-center"><input type="text" class="number" id="qty_' + value['dispatchDetailsID'] + '" onkeyup="select_check_box(this,' + value['dispatchDetailsID'] + ',' + (value['qtygrn'] - value['returnqty'] ) + ' )" ></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['dispatchDetailsID'] + '" type="checkbox" value="' + value['dispatchDetailsID'] + '"></td></tr>');
                            x++;

                            //.formatMoney(currency_decimal, '.', ',')
                        });
                    }
                    number_validation();
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
        ;
    }
    function select_check_box(data, id,reqqty) {
        var qty = $('#qty_' + id).val();
        if (qty <= reqqty) {
            $("#check_" + id).prop("checked", false);
            if (data.value > 0) {
                $("#check_" + id).prop("checked", true);
            }
        } else {

            $('#qty_' + id).val('');
            myAlert('w','You cannot return more than balance qty');
        }
    }
    function save_return_items()
    {
        var selected = [];
        var amount = [];
        var qty = [];
        var unitTransferCostCost = [];
        var discount = [];
        var discountamt = [];
        $('#table_body_pr_detail input:checked').each(function () {
            if ($('#qty_' + $(this).val()).val() == '') {
                swal("Cancelled", "Return qty cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                qty.push($('#qty_' + $(this).val()).val());
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'DetailsID': selected,
                    'returnAutoID': returnAutoID,
                    'qty': qty,
                },
                url: "<?php echo site_url('Buyback/save_return_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status'] == true) {
                        $('#dispatch_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_detail();
                        }, 300);
                    } else {
                        myAlert('w', data['data'], 1000);
                    }

                }, error: function () {
                    $('#dispatch_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }
    function delete_buyback_return(id) {
        if (returnAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'returnDetailsID': id},
                        url: "<?php echo site_url('Buyback/delete_return_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_detail();
                                stopLoad();
                                refreshNotifications(true);
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }



</script>