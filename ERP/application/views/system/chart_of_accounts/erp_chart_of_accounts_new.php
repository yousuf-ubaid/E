<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('finance_ms_ca_chart_of_accounts');
echo head_page($title, false);


/*echo head_page('Chart of Accounts', false);*/
$master_acc_arr = master_coa_account();
$currency_arr = all_currency_new_drop();
$master_arr = array('' => 'Select Type', '1' => 'Master Account', '0' => 'Ledger Account');
$controll_arr = array('' => 'Select Type', '1' => 'Controll Account', '0' => 'Ledger Account');
$usergroupcompanywiseallow = getPolicyValuesgroup('CHA','All');
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-2">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:green; font-size:15px;"></span> <?php echo $this->lang->line('common_active');?><!--Active-->
                </td>
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:red; font-size:15px;"></span> <?php echo $this->lang->line('finance_common_inactive');?><!--Inactive-->
                </td>
            </tr>
        </table>
    </div>
    <?php echo form_open('', 'role="form" id="chartofaccountmaster_arabic_filter_form"'); ?>
    <div class="col-md-3">
        <span style=""><?php echo $this->lang->line('finance_ms_ca_account_category');?><!--Account Category--></span>

        <?php echo form_dropdown('accountType', all_account_category_drop(false), '', 'class="form-control" multiple onchange="load_page()" id="accountType"'); ?>
    </div>
    </form>
    <div class="col-md-4">
        <span style=""><?php echo $this->lang->line('finance_common_find_gl');?><!--Find GL--> &nbsp;</span>
        <input name="query" id="query" class="" type="text" size="30" maxlength="30"
               onkeyup="highlightSearch(this.value)">
    </div>
    <div class="col-md-3 text-right pull-right">
    <?php if($usergroupcompanywiseallow == 0){?>
        <button type="button" class="btn btn-primary " data-toggle="modal" onclick="createcustomer()"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New-->
        </button>
    <?php } else if ($usergroupcompanywiseallow != 0) { ?>
        <button type="button" class="btn btn-primary " data-toggle="modal" onclick="load_gl_model()"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New-->
        </button>
    <?php }?>
        <a href="#" type="button" class="btn btn-excel " style="margin-left: 2px" onclick="excel_export()">
            <i class="fa fa-file-excel-o"></i> Excel <!--Excel-->
        </a>
</div>
<hr>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('finance_ms_ca_chart_of_accounts');?><!--Chart of Accounts--></h4>
            </div>
            <form class="form-horizontal" id="chart_of_accont_form">
                <input type="hidden" id="controlAccountUpdate" name="controlAccountUpdate" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="masterCategory" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_account_type');?><!--Account Type-->  <?php required_mark(); ?></label>

                                <div class="col-sm-8">
                                    <?php echo form_dropdown('accountCategoryTypeID', all_account_category_drop(), '', 'class="form-control" onchange="fetch_master_Account(this.value)" id="accountCategoryTypeID"'); ?>
                                </div>
                            </div>
                            <div class="form-group isCash" style="display: none;">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('common_type');?><!--Type--></label>

                                <div class="col-sm-6">
                                    <?php echo form_dropdown('isCash', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 1 => $this->lang->line('finance_ms_ca_cash_account')/*'Cash Account'*/, 0 => $this->lang->line('finance_ms_ca_bank_account')/*'Bank Account'*/), 0, 'class="form-control" onchange="is_cash(this.value)" id="isCash"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="masterAccountYN1" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_ms_ca_master_account');?><!--Master Account-->  <?php required_mark(); ?></label>

                                <div class="col-sm-3">
                                    <?php echo form_dropdown('masterAccountYN', array('' => $this->lang->line('finance_common_select_status')/*'Select Status'*/, '1' => $this->lang->line('common_yes')/*'Yes'*/, '0' => $this->lang->line('common_no')/*'No'*/), '0', 'class="form-control " id="masterAccountYN" onchange="set_master_detail(this.value)"'); ?>
                                    <?php echo form_dropdown('isBank', array('' => $this->lang->line('finance_common_select_status')/*'Select Status'*/, '1' => $this->lang->line('common_yes')/*'Yes'*/, '0' => $this->lang->line('common_no')/*'No'*/), '0', 'class="form-control control_account" id="isBank" style="display: none;" required'); ?>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo form_dropdown('masterAccount', array('' => $this->lang->line('finance_ms_ca_master_account')/*'Master Account'*/), '', 'class="form-control set_master" id="masterAccount"'); ?>
                                </div>
                            </div>

                            <div class="form-group set_bank set_controll">
                                <label for="bankName" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_bank_name');?><!--Bank Name-->  <?php required_mark(); ?></label>

                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="bankName" name="bankName">
                                </div>
                            </div>
                            <div class="form-group set_bank">
                                <label for="bankAccountNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_b_account_number');?><!--B/Account Number--> <?php required_mark();?></label>

                                <div class="col-sm-8">
                                    <input type="text" class="form-control number" id="bankAccountNumber"
                                           name="bankAccountNumber">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_ms_ca_check_number');?><!--Check Number-->  <?php required_mark(); ?></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control number" id="bankCheckNumber"
                                           name="bankCheckNumber">
                                </div>
                            </div>
                            <div class="form-group activeSub">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_is_active_is');?><!--isActive--></label>

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
                                <label for="GLSecondaryCode" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_secondary_code');?><!--Secondary Code-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="GLSecondaryCode" name="GLSecondaryCode">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="GLDescription" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_account_name');?><!--Account Name-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="GLDescription" id="GLDescription">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bank_branch" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_common_bank_branch');?><!--Bank Branch-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bank_branch" name="bank_branch">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bank_swift_code" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_ms_ca_bank_swift_code');?><!--Bank Swift Code-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bank_swift_code" name="bank_swift_code">
                                </div>
                            </div>
                            <div class="form-group set_bank set_controll">
                                <label for="bank_branch" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_ms_ca_bank_currency');?><!--Bank Currency-->  <?php required_mark(); ?></label>

                                <div class="col-sm-7">
                                    <?php echo form_dropdown('bankCurrencyCode', $currency_arr, '', 'class="form-control select2" id="bankCurrencyCode" '); ?>
                                </div>
                            </div>
                            <div class="form-group set_bank">
                                <label for="bankCheckNumber" class="col-sm-4 control-label"><?php echo $this->lang->line('finance_ms_ca_bank_iscard');?><!--isCard--></label>

                                <div class="col-sm-6">
                                    <?php echo form_dropdown('isCard', array('' => $this->lang->line('finance_common_select_status')/*'Select Status'*/, '1' => $this->lang->line('common_yes')/*'Yes'*/, '0' => $this->lang->line('common_no')/*'No'*/), '0', 'class="form-control control_account" id="isCard"'); ?>
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <label for="" class="col-sm-4 control-label">Signature level</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control number"  id="authourizedSignatureLevel" name="authourizedSignatureLevel">
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="Submit" class="btn btn-primary" id="chartofaccountbtn"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function () {
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
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
            url: "<?php echo site_url('Chart_of_acconts_new/load_master_ofAccount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#load_chartofAccount').html(data);
                $("[rel=tooltip]").tooltip();

            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    $('#chart_of_accont_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
           /* accountCategoryTypeID: {validators: {notEmpty: {message: 'Account Type is required.'}}},
            GLSecondaryCode: {validators: {notEmpty: {message: 'Account Code is required.'}}},
            GLDescription: {validators: {notEmpty: {message: 'Account Description is required.'}}},
            masterAccountYN: {validators: {notEmpty: {message: 'Is Master Account is required.'}}},*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        $('#accountCategoryTypeID ').prop('disabled', false);
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
            url: "<?php echo site_url('Chart_of_acconts_new/save_chart_of_accont'); ?>",
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
                }else
                {
                    <?php if($usergroupcompanywiseallow == 0){?>
                    $('#accountCategoryTypeID ').prop('disabled', true);
                    <?php } ?>
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    function load_gl_model() {
        GLAutoID = null;
        $("#GL_modal").modal({backdrop: "static"});
        $('#chart_of_accont_form')[0].reset();
        $('.set_bank').addClass('hidden');
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
        $('.set_bank').addClass('hidden');
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
                $('.set_bank').removeClass('hidden');
                $('#isCash').val('0');
                $('.isCash').show();
            }
        }
    }

    function is_cash(val) {
        if (val == 1) {
            $('.set_bank').addClass('hidden');
        }else{
            $('.set_bank').removeClass('hidden');
        }
        fetch_master_Account();
    }

    function fetch_master_Account(val, select_value) {
        string = $('#accountCategoryTypeID option:selected').text();
        accountCategoryTypeID = $('#accountCategoryTypeID option:selected').val();
        isCash = $('#isCash').val();
        if (string) {
            sub_cat = string.split('|');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'subCategory': sub_cat[1], 'accountCategoryTypeID': accountCategoryTypeID, GLAutoID: GLAutoID, isCash: isCash},
                url: "<?php echo site_url('Chart_of_acconts_new/fetch_master_account'); ?>",
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
                    if(isCash==0){
                        set_master_detail()
                    }
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }
    }

    function edit_chart_of_accont(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('finance_common_you_want_to_edit_this_file');?>",/*You want to edit this file !*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f8bb86",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>",/*Edit*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'GLAutoID': id},
                    url: "<?php echo site_url('Chart_of_acconts_new/load_chart_of_accont_header'); ?>",
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
                        $('#bankCurrencyCode').val(data['bankCurrencyID']);
                        $('#isCard').val(data['isCard']);
                        if (data['isBank'] == 1) {
                            is_cash(data['isCash']);
                            setTimeout(function () {
                                $('#isCash').val(data['isCash']).change();
                            }, 2500);
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
                            <?php if($usergroupcompanywiseallow == 0){?>
                            $('#accountCategoryTypeID ').prop('disabled', true);
                            <?php } else if ($usergroupcompanywiseallow != 0) { ?>
                            $('#accountCategoryTypeID ').prop('disabled', false);
                            <?php }?>

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
    function createcustomer() {
        swal(" ", "You do not have permission to create  chart of accounts at company level,please contact your system administrator.", "error");
    }

 /* Function added */
    function excel_export() {
        var form = document.getElementById('chartofaccountmaster_arabic_filter_form');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#chartofaccountmaster_arabic_filter_form').serializeArray();
        form.action = '<?php echo site_url('Chart_of_acconts_new/export_excel_chartofaccounts_master_new'); ?>';
        form.submit();
    }
   /* End  Function */
</script>