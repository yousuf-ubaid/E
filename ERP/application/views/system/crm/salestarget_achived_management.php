<?php echo head_page('Sales Target', false);
$this->load->helper('crm_helper');
$date_format_policy = date_format_policy();
$arr_project = fetch_project_multiple();
$currency_arr = all_currency_new_drop();
$current_date = current_format_date();
$arr_employees = fetch_employees_by_company_multiple();
$arr_crm_products = all_crm_product_master();
$deafaultCurrency = $this->common_data['company_data']['company_default_currencyID'];
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
        <div class="col-sm-5">
            <?php echo form_dropdown('userID', $arr_employees, '', 'class="form-control select2" onchange="startMasterSearch()" id="filter_userID"'); ?>
        </div>
        <div class="col-sm-2 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="dispatchNote_item_add_modal()"><i
                class="fa fa-plus"></i> New Sales
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">

            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <div id="SalesTargetMaster_view"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="sales_target_multiple_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 70%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Sales Target</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="sales_target_multiple_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Employee <?php required_mark(); ?></th>
                            
                            <th style="width: 200px;">Date From <?php required_mark(); ?></th>
                            <th style="width: 200px;">Date To <?php required_mark(); ?></th>
                            <th style="width: 150px;">Currency <?php required_mark(); ?></th>
                            <th style="width: 150px;">Amount<?php required_mark(); ?></th>
                           
                            <th style="width: 250px;">Product</th>
                            <th style="width: 250px;">No of Units</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('userID[]', $arr_employees, '', 'class="form-control select2" "'); ?>
                            </td>
                           
                            <td>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="dateFrom[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>"
                                           class="form-control" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="dateTo[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>"
                                           class="form-control" required>
                                </div>
                            </td>
                            <td>
                                <?php echo form_dropdown('transactionCurrencyID[]', $currency_arr, $deafaultCurrency, 'class="form-control select2"'); ?>
                            </td>
                            <td><input type="text" name="targetValue[]" placeholder="0.00"
                                       class="form-control number targetValue"></td>

                            <td>
                                <?php echo form_dropdown('arr_crm_productsID', $arr_crm_products, '', 'class="form-control select2" id="arr_crm_productsID" '); ?>

                            </td>
                            <td>
                                <input type="number" name="no_of_units"
                                       class="form-control">
                            </td>

                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>

                            
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_sales_target_multiple()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<div id="add-salesTarget-achieved-model" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 50%">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Sales Target</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="sales_target_achieved_form" class="form-horizontal"'); ?>
                <input type="hidden" id="edit_salesTargetID" name="salesTargetID">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">Employee</label>

                            <div class="col-sm-6">
                                <?php echo form_dropdown('userID', $arr_employees, '', 'class="form-control select2" id="userID"'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">Date From</label>

                            <div class="col-sm-6">
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
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">Date To</label>

                            <div class="col-sm-6">
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
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">Currency</label>

                            <div class="col-sm-6">
                                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $deafaultCurrency, 'class="form-control select2" id="transactionCurrencyID"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">Amount</label>

                            <div class="col-sm-6">
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
                            <label class="col-sm-3 control-label" for="selectbasic">No of Units</label>

                            <div class="col-sm-6">
                                <input type="text" name="noOfUnits" placeholder="0"
                                       class="form-control number" onfocus="this.select();" id="noOfUnits"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">Product</label>

                            <div class="col-sm-6">
                                <?php echo form_dropdown('productID', $arr_crm_products, '', 'class="form-control select2" id="productID"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/crm/salestarget_achived_management','','CRM');
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#sales_target_achieved_form').bootstrapValidator('revalidateField', 'dateFrom');
            $('#sales_target_achieved_form').bootstrapValidator('revalidateField', 'dateTo');
        });

        $('.select2').select2();

        number_validation();

        getSalesTargetManagement_tableView();

        $('#sales_target_achieved_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                dateFrom: {validators: {notEmpty: {message: 'Date From is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                dateTo: {validators: {notEmpty: {message: 'Date To is required.'}}},
                targetValue: {validators: {notEmpty: {message: 'Amount is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmLead/save_sales_targetAchieved_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        getSalesTargetManagement_tableView();
                        $('#add-salesTarget-achieved-model').modal('hide');

                    } else {
                        $('.btn-primary').prop('disabled', false);
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

    function getSalesTargetManagement_tableView() {
        var employee = $('#filter_userID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {employee: employee},
            url: "<?php echo site_url('CrmLead/load_SalesTargetAchievedManagement_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#SalesTargetMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_salesTargetAcheived(id) {
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
                    data: {'salesTargetID': id},
                    url: "<?php echo site_url('CrmLead/delete_salesTarget_Acheived'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', 'Deleted Successfully');
                        getSalesTargetManagement_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function open_add_personTargetModel() {
        $('#sales_target_achieved_form')[0].reset();
        $('#sales_target_achieved_form').bootstrapValidator('resetForm', true);
        $("#add-salesTarget-achieved-model").modal({backdrop: "static"});
    }

    function load_sub_cat(select_val) {
        $('#salesTargetID').val("");
        $('#salesTargetID option').remove();
        var projectID = $('#projectID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("CrmLead/load_projectBase_period"); ?>',
            dataType: 'json',
            data: {'projectID': projectID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#salesTargetID').empty();
                    var mySelect = $('#salesTargetID');
                    mySelect.append($('<option></option>').val('').html('Select Period'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['salesTargetID']).html(text['formattedDate']));
                    });
                    if (select_val) {
                        $("#salesTargetID").val(select_val);
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function edit_salesTarget_achieved(salesTargetID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'salesTargetID': salesTargetID},
            url: "<?php echo site_url('CrmLead/load_edit_salesTarget_achieved'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#edit_salesTargetID').val(data['salesTargetID']);
                    $('#dateFrom').val(data['dateFrom']);
                    $('#dateTo').val(data['dateTo']);
                    //load_sub_cat(data['salesTargetID']);
                    $('#targetValue').val(data['targetValue']);
                    $('#noOfUnits').val(data['units']);
                    $('#collectionAmount').val(data['collectionAmount']);
                    $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                    $('#userID').val(data['userID']).change();
                    $("#add-salesTarget-achieved-model").modal({backdrop: "static"});
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getSalesTargetManagement_tableView();
    }

    function clearSearchFilter() {
        //$('#search_cancel').hide();
        $('#search_cancel').addClass('hide');
        $("#filter_userID").val(null).trigger("change");
        getSalesTargetManagement_tableView();
    }

    function dispatchNote_item_add_modal() {
        $('#sales_target_multiple_form')[0].reset();
        $('#discount').val(0);
        $('#discount_amount').val(0);
        $('#po_detail_add_table tbody tr').not(':first').remove();
        $("#sales_target_multiple_modal").modal({backdrop: "static"});

    }

    function add_more() {
        $('select.select2').select2('destroy');
        var appendData = $('#po_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#po_detail_add_table').append(appendData);
        var lenght = $('#po_detail_add_table tbody tr').length - 1;
        number_validation();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });
        $(".select2").select2();
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function save_sales_target_multiple() {
        var data = $("#sales_target_multiple_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CrmLead/save_sales_target_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    getSalesTargetManagement_tableView();
                    $('#sales_target_multiple_modal').modal('hide');
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>