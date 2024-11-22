
<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_over_time_over_time_slab');
echo head_page($title, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$supplier_arr = all_supplier_drop();
$segment_arr = fetch_segment();
$employee_arr = fetch_employee_ec();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 1 - <?php echo $this->lang->line('hrms_over_time_over_time_slab');?> <?php echo $this->lang->line('hrms_over_time_header');?><!--Over Time Slab Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_OT_detail_table()" data-toggle="tab"><?php echo $this->lang->line('common_step');?><!--Step--> 2 -
        <?php echo $this->lang->line('hrms_over_time_over_time_slab');?><?php echo $this->lang->line('common_details');?><!--Over Time Slab Detail--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="over_time_slab_form"'); ?>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for=""><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                    <input type="text" class="form-control" id="Description" name="Description">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <div class="form-group ">
                    <label for="salesPersonCurrencyID"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2"  id="transactionCurrencyID" required'); ?>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        </div>
        </form>
    </div>

    <div id="step2" class="tab-pane">
        <div class="table-responsive">
            <div class="row">
                <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('hrms_over_time_over_time_slab');?><?php echo $this->lang->line('common_details');?><!--Over Time Slab Detail--> </h4>
                    <h4></h4>
                </div>
                <div class="col-md-4">
                    <button type="button" onclick="over_time_slab_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> &nbsp;&nbsp;<?php echo $this->lang->line('common_add_detail');?><!--Add Detail-->
                    </button>
                </div>
            </div>
            <table class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('hrms_over_time_start_hour');?><!--Start Hour--> </span></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('hrms_over_time_end_hour');?><!--End Hour--> </span></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('hrms_over_time_hourly_rate');?><!--Hourly Rate--> (<span class="currencyid"></span>)</th>
                    <th style="min-width: 10%">&nbsp;</th>
                </tr>
                </thead>
                <tbody id="table_body">
                <tr class="danger">
                    <td class="text-center" colspan="5"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="sales_target_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="over_time_slab_Detail_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_over_time_over_time_slab');?><!--Over Time Slab--></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_over_time_start_hour');?><!--Start Hour--></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" id="startHour" value="0"
                                       name="startHour" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_over_time_end_hour');?><!--End Hour--></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control number" id="EndHour" value="0"
                                       name="EndHour">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('hrms_over_time_hourly_rate');?><!--Hourly Rate--></label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="input-group-addon">(<span class="currencyid"></span>)</div>
                                <input type="text" class="form-control number" id="hourlyRate" value="0"
                                       name="hourlyRate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var otSlabsMasterID=null;
    var currencyid;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/OverTimeManagementSalamAir/over_time_slab', 'Test', 'Expense Claim');
        });

        $('.select2').select2();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            otSlabsMasterID = p_id;
            laad_over_time_slab_header();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }

        $('#over_time_slab_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                Description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}}/*Currency  is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'otSlabsMasterID', 'value': otSlabsMasterID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OverTimeSlab/save_over_time_slab_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        otSlabsMasterID = data['last_id'];
                        currencyid = data['CurrencyCode'];
                        $('.currencyid').html(data['CurrencyCode']);
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
        });

        $('#over_time_slab_Detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                startHour: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_start_hour_is_required');?>.'}}},/*Start Hour is required*/
                EndHour: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_end_hour_is_required');?>.'}}},/*End Hour is required*/
                hourlyRate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_hourly_rate_is_required');?>.'}}},/*Hourly Rate is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'otSlabsMasterID', 'value': otSlabsMasterID});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OverTimeSlab/save_over_time_slab_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    myAlert(data['type'], data['message'], 1000);
                    if (data['type']=='s') {
                        $('#sales_target_modal').modal('hide');
                        fetch_OT_detail_table();
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

    function over_time_slab_modal() {
        $('#over_time_slab_Detail_form')[0].reset();
        $('#over_time_slab_Detail_form').bootstrapValidator('resetForm', true);
        $("#sales_target_modal").modal({backdrop: "static"});
        load_sover_time_slab_endhour();
    }

    function fetch_OT_detail_table() {
        if (otSlabsMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'otSlabsMasterID': otSlabsMasterID},
                url: "<?php echo site_url('OverTimeSlab/fetch_over_time_slab_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                    } else {
                        currency_decimal = 2;
                        $.each(data['detail'], function (key, value) {
                            $('#table_body').append('<tr><td>' + x + '</td><td class="text-right">' + value['startHour'] + '</td><td class="text-right">' + value['EndHour'] + '</td><td class="text-center">' + parseFloat(value['hourlyRate']).formatMoney(currency_decimal, '.', ',') + '</td><td> <a onclick="delete_item(' + value['otSlabsDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                        });
                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function laad_over_time_slab_header() {
        if (otSlabsMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'otSlabsMasterID': otSlabsMasterID},
                url: "<?php echo site_url('OverTimeSlab/laad_over_time_slab_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        otSlabsMasterID = data['otSlabsMasterID'];
                        currencyid = data['CurrencyCode'];
                        $('#Description').val(data['Description']);
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        fetch_OT_detail_table();
                        $('[href=#step2]').tab('show');
                        $('.currencyid').html(currencyid);
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

    function load_sover_time_slab_endhour() {
        if (otSlabsMasterID) {
            $.ajax({
                async: true,
                type: 'get',
                dataType: 'json',
                data: {'otSlabsMasterID': otSlabsMasterID},
                url: "<?php echo site_url('OverTimeSlab/load_sover_time_slab_endhour'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        if(data['EndHour'] == null){
                            $('#startHour').val(0);
                        }else{
                            $('#startHour').val(parseFloat(data['EndHour']));
                        }
                    }else{
                        $('#startHour').val(0);
                    }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
    }

    function delete_item(id) {
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
                    data: {'otSlabsDetailID': id},
                    url: "<?php echo site_url('OverTimeSlab/delete_over_time_slab_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data['type'], data['message'], 1000);
                        if (data['type']=='s') {
                            fetch_OT_detail_table();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>