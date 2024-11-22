<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_group_financial_year');
echo head_page($title, false);
 ?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active') ?></td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_closed') ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="open_group_Financial_year_model()" class="btn btn-primary pull-right"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('config_create_financial_year') ?><!--Create Financial Year-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="Group_financial_year_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th colspan="3"><?php echo $this->lang->line('common_financial_year') ?> </th>
            <th colspan="4"><?php echo $this->lang->line('common_action') ?></th>
        </tr>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_financial_year') ?></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_comments') ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_active') ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('transaction_common_currenct') ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_closed') ?></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="Group_Financial_year_model" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><?php echo $this->lang->line('config_create_new_group_financial_year') ?><!--Add New Group Financial Year--></h3>
            </div>
            <form role="form" id="Group_Financial_year_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_start_date') ?></label>

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
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_end_date') ?></label>

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
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_comments') ?></label>

                            <div class="col-sm-6">
                                <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close') ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var OTable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/GroupFinanceYear/group_financial_year_view', '', 'Group Financial Year');
        });

        $('#beginningdate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose:true
        }).on('changeDate', function (ev) {
            $('#Group_Financial_year_form').bootstrapValidator('revalidateField', $('#beginningdate'));
            //$('#Group_Financial_year_form').bootstrapValidator('revalidateField', 'beginningdate');
            //$(this).datepicker('hide');
        }).mask("9999-99-99");


        $('#endingdate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose:true
        }).on('changeDate', function (ev) {
            $('#Group_Financial_year_form').bootstrapValidator('revalidateField', $('#endingdate'));
            //$('#Group_Financial_year_form').bootstrapValidator('revalidateField', 'endingdate');
            //$(this).datepicker('hide');
        }).mask("9999-99-99");


        fetch_Financial_year();


        $('#Group_Financial_year_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                beginningdate: {validators: {notEmpty: {message: 'Beginning Date is required.'}}},
                endingdate: {validators: {notEmpty: {message: 'Ending Date is required.'}}},
                comments: {validators: {notEmpty: {message: 'Comments are required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Group_financial_year/save_financial_year'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $('#Group_Financial_year_form')[0].reset();
                        $('#Group_Financial_year_form').bootstrapValidator('resetForm', true);
                        $("#Group_Financial_year_model").modal("hide");
                        OTable.draw();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function open_group_Financial_year_model() {
        //$('#Group_Financial_year_form')[0].reset();
        //$('#Group_Financial_year_form').bootstrapValidator('resetForm', true);
        $("#Group_Financial_year_model").modal({backdrop: "static"});
    }

    function fetch_Financial_year() {
        OTable = $('#Group_financial_year_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            bAutoWidth: false,
            "sAjaxSource": "<?php echo site_url('Group_financial_year/load_Financial_year'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "groupFinanceYearID"},
                {"mData": "financial_year"},
                {"mData": "comments"},
                {"mData": "status"},
                {"mData": "current"},
                {"mData": "close"},
                {"mData": "endingDate"},
                {"mData": "beginingDate"}
            ],

            "columnDefs": [{"targets": [3, 4, 5], "orderable": false},{"visible":false,"searchable": true,"targets": [6,7] }],
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

    function changeFinancial_yearsatus(id) {
        var compchecked = 0;
        if ($('#statusactivate_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {groupFinanceYearID: id, chkedvalue: compchecked},
                url: "<?php echo site_url('Group_financial_year/update_year_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        OTable.draw();
                    } else {
                        $('#statusactivate_' + id).attr('checked', false);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
        else if (!$('#statusactivate_' + id).is(":checked")) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {groupFinanceYearID: id, chkedvalue: 0},
                url: "<?php echo site_url('Group_financial_year/update_year_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        OTable.draw();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
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
                data: {groupFinanceYearID: id, chkedvalue: compchecked},
                url: "<?php echo site_url('Group_financial_year/update_year_current'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        //fetch_Financial_year();
                        OTable.fnDraw();
                    } else {
                        $('#statuscurrent_' + id).prop('checked', false);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
        else if (!$('#statuscurrent_' + id).prop("checked")) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {groupFinanceYearID: id, chkedvalue: 0},
                url: "<?php echo site_url('Group_financial_year/update_year_current'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        OTable.draw();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function openisactiveeditmodel(id) {
        $("#isactiveedit_model").modal({backdrop: "static"});
        loadisactiveeditdetails(id);
    }


    $("#isactiveedit_model").on("hidden.bs.modal", function () {

        OTable.draw();

    });


    function changeFinancial_yearclose(id) {
        var compchecked = 0;
        if ($('#closeactivate_' + id).is(":checked")) {
            compchecked = 1;
            swal({
                    title: "Are you sure?",
                    text: "You want to close this financial year!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Ok"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            data: {groupFinanceYearID: id, chkedvalue: compchecked},
                            url: "<?php echo site_url('Group_financial_year/update_year_close'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                if (data) {
                                    OTable.draw();
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    } else {
                        $('#closeactivate_' + id).prop("checked", false);
                    }
                });

        }
    }


</script>