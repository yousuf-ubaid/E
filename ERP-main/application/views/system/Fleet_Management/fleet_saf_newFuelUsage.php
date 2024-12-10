<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('fleet_lang', $primaryLanguage);
$fueltype = array('');
$this->load->helper('community_ngo_helper');
$this->load->helper('fleet_helper');
$vehicle = fetch_all_vehicle();
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment(false);
$segment_arr_default = default_segment_drop();
$type_arr = array('' => 'Select Type', 'Standard' => 'Standard');
$currency_arr = all_currency_new_drop();
$supplier_arr = all_supplier_drop();
$financeyear_arr = all_financeyear_drop(true);

$invoice_arr = invoice_to();
$umo_arr = array('' => $this->lang->line('common_select_uom')/*'Select UOM'*/);
$gl_code_arr_income = fetch_gl_categories();

$transaction_total = 100;
?>
    <style>
        .autocomplete-suggestions {
            border: 1px solid #999;
            background: #FFF;
            overflow: auto;
            cursor: pointer;
        }

        .autocomplete-suggestion {
            padding: 2px 5px;
            white-space: nowrap;
            cursor: pointer;
        }

        .autocomplete-selected {
            background: #F0F0F0;
        }

        .autocomplete-suggestions strong {
            font-weight: normal;
            color: #3399FF;
            cursor: pointer;
        }

        .autocomplete-group {
            padding: 2px 5px;
            cursor: pointer;
        }

        .autocomplete-group strong {
            display: block;
            border-bottom: 1px solid #000;
            cursor: pointer;
        }
    </style>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab">
            <?php echo $this->lang->line('common_step'); ?><!--Step--> 1 -
            <?php echo $this->lang->line('common_header'); ?><!--Header--></a>
        <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">
            <?php echo $this->lang->line('common_step'); ?><!--Step--> 2 -
            <?php echo $this->lang->line('fuel_purchase_details'); ?> </a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <?php echo $this->lang->line('common_step'); ?> 3 -
            <?php echo $this->lang->line('common_confirmation'); ?></a>

    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="item_request_form"'); ?>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">
                        <?php echo $this->lang->line('fleet_document_Date'); ?><?php required_mark(); ?></label>

                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="documentDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="segment">
                        <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment"  required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <div class="form-group">
                        <label for="referenceNumber">
                            <?php echo $this->lang->line('common_reference'); ?><!--Reference-->
                            # </label>
                        <input type="text" class="form-control" id="referenceNumber" name="referenceNumber"
                               placeholder="<?php echo $this->lang->line('common_reference'); ?> #"><!--Reference-->
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="form-group col-sm-4">
                    <label for="financeyear">
                        <?php echo $this->lang->line('fleet_supplier_name'); ?><!--Financial Year--> <?php required_mark(); ?></label>
                    <?php
                    $supplier_arr = fuel_supplier_drop();
                    echo form_dropdown('supplierAutoID', $supplier_arr, '', 'class="form-control select2" id="supplierAutoID" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="financeyear">
                        <?php echo $this->lang->line('fleet_financial_year'); ?><!--Financial Year--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('companyFinanceYearID', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control select2" id="companyFinanceYearID" onchange="fetch_finance_year_period(this.value)"'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="financeyear_period">
                        <?php echo $this->lang->line('fleet_financial_period'); ?><!--Financial Period--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '',
                        'class="form-control" id="financeyear_period" required'); ?>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="transactionCurrencyID">
                        <?php echo $this->lang->line('common_currency'); ?><?php required_mark(); ?></label>
                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" '); ?>
                </div>
                <div class="col-sm-4">
                    <div class="form-group ">
                        <label for="narration">
                            <?php echo $this->lang->line('common_narration'); ?><!--Narration--> <?php required_mark(); ?></label>
                        <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for=""><?php echo $this->lang->line('fleet_link_to_iou_booking')?><!--Link To IOU Booking--></label>

                        <div class="skin skin-square">
                            <div class="skin-section" id="extraColumns">
                                <input id="linktoioubook" type="checkbox"
                                       data-caption="" class="columnSelected" name="linktoioubook" value="1">
                                <label for="checkbox">
                                    &nbsp;
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
            </div>
            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-primary" type="submit">
                    <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
            </div>
            </form>
        </div>

        <div id="step2" class="tab-pane">
            <div class="row">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i>
                        <?php echo $this->lang->line('fleet_fuel_usage'); ?><!--Item Detail--> </h4>
                    <h4></h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="fuelUsage_detail_modal()" class="btn btn-primary pull-right">
                        <i
                                class="fa fa-plus"></i>
                        <?php echo $this->lang->line('common_add_detail'); ?><!--Add Detail-->
                    </button>
                </div>
            </div>
            <br>
            <table class="table table-bordered table-striped table-condesed">
                <thead>
                <tr>
                    <th colspan="5"><?php echo $this->lang->line('fleet_vehicle_details'); ?></th>
                    <th colspan="3"><?php echo $this->lang->line('fuel_purchase_details'); ?></th>
                    <th colspan="2"><?php echo $this->lang->line('common_amount'); ?> <span
                                class="currency">(LKR)</span>
                    </th>
                    <th>&nbsp;</th>

                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_category'); ?></th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_segment'); ?></th>
                    <th style="min-width: 25%"><?php echo $this->lang->line('fleet_vehicle_usage'); ?></th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('fleet_driverName'); ?></th>
                    <th style="min-width: 10%" class="text-left"><?php echo $this->lang->line('fleet_vehicle_fuel'); ?></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('fleet_start_km'); ?></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('fleet_end_km'); ?></th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('fuel_rate'); ?></th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?></th>
                    <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                </tr>
                </thead>
                <tbody id="table_body">
                <tr class="danger">
                    <td colspan="10" class="text-center"><b>
                            <?php echo $this->lang->line('common_no_records_found'); ?></b></td>
                </tr>
                </tbody>
                <tfoot id="table_tfoot">

                </tfoot>
            </table>
            <br>
        </div>

        <div id="step3" class="tab-pane">

            <div id="conform_body"></div>
            <hr>

            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-default prev">
                    <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
                <button class="btn btn-primary " onclick="save_draft()">
                    <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
                <button class="btn btn-success submitWizard" onclick="confirmation()">
                    <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
            </div>
        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div aria-hidden="true" role="dialog" id="fuelUsage_detail_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 100%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                        <?php echo $this->lang->line('fleet_new_fuel_details'); ?><!--Add Item Detail--></h5>
                </div>
                <div class="modal-body">
                    <form role="form" id="purchase_request_detail_form" class="form-horizontal">
                        <input type="hidden" name="fuelusageDetailsID_edit" id="fuelusageDetailsID_edit">

                        <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                            <thead>
                            <tr>
                                <th style="width: 12%;">
                                    <?php echo $this->lang->line('common_category'); ?><!--Category--> <!--GL Code--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('fleet_vehicle_usage'); ?><!--Vehicle Code--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('fleet_Driver'); ?><!--Driver Code--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('fleet_receiptDate'); ?><!-- Receipt Number--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('fleet_vehicle_fuel'); ?><!--Fuel--> <?php required_mark(); ?></th>
                                <th style="width: 2%;">
                                    <?php echo $this->lang->line('fleet_vehicle_speed'); ?><!--expected km/L--></th>
                                <!--  <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_segment'); ?> </th>
                                -->
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('fleet_start_km'); ?><!--Start Km--></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('fleet_end_km'); ?><!--End Km--></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('fuel_rate'); ?><!--Fuel Rate--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Net Amount--></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary addmorebtn btn-xs"
                                            onclick="add_more_income()"><i
                                                class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="display:inline-block;width:200px;white-space: nowrap;overflow:hidden !important;text-overflow: ellipsis;">
                                    <?php echo form_dropdown('gl_code[]', $gl_code_arr_income, '', 'class="form-control select2" id="gl_code" required'); ?>
                                </td>
                                <td>
                                    <?php
                                    echo form_dropdown('vehicleMasterID[]', $vehicle, '', 'class="form-control select2" id="vehicleMasterID" onchange="fuelTypeAutoLoad(this.value,this.value,this)"'); ?>
                                </td>


                                <td>
                                    <?php $Driver = fetch_all_drivers();
                                    echo form_dropdown('driverMasID[]', $Driver, '', 'class="form-control driverMasID select2" id="driverMasID"'); ?>

                                </td>

                                <td>
                                    <?php


                                    echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control select2" id="segmentdetail"'); ?>

                                </td>


                                <td>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                       <input type="text" name="receiptDate[]"
                                              data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                              value="<?php echo $current_date; ?>" id="receiptDate" class="form-control receiptDate"
                                              required>
                                </div>
                                </td>
                                <td>
                                    <?php echo form_dropdown('fuelType[]', $fueltype, '', 'class="form-control select2 fuelType" id="fuelType" required  disabled'); ?>

                                    <input class="form-control fuelTypeID hidden" id="fuelTypeID" name="fuelTypeID[]"
                                           class="pull-right fuelTypeID">

                                </td>
                                <td>
                                    <input class="form-control expKmLiter" id="expKmLiter" name="expKmLiter[]"
                                           class="pull-right expKmLiter" readonly>
                                </td>
                                <td><input type="text" name="startKm[]" id="startKm"
                                           class="form-control number startKm" onfocus="this.select();">
                                </td>
                                <td>


                                    <input type="text" onfocus="this.select();" name="endKm[]" id="endKm"
                                           onchange="change_qty(this)"
                                           class="form-control number input-mini endKm" required>
                                </td>
                                <td><input type="text" name="FuelRate[]" value="0" placeholder="0.00"
                                           onkeyup="change_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number FuelRate" onfocus="this.select();" readonly></td>

                                <td class="hidden">&nbsp;<span class="net_unit_cost pull-right "
                                                               style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control number amount" name="amount[]" id="amount">

                                    <span class="net_amount pull-right hidden"
                                          style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                                </td>

                                <td><textarea class="form-control" rows="1" name="comment[]"
                                              placeholder="<?php echo $this->lang->line('common_comment'); ?>..."></textarea>
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="save_item_issue_details()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var search_id = 1;
        var itemAutoID;
        var faID;
        var rentalItemType;
        var fuelusageID;
        var fuelusageDetailsID;
        var currency_decimal;
        var documentCurrency;
        var deliverydat;
        var issuedat;
        var no_of_days;

        $(document).ready(function () {
            $('#extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-blue',
                radioClass: 'iradio_square_relative-blue',
                increaseArea: '20%'
            });

            $('.headerclose').click(function () {
                fetchPage('system/Fleet_Management/fleet_saf_fuelusage', fuelusageID, 'Fuel Usage');
            });
            $('.select2').select2();
            fuelusageID = null;
            fuelusageDetailsID = null;
            itemAutoID = null;
            faID = null;
            rentalItemType = null;
            currency_decimal = 2;
            documentCurrency = null;
            number_validation();



            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#item_request_form').bootstrapValidator('revalidateField', 'documentDate');
                //   $('#item_request_form').bootstrapValidator('revalidateField', 'expectedReturnDate');
            });
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

            if (p_id) {
                fuelusageID = p_id;
                laad_pqr_header();
                fetch_fuelusage_detail_table();
                $("#a_link").attr("href", "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>/" + fuelusageID);
                $('.btn-wizard').removeClass('disabled');
            } else {
                $('.btn-wizard').addClass('disabled');
            }
            FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
            DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
            DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
            periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
            fetch_finance_year_period(FinanceYearID, periodID);

            $('#item_request_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    supplierAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_name_is_required');?>.'}}}, /*Name is required*/
                    transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_supplier_currency_is_required');?>.'}}}, /*Supplier Currency is required*/
                    companyFinanceYearID: {validators: {notEmpty: {message: 'Finance year is required.'}}}, /*Supplier Currency is required*/
                    financeyear_period: {validators: {notEmpty: {message: 'Finance period is required.'}}}, /*Supplier Currency is required*/
                    documentDate: {validators: {notEmpty: {message: 'Document date is required.'}}}, /*PRQ Date is required*/
                    narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_narration_is_required');?>.'}}}, /*Narration is required*/
                    segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}/*Segment is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#segment").prop("disabled", false);
                $("#transactionCurrencyID").prop("disabled", false);
                $("#documentDate").prop("disabled", false);
                $("#supplierAutoID").prop("disabled", false);
                $("#companyFinanceYearID").prop("disabled", false);
                $("#financeyear_period").prop("disabled", false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                data.push({'name': 'fuelusageID', 'value': fuelusageID});
                data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
                data.push({'name': 'companyFinanceYear', 'value': $('#companyFinanceYearID option:selected').text()});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Fleet/save_fuelusage_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        var result = $('#transactionCurrencyID option:selected').text().split('|');
                        $('.currency').html('( ' + result[0] + ' )');
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            $('.btn-wizard').removeClass('disabled');
                            fuelusageID = data[2];
                            $("#a_link").attr("href", "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>/" + fuelusageID);
                            $("#segment").prop("disabled", true);
                            $("#transactionCurrencyID").prop("disabled", true);
                            $("#documentDate").prop("disabled", true);
                            $("#supplierAutoID").prop("disabled", true);
                            $("#companyFinanceYearID").prop("disabled", true);
                            $("#financeyear_period").prop("disabled", true);
                            $('[href=#step2]').tab('show');
                            fetch_fuelusage_detail_table();
                        }
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
                    swal("Cancelled", "Your " + select_value + " file is safe :)", "error");
                }
            });
        }

        function fetch_fuelusage_detail_table() {
            if (fuelusageID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'fuelusageID': fuelusageID},
                    url: "<?php echo site_url('Fleet/fetch_fuel_usage_detail_table'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                        $('#table_body').empty();
                        $('#table_tfoot').empty();
                        x = 1;
                        if (jQuery.isEmptyObject(data['detail'])) {
                            $("#segment").prop("disabled", true);
                            $("#transactionCurrencyID").prop("disabled", true);
                            $("#documentDate").prop("disabled", true);
                            $("#supplierAutoID").prop("disabled", true);
                            $("#companyFinanceYearID").prop("disabled", true);
                            $("#financeyear_period").prop("disabled", true);

                            $('#table_body').append('<tr class="danger"><td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                            <!--No Records Found-->
                        } else {
                            $("#segment").prop("disabled", true);
                            $("#transactionCurrencyID").prop("disabled", true);
                            $("#documentDate").prop("disabled", true);
                            $("#supplierAutoID").prop("disabled", true);
                            $("#companyFinanceYearID").prop("disabled", true);
                            $("#financeyear_period").prop("disabled", true);
                            tot_amount = 0;
                            currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];

                            $.each(data['detail'], function (key, value) {

                                $('#table_body').append('<tr><td>' + x + '</td><td>' + value['glConfigDescription'] + '</td><td>' + value['segmentCode'] + '</td><td><strong>Vehicale Code :</strong> ' + value['vehicleCode']  + '<br><strong>Vehicale No :</strong> ' + value['VehicleNo']  + ' </td><td>' + value['driverName'] + '</td><td>' + value['FuelType'] + '</td><td class="text-center">' + value['startKm'] + '</td><td class="text-center">' + value['endKm'] + '</td><td class="text-center">' + value['fuelRate'] + '</td><td class="text-right">' + parseFloat(value['totalAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item(' + value['fuelusageDetailsID'] + ',\'' + value['itemDescription'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                x++;
                                tot_amount += parseFloat(value['totalAmount']);
                            });
                            $('#table_tfoot').append('<tr><td colspan="9" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            }
        }

        function link_supplier_model() {
            $('#supplierAutoID').val('').change();
            $('#supplier_model').modal('show');
        }

        function laad_pqr_header() {
            if (fuelusageID) {
                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'fuelusageID': fuelusageID},
                        url: "<?php echo site_url('Fleet/load_fuelPurchase_request_header'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (!jQuery.isEmptyObject(data)) {
                                $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                                $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                                documentCurrency = data['transactionCurrencyID'];
                                $('#documentDate').val(data['documentDate']);
                                $('#narration').val(data['narration']);
                                $('#supplierAutoID').val(data['supplierAutoID']).change();
                                $('#companyFinanceYearID').val(data['companyFinanceYearID']).change();
                                if (data['linkedToIOUYN'] == 1) {
                                    $('#linktoioubook').iCheck('check');
                                } else {
                                    $('#linktoioubook').iCheck('uncheck');
                                }

                                setTimeout(function () {
                                    fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                                }, 500);

                                $('[href=#step2]').tab('show');
                                $('a[data-toggle="tab"]').removeClass('btn-primary');
                                $('a[data-toggle="tab"]').addClass('btn-default');
                                $('[href=#step2]').removeClass('btn-default');
                                $('[href=#step2]').addClass('btn-primary');
                            }
                            stopLoad();
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    }
                )
                ;
            }
        }

        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

        function fuelUsage_detail_modal() {
            if (fuelusageID) {

                fuelusageDetailsID = null;
                $('#purchase_request_detail_form')[0].reset();

                $('#po_detail_add_table tbody tr').not(':first').remove();
                $('#gl_code').val('').change();
                $('#vehicleMasterID').val('').change();
                $('#driverMasID').val('').change();
                //  $('.receiptDate').val('');
                $('#fuelType').val('');
                $('#expKmLiter').val('');
                $('#startKm').val('');
                $('#endKm').val('');
                $('#FuelRate').val('');
                $('#amount').val('');
                $('#comment').val('');
                $('#receiptDate').closest('tr').css("background-color", 'white');

                $('.net_amount,.net_unit_cost').text('0.00');

                $('.itemAutoID').val('');
                $('.faID').val('');
                $('.rentalItemType').val('');
                $('.rentalItemID').val('').change();

                Inputmask().mask(document.querySelectorAll("input"));
                $("#fuelUsage_detail_modal").modal({backdrop: "static"});
                $('.rentalItemID').closest('tr').css("background-color", 'white');
                $('.deliverydat').closest('tr').css("background-color", 'white');
                $('.quantityRequested').closest('tr').css("background-color", 'white');

            }
        }

        function load_conformation() {
            if (fuelusageID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'fuelusageID': fuelusageID, 'html': true},
                    url: "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#conform_body').html(data);
                        $("#a_link").attr("href", "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>/" + fuelusageID);
                        $("#de_link").attr("href", "<?php echo site_url('Fleet/fetch_double_fuelusage'); ?>/" + fuelusageID);

                        //   stopLoad();
                        refreshNotifications(true);
                        /*Purchase Request*/
                    }, error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        refreshNotifications(true);
                    }
                });
            }
        }

        function currency_validation(CurrencyID, documentID) {
            if (CurrencyID) {
                partyAutoID = $('#supplierPrimaryCode').val();
                currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
            }
        }

        function delete_item(id, value) {
            if (fuelusageID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                        text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55 ",
                        confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'fuelusageDetailsID': id},
                            url: "<?php echo site_url('Fleet/delete_fuelUsage_details'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                fetch_fuelusage_detail_table();
                                stopLoad();
                                refreshNotifications(true);
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function edit_item(id, value) {
            if (fuelusageID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure*/
                        text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_edit');?>"/*Edit*/
                    },
                    function () {
                        $('#po_detail_add_table tbody tr').not(':first').remove();
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'fuelusageDetailsID': id},
                            url: "<?php echo site_url('Fleet/fetch_item_issue_detail_edit'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {

                                var totAmount = parseFloat(data['totalAmount']);
                                //    var unitAmount = parseFloat(data['unitAmount']);
                                fuelusageDetailsID = data['fuelusageDetailsID'];

                                $('#vehicleMasterID').val(data['vehicleMasterID']).change();
                                $('#gl_code').val(data['glConfigAutoID']).change();
                                $('#segmentdetail').val(data['segmentIDdet'] + '|' + data['segmentCodedet']).change();

                                $('#fuelusageDetailsID_edit').val(data['fuelusageDetailsID']);
                                $('#receiptDate').val(data['receiptDate']);
                                $('#driverMasID').val(data['driverMasID']).change();
                                //  $('#fuelType').val(data['fuelType']);
                                $('#expKmLiter').val(data['expKmLiter']);
                            //    $('#fuelTypeID').val(data['fuelTypeID']);
                                $('#startKm').val(data['startKm']);
                                $('#endKm').val(data['endKm']);
                                $('#FuelRate').val(data['fuelRate']);
                                $('#amount').val(data['totalAmount']);
                                $('#comment').val(data['comment']);
                                //  $('#net_unit_cost_edit').text((unitAmount).formatMoney(2, '.', ','));
                                // $('#totalAmount_edit').text((totAmount).formatMoney(2, '.', ','));
                                // $('#comment_edit').val(data['comment']);
                                // issuedat = data['issueDate'];


                                $("#fuelUsage_detail_modal").modal({backdrop: "static"});
                                stopLoad();
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Try Again ", "error");
                            }
                        });
                    });
            }
        }

        function confirmation() {
            if (fuelusageID) {
                swal({

                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55 ",
                        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'fuelusageID': fuelusageID},
                            url: "<?php echo site_url('Fleet/fuel_usage_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                if (data) {
                                    fetchPage('system/Fleet_Management/fleet_saf_fuelusage', fuelusageID, 'Fuel Usage');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function change_amount(element) {

            net_amount(element);
        }

        function change_qty(element) {

            var startKm = $(element).closest('tr').find('.startKm').val();


            if ( element.value < parseFloat(startKm)) {
                myAlert('w', 'End Km should not be less than or equal to start Km');
                $(element).val('');
                $(element).closest('tr').find('.net_amount').text('0.00');
            } else {
                net_amount(element);
            }

            /*if (element.value > 0) {
                $(element).closest('tr').css("background-color", 'white');
            }*/
        }

        function net_amount(element) {
            var start = $(element).closest('tr').find('.startKm').val();
            var end = $(element).closest('tr').find('.endKm').val();
            var rate = $(element).closest('tr').find('.FuelRate').val();
            var expKm = $(element).closest('tr').find('.expKmLiter').val();

            if (end == null || end == start) {
                /* $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));*/
                $(element).closest('tr').find('.net_amount').text('0.00');
                //      $(element).closest('tr').find('.net_amount').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));

                //   $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0.00');
            } else {
                $(element).closest('tr').find('.net_amount').text((((((parseFloat(1) * parseFloat(end))) - parseFloat(start)) / parseFloat(expKm)) * parseFloat(rate)).formatMoney(2, '.', ','));
                //   $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
            }
        }

        function change_qty_edit() {

            var currentStock = $('#currentStock_edit').val();
            var qut = $('#quantityRequested_edit').val();

            if (qut > parseFloat(currentStock)) {
                myAlert('w', 'Transfer quantity should be less than or equal to current stock');
                $('#quantityRequested_edit').val(0);
            } else {
                net_amount_edit();
            }


        }

        function change_amount_edit() {
            net_amount_edit();
        }

        function net_amount_edit() {
            var qut = $('#quantityRequested_edit').val();
            var amount = $('#estimatedAmount_edit').val();
            var days = $('#no_of_daysEdit').val();

            if (qut == null || qut == 0) {
                /*$('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut)).formatMoney(2, '.', ','));*/
                $('#totalAmount_edit').text('0.00');
                $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
            } else {
                $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
            }
        }

        function save_item_issue_details() {
            var data = $('#purchase_request_detail_form').serializeArray();

            $('select[name="vehicleMasterID[]"] option:selected').each(function () {
                data.push({'name': 'vehicale_details[]', 'value': $('#vehicleMasterID option:selected').text()});
            });
            $('select[name="driverMasID[]"] option:selected').each(function () {
                data.push({'name': 'driver_details[]', 'value': $('#driverMasID option:selected').text()});
            });

            $('select[name="fuelType[]"] option:selected').each(function () {
                data.push({'name': 'FuelType[]', 'value': $('#fuelType option:selected').text()});
            });
            data.push({'name': 'fuelusageID', 'value': fuelusageID});

            if (fuelusageID) {
                $('.rentalItemType').each(function () {
                    if (this.value == '') {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    }
                });

                $.ajax(
                    {
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Fleet/save_fuel_usage_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fuelusageDetailsID = null;
                                fetch_fuelusage_detail_table();
                                $('#fuelUsage_detail_modal').modal('hide');
                            }
                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
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
        }


        function fuelTypeAutoLoad(vehicleMasterID,select_value,element) {
            if(vehicleMasterID)
            {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'vehicleMasterID': vehicleMasterID},
                    url: "<?php echo site_url('Fleet/fetch_fuelType'); ?>",
                    success: function (data) {
                        $(element).closest('tr').find('.fuelType').empty();

                        var mySelect = $(element).parent().closest('tr').find('.fuelType');

                      //  mySelect.append($('<option></option>').val('').html('Select Fuel Type'));
                        if (!jQuery.isEmptyObject(data)) {
                            $.each(data, function (val, text) {
                                mySelect.append($('<option></option>').val(text['fuelTypeID']).html(text['description']));
                            });
                            if (select_value) {
                                $(element).closest('tr').find('.fuelType').val(select_value);
                            }
                        }

                        setTimeout(function(){
                            $(element).closest('tr').find('.fuelType').val(data[0]['fuelTypeID']).change();
                            $(element).closest('tr').find('.fuelTypeID').val(data[0]['fuelTypeID']).change();
                        //    $("#fuelType").prop("disabled", true);

                            $(element).closest('tr').find('.expKmLiter').val(data[0]['expKMperLiter']).change();
                            $(element).closest('tr').find('.FuelRate').val(data[0]['fuelRate']).change();
                        }, 400);
                    }, error: function () {
                        swal("Cancelled", "Your " + value + " file is safe :)", "error");
                    }
                });
            }

        }





        function clearSupplier() {
            $('#supplierAutoID').val('').change();
            $('#supplierAutoID').val('').trigger('input');
            $('#supplierAutoID').val('');
            $('#requestedByName').prop('readonly', false);
        }

        function fetch_supplier_detail() {
            var supplierAutoID = $('#supplierAutoID').val();
            $('#supplierAutoID').val(supplierAutoID);

            if (supplierAutoID) {
                window.EIdNo = supplierAutoID;
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'supplierAutoID': supplierAutoID},
                    url: "<?php echo site_url('Fleet/fetch_supplier_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $('#requestedByName').val(data['supplierAutoID']).trigger('input');
                            $('#supplierAutoID').val(data['supplierAutoID']).trigger('input');

                            $('#requestedByName').prop('readonly', true);
                            $('#supplier_model').modal('hide');
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            } else {

            }
        }

        function save_draft() {
            if (fuelusageID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure*/
                        text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        fetchPage('system/Fleet_Management/fleet_saf_fuelusage', fuelusageID, 'Fuel Usage');
                    });
            }
        }

        function update_item_issue_details() {
            var data = $('#purchase_request_detail_edit_form').serializeArray();
            if (fuelusageID) {
                data.push({'name': 'fuelusageID', 'value': fuelusageID});
                data.push({'name': 'fuelusageDetailsID', 'value': fuelusageDetailsID});
                data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
                data.push({'name': 'no_of_daysEdit', 'value': $('#no_of_daysEdit').val()});

                $.ajax(
                    {
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('CommunityNgo/update_item_issue_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data) {
                                myAlert(data[0], data[1]);
                                if (data[0] == 's') {
                                    fuelusageDetailsID = null;
                                    $('#item_issue_detail_edit_mod').modal('hide');
                                    fetch_fuelusage_detail_table();
                                }
                            }

                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                    });
            } else {
            }
        }

        function load_conformation() {
            if (fuelusageID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'fuelusageID': fuelusageID, 'html': true},
                    url: "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#conform_body').html(data);
                        stopLoad();
                        refreshNotifications(true);
                        /*Purchase Request*/
                    }, error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        refreshNotifications(true);
                    }
                });
            }
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
            //get the carat position
            var caratPos = getSelectionStart(el);
            var dotPos = el.value.indexOf(".");
            if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
                return false;
            }
            return true;
        }

        //thanks: http://javascript.nwbox.com/cursor_position/
        function getSelectionStart(o) {
            if (o.createTextRange) {
                var r = document.selection.createRange().duplicate()
                r.moveEnd('character', o.value.length)
                if (r.text == '') return o.value.length
                return o.value.lastIndexOf(r.text)
            } else return o.selectionStart
        }

        /*.......................................................*/

        function fetchExpectedDate() {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'fuelusageID': fuelusageID},
                    url: "<?php echo site_url('CommunityNgo/load_item_request_date'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#expectedReturnDateDetail').val(data['expectedReturnDate']);

                            deliverydat = data['expectedReturnDate'];
                            issuedat = data['MySQLissueDate'];

                            var date1 = new Date(data['MySQLexpectedReturnDate']);
                            var date2 = new Date(data['MySQLissueDate']);
                            var diffDays = (date1.getDate() - date2.getDate()) + parseInt(1);

                            no_of_days = diffDays;
                            $('#no_of_days').val(diffDays);

                        }
                        stopLoad();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            );
        }


        function deliverydate_val(det) {

            if (det.value != 0) {
                $(det).closest('tr').css("background-color", 'white');

                var date = det.value;

                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'date': date},
                        url: "<?php echo site_url('CommunityNgo/get_date_format'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (!jQuery.isEmptyObject(data)) {

                                var date1 = new Date(data);
                                var date2 = new Date(issuedat);
                                var diffDays = (date1.getDate() - date2.getDate()) + parseInt(1);

                                $(det).closest('tr').find('.no_of_days').val(diffDays);

                                var qut = $(det).closest('tr').find('.quantityRequested').val();
                                var amount = $(det).closest('tr').find('.estimatedAmount').val();
                                var days = diffDays;

                                if (qut == null || qut == 0) {
                                    //  $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_amount').text('0.00');
                                    $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                } else {
                                    $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                }
                            }
                            stopLoad();
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    }
                );
            }
        }

        function deliverydate_val_edit(det) {

            if (det.value != 0) {
                $(det).closest('tr').css("background-color", 'white');

                var date = det.value;

                $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'date': date},
                        url: "<?php echo site_url('CommunityNgo/get_date_format'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (!jQuery.isEmptyObject(data)) {

                                var date1 = new Date(data);
                                var date2 = new Date(issuedat);
                                var diffDays = (date1.getDate() - date2.getDate()) + parseInt(1);

                                $(det).closest('tr').find('.no_of_daysEdit').val(diffDays);

                                var qut = $(det).closest('tr').find('.quantityRequested_edit').val();
                                var amount = $(det).closest('tr').find('.estimatedAmount_edit').val();
                                var days = diffDays;

                                if (qut == null || qut == 0) {
                                    $(det).closest('tr').find('.net_amount_edit').text('0.00');
                                    //  $(det).closest('tr').find('.net_amount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                } else {
                                    $(det).closest('tr').find('.net_amount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                                    $(det).closest('tr').find('.net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                                }

                            }
                            stopLoad();
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    }
                );
            }
        }

        function get_itemIDs(det) {
            if (det.value) {

                var rentalItemID = det.value;

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'rentalItemID': rentalItemID},
                    url: "<?php echo site_url('CommunityNgo/fetch_rent_item_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $(det).closest('tr').find('.rentalItemType').val(data['rentalItemType']);
                            $(det).closest('tr').find('.itemAutoID').val(data['itemAutoID']);
                            $(det).closest('tr').find('.faID').val(data['faID']);
                            $(det).closest('tr').find('.PeriodTypeID').val(data['PeriodTypeID']);
                            $(det).closest('tr').find('.currentStock').val(data['currentStock']);
                            $(det).closest('tr').find('.estimatedAmount').val(data['RentalPrice']);

                            var qut = $(det).closest('tr').find('.quantityRequested').val();
                            var amount = data['RentalPrice'];
                            var days = $(det).closest('tr').find('.no_of_days').val();

                            if (qut == null || qut == 0) {
                                $(det).closest('tr').find('.net_amount').text('0.00');
                                // $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(days)).formatMoney(2, '.', ','));
                                $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                            } else {
                                $(det).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut) * parseFloat(days)).formatMoney(2, '.', ','));
                                $(det).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount))).formatMoney(2, '.', ','));
                            }

                            fetch_related_uom_id(data['defaultUnitOfMeasureID'], data['defaultUnitOfMeasureID'], det);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

            }
        }

        function get_itemIDs_edit(det) {
            if (det.value) {

                var rentalItemID = det.value;

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'rentalItemID': rentalItemID},
                    url: "<?php echo site_url('CommunityNgo/fetch_rent_item_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $(det).closest('tr').find('.currentStock_edit').val(data['currentStock']);
                            fetch_related_uom_id_edit(data['defaultUnitOfMeasureID'], data['defaultUnitOfMeasureID'], det);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }

        function fetch_related_uom_id(masterUnitID, select_value, element) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'masterUnitID': masterUnitID},
                url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
                success: function (data) {
                    $(element).closest('tr').find('.umoDropdown').empty();

                    var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                    mySelect.append($('<option></option>').val('').html('Select UOM'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                        });
                        if (select_value) {
                            $(element).closest('tr').find('.umoDropdown').val(select_value);
                        }
                    }
                }, error: function () {

                }
            });
        }


        function fetch_related_uom_id_edit(masterUnitID, select_value, element) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'masterUnitID': masterUnitID},
                url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
                success: function (data) {
                    $(element).closest('tr').find('.uomdrop').empty();

                    var mySelect = $(element).parent().closest('tr').find('.uomdrop');

                    mySelect.append($('<option></option>').val('').html('Select UOM'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                        });
                        if (select_value) {
                            $(element).closest('tr').find('.uomdrop').val(select_value);
                        }
                    }
                }, error: function () {

                }
            });
        }
        function add_more_income() {

            $('select.select2').select2('destroy');
            var appendData = $('#po_detail_add_table tbody tr:first').clone();
            appendData.find('input').val('');
            appendData.find('textarea').val('');


            appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
            $('#po_detail_add_table').append(appendData);
            var lenght = $('#po_detail_add_table tbody tr').length - 1;
            $(".select2").select2();

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#po_detail_add_table').bootstrapValidator('revalidateField', 'receiptDate');
            });


            number_validation();

            Inputmask().mask(document.querySelectorAll("input"));
        }

        /* */

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 4/4/2018
 * Time: 2:12 PM
 */