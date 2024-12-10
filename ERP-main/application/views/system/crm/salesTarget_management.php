<?php
$this->load->helper('crm_helper');
$arr_employees = fetch_employees_by_company_multiple();
$arr_project = fetch_project_multiple();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$deafaultCurrency = $this->common_data['company_data']['company_default_currencyID'];
?>
<style>
    .width100p {
        width: 100%;
    }

    .user-table {
        width: 100%;
    }

    .bottom10 {
        margin-bottom: 10px !important;
    }

    .btn-toolbar {
        margin-top: -2px;
    }

    table {
        max-width: 100%;
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .flex {
        display:
    }
</style>


<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-cubes" aria-hidden="true"></i> Sales Person Targets
                        </div>
                        <div class="btn-toolbar btn-toolbar-small pull-right">
                            <button class="btn btn-primary btn-xs bottom10" onclick="open_add_personModel()">Add Person
                            </button>
                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">
                            <div class="system-settings">
                                <div id="salestargetMaster_view"></div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div id="add-user-modal" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width: 60%">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">New Person</h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open('', 'role="form" id="person_salestarget_form" class="form-horizontal"'); ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <header class="head-title">
                                <h2>Header Details</h2>
                            </header>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="selectbasic">Person</label>

                                <div class="col-sm-8" id="">
                                    <?php echo form_dropdown('userID', $arr_employees, '', 'class="form-control" id="userID""'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="selectbasic">Project</label>

                                <div class="col-sm-8" id="">
                                    <?php echo form_dropdown('projectID', $arr_project, '', 'class="form-control" id="projectID""'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="selectbasic">Currency</label>

                                <div class="col-sm-8">
                                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr ,$deafaultCurrency, 'class="form-control select2" id="transactionCurrencyID"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-sm-12">
                            <header class="head-title">
                                <h2>Target Details</h2>
                            </header>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="selectbasic">Date From</label>

                                <div class="col-sm-8">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="dateFrom"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="dateFrom"
                                               class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="selectbasic">Date To</label>

                                <div class="col-sm-8">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="dateTo"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="dateTo"
                                               class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="selectbasic">Target Value</label>

                                <div class="col-sm-8">
                                    <input type="text" name="targetValue" placeholder="0.00"
                                           class="form-control number" onfocus="this.select();" id="targetValue"
                                           autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button id="singlebutton" type="submit" name="singlebutton"
                                        class="btn btn-primary btn-xs pull-right" style="margin-right: 2%;">Submit
                                </button>
                            </div>
                        </div>
                    </div>
                    </form>
                    <br>

                    <div class="row">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                <tr class="task-cat-upcoming">
                                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Date From</td>
                                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Date To</td>
                                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Currency</td>
                                    <td class="headrowtitle"
                                        style="border-bottom: solid 1px #f76f01;text-align: center">Target Value
                                    </td>
                                </tr>
                                </thead>
                                <tbody id="sales_target_add">
                                </tbody>
                                <tfoot id="sales_target_add_footer">
                                </tfoot>

                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {
            getPersonTarget_tableView();

            number_validation();

            $('.select2').select2();

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#person_salestarget_form').bootstrapValidator('revalidateField', 'dateFrom');
                $('#person_salestarget_form').bootstrapValidator('revalidateField', 'dateTo');
            });

            Inputmask().mask(document.querySelectorAll("input"));

            $('#person_salestarget_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    userID: {validators: {notEmpty: {message: 'Person is required.'}}},
                    projectID: {validators: {notEmpty: {message: 'Project is required.'}}},
                    //transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                    dateFrom: {validators: {notEmpty: {message: 'Date From is required.'}}},
                    dateTo: {validators: {notEmpty: {message: 'Date To is required.'}}},
                    targetAmount: {validators: {notEmpty: {message: 'Target Value is required.'}}}
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#userID").prop("disabled", false);
                $("#transactionCurrencyID").prop("disabled", false);
                $("#projectID").prop("disabled", false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Crm/save_sales_person_target'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            $("#userID").prop("disabled", true);
                            $("#transactionCurrencyID").prop("disabled", true);
                            $("#projectID").prop("disabled", true);
                            $("#targetValue").val('');
                            $('#person_salestarget_form').bootstrapValidator('revalidateField', 'dateFrom');
                            $('#person_salestarget_form').bootstrapValidator('revalidateField', 'dateTo');
                            fetch_salesTarget_detail_table();
                            getPersonTarget_tableView();

                        } else {
                            $('.btn-primary').prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
        });

        function open_add_personModel() {
            var currentDate = '<?php echo $current_date ?>';
            $("#userID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#projectID").prop("disabled", false);
            $('#person_salestarget_form')[0].reset();
            $('#person_salestarget_form').bootstrapValidator('resetForm', true);
            $("#dateFrom").val(currentDate);
            $("#dateTo").val(currentDate);
            fetch_salesTarget_detail_table();
            $('#add-user-modal').modal('show');
        }

        function getPersonTarget_tableView() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {},
                url: "<?php echo site_url('crm/salesTarget_ManagementView'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#salestargetMaster_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function fetch_salesTarget_detail_table() {
            var userID = $('#userID').val();
            var projectID = $('#projectID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {userID: userID, projectID: projectID},
                url: "<?php echo site_url('crm/fetch_salesTarget_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#sales_target_add').empty();
                    $('#sales_target_add_footer').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#sales_target_add').append('<tr class="danger"><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        tot_amount = 0;
                        tax_total = 0;
                        $.each(data['detail'], function (key, value) {
                            $('#sales_target_add').append('<tr><td>' + x + '</td><td>' + value['dateFrom'] + '</td><td>' + value['dateTo'] + '</td><td>' + value['CurrencyCode'] + '</td><td class="text-right">' + parseFloat(value['targetValue']).formatMoney(2, '.', ',') + '</td><td class="text-right"> <a onclick="delete_salesTargetAdd(' + value['salesTargetID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                            tot_amount += parseFloat(value['targetValue']);
                        });
                        $('#sales_target_add_footer').append('<tr><td colspan="4">Total</td><td class="text-right total">' + tot_amount.formatMoney(2, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function delete_salesTargetAdd(salesTargetID) {
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
                        data: {'salesTargetID': salesTargetID},
                        url: "<?php echo site_url('Crm/delete_salesTarget_newPerson'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_salesTarget_detail_table();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    </script>