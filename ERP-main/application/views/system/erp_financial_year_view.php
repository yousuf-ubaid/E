<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('finance_ms_final_year');
echo head_page($title, false);
$D_wise_FP = getPolicyValues('DFY', 'All');
$is_13FinancePeriod = getPolicyValues('13FP', 'All');
$departments_arr = load_fp_departments();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active'); ?><!--Active--></td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_closed'); ?><!--Closed--></td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="open_Financial_year_model()" class="btn btn-primary pull-right"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('finance_ms_create_final_year'); ?><!--Create Financial Year-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="Financial_year_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th colspan="3"><?php echo $this->lang->line('finance_ms_final_year'); ?><!--Financial Year--></th>
                <th colspan="4"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('finance_ms_final_year'); ?><!--Financial Year--></th>
                <th style="min-width: 40%"><?php echo $this->lang->line('common_comments'); ?><!--Comments--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_active'); ?><!--Active--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('finance_common_current'); ?><!--Current--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_closed'); ?><!--Closed--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="Financial_year_model" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><?php echo $this->lang->line('finance_ms_add_new_final_year'); ?><!--Add New Financial Year--></h3>
            </div>
            <form role="form" id="Financial_year_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_beginning_date'); ?><!--Beginning Date--></label>

                            <div class="col-sm-5">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" class="form-control" id="beginningdate"
                                        value="<?php echo date('Y-01-01'); ?>" name="beginningdate">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_ending_date'); ?><!--Ending Date--></label>

                            <div class="col-sm-5">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i>
                                    </div>
                                    <input type="text" class="form-control" id="endingdate"
                                        value="<?php echo date('Y-12-31'); ?>" name="endingdate"
                                        onchange="date_change_end()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_comments'); ?><!--Comments--></label>

                            <div class="col-sm-6">
                                <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="isactiveedit_model" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><?php echo $this->lang->line('finance_ms_edit_financial_period'); ?><!--Edit Financial Period--></h3>
            </div>
            <form role="form" id="isactiveedit_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" name="comFinanceYearID" id="comFinanceYearID">
                    <?php if ($is_13FinancePeriod == 1)
                    { ?>
                        <div class="row">
                            <button class="btn btn-primary btn-sm pull-right" style="margin-right:10px;" type="button" onclick="create_13th_month_Financial_Period_toThisYear()" id="create_13th_month_Financial_Period_modalBtn">Create 13th Month Financial Period</button>
                        </div>
                        <hr>
                    <?php } ?>
                    <div class="table-responsive">
                        <table id="isactiveedit_table" class="<?php echo table_class(); ?>">
                            <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 20%"><?php echo $this->lang->line('finance_common_beginning_date'); ?><!--Beginning Date--></th>
                                    <th style="min-width: 20%"><?php echo $this->lang->line('finance_common_ending_date'); ?><!--Ending Date--></th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_is_active'); ?><!--Is Active--></th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_is_current'); ?><!--Is Current--></th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_is_closed'); ?><!--Is Closed--></th>
                                    <th style="min-width: 10%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <br>

                    <div class="Usernote">
                        <strong style="color: red"><?php echo $this->lang->line('common_note'); ?><!--Note--> :</strong>
                        <ul>
                            <li>
                                <p><?php echo $this->lang->line('finance_ms_you_can_have_multiple'); ?><!--You can have multiple period active at the same time. Click the necessary period--></p>
                            </li>

                            <li>
                                <p><?php echo $this->lang->line('finance_ms_only_one_period'); ?><!--Only one period should be kept as Current. By default system will take the Current Period based on this selection-->.</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($D_wise_FP == 1)
{ ?>
    <div aria-hidden="true" role="dialog" tabindex="-1" id="create_department_Financial_Period_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Generate department Financial year Periods</h4>
                </div>
                <form role="form" id="department_Financial_Period_form" class="form-horizontal">
                    <div class="modal-body">
                        <input type="hidden" name="finYearID" id="finYearID">
                        <div class="row">
                            <label class="col-sm-2" for="department">Department</label>
                            <label class="col-sm-1" for="">:</label>
                            <div class="col-sm-5">
                                <?php echo form_dropdown('department', $departments_arr, '', 'class="form-control select2" id="department" onchange="load_department__Financial_Period(this.value)"'); ?>
                            </div>
                            <div class="col-sm-4">
                                <button class="btn btn-primary" type="button" onclick="save_department_financial_periods()" id="save_btn">Generate</button>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="table_department_Financial_Period" class="<?php echo table_class(); ?>">
                                <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 20%"><?php echo $this->lang->line('finance_common_beginning_date'); ?><!--Beginning Date--></th>
                                        <th style="min-width: 20%"><?php echo $this->lang->line('finance_common_ending_date'); ?><!--Ending Date--></th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_is_active'); ?><!--Is Active--></th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_is_current'); ?><!--Is Current--></th>
                                        <th style="min-width: 15%"><?php echo $this->lang->line('finance_common_is_closed'); ?><!--Is Closed--></th>
                                        <th style="min-width: 10%">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
    var OTable;
    $(document).ready(function() {

        <?php if ($is_13FinancePeriod == 1)
        { ?>
            $('.select2').select2();
        <?php } ?>

        $('.headerclose').click(function() {
            fetchPage('system/erp_financial_year_view', '', 'Financial Year');
        });

        $('#beginningdate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        }).on('changeDate', function(ev) {
            $('#Financial_year_form').bootstrapValidator('revalidateField', $('#beginningdate'));
            //$('#Financial_year_form').bootstrapValidator('revalidateField', 'beginningdate');
            //$(this).datepicker('hide');
        }).mask("9999-99-99");


        $('#endingdate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        }).on('changeDate', function(ev) {
            $('#Financial_year_form').bootstrapValidator('revalidateField', $('#endingdate'));
            //$('#Financial_year_form').bootstrapValidator('revalidateField', 'endingdate');
            //$(this).datepicker('hide');
        }).mask("9999-99-99");


        fetch_Financial_year();


        $('#Financial_year_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
            /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                beginningdate: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('finance_common_beginning_date_is_required'); ?>.'
                        }
                    }
                },
                /*Beginning Date is required*/
                endingdate: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('finance_common_ending_date_is_required'); ?>.'
                        }
                    }
                },
                /*Ending Date is required*/
                comments: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('common_comments_are_required'); ?>.'
                        }
                    }
                } /*Comments are required*/
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Financial_year/save_financial_year'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $('#Financial_year_form')[0].reset();
                        $('#Financial_year_form').bootstrapValidator('resetForm', true);
                        $("#Financial_year_model").modal("hide");
                        OTable.draw();
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function open_Financial_year_model() {
        //$('#Financial_year_form')[0].reset();
        //$('#Financial_year_form').bootstrapValidator('resetForm', true);
        $("#Financial_year_model").modal({
            backdrop: "static"
        });
    }

    function fetch_Financial_year() {
        OTable = $('#Financial_year_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sAjaxSource": "<?php echo site_url('Financial_year/load_Financial_year'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i;
                    (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                // Initialize xEditable for comments
                $('.xEditableComments').editable({
                    url: '<?php echo site_url('Financial_year/update_comments') ?>', // Update URL
                    type: 'text',
                    send: 'always',
                    ajaxOptions: {
                        type: 'post',
                        dataType: 'json',
                        success: function(data) {
                            if (data[0] == 'e') {
                                myAlert(data[0], data[1], data[2], data[3]);
                                setTimeout(function() {
                                    $('.comment_change_' + data[3]).editable('setValue', data[2], true);
                                }, 1500);
                            }
                        },
                        error: function(xhr) {
                            myAlert('e', xhr.responseText);
                        }
                    }
                });
            },
            "aoColumns": [{
                    "mData": "companyFinanceYearID"
                },
                {
                    "mData": "financial_year"
                },
                {
                    "mData": "comments"
                }, // Editable comments column
                {
                    "mData": "status"
                },
                {
                    "mData": "current"
                },
                {
                    "mData": "close"
                },
                {
                    "mData": "action"
                },
                {
                    "mData": "endingDate"
                },
                {
                    "mData": "beginingDate"
                }
            ],
            "columnDefs": [{
                "targets": [3, 4, 5, 6],
                "orderable": false
            }, {
                "visible": false,
                "searchable": true,
                "targets": [7, 8]
            }, {
                "searchable": false,
                "targets": [0]
            }],
            "fnServerData": function(sSource, aoData, fnCallback) {
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


    function changeFinancial_yearsatus(id) {
        var compchecked = 0;
        if ($('#statusactivate_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    companyFinanceYearID: id,
                    chkedvalue: compchecked
                },
                url: "<?php echo site_url('Financial_year/update_year_status'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        OTable.draw();
                    } else {
                        $('#statusactivate_' + id).attr('checked', false);
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        } else if (!$('#statusactivate_' + id).is(":checked")) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    companyFinanceYearID: id,
                    chkedvalue: 0
                },
                url: "<?php echo site_url('Financial_year/update_year_status'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        OTable.draw();
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function changeFinancial_yearcurrent(id) {
        var compchecked = 0;
        if ($('#statuscurrent_' + id).prop("checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    companyFinanceYearID: id,
                    chkedvalue: compchecked
                },
                url: "<?php echo site_url('Financial_year/update_year_current'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        //fetch_Financial_year();
                        OTable.draw();
                    } else {
                        $('#statuscurrent_' + id).prop('checked', false);
                    }
                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        } else if (!$('#statuscurrent_' + id).prop("checked")) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    companyFinanceYearID: id,
                    chkedvalue: 0
                },
                url: "<?php echo site_url('Financial_year/update_year_current'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        OTable.draw();
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function openisactiveeditmodel(id) {
        $("#isactiveedit_model").modal({
            backdrop: "static"
        });
        loadisactiveeditdetails(id);
    }

    function loadisactiveeditdetails(id) {
        $('#comFinanceYearID').val(id);
        var Otable2 = $('#isactiveedit_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "iDisplayLength": 50,
            "sAjaxSource": "<?php echo site_url('Financial_year/load_isactiveeditdetails'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "fnDrawCallback": function(oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $('.Closed').closest('tr').find('input').attr('disabled', true);

            },
            "aoColumns": [{
                    "mData": "companyFinancePeriodID"
                },
                {
                    "mData": "dateFrom"
                },
                {
                    "mData": "dateTo"
                },
                {
                    "mData": "status"
                },
                {
                    "mData": "current"
                },
                {
                    "mData": "closed"
                },
                {
                    "mData": "reopen"
                }
            ],
            "columnDefs": [{
                "targets": [3, 4, 5],
                "orderable": false
            }],
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "companyFinanceYearID",
                    "value": id
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
        setTimeout(function() {
            $('.Current').closest('tr').find('.radiobtn').prop('checked', "checked");
        }, 400);

    }


    function changeFinancial_yearisactivesatus(id) {
        var compchecked = 0;
        if ($('#isactivesatus_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    companyFinancePeriodID: id,
                    chkedvalue: compchecked
                },
                url: "<?php echo site_url('Financial_year/update_financial_year_isactive_status'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {

                    } else {
                        $('#isactivesatus_' + id).prop("checked", false);
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        } else if (!$('#isactivesatus_' + id).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    companyFinancePeriodID: id,
                    chkedvalue: 0
                },
                url: "<?php echo site_url('Financial_year/update_financial_year_isactive_status'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {

                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }


    function change_financial_period_current(companyFinancePeriodID, companyFinanceYearID) {
        if ($('#iscurrentstatus_' + companyFinancePeriodID).prop("checked")) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    'companyFinancePeriodID': companyFinancePeriodID,
                    'companyFinanceYearID': companyFinanceYearID
                },
                url: "<?php echo site_url('Financial_year/change_financial_period_current'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    $("#isactiveedit_model").on("hidden.bs.modal", function() {

        OTable.draw();

    });


    function changeFinancial_yearclose(id) {
        var compchecked = 0;
        if ($('#closeactivate_' + id).is(":checked")) {
            compchecked = 1;
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "<?php echo $this->lang->line('finance_common_you_want_to_close_this_financial_year'); ?>",
                    /*You want to close this financial year!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_ok'); ?>",
                    /*Ok*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            data: {
                                companyFinanceYearID: id,
                                chkedvalue: compchecked
                            },
                            url: "<?php echo site_url('Financial_year/update_year_close'); ?>",
                            beforeSend: function() {
                                startLoad();
                            },
                            success: function(data) {
                                stopLoad();
                                refreshNotifications(true);
                                if (data) {
                                    OTable.draw();
                                }
                            },
                            error: function() {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    } else {
                        $('#closeactivate_' + id).prop("checked", false);
                    }
                });

        }
    }

    function changefinancialperiodclose(id) {
        var compchecked = 0;
        if ($('#closefinaperiod_' + id).is(":checked")) {
            compchecked = 1;
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "<?php echo $this->lang->line('finance_common_you_want_to_close_this_financial_period'); ?>",
                    /*You want to close this financial period!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_Close'); ?>",
                    /*Close*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            data: {
                                companyFinancePeriodID: id,
                                chkedvalue: compchecked
                            },
                            url: "<?php echo site_url('Financial_year/update_financialperiodclose'); ?>",
                            beforeSend: function() {
                                startLoad();
                            },
                            success: function(data) {
                                stopLoad();
                                refreshNotifications(true);
                                if (data) {
                                    $("#isactiveedit_model").modal("hide");
                                    OTable.draw();
                                }
                            },
                            error: function() {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    } else {
                        $('#closefinaperiod_' + id).prop("checked", false);
                    }
                });
        }
    }

    function check_financial_period_iscurrent(companyFinancePeriodID, companyFinanceYearID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                'companyFinanceYearID': companyFinanceYearID,
                companyFinancePeriodID: companyFinancePeriodID
            },
            url: "<?php echo site_url('Financial_year/check_financial_period_iscurrent_activated'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                refreshNotifications(true);
                if (data['details']['isActive'] == 0) {
                    //$("#iscurrentstatus_" + companyFinancePeriodID).attr("checked", false);
                    swal("Cancelled", "Please Active Financial Period !", "error");
                } else if (data['master']['isCurrent'] == 1) {
                    change_financial_period_current(companyFinancePeriodID, companyFinanceYearID);
                } else {
                    //$("#iscurrentstatus_" + companyFinancePeriodID).attr("checked", false);
                    swal("Cancelled", "Selected period is not between current financial year !", "error");
                }
            },
            error: function() {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function reopen_finacial_year(financeYear) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to re-open this finacial year",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Reopen",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'financeYearID': financeYear
                    },
                    url: "<?php echo site_url('Financial_year/reopen_financial_year'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if (data[0] == 's') {
                            OTable.draw();
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function reopen_finacial_period(financeYear) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to re-open this finacial period",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Reopen",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'financePeriodID': financeYear
                    },
                    url: "<?php echo site_url('Financial_year/reopen_financial_period'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if (data) {
                            $("#isactiveedit_model").modal("hide");
                            OTable.draw();
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_financial_year(financeYear) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'financeYearID': financeYear
                    },
                    url: "<?php echo site_url('Financial_year/delete_financial_year'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if (data[0] == 's') {
                            OTable.draw();
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function create_department_Financial_Period_modal(companyFinanceYearID) {
        $('#finYearID').val(companyFinanceYearID);
        $("#create_department_Financial_Period_modal").modal({
            backdrop: "static"
        });
        load_department__Financial_Period();
    }

    function load_department__Financial_Period(department_id) {
        var companyFinanceYearID = $('#finYearID').val();
        var Otable2 = $('#table_department_Financial_Period').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "iDisplayLength": 50,
            "sAjaxSource": "<?php echo site_url('Financial_year/load_department_isactiveeditdetails'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "fnDrawCallback": function(oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $('.Closed').closest('tr').find('input').attr('disabled', true);

            },
            "aoColumns": [{
                    "mData": "companyFinancePeriodID"
                },
                {
                    "mData": "dateFrom"
                },
                {
                    "mData": "dateTo"
                },
                {
                    "mData": "status"
                },
                {
                    "mData": "current"
                },
                {
                    "mData": "closed"
                },
                {
                    "mData": "reopen"
                }
            ],
            "columnDefs": [{
                "targets": [3, 4, 5],
                "orderable": false
            }],
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "fyDepartmentID",
                    "value": department_id
                });
                aoData.push({
                    "name": "finYearID",
                    "value": companyFinanceYearID
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
        setTimeout(function() {
            $('.Current').closest('tr').find('.radiobtn').prop('checked', "checked");
        }, 400);
    }

    function save_department_financial_periods() {
        var finYearID = $('#finYearID').val();
        var fyDepartmentID = $('#department').val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to create financial periods for this department",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'financeYearID': finYearID,
                        'fyDepartmentID': fyDepartmentID
                    },
                    url: "<?php echo site_url('Financial_year/save_department_financial_periods'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if (data[0] == 's') {
                            load_department__Financial_Period(fyDepartmentID);
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }


    function create_13th_month_Financial_Period_toThisYear() {
        var compFinanceYearID = $('#comFinanceYearID').val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to create 13th financial period for this year",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'financeYearID': compFinanceYearID
                    },
                    url: "<?php echo site_url('Financial_year/create_13th_month_Financial_Period_toThisYear'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if (data[0] == 's') {
                            openisactiveeditmodel(compFinanceYearID);
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function change_department_financial_period_current(departmentFinancePeriodID, companyFinanceYearID) {
        var fyDepartmentID = $('#department').val();
        if ($('#iscurrentstatus_' + departmentFinancePeriodID).prop("checked")) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    'departmentFinancePeriodID': departmentFinancePeriodID,
                    'companyFinanceYearID': companyFinanceYearID
                },
                url: "<?php echo site_url('Financial_year/change_department_financial_period_current'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    load_department__Financial_Period(fyDepartmentID);
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function change_department_Financial_yearisactivesatus(departmentFinancePeriodID) {
        var fyDepartmentID = $('#department').val();
        var compchecked = 0;
        if ($('#isactivesatus_' + departmentFinancePeriodID).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    departmentFinancePeriodID: departmentFinancePeriodID,
                    chkedvalue: compchecked
                },
                url: "<?php echo site_url('Financial_year/update_department_financial_year_isactive_status'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        load_department__Financial_Period(fyDepartmentID);
                    } else {
                        $('#isactivesatus_' + departmentFinancePeriodID).prop("checked", false);
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        } else if (!$('#isactivesatus_' + departmentFinancePeriodID).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {
                    departmentFinancePeriodID: departmentFinancePeriodID,
                    chkedvalue: 0
                },
                url: "<?php echo site_url('Financial_year/update_department_financial_year_isactive_status'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        load_department__Financial_Period(fyDepartmentID);
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function check_department_financial_period_iscurrent(departmentFinancePeriodID, companyFinanceYearID) {
        var fyDepartmentID = $('#department').val();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                'companyFinanceYearID': companyFinanceYearID,
                'departmentFinancePeriodID': departmentFinancePeriodID
            },
            url: "<?php echo site_url('Financial_year/check_department_financial_period_iscurrent_activated'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                refreshNotifications(true);
                if (data['details']['isActive'] == 0) {
                    //$("#iscurrentstatus_" + departmentFinancePeriodID).attr("checked", false);
                    swal("Cancelled", "Please Active Department Financial Period !", "error");
                    load_department__Financial_Period(fyDepartmentID);
                } else if (data['master']['isCurrent'] == 1) {
                    change_department_financial_period_current(departmentFinancePeriodID, companyFinanceYearID);
                } else {
                    //$("#iscurrentstatus_" + departmentFinancePeriodID).attr("checked", false);
                    swal("Cancelled", "Selected Department Financial period is not between current financial year !", "error");
                    load_department__Financial_Period(fyDepartmentID);
                }
            },
            error: function() {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function change_department_financialperiodclose(departmentFinancePeriodID) {
        var fyDepartmentID = $('#department').val();
        var compchecked = 0;
        if ($('#closefinaperiod_' + departmentFinancePeriodID).is(":checked")) {
            compchecked = 1;
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "You want to close this Department financial period!",
                    /*You want to close this financial period!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_Close'); ?>",
                    /*Close*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            data: {
                                departmentFinancePeriodID: departmentFinancePeriodID,
                                chkedvalue: compchecked,
                                fyDepartmentID: fyDepartmentID
                            },
                            url: "<?php echo site_url('Financial_year/update_department_financialperiodclose'); ?>",
                            beforeSend: function() {
                                startLoad();
                            },
                            success: function(data) {
                                stopLoad();
                                refreshNotifications(true);
                                if (data) {
                                    load_department__Financial_Period(fyDepartmentID);
                                    // $("#create_department_Financial_Period_modal").modal("hide");
                                    // OTable.draw();
                                }
                            },
                            error: function() {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    } else {
                        $('#closefinaperiod_' + departmentFinancePeriodID).prop("checked", false);
                    }
                });
        }
    }

    function reopen_department_finacial_period(departmentFinancePeriodID) {
        var fyDepartmentID = $('#department').val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to re-open this finacial period",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Reopen",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'departmentFinancePeriodID': departmentFinancePeriodID
                    },
                    url: "<?php echo site_url('Financial_year/reopen_department_financial_period'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if (data) {
                            load_department__Financial_Period(fyDepartmentID);
                            // $("#create_department_Financial_Period_modal").modal("hide");
                            // OTable.draw();
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function department_required() {
        myAlert('w', 'Please Select a Department');
        load_department__Financial_Period(null);
    }
</script>