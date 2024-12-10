<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_sales_person');
echo head_page($title, false);



/*echo head_page($_POST['page_name'], false);*/
$date_format_policy = date_format_policy();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$location_arr = all_delivery_location_drop();
$segment_arr = fetch_segment();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

    <div class="steps">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('sales_maraketing_masters_step_one');?> - <?php echo $this->lang->line('sales_maraketing_masters_header');?></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_sales_person_details()" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('sales_maraketing_masters_step_two');?>
        - <?php echo $this->lang->line('sales_maraketing_masters_commision');?></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('sales_maraketing_masters_step_three');?> - <?php echo $this->lang->line('sales_maraketing_masters_confirmation');?></span>
        </a>
    </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="sales_person_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="SalesPersonName"><?php echo $this->lang->line('common_name');?> <?php required_mark(); ?></label><!--Name-->
                <div class="input-group">
                    <input type="text" class="form-control" id="SalesPersonName" name="SalesPersonName" required>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip" onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Employee" rel="tooltip" onclick="link_employee_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="SecondaryCode"><?php echo $this->lang->line('sales_maraketing_masters_secondary_code');?></label><!--Secondary Code-->
                <input type="text" class="form-control" id="SecondaryCode" name="SecondaryCode">
            </div>
            <div class="form-group col-sm-2">
                <label for="salesPersonTarget"><?php echo $this->lang->line('sales_maraketing_masters_target_type');?> <?php required_mark(); ?></label><!--Target Type-->
                <div class="input-group">
                    <?php echo form_dropdown('salesPersonTargetType', array('1' => $this->lang->line('sales_maraketing_masters_yearly')/*'Yearly'*/, '2' => $this->lang->line('common_monthly')/*'Monthly'*/), 1, 'class="form-control" id="salesPersonTargetType" required'); ?>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="salesPersonTarget"><?php echo $this->lang->line('sales_maraketing_masters_sales_target');?> <?php required_mark(); ?></label><!--Sales Target-->
                <input type="text" class="form-control number" id="salesPersonTarget" name="salesPersonTarget" required>
            </div>
            <div class="form-group col-sm-2">
                <label for="salesPersonCurrencyID"><?php echo $this->lang->line('common_currency');?> <?php required_mark(); ?></label><!--Currency-->
                <?php echo form_dropdown('salesPersonCurrencyID', $currncy_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2"  id="salesPersonCurrencyID" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="receivableAutoID"><?php echo $this->lang->line('sales_maraketing_masters_liability_account');?> <?php required_mark(); ?></label><!--Liability Account-->
                <?php echo form_dropdown('receivableAutoID', $gl_code_arr, $this->common_data['controlaccounts']['ARA'], 'class="form-control select2" id="receivableAutoID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="expanseAutoID"><?php echo $this->lang->line('sales_maraketing_masters_expense_account');?> <?php required_mark(); ?></label><!--Expense Account-->
                <?php echo form_dropdown('expanseAutoID', all_cost_gl_drop(), '', 'class="form-control select2" id="expanseAutoID" required'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="wareHouseAutoID"><?php echo $this->lang->line('common_Location');?> <?php required_mark(); ?></label><!--Location-->
                <?php echo form_dropdown('wareHouseAutoID', $location_arr, '', 'class="form-control select2" id="wareHouseAutoID" required'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="segmentID"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></label><!--Segment-->
                <?php echo form_dropdown('segmentID', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segmentID" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="contactNumber"><?php echo $this->lang->line('common_telephone');?></label><!--Telephone-->
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="contactNumber" name="contactNumber">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="SalesPersonEmail"><?php echo $this->lang->line('common_email');?></label><!--Email-->
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="SalesPersonEmail" name="SalesPersonEmail">
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label for=""><?php echo $this->lang->line('sales_maraketing_masters_is_active');?> </label><!--Is Active-->
                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isActive" type="checkbox" data-caption="" class="columnSelected"
                                   name="isActive" value="1" checked>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="SalesPersonAddress"><?php echo $this->lang->line('common_address');?> </label><!--Address-->
                <textarea class="form-control" rows="2" id="SalesPersonAddress" name="SalesPersonAddress"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg" id="sales_person_btn" type="submit"><?php echo $this->lang->line('sales_maraketing_masters_add_sales_person');?> </button><!--Add Sales Person-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="table-responsive">
            <div class="row">
                <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_maraketing_masters_sales_targets');?></h4><!-- Sales Targets -->
                    <h4></h4>
                </div>
                <div class="col-md-4">
                    <button type="button" onclick="sales_target_modal()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> <?php echo $this->lang->line('sales_maraketing_masters_sales_target');?>
                    </button><!--Sales Target-->
                </div>
            </div>
            <table class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                   <!-- <th style="min-width: 30%">Date from</th>
                    <th style="min-width: 30%">Date to</th>-->
                    <th style="min-width: 15%"> <?php echo $this->lang->line('sales_maraketing_masters_start_amount');?> <span class="currency"> (LKR)</span></th><!--Start Amount-->
                    <th style="min-width: 15%"> <?php echo $this->lang->line('sales_maraketing_masters_end_amount');?>  <span class="currency"> (LKR)</span></th><!--End Amount-->
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_percentage');?> %</th><!--Percentage-->
                    <th style="min-width: 10%">&nbsp;</th>
                </tr>
                </thead>
                <tbody id="table_body">
                <tr class="danger">
                    <td class="text-center" colspan="6"><b><?php echo $this->lang->line('common_no_records_found');?></b></td><!--No Records Found-->
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="sales_target_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="sales_target_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_maraketing_masters_sales_target');?> </h4><!--Sales Target-->
                </div>
                <div class="modal-body">
                    <!--<div class="form-group">
                        <label for="datefrom" class="col-sm-4 control-label">Date from</label>
                        <div class="col-sm-5">
                            <input type="text" name="datefrom"
                                   data-inputmask="'alias': '<?php /*echo $date_format_policy */?>'" id="datefrom"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dateTo" class="col-sm-4 control-label">Date To</label>
                        <div class="col-sm-5">
                            <input type="text" name="dateTo"
                                   data-inputmask="'alias': '<?php /*echo $date_format_policy */?>'" id="dateTo"
                                   class="form-control" required>
                        </div>
                    </div>-->
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('sales_maraketing_masters_start_amount');?> </label><!--Start Amount-->
                        <div class="col-sm-4">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency"> (LKR)</span></div>
                                <input type="text" class="form-control number" id="fromTargetAmount" value="0"
                                       name="fromTargetAmount" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('sales_maraketing_masters_end_amount');?></label><!--End Amount-->
                        <div class="col-sm-4">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency"> (LKR)</span></div>
                                <input type="text" class="form-control number" id="toTargetAmount" value="0"
                                       name="toTargetAmount">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_percentage');?></label><!--Percentage-->
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" class="form-control number" id="percentage" value="0"
                                       name="percentage">
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?></button><!--Save-->
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" tabindex="-1" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel"><?php echo $this->lang->line('sales_maraketing_masters_link_employee');?> </h4><!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('common_employee');?></label><!--Employee-->
                        <div class="col-sm-7">
                            <?php
                            $employee_arr = all_employee_drop();
                            echo form_dropdown('employee_id', $employee_arr, '', 'class="form-control select2" id="employee_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                <button type="button" class="btn btn-primary" onclick="fetch_employee_detail()"><?php echo $this->lang->line('common_add_employee');?></button><!--Add employee-->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var salesPersonID;
    var EIdNo;
    var targetID;
    var currencyID;
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        salesPersonID = null;
        window.EIdNo = null;
        window.targetID = null;
        window.currencyID = null;
        $("#salesPersonCurrencyID").prop("disabled", true);
        $('#sales_person_btn').text('<?php echo $this->lang->line('sales_maraketing_masters_add_sales_person');?>');/*Add Sales Person*/
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/sales/erp_sales_person_master', '', 'Sales Person');
        });
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            salesPersonID = p_id;
            laad_sale_person_header();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }

        $('#sales_person_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                SalesPersonName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_sales_person_name_is_required');?>.'}}},/*Sales Person Name is required*/
                salesPersonTargetType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_target_type_is_required');?>.'}}},/*Target Type is required*/
                receivableAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_receivable_is_required');?>.'}}},/*Receivable is required*/
                expanseAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_sales_expanse_is_required');?>.'}}},/*Expanse is required*/
                wareHouseAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_target_type_is_required');?>.'}}},/*Location is required*/
                salesPersonTarget: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_receivable_is_required');?>.'}}},/*Sales Target is required*/
                segmentID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*segment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            //$("#salesPersonCurrencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'salesPersonID', 'value': salesPersonID});
            data.push({'name': 'EIdNo', 'value': EIdNo});
            data.push({'name': 'currency_code', 'value': $('#salesPersonCurrencyID option:selected').text()});
            data.push({'name': 'delivery_location', 'value': $('#wareHouseAutoID option:selected').text()});
            data.push({'name': 'receivableAccount', 'value': $('#receivableAutoID option:selected').text()});
            data.push({'name': 'expanseAccount', 'value': $('#expanseAutoID option:selected').text()});
            data.push({'name': 'segment', 'value': $('#segmentID option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Sales/save_sales_person'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    myAlert(data['type'], data['message'], 1000);
                    if (data['status']) {
                        salesPersonID = data['last_id'];
                        window.EIdNo = null;
                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('.currency').html('( ' + data['salesPersonCurrency'] + ' )');
                        sales_person_table();
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#sales_target_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                /*datefrom: {validators: {notEmpty: {message: 'Date from is required.'}}},
                dateTo: {validators: {notEmpty: {message: 'Date to is required.'}}},*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_percentage_is_required');?>.'}}},/*Percentage is required*/
                fromTargetAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_amount_is_required');?>.'}}},/*Amount is required*/
                toTargetAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_maraketing_masters_amount_is_required');?>.'}}},/*Amount is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'targetID', 'value': targetID});
            data.push({'name': 'salesPersonID', 'value': salesPersonID});
            data.push({'name': 'currencyID', 'value': currencyID});
            // data.push({'name' : 'delivery_location', 'value' : $('#wareHouseAutoID option:selected').text()});
            // data.push({'name' : 'receivableAccount', 'value' : $('#receivableAutoID option:selected').text()});
            // data.push({'name' : 'expanseAccount', 'value' : $('#expanseAutoID option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Sales/save_sales_target'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    myAlert(data['type'], data['message'], 1000);
                    if (data['status']) {
                        window.EIdNo = null;
                        $('#sales_target_modal').modal('hide');
                        fetch_sales_person_details();
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
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

    function laad_sale_person_header() {
        if (salesPersonID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'salesPersonID': salesPersonID},
                url: "<?php echo site_url('Customer/laad_sale_person_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        currencyID = data['salesPersonCurrencyID'];
                        salesPersonID = data['salesPersonID'];
                        if(data["EIdNo"] > 0){
                            $('#SalesPersonName').prop('readonly',true);
                        }
                        $('#sales_person_btn').text('<?php echo $this->lang->line('sales_maraketing_masters_update_sales_person');?>');/*Update Sales Person*/
                        $('.currency').html('( ' + data['salesPersonCurrency'] + ' )');
                        $('#SalesPersonName').val(data['SalesPersonName']).trigger('input');;
                        $('#wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        $('#receivableAutoID').val(data['receivableAutoID']).change();
                        $('#expanseAutoID').val(data['expanseAutoID']).change();
                        $('#salesPersonCurrencyID').val(data['salesPersonCurrencyID']).change();
                        $("#salesPersonCurrencyID").prop("disabled", true);
                        $('#SalesPersonEmail').val(data['SalesPersonEmail']);
                        $('#contactNumber').val(data['contactNumber']);
                        $('#SecondaryCode').val(data['SecondaryCode']);
                        $('#salesPersonTargetType').val(data['salesPersonTargetType']);
                        $('#salesPersonTarget').val(data['salesPersonTarget']);
                        $('#SalesPersonAddress').val(data['SalesPersonAddress']);
                        if (data['isActive'] == 1) {
                            $('#checkbox_isActive').iCheck('check');
                        } else {
                            $('#checkbox_isActive').iCheck('uncheck');
                        }
                        $('#segmentID').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        fetch_sales_person_details();
                        $('[href=#step2]').tab('show');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function save_draft() {
        if (salesPersonID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('sales_maraketing_masters_you_want_to_close_this_document');?>",/*You want to close this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_Close');?>",/*Close*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/sales/erp_sales_person_master', '', 'Sales Person');
                });
        }
        ;
    }

    function fetch_sales_person_details() {
        if (salesPersonID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'salesPersonID': salesPersonID},
                url: "<?php echo site_url('Customer/fetch_sales_person_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    //$('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                    $('#table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                    } else {
                        currency_decimal = 2;// data['currency']['transactionCurrencyDecimalPlaces'];
                        $.each(data['detail'], function (key, value) {
                            //$('#table_body').append('<tr><td>' + x + '</td><td>' + value['datefrom'] + '</td><td>' + value['dateTo'] + '</td><td class="text-right">' + parseFloat(value['fromTargetAmount']).formatMoney(currency_decimal, '.', ',') + ' - ' + parseFloat(value['toTargetAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-center">' + value['percentage'] + '</td><td class="text-right"><a onclick="laad_sale_target(' + value['targetID'] + ',\'' + value['salesPersonID'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_target(' + value['targetID'] + ',\'' + value['salesPersonID'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            $('#table_body').append('<tr><td>' + x + '</td><td class="text-right">' + parseFloat(value['fromTargetAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['toTargetAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-center">' + value['percentage'] + '</td><td class="text-right"><a onclick="laad_sale_target(' + value['targetID'] + ',\'' + value['salesPersonID'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_target(' + value['targetID'] + ',\'' + value['salesPersonID'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                        });
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function link_employee_model() {
        $('#employee_id').val('').change();
        $('#emp_model').modal('show');
    }

    function clearEmployee() {
        $('#employee_id').val('').change();
        $('#SalesPersonName').val('').trigger('input');
        $('#SalesPersonEmail').val('');
        $('#contactNumber').val('');
        $('#SecondaryCode').val('');
        $('#SalesPersonAddress').val('');
        $('#SalesPersonName').prop('readonly',false);
    }

    function fetch_employee_detail() {
        var employee_id = $('#employee_id').val();
        if (employee_id) {
            window.EIdNo = employee_id;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'employee_id': employee_id},
                url: "<?php echo site_url('Customer/fetch_employee_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data) {
                        $('#SalesPersonName').val(data['Ename2']).trigger('input');
                        $('#SalesPersonEmail').val(data['EEmail']);
                        $('#contactNumber').val(data['EcMobile']);
                        $('#SecondaryCode').val(data['ECode']);
                        var EpAddress1 = data['EpAddress1'];
                        var EpAddress2 = data['EpAddress2'];
                        if(EpAddress1 == 'null' || EpAddress2 == 'null'){
                            $('#SalesPersonAddress').val('');
                        }else{
                            $('#SalesPersonAddress').val(data['EpAddress1'] + ' ' + data['EpAddress2']);
                        }
                        $('#SalesPersonName').prop('readonly',true);
                        $('#emp_model').modal('hide');
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        } else {

        }
    }

    function changecreditlimitcurr() {
        var currncy;
        var split;
        currncy = $('#salesPersonCurrencyID option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#salesPersonCurrencyID').val();
        currency_validation_modal(CurrencyID, 'CUS', '', 'CUS');
    }

    function sales_target_modal() {
        window.targetID = null;
        $('#sales_target_form')[0].reset();
        $('#sales_target_form').bootstrapValidator('resetForm', true);
        $("#sales_target_modal").modal({backdrop: "static"});
        load_sales_target_endamount();

    }

    function delete_target(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_maraketing_masters_are_you_sure_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
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
                    data: {'targetID': id},
                    url: "<?php echo site_url('Customer/delete_sales_target'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['status']) {
                            fetch_sales_person_details();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function laad_sale_target(targetID) {
        if (salesPersonID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'targetID': targetID},
                url: "<?php echo site_url('Customer/laad_sale_target'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#sales_target_form").bootstrapValidator('resetForm', true);
                    if (!jQuery.isEmptyObject(data)) {
                        window.targetID = data['targetID'];
                        window.CurrencyID = data['currencyID'];
                        /*$('#datefrom').val(data['datefrom']);
                        $('#dateTo').val(data['dateTo']);*/
                        $('#percentage').val(data['percentage']);
                        $('#fromTargetAmount').val(data['fromTargetAmount']);
                        $('#toTargetAmount').val(data['toTargetAmount']);
                        // $('#salesPersonTarget').val(data['salesPersonTarget']);
                        // $('#SalesPersonAddress').val(data['SalesPersonAddress']);
                        // fetch_sales_person_details();
                        $("#sales_target_modal").modal({backdrop: "static"});
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_conformation() {
        if (salesPersonID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'salesPersonID': salesPersonID, 'html': true},
                url: "<?php echo site_url('Customer/load_sale_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    //attachment_modal_purchaseOrder(salesPersonID, "Good Received Note", "GRV");
                    stopLoad();
                    //refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_sales_target_endamount() {
        if (salesPersonID) {
            $.ajax({
                async: true,
                type: 'get',
                dataType: 'json',
                data: {'salesPersonID': salesPersonID},
                url: "<?php echo site_url('Customer/load_sales_target_endamount'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        if(data['toTargetAmount'] == null){
                            $('#fromTargetAmount').val(0);
                        }else{
                            $('#fromTargetAmount').val(parseFloat(data['toTargetAmount'])+1);
                        }
                    }else{
                        $('#fromTargetAmount').val(0);
                    }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
    }
</script>