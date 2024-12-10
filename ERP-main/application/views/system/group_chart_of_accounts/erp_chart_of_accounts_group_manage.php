<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title = $this->lang->line('config_chart_of_accounts');
echo head_page($title, false);
$master_acc_arr = master_coa_account_group();
$currency_arr = all_currency_master_drop();
$master_arr = array('' => 'Select Type', '1' => 'Master Account', '0' => 'Ledger Account');
$controll_arr = array('' => 'Select Type', '1' => 'Controll Account', '0' => 'Ledger Account');

$policydescription = getPolicydescription_masterid(4);
$policyvalue = getgrouppolicyvalues($policydescription['grouppolicymasterID'] ?? '');
$policyvalue_detail = getPolicydescription_values_detail($policydescription['grouppolicymasterID'] ?? '');

$secondary_code = getPolicyValues('SCAC', 'All');

?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    /**
     * Framework starts from here ...
     * ------------------------------
     */
    .tree,
    .tree ul {
        margin: 0 0 0 1em; /* indentation */
        padding: 0;
        list-style: none;
        color: #cbd6cc;
        position: relative;
    }

    .tree ul {
        margin-left: .5em
    }

    /* (indentation/2) */

    .tree:before,
    .tree ul:before {
        content: "";
        display: block;
        width: 0;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        border-left: 1px solid;
    }

    .tree li {
        margin: 0;
        padding: 0 1.5em; /* indentation + .5em */
        line-height: 2em; /* default list item's `line-height` */
        font-weight: bold;
        position: relative;
        font-size: 11px
    }

    .tree li:before {
        content: "";
        display: block;
        width: 10px; /* same with indentation */
        height: 0;
        border-top: 1px solid;
        margin-top: -1px; /* border top width */
        position: absolute;
        top: 1em; /* (line-height/2) */
        left: 0;
    }

    .tree li:last-child:before {
        background: white; /* same with body background */
        height: auto;
        top: 1em; /* (line-height/2) */
        bottom: 0;
    }

    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;
        background-color: #E8F1F4;
    }

    .subheader {
        color: black;
        font-weight: bolder;
        font-size: 11px;
        background-color: #fbfbfb;
    }

    .subdetails {
        /* color: #4e4e4e;*/

        font-size: 11px;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 4px;
    }

    .highlight {
        background-color: #FFF59D;
        /* color:#555;*/
    }

</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-2">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:green; font-size:15px;"></span> <?php echo $this->lang->line('common_active') ?><!--Active-->
                </td>
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:red; font-size:15px;"></span> <?php echo $this->lang->line('config_common_inactive') ?><!--Inactive-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-3">
        <span style=""><?php echo $this->lang->line('common_account_category') ?><!--Account Category--></span>

        <?php echo form_dropdown('accountType', all_account_category_drop(false), '', 'class="form-control" multiple onchange="load_page()" id="accountType"'); ?>

    </div>
    <div class="col-md-4">
        <span style=""> &nbsp;</span>
        <input name="query" id="query" class="" type="text" size="30" maxlength="30"
               onkeyup="highlightSearch(this.value)">
    </div>

    <div class="col-md-1 text-right pull-right">
        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" onclick="load_gl_model()"><i
                class="fa fa-plus"></i><?php echo $this->lang->line('common_create_new') ?><!--Create New-->
        </button>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-4">
        <label for=""><?php echo $policydescription['groupPolicyDescription'] ?? '' ?></label>
    </div>
    <div class="col-md-1">
        <?php echo form_dropdown('isallow',$policyvalue, $policyvalue_detail['value'] ?? '', 'class="form-control" id="isallow" onchange="updatepolicy(this.value)" '); ?>
    </div>
</div>

<div id="load_chartofAccount">

</div>
<br>
<br>


<div class="modal fade" id="GL_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('config_chart_of_accounts') ?><!--Chart of Accounts--></h4>
            </div>
            <form class="form-horizontal" id="chart_of_accont_form">
                <input type="hidden" id="controlAccountUpdate" name="controlAccountUpdate" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="masterCategory" class="col-sm-4 control-label"><?php echo $this->lang->line('common_account_type') ?><!--Account Type-->  <?php required_mark(); ?></label>

                                <div class="col-sm-8">
                                    <?php echo form_dropdown('accountCategoryTypeID', all_account_category_drop(), '', 'class="form-control" onchange="fetch_master_Account(this.value)" id="accountCategoryTypeID"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="masterAccountYN1" class="col-sm-4 control-label"><?php echo $this->lang->line('config_master_account') ?><!--Master Account-->  <?php required_mark(); ?></label>

                                <div class="col-sm-3">
                                    <?php echo form_dropdown('masterAccountYN', array('' => 'Select Status', '1' => 'Yes', '0' => 'No'), '0', 'class="form-control " id="masterAccountYN" onchange="set_master_detail(this.value)"'); ?>
                                    <?php echo form_dropdown('isBank', array('' => 'Select Status', '1' => 'Yes', '0' => 'No'), '0', 'class="form-control control_account" id="isBank" style="display: none;" required'); ?>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo form_dropdown('masterAccount', array('' => 'Master Account'), '', 'class="form-control set_master" id="masterAccount"'); ?>
                                </div>
                            </div>
                            <div class="form-group isCash" style="display: none;">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('common_type') ?><!--Type--></label>

                                <div class="col-sm-6">
                                    <?php echo form_dropdown('isCash', array('' => 'Select Type', 1 => 'Cash Account', 0 => 'Bank Account'), 0, 'class="form-control" onchange="is_cash(this.value)" id="isCash"'); ?>
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bankName" class="col-sm-4 control-label"><?php echo $this->lang->line('config_bank_name') ?><!--Bank Name-->  <?php required_mark(); ?></label>

                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="bankName" name="bankName">
                                </div>
                            </div>
                            <div class="form-group set_bank">
                                <label for="bankAccountNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('common_bank_account_no') ?><!--B/Account Number --> <?php required_mark(); ?></label>

                                <div class="col-sm-8">
                                    <input type="text" class="form-control number" id="bankAccountNumber"
                                           name="bankAccountNumber">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('config_check_number') ?><!--Check Number-->  <?php required_mark(); ?></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control number" id="bankCheckNumber"
                                           name="bankCheckNumber">
                                </div>
                            </div>
                            <div class="form-group activeSub">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('common_is_active') ?><!--isActive--></label>

                                <div class="col-sm-6">
                                    <div class="skin skin-square">
                                        <div class="skin-section" id="extraColumns">
                                            <input id="checkbox_isActive" type="checkbox"
                                                   data-caption="" class="columnSelected" name="isActive" value="1"
                                                   checked>
                                            <label for="checkbox">
                                                &nbsp;
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="GLSecondaryCode" class="col-sm-4 control-label"><?php echo $this->lang->line('config_secondary_code') ?><!--Secondary Code --> <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="GLSecondaryCode" name="GLSecondaryCode">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="GLDescription" class="col-sm-4 control-label"><?php echo $this->lang->line('common_account_name') ?><!--Account Name-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="GLDescription" id="GLDescription">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bank_branch" class="col-sm-4 control-label"><?php echo $this->lang->line('config_bank_brach') ?><!--Bank Branch --> <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bank_branch" name="bank_branch">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bank_swift_code" class="col-sm-4 control-label"><?php echo $this->lang->line('config_bank_swift_code') ?><!--Bank Swift Code-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bank_swift_code" name="bank_swift_code">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bank_branch" class="col-sm-4 control-label"><?php echo $this->lang->line('config_bank_currency') ?><!--Bank Currency-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <?php echo form_dropdown('bankCurrencyCode', $currency_arr, '', 'class="form-control select2" id="bankCurrencyCode" '); ?>
                                </div>
                            </div>
                            <div class="form-group set_bank">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('config_iscard') ?><!--isCard--></label>

                                <div class="col-sm-6">
                                    <?php echo form_dropdown('isCard', array('' => 'Select Status', '1' => 'Yes', '0' => 'No'), '0', 'class="form-control control_account" id="isCard"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                    <button type="Submit" class="btn btn-primary" id="chartofaccountbtn"><?php echo $this->lang->line('common_save_change') ?><!--Save changes--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="chartofaccountLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="chart_of_accounts_link_form"'); ?>
            <input type="hidden" name="GLAutoIDhn" id="GLAutoIDhn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_chart_of_account_link') ?><!--Chart of account Link--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyChartOfAccounts">

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('config_common_add_link') ?><!--Add Link-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="chartofaccountDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="chart_of_accounts_duplicate_form"'); ?>
            <input type="hidden" name="GLAutoIDDuplicatehn" id="GLAutoIDDuplicatehn">
            <input type="hidden" name="masterAccountYNhn" id="masterAccountYNhn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_chart_of_account_replication') ?><!--Chart of account Replication--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyChartOfAccountsDuplicate">

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSavedup"><?php echo $this->lang->line('config_duplicate') ?><!--Duplicate-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="invalidinvoicemodal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_chart_of_accounts_or_category_not_linked') ?><!--Chart of account or category not linked--></h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th ><?php echo $this->lang->line('common_company') ?><!--Company--></th>
                            <th><?php echo $this->lang->line('common_message') ?><!--Message--></th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?></button>
            </div>

        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    var Otable;
    $(document).ready(function () {
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('#chart_of_accounts_link_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Chart_of_acconts_group/save_chart_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled',false);
                        if (data[0] == 's') {
                            /*load_chart_details_table();
                            load_company($('#GLAutoIDhn').val());
                            $('#companyID').val('').change();*/
                            load_all_companies_chartofaccounts();
                            $('#chartofaccountLinkModal').modal('hide');
                        }

                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });

        $('#chart_of_accounts_duplicate_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Chart_of_acconts_group/save_chart_duplicate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSavedup').attr('disabled',false);
                        if (data[0] == 's') {
                            $('#chartofaccountDuplicateModal').modal('hide');
                        }

                        if (jQuery.isEmptyObject(data[2])) {

                        } else {
                            $('#errormsg').empty();
                            $.each(data[2], function (key, value) {
                                $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                            });
                            $('#invalidinvoicemodal').modal('show');
                            $('#chartofaccountDuplicateModal').modal('hide');
                        }

                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });
    });

    $('.headerclose').click(function () {
        fetchPage('system/chart_of_accounts/erp_chart_of_accounts_manage', '', 'Chart of Accounts');
    });
    $('#accountType').multiselect2({
        includeSelectAllOption: true,
        enableFiltering: true,
        onChange: function (element, checked) {
        }
    });
    $("#accountType").multiselect2('selectAll', false);
    $("#accountType").multiselect2('updateButtonText');

    load_master_ofAccount();

    function load_page() {
        load_master_ofAccount();
    }
    function load_master_ofAccount() {
        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {accountTYpe: $('#accountType').val()},
            url: "<?php echo site_url('Chart_of_acconts_group/load_master_ofAccount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#load_chartofAccount').html(data);
                $("[rel=tooltip]").tooltip();

            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    $('#chart_of_accont_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
           /* accountCategoryTypeID: {validators: {notEmpty: {message: 'Account Type is required.'}}},
            GLSecondaryCode: {validators: {notEmpty: {message: 'Account Code is required.'}}},
            GLDescription: {validators: {notEmpty: {message: 'Account Description is required.'}}},
            masterAccountYN: {validators: {notEmpty: {message: 'Is Master Account is required.'}}},*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'GLAutoID', 'value': GLAutoID});
        data.push({'name': 'masterAccount_dec', 'value': $('#masterAccount option:selected').text()});
        data.push({'name': 'account_type', 'value': $('#accountCategoryTypeID option:selected').text()});
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Chart_of_acconts_group/save_chart_of_accont'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data_arr) {
                stopLoad();
                refreshNotifications(true);
                $('#chartofaccountbtn').attr('disabled',false);
                if (data_arr) {
                    $("#GL_modal").modal("hide");
                    $('#chart_of_accont_form')[0].reset();
                    /*             $('#chart_of_accont_form').bootstrapValidator('resetForm', true);*/
                    load_master_ofAccount();
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    function load_gl_model() {
        GLAutoID = null;
        $("#GL_modal").modal({backdrop: "static"});
        $('#chart_of_accont_form')[0].reset();
        $('.set_bank').hide();
        $('#isCash').val('0');
        $('.isCash').hide();
        $('#accountCategoryTypeID ').prop('disabled', false);
        $('#masterAccountYN ').prop('disabled', false);
        $('#masterAccount ').prop('disabled', false);
        $('#checkbox_isActive').iCheck('enable');
        $('#controlAccountUpdate').val(0);
    }

    function set_master_detail(val) {
        var account_id = $('#accountCategoryTypeID option:selected').val();

        val = $('#masterAccountYN option:selected').val();
        $('#masterAccountYN').val(val.toString());
        $('#isBank').val('0');
        $('.set_bank').hide();
        $('#isCash').val('0');
        $('.isCash').hide();
        if (val == 1) {
            $('.set_master').hide();
            $('.is_master').show();
            $('#masterAccount').val('');
            $('.activeSub').hide();
        } else {
            $('.set_master').show();
            $('.is_master').hide();
            $('.activeSub').show();
            if (account_id == 1 && val == 0) {
                $('#isBank').val('1');
                $('.set_bank').show();
                $('#isCash').val('0');
                $('.isCash').show();
            }
        }
    }

    function is_cash(val) {
        $('.set_bank').show();
        if (val == 1) {
            $('.set_bank').hide();
        }
    }

    function fetch_master_Account(val, select_value) {
        string = $('#accountCategoryTypeID option:selected').text();
        accountCategoryTypeID = $('#accountCategoryTypeID option:selected').val();
        if (string) {
            sub_cat = string.split('|');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'subCategory': sub_cat[1], 'accountCategoryTypeID': accountCategoryTypeID, GLAutoID: GLAutoID},
                url: "<?php echo site_url('Chart_of_acconts_group/fetch_master_account'); ?>",
                success: function (data) {
                    $('#masterAccount').empty();
                    var mySelect = $('#masterAccount');
                    mySelect.append($('<option></option>').val('').html('Select Master Account'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['GLAutoID']).html(text['systemAccountCode'] + ' | ' + text['GLSecondaryCode'] + ' | ' + text['GLDescription']));
                        });
                        if (select_value) {
                            $("#masterAccount").val(select_value);
                        }
                    }

                    /*If Bank*/
                    set_master_detail()
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }
    }

    function edit_chart_of_accont(id) {
        swal({
                title: "Are you sure?",
                text: "You want to edit this file !",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f8bb86",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'GLAutoID': id},
                    url: "<?php echo site_url('Chart_of_acconts_group/load_chart_of_accont_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $("#GL_modal").modal({backdrop: "static"});
                        GLAutoID = data['GLAutoID'];
                        $('#GLSecondaryCode').val(data['GLSecondaryCode']);
                        $('#GLDescription').val(data['GLDescription']);
                        $('#masterAccount').val(data['masterAccount']);
                        $('#masterCategory').val(data['masterCategory']);
                        $('#controllAccountYN').val(data['controllAccountYN']);
                        $('#accountCategoryTypeID').val(data['accountCategoryTypeID']);
                        fetch_master_Account(data['subCategory'], data['masterAutoID']);
                        $('#subCategory').val(data['subCategory']);
                        $('#isBank').val(data['isBank']);
                        set_master_detail(data['masterAccountYN']);
                        $('#bankName').val(data['bankName']);
                        $('#bankAccountNumber').val(data['bankAccountNumber']);
                        $('#bankCheckNumber').val(data['bankCheckNumber']);
                        $('#masterAccountYN').val(data['masterAccountYN']);
                        $('#bank_swift_code').val(data['bankSwiftCode']);
                        $('#bank_branch').val(data['bankBranch']);
                        $('#bankCurrencyCode').val(data['bankCurrencyCode']);

                        if (data['subexists'] == 1) {
                            $('#accountCategoryTypeID').attr("style", "pointer-events: none;");
                            $('#masterAccountYN').attr("style", "pointer-events: none;");
                        }else{
                            $('#accountCategoryTypeID').attr("style", "pointer-events: visible;");
                            $('#masterAccountYN').attr("style", "pointer-events: visible;");
                        }
                       

                        if (data['isBank'] == 1) {
                            is_cash(data['isCash']);
                            setTimeout(function () {
                                $('#isCash').val(data['isCash']).change();
                            }, 500);
                        }
                        if(data['isActive'] == 1){
                            $('#checkbox_isActive').iCheck('check');
                        }else{
                            $('#checkbox_isActive').iCheck('uncheck');
                        }

                        if(data['controllAccountYN']==1){
                            $('#accountCategoryTypeID ').prop('disabled', true);
                            $('#masterAccountYN ').prop('disabled', true);
                            $('#masterAccount ').prop('disabled', true);
                            $('#checkbox_isActive').iCheck('disable');
                            $('#controlAccountUpdate').val(1);

                        }
                        else{
                            $('#accountCategoryTypeID ').prop('disabled', false);
                            $('#masterAccountYN ').prop('disabled', false);
                            $('#masterAccount ').prop('disabled', false);
                            $('#checkbox_isActive').iCheck('enable');
                            $('#controlAccountUpdate').val(0);
                        }

                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function link_chart_of_accont(GLAutoID,masterAccountYN) {
        $('#chartofaccountLinkModal').modal({backdrop: "static"});
        $('#companyID').val('').change();
        $('#GLAutoIDhn').val(GLAutoID);
        $('#btnSave').attr('disabled', false);
        /*load_company(GLAutoID);
        load_chart_details_table();*/
        load_all_companies_chartofaccounts(masterAccountYN,GLAutoID);
        //$('#customerlink_form').bootstrapValidator('revalidateField', 'companyID');
    }

    function load_company(GLAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {GLAutoID: GLAutoID, All: 'true'},
            url: "<?php echo site_url('Chart_of_acconts_group/load_company'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapny').html(data);
                $('.select2').select2();
               // $('#loadComapny').removeClass('hidden');
                load_comapny_chart_of_accounts();
            }, error: function () {

            }
        });
    }

    function load_comapny_chart_of_accounts() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#companyID').val(), GLAutoID: $('#GLAutoIDhn').val(), All: 'true'},
            url: "<?php echo site_url('Chart_of_acconts_group/load_chart_of_accounts'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyChartOfAccounts').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function load_chart_details_table() {
        Otable = $('#chart_of_accounts_group_details').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Chart_of_acconts_group/fetch_chart_Details'); ?>",
            "aaSorting": [[0, 'desc']],
            "searching": false,
            "bLengthChange": false,
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "groupChartofAccountDetailID"},
                {"mData": "company_name"},
                {"mData": "systemAccountCode"},
                {"mData": "GLDescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [4], "orderable": false}, {

            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "GLAutoID", "value": $('#GLAutoIDhn').val()});
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

    function delete_chart_link(id){
        swal({
                title: "Are you sure?",
                text: "You want to delete this link!",
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
                    data: {'groupChartofAccountDetailID': id},
                    url: "<?php echo site_url('Chart_of_acconts_group/delete_chart_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                       if(data[0]=='s'){
                       }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_all_companies_chartofaccounts(masterAccountYN,GLAutoID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupChartofAccountMasterID: $('#GLAutoIDhn').val(),masterAccountYN: masterAccountYN,GLAutoid:GLAutoID},
            url: "<?php echo site_url('Chart_of_acconts_group/load_all_companies_chartofaccounts'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyChartOfAccounts').removeClass('hidden');
                $('#loadComapnyChartOfAccounts').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }
    function clearcustomer(id){
        $('#chartofAccountID_'+id).val('').change();
    }

    function load_duplicate_chart_of_accont(GLAutoID,masterAccountYN) {
        $('#chartofaccountDuplicateModal').modal({backdrop: "static"});
        $('#GLAutoIDDuplicatehn').val(GLAutoID);
        $('#masterAccountYNhn').val(masterAccountYN);
        $('#btnSavedup').attr('disabled', false);
        load_all_companies_duplicate(masterAccountYN,GLAutoID);
    }

    function load_all_companies_duplicate(masterAccountYN,GLAutoID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupChartofAccountMasterID: $('#GLAutoIDDuplicatehn').val(),masterAccountYN: masterAccountYN,GLAutoid:GLAutoID},
            url: "<?php echo site_url('Chart_of_acconts_group/load_all_companies_duplicate'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyChartOfAccountsDuplicate').removeClass('hidden');
                $('#loadComapnyChartOfAccountsDuplicate').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }
    function updatepolicy(value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {policyValue: value,groupPolicymasterID:4},
            url: "<?php echo site_url('Chart_of_acconts_group/updategroppolicy'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                }
            }, error: function () {

            }
        });
    }
</script>